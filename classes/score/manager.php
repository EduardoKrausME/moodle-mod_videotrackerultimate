<?php
namespace mod_videotrackerultimate\score;

use completion_info;
use context_module;
use mod_videotrackerultimate\event\score_updated;
use mod_videotrackerultimate\grade\manager as grade_manager;
use mod_videotrackerultimate\metrics\provider as metrics_provider;
use mod_videotrackerultimate\rule\engine;
use mod_videotrackerultimate\task\recalculate_user as recalculate_user_task;

defined('MOODLE_INTERNAL') || die;

/**
 * Authoritative server-side Engagement Score service.
 *
 * @package mod_videotrackerultimate
 */
final class manager {
    public static function recalculate(context_module $context, \stdClass $activity, int $userid,
            string $origin = 'manual', int $triggeredby = 0): \stdClass {
        global $DB;

        $origin = clean_param($origin, PARAM_ALPHANUMEXT);
        if ($origin === '') {
            $origin = 'unknown';
        }

        $metrics = metrics_provider::collect($context, $activity, $userid);
        $indicators = $DB->get_records(
            'videotrackerultimate_ind',
            ['ultimateid' => $activity->id, 'enabled' => 1],
            'sortorder ASC, id ASC'
        );

        $breakdown = [];
        $rawscore = 0.0;
        $totalweight = 0.0;
        foreach ($indicators as $indicator) {
            $result = engine::evaluate($indicator, $metrics);
            $breakdown[] = $result;
            $rawscore += (float)$result['points'];
            $totalweight += (float)$result['weight'];
        }

        $score = $totalweight > 0 ? round(min(100, max(0, ($rawscore / $totalweight) * 100)), 2) : 0.0;
        $statuscode = self::status_code($activity, $score);
        $existing = $DB->get_record('videotrackerultimate_score', [
            'ultimateid' => $activity->id,
            'userid' => $userid,
        ]);
        $oldscore = $existing ? (float)$existing->score : 0.0;
        $now = time();

        $record = (object)[
            'ultimateid' => (int)$activity->id,
            'userid' => $userid,
            'rawscore' => round($rawscore, 2),
            'totalweight' => round($totalweight, 2),
            'score' => $score,
            'statuscode' => $statuscode,
            'metricsjson' => json_encode($metrics->to_array(), JSON_THROW_ON_ERROR),
            'breakdownjson' => json_encode($breakdown, JSON_THROW_ON_ERROR),
            'calculatedby' => max(0, $triggeredby),
            'origin' => $origin,
            'timecalculated' => $now,
            'gradeupdated' => $existing ? (int)$existing->gradeupdated : 0,
            'gradeorigin' => $existing ? (string)$existing->gradeorigin : '',
            'timecreated' => $existing ? (int)$existing->timecreated : $now,
            'timemodified' => $now,
        ];

        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('videotrackerultimate_score', $record);
        } else {
            $record->id = $DB->insert_record('videotrackerultimate_score', $record);
        }

        $DB->insert_record('videotrackerultimate_log', (object)[
            'ultimateid' => $activity->id,
            'userid' => $userid,
            'triggeredby' => max(0, $triggeredby),
            'origin' => $origin,
            'oldscore' => $oldscore,
            'newscore' => $score,
            'timecreated' => $now,
        ]);

        if (!empty($activity->usegrade)) {
            $graderesult = grade_manager::update_user($activity, $userid, $score, $origin);
            if ($graderesult === GRADE_UPDATE_OK) {
                $record->gradeupdated = $now;
                $record->gradeorigin = $origin;
                $DB->set_field('videotrackerultimate_score', 'gradeupdated', $now, ['id' => $record->id]);
                $DB->set_field('videotrackerultimate_score', 'gradeorigin', $origin, ['id' => $record->id]);
            }
        }

        self::update_completion($context, $activity, $userid, $record, $breakdown);

        $event = score_updated::create([
            'objectid' => $record->id,
            'context' => $context,
            'relateduserid' => $userid,
            'other' => [
                'ultimateid' => (int)$activity->id,
                'oldscore' => $oldscore,
                'newscore' => $score,
                'origin' => $origin,
            ],
        ]);
        $event->trigger();

        return $record;
    }

    public static function get_cached(int $activityid, int $userid): ?\stdClass {
        global $DB;
        $record = $DB->get_record('videotrackerultimate_score', [
            'ultimateid' => $activityid,
            'userid' => $userid,
        ]);
        return $record ?: null;
    }

    public static function queue_user(int $cmid, int $userid, string $origin, int $triggeredby = 0): void {
        $task = new recalculate_user_task();
        $task->set_component('mod_videotrackerultimate');
        $task->set_custom_data([
            'cmid' => $cmid,
            'userid' => $userid,
            'origin' => clean_param($origin, PARAM_ALPHANUMEXT),
            'triggeredby' => max(0, $triggeredby),
        ]);
        \core\task\manager::queue_adhoc_task($task, true);
    }

    public static function queue_activity(\stdClass $cm, string $origin, int $triggeredby = 0): int {
        $context = context_module::instance($cm->id);
        $users = get_enrolled_users($context, 'mod/videotrackerultimate:view', 0, 'u.id');
        foreach ($users as $user) {
            self::queue_user((int)$cm->id, (int)$user->id, $origin, $triggeredby);
        }
        return count($users);
    }

    public static function status_code(\stdClass $activity, float $score): string {
        if ($score >= (float)$activity->excellentmin) {
            return 'excellent';
        }
        if ($score >= (float)$activity->adequatemin) {
            return 'adequate';
        }
        if ($score >= (float)$activity->attentionmin) {
            return 'attention';
        }
        return 'insufficient';
    }

    public static function status_label(\stdClass $activity, string $statuscode): string {
        return match ($statuscode) {
            'excellent' => (string)$activity->excellentlabel,
            'adequate' => (string)$activity->adequatelabel,
            'attention' => (string)$activity->attentionlabel,
            default => (string)$activity->insufficientlabel,
        };
    }

    public static function completion_met(\stdClass $activity, ?\stdClass $score): bool {
        if (!$score) {
            return false;
        }
        $metrics = json_decode((string)$score->metricsjson, true) ?: [];
        $breakdown = json_decode((string)$score->breakdownjson, true) ?: [];

        if ((float)$activity->completionminscore > 0
                && (float)$score->score < (float)$activity->completionminscore) {
            return false;
        }
        if ((int)$activity->completionminpercent > 0
                && (int)($metrics['percent'] ?? 0) < (int)$activity->completionminpercent) {
            return false;
        }
        if (!empty($activity->completionindicators)) {
            foreach ($breakdown as $indicator) {
                if (!empty($indicator['requiredcompletion']) && empty($indicator['passed'])) {
                    return false;
                }
            }
        }
        return true;
    }

    private static function update_completion(context_module $context, \stdClass $activity, int $userid,
            \stdClass $score, array $breakdown): void {
        global $DB;

        $cm = get_coursemodule_from_id('videotrackerultimate', $context->instanceid, 0, false, MUST_EXIST);
        if ((int)$cm->completion !== COMPLETION_TRACKING_AUTOMATIC) {
            return;
        }
        $course = $DB->get_record('course', ['id' => $activity->course], '*', MUST_EXIST);
        $completion = new completion_info($course);
        $completion->update_state(
            $cm,
            self::completion_met($activity, $score) ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE,
            $userid
        );
    }
}

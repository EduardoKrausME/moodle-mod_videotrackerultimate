<?php
namespace mod_videotrackerultimate\task;

use context_module;
use mod_videotrackerultimate\score\manager as score_manager;

defined('MOODLE_INTERNAL') || die;

/**
 * Periodic consistency pass for cached scores.
 *
 * The task queues adhoc work and intentionally avoids calculating analytics
 * in the cron loop itself.
 *
 * @package mod_videotrackerultimate
 */
final class reconcile extends \core\task\scheduled_task {
    public function get_name(): string {
        return get_string('taskreconcile', 'videotrackerultimate');
    }

    public function execute(): void {
        global $DB;

        $cutoff = time() - 21600;
        $activities = $DB->get_records('videotrackerultimate', null, 'id ASC', '*', 0, 100);
        foreach ($activities as $activity) {
            $cm = get_coursemodule_from_instance('videotrackerultimate', $activity->id, $activity->course, false, IGNORE_MISSING);
            if (!$cm) {
                continue;
            }
            $context = context_module::instance($cm->id);
            $users = get_enrolled_users($context, 'mod/videotrackerultimate:view', 0, 'u.id', null, 0, 500);
            foreach ($users as $user) {
                $cached = $DB->get_record('videotrackerultimate_score', [
                    'ultimateid' => $activity->id,
                    'userid' => $user->id,
                ], 'id,timecalculated');
                if (!$cached || (int)$cached->timecalculated < $cutoff) {
                    score_manager::queue_user((int)$cm->id, (int)$user->id, 'scheduled');
                }
            }
        }
    }
}

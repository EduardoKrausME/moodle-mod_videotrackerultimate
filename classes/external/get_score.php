<?php
namespace mod_videotrackerultimate\external;

use context_module;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use mod_videotrackerultimate\reporting;
use mod_videotrackerultimate\score\manager as score_manager;

defined('MOODLE_INTERNAL') || die;

/**
 * Read-only score External API.
 *
 * @package mod_videotrackerultimate
 */
final class get_score extends external_api {
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id'),
            'userid' => new external_value(PARAM_INT, 'User id, 0 means current user', VALUE_DEFAULT, 0),
        ]);
    }

    public static function execute(int $cmid, int $userid = 0): array {
        global $DB, $USER;

        ['cmid' => $cmid, 'userid' => $userid] = self::validate_parameters(
            self::execute_parameters(),
            ['cmid' => $cmid, 'userid' => $userid]
        );
        $cm = get_coursemodule_from_id('videotrackerultimate', $cmid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/videotrackerultimate:view', $context);

        $userid = $userid ?: (int)$USER->id;
        if ($userid !== (int)$USER->id) {
            require_capability('mod/videotrackerultimate:viewreport', $context);
            $visible = reporting::visible_users($cm, $context);
            if (!isset($visible[$userid])) {
                throw new \required_capability_exception($context, 'mod/videotrackerultimate:viewreport', 'nopermissions', '');
            }
        }

        $activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance], '*', MUST_EXIST);
        $score = score_manager::get_cached((int)$activity->id, $userid);
        if (!$score) {
            return [
                'calculated' => false,
                'score' => 0,
                'rawscore' => 0,
                'totalweight' => 0,
                'status' => '',
                'metricsjson' => '{}',
                'breakdownjson' => '[]',
                'timecalculated' => 0,
            ];
        }

        return [
            'calculated' => true,
            'score' => (float)$score->score,
            'rawscore' => (float)$score->rawscore,
            'totalweight' => (float)$score->totalweight,
            'status' => score_manager::status_label($activity, (string)$score->statuscode),
            'metricsjson' => (string)$score->metricsjson,
            'breakdownjson' => (string)$score->breakdownjson,
            'timecalculated' => (int)$score->timecalculated,
        ];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'calculated' => new external_value(PARAM_BOOL, 'Whether cached evidence exists'),
            'score' => new external_value(PARAM_FLOAT, 'Normalized 0-100 score'),
            'rawscore' => new external_value(PARAM_FLOAT, 'Raw awarded points'),
            'totalweight' => new external_value(PARAM_FLOAT, 'Active configured weight'),
            'status' => new external_value(PARAM_TEXT, 'Configured behavioral category label'),
            'metricsjson' => new external_value(PARAM_RAW, 'Normalized cached metrics JSON'),
            'breakdownjson' => new external_value(PARAM_RAW, 'Explainable rule breakdown JSON'),
            'timecalculated' => new external_value(PARAM_INT, 'Last calculation timestamp'),
        ]);
    }
}

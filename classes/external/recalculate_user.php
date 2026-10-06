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
 * Secure recalculation External API.
 *
 * @package mod_videotrackerultimate
 */
final class recalculate_user extends external_api {
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id'),
            'userid' => new external_value(PARAM_INT, 'Target user id'),
        ]);
    }

    public static function execute(int $cmid, int $userid): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), ['cmid' => $cmid, 'userid' => $userid]);
        $cm = get_coursemodule_from_id('videotrackerultimate', $params['cmid'], 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/videotrackerultimate:recalculate', $context);

        $visible = reporting::visible_users($cm, $context);
        if (!isset($visible[$params['userid']])) {
            throw new \moodle_exception('usernotavailable', 'videotrackerultimate');
        }

        score_manager::queue_user((int)$cm->id, (int)$params['userid'], 'external', (int)$USER->id);
        return ['queued' => true];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'queued' => new external_value(PARAM_BOOL, 'Whether server-side recalculation was queued'),
        ]);
    }
}

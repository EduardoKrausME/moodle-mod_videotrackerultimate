<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_videotrackerultimate\external;

use context_module;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use mod_videotrackerultimate\reporting;
use mod_videotrackerultimate\score\manager as score_manager;

/**
 * Secure recalculation External API.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class recalculate_user extends external_api {
    /**
     * Method execute_parameters.
     *
     * @return external_function_parameters Return value.
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id'),
            'userid' => new external_value(PARAM_INT, 'Target user id'),
        ]);
    }

    /**
     * Method execute.
     *
     * @param int $cmid Parameter cmid.
     * @param int $userid Parameter userid.
     * @return array Return value.
     */
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

    /**
     * Method execute_returns.
     *
     * @return external_single_structure Return value.
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'queued' => new external_value(PARAM_BOOL, 'Whether server-side recalculation was queued'),
        ]);
    }
}

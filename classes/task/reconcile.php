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

namespace mod_videotrackerultimate\task;

use context_module;
use mod_videotrackerultimate\score\manager as score_manager;

/**
 * Periodic consistency pass for cached scores.
 *
 * The task queues adhoc work and intentionally avoids calculating analytics
 * in the cron loop itself.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class reconcile extends \core\task\scheduled_task {
    /**
     * Method get_name.
     *
     * @return string Return value.
     */
    public function get_name(): string {
        return get_string('taskreconcile', 'videotrackerultimate');
    }

    /**
     * Method execute.
     *
     * @return void Return value.
     */
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

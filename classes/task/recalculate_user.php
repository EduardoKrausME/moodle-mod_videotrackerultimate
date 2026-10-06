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

defined('MOODLE_INTERNAL') || die;

/**
 * Adhoc recalculation task.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class recalculate_user extends \core\task\adhoc_task {
    /**
     * Method execute.
     *
     * @return void Return value.
     */
    public function execute(): void {
        global $DB;

        $data = $this->get_custom_data();
        $cmid = (int)($data->cmid ?? 0);
        $userid = (int)($data->userid ?? 0);
        if ($cmid <= 0 || $userid <= 0) {
            return;
        }

        $cm = get_coursemodule_from_id('videotrackerultimate', $cmid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return;
        }
        $activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance]);
        if (!$activity) {
            return;
        }
        $context = context_module::instance($cm->id);
        if (!is_enrolled($context, $userid, '', true)) {
            return;
        }

        score_manager::recalculate(
            $context,
            $activity,
            $userid,
            (string)($data->origin ?? 'adhoc'),
            (int)($data->triggeredby ?? 0)
        );
    }
}

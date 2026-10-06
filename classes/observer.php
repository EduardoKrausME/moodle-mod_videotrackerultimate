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

namespace mod_videotrackerultimate;

use mod_videotrackerultimate\score\manager as score_manager;

/**
 * Observers for shared Video Bridge telemetry.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class observer {
    /**
     * Method analytics_updated.
     *
     * @param \core\event\base $event Parameter event.
     * @return void Return value.
     */
    public static function analytics_updated(\core\event\base $event): void {
        if (($event->other['component'] ?? '') !== 'mod_videotrackerultimate') {
            return;
        }
        $cmid = (int)$event->contextinstanceid;
        $userid = (int)$event->relateduserid;
        if ($cmid > 0 && $userid > 0) {
            score_manager::queue_user($cmid, $userid, 'bridge_event');
        }
    }
}

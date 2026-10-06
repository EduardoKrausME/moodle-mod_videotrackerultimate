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

/**
 * External functions.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    'mod_videotrackerultimate_get_score' => [
        'classname' => '\mod_videotrackerultimate\external\get_score',
        'methodname' => 'execute',
        'description' => 'Returns an explainable cached Engagement Score.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/videotrackerultimate:view',
    ],
    'mod_videotrackerultimate_recalculate_user' => [
        'classname' => '\mod_videotrackerultimate\external\recalculate_user',
        'methodname' => 'execute',
        'description' => 'Queues a server-side analytics recalculation for an allowed user.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/videotrackerultimate:recalculate',
    ],
];

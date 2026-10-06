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

namespace mod_videotrackerultimate\grade;

defined('MOODLE_INTERNAL') || die;

/**
 * Gradebook integration.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class manager {
    /**
     * Method update_item.
     *
     * @param \stdClass $activity Parameter activity.
     * @return int Return value.
     */
    public static function update_item(\stdClass $activity): int {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');

        $item = [
            'itemname' => clean_param($activity->name, PARAM_NOTAGS),
            'gradetype' => !empty($activity->usegrade) ? GRADE_TYPE_VALUE : GRADE_TYPE_NONE,
            'grademin' => 0,
            'grademax' => max(0.00001, (float)$activity->grademax),
        ];
        return grade_update(
            'mod/videotrackerultimate',
            (int)$activity->course,
            'mod',
            'videotrackerultimate',
            (int)$activity->id,
            0,
            null,
            $item
        );
    }

    /**
     * Method update_user.
     *
     * @param \stdClass $activity Parameter activity.
     * @param int $userid Parameter userid.
     * @param float $score Parameter score.
     * @param string $origin Parameter origin.
     * @return int Return value.
     */
    public static function update_user(\stdClass $activity, int $userid, float $score, string $origin): int {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');

        if (empty($activity->usegrade)) {
            return GRADE_UPDATE_OK;
        }

        $rawgrade = round((max(0, min(100, $score)) / 100) * (float)$activity->grademax, 5);
        return grade_update(
            'mod/videotrackerultimate',
            (int)$activity->course,
            'mod',
            'videotrackerultimate',
            (int)$activity->id,
            0,
            [(object)[
                'userid' => $userid,
                'rawgrade' => $rawgrade,
                'feedback' => get_string('gradefeedbackorigin', 'videotrackerultimate', $origin),
                'feedbackformat' => FORMAT_PLAIN,
            ]],
            [
                'itemname' => clean_param($activity->name, PARAM_NOTAGS),
                'gradetype' => GRADE_TYPE_VALUE,
                'grademin' => 0,
                'grademax' => (float)$activity->grademax,
            ]
        );
    }

    /**
     * Method delete_item.
     *
     * @param \stdClass $activity Parameter activity.
     * @return int Return value.
     */
    public static function delete_item(\stdClass $activity): int {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');

        return grade_update(
            'mod/videotrackerultimate',
            (int)$activity->course,
            'mod',
            'videotrackerultimate',
            (int)$activity->id,
            0,
            null,
            ['deleted' => 1]
        );
    }
}

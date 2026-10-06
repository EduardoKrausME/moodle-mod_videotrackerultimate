<?php
namespace mod_videotrackerultimate\grade;

defined('MOODLE_INTERNAL') || die;

/**
 * Gradebook integration.
 *
 * @package mod_videotrackerultimate
 */
final class manager {
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

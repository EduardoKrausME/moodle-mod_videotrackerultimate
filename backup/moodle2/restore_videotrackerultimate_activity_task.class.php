<?php
defined('MOODLE_INTERNAL') || die;

/**
 * Restore task.
 *
 * @package mod_videotrackerultimate
 */
final class restore_videotrackerultimate_activity_task extends restore_activity_task {
    protected function define_my_settings(): void {
    }

    protected function define_my_steps(): void {
        $this->add_step(new restore_videotrackerultimate_activity_structure_step(
            'videotrackerultimate_structure',
            'videotrackerultimate.xml'
        ));
    }

    public static function define_decode_contents(): array {
        return [
            new restore_decode_content('videotrackerultimate', ['intro'], 'videotrackerultimate'),
        ];
    }

    public static function define_decode_rules(): array {
        return [
            new restore_decode_rule('VIDEOTRACKERULTIMATEVIEWBYID', '/mod/videotrackerultimate/view.php?id=$1', 'course_module'),
            new restore_decode_rule('VIDEOTRACKERULTIMATEINDEX', '/mod/videotrackerultimate/index.php?id=$1', 'course'),
        ];
    }
}

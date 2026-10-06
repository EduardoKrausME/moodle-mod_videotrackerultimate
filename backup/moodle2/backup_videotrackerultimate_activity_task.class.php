<?php
defined('MOODLE_INTERNAL') || die;

/**
 * Backup task.
 *
 * @package mod_videotrackerultimate
 */
final class backup_videotrackerultimate_activity_task extends backup_activity_task {
    protected function define_my_settings(): void {
    }

    protected function define_my_steps(): void {
        $this->add_step(new backup_videotrackerultimate_activity_structure_step(
            'videotrackerultimate_structure',
            'videotrackerultimate.xml'
        ));
    }

    public static function encode_content_links($content): string {
        global $CFG;
        $base = preg_quote($CFG->wwwroot . '/mod/videotrackerultimate/index.php?id=', '/');
        $content = preg_replace(
            "/({$base})([0-9]+)/",
            '$@VIDEOTRACKERULTIMATEINDEX*$2@$',
            $content
        );
        $base = preg_quote($CFG->wwwroot . '/mod/videotrackerultimate/view.php?id=', '/');
        return preg_replace(
            "/({$base})([0-9]+)/",
            '$@VIDEOTRACKERULTIMATEVIEWBYID*$2@$',
            $content
        );
    }
}

<?php
defined('MOODLE_INTERNAL') || die;

/**
 * Restore structure.
 *
 * @package mod_videotrackerultimate
 */
final class restore_videotrackerultimate_activity_structure_step extends restore_activity_structure_step {
    protected function define_structure(): array {
        return [
            new restore_path_element('videotrackerultimate', '/activity/videotrackerultimate'),
            new restore_path_element('videotrackerultimate_indicator', '/activity/videotrackerultimate/indicators/indicator'),
        ];
    }

    protected function process_videotrackerultimate(array $data): void {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $newid = $DB->insert_record('videotrackerultimate', $data);
        $this->apply_activity_instance($newid);
    }

    protected function process_videotrackerultimate_indicator(array $data): void {
        global $DB;

        $data = (object)$data;
        $data->ultimateid = $this->get_new_parentid('videotrackerultimate');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $DB->insert_record('videotrackerultimate_ind', $data);
    }

    protected function after_execute(): void {
        $this->add_related_files('mod_videotrackerultimate', 'intro', null);
        $this->add_related_files('local_video_bridge', 'video', 0);
    }
}

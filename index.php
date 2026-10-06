<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_course_login($course);

$PAGE->set_url('/mod/videotrackerultimate/index.php', ['id' => $course->id]);
$PAGE->set_title(get_string('modulenameplural', 'videotrackerultimate'));
$PAGE->set_heading(format_string($course->fullname));

$instances = get_all_instances_in_course('videotrackerultimate', $course);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'videotrackerultimate'));

$table = new html_table();
$table->head = [get_string('name')];
foreach ($instances as $instance) {
    $table->data[] = [
        html_writer::link(
            new moodle_url('/mod/videotrackerultimate/view.php', ['id' => $instance->coursemodule]),
            format_string($instance->name)
        ),
    ];
}
echo html_writer::table($table);
echo $OUTPUT->footer();

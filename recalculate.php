<?php
use mod_videotrackerultimate\reporting;
use mod_videotrackerultimate\score\manager as score_manager;

require('../../config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new moodle_exception('invalidrequest', 'error');
}

$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$cm = get_coursemodule_from_id('videotrackerultimate', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videotrackerultimate:recalculate', $context);
require_sesskey();

$visible = reporting::visible_users($cm, $context);
$count = 0;
if ($userid > 0) {
    if (!isset($visible[$userid])) {
        throw new moodle_exception('usernotavailable', 'videotrackerultimate');
    }
    score_manager::queue_user((int)$cm->id, $userid, 'manual', (int)$USER->id);
    $count = 1;
} else {
    foreach ($visible as $user) {
        score_manager::queue_user((int)$cm->id, (int)$user->id, 'manual', (int)$USER->id);
        $count++;
    }
}

redirect(
    new moodle_url('/mod/videotrackerultimate/report.php', ['id' => $cm->id]),
    get_string('recalculationqueued', 'videotrackerultimate', $count)
);

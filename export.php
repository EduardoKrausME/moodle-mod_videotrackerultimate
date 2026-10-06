<?php
use mod_videotrackerultimate\reporting;

require('../../config.php');
require_once($CFG->libdir . '/csvlib.class.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('videotrackerultimate', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videotrackerultimate:export', $context);
require_sesskey();

$rows = reporting::rows($cm, $activity, $context);
$csv = new csv_export_writer();
$csv->set_filename(clean_filename($activity->name . '-engagement-score'));
$csv->add_data([
    get_string('fullname'),
    get_string('email'),
    get_string('engagementscore', 'videotrackerultimate'),
    get_string('status', 'videotrackerultimate'),
    get_string('percentwatched', 'videotrackerultimate'),
    get_string('effectivetime', 'videotrackerultimate'),
    get_string('sessions', 'videotrackerultimate'),
    get_string('seeks', 'videotrackerultimate'),
    get_string('replays', 'videotrackerultimate'),
    get_string('maxrate', 'videotrackerultimate'),
    get_string('lastupdatedlabel', 'videotrackerultimate'),
]);
foreach ($rows as $row) {
    $csv->add_data([
        fullname($row->user),
        $row->user->email,
        $row->score ? (float)$row->score->score : '',
        $row->statuslabel,
        $row->metrics['percent'] ?? '',
        $row->metrics['playbackTime'] ?? '',
        $row->metrics['sessions'] ?? '',
        $row->metrics['seekCount'] ?? '',
        $row->metrics['replayCount'] ?? '',
        $row->metrics['maxRate'] ?? '',
        $row->score ? userdate((int)$row->score->timecalculated) : '',
    ]);
}
$csv->download_file();

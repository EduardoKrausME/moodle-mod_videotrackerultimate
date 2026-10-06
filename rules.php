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
 * rules.php
 *
 * @package   mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('videotrackerultimate', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videotrackerultimate:manageindicators', $context);

$PAGE->set_url('/mod/videotrackerultimate/rules.php', ['id' => $cm->id]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('indicators', 'videotrackerultimate'));
$PAGE->set_heading(format_string($activity->name));

$indicators = $DB->get_records('videotrackerultimate_ind', ['ultimateid' => $activity->id], 'sortorder ASC, id ASC');
$total = 0.0;
foreach ($indicators as $indicator) {
    if ($indicator->enabled) {
        $total += (float)$indicator->weight;
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('indicators', 'videotrackerultimate'));
echo html_writer::div(get_string('scoreexplanation', 'videotrackerultimate'), 'alert alert-info');

if (abs($total - 100) > 0.001) {
    echo $OUTPUT->notification(
        get_string('weightwarning', 'videotrackerultimate', format_float($total, 2)),
        \core\output\notification::NOTIFY_WARNING
    );
} else {
    echo $OUTPUT->notification(get_string('weightcomplete', 'videotrackerultimate'), \core\output\notification::NOTIFY_SUCCESS);
}

echo html_writer::div(
    html_writer::link(
        new moodle_url('/mod/videotrackerultimate/indicator.php', ['id' => $cm->id]),
        get_string('addindicator', 'videotrackerultimate'),
        ['class' => 'btn btn-primary']
    ),
    'mb-3'
);

$table = new html_table();
$table->head = [
    get_string('indicatorname', 'videotrackerultimate'),
    get_string('ruletype', 'videotrackerultimate'),
    get_string('indicatorweight', 'videotrackerultimate'),
    get_string('rulelimit', 'videotrackerultimate'),
    get_string('indicatorenabled', 'videotrackerultimate'),
    get_string('requiredcompletion', 'videotrackerultimate'),
    get_string('actions'),
];
foreach ($indicators as $indicator) {
    $config = json_decode((string)$indicator->configjson, true) ?: [];
    $limit = $indicator->ruletype === \mod_videotrackerultimate\rule\engine::REACHED_END
        ? '—'
        : ($indicator->ruletype === \mod_videotrackerultimate\rule\engine::SEGMENT_WATCHED
            ? get_string('segmentdisplay', 'videotrackerultimate', (object)$config)
            : format_float((float)$indicator->limitvalue, 3));

    $edit = html_writer::link(
        new moodle_url('/mod/videotrackerultimate/indicator.php', [
            'id' => $cm->id,
            'indicatorid' => $indicator->id,
        ]),
        get_string('edit')
    );
    $deleteform = html_writer::start_tag('form', [
        'method' => 'post',
        'action' => (new moodle_url('/mod/videotrackerultimate/indicator.php'))->out(false),
        'class' => 'd-inline ms-2',
    ]);
    $deleteform .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cm->id]);
    $deleteform .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'indicatorid', 'value' => $indicator->id]);
    $deleteform .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'delete']);
    $deleteform .= html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    $deleteform .= html_writer::tag('button', get_string('delete'), ['type' => 'submit', 'class' => 'btn btn-link p-0']);
    $deleteform .= html_writer::end_tag('form');

    $table->data[] = [
        format_string($indicator->name),
        get_string('ruletype:' . $indicator->ruletype, 'videotrackerultimate'),
        format_float((float)$indicator->weight, 2),
        $limit,
        $indicator->enabled ? get_string('yes') : get_string('no'),
        $indicator->requiredcompletion ? get_string('yes') : get_string('no'),
        $edit . $deleteform,
    ];
}
echo html_writer::table($table);
echo html_writer::link(
    new moodle_url('/mod/videotrackerultimate/view.php', ['id' => $cm->id]),
    get_string('backtoactivity', 'videotrackerultimate')
);
echo $OUTPUT->footer();

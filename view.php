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
 * view.php
 *
 * @package   mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_video_bridge\analytics;
use mod_videotrackerultimate\score\manager as score_manager;
use mod_videotrackerultimate\source_manager;

require('../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('videotrackerultimate', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videotrackerultimate:view', $context);

$PAGE->set_url('/mod/videotrackerultimate/view.php', ['id' => $cm->id]);
$PAGE->set_context($context);
$PAGE->set_title(format_string($activity->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->add_body_class('mod-videotrackerultimate');

$completion = new completion_info($course);
if ($completion->is_enabled($cm)) {
    $completion->set_module_viewed($cm);
}

$bridge = source_manager::create();
$player = $bridge->get_player_config($activity, $context, analytics::LEVEL_DETAILED);
$rootid = 'videotrackerultimate-player-' . $cm->id;
$sourcehtml = $OUTPUT->render_from_template($player['sourcetemplate'], $player);
$PAGE->requires->js_call_amd('mod_videotrackerultimate/player', 'init', [$rootid, $player]);

$score = score_manager::get_cached((int)$activity->id, (int)$USER->id);
if (!$score && !has_capability('mod/videotrackerultimate:viewreport', $context)) {
    score_manager::queue_user((int)$cm->id, (int)$USER->id, 'first_view');
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($activity->name));
if (trim((string)$activity->intro) !== '') {
    echo format_module_intro('videotrackerultimate', $activity, $cm->id);
}

echo html_writer::start_div('videotrackerultimate-player card mb-4', ['id' => $rootid]);
echo html_writer::div($sourcehtml, 'card-body');
echo html_writer::end_div();

if ($score) {
    $metrics = json_decode((string)$score->metricsjson, true) ?: [];
    $breakdown = json_decode((string)$score->breakdownjson, true) ?: [];

    echo html_writer::start_div('videotrackerultimate-score card mb-4');
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h3', get_string('engagementscore', 'videotrackerultimate'));
    echo html_writer::div(
        format_float((float)$score->score, 2) . '/100',
        'videotrackerultimate-score-value'
    );
    echo html_writer::div(
        s(score_manager::status_label($activity, (string)$score->statuscode)),
        'videotrackerultimate-status'
    );
    if (abs((float)$score->totalweight - 100) > 0.001) {
        echo html_writer::div(get_string('normalizedscoreexplain', 'videotrackerultimate', (object)[
            'raw' => format_float((float)$score->rawscore, 2),
            'total' => format_float((float)$score->totalweight, 2),
            'score' => format_float((float)$score->score, 2),
        ]), 'text-muted');
    }

    $table = new html_table();
    $table->head = [
        get_string('indicator', 'videotrackerultimate'),
        get_string('evidence', 'videotrackerultimate'),
        get_string('points', 'videotrackerultimate'),
    ];
    foreach ($breakdown as $item) {
        $table->data[] = [
            s((string)$item['name']),
            s(get_string('ruleevidence', 'videotrackerultimate', (object)[
                'actual' => is_bool($item['actual']) ? ($item['actual'] ? get_string('yes') : get_string('no')) : $item['actual'],
                'limit' => $item['limit'],
            ])),
            format_float((float)$item['points'], 2) . '/' . format_float((float)$item['weight'], 2),
        ];
    }
    echo html_writer::table($table);
    echo html_writer::div(
        get_string('lastupdated', 'videotrackerultimate', userdate((int)$score->timecalculated)),
        'text-muted small'
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
} else {
    echo $OUTPUT->notification(
        get_string('scorenotyetcalculated', 'videotrackerultimate'),
        \core\output\notification::NOTIFY_INFO
    );
}

if (has_capability('mod/videotrackerultimate:viewreport', $context)
        || has_capability('mod/videotrackerultimate:manageindicators', $context)) {
    $links = [];
    if (has_capability('mod/videotrackerultimate:viewreport', $context)) {
        $links[] = html_writer::link(
            new moodle_url('/mod/videotrackerultimate/report.php', ['id' => $cm->id]),
            get_string('reports', 'videotrackerultimate'),
            ['class' => 'btn btn-secondary']
        );
    }
    if (has_capability('mod/videotrackerultimate:manageindicators', $context)) {
        $links[] = html_writer::link(
            new moodle_url('/mod/videotrackerultimate/rules.php', ['id' => $cm->id]),
            get_string('indicators', 'videotrackerultimate'),
            ['class' => 'btn btn-secondary']
        );
    }
    if (has_capability('mod/videotrackerultimate:recalculate', $context)) {
        $links[] = html_writer::start_tag('form', [
            'method' => 'post',
            'action' => (new moodle_url('/mod/videotrackerultimate/recalculate.php'))->out(false),
            'class' => 'd-inline',
        ])
        . html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cm->id])
        . html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()])
        . html_writer::tag('button', get_string('recalculateanalytics', 'videotrackerultimate'), [
            'type' => 'submit',
            'class' => 'btn btn-secondary',
        ])
        . html_writer::end_tag('form');
    }
    echo html_writer::div(implode(' ', $links), 'd-flex gap-2 flex-wrap');
}

echo $OUTPUT->footer();

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
 * report.php
 *
 * @package   mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_video_bridge\analytics;
use mod_videotrackerultimate\reporting;
use mod_videotrackerultimate\score\manager as score_manager;

require('../../config.php');

$id = required_param('id', PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);

$cm = get_coursemodule_from_id('videotrackerultimate', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videotrackerultimate:viewreport', $context);

$PAGE->set_url('/mod/videotrackerultimate/report.php', ['id' => $cm->id, 'userid' => $userid ?: null]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('reports', 'videotrackerultimate'));
$PAGE->set_heading(format_string($activity->name));
$PAGE->add_body_class('mod-videotrackerultimate');

$visible = reporting::visible_users($cm, $context);

echo $OUTPUT->header();

if ($userid > 0) {
    if (!isset($visible[$userid])) {
        throw new moodle_exception('usernotavailable', 'videotrackerultimate');
    }

    $user = $visible[$userid];
    $score = score_manager::get_cached((int)$activity->id, $userid);
    echo $OUTPUT->heading(get_string('individualreport', 'videotrackerultimate', fullname($user)));

    echo html_writer::div(
        html_writer::link(
            new moodle_url('/mod/videotrackerultimate/report.php', ['id' => $cm->id]),
            get_string('backtooverview', 'videotrackerultimate')
        ),
        'mb-3'
    );

    if (!$score) {
        echo $OUTPUT->notification(get_string('notcalculated', 'videotrackerultimate'), \core\output\notification::NOTIFY_INFO);
        if (has_capability('mod/videotrackerultimate:recalculate', $context)) {
            echo html_writer::start_tag('form', [
                'method' => 'post',
                'action' => (new moodle_url('/mod/videotrackerultimate/recalculate.php'))->out(false),
            ]);
            echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cm->id]);
            echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'userid', 'value' => $userid]);
            echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
            echo html_writer::tag('button', get_string('recalculateanalytics', 'videotrackerultimate'), [
                'type' => 'submit', 'class' => 'btn btn-primary',
            ]);
            echo html_writer::end_tag('form');
        }
        echo $OUTPUT->footer();
        exit;
    }

    $metrics = json_decode((string)$score->metricsjson, true) ?: [];
    $breakdown = json_decode((string)$score->breakdownjson, true) ?: [];

    echo html_writer::start_div('vtu-kpis');
    foreach ([
        get_string('engagementscore', 'videotrackerultimate') => format_float((float)$score->score, 2) . '/100',
        get_string('percentwatched', 'videotrackerultimate') => ((int)($metrics['percent'] ?? 0)) . '%',
        get_string('effectivetime', 'videotrackerultimate') => format_time((int)($metrics['playbackTime'] ?? 0)),
        get_string('sessions', 'videotrackerultimate') => (int)($metrics['sessions'] ?? 0),
        get_string('seeks', 'videotrackerultimate') => (int)($metrics['seekCount'] ?? 0),
        get_string('replays', 'videotrackerultimate') => (int)($metrics['replayCount'] ?? 0),
    ] as $label => $value) {
        echo html_writer::start_div('vtu-kpi');
        echo html_writer::div(s((string)$label), 'text-muted');
        echo html_writer::div(s((string)$value), 'vtu-kpi-value');
        echo html_writer::end_div();
    }
    echo html_writer::end_div();

    echo html_writer::tag('h3', get_string('scorecomposition', 'videotrackerultimate'));
    $table = new html_table();
    $table->head = [
        get_string('indicator', 'videotrackerultimate'),
        get_string('rule', 'videotrackerultimate'),
        get_string('actual', 'videotrackerultimate'),
        get_string('points', 'videotrackerultimate'),
        get_string('state', 'videotrackerultimate'),
    ];
    foreach ($breakdown as $item) {
        $table->data[] = [
            s((string)$item['name']),
            get_string('ruletype:' . $item['type'], 'videotrackerultimate'),
            is_bool($item['actual'])
                ? ($item['actual'] ? get_string('yes') : get_string('no'))
                : s((string)$item['actual']),
            format_float((float)$item['points'], 2) . '/' . format_float((float)$item['weight'], 2),
            !empty($item['passed'])
                ? get_string('rulemet', 'videotrackerultimate')
                : get_string('rulepending', 'videotrackerultimate'),
        ];
    }
    echo html_writer::table($table);

    $duration = (int)($metrics['duration'] ?? 0);
    $ranges = is_array($metrics['watchedRanges'] ?? null) ? $metrics['watchedRanges'] : [];
    echo html_writer::tag('h3', get_string('watchedmap', 'videotrackerultimate'));
    if ($duration > 0) {
        echo html_writer::start_div('vtu-range-map', [
            'role' => 'img',
            'aria-label' => get_string('watchedmap', 'videotrackerultimate'),
        ]);
        foreach ($ranges as $range) {
            if (!is_array($range) || count($range) < 2) {
                continue;
            }
            $left = max(0, min(100, ((float)$range[0] / $duration) * 100));
            $right = max($left, min(100, ((float)$range[1] / $duration) * 100));
            echo html_writer::div('', 'vtu-range', [
                'style' => 'left:' . format_float($left, 3, true) . '%;width:' . format_float($right - $left, 3, true) . '%',
                'title' => format_time((int)$range[0]) . ' – ' . format_time((int)$range[1]),
            ]);
        }
        echo html_writer::end_div();
    } else {
        echo html_writer::div(get_string('noduration', 'videotrackerultimate'), 'text-muted');
    }

    echo html_writer::tag('h3', get_string('sessions', 'videotrackerultimate'), ['class' => 'mt-4']);
    $mediahash = analytics::media_hash((string)$activity->videosource, (string)$activity->sourceconfig);
    $sessions = analytics::get_session_metrics(
        $context->id,
        'mod_videotrackerultimate',
        (int)$activity->id,
        $mediahash,
        $userid
    );
    $sessiontable = new html_table();
    $sessiontable->head = [
        get_string('sessionstart', 'videotrackerultimate'),
        get_string('sessionend', 'videotrackerultimate'),
        get_string('effectivetime', 'videotrackerultimate'),
        get_string('seeks', 'videotrackerultimate'),
        get_string('replays', 'videotrackerultimate'),
        get_string('maxposition', 'videotrackerultimate'),
    ];
    foreach ($sessions as $session) {
        $sessiontable->data[] = [
            userdate((int)$session->startedat),
            (int)$session->endedat > 0 ? userdate((int)$session->endedat) : '—',
            format_time((int)$session->watchtime),
            (int)$session->seeks,
            (int)$session->replays,
            format_time((int)$session->maxposition),
        ];
    }
    echo html_writer::table($sessiontable);

    echo html_writer::div(
        get_string('lastupdated', 'videotrackerultimate', userdate((int)$score->timecalculated))
        . ' · ' . get_string('calculationorigin', 'videotrackerultimate', s((string)$score->origin)),
        'text-muted small mt-3'
    );

    if (has_capability('mod/videotrackerultimate:recalculate', $context)) {
        echo html_writer::start_tag('form', [
            'method' => 'post',
            'action' => (new moodle_url('/mod/videotrackerultimate/recalculate.php'))->out(false),
            'class' => 'mt-3',
        ]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cm->id]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'userid', 'value' => $userid]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::tag('button', get_string('recalculateanalytics', 'videotrackerultimate'), [
            'type' => 'submit', 'class' => 'btn btn-secondary',
        ]);
        echo html_writer::end_tag('form');
    }

    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->heading(get_string('classoverview', 'videotrackerultimate'));
if (groups_get_activity_groupmode($cm) != NOGROUPS) {
    groups_print_activity_menu($cm, new moodle_url('/mod/videotrackerultimate/report.php', ['id' => $cm->id]));
}

$rows = reporting::rows($cm, $activity, $context);
$summary = reporting::summary($rows, $activity);

echo html_writer::start_div('vtu-kpis');
$kpis = [
    get_string('averagescore', 'videotrackerultimate') => format_float((float)$summary['averagescore'], 2),
    get_string('averagepercent', 'videotrackerultimate') => format_float((float)$summary['averagepercent'], 2) . '%',
    get_string('averagewatchtime', 'videotrackerultimate') => format_time((int)$summary['averagewatchtime']),
    get_string('completion', 'completion') => $summary['completed'] . '/' . $summary['calculated'],
    get_string('belowthreshold', 'videotrackerultimate') => (int)$summary['belowthreshold'],
];
foreach ($kpis as $label => $value) {
    echo html_writer::start_div('vtu-kpi');
    echo html_writer::div(s((string)$label), 'text-muted');
    echo html_writer::div(s((string)$value), 'vtu-kpi-value');
    echo html_writer::end_div();
}
echo html_writer::end_div();

echo html_writer::tag('h3', get_string('scoredistribution', 'videotrackerultimate'));
$distribution = new html_table();
$distribution->head = [get_string('status', 'videotrackerultimate'), get_string('users')];
foreach ([
    'excellent' => $activity->excellentlabel,
    'adequate' => $activity->adequatelabel,
    'attention' => $activity->attentionlabel,
    'insufficient' => $activity->insufficientlabel,
] as $code => $label) {
    $distribution->data[] = [s($label), (int)($summary['distribution'][$code] ?? 0)];
}
echo html_writer::table($distribution);

echo html_writer::tag('h3', get_string('frequentfailures', 'videotrackerultimate'));
if ($summary['failures']) {
    $failuretable = new html_table();
    $failuretable->head = [get_string('indicator', 'videotrackerultimate'), get_string('users')];
    foreach ($summary['failures'] as $failure) {
        $failuretable->data[] = [s($failure['name']), (int)$failure['count']];
    }
    echo html_writer::table($failuretable);
} else {
    echo html_writer::div(get_string('nofailures', 'videotrackerultimate'), 'text-muted');
}

$showranking = !empty($activity->rankingenabled) && has_capability('mod/videotrackerultimate:viewranking', $context);
if ($showranking) {
    usort($rows, static function($a, $b): int {
        $ascore = $a->score ? (float)$a->score->score : -1;
        $bscore = $b->score ? (float)$b->score->score : -1;
        return $bscore <=> $ascore;
    });
} else {
    usort($rows, static fn($a, $b): int => strcasecmp(fullname($a->user), fullname($b->user)));
}

echo html_writer::tag('h3', $showranking
    ? get_string('teacherranking', 'videotrackerultimate')
    : get_string('learners', 'videotrackerultimate'));

$table = new html_table();
$table->head = array_filter([
    $showranking ? get_string('rank', 'videotrackerultimate') : null,
    get_string('fullname'),
    get_string('engagementscore', 'videotrackerultimate'),
    get_string('status', 'videotrackerultimate'),
    get_string('percentwatched', 'videotrackerultimate'),
    get_string('effectivetime', 'videotrackerultimate'),
    get_string('lastupdatedlabel', 'videotrackerultimate'),
]);
$position = 0;
foreach ($rows as $row) {
    $position++;
    $cells = [];
    if ($showranking) {
        $cells[] = $position;
    }
    $cells[] = html_writer::link(
        new moodle_url('/mod/videotrackerultimate/report.php', ['id' => $cm->id, 'userid' => $row->user->id]),
        fullname($row->user)
    );
    $cells[] = $row->score ? format_float((float)$row->score->score, 2) : '—';
    $cells[] = s($row->statuslabel);
    $cells[] = isset($row->metrics['percent']) ? ((int)$row->metrics['percent'] . '%') : '—';
    $cells[] = isset($row->metrics['playbackTime']) ? format_time((int)$row->metrics['playbackTime']) : '—';
    $cells[] = $row->score ? userdate((int)$row->score->timecalculated) : '—';
    $table->data[] = $cells;
}
echo html_writer::table($table);

$actions = [];
if (has_capability('mod/videotrackerultimate:recalculate', $context)) {
    $actions[] = html_writer::start_tag('form', [
        'method' => 'post',
        'action' => (new moodle_url('/mod/videotrackerultimate/recalculate.php'))->out(false),
        'class' => 'd-inline',
    ])
    . html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $cm->id])
    . html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()])
    . html_writer::tag('button', get_string('recalculateanalytics', 'videotrackerultimate'), [
        'type' => 'submit', 'class' => 'btn btn-secondary',
    ])
    . html_writer::end_tag('form');
}
if (has_capability('mod/videotrackerultimate:export', $context)) {
    $actions[] = html_writer::link(
        new moodle_url('/mod/videotrackerultimate/export.php', ['id' => $cm->id, 'sesskey' => sesskey()]),
        get_string('exportcsv', 'videotrackerultimate'),
        ['class' => 'btn btn-secondary']
    );
}
if (has_capability('mod/videotrackerultimate:manageindicators', $context)) {
    $actions[] = html_writer::link(
        new moodle_url('/mod/videotrackerultimate/rules.php', ['id' => $cm->id]),
        get_string('indicators', 'videotrackerultimate'),
        ['class' => 'btn btn-secondary']
    );
}
echo html_writer::div(implode(' ', $actions), 'd-flex gap-2 flex-wrap mt-3');
echo $OUTPUT->footer();

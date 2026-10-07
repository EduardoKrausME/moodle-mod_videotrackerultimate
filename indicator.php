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
 * indicator.php
 *
 * @package   mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_videotrackerultimate\form\indicator_form;
use mod_videotrackerultimate\rule\engine;
use mod_videotrackerultimate\score\manager as score_manager;

require('../../config.php');

$id = required_param('id', PARAM_INT);
$indicatorid = optional_param('indicatorid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

$cm = get_coursemodule_from_id('videotrackerultimate', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/videotrackerultimate:manageindicators', $context);

$indicator = null;
if ($indicatorid) {
    $indicator = $DB->get_record('videotrackerultimate_ind', [
        'id' => $indicatorid,
        'ultimateid' => $activity->id,
    ], '*', MUST_EXIST);
}

if ($action === 'delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new moodle_exception('invalidrequest', 'error');
    }
    require_sesskey();
    if (!$indicator) {
        throw new moodle_exception('invalidrecord', 'error');
    }
    $DB->delete_records('videotrackerultimate_ind', ['id' => $indicator->id]);
    score_manager::queue_activity($cm, 'rule_change', (int)$USER->id);
    redirect(
        new moodle_url('/mod/videotrackerultimate/rules.php', ['id' => $cm->id]),
        get_string('indicatordeleted', 'videotrackerultimate')
    );
}

$PAGE->set_url('/mod/videotrackerultimate/indicator.php', ['id' => $cm->id, 'indicatorid' => $indicatorid]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('indicator', 'videotrackerultimate'));
$PAGE->set_heading(format_string($activity->name));

$form = new indicator_form(null, [
    'cmid' => $cm->id,
    'activityid' => $activity->id,
    'indicator' => $indicator,
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/videotrackerultimate/rules.php', ['id' => $cm->id]));
}

if ($data = $form->get_data()) {
    require_sesskey();

    $config = [];
    if ($data->ruletype === engine::SEGMENT_WATCHED) {
        $config = engine::validate($data->ruletype, 0, [
            'start' => (float)$data->segmentstart,
            'end' => (float)$data->segmentend,
            'mincoverage' => (float)$data->segmentcoverage,
        ]);
    } else {
        engine::validate($data->ruletype, (float)($data->limitvalue ?? 0));
    }

    $now = time();
    $record = (object)[
        'ultimateid' => $activity->id,
        'name' => clean_param($data->name, PARAM_TEXT),
        'ruletype' => clean_param($data->ruletype, PARAM_ALPHANUMEXT),
        'weight' => round((float)$data->weight, 2),
        'limitvalue' => $data->ruletype === engine::REACHED_END || $data->ruletype === engine::SEGMENT_WATCHED
            ? 0
            : (float)$data->limitvalue,
        'configjson' => json_encode($config, JSON_THROW_ON_ERROR),
        'enabled' => !empty($data->enabled) ? 1 : 0,
        'requiredcompletion' => !empty($data->requiredcompletion) ? 1 : 0,
        'sortorder' => $indicator ? (int)$indicator->sortorder : ((int)$DB->get_field_sql(
            'SELECT COALESCE(MAX(sortorder), 0) FROM {videotrackerultimate_ind} WHERE ultimateid = :id',
            ['id' => $activity->id]
        ) + 10),
        'timecreated' => $indicator ? (int)$indicator->timecreated : $now,
        'timemodified' => $now,
    ];

    if ($indicator) {
        $record->id = $indicator->id;
        $DB->update_record('videotrackerultimate_ind', $record);
    } else {
        $record->id = $DB->insert_record('videotrackerultimate_ind', $record);
    }

    score_manager::queue_activity($cm, 'rule_change', (int)$USER->id);
    redirect(
        new moodle_url('/mod/videotrackerultimate/rules.php', ['id' => $cm->id]),
        get_string('indicatorsaved', 'videotrackerultimate')
    );
}

$heading = $indicator
    ? get_string('editindicator', 'videotrackerultimate')
    : get_string('addindicator', 'videotrackerultimate');

echo $OUTPUT->header();
echo $OUTPUT->heading($heading);
$form->display();
echo $OUTPUT->footer();

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

use mod_videotrackerultimate\grade\manager as grade_manager;
use mod_videotrackerultimate\score\manager as score_manager;
use mod_videotrackerultimate\source_manager;

/**
 * Core callbacks for Video Tracker Ultimate.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function videotrackerultimate_supports($feature) {
    return match ($feature) {
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_OTHER,
        FEATURE_GROUPS,
        FEATURE_GROUPINGS,
        FEATURE_MOD_INTRO,
        FEATURE_SHOW_DESCRIPTION,
        FEATURE_COMPLETION_TRACKS_VIEWS,
        FEATURE_COMPLETION_HAS_RULES,
        FEATURE_GRADE_HAS_GRADE,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        default => null,
    };
}

function videotrackerultimate_add_instance(stdClass $data, ?mod_videotrackerultimate_mod_form $mform = null): int {
    global $DB;

    $now = time();
    $data->timecreated = $now;
    $data->timemodified = $now;
    source_manager::create()->normalise_record($data);
    $id = $DB->insert_record('videotrackerultimate', $data);
    $data->id = $id;

    $context = context_module::instance((int)$data->coursemodule);
    source_manager::create()->save_files($data, $context);
    grade_manager::update_item($data);
    return $id;
}

function videotrackerultimate_update_instance(stdClass $data, ?mod_videotrackerultimate_mod_form $mform = null): bool {
    global $DB, $USER;

    $data->id = $data->instance;
    $data->timemodified = time();
    $old = $DB->get_record('videotrackerultimate', ['id' => $data->id], '*', MUST_EXIST);
    source_manager::create()->normalise_record($data);
    $result = $DB->update_record('videotrackerultimate', $data);

    $context = context_module::instance((int)$data->coursemodule);
    source_manager::create()->save_files($data, $context, (string)$old->videosource);
    $activity = $DB->get_record('videotrackerultimate', ['id' => $data->id], '*', MUST_EXIST);
    grade_manager::update_item($activity);

    $cm = get_coursemodule_from_id('videotrackerultimate', (int)$data->coursemodule, 0, false, MUST_EXIST);
    score_manager::queue_activity($cm, 'config_change', (int)$USER->id);
    return $result;
}

function videotrackerultimate_delete_instance(int $id): bool {
    global $DB;

    $activity = $DB->get_record('videotrackerultimate', ['id' => $id]);
    if (!$activity) {
        return false;
    }

    $cm = get_coursemodule_from_instance('videotrackerultimate', $id, $activity->course, false, IGNORE_MISSING);
    if ($cm) {
        source_manager::create()->delete_files(context_module::instance($cm->id));
    }
    grade_manager::delete_item($activity);

    $transaction = $DB->start_delegated_transaction();
    $DB->delete_records('videotrackerultimate_log', ['ultimateid' => $id]);
    $DB->delete_records('videotrackerultimate_score', ['ultimateid' => $id]);
    $DB->delete_records('videotrackerultimate_ind', ['ultimateid' => $id]);
    $DB->delete_records('videotrackerultimate', ['id' => $id]);
    $transaction->allow_commit();
    return true;
}

function videotrackerultimate_grade_item_update(stdClass $activity, $grades = null): int {
    if ($grades !== null && !empty($activity->usegrade)) {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');
        return grade_update(
            'mod/videotrackerultimate',
            (int)$activity->course,
            'mod',
            'videotrackerultimate',
            (int)$activity->id,
            0,
            $grades,
            [
                'itemname' => clean_param($activity->name, PARAM_NOTAGS),
                'gradetype' => GRADE_TYPE_VALUE,
                'grademin' => 0,
                'grademax' => (float)$activity->grademax,
            ]
        );
    }
    return grade_manager::update_item($activity);
}

function videotrackerultimate_grade_item_delete(stdClass $activity): int {
    return grade_manager::delete_item($activity);
}

function videotrackerultimate_update_grades(stdClass $activity, int $userid = 0, bool $nullifnone = true): void {
    global $DB;

    if (empty($activity->usegrade)) {
        grade_manager::update_item($activity);
        return;
    }
    $params = ['ultimateid' => $activity->id];
    if ($userid > 0) {
        $params['userid'] = $userid;
    }
    $scores = $DB->get_records('videotrackerultimate_score', $params);
    foreach ($scores as $score) {
        grade_manager::update_user($activity, (int)$score->userid, (float)$score->score, 'grade_sync');
    }
}

function videotrackerultimate_get_coursemodule_info(stdClass $cm): ?cached_cm_info {
    global $DB;

    $activity = $DB->get_record(
        'videotrackerultimate',
        ['id' => $cm->instance],
        'id,name,intro,introformat,completionminscore,completionminpercent,completionindicators'
    );
    if (!$activity) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $activity->name;
    if ($cm->showdescription) {
        $info->content = format_module_intro('videotrackerultimate', $activity, $cm->id, false);
    }
    if ((int)$cm->completion === COMPLETION_TRACKING_AUTOMATIC) {
        $info->customdata['customcompletionrules'] = [
            'completionminscore' => (float)$activity->completionminscore,
            'completionminpercent' => (int)$activity->completionminpercent,
            'completionindicators' => !empty($activity->completionindicators),
        ];
    }
    return $info;
}

function videotrackerultimate_get_completion_active_rule_descriptions(cached_cm_info $cm): array {
    if ((int)$cm->completion !== COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }
    $rules = $cm->customdata['customcompletionrules'] ?? [];
    $descriptions = [];
    if (!empty($rules['completionminscore'])) {
        $descriptions[] = get_string('completiondetail:score', 'videotrackerultimate', $rules['completionminscore']);
    }
    if (!empty($rules['completionminpercent'])) {
        $descriptions[] = get_string('completiondetail:percent', 'videotrackerultimate', $rules['completionminpercent']);
    }
    if (!empty($rules['completionindicators'])) {
        $descriptions[] = get_string('completiondetail:indicators', 'videotrackerultimate');
    }
    return $descriptions;
}

function videotrackerultimate_get_completion_state($course, $cm, int $userid, bool $type): bool {
    global $DB;

    $activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance], '*', MUST_EXIST);
    $score = score_manager::get_cached((int)$activity->id, $userid);
    return score_manager::completion_met($activity, $score);
}

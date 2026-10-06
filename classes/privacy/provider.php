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

namespace mod_videotrackerultimate\privacy;

use context;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die;

/**
 * Privacy provider for cached playback evidence and scores.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Method get_metadata.
     *
     * @param collection $collection Parameter collection.
     * @return collection Return value.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('videotrackerultimate_score', [
            'userid' => 'privacy:metadata:score:userid',
            'rawscore' => 'privacy:metadata:score:rawscore',
            'totalweight' => 'privacy:metadata:score:totalweight',
            'score' => 'privacy:metadata:score:score',
            'statuscode' => 'privacy:metadata:score:statuscode',
            'metricsjson' => 'privacy:metadata:score:metricsjson',
            'breakdownjson' => 'privacy:metadata:score:breakdownjson',
            'calculatedby' => 'privacy:metadata:score:calculatedby',
            'origin' => 'privacy:metadata:score:origin',
            'timecalculated' => 'privacy:metadata:score:timecalculated',
            'gradeupdated' => 'privacy:metadata:score:gradeupdated',
            'gradeorigin' => 'privacy:metadata:score:gradeorigin',
        ], 'privacy:metadata:score');

        $collection->add_database_table('videotrackerultimate_log', [
            'userid' => 'privacy:metadata:log:userid',
            'triggeredby' => 'privacy:metadata:log:triggeredby',
            'origin' => 'privacy:metadata:log:origin',
            'oldscore' => 'privacy:metadata:log:oldscore',
            'newscore' => 'privacy:metadata:log:newscore',
            'timecreated' => 'privacy:metadata:log:timecreated',
        ], 'privacy:metadata:log');
        return $collection;
    }

    /**
     * Method get_contexts_for_userid.
     *
     * @param int $userid Parameter userid.
     * @return contextlist Return value.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT DISTINCT ctx.id
                  FROM {videotrackerultimate_score} s
                  JOIN {videotrackerultimate} v ON v.id = s.ultimateid
                  JOIN {modules} m ON m.name = 'videotrackerultimate'
                  JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = v.id
                  JOIN {context} ctx ON ctx.contextlevel = :contextlevel AND ctx.instanceid = cm.id
                 WHERE s.userid = :userid";
        $list = new contextlist();
        $list->add_from_sql($sql, ['contextlevel' => CONTEXT_MODULE, 'userid' => $userid]);
        return $list;
    }

    /**
     * Method export_user_data.
     *
     * @param approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_MODULE) {
                continue;
            }
            $cm = get_coursemodule_from_id('videotrackerultimate', $context->instanceid, 0, false, IGNORE_MISSING);
            if (!$cm) {
                continue;
            }
            $activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance]);
            if (!$activity) {
                continue;
            }
            $score = $DB->get_record('videotrackerultimate_score', [
                'ultimateid' => $activity->id,
                'userid' => $userid,
            ]);
            if (!$score) {
                continue;
            }
            $indicators = $DB->get_records(
                'videotrackerultimate_ind',
                ['ultimateid' => $activity->id],
                'sortorder ASC, id ASC'
            );

            $rules = [];
            foreach ($indicators as $indicator) {
                $rules[] = (object)[
                    'name' => $indicator->name,
                    'type' => $indicator->ruletype,
                    'weight' => $indicator->weight,
                    'limit' => $indicator->limitvalue,
                    'config' => json_decode((string)$indicator->configjson, true) ?: [],
                    'enabled' => (bool)$indicator->enabled,
                    'requiredcompletion' => (bool)$indicator->requiredcompletion,
                ];
            }

            writer::with_context($context)->export_data(
                [get_string('privacy:path', 'videotrackerultimate')],
                (object)[
                    'score' => (float)$score->score,
                    'rawscore' => (float)$score->rawscore,
                    'totalweight' => (float)$score->totalweight,
                    'statuscode' => (string)$score->statuscode,
                    'metrics' => json_decode((string)$score->metricsjson, true) ?: [],
                    'breakdown' => json_decode((string)$score->breakdownjson, true) ?: [],
                    'appliedrules' => $rules,
                    'calculationorigin' => (string)$score->origin,
                    'timecalculated' => transform::datetime((int)$score->timecalculated),
                ]
            );
        }
    }

    /**
     * Method delete_data_for_all_users_in_context.
     *
     * @param context $context Parameter context.
     * @return void Return value.
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if ($context->contextlevel !== CONTEXT_MODULE) {
            return;
        }
        $cm = get_coursemodule_from_id('videotrackerultimate', $context->instanceid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return;
        }
        $DB->delete_records('videotrackerultimate_log', ['ultimateid' => $cm->instance]);
        $DB->delete_records('videotrackerultimate_score', ['ultimateid' => $cm->instance]);
    }

    /**
     * Method delete_data_for_user.
     *
     * @param approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_MODULE) {
                continue;
            }
            $cm = get_coursemodule_from_id('videotrackerultimate', $context->instanceid, 0, false, IGNORE_MISSING);
            if (!$cm) {
                continue;
            }
            $DB->delete_records('videotrackerultimate_log', [
                'ultimateid' => $cm->instance,
                'userid' => $userid,
            ]);
            $DB->delete_records('videotrackerultimate_score', [
                'ultimateid' => $cm->instance,
                'userid' => $userid,
            ]);
        }
    }
}

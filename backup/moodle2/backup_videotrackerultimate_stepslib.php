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

defined('MOODLE_INTERNAL') || die;

/**
 * Backup structure.
 *
 * Learner score snapshots are intentionally excluded.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class backup_videotrackerultimate_activity_structure_step extends backup_activity_structure_step {
    /**
     * Method define_structure.
     *
     * @return backup_nested_element Return value.
     */
    protected function define_structure(): backup_nested_element {
        $activity = new backup_nested_element('videotrackerultimate', ['id'], [
            'name', 'intro', 'introformat', 'videosource', 'sourceconfig', 'videourl',
            'usegrade', 'grademax',
            'excellentmin', 'adequatemin', 'attentionmin',
            'excellentlabel', 'adequatelabel', 'attentionlabel', 'insufficientlabel',
            'reviewthreshold', 'rankingenabled',
            'completionminscore', 'completionminpercent', 'completionindicators',
            'timecreated', 'timemodified',
        ]);
        $indicators = new backup_nested_element('indicators');
        $indicator = new backup_nested_element('indicator', ['id'], [
            'name', 'ruletype', 'weight', 'limitvalue', 'configjson', 'enabled',
            'requiredcompletion', 'sortorder', 'timecreated', 'timemodified',
        ]);

        $activity->add_child($indicators);
        $indicators->add_child($indicator);

        $activity->set_source_table('videotrackerultimate', ['id' => backup::VAR_ACTIVITYID]);
        $indicator->set_source_table('videotrackerultimate_ind', ['ultimateid' => backup::VAR_PARENTID], 'sortorder ASC, id ASC');

        $activity->annotate_files('mod_videotrackerultimate', 'intro', null);
        $activity->annotate_files('local_video_bridge', 'video', 0);

        return $this->prepare_activity_structure($activity);
    }
}

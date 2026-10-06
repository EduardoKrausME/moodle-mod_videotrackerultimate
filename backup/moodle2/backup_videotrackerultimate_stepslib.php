<?php
defined('MOODLE_INTERNAL') || die;

/**
 * Backup structure.
 *
 * Learner score snapshots are intentionally excluded.
 *
 * @package mod_videotrackerultimate
 */
final class backup_videotrackerultimate_activity_structure_step extends backup_activity_structure_step {
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

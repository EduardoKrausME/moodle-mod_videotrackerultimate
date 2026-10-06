<?php
defined('MOODLE_INTERNAL') || die;

/**
 * Test generator.
 *
 * @package mod_videotrackerultimate
 */
final class mod_videotrackerultimate_generator extends testing_module_generator {
    public function create_instance($record = null, array $options = null) {
        $record = (object)($record ?? []);
        if (!isset($record->videosource)) {
            $record->videosource = 'url';
        }
        if (!isset($record->videourl)) {
            $record->videourl = 'https://example.com/video.mp4';
        }
        if (!isset($record->usegrade)) {
            $record->usegrade = 0;
        }
        if (!isset($record->grademax)) {
            $record->grademax = 100;
        }
        if (!isset($record->excellentmin)) {
            $record->excellentmin = 85;
            $record->adequatemin = 70;
            $record->attentionmin = 50;
            $record->excellentlabel = 'Excelente';
            $record->adequatelabel = 'Adequado';
            $record->attentionlabel = 'Atenção';
            $record->insufficientlabel = 'Insuficiente';
            $record->reviewthreshold = 70;
            $record->rankingenabled = 0;
            $record->completionminscore = 0;
            $record->completionminpercent = 0;
            $record->completionindicators = 0;
        }
        return parent::create_instance($record, $options);
    }
}

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
 * Test generator.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class mod_videotrackerultimate_generator extends testing_module_generator {
    /**
     * Method create_instance.
     *
     * @param mixed $record Parameter record.
     * @param array $options Parameter options.
     * @return mixed Return value.
     */
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

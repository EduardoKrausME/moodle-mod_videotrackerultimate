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

namespace mod_videotrackerultimate\form;

use mod_videotrackerultimate\rule\engine;

require_once($CFG->libdir . '/formslib.php');

/**
 * Safe fixed rule builder.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class indicator_form extends \moodleform {
    /**
     * Method definition.
     *
     * @return void Return value.
     */
    public function definition(): void {
        $mform = $this->_form;
        $indicator = $this->_customdata['indicator'] ?? null;

        $mform->addElement('hidden', 'id', (int)$this->_customdata['cmid']);
        $mform->setType('id', PARAM_INT);
        if ($indicator) {
            $mform->addElement('hidden', 'indicatorid', (int)$indicator->id);
            $mform->setType('indicatorid', PARAM_INT);
        }

        $mform->addElement('text', 'name', get_string('indicatorname', 'videotrackerultimate'), ['size' => 60]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $types = [];
        foreach (engine::types() as $type) {
            $types[$type] = get_string('ruletype:' . $type, 'videotrackerultimate');
        }
        $mform->addElement('select', 'ruletype', get_string('ruletype', 'videotrackerultimate'), $types);

        $mform->addElement('text', 'weight', get_string('indicatorweight', 'videotrackerultimate'), ['size' => 10]);
        $mform->setType('weight', PARAM_FLOAT);
        $mform->addRule('weight', null, 'required', null, 'client');

        $mform->addElement('text', 'limitvalue', get_string('rulelimit', 'videotrackerultimate'), ['size' => 12]);
        $mform->setType('limitvalue', PARAM_FLOAT);
        $mform->hideIf('limitvalue', 'ruletype', 'eq', engine::REACHED_END);
        $mform->hideIf('limitvalue', 'ruletype', 'eq', engine::SEGMENT_WATCHED);

        $mform->addElement('text', 'segmentstart', get_string('segmentstart', 'videotrackerultimate'), ['size' => 12]);
        $mform->setType('segmentstart', PARAM_FLOAT);
        $mform->hideIf('segmentstart', 'ruletype', 'neq', engine::SEGMENT_WATCHED);

        $mform->addElement('text', 'segmentend', get_string('segmentend', 'videotrackerultimate'), ['size' => 12]);
        $mform->setType('segmentend', PARAM_FLOAT);
        $mform->hideIf('segmentend', 'ruletype', 'neq', engine::SEGMENT_WATCHED);

        $mform->addElement('text', 'segmentcoverage', get_string('segmentcoverage', 'videotrackerultimate'), ['size' => 12]);
        $mform->setType('segmentcoverage', PARAM_FLOAT);
        $mform->setDefault('segmentcoverage', 100);
        $mform->hideIf('segmentcoverage', 'ruletype', 'neq', engine::SEGMENT_WATCHED);

        $mform->addElement('advcheckbox', 'enabled', get_string('indicatorenabled', 'videotrackerultimate'));
        $mform->setDefault('enabled', 1);
        $mform->addElement('advcheckbox', 'requiredcompletion', get_string('requiredcompletion', 'videotrackerultimate'));

        $this->add_action_buttons(true, get_string('saveindicator', 'videotrackerultimate'));

        if ($indicator) {
            $config = json_decode((string)$indicator->configjson, true) ?: [];
            $this->set_data([
                'id' => (int)$this->_customdata['cmid'],
                'indicatorid' => (int)$indicator->id,
                'name' => $indicator->name,
                'ruletype' => $indicator->ruletype,
                'weight' => $indicator->weight,
                'limitvalue' => $indicator->limitvalue,
                'segmentstart' => $config['start'] ?? 0,
                'segmentend' => $config['end'] ?? 0,
                'segmentcoverage' => $config['mincoverage'] ?? 100,
                'enabled' => $indicator->enabled,
                'requiredcompletion' => $indicator->requiredcompletion,
            ]);
        }
    }

    /**
     * Method validation.
     *
     * @param mixed $data Parameter data.
     * @param mixed $files Parameter files.
     * @return array Return value.
     */
    public function validation($data, $files): array {
        global $DB;
        $errors = parent::validation($data, $files);
        $weight = (float)($data['weight'] ?? 0);
        if ($weight <= 0 || $weight > 100) {
            $errors['weight'] = get_string('errorweight', 'videotrackerultimate');
        }

        $config = [];
        if (($data['ruletype'] ?? '') === engine::SEGMENT_WATCHED) {
            $config = [
                'start' => (float)($data['segmentstart'] ?? 0),
                'end' => (float)($data['segmentend'] ?? 0),
                'mincoverage' => (float)($data['segmentcoverage'] ?? 100),
            ];
        }
        try {
            engine::validate((string)($data['ruletype'] ?? ''), (float)($data['limitvalue'] ?? 0), $config);
        } catch (\Throwable $exception) {
            $errors['ruletype'] = $exception->getMessage();
        }

        if (!empty($data['enabled'])) {
            $activityid = (int)$this->_customdata['activityid'];
            $currentid = (int)($data['indicatorid'] ?? 0);
            $params = ['activityid' => $activityid];
            $sql = 'ultimateid = :activityid AND enabled = 1';
            if ($currentid > 0) {
                $sql .= ' AND id <> :currentid';
                $params['currentid'] = $currentid;
            }
            $sum = (float)$DB->get_field_sql(
                "SELECT COALESCE(SUM(weight), 0) FROM {videotrackerultimate_ind} WHERE {$sql}",
                $params
            );
            if ($sum + $weight > 100.0001) {
                $errors['weight'] = get_string('errortotalweight', 'videotrackerultimate', round($sum, 2));
            }
        }

        return $errors;
    }
}

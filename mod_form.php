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

use mod_videotrackerultimate\source_manager;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Activity configuration form.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_videotrackerultimate_mod_form extends moodleform_mod {
    /**
     * Method definition.
     *
     * @return void Return value.
     */
    public function definition(): void {
        $mform = $this->_form;
        $bridge = source_manager::create();

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('videotrackerultimatename', 'videotrackerultimate'), ['size' => 64]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $this->standard_intro_elements();

        $mform->addElement('header', 'videosourceheader', get_string('videosourceheader', 'videotrackerultimate'));
        $options = $bridge->get_options(['tracking']);
        $mform->addElement('select', 'videosource', get_string('videosource', 'videotrackerultimate'), $options);
        if ($options) {
            $mform->setDefault('videosource', array_key_first($options));
        }
        $bridge->add_form_elements($mform, 'videosource');

        $mform->addElement('header', 'scoreheader', get_string('scoreheader', 'videotrackerultimate'));
        $mform->addElement('advcheckbox', 'usegrade', get_string('usegrade', 'videotrackerultimate'));
        $mform->setDefault('usegrade', 0);
        $mform->addHelpButton('usegrade', 'usegrade', 'videotrackerultimate');
        $mform->addElement('text', 'grademax', get_string('grademax', 'videotrackerultimate'));
        $mform->setType('grademax', PARAM_FLOAT);
        $mform->setDefault('grademax', 100);
        $mform->hideIf('grademax', 'usegrade', 'notchecked');

        $mform->addElement('text', 'reviewthreshold', get_string('reviewthreshold', 'videotrackerultimate'));
        $mform->setType('reviewthreshold', PARAM_FLOAT);
        $mform->setDefault('reviewthreshold', 70);

        $mform->addElement('advcheckbox', 'rankingenabled', get_string('rankingenabled', 'videotrackerultimate'));
        $mform->setDefault('rankingenabled', 0);
        $mform->addHelpButton('rankingenabled', 'rankingenabled', 'videotrackerultimate');

        $mform->addElement('header', 'statusheader', get_string('statusheader', 'videotrackerultimate'));
        $statuses = [
            ['excellentlabel', 'excellentmin', 'Excelente', 85],
            ['adequatelabel', 'adequatemin', 'Adequado', 70],
            ['attentionlabel', 'attentionmin', 'Atenção', 50],
        ];
        foreach ($statuses as [$label, $minimum, $defaultlabel, $defaultminimum]) {
            $mform->addElement('text', $label, get_string($label, 'videotrackerultimate'), ['size' => 30]);
            $mform->setType($label, PARAM_TEXT);
            $mform->setDefault($label, $defaultlabel);
            $mform->addElement('text', $minimum, get_string($minimum, 'videotrackerultimate'), ['size' => 8]);
            $mform->setType($minimum, PARAM_FLOAT);
            $mform->setDefault($minimum, $defaultminimum);
        }
        $mform->addElement('text', 'insufficientlabel', get_string('insufficientlabel', 'videotrackerultimate'), ['size' => 30]);
        $mform->setType('insufficientlabel', PARAM_TEXT);
        $mform->setDefault('insufficientlabel', 'Insuficiente');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Method add_completion_rules.
     *
     * @return array Return value.
     */
    public function add_completion_rules(): array {
        $mform = $this->_form;

        $mform->addElement('text', 'completionminscore', get_string('completionminscore', 'videotrackerultimate'));
        $mform->setType('completionminscore', PARAM_FLOAT);
        $mform->setDefault('completionminscore', 0);

        $mform->addElement('text', 'completionminpercent', get_string('completionminpercent', 'videotrackerultimate'));
        $mform->setType('completionminpercent', PARAM_INT);
        $mform->setDefault('completionminpercent', 0);

        $mform->addElement('advcheckbox', 'completionindicators', get_string('completionindicators', 'videotrackerultimate'));
        $mform->setDefault('completionindicators', 0);

        return ['completionminscore', 'completionminpercent', 'completionindicators'];
    }

    /**
     * Method completion_rule_enabled.
     *
     * @param mixed $data Parameter data.
     * @return bool Return value.
     */
    public function completion_rule_enabled($data): bool {
        return (float)($data['completionminscore'] ?? 0) > 0
            || (int)($data['completionminpercent'] ?? 0) > 0
            || !empty($data['completionindicators']);
    }

    /**
     * Method data_preprocessing.
     *
     * @param mixed $defaultvalues Parameter defaultvalues.
     * @return void Return value.
     */
    public function data_preprocessing(&$defaultvalues): void {
        parent::data_preprocessing($defaultvalues);
        if (!empty($this->current->id) && !empty($this->context)) {
            source_manager::create()->prepare_form_data($defaultvalues, $this->context);
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
        $errors = parent::validation($data, $files);
        $errors += source_manager::create()->validation($data, $files);

        $grademax = (float)($data['grademax'] ?? 100);
        if (!empty($data['usegrade']) && ($grademax <= 0 || $grademax > 10000)) {
            $errors['grademax'] = get_string('errorgrademax', 'videotrackerultimate');
        }

        foreach (['excellentmin', 'adequatemin', 'attentionmin', 'reviewthreshold', 'completionminscore'] as $field) {
            $value = (float)($data[$field] ?? 0);
            if ($value < 0 || $value > 100) {
                $errors[$field] = get_string('errorpercent', 'videotrackerultimate');
            }
        }
        $completionpercent = (int)($data['completionminpercent'] ?? 0);
        if ($completionpercent < 0 || $completionpercent > 100) {
            $errors['completionminpercent'] = get_string('errorpercent', 'videotrackerultimate');
        }

        if ((float)($data['excellentmin'] ?? 85) < (float)($data['adequatemin'] ?? 70)
                || (float)($data['adequatemin'] ?? 70) < (float)($data['attentionmin'] ?? 50)) {
            $errors['excellentmin'] = get_string('errorstatusorder', 'videotrackerultimate');
        }

        return $errors;
    }
}

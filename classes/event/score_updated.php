<?php
namespace mod_videotrackerultimate\event;

defined('MOODLE_INTERNAL') || die;

/**
 * Fired after an authoritative Engagement Score recalculation.
 *
 * @package mod_videotrackerultimate
 */
final class score_updated extends \core\event\base {
    protected function init(): void {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'videotrackerultimate_score';
    }

    public static function get_name(): string {
        return get_string('eventscoreupdated', 'videotrackerultimate');
    }

    public function get_description(): string {
        return "The Engagement Score for user {$this->relateduserid} was recalculated in module context {$this->contextid}.";
    }

    public function get_url(): \moodle_url {
        return new \moodle_url('/mod/videotrackerultimate/report.php', [
            'id' => $this->contextinstanceid,
            'userid' => $this->relateduserid,
        ]);
    }
}

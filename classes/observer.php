<?php
namespace mod_videotrackerultimate;

use mod_videotrackerultimate\score\manager as score_manager;

defined('MOODLE_INTERNAL') || die;

/**
 * Observers for shared Video Bridge telemetry.
 *
 * @package mod_videotrackerultimate
 */
final class observer {
    public static function analytics_updated(\core\event\base $event): void {
        if (($event->other['component'] ?? '') !== 'mod_videotrackerultimate') {
            return;
        }
        $cmid = (int)$event->contextinstanceid;
        $userid = (int)$event->relateduserid;
        if ($cmid > 0 && $userid > 0) {
            score_manager::queue_user($cmid, $userid, 'bridge_event');
        }
    }
}

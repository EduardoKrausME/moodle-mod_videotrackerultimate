<?php
namespace mod_videotrackerultimate\task;

use context_module;
use mod_videotrackerultimate\score\manager as score_manager;

defined('MOODLE_INTERNAL') || die;

/**
 * Adhoc recalculation task.
 *
 * @package mod_videotrackerultimate
 */
final class recalculate_user extends \core\task\adhoc_task {
    public function execute(): void {
        global $DB;

        $data = $this->get_custom_data();
        $cmid = (int)($data->cmid ?? 0);
        $userid = (int)($data->userid ?? 0);
        if ($cmid <= 0 || $userid <= 0) {
            return;
        }

        $cm = get_coursemodule_from_id('videotrackerultimate', $cmid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return;
        }
        $activity = $DB->get_record('videotrackerultimate', ['id' => $cm->instance]);
        if (!$activity) {
            return;
        }
        $context = context_module::instance($cm->id);
        if (!is_enrolled($context, $userid, '', true)) {
            return;
        }

        score_manager::recalculate(
            $context,
            $activity,
            $userid,
            (string)($data->origin ?? 'adhoc'),
            (int)($data->triggeredby ?? 0)
        );
    }
}

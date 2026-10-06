<?php
namespace mod_videotrackerultimate\metrics;

use context_module;
use local_video_bridge\analytics;
use local_video_bridge\analytics\manager as analytics_manager;
use local_video_bridge\analytics\metrics;

defined('MOODLE_INTERNAL') || die;

/**
 * Reads the stable consolidated metrics contract owned by Video Bridge.
 *
 * No Video Bridge storage table or raw telemetry event is accessed here.
 *
 * @package mod_videotrackerultimate
 */
final class provider {
    public static function collect(context_module $context, \stdClass $activity, int $userid): metrics {
        $mediahash = analytics::media_hash((string)$activity->videosource, (string)$activity->sourceconfig);
        return analytics_manager::get_user_metrics(
            $context->id,
            'mod_videotrackerultimate',
            (int)$activity->id,
            $mediahash,
            $userid
        );
    }
}

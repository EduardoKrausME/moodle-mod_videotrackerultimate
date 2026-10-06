<?php
namespace mod_videotrackerultimate;

use local_video_bridge\source\manager as bridge_manager;

defined('MOODLE_INTERNAL') || die;

/**
 * Configured Video Bridge source manager.
 *
 * @package mod_videotrackerultimate
 */
final class source_manager {
    public static function create(): bridge_manager {
        return new bridge_manager(
            sourcefield: 'videosource',
            configfield: 'sourceconfig',
            legacyfield: 'videourl'
        );
    }
}

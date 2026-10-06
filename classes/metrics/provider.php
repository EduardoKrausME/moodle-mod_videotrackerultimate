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
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider {
    /**
     * Method collect.
     *
     * @param context_module $context Parameter context.
     * @param \stdClass $activity Parameter activity.
     * @param int $userid Parameter userid.
     * @return metrics Return value.
     */
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

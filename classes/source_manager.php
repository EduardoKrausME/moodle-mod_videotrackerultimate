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

namespace mod_videotrackerultimate;

use local_video_bridge\source\manager as bridge_manager;

/**
 * Configured Video Bridge source manager.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class source_manager {
    /**
     * Method create.
     *
     * @return bridge_manager Return value.
     */
    public static function create(): bridge_manager {
        return new bridge_manager(
            sourcefield: 'videosource',
            configfield: 'sourceconfig',
            legacyfield: 'videourl'
        );
    }
}

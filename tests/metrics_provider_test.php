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

use advanced_testcase;
use mod_videotrackerultimate\metrics\provider;

/**
 * Metrics normalization tests.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class metrics_provider_test extends advanced_testcase {
    /**
     * Method test_ranges_are_merged_without_double_counting.
     *
     * @return void Return value.
     */
    public function test_ranges_are_merged_without_double_counting(): void {
        $ranges = provider::merge_ranges([
            [0, 10],
            [5, 20],
            [20.2, 30],
            [50, 60],
        ], 100);
        $this->assertSame([[0.0, 30.0], [50.0, 60.0]], $ranges);
    }

    /**
     * Method test_ranges_are_clamped_to_duration.
     *
     * @return void Return value.
     */
    public function test_ranges_are_clamped_to_duration(): void {
        $ranges = provider::merge_ranges([[-5, 10], [90, 120]], 100);
        $this->assertSame([[0.0, 10.0], [90.0, 100.0]], $ranges);
    }
}

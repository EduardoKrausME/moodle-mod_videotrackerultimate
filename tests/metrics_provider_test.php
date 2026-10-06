<?php
namespace mod_videotrackerultimate;

use advanced_testcase;
use mod_videotrackerultimate\metrics\provider;

/**
 * Metrics normalization tests.
 *
 * @package mod_videotrackerultimate
 */
final class metrics_provider_test extends advanced_testcase {
    public function test_ranges_are_merged_without_double_counting(): void {
        $ranges = provider::merge_ranges([
            [0, 10],
            [5, 20],
            [20.2, 30],
            [50, 60],
        ], 100);
        $this->assertSame([[0.0, 30.0], [50.0, 60.0]], $ranges);
    }

    public function test_ranges_are_clamped_to_duration(): void {
        $ranges = provider::merge_ranges([[-5, 10], [90, 120]], 100);
        $this->assertSame([[0.0, 10.0], [90.0, 100.0]], $ranges);
    }
}

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
use invalid_parameter_exception;
use local_video_bridge\analytics\metrics;
use mod_videotrackerultimate\rule\engine;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Deterministic rule engine tests.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(engine::class)]
final class rule_engine_test extends advanced_testcase {
    /**
     * Method indicator.
     *
     * @param string $type Parameter type.
     * @param float $weight Parameter weight.
     * @param float $limit Parameter limit.
     * @param array $config Parameter config.
     * @return \stdClass Return value.
     */
    private function indicator(string $type, float $weight, float $limit = 0, array $config = []): \stdClass {
        return (object)[
            'id' => 7,
            'name' => 'Test indicator',
            'ruletype' => $type,
            'weight' => $weight,
            'limitvalue' => $limit,
            'configjson' => json_encode($config),
            'requiredcompletion' => 0,
        ];
    }

    /**
     * Method test_percent_minimum_is_proportional_and_capped.
     *
     * @return void Return value.
     */
    public function test_percent_minimum_is_proportional_and_capped(): void {
        $metrics = new metrics();
        $metrics->percent = 45;
        $result = engine::evaluate($this->indicator(engine::PERCENT_GTE, 35, 90), $metrics);
        $this->assertEqualsWithDelta(17.5, $result['points'], 0.001);
        $this->assertFalse($result['passed']);
        $metrics->percent = 95;
        $this->assertSame(35.0, engine::evaluate($this->indicator(engine::PERCENT_GTE, 35, 90), $metrics)['points']);
    }

    /**
     * Method test_watchtime_minimum_is_proportional.
     *
     * @return void Return value.
     */
    public function test_watchtime_minimum_is_proportional(): void {
        $metrics = new metrics();
        $metrics->playbacktime = 300;
        $this->assertSame(10.0, engine::evaluate($this->indicator(engine::WATCHTIME_GTE, 20, 600), $metrics)['points']);
    }

    /**
     * Method test_sessions_minimum_is_proportional.
     *
     * @return void Return value.
     */
    public function test_sessions_minimum_is_proportional(): void {
        $metrics = new metrics();
        $metrics->sessions = 2;
        $this->assertSame(5.0, engine::evaluate($this->indicator(engine::SESSIONS_GTE, 10, 4), $metrics)['points']);
    }

    /**
     * Method test_ceiling_rules_require_session_evidence.
     *
     * @return void Return value.
     */
    public function test_ceiling_rules_require_session_evidence(): void {
        $metrics = new metrics();
        $this->assertSame(0.0, engine::evaluate($this->indicator(engine::SESSIONS_LTE, 10, 3), $metrics)['points']);
        $this->assertSame(0.0, engine::evaluate($this->indicator(engine::MAXRATE_LTE, 10, 1.5), $metrics)['points']);
        $this->assertSame(0.0, engine::evaluate($this->indicator(engine::SEEKCOUNT_LTE, 10, 2), $metrics)['points']);
    }

    /**
     * Method test_sessions_maximum_is_binary.
     *
     * @return void Return value.
     */
    public function test_sessions_maximum_is_binary(): void {
        $metrics = new metrics();
        $metrics->sessions = 3;
        $this->assertSame(10.0, engine::evaluate($this->indicator(engine::SESSIONS_LTE, 10, 3), $metrics)['points']);
        $metrics->sessions = 4;
        $this->assertSame(0.0, engine::evaluate($this->indicator(engine::SESSIONS_LTE, 10, 3), $metrics)['points']);
    }

    /**
     * Method test_maxrate_is_binary.
     *
     * @return void Return value.
     */
    public function test_maxrate_is_binary(): void {
        $metrics = new metrics();
        $metrics->sessions = 1;
        $metrics->maxrate = 1.5;
        $this->assertTrue(engine::evaluate($this->indicator(engine::MAXRATE_LTE, 10, 1.5), $metrics)['passed']);
        $metrics->maxrate = 2.0;
        $this->assertFalse(engine::evaluate($this->indicator(engine::MAXRATE_LTE, 10, 1.5), $metrics)['passed']);
    }

    /**
     * Method test_reached_end_is_binary.
     *
     * @return void Return value.
     */
    public function test_reached_end_is_binary(): void {
        $metrics = new metrics();
        $this->assertSame(0.0, engine::evaluate($this->indicator(engine::REACHED_END, 10), $metrics)['points']);
        $metrics->reachedend = true;
        $this->assertSame(10.0, engine::evaluate($this->indicator(engine::REACHED_END, 10), $metrics)['points']);
    }

    /**
     * Method test_segment_coverage_is_explainable_and_proportional.
     *
     * @return void Return value.
     */
    public function test_segment_coverage_is_explainable_and_proportional(): void {
        $metrics = new metrics();
        $metrics->watchedranges = [[0, 25], [40, 65]];
        $result = engine::evaluate($this->indicator(engine::SEGMENT_WATCHED, 20, 0, [
            'start' => 0,
            'end' => 100,
            'mincoverage' => 80,
        ]), $metrics);
        $this->assertSame(50.0, $result['actual']);
        $this->assertEqualsWithDelta(12.5, $result['points'], 0.001);
        $this->assertFalse($result['passed']);
    }

    /**
     * Method test_seek_maximum_is_binary.
     *
     * @return void Return value.
     */
    public function test_seek_maximum_is_binary(): void {
        $metrics = new metrics();
        $metrics->sessions = 1;
        $metrics->seekcount = 2;
        $this->assertTrue(engine::evaluate($this->indicator(engine::SEEKCOUNT_LTE, 5, 2), $metrics)['passed']);
        $metrics->seekcount = 3;
        $this->assertFalse(engine::evaluate($this->indicator(engine::SEEKCOUNT_LTE, 5, 2), $metrics)['passed']);
    }

    /**
     * Method test_replay_minimum_is_proportional.
     *
     * @return void Return value.
     */
    public function test_replay_minimum_is_proportional(): void {
        $metrics = new metrics();
        $metrics->replaycount = 1;
        $this->assertSame(5.0, engine::evaluate($this->indicator(engine::REPLAYCOUNT_GTE, 10, 2), $metrics)['points']);
    }

    /**
     * Method test_regularity_minimum_is_proportional.
     *
     * @return void Return value.
     */
    public function test_regularity_minimum_is_proportional(): void {
        $metrics = new metrics();
        $metrics->regularity = 40;
        $result = engine::evaluate($this->indicator(engine::REGULARITY_GTE, 5, 80), $metrics);
        $this->assertSame(2.5, $result['points']);
        $this->assertFalse($result['passed']);
    }

    /**
     * Method test_unknown_rule_is_rejected_without_execution.
     *
     * @return void Return value.
     */
    public function test_unknown_rule_is_rejected_without_execution(): void {
        $this->expectException(invalid_parameter_exception::class);
        engine::validate('php_eval', 1, []);
    }

    /**
     * Method test_invalid_segment_is_rejected.
     *
     * @return void Return value.
     */
    public function test_invalid_segment_is_rejected(): void {
        $this->expectException(invalid_parameter_exception::class);
        engine::validate(engine::SEGMENT_WATCHED, 0, [
            'start' => 100,
            'end' => 50,
            'mincoverage' => 90,
        ]);
    }
}

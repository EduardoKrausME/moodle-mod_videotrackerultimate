<?php
namespace mod_videotrackerultimate;

use advanced_testcase;
use invalid_parameter_exception;
use mod_videotrackerultimate\metrics\value;
use mod_videotrackerultimate\rule\engine;

/**
 * Deterministic rule engine tests.
 *
 * @package mod_videotrackerultimate
 */
final class rule_engine_test extends advanced_testcase {
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

    public function test_percent_minimum_is_proportional_and_capped(): void {
        $metrics = new value();
        $metrics->percent = 45;
        $result = engine::evaluate($this->indicator(engine::PERCENT_GTE, 35, 90), $metrics);
        $this->assertEqualsWithDelta(17.5, $result['points'], 0.001);
        $this->assertFalse($result['passed']);

        $metrics->percent = 95;
        $result = engine::evaluate($this->indicator(engine::PERCENT_GTE, 35, 90), $metrics);
        $this->assertSame(35.0, $result['points']);
        $this->assertTrue($result['passed']);
    }

    public function test_watchtime_minimum_is_proportional(): void {
        $metrics = new value();
        $metrics->playbackTime = 300;
        $result = engine::evaluate($this->indicator(engine::WATCHTIME_GTE, 20, 600), $metrics);
        $this->assertSame(10.0, $result['points']);
    }

    public function test_sessions_minimum_is_proportional(): void {
        $metrics = new value();
        $metrics->sessions = 2;
        $result = engine::evaluate($this->indicator(engine::SESSIONS_GTE, 10, 4), $metrics);
        $this->assertSame(5.0, $result['points']);
    }

    public function test_sessions_maximum_is_binary(): void {
        $metrics = new value();
        $metrics->sessions = 3;
        $this->assertSame(10.0, engine::evaluate(
            $this->indicator(engine::SESSIONS_LTE, 10, 3), $metrics
        )['points']);
        $metrics->sessions = 4;
        $this->assertSame(0.0, engine::evaluate(
            $this->indicator(engine::SESSIONS_LTE, 10, 3), $metrics
        )['points']);
    }

    public function test_maxrate_is_binary(): void {
        $metrics = new value();
        $metrics->maxRate = 1.5;
        $this->assertTrue(engine::evaluate(
            $this->indicator(engine::MAXRATE_LTE, 10, 1.5), $metrics
        )['passed']);
        $metrics->maxRate = 2.0;
        $this->assertFalse(engine::evaluate(
            $this->indicator(engine::MAXRATE_LTE, 10, 1.5), $metrics
        )['passed']);
    }

    public function test_reached_end_is_binary(): void {
        $metrics = new value();
        $metrics->reachedEnd = false;
        $this->assertSame(0.0, engine::evaluate(
            $this->indicator(engine::REACHED_END, 10), $metrics
        )['points']);
        $metrics->reachedEnd = true;
        $this->assertSame(10.0, engine::evaluate(
            $this->indicator(engine::REACHED_END, 10), $metrics
        )['points']);
    }

    public function test_segment_coverage_is_explainable_and_proportional(): void {
        $metrics = new value();
        $metrics->watchedRanges = [[0, 25], [40, 65]];
        $indicator = $this->indicator(engine::SEGMENT_WATCHED, 20, 0, [
            'start' => 0,
            'end' => 100,
            'mincoverage' => 80,
        ]);
        $result = engine::evaluate($indicator, $metrics);
        $this->assertSame(50.0, $result['actual']);
        $this->assertEqualsWithDelta(12.5, $result['points'], 0.001);
        $this->assertFalse($result['passed']);
    }

    public function test_seek_maximum_is_binary(): void {
        $metrics = new value();
        $metrics->seekCount = 2;
        $this->assertTrue(engine::evaluate(
            $this->indicator(engine::SEEKCOUNT_LTE, 5, 2), $metrics
        )['passed']);
        $metrics->seekCount = 3;
        $this->assertFalse(engine::evaluate(
            $this->indicator(engine::SEEKCOUNT_LTE, 5, 2), $metrics
        )['passed']);
    }

    public function test_replay_minimum_is_proportional(): void {
        $metrics = new value();
        $metrics->replayCount = 1;
        $result = engine::evaluate($this->indicator(engine::REPLAYCOUNT_GTE, 10, 2), $metrics);
        $this->assertSame(5.0, $result['points']);
    }

    public function test_unknown_rule_is_rejected_without_execution(): void {
        $this->expectException(invalid_parameter_exception::class);
        engine::validate('php_eval', 1, []);
    }

    public function test_invalid_segment_is_rejected(): void {
        $this->expectException(invalid_parameter_exception::class);
        engine::validate(engine::SEGMENT_WATCHED, 0, [
            'start' => 100,
            'end' => 50,
            'mincoverage' => 90,
        ]);
    }
}

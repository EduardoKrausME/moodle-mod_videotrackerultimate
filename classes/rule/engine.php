<?php
namespace mod_videotrackerultimate\rule;

use invalid_parameter_exception;
use local_video_bridge\analytics\manager as analytics_manager;
use local_video_bridge\analytics\metrics;

defined('MOODLE_INTERNAL') || die;

/**
 * Deterministic, allow-listed Engagement Score rule engine.
 *
 * @package mod_videotrackerultimate
 */
final class engine {
    public const PERCENT_GTE = 'percent_gte';
    public const WATCHTIME_GTE = 'watchtime_gte';
    public const SESSIONS_GTE = 'sessions_gte';
    public const SESSIONS_LTE = 'sessions_lte';
    public const MAXRATE_LTE = 'maxrate_lte';
    public const REACHED_END = 'reachedend';
    public const SEGMENT_WATCHED = 'segment_watched';
    public const SEEKCOUNT_LTE = 'seekcount_lte';
    public const REPLAYCOUNT_GTE = 'replaycount_gte';
    public const REGULARITY_GTE = 'regularity_gte';

    public static function types(): array {
        return [
            self::PERCENT_GTE,
            self::WATCHTIME_GTE,
            self::SESSIONS_GTE,
            self::SESSIONS_LTE,
            self::MAXRATE_LTE,
            self::REACHED_END,
            self::SEGMENT_WATCHED,
            self::SEEKCOUNT_LTE,
            self::REPLAYCOUNT_GTE,
            self::REGULARITY_GTE,
        ];
    }

    public static function validate(string $type, float $limit, array $config = []): array {
        if (!in_array($type, self::types(), true)) {
            throw new invalid_parameter_exception('Unknown Engagement Score rule type.');
        }

        if ($type === self::REACHED_END) {
            return [];
        }

        if ($type === self::SEGMENT_WATCHED) {
            $start = max(0.0, (float)($config['start'] ?? 0));
            $end = max(0.0, (float)($config['end'] ?? 0));
            $coverage = (float)($config['mincoverage'] ?? 100);
            if ($end <= $start) {
                throw new invalid_parameter_exception('Segment end must be greater than segment start.');
            }
            if ($coverage <= 0 || $coverage > 100) {
                throw new invalid_parameter_exception('Segment minimum coverage must be between 0 and 100.');
            }
            return [
                'start' => round($start, 3),
                'end' => round($end, 3),
                'mincoverage' => round($coverage, 2),
            ];
        }

        if ($limit <= 0) {
            throw new invalid_parameter_exception('Rule limit must be greater than zero.');
        }
        if (in_array($type, [self::PERCENT_GTE, self::REGULARITY_GTE], true) && $limit > 100) {
            throw new invalid_parameter_exception('Percentage cannot be greater than 100.');
        }
        if ($type === self::MAXRATE_LTE && $limit > 16) {
            throw new invalid_parameter_exception('Playback rate limit is too high.');
        }
        return [];
    }

    public static function evaluate(\stdClass $indicator, metrics $metrics): array {
        $type = (string)$indicator->ruletype;
        $weight = max(0.0, (float)$indicator->weight);
        $limit = (float)$indicator->limitvalue;
        $config = json_decode((string)$indicator->configjson, true);
        $config = is_array($config) ? self::validate($type, $limit, $config) : self::validate($type, $limit, []);

        $actual = null;
        $ratio = 0.0;
        $binary = false;
        $hassessions = $metrics->sessions > 0;

        switch ($type) {
            case self::PERCENT_GTE:
                $actual = $metrics->percent;
                $ratio = self::minimum_ratio((float)$actual, $limit);
                break;
            case self::WATCHTIME_GTE:
                $actual = $metrics->playbackTime;
                $ratio = self::minimum_ratio((float)$actual, $limit);
                break;
            case self::SESSIONS_GTE:
                $actual = $metrics->sessions;
                $ratio = self::minimum_ratio((float)$actual, $limit);
                break;
            case self::REPLAYCOUNT_GTE:
                $actual = $metrics->replayCount;
                $ratio = self::minimum_ratio((float)$actual, $limit);
                break;
            case self::REGULARITY_GTE:
                $actual = $metrics->regularity;
                $ratio = self::minimum_ratio((float)$actual, $limit);
                break;
            case self::SESSIONS_LTE:
                $actual = $metrics->sessions;
                $binary = true;
                $ratio = $hassessions && (float)$actual <= $limit ? 1.0 : 0.0;
                break;
            case self::MAXRATE_LTE:
                $actual = $metrics->maxRate;
                $binary = true;
                $ratio = $hassessions && (float)$actual <= $limit ? 1.0 : 0.0;
                break;
            case self::SEEKCOUNT_LTE:
                $actual = $metrics->seekCount;
                $binary = true;
                $ratio = $hassessions && (float)$actual <= $limit ? 1.0 : 0.0;
                break;
            case self::REACHED_END:
                $actual = $metrics->reachedEnd;
                $binary = true;
                $ratio = $metrics->reachedEnd ? 1.0 : 0.0;
                break;
            case self::SEGMENT_WATCHED:
                $actual = analytics_manager::segment_coverage(
                    $metrics->watchedRanges,
                    (float)$config['start'],
                    (float)$config['end']
                );
                $ratio = self::minimum_ratio((float)$actual, (float)$config['mincoverage']);
                break;
            default:
                throw new invalid_parameter_exception('Unsupported Engagement Score rule.');
        }

        $ratio = max(0.0, min(1.0, $ratio));
        return [
            'indicatorid' => (int)$indicator->id,
            'name' => (string)$indicator->name,
            'type' => $type,
            'weight' => round($weight, 2),
            'limit' => $type === self::SEGMENT_WATCHED ? $config['mincoverage'] : $limit,
            'config' => $config,
            'actual' => $actual,
            'ratio' => round($ratio, 4),
            'points' => round($weight * $ratio, 2),
            'passed' => $ratio >= 0.9999,
            'binary' => $binary,
            'requiredcompletion' => !empty($indicator->requiredcompletion),
        ];
    }

    private static function minimum_ratio(float $actual, float $target): float {
        return $target > 0 ? min(1.0, max(0.0, $actual / $target)) : 0.0;
    }
}

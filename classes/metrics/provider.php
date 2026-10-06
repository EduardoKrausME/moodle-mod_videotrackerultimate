<?php
namespace mod_videotrackerultimate\metrics;

use context_module;
use local_video_bridge\analytics;
use local_video_bridge\progress\manager as progress_manager;

defined('MOODLE_INTERNAL') || die;

/**
 * Converts public Video Bridge APIs into one stable normalized snapshot.
 *
 * This class deliberately does not query any local_video_bridge_* table.
 *
 * @package mod_videotrackerultimate
 */
final class provider {
    public static function collect(context_module $context, \stdClass $activity, int $userid): value {
        $metrics = new value();
        $mediahash = analytics::media_hash((string)$activity->videosource, (string)$activity->sourceconfig);
        $progress = progress_manager::get_progress(
            $context->id,
            'mod_videotrackerultimate',
            (int)$activity->id,
            $mediahash,
            $userid
        );
        $sessions = analytics::get_session_metrics(
            $context->id,
            'mod_videotrackerultimate',
            (int)$activity->id,
            $mediahash,
            $userid
        );

        if ($progress) {
            $metrics->percent = max(0, min(100, (int)$progress->percent));
            $metrics->duration = max(0, (int)$progress->duration);
            $metrics->lastPosition = max(0, (int)$progress->currenttime);
        }

        $ranges = [];
        foreach ($sessions as $session) {
            $metrics->duration = max($metrics->duration, (int)$session->duration);
            $metrics->playbackTime += max(0, (int)$session->watchtime);
            $metrics->pauseCount += max(0, (int)$session->pauses);
            $metrics->seekCount += max(0, (int)$session->seeks);
            $metrics->replayCount += max(0, (int)$session->replays);
            $metrics->lastPosition = max($metrics->lastPosition, (int)$session->maxposition);

            $end = (int)$session->endedat > 0 ? (int)$session->endedat : (int)$session->timemodified;
            $metrics->sessionTime += max(0, min(604800, $end - (int)$session->startedat));

            $metrics->maxRate = max($metrics->maxRate, (float)$session->speedavg);
            $rates = json_decode((string)$session->rates, true);
            if (is_array($rates)) {
                foreach (array_keys($rates) as $rate) {
                    if (is_numeric($rate)) {
                        $metrics->maxRate = max($metrics->maxRate, (float)$rate);
                    }
                }
            }

            $sessionranges = json_decode((string)$session->ranges, true);
            if (is_array($sessionranges)) {
                foreach ($sessionranges as $range) {
                    if (is_array($range) && count($range) >= 2) {
                        $ranges[] = [(float)$range[0], (float)$range[1]];
                    }
                }
            }
        }
        $metrics->sessions = count($sessions);

        if (!$ranges && $progress) {
            $map = json_decode((string)$progress->map, true);
            $ranges = self::ranges_from_map(is_array($map) ? $map : [], $metrics->duration);
        }
        $metrics->watchedRanges = self::merge_ranges($ranges, $metrics->duration);

        $covered = 0.0;
        foreach ($metrics->watchedRanges as $range) {
            $covered += max(0, $range[1] - $range[0]);
        }
        $metrics->uniqueWatchTime = (int)round(min($metrics->duration, $covered));
        if ($metrics->duration > 0 && $metrics->percent === 0 && $metrics->uniqueWatchTime > 0) {
            $metrics->percent = min(100, (int)floor(($metrics->uniqueWatchTime / $metrics->duration) * 100));
        }

        $metrics->reachedEnd = $metrics->duration > 0
            && $metrics->lastPosition >= max(0, $metrics->duration - 2);
        $metrics->regularity = $metrics->sessionTime > 0
            ? round(min(100, ($metrics->playbackTime / $metrics->sessionTime) * 100), 2)
            : 0.0;

        return $metrics;
    }

    private static function ranges_from_map(array $map, int $duration): array {
        $length = progress_manager::progress_length($duration);
        if ($duration <= 0 || $length <= 0 || !$map) {
            return [];
        }
        $ranges = [];
        foreach (array_unique(array_map('intval', $map)) as $bucket) {
            if ($bucket < 1 || $bucket > $length) {
                continue;
            }
            $ranges[] = [
                (($bucket - 1) / $length) * $duration,
                ($bucket / $length) * $duration,
            ];
        }
        return $ranges;
    }

    public static function merge_ranges(array $ranges, int $duration = 0): array {
        $clean = [];
        foreach ($ranges as $range) {
            if (!is_array($range) || count($range) < 2) {
                continue;
            }
            $start = max(0.0, (float)$range[0]);
            $end = max($start, (float)$range[1]);
            if ($duration > 0) {
                $start = min($duration, $start);
                $end = min($duration, $end);
            }
            if ($end > $start) {
                $clean[] = [$start, $end];
            }
        }
        usort($clean, static fn(array $a, array $b): int => $a[0] <=> $b[0]);

        $merged = [];
        foreach ($clean as $range) {
            $last = count($merged) - 1;
            if ($last >= 0 && $range[0] <= $merged[$last][1] + 0.5) {
                $merged[$last][1] = max($merged[$last][1], $range[1]);
            } else {
                $merged[] = $range;
            }
        }
        return array_map(static fn(array $range): array => [
            round($range[0], 3),
            round($range[1], 3),
        ], $merged);
    }
}

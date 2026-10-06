<?php
namespace mod_videotrackerultimate\metrics;

defined('MOODLE_INTERNAL') || die;

/**
 * Normalized metrics snapshot consumed by the deterministic rule engine.
 *
 * @package mod_videotrackerultimate
 */
final class value {
    public int $percent = 0;
    public int $duration = 0;
    public int $uniqueWatchTime = 0;
    public int $playbackTime = 0;
    public int $sessionTime = 0;
    public int $sessions = 0;
    public int $pauseCount = 0;
    public int $seekCount = 0;
    public int $replayCount = 0;
    public float $maxRate = 1.0;
    public bool $reachedEnd = false;
    public array $watchedRanges = [];
    public int $lastPosition = 0;
    public float $regularity = 0.0;

    public function to_array(): array {
        return [
            'percent' => $this->percent,
            'duration' => $this->duration,
            'uniqueWatchTime' => $this->uniqueWatchTime,
            'playbackTime' => $this->playbackTime,
            'sessionTime' => $this->sessionTime,
            'sessions' => $this->sessions,
            'pauseCount' => $this->pauseCount,
            'seekCount' => $this->seekCount,
            'replayCount' => $this->replayCount,
            'maxRate' => $this->maxRate,
            'reachedEnd' => $this->reachedEnd,
            'watchedRanges' => $this->watchedRanges,
            'lastPosition' => $this->lastPosition,
            'regularity' => $this->regularity,
        ];
    }

    public static function from_array(array $data): self {
        $value = new self();
        foreach ([
            'percent', 'duration', 'uniqueWatchTime', 'playbackTime', 'sessionTime',
            'sessions', 'pauseCount', 'seekCount', 'replayCount', 'lastPosition',
        ] as $field) {
            $value->{$field} = (int)($data[$field] ?? 0);
        }
        $value->maxRate = (float)($data['maxRate'] ?? 1);
        $value->reachedEnd = !empty($data['reachedEnd']);
        $value->watchedRanges = is_array($data['watchedRanges'] ?? null) ? $data['watchedRanges'] : [];
        $value->regularity = (float)($data['regularity'] ?? 0);
        return $value;
    }
}

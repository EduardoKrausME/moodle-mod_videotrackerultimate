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

defined('MOODLE_INTERNAL') || die;

/**
 * Normalized metrics snapshot consumed by the deterministic rule engine.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class value {
    /**
     * Property percent.
     *
     * @var int
     */
    public int $percent = 0;
    /**
     * Property duration.
     *
     * @var int
     */
    public int $duration = 0;
    /**
     * Property uniqueWatchTime.
     *
     * @var int
     */
    public int $uniqueWatchTime = 0;
    /**
     * Property playbackTime.
     *
     * @var int
     */
    public int $playbackTime = 0;
    /**
     * Property sessionTime.
     *
     * @var int
     */
    public int $sessionTime = 0;
    /**
     * Property sessions.
     *
     * @var int
     */
    public int $sessions = 0;
    /**
     * Property pauseCount.
     *
     * @var int
     */
    public int $pauseCount = 0;
    /**
     * Property seekCount.
     *
     * @var int
     */
    public int $seekCount = 0;
    /**
     * Property replayCount.
     *
     * @var int
     */
    public int $replayCount = 0;
    /**
     * Property maxRate.
     *
     * @var float
     */
    public float $maxRate = 1.0;
    /**
     * Property reachedEnd.
     *
     * @var bool
     */
    public bool $reachedEnd = false;
    /**
     * Property watchedRanges.
     *
     * @var array
     */
    public array $watchedRanges = [];
    /**
     * Property lastPosition.
     *
     * @var int
     */
    public int $lastPosition = 0;
    /**
     * Property regularity.
     *
     * @var float
     */
    public float $regularity = 0.0;

    /**
     * Method to_array.
     *
     * @return array Return value.
     */
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

    /**
     * Method from_array.
     *
     * @param array $data Parameter data.
     * @return self Return value.
     */
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

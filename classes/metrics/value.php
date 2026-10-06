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
     * Property uniquewatchtime.
     *
     * @var int
     */
    public int $uniquewatchtime = 0;
    /**
     * Property playbacktime.
     *
     * @var int
     */
    public int $playbacktime = 0;
    /**
     * Property sessiontime.
     *
     * @var int
     */
    public int $sessiontime = 0;
    /**
     * Property sessions.
     *
     * @var int
     */
    public int $sessions = 0;
    /**
     * Property pausecount.
     *
     * @var int
     */
    public int $pausecount = 0;
    /**
     * Property seekcount.
     *
     * @var int
     */
    public int $seekcount = 0;
    /**
     * Property replaycount.
     *
     * @var int
     */
    public int $replaycount = 0;
    /**
     * Property maxrate.
     *
     * @var float
     */
    public float $maxrate = 1.0;
    /**
     * Property reachedend.
     *
     * @var bool
     */
    public bool $reachedend = false;
    /**
     * Property watchedranges.
     *
     * @var array
     */
    public array $watchedranges = [];
    /**
     * Property lastposition.
     *
     * @var int
     */
    public int $lastposition = 0;
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
            'uniqueWatchTime' => $this->uniquewatchtime,
            'playbackTime' => $this->playbacktime,
            'sessionTime' => $this->sessiontime,
            'sessions' => $this->sessions,
            'pauseCount' => $this->pausecount,
            'seekCount' => $this->seekcount,
            'replayCount' => $this->replaycount,
            'maxRate' => $this->maxrate,
            'reachedEnd' => $this->reachedend,
            'watchedRanges' => $this->watchedranges,
            'lastPosition' => $this->lastposition,
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
        $value->maxrate = (float)($data['maxRate'] ?? 1);
        $value->reachedend = !empty($data['reachedEnd']);
        $value->watchedranges = is_array($data['watchedRanges'] ?? null) ? $data['watchedRanges'] : [];
        $value->regularity = (float)($data['regularity'] ?? 0);
        return $value;
    }
}

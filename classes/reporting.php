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

use context_module;
use mod_videotrackerultimate\score\manager as score_manager;

/**
 * Group-aware reporting helpers.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class reporting {
    /**
     * Method visible_users.
     *
     * @param \stdClass $cm Parameter cm.
     * @param context_module $context Parameter context.
     * @return array Return value.
     */
    public static function visible_users(\stdClass $cm, context_module $context): array {
        $users = get_enrolled_users(
            $context,
            'mod/videotrackerultimate:view',
            0,
            'u.id,u.firstname,u.lastname,u.email,u.idnumber'
        );

        $groupmode = groups_get_activity_groupmode($cm);
        if ($groupmode == NOGROUPS) {
            return $users;
        }

        $groupid = groups_get_activity_group($cm, true);
        if ($groupid > 0) {
            $members = groups_get_members($groupid, 'u.id');
            return array_intersect_key($users, $members);
        }

        if (!has_capability('moodle/site:accessallgroups', $context)) {
            return [];
        }
        return $users;
    }

    /**
     * Method rows.
     *
     * @param \stdClass $cm Parameter cm.
     * @param \stdClass $activity Parameter activity.
     * @param context_module $context Parameter context.
     * @return array Return value.
     */
    public static function rows(\stdClass $cm, \stdClass $activity, context_module $context): array {
        $rows = [];
        foreach (self::visible_users($cm, $context) as $user) {
            $score = score_manager::get_cached((int)$activity->id, (int)$user->id);
            $metrics = $score ? (json_decode((string)$score->metricsjson, true) ?: []) : [];
            $rows[] = (object)[
                'user' => $user,
                'score' => $score,
                'metrics' => $metrics,
                'statuslabel' => $score
                    ? score_manager::status_label($activity, (string)$score->statuscode)
                    : get_string('notcalculated', 'videotrackerultimate'),
            ];
        }
        return $rows;
    }

    /**
     * Method summary.
     *
     * @param array $rows Parameter rows.
     * @param \stdClass $activity Parameter activity.
     * @return array Return value.
     */
    public static function summary(array $rows, \stdClass $activity): array {
        $calculated = array_values(array_filter($rows, static fn($row): bool => !empty($row->score)));
        if (!$calculated) {
            return [
                'count' => count($rows),
                'calculated' => 0,
                'averagescore' => 0,
                'averagepercent' => 0,
                'averagewatchtime' => 0,
                'belowthreshold' => 0,
                'completed' => 0,
                'distribution' => [],
                'failures' => [],
            ];
        }

        $scores = [];
        $percents = [];
        $watchtimes = [];
        $below = 0;
        $completed = 0;
        $distribution = ['excellent' => 0, 'adequate' => 0, 'attention' => 0, 'insufficient' => 0];
        $failures = [];

        foreach ($calculated as $row) {
            $scores[] = (float)$row->score->score;
            $percents[] = (int)($row->metrics['percent'] ?? 0);
            $watchtimes[] = (int)($row->metrics['playbackTime'] ?? 0);
            if ((float)$row->score->score < (float)$activity->reviewthreshold) {
                $below++;
            }
            $distribution[(string)$row->score->statuscode] = ($distribution[(string)$row->score->statuscode] ?? 0) + 1;
            if (score_manager::completion_met($activity, $row->score)) {
                $completed++;
            }

            $breakdown = json_decode((string)$row->score->breakdownjson, true) ?: [];
            foreach ($breakdown as $indicator) {
                if (empty($indicator['passed'])) {
                    $key = (int)($indicator['indicatorid'] ?? 0);
                    if (!isset($failures[$key])) {
                        $failures[$key] = [
                            'indicatorid' => $key,
                            'name' => (string)($indicator['name'] ?? ''),
                            'count' => 0,
                        ];
                    }
                    $failures[$key]['count']++;
                }
            }
        }

        usort($failures, static fn(array $a, array $b): int => $b['count'] <=> $a['count']);
        return [
            'count' => count($rows),
            'calculated' => count($calculated),
            'averagescore' => round(array_sum($scores) / count($scores), 2),
            'averagepercent' => round(array_sum($percents) / count($percents), 2),
            'averagewatchtime' => (int)round(array_sum($watchtimes) / count($watchtimes)),
            'belowthreshold' => $below,
            'completed' => $completed,
            'distribution' => $distribution,
            'failures' => $failures,
        ];
    }
}

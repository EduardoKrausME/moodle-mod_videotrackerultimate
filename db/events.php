<?php
/**
 * Event observers.
 *
 * @package mod_videotrackerultimate
 */

defined('MOODLE_INTERNAL') || die;

$observers = [
    [
        'eventname' => '\local_video_bridge\event\analytics_updated',
        'callback' => '\mod_videotrackerultimate\observer::analytics_updated',
    ],
];

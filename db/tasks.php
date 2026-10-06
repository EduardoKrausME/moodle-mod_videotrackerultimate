<?php
/**
 * Scheduled tasks.
 *
 * @package mod_videotrackerultimate
 */

defined('MOODLE_INTERNAL') || die;

$tasks = [
    [
        'classname' => '\mod_videotrackerultimate\task\reconcile',
        'blocking' => 0,
        'minute' => '17',
        'hour' => '*/6',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
];

<?php
/**
 * External functions.
 *
 * @package mod_videotrackerultimate
 */

defined('MOODLE_INTERNAL') || die;

$functions = [
    'mod_videotrackerultimate_get_score' => [
        'classname' => '\mod_videotrackerultimate\external\get_score',
        'description' => 'Returns an explainable cached Engagement Score.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'mod/videotrackerultimate:view',
    ],
    'mod_videotrackerultimate_recalculate_user' => [
        'classname' => '\mod_videotrackerultimate\external\recalculate_user',
        'description' => 'Queues a server-side analytics recalculation for an allowed user.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/videotrackerultimate:recalculate',
    ],
];

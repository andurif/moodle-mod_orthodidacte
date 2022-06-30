<?php

defined('MOODLE_INTERNAL') || die;

$capabilities = array(
    'mod/orthodidacte:view'   => array(
        'captype'       => 'read',
        'contextlevel'  => CONTEXT_MODULE,
        'archetypes' => array(
            'guest'             => CAP_PREVENT,
            'user'              => CAP_PREVENT,
            'editingteacher'    => CAP_ALLOW,
            'teacher'           => CAP_ALLOW,
            'manager'           => CAP_ALLOW,
            'student'           => CAP_ALLOW,
        )
    ),

    'mod/orthodidacte:addinstance' => array(
        'riskbitmask'   => RISK_XSS,
        'captype'       => 'write',
        'contextlevel'  => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),

);


<?php // $Id: access.php,v 1.6.2.1 2008/07/24 21:58:08 skodak Exp $

$capabilities = array(
    'mod/connectquiz:addinstance' => array(
        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),
);
?>

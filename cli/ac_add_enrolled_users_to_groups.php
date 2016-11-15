<?php

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once( $CFG->dirroot.'/mod/connectquiz/connectlib.php' );


// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('help' => false),
    array('h' => 'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
        "Add enrolled users to AC group.


        Options:
        -h, --help            Print out this help
        ";

    echo $help;
    die;
}

cli_heading('Looking for enrolled users');


$sql = "SELECT u.id, c.id as cid, e.courseid, c.shortname, u.aclogin
          FROM {user_enrolments} ue
          LEFT JOIN {enrol} e ON ue.enrolid=e.id
          LEFT JOIN {course} c ON e.courseid=c.id
          LEFT JOIN {user} u ON ue.userid=u.id
          WHERE e.status=0 AND ue.status=0";
$rows = $DB->get_records_sql($sql);
$total = count($rows);
$i = 0;
foreach ($rows as $row) {

    echo "Adding user [$row->username] to group [" . $row->shortname . "]";

        connect_group_access($row->id, $row->cid);
    
    $i++;
    echo " --" . round($i / $total * 100, 2) . "%--\n";
}

cli_heading('Done');

exit(0);

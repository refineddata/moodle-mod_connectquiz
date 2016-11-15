<?php // $Id: connectpro.php,v 1.00 2008/04/07 09:37:58 terryshane Exp $

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/connectquiz/lib.php');
global $CFG, $DB, $PAGE, $USER;

$PAGE->set_url('/mod/connectquiz/launch.php');

$acurl = optional_param('acurl', '', PARAM_RAW);
$connect_id = optional_param('connect_id', 0, PARAM_INT);
$courseid = optional_param('course', 1, PARAM_INT);
$archive = optional_param('archive', '', PARAM_ALPHANUM);
$guest = optional_param('guest', 0, PARAM_INT);
$edit = optional_param('edit', 0, PARAM_INT);
$type = optional_param('type', '', PARAM_ALPHA);
$forceaddin = optional_param('forceaddin', '0', PARAM_INT);
$cm = 0;
$context = context_course::instance($courseid);
$PAGE->set_context($context);

$url = str_replace('/', '', $acurl);
if ( !isset( $guest ) || !$guest ) {
	require_login();
}

if (empty($acurl)) redirect($CFG->wwwroot);

$connectquery = array( 'url' => $url, 'course' => $courseid );
if( $connect_id ) $connectquery['id'] = $connect_id;

//Check Locking
$courseid = isset($courseid) ? $courseid : 1;
if ($course = $DB->get_record('course', array('id' => $courseid))) {
    if ($connectquiz = $DB->get_record('connectquiz', $connectquery, '*', IGNORE_MULTIPLE)) {
        if ($cm = get_coursemodule_from_instance('connectquiz', $connectquiz->id, $course->id)) {
        	if ( !isset( $guest ) || !$guest ) {
        		require_course_login($course, false, $cm, true, false, true);
                // add them to group, just in case
                if( isset( $USER->id ) && $USER->id ){
                    connect_group_access( $USER->id, $course->id, true );
                }
        	}
        }
    }
}

if (!$edit && !$archive && !$guest) {
    connectquiz_launch($acurl, $courseid, true, $cm);
}

if( $forceaddin ){
	$url.= $forceaddin == 1 ? '?launcher=true' : '?launcher=false';	
}

$launch_url = connect_get_launch_url($url, 'cquiz', $edit, $archive, $guest, 'connectquiz');
if (is_object($launch_url)){
    print_error($launch_url->error);
} else {
    $event = \mod_connectquiz\event\course_module_viewed::create(array(
        'objectid' => $cm->instance,
        'context' => context_module::instance($cm->id),
    ));
    $event->add_record_snapshot('course', $course);
    // In the next line you can use $PAGE->activityrecord if you have set it, or skip this line if you don't have a record.
    $event->add_record_snapshot('connectquiz', $connectquiz);
    $event->trigger();
    header("Location: " . $launch_url);
}
exit(1);

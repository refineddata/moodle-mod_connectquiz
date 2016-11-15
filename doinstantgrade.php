<?php // $Id: connect.php,v 1.00 2008/04/07 09:37:58 terryshane Exp $
require_once('../../config.php');
require_once($CFG->dirroot . '/mod/connectquiz/lib.php');
require_once($CFG->dirroot.'/course/lib.php');
global $CFG, $DB;

$courseid = optional_param('courseid', 0, PARAM_INT);
$section = optional_param('section', 0, PARAM_INT);

$fromurl  = isset( $SESSION->fromdiscussion ) ? $SESSION->fromdiscussion : '';
if( !$fromurl && isset( $_SERVER['HTTP_REFERER'] ) ){ $fromurl = $_SERVER['HTTP_REFERER']; }
if( !$courseid && $fromurl ){
	if( preg_match( '/course\/view.php\?id=(\d+)/', $fromurl, $match ) ){
		if( isset( $match[1] ) && $match[1] && is_numeric( $match[1] ) ){
			$courseid = $match[1];	
		}
	}
}

if( !$courseid ){
	redirect( "$CFG->wwwroot", '', 0 );
}

// require_login();
$context = context_system::instance();

$USER->usercourseconnectswithgrade = '';
$USER->usercourseconnects = '';

if ( $courseid && isset( $CFG->connect_instant_regrade ) AND $CFG->connect_instant_regrade ) {
	if ( $connectquizs = $DB->get_records( 'connectquiz', array( 'course'=>$courseid ) ) ) {
		require_once( $CFG->dirroot . '/mod/connectquiz/lib.php' );
                foreach( $connectquizs as $connectquiz ) {
                    connectquiz_regrade_fullquiz( $connectquiz, true, $USER->id );
		}		
		rebuild_course_cache( $courseid );
		global $SESSION;
		unset( $SESSION->gradescorecache );
	}

    // do recordings as well
    if( file_exists( $CFG->dirroot . '/mod/rtrecording/lib.php' ) ){
        if ( $rtrecs = $DB->get_records( 'rtrecording', array( 'course'=>$courseid ) ) ) {
            require_once( $CFG->dirroot . '/mod/rtrecording/lib.php' );
            foreach( $rtrecs as $rtrec ) {
                $entries = $DB->get_records( 'rtrecording_entries', array( 'rtrecording_id' => $rtrec->id, 'userid' => $USER->id ) );
                foreach( $entries as $entry ){
                    rtrecording_do_grade_entry( $entry );
                }
            }
        }
    }
}
if( isset( $CFG->show_instant_regrade_message ) && $CFG->show_instant_regrade_message && 
		isset( $USER->usercourseconnectswithgrade ) && isset( $USER->usercourseconnects ) && 
		$USER->usercourseconnects != $USER->usercourseconnectswithgrade ){
	redirect( "$CFG->wwwroot/course/view.php?id=$courseid#section-$section", get_string( 'connect_grades_notyet', 'connectquiz' ), 25 );
}else{
	redirect( "$CFG->wwwroot/course/view.php?id=$courseid#section-$section", '', 0 );
}

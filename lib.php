<?php // $Id: lib.php
/**
 * Library of functions and constants for module connect
 *
 * @author  Gary Menezes
 * @version $Id: lib.php
 * @package connect
 **/

require_once($CFG->dirroot . '/mod/connectquiz/connectlib.php');
require_once($CFG->dirroot . '/lib/completionlib.php');

global $PAGE;
//$PAGE->requires->js('/mod/connectquiz/js/mod_connectquiz_coursepage.js');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted connect record
 **/
function connectquiz_add_instance($connectquiz) {
    global $CFG, $USER, $COURSE, $DB;
    require_once($CFG->libdir . '/gdlib.php');

    $cmid = $connectquiz->coursemodule;

    $connectquiz->timemodified = time();
    // complete url for video to check
    
    if (empty($connectquiz->url) and !empty($connectquiz->newurl)) {
        $connectquiz->url = $connectquiz->newurl;
    }
    
    
    	$connectquiz->url = preg_replace( '/\//', '', $connectquiz->url ); // if someone tries to save with slashes, get ride of it
    

    $connectquiz->display = '';
    $connectquiz->complete = 0;
    
    if( isset( $connectquiz->addinroles ) && is_array( $connectquiz->addinroles ) ){
    	$connectquiz->addinroles = implode( ',', $connectquiz->addinroles );
    }
    
    if( !isset( $connectquiz->displayoncourse ) ) $connectquiz->displayoncourse = 0;

    //insert instance
    if ($connectquiz->id = $DB->insert_record("connectquiz", $connectquiz)) {
        // Update display to include ID and save custom file if needed
        $connectquiz = connectquiz_set_forceicon($connectquiz);
        $display = connectquiz_translate_display($connectquiz);
        if ($display != $connectquiz->display) {
            $DB->set_field('connectquiz', 'display', $display, array('id' => $connectquiz->id));
            $connectquiz->display = $display;
        }

        // Save the grading
        $DB->delete_records('connectquiz_grading', array('connectquizid' => $connectquiz->id));
        if (isset($connectquiz->detailgrading) && $connectquiz->detailgrading) {
            for ($i = 1; $i < 4; $i++) {

                $grading = new stdClass;
                $grading->connectquizid = $connectquiz->id;
                if ($connectquiz->detailgrading == 3) {
                    $grading->threshold = $connectquiz->vpthreshold[$i];
                    $grading->grade = $connectquiz->vpgrade[$i];
                } else {
                    $grading->threshold = $connectquiz->threshold[$i];
                    $grading->grade = $connectquiz->grade[$i];
                }
                if (!$DB->insert_record('connectquiz_grading', $grading, false)) {
                    return "Could not save connect grading.";
                }
            }
        }

        if (isset($connectquiz->reminders) && $connectquiz->reminders) {
            $event = new stdClass();
            $event->name = $connectquiz->name;
            $event->description = isset($connectquiz->intro) ? $connectquiz->intro : '';
            $event->format = 1;
            $event->courseid = $connectquiz->course;
            $event->modulename = (empty($CFG->connect_courseevents) OR !$CFG->connect_courseevents) ? 'connectquiz' : '';
            $event->instance = (empty($CFG->connect_courseevents) OR !$CFG->connect_courseevents) ? $connectquiz->id : 0;
            $event->eventtype = 'course';
            $event->timestart = $connectquiz->start;
            $event->timeduration = $connectquiz->duration;
            $event->uuid = '';
            $event->visible = 1;
            $event->acurl = $connectquiz->url;
            $event->timemodified = time();

            if ($event->id = $DB->insert_record('event', $event)) {
                $DB->set_field('connectquiz', 'eventid', $event->id, array('id' => $connectquiz->id));
                $connectquiz->eventid = $event->id;
                if (isset($CFG->local_reminders) AND $CFG->local_reminders) {
                    require_once($CFG->dirroot . '/local/reminders/lib.php');
                    reminders_update($event->id, $connectquiz);
                }
            }
        }
        // Create meeting on connect
            if (!empty($connectquiz->url)) {
            	if (!empty($COURSE)) $course = $COURSE;
            	else $course = $DB->get_record('course', 'id', $connectquiz->course);
            	$result = connect_use_sco($connectquiz->id, $connectquiz->url, $connectquiz->type, $course->id);
            	if (!$result) {
            		return false;
            	}
            }
    }

    //create grade item for locking
    $entry = new stdClass;
    $entry->grade = 0;
    $entry->userid = $USER->id;
    connectquiz_gradebook_update($connectquiz, $entry);

    connectquiz_update_from_adobe( $connectquiz );

    return $connectquiz->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function connectquiz_update_instance($connectquiz) {
    global $CFG, $DB;

    $connectquiz->timemodified = time();
    

    if (!isset($connectquiz->detailgrading)) {
        $connectquiz->detailgrading = 0;
    }

    if (isset($connectquiz->iconsize) && $connectquiz->iconsize == 'custom') {
        $connectquiz = connectquiz_set_forceicon($connectquiz);
    } else {
        $connectquiz->forceicon = '';
    }
    $connectquiz->display = connectquiz_translate_display($connectquiz);
    $connectquiz->complete = 0;
    
    
    	$connectquiz->url = preg_replace( '/\//', '', $connectquiz->url ); // if someone tries to save with slashes, get ride of it
    
    
    if( isset( $connectquiz->addinroles ) && is_array( $connectquiz->addinroles ) ){
    	$connectquiz->addinroles = implode( ',', $connectquiz->addinroles );
    }
    
    if( !isset( $connectquiz->displayoncourse ) ) $connectquiz->displayoncourse = 0;
    
    //update instance
    if (!$DB->update_record("connectquiz", $connectquiz)) {
        return false;
    }

    // Save the grading
    $DB->delete_records('connectquiz_grading', array('connectquizid' => $connectquiz->id));
    if (isset($connectquiz->detailgrading) && $connectquiz->detailgrading) {
        for ($i = 1; $i < 4; $i++) {
            $grading = new stdClass;
            $grading->connectquizid = $connectquiz->id;
            if ($connectquiz->detailgrading == 3) {
                $grading->threshold = $connectquiz->vpthreshold[$i];
                $grading->grade = $connectquiz->vpgrade[$i];
            } else {
                $grading->threshold = $connectquiz->threshold[$i];
                $grading->grade = $connectquiz->grade[$i];
            }
            $grading->timemodified = time();
            if (!$DB->insert_record('connectquiz_grading', $grading, false)) {
                return false;
            }
        }
    }

    if (isset($connectquiz->reminders) && $connectquiz->reminders) {
        if (isset($connectquiz->eventid) AND $connectquiz->eventid){
        	$event = $DB->get_record('event', array('id' => $connectquiz->eventid));
        }else{
        	$event = new stdClass();
        }

        $event->name = $connectquiz->name;
        $event->description = isset($connectquiz->intro) ? $connectquiz->intro : '';
        $event->format = 1;
        $event->courseid = $connectquiz->course;
        $event->modulename = (empty($CFG->connect_courseevents) OR !$CFG->connect_courseevents) ? 'connectquiz' : '';
        $event->instance = (empty($CFG->connect_courseevents) OR !$CFG->connect_courseevents) ? $connectquiz->id : 0;
        $event->timestart = $connectquiz->start;
        $event->timeduration = $connectquiz->duration;
        $event->visible = 1;
        $event->uuid = '';
        $event->sequence = 1;
        $event->acurl = $connectquiz->url;
        $event->timemodified = time();

        if (isset($event->id) AND $event->id) $DB->update_record('event', $event);
        else $event->id = $DB->insert_record('event', $event);

        if (isset($event->id) AND $event->id) {
            if ($connectquiz->eventid != $event->id){
                $DB->set_field('connectquiz', 'eventid', $event->id, array('id' => $connectquiz->id));
                $connectquiz->eventid = $event->id;
            }
            if (isset($CFG->local_reminders) AND $CFG->local_reminders) {
                $DB->delete_records('reminders', array('event' => $event->id));
                require_once($CFG->dirroot . '/local/reminders/lib.php');
                reminders_update($event->id, $connectquiz);
            }
        }
    } elseif (isset($connectquiz->eventid) AND $connectquiz->eventid) {
        $DB->delete_records('reminders', array('event' => $connectquiz->eventid));
        $DB->delete_records('event', array('id' => $connectquiz->eventid));
    }

    // Update connect
    if (isset($CFG->connect_update) AND $CFG->connect_update AND !empty($connectquiz->url)) {
        $date_begin = 0;
        $date_end = 0;
        if (isset($CFG->connect_updatedts) AND $CFG->connect_updatedts && isset( $connectquiz->start ) && isset( $connectquiz->duration )) {
            $date_begin = $connectquiz->start;
            $date_end = $connectquiz->start + $connectquiz->duration;
        }
        connect_update_sco($connectquiz->id, $connectquiz->name, $connectquiz->intro, $date_begin, $date_end, 'cquiz');
    }

    //create grade item for locking
    global $USER;
    $entry = new stdClass;
    $entry->grade = 0; 
    $entry->userid = $USER->id;
    connectquiz_gradebook_update($connectquiz, $entry);

    connectquiz_update_from_adobe( $connectquiz );

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function connectquiz_delete_instance($id) {
    global $DB;

    if (!$connectquiz = $DB->get_record('connectquiz', array('id' => $id))) {
        return false;
    }

    // Delete area files (must be done before deleting the instance)
    $cm = get_coursemodule_from_instance('connectquiz', $id);
    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_connectquiz');

    // Delete dependent records
    if (isset($connectquiz->eventid) AND $connectquiz->eventid) $DB->delete_records('reminders', array('event' => $connectquiz->eventid));
    if (isset($connectquiz->eventid) AND $connectquiz->eventid) $DB->delete_records('event', array('id' => $connectquiz->eventid));

    // Delete connect records
    $DB->delete_records("connectquiz_grading", array("connectquizid" => $id));
    $DB->delete_records("connectquiz_entries", array("connectquizid" => $id));
    //$DB->delete_records("connectquiz_recurring", array("connectquizid" => $id));
    $DB->delete_records("connectquiz", array('id' => $id));

    return true;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 **/
function connectquiz_user_outline($course, $user, $mod, $connectquiz) {
    global $DB;

    if ($grade = $DB->get_record('connectquiz_entries', array('userid' => $user->id, 'connectquizid' => $connectquiz->id))) {

        $result = new stdClass;
        if ((float)$grade->grade) {
            $result->info = get_string('grade') . ':&nbsp;' . $grade->grade;
        }
        $result->time = $grade->timemodified;
        return $result;
    }
    return NULL;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 **/
function connectquiz_user_complete($course, $user, $mod, $connectquiz) {
    global $DB;

    if ($grade = $DB->get_record('connectquiz_entries', array('userid' => $user->id, 'connectquizid' => $connectquiz->id))) {
        echo get_string('grade') . ': ' . $grade->grade;
        echo ' - ' . userdate($grade->timemodified) . '<br />';
    } else {
        print_string('nogrades', 'connectquiz');
    }

    return true;
}



/**
 * Runs each time cron runs.
 *  Updates meeting completion and recurring meetings.
 *  Gets and processes entries who's recheck time has elapsed.
 *
 * @return boolean
 **/
function connectquiz_cron_task() {
    echo '+++++ connectquiz_cron'."\n";
    global $CFG, $DB;
    $now = time();

    
    //Instant Grading - just return
    //if (isset($CFG->connect_instant_grade) AND $CFG->connect_instant_grade == 1) return true;
    //echo "SELECT * FROM {$CFG->prefix}connectquiz_entries WHERE rechecks > 0 AND rechecktime < $now \n";
    //Entries Every 15min
    if (!$entries = $DB->get_records_sql("SELECT * FROM {$CFG->prefix}connectquiz_entries WHERE rechecks > 0 AND rechecktime < $now")) return true;
    
    foreach ($entries as $entry) {
        
        if (!$connectquiz = $DB->get_record("connectquiz", array("id" => $entry->connectquizid))) break;
        
        if (!$user = $DB->get_record("user", array("id" => $entry->userid))) break;
                
        $entry->timemodified = time();
        $entry->rechecks--;
        $entry->rechecktime = time() + $connectquiz->loopdelay;
        if ($entry->rechecks < 0) $entry->rechecktime = 0;
        
        $oldgrade = isset( $entry->grade ) ? $entry->grade : 0;

        if (!connectquiz_grade_entry($user->id, $connectquiz, $entry)) continue;
        
        if( $connectquiz->type == 'cquiz' && $oldgrade == 0 && $entry->grade > 0 ){
        	$event = \mod_connectquiz\event\connectquiz_quizsubmitted::create(array(
        			'objectid' => $connectquiz->id,
        			'relateduserid' => $user->id,
        			'other' => array( 'acurl' => $connectquiz->url, 'description' => "Quiz submitted: $connectquiz->name" )
        	));
        	$event->trigger();
        }        
        $DB->update_record('connectquiz_entries', $entry);

        if ($entry->grade == 100 AND $cm = get_coursemodule_from_instance('connectquiz', $connectquiz->id)) {
            // Mark Users Complete
            if ($cmcomp = $DB->get_record('course_modules_completion', array('coursemoduleid' => $cm->id, 'userid' => $user->id))) {
                $cmcomp->completionstate = 1;
                $cmcomp->viewed = 1;
                $cmcomp->timemodified = time();
                $DB->update_record('course_modules_completion', $cmcomp);
            } else {
                $cmcomp = new stdClass;
                $cmcomp->coursemoduleid = $cm->id;
                $cmcomp->userid = $user->id;
                $cmcomp->completionstate = 1;
                $cmcomp->viewed = 1;
                $cmcomp->timemodified = time();
                $DB->insert_record('course_modules_completion', $cmcomp);
            }
            rebuild_course_cache($connectquiz->course);
        }
    }
    return true;
}

function connectquiz_grade_based_on_range( $userid, $connectquizid, $startdaterange, $enddaterange, $regrade ){
    if( function_exists( 'local_connect_grade_based_on_range' ) ){
        return local_connect_grade_based_on_range( $userid, $connectquizid, $startdaterange, $enddaterange, $regrade, 'connectquiz' );
    }else{
        return false;
    }
}

function connectquiz_complete_meeting($connectquiz, $startdaterange = 0, $enddaterange = 0) {
    global $CFG, $DB;

    $regrade = $startdaterange ? 1 : 0; // if we are passed a date range, this is a regrade
    if( !$startdaterange ){
        $startdaterange = $connectquiz->start;
        $enddaterange = $connectquiz->start + $connectquiz->compdelay + (60*60*2);
    }

    if ($connectquiz->start > 0 AND ($connectquiz->start + $connectquiz->compdelay) < time() AND $connectquiz->complete == 0) {
        $complete = true;
    } else {
        $complete = false;
    }

    $cm = get_coursemodule_from_instance('connectquiz', $connectquiz->id);
    if ($cm && connectquiz_grade_meeting(0, '', $connectquiz, $startdaterange, $enddaterange, $regrade)) {
        $context = context_course::instance($connectquiz->course);
        $course = $DB->get_record('course', array('id' => $connectquiz->course));
        if ($users = get_enrolled_users($context)) {
            //Certificate Setup
            if ($DB->get_record('modules', array('name' => 'certificate'))) {
                global $certificate; // To deal with bad code in certificate_issue;
                if ($connectquiz->autocert AND $certificate = $DB->get_record('certificate', array('id' => $connectquiz->autocert))) {
                    require_once($CFG->dirroot . '/mod/certificate/lib.php');
                    require_once($CFG->libdir . '/pdflib.php');
                    $cmcert = get_coursemodule_from_instance('certificate', $certificate->id);
                    $certctx = get_context_instance(CONTEXT_MODULE, $cmcert->id);
                }
            }

            //Loop through each user
            foreach ($users as $user) {
                
                // skip them if they have a grade outside the range
                if( !connectquiz_grade_based_on_range( $user->id, $connectquiz->id, $startdaterange, $enddaterange, $regrade ) ) continue;

                if ($grade = $DB->get_field('connectquiz_entries', 'grade', array('connectquizid' => $connectquiz->id, 'userid' => $user->id)) AND $grade == 100) {
                    // Mark Users Complete
                    if ($cmcomp = $DB->get_record('course_modules_completion', array('coursemoduleid' => $cm->id, 'userid' => $user->id))) {
                        $cmcomp->completionstate = 1;
                        $cmcomp->viewed = 1;
                        $cmcomp->timemodified = time();
                        $DB->update_record('course_modules_completion', $cmcomp);
                    } else {
                        $cmcomp = new stdClass;
                        $cmcomp->coursemoduleid = $cm->id;
                        $cmcomp->userid = $user->id;
                        $cmcomp->completionstate = 1;
                        $cmcomp->viewed = 1;
                        $cmcomp->timemodified = time();
                        $DB->insert_record('course_modules_completion', $cmcomp);
                    }

                    // Issue Certificates
                    if (!empty($certctx) AND !$DB->get_record('certificate_issues', array('certificateid' => $certificate->id, 'userid' => $user->id, 'notified' => 1))) {
                        global $USER, $pdf, $certificate;
                        $session_user = $USER;
                        //TODO: Remove the $USER cloning
                        $USER = clone($user);
                        if ($certrecord = certificate_get_issue($course, $user, $certificate, $cmcert)) {
                            //RT-1458 Certificates not being emailed / Activity completion not updating for certificates when issued from meeting completion.
                            //It is because we remove $certificate->savecert setting not to save pdf file in the file system
                            //if ($certificate->savecert) {

                                $studentname = '';
                                $student = $user;
                                $certrecord->studentname = $student->firstname . ' ' . $student->lastname;

                                $classname = '';
                                $certrecord->classname = $course->fullname;

                                require($CFG->dirroot . '/mod/certificate/type/' . $certificate->certificatetype . '/certificate.php');
                                $file_contents = $pdf->Output('', 'S');
                                $filename = clean_filename($certificate->name . '.pdf');
                                certificate_save_pdf($file_contents, $certrecord->id, $filename, $certctx->id, $user);

                                if ($certificate->delivery == 2) {
                                    certificate_email_student($course, $certificate, $certrecord, $certctx, $user, $file_contents);
                                }
                            
                                // Mark certificate as viewed
                                $cm = get_coursemodule_from_instance('certificate', $certificate->id, $certificate->course);
                                $completion = new completion_info($course);
                                $completion->set_module_viewed($cm, $user->id);
                            //}
                        }
                        $USER = $session_user;
                    }
                } else $grade = 0;

                // Unenrol All(1), Attended(2) or Absent(3)
                if ( !$regrade && ( ($grade == 100 AND $connectquiz->unenrol == 2) OR ($complete AND ($connectquiz->unenrol == 1 OR ($grade < 100 AND $connectquiz->unenrol == 3))))) {
                    if ($enrols = $DB->get_records_sql("SELECT e.* FROM {$CFG->prefix}user_enrolments u, {$CFG->prefix}enrol e WHERE u.enrolid = e.id AND u.userid = {$user->id} AND e.courseid = {$connectquiz->course}")) {
                        foreach ($enrols as $enrol) {
                            $plugin = enrol_get_plugin($enrol->enrol);
                            $plugin->unenrol_user($enrol, $user->id);
                        }
                    }
                    role_unassign($CFG->studentrole, $user->id, $context->id);
                }
            }
        }

        // Attendance Report
        if ( !$regrade && $complete AND !empty($connectquiz->email)) {
            require_once($CFG->dirroot . '/filter/connect/lib.php');
            if (!$to = $DB->get_record('user', array('email' => $connectquiz->email))) {
                $to = new stdClass;
                $to->firstname = 'Attendance';
                $to->lastname = 'Report';
                $to->email = $connectquiz->email;
                $to->mailformat = 1;
                $to->maildisplay = true;
            }
            $subj = 'Attendance Report for ' . $connectquiz->url;
            $body = connectquiz_attendance_output($connectquiz->url);
            $text = html_to_text($body);
            email_to_user($to, 'LMS Admin', $subj, $text, $body);
        }

        if (!$regrade && $complete) {
            // Next instance or mark complete
            if ($instance = $DB->get_record_sql("SELECT * FROM {$CFG->prefix}connectquiz_recurring WHERE connectquizid={$connectquiz->id} AND record_used=0 ORDER BY start LIMIT 1")) {
                $newurl = false;
                if ($connectquiz->url != $instance->url) $newurl = true;

                $connectquiz->start = $instance->start;
                $connectquiz->display = str_replace($connectquiz->url, $instance->url, $connectquiz->display);
                $connectquiz->url = $instance->url;
                $connectquiz->email = $instance->email;
                $connectquiz->eventid = $instance->eventid;
                $connectquiz->unenrol = $instance->unenrol;
                $connectquiz->compdelay = $instance->compdelay;
                $connectquiz->autocert = $instance->autocert;
                $connectquiz->timemodified = time();

                // Update Adobe
                $date_begin = 0;
                $date_end = 0;
                if (isset($CFG->connect_updatedts) AND $CFG->connect_updatedts) {
                    $date_begin = $connectquiz->start;
                    $date_end = $connectquiz->start + $instance->duration;
                }
                connect_update_sco($connectquiz->id, $connectquiz->name, $connectquiz->intro, $date_begin, $date_end, 'cquiz');

                if (isset($newurl) AND $newurl) connect_add_access($connectquiz->id, $course->id, 'group', 'view', false, 'cquiz');

                // Update Grouping
                if (isset($instance->groupingid) AND $instance->groupingid AND $cm) {
                    $cm->groupingid = $instance->groupingid;
                    $DB->update_record('course_modules', $cm);
                }

                $instance->record_used = 1;
                $DB->update_record('connectquiz_recurring', $instance);
            } else $connectquiz->complete = 1;

            rebuild_course_cache($connectquiz->course);
            $DB->update_record('connectquiz', $connectquiz);
        }
    }

    return;
}

function connectquiz_process_options(&$connectquiz) {
    return true;
}

function connectquiz_install() {
    return true;
}

function connectquiz_get_view_actions() {
    return array('launch', 'view all');
}

function connectquiz_get_post_actions() {
    return array('');
}

function connectquiz_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return false;

        default:
            return null;
    }
}

function connectquiz_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;

    return $DB->record_exists('connectquiz_entries', array('connectquizid' => $cm->instance, 'userid' => $userid));
}

function connectquiz_cm_info_dynamic($mod) {
    global $DB, $USER;

    if (!$mod->available) return;

    $connectquiz = $DB->get_record('connectquiz', array('id' => $mod->instance));
    if (!empty($connectquiz->display) && $connectquiz->displayoncourse) {
        
            $mod->set_content( connectquiz_create_display( $connectquiz ) );
        
        // If set_no_view_link is TRUE - it's not showing on Activity Report (https://app.liquidplanner.com/space/73723/projects/show/9961959)
        if( method_exists( $mod, 'rt_set_no_view_link' ) ){
            $mod->rt_set_no_view_link();
        }
    }
    return;
}

function connectquiz_cm_info_view($mod) {
    global $CFG, $OUTPUT, $DB;    
    return;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other connect functions go here.  Each of them must have a name that
/// starts with connect_

/**
 * Called from /filters/connect/launch.php each time connect is launched.
 * Works out if it is an activity, and if so, updates the grade or sets up cron to.
 *
 * @param string $acurl The unique connect url for the resource
 * @param boolean $fullupdate Whethr all information should be updated even if max grade reached
 **/
function connectquiz_launch($acurl, $courseid = 1, $regrade = false, $cm = 0) {
    global $CFG, $USER, $DB, $PAGE;

    if (!$connectquiz = $DB->get_record('connectquiz', array('url' => $acurl, 'course' => $courseid), '*', IGNORE_MULTIPLE)) {
        return;
    }

    if (!$entry = $DB->get_record('connectquiz_entries', array('userid' => $USER->id, 'connectquizid' => $connectquiz->id))) {
        $entry = new stdClass;
        $entry->connectquizid = $connectquiz->id;
        $entry->userid = $USER->id;
        $entry->type = $connectquiz->type;
        $entry->views = 0;
    }

    if (!is_siteadmin() AND isset($CFG->connect_maxviews) AND $CFG->connect_maxviews >= 0 AND isset($connectquiz->maxviews) AND $connectquiz->maxviews > 0 AND $connectquiz->maxviews <= $entry->views) {
        $PAGE->set_url('/');
        notice(get_string('overmaxviews', 'connectquiz'), $CFG->wwwroot . '/course/view.php?id=' . $connectquiz->course);
    }

    $entry->timemodified = time();
    $entry->views++;
    
    $oldgrade = isset( $entry->grade ) ? $entry->grade : 0;

    // Without detail grading, just set the grade to 100 and return
    if (!$connectquiz->detailgrading) {
        $entry->grade = 100;
        connectquiz_gradebook_update($connectquiz, $entry);
    } elseif (!isset($entry->grade) OR $entry->grade < 100) {
        connectquiz_grade_entry($USER->id, $connectquiz, $entry);
    }
    
    if( $connectquiz->type == 'cquiz' && $oldgrade == 0 && $entry->grade > 0 ){
    	//means they had not submitted anythning before, but have now, do event
    	$event = \mod_connectquiz\event\connectquiz_quizsubmitted::create(array(
    			'objectid' => $connectquiz->id,
    			'relateduserid' => $USER->id,
    			'other' => array( 'acurl' => $connectquiz->url, 'description' => "Quiz submitted: $connectquiz->name" )
    	));
    	$event->trigger();
    }
    

    $entry->rechecks = $entry->grade == 100 ? 0 : $connectquiz->loops;
    $entry->rechecktime = $entry->grade == 100 ? 0 : time() + $connectquiz->initdelay;

    if (!isset($entry->id)) {
        $DB->insert_record('connectquiz_entries', $entry);
    } else {
        $DB->update_record('connectquiz_entries', $entry);
    }

    if ($cm) {
        $course = $DB->get_record('course', array('id' => $courseid));
        //error_log('+++ $course' . json_encode($course));
        $completion = new completion_info($course);
        if ($completion->is_enabled($cm)) {
            if ( $cm->completiongradeitemnumber == null and $cm->completionview == 1){
                $completion->set_module_viewed($cm);
            }
        }
    }

    //if ($regrade) return;

    if ($cm) {
    	$description = '';
    	$action = '';
    	

    			$scores = connect_sco_scores($connectquiz->id, $USER->id, 'cquiz');
    			if (isset($scores->slides)) {
    				$entry->slides = (int)$scores->slides;
    			}
    			$description = "Slides: $entry->slides";
    			$description.= ", Score: $scores->score";
    			$description.= ", Grade: $entry->grade";
    			$description.= ", Views: $entry->views";
    			$description.= ", Connect ID: $entry->connectquizid";
    			$action = 'connect_quiz';
    			
    	
        $event = \mod_connectquiz\event\connectquiz_launch::create(array(
            'objectid' => $connectquiz->id,
            'other' => array('acurl' => $acurl, 'description' => "$action - $connectquiz->name ( $acurl ) - $description")
        ));
        $event->trigger();
    }
}


/**
 * returns updated entry record based on grading
 * called from launch and cron
 *
 * @param char $url Custom URL of Adobe connect Resource
 * @param char $userid Login acp_login (Adobe connect Username)
 * @param object $connectquiz Original connect record
 * @param object $entry Original entry record
 **/
function connectquiz_grade_entry($userid, $connectquiz, &$entry, $scores = null) {
    global $CFG, $DB;

    if (!$scores) $scores = connect_sco_scores($connectquiz->id, $userid, 'cquiz');
    //error_log('==== $scores: ' . json_encode($scores));
    if (isset($scores->score)) {
        $entry->score = (int)$scores->score;
        $threshold = (int)$scores->score;
    } else $threshold = 0;

    if ($specs = $DB->get_field_sql("SELECT MAX(grade) AS grade FROM {$CFG->prefix}connectquiz_grading WHERE connectquizid = {$connectquiz->id} AND threshold <= $threshold AND threshold > 0")) {
        $grade = (int)$specs;
    } elseif ($specs = $DB->get_field_sql("SELECT MAX(grade) AS grade FROM {$CFG->prefix}connectquiz_grading WHERE connectquizid = {$connectquiz->id} AND threshold > 0")) {
        $grade = 0;
    } else $grade = (int)$threshold;

    if (!isset($entry->grade) OR $entry->grade < $grade) {
        $entry->grade = $grade;
        connectquiz_gradebook_update($connectquiz, $entry);
    }

    if ($grade == 100) {
        $entry->rechecks = 0;
        $entry->rechecktime = 0;
    }

    return true;
}

/**
 * Update gradebook
 *
 * @param object $entry connect instance
 */
function connectquiz_gradebook_update($connectquiz, $entry) {
    if( function_exists( 'local_connect_gradebook_update' ) ){
        return local_connect_gradebook_update( $connectquiz, $entry, 'connectquiz' );
    }else{
        return false;
    }
}

function connectquiz_update_from_adobe( &$connectquiz ){
    global $DB;

    $sco = connect_get_sco_by_url( $connectquiz->url, 1 );
    if( $sco ){
        if(isset( $sco->name ))$connectquiz->name = $sco->name;
        if(isset( $sco->desc ))$connectquiz->intro = $sco->desc;
        if(isset( $sco->archive ))$connectquiz->ac_archive = $sco->archive;
        if(isset($sco->type))$connectquiz->ac_type = $sco->type;
        if(isset($sco->phone))$connectquiz->ac_phone = $sco->phone;
        if(isset($sco->pphone))$connectquiz->ac_pphone = $sco->pphone;
        if(isset($sco->id))$connectquiz->ac_id=$sco->id;
        if(isset($sco->views))$connectquiz->ac_views = $sco->views;
        $DB->update_record( 'connectquiz', $connectquiz );
    }
}

function connectquiz_translate_display($connectquiz, $forviewpage = 0) {
    global $CFG;

    
        if ( !$forviewpage && (empty($connectquiz->url) OR empty($connectquiz->iconsize) OR $connectquiz->iconsize == 'none')) return ''; 
        $flags = '-';

        if (!empty($connectquiz->iconpos) AND $connectquiz->iconpos) $flags .= $connectquiz->iconpos;
        if (!empty($connectquiz->iconsilent) AND $connectquiz->iconsilent) $flags .= 's';
        if (!empty($connectquiz->iconphone) AND $connectquiz->iconphone) $flags .= 'p';
        //if (!empty($connectquiz->iconmouse) AND $connectquiz->iconmouse) $flags .= 'm';
        if (!empty($connectquiz->iconguests) AND $connectquiz->iconguests) $flags .= 'g';
        if (!empty($connectquiz->iconnorec) AND $connectquiz->iconnorec) $flags .= 'a';

        $start = ''; //TODO - get start and end from Restrict Access area
        $end = ''; 
        $extrahtml = empty($connectquiz->extrahtml) ? '' : $connectquiz->extrahtml;

        if( !isset( $connectquiz->iconsize ) )$connectquiz->iconsize = 'large';
        $options = $connectquiz->iconsize . $flags . '~' . $start . '~' . $end . '~' . $extrahtml . '~' . $connectquiz->forceicon . '~' . $connectquiz->id;

        $display = '<div class="connectquiz_display_block" ';
        $display.= 'data-courseid="' . $connectquiz->course . '" ';
        $display.= 'data-acurl="' . $connectquiz->url . '" ';
        $display.= 'data-sco="' . json_encode(false) . '" ';
        $display.= 'data-options="' . preg_replace( '/"/', '%%quote%%', $options ) . '" ';
        $display.= 'data-frommymeetings="0" ';
        $display.= 'data-frommyrecordings="0" >'
            . '<div id="id_ajax_spin" class="rt-loading-image"></div>'
            . '</div>';
        
//        $display = '[[connect#' . $connectquiz->url . '#' . $connectquiz->iconsize . $flags . '#' . $start . '#' . $end . '#' . $extrahtml . '#' . $connectquiz->forceicon . '#' . $connectquiz->id . ']]';

        return $display;
    
}

function connectquiz_create_display( $connectquiz ){
    global $USER, $CFG, $PAGE, $DB, $OUTPUT;

    if( !$connectquiz ){
        return '<div style="text-align:center;"><img src="' . $CFG->wwwroot
            . '/mod/connectquiz/images/notfound.gif"/><br/>'
            . get_string('notfound', 'connectquiz')
            . '</div>';
    }

    if( !$connectquiz->ac_id ){ // no ac id, probably first load of this activity after upgrade, lets update
        connectquiz_update_from_adobe( $connectquiz );
        if( !$connectquiz->ac_id ){// must no longer exist in AC
            return '<div style="text-align:center;"><img src="' . $CFG->wwwroot
            . '/mod/connectquiz/images/notfound.gif"/><br/>'
            . get_string('notfound', 'connectquiz')
            . '</div>';
        }
    }

    if( !$connectquiz->display || preg_match( '/\[\[/', $connectquiz->display ) ){
        $connectquiz = connectquiz_set_forceicon($connectquiz);
        $connectquiz->display = connectquiz_translate_display( $connectquiz, 1 );
        $DB->update_record( 'connectquiz', $connectquiz );
    }   
    preg_match('/data-options="([^"]+)"/', $connectquiz->display, $matches);
    if( isset( $matches[1] ) ){
        $element = explode('~', $matches[1] );
    }

    $sizes = array(
        "medium" => "_md",
        "med" => "_md",
        "md" => "_md",
        "_md" => "_md",
        "small" => "_sm",
        "sml" => "_sm",
        "sm" => "_sm",
        "_sm" => "_sm",
        "block" => "_sm",
        "sidebar" => "_sm"
    );
    $types = array("meeting" => "meeting", "content" => "presentation");
    $breaks = array("_md" => "<br/>", "_sm" => "<br/>");

    $thisdir = $CFG->wwwroot . '/mod/connectquiz';


    $iconsize = '';
    $iconalign = 'center';
    $silent = false;
    $telephony = true;
    $mouseovers = true;
    $allowguests = false;
    $viewlimit = '';    

    if (isset($element[0])) {
        $iconopts = explode("-", strtolower($element[0]));
        $iconsize = empty($iconopts[0]) ? '' : $iconopts[0];
        if (isset($iconopts[1])) {
            $silent = strpos($iconopts[1], 's') !== false; // no text output
            $autoarchive = strpos($iconopts[1], 'a') === false; // point to the recording unless the 'a' is included
            $telephony = strpos($iconopts[1], 'p') === false; // no phone info
            $allowguests = strpos($iconopts[1], 'g') !== false; // allow guest user access
            //$mouseovers = strpos($iconopts[1], 'm') === false; // no mouseover
            if (strpos($iconopts[1], 'l') !== false) $iconalign = 'left';
            elseif (strpos($iconopts[1], 'r') !== false) $iconalign = 'right';
        }
    }
    if (empty($CFG->connect_telephony))
        $telephony = false;
    //if (empty($CFG->connect_mouseovers))
       // $mouseovers = false;

    $startdate = empty($element[1]) ? '' : $element[1];
    $enddate = empty($element[2]) ? '' : $element[2];
    $extra_html = empty($element[3]) ? '' : $element[3];
    $extra_html = preg_replace( '/%%quote%%/', '"', $extra_html );
    $force_icon = empty($element[4]) ? '' : $element[4];
    $connectquizid = empty($element[5]) ? 0 : $element[5];
    $grouping = '';

    if (!(!empty($PAGE->context) && $PAGE->user_allowed_editing())) {
        if (!empty($startdate) and time() < strtotime($startdate)) return;
        if (!empty($enddate) and time() > strtotime($enddate)) return;
    } else $nomouseover = false;

    if ($connectquiz->start) {
        $connectquiz->end = $connectquiz->start + $connectquiz->duration;
    }elseif ($connectquiz->eventid AND $event = $DB->get_record('event', array('id' => $connectquiz->eventid))) {
        $connectquiz->start = $event->timestart;
        $connectquiz->end = $event->timestart + $event->timeduration;
    }else{
        $connectquiz->end = 0;
    }
    if ($connectquiz->end > time()) unset($connectquiz->ac_archive);
    if ($connectquiz->maxviews) {
        if (!$views = $DB->get_field('connectquiz_entries', 'views', array('connectquizid' => $connectquiz->id, 'userid' => $USER->id))) $views = 0;
        $viewlimit = get_string('viewlimit', 'connectquiz') . $views . '/' . $connectquiz->maxviews . '<br/>';
    }

    // Check for grouping
    $grouping = '';
    $mod = get_coursemodule_from_instance('connectquiz', $connectquiz->id, $connectquiz->course);
    if (!empty($mod->groupingid) && has_capability('moodle/course:managegroups', context_course::instance($mod->course))) {
        $groupings = groups_get_all_groupings($mod->course);
        $textclasses = isset( $textclasses ) ? $textclasses : '';
        $grouping = html_writer::tag('span', '('.format_string($groupings[$mod->groupingid]->name).')',
                array('class' => 'groupinglabel '.$textclasses));
    }

    // check for addin launch settings
    if( isset( $CFG->connect_adobe_addin ) && $CFG->connect_adobe_addin && isset( $connectquiz->addinroles ) && $connectquiz->addinroles ){
        $forceaddin = 1;
        $roleids = explode( ',', $connectquiz->addinroles );
        $userroles = get_user_roles( context_course::instance( $connectquiz->course ), $USER->id );
        foreach( $userroles as $userrole ){
            if( in_array( $userrole->roleid, $roleids ) ){
                $forceaddin = 2; // one of there roles is marked to launch from browser
                break;
            }
        }
    }

    // Custom icon from activity settings
    if (!empty($force_icon)) {
        // get the custom icon file url
        // TODO consider storing file name in display so as not to fetch it from the database here
        if ($cm = get_coursemodule_from_instance('connectquiz', $connectquiz->id, $connectquiz->course, false)) {
            $context = context_module::instance($cm->id);
            $fs = get_file_storage();
            if ($files = $fs->get_area_files($context->id, 'mod_connectquiz', 'content', 0, 'sortorder', false)) {
                $iconfile = reset($files);

                $filename = $iconfile->get_filename();
                $path = "/$context->id/mod_connectquiz/content/0";
                $iconurl = moodle_url::make_file_url('/pluginfile.php', "$path/$filename");
                $iconsize = '';
                $icondiv = 'force_icon';
            }
        }

        // Custom icon from editor has the url in the force icon but no connect id
    } else if (!$connectquiz->id and !empty($force_icon)) {
        $iconurl = $force_icon;
        $iconsize = '';
        $icondiv = 'force_icon';
    }

    // No custom icon, see if there is a custom default for this type
    if (empty($iconurl)) {
        $icontype = 'quiz';
        
        $iconsize = isset($sizes[$iconsize]) ? $sizes[$iconsize] : '';

        $context = context_system::instance();
        $fs = get_file_storage();
        if ($files = $fs->get_area_files($context->id, 'mod_connectquiz', $icontype . '_icon', 0, 'sortorder', false)) {
            $iconfile = reset($files);

            $filename = $iconfile->get_filename();
            $path = "/$context->id/mod_connectquiz/{$icontype}_icon/0";
            $iconurl = moodle_url::make_file_url('/pluginfile.php', "$path/$filename");
            $icondiv = $icontype . '_icon' . $iconsize;

            if ($iconsize == '_md') {
                $iconforcewidth = 120;
            } elseif ($iconsize == '_sm') {
                $iconforcewidth = 60;
            } else {
                $iconforcewidth = 180;
            }

        }
    }

    // No custom icon so just display the default icon
    if (empty($iconurl)) {
        $scotype = 'content';
        $icontype = isset($types[$scotype]) ? $types[$scotype] : 'misc';
        if ($autoarchive AND !empty($sco->archive)) $icontype = 'archive';
        $iconsize = isset($sizes[$iconsize]) ? $sizes[$iconsize] : '';
        $iconurl = new moodle_url("/mod/connectquiz/images/$icontype$iconsize.jpg");
        $icondiv = $icontype . '_icon' . $iconsize;
    }

    $strtime = '';
    if ($connectquiz->ac_type == 'meeting' AND $connectquiz->end > time()) {
        $strtime .= userdate($connectquiz->start, '%a %b %d, %Y', $USER->timezone);
        if ($iconsize == '_md' OR $iconsize == '_sm') $strtime .= "<br/>";
        $strtime .= userdate($connectquiz->start, "@ %I:%M%p") . ' - ';
        $strtime .= userdate($connectquiz->end, "%I:%M%p ") . connectquiz_mod_tzabbr() . '<br/>';
    }

    $strtele = '';
    if ($connectquiz->ac_type == 'meeting' AND $telephony AND $connectquiz->end > time()) {
        $strtele .= '<b>';
        if (!empty($connectquiz->ac_phone)) {
            $strtele .= get_string('tollfree', 'connectquiz') . ' ' . $connectquiz->ac_phone;
            if ($iconsize == '_md' OR $iconsize == '_sm') $strtele .= "<br/>";
        }
        if (!empty($connectquiz->ac_pphone)){
            $strtele .= " (";
            $strtele .= get_string('pphone', 'connectquiz');
            $strtele .= $connectquiz->ac_pphone . ')';
        }
        $strtele .= '</b><br/>';
    }

    if (!$silent) {
        $font = '<font>';
        if ($iconsize == '_sm') {
            $font = '<font size="1">';
        }
        $instancename = html_writer::tag('span', $connectquiz->name, array('class' => 'instancename')) . '<br/>';
        $aftertext = $font . $instancename . $strtime . $strtele . $viewlimit . $grouping . $extra_html . '</font>';
    } else {
        $aftertext = $extra_html;
    }

    $archive = '';
    if ($autoarchive AND !empty($connectquiz->ac_archive)) $archive = '&archive=' . $connectquiz->ac_archive;

    if( !isset( $forceaddin ) || !$forceaddin ){
        $forceaddin = 0;
    }
    $linktarget = $forceaddin == 1 ? '_self' : '_blank';

    $link = $thisdir . '/launch.php?acurl='.$connectquiz->url.'&connect_id=' . $connectquiz->id . $archive . '&guests=' . ($allowguests ? 1 : 0) . '&course=' . $connectquiz->course.'&forceaddin='.$forceaddin;

    $overtext = '';
    if ($mouseovers || is_siteadmin($USER)) {
        $overtext = '<div align="right"><br /><br /><br />';
        /*$overtext .= '<div align="left"><a href="' . $link . '" target="'.$linktarget.'" >';
        if (!empty($archive)) $overtext .= '<b>' . get_string('launch_archive', 'connectquiz') . '</a></b><br/>';
        else $overtext .= '<b>' . get_string('launch_' . $connectquiz->ac_type, 'connectquiz') . '</a></b><br/>';*/

        if (!empty($connectquiz->intro)) {
            $search = '/\[\[user#([^\]]+)\]\]/is';
            $connectquiz->intro = preg_replace_callback($search, 'mod_connectquiz_user_callback', $connectquiz->intro);
            $overtext .= str_replace("\n", "<br />", $connectquiz->intro) . '<br/>';
        }
        $overtext .= $strtime . $strtele;

        if (!empty($PAGE->context->id) && $PAGE->user_allowed_editing() && !empty($USER->editing) && empty(strstr($PAGE->url, 'launch'))) {
            if( $course = $DB->get_record( 'course', array( 'id' => $connectquiz->course ) ) ){
                $editcontext = context_course::instance($course->id);
            }else{
                $editcontext = context_system::instance();
            }
            if (has_capability('filter/connect:editresource', $editcontext)) {
                $overtext .= '<a href="' . $link . '&edit=' . $connectquiz->ac_id . '&type=' . $connectquiz->ac_type . '" target="'.$linktarget.'" >';
                //$overtext .= '<img src="' . $CFG->wwwroot . '/mod/connectquiz/images/adobe.gif" border="0" align="middle"> ';
                //$overtext .= get_string('launch_edit', 'connectquiz') . '</a><br/>';
                $overtext .= "<img src='" . $OUTPUT->pix_url('/t/edit') . "' class='iconsmall' title='" . get_string('launch_edit', 'connectquiz')  ."' />". "</a>";

                $overtext .= '<a href="#" id="connectquiz-update-from-adobe" data-connectquizid="'.$connectquiz->id.'">';
                //$overtext .= '<img src="' . $CFG->wwwroot . '/mod/connectquiz/images/adobe.gif" border="0" align="middle"> ';
                //$overtext .= get_string('update_from_adobe', 'connectquiz') . '</a><br/>';
                $overtext .= "<img src='" . $OUTPUT->pix_url('/i/return') . "' class='iconsmall' title='" . get_string('update_from_adobe', 'connectquiz')  ."' />". "</a>";
            }

            if ($connectquiz->ac_type == 'meeting') {
                if ($connectquiz->start > time()) {
                } else {
                    if( file_exists( $CFG->dirroot.'/filter/connect/attendees.php' ) ){
                        $overtext .= '<a href="' . $CFG->wwwroot . '/filter/connect/attendees.php?acurl=' . $connectquiz->url . '&course=' . $connectquiz->course . '">';
                        //$overtext .= '<img src="' . $CFG->wwwroot . '/filter/connect/images/attendee.gif" border="0" align="middle"> ' . get_string('viewattendees', 'filter_connect') . '</a>';
                        $overtext .= "<img src='" . $OUTPUT->pix_url('/t/groups') . "' class='iconsmall' title='" . get_string('viewattendees', 'filter_connect') ."' />". "</a>";
                    }
                    $overtext .= '<a href="' . $CFG->wwwroot . '/mod/connectquiz/past_sessions.php?acurl=' . $connectquiz->url . '&course=' . $connectquiz->course . '">';
                    //$overtext .= '<br /><img src="' . $CFG->wwwroot . '/mod/connectquiz/images/attendee.gif" border="0" align="middle"> ' . get_string('viewpastsessions', 'connectquiz') . '</a>';
                    $overtext .= "<img src='" . $OUTPUT->pix_url('/t/calendar') . "' class='iconsmall' title='" . get_string('viewpastsessions', 'connectquiz') ."' />". "</a>";
                }
            }
        }
        $overtext .= '</div>';
    }

    $clock = '';
    if ($connectquiz->ac_type == 'meeting' AND time() > ($connectquiz->start - 1800) AND $connectquiz->end > time()) {
        $clock = '<img id="tooltipimage" class="clock" src="' . $CFG->wwwroot . '/mod/connectquiz/images/clock';
        if ($iconsize == '_sm') $clock .= '-s';
        $clock .= '.gif" border="0" id="clock"' . $link . '>';
        // do qtip here
    }

    $height = (isset($CFG->connect_popup_height) ? 'height=' . $CFG->connect_popup_height . ',' : '');
    $width = (isset($CFG->connect_popup_width) ? 'width=' . $CFG->connect_popup_width . ',' : '');

    $font = '';
    if ($iconsize == '_sm') $font = '<font size="1">';

    $onclick = $link;
    $onclick = str_replace("'", "\'", htmlspecialchars($link));
    $onclick = str_replace('"', '\"', $onclick);
    if( $linktarget == '_self' ){
        $onclick = "window.location.href='$onclick'";
    }else{
        $onclick = ' onclick="return window.open(' . "'" . $onclick . "' , 'connectquiz', '{$height}{$width}menubar=0,location=0,scrollbars=0,resizable=1' , 0);" . '"';
    }

    $iconwidth = (isset($iconforcewidth)) ? "width=\"$iconforcewidth\" " : "";
    $iconheight = (isset($iconforceheight)) ? "height=\"$iconforceheight\" " : "";



    $display = '<div id="connectquizcontent'.$connectquiz->id.'" style="text-align: '.$iconalign.'; width: 100%;">
        <div class="connect-course-icon-'.$iconalign.'" id="'.$icondiv.'">
            <a href="'.$link.'" 
                '.($mouseovers || is_siteadmin($USER) ? 'class="mod_connectquiz_tooltip"' : '').'
                style="display: inline-block;" target="'.$linktarget.'">
                <img src="'.$iconurl.'" border="0"/>
                '.$clock.'
            </a>
        </div>
        <div class="connect-course-aftertext-'.$iconalign.'">
        '.$aftertext.'
        </div>
        <div class="mod_connectquiz_popup" style="display: block;">
                '.$overtext.'
            </div>
    </div>';

    return $display;
}

// User substitutions
function mod_connectquiz_user_callback($link) {
    global $CFG, $USER, $PAGE;
    $disallowed = array('password', 'aclogin', 'ackey');

    $PAGE->set_cacheable(false);
    // don't show any content to users who are not logged in using an authenticated account
    if (!isloggedin()) return;

    if (!isset($USER->{$link[1]}) || in_array($link[1], $disallowed)) return;

    return $USER->{$link[1]};
}

function connectquiz_mod_tzabbr() {
    global $USER, $CFG;
    if ($USER->timezone == 99) {
        $userTimezone = $CFG->timezone;
    } else {
        $userTimezone = $USER->timezone;
    }
    $dt = new DateTime("now", new DateTimeZone($userTimezone));
    return $dt->format('T');
}

function connectquiz_grade_meeting($courseid, $url, $connectquiz = null, $startdaterange, $enddaterange, $regrade) {
    global $CFG, $DB, $USER;

    if (!$connectquiz AND !$connectquiz = $DB->get_record('connectquiz', array('course' => $courseid, 'url' => $url))) return false;

    if ($connectquiz->detailgrading == 2) {
        //Fast-Track
        if ($scores = ft_get_scores($connectquiz->url)) {
            foreach ($scores as $userid => $grade) {
                
                // skip them if they have a grade outside the range
                if( !connectquiz_grade_based_on_range( $userid, $connectquiz->id, $startdaterange, $enddaterange, $regrade ) ) continue;

                if (empty($userid)) continue;
                $field = 'id';
                if (!$user = $DB->get_record('user', array($field => $userid, 'deleted' => 0))) continue;
                if (!$entry = $DB->get_record('connectquiz_entries', array('connectquizid' => $connectquiz->id, 'userid' => $user->id))) {
                    $entry = new stdClass();
                    $entry->connectquizid = $connectquiz->id;
                    $entry->userid = $user->id;
                    $entry->type = 'meeting';
                    $entry->minutes = 0;
                    $entry->slides = 0;
                    $entry->positions = 0;
                    $entry->score = 0;
                    $entry->timemodified = time();
                }

                if (!isset($entry->grade) OR $entry->grade < $grade) $entry->grade = $grade;
                if (!isset($entry->id)) $entry->id = $DB->insert_record('connectquiz_entries', $entry);
                else $DB->update_record('connectquiz_entries', $entry);
                connectquiz_gradebook_update($connectquiz, $entry);
            }
        }
    } elseif ($connectquiz->detailgrading == 3) { //Vantage Point
        $context = context_course::instance($connectquiz->course);
        $course = $DB->get_record('course', array('id' => $connectquiz->course));
        $users = get_enrolled_users($context);
        if (!$users) return true; // no enroled users, nothing to grade

        foreach ($users as $user) {

            // skip them if they have a grade outside the range
            if( !connectquiz_grade_based_on_range( $user->id, $connectquiz->id, $startdaterange, $enddaterange, $regrade ) ) continue;

            $grade = connectquiz_vp_get_score($connectquiz, $user);

            if ($grade == -1) {
                return false; // scores not ready yet, return false so meeting won't be completed yet and will check again next cron
            } elseif ($grade == -2) {
                return true; // vantage point couldn't find any grades, meeting will complete without it
            } elseif ($grade > 0) { // woo, we have a grade!!
                if (!$entry = $DB->get_record('connectquiz_entries', array('connectquizid' => $connectquiz->id, 'userid' => $user->id))) {
                    $entry = new stdClass();
                    $entry->connectquizid = $connectquiz->id;
                    $entry->userid = $user->id;
                    $entry->type = 'meeting';
                    $entry->minutes = 0;
                    $entry->slides = 0;
                    $entry->positions = 0;
                    $entry->score = 0;
                    $entry->timemodified = time();
                }

                $scores = new stdClass;
                $scores->minutes = $grade;
                connectquiz_grade_entry('', $connectquiz, $entry, $scores);
                if (!isset($entry->id)) $entry->id = $DB->insert_record('connectquiz_entries', $entry);
                else $DB->update_record('connectquiz_entries', $entry);
            }
        }
    } else {
        //Adobe Connect
        if (!$sco = connect_get_sco_by_url($connectquiz->url, 1)) return false;
        if ( !isset( $sco->type ) || $sco->type != 'meeting') return false;

        if (isset($sco->times)) {
            foreach ($sco->times as $userid => $time) {
                if (empty($userid)) continue;
                // Bug fix - $field is aclogin by default for table user.
                //$field = 'email';
                $field = 'id';

                // skip them if they have a grade outside the range
                if( !connectquiz_grade_based_on_range( $userid, $connectquiz->id, $startdaterange, $enddaterange, $regrade ) ) continue;
                
                if (!$user = $DB->get_record('user', array($field => $userid, 'deleted' => 0))) continue;
                if (!$entry = $DB->get_record('connectquiz_entries', array('connectquizid' => $connectquiz->id, 'userid' => $user->id))) {
                    $entry = new stdClass();
                    $entry->connectquizid = $connectquiz->id;
                    $entry->userid = $user->id;
                    $entry->type = 'meeting';
                    $entry->grade = 0;
                    $entry->minutes = 0;
                    $entry->score = 0;
                    $entry->slides = 0;
                    $entry->positions = 0;
                    $entry->timemodified = time();
                }

                $scores = new stdClass;
                $scores->minutes = $time;
                connectquiz_grade_entry($userid, $connectquiz, $entry, $scores);
                if (!isset($entry->id)) $DB->insert_record('connectquiz_entries', $entry);
                else $DB->update_record('connectquiz_entries', $entry);
            }
        }
    }

    return true;
}

function connectquiz_vp_get_score($connectquiz, $user){
    $connect_instance = _connect_get_instance();
    $params = array(
        'external_connect_id' => $connectquiz->id,
        'external_user_id'    => $user->id,
        'start'               => $connectquiz->start,
        'duration'            => $connectquiz->duration
    );    
    $result =  $connect_instance->connect_call('vp-get-score', $params);  
    return $result;
}

// Called when about to be locked out based on a Connect Activity
// Called from locklib
// Requires $CFG->connect_instant_grade > 0;
function connectquiz_regrade_one($connectquizid, $userid) {
    global $CFG, $DB, $USER;

    if (!$user = $DB->get_record('user', array('id' => $userid))) return false;
    if (!$connectquiz = $DB->get_record('connectquiz', array('id' => $connectquizid))) return false;
    if (!$entry = $DB->get_record('connectquiz_entries', array('userid' => $user->id, 'connectquizid' => $connectquizid))) return false;
    if ( !connectquiz_grade_entry($user->id, $connectquiz, $entry)) return false;
    elseif (!connectquiz_grade_entry($user->id, $connectquiz, $entry)) return false;
    $DB->update_record('connectquiz_entries', $entry);
    return $entry->grade;
}

function connectquiz_set_forceicon($connectquiz) {
    if( function_exists( 'local_connect_set_forceicon' ) ){
        return local_connect_set_forceicon( $connectquiz, 'connectquiz' );
    }else{
        return false;
    }
}

/**
 * Serves the resource files.
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - just send the file
 */
function connectquiz_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if( function_exists( 'local_connect_pluginfile' ) ){
        return local_connect_pluginfile( $course, $cm, $context, $filearea, $args, $forcedownload, $options, 'connectquiz' );
    }else{
        return false;
    }
}

function connectquiz_regrade_fullquiz($connectquiz, $shh = true, $user_tograde_id = 0 ) {
    global $CFG, $USER, $DB;

    if ($connectquiz->type != 'cquiz')
        return false;
    
    if( isset( $USER->usercourseconnects ) ){
    	$USER->usercourseconnects.= "$connectquiz->id";
    }else{
    	$USER->usercourseconnects = "$connectquiz->id";
    }

    if( $sco = connect_get_sco_by_url($connectquiz->url, 1)) {
        if ($sco->scores) {
            foreach ($sco->scores as $userid => $item) {
                if( $user_tograde_id && $user_tograde_id != $userid ) continue; // we only want to grade one user, if this is not them, skip them
            	$score = $item->score;
                if ($user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0))) {
                	if( $user->id == $USER->id ){
	                	if( isset( $USER->usercourseconnects ) ){
					    	$USER->usercourseconnectswithgrade.= "$connectquiz->id";
					    }else{
					    	$USER->usercourseconnectswithgrade = "connect->id";
					    }
                	}
                	
                    if (!$entry = $DB->get_record('connectquiz_entries', array('userid' => $user->id, 'connectquizid' => $connectquiz->id))) {
                        $entry = new stdClass;
                        $entry->connectquizid = $connectquiz->id;
                        $entry->userid = $user->id;
                        $entry->type = $connectquiz->type;
                        $entry->views = 0;
                    }
                    $entry->timemodified = time();
                    
                    $oldgrade = isset( $entry->grade ) ? $entry->grade : 0;

                    // Without detail grading, just set the grade to 100 and return
                    if (!$connectquiz->detailgrading) {
                        $entry->grade = 100;
                        connectquiz_gradebook_update($connectquiz, $entry);
                    } elseif (!isset($entry->grade) OR $entry->grade < 100) {
                        $scores = new stdClass;
                        $scores->score = $score;
                        connectquiz_grade_entry($user->id, $connectquiz, $entry, $scores);
                    }
                    
                    if( $oldgrade == 0 && $entry->grade > 0 ){
                    	//means they had not submitted anythning before, but have now, do event
                    	$event = \mod_connectquiz\event\connectquiz_quizsubmitted::create(array(
                    			'objectid' => $connectquiz->id,
                    			'relateduserid' => $user->id,
                    			'other' => array( 'acurl' => $connectquiz->url, 'description' => "Quiz submitted: $connectquiz->name" )
                    	));
                    	$event->trigger();
                    }
                    
                    $entry->rechecks = $entry->grade == 100 ? 0 : $connectquiz->loops;
                    $entry->rechecktime = $entry->grade == 100 ? 0 : time() + $connectquiz->initdelay;

                    if (!isset($entry->id)) $DB->insert_record('connectquiz_entries', $entry);
                    else $DB->update_record('connectquiz_entries', $entry);

                    if (!$shh) echo '-- Updating ' . fullname($user) . ' (' . $userid . ')  with a score of ' . $score . ' to a grade of ' . $entry->grade . '%<br/>';

                    if ($cm = get_coursemodule_from_instance('connectquiz', $connectquiz->id)) {
                        if ($course = $DB->get_record('course', array('id' => $connectquiz->course))) {
                            $completion = new completion_info($course);
                            if ($completion->is_enabled($cm)) $completion->update_state($cm, COMPLETION_COMPLETE, $user->id);
                        }
                    }
                }
            }
        }
    }
    return true;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function connectquiz_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-connectquiz-*' => 'Any connect page type');
    return $module_pagetype;
}

<?php // $Id: index.php
/**
 * @author  Gary Menezes
 * @version $Id: index.php
 * @package RECURRING MEETING 
 **/
    global $CFG, $OUTPUT, $PAGE, $DB;
    require_once("../../config.php");

    $id        = optional_param( 'id',        0, PARAM_INT );
    $instid    = optional_param( 'inst',      0, PARAM_INT );
    $task      = optional_param( 'task',     '', PARAM_ALPHA );
    $ack       = optional_param( 'ack',      '', PARAM_RAW );
    $cancel    = optional_param( 'cancel',   '', PARAM_RAW );

    if ( !isset( $id ) OR !$id OR ! $connectquiz = $DB->get_record( 'connectquiz', array( "id"=>$id ) ) ) print_error( 'Invalid Connect ID passed.' );
    if ( !$course = $DB->get_record( 'course', array( 'id'=>$connectquiz->course ) ) ) print_error( 'Invalid Connect Course.' );

    require_login( $course );
    $context = context_course::instance($course->id );
    require_capability( 'moodle/course:update', $context );
    $strtitle = get_string( 'recurring', 'connectquiz' );
    
    $PAGE->set_url( '/mod/connectquizmeeting/recurring.php', array( 'id'=>$id ) );
    $PAGE->set_context( $context );
    $PAGE->set_title( $strtitle );
    $PAGE->set_heading( $strtitle );
    $PAGE->set_pagelayout( 'incourse' );
    $PAGE->navbar->add( $strtitle, $PAGE->url );
    $event = \mod_connect\event\connectquiz_recurring::create(array(
    		'objectid' => $id,
    		'other' => array( 'description' => "mod/connectquizmeeting/recurring.php?id=$id")
    ));
    $event->trigger();
    echo $OUTPUT->header();
    
    if ( !empty( $cancel ) ) $task = 'browse';

    switch( $task ) {
        case 'delete':   
            if ( $ack == md5( $instid.'CHECKINGISCOOL' ) AND $inst = $DB->get_record( 'connectquiz_recurring', array( 'id'=>$instid ) ) ) {
                if ( $inst->eventid ) {
                    $DB->delete_records( 'reminders', array( 'event'=>$inst->eventid ) );
                    $DB->delete_records( 'event', array( 'id'=>$inst->eventid ) );
                }
                $DB->delete_records( 'connectquiz_recurring', array( 'id'=>$instid ));
            }
            browse( $connectquiz );
            break;
        case 'confirm':
            if ( !empty( $instid ) ) $start = $DB->get_field( 'connectquiz_recurring', 'start', array( 'id'=>$instid ) );
            echo $OUTPUT->confirm( get_string( 'recurringconf', 'connectquiz' ) . DATE( 'M d Y h:ia', $start ), $PAGE->url . '&inst=' . $instid . '&task=delete&ack=' . md5( $instid . 'CHECKINGISCOOL' ), $PAGE->url );
            break;
        case 'add':
        case 'edit':
            edit( $connectquiz, $instid, $task );
            break;
        case 'save':
            ssave( $connectquiz, $instid, $task );
        default:
            browse( $connectquiz );
            break;
    }
    echo $OUTPUT->footer();
    die;

function browse( $connectquiz ) {
    global $CFG, $DB, $PAGE, $OUTPUT;

    echo $OUTPUT->heading( '<a href="' . $PAGE->url . '&id=' . $connectquiz->id . '&task=add">' . get_string( 'addinst', 'connectquiz' ) . '</a>' );
    
    if ( $instances = $DB->get_records( 'connectquiz_recurring', array( 'connectquizid'=>$connectquiz->id, 'record_used' => 0 ), 'start' ) ) {
        $table        = new html_table();
        $table->head  = array ( get_string( 'start', 'connectquiz' ), get_string( 'url', 'connectquiz' ), '' );
        $table->align = array ( 'left', 'left', 'center' );
        $table->width = '100%';

        foreach( $instances as $inst ) {
            $edit   = '<a href="' . $PAGE->url . '&task=edit&id=' . $connectquiz->id . '&inst=' . $inst->id . '"><img src="' . $OUTPUT->pix_url('/t/edit') . '" class="iconsmall" alt="Edit" /></a>';
            $delete = '<a href="' . $PAGE->url . '&task=confirm&id=' . $connectquiz->id . '&inst=' . $inst->id . '"><img src="' . $OUTPUT->pix_url('/t/delete') . '" class="iconsmall" alt="Edit" /></a>';
            $table->data[] = array( DATE( 'M d Y h:ia T', $inst->start ), $inst->url, $edit . '&nbsp;' . $delete );
        }

        if ( !empty( $table ) ) echo html_writer::table( $table );
    } else echo $OUTPUT->heading( get_string( 'noinst', 'connectquiz' ) );

    return true;

}

function edit( $connectquiz, $instid=0, $task='edit' ) {
    global $CFG, $DB, $PAGE, $OUTPUT, $rform;

    if ( $instid ) {
        $inst  = $DB->get_record( 'connectquiz_recurring', array( 'id'=>$instid ) );
        if ( $event = $DB->get_record( 'event', array( 'id'=>$inst->eventid ) ) ) {
            $inst->duration = $event->timeduration;
        } 
    } else {
        $inst            = new stdClass;
        $inst->nextdate  = time() + ( 24 * 60 * 60 );
        $inst->nexturl   = $connectquiz->url;
    }

    $inst->id       = $connectquiz->id;
    $inst->instid   = $instid;
    $inst->task     = 'save';
    $inst->lasttask = $task;

    classdef();
    $rform = new recurring_form();
    $rform->set_data( $_POST );
    $rform->set_data( $inst );
    $rform->display();
}

function classdef() {
    global $CFG;
    require_once( $CFG->libdir . '/formslib.php' );

    class recurring_form extends moodleform {

        // Define the form
        function definition() {
            global $CFG, $DB;

            $mform =& $this->_form;
            
            $id      = optional_param( 'id', 0, PARAM_INT );
            $connectquiz = $DB->get_record( 'connectquiz', array( 'id'=>$id ) );

            $mform->addElement( 'hidden', 'id',        0 );
            $mform->setType('id', PARAM_INT);

            $mform->addElement( 'hidden', 'instid',    0 );
            $mform->setType('instid', PARAM_INT);

            $mform->addElement( 'hidden', 'eventid',   0 );
            $mform->setType('eventid', PARAM_INT);

            $mform->addElement( 'hidden', 'task',      'save' );
            $mform->setType('task', PARAM_RAW);

            $mform->addElement( 'hidden', 'lasttask',  '' );
            $mform->setType('lasttask', PARAM_RAW);

            $mform->addElement( 'hidden', 'sesskey',   sesskey() );
            $mform->setType('sesskey', PARAM_RAW);

            $mform->addElement( 'header', '', get_string( 'edithdr', 'connectquiz' ) );
            
            $mform->addElement( 'date_time_selector', 'start', get_string( 'start', 'connectquiz' ) );
            $mform->addRule( 'start', null, 'required', null, 'client' );
            if ( $connectquiz->start > time() ) $start = $connectquiz->start + ( 7*24*60*60 );
            else $start = time() + (24*60*60);
            $mform->setDefault( 'start', $start );
            
            $mform->addElement( 'header', '', get_string( 'forcehdr', 'connectquiz' ) );

            $mform->addElement( 'text', 'url', get_string( 'url', 'connectquiz' ), 'size="50"' );
            $mform->setDefault( 'url', $connectquiz->url );
            $mform->setType('url', PARAM_RAW);

            $doptions = array();
            $doptions[60*15*01] = '15 ' . get_string('mins');
            $doptions[60*30*01] = '30 ' . get_string('mins');
            $doptions[60*45*01] = '45 ' . get_string('mins');
            $doptions[60*60*01] = '1 ' . get_string('hour');
            for ($i=1; $i<=51; $i++ ) $doptions[60*15*$i] = GMDATE( 'H:i', 60*15*$i); 
            $mform->addElement('select', 'duration', get_string('duration', 'connectquiz'), $doptions);
            $mform->setDefault('duration', 60*60);

            $cm = get_coursemodule_from_instance( 'connectquiz', $connectquiz->id, $connectquiz->course );
            if ( $cm->groupingid ) {
                $options = array();
                $options[0] = get_string('none');
                if ( $groupings = $DB->get_records( 'groupings', array( 'courseid'=>$connectquiz->course ) ) ) {
                    foreach ( $groupings as $grouping ) {
                        $options[$grouping->id] = format_string( $grouping->name );
                    }
                }
                $mform->addElement( 'select', 'groupingid', get_string( 'grouping', 'group' ), $options );
                $mform->addHelpButton( 'groupingid', 'grouping', 'group' );
                $mform->setDefault( 'groupingid', $cm->groupingid );
            }
            
            $mform->addElement( 'header', 'comphdr', get_string( 'comphdr', 'connectquiz' ) );

            $mform->addElement( 'select', 'compdelay', get_string( 'compdelay', 'connectquiz' ), $doptions );
            $mform->setDefault( 'compdelay', $connectquiz->compdelay );
            
            $mform->addElement( 'text', 'email', get_string( 'email', 'connectquiz' ), 'size="50"' );
            $mform->setDefault( 'email', $connectquiz->email );
            $mform->setType('email', PARAM_RAW);

            $uoptions = array();
            $uoptions[0] = get_string( 'none', 'connectquiz' );
            $uoptions[1] = get_string( 'all', 'connectquiz' );
            $uoptions[2] = get_string( 'attended', 'connectquiz' );
            $uoptions[3] = get_string( 'absent', 'connectquiz' );
            $mform->addElement( 'select', 'unenrol', get_string( 'unenrol', 'connectquiz' ), $uoptions );
            $mform->setDefault( 'unenrol', $connectquiz->unenrol );

            $dbman = $DB->get_manager();
            $coptions = array();
            if ($dbman->table_exists('certificate')){
                $coptions = $DB->get_records_menu( 'certificate', array( 'course'=>$connectquiz->course ), 'name', 'id,name' );
            }

            $coptions = array( 0=>get_string( 'none', 'connectquiz' ) ) + $coptions;
            $mform->addElement( 'select', 'autocert', get_string( 'autocert', 'connectquiz' ), $coptions );
            $mform->setDefault( 'autocert', $connectquiz->autocert );

            if ( isset( $CFG->local_reminders ) AND $CFG->local_reminders ) {
                require_once( $CFG->dirroot . '/local/reminders/lib.php' );
                reminders_form( $mform, $check=true );
            }

            $this->add_action_buttons();
        }
        function definition_after_data() {
            global $CFG, $COURSE, $DB, $USER;
        
            $mform  =& $this->_form;

            if ( isset( $CFG->local_reminders ) AND $CFG->local_reminders ) {
                $id      = $mform->getElementValue( 'id' );
                $instid  = $mform->getElementValue( 'instid' );
                if ( $instid ) $eventid = $mform->getElementValue( 'eventid' );
                else $eventid = $DB->get_field( 'connectquiz', 'eventid', array( 'id'=>$id ) );
                
                if ( $event = $DB->get_record( 'event', array( 'id'=>$eventid ) ) ) {
                    $mform->setDefault( 'reminders', 1 );
                    reminders_get( $event->id, $mform );
                }
            }
        }
        function validation($data, $files) {
            $errors = parent::validation($data, $files);

            if ( ! $sco = connect_get_sco_by_url( $data['url'] ) ) {
                $errors['url'] = get_string( 'notfound', 'connectquiz' );
            }
        
            if ( count( $errors ) == 0 ) return true;
            return $errors;
        }
    }
}

function ssave( $connectquiz, $instid, $task ) {
    global $CFG, $DB, $PAGE, $OUTPUT;

    classdef();
    $rform = new recurring_form();
    
    if ( ! $rdata = $rform->get_data() ) {
    	$inst = new stdClass();
        $inst->id       = $connectquiz->id;
        $inst->instid   = $instid;
        $inst->task     = 'save';
        $inst->lasttask = 'edit';

        $rform->set_data( $inst );
        $rform->set_data( $_POST );
        $rform->display();
        die;
    }
    
    if ( isset( $rdata->reminders ) AND $rdata->reminders ) {
        if ( !isset( $rdata->eventid ) OR !$rdata->eventid OR !$event = $DB->get_record( 'event', array( 'id'=>$rdata->eventid ) ) ) {
            $event               = new stdClass;
            $event->name         = $connectquiz->name;
            $event->description  = isset( $connectquiz->intro ) ? $connectquiz->intro : '';
            $event->format       = 1;
            $event->courseid     = $connectquiz->course;
            $event->modulename   = 'connectquiz';
            $event->instance     = $connectquiz->id;
            $event->eventtype    = 'course';
            $event->uuid         = '';
            $event->timemodified = time();
        }
        $event->timestart    = $rdata->start;
        $event->timeduration = $rdata->duration;
        $event->acurl        = $rdata->url;

        if ( isset( $event->id ) ) $DB->update_record( 'event', $event );
        else $event->id = $DB->insert_record( 'event', $event );
        
        if ( isset( $CFG->local_reminders ) AND $CFG->local_reminders ) {
            $DB->delete_records( 'reminders', array( 'event'=>$event->id ) );
            require_once( $CFG->dirroot.'/local/reminders/lib.php' );
            reminders_update( $event->id, $rdata );
        }
        $rdata->eventid = $event->id;
    }

    require_once($CFG->dirroot . '/mod/connectquiz/connectlib.php');
    $sco = connect_get_sco_by_url($rdata->url);
    $rdata->scoid = $sco->id;

    $rdata->connectquizid = $rdata->id;
    if( !isset( $rdata->groupingid ) ) $rdata->groupingid = 0;
    unset( $rdata->id );
    if ( isset( $rdata->instid ) AND $rdata->instid ) $rdata->id = $rdata->instid;
    if ( isset( $rdata->id ) ) $DB->update_record( 'connectquiz_recurring', $rdata );
    else $DB->insert_record( 'connectquiz_recurring', $rdata );
    
    if ( $connectquiz->complete ) {
        $connectquiz->complete = 0;
        $DB->update_record( 'connectquiz', $connectquiz );
    }
    
    return true;
}

?>

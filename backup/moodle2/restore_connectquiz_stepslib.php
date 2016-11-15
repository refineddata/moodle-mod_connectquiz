<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_connect_activity_task
 */

/**
 * Structure step to restore one connect activity
 */
class restore_connectquiz_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('connectquiz', '/activity/connectquiz');
        $paths[] = new restore_path_element('connectquiz_grade', '/activity/connectquiz/grades/grade');
        if ($userinfo) {
            $paths[] = new restore_path_element('connectquiz_entry', '/activity/connectquiz/entries/entry');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_connectquiz($data) {
        global $DB, $CFG, $USER;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->start = $this->apply_date_offset($data->start);
//        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        if( $data->autocert ){
            $data->autocert = $this->get_mappingid('certificate', $data->autocert);
        }

        // insert the connect record
        $newitemid = $DB->insert_record('connectquiz', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
        // RT-394 Assign enrolled users to adobe group
        $connectquiz = $DB->get_record('connectquiz', array( 'id' => $newitemid));
        if ( $connectquiz->type != 'video' AND !empty( $connectquiz->url ) ) {
            require_once($CFG->dirroot . '/mod/connectquiz/lib.php');

            // update display so it has new connectid
            $connectquiz->display = preg_replace( "/~$oldid/", "~$newitemid", $connectquiz->display );
            $DB->update_record( 'connectquiz', $connectquiz );

            //if (!empty($COURSE)) $course = $COURSE;
            //else $course = $DB->get_record('course', 'id', $connectquiz->course);
            $result = connect_use_sco($connectquiz->id, $connectquiz->url, $connectquiz->type, $data->course);
            //if (!$result) {
                    //return false;
            //}
            
            //$result = connect_use_sco($newitemid, $connectquiz->url, $connectquiz->type, $data->course);
            connect_add_access( $newitemid, $data->course, 'group', 'view', false, 'cquiz' );
        }
    }

    protected function process_connectquiz_grade($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->connectquizid = $this->get_new_parentid('connectquiz');
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('connectquiz_grading', $data);
        $this->set_mapping('connectquiz_grading', $oldid, $newitemid);
    }

    protected function process_connectquiz_entry($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->connectquizid = $this->get_new_parentid('connectquiz');
        //$data->gradeid = $this->get_mappingid('connect_grading', $oldid);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('connectquiz_entries', $data);
        // No need to save this mapping as far as nothing depend on it
        // (child paths, file areas nor links decoder)
    }

    protected function after_execute() {
        // Add connect related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_connectquiz', 'intro', null);
        // Add force icon related files, matching by item id (connect)
        $this->add_related_files('mod_connectquiz', 'content', null);
    }
}

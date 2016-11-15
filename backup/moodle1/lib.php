<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your grade) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage connect
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Choice conversion handler
 */
class moodle1_mod_connectquiz_handler extends moodle1_mod_handler {

    /** @var moodle1_file_manager */
    protected $fileman = null;

    /** @var int cmid */
    protected $moduleid = null;

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/CONNECT does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'connect', '/MOODLE_BACKUP/COURSE/MODULES/MOD/CONNECTPRO',
                array(
                    'renamefields' => array(),
                    'newfields' => array(
                        'intro' => '',
                        'introformat' => 0
                    ),
                    'dropfields' => array(),
                )
            ),
            new convert_path('connect_grades', '/MOODLE_BACKUP/COURSE/MODULES/MOD/CONNECTQUIZ/GRADING'),
            new convert_path('connect_grade', '/MOODLE_BACKUP/COURSE/MODULES/MOD/CONNECTQUIZ/GRADING/GRADE'),
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/CONNECT
     * data available
     */
    public function process_connect($data) {

        // get the course module id and context id
        $instanceid     = $data['id'];
        $cminfo         = $this->get_cminfo($instanceid);
        $this->moduleid = $cminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // get a fresh new file manager for this instance
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_connect');

        // convert course files embedded into the intro
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $data['intro'] = moodle1_converter::migrate_referenced_files($data['intro'], $this->fileman);

        // start writing connect.xml
        $this->open_xml_writer("activities/connect_{$this->moduleid}/connect.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $this->moduleid,
            'modulename' => 'connect', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('connect', array('id' => $instanceid));

        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        return $data;
    }

    /**
     * This is executed when the parser reaches the <GRADES> opening element
     */
    public function on_connect_grades_start() {
        $this->xmlwriter->begin_tag('grades');
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/CONNECTMEETING/GRADES/GRADE
     * data available
     */
    public function process_connect_grade($data) {
        $this->write_xml('grade', $data, array('/grade/id'));
    }

    /**
     * This is executed when the parser reaches the closing </GRADES> element
     */
    public function on_connect_grades_end() {
        $this->xmlwriter->end_tag('grades');
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'connect' path
     */
    public function on_connect_end() {
        // finalize connect.xml
        $this->xmlwriter->end_tag('connect');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/connect_{$this->moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}

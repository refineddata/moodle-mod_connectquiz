<?php

// $Id: mod_form.php
require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once("$CFG->dirroot/mod/connectquiz/lib.php");
require_once($CFG->libdir . '/filelib.php');

class mod_connectquiz_mod_form extends moodleform_mod
{

    var       $_gradings;
    protected $_fmoptions = array(
        // 3 == FILE_EXTERNAL & FILE_INTERNAL
        // These two constant names are defined in repository/lib.php
        'return_types'   => 3,
        'accepted_types' => 'images',
        'maxbytes'       => 0,
        'maxfiles'       => 1
    );

    function definition()
    {

        global $COURSE, $CFG, $DB, $USER, $PAGE;

//        $PAGE->requires->js('/mod/connectquiz/js/mod_connectquiz.js');
//        $PAGE->requires->js('/mod/connectquiz/js/jQueryFileTree.min.js');
        $PAGE->requires->js_init_code('window.browsetitle = "' . get_string('browsetitle', 'connectquiz') . '";');
        $PAGE->requires->css('/local/connect/css/jQueryFileTree.css');

        $mform = &$this->_form;
        // this hack is needed for different settings of each subtype
        if ( ! empty($this->_instance)) {
            $new = true;
            //$type = $DB->get_field('connectquiz', 'type', array('id' => $this->_instance));
        } else {
            $new = false;
            //$type = required_param('type', PARAM_ALPHANUM);
        }

        $PAGE->requires->string_for_js('notfound', 'connectquiz');
        $PAGE->requires->string_for_js('typelistcquiz', 'connectquiz');
        if ( ! empty($CFG->connect_update) && $CFG->connect_update) {
            $PAGE->requires->string_for_js('whensaved', 'connectquiz');
        } else {
            $PAGE->requires->string_for_js('connect_not_update', 'connectquiz');
        }

        //$PAGE->requires->js_init_code('window.connect_type = "' . $type . '";');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'type', 'cquiz');
        $mform->setType('type', PARAM_RAW);
        $mform->addElement('hidden', 'newurl', '');
        $mform->setType('newurl', ( ! empty($CFG->formatstringstriptags)) ? PARAM_TEXT : PARAM_CLEAN);
        $mform->addElement('hidden', 'eventid', 0);
        $mform->setType('eventid', PARAM_INT);
        $mform->addElement('hidden', 'scoid', 0);
        $mform->setType('scoid', PARAM_INT);
        //$mform->setDefault('type', $type);
        $url  = optional_param('url', '', PARAM_RAW);
        $name = optional_param('name', '', PARAM_RAW);
        if (is_numeric(substr($url, 0, 1))) {
            $url = 'INVALID';
        }
        if ($url != clean_text($url, PARAM_ALPHAEXT)) {
            $url = 'INVALID';
        }
        if (strpos($url, '/') OR strpos($url, ' ')) {
            $url = 'INVALID';
        }
        if ( ! empty($url)) {
            $mform->setDefault('newurl', $url);
        }

        $mform->addElement('header', 'general',
            get_string('typelistcquiz', 'connectquiz') . ' ' . get_string('connect_details', 'connectquiz'));

//-------------------------------------------------------------------------------
        $options    = array();
        $options[0] = get_string('none');

        for ($i = 100; $i >= 1; $i--) {
            $options[$i] = $i . '%';
        }
        $formgroup   = array();
        $formgroup[] = &$mform->createElement('text', 'url', '',
            array('maxlength' => 255, 'size' => 48, 'class' => 'ignoredirty'));
        $mform->setType('url', ( ! empty($CFG->formatstringstriptags)) ? PARAM_TEXT : PARAM_CLEAN);
        if ( empty($_REQUEST['update'])) {
            $formgroup[] = &$mform->createElement('button', 'browse', get_string('browse', 'connectquiz'));
        }
        $mform->addElement('group', 'urlgrp', get_string('url', 'connectquiz'), $formgroup, array(' '), false);
        $mform->setDefault('url', $url);
        if ( empty($_REQUEST['update'])) {
            $mform->addRule( 'urlgrp', null, 'required' );
            $mform->addGroupRule( 'urlgrp', array(
                'url' => array(
                    array( null, 'required', null, 'client' )
                ),
            ) );
        }
        if (!empty($_REQUEST['update'])) {
            $mform->hardFreeze('urlgrp');
        }

        $goptions = array();
        for ($i = 100; $i >= 1; $i--) {
            $goptions[$i] = $i . '%';
        }

//-------------------------------------------------------------------------------

        $mform->addElement('text', 'name', get_string('connect_name', 'connectquiz'), array(
            'size'      => '64',
            'maxlength' => '60',
            'style'     => 'width:412px;'
        ));

        if ( ! empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

//-------------------------------------------------------------------------------
        // Duration
        $doptions               = array();
        $doptions[60 * 15 * 01] = '15 ' . get_string('mins');
        $doptions[60 * 30 * 01] = '30 ' . get_string('mins');
        $doptions[60 * 45 * 01] = '45 ' . get_string('mins');
        $doptions[60 * 60 * 01] = '1 ' . get_string('hour');
        for ($i = 1; $i <= 51; $i++) {
            $doptions[60 * 15 * $i] = GMDATE('H:i', 60 * 15 * $i);
        }

        $this->standard_intro_elements(false, get_string('summary', 'connectquiz'));

//-------------------------------------------------------------------------------

        if (isset($CFG->connect_icondisplay) AND $CFG->connect_icondisplay) {
            $mform->addElement('header', 'disphdr', get_string('disphdr', 'connectquiz'));

            $displayoncoursedefault = isset($CFG->connect_displayoncourse) ? $CFG->connect_displayoncourse : 1;
            $mform->addElement('checkbox', 'displayoncourse', get_string('displayoncourse', 'connectquiz'));
            $mform->setDefault('displayoncourse', $displayoncoursedefault);

            $szopt = array();
            //$szopt['none'] = get_string('none');
            $szopt['large']  = get_string('large', 'connectquiz');
            $szopt['medium'] = get_string('medium', 'connectquiz');
            $szopt['small']  = get_string('small', 'connectquiz');
            $szopt['block']  = get_String('block', 'connectquiz');
            $szopt['custom'] = get_String('custom', 'connectquiz');
            $mform->addElement('select', 'iconsize', get_string('iconsize', 'connectquiz'), $szopt);
            $default = isset($CFG->connect_iconsize) ? $CFG->connect_iconsize : 'medium';
            $mform->setDefault('iconsize', $default);

            $posopt      = array();
            $posopt['l'] = get_string('left', 'connectquiz');
            $posopt['c'] = get_string('center', 'connectquiz');
            $mform->addElement('select', 'iconpos', get_string('iconpos', 'connectquiz'), $posopt);
            $default = isset($CFG->connect_iconpos) ? $CFG->connect_iconpos : 'left';
            $mform->setDefault('iconpos', $default);

            if (isset($CFG->connect_maxviews) AND $CFG->connect_maxviews >= 0) {
                $vstr     = get_string('views', 'connectquiz');
                $viewopts = array(
                    0 => get_string('disabled', 'connectquiz'),
                    1 => '1' . get_string('view', 'connectquiz')
                );
                for ($i = 2; $i <= 100; $i++) {
                    $viewopts[$i] = $i . $vstr;
                }
                $mform->addElement('select', 'maxviews', get_string('maxviews', 'connectquiz'), $viewopts);
                $mform->setDefault('maxviews', $CFG->connect_maxviews);
            }

            $mform->addElement('checkbox', 'iconsilent', get_string('iconsilent', 'connectquiz'));
            $default = isset($CFG->connect_iconsilent) ? $CFG->connect_iconsilent : 0;
            $mform->setDefault('iconsilent', $default);
            //$mform->setAdvanced('iconsilent', 'processing');

            //$mform->addElement('checkbox', 'iconmouse', get_string('iconmouse', 'connectquiz'));
            //$default = ! empty($CFG->connect_mouseovers) ? 0 : 1;
            //$mform->setDefault('iconmouse', $default);
            //$mform->setAdvanced('iconmouse', 'icon');

            $mform->addElement('editor', 'extrahtml', get_string('extrahtml', 'connectquiz'),
                array('cols' => '64', 'rows' => '8'));
            //$mform->setAdvanced('extrahtml', 'icon');

            $mform->addElement('filemanager', 'forceicon_filemanager', get_string('forceicon', 'connectquiz'), null,
                $this->_fmoptions);
            //$mform->setAdvanced('forceicon_filemanager', 'icon');

            $mform->disabledIf('iconpos', 'iconsize', 'eq', 'none');
            $mform->disabledIf('iconsilent', 'iconsize', 'eq', 'none');
            $mform->disabledIf('iconphone', 'iconsize', 'eq', 'none');
            //$mform->disabledIf('iconmouse', 'iconsize', 'eq', 'none');
            $mform->disabledIf('iconguests', 'iconsize', 'eq', 'none');
            $mform->disabledIf('iconnorec', 'iconsize', 'eq', 'none');
            $mform->disabledIf('extrahtml', 'iconsize', 'eq', 'none');
            $mform->disabledIf('forceicon_filemanager', 'iconsize', 'ne', 'custom');
            $mform->disabledIf('iconphone', 'iconsilent', 'checked');
            //$mform->disabledIf('iconmouse', 'iconsilent', 'checked');
            $mform->disabledIf('extrahtml', 'iconsilent', 'checked');
        }


//-------------------------------------------------------------------------------
        $mform->addElement('header', 'grading', get_string('gradinghdr', 'connectquiz'));
//        $mform->addHelpButton('grading', 'grading', 'connectquiz');


        $dgoptions = array(
            0 => get_string('off', 'connectquiz'),
            1 => get_string('fromadobe', 'connectquiz')
        );


        $mform->addElement('select', 'detailgrading', get_string("detailgradingcquiz", 'connectquiz'), $dgoptions);
        $mform->addHelpButton('detailgrading', "detailgradingcquiz", 'connectquiz');
        $pluginconfig = get_config("mod_connectquiz", "detailgrading");
        $default      = ! empty($pluginconfig) ? $pluginconfig : 0;
        $mform->setDefault('detailgrading', $default);
        //$mform->setAdvanced('detailgrading', 'grade');

        $formgroup   = array();
        $formgroup[] = &$mform->createElement('select', 'threshold[1]', '', $options);
        $mform->setDefault('threshold[1]', 0);
        $mform->disabledIf('threshold[1]', 'detailgrading', 'eq', 0);
        $mform->disabledIf('threshold[1]', 'detailgrading', 'eq', 3);
        $formgroup[] = &$mform->createElement('select', 'grade[1]', '', $goptions);
        $mform->setDefault('grade[1]', 0);
        $mform->disabledIf('grade[1]', 'detailgrading', 'eq', 0);
        $mform->disabledIf('grade[1]', 'detailgrading', 'eq', 3);
        $mform->addElement('group', 'tg1', get_string("tgcquiz", 'connectquiz') . ' 1', $formgroup, array(' '), false);
        $mform->addHelpButton('tg1', "tgcquiz", 'connectquiz');
        //$mform->setAdvanced('tg1', 'grade');

        $formgroup   = array();
        $formgroup[] = &$mform->createElement('select', 'threshold[2]', '', $options);
        $mform->setDefault('threshold[2]', 0);
        $mform->disabledIf('threshold[2]', 'detailgrading', 'eq', 0);
        $mform->disabledIf('threshold[2]', 'detailgrading', 'eq', 3);
        $formgroup[] = &$mform->createElement('select', 'grade[2]', '', $goptions);
        $mform->setDefault('grade[2]', 0);
        $mform->disabledIf('grade[2]', 'detailgrading', 'eq', 0);
        $mform->disabledIf('grade[2]', 'detailgrading', 'eq', 3);
        $mform->addElement('group', 'tg2', get_string("tgcquiz", 'connectquiz') . ' 2', $formgroup, array(' '), false);
        $mform->addHelpButton('tg2', "tgcquiz", 'connectquiz');
        //$mform->setAdvanced('tg2', 'grade');

        $formgroup   = array();
        $formgroup[] = &$mform->createElement('select', 'threshold[3]', '', $options);
        $mform->setDefault('threshold[3]', 0);
        $mform->disabledIf('threshold[3]', 'detailgrading', 'eq', 0);
        $mform->disabledIf('threshold[3]', 'detailgrading', 'eq', 3);
        $formgroup[] = &$mform->createElement('select', 'grade[3]', '', $goptions);
        $mform->setDefault('grade[3]', 0);
        $mform->disabledIf('grade[3]', 'detailgrading', 'eq', 0);
        $mform->disabledIf('grade[3]', 'detailgrading', 'eq', 3);
        $mform->addElement('group', 'tg3', get_string("tgcquiz", 'connectquiz') . ' 3', $formgroup, array(' '), false);
        $mform->addHelpButton('tg3', "tgcquiz", 'connectquiz');
        //$mform->setAdvanced('tg3', 'grade');
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
        if (( ! isset($CFG->connect_instant_grade) OR ! $CFG->connect_instant_grade)) {
            $mform->addElement('header', 'prochdr', get_string('prochdr', 'connectquiz'));
            //$mform->setAdvanced('prochdr', 'processing');

            $mform->addElement('select', 'initdelay', get_string('initdelay', 'connectquiz'), $doptions);
            $mform->disabledIf('initdelay', 'detailgrading', 'eq', 0);
            $mform->setType('initdelay', PARAM_INT);
            //$mform->setAdvanced('initdelay', 'processing');
            $mform->setDefault('initdelay', 3600);
//            $mform->addHelpButton('initdelay', 'initdelay', 'connectquiz');

            $mform->addElement('text', 'loops', get_string('loops', 'connectquiz'), array('size' => 10));
            $mform->disabledIf('loops', 'detailgrading', 'eq', 0);
            $mform->setType('loops', PARAM_INT);
            //$mform->setAdvanced('loops', 'processing');
            $mform->setDefault('loops', 4);
//            $mform->addHelpButton('loops', 'loops', 'connectquiz');
            $mform->addElement('select', 'loopdelay', get_string('loopdelay', 'connectquiz'), $doptions);
            $mform->disabledIf('loopdelay', 'detailgrading', 'eq', 0);
            $mform->setType('loopdelay', PARAM_INT);
            //$mform->setAdvanced('loopdelay', 'processing');
            $mform->setDefault('loopdelay', 900);
//            $mform->addHelpButton('loopdelay', 'loopdelay', 'connectquiz');
        }


//-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    function definition_after_data()
    {
        global $CFG, $COURSE, $DB, $USER, $AC;

        //if (empty($USER->aclogin)) $USER->aclogin = _connect_new_login($USER);

        $mform = &$this->_form;
        // this hack is needed for different settings of each subtype
        if ( ! empty($this->_instance)) {
            $connect = $DB->get_record('connectquiz', array('id' => $this->_instance));
            $eventid = $connect->eventid;
        }


        $urlgrp = $mform->getElementValue('urlgrp');
        $url    = ! empty($urlgrp['url']) ? $urlgrp['url'] : '';
        $name   = $mform->getElementValue('name');
        if ( ! empty($url)) {
            if (is_numeric(substr($url, 0, 1))) {
                $url = 'INVALID';
            }
            if ($url != clean_text($url, PARAM_ALPHAEXT)) {
                $url = 'INVALID';
            }
            if (strpos($url, '/') OR strpos($url, ' ')) {
                $url = 'INVALID';
            }
        }

        if ( ! empty($url) AND $url != 'INVALID') {
            if ( ! empty($this->_instance)) {
                $info = connect_get_sco($this->_instance, 0, 'cquiz');
            } else {
                $info = connect_get_sco_by_url($url);
            }

            if (isset($info->type)) {

                $mform->setDefault('urlgrp', $url);

                //Make URL field uneditable if editing existing activity
                if (!empty($_REQUEST['update'])) {
                    $element = &$mform->createElement('text', 'url', get_string('url', 'connectquiz'));
                    $mform->setType('url', ( ! empty($CFG->formatstringstriptags)) ? PARAM_TEXT : PARAM_CLEAN);
                    $mform->insertElementBefore($element, 'urlgrp');
                    $mform->hardFreeze('url');
                    $mform->setDefault('url', $url);

                    $mform->removeElement('urlgrp', true);
                }

                if ((isset($CFG->connect_update) && $CFG->connect_update) || empty($_REQUEST['update'])) {
                    $mform->setDefault('name', $info->name);
                    $mform->setDefault('introeditor', array('text' => $info->desc));
                }
            } else {
                $mform->setDefault('url', 'INVALID');
            }
        } elseif ($url == 'INVALID') {
            $mform->setDefault('url', 'INVALID');
        }

        if (isset($CFG->connect_icondisplay) AND $CFG->connect_icondisplay) {
            if ( ! empty($this->_instance)) {
                $disp = $DB->get_field('connectquiz', 'display', array('id' => $this->_instance));
            }
            if ( ! empty($disp)) {

                preg_match('/data-options="([^"]+)"/', $disp, $matches);
                if (isset($matches[1])) {
                    $options = explode('~', $matches[1]);
                    $tags    = explode('-', strtolower($options[0]));
                    $size    = empty($tags[0]) ? 'large' : (($tags[0] == 'large' OR $tags[0] == 'medium' OR $tags[0] == 'small' OR $tags[0] == 'block') ? $tags[0] : 'large');
                    $silent  = isset($tags[1]) ? strpos($tags[1], 's') !== false : false;
                    $norec   = isset($tags[1]) ? strpos($tags[1], 'a') !== false : false;
                    $phone   = isset($tags[1]) ? strpos($tags[1], 'p') !== false : false;
                    $guest   = isset($tags[1]) ? strpos($tags[1], 'g') !== false : false;
                    $mouse   = isset($tags[1]) ? strpos($tags[1], 'm') !== false : false;
                    $pos     = isset($tags[1]) ? strpos($tags[1], 'l') !== false ? 'l' : 'c' : 'l';

                    $xhtml = isset($options[3]) ? $options[3] : '';
                    $force = isset($options[4]) ? basename($options[4]) : '';
                    $size  = empty($force) ? $size : 'custom';

                    $mform->setDefault('iconsize', $size);
                    $mform->setDefault('iconpos', $pos);
                    $mform->setDefault('iconsilent', $silent);
                    $mform->setDefault('iconphone', $phone);
                    //$mform->setDefault('iconmouse', $mouse);
                    $mform->setDefault('iconguests', $guest);
                    $mform->setDefault('iconnorec', $norec);
                    $xhtml = preg_replace('/%%quote%%/', '"', $xhtml);
                    $mform->setDefault('extrahtml', $xhtml);
                }

                $draftitemid = file_get_submitted_draft_itemid('forceicon');
                file_prepare_draft_area($draftitemid, $this->context->id, 'mod_connectquiz', 'content', 0,
                    $this->_fmoptions);
                $mform->setDefault('forceicon_filemanager', $draftitemid);
            }
        }


        parent::definition_after_data();
    }

    function data_preprocessing(&$data)
    {
        global $DB;

        parent::data_preprocessing($data);

        if (isset($data['id']) && is_numeric($data['id'])) {
            if ($gradings = $DB->get_records('connectquiz_grading', array('connectquizid' => $data['id']),
                'threshold desc')
            ) {
                $key = 1;
                foreach ($gradings as $grading) {
                    if ($data['detailgrading'] == 3) {
                        $data['vpthreshold[' . $key . ']'] = $grading->threshold;
                        $data['vpgrade[' . $key . ']']     = $grading->grade;
                    } else {
                        $data['threshold[' . $key . ']'] = $grading->threshold;
                        $data['grade[' . $key . ']']     = $grading->grade;
                    }
                    $key++;
                }
            }
        }
    }

    function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        if (count($errors) == 0) {
            return true;
        } else {
            return $errors;
        }
    }

}

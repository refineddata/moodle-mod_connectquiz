<?php
namespace mod_connectquiz\event;
defined('MOODLE_INTERNAL') || die();

if( !($CFG->branch >= 27) ){
	class_alias( '\core\event\content_viewed', '\core\event\course_module_viewed' );
}

class course_module_viewed extends \core\event\course_module_viewed {
    protected function init() {
        $this->data['objecttable'] = 'connectquiz';
        parent::init();
    }
    
    protected function validate_data() {
    	// Hack to please the parent class. 'view chapter' was the key used in old add_to_log().
    	$this->data['other']['content'] = 'view chapter';
    	parent::validate_data();
    }
}

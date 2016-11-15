<?php
namespace mod_connectquiz\task;

class connectquiz_cron extends \core\task\scheduled_task {      
    public function get_name() {
        // Shown in admin screens
        return get_string('connectquizcron', 'connectquiz');
    }
                                                                     
    public function execute() { 
        global $CFG;
        mtrace('++ Connect Quiz Cron Task: start');
        require_once($CFG->dirroot . '/mod/connectquiz/lib.php');
        connectquiz_cron_task();
        mtrace('++ Connect Quiz Cron Task: end');
    }                                                                                                                               
} 
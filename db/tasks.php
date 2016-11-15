<?php
defined('MOODLE_INTERNAL') || die();

$tasks = array(                                                                                                                     
    array(                                                                                                                          
        'classname' => 'mod_connectquiz\task\connectquiz_cron',                                                                            
        'blocking' => 0,                                                                                                            
        'minute' => '*/15',
        'hour' => '*',                                                                                                              
        'day' => '*',                                                                                                               
        'dayofweek' => '*',                                                                                                         
        'month' => '*'                                                                                                              
    )
);
<?php // $Id: defaults.php,v 1.3
/**
 * Code fragment to define the version of newmodule
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author  Gary Menezes
 * @version $Id: defaults.php
 * @package connect
 **/

// This file is generally only included from upgrade_activity_modules()
// It defines default values for any important configuration variables

   $defaults = array (
       'connect_update' => 1,
       'connect_updatedts' => 1,
       'connect_instant_grade' => 0,
       'connect_icondisplay' => 1,
       'connect_template' => 0,
       'connect_autofolder' => 0,
    );


?>

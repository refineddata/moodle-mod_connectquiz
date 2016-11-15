<?php
// Add fields not originally created.
  echo "running db onetime...<br/>";

  require_once("../../../config.php");
  require_once($CFG->dirroot.'/lib/ddllib.php');
  require_once($CFG->dirroot.'/lib/accesslib.php');

  require_login();

  if (has_capability('moodle/site:config', context_system::instance())) {

	  $table = "connect_entries";
	
	  echo "Table: $table<br/>";
	  $fields = $DB->get_records_sql( "SHOW FIELDS FROM $table" );
		foreach ($fields as $field) { echo "  ".$field->Field." <br/>"; }

    // add required Connect Pro Fields and Index to user table and set default values
/*    echo "alter entries db onetime...<br/>";
    $result = execute_sql('ALTER TABLE '. $CFG->prefix . 'connect_entries' . ' ADD COLUMN score INT(10) NOT NULL AFTER userid');
    echo "alter entries db onetime...<br/>";
    $result = execute_sql('ALTER TABLE '. $CFG->prefix . 'connect_entries' . ' ADD COLUMN slides INT(10) NOT NULL AFTER score');
    echo "alter entries db onetime...<br/>";
    $result = execute_sql('ALTER TABLE '. $CFG->prefix . 'connect_entries' . ' ADD COLUMN type VARCHAR(30) NOT NULL AFTER minutes');
    echo "alter entries db onetime...<br/>";
    $result = execute_sql('ALTER TABLE '. $CFG->prefix . 'connect_entries' . ' ADD COLUMN views INT(10) NOT NULL AFTER type');
*/
  }
  echo "done db onetime...<br/>";

?>
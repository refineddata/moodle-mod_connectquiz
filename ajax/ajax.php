<?php
/**
 * ajax.php.
 *
 * @author     Dmitriy
 * @since      08/07/14
 */

require_once('../../../config.php');
require_once($CFG->dirroot . '/mod/connectquiz/connectlib.php');
require_login();

$action = optional_param('action', null, PARAM_ALPHAEXT);

switch ($action) {
    case 'connect_get_sco_by_url':
        $url = optional_param('url', null, PARAM_ALPHANUMEXT);
        
        if (!$url) {
            $error = "url not provided";
        } else {
            $response = connect_get_sco_by_url($url, 0, 1);
            if ( $response == 'no-data' && empty($CFG->connect_update)){
                $response = 'connect_not_update';
            }
        }
        break;
    case 'connect_get_sco_by_name':
       
        $name = optional_param('name', null, PARAM_TEXT);
        if (!$name) {
            $error = "name not provided";
        } else {
            $response = connect_get_sco_by_name($name);
            //var_dump($response);
        }
        break;
    default:
        $error = "action not provided";
}

$return = new stdClass();
if( isset( $_SESSION['refined_noauth'] ) && $_SESSION['refined_noauth'] ){
	$return->refined_noauth = 1;
	$return->refined_noauth_message = get_string('rs_expired_message', 'connectquiz');
}elseif (isset($error)) {
    $return->error = $error;
} else {
    $return->response = $response;
}

header('Content-Type: application/json');
echo json_encode($return);

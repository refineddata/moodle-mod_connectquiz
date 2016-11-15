<?php
require_once('../../config.php');
//global $CFG, $SESSION;
require_login();

require_once($CFG->dirroot . '/mod/connectquiz/connectlib.php');

//$type = required_param('type', PARAM_ALPHA);
$scoid = optional_param('scoid', 0, PARAM_INT);
$name = optional_param('name', 'Top', PARAM_CLEAN);

$type = 'meeting';

//    print_header(get_string( 'browse', 'connect' ) );
if (empty($SESSION->crumb) OR !isset($scoid) OR !$scoid) $SESSION->crumb = array();
$SESSION->crumb[] = '<a class=sco-breadcrumb data-name="'.$name.'" data-scoid="'.$scoid.'" data-type="'.$type.'" href="#">' . $name . '</a>';

function cp_one($type, $name, $scoid, $icon = 'folder', $url = '') {
    if ($icon == 'meeting' OR $icon == 'content') {
        echo '<tr><td>' . $name . '</td><td><a href="#" class="sco-choose">' . $url . '</a></td></tr>';
    } else {
        echo '<tr><td><a class="sco-folder" href="#" data-name="' . $name . '" data-scoid="' . $scoid . '">' . $name . '</a></td><td></td></tr>';
    }
}

?>

<h1>

    <?php
    $newcrumbs = array();
    foreach ($SESSION->crumb as $crumb) {
        echo '/' . $crumb;
        $newcrumbs[] = $crumb;
        if (strpos($crumb, (string)$scoid)) {
            $SESSION->crumb = $newcrumbs;
            break;
        }
    }
    ?>

</h1>

<div style="height: 500px; overflow-y:auto;">
    <table width="100%">
        <form id="myform">
            <?php 
            if (!isset($scoid) OR !$scoid) {
                $shortcuts = connect_get_sco_shortcuts();
                if (!is_array($shortcuts)) {
                    die($shortcuts);
                }
                foreach ($shortcuts as $sco) {
                    if ($type == 'meeting' AND $sco->type == 'my-meetings') cp_one($type, 'My-Meetings', $sco->sco_id);
                    if ($type == 'meeting' AND $sco->type == 'user-meetings') cp_one($type, 'User-Meetings', $sco->sco_id);
                    if ($type == 'meeting' AND $sco->type == 'meetings') cp_one($type, 'Shared-Meetings', $sco->sco_id);
                    if ($type == 'content' AND $sco->type == 'my-content') cp_one($type, 'My-content', $sco->sco_id);
                    if ($type == 'content' AND $sco->type == 'user-content') cp_one($type, 'User-content', $sco->sco_id);
                    if ($type == 'content' AND $sco->type == 'content') cp_one($type, 'Shared-content', $sco->sco_id);
                }
            } else {
                $scos = connect_get_sco_contents($scoid);
                if (!is_array($scos)) {
                    die($scos);
                }
                foreach ($scos as $sco) {
                    cp_one($type, $sco->name, $sco->sco_id, $sco->type, $sco->url_path);
                }
            } 
?>
        </form>
    </table>
</div>

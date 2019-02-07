<?php
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
$DB = new DBHelper();
require('../../../Library/ControlsRender.php');
$Render = new ControlsRender();





//Disk space management


$DB->DISPLAY_STATS();

?>

<?php
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
$DB = new DBHelper();
require('../../../Library/ControlsRender.php');
$Render = new ControlsRender();
$collections = $DB->GET_COLLECTION_TABLE();
$total_storage = 0;
$units = explode(' ', 'B KB MB GB TB PB');



//Disk space management

$DB->DISPLAY_STORAGE_INTO_TABLE();



?>

<?php
//Super Admin only???
require_once '../../Library/SessionManager.php';
$session = new SessionManager();
require_once '../../Library/DBHelper.php';
require_once '../../Library/ControlsRender.php';

//temporary CreatorHelper class
require_once '../../Library/CreatorHelper.php';
$DB = new CreatorHelper();
echo "Coming Soon!";
?>
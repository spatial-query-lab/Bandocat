<?php
//Super Admin only???
include '../../Library/SessionManager.php';
$session = new SessionManager();
require '../../Library/DBHelper.php';
require '../../Library/CreatorHelper.php';
$DB = new CreatorHelper();
$ret = $DB->COLLECTION_VALIDATE_NEW_ENTRY($_POST['txtParameterName'],$_POST['txtDisplayName'],$_POST['txtDatabaseName']);
echo $ret;
?>
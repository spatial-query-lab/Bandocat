<?php
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/MapDBHelper.php');
$DB = new MapDBHelper();
$ret =$DB->SWITCH_DB($_GET['col']);
$isBack = $_GET['type'] == "back" ? true : false; //identify if this map is a front or a back scan
if($ret)
    $ret = $DB->DOCUMENT_GEORECSTATUS_UPDATE($_POST['txtDocID'],$isBack,$_POST['ddlGeoStatus']);
echo $ret;
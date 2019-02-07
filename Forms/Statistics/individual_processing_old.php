<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require('../../Library/DBHelper.php');
$DB = new DBHelper();
$year =  $_GET['year'];
$month = $_GET['month'];
$action = $_GET['action'];
$collections = $DB->GET_COLLECTION_FOR_DROPDOWN();
$users = $DB->USER_SELECT(true);
$ret =  array();
foreach($users as $user)
{
       //var_dump($user['userID']);
    $temp = $DB->SELECT_USER_PERFORMANCE_BY_MONTH($year, $month, $user['userID'], $action);
    if($temp[1] != null)
    {
        array_push($ret,$temp);
    }


}
echo json_encode(array("data" => $ret));

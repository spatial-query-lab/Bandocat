<?php
require '../../Library/DBHelper.php';
$DB = new DBHelper();
$collections = $DB->GET_COLLECTION_FOR_DROPDOWN();
$year =  $_GET['year'];
$ret = array();
foreach($collections as $col) {
    array_push($ret,$DB->GET_MONTHLYREPORT2($year, $col['collectionID']));
}
echo json_encode($ret);

?>
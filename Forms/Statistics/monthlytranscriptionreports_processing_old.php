<?php
require '../../Library/DBHelper.php';
$DB = new DBHelper();
//get the collections
$collections = $DB->GET_COLLECTION_FOR_DROPDOWN();
$year =  $_GET['year'];
$ret = array();
foreach($collections as $col)
{
    //returns the transcriptions /per month passign year and collection type
    array_push($ret,$DB->GET_MONTHLY_TRANSCRIPTION_REPORT($year, $col['collectionID']));
}
echo json_encode($ret);

?>
<?php
/* 	This script is called when an entry rectangle is selected. This script recieves the fileName and
	coordinates of the entry from an AJAX call and executes an SQL statement that returns a JSON 
	containing all the data so that it can be displayed in the html form. */
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/IndicesDBHelper.php');
$DB = new IndicesDBHelper();
	$entryObject = json_decode($_POST['entryObject']);
    $collection = $entryObject -> collection;
	$docID = $entryObject -> docID;
	$fileName = $entryObject -> fileName;
	$x1 = $entryObject -> x1;
	$y1 = $entryObject -> y1;
	$x2 = $entryObject -> x2;
	$y2 = $entryObject -> y2;

    $result = $DB->TRANSCRIPTION_ENTRY_SELECT($collection,$docID,$x1,$y1,$x2,$y2);

	if ($result == false) {
		echo "0 results";
	}
	echo json_encode($result);
?>
<?php
/* 	This function is called when the page "Transcription_Status"(should probably be renamed) is loaded.
	The script querys the database for the coordinates for all the entries that match the fileName. These 
	coordinates are used to draw all the rectangles for entries that have alreaded been submitted. */

include '../../../Library/SessionManager.php';
$session = new SessionManager();
require '../../../Library/DBHelper.php';
require '../../../Library/IndicesDBHelper.php';
$DB = new IndicesDBHelper();
	$collection = $_POST['collection'];
	$fileName = $_POST['fileName'];
	$fileName = $fileName;
	$docID = $_POST['docID'];

	
	$coords = array();
		
    $result = $DB->TRANSCRIPTION_GET_COORDINATES($collection,$docID);

	if ($result != false) {
		// output data of each row
		foreach($result as $r)
		    $coords[] = $r;
        echo json_encode($coords);
	}

?>
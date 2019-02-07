<?php
/* 	The purpose of this script is to be called when the user hits "Submit Entry". This script recieves the
	data for the new entry from an AJAX call and executes an SQL statement that inserts the data into
	a new row in our database. */
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/IndicesDBHelper.php');
$DB = new IndicesDBHelper();
    $newobject = json_decode($_POST['newobject']);
    $ret = $DB->TRANSCRIPTION_ENTRY_INSERT($newobject->collection,$newobject);

	if ($ret) {
    echo "New record created successfully";
} else {
    echo "Failed to create new record";
}

	
?>
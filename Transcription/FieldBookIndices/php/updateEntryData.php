<?php
	/* The purpose of this script is to execute the proccesses that are required to update an entry in
	our database. It recieves the data, and coordinates of the entry to be updated from an AJAX call and performs 
	a query to update the entry that matches the coordinates with the new data.  */
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/IndicesDBHelper.php');
$DB = new IndicesDBHelper();

	$updateObject = json_decode($_POST['updateObject']);
    $ret = $DB->TRANSCRIPTION_ENTRY_UPDATE($updateObject->collection,$updateObject);
	if ($ret) {
        echo "Successful Update";
    }
    else echo "Update failed";


?>
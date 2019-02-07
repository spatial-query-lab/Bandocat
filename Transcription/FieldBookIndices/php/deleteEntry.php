<?php
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/IndicesDBHelper.php');
$DB = new IndicesDBHelper();

/* The purpose of this script is to execute the proccesses that are required to delete an entry from 
our database. It recieves the coordinates of the entry to be deleted from an AJAX call and performs 
a query to delete the entry that matches those coordinates and fileName in the database.  */

	$updateObject = json_decode($_POST['deleteObject']);
	//assigning all updateObject data to appropriate variable
    $collection = $updateObject -> collection;
	$docID = $updateObject -> docID;
	$x1 = $updateObject -> x1;
	$y1 = $updateObject -> y1;
	$x2 = $updateObject -> x2;
	$y2 = $updateObject -> y2;

	$sql = $DB->TRANSCRIPTION_ENTRY_DELETE($collection,$docID,$x1,$y1,$x2,$y2);
	echo $docID;
?>
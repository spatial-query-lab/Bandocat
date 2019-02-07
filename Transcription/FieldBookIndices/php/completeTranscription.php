<?php
    include '../../../Library/SessionManager.php';
    $session = new SessionManager();
    require('../../../Library/DBHelper.php');
    require('../../../Library/IndicesDBHelper.php');
    $DB = new IndicesDBHelper();
	//error_reporting(E_ALL);
	$docID = $_POST['docID'];
    $collection = $_POST['collection'];
    $collectionID = $DB->SP_GET_COLLECTION_CONFIG($collection)['CollectionID'];
    $fileName = $_POST['fileName'];

    $action = "transcribe";
    $comments = "Finished";

    $ret = $DB->SET_DOCUMENT_TRANSCRIBED($collection,$docID,1);


    //delete file from Temp (move this to the incomplete transcription)
    //unlink("../" . $fileName);
    //Write Log
    $ret = $DB->SP_LOG_WRITE($action,$collectionID,$docID,$session->getUserID(),"success",$comments);

    if($ret != false)
        echo 'This document has been marked as complete.';
    else echo "Failed to mark as complete.";


?>
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


    $tempDir = "../../../Temp/";
    //delete file from Temp
    unlink("../" . $fileName);

    //clean old temporary images inside the Temp directory
    $fileArray = array_diff(scandir($tempDir), array('..', '.'));
    foreach($fileArray as $file) //deleting files that has the last modification time more than 8 hours
    {
        if (strtotime("now") - filemtime($tempDir . $file) > 14400) //14400 sec = 4 hours
            unlink($tempDir . $file);
    }





?>
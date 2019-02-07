<?php
//for admin use only
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(!$session->isAdmin())
    header('Location: ../../');
//This page executes the action from the queue.php
require('../../Library/DBHelper.php');
$DB = new DBHelper();
$collectionConfig = $DB->SP_GET_COLLECTION_CONFIG($_POST['ddlCollection']);
$DB->SWITCH_DB($_POST['ddlCollection']);
$hasRec = false;
switch($collectionConfig['TemplateID']) //switch $hasRec to true if the collection has GeoRectification schema
{
    case 1: $hasRec = true; //Collections in the Map Template
    break;
}
switch($_GET['action'])
{
    case "load":
	    //echo $_POST['ddlCollection'];
		$collectionNameFile = "../CRON/currentCollection.txt";
		if(!file_exists($collectionNameFile))
			$collectionName = fopen($collectionNameFile, "w"); // creates error log file to log progess of each item (write)
		else $collectionName = fopen($collectionNameFile,"w"); //open error log file and append to it if file already exists
		
		fwrite($collectionName,$_POST['ddlCollection']);
		fclose($collectionName);
        echo json_encode($DB->PUBLISHING_GET_PUBLISH_QUEUE());	
        break;
    case "reset":
        echo json_encode($DB->PUBLISHING_RESET_QUEUE());
        break;
    case "push":
        if($_POST['howMany'] == "")
            echo json_encode($DB->PUBLISHING_PUSH_TO_QUEUE(null,$hasRec));
        else json_encode($DB->PUBLISHING_PUSH_TO_QUEUE($_POST['howMany'],$hasRec));
        break;
    case "displaylog":
        echo nl2br(file_get_contents("../CRON/log.txt"));
        break;
    default: break;
}

?>
<?php
	/* This script creates a JPEG from the tif so that it can be displayed 
	in our viewer on the transcription page. */
include '../../../Library/SessionManager.php';
$session = new SessionManager();
include '../../../Library/DBHelper.php';
include '../../../Library/IndicesDBHelper.php';

$collection = $_POST["collection"];
//error_reporting(E_ERROR);
//Image variable which holds the filename of the document that was selected
//from the index page and was posted in the php file for convertion.
$image = $_POST["fileName"];
$docID = $_POST["docID"];
$DB = new IndicesDBHelper();
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
$doc = $DB->SP_TEMPLATE_INDICES_DOCUMENT_SELECT($collection,$docID);


	//Batch command that executes an Imagemagick conversion.
	//temp directory:
    $dir = $_SERVER['DOCUMENT_ROOT'] . "/" . "BandoCat/Temp/";
    if(!is_dir($dir))
        mkdir($dir,0777);
	$temp = $dir . $image;

	$command = "convert $config[StorageDir]/" . preg_replace('/\s+/', '', $doc['BookName']) . "/$image.tif $temp.jpg";
	exec($command,$yaks1);
    echo "../../Temp/$image.jpg";
?>
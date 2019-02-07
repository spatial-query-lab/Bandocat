<?php
/* 	This script is called when a user submits a rectification. The script executes
	commands that perform a translate, warp, and a kml overylay on the tif that is being rectified.
	This script needs to have SQL statements added to it eventually that will allow our bando-query app to 
	work seemlessly with the rectified maps. */
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/MapDBHelper.php');
$DB = new MapDBHelper();

	//store errors in string
	$error_str = "";

	ini_set("allow_url_fopen", 1);
	include('class.php');
	$script = json_decode( $_POST['jsonData']);

	$imageInfo = get_object_vars($script->fileName);

	//get collection information and stored them in array $collection_info for later use
	$collection_info = $DB->SP_GET_COLLECTION_CONFIG($imageInfo['collection']);
	//get information from a map in document table and store them in array $document
	$document = $DB->SP_TEMPLATE_MAP_DOCUMENT_SELECT($imageInfo['collection'],$imageInfo['docID']);

	//Specify the input and ouput Path for translated image on temporary workspace
	//type == front
	if($imageInfo['type'] == "front") {

		$inputTranslatePath = $collection_info['StorageDir'] . $document['FileNamePath'];

        $outputTranslatePath = "../Temp/translated_" . $document['FileName'];
    }
	else //type == back
    {
		$inputTranslatePath = $collection_info['StorageDir'] . $document['FileNameBackPath'];
        $outputTranslatePath = "../Temp/translated_" . $document['FileNameBack'];
    }
    //generate shell script for translate and warp
    $GeoTIFFsPath = "../Temp/GeoTIFFs/" . $imageInfo['geoTIFFName'];
    
	$command = $script->translate . " " . $inputTranslatePath . " " . $outputTranslatePath ; //translate script
	$command2 = $script -> warp . " " . $outputTranslatePath . " " . $GeoTIFFsPath ; //warp script

	//run translation script on CLI
	if(exec($command)) {
        //echo "TRANSLATE SUCCESS";
    }
	else {
        echo "Translate failed";
        $error_str .= "Translate failed";
        return;
    }
	//run warp script on CLI
	if(exec($command2)) {
        //echo "\nWARP SUCCESS";
    }
	else {
        echo "\nWarp failed";
        $error_str .= "Warp failed";
        return;
    }

    //run KML superoverlay script on CLI
	if(exec("gdal_translate -of KMLSUPEROVERLAY " . $GeoTIFFsPath . " ../Temp/GeoTIFFs/" . $imageInfo['KMZname'] .  " -co FORMAT=JPEG")){
        //echo "\nKMLSUPEROVERLAY SUCCESS";
	}
	else {
        echo "\nSuperoverlay failed";
        $error_str .= "\nSuperoverlay failed";
        return;
    }
    //specify full directory of georec file ot use in CLI
	$geoRec_fulldir = $collection_info['GeoRecDir'] . $imageInfo['subDirectory'];

	//check and create GeoRecDir if needed
	$cmd_georecdir = 'if not exist "' . $collection_info['GeoRecDir'] . '" mkdir "' . $collection_info['GeoRecDir'] . '"';
	exec($cmd_georecdir,$retcode);

	//create subfolder;
	$cmd_mkdir = 'if not exist "' . $geoRec_fulldir  . '" mkdir  "' . $geoRec_fulldir .  '"';
	exec($cmd_mkdir,$output,$code);
	//move rectified TIF to GeoRecDir/DrawerName;
	$cmd_movetif =  'cd "../Temp/GeoTIFFs/" & move /Y "' . $imageInfo['geoTIFFName'] . '" "' . $geoRec_fulldir . '/"';
	exec($cmd_movetif,$output2,$code2);
	//print_r($output);
	//move KMZ to GeoRecDir/DrawerName
	$cmd_moveKMZ = 'cd "../Temp/GeoTIFFs/" & move /Y "' . $imageInfo['KMZname'] . '" "' . $geoRec_fulldir . '/"';
	exec($cmd_moveKMZ,$output3,$code3);
	//print_r($output2);
	//get points
	$error_flag = false;
	$isBack = ($imageInfo['type'] == "back" ? true : false); //isback == 1 (true)
	$pointEntries = json_decode($_POST['pointEntries']);

	//delete old entries from georectification table
	$ret = $DB->GEOREC_ENTRIES_DELETE($imageInfo['docID']);
	//insert new georec entries into georectification table
	for($i = 0; $i < count($pointEntries); $i++)
	{
		if(array_filter($pointEntries[$i]))
			$ret = $DB->GEOREC_ENTRY_INSERT($imageInfo['docID'],$isBack,$pointEntries[$i][0],$pointEntries[$i][1],$pointEntries[$i][2],$pointEntries[$i][3],$pointEntries[$i][4],$pointEntries[$i][5],$pointEntries[$i][6]);
			if(!$ret)
			{
                $error_flag = true;
			}


	}

	//update georec KMZ Path and GeoTIFFs Path into document table
	if($error_flag == false) //means there is no error occured so far
	{

        $geoTIFFpath = $imageInfo['subDirectory'] . "/" . $imageInfo['geoTIFFName'];
        $KMZpath = $imageInfo['subDirectory'] . "/" . $imageInfo['KMZname'];
		switch($imageInfo['type'])
		{
			case "front":
				$ret = $DB->DOCUMENT_GEORECPATHS_UPDATE($imageInfo['docID'],$KMZpath,$geoTIFFpath,null,null);
				break;
			case "back":
                $ret = $DB->DOCUMENT_GEORECPATHS_UPDATE($imageInfo['docID'],null,null,$KMZpath,$geoTIFFpath);
				break;
			default: $error_flag = true;
				break;
		}

	}

	//check errors
	if(!$ret)
		$error_flag = true;
	else
	{
		//update rectification status
        switch($imageInfo['type'])
        {
            case "front":
                $ret = $DB->DOCUMENT_GEORECSTATUS_UPDATE($imageInfo['docID'],false,1);
                break;
            case "back":
                $ret = $DB->DOCUMENT_GEORECSTATUS_UPDATE($imageInfo['docID'],true,1);
                break;
            default: $error_flag = true;
                break;
        }
	}

	//check errors
	if(!$ret)
		$error_flag = true;

	if($error_flag) {
        echo "\nDatabase Update failed";
        $error_str .= "Database Update failed";
    }
	//else echo "\nDATABASE UPDATE SUCCESS";


	//Moved to cancel.php --- delete Tiles and
	//A::deleteDir("../Temp/Tiles/" . $imageInfo['tempSubDirectory']);
    //delete translated image from temporary workspace
	unlink($outputTranslatePath);

	//write log
	if($error_flag == false) //no error
    {
        $ret = $DB->SP_LOG_WRITE("rectify", $collection_info['CollectionID'], $imageInfo['docID'], $session->getUserID(), "success", $imageInfo['type'] . " scan");
    	echo "Success!";
    }
	else $ret = $DB->SP_LOG_WRITE("rectify", $collection_info['CollectionID'], $imageInfo['docID'], $session->getUserID(),"fail",$error_str); //error: status = fail
?>

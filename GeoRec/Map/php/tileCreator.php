<?php
/* 	This script creates tiles from a tif that is passed to it. 
	The script returns a JSON containing the dimensions of the image and 
	the path to the newly creates tiles directory. */
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/MapDBHelper.php');
$DB = new MapDBHelper();
$collection = $DB->SP_GET_COLLECTION_CONFIG($_POST['collection']);

//get document information from the database
$document = $DB->SP_TEMPLATE_MAP_DOCUMENT_SELECT($_POST['collection'],$_POST['docID']);

//declare image name and image path based on type of the image (front scan or back scan)
	switch($_POST['type'])
	{
		case "front":
			$image = $document['FileName'];
			$imagepath = $collection['StorageDir'] . $document['FileNamePath'];
			break;
		case "back":
			$image = $document['FileNameBack'];
			$imagepath = $collection['StorageDir'] . $document['FileNameBackPath'];
			break;
		default: // this case should not happen
			$image = "";
			$imagepath = "";
			break;
	}

//name of sub directory inside Temp directory (to store generated Tiles for this document)

if(strchr($image, ".tif") == ".tif")
	$tempSubDir = str_replace(".tif","",$image);

if (strchr($image, ".tiff") == ".tiff")
    $tempSubDir = str_replace(".tiff", "", $image);


	//create Temp folder if needed (to store tiles temporarily)
	if(!is_dir("../Temp"))
    	mkdir("../Temp");
//create Tiles folder in Temp if needed (to store tiles temporarily)
	if(!is_dir("../Temp/Tiles"))
		mkdir("../Temp/Tiles");
//create GeoTiffs folder in Temp if needed (to temporarily store GeoTIFFS for later user)
	if(!is_dir("../Temp/GeoTIFFs"))
		mkdir("../Temp/GeoTIFFs");
	//create sub directory inside Temp folder
	if(!is_dir("../Temp/Tiles/".$tempSubDir))
		mkdir("../Temp/Tiles/".$tempSubDir);

	//shell command that return width and height of the image
	$cmd_imagesize = 'identify -format "%w,%h" "' . $imagepath . '"';
	exec($cmd_imagesize,$dimensions_output);

//Conditional statement that replacess .tif or .tiff extension from the image info
if(strchr($image, ".tif") == ".tif")
    $kmzName = str_replace(".tif",".kmz",$image);

if (strchr($image, ".tiff") == ".tiff")
    $kmzName = str_replace(".tiff",".kmz",$image);

//array of image information for this document
$imageInfo = array(
	'type' => $_POST['type'],
	'collection' => $_POST['collection'],
	'docID' => $_POST['docID'],
    'fileName' => $image,
    'tempSubDirectory' => $tempSubDir,
	'subDirectory' => explode("-",$image)[0],
	'geoTIFFName' => str_replace(".tif","_rectified.tif",$image),
	'KMZname' => $kmzName,
    'height' => explode(',',$dimensions_output[0])[1],
    'width' => explode(',',$dimensions_output[0])[0]
	);

	//compute and run shell command to create tiles
	$zoom = log(max($imageInfo['width'], $imageInfo['height'])/256, 2);
	$zoom = ceil($zoom);
	$command = "python ../../../ExtLibrary/GDAL/gdal2tiles-multiprocess.py -l -p raster -z 0-" . $zoom . " -w none -e " . $imagepath . " ../Temp/Tiles/". $imageInfo['tempSubDirectory'];
	exec($command,$output,$ret);
	//echo $command . "<br>";
	//print_r(array($output,$ret)); //use this to debug $command
//return image info array

	echo json_encode($imageInfo);
?>

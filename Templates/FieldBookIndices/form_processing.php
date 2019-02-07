<?php
//check for session
require '../../Library/SessionManager.php';
$session = new SessionManager();
//prevent accessing directly
if(!isset($_POST))
    header('Location: index.php');

require '../../Library/DBHelper.php';
require '../../Library/IndicesDBHelper.php';
require '../../Library/ControlsRender.php';

$Render = new ControlsRender();
$DB = new IndicesDBHelper();
//store passed info into data variable
$data = $_POST;
//check for special characters in passed variables
$action = htmlspecialchars($data['txtAction']);
$collection = htmlspecialchars($data['txtCollection']);
//get appropriate DB
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
//store book indicies
$book = $DB->GET_INDICES_BOOK($collection);
//check if indicies exists 0 is return false 1 is true (found not found)
$entry = $DB->SP_TEMPLATE_INDICES_DOCUMENT_CHECK_EXIST_RECORD($collection, $data['txtLibraryIndex']);
$comments = null;
$valid = false;
$msg = array();
$retval = false;

//if we are reviewing the indices
if($action == "review")
{
    $valid = true;
    //update the DB with the new reviewed information
    $retval = $DB->SP_TEMPLATE_INDICES_DOCUMENT_UPDATE($collection, $data['txtDocID'], $data['txtLibraryIndex'], $data['ddlBookID'],
        $data['rbPageType'], $data['txtPageNumber'], $data['txtComments'], $data['rbNeedsReview'], $data['txtLibraryIndex']);
    $comments = "Library Index:" . $data['txtLibraryIndex'];
    if($retval)
        array_push($msg,"Update Query: Success");
    else
        //print_r($retval);
        array_push($msg, "Update Query: Fail", $retval);
}

//if we are cataloging
if ($action == "catalog")
{
    //Create Thumbnails dir if it doesn't exist
    if(!is_dir("../../Thumbnails"))
        exec(mkdir("../../Thumbnails", 0777));


    $valid = true;
    //get the file names from the POST
    $file_name = $_FILES['file_array']['name'];
    $indicesFolder = $Render->NAME_INDICES_FOLDER($file_name, $book);

    $filenamepath = $config['StorageDir'].$indicesFolder;
    $thumbFilenamepath = $config['ThumbnailDir'].$indicesFolder;

    //VALIDATION
    //Entry exists in Database//                1
    //Indices file exists //                    2
    //Indices Thumbnail file exists//           3
    //Indices File error check //               4
    //Indices Thumbnail file error check//      5

    //1
    if($entry > 0)
    {
        $valid = false;
        array_push($msg, "Entry existence validation: FAIL");
    }

    else{
        $valid = true;
        //array_push($msg, "Entry existence validation: Success");
    }

    //2
    if(file_exists($filenamepath . '/' . $file_name)){
        $valid = false;
        array_push($msg, "File existence validation: FAIL");
    }
    else{
        //array_push($msg, "File existence validation: Success");
    }

    //3
    if(file_exists('../../'.$thumbFilenamepath.'/' . str_replace('.tif', '.jpg',$file_name))){
        $valid = false;
        array_push($msg, "Thumbnail file existence validation: FAIL");
    }
    else
       // array_push($msg, "Thumbnail file existence validation: Success");

    //4
    if ($_FILES['file_array']['error'] == 0)
    {
        $filename = $_FILES['file_array']['name'];
        //array_push($msg, "Indices File error check: Success");
    } else {
        $valid = false;
        array_push($msg, "Indices File error check: FAIL");
    }


    //5
    if ($_FILES['file_array']['error'] == 0)
    {
        $thumbnail = $config['ThumbnailDir'] . str_replace('.tif', '.jpg', $file_name);
        //array_push($msg, 'Indices Thumbnail File error check: Success');
    }
    else{
        $valid = false;
        array_push($msg, "Indices Thumbnail File error check: FAIL");
    }


    //assuming all of the above error checking passes. we can proceed to insert
    if( $valid == true)
    {
        $file_name_path = preg_replace('/\s+/', '', $indicesFolder) . '/' . $file_name;
        $retval = $DB->SP_TEMPLATE_INDICES_DOCUMENT_INSERT($collection, $data['txtLibraryIndex'], $data['ddlBookID'],
            $data['rbPageType'], $data['txtPageNumber'], $data['txtComments'], $data['rbNeedsReview'], $data['txtLibraryIndex'], $file_name_path);

        //Stores the document id from the database to the variable for
        $data['txtDocID'] = $retval;
        $comments = "Library Index: " . $data['txtLibraryIndex'];
        if($valid == true && $retval != false)
            array_push($msg, "Entry upload: Successful");
        if($retval == false)
        {
            $valid = false;
            array_push($msg, "Upload: FAIL");
        }
    }

    else
        array_push($msg, "Upload: FAIL");

        //Check folder, create folder for indices TIF
    if ($valid == true)
    {
        if (!is_dir($filenamepath))
            mkdir($filenamepath, 0777);
        move_uploaded_file($_FILES["file_array"]["tmp_name"], $filenamepath . '/' . basename($file_name));

        //Script for creation of file and thumbnail
        $thumbnailExtTIFF = $file_name;
        $thumbnailExtJPG = str_replace('.tif', '.jpg', $thumbnailExtTIFF);
        if (!is_dir('../../' . $thumbFilenamepath))
            mkdir('../../' . $thumbFilenamepath, 0777);
        $exec1 = "convert " . $filenamepath . '/' . basename($file_name) . " -deskew 40% -fuzz 50% -trim -resize 200 " . '../../' . $thumbFilenamepath.'/'.$thumbnailExtJPG;
        exec($exec1, $yaks1);
    }
}
//need to implement this
if($action == "delete")
{
//    $errors = 0;
//    $info = $DB->SP_TEMPLATE_INDICES_DOCUMENT_SELECT($data["txtCollection"], $data['txtDocID']);
//    $comments = "Library Index: " . $info['LibraryIndex'];
//
//    $frontScanPath = $config['StorageDir'].$info['FileNamePath'];
//
//    //Thumbnail conversion to jpg and path detection
//    //$thumbnailPath = str_replace('/','\\',$config['ThumbnailDir']);
//    $directory = $_SERVER['DOCUMENT_ROOT']."/BandoCat";
//
//    $frontThumbnailPathTIF = $config['ThumbnailDir'].$info['FileName'];
//    $backThumbnailPathTIF = $config['ThumbnailDir'].$info['FileNameBack'];
//
//    $frontThumbnailPathJPG = "../../".str_replace(".tif", ".jpg", $frontThumbnailPathTIF);
//    $backThumbnailPathJPG = "../../".str_replace(".tif", ".jpg", $backThumbnailPathTIF);
//
//    $retval = $DB->DELETE_DOCUMENT($collection,$data['txtDocID']);
//
//    if (file_exists($frontScanPath))
//        unlink($frontScanPath);
//    if (file_exists($frontThumbnailPathJPG))
//        unlink($frontThumbnailPathJPG);
//
//    if ($info['FileNameBack'] !== "")
//    {
//        if (file_exists($backScanPath))
//            unlink($backScanPath);
//        if (file_exists($backThumbnailPathJPG))
//            unlink($backThumbnailPathJPG);
//    }

}


    //REPORT STATUS
    if ($retval == false) {
        $logstatus = "fail";
        array_push($msg, "Failed to Submit!");
    } else {
        $logstatus = "success";
        array_push($msg, "Report Status: Success!");
    }

    //write log
    $retval = $DB->SP_LOG_WRITE($action,$config['CollectionID'],$data['txtDocID'],$session->getUserID(),$logstatus,$comments);
    if(!$retval)
        array_push($msg, "ERROR: Fail to write log!");

    if($retval == false || $valid == false)
    {
        require '../../Library/ErrorLogger.php';
        $LOG = new ErrorLogger();
        if($action == "review")
            $LOG->writeErrorLog($session->getUserName(),$collection,$data['txtDocID'],$msg,$comments);
        else if ($action == "catalog")
            $LOG->writeErrorLog($session->getUserName(),$collection,basename($_FILES['file_array']['name']),$msg,$comments);
        else if ($action == "delete")
            $LOG->writeErrorLog($session->getUserName(),$collection,$data['txtDocID'],$msg,$comments);
    }
echo json_encode($msg);

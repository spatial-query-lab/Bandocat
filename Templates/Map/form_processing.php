<?php
//check for session
require '../../Library/SessionManager.php';
$session = new SessionManager();
//prevent accessing directly
if(!isset($_POST))
    header('Location: index.php');
require '../../Library/DBHelper.php';
require '../../Library/MapDBHelper.php';
$DB = new MapDBHelper();
//store passed info into data variable)
$data = $_POST;
var_dump($data);
//check for special characters in passed variables
$action = htmlspecialchars($data['txtAction']);
$collection = htmlspecialchars($data['txtCollection']);
//get appropriate db
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
$comments = null;

//if the action is not delete
if($action != "delete")
{
    //data pre-processing
    //DateF
    require '../../Library/DateHelper.php';
    $date = new DateHelper();
    $startdate = $date->mergeDate($data['ddlStartMonth'], $data['ddlStartDay'], $data['ddlStartYear']);
    $enddate = $date->mergeDate($data['ddlEndMonth'], $data['ddlEndDay'], $data['ddlEndYear']);
    //Company, Author, Customer, Medium ID
    $DB->SP_TEMPLATE_MAP_CUSTOMER_GET_ID_FROM_NAME_WITH_INSERT($collection, $data['txtCustomer'], $customerID);
    $DB->SP_TEMPLATE_MAP_COMPANY_GET_ID_FROM_NAME_WITH_INSERT($collection, $data['txtCompany'], $companyID);
    $DB->SP_TEMPLATE_MAP_AUTHOR_GET_ID_FROM_NAME_WITH_INSERT($collection, $data['txtAuthor'], $authorID);
    $DB->SP_TEMPLATE_MAP_MEDIUM_GET_ID_FROM_NAME($collection, $data['ddlMedium'], $mediumID);
}
$valid = false;
$msg = array();
$retval = false;
//if the action is review
if($action == "review")
{
    if($collection != "pennyfenner")
    {
        $valid = true;
        $retval = $DB->SP_TEMPLATE_MAP_DOCUMENT_UPDATE($collection,$data['txtDocID'],$data['txtLibraryIndex'],$data['txtTitle'],$data['txtSubtitle'],
            $data['rbIsMap'],$data['txtMapScale'],$data['rbHasNorthArrow'],$data['rbHasStreets'],
            $data['rbHasPOI'],$data['rbHasCoordinates'],$data['rbHasCoast'],$data['rbNeedsReview'],
            $data['txtComments'],$customerID,$startdate,$enddate,$data['txtFieldBookNumber'],$data['txtFieldBookPage'],$data['ddlReadability'],
            $data['ddlRectifiability'],$companyID,$data['txtType'],$mediumID,$authorID);
        $comments = "Library Index:" . $data['txtLibraryIndex'];
    }
    else if ($collection == "pennyfenner")
    {
        $valid = true;
        $data['txtType'] = null;
        $retval = $DB->SP_TEMPLATE_MAP_DOCUMENT_WITH_JOBNUMBER_UPDATE($collection,$data['txtDocID'],$data['txtLibraryIndex'],$data['txtTitle'],$data['txtSubtitle'],
            $data['rbIsMap'],$data['txtMapScale'],$data['rbHasNorthArrow'],$data['rbHasStreets'],
            $data['rbHasPOI'],$data['rbHasCoordinates'],$data['rbHasCoast'],$data['rbNeedsReview'],
            $data['txtComments'],$customerID,$startdate,$enddate,$data['txtJobNumber'],$data['txtFieldBookNumber'],$data['txtFieldBookPage'],$data['ddlReadability'],
            $data['ddlRectifiability'],$companyID,$data['txtType'],$mediumID,$authorID);
        $comments = "Library Index:" . $data['txtLibraryIndex'];
    }

    // array_push($msg,"Update Query: GOOD");
}
//catalog (new document)
else if($action == "catalog")
{
    $session->unblockSession(); //close current session so that user can run another tab
    //Create Thumbnails dir if it doesn't exist
    if(!is_dir("../../Thumbnails"))
        exec(mkdir("../../Thumbnails", 0777));


    $filename = "";
    $filenameback = "";
    $filenamepath = "";
    $filenamebackpath = "";
    $folder = "";

    //VALIDATION
    //check front FILES upload //1
    //check back FILES upload //2
    //check existing front map //3
    //check existing back map (if available) //4
    //check if thumbnail front exist //5
    //check if thumbnail back exist (if available) //6
    //check if record exists on DB //7
    $valid = false;
    $hasBack = true;

    //1
    if ($_FILES['fileUpload']['error'] == 0) {
        $valid = true;
        $filename = $_FILES['fileUpload']['name'];
        //array_push($msg, "Front Scan: GOOD");
    } else {
        $valid = false;
        array_push($msg, "Front Scan: ERROR");
    }
    //2
    if ($valid == true && ($_FILES['fileUploadBack']['error'] == 0 || $_FILES['fileUploadBack']['error'] == 4)) {
        $valid = true;
        if ($_FILES['fileUploadBack']['error'] == 4) {
            $hasBack = false;
            //array_push($msg, "Back Scan: NA");
        } else {
            $filenameback = $_FILES['fileUploadBack']['name'];
            //array_push($msg, "Back Scan: GOOD");
        }
    } else {
        $valid = false;
        array_push($msg, "Back Scan: ERROR");
    }
    //3
    if ($valid == true && $_FILES['fileUpload']['error'] == 0) {
        $tempFilename = explode('-', $filename);
        $folder = $tempFilename[0];
        $filenamepath = $config['StorageDir'] . $folder;
        if (file_exists($filenamepath . '/' . $filename)) {
            array_push($msg, "Front Map: EXISTED");
            $valid = false;
        }
        //else array_push($msg, "Front Map: GOOD");
    }
    //4
    if ($hasBack == true && $valid == true) {
        $tempFilename = explode('-', $filenameback);
        $filenamebackpath = $config['StorageDir'] . $tempFilename[0];
        if (file_exists($filenamebackpath . '/' . $filenameback)) {
            array_push($msg, "Back Map: EXISTED");
            $valid = false;
        }
        //else array_push($msg, "Back Map: GOOD");
    }
    //5
    if ($valid == true && $_FILES['fileUpload']['error'] == 0) {
        $frontthumbnail = $config['ThumbnailDir'] . str_replace('.tif', '.jpg', $filename);
        if (file_exists($frontthumbnail)) {
            array_push($msg, "Front Thumbnail: EXISTED");
            $valid = false;
        }
        //else array_push($msg, "Front Thumbnail: GOOD");
    }
    //6
    if ($valid == true && $hasBack == true) {
        $backthumbnail = $config['ThumbnailDir'] . str_replace('.tif', '.jpg', $filenameback);
        if (file_exists($backthumbnail)) {
            array_push($msg, "Back Thumbnail: EXISTED");
            $valid = false;
        }
        //else array_push($msg, "Back Thumbnail: GOOD");
    }

    //7
    if ($valid == true) {
        $existed = $DB->SP_TEMPLATE_MAP_DOCUMENT_CHECK_EXIST_RECORD($collection, $data['txtLibraryIndex']);
        if ($existed != "GOOD") {
            $valid = false;
            array_push($msg, "Database Check: EXISTED");
        }
        //else array_push($msg, "Database Check: GOOD");
    }


    //Check folder, create folder
    if ($valid == true) {
        if (!is_dir($filenamepath))
            mkdir($filenamepath, 0777);
        move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $filenamepath . '/' . basename($_FILES["fileUpload"]["name"]));

        if ($hasBack == true) {
            if (!is_dir($filenamebackpath))
                mkdir($filenamebackpath, 0777);
            move_uploaded_file($_FILES["fileUploadBack"]["tmp_name"], $filenamebackpath . '/' . basename($_FILES["fileUploadBack"]["name"]));
        }

        $backpath = "";
        if($hasBack == true)
            $backpath = str_replace($config['StorageDir'],"",$filenamebackpath) . "/" . $filenameback;
        // INSERT QUERY
        if($collection != "pennyfenner")
        {
            // Building map scale string
            $mapScale = $data['numberLeft'] . " " . $data['unitLeft'] . " = " . $data['numberRight'] . " " . $data['unitRight'];
            $retval = $DB->MAP_DOCUMENT_INSERT($collection, $data['txtLibraryIndex'], $data['txtTitle'], $data['txtSubtitle'],
                $data['rbIsMap'], $mapScale, $data['rbHasNorthArrow'], $data['rbHasStreets'],
                $data['rbHasPOI'], $data['rbHasCoordinates'], $data['rbHasCoast'], $filename, $filenameback, $data['rbNeedsReview'],
                $data['txtComments'], $customerID, $startdate, $enddate, $data['txtFieldBookNumber'], $data['txtFieldBookPage'], $data['ddlReadability'],
                $data['ddlRectifiability'], $companyID, $data['txtType'], $mediumID, $authorID, str_replace($config['StorageDir'],"",$filenamepath) . "/" . $filename,$backpath,$data['hasScaleBar'],$data['POIDescription']);
            $data['txtDocID'] = $retval;
            $comments = "Library Index: " . $data['txtLibraryIndex'];
        }
        else if($collection == "pennyfenner")
        {
            $mapScale = $data['numberLeft'] . " " . $data['unitLeft'] . " = " . $data['numberRight'] . " " . $data['unitRight'];
            $data['txtType'] = null;
            $retval = $DB->SP_TEMPLATE_MAP_DOCUMENT_WITH_JOBNUMBER_INSERT($collection, $data['txtLibraryIndex'], $data['txtTitle'], $data['txtSubtitle'],
                $data['rbIsMap'], $mapScale, $data['rbHasNorthArrow'], $data['rbHasStreets'],
                $data['rbHasPOI'], $data['rbHasCoordinates'], $data['rbHasCoast'], $filename, $filenameback, $data['rbNeedsReview'],
                $data['txtComments'], $customerID, $startdate, $enddate, $data['txtJobNumber'], $data['txtFieldBookNumber'], $data['txtFieldBookPage'], $data['ddlReadability'],
                $data['ddlRectifiability'], $companyID,$data['txtType'], $mediumID, $authorID, str_replace($config['StorageDir'],"",$filenamepath) . "/" . $filename,$backpath,$data['hasScaleBar'],$data['POIDescription']);
            $data['txtDocID'] = $retval;
            $comments = "Library Index: " . $data['txtLibraryIndex'];
        }


        //script for thumbnail
        if (!is_dir('../../' . $config['ThumbnailDir']))
            mkdir('../../' . $config['ThumbnailDir'], 0777);

        $exec1 = "convert " . $filenamepath . '/' . basename($_FILES["fileUpload"]["name"]) . " -deskew 40% -fuzz 50% -trim -resize 200 " . '../../' . $frontthumbnail;
        exec($exec1, $yaks1);
        if ($hasBack == true) {
            $exec2 = "convert " . $filenamebackpath . '/' . basename($_FILES["fileUploadBack"]["name"]) . " -deskew 40% -fuzz 50% -trim -resize 200 " . '../../' . $backthumbnail;
            exec($exec2, $yaks2);
        }

    }


}
else if($action == "delete")
{
    $errors = 0;
    $info = $DB->SP_TEMPLATE_MAP_DOCUMENT_SELECT($data["txtCollection"], $data['txtDocID']);
    $comments = "Library Index: " . $info['LibraryIndex'];

    $frontScanPath = $config['StorageDir'].$info['FileNamePath'];
    $backScanPath = $config['StorageDir'].$info['FileNameBackPath'];

    //Thumbnail conversion to jpg and path detection
    //$thumbnailPath = str_replace('/','\\',$config['ThumbnailDir']);
    $directory = $_SERVER['DOCUMENT_ROOT']."/BandoCat";

    $frontThumbnailPathTIF = $config['ThumbnailDir'].$info['FileName'];
    $backThumbnailPathTIF = $config['ThumbnailDir'].$info['FileNameBack'];

    $frontThumbnailPathJPG = "../../".str_replace(".tif", ".jpg", $frontThumbnailPathTIF);
    $backThumbnailPathJPG = "../../".str_replace(".tif", ".jpg", $backThumbnailPathTIF);

    $retval = $DB->DELETE_DOCUMENT($collection,$data['txtDocID']);

    if (file_exists($frontScanPath))
        unlink($frontScanPath);
    if (file_exists($frontThumbnailPathJPG))
        unlink($frontThumbnailPathJPG);

    if ($info['FileNameBack'] !== "")
    {
        if (file_exists($backScanPath))
            unlink($backScanPath);
        if (file_exists($backThumbnailPathJPG))
            unlink($backThumbnailPathJPG);
    }
//        if (file_exists($frontScanPath) || file_exists($frontThumbnailPathJPG) || file_exists($backScanPath) || file_exists($backThumbnailPathJPG))
//            $errors++;
}

//REPORT STATUS
if ($retval == false) {
    $logstatus = "fail";
    array_push($msg, "Failed to Submit!");
} else {
    $logstatus = "success";
    array_push($msg, "Success!");
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
        $LOG->writeErrorLog($session->getUserName(),$collection,basename($_FILES['fileUpload']['name']),$msg,$comments);
    else if ($action == "delete")
        $LOG->writeErrorLog($session->getUserName(),$collection,$data['txtDocID'],$msg,$comments);
}
echo json_encode($msg);
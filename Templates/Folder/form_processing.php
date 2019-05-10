<?php
//check for session
require '../../Library/SessionManager.php';
$session = new SessionManager();
//prevent accessing directly
if(!isset($_POST))
    header('Location: index.php');

require '../../Library/DBHelper.php';
require '../../Library/FolderDBHelper.php';
$DB = new FolderDBHelper();
//store passed info into data variable (txtAction: "delete", "txtCollection": collection, "txtDocID": documentID)
$data = $_POST;
$action = htmlspecialchars($data['txtAction']);
$collection = htmlspecialchars($data['txtCollection']);
//get appropriate db
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
$comments = null;
var_dump($_POST);
//if the action is not delete
if($action != "delete")
{
    //data pre-processing
    //Date
    require '../../Library/DateHelper.php';
    $date = new DateHelper();
    $startdate = $date->mergeDate($data['ddlStartMonth'], $data['ddlStartDay'], $data['ddlStartYear']);
    $enddate = $date->mergeDate($data['ddlEndMonth'], $data['ddlEndDay'], $data['ddlEndYear']);
}
$valid = false;
$msg = array();
$retval = false;
//if the action is review or catalog
if($action == "review" || $action == "catalog")
{
    //grab authors that were in the _POST
    $author_arrays = json_decode($_POST['authors']);
    //attempt to insert authors into db
    $retval = $DB->SP_TEMPLATE_FOLDER_DOCUMENTAUTHOR_INSERT($collection,$data['txtDocID'],$author_arrays);

    if($retval != false)
    {
        //insert retval is true, update the fieldbook document
        $retval = $DB->SP_TEMPLATE_FOLDER_DOCUMENT_UPDATE($collection, $data['txtDocID'], $data['txtLibraryIndex'], $data['txtTitle'], $data['rbInASubfolder'], $data['txtSubfolderComments'], $data['classificationMethod'], $data['txtClassificationComments'],0,$data['folderNeedsReview'],$data['txtComments'],$startdate, $enddate);
        $comments = "Library Index:" . $data['txtLibraryIndex'];
        $valid = true;
    }
    // array_push($msg,"Update Query: GOOD");
}
//if the action is delete
else if($action == "delete")
{
    $errors = 0;
    //select the folder document template from the supplied collection, and docid
    $info = $DB->SP_TEMPLATE_FOLDER_DOCUMENT_SELECT($data["txtCollection"], $data['txtDocID']);
    //store the library index into comments
    $comments = "Library Index: " . $info['LibraryIndex'];
    //store the filenamepath into frontscanpath
    $frontScanPath = $config['StorageDir'].$info['FileNamePath'];
    //store the filenamepath into backscanpath
    $backScanPath = $config['StorageDir'].$info['FileNameBackPath'];
    //store the directory
    $directory = $_SERVER['DOCUMENT_ROOT']."/BandoCat";
    //store the thumbnailpath
    $frontThumbnailPath = "../../" . $config['ThumbnailDir'] . str_replace(".tif",".jpg",$info['FileName']);
    $backThumbnailPath = "../../" . $config['ThumbnailDir']. str_replace(".tif",".jpg",$info['FileNameBack']);
    //call the delete document function passing in the collection, and the documentId
    $retval = $DB->DELETE_DOCUMENT($collection,$data['txtDocID']);
    if($retval)
        //If document was deleted, delete the documents author
        $retval = $DB->TEMPLATE_FOLDER_DELETE_DOCUMENTAUTHOR($collection,$data['txtDocID']);
    //If document was deleted, unlink all filepaths
    if($retval)
    {
        if (file_exists($frontScanPath))
            unlink($frontScanPath);
        if (file_exists($frontThumbnailPath)) {
            unlink($frontThumbnailPath);
        }
        if (file_exists($backScanPath))
            unlink($backScanPath);
        if (file_exists($backThumbnailPath)) {
            unlink($backThumbnailPath);
        }
    }
}

//REPORT STATUS
if ($retval == false) {
    $logstatus = "fail";
    array_push($msg, "Failed to Submit!");
} else {
    $logstatus = "success";
    array_push($msg, "Success!");
}

//write log passing what happened to the db
$retval = $DB->SP_LOG_WRITE($action,$config['CollectionID'],$data['txtDocID'],$session->getUserID(),$logstatus,$comments);
if(!$retval)
    array_push($msg, "ERROR: Fail to write log!");

if($retval == false || $valid == false)
{
    require '../../Library/ErrorLogger.php';
    $LOG = new ErrorLogger();
    if ($action == "catalog" || $action == "review")
        $LOG->writeErrorLog($session->getUserName(),$collection,$data['txtDocID'],$msg,$comments);
    else if ($action == "delete")
        $LOG->writeErrorLog($session->getUserName(),$collection,$data['txtDocID'],$msg,$comments);
}
echo json_encode($msg);
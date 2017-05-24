<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require '../../Library/DBHelper.php';
require '../../Library/CreatorHelper.php';
$DB = new CreatorHelper();
$ret = $DB->COLLECTION_INSERT($_POST['txtParameterName'],$_POST['txtDisplayName'],
    $_POST['txtDatabaseName'],$_POST['txtStorageDir'],$_POST['txtThumbnailDir'],
    $_POST['ddlTemplate'],$_POST['txtGeorecStorageDir']);
$newdbID = $ret;
if($ret) //$ret = new collection ID
{
    //copy structure of an existing db with same template to the new database
    //get dbname of an existing db with same template
    $existingdbname = $DB->GET_DBNAME_FROM_TEMPLATEID($_POST['ddlTemplate'], true);
    if ($existingdbname)
    {
        $ret = $DB->DATABASE_CLONE_NEW($_POST['txtDatabaseName'], $existingdbname);
    }
    else {
        $ret = false;
    }

}

if($ret == false)
{
    //rollback
    $rollback = $DB->COLLECTION_DELETE($newdbID);
}
else
{
//creating directories for thumbnail
    exec("cd ../../Thumbnails/ & md " . $_POST['txtParameterName'],$output,$retval);

    //creating directories for storage dir
    exec("md " . str_replace('/', '\\', $_POST['txtStorageDir']),$output2,$retval2);
    //creating directories for GeoRecStorageDir if not empty
    if($_POST['txtGeorecStorageDir'] != "")
        exec("md " . str_replace('/', '\\', $_POST['txtGeorecStorageDir']),$output3,$retval3);
}
if($ret)
    echo "Collection " . $_POST['txtDisplayName'] . " has been successfully created!";
else echo "Fail to create new collection";
?>
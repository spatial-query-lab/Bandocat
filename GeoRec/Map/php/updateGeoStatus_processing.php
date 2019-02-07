<?php
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/MapDBHelper.php');
$DB = new MapDBHelper();
$config = $DB->SP_GET_COLLECTION_CONFIG($_GET['col']);
$ret =$DB->SWITCH_DB($_GET['col']);
$comments = null;
$isBack = $_GET['type'] == "back" ? true : false; //identify if this map is a front or a back scan
if($ret)
{
	 $flag = $_POST['ddlGeoStatus'];
    switch ($flag)
    {
        case 2:			
            $comments = "Not Rectifiable";
            $retval = $DB->SP_LOG_WRITE("rstatus",$config['CollectionID'],$_POST['txtDocID'],$session->getUserID(),"success",$comments);
            if(!$retval)
                array_push($msg, "ERROR: Fail to write log!");
            break;
        
        case 4:     
            $comments = "Research Required";
            $retval = $DB->SP_LOG_WRITE("rstatus",$config['CollectionID'],$_POST['txtDocID'],$session->getUserID(),"success",$comments);		
            if(!$retval)
                array_push($msg, "ERROR: Fail to write log!");
            break;
        
        default:		
            $comments = null;
            break;
        
		
    }
    //write log	
	$DB->SWITCH_DB($_GET['col']);
    $ret = $DB->DOCUMENT_GEORECSTATUS_UPDATE($_POST['txtDocID'],$isBack,$_POST['ddlGeoStatus']);	
}
echo $ret;
<?php
//This performs server side action when user select an action on Action column in index.php
//This performs server side action when user select an action on Action column in index.php
spl_autoload_register(function ($class_name) {
    require_once "../../Library/" . $class_name . '.php';});

date_default_timezone_set("America/Chicago");
$session = new SessionManager();
$Schema = new TDLSchema();
$TDL = new TDLPublishJob();
$DB = new DBHelper();
$collectionName = $_POST['ddlCollection'];
$collection = $DB->GET_COLLECTION_INFO($collectionName);
$docID = $_POST['docID'];

//collect metadata of the document, given collectionName, templateID and documentID
switch($collection["templateID"])
{
    case 1: //map template
        $DB = new MapDBHelper();
        $doc = $DB->SP_TEMPLATE_MAP_DOCUMENT_SELECT($collectionName,$docID);
        break;
    case 2: //jobfolder template
        $DB = new FolderDBHelper();
        $doc = $DB->SP_TEMPLATE_FOLDER_DOCUMENT_SELECT($collectionName,$docID);
        $doc['Authors'] = $DB->GET_FOLDER_AUTHORS_BY_DOCUMENT_ID($collectionName,$docID); //multiple authors to 1 document
        break;
    case 3: //fieldbook template
        $DB = new FieldBookDBHelper();
        $doc = $DB->SP_TEMPLATE_FIELDBOOK_DOCUMENT_SELECT($collectionName,$docID);
        $doc['Crews'] = $DB->GET_FIELDBOOK_CREWS_BY_DOCUMENT_ID($collectionName,$docID); //multiple crews
        break;
    case 4: //indices template
        $DB = new IndicesDBHelper();
        $doc = $DB->SP_TEMPLATE_INDICES_DOCUMENT_SELECT($collectionName,$docID);
        break;
    default:
        $doc = null;
        echo "Error initializing class!";
        return; //error
}
//include Collection's info to the $doc Array
$doc["TDLCollection"] = $collection["TDLname"];
$doc += $collection;

//switch to the currently working Database
$DB->SWITCH_DB($collectionName);
//GET ITEM INFO
$dspaceDocInfo = $DB->PUBLISHING_DOCUMENT_GET_DSPACE_INFO($docID);
$dspaceID = $dspaceDocInfo['dspaceID'];


//Specify different actions to perform in this SWITCH/CASE statement
switch($_POST['action'])
{
    case "push":
        //PUSH
        $ret = $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,2); //push to queue;
        break;
    case "pop":
        //POP
        $ret = $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,0);
        break;
    case "unpublish":
        //START UNPUBLISH
        //FIND ALL BITSTREAMS, DELETE ALL BITSTREAMS BELONG TO THIS ITEM
        $bitstreams = json_decode($TDL->TDL_CUSTOM_GET("items/" . $dspaceID . "/bitstreams"),true);
        foreach($bitstreams as $b)
        {
            $ret = $TDL->TDL_CUSTOM_DELETE("bitstreams/" . $b["id"]); //delete bitstreams
            if($ret != "200") {
                echo "Deleting bitstreams, return value: " . $ret . "\n";
                return;
            }
        }
        //DELETE ITEM
        $ret = $TDL->TDL_CUSTOM_DELETE("items/" . $dspaceID); //delete item (metadata)

        //reset status flag
        //write log to DB
        $ret = $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,0);
        //reset
        if($ret) $ret = $DB->PUBLISHING_DOCUMENT_TDL_UPDATE($docID,0,null);
        break;
        //end UNPUBLISH
    case "update":
        //START UPDATE
        $convertedSchema = $Schema->convertSchema($collection['templateID'],$doc,false); //convert from BandoCat to TDL schema using method in TDLSchema
        //save the converted schema in a temporary json file
        $jsonfilename = __DIR__ . '\\' . $docID . '.json';
        $fp = fopen($jsonfilename, 'w');
        fwrite($fp, $convertedSchema);
        fclose($fp);
        $ret = $TDL->TDL_CUSTOM_PUT("items/" . $dspaceID . "/metadata",$jsonfilename,"application/json",null);
        unlink($jsonfilename);
        //END UPDATE
        break;
    default:
        echo "Error! Unclassified action!";
        return;
}

//WRITE A LOG
    if($ret)
        $ret = $DB->SP_LOG_WRITE($_POST['action'] . " (tdl)",$collection["collectionID"],$docID,$session->getUserID(),"success","");
    if($ret)
        echo "Success!";
    else echo "Error!";
    return;
?>
<?php
require_once __DIR__ . '\..\..\Library\SessionManager.php';
require_once __DIR__ . '\..\..\Library\DBHelper.php';
require_once __DIR__ . '\..\..\Library\DBHelper.php';
require_once __DIR__ . '\..\..\Library\MapDBHelper.php';
require_once __DIR__ . '\..\..\Library\FolderDBHelper.php';
require_once __DIR__ . '\..\..\Library\FieldBookDBHelper.php';
require_once __DIR__ . '\..\..\Library\IndicesDBHelper.php';

require_once __DIR__ . '\..\..\Library\TDLPublishDB.php';
require_once __DIR__ . '\..\..\Library\TDLSchema.php';
require_once __DIR__ . '\..\..\Library\TDLPublishJob.php';


class bitstream{
    public $name;
    public $type;
    public $md5_checksum;
    public $path;
    function __construct($name,$path,$type=null)
    {
        $this->name = $name;
        $this->path = $path;
        if($type != null)
            $this->type = $type;

        exec('certUtil -hashfile "' . $this->path . '" MD5',$output);
	    $this->md5_checksum = preg_replace('/\s+/', '', $output[1]);
    }
}

    /*
     * TDL Status:
     * -1: Error
     * 0 : not published
     * 1 : published
     * 2 : In Queue
     *10: publishing front
     * 11 publishing back
     */

    //debug flag
    $debug = true;
    if($debug)
        echo "DEBUG MODE IS ON\n";
    else echo "DEBUG MODE IS OFF\n";

    //prepare
    $DB = new DBHelper();
    $DS = new TDLPublishJob();
    $Schema = new TDLSchema();

    //create a log file if not exists
    $logfilename = "log.txt";
    if(!file_exists($logfilename))
        $logfile = fopen($logfilename,"w"); //create log file if not exists (write)
    else $logfile = fopen($logfilename,"a"); //open log file and append to it if file already exists

    fwrite($logfile,date(DATE_RFC2822) . ": " . "Job Started.\r\n");

    echo "TDL Publishing job\nLast updated: 04/14/2017\nBy Son Nguyen\n";
    echo "TDL Publishing job is ready....\n\n...\n...\n";

//*********************************************************************************************************************
    //Current collection: Blucher Maps (bluchermaps), ID = 1,template Map (use MapDBHelper)
    $collectionName = "bluchermaps";//change the variable if publishing to new collection
//*********************************************************************************************************************
    fwrite($logfile,date(DATE_RFC2822) . ": Targeted collection: " . $collectionName . "\r\n");
    //Dspace Community and collection info:
    $collection = $DB->GET_COLLECTION_INFO($collectionName);

    $TDLCommunityID = $collection["TDLcommunityID"];
    $TDLCollectionID = $collection["TDLcollectionID"];
    while(true){ //infinite loop
	$docID = null;
    //while(!$isEmptyQueue) {
        //use this database
        $DB->SWITCH_DB($collectionName);
        $docID = $DB->PUBLISHING_DOCUMENT_GET_PUBLISHING_ID(); //look for abandoned job
        //grab the next one
        if ($docID == null) {
            $docID = $DB->PUBLISHING_DOCUMENT_GET_NEXT_IN_QUEUE_ID();
            if ($docID == null) {
                echo "\nThere is no item in the Queue. Sleep for 30 seconds.";
                //fwrite($logfile,date(DATE_RFC2822) . ": " . "No item in the Queue.\r\n");
                sleep(30);
                //break;
					//re-run (testing)
					$DB = null;
					$DS = null;
					$Schema = null;
				    //prepare
					$DB = new DBHelper();
					$DS = new TDLPublishJob();
					$Schema = new TDLSchema();
            }
        }
		

		if($docID != null)		//begin if[1]
		{
        //add more case if there are new templates
        //get document info
        switch($collection["templateID"])
        {
            case 1: //map template
                $DB = new MapDBHelper();
                $doc = $DB->SP_TEMPLATE_MAP_DOCUMENT_SELECT($collectionName,$docID) + $DB->DOCUMENT_GEOREC_INFO_SELECT($docID);
                break;
            case 2: //jobfolder template
                $DB = new FolderDBHelper();
                $doc = $DB->SP_TEMPLATE_FOLDER_DOCUMENT_SELECT($collectionName,$docID);
                $doc['Authors'] = $DB->GET_FOLDER_AUTHORS_BY_DOCUMENT_ID($collectionName,$docID);
                break;
            case 3: //fieldbook template
                $DB = new FieldBookDBHelper();
                $doc = $DB->SP_TEMPLATE_FIELDBOOK_DOCUMENT_SELECT($collectionName,$docID);
                $doc['Crews'] = $DB->GET_FIELDBOOK_CREWS_BY_DOCUMENT_ID($collectionName,$docID);
                break;
            case 4: //indices template
                $DB = new IndicesDBHelper();
                $doc = $DB->SP_TEMPLATE_INDICES_DOCUMENT_SELECT($collectionName,$docID);
                break;
            default:
                $doc = null;
                break; //error
        }
        //get tdl (dspace) fields from document table
        $doc_dspace = $DB->PUBLISHING_DOCUMENT_GET_DSPACE_INFO($docID);
        $doc["TDLCollection"] = $collection["TDLname"];
        $doc += $collection;

        echo "\nPublishing item ID: " . $docID . "\n";
        fwrite($logfile,date(DATE_RFC2822) . ": " . "Publishing item ID:$docID" . "\r\n");
        if($debug) {
            echo "Document Info:\n\n";
            print_r($doc);

            echo "\n\nConverted Schema:\n";
            print_r($Schema->convertSchema($collection["templateID"],$doc));
        }
        echo "\n";

        //CASES:
        // IF status = 2: metadata preprocessing, publish data, prepare and publish bitstream front/back
        // IF status = 10: prepare bitstream and publish bitstreams front and back
        // IF status = 11: prepare bitstream and publish bitstream back
        switch($doc_dspace["dspacePublished"])
        {
            case 2:
                //publish from the start
                //METADATA PREPROCESSING
                //PUBLISH DATA
                $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,10); //set status to uploading
                $doc_dspace["dspacePublished"] = 10;
                $convertedSchema = $Schema->convertSchema($collection['templateID'],$doc); //convert from BandoCat to TDL schema using method in TDLSchema
                //save the converted schema in a temporary json file
                $jsonfilename = __DIR__ . '\\' . $docID . '.json';
                $fp = fopen($jsonfilename, 'w');
                fwrite($fp, $convertedSchema);
                fclose($fp);
                $header = $DS->TDL_POST_NEW_ITEM($TDLCollectionID,$jsonfilename,"application/json",null);
                if($debug)
                {
                    echo "Header:\n\n";
                    print_r($header);
                }
                $dspaceID = $Schema->getHeaderValueFromKey($header,"id"); //get item id
                $dspaceURI = $Schema->getHeaderValueFromKey($header,"handle"); //get handle uri
                unlink($jsonfilename); //delete json file
                $ret = $DB->PUBLISHING_DOCUMENT_TDL_UPDATE($docID,$dspaceID,$dspaceURI); //update new dspaceID and URI to the database
                if(!$ret)
                {
                    fwrite($logfile,date(DATE_RFC2822) . ": " . " Error updating to document table. Job Ended.\r\n");
                    fclose($logfile);
                    return;
                }
                else fwrite($logfile,date(DATE_RFC2822) . ": " . " Post new item success! DspaceID: $dspaceID | DspaceURI : $dspaceURI.\r\n");
                break;
            case 10:
            case 11:
            $dspaceID = $doc_dspace["dspaceID"];
            $dspaceURI = $doc_dspace["dspaceURI"];
            fwrite($logfile,date(DATE_RFC2822) . ": " . " Continue working on abandoned job. DspaceID: $dspaceID | DspaceURI : $dspaceURI.\r\n");
                break;
            default: fwrite($logfile,date(DATE_RFC2822) . ": " . " Unknown dspacePublished status code " . $doc_dspace["dspacePublished"] . ". Job Ended.\r\n");
                fclose($logfile);
                return;
        }
        if(!$dspaceID) //error checking
        {
            fwrite($logfile,date(DATE_RFC2822) . ": " . " Unable to post the item. Job End.\r\n");
            fclose($logfile);
            return;
        }
        fwrite($logfile,date(DATE_RFC2822) . ": Uploading bitstreams....\r\n");


        //Need to change publish bitstream schema
        // - Find the bitstream's names on Dspace
        // - Gather fullpath, filename, isBack, type (jpg/kmz/geotiff)
        // - compare: (if bitstreams name match then pass, if bitstreams name not match, then publish)
        $bitStreamError = false;
        $hasBack = true;
        $hasGeorectification = true;
        if(!isset($doc["FileNameBack"]) || $doc["FileNameBack"] == "" || $doc["FileNameBack"] == null)
            $hasBack = false;
        if(!isset($doc['geoRecFrontStatus']))
            $hasGeorectification = false;


        $internalBitstreams = array();

        //tif(s) (convert to jpg(s))
        //convert TIF to JPEG
        //POST jpeg bitstream
        //delete JPEG bitstream

        //front JPG scan
        $pathtoImage = $collection["storagedir"] . $doc["FileNamePath"];
        $jpgFilePath = str_replace(".tif",".jpg",$pathtoImage);
        $jpgFileName = str_replace(".tif",".jpg",$doc["FileName"]);
        exec("convert " . $pathtoImage . " " . $jpgFilePath );
        array_push($internalBitstreams,new bitstream($jpgFileName,$jpgFilePath,"frontJPG"));
        if($hasBack)
        {
            $pathtoImageBack = $collection["storagedir"] . $doc["FileNameBackPath"];
            //convert TIF to JPEG
            //POST jpeg bitstream
            //delete JPEG bitstream
            $jpgFilePathBack = str_replace(".tif",".jpg",$pathtoImageBack);
            $jpgFileNameBack = str_replace(".tif",".jpg",$doc["FileNameBack"]);
            exec("convert " . $pathtoImageBack . " " . $jpgFilePathBack );

            array_push($internalBitstreams,new bitstream($jpgFileNameBack,$jpgFilePathBack,"backJPG"));

        }

        if($hasGeorectification) {
            //kmz(s)and geotiff
            if ($doc['geoRecFrontStatus'] == 1) {
                array_push($internalBitstreams,new bitstream(explode('/', $doc['georecFrontDirKMZ'])[1],$doc['georecdir'] . $doc['georecFrontDirKMZ'], 'frontKMZ'));
                array_push($internalBitstreams,new bitstream(explode('/', $doc['georecFrontDirGeoTIFF'])[1], $doc['georecdir'] . $doc['georecFrontDirGeoTIFF'], 'frontGeoTIFF'));
            }
            if ($hasBack && $doc['geoRecBackStatus'] == 1) {
                array_push($internalBitstreams,new bitstream(explode('/', $doc['georecBackDirKMZ'])[1], $doc['georecdir'] . $doc['georecBackDirKMZ'], 'backKMZ'));
                array_push($internalBitstreams,new bitstream(explode('/', $doc['georecBackDirGeoTIFF'])[1], $doc['georecdir'] . $doc['georecBackDirGeoTIFF'], 'backGeoTIFF'));
            }
        }

        //get published bitstreams:
        $publishedBitstreams = $DS->TDL_GET_BITSTREAMS($dspaceID);

        //compare & publish
        foreach($internalBitstreams as $iB)
        {
            $published = false;
            foreach($publishedBitstreams as $pB)
            {
                if($pB->name == $iB->name) {
                    if($pB->checkSum != $iB->md5_checksum) //compare checksum
                    {
                        $http_retval = $DS->TDL_DELETE_BITSTREAM($pB->id);
                        if($http_retval == "200")
                            $published = false;
                        else fwrite($logfile, date(DATE_RFC2822) . ": " . $iB->type . " failed to delete bitstreams from TDL " . $pB->name .  ".... HTTP CODE:" . $http_retval . "\r\n");
                    }
                    else $published = true;
                    break;
                }
            }

            if(!$published){
                fwrite($logfile, date(DATE_RFC2822) . ": Publishing " . $iB->name . " - " . $iB->type . "\r\n");
                $http_retval = $DS->TDL_POST_BITSTREAM($dspaceID, $iB->path, $iB->name);
                if ($http_retval == "200")
                    fwrite($logfile, date(DATE_RFC2822) . ": " . $iB->type . " has been uploaded....\r\n");
                else {
                    fwrite($logfile, date(DATE_RFC2822) . ": " . $iB->type . " failed to upload.... HTTP CODE:" . $http_retval . "\r\n");
                    $bitStreamError = true;
                }
            }
        }

        //cleaning
        exec("del " . str_replace('/','\\',$jpgFilePath));
        if($hasBack)
        exec("del " . str_replace('/','\\',$jpgFilePathBack));

        $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,1); //set status to Published

        if($http_retval == "200" && !$bitStreamError) {
            fwrite($logfile, date(DATE_RFC2822) . ": Bitstream(s) uploaded....\r\n");
            fwrite($logfile, date(DATE_RFC2822) . ": Document ID: $docID has been published!\r\n");
            $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,1); //set status to Published

            //LOG
            $ret = $DB->SP_LOG_WRITE("publish",$collection["collectionID"],$docID,1,"success","System Log");
            if(!$ret)
                fwrite($logfile, date(DATE_RFC2822) . ": Unable to write log to DB!\r\n");
        }
        else
        {
            if($bitStreamError) //error when publishing one of the bitstream
                $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,11);
            else $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,-1); //unknown error
        } //set status to 11

        //$isEmptyQueue = true; //break the loop, for testing
		}//end if [1]
    }

    echo "\n...\n...\n...\nEnd Job...";
    fwrite($logfile,date(DATE_RFC2822) . ": " . "Job Ended.\r\n");
    fclose($logfile);
    return;
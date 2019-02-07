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

/**************************************
 * CLASSES
 *************************************/
class bitstream
{
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
	$errorLogFileName = "ErrorLog.txt";
	if(!file_exists($errorLogFileName))
		$errorLog = fopen($errorLogFileName, "w"); // creates error log file to log progess of each item (write)
	else $errorLog = fopen($errorLogFileName,"a"); //open error log file and append to it if file already exists
try
{
	//prepare
    $DB = new DBHelper();
	$FB = new FieldBookDBHelper();
    $DS = new TDLPublishJob();
    $Schema = new TDLSchema();
	$logfilename = "log.txt";
	$collectionFileName = "currentCollection.txt";
	
	//*********************************************************************************************************************
	//Current collection: Blucher Maps (bluchermaps), ID = 1,template Map (use MapDBHelper)
	  $collectionName = "bluchermaps";//$collectionFile;//"bluchermaps";//change the variable if publishing to new collection
	//*********************************************************************************************************************
	//create a log file if not exists
	
   
	/********************************************************************************************
	 * Create log files for the map collections
	 *******************************************************************************************/
	if(!file_exists($collectionFileName))
	{
		 $collectionFile = fopen($collectionFileName,"w"); //create log file if not exists (write)
		 fwrite($collectionFile, "bluchermaps"); // set default if the .txt file does not exist
	}   
    else
	{
		$collectionFile = file_get_contents($collectionFileName);
		//$collectionFile = fopen($collectionFileName,"r"); //open collection file and read it
		echo $collectionFile;
		$collectionName = $collectionFile;
		fwrite($errorLog,date(DATE_RFC2822) . ": " . $collectionFile . " is the selected collection.\r\n");
	}
    if(!file_exists($logfilename))
	{
        $logfile = fopen($logfilename,"w"); //create log file if not exists (write)
	}
    else
	{		
		$logfile = fopen($logfilename,"a"); //open log file and append to it if file already exists
	}
	 

    fwrite($logfile,date(DATE_RFC2822) . ": " . "Job Started.\r\n");
	fwrite($errorLog,date(DATE_RFC2822) . ": " . "Job Started.\r\n");
	
	fwrite($logfile,date(DATE_RFC2822) . ": Targeted collection: " . $collectionName . "\r\n");
	fwrite($errorLog,date(DATE_RFC2822) . ": Targeted collection: " . $collectionName . "\r\n");
	
    echo "TDL Publishing job\nLast updated: 01/30/2018\nBy Sam Allred\n";
    echo "TDL Publishing job is ready....\n\n...\n...\n";
	
    echo "Targeted Collection: " . $collectionName . "\n";
	
	/********************************************************************************************
	 *Get the collection then use that to get the Community ID and Collection ID
	 *******************************************************************************************/
    $collection = $DB->GET_COLLECTION_INFO($collectionName);
    $TDLCommunityID = $collection["TDLcommunityID"];
    $TDLCollectionID = $collection["TDLcollectionID"];
	fwrite($errorLog,date(DATE_RFC2822) . ": Targeted TDL Community: " . $TDLCommunityID. "\r\n");
	fwrite($errorLog,date(DATE_RFC2822) . ": Targeted TDL Collection: " . $TDLCollectionID. "\r\n");
	
	/********************************************************************************************
	 * Main Loop
	 *******************************************************************************************/
	
    while(true)
	{ //infinite loop
		//fwrite($errorLog,date(DATE_RFC2822) . ": Looped: " "\r\n");
        $docID = null;
        //Switch to the databse specified by this variable. bandocatdb
        $DB->SWITCH_DB($collectionName);
		//This call searches the server DB for dspacePublished = 10 or 11 which specifies that the front or back is uploading or publishing (This is an abandoned job)
        $docID = $DB->PUBLISHING_DOCUMENT_GET_PUBLISHING_ID(); //look for abandoned job
        
		//Check if there are any abandoned jobs
        if ($docID == null) 
		{
			//Grab an item in the server DB where dspacePublished = 2 (in queue)
            $docID = $DB->PUBLISHING_DOCUMENT_GET_NEXT_IN_QUEUE_ID($collectionName);
			//Check if there are any items in the queue
            if ($docID == null) 
			{
                echo "\nThere is no item in the Queue. Sleep for 30 seconds.";              
                sleep(30);
				//Reset everything
                $DB = null;
                $DS = null;
                $Schema = null;
                //prepare
                $DB = new DBHelper();
                $DS = new TDLPublishJob();
                $Schema = new TDLSchema();
            }
        }
		//This IF statement checks if there IS a job. 
        if($docID != null)		//begin if[1]
        {
            //add more case if there are new templates
            
            switch($collection["templateID"])
            {
                case 1: //map template
                    $DB = new MapDBHelper();
					//Grabs the document and then appends the GEOREC information from the same table after
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
			//create a new variable in the $doc called TDLCollection - store the TDLname variable from the bandocatDB here then append the collection data to the doc
			$doc["TDLCollection"] = $collection["TDLname"];
            $doc += $collection;
            //returns the status(dspacePublished), the dspaceURI, dspaceID of the specified document
            $doc_dspace = $DB->PUBLISHING_DOCUMENT_GET_DSPACE_INFO($docID);
			      

            echo "\nPublishing item ID: " . $docID . "\r\n";
			fwrite($errorLog,date(DATE_RFC2822) . ": " . "Publishing item ID:$docID" . "\r\n");
            fwrite($logfile,date(DATE_RFC2822) . ": " . "Publishing item ID:$docID" . "\r\n");
			
			//Debug is set to true in the top
            if($debug) 
			{
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
			fwrite($errorLog,date(DATE_RFC2822) . ": Job dspacePublishedID: " . $doc_dspace["dspacePublished"]. "\r\n");
			
			//This switch statement checks the status of the current document. This is the dspacePublished on the document
			//See the CASES above.
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
                    $header = $DS->TDL_POST_ITEM($TDLCollectionID,$jsonfilename,"application/json",null);
					fwrite($errorLog,date(DATE_RFC2822) . ": " . " Header: " . $header . "\r\n");				
					$debug = true;
                    if($debug)
                    {
                        echo "Header:\n\n";
                        print_r($header);
                    }
                    $dspaceID = $Schema->getHeaderValueFromKey($header,"id"); //get item id
                    $dspaceURI = $Schema->getHeaderValueFromKey($header,"handle"); //get handle uri
				
					fwrite($errorLog,date(DATE_RFC2822) . ": " . " DSPACEID: " . $dspaceID . "\r\n");	
					fwrite($errorLog,date(DATE_RFC2822) . ": " . " DSPACEHANDLE: " . $dspaceURI . "\r\n");	
                    //unlink($jsonfilename); //delete json file
                    $ret = $DB->PUBLISHING_DOCUMENT_TDL_UPDATE($docID,$dspaceID,$dspaceURI); //update new dspaceID and URI to the database
                    if(!$ret)
                    {
                        fwrite($logfile,date(DATE_RFC2822) . ": " . " Error updating to document table. Job Ended.\r\n");
                        fclose($logfile);
                        return;
                    }
                    else
					{
						//if fieldbook we need to update all items related to the book to the same URI
						if($collectionName == "blucherfieldbook")
						{
							$ret = $FB->UPDATE_FIELDBOOK_DOCUMENTS_TDL($collectionName,$doc["BookTitle"],$dspaceID,$dspaceURI, $doc_dspace["dspacePublished"]); //update new dspaceID and URI to the database							
						}
						fwrite($logfile,date(DATE_RFC2822) . ": " . " Post new item success! DspaceID: $dspaceID | DspaceURI : $dspaceURI.\r\n");
						fwrite($errorLog,date(DATE_RFC2822) . ": " . " Post new item success! DspaceID: $dspaceID | DspaceURI : $dspaceURI.\r\n");
					}
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
			if($collectionName == "blucherfieldbook")
			{
				$bitStreamError = false;
				if(!isset($doc["FileNamePath"]) || $doc["FileNamePath"] == "" || $doc["FileNamePath"] == null)
					$bitStreamError = true;
				else
				{
					//var_dump("TEST::::" . substr($doc["FileNamePath"],0,strrpos($doc["FileNamePath"], "/")) . "Book-" . $doc["BookTitle"] . ".pdf");
					
					if(!file_exists($doc['storagedir'] . substr($doc["FileNamePath"],0,strrpos($doc["FileNamePath"], "/")) . "/" . "Book-" . $doc["BookTitle"] . ".pdf"))
					{
						
						var_dump($doc['storagedir'] . substr($doc["FileNamePath"],0,strrpos($doc["FileNamePath"], "/")) . "/" . "Book-" . $doc["BookTitle"] . ".pdf");
						$bitStreamError = true;
						fwrite($errorLog,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["BookTitle"] . "  \r\nTYPE: PDF DOES NOT EXIST   \r\nLOCATION: INTERNAL  \r\n\r\n");					
						return;
					}
					else
					{
						//var_dump("FILE EXISTS");
						var_dump($doc['storagedir'] . substr($doc["FileNamePath"],0,strrpos($doc["FileNamePath"], "/")) . "/" . "Book-" . $doc["BookTitle"] . ".pdf");
						$internalBitstreams = array();
						$pathtoPDF = $doc['storagedir'] . substr($doc["FileNamePath"],0,strrpos($doc["FileNamePath"], "/")) . "/" . "Book-" . $doc["BookTitle"] . ".pdf";
						array_push($internalBitstreams, new bitstream("Book-" . $doc["BookTitle"] . ".pdf", $pathtoPDF,"BookPDF"));
						
						//get published bitstreams:
						$publishedBitstreams = $DS->TDL_GET_BITSTREAMS($dspaceID);
						
						
						//compare & publish
						try
						{
							foreach($internalBitstreams as $iB)
							{
								fwrite($errorLog,date(DATE_RFC2822) . ": IB:::" . $iB->name . " \r\n");
								$published = false;
								foreach($publishedBitstreams as $pB)
								{								
									if($pB->name == $iB->name) 
									{								
										if($pB->checkSum != $iB->md5_checksum) //compare checksum
										{										
											$http_retval = $DS->TDL_DELETE_BITSTREAM($pB->uuid,"application/json");//TDL Version 5 :: $pB->id  //TDL Version 6 :: $pB->id											
											if($http_retval == "200")
												$published = false;
											else fwrite($logfile, date(DATE_RFC2822) . ": " . $iB->type . " failed to delete bitstreams from TDL " . $pB->name .  ".... HTTP CODE:" . $http_retval["http_code"] . "\r\n");
										}
										else $published = true;
										break;
									}
								}								
								if(!$published)
								{												
									var_dump($iB->name);
									$http_retval = $DS->TDL_POST_BITSTREAM($dspaceID, $iB->path, $iB->name);									
									if ($http_retval["http_code"] == "200")
									{
										fwrite($logfile, date(DATE_RFC2822) . ": " . $iB->type . " has been uploaded....\r\n");
										$ret = $FB->UPDATE_FIELDBOOK_DOCUMENTS_TDL($collectionName,$doc["BookTitle"],$dspaceID,$dspaceURI, 1); //update new dspaceID and URI to the database	
									}
									else 
									{
										fwrite($logfile, date(DATE_RFC2822) . ": " . $iB->type . " failed to upload.... HTTP CODE:" . $http_retval["http_code"] . "\r\n");
										$bitStreamError = true;
									}
								}
							}

						}
						catch(Exception $ee)
						{
							fwrite($errorLog,date(DATE_RFC2822) . ": Something went wrong: " . $ee . "\r\n");
							fwrite($errorLog, $ee);
							fclose($errorLog);
						}
					}//if the path is set
				}//if the path is not set
			}//if collection is fieldbooks
			else
			{
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
				
				exec("convert -limit memory 32MiB " . $pathtoImage . " " . $jpgFilePath );
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
				print_r($publishedBitstreams);
				//fwrite($errorLog,date(DATE_RFC2822) . ":testttt " . print_r($publishedBitstreams,true) . " \r\n");
				fwrite($errorLog,date(DATE_RFC2822) . ":ITEM ID PASSED123: " . $dspaceID . " \r\n");
			
				
				//compare & publish
				try
				{
					 foreach($internalBitstreams as $iB)
					{
						fwrite($errorLog,date(DATE_RFC2822) . ": IB:::" . $iB->name . " \r\n");
						$published = false;
						foreach($publishedBitstreams as $pB)
						{
							
							if($pB->name == $iB->name) 
							{
								//fwrite($errorLog,date(DATE_RFC2822) . ": IB:::" . $iB->md5_checksum . " \r\n");
								//fwrite($errorLog,date(DATE_RFC2822) . ": pB:::" . $pB->checkSum . " \r\n");
								if($pB->checkSum != $iB->md5_checksum) //compare checksum
								{
								
									$http_retval = $DS->TDL_DELETE_BITSTREAM($pB->uuid,"application/json");//TDL Version 5 :: $pB->id  //TDL Version 6 :: $pB->id							
									if($http_retval == "200")
										$published = false;
									else fwrite($logfile, date(DATE_RFC2822) . ": " . $iB->type . " failed to delete bitstreams from TDL " . $pB->name .  ".... HTTP CODE:" . $http_retval["http_code"] . "\r\n");
								}
								else $published = true;
								break;
							}
						}				
						if(!$published)
						{

							fwrite($logfile, date(DATE_RFC2822) . ": Publishing " . $iB->name . " - " . $iB->type . "\r\n");								
							$http_retval = $DS->TDL_POST_BITSTREAM($dspaceID, $iB->path, $iB->name);
							
							if ($http_retval["http_code"] == "200")
								fwrite($logfile, date(DATE_RFC2822) . ": " . $iB->type . " has been uploaded....\r\n");
							else 
							{
								fwrite($logfile, date(DATE_RFC2822) . ": " . $iB->type . " failed to upload.... HTTP CODE:" . $http_retval["http_code"] . "\r\n");
								$bitStreamError = true;
							}
						}
					}

				}
				catch(Exception $ee)
				{
					fwrite($errorLog,date(DATE_RFC2822) . ": Something went wrong: " . $ee . "\r\n");
					fwrite($errorLog, $ee);
					fclose($errorLog);
				}
			  
				//cleaning
				exec("del " . str_replace('/','\\',$jpgFilePath));
				if($hasBack)
					exec("del " . str_replace('/','\\',$jpgFilePathBack));

				$DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,1); //set status to Published

				if($http_retval["http_code"] == "200" && !$bitStreamError) 
				{
					fwrite($logfile, date(DATE_RFC2822) . ": Bitstream(s) uploaded....\r\n");

					//UPDATE DC.PROVENANCE

					$doc['Provenance'] = generateProvenance($dspaceID);

					$convertedSchema = $Schema->convertSchema($collection['templateID'],$doc,false); //convert from BandoCat to TDL schema using method in TDLSchema
					//save the converted schema in a temporary json file
					$jsonfilename = __DIR__ . '\\' . $docID . '.json';
					$fp = fopen($jsonfilename, 'w');
					fwrite($fp, $convertedSchema);
					fclose($fp);
					$ret = $DS->TDL_CUSTOM_PUT("items/" . $dspaceID . "/metadata",$jsonfilename,"application/json",null);
					unlink($jsonfilename);
					//END update DC.Provenance
					
					fwrite($errorLog, date(DATE_RFC2822) . ": Document ID: $docID has been published!\r\n");
					
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

			}
            
            //$isEmptyQueue = true; //break the loop, for testing
        }//end if doc null
    }//end whileloop
	echo "\n...\n...\n...\nEnd Job...";
    fwrite($logfile,date(DATE_RFC2822) . ": " . "Job Ended.\r\n");
    fclose($logfile);
}catch(Exception $e)
{
    fwrite($errorLog,date(DATE_RFC2822) . ": JOB FAILED!"."\r\n");
    fwrite($errorLog, $e);
    fclose($errorLog);
}

/********************************************
 * FUNCTIONS
 *******************************************/
 
	//generate dc.provenance field (manually)
	function generateProvenance($dspaceID)
	{
		$TDL = new TDLPublishJob();
		$item = json_decode($TDL->TDL_CUSTOM_GET("items/" . $dspaceID . "/metadata"));
		$bitstreams = json_decode($TDL->TDL_CUSTOM_GET("items/" . $dspaceID . "/bitstreams"));
		$datetime = null;
		$noBitstreams = count($bitstreams);
		foreach($item as $elem)
		{
			if($elem->key == "dc.date.available") {
				$datetime = $elem->value;
				break;
			}
		}
		$provenance = "Made available in DSpace on " . $datetime . " No. of bitstreams: " . $noBitstreams;
		foreach($bitstreams as $bitstream) {
			$provenance .= " " . $bitstream->name . ": " . $bitstream->sizeBytes . " bytes, checksum: " . $bitstream->checkSum->value . " (" . $bitstream->checkSum->checkSumAlgorithm . ")";
		}
		return $provenance;
	}

    return;
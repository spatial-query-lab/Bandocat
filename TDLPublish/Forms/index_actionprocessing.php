<?php
//This performs server side action when user select an action on Action column in index.php
//This performs server side action when user select an action on Action column in index.php
spl_autoload_register(function ($class_name) {
    require_once "../../Library/" . $class_name . '.php';});
date_default_timezone_set("America/Chicago");
include '../../Library/SessionManager.php';
$session = new SessionManager();

$Schema = new TDLSchema();
$TDL = new TDLPublishJob();
$DB = new DBHelper();
$collectionName = $_POST['ddlCollection'];
$collection = $DB->GET_COLLECTION_INFO($collectionName);

$docID = $_POST['docID'];
var_dump("ORIGINAL DOC ID:: " . $docID);
//$return = $DB->GET_10_SPECIFIED_DOCUMENTS_FOR_AUTOMATION_TESTING($collectionName);
$BOOLFLAG = false;

/**************************************
 * CLASSES
 *************************************/
class InternalBitstream
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
if($_POST['action'] == "update")
{
	$LogFileName = "../UpdateCron/UpdateLog.txt";
	if(!file_exists($LogFileName))
		$Log = fopen($LogFileName, "w"); // creates log file to log progess of each item (write)
	else $Log = fopen($LogFileName,"a"); //open error log file and append to it if file already exists
		fwrite($Log,"\r\n\r\n");
		fwrite($Log,date(DATE_RFC2822) . ": " . "Update Job Started. \r\n");
		fwrite($Log,"\r\n");
}
//$TEST = $DB -> GET_ALL_PUBLISHED_DOCUMENTS_FOR_AUTOMATION_TESTING_50($collectionName);
//print_r($TEST);
//var_dump($TEST);


	//var_dump($document["documentID"]);
	//fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nNEXT DOCUMENT: " . $document["documentID"]."  \r\nTYPE: MOVING TO NEXT LINE   \r\nLOCATION:INTERNAL" ." \r\n\r\n");
	//$docID = $document["documentID"];
	//collect metadata of the document, given collectionName, templateID and documentID
	switch($collection["templateID"])
	{
		case 1: //map template
			$DB = new MapDBHelper();
			$doc = $DB->SP_TEMPLATE_MAP_DOCUMENT_SELECT($collectionName,$docID) + $DB->DOCUMENT_GEOREC_INFO_SELECT($docID);
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

	$collectionNameFile = "../Cron/currentCollection.txt";
				if(!file_exists($collectionNameFile))
					$collectionName = fopen($collectionNameFile, "w"); // creates error log file to log progess of each item (write)
				else $collectionName = fopen($collectionNameFile,"w"); //open error log file and append to it if file already exists
				
				fwrite($collectionName,$_POST['ddlCollection']);
				fclose($collectionName);
	//Specify different actions to perform in this SWITCH/CASE statement
	switch($_POST['action'])
	{
		case "push":
			//PUSH
			if($collectionName == "blucherfieldbook")
			{
				var_dump($doc);
				$ret = $DB->PUBLISHING_DOCUMENT_UPDATE_ALL_FB_STATUS($doc["BookTitle"],2); //push to queue;
				
				break;
			}
			else
			{
				$ret = $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,2); //push to queue;
				break;
			}
			
		case "pop":
			//POP
			if($collectionName == "blucherfieldbook")
			{
				$ret = $DB->PUBLISHING_DOCUMENT_UPDATE_ALL_FB_STATUS($doc["BookTitle"],0); //push to queue;		
			}
			else
			{
				$ret = $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,0);
			}
			
			break;
		case "unpublish":
			//START UNPUBLISH
			
			$BOOLFLAG = true;
			//FIND ALL BITSTREAMS, DELETE ALL BITSTREAMS BELONG TO THIS ITEM
			$bitstreams = json_decode($TDL->TDL_GET_ITEM_BITSTREAMS($dspaceID),true);	
			$jsonfilename = __DIR__ . '\\' . $docID . '.json';
			$fp = fopen($jsonfilename, 'w');
			
			fwrite($fp, $bitstreams);
			
		   
			fclose($fp);
			//print_r($bitstreams);
			 foreach($bitstreams as $b)
			{
				
				$ret = $TDL->TDL_DELETE_BITSTREAM($b["uuid"],"application/json"); //delete bitstreams //TDL Version 5 :: $b["id"]  //TDL Version 6 :: $b["uuid"]
				if($ret != "200") 
				{
					echo "Deleting bitstreams, return value: " . $ret . "\n";
					return;
				}
			}
			//DELETE ITEM
			$ret = $TDL->TDL_DELETE_ITEM($dspaceID, "application/json"); //delete item (metadata)
			//print_r("RET" . $ret);
			//reset status flag
			//write log to DB
			$ret = $DB->PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,0);
			//reset
			if($ret) $ret = $DB->PUBLISHING_DOCUMENT_TDL_UPDATE($docID,0,null); 
			break;
			//end UNPUBLISH
		case "update":
			//START UPDATE
			if($collection["templateID"] == 1) // Maps Update
			{
					
						
						fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nNEXT DOCUMENT: " . $docID ."  \r\nTYPE: MOVING TO NEXT LINE   " ." \r\n\r\n");
						
						
						
						$DB_LOG_BOOLFLAG = true;
						$hasBack = true;
						$hasFrontGeorectification = true;
						$hasBackGeorectification = true;
						
						$convertedSchema = $Schema->convertSchema($collection['templateID'],$doc,false); //convert from BandoCat to TDL schema using method in TDLSchema
						$publicBitstreams = $TDL->TDL_GET_ITEM_BITSTREAMS($dspaceID);
						$internalBitstreams = array();
						
						/**********************************************
						* FileName / FileNamePath Error Checking
						***********************************************/
						if(!isset($doc["FileName"]) || $doc["FileName"] == "" || $doc["FileName"] == null)
						{
							//ERROR: FileName is not set
							$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
							CREATE_TICKET($collection["collectionID"], $doc["LibraryIndex"] . " :: FILENAME DOES NOT EXIST! :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
							fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["LibraryIndex"] ."  \r\nTYPE: FILENAME DOES NOT EXIST!   \r\nLOCATION:INTERNAL" ." \r\n\r\n");
							//Skip item
							continue;
						}
						if(!file_exists($doc['storagedir'].$doc["FileNamePath"]) || $doc["FileNamePath"] == NULL)
						{
							//ERROR: The FRONT(JPG) PATH does not exist. (Log + Skip item)	
							$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
							CREATE_TICKET($collection["collectionID"], $doc["LibraryIndex"] . " :: PATH DOES NOT EXIST! :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
							fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["LibraryIndex"] ."  \r\nTYPE: PATH DOES NOT EXIST!   \r\nLOCATION:INTERNAL" ." \r\n\r\n");
							continue;
						}
						else
						{
							//FOUND
							if(preg_match('/back/',$doc["FileName"]))
							{
								//ERROR: The FRONT(JPG) PATH exists but it contains "back" in the name. (Log + Skip item)	
								$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
								CREATE_TICKET($collection["collectionID"], $doc["FileName"] . " :: FRONT CONTAINS (BACK) :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
								fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["FileName"] ."  \r\nTYPE: FRONT CONTAINS (BACK)   \r\nLOCATION:INTERNAL" ." \r\n\r\n");
								continue;
							}
						}
								
						/**********************************************
						* FileNameBack / FileNameBackPath Error Checking
						***********************************************/		
						if(!isset($doc["FileNameBack"]) || $doc["FileNameBack"] == "" || $doc["FileNameBack"] == null)
						{
							//SET: The BACK(JPG) NAME does not exist. (SET FLAG TO FALSE)	
							$hasBack = false;
						}
						else
						{
							//SUCCESS
							if(!file_exists($doc['storagedir'].$doc["FileNameBackPath"]) || $doc["FileNameBackPath"] == NULL )
							{
								//ERROR: The BACK(JPG) PATH does not exist. (Log item but don't skip so we can remove the 0 bytes uploads on TDL later in the code)	
								$hasBack = false;
								$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
								CREATE_TICKET($collection["collectionID"], $doc["LibraryIndex"] . " :: PATH DOES NOT EXIST! :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
								fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["LibraryIndex"] ."  \r\nTYPE: PATH DOES NOT EXIST!   \r\nLOCATION:INTERNAL" ." \r\n\r\n");			
							}
							else
							{
								//FOUND
								if(!preg_match('/back/',$doc["FileNameBack"]))
								{
									//ERROR: The BACK(JPG) PATH exists but it does not contain "back" in the name. (Log + Skip item)	
									$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
									CREATE_TICKET($collection["collectionID"], $doc["FileNameBack"] . " :: BACK DOES NOT CONTAIN (BACK) :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
									fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["FileNameBack"] ."  \r\nTYPE: BACK DOES NOT CONTAIN (BACK)   \r\nLOCATION:INTERNAL" ." \r\n\r\n");
									continue;
								}
							}
									
						}
						
						/**********************************************
						* GeoRectification / FRONT/ Error Checking
						***********************************************/			
						if(!isset($doc['geoRecFrontStatus']) || $doc["geoRecFrontStatus"] == 0 || $doc["geoRecFrontStatus"] == 2)
						{
							$hasFrontGeorectification = false;
						}
						else
						{
							//SUCCESS
							if(!file_exists($doc['georecdir']. $doc["georecFrontDirKMZ"]) || !file_exists($doc['georecdir'].$doc["georecFrontDirGeoTIFF"]) || $doc["georecFrontDirKMZ"] == NULL || $doc["georecFrontDirGeoTIFF"] == NULL)
							{
								//ERROR: The FRONT(KMZ/TIFF) PATH does not exist. (Log item but don't skip so we can remove the 0 bytes uploads on TDL later in the code)	
								$hasFrontGeorectification = false;
								$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
								CREATE_TICKET($collection["collectionID"], $doc["LibraryIndex"] . " :: PATH DOES NOT EXIST! :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
								fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["LibraryIndex"] ."  \r\nTYPE: PATH DOES NOT EXIST!   \r\nLOCATION:INTERNAL" ." \r\n\r\n");			
							}
							else
							{
								//FOUND
								if(preg_match('/back/',$doc["georecFrontDirKMZ"]) || preg_match('/back/',$doc["georecFrontDirGeoTIFF"]))
								{						
									//ERROR: The FRONT(KMZ/TIFF) PATH exists but it does not contain "back" in the name. (Log + Skip item)	
									$hasFrontGeorectification = false;
									$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
									CREATE_TICKET($collection["collectionID"], $doc["georecFrontDirKMZ"] . " :: FRONT CONTAINS (BACK) :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
									fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["georecFrontDirKMZ"] ."  \r\nTYPE: FRONT CONTAINS (BACK)   \r\nLOCATION:INTERNAL" ." \r\n\r\n");
									continue;
								}
							}
									
									
						}
						/**********************************************
						* GeoRectification / BACK / Error Checking
						***********************************************/	
						if(!isset($doc['geoRecBackStatus']) || $doc["geoRecBackStatus"] == 0 || $doc["geoRecBackStatus"] == 2)
						{
							$hasBackGeorectification = false;
						}
						else
						{
							//SUCCESS
							if(!file_exists($doc['georecdir'].$doc["georecBackDirKMZ"]) || !file_exists($doc['georecdir'].$doc["georecBackDirGeoTIFF"])|| $doc["georecBackDirKMZ"] == NULL || $doc["georecBackDirGeoTIFF"] == NULL)
							{
								//ERROR: The BACK(KMZ/TIFF) PATH does not exist. (Log item but don't skip so we can remove the 0 bytes uploads on TDL later in the code)	
								$hasBackGeorectification = false;
								$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
								CREATE_TICKET($collection["collectionID"], $doc["LibraryIndex"] . " :: PATH DOES NOT EXIST! :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
								fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["LibraryIndex"] ."  \r\nTYPE: PATH DOES NOT EXIST!   \r\nLOCATION:INTERNAL" ." \r\n\r\n");
							}
							else
							{
								//FOUND
								if(!preg_match('/back/',$doc["georecBackDirKMZ"]) || !preg_match('/back/',$doc["georecBackDirGeoTIFF"]))
								{
									//ERROR: The BACK(KMZ/TIFF) PATH exists but it does not contain "back" in the name. (Log + Skip item)	
									$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
									CREATE_TICKET($collection["collectionID"], $doc["georecBackDirKMZ"] . " :: BACK DOES NOT CONTAIN (BACK) :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
									fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $doc["georecBackDirKMZ"] ."  \r\nTYPE: BACK DOES NOT CONTAIN (BACK)   \r\nLOCATION:INTERNAL" ." \r\n\r\n");
									continue;
								}
							}
									
								}
								
								/***************Create InternalBitstream to loop through*****************************/		

								
								 //Generate JPG of Front image scan to add to internal bitstream
								 $pathtoImage = $collection["storagedir"] . $doc["FileNamePath"];
								 $jpgFilePath = str_replace(".tif",".jpg",$pathtoImage);
								 $jpgFileName = str_replace(".tif",".jpg",$doc["FileName"]);
								 exec("convert -limit memory 32MiB " . $pathtoImage . " " . $jpgFilePath );
								 array_push($internalBitstreams,new InternalBitstream($jpgFileName,$jpgFilePath,"frontJPG"));
								 //Generate JPG of Back image scan to add to internal bitstream
								 if($hasBack == true)
								 {
										$pathtobackImage = $collection["storagedir"] . $doc["FileNameBackPath"];
										$jpgFileBackPath = str_replace(".tif",".jpg",$pathtobackImage);
										$jpgFileNameBack = str_replace(".tif",".jpg",$doc["FileNameBack"]);
										exec("convert -limit memory 32MiB " . $pathtobackImage . " " . $jpgFileBackPath );
										array_push($internalBitstreams,new InternalBitstream($jpgFileNameBack,$jpgFileBackPath,"backJPG"));
								 }
								 //TIFF and KMZ generation is not possible to be automated, but we can check if it exists and alert user
								 var_dump($hasFrontGeorectification);
								 var_dump("HAS FRONT??? -----^");
								 if($hasFrontGeorectification == true)
								 {
										//This should return everything after the last slash in a string
										//substr($doc['georecFrontDirGeoTIFF'], strrpos($doc['georecFrontDirGeoTIFF'], '/') + 1)		
									   array_push($internalBitstreams,new InternalBitstream(substr($doc['georecFrontDirGeoTIFF'], strrpos($doc['georecFrontDirGeoTIFF'], '/') + 1), $doc['georecdir'] . $doc['georecFrontDirGeoTIFF'], 'frontGeoTIFF'));
									   array_push($internalBitstreams,new InternalBitstream(substr($doc['georecFrontDirKMZ'], strrpos($doc['georecFrontDirKMZ'], '/') + 1),$doc['georecdir'] . $doc['georecFrontDirKMZ'], 'frontKMZ'));           
								 }
								 if($hasBackGeorectification == true)
								 {
									  array_push($internalBitstreams,new InternalBitstream(substr($doc['georecBackDirGeoTIFF'], strrpos($doc['georecBackDirGeoTIFF'], '/') + 1), $doc['georecdir'] . $doc['georecBackDirGeoTIFF'], 'backGeoTIFF'));
									  array_push($internalBitstreams,new InternalBitstream(substr($doc['georecBackDirKMZ'], strrpos($doc['georecBackDirKMZ'], '/') + 1),$doc['georecdir'] . $doc['georecBackDirKMZ'], 'backKMZ'));           
								 }
								
							  //  print_r($internalBitstreams);
								//print_r($publicBitstreams);
								
								//We need to count the public ORIGINAL bitstreams and compare them to the count of internal bitstreams.
								//This will tell us if TDL has MORE files than we have.
								$publicCount = 0;
								
								foreach($publicBitstreams as $publicbit)
								{
									$pbstream = json_decode(json_encode($publicbit), true);
									if($pbstream["bundleName"] == "ORIGINAL")
									{
										$publicCount = $publicCount + 1;
									}
								}
								var_dump("PUBLIC BITSTREAM COUNT = " . $publicCount );
								var_dump("INTERNAL BITSTREAM COUNT = " . count($internalBitstreams));
								//Flags to determine if we are missing any files from TDL
								$tdlHasFrontJPG = false;
								$tdlHasBackJPG = false;
								$tdlHasFrontTIFF = false;
								$tdlHasBackTIFF = false;
								$tdlHasFrontKMZ = false;
								$tdlHasBackKMZ = false;
								
								//This condition checks if TDL has more items than we do.
								if($publicCount <= count($internalBitstreams))
								{
									var_dump("THE PUBLIC IS LESS THAN OR = TOO THE INTERNAL");
									foreach($internalBitstreams as $internalbit)
									{
										foreach($publicBitstreams as $publicbit)
										{
											$pbstream = json_decode(json_encode($publicbit), true);
											//Grab the name of the bitstream and check its extension to verify what bitstream we have
											$ext = substr($pbstream["name"], -4);
											switch($ext)
											{
												case ".jpg":
															if($pbstream["bundleName"] == "ORIGINAL")
															{
																if($publicbit->name == $internalbit->name) 
																{
																	if(preg_match('/back/',$pbstream["name"]))
																	{
																		$tdlHasBackJPG = true;
																	}
																	else
																	{
																		$tdlHasFrontJPG = true;
																	}
																	//MATCH FOUND: TDL has the bitstream we are checking (BandoCat = TDL)
																	//Check TDL properties for discrepancies
																	//TDL returns checksum as a stdClass Object (need to use checkSum->value
																	if($pbstream["sizeBytes"] == 0 || $publicbit->checkSum->value != $internalbit->md5_checksum )
																	{
																		for($i = 0; $i < 3; $i++)
																		{
																			 $ret = $TDL->TDL_PUT_BITSTREAM($dspaceID,$pbstream['uuid'], $internalbit->path,"application/json",null);
																			//The bitstreams filesize is 0 OR the MD5 checksum does not match	
																			 if($ret["http_code"] == "200")
																			 {
																				
																				if($ret["upload_content_length"] == "0")
																				{
																					
																				}
																				else
																				{
																					fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nUPDATED: " . $pbstream["name"] ."  \r\nTYPE: Size/Checksum" . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
																					break;
																				}
																			 }
																			 if($i == 2)
																			 {
																				$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
																				CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: SIZE/CHECKSUM/PUT FAILURE :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
																				fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: UPLOAD FAILED   \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
																			 }
																		}
																		
																		 
																		 
																	}
																	else
																	{
																		var_dump("NOTHINGS WRONG WITH JPG: " . $pbstream["name"]);
																	}
																	
																	
																}
																
															}
												break;
												case ".tif":
															if($pbstream["bundleName"] == "ORIGINAL")
															{
																// var_dump($publicbit->name . "PUBLIC");
																// var_dump($internalbit->name . "INTERNAL");
																if($publicbit->name == $internalbit->name)
																{
																	if(preg_match('/back/',$pbstream["name"]))
																	{
																		$tdlHasBackTIFF = true;
																	}
																	else
																	{
																		$tdlHasFrontTIFF = true;
									
																	}
																	
																	//MATCH FOUND: TDL has the bitstream we are checking (BandoCat = TDL)
																	//Check TDL properties for discrepancies
																	//TDL returns checksum as a stdClass Object (need to use checkSum->value
																	if($pbstream["sizeBytes"] == 0 || $publicbit->checkSum->value != $internalbit->md5_checksum )
																	{
																		//Size = 0 || checksum does not match
																		for($i = 0; $i < 3; $i++)
																		{
																			 $ret = $TDL->TDL_PUT_BITSTREAM($dspaceID,$pbstream['uuid'], $internalbit->path,"application/json",null);
																			
																			 if($ret["http_code"] == "200")
																			 {
																				
																				if($ret["upload_content_length"] == "0")//UPLOAD FAILED
																				{
																					
																					$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
																					CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: UPLOAD FAILURE: Content Length :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
																					fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name . "  \r\nTYPE: UPLOAD FAILED .   \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
																				}
																				else //SUCCESS
																				{																			
																					fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nUPDATED: " . $pbstream["name"] ."  \r\nTYPE: Size/Checksum" . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
																					break;
																				}
																			 }
																			 if($i == 2)  //TRIED 3 TIMES
																			 {														
																				$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);			
																				CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: SIZE/CHECKSUM/PUT FAILURE :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
																				fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name . "  \r\nTYPE: UPLOAD FAILED .   \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
																			 }
																		}			
																		 
																	}
																	else
																	{
																		var_dump("NOTHINGS WRONG WITH TIFF: " . $pbstream["name"]);
																	}
																}
															}
												break;
												case ".kmz":
															if($pbstream["bundleName"] == "ORIGINAL")
															{
																if($publicbit->name == $internalbit->name)
																{
																	if(preg_match('/back/',$pbstream["name"]))
																	{
																		$tdlHasBackKMZ = true;
																	}
																	else
																	{
																		$tdlHasFrontKMZ = true;
																	}
																	
																	//MATCH FOUND: TDL has the bitstream we are checking (BandoCat = TDL)
																	//Check TDL properties for discrepancies
																	//TDL returns checksum as a stdClass Object (need to use checkSum->value
																	if($pbstream["sizeBytes"] == 0 || $publicbit->checkSum->value != $internalbit->md5_checksum )
																	{
																		for($i = 0; $i < 3; $i++)
																		{
																			 $ret = $TDL->TDL_PUT_BITSTREAM($dspaceID,$pbstream['uuid'], $internalbit->path,"application/json",null);
																			//The bitstreams filesize is 0 OR the MD5 checksum does not match	
																			 if($ret["http_code"] == "200")
																			 {
																				
																				if($ret["upload_content_length"] == "0")
																				{
																					
																				}
																				else
																				{
																					fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nUPDATED: " . $pbstream["name"] ."  \r\nTYPE: Size/Checksum" . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
																					break;
																				}
																			 }
																			 if($i == 2)
																			 {
																				$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);											   		
																				CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: SIZE/CHECKSUM/PUT FAILURE :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
																				fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: UPLOAD FAILED .   \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
																			 }
																		}			
																		 
																	}
																	else
																	{
																		var_dump("NOTHINGS WRONG WITH KMZ: " . $pbstream["name"]);
																	}
																}
															}
															
															
											}
											
										}
									}
								
								}
								if($publicCount > count($internalBitstreams))
								{
									var_dump("DELETE ALL BITSTREAMS");
									fwrite($Log,date(DATE_RFC2822) . ": " . "INVALID COUNT FOUND!" . "\r\n");
									foreach($publicBitstreams as $publicbit)
									{
										$http_retval = $TDL->TDL_DELETE_BITSTREAM($publicbit->uuid,"application/json");//TDL Version 5 :: $pB->id  //TDL Version 6 :: $pB->id
										if($http_retval == "200")
										{
											var_dump("DELETED :: " . $publicbit->uuid);
											
											fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nDELETED: " . $publicbit->name ."  \r\nTYPE: TOO MANY ITEMS " . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
										}
										else
										{
											$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);					
											CREATE_TICKET($collection["collectionID"], $publicbit->uuid . " :: TDL COUNT DISCREPANCY, BITSTREAM DELETE FAILED :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
											var_dump("FAILED :: " . $http_retval);
										}
										
									}
								}
								$FRONTJPG = false;
								$BACKJPG = false;
								$FRONTTIFF = false;
								$BACKTIFF = false;
								$FRONTKMZ = false;
								$BACKKMZ = false;
								var_dump($internalBitstreams);
								for($i = 0; $i < 3; $i++)
								{
									foreach($internalBitstreams as $internalbit)
									{
										
										if($internalbit->type == "frontJPG" && $tdlHasFrontJPG == false && $FRONTJPG == false)//Verify bit-stream && does it exist? && are we done?
										{						
											$http_retval = $TDL->TDL_POST_BITSTREAM($dspaceID, $internalbit->path, $internalbit->name);	//Post bit-stream
											if($http_retval["http_code"] == "200")
											{
												$FRONTJPG = true; //Bit-stream posted successfully
												if($http_retval["upload_content_length"] == "0") // check upload data stream
												{
													$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);
													CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: SIZE FAILURE :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: SIZE:" . $http_retval["upload_content_length"] . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
												else // success
												{
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nPOSTED: " . $internalbit->name ."  \r\nTYPE: NEW ADD" . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
											}
											if($i == 2 && $FRONTJPG == false) // Tried 3 times and failed
											{
												$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);				
												CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: POST FAILURE :: " . $http_retval["http_code"], "Auto Update: FAILED", json_encode($LIBARRAY));
												fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: UPLOAD FAILED .   \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
											}
									
										}
										if($internalbit->type == "backJPG" && $tdlHasBackJPG == false && $BACKJPG == false)
										{
											$http_retval = $TDL->TDL_POST_BITSTREAM($dspaceID, $internalbit->path, $internalbit->name);	
											if($http_retval["http_code"] == "200")
											{
												$BACKJPG = true;
												if($http_retval["upload_content_length"] == "0")
												{
													$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);
													CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: SIZE FAILURE :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: SIZE:" . $http_retval["upload_content_length"] . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
												else
												{
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nPOSTED: " . $internalbit->name ."  \r\nTYPE: NEW ADD" . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
											}
											if($i == 2 && $BACKJPG == false)
											{
												$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);				
												CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: POST FAILURE :: " . $http_retval["http_code"], "Auto Update: FAILED", json_encode($LIBARRAY));
												fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: UPLOAD FAILED .   \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
											}
										}
										if($internalbit->type == "frontGeoTIFF" && $tdlHasFrontTIFF == false && $FRONTTIFF == false)
										{
											$http_retval = $TDL->TDL_POST_BITSTREAM($dspaceID, $internalbit->path, $internalbit->name);	
											if($http_retval["http_code"] == "200")
											{
												$FRONTTIFF = true;
												if($http_retval["upload_content_length"] == "0")
												{
													$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);
													CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: SIZE FAILURE :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: SIZE:" . $http_retval["upload_content_length"] . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
												else
												{
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nPOSTED: " . $internalbit->name ."  \r\nTYPE: NEW ADD" ."  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
											}
											if($i == 2 && $FRONTTIFF == false)
											{
												$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);				
												CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: POST FAILURE :: " . $http_retval["http_code"], "Auto Update: FAILED", json_encode($LIBARRAY));
												fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: UPLOAD FAILED .   \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
											}
										}
										if($internalbit->type == "backGeoTIFF" && $tdlHasBackTIFF == false && $BACKTIFF == false)
										{
											$http_retval = $TDL->TDL_POST_BITSTREAM($dspaceID, $internalbit->path, $internalbit->name);	
											if($http_retval["http_code"] == "200")
											{
												$BACKTIFF = true;
												if($http_retval["upload_content_length"] == "0")
												{
													$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);
													CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: SIZE FAILURE :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: SIZE:" . $http_retval["upload_content_length"] . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
												else
												{
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nPOSTED: " . $internalbit->name ."  \r\nTYPE: NEW ADDs" . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
											}
											if($i == 2 && $BACKTIFF == false)
											{
												$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);				
												CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: POST FAILURE :: " . $http_retval["http_code"], "Auto Update: FAILED", json_encode($LIBARRAY));
												fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: UPLOAD FAILED .   \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
											}
										}
										if($internalbit->type == "frontKMZ" && $tdlHasFrontKMZ == false && $FRONTKMZ == false)
										{
											
											$http_retval = $TDL->TDL_POST_BITSTREAM($dspaceID, $internalbit->path, $internalbit->name);	
											if($http_retval["http_code"] == "200")
											{
												$FRONTKMZ = true;
												if($http_retval["upload_content_length"] == "0")
												{
													$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);
													CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: SIZE FAILURE :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: SIZE:" . $http_retval["upload_content_length"] . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
												else
												{
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nPOSTED: " . $internalbit->name ."  \r\nTYPE: NEW ADD" . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
											}
											if($i == 2 && $FRONTKMZ == false)
											{
												$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);				
												CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: POST FAILURE :: " . $http_retval["http_code"], "Auto Update: FAILED", json_encode($LIBARRAY));
												fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: UPLOAD FAILED .   \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
											}
										}
										if($internalbit->type == "backKMZ" && $tdlHasBackKMZ == false && $BACKKMZ == false)
										{
											$http_retval = $TDL->TDL_POST_BITSTREAM($dspaceID, $internalbit->path, $internalbit->name);	
											if($http_retval["http_code"] == "200")
											{
												$BACKKMZ = true;
												if($http_retval["upload_content_length"] == "0")
												{
													$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);
													CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: SIZE FAILURE :: ", "Auto Update: FAILED", json_encode($LIBARRAY));
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: SIZE:" . $http_retval["upload_content_length"] . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
												else
												{
													fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nPOSTED: " . $internalbit->name ."  \r\nTYPE: NEW ADD" . "  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
												}
											}
											if($i == 2 && $BACKKMZ == false)
											{
												$LIBARRAY = array("libraryIndex"=>$doc["LibraryIndex"]);				
												CREATE_TICKET($collection["collectionID"], $internalbit->path . " :: POST FAILURE :: " . $http_retval["http_code"], "Auto Update: FAILED", json_encode($LIBARRAY));
												fwrite($Log,date(DATE_RFC2822) . ": " . "\r\nFAILED: " . $internalbit->name ."  \r\nTYPE: UPLOAD FAILED .  \r\nLOCATION: https://tamucc-ir.tdl.org/handle/" . $dspaceDocInfo['dspaceURI']."  \r\n\r\n");
											}
										}
									}
								}
							 
								 //Check if TDL is missing anyfiles.
								 //print_r($doc);
								
								$jsonfilename = __DIR__ . '\\' . $docID . '.json';
								$fp = fopen($jsonfilename, 'w');
								fwrite($fp, $convertedSchema);
								fclose($fp);
								//$ret = $TDL->TDL_CUSTOM_PUT("items/" . $dspaceID . "/metadata",$jsonfilename,"application/json",null);
								$ret = $TDL->TDL_PUT_ITEM_METADATA($dspaceID , $jsonfilename);
								unlink($jsonfilename);
								//END UPDATE
					
				
				
				if($DB_LOG_BOOLFLAG)
					$ret = $DB->SP_LOG_WRITE("update" . " (tdl)",$collection["collectionID"],$docID,"Mr. Gato","success","");
					
				if($ret)
					echo "Success!";
				else echo "Error!";
				
					
				//return;

				fwrite($Log,"\r\n");
				fwrite($Log,date(DATE_RFC2822) . ": " . "Update Job Complete. \r\n");
				fwrite($Log,"\r\n");
				
			}		 
			break;
		default:
			echo "Error! Unclassified action!";
			return;
	}

	
	function objectToArray( $data ) 
	{
    if ( is_object( $data ) ) 
        $d = get_object_vars( $data );
	}
	function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0)
		{
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}
	/* /************************************************************
	 * Sam Master Work
	 *
	 ***********************************************************/
	/* function callBitstreams($action,$docID)
    {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "index_bitstreams.php",
            data: {ddlCollection: $("#ddlCollection").val(),docID: docID, action: action},
            success: function (data) {
                $('#dtable').DataTable().draw();
            }
        });
    } */ 
	function CREATE_TICKET($COLLECTION, $DESCRIPTION, $SUBJECT, $LIBINDEX)
	{
		$DB = new DBHelper();
		$result = $DB->SP_TICKET_INSERT($SUBJECT, "Mr. Gato",$COLLECTION,$DESCRIPTION, $LIBINDEX);
		print_r($result);
	}
?>
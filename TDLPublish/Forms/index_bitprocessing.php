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
$TDLJob = new TDLPublishJob();
$collectionName = $_POST['ddlCollection'];
$bitID = $_POST['bitID'];
$collection = $DB->GET_COLLECTION_INFO($collectionName);

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


//Specify different actions to perform in this SWITCH/CASE statement
switch($_POST['action'])
{
    case "delete":
        //PUSH
		//var_dump($bitID);
         $ret = $TDLJob->TDL_DELETE_BITSTREAM($bitID); //delete bitstream
        break;
    case "update":
        //START UPDATE
        $convertedSchema = $Schema->convertSchema($collection['templateID'],$doc,false); //convert from BandoCat to TDL schema using method in TDLSchema
		$bitstreams = $TDL->TDL_GET_ITEM_BITSTREAMS($dspaceID);
		
		 foreach($bitstreams as $b)
        { 
			//var_dump($b);
			$bstream = json_decode(json_encode($b), true);
			
			$ext = substr($bstream["name"], -4);
			
				switch($ext)
				{
					//this case coveres the bitstream where the name ends in ".tif.jpg" i.e : 1-_31_rectified.tif.jpg // this is also known as the thumbnail (check bundleName in the rest Meta data)
					//so we need to check to see if the thumbnail or if this is an image
					case ".jpg":
						if(substr($bstream["name"],-8) == ".tif.jpg")
						{
							//this is the thumbnail
							if($bstream["sizeBytes"] == 0)
							{
								
							}
							
						}
						else
						{
							//this is an original .jpg
							if($bstream["sizeBytes"] == 0)
							{
								//this means the .jpg had an error and doesn't exist on TDL (0 size file)
								//we need to identify the bitstream in our database, and regenerate the .jpg
								
								//!!!! Our FileNames hold a .tif image. We need to strip off the .jpg from this and append a .jpg
								$retdoc = $DB->GET_DOCUMENT_BY_FILENAME($collectionName, substr($bstream["name"],0, -4) . ".tif");
								var_dump($retdoc);
								//var_dump(gettype($retdoc));
								//verify this is the same item
								//var_dump($dspaceID . "I AM HER EBRO");
								//$retdoc is returned as a 2D array. It is technically possible to get more than 1 return, this is why we verify in the next step
								if($retdoc[0]["dspaceID"] == $dspaceID)
								{
									//Verified!
									//var_dump("We have verified the document");
									// var_dump($collection = $DB->GET_COLLECTION_INFO($collectionName));
									//Need to regenerate the .jpg file
									//var_dump($retdoc);
									 $pathtoImage = $collection["storagedir"] . $retdoc[0]["filenamepath"];
									 $jpgFilePath = str_replace(".tif",".jpg",$pathtoImage);
									 $jpgFileName = str_replace(".tif",".jpg",$retdoc[0]["filename"]);
									 
									 var_dump($jpgFilePath);
									 var_dump($jpgFileName);
									 exec("convert -limit memory 64MiB " . $pathtoImage . " " . $jpgFilePath );
									 $internalBitstreams = array();
									 
									 array_push($internalBitstreams,new bitstream($jpgFileName,$jpgFilePath,"frontJPG"));
									 $DS = new TDLPublishJob();
									 
									 $ret = $DS->TDL_PUT_BITSTREAM($dspaceID,$bstream['uuid'],$jpgFilePath,"application/json",null);
									// var_dump($ret . "RETURN VALUE");
									 
								}
								else
								{
									//UNVERIFIED - problem
								}
								
							}
						}
						break;
					//this case covers the recitifed original TIF image
					case ".tif":
						   //this is a .tif (recitifed)
						break;
						
					case ".kmz":
						   //this is  a .kmz
						break;
				}
				
			
		}
           /*  $ret = $TDL->TDL_CUSTOM_DELETE("bitstreams/" . $b["id"]); //delete bitstreams
            if($ret != "200") {
                echo "Deleting bitstreams, return value: " . $ret . "\n";
                return;
            } */
        //}
        //save the converted schema in a temporary json file
        $jsonfilename = __DIR__ . '\\' . $docID . '.json';
        $fp = fopen($jsonfilename, 'w');
        fwrite($fp, $convertedSchema);
        fclose($fp);
        $ret = $TDL->TDL_CUSTOM_PUT("items/" . $dspaceID . "/metadata",$jsonfilename,"application/json",null);
		
        //unlink($jsonfilename);
        //END UPDATE
        break;
    default:
        echo "Error! Unclassified action!";
        return;
}

/* //WRITE A LOG
    if($ret)
        $ret = $DB->SP_LOG_WRITE($_POST['action'] . " (tdl)",$collection["collectionID"],$docID,$session->getUserID(),"success","");
    if($ret)
        echo "Success!";
    else echo "Error!";
    return; */
	
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
	
	
?>
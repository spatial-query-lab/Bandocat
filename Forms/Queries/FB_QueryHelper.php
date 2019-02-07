<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();

if(isset($_GET['col']) && isset($_GET['column']))
{
    require('../../Library/DBHelper.php');
    require('../../Library/FieldBookDBHelper.php');
    //Get passed variables, use htmlspecialchars to verify col
    $collection = htmlspecialchars($_GET['col']);
    $column = $_GET['column'];
    $booktitle = $_GET['booktitle'];
    $stage = $_GET['stage'];

    $errorFlag = false;
    $errorType = null;
    //create new instance of DBHelper
    $DB = new DBHelper();
    $FB = new FieldBookDBHelper();
    //Selects appropriate database, and counts the number of columns to return an accurate count.
    $count = $DB->GET_DOCUMENT_COUNT($collection);
    //Switch statement for "action" parameter to determine which document we are counting
}

else header('Location: ../../');
    $config = $DB->SP_GET_COLLECTION_CONFIG($_GET['col']);
    $ret = $FB->GET_ALL_FIELDBOOK_FILENAMES_BY_BOOKTITLE($_GET['col'], $booktitle);
    $folder = null;
	//ret returns a nested array, so [0][0] is just the first item.
	$folder = explode("-", $ret[0][0])[0];
	
	
    //COMBINE THE PATH AND CHECK IF VALID
    $path = $config['StorageDir'] . $folder;
    $pathToStorage = $config['StorageDir'];

    if(!is_dir($path))
    {
        $errorFlag = true;
        $errorType = "1";
    }
function contains($needle, $haystack)
{
	if(strpos($haystack, $needle) !== false)
	{
		return true;
	}
	else
	{
		return false;
	}
}
//GET Partial document and field crew
$orderedListOfBookTitleByJobNumber = $FB-> GET_FIELDBOOK_CREWS_AND_PARTIAL_DOCUMENT_BY_BOOKTITLE($_GET['col'], $booktitle);
// $orderedListOfBookTitleByJobNumber;
$distintJobNumbers = $FB->GET_ALL_DISTINT_JOBNUMBER($_GET['col'], $booktitle);
//var_dump($orderedListOfBookTitleByJobNumber);
$jobnumberarray =[];
//libraryindex, jobnumber,startdate,enddate,indexedpage,filenamepath
$nojobarray = [];
$fp = fopen('C:\xampp\htdocs\BandoCat\ExtLibrary\jsreport\results.json', 'w');
$arr = array('listObj' => $orderedListOfBookTitleByJobNumber);
$arr2_correctKeys = array('listObj' => $orderedListOfBookTitleByJobNumber);


//var_dump($arr);
//The outer loop below iterates through $arr on the outer level (this is a nested loop) each index at this level
//contains an array for each book.
foreach($arr as &$value)
{
    //this innerarray is the nested array inside each index of the above outer array. Each iteration of $value contains
    // an array with 4-5 values. The value we are after is [6] which contains the library index
    foreach($value as &$innerarray)
    {
        //this prints out the library index of each book
        //we need to append the path to this array to each location so we can reference it later
        //the following line changes the [6] value from the libraryindex to the path/libraryindex
        $innerarray[6] = $pathToStorage . '/' . $innerarray[6];
        //remove .tif at the end of the string and add .jpg
        $innerarray[6] = substr($innerarray[6],0,-4). '.jpg';
		
		//check the date $innerarray[2-3]
		if(contains("0000",$innerarray[3]))
		{
			
			$innerarray[3] = "";
		}
		//check the date $innerarray[2-3]
		if(contains("0000",$innerarray[4]))
		{
			
			$innerarray[4] = "";
		}



    }

}






//the values in $arr are not sorted in NATURAL order ( 001, 002, 003) they are stored in ascending order (001, 011, 012)
// the following foreach segment reorients the array into natural order
foreach ($arr as $key => $value)
{
    uasort($value, "cmp");

}

fwrite($fp, json_encode($arr));
fclose($fp);

//Convert TIFs to JPGS
$files = scandir($path);
//If the PDF does not already exist
if(!file_exists($path . "/Book-" . $booktitle . ".pdf"))
{	
	//loop through each file in the storage directory
	foreach($files as $file)
	{
		//Ignore directories, (depreciated) title_page, and already converted JPEGS
		if ($file != '.' && $file != '..' && $file != "Title_Page.pdf" && $file != IMAGETYPE_JPEG)
		{
			//Again verify the file we are about to convert is a scanned original .TIF
			$ext = pathinfo($path . '/' . basename($file),PATHINFO_EXTENSION);
			if($ext == "tif")
			{
				//verify the .tif file does not already have a scanned JPEG
				if(!file_exists($path . '/' . basename($file,".tif") . ".jpg"))
				{
					//Use ImageMagick to convert and compress the scanned original TIF to a JPEG
					$exec1 = "convert " . $path . '/' . basename($file) . " -quiet -compress jpeg -quality 40 " . $path . '/' . basename($file, '.tif') . ".jpg";
					exec($exec1, $yaks, $return);
				}

			}


		}
	}

	$templateContent = '"C:\xampp\htdocs\BandoCat\ExtLibrary\jsreport\report.html"';
	$templateData = '"C:\xampp\htdocs\BandoCat\ExtLibrary\jsreport\results.json"';
	$templateOutput = $path . '"\Book-"' . $booktitle . '".pdf"';


	$execReport = "C:\\Users\\sallred\\AppData\\Roaming\\npm\\jsreport.cmd render --template.content=" . $templateContent . " --template.engine=handlebars --template.recipe=phantom-pdf --data=" . $templateData . " --out=" . $templateOutput;


	exec("cd " . '"C:\xampp\htdocs\BandoCat\ExtLibrary\jsreport"' . " && " . $execReport ." 2>&1", $yaks2, $return2);

	$fpp = fopen('lolwhat.txt', 'w');


	fwrite($fpp, $execReport);

	fclose($fpp);
	// Return will return non-zero upon an error
	if (!$return2)
	{
		//delete the .jpg files if we made the pdf
		//var_dump("PDF Created Successfully");
		$files = glob($path . '/' . "*.jpg");
		foreach($files as $file)
		{
			//var_dump("Here2");
			unlink($file);
		}
		
		$stage = (string)$stage;
		$boop = $FB->UPDATE_FIELDBOOK_READYFORPDF($collection,$booktitle, 2);
		
		var_dump($boop);
		var_dump("Updated");
	}
	else
	{
		var_dump("PDF not created");
	}
	
}
else
{
	//THE PDF ALREADY EXISTS, SO SKIP
}

//foreach($jobnumberarray as $key=>$value)
//{
//    //for each unique job number
//
//    foreach($orderedListOfBookTitleByJobNumber as $innerArray)
//    {
//       //var_dump($innerArray);
//        //Innerarray 0 contains the library index
//        //Innerarray 1 contains the job number
//        //Innerarray 2 contains the start date
//        //Innerarray 3 contains the end date
//        //innerarray 4 contains the indexed page
//        //innerarray 5 contains the filepathname
//
//        // var_dump(jason_encode($innerArray));
//       // $files = scandir($path);
////        //conver the filename returned by the array to jpg.
////         $exec1 = "convert " . $path . '/' . basename($file) . " -quiet -compress jpeg -quality 40 " . $path . '/' . basename($file, '.tif');
////        exec($exec1, $yaks, $return);
//
//        //jsreport render
//       // --template.name=MyTemplate
//      //  --data=mydata.json
//       // --out=myreport.pdf
//
//        if($innerArray["1"] == $value)
//        {
//
//            //Which ever value we are at: IE "" or "J-B212"
//            //We will check if the innerArray matches. if it does
//            //We can match the library indexes together.
//
////            //SCAN DIRECTORY AND CONVERT TIF FILES TO PDF WITH 40% COMPRESSION
//            $files = scandir($path);
//            foreach($files as $file)
//            {
//
//                if ($file != '.' && $file != '..' && $file != "Title_Page.pdf")
//                {
////                    $exec1 = "convert " . $path . '/' . basename($file) . " -quiet -compress jpeg -quality 40 " . $path . '/' . basename($file, '.jpg');
////                    exec($exec1, $yaks, $return);
//                }
//            }
//        }
////        $arr = array('listObj' => $orderedListOfBookTitleByJobNumber);
////        var_dump($arr);
//        // if($innerArray["1"])
//        if($innerArray["1"] == "")
//        {
//            //if the fieldbook_filenames have no jobnumber
//            array_push($nojobarray,$innerArray["0"]);
//        }
//        else
//        {
//
//        }
//        //we will use the library index to find the files
//        //we will use the job number to order
//    }
//
//}

function cmp($a, $b)
{
    return strnatcmp($a[0], $b[0]);
}
// A function that you can pass a percentage as
// a whole number and it will generate the
// appropriate new div's to overlay the
// current ones:

function custom_combine($numeric_array, $keyed_array)
{
    $temp = array();
    $i=0;
    foreach($keyed_array as $key=>$val)
    {
        if(isset($numeric_array[$i]))
            $temp[$key] = $numeric_array[$i];
        else
            $temp[$key] ='';
        $i++;
    }
    return($temp);
}



    //IF THE DIRECTORY WAS FOUND
//    if($errorFlag != true) {
//        //SCAN DIRECTORY AND CONVERT TIF FILES TO PDF WITH 40% COMPRESSION
//        $files = scandir($path);
//        foreach ($files as $file) {
//
//            if ($file != '.' && $file != '..' && $file != "Title_Page.pdf") {
//                $exec1 = "convert " . $path . '/' . basename($file) . " -quiet -compress jpeg -quality 40 " . $path . '/' . basename($file, '.tif') . ".jpg";
//                exec($exec1, $yaks, $return);
//            }
//        }
//        //If the compression/conversion failed
//        if ($return == 1) {
//            $errorFlag = true;
//            $errorType = "2";
//        }
//    }
//        else
//        {
//            //SCAN DIRECTORY FOR NEWLY CREATED PDF FILES
//            $files = scandir($path);
//            $exec2 = "pdftk ";
//            $titlepage = "C:/xampp/htdocs/BandoCat/Images/Title_Page.pdf ";
//            $exec2 .= $titlepage;
//            //BUILD STRING TO COMBINE ALL PDFS INTO ONE
//           // $exec2 .= $path . '/' . "Title_Page.pdf" . " ";
//
//
//            foreach($files as $file)
//            {
//                // var_dump($files);.
//                if($file != '.' && $file != '..')
//                {
//                    $file_info = pathinfo($file);
//                    if($file_info['extension'] == "pdf")
//                    {
//                        $exec2 .= $path . '/' . basename($file) . " ";
//
//
//                    }
//                }
//            }
//            $exec2 .= " cat output " . $path . '/' . "Book_" . $booktitle . ".pdf";
//
//            exec($exec2,$yaks2,$return1);
//            //IF THE COMBINING OF PDFS IS SUCCESSFUL DELETE CREATED PDFS
//            if($return1 == 0)
//            {
//
//                foreach($files as $file)
//                {
//                    // var_dump($files);
//                    if($file != '.' && $file != '..')
//                    {
//                        $file_info = pathinfo($file);
//                        if($file_info['extension'] == "pdf")
//                        {
//                            unlink($path . '/' . $file);
//
//
//                        }
//                    }
//                }
//                echo 'Success!';
//                $booktitle = (string)$booktitle;
//                $stage = (string)$stage;
//                $boop = $FB->UPDATE_FIELDBOOK_READYFORPDF($collection,$booktitle,$stage);
//            }else{
//                echo 'Failed! ';
//                echo $exec2;
//            }
//
//        }
//    }
//
//
//
//    if($errorFlag == true)
//    {
//        echo "Error: Type " . $errorType;
//       // echo $path;
//    }
?>





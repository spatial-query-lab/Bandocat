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
$rets = array();
//get collection name
$config = $DB->SP_GET_COLLECTION_CONFIG($_GET['col']);
//Create Thumbnails dir if it doesn't exist
if(!is_dir("../../Thumbnails"))
    exec(mkdir("../../Thumbnails", 0777));
foreach($_FILES as $f)
{
    if(strpos($f['name'],"back") === false)
    {   //if the map is front
        //Check to see if uploaded file already exists in the DB using filename and db name;
        $ret = $DB->TEMPLATE_FOLDER_CHECK_EXIST_RECORD_BY_FILENAME($_GET['col'], $f['name']);
        //if doc not existed
        if ($ret == 0)
        {
            //sample upload 0164-188 explode grabs 0164
            $folder = explode("-", $f['name'])[0];
            //set filename
            $filename = $f['name'];
            //concat filepath
            $filenamepath = $folder . "/" . $filename;
            //change thumbnail from tif to jpg
            $thumbnail = str_replace(".tif", ".jpg", $filename);
            //create fullpath
            $fullpath = $config['StorageDir'] . $folder . "/" . $filename;
            //check if the directory exists
            if (!is_dir($config['StorageDir'] . $folder))
                mkdir($config['StorageDir'] . $folder, 0777);
            //check if the file already exists
            if (file_exists($fullpath)) {
                chmod($fullpath, 0755); //Change the file permissions if allowed
                unlink($fullpath); //remove the file
            }
            //This function checks to ensure that the file
            // designated by filename is a valid upload file
            // (meaning that it was uploaded via PHP's HTTP POST upload mechanism).
            // If the file is valid, it will be moved to the filename given by destination.
            move_uploaded_file($f["tmp_name"], $fullpath);
            //Thumbnail directory
            $thumbFilenamepath = "../../" . $config['ThumbnailDir'];
            //check if thumbnail dir exists
            if (!is_dir($thumbFilenamepath))
                mkdir($thumbFilenamepath, 0777);
            //remove thumbnail if it exists
            if (file_exists($thumbFilenamepath . $thumbnail))
            {
                chmod($thumbFilenamepath . $thumbnail, 0755); //Change the file permissions if allowed
                unlink($thumbFilenamepath . $thumbnail); //remove the file
            }
            //executes a php function to convert the image supplied to the supplied jpg
            //also trims the jpg for thumbnail viewing
            $exec1 = "convert " . $fullpath . " -deskew 40% -fuzz 50% -trim -resize 200 ./" . $thumbFilenamepath . $thumbnail;
            exec($exec1, $yaks1);

            $filename = $f['name'];

            //check if there is a back
            $filenamenoext = str_replace(".tif", "", $f['name']);
            $filenameback = "";
            $filenamebackpath = "";
            foreach ($_FILES as $f2) { //convert thumbnail and move to storage for Back file
                if (strpos($f2['name'], $filenamenoext) !== false && strpos($f2['name'], "back") !== false) {
                    $filenameback = $f2['name'];
                    $filenamebackpath = $folder . "/" . $filenameback;
                    $thumbnailback =  str_replace(".tif",".jpg",$filenameback);
                    if (file_exists($thumbFilenamepath . $thumbnailback)) {
                        chmod($thumbFilenamepath . $thumbnailback, 0755); //Change the file permissions if allowed
                        unlink($thumbFilenamepath . $thumbnailback); //remove the file
                    }
                    $fullpathback = $config['StorageDir'] . $folder . "/" . $filenameback;
                    move_uploaded_file($f2["tmp_name"], $fullpathback);
                    $exec2 = "convert " . $fullpathback . " -deskew 40% -fuzz 50% -trim -resize 200 ./" . $thumbFilenamepath . $thumbnailback;
                    exec($exec2, $yaks1);
                }
            }

            $ret = $DB->SP_TEMPLATE_FOLDER_DOCUMENT_INSERT($_GET['col'], $filenamenoext, $filenamenoext, $filename, $filenameback, $filenamepath, $filenamebackpath);
            //error checking for image
            if ($ret != false)
                array_push($rets, array($f['name'], "Uploaded"));
            else array_push($rets, array($f['name'], "Error"));
        } else array_push($rets, array($f['name'], "Ignored"));
    }else { //back map
        array_push($rets, array($f['name'], "See Front"));
    }
}

echo json_encode($rets);


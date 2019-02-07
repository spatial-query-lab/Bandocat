<?php
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
$DB = new DBHelper();
require('../../../Library/ControlsRender.php');
$Render = new ControlsRender();
$collections = $DB->GET_COLLECTION_TABLE();
$total_storage = 0;
$units = explode(' ', 'B KB MB GB TB PB');


$DB->TRUNCATE();
$DB->TRUNCATE_TOTAL_STATS();




foreach($collections as $col)
{


    $temp = foldersize($col['storagedir']);
    $total_storage += $temp;

    date_default_timezone_set("America/Chicago");

    $DB->INSERT_INTO_STORAGE_COLLECTIONS($col['displayname'].':' , format_size($temp),date("Y-m-d h:i:sa"));


}

$DB->INSERT_INTO_STORAGE_All_COLLECTIONS("All Collections: ", format_size($total_storage), date("Y-m-d h:i:sa"));
$DB->INSERT_INTO_STORAGE_DISKSPACE("Disk Free Space: ", format_size(disk_free_space($collections[0]['storagedir'])), date("Y-m-d h:i:sa"));

$DB->DISPLAY_STORAGE_INTO_TABLE();
$DB->DISPLAY_STATS();


//Disk space management


function foldersize($path)
    {
        //hits here 10 times // twice for each collection
        $total_size = 0;
        $files = scandir($path);
        $cleanPath = rtrim($path, '/') . '/';

        foreach ($files as $t) {
            if ($t <> "." && $t <> "..")
            {
                $currentFile = $cleanPath . $t;
                if (is_dir($currentFile)) {
                    $size = foldersize($currentFile);
                    $total_size += $size;
                } else {
                    $size = filesize($currentFile);
                    $total_size += $size;
                }
            }
        }


        return $total_size;
    }




    function format_size($size)
    {


        global $units;
        $mod = 1024;

        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }

        $endIndex = strpos($size, ".") + 3;


        return substr($size, 0, $endIndex) . ' ' . $units[$i];
    }



?>


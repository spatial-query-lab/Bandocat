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

foreach($collections as $col)
{
    $temp = foldersize($col['storagedir']);
    $total_storage += $temp;
  //  echo "<div class='storagecap'>$col[displayname]: " . format_size($temp) . "</div>";
}
/* echo "<div class='storagecap'><b>All Collections:" . format_size($total_storage) . "</b></div>";
echo "<div class='storagecap'>Disk Free Space: " . format_size(disk_free_space($collections[0]['storagedir'])) . "</div>"; */
//Disk space management
function foldersize($path)
{
    //hits here 10 times // twice for each collection
   /*  $total_size = 0;
    $files = scandir($path);
    $cleanPath = rtrim($path, '/'). '/';

    foreach($files as $t)
    {
        if ($t<>"." && $t<>"..")
        {
            $currentFile = $cleanPath . $t;
            if (is_dir($currentFile))
            {
                $size = foldersize($currentFile);
                $total_size += $size;
            }
            else {
                $size = filesize($currentFile);
                $total_size += $size;
            }
        }
    }

    return $total_size; */
}


function format_size($size) {
    global $units;

    $mod = 1024;

    for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
    }

    $endIndex = strpos($size, ".")+3;

    return substr( $size, 0, $endIndex) . ' ' . $units[$i];
}



?>
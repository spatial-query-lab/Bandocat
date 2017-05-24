<?php
require '../../Library/DBHelper.php';
require '../../Library/AnnouncementDBHelper.php';
$DB = new DBHelper();
$announcement = new AnnouncementDBHelper();
$announcementData = $announcement->GET_ANNOUNCEMENT_DATA();
$announcementLenght = count($announcementData);
$announcementJSON = json_encode($announcementData);
echo $announcementJSON;
?>
<?php
require '../../Library/DBHelper.php';
require '../../Library/AnnouncementDBHelper.php';
$announceHelper = new AnnouncementDBHelper();
$data = $_POST;
$action = $data['action'];

if($action == 1 || $action == 2)
{
    $title = htmlspecialchars($data['title'], ENT_QUOTES);
    $message = htmlspecialchars($data['message'], ENT_QUOTES);
    $endDate = $data['date'];
    $expDate = date("Y-m-d H:i:s", strtotime($endDate));
    $user = $data['userID'];
    $announcementID = $data['announcementID'];

    if ($action == 1)
        $announcement = $announceHelper->SP_ANNOUNCEMENT_INSERT($title, $message, $expDate, $user);
    if ($action == 2)
    {
        $announcementEdit = $announceHelper->SP_ANNOUNCEMENT_UPDATE($title, $message, $expDate, $announcementID);
        return htmlspecialchars_decode($announcementEdit);
    }
}

if ($action == 3)
{
    $announcement = $announceHelper->GET_ANNOUNCEMENT_DATA();
    echo json_encode($announcement);
}
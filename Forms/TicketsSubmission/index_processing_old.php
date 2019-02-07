<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require '../../Library/DBHelper.php';
$DB = new DBHelper();

$collection_id = $_POST['dbname'];
$desc = $_POST['description'];
$subject = $_POST['subject'];

$result = $DB->SP_TICKET_INSERT($subject,$session->getUserID(),$collection_id,$desc);
echo json_encode($result);

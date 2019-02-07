<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require '../../Library/DBHelper.php';
$DB = new DBHelper();

$collection_id = $_POST['dbname'];
$desc = $_POST['description'];
$subject = $_POST['subject'];
$libIndex = $_POST['libIndex'];

$result = $DB->SP_TICKET_INSERT($subject,$session->getUserID(),$collection_id,$desc, $libIndex);
echo json_encode($result);

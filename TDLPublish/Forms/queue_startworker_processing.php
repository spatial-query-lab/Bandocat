<?php
//this page is used for testing only
include '../../Library/SessionManager.php';
$session = new SessionManager();
$cmd = "cd ../Cron & CRON";
exec($cmd,$output,$ret);

echo json_encode($output);
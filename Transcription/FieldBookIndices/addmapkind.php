<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if($session->isAdmin() == false) {
    header('Location: ../../');
}
require '../../Library/IndicesDBHelper.php';
//for mapindices
$collection='mapindices';

$us =$_POST['txt'];
$IDB= new IndicesDBHelper();
$IDB->INSERT_INDICES_MAPKIND($us, $collection);


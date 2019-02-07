<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if($session->isAdmin() == false) {
    header('Location: ../../');
}
require '../../Library/IndicesDBHelper.php';
$us =$_REQUEST['txt'];
$collection='mapindices';
$IDB= new IndicesDBHelper();
$ola = $IDB->GET_INDICES_MAPKIND($collection);
$split = json_encode($ola);
$UpperArray = strtoupper($split);
$UpperUs = strtoupper($us);

var_dump($ola);

/*
foreach($ola as $arr){
    if (strpos($UpperUs, $arr) !== FALSE) {
        echo "Match found";
        return true;
    }
}
echo "Not found!";
return false;
*/
/*
if (in_array($UpperUs, $ola)) {
    echo 'Exists';
}
else {
    echo 'Doesnt Exist';
}
*/
/*
else{
    $IDB->INSERT_INDICES_MAPKIND($us, $collection);;
    echo 1;
}
*/
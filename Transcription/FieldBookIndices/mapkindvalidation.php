<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if($session->isAdmin() == false) {
    header('Location: ../../');
}
require '../../Library/IndicesDBHelper.php';
/**for mapindices **/
$collection='mapindices';
$us =$_REQUEST['txt'];
$IDB= new IndicesDBHelper();
$ola = $IDB->GET_MAPKIND_TABLE($collection);
$UpperUs = strtoupper($us);
/**Make a simpler Array to use in_array **/
foreach ($ola as $item) {
    $array[] = strtoupper($item['mapkindname']);
}
/**If the onkeyup value exists in the array return 1, if not 0 **/
if (in_array($UpperUs, $array))
{
    echo 1;
}
else
{
    echo 0;
}



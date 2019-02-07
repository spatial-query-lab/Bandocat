<?php
/* PHP INCLUDES */
include '../../Library/SessionManager.php';
$session = new SessionManager();
require('../../Library/DBHelper.php');
$DB = new DBHelper();
require('../../Library/ControlsRender.php');
$Render = new ControlsRender();
/*
$sth = $DB->getConn()->exec("USE bandocat_fieldbookinventory");
$sth = $DB->getConn()->prepare("SELECT DISTINCT `booktitle` FROM `document`");
$ret = $sth->execute();
$fbs = $sth->fetchAll(PDO::FETCH_NUM);
$fp = fopen("fieldbook.csv","w");
$str = "";
foreach($fbs as $fb)
{
	$q = $DB->getConn()->prepare("SELECT COUNT(*) FROM `document` WHERE `booktitle` = :booktitle ");
	$q->bindParam(":booktitle",$fb[0],PDO::PARAM_INT);
	$q->execute();
	$count = $q->fetchColumn();
	if($count > 10)
		$str = $str . $fb[0] . "," . $count . "\n"; 
}

fwrite($fp,$str);
fclose($fp); */

/*
$fp = fopen("FBNo.json","w");
$sth = $DB->getConn()->prepare("SELECT DISTINCT map.`documentID` AS MapID, map.`fieldbooknumber` AS MapFieldBook, fb.`booktitle` AS FieldBook FROM `bandocat_bluchermapsinventory`.`document` AS map INNER JOIN `bandocat_fieldbookinventory`.`document` AS fb ON (map.`fieldbooknumber` = CAST(fb.`booktitle` AS UNSIGNED)) WHERE map.`fieldbooknumber` > 0");
$ret = $sth->execute();
print_r($sth->errorInfo());
$out = $sth->fetchAll(PDO::FETCH_ASSOC);
$str = json_encode($out);
fwrite($fp,$str);
fclose($fp);
*/


$fp = fopen("JobNo.json","w");
$sth = $DB->getConn()->prepare("SELECT DISTINCT map.`documentID` AS MapID, map.`fieldbooknumber` AS MapFieldBook, fb.`booktitle` AS FieldBook FROM `bandocat_bluchermapsinventory`.`document` AS map INNER JOIN `bandocat_fieldbookinventory`.`document` AS fb ON (map.`fieldbooknumber` = CAST(fb.`booktitle` AS UNSIGNED)) WHERE map.`fieldbooknumber` > 0");
$ret = $sth->execute();
print_r($sth->errorInfo());
$out = $sth->fetchAll(PDO::FETCH_ASSOC);
$str = json_encode($out);
fwrite($fp,$str);
fclose($fp);
?>

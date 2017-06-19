<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require('../../Library/DBHelper.php');
$DB = new DBHelper();

$config = $DB->SP_GET_COLLECTION_CONFIG($_GET['col']);

// SQL server connection information
$sql_details = array(
    'user' => $DB->getUser(),
    'pass' => $DB->getPwd(),
    'db'   => 'bandocatdb',
    'host' => $DB->getHost()
);

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'log';

// Table's primary key
$primaryKey = 'logID';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => '`log`.`logID`', 'dt' => 0, 'field' => 'logID' ),
    array( 'db' => '`log`.`timestamp`', 'dt' => 1,'field' => 'timestamp'),
    array( 'db' => '`log`.`action`', 'dt' => 2,'field' => 'action'),
    array( 'db' => '`document`.`libraryindex`', 'dt' => 3, 'field' => 'libraryindex'),
    array( 'db' => '`user`.`username`', 'dt' => 4,'field' => 'username' ),
    array( 'db' => '`log`.`comments`',  'dt' => 5, 'field' => 'comments' )
   // array( 'db' => '`author`.`authorname`', 'dt' => 6, 'field' => 'authorname'),

);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('../../Library/sspwithjoin.class.php');
if($config['TemplateID'] == '2')
{
    array_push($columns,array( 'db' => '`log`.`comments`', 'dt' => 6, 'field' => 'comments'));
    $joinQuery = " FROM `log` LEFT JOIN `user` ON (`user`.`userID` = `log`.`userID`) LEFT JOIN `collection` ON (`collection`.`collectionID` = `log`.`collectionID`) 
 LEFT JOIN `$config[DbName]`.`document` ON (`log`.`docID` = `document`.`documentID`) ";
}
else
{
   array_push($columns,array( 'db' => '`author`.`authorname`', 'dt' => 6, 'field' => 'authorname'));
    $joinQuery = " FROM `log` LEFT JOIN `user` ON (`user`.`userID` = `log`.`userID`) LEFT JOIN `collection` ON (`collection`.`collectionID` = `log`.`collectionID`) 
 LEFT JOIN `$config[DbName]`.`document` ON (`log`.`docID` = `document`.`documentID`) LEFT JOIN `$config[DbName]`.`author` ON (`author`.`authorID` = `document`.`authorID`) ";
}

$extraWhere = " `log`.`status` = 'success' AND `log`.`collectionID` = '$config[CollectionID]'";
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);
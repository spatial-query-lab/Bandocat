<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
/*Ensures the col variable is not null then assigns to the $collection variable */
if(isset($_GET['col']))
    $collection = htmlspecialchars($_GET['col']);
else header('Location: ../../');
require('../../Library/DBHelper.php');
$DB = new DBHelper();

// SQL server connection information
$sql_details = array(
    'user' => $DB->getUser(),
    'pass' => $DB->getPwd(),
    'db'   => $DB->SP_GET_COLLECTION_CONFIG($collection)['DbName'],
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
//HMM
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'document';

// Table's primary key
$primaryKey = 'documentID';


if($collection == "blucherfieldbook")
{
	  $columns = array(
        array( 'db' => '`document`.`documentID`', 'dt' => 0, 'field' => 'documentID' ),
        array( 'db' => '`document`.`booktitle`', 'dt' => 1,'field' => 'booktitle' ),
		array( 'db' => '`document`.`dspacePublished`', 'dt' => 2,'field' => 'dspacePublished'),
		array( 'db' => '`document`.`dspaceURI`', 'dt' => 3,'field' => 'dspaceURI'),
		array( 'db' => '`document`.`dspaceID`', 'dt' => 4,'field' => 'dspaceID')
	);

	$joinQuery = " FROM `document` ";
	//extra where parameter to search with
    $groupby = "`booktitle` HAVING COUNT(*) >= 1";
    $extraWhere = null;
    $extraWhere = " `document`.`RdyForPdf` = 2";	
	
	require('../../Library/sspwithjoin.class.php');
	//document is the name of the db
	echo json_encode(
        SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupby)
    );
}
else
{
	// Array of database columns which should be read and sent back to DataTables.
	// The `db` parameter represents the column name in the database, while the `dt`
	// parameter represents the DataTables column identifier. In this case simple
	// indexes
	//DB is the bandocat database that holds the documents
	$columns = array(
    array( 'db' => '`document`.`documentID`', 'dt' => 0, 'field' => 'documentID' ),
    array( 'db' => '`document`.`libraryindex`', 'dt' => 1,'field' => 'libraryindex'),
    array( 'db' => '`document`.`dspacePublished`', 'dt' => 2,'field' => 'dspacePublished'),
    array( 'db' => '`document`.`dspaceURI`', 'dt' => 3,'field' => 'dspaceURI'),
    array( 'db' => '`document`.`dspaceID`', 'dt' => 4,'field' => 'dspaceID')
	);
	$joinQuery = "FROM `document`";
	$extraWhere = "`needsreview` = 0";
	
	require('../../Library/sspwithjoin.class.php');
	//document is the name of the db

	echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);

}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */




<?php
    include '../../Library/SessionManager.php';
    $session = new SessionManager();
    require('../../Library/DBHelper.php');
    $DB = new DBHelper();

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
$table = 'ticket';

// Table's primary key
$primaryKey = 'ticketID';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => '`ticket`.`ticketID`', 'dt' => 0, 'field' => 'ticketID' ),
    array( 'db' => '`collection`.`displayname`', 'dt' => 1,'field' => 'displayname'),
    array( 'db' => '`ticket`.`subject`', 'dt' => 2,'field' => 'subject'),
    array( 'db' => '`ticket`.`submissiondate`', 'dt' => 3,'field' => 'submissiondate' ),
    array( 'db' => '`user`.`username`',  'dt' => 4, 'field' => 'username' ),
    array( 'db' => '`ticket`.`status`', 'dt' => 5,'field' => 'status')
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('../../Library/sspwithjoin.class.php');
$joinQuery = " FROM `ticket` INNER JOIN `collection` ON (`ticket`.`collectionID` = `collection`.`collectionID`)
 INNER JOIN `user` ON (`ticket`.`posterID` = `user`.`userID`)";
$extraWhere = "";

echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);
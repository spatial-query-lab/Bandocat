<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
/*Ensures the col variable is not null then assigns to the $collection variable */
if(isset($_GET['col']))
    $collection = htmlspecialchars($_GET['col']);
else header('Location: ../../');
require('../../Library/DBHelper.php');
$DB = new DBHelper();

if(isset($_GET['action']))
{
    switch($_GET['action'])
    {
        case "load": //load a row from author table
            echo json_encode($DB->GET_AUTHOR_INFO($_GET['col'],$_GET['id']));
            return;
        case "loadnext": //load a row from author table
            echo json_encode($DB->GET_AUTHOR_INFO($_GET['col'],$_GET['id'],"nextID"));
            return;
        case "update": //update tdlname
            echo json_encode($DB->UPDATE_AUTHOR_INFO($_GET['col'],$_POST['authorID'],$_POST['authorname'],$_POST['oldname']));
            return;
        default: break;
    }
}
else { //list processing

// SQL server connection information
    $sql_details = array(
        'user' => $DB->getUser(),
        'pass' => $DB->getPwd(),
        'db' => $DB->SP_GET_COLLECTION_CONFIG($collection)['DbName'],
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
    $table = 'author';

// Table's primary key
    $primaryKey = 'authorID';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
//DB is the bandocat database that holds the documents

    $columns = array(
        array('db' => '`author`.`authorID`', 'dt' => 0, 'field' => 'authorID'),
        array('db' => '`author`.`authorname`', 'dt' => 1, 'field' => 'authorname'),
        array('db' => '`author`.`TDLname`', 'dt' => 2, 'field' => 'TDLname'),
    );

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP
     * server-side, there is no need to edit below this line.
     */

    require('../../Library/sspwithjoin.class.php');
//document is the name of the db
    $joinQuery = " FROM `author`";
//extra where parameter to search with
    $extraWhere = "";
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );

}
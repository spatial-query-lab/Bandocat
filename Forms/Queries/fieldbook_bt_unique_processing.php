<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
/*Ensures the col variable is not null then assigns to the $collection variable */
if(isset($_GET['col']))
    $collection = htmlspecialchars($_GET['col']);
else header('Location: ../../');
require('../../Library/DBHelper.php');
require('../../Library/FieldBookDBHelper.php');
$DB = new DBHelper();
$FB = new FieldBookDBHelper();
$ret = null;
$stage = $_GET['stage'];
// SQL server connection information
$sql_details = array(
    'user' => $DB->getUser(),
    'pass' => $DB->getPwd(),
    'db'   => $DB->SP_GET_COLLECTION_CONFIG($collection)['DbName'],
    'host' => $DB->getHost()
);
if($stage == 0)
{
    $doc = new DomDocument;

// We need to validate our document before refering to the id
    $doc->validateOnParse = true;

    //GRAB UNIQUE BOOK TITLES
    $ret = $FB->GET_BOOKS($collection);
    //ITERATE THROUGH BOOKS TO COUNT NEEDS REVIEW
    foreach($ret as $OuterArray)
    {

        if(is_array($OuterArray))
        {
            foreach($OuterArray as $value)
            {
                //At this point $value is a unique booktitle
                //Checks DB for needsreview 0 (Does not need to be reviewed)
                $countfilter0 = $DB->GET_DOCUMENT_FILTEREDNEEDSREVIEW0_COUNT($collection, $value);
                //Checks DB for needsreview 1 (Needs to be reviewed)
                $countfilter1 = $DB->GET_DOCUMENT_FILTEREDNEEDSREVIEW1_COUNT($collection, $value);
                //Get the Total number of
                $total = $DB->GET_DOCUMENT_MATCHBOOKTITLE_COUNT($collection,$value);
                $returnedStage = $DB->GET_DOCUMENT_MATCHBOOKTITLE_PDFSTAGE($collection,$value);

                //compare the not needing to be reviewed
                if($countfilter0 != $total)
                {
                    //If the number of items that does not need to be reviewed match the number of items in total
                    //then we know that the fieldbook is complete and ready
                    //Flag entire book to be ready for PDF
                    $ret = $FB->UPDATE_FIELDBOOK_READYFORPDF($collection,$value,$stage);
                }
                else if($countfilter0 == $total)
                {
                    //If the number of items that does not need to be reviewed match the number of items in total
                    //then we know that the fieldbook is complete and ready
                    //Flag entire book to be ready for PDF
                    $ret = $FB->UPDATE_FIELDBOOK_READYFORPDF($collection,$value,1);
                }
            }
        }

    }
}
if($stage == 1)
{

    //GRAB UNIQUE BOOK TITLES
    $ret = $FB->GET_BOOKS($collection);
    //ITERATE THROUGH BOOKS TO COUNT NEEDS REVIEW
    foreach($ret as $OuterArray)
    {

        if(is_array($OuterArray))
        {
            foreach($OuterArray as $value)
            {
               //At this point $value is a unique booktitle
                //Checks DB for needsreview 0 (Does not need to be reviewed)
                $countfilter0 = $DB->GET_DOCUMENT_FILTEREDNEEDSREVIEW0_COUNT($collection, $value);

                //Checks DB for needsreview 1 (Needs to be reviewed)
               // $countfilter1 = $DB->GET_DOCUMENT_FILTEREDNEEDSREVIEW1_COUNT($collection, $value);
                //Get the Total number of
                $total = $DB->GET_DOCUMENT_MATCHBOOKTITLE_COUNT($collection,$value);
               // echo $total;
                $returnedStage = $DB->GET_DOCUMENT_MATCHBOOKTITLE_PDFSTAGE($collection,$value);

                //compare the not needing to be reviewed
                if($countfilter0 == $total && $returnedStage == 0)
                {
                    //If the number of items that does not need to be reviewed match the number of items in total
                    //then we know that the fieldbook is complete and ready
                    //Flag entire book to be ready for PDF
                     $ret = $FB->UPDATE_FIELDBOOK_READYFORPDF($collection,$value,$stage);
                }
                else if($countfilter0 != $total)
                {
                    //If the number of items that does not need to be reviewed match the number of items in total
                    //then we know that the fieldbook is complete and ready
                    //Flag entire book to be ready for PDF
                    $ret = $FB->UPDATE_FIELDBOOK_READYFORPDF($collection,$value,0);
                }
            }
        }

    }

}

$pagestat = "that don't need to be reviewed,";
//$test = $FB->COUNT_FIELDBOOK_READYFORPDF($collection,$stage);
//var_dump($test . "TEST ");

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

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
//DB is the bandocat database that holds the documents

$columns = array(
    array( 'db' => '`document`.`documentID`', 'dt' => 0, 'field' => 'documentID' ),
    array( 'db' => '`document`.`booktitle`', 'dt' => 1,'field' => 'booktitle' )

);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('../../Library/sspwithjoin.class.php');
//document is the name of the db
$joinQuery = " FROM `document` ";
//extra where parameter to search with
$groupby = "`booktitle` HAVING COUNT(*) >= 1";
$extraWhere = null;
$extraWhere = " `document`.`RdyForPdf` = '$stage'";
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupby)
);
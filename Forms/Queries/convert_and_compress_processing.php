<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
/*Ensures the col variable is not null then assigns to the $collection variable */
if(isset($_GET['col']))
    $collection = htmlspecialchars($_GET['col']);
else header('Location: ../../');
require('../../Library/DBHelper.php');
require('../../Library/FieldBookDBHelper.php');
require('../../Library/FolderDBHelper.php');
$DB = new DBHelper();
$FB = new FieldBookDBHelper();
$JF = new FolderDBHelper();
$ret = null;
$stage = $_GET['stage'];
// SQL server connection information
$sql_details = array(
    'user' => $DB->getUser(),
    'pass' => $DB->getPwd(),
    'db'   => $DB->SP_GET_COLLECTION_CONFIG($collection)['DbName'],
    'host' => $DB->getHost()
);
if($collection == "blucherfieldbook")
{
	//NEEDS REVIEW - Stage 0
    if($stage == 0)
    {
        $doc = new DomDocument;

		// We need to validate our document before refering to the id
        $doc->validateOnParse = true;
		
       
    }
	//RDY FOR PDF - Stage 1
    if($stage == 1)
    {
        //GRAB UNIQUE BOOK TITLES
        $ret = $FB->GET_NON_PUBLISHED_UNIQUE_BOOKS($collection);
        //ITERATE THROUGH BOOKS TO COUNT NEEDS REVIEW	
        foreach($ret as $OuterArray)
        {     
            if(is_array($OuterArray))
            {
                foreach($OuterArray as $value)
                {                 
                    //count documents where needsreview  is 0 for a book (Does not need to be reviewed)
                    $countfilter0 = $DB->GET_DOCUMENT_FILTEREDNEEDSREVIEW0_COUNT_FIELDBOOK($collection, $value);              
                    //Get the Total number of documents for a book
                    $total = $DB->GET_DOCUMENT_MATCHBOOKTITLE_COUNT_FIELDBOOK($collection,$value);
                    //Verify book is in stage 0
                    $returnedStage = $DB->GET_DOCUMENT_MATCHBOOKTITLE_PDFSTAGE($collection,$value);
					//if the book is ready for PDF
                    if($countfilter0 == $total && $returnedStage == 0)
                    {                       
                        //Flag entire book to be ready for PDF (1)
                        $ret = $FB->UPDATE_FIELDBOOK_READYFORPDF($collection,$value,$stage);
                    }
                    else if($countfilter0 != $total)
                    {
                        //Flag entire book not ready for PDF (0)
                        $ret = $FB->UPDATE_FIELDBOOK_READYFORPDF($collection,$value,0);
                    }
                }
            }
        }
    }

    $pagestat = "that don't need to be reviewed,";
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

    require('../../Library/sspwithjoin.class.php');
	//document is the name of the table
    $joinQuery = " FROM `document` ";
	//extra where parameter to search with
    $groupby = "`booktitle` HAVING COUNT(*) >= 1";
    $extraWhere = null;
    $extraWhere = " `document`.`RdyForPdf` = '$stage'";
    echo json_encode(
        SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupby)
    );
}
if($collection == "jobfolder")
{
    if($stage == 0)
    {
        $doc = new DomDocument;

		// We need to validate our document before refering to the id
        $doc->validateOnParse = true;
		
    }
    if($stage == 1)
    {

        //GRAB UNIQUE BOOK TITLES
        $ret = $JF->GET_FOLDERS($collection);
        //ITERATE THROUGH BOOKS TO COUNT NEEDS REVIEW
        foreach($ret as $OuterArray)
        {
			

            if(is_array($OuterArray))
            {
                foreach($OuterArray as $value)
                {
					
                    //At this point $value is a unique booktitle
                    //Checks DB for needsreview 0 (Does not need to be reviewed)
                    $countfilter0 = $DB->GET_DOCUMENT_FILTEREDNEEDSREVIEW0_COUNT_JOBFOLDER($collection, $value);

                    //Checks DB for needsreview 1 (Needs to be reviewed)
                    // $countfilter1 = $DB->GET_DOCUMENT_FILTEREDNEEDSREVIEW1_COUNT($collection, $value);
                    //Get the Total number of
                    $total = $DB->GET_DOCUMENT_MATCHBOOKTITLE_COUNT_JOBFOLDER($collection,$value);
                    // echo $total;
					
					
                    $returnedStage = $DB->GET_DOCUMENT_MATCHFOLDERNAME_PDFSTAGE($collection,$value);

                    //compare the not needing to be reviewed
                    if($countfilter0 == $total && $returnedStage == 0)
                    {
                        //If the number of items that does not need to be reviewed match the number of items in total
                        //then we know that the fieldbook is complete and ready
                        //Flag entire book to be ready for PDF
                        $ret = $JF->UPDATE_JOBFOLDER_READYFORPDF($collection,$value,$stage);
                    }
                    else if($countfilter0 != $total)
                    {
                        //If the number of items that does not need to be reviewed match the number of items in total
                        //then we know that the fieldbook is complete and ready
                        //Flag entire book to be ready for PDF
                        $ret = $JF->UPDATE_JOBFOLDER_READYFORPDF($collection,$value,0);
                    }
                }
            }

        }

    }

    $pagestat = "that don't need to be reviewed,";
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
        array( 'db' => '`document`.`foldername`', 'dt' => 1,'field' => 'foldername' )

    );

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP
     * server-side, there is no need to edit below this line.
     */

    require('../../Library/sspwithjoin.class.php');
//document is the name of the db
    $joinQuery = " FROM `document` ";
//extra where parameter to search with
    $groupby = "`foldername` HAVING COUNT(*) >= 1";
    $extraWhere = null;
    $extraWhere = " `document`.`RdyForPdf` = '$stage'";
    echo json_encode(
        SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupby)
    );
}

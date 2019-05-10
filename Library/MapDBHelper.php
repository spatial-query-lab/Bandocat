<?php
/**********************************************
Function:
Description:
Parameter(s):
Return value(s):
 ***********************************************/
require_once 'GeoRectificationDB.php';
class MapDBHelper extends DBHelper
{
    use GeoRectificationTrait;
    /**********************MAP FUNCTIONS************************/

     /**********************************************
 * Function: SP_TEMPLATE_MAP_DOCUMENT_SELECT
 * Description: GIVEN collection name & document ID, RETURN INFORMATION ABOUT Document
 * Parameter(s):
 * collection (in string) - name of the collection
 * $iDocID (in Integer) - document ID
 * Return value(s):
 * $result (assoc array) - return a document info in an associative array, or FALSE if failed
 ***********************************************/
    function SP_TEMPLATE_MAP_DOCUMENT_SELECT($collection, $iDocID)
    {
        //get appropriate DB name
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_DOCUMENT_SELECT(?,@oLibraryIndex,@oTitle,@oSubtitle,@oIsMap,@oMapScale,@oHasNorthArrow,@oHasStreets,@oHasPOI,@oHasCoordinates,@oHasCoast,@oFileName,@oFileNameBack,@oNeedsReview,@oComments,@oCustomerName,@oStartDate,@oEndDate,@oFieldBookNumber,@oFieldBookPage,@oReadability,@oRectifiability,@oCompanyName,@oType,@oMedium,@oAuthorName,@oFileNamePath,@oFileNameBackPath,@oTDLAuthorName)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            // bind $iDocID to the above call statement
            $call->bindParam(1, $iDocID, PDO::PARAM_INT, 11);
            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            //select statement
            $select = $this->getConn()->query('SELECT @oLibraryIndex AS LibraryIndex,@oTitle AS Title,@oSubtitle AS Subtitle,@oIsMap AS IsMap,@oMapScale AS MapScale,@oHasNorthArrow AS HasNorthArrow,@oHasStreets AS HasStreets,@oHasPOI AS HasPOI,@oHasCoordinates AS HasCoordinates,@oHasCoast AS HasCoast,@oFileName AS FileName,@oFileNameBack AS FileNameBack,@oNeedsReview AS NeedsReview,@oComments AS Comments,@oCustomerName AS CustomerName,@oStartDate AS StartDate,@oEndDate AS EndDate,@oFieldBookNumber AS FieldBookNumber,@oFieldBookPage AS FieldBookPage,@oReadability AS Readability,@oRectifiability AS Rectifiability,@oCompanyName AS CompanyName,@oType AS Type,@oMedium AS Medium,@oAuthorName AS AuthorName,@oFileNamePath AS FileNamePath,@oFileNameBackPath AS FileNameBackPath, @oTDLAuthorName AS TDLAuthorName');
            //return selected information
            $result = $select->fetch(PDO::FETCH_ASSOC);
            return $result;
        } else return false;
    }
    /**********************************************
     * Function: SP_TEMPLATE_MAP_DOCUMENT_SELECT
     * Description: GIVEN collection name & document ID, RETURN INFORMATION ABOUT Document
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iDocID (in Integer) - document ID
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_MAP_DOCUMENT_WITH_JOBNUMBER_SELECT($collection, $iDocID)
    {
        //get appropriate DB name
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_DOCUMENT_WITH_JOBNUMBER_SELECT(?,@oLibraryIndex,@oTitle,@oSubtitle,@oIsMap,@oMapScale,@oHasNorthArrow,@oHasStreets,@oHasPOI,@oHasCoordinates,@oHasCoast,@oFileName,@oFileNameBack,@oNeedsReview,@oComments,@oCustomerName,@oStartDate,@oEndDate,@oJobNumber,@oFieldBookNumber,@oFieldBookPage,@oReadability,@oRectifiability,@oCompanyName,@oType,@oMedium,@oAuthorName,@oFileNamePath,@oFileNameBackPath,@oTDLAuthorName)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            // bind $iDocID to the above call statement
            $call->bindParam(1, $iDocID, PDO::PARAM_INT, 11);
            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            //select statement
            $select = $this->getConn()->query('SELECT @oLibraryIndex AS LibraryIndex,@oTitle AS Title,@oSubtitle AS Subtitle,@oIsMap AS IsMap,@oMapScale AS MapScale,@oHasNorthArrow AS HasNorthArrow,@oHasStreets AS HasStreets,@oHasPOI AS HasPOI,@oHasCoordinates AS HasCoordinates,@oHasCoast AS HasCoast,@oFileName AS FileName,@oFileNameBack AS FileNameBack,@oNeedsReview AS NeedsReview,@oComments AS Comments,@oCustomerName AS CustomerName,@oStartDate AS StartDate,@oEndDate AS EndDate,@oJobNumber AS JobNumber,@oFieldBookNumber AS FieldBookNumber,@oFieldBookPage AS FieldBookPage,@oReadability AS Readability,@oRectifiability AS Rectifiability,@oCompanyName AS CompanyName,@oType AS Type,@oMedium AS Medium,@oAuthorName AS AuthorName,@oFileNamePath AS FileNamePath,@oFileNameBackPath AS FileNameBackPath, @oTDLAuthorName AS TDLAuthorName');
            //return selected information
            $result = $select->fetch(PDO::FETCH_ASSOC);
            return $result;
        } else return false;
    }
    /**********************************************
     * Function: SP_TEMPLATE_MAP_DOCUMENT_UPDATE
     * Description: Updates a specified map template document
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iDocID (in Integer) - document ID
     * $iLibraryIndex (in string) -
     * $iTitle (in string) - title of the document
     * $iSubtitle (in string) - subtitle of the document
     * $iIsMap (in Integer) - flag if document is a map
     * $iMapScale (in string) - identifies the map scale
     * $iHasNorthArrow (in Integer) - flag if document has a north arrow
     * $iHasStreets (in Integer) - flag if document has streets
     * $iHasPOI (in Integer) -
     * $iHasCoordinates (in Integer) - flag if document has coordinates
     * $iHasCoast (in Integer) - flag if document has a coastline
     * $iNeedsReview (in Integer) - flag if document needs to be reviewed
     * $iComments (in string) -
     * $iCustomerID (in Integer) - identifies the customer's ID number
     * $iStartDate (in string) - date when the document
     * $iEndDate (in string) - date when the document
     * $iFieldBookNumber (in Integer) - identifies the fieldbooknumber
     * $iFieldBookPage (in Integer) - identifies the fieldbookpage
     * $iReadability (in string) - string specifies how readable the document is
     * $iRectifiability (in string) -
     * $iCompanyID (in Integer) - identifies the companysID
     * $iType (in string) -
     * $iMedium (in Integer) -
     * $iAuthorID (in Integer) - identifies the author of the document
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_MAP_DOCUMENT_UPDATE($collection,
                                             $iDocID, $iLibraryIndex, $iTitle, $iSubtitle, $iIsMap, $iMapScale,
                                             $iHasNorthArrow, $iHasStreets, $iHasPOI, $iHasCoordinates, $iHasCoast,
                                             $iNeedsReview, $iComments, $iCustomerID, $iStartDate,
                                             $iEndDate, $iFieldBookNumber, $iFieldBookPage, $iReadability, $iRectifiability,
                                             $iCompanyID, $iType, $iMedium, $iAuthorID)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_DOCUMENT_UPDATE(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //Binds all parameters to the prepared SQL statement
            $call->bindParam(1, $iDocID, PDO::PARAM_INT);
            $call->bindParam(2, $iLibraryIndex, PDO::PARAM_STR);
            $call->bindParam(3, $iTitle, PDO::PARAM_STR);
            $call->bindParam(4, $iSubtitle, PDO::PARAM_STR);
            $call->bindParam(5, $iIsMap, PDO::PARAM_INT);
            $call->bindParam(6, $iMapScale, PDO::PARAM_STR);
            $call->bindParam(7, $iHasNorthArrow, PDO::PARAM_INT);
            $call->bindParam(8, $iHasStreets, PDO::PARAM_INT);
            $call->bindParam(9, $iHasPOI, PDO::PARAM_INT);
            $call->bindParam(10, $iHasCoordinates, PDO::PARAM_INT);
            $call->bindParam(11, $iHasCoast, PDO::PARAM_INT);
            $call->bindParam(12, $iNeedsReview, PDO::PARAM_INT);
            $call->bindParam(13, $iComments, PDO::PARAM_STR);
            $call->bindParam(14, $iCustomerID, PDO::PARAM_INT);
            $call->bindParam(15, $iStartDate, PDO::PARAM_STR);
            $call->bindParam(16, $iEndDate, PDO::PARAM_STR);
            $call->bindParam(17, $iFieldBookNumber, PDO::PARAM_STR);
            $call->bindParam(18, $iFieldBookPage, PDO::PARAM_STR);
            $call->bindParam(19, $iReadability, PDO::PARAM_STR);
            $call->bindParam(20, $iRectifiability, PDO::PARAM_STR);
            $call->bindParam(21, $iCompanyID, PDO::PARAM_INT);
            $call->bindParam(22, $iType, PDO::PARAM_STR);
            $call->bindParam(23, $iMedium, PDO::PARAM_INT);
            $call->bindParam(24, $iAuthorID, PDO::PARAM_INT);

            /* EXECUTE STATEMENT */
            //execute sql statement
            return $call->execute();
        } else return false;
    }
	/**********************************************
     * Function: SP_TEMPLATE_MAP_DOCUMENT_UPDATE
     * Description: Updates a specified map template document
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iDocID (in Integer) - document ID
     * $iLibraryIndex (in string) -
     * $iTitle (in string) - title of the document
     * $iSubtitle (in string) - subtitle of the document
     * $iIsMap (in Integer) - flag if document is a map
     * $iMapScale (in string) - identifies the map scale
     * $iHasNorthArrow (in Integer) - flag if document has a north arrow
     * $iHasStreets (in Integer) - flag if document has streets
     * $iHasPOI (in Integer) -
     * $iHasCoordinates (in Integer) - flag if document has coordinates
     * $iHasCoast (in Integer) - flag if document has a coastline
     * $iNeedsReview (in Integer) - flag if document needs to be reviewed
     * $iComments (in string) -
     * $iCustomerID (in Integer) - identifies the customer's ID number
     * $iStartDate (in string) - date when the document
     * $iEndDate (in string) - date when the document
     * $iFieldBookNumber (in Integer) - identifies the fieldbooknumber
     * $iFieldBookPage (in Integer) - identifies the fieldbookpage
     * $iReadability (in string) - string specifies how readable the document is
     * $iRectifiability (in string) -
     * $iCompanyID (in Integer) - identifies the companysID
     * $iType (in string) -
     * $iMedium (in Integer) -
     * $iAuthorID (in Integer) - identifies the author of the document
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_MAP_DOCUMENT_WITH_JOBNUMBER_UPDATE($collection,
                                             $iDocID, $iLibraryIndex, $iTitle, $iSubtitle, $iIsMap, $iMapScale,
                                             $iHasNorthArrow, $iHasStreets, $iHasPOI, $iHasCoordinates, $iHasCoast,
                                             $iNeedsReview, $iComments, $iCustomerID, $iStartDate,
                                             $iEndDate,$iJobNumber, $iFieldBookNumber, $iFieldBookPage, $iReadability, $iRectifiability,
                                             $iCompanyID, $iType, $iMedium, $iAuthorID)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_DOCUMENT_WITH_JOBNUMBER_UPDATE(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //Binds all parameters to the prepared SQL statement
            $call->bindParam(1, $iDocID, PDO::PARAM_INT);
            $call->bindParam(2, $iLibraryIndex, PDO::PARAM_STR);
            $call->bindParam(3, $iTitle, PDO::PARAM_STR);
            $call->bindParam(4, $iSubtitle, PDO::PARAM_STR);
            $call->bindParam(5, $iIsMap, PDO::PARAM_INT);
            $call->bindParam(6, $iMapScale, PDO::PARAM_STR);
            $call->bindParam(7, $iHasNorthArrow, PDO::PARAM_INT);
            $call->bindParam(8, $iHasStreets, PDO::PARAM_INT);
            $call->bindParam(9, $iHasPOI, PDO::PARAM_INT);
            $call->bindParam(10, $iHasCoordinates, PDO::PARAM_INT);
            $call->bindParam(11, $iHasCoast, PDO::PARAM_INT);
            $call->bindParam(12, $iNeedsReview, PDO::PARAM_INT);
            $call->bindParam(13, $iComments, PDO::PARAM_STR);
            $call->bindParam(14, $iCustomerID, PDO::PARAM_INT);
            $call->bindParam(15, $iStartDate, PDO::PARAM_STR);
            $call->bindParam(16, $iEndDate, PDO::PARAM_STR);
            $call->bindParam(17, $iJobNumber, PDO::PARAM_STR);
            $call->bindParam(18, $iFieldBookNumber, PDO::PARAM_STR);
            $call->bindParam(19, $iFieldBookPage, PDO::PARAM_STR);
            $call->bindParam(20, $iReadability, PDO::PARAM_STR);
            $call->bindParam(21, $iRectifiability, PDO::PARAM_STR);
            $call->bindParam(22, $iCompanyID, PDO::PARAM_INT);
            $call->bindParam(23, $iType, PDO::PARAM_STR);
            $call->bindParam(24, $iMedium, PDO::PARAM_INT);
            $call->bindParam(25, $iAuthorID, PDO::PARAM_INT);

            /* EXECUTE STATEMENT */
            //execute sql statement
            return $call->execute();
        } else return false;
    }
     /**********************************************
     * Function: SP_TEMPLATE_MAP_DOCUMENT_INSERT
     * Description: inserts a specified map template document
     * Parameter(s):
     * $collection (in string) - name of the collection
     * $iLibraryIndex (in string) -
     * $iTitle (in string) - title of the document
     * $iSubtitle (in string) - subtitle of the document
     * $iIsMap (in Integer) - flag if document is a map
     * $iMapScale (in string) - identifies the map scale
     * $iHasNorthArrow (in Integer) - flag if document has a north arrow
     * $iHasStreets (in Integer) - flag if document has streets
     * $iHasPOI (in Integer) -
     * $iHasCoordinates (in Integer) - flag if document has coordinates
     * $iHasCoast (in Integer) - flag if document has a coastline
     * $iFileName (in string) -
     * $iFileNameBack (in string) -
     * $iNeedsReview (in Integer) - flag if document needs to be reviewed
     * $iComments (in string) -
     * $iCustomerID (in Integer) - identifies the customer's ID number
     * $iStartDate (in string) - date when the document
     * $iEndDate (in string) - date when the document
     * $iFieldBookNumber (in Integer) - identifies the fieldbooknumber
     * $iFieldBookPage (in Integer) - identifies the fieldbookpage
     * $iReadability (in string) - string specifies how readable the document is
     * $iRectifiability (in string) -
     * $iCompanyID (in Integer) - identifies the companysID
     * $iType (in string) -
     * $iMedium (in Integer) -
     * $iAuthorID (in Integer) - identifies the author of the document
     * $iFileNamePath (in string) -
     * $iFileNameBackPath (in string) -
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_MAP_DOCUMENT_INSERT($collection,
                                             $iLibraryIndex, $iTitle, $iSubtitle, $iIsMap,
                                             $iMapScale, $iHasNorthArrow, $iHasStreets, $iHasPOI,
                                             $iHasCoordinates, $iHasCoast,$iFileName, $iFileNameBack,
                                             $iNeedsReview, $iComments, $iCustomerID, $iStartDate,
                                             $iEndDate, $iFieldBookNumber, $iFieldBookPage, $iReadability,
                                             $iRectifiability, $iCompanyID, $iType, $iMedium,
                                             $iAuthorID,$iFileNamePath,$iFileNameBackPath)
    {
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_DOCUMENT_INSERT(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //Binds all parameters to the prepared SQL statement
            $call->bindParam(1, $iLibraryIndex, PDO::PARAM_STR);
            $call->bindParam(2, $iTitle, PDO::PARAM_STR);
            $call->bindParam(3, $iSubtitle, PDO::PARAM_STR);
            $call->bindParam(4, $iIsMap, PDO::PARAM_INT);
            $call->bindParam(5, $iMapScale, PDO::PARAM_STR);
            $call->bindParam(6, $iHasNorthArrow, PDO::PARAM_INT);
            $call->bindParam(7, $iHasStreets, PDO::PARAM_INT);
            $call->bindParam(8, $iHasPOI, PDO::PARAM_INT);
            $call->bindParam(9, $iHasCoordinates, PDO::PARAM_INT);
            $call->bindParam(10, $iHasCoast, PDO::PARAM_INT);
            $call->bindParam(11, $iFileName, PDO::PARAM_STR);
            $call->bindParam(12, $iFileNameBack, PDO::PARAM_STR);
            $call->bindParam(13, $iNeedsReview, PDO::PARAM_INT);
            $call->bindParam(14, $iComments, PDO::PARAM_STR);
            $call->bindParam(15, $iCustomerID, PDO::PARAM_INT);
            $call->bindParam(16, $iStartDate, PDO::PARAM_STR);
            $call->bindParam(17, $iEndDate, PDO::PARAM_STR);
            $call->bindParam(18, $iFieldBookNumber, PDO::PARAM_STR);
            $call->bindParam(19, $iFieldBookPage, PDO::PARAM_STR);
            $call->bindParam(20, $iReadability, PDO::PARAM_STR);
            $call->bindParam(21, $iRectifiability, PDO::PARAM_STR);
            $call->bindParam(22, $iCompanyID, PDO::PARAM_INT);
            $call->bindParam(23, $iType, PDO::PARAM_STR);
            $call->bindParam(24, $iMedium, PDO::PARAM_INT);
            $call->bindParam(25, $iAuthorID, PDO::PARAM_INT);
            $call->bindParam(26, $iFileNamePath, PDO::PARAM_STR);
            $call->bindParam(27, $iFileNameBackPath, PDO::PARAM_STR);


            /* EXECUTE STATEMENT */
            $ret = $call->execute();
            if($ret)
            {
                //select the LAST_INSERT_ID
                $select = $this->getConn()->query('SELECT LAST_INSERT_ID()');
                //return LAST_INSERT_ID
                $ret = $select->fetch(PDO::FETCH_COLUMN);
                return $ret;
            }
            print_r($call->errorInfo());
            return 0;
        } else return false;
    }

    /**********************************************
     * Function: SP_TEMPLATE_MAP_DOCUMENT_INSERT
     * Description: inserts a specified map template document
     * Parameter(s):
     * $collection (in string) - name of the collection
     * $iLibraryIndex (in string) -
     * $iTitle (in string) - title of the document
     * $iSubtitle (in string) - subtitle of the document
     * $iIsMap (in Integer) - flag if document is a map
     * $iMapScale (in string) - identifies the map scale
     * $iHasNorthArrow (in Integer) - flag if document has a north arrow
     * $iHasStreets (in Integer) - flag if document has streets
     * $iHasPOI (in Integer) -
     * $iHasCoordinates (in Integer) - flag if document has coordinates
     * $iHasCoast (in Integer) - flag if document has a coastline
     * $iFileName (in string) -
     * $iFileNameBack (in string) -
     * $iNeedsReview (in Integer) - flag if document needs to be reviewed
     * $iComments (in string) -
     * $iCustomerID (in Integer) - identifies the customer's ID number
     * $iStartDate (in string) - date when the document
     * $iEndDate (in string) - date when the document
     * $iFieldBookNumber (in Integer) - identifies the fieldbooknumber
     * $iFieldBookPage (in Integer) - identifies the fieldbookpage
     * $iReadability (in string) - string specifies how readable the document is
     * $iRectifiability (in string) -
     * $iCompanyID (in Integer) - identifies the companysID
     * $iType (in string) -
     * $iMedium (in Integer) -
     * $iAuthorID (in Integer) - identifies the author of the document
     * $iFileNamePath (in string) -
     * $iFileNameBackPath (in string) -
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_MAP_DOCUMENT_WITH_JOBNUMBER_INSERT($collection,
                                             $iLibraryIndex, $iTitle, $iSubtitle, $iIsMap,
                                             $iMapScale, $iHasNorthArrow, $iHasStreets, $iHasPOI,
                                             $iHasCoordinates, $iHasCoast,$iFileName, $iFileNameBack,
                                             $iNeedsReview, $iComments, $iCustomerID, $iStartDate,
                                             $iEndDate,$iJobNumber, $iFieldBookNumber, $iFieldBookPage, $iReadability,
                                             $iRectifiability, $iCompanyID,$iType, $iMedium,
                                             $iAuthorID,$iFileNamePath,$iFileNameBackPath,$iHasScaleBar)
    {
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_DOCUMENT_WITH_JOBNUMBER_INSERT(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //Binds all parameters to the prepared SQL statement
            $call->bindParam(1, $iLibraryIndex, PDO::PARAM_STR);
            $call->bindParam(2, $iTitle, PDO::PARAM_STR);
            $call->bindParam(3, $iSubtitle, PDO::PARAM_STR);
            $call->bindParam(4, $iIsMap, PDO::PARAM_INT);
            $call->bindParam(5, $iMapScale, PDO::PARAM_STR);
            $call->bindParam(6, $iHasNorthArrow, PDO::PARAM_INT);
            $call->bindParam(7, $iHasStreets, PDO::PARAM_INT);
            $call->bindParam(8, $iHasPOI, PDO::PARAM_INT);
            $call->bindParam(9, $iHasCoordinates, PDO::PARAM_INT);
            $call->bindParam(10, $iHasCoast, PDO::PARAM_INT);
            $call->bindParam(11, $iFileName, PDO::PARAM_STR);
            $call->bindParam(12, $iFileNameBack, PDO::PARAM_STR);
            $call->bindParam(13, $iNeedsReview, PDO::PARAM_INT);
            $call->bindParam(14, $iComments, PDO::PARAM_STR);
            $call->bindParam(15, $iCustomerID, PDO::PARAM_INT);
            $call->bindParam(16, $iStartDate, PDO::PARAM_STR);
            $call->bindParam(17, $iEndDate, PDO::PARAM_STR);
            $call->bindParam(18, $iJobNumber, PDO::PARAM_STR);
            $call->bindParam(19, $iFieldBookNumber, PDO::PARAM_STR);
            $call->bindParam(20, $iFieldBookPage, PDO::PARAM_STR);
            $call->bindParam(21, $iReadability, PDO::PARAM_STR);
            $call->bindParam(22, $iRectifiability, PDO::PARAM_STR);
            $call->bindParam(23, $iCompanyID, PDO::PARAM_INT);
            $call->bindParam(24, $iType, PDO::PARAM_STR);
            $call->bindParam(25, $iMedium, PDO::PARAM_INT);
            $call->bindParam(26, $iAuthorID, PDO::PARAM_INT);
            $call->bindParam(27, $iFileNamePath, PDO::PARAM_STR);
            $call->bindParam(28, $iFileNameBackPath, PDO::PARAM_STR);
            $call->bindParam(29, $iHasScaleBar, PDO::PARAM_INT);

            /* EXECUTE STATEMENT */
            $ret = $call->execute();
            if($ret)
            {
                //select the LAST_INSERT_ID
                $select = $this->getConn()->query('SELECT LAST_INSERT_ID()');
                //return LAST_INSERT_ID
                $ret = $select->fetch(PDO::FETCH_COLUMN);
                return $ret;
            }
            print_r($call->errorInfo());
            return 0;
        } else return false;
    }
    /**********************************************
     * Function: SP_TEMPLATE_MAP_DOCUMENT_CHECK_EXIST_RECORD
     * Description: Checks whether a map document record exists
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iLibraryIndex (in Integer) - the library index
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_MAP_DOCUMENT_CHECK_EXIST_RECORD($collection, $iLibraryIndex)
    {
        //get appropriate DB
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_DOCUMENT_CHECK_EXIST_RECORD(?,@oReturnValue)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //binds the libraryIndex variable to the above sql statement
            $call->bindParam(1, $iLibraryIndex, PDO::PARAM_STR);
            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            //selects the db variable that indicates if a record exists
            $select = $this->getConn()->query('SELECT @oReturnValue');
            //returns a bool statement
            $result = $select->fetch(PDO::FETCH_NUM);
            //Integer specifies status of the record
            if($result[0] == 1)
                return "EXISTED";
            else if($result[0] == 0)
                return "GOOD";
        } else return false;
    }

    /**********************************************
     * Function: GET_TEMPLATE_MAP_MEDIUM_FOR_DROPDOWN
     * Description: GET DOCUMENT MEDIUMS FOR DROPDOWN LIST
     * Parameter(s): $collection (in String) - Name of the collection
     * Return value(s):
     * $result  (array) - return array of document medium
     ***********************************************/
    function GET_TEMPLATE_MAP_MEDIUM_FOR_DROPDOWN($collection)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepares a select sql statement that selects the map medium
            $sth = $this->getConn()->prepare("SELECT `mediumname` FROM `documentmedium`");
            $sth->execute();
            //returns the map medium
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }

    /**********************************************
     * Function: SP_TEMPLATE_MAP_CUSTOMER_GET_ID_FROM_NAME_WITH_INSERT
     * Description: Attempts to return the customer ID from the db, if a customer does not exist, one is inserted first
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * $iCustomerName (in String) -
     * $oCustomerID - (out Integer) -
     * Return value(s):
     * $result  (array) - return array of document medium
     ***********************************************/
    function SP_TEMPLATE_MAP_CUSTOMER_GET_ID_FROM_NAME_WITH_INSERT($collection, $iCustomerName, &$oCustomerID)
    {
        //check parameter has a value
        if ($iCustomerName == "")
        {
            $oCustomerID = 0;
            return $oCustomerID;
        }
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "")
        {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            //SP_TEMPLATE_MAP_CUSTOMER_GET_ID_FROM_NAME_WITH_INSERT selects the customerID from customer db if it exists, if it does not exist it inserts it into the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_CUSTOMER_GET_ID_FROM_NAME_WITH_INSERT(?,@oCustomerID)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //bind iCustomerName to the above sql statement
            $call->bindParam(1, $iCustomerName, PDO::PARAM_STR, 100);
            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            //select customerID from DB
            $select = $this->getConn()->query('SELECT @oCustomerID');
            //return customerID
            $oCustomerID = $select->fetch(PDO::FETCH_ASSOC)['@oCustomerID'];
            return $oCustomerID;
        } else return false;
    }

    /**********************************************
     * Function: SP_TEMPLATE_MAP_COMPANY_GET_ID_FROM_NAME_WITH_INSERT
     * Description: Attempts to return the company ID from the db, if a company does not exist, one is inserted first
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * $iCompanyName (in String) -
     * $oCompanyID (out Integer) -
     * Return value(s):
     * $result  (array) - return array of document medium
     ***********************************************/
    function SP_TEMPLATE_MAP_COMPANY_GET_ID_FROM_NAME_WITH_INSERT($collection, $iCompanyName, &$oCompanyID)
    {
        //check if parameter is empty
        if ($iCompanyName == "")
        {
            $oCompanyID = 0;
            return $oCompanyID;
        }
        //get appropriate DB
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            //SP_TEMPLATE_MAP_COMPANY_GET_ID_FROM_NAME_WITH_INSERT selects the companyID from company db if it exists, if it does not exist it inserts it into the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_COMPANY_GET_ID_FROM_NAME_WITH_INSERT(?,@oCompanyID)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //bind $iCompanyName to the above sql statement
            $call->bindParam(1, $iCompanyName, PDO::PARAM_STR, 100);
            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            $select = $this->getConn()->query('SELECT @oCompanyID');
            $oCompanyID = $select->fetch(PDO::FETCH_ASSOC)['@oCompanyID'];
            return $oCompanyID;
        } else return false;
    }

    /**********************************************
     * Function: SP_TEMPLATE_MAP_AUTHOR_GET_ID_FROM_NAME_WITH_INSERT
     * Description: Attempts to return the author ID from the db, if a author does not exist, one is inserted first
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * $iCompanyName (in String) -
     * $oCompanyID (out Integer) -
     * Return value(s):
     * $result  (array) - return array of document medium
     ***********************************************/
    function SP_TEMPLATE_MAP_AUTHOR_GET_ID_FROM_NAME_WITH_INSERT($collection, $iAuthorName, &$oAuthorID)
    {
        //check if parameter is empty
        if ($iAuthorName == "")
        {
            $oAuthorID = 0;
            return $oAuthorID;
        }
        //get appropriate DB
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            //SP_TEMPLATE_MAP_AUTHOR_GET_ID_FROM_NAME_WITH_INSERT selects the authorID from author db if it exists, if it does not exist it inserts it into the db
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_AUTHOR_GET_ID_FROM_NAME_WITH_INSERT(?,@oAuthorID)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //bind $iAuthorName to the above sql statement
            $call->bindParam(1, $iAuthorName, PDO::PARAM_STR, 100);
            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            //select authorid from db
            $select = $this->getConn()->query('SELECT @oAuthorID');
            //return authorid
            $oAuthorID = $select->fetch(PDO::FETCH_ASSOC)['@oAuthorID'];
            return $oAuthorID;
        } else return false;
    }
    /**********************************************
     * Function: SP_TEMPLATE_MAP_MEDIUM_GET_ID_FROM_NAME
     * Description: Attempts to return the medium ID from the db
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * $iMediumName (in String) -
     * $oMediumID (out Integer) -
     * Return value(s):
     * $result  (array) - return array of document medium
     ***********************************************/
    function SP_TEMPLATE_MAP_MEDIUM_GET_ID_FROM_NAME($collection, $iMediumName, &$oMediumID)
    {
        //check if parameter has a value
        if ($iMediumName == "") {
            $oMediumID = "";
            return $oMediumID;
        }
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "")
        {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // sql statement CALL calls the function pointed to in the db
            //SP_TEMPLATE_MAP_MEDIUM_GET_ID_FROM_NAME selects the authorID from author db if it exists,
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_MAP_MEDIUM_GET_ID_FROM_NAME(?,@oMediumID)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //binds $iMediumName to the above SQL statement
            $call->bindParam(1, $iMediumName, PDO::PARAM_STR, 20);
            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            //select MediumID
            $select = $this->getConn()->query('SELECT @oMediumID');
            //return MediumID
            $oMediumID = $select->fetch(PDO::FETCH_ASSOC)['@oMediumID'];
            return $oMediumID;
        } else return false;
    }
}
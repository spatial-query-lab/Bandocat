<?php
/**********************************************
Function:
Description:
Parameter(s):
Return value(s):
 ***********************************************/
class FieldBookDBHelper extends DBHelper
{

    /**********************************************
     * Function: SP_TEMPLATE_FIELDBOOK_DOCUMENT_SELECT
     * Description: GIVEN collection name & document ID, RETURN INFORMATION ABOUT Document
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iDocID (in Integer) - document ID
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/

    function SP_TEMPLATE_FIELDBOOK_DOCUMENT_SELECT($collection, $iDocID)
    {
        //get appropriate DB
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "")
        {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            /* Prepares the SQL query, and returns a statement handle
            to be used for further operations on the statement*/
            //SQL Call command is used to call a DB function
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_FIELDBOOK_DOCUMENT_SELECT(?,@oLibraryIndex,@oFBCollection,@oBookTitle,@oJobNumber,@oJobTitle,@oAuthor,@oStartDate,@oEndDate,@oComments,@oIndexedPage,@oIsBlankPage,@oIsSketch,@oIsLooseDoc,@oNeedsInput,@oNeedsReview,@oFileNamePath,@oThumbnail)");
            //Bind variable to sql statement above.
            $call->bindParam(1, $iDocID, PDO::PARAM_INT, 11);
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);

            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            //select statement
            $select = $this->getConn()->query('SELECT @oLibraryIndex AS LibraryIndex,@oFBCollection AS Collection,@oBookTitle AS BookTitle,@oJobNumber As JobNumber,@oJobTitle AS JobTitle,@oAuthor AS Author,@oStartDate AS StartDate,@oEndDate AS EndDate,@oComments AS Comments,@oIndexedPage AS IndexedPage,@oIsBlankPage AS IsBlankPage,@oIsSketch AS IsSketch,@oIsLooseDoc AS IsLooseDoc,@oNeedsInput AS NeedsInput,@oNeedsReview AS NeedsReview,@oFileNamePath AS FileNamePath,@oThumbnail AS Thumbnail');
            //return selected data
            $result = $select->fetch(PDO::FETCH_ASSOC);
            return $result;
        } else return false;
    }

    /**********************************************
     * Function: SP_TEMPLATE_FIELDBOOK_DOCUMENT_INSERT
     * Description: GIVEN collection name & document ID, RETURNs last inserted ID
     * Parameter(s):
     * $collection (in string) - name of the collection
     * $iLibraryIndex (in string) -
     * $iBookTitle (in string) - title of the fieldbook
     * $iFileNamePath (in string) -
     * $iFileName (in string) -
     * $iThumbnail (in string) -
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_FIELDBOOK_DOCUMENT_INSERT($collection,$iLibraryIndex,$iBookTitle,$iFileNamePath,$iFileName,$iThumbnail)
    {
        //get appropriate db
        $db = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($db != null && $db != "")
        {
            $this->getConn()->exec('USE ' . $db);
            /* Prepares the SQL query, and returns a statement handle
          to be used for further operations on the statement*/
            //SQL Call command is used to call a DB function
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_FIELDBOOK_DOCUMENT_INSERT(:libindex,:book,:path,:file,:thumbnail)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //bind parameters to the sql statement above
            $call->bindParam(':libindex', ($iLibraryIndex), PDO::PARAM_STR);
            $call->bindParam(':book', ($iBookTitle), PDO::PARAM_STR);
            $call->bindParam(':path', ($iFileNamePath), PDO::PARAM_STR);
            $call->bindParam(':file', ($iFileName), PDO::PARAM_STR);
            $call->bindParam(':thumbnail', ($iThumbnail), PDO::PARAM_STR);

            //Execute Statement
            $ret = $call->execute();
            if($ret)
            {
                //select returned last inserted id
                $select = $this->getConn()->query('SELECT LAST_INSERT_ID()');
                $data = $select->fetch(PDO::FETCH_COLUMN);
                return $data;
            }
            else{
                return false;
            }

        }
        else return false;

    }
    /**********************************************
     * Function: SP_TEMPLATE_FIELDBOOK_DOCUMENT_UPDATE
     * Description: GIVEN collection name & document ID, RETURNs last inserted ID
     * Parameter(s):
     * $collection (in string) - name of the collection
     * $iDocID (in int) - the document id
     * $iLibraryIndex (in string) -
     * $iFBCollectionName (in string) - specifies the name of the fieldbook collection name
     * $iBookTitle (in string) - specifies the title of the fieldbook
     * $iJobNumber (in string) - specifies the the job number
     * $iJobTitle (in string) - specifies the the job title
     * $iAuthorName (in string) - specifies the the authors name
     * $iStartDate (in string) - specifies the job start date
     * $iEndDate (in string) - specifies the job end date
     * $iComments (in string) - comments
     * $iIndexedPage (in string) - specifies the string if the page is indexed
     * $iIsBlankPage (in int) - specifies the flag if the page is blank
     * $iIsSketch (in int) - specifies the flag if the page is a sketch
     * $iIsLooseDoc (in int) - specifies the flag if the document is a loose document
     * $iNeedsInput (in int) - specifies the flag if input is needed
     * $iNeedsReview (in int) - specifies the flag if the document needs review
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_FIELDBOOK_DOCUMENT_UPDATE($collection, $iDocID, $iLibraryIndex, $iFBCollectionName, $iBookTitle, $iJobNumber,$iJobTitle,$iAuthorName,$iStartDate,$iEndDate,$iComments,$iIndexedPage,$iIsBlankPage,$iIsSketch,$iIsLooseDoc,$iNeedsInput, $iNeedsReview)
    {
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection));
        $db = $dbname['DbName'];
        if ($db != null && $db != ""){
            $this->getConn()->exec('USE ' . $db);
            //Prepare Statement
            if($iIndexedPage == "")
                $iIndexedPage = null;
            /* Prepares the SQL query, and returns a statement handle
          to be used for further operations on the statement*/
            //SQL Call command is used to call a DB function
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_FIELDBOOK_DOCUMENT_UPDATE(:docID,:lib,:fbcol,:btitle,:jnumber,:jtitle,:author,:sdate,:edate,:comments,:indexed,:blankp,:sketch,:loose,:input,:review)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            $call->bindParam(':docID', ($iDocID), PDO::PARAM_INT);
            $call->bindParam(':lib', ($iLibraryIndex), PDO::PARAM_STR);
            $call->bindParam(':fbcol', ($iFBCollectionName), PDO::PARAM_STR);
            $call->bindParam(':btitle',($iBookTitle), PDO::PARAM_STR);
            $call->bindParam(':jnumber', ($iJobNumber), PDO::PARAM_STR);
            $call->bindParam(':jtitle', ($iJobTitle), PDO::PARAM_STR);
            $call->bindParam(':author', ($iAuthorName), PDO::PARAM_STR);
            $call->bindParam(':sdate', ($iStartDate), PDO::PARAM_STR);
            $call->bindParam(':edate', ($iEndDate), PDO::PARAM_STR);
            $call->bindParam(':comments', ($iComments), PDO::PARAM_STR);
            $call->bindParam(':indexed', ($iIndexedPage), PDO::PARAM_STR);
            $call->bindParam(':blankp', ($iIsBlankPage), PDO::PARAM_INT);
            $call->bindParam(':sketch', ($iIsSketch), PDO::PARAM_INT);
            $call->bindParam(':loose', ($iIsLooseDoc), PDO::PARAM_INT);
            $call->bindParam(':input', ($iNeedsInput), PDO::PARAM_INT);
            $call->bindParam(':review', ($iNeedsReview), PDO::PARAM_INT);

            //Execute Statement
            $ret = $call->execute();

            if($ret)
                return true;
            return false;
        }

    }

    /**********************************************
     * Function: TEMPLATE_FIELDBOOK_DELETE_DOCUMENTCREW
     * Description: deletes the fieldbook documentcrew from the db
     * Parameter(s):
     * $collection (in string) - name of the collection
     * $iDocID (in int ) - document id
     * Return value(s):
     * $result (assoc array) - true if success, or FALSE if failed
     ***********************************************/
    function TEMPLATE_FIELDBOOK_DELETE_DOCUMENTCREW($collection,$iDocID)
    {
        //get the appropriate db
        $db = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $db);
        //prepares a sql statement that deletes documentcrew from the db
        $sth = $this->getConn()->prepare("DELETE FROM `documentcrew` WHERE `documentcrew`.`docID` = :docID");
        //bind variables to the sql statement above
        $sth->bindParam(':docID', ($iDocID), PDO::PARAM_INT);
        $ret = $sth->execute();
        if($ret)
            return true;
        return false;
    }

    /**********************************************
     * Function: SP_TEMPLATE_FIELDBOOK_DOCUMENTCREW_INSERT
     * Description: GIVEN document ID & array of CrewName, delete from documentcrew first, then call SP to select/insert from crew table, then insert into documentcrew
     * Parameter(s):
     * $collection (in string) - name of the collection
     * $iDocID (in Integer) - document ID
     * $iCrewNameArray (in Array) - Array of Crew names
     * Return value(s):
     * True if good, False if fail
     ***********************************************/

    function SP_TEMPLATE_FIELDBOOK_DOCUMENTCREW_INSERT($collection,$iDocID,$iCrewNameArray)
    {
        //DELETE FROM DOCUMENTCREW BEFORE INSERTING
        $ret = $this->TEMPLATE_FIELDBOOK_DELETE_DOCUMENTCREW($collection,$iDocID);
        if($ret)
        {
            if (!$ret)
                return false;
            //INSERT VALUES INTO DOCUMENTCREW, IF VALUE DOES NOT EXIST IN CREW TABLE, INSERT INTO CREW FIRST
            foreach ($iCrewNameArray as $iCrew)
            {
                if($iCrew != "")
                {
                    /* Prepares the SQL query, and returns a statement handle
                    to be used for further operations on the statement*/
                    //SQL Call command is used to call a DB function
                    $call = $this->getConn()->prepare("CALL SP_TEMPLATE_FIELDBOOK_DOCUMENTCREW_INSERT(:docID,:crew)");
                    if (!$call)
                    {
                        trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
                        return false;
                    }
                    //bind variables into sql statement above
                    $call->bindParam(':docID', ($iDocID), PDO::PARAM_INT);
                    $call->bindParam(':crew', ($iCrew), PDO::PARAM_STR);
                    $ret = $call->execute();
                    if ($ret == false)
                        return false;
                }
            }
            return true;
        }
        return false;
    }

    /**********************************************
     * Function: TEMPLATE_FIELDBOOK_CHECK_EXIST_RECORD_BY_FILENAME
     * Description: checks to see if the specified filename exists in the db
     * Parameter(s):
     * $collection (in string) - name of the collection
     * $iFileName (in string) - filename to be searched
     * Return value(s):
     * True if good, False if fail
     ***********************************************/
    //FileName is unique on document table
    public function TEMPLATE_FIELDBOOK_CHECK_EXIST_RECORD_BY_FILENAME($collection, $iFileName)
    {
        //get appropriate db
        $db = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($db != null && $db != "")
        {
            $this->getConn()->exec('USE ' . $db);
                //selects all files in document that match the filename parameter
                $sth = $this->getConn()->prepare("SELECT COUNT(*) FROM `document` WHERE `filename` = :filename");
                //binds the variable to the above supplied parameter
                $sth->bindParam(':filename',$iFileName,PDO::PARAM_STR);
                $sth->execute();
                $result = $sth->fetchColumn();
                return $result;
        }
        else return false;
    }
    /**********************************************
     * Function: GET_FIELDBOOK_COLLECTION_LIST
     * Description: attempts to get the list of fieldbook collection names
     * Parameter(s):
     * $collection (in string) - name of the collection
     * Return value(s):
     * True if good, False if fail
     ***********************************************/
    function GET_FIELDBOOK_COLLECTION_LIST($collection)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //selects the fieldbook collection names from the fieldbook collection
            $sth = $this->getConn()->prepare("SELECT `fbcollectionname` FROM `fbcollection`");
            $sth->execute();
            //return the collection names
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }
    /**********************************************
     * Function: GET_CREW_LIST
     * Description: gets the names of the crew members
     * Parameter(s):
     * $collection (in string) - name of the collection
     * Return value(s):
     * True if good, False if fail
     ***********************************************/
    function GET_CREW_LIST($collection)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepare select statement to return all crewnames from the crew table
            $sth = $this->getConn()->prepare("SELECT `crewname` FROM `crew`");
            $sth->execute();

            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }
    /**********************************************
     * Function: GET_FIELDBOOK_CREWS_BY_DOCUMENT_ID
     * Description: gets the names of the crew members in the collection by document
     * Parameter(s):
     * $collection (in string) - name of the collection
     * $iDocID (in Int) - docoument crew member is associated with
     * Return value(s):
     * Array of possible values if success
     ***********************************************/
    function GET_FIELDBOOK_CREWS_BY_DOCUMENT_ID($collection,$iDocID)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepares a select statement to select crew names from documentcrew (matches crew names from crew to the documentid's of document_
            $sth = $this->getConn()->prepare("SELECT c.`crewname` FROM `documentcrew` AS dc LEFT JOIN  `crew` AS c ON dc.`crewID` = c.`crewID` WHERE dc.`docID` = ? ");
            //binds the variables to the prepares sql statement above
            $sth->bindParam(1, $iDocID, PDO::PARAM_INT, 11);
            $sth->execute();
            // return all matches
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }

}
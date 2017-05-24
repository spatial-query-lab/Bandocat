<?php
require_once 'DBHelper.php';
require_once 'TranscriptionDB.php';
/**********************************************
Function:
Description:
Parameter(s):
Return value(s):
 ***********************************************/
class IndicesDBHelper extends DBHelper implements TranscriptionDB
{
    use TranscriptionTrait;

    /**********************************************
     * Function: SP_TEMPLATE_INDICES_DOCUMENT_SELECT
     * Description: GIVEN collection name & document ID, RETURN INFORMATION ABOUT Document
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iDocID (in Integer) - document ID
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/

    function SP_TEMPLATE_INDICES_DOCUMENT_SELECT($collection, $iDocID)
    {
        //get appropriate DB
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            //CALL is sql for telling the db to execute the function following call.
            //The ? in the functions parameter list is a variable that we bind a few lines down
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_INDICES_DOCUMENT_SELECT(?,@oLibraryIndex,@oBookName,@oPageType,@oPageNumber,@oComments,@oNeedsReview,@oFileName,@oFileNamePath,@oTranscribed)");
            //bind parameter variables into the prepared sql statement above
            $call->bindParam(1, $iDocID, PDO::PARAM_INT, 11);
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);

            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            //select requested document info
            $select = $this->getConn()->query('SELECT @oLibraryIndex AS LibraryIndex,@oBookName AS BookName,@oPageType AS PageType,@oPageNumber AS PageNumber,@oComments AS Comments,@oNeedsReview AS NeedsReview,@oFileName AS FileName,@oFileNamePath AS FileNamePath,@oTranscribed AS Transcribed');
            //return document info
            $result = $select->fetch(PDO::FETCH_ASSOC);
            return $result;
        } else return false;
    }
    /**********************************************
     * Function: SP_TEMPLATE_INDICES_DOCUMENT_INSERT
     * Description: GIVEN collection name & document ID, Insert information into the DB
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iLibraryIndex (in string) - the library index
     * $iBookID (in int) - the ID of the book
     * $iPageType (in string) - description of the page type
     * $iPageNumber (in int) - the page number of the page
     * $iComments (in string) - any comments on the page
     * $iNeedsReview (in int) - flag indicating if the page needs review
     * $iFilename (in string) - the filename leading to the scanned document
     * $iFilenamePath (in string) - the filename path leading to the scanned document
     * Return value(s):
     * $result True If success, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_INDICES_DOCUMENT_INSERT($collection, $iLibraryIndex, $iBookID,
                                                 $iPageType, $iPageNumber, $iComments,
                                                 $iNeedsReview, $iFilename,$iFilenamePath)
    {
        //get appropriate DB
        $db = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($db != null && $db != "")
        {
            $this->getConn()->exec('USE ' . $db);
        //Prepare Statement
            if($iPageNumber == "")
                $iPageNumber = null;
            //CALL is sql for telling the db to execute the function following call.
            //The ? in the functions parameter list is a variable that we bind a few lines down
        $call = $this->getConn()->prepare("CALL SP_TEMPLATE_INDICES_DOCUMENT_INSERT(?,?,?,?,?,?,?,?)");
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        //bind variables into the above sql statement
        $call->bindParam(1, ($iLibraryIndex), PDO::PARAM_STR);
        $call->bindParam(2, ($iBookID), PDO::PARAM_INT);
        $call->bindParam(3, ($iPageType), PDO::PARAM_STR);
        $call->bindParam(4, ($iPageNumber), PDO::PARAM_INT);
        $call->bindParam(5, ($iComments), PDO::PARAM_STR);
        $call->bindParam(6, ($iNeedsReview), PDO::PARAM_INT);
        $call->bindParam(7, ($iFilename), PDO::PARAM_STR);
        $call->bindParam(8, ($iFilenamePath), PDO::PARAM_STR);

        //Execute Statement
        $ret = $call->execute();
        if($ret)
        {
            //select the last entered ID
            $select = $this->getConn()->query('SELECT LAST_INSERT_ID()');
            //return the ID of the last entered object
            $data = $select->fetch(PDO::FETCH_COLUMN);
            return $data;
        }
        else{
            print_r($call->errorInfo());
            return false;
        }

    }
    else return false;

    }
    /**********************************************
     * Function: SP_TEMPLATE_INDICES_DOCUMENT_UPDATE
     * Description: GIVEN collection name & document ID, Update information into the DB
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iDocID (in int) - the id of the document
     * $iLibraryIndex (in string) - the library index
     * $iBookID (in int) - the ID of the book
     * $iPageType (in string) - description of the page type
     * $iPageNumber (in int) - the page number of the page
     * $iComments (in string) - any comments on the page
     * $iNeedsReview (in int) - flag indicating if the page needs review
     * Return value(s):
     * $result True If success, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_INDICES_DOCUMENT_UPDATE($collection, $iDocID, $iLibraryIndex, $iBookID, $iPageType, $iPageNumber, $iComments, $iNeedsReview)
    {
        $db = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];

        if ($db != null && $db != "")
        {
            $this->getConn()->exec('USE ' . $db);
            //Prepare Statement
            if($iPageNumber == "")
                $iPageNumber = null;
            //CALL is sql for telling the db to execute the function following call.
            //The ? in the functions parameter list is a variable that we bind a few lines down
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_INDICES_DOCUMENT_UPDATE(?,?,?,?,?,?,?)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //bind parameters into variables for the above SQL statement
            $call->bindParam(1, ($iDocID), PDO::PARAM_INT);
            $call->bindParam(2, ($iLibraryIndex), PDO::PARAM_STR);
            $call->bindParam(3, ($iBookID), PDO::PARAM_INT);
            $call->bindParam(4, ($iPageType), PDO::PARAM_STR);
            $call->bindParam(5, ($iPageNumber), PDO::PARAM_INT);
            $call->bindParam(6, ($iComments), PDO::PARAM_STR);
            $call->bindParam(7, ($iNeedsReview), PDO::PARAM_INT);
            $ret = $call->execute();
            //Execute Statement
            if($ret)
                return true;

            else
                return print_r($call->errorInfo()[2]);
        }

    }

    /**********************************************
     * Function: GET_INDICES_MAPKIND
     * Description: Grabs the indices from mapkind db
     * Parameter(s):
     * collection (in string) - name of the collection
     * Return value(s):
     * $result True If success, or FALSE if failed
     ***********************************************/
    function GET_INDICES_MAPKIND($collection)
    {
            //get appropriate db
            $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
            $this->getConn()->exec('USE ' . $dbname);
            //select mapkindnames from the mapkind db and order them
            $sth = $this->getConn()->prepare("SELECT `mapkindname` FROM `mapkind` ORDER BY `mapkindname` ASC");
            $sth->execute();
            //get data
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
    }
//IN PROGRESS
    /**********************************************
     * Function: GET_MAPKIND_TABLE
     * Description: Gets a table of users.
     * Parameter(s): NONE
     * Return value(s):
     * $result  (associative array) - return associative array of Users
     ***********************************************/
    function GET_MAPKIND_TABLE($collection)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        $sth = $this->getConn()->prepare("SELECT `mapkindname`,`mapkindID` FROM `mapkind` ORDER BY `mapkindname` ASC ");
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    /**********************************************
     * Function: INSERT_INDICES_MAPKIND
     * Description: Inserts new mapkind
     * Parameter(s):
     * collection (in string) - name of the collection
     * Return value(s):    `mapkindname`
     * $result True If success, or FALSE if failed
     ***********************************************/
    function INSERT_INDICES_MAPKIND($new, $collection)
    {
            //get appropriate db
            $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
            $this->getConn()->exec('USE ' . $dbname);
            $sth = $this->getConn()->prepare("INSERT INTO `mapkind` (`mapkindID`, `mapkindname`) VALUES (NULL, :new)");
            $sth->bindParam(':new',$new,PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        }

    /**********************************************
     * Function: GET_INDICES_BOOK
     * Description: Grabs the indices from mapkind db
     * Parameter(s):
     * collection (in string) - name of the collection
     * Return value(s):
     * $result True If success, or FALSE if failed
     ***********************************************/
    function GET_INDICES_BOOK($collection)
    {
        //get appropriate DB
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE '. $dbname);
        //select the booknames and id's from book db
        $sth = $this->getConn()->prepare("SELECT `bookname`, `bookID`  FROM `book`");
        $sth->execute();
        //get data
        return $sth->fetchAll(PDO::FETCH_NUM);
    }

    /**********************************************
     * Function: SP_TEMPLATE_INDICES_DOCUMENT_CHECK_EXIST_RECORD
     * Description: Grabs the indices from mapkind db
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iLibraryIndex (in string) - the library index
     * Return value(s):
     * $result True If success, or FALSE if failed
     ***********************************************/
    function SP_TEMPLATE_INDICES_DOCUMENT_CHECK_EXIST_RECORD($collection, $iLibraryIndex)
    {
        //get appropriate db
        $db = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($db != null && $db != "")
        {
            $this->getConn()->exec('USE ' . $db);
            //CALL is sql for telling the db to execute the function following call.
            //The ? in the functions parameter list is a variable that we bind a few lines down
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_INDICES_DOCUMENT_CHECK_EXIST_RECORD(?, @oReturnValue)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //bind variables to the supplied sql statement above
            $call->bindParam(1, $iLibraryIndex, PDO::PARAM_STR);
            $call->execute();
            //select information from the db
            $select = $this->getConn()->query('SELECT @oReturnValue');
            //get data
            $ret = $select->fetch(PDO::FETCH_NUM);
            return (int)$ret[0];
        }
    }
    /**********************************************
     * Function: GET_INDICES_INFO
     * Description: Grabs the indices information
     * Parameter(s):
     * collection (in string) - name of the collection
     * $docID (in int) - the documentID
     * Return value(s):
     * $result True If success, or FALSE if failed
     ***********************************************/
    function GET_INDICES_INFO($collection, $docID)
    {
        //get the appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE '. $dbname);
        //select the information via the supplied docID
        $sth = $this->getConn()->prepare("SELECT `libraryindex`, `bookID`, `pagetype`, `pagenumber`, `needsreview` FROM `document` WHERE `documentID` = :docID");
        //bind variables to the suppllied sql statement
        $sth->bindParam( ':docID', $docID,PDO::PARAM_INT);
        $sth->execute();
        //get data
        return $sth->fetchAll(PDO::FETCH_NUM);
    }
    /**********************************************
     * Function: TRANSCRIPTION_ENTRY_INSERT
     * Description: Inserts a Transcription Entry into the DB
     * Parameter(s):
     * collection (in string) - name of the collection
     * $newObject (in object) - the documentID
     * Return value(s):
     * $result True If success, or FALSE if failed
     ***********************************************/
    public function TRANSCRIPTION_ENTRY_INSERT($collection,$newObject)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        //prepares an insert statement
        $sth = $this->getConn()->prepare("INSERT INTO `transcription` (`documentID`,`x1`, `y1`, `x2`, `y2`, `surveyorsection`, `blockortract`,
                                          `lotoracres`,`description`,`client`,`fieldbookinfo`,`relatedpapersfileno`,`mapinfo`,`date`,`jobnumber`,`comments`)
			VALUES (:docID,:x1,:y1,:x2,:y2,:surveyOrSection, :blockOrTract,:lotOrAcres,
			:description, :entryclient, :fieldBookInfo,:relatedPapersFileNo,:mapInfo,:entrydate,:jobNumber,:comments)");
        //bind DB variables into the above sql statement
        $sth->bindParam(':docID',$newObject->docID,PDO::PARAM_INT);
        $sth->bindParam(':x1',$newObject->x1,PDO::PARAM_INT);
        $sth->bindParam(':y1',$newObject->y1,PDO::PARAM_INT);
        $sth->bindParam(':x2',$newObject->x2,PDO::PARAM_INT);
        $sth->bindParam(':y2',$newObject->y2,PDO::PARAM_INT);
        $sth->bindParam(':surveyOrSection',$newObject->surveyOrSection,PDO::PARAM_STR);
        $sth->bindParam(':blockOrTract',$newObject->blockOrTract,PDO::PARAM_STR);
        $sth->bindParam(':lotOrAcres',$newObject->lotOrAcres,PDO::PARAM_STR);
        $sth->bindParam(':description',$newObject->description,PDO::PARAM_STR);
        $sth->bindParam(':entryclient',$newObject->client,PDO::PARAM_STR);
        $sth->bindParam(':fieldBookInfo',$newObject->fieldBookInfo,PDO::PARAM_STR);
        $sth->bindParam(':relatedPapersFileNo',$newObject->relatedPapersFileNo,PDO::PARAM_STR);
        $sth->bindParam(':mapInfo',$newObject->mapInfo,PDO::PARAM_STR);
        $sth->bindParam(':entrydate',$newObject->entryDate,PDO::PARAM_STR);
        $sth->bindParam(':jobNumber',$newObject->jobNumber,PDO::PARAM_STR);
        $sth->bindParam(':comments',$newObject->comments,PDO::PARAM_STR);
        $ret = $sth->execute();
        return $ret;
        
    }
    /**********************************************
     * Function: TRANSCRIPTION_ENTRY_UPDATE
     * Description: Updates an existing transcription entry
     * Parameter(s):
     * collection (in string) - name of the collection
     * $updateObject (in object) - object to be updated
     * Return value(s):
     * $result True If success, or FALSE if failed
     ***********************************************/
    public function TRANSCRIPTION_ENTRY_UPDATE($collection,$updateObject)
    {
        //get appropriate DB
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        //prepares an update sql statemetn
        $sth = $this->getConn()->prepare("UPDATE `transcription` SET
			`surveyorsection` = :surveyOrSection,
			`blockortract` = :blockOrTract,
			`lotoracres` = :lotOrAcres,
			`description` = :description,
			`client` = :entryclient,
			`fieldbookinfo` = :fieldBookInfo,
			`relatedpapersfileno` = :relatedPapersFileNo,
			`mapinfo` = :mapInfo,
			`date` = :entrydate,
			`jobnumber` = :jobNumber,
			`comments` = :comments
			WHERE `documentID` = :docID AND `x1` = :x1 AND `y1` = :y1 AND `x2` = :x2 AND `y2` = :y2");
        //bind variables into the above sql statement
        $sth->bindParam(':docID',$updateObject->docID,PDO::PARAM_INT);
        $sth->bindParam(':x1',$updateObject->x1,PDO::PARAM_INT);
        $sth->bindParam(':y1',$updateObject->y1,PDO::PARAM_INT);
        $sth->bindParam(':x2',$updateObject->x2,PDO::PARAM_INT);
        $sth->bindParam(':y2',$updateObject->y2,PDO::PARAM_INT);
        $sth->bindParam(':surveyOrSection',$updateObject->surveyOrSection,PDO::PARAM_STR);
        $sth->bindParam(':blockOrTract',$updateObject->blockOrTract,PDO::PARAM_STR);
        $sth->bindParam(':lotOrAcres',$updateObject->lotOrAcres,PDO::PARAM_STR);
        $sth->bindParam(':description',$updateObject->description,PDO::PARAM_STR);
        $sth->bindParam(':entryclient',$updateObject->client,PDO::PARAM_STR);
        $sth->bindParam(':fieldBookInfo',$updateObject->fieldBookInfo,PDO::PARAM_STR);
        $sth->bindParam(':relatedPapersFileNo',$updateObject->relatedPapersFileNo,PDO::PARAM_STR);
        $sth->bindParam(':mapInfo',$updateObject->mapInfo,PDO::PARAM_STR);
        $sth->bindParam(':entrydate',$updateObject->entryDate,PDO::PARAM_STR);
        $sth->bindParam(':jobNumber',$updateObject->jobNumber,PDO::PARAM_STR);
        $sth->bindParam(':comments',$updateObject->comments,PDO::PARAM_STR);
        $ret = $sth->execute();
        return $ret;

    }

    /**********************************************
     * Function: TRANSCRIPTION_ENTRY_SELECT
     * Description: Selects a transcription entry in the db
     * Parameter(s):
     * collection (in string) - name of the collection
     * $docID (in int) - the id of the document
     * $x1 (in int) - x value of the box selecting a piece of information on the page
     * $y1 (in int) - y value of the box selecting a piece of information on the page
     * $x2 (in int) - x2 value of the box selecting a piece of information on the page
     * $y2 (in int) - y2 value of the box selecting a piece of information on the page
     * Return value(s):
     * $result True If success, or FALSE if failed
     ***********************************************/
    public function TRANSCRIPTION_ENTRY_SELECT($collection,$docID,$x1,$y1,$x2,$y2)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepare a select sql statement
        $sth = $this->getConn()->prepare("SELECT `documentID`,`x1`,`y1`,`x2`,`y2`,`surveyorsection`,`blockortract`,`lotoracres`,
			`description`, `client`, `fieldbookinfo`, `relatedpapersfileno`,
			`mapinfo`, `date`, `jobnumber`,`comments` FROM `transcription` WHERE `documentID` = :docID AND `x1` = :x1 AND `y1` = :y1 AND `x2` = :x2 AND `y2` = :y2 LIMIT 1");
            //bind variables to the supplied sql statement
            $sth->bindParam(':docID',$docID,PDO::PARAM_INT,11);
            $sth->bindParam(':x1',$x1,PDO::PARAM_INT,11);
            $sth->bindParam(':x2',$x2,PDO::PARAM_INT,11);
            $sth->bindParam(':y1',$y1,PDO::PARAM_INT,11);
            $sth->bindParam(':y2',$y2,PDO::PARAM_INT,11);
            $sth->execute();
            //return an array of information
            return $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }
}
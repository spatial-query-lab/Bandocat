<?php
/**********************************************
Function:
Description:
Parameter(s):
Return value(s):
 ***********************************************/
class FolderDBHelper extends DBHelper
{

    /**********************************************
     * Function: SP_TEMPLATE_FOLDER_DOCUMENT_INSERT
     * Description: Creates a new folder document in the template folder db then returns the id if successful
     * Parameter(s):
     * $collection (in String) - the collection name
     * $iLibraryIndex (in String) - the index of the document
     * $iTitle (in String) - the title of the document
     * $iFileName (in String) - the filename associated with the document
     * $iFileNameBack (in String) -
     * $iFileNamePath (in String) - the filepath to the file
     * $iFileNameBackPath (in String) -
     * Return value(s):
     ***********************************************/
    function SP_TEMPLATE_FOLDER_DOCUMENT_INSERT($collection, $iLibraryIndex, $iTitle,$iFileName, $iFileNameBack,
                                                $iFileNamePath,$iFileNameBackPath)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "")
        {
            $this->getConn()->exec('USE ' . $dbname);
            if($iFileNameBack == "")
            {
                $iFileNameBack = null;
                $iFileNameBackPath = null;
            }
            if($iFileName == "")
            {
                $iFileName = null;
                $iFileNamePath = null;
            }
            /* PREPARE STATEMENT */
            //CALL is sql for telling the db to execute the function following call.
            //The ? in the functions parameter list is a variable that we bind a few lines down
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_FOLDER_DOCUMENT_INSERT(:libindex,:title,:fname,
            :fnameback,:fnamepath,:fnamebackpath)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //bind variables to the prepared  sql statement above
            $call->bindParam(':libindex', $iLibraryIndex, PDO::PARAM_STR, 200);
            $call->bindParam(':title', $iTitle, PDO::PARAM_STR, 350);
            $call->bindParam(':fname', $iFileName, PDO::PARAM_STR, 80);
            $call->bindParam(':fnameback', $iFileNameBack, PDO::PARAM_STR, 80);
            $call->bindParam(':fnamepath', $iFileNamePath, PDO::PARAM_STR, 200);
            $call->bindParam(':fnamebackpath', $iFileNameBackPath, PDO::PARAM_STR, 200);

            /* EXECUTE STATEMENT */
            $ret = $call->execute();
            if($ret)
            {
                //get the last inserted id()
                $select = $this->getConn()->query('SELECT LAST_INSERT_ID()');
                $ret = $select->fetch(PDO::FETCH_COLUMN);
                return $ret;
            }
            return 0;
        } else return false;
    }


    /**********************************************
     * Function: SP_TEMPLATE_FOLDER_DOCUMENT_UPDATE
     * Description: Updates a specific entry in the db
     * Parameter(s):
     * $collection (in String) - name of the collection
     * $iDocID (in String) - documentID in the collection
     * $iLibraryIndex (in String) - the library index
     * $iTitle (in String) - the title of the document
     * $iInSubfolder (in Int) - flag if it is in a subfolder
     * $iSubfolderComment (in String) - comments for the subfolder
     * $iClassificationName (in String) - classification name
     * $iClassificationComment (in String) - classification comments
     * $iNeedsInput (in Int) - flag if the document needs input
     * $iNeedsReview (in Int) - flag if the document needs review
     * $iComments (in String) - general comments
     * $iStartDate (in String) - job start date (document)
     * $iEndDate (in String) - job end date (document)
     * Return value(s): true if succcessful, false if failed
     ***********************************************/
    function SP_TEMPLATE_FOLDER_DOCUMENT_UPDATE($collection,$iDocID,$iLibraryIndex, $iTitle, $iInSubfolder,
                                                $iSubfolderComment, $iClassificationName, $iClassificationComment,
                                                $iNeedsInput,$iNeedsReview,$iComments, $iStartDate, $iEndDate)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "")
        {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            //CALL is sql for telling the db to execute the function following call.
            //The ? in the functions parameter list is a variable that we bind a few lines down
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_FOLDER_DOCUMENT_UPDATE(:docID,:libindex,:title,:insub,:subcomment,:classname,:classcom,
            :input,:review,:comm,:startdate,:enddate)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //bind variables to the above sql statement
            $call->bindParam(':docID', $iDocID, PDO::PARAM_INT, 200);
            $call->bindParam(':libindex', $iLibraryIndex, PDO::PARAM_STR, 200);
            $call->bindParam(':title', $iTitle, PDO::PARAM_STR, 350);
            $call->bindParam(':insub',$iInSubfolder,PDO::PARAM_INT,1);
            $call->bindParam(':subcomment',$iSubfolderComment,PDO::PARAM_STR,300);
            $call->bindParam(':classname',$iClassificationName,PDO::PARAM_STR,100);
            $call->bindParam(':classcom',$iClassificationComment,PDO::PARAM_STR,400);
            $call->bindParam(':input',$iNeedsInput,PDO::PARAM_INT,1);
            $call->bindParam(':review', $iNeedsReview, PDO::PARAM_INT, 1);
            $call->bindParam(':comm', $iComments, PDO::PARAM_STR,500);
            $call->bindParam(':startdate', $iStartDate, PDO::PARAM_STR, 10);
            $call->bindParam(':enddate', $iEndDate, PDO::PARAM_STR, 10);
            /* EXECUTE STATEMENT */
            $ret = $call->execute();
            return $ret;
        } else return false;
    }



    /**********************************************
     * Function: SP_TEMPLATE_FOLDER_DOCUMENT_SELECT
     * Description: Updates a specific entry in the db
     * Parameter(s):
     * $collection (in String) - name of the collection
     * $iDocID (in String) - documentID in the collection
     * Return value(s): returns array of selected values
     ***********************************************/
    function SP_TEMPLATE_FOLDER_DOCUMENT_SELECT($collection, $iDocID)
    {
        //get appropriate DB
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "")
        {
            $this->getConn()->exec('USE ' . $dbname);
            /* PREPARE STATEMENT */
            //CALL is sql for telling the db to execute the function following call.
            //The ? in the functions parameter list is a variable that we bind a few lines down
            $call = $this->getConn()->prepare("CALL SP_TEMPLATE_FOLDER_DOCUMENT_SELECT(?,@oLibraryIndex,@oTitle,@oInSubfolder,@oSubfolderComment,@oClassification,@oClassificationComment,@oFileName,@oFileNameBack,@oNeedsInput,@oNeedsReview,@oFileNamePath,@oFileNameBackPath,@oComments,@oStartDate,@oEndDate)");
            if (!$call)
                trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
            //bind variables to the above sql statement
            $call->bindParam(1, $iDocID, PDO::PARAM_INT, 11);
            /* EXECUTE STATEMENT */
            $call->execute();
            /* RETURN RESULT */
            //select the values we want returned
            $select = $this->getConn()->query('SELECT @oLibraryIndex AS LibraryIndex,@oTitle AS Title,@oInSubfolder AS InSubfolder,@oSubfolderComment AS SubfolderComment,@oClassification AS Classification,@oClassificationComment AS ClassificationComment,@oFileName AS FileName,@oFileNameBack AS FileNameBack,@oNeedsInput AS NeedsInput,@oNeedsReview AS NeedsReview,@oFileNamePath AS FileNamePath,@oFileNameBackPath AS FileNameBackPath,@oComments AS Comments,@oStartDate AS StartDate,@oEndDate AS EndDate');
            //return values as array
            $result = $select->fetch(PDO::FETCH_ASSOC);
            return $result;
        } else return false;
    }



    /**********************************************
     * Function: SP_TEMPLATE_FOLDER_DOCUMENTAUTHOR_INSERT
     * Description: GIVEN document ID & array of AuthorName, delete from documentauthor first,
     * then call SP to select/insert from author table, then insert into documentauthor
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iDocID (in Integer) - document ID
     * $iAuthorNameArray (in Array) - Array of Author names
     * Return value(s):
     * True if good, False if fail
     ***********************************************/

    function SP_TEMPLATE_FOLDER_DOCUMENTAUTHOR_INSERT($collection,$iDocID,$iAuthorNameArray)
    {
        //DELETE FROM DOCUMENTAUTHOR BEFORE INSERTING
        $ret = $this->TEMPLATE_FOLDER_DELETE_DOCUMENTAUTHOR($collection,$iDocID);
        if($ret) {
            if (!$ret)
                return false;
            //INSERT VALUES INTO DOCUMENTAUTHOR, IF VALUE DOES NOT EXIST IN AUTHOR TABLE, INSERT INTO AUTHOR FIRST
            foreach ($iAuthorNameArray as $iAuthor)
            {
                if($iAuthor != "")
                {
                    //CALL is sql for telling the db to execute the function following call.
                    //The ? in the functions parameter list is a variable that we bind a few lines down
                    $call = $this->getConn()->prepare("CALL SP_TEMPLATE_FOLDER_DOCUMENTAUTHOR_INSERT(:docID,:author)");
                    if (!$call)
                    {
                        trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
                        return false;
                    }
                    //bind parameters into the above sql statement
                    $call->bindParam(':docID', ($iDocID), PDO::PARAM_INT);
                    $call->bindParam(':author', ($iAuthor), PDO::PARAM_STR);
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
     * Function: TEMPLATE_FOLDER_DELETE_DOCUMENTAUTHOR
     * Description: deletes the documents author from the documentID supplied
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iDocID (in Integer) - document ID
     * Return value(s):
     * True if good, False if fail
     ***********************************************/
    function TEMPLATE_FOLDER_DELETE_DOCUMENTAUTHOR($collection,$iDocID)
    {
        //get appropriate db
        $db= $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $db);
        //DELETE FROM DOCUMENTAUTHOR BEFORE INSERTING
        $sth = $this->getConn()->prepare("DELETE FROM `documentauthor` WHERE `documentauthor`.`docID` = :docID");
        $sth->bindParam(':docID', ($iDocID), PDO::PARAM_INT);
        $ret = $sth->execute();
        if($ret)
            return true;
        return false;
    }
    /**********************************************
     * Function: GET_FOLDER_AUTHORS_BY_DOCUMENT_ID
     * Description: returns the authors of the folder by the supplied document id
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iDocID (in Integer) - document ID
     * Return value(s):
     * True if good, False if fail
     ***********************************************/
    function GET_FOLDER_AUTHORS_BY_DOCUMENT_ID($collection,$iDocID)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepares a select statement to compare author names with the documentID
            $sth = $this->getConn()->prepare("SELECT a.`authorname` FROM `documentauthor` AS da LEFT JOIN  `author` AS a ON da.`authorID` = a.`authorID` WHERE da.`docID` = ? ");
            //bind variables to the sql statement above
            $sth->bindParam(1, $iDocID, PDO::PARAM_INT, 11);
            $sth->execute();
            //return array of selected authors
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }


    /**********************************************
     * Function: TEMPLATE_FOLDER_CHECK_EXIST_RECORD_BY_FILENAME
     * Description: check if the records exist by filename
     * Parameter(s):
     * collection (in string) - name of the collection
     * $iFileName (in string) - filename
     * Return value(s):
     * True if good, False if fail
     ***********************************************/
    public function TEMPLATE_FOLDER_CHECK_EXIST_RECORD_BY_FILENAME($collection, $iFileName)
    {
        //get appropriate db
        $db = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($db != null && $db != "")
        {
            $this->getConn()->exec('USE ' . $db);
            //strpos finds the position of the first occurrence of a substring in a string
            if(strpos($iFileName,"back") !== false)
                $sth = $this->getConn()->prepare("SELECT COUNT(*) FROM `document` WHERE `filenameback` = :filename");
            else $sth = $this->getConn()->prepare("SELECT COUNT(*) FROM `document` WHERE `filename` = :filename");
            //binds the variable to the above sql statement
            $sth->bindParam(':filename',$iFileName,PDO::PARAM_STR);
            $sth->execute();
            $result = $sth->fetchColumn();
            return $result;
        }
        else return false;
    }
    /**********************************************
     * Function: GET_FOLDER_CLASSIFICATION_LIST
     * Description: returns the folders classifications
     * Parameter(s):
     * collection (in string) - name of the collection
     * Return value(s):
     * True if good, False if fail
     ***********************************************/
    function GET_FOLDER_CLASSIFICATION_LIST($collection)
    {
        //get appropriate DB
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepares a select statement to select the classifications and descriptions from the classification table
            $sth = $this->getConn()->prepare("SELECT `classificationname`,`classificationdescription` FROM `classification`");
            $sth->execute();

            //returns the classifications and their descriptions
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }
	/**********************************************
     * Function: GET_FOLDERS
     * Description: retrieve unique FOLDERS in `foldername` field of `document` table in the targeted collection
     * Parameter(s):
     * $collection (in string) - name of the collection
     * Return value(s):
     * false if fail, else return an array of folder names
     ***********************************************/
    function GET_FOLDERS($collection)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //selects the fieldbook collection names from the fieldbook collection
            $sth = $this->getConn()->prepare("SELECT DISTINCT `foldername` FROM `document` WHERE `foldername` ORDER BY `foldername` ASC");
            $sth->execute();
            //return the collection names
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }
	/**********************************************
     * Function: UPDATE_FIELDBOOK_READYFORPDF
     * Description: attempts to get the list of fieldbook collection names
     * Parameter(s):
     * $collection (in string) - name of the collection
     * Return value(s):
     * True if good, False if fail
     ***********************************************/
    function UPDATE_JOBFOLDER_READYFORPDF($collection, $iFolderName,$iReadyForPdf)
    {
            $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection));
            $db = $dbname['DbName'];
            if ($db != null && $db != "")
            {
                $this->getConn()->exec('USE ' . $db);

                $call = $this->getConn()->prepare("CALL SP_TEMPLATE_JOBFOLDER_DOCUMENT_READYFORPDF_UPDATE(:iFolderName,:iReadyForPdf)");
                if (!$call)
                    trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
                //bind parameters into variables for the above SQL statement
                $call->bindParam(':iFolderName', ($iFolderName), PDO::PARAM_STR);
                $call->bindParam(':iReadyForPdf', ($iReadyForPdf), PDO::PARAM_INT);

                $ret = $call->execute();
                //Execute Statement

                if($ret)
                    return true;
                return false;
            }

    }
	 /**********************************************
     * Function: GET_ALL_FOLDER_FILENAMES_BY_FOLDERNAME
     * Description: attempts to get the list of fieldbook collection names
     * Parameter(s):
     * $collection (in string) - name of the collection
     * Return value(s):
     * True if good, False if fail
     ***********************************************/
    function GET_ALL_FOLDER_FILENAMES_BY_FOLDERNAME($collection,$foldername)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //selects the fieldbook collection names from the fieldbook collection
            $sth = $this->getConn()->prepare("SELECT `filename` FROM `document` WHERE `foldername` = :foldername");
            $sth->bindParam(':foldername',$foldername,PDO::PARAM_STR);
            $sth->execute();
            //return the collection names
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }
	/**********************************************
     * Function: GET_FIELDBOOK_CREWS_AND_PARTIAL_DOCUMENT_BY_BOOKTITLE_AND_DOCID
     * Description: Returns the information from the documents needed for PDF generation + lists all crew members associated with that document in column[0].
     * Parameter(s): booktitle
     * $collection (in string) - name of the collection
     * $booktitle (in Int) - document booktitle
     * Return value(s):
     * Array of possible values if success
     ***********************************************/
    function GET_FOLDER_AUTHOR_AND_PARTIAL_DOCUMENT_BY_FOLDERNAME($collection,$foldername)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //selects the fieldbook collection names from the fieldbook collection
            //$sth = $this->getConn()->prepare("SELECT libraryindex, jobnumber,startdate,enddate,indexedpage,filenamepath,jobtitle FROM `document` WHERE `booktitle` = :booktitle ORDER BY `jobnumber`");
            $sth = $this->getConn()->prepare("SELECT (SELECT GROUP_CONCAT(a.`authorname`) FROM `documentauthor` AS da LEFT JOIN `author` AS a ON da.`authorID` = a.`authorID` WHERE da.`docID` IN (SELECT documentID WHERE `foldername` = :foldername ) GROUP BY da.`docID`) as `authorlist`,libraryindex,insubfolder,startdate,enddate,filenamepath,filenamebackpath,foldername,cc.classificationname FROM `document` LEFT JOIN `classification` AS cc ON document.classificationID = cc.`classificationID` WHERE `foldername` = :foldername ");
            $sth->bindParam(':foldername',$foldername,PDO::PARAM_STR);
            $sth->execute();
            //return the collection names
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;

    }
	
	

}
<?php
//NOTE:
/*
- BEFORE EXECUTING QUERIES OR CALL SPs, MUST EITHER:
    + Use prepare statement
    + OR Sanitize input using mysqli_real_escape_string

- BIND PARAMETER: Types: s = string, i = integer, d = double,  b = blob
*/

//PLEASE USE THIS TEMPLATE TO COMMENT ON FUNCTIONS
/**********************************************
Function:
Description:
Parameter(s):
Return value(s):
 ***********************************************/

require_once 'TDLPublishDB.php';

class DBHelper
{
    use TDLPublishTrait;
    //Members
     static protected $ini_dir = "Bandocat_config\\bandoconfig.ini";
     protected $host;
     protected $user;
     protected $pwd;
     protected $maindb;
     protected $conn;

    //Getter and setters

    function __construct()
    {
     $root = substr(getcwd(),0,strpos(getcwd(),"htdocs\\")); //point to xampp// directory
     $config = parse_ini_file($root . DBHelper::$ini_dir);
     $this->host = $config['servername'];
     $this->user = $config['username'];
     $this->pwd = $config['password'];
     $this->maindb = $config['dbname'];
        /*if not currently connected, attempt to connect to DB*/
        if ($this->getConn() == null)
            $this->DB_CONNECT(null);
    }

    /**
     * @return mixed
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * @param mixed $conn
     */
    public function setConn($conn)
    {
        $this->conn = $conn;
    }

    /**********************************************
     * Function: DB_CONNECT
     * Description: Connect to XAMPP db. localhost/phpadmin All functions connect to db using this.
     * Parameter(s):
     * $db (string) - name of database to be connected to
     * Return value(s):
     * $conn (object) - return connection object if success
     * - return -1 if failed
     ***********************************************/

    function DB_CONNECT($db)
    {
        if ($db == "" || $db == null) //empty parameter = default = bandocatdb
            $db = "bandocatdb";
        /* assign conn as a PHP Data Object, concat the host, user and pwd */
        $this->conn = new PDO('mysql:host=' . $this->getHost() . ';dbname=' . $db, $this->getUser(), $this->getPwd());
        return 0;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }


    //Constructor

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    //IMPORTANT: Default Host, username, password can be changed here!

    /**
     * @return string
     */
    public function getPwd()
    {
        return $this->pwd;
    }

    function DB_CLOSE()
    {
        $this->setConn(null);
    }

    /**********************************************
    Function: SWITCH_DB
    Description:This function switches the current connection to the database specified in the parameter
    Parameter(s):
     *$collection (string) - db parameter name (such as bluchermaps, greenmaps)
    Return value(s): true if success, false if error occurs
     ***********************************************/
    public function SWITCH_DB($collection)
    {
        if($collection == null || $collection == "") //or == maindb
            $this->getConn()->exec('USE ' . self::$maindb);
        //get appropriate database
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            return true;
        }
        return false;
    }

    /* DEPRECATED */
    /**********************************************
     * Function: SP_USER_AUTH
     * Description: USER LOGIN AUTHENTICATION
     * Parameter(s):
     * $iUsername (in string) - input username
     * $iPassword (in string) - input password (before md5)
     * &$oMessage (out ref string) - output message response
     * &$oUserID (out ref int) - output UserID if success
     * &$oRole (out ref int) - output User Role if success
     * Return value(s): NONE
     ***********************************************/

//    function SP_USER_AUTH($iUsername, $iPassword, &$oMessage, &$oUserID, &$oRole)
//    {
//        /* PREPARE STATEMENT */
//        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
//        //The ? in the functions parameter list is a variable that we bind a few lines down.
//        $call = $this->getConn()->prepare("CALL SP_USER_AUTH(?,?,@oMessage,@oUserID,@oRole)");
//        //Encrypts the password using built in function md5. Uses md5 Hash.
//        $iPassword = md5($iPassword);
//        //ERROR HANDLEING
//        if (!$call)
//            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
//        /* bindParam is used to attach variables to the SQL query */
//        $call->bindParam(1, $iUsername, PDO::PARAM_STR, 32);
//        $call->bindParam(2, $iPassword, PDO::PARAM_STR, 64);
//
//        /* EXECUTE STATEMENT */
//        $call->execute();
//        /* RETURN RESULT */
//        //returned after successful call statement. Need to tell the db what data we want selected.
//        $select = $this->getConn()->query('SELECT @oMessage,@oUserID, @oRole');
//        /* returning the selected data */
//        $result = $select->fetch(PDO::FETCH_ASSOC);
//        /*store data into variables */
//        $oMessage = $result['@oMessage'];
//        $oUserID = $result['@oUserID'];
//        $oRole = $result['@oRole'];
//    }

    /**********************************************
     * Function: SP_GET_COLLECTION_CONFIG (SP_GET_COLLECTION_DATA)
     * Description: Pulls the data associated with the collection name passed in.
     * Parameter(s):
     * $iName (in string) - input DB Name
     * Return value(s):
     * $result (assoc array) - return above values in an assoc array
     ***********************************************/
    function SP_GET_COLLECTION_CONFIG($iName)
    {   //USE is sql for changing to the database supplied after the concat.
        $this->getConn()->exec('USE ' . $this->maindb);
        /* PREPARE STATEMENT */
        //CALL is sql for telling the db to execute the function following call.
        //The ? in the functions parameter list is a variable that we bind a few lines down
        $call = $this->getConn()->prepare("CALL SP_GET_COLLECTION_CONFIG(?,@oDisplayName,@oDbName,@oStorageDir,@oPublicDir,@oThumbnailDir,@oTemplateID,@oGeorecDir,@oCollectionID)");
        //ERROR HANDLING
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        //Bind the database name ($iName) into the prepared call statement from above.
        $call->bindParam(1, $iName, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 50);
        /* EXECUTE STATEMENT */
        $call->execute();
        /* RETURN RESULT */
        //returned after successful call statement. Need to tell the db what data we want selected.
        $select = $this->getConn()->query('SELECT @oDisplayName AS DisplayName,@oDbName AS DbName,@oStorageDir AS StorageDir,@oPublicDir AS PublicDir,@oThumbnailDir AS ThumbnailDir,@oTemplateID as TemplateID,@oGeorecDir as GeoRecDir,@oCollectionID as CollectionID');
        //Database now has the appropriate data selected. We now need to ask the DB to fetch the values for us
        $result = $select->fetch(PDO::FETCH_ASSOC);
        $result["Name"] = htmlspecialchars($iName);
        return $result;
    }

    /**********************************************
     * Function: USER_AUTH
     * Description: USER LOGIN AUTHENTICATION (not calling a stored procedure)
     * Parameter(s):
     * $iUsername (in string) - input username
     * $iPassword (in string) - input password (before md5)
     * &$oMessage (out ref string) - output message response
     * &$oUserID (out ref int) - output UserID if success
     * &$oRole (out ref int) - output User Role if success
     * Return value(s): false if not success, true if success
     ***********************************************/
    function USER_AUTH($iUsername, $iPassword, &$oMessage, &$oUserID, &$oRole)
    {
        $iUsername = htmlspecialchars($iUsername);
        $iPassword = md5(htmlspecialchars($iPassword));
         $sth = $this->getConn()->prepare("SELECT `userID`,`role`.`name` AS role,`password` FROM `user` LEFT JOIN `role` ON `role`.`roleID` = `user`.`roleID` WHERE `username` = :username LIMIT 1");
         $sth->bindParam(':username',$iUsername,PDO::PARAM_STR);
         $ret = $sth->execute();
         if(!$ret)
         {
             $oMessage = "Error";
             return false;
         }
         $output = $sth->fetch(PDO::FETCH_ASSOC);
         if(password_verify($iPassword,$output['password']))
         {


             if($output['role'] === "Inactive")
             {
                 $oMessage = "Inactive";
                 return false;
             }
             $oMessage = "Success";
             $oUserID = $output['userID'];
             $oRole = $output['role'];
             return true;
         }
         $oMessage = "Invalid";
         return false;

    }

    /**********************************************
     * Function: GET_USER_INFO
     * Description: GIVEN USERID, RETURN USER INFO - Note: for future modification, do not include password in the output
     * Parameter(s):
     * $userID (in int) - user ID
     * Return value(s):
     * $result  (associative array) - return associative array of user info
     ***********************************************/
    function GET_USER_INFO($userID)
    {
        //USE is sql for changing to the database supplied after the concat.
        $this->getConn()->exec('USE' . $this->maindb);
        //r.name = role name
        //Select all relevant data to the supplied userID excluding password
        $sth = $this->getConn()->prepare("SELECT `username`,`fullname`,`email`,r.`name` FROM `user` LEFT JOIN `role` AS r ON r.`roleID` = `user`.`roleID` WHERE `userID` = :userID LIMIT 1");
        //Bind the supplied userID into the select statement above.
        $sth->bindParam(':userID',$userID,PDO::PARAM_INT);
        //Execute SQL statement
        $sth->execute();
        //Retrieve results from executed statement
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
//IN PROGRESS
    /**********************************************
     * Function: GET_ACTIVE USER_TABLE
     * Description: Gets a table of users.
     * Parameter(s): NONE
     * Return value(s):
     * $result  (associative array) - return associative array of Users
     ***********************************************/
    function GET_ACTIVE_USER_TABLE()
    {
        $this->getConn()->exec('USE' . $this->maindb);
        $sth = $this->getConn()->prepare("SELECT `username`,`userID`,`email`,r.`name` FROM `user` LEFT JOIN `role` AS r ON r.`roleID` = `user`.`roleID` WHERE  NOT `user`.`roleID`=0 ORDER BY `username` ASC ");
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    /**********************************************
     * Function: GET_COLLECTION_FOR_DROPDOWN
     * Description: Retrieves the collection names from the database to be used as items in a drop down list
     * Parameter(s): NONE
     * Return value(s):
     * $result  (associative array) - return associative array of collection info names
     ***********************************************/
    function GET_COLLECTION_FOR_DROPDOWN()
    {
        $this->getConn()->exec('USE' . $this->maindb);
        $sth = $this->getConn()->prepare("SELECT `collectionID`,`name`,`displayname` FROM `collection`");
        $sth->execute();

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**********************************************
     * Function: GET_COLLECTION_FOR_DROPDOWN
     * Description: Retrieves the collection names from the database to be used as items in a drop down list
     * Parameter(s): NONE
     * Return value(s):
     * $result  (associative array) - return associative array of collection info names
     ***********************************************/
    function GET_COLLECTION_FOR_DROPDOWN_FROM_TEMPLATEID($iATemplateID, $iSwitch)
    {

        $this->getConn()->exec('USE' . $this->maindb);
//        if($iSwitch == null)
//        {
//            //if the user failed to supply an indicator supply true to find exact what is passed
//            $iSwitch = true;
//        }
        $original = "SELECT `collectionID`,`name`,`displayname` FROM `collection` WHERE ";
        $count = 0;
        for($i = 1; $i <= count($iATemplateID); $i++)
        {
            $count++;
            if($iSwitch == true)
            {
                $original = $original . "templateID = ? ";
                if($i != count($iATemplateID))
                {
                    $original = $original . " OR ";
                }
            }
            if($iSwitch == false)
                {
                    $original = $original . "templateID != ? ";
                    if($i != count($iATemplateID))
                    {
                        $original = $original . " OR ";
                    }
                }
        }
        $sth = $this->getConn()->prepare($original);
        for($i = 1; $i <= $count; $i++)
        {
            $sth->bindParam($i,$iATemplateID[$i - 1], PDO::PARAM_INT);
        }

        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**********************************************
    /**********************************************

     ***********************************************/
    function GET_ACTION_UNIQUE()
    {
        $this->getConn()->exec('USE' . $this->maindb);
        $sth = $this->getConn()->prepare("SELECT DISTINCT `action` FROM `log`");
        $sth->execute();

        $result = $sth->fetchAll(PDO::FETCH_NUM);
        return $result;
    }

    /**********************************************
     * Function: GET_COLLECTION_TABLE
     * Description: Gets the table in the db that points to all available databases.
     * Parameter(s): NONE
     * Return value(s):
     * $result  (associative array) - return associative array of collection infos
     ***********************************************/
    function GET_COLLECTION_TABLE()
    {
        $this->getConn()->exec('USE' . $this->maindb);
        $sth = $this->getConn()->prepare("SELECT * FROM `collection`");
        $sth->execute();

        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**********************************************
     * Function: GET_USER_ROLE_FOR_DROPDOWN
     * Description: GET USERS ROLE INFO FOR DROPDOWN CONTROL
     * Parameter(s): NONE
     * Return value(s):
     * $result  (associative array) - return associative array of role info
     ***********************************************/
    function GET_USER_ROLE_FOR_DROPDOWN()
    {
        $this->getConn()->exec('USE' . $this->maindb);
        $call = $this->getConn()->prepare("SELECT `roleID`,`name`, `description` FROM `bandocatdb`.`role`");
        $call->execute();

        $role = $call->fetchAll(PDO::FETCH_ASSOC);
        return $role;
    }

    /**********************************************
     * Function: GET_USER_ROLE
     * Description: GIVEN USERID, RETURN USER ROLE - Note: for future modification, do not include password in the output
     * Parameter(s):
     * $userID (in int) - user ID
     * Return value(s):
     * $result  (associative array) - return associative array of user info
     ***********************************************/
    function GET_USER_ROLE($userID)
    {
        //USE is sql for changing to the database supplied after the concat.
        $this->getConn()->exec('USE' . $this->maindb);
        //Select all relevant data to the supplied userID excluding password
        $sth = $this->getConn()->prepare("SELECT `user`.`userID`,`user`.`roleID`, `role`.`name` FROM `user` INNER JOIN `role` ON `user`.`roleID` = `role`.`roleID` WHERE `userID` = :userID");
        //Bind the supplied userID into the select statement above.
        $sth->bindParam(':userID',$userID,PDO::PARAM_INT);
        //Execute SQL statement
        $sth->execute();
        //Retrieve results from executed statement
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result['name'];
    }
    /**********************************************
     * Function: USER_ROLE_UPDATE
     * Description: UPDATES THE USERS ROLE
     * Parameter(s): NONE
     * Return value(s):
     * $result  (associative array) - return associative array of role info
     ***********************************************/
    function USER_ROLE_UPDATE($iUserID,$iNewRole)
    {
        $this->getConn()->exec('USE' . $this->maindb);
        $sth = $this->getConn()->prepare("UPDATE `user` SET `roleID` = :newrole WHERE `userID` = :uID LIMIT 1");
        $sth->bindParam(':newrole',$iNewRole,PDO::PARAM_STR);
        $sth->bindParam(':uID',$iUserID,PDO::PARAM_INT);
        $ret=$sth->execute();
        if($ret)
            $ret = $sth->rowCount(); //return number of rows affected (must be 1 or 0)
        return $ret;
    }
    /**********************************************
     * Function: GET_USER_ROLE_FOR_DROPDOWN
     * Description: GET USERS ROLE INFO FOR DROPDOWN CONTROL
     * Parameter(s): NONE
     * Return value(s):
     * $result  (associative array) - return associative array of role info
     ***********************************************/
    function USER_SELECT($bActive)
    {
        //select only active USERs
        if($bActive == true)
        {
            $this->getConn()->exec('USE' . $this->maindb);
            $call = $this->getConn()->prepare("SELECT `userID`,`username` FROM `user` WHERE `roleID` != 0");
            $call->execute();

            $role = $call->fetchAll(PDO::FETCH_ASSOC);
            return $role;
        }else
        {
            $this->getConn()->exec('USE' . $this->maindb);
            $call = $this->getConn()->prepare("SELECT `userID`,`username` FROM `user`");
            $call->execute();

            $role = $call->fetchAll(PDO::FETCH_ASSOC);
            return $role;
        }

    }

    /**********************************************
     * Function: SP_TICKET_INSERT
     * Description: Inserts a ticket into the DB when a user submits a ticket
     * Parameter(s):
     * $iSubject (in string) - Subject or library index
     * $iPosterID (in int) - userID of submitter
     * $iCollectionID (in int) - collectionID in which the ticket submit for
     * $iDescription (in string) - description of what goes wrong
     * Return value(s): true if success, false if fail
     ***********************************************/
    function SP_TICKET_INSERT($iSubject, $iPosterID, $iCollectionID, $iDescription, $iLibraryIndex)
    {
        //Switch to correct DB
        $this->getConn()->exec('USE ' . $this->maindb);
        /* PREPARE STATEMENT */
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        //The ? in the functions parameter list is a variable that we bind a few lines down.
        //CALL is sql for calling the function built into the db at localhost/phpmyadmin
        $call = $this->getConn()->prepare("CALL SP_TICKET_INSERT(?,?,?,?,?)");
        //Error handling
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        //bind parameters to the sql statement
        $call->bindParam(1, $iSubject, PDO::PARAM_STR, strlen($iSubject));
        $call->bindParam(2, $iPosterID, PDO::PARAM_INT);
        $call->bindParam(3, $iCollectionID, PDO::PARAM_INT);
        $call->bindParam(4, $iDescription, PDO::PARAM_STR, strlen($iDescription));
        $call->bindParam(5, $iLibraryIndex, PDO::PARAM_STR, strlen($iLibraryIndex));
        /* EXECUTE STATEMENT */
        $call->execute();
        if ($call)
            return true;
        return false;
    }

    /**********************************************
     * Function: TICKET_UPDATE
     * Description: UPDATE TICKET ON ADMIN Submitting an updated ticket
     * Parameter(s):
     * $ticketID (in int) - ticket ID
     * $notes (in string) - $notes
     * $status (in int) - 0 = open, 1 = close
     * $solverID (in string) - user ID who submits
     * Return value(s): true if success, false if fail
     ***********************************************/
    function TICKET_UPDATE($ticketID,$notes,$status,$solverID)
    {
        //switch to correct db
        $this->getConn()->exec('USE ' . $this->maindb);
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        //SQL update updates an existing record in the db
        $sth = $this->getConn()->prepare("UPDATE `ticket` SET `notes` = :note, `status` = :stat,`solverID` = :uID,`solveddate` = NOW() WHERE `ticketID` = :id");
        if (!$sth)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        $sth->bindParam(':id', $ticketID, PDO::PARAM_INT, 11);
        $sth->bindParam(':note', $notes, PDO::PARAM_STR);
        $sth->bindParam(':stat',$status,PDO::PARAM_INT);
        $sth->bindParam(':uID',$solverID,PDO::PARAM_INT);
        $ret =$sth->execute();
        return $ret;
    }

    /**********************************************
     * Under development.
     * Function:SP_USER_INSERT
     * Description: Inserts a new user into the database
     * Parameter(s):
     * $iAction (in string) - input, edit or ....
     * $iCollectionID (in int) - collection id of the document
     * $iDocID (in int) - document ID
     * $iUserID (in string) -  userID of user who performs the action
     ***********************************************/
    function SP_USER_INSERT($iUsername, $iPassword, $iFullname, $iEmail, $iRoleID, &$oMessage){
        $this->getConn()->exec('USE' . $this->maindb);
        /*PREPARE STATEMENT*/
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        $call = $this->getConn()->prepare("CALL SP_USER_INSERT(?,?,?,?,?,@oMessage)");
        if (!$call)
            trigger_error("SQL failed: ". $this->getConn()->errorCode()."-".$this->conn->erorInfo()[0]);
        $iPassword = password_hash(md5($iPassword),PASSWORD_DEFAULT);
        $call->bindParam(1, $iUsername, PDO::PARAM_STR, strlen($iUsername));
        $call->bindParam(2, $iPassword, PDO::PARAM_STR, strlen($iPassword));
        $call->bindParam(3, $iFullname, PDO::PARAM_STR, strlen($iFullname));
        $call->bindParam(4, $iEmail, PDO::PARAM_STR, strlen($iEmail));
        $call->bindParam(5, $iRoleID, PDO::PARAM_INT);

        /* EXECUTE STATEMENT */
        $call->execute();

        /* RETURN RESULT */
        $select = $this->getConn()->query('SELECT @oMessage');
        $result = $select->fetch(PDO::FETCH_ASSOC);
        $oMessage = $result['@oMessage'];
        if ($call){
            return true;
            return $oMessage;
        }
        return false;
        return $oMessage;
    }

    /**********************************************
     * Function: SP_LOG_WRITE
     * Description: Insert new log entry into the db
     * Parameter(s):
     * $iAction (in string) - input, edit or ....
     * $iCollectionID (in int) - collection id of the document
     * $iDocID (in int) - document ID
     * $iUserID (in string) -  userID of user who performs the action
     * $iStatus (in string) - success or fail
     * $iComment (in string) - comment
     * Return value(s): true if success, false if fail
     ***********************************************/
    function SP_LOG_WRITE($iAction, $iCollectionID, $iDocID, $iUserID, $iStatus,$iComment)
    {
        //switch to correct db
        $this->getConn()->exec('USE ' . $this->maindb);
        /* PREPARE STATEMENT */
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        //sql CALL calls the built in function on the db
        //the ?'s are variables that we will bind values too a few lines down.
        $call = $this->getConn()->prepare("CALL SP_LOG_WRITE(?,?,?,?,?,?)");
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        $call->bindParam(1, $iAction, PDO::PARAM_STR, 10);
        $call->bindParam(2, $iCollectionID, PDO::PARAM_INT,11);
        $call->bindParam(3, $iDocID, PDO::PARAM_INT,11);
        $call->bindParam(4, $iUserID, PDO::PARAM_INT,11);
        $call->bindParam(5, $iStatus, PDO::PARAM_STR, 7);
        $call->bindParam(6, $iComment, PDO::PARAM_STR, 250);
        /* EXECUTE STATEMENT */
        $ret = $call->execute();
        return $ret;
    }

    /**********************************************
     * Function: SP_LOG_INSERT
     * Description: Slightly different version of SP_LOG_WRITE, allows to insert timestamp (use for log migration) and comments
     * Parameter(s):
     * $iAction (in string) - input, edit or ....
     * $iCollectionID (in int) - collection id of the document
     * $iDocID (in int) - document ID
     * $iUserID (in string) -  userID of user who performs the action
     * $iStatus (in string) - success or fail
     * $iTimestamp (in string) - timestamp
     * $iComments (in string) - comments (usually libraryindex if docID is missing)
     * Return value(s): true if success, false if fail
     ***********************************************/
    function SP_LOG_INSERT($iAction,$iCollectionID,$iDocID,$iUserID,$iStatus,$iTimestamp,$iComments)
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        /* PREPARE STATEMENT */
        $call = $this->getConn()->prepare("CALL SP_LOG_INSERT(?,?,?,?,?,?,?)");
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        $call->bindParam(1, $iAction, PDO::PARAM_STR, 10);
        $call->bindParam(2, $iCollectionID, PDO::PARAM_INT,11);
        $call->bindParam(3, $iDocID, PDO::PARAM_INT,11);
        $call->bindParam(4, $iUserID, PDO::PARAM_INT,11);
        $call->bindParam(5, $iStatus, PDO::PARAM_STR, 7);
        $call->bindParam(6, $iTimestamp, PDO::PARAM_STR);
        $call->bindParam(7, $iComments, PDO::PARAM_STR,250);
        /* EXECUTE STATEMENT */
        $ret = $call->execute();
        return $ret;
    }

    /**********************************************
     * Function: SP_ADMIN_TICKET_SELECT
     * Description: GIVEN A TICKET ID, RETURN INFORMATION ABOUT TICKET
     * Parameter(s):
     * $iTicketID (in Integer) - ticket ID
     * Return value(s):
     * $result (assoc array) - return a ticket info into a associative array
     ***********************************************/
    function SP_ADMIN_TICKET_SELECT($iTicketID)
    {
        //switch to correct db
        $this->getConn()->exec('USE ' . $this->maindb);
        /* PREPARE STATEMENT */
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // sql statement CALL calls the function pointed to in the db
        $call = $this->getConn()->prepare("CALL SP_ADMIN_TICKET_SELECT(?,@oSubject,@oSubmissionDate,@oSolvedDate,@oPoster,@oCollection,@oDescription,@oNotes,@oSolver,@oStatus,@oLastSeen,@oLibraryIndex)");
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        //bind variable to the ? in the above call statement
        $call->bindParam(1, $iTicketID, PDO::PARAM_INT);
        /* EXECUTE STATEMENT */
        $call->execute();
        /* RETURN RESULT */
        //select appropriate ticket
        $select = $this->getConn()->query('SELECT @oSubject AS Subject,@oSubmissionDate AS SubmissionDate,@oSolvedDate AS SolvedDate,@oPoster AS Submitter,@oCollection AS Collection,@oDescription AS Description,@oNotes AS Notes,@oSolver AS Solver,@oStatus AS Status,@oLastSeen AS LastSeen, @oLibraryIndex AS LibraryIndex');
        //return appropriate ticket
        $result = $select->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**********************************************
     * Function: TICKET_UPDATE_LASTSEEN
     * Description: UPDATE `lastseen` field in `ticket` table to current timestamp, given the ticket ID
     * Parameter(s):
     * $iTicketID (in Integer) - ticket ID
     * Return value(s):
     * $result (assoc array) - return 1 if success, otherwise fail
     ***********************************************/
    function TICKET_UPDATE_LASTSEEN($iTicketID)
    {
        //switch to correct db
        $this->getConn()->exec('USE ' . $this->maindb);
        $sth = $this->getConn()->prepare('UPDATE `ticket` SET `lastseen` = NOW() WHERE `ticketID` = :ticketID LIMIT 1');
        $sth->bindParam(':ticketID',$iTicketID,PDO::PARAM_INT);
        $ret = $sth->execute();
        return $ret;
    }

    /**********************************************
     * Function:  GET_DOCUMENT_COUNT
     * Description: Count number of rows for the table specified by the Col parameter
     * Parameter(s):
     * collection (in string) - name of the collection
     * Return value(s):
     * $result (integer) - Number of rows as an integer
     ***********************************************/
    function GET_DOCUMENT_COUNT($collection)
    {
        //get correct database
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select the number of documentID's found from document
            $sth = $this->getConn()->prepare("SELECT COUNT(`documentID`) FROM `document`");
            $sth->execute();
            //return result
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }

    /**********************************************
     * Function: TEMPLATE_DOCUMENT_SELECT
     * Description: Pulls a specified document metadata from document table of a collection database (no SP, not recommended)
     * Parameter(s):
     * $iDocID (in string) - input document id
     * Return value(s):
     * $result (assoc array) - return document metadata in an assoc array
     ***********************************************/
    function TEMPLATE_DOCUMENT_SELECT($iDocID)
    {
        $sth = $this->getConn()->prepare("SELECT * FROM `document` where `documentID` = :docID");
        $sth->bindParam(':docID',$iDocID,PDO::PARAM_INT);
        $ret = $sth->execute();
        if($ret)
            return $sth->fetch(PDO::FETCH_ASSOC);
        return $ret;
    }

    /**********************************************
     * Function: GET_COLLECTION_CONFIG
     * Description: Pulls the data associated with the collection name passed in (short version, no SP, not recommended)
     * Parameter(s):
     * $iName (in string) - input DB Name
     * Return value(s):
     * $result (assoc array) - return above values in an assoc array
     ***********************************************/
    function GET_COLLECTION_INFO($iName)
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        $sth = $this->getConn()->prepare("SELECT * FROM `collection` WHERE `collection`.`name` = :name");
        $sth->bindParam(':name',$iName,PDO::PARAM_STR);
        $ret = $sth->execute();
        if($ret)
            return $sth->fetch(PDO::FETCH_ASSOC);
        return false;
    }

    /**********************************************
     * Function: SP_GET_COLLECTION_CONFIG_FROM_TEMPLATEID (SP_GET_COLLECTION_DATA)
     * Description: Pulls the data associated with the collection name passed in.
     * Parameter(s):
     * $iName (in string) - input DB Name
     * Return value(s):
     * $result (assoc array) - return above values in an assoc array
     ***********************************************/
    function SP_GET_COLLECTION_CONFIG_FROM_TEMPLATEID($iName, $iTemplateID)
    {   //USE is sql for changing to the database supplied after the concat.
        $this->getConn()->exec('USE ' . $this->maindb);
        /* PREPARE STATEMENT */
        //CALL is sql for telling the db to execute the function following call.
        //The ? in the functions parameter list is a variable that we bind a few lines down
        $call = $this->getConn()->prepare("CALL SP_GET_COLLECTION_CONFIG_FROM_TEMPLATEID(?,?,@oDisplayName,@oDbName,@oStorageDir,@oPublicDir,@oThumbnailDir,@oTemplateID,@oGeorecDir,@oCollectionID)");
        //ERROR HANDLING
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        //Bind the database name ($iName) into the prepared call statement from above.
        $call->bindParam(1, $iName, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 50);
        $call->bindParam(2, $iTemplateID, PDO::PARAM_INT, 11);
        /* EXECUTE STATEMENT */
        $call->execute();
        /* RETURN RESULT */
        //returned after successful call statement. Need to tell the db what data we want selected.
        $select = $this->getConn()->query('SELECT @oDisplayName AS DisplayName,@oDbName AS DbName,@oStorageDir AS StorageDir,@oPublicDir AS PublicDir,@oThumbnailDir AS ThumbnailDir,@oTemplateID as TemplateID,@oGeorecDir as GeoRecDir,@oCollectionID as CollectionID');
        //Database now has the appropriate data selected. We now need to ask the DB to fetch the values for us
        $result = $select->fetch(PDO::FETCH_ASSOC);
        $result["Name"] = htmlspecialchars($iName);
        return $result;
    }

    /**********************************************
     * Function:  GET_LOG_INFO
     * Description: function is used to access log information in the database
     * Parameter(s):
     * collection (in string) - name of the collection
     * docID in (in int) - document id number
     * Return value(s):
     * $result (integer) - Number of rows as an integer
     ***********************************************/
    function GET_LOG_INFO($collection, $docID)
    {
        //switch to appropriate db
        $this->getConn()->exec('USE ' . $this->maindb);
        //return appropriate db
        $dbID = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['CollectionID'];
        //select log, user, and collection information from their respective tables
        $logsth = $this->getConn()->prepare("SELECT `action`, `user`.`username`, `timestamp` FROM `log` LEFT JOIN `user` ON `user`.`userID`=`log`.`userID` WHERE `docID` = ? AND `collectionID` = ?");
        if (!$logsth)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        //bind variables to the sql statement above
        $logsth->bindParam(1, $docID, PDO::PARAM_INT, 11);
        $logsth->bindParam(2, $dbID, PDO::PARAM_INT, 11);
        $logsth->execute();
        //return result from db
        $logArray = $logsth->fetchAll();
        return $logArray;
    }

    /**********************************************
     * Function: GET_COLLECTION_TEMPLATE
     * Description: GIVEN collection name , return the collection's template data (dir, fulldir, name)
     * Parameter(s):
     * collection (in string) - name of the collection
     * Return value(s):
     * (assoc array) - return a template info in an associative array
     ***********************************************/
    function GET_COLLECTION_TEMPLATE($collection)
    {
        //switch to appropriate db
        $this->getConn()->exec('USE ' . $this->maindb);
        //prepares select sql statement to return template information
        $sth = $this->getConn()->prepare("SELECT * FROM `template` WHERE `template`.`templateID` = (SELECT `templateID` FROM `collection` WHERE `name` = ? LIMIT 1) LIMIT 1");
        //bind parameter collection into the above sql statement
        $sth->bindParam(1,$collection, PDO::PARAM_STR,50);
        $sth->execute();
        //return selected information
        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**********************************************
     * Function: DELETE_DOCUMENT
     * Description: DELETE DOCUMENT GIVEN collection name and document ID
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * $iDocID (in int) - document id
     * Return value(s):
     * $result  (array) - true if success, otherwise, false
     ***********************************************/
    function DELETE_DOCUMENT($collection,$iDocID)
    {
        //get appropriate database
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        if ($dbname != null && $dbname != "") {
            $this->getConn()->exec('USE ' . $dbname);
            //prepare an sql statement
            $sth = $this->getConn()->prepare("DELETE FROM `document` WHERE `documentID`=?");
            //bind iDocID into the above statement
            $sth->bindParam(1,$iDocID,PDO::PARAM_INT,11);
            //execute delete
            $ret = $sth->execute();
            return $ret;
        }
        return false;
    }

    /**********************************************
     * Function: GET_AUTHOR_LIST
     * Description: function responsible for returning the names of authors from the db
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * Return value(s):
     * $result  (array) - true if success, otherwise, false
     ***********************************************/
    function GET_AUTHOR_LIST($collection)
    {
        //get appropriate database
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepares sql statement for execution
            $sth = $this->getConn()->prepare("SELECT `authorname` FROM `author`");
            $sth->execute();
            //return statement result
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }

    /**********************************************
     * Function: GET_CUSTOMER_LIST
     * Description: function responsible for returning the names of customers from the db
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * Return value(s):
     * $result  (array) - true if success, otherwise, false
     ***********************************************/

    function GET_CUSTOMER_LIST($collection)
    {
        //get appropriate database
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepares sql statement for execution
            $sth = $this->getConn()->prepare("SELECT `customername` FROM `customer`");
            $sth->execute();
            //return statement result
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }

    /**********************************************
     * Function: GET_COMPANY_LIST
     * Description: function responsible for returning the names of customers from the db
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * Return value(s):
     * $result  (array) - true if success, otherwise, false
     ***********************************************/
    function GET_COMPANY_LIST($collection)
    {
        //get appropriate database
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepares sql statement for execution
            $sth = $this->getConn()->prepare("SELECT `companyname` FROM `company`");
            $sth->execute();
            //return statement result
            $result = $sth->fetchAll(PDO::FETCH_NUM);
            return $result;
        } else return false;
    }

    /**********************************************
     * Function: GET_AUTHOR_INFO
     * Description: return info from a row of author table
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * $iAuthorID (in int) - ID of user
     * $option (in str) - default value is null, generate different condition for the queries depends on the option
     * Return value(s):
     * $result  (array) - assoc array if success, otherwise, false
     ***********************************************/
    function GET_AUTHOR_INFO($collection,$iAuthorID,$option = null)
    {
        //get appropriate database
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepares sql statement for execution
            if($option == null ) // grab the exact authorID
                $sth = $this->getConn()->prepare("SELECT * FROM `author` WHERE `authorID` = :authorID LIMIT 1");
            else //option is available (not null)
            {
                switch($option)
                {
                    case "nextID": //grab the next available authorID
                        $sth = $this->getConn()->prepare("SELECT * FROM `author` WHERE `authorID` > :authorID ORDER BY `authorID` LIMIT 1");
                        break;
                    default: return false;
                }
            }
            $sth->bindParam(":authorID",$iAuthorID,PDO::PARAM_INT);
            $sth->execute();
            //return statement result
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } else return false;
    }

    /**********************************************
     * Function: UPDATE_AUTHOR_INFO
     * Description: Update authorname and TDLname, given userID and name of a collection database
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * $iAuthorID (in int) - ID of user
     * $iAuthorName (in str) - new value of author name
     * $iOldName (in str) - new value of old name
     * Return value(s):
     * return true if success, false if error occurs
     ***********************************************/
    function UPDATE_AUTHOR_INFO($collection,$iAuthorID,$iAuthorName,$iOldName)
    {
        //get appropriate database
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //prepares sql statement for execution
            $sth = $this->getConn()->prepare("UPDATE `author` SET `authorname` = :authorname, `TDLname` = :oldname WHERE `authorID` = :authorID");
            $sth->bindParam(':authorID',$iAuthorID,PDO::PARAM_INT);
            $sth->bindParam(':authorname',$iAuthorName,PDO::PARAM_STR);
            $sth->bindParam(':oldname',$iOldName,PDO::PARAM_STR);
            $ret = $sth->execute();
            //return statement result
            return $ret;
        } else return false;
    }

    /**********************************************
     * Function: GET_ADMIN_OPENTICKET_COUNT
     * Description: function query's the db selecting all tickets who have a status of 0
     * Parameter(s):
     * Return value(s):
     * $result  (int) - number of tickets
     ***********************************************/
    function GET_ADMIN_OPENTICKET_COUNT()
    {
        //switch to correct db
        $this->getConn()->exec('USE ' . $this->maindb);
        /* PREPARE STATEMENT */
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // sql statement CALL calls the function pointed to in the db
        $call = $this->getConn()->prepare("SELECT COUNT(`ticketID`) FROM `ticket` INNER JOIN `collection` ON (`ticket`.`collectionID` = `collection`.`collectionID`) WHERE `status`='0'");
        // FROM `ticket` INNER JOIN `collection` ON (`ticket`.`collectionID` = `collection`.`collectionID`)
        //INNER JOIN `user` ON (`ticket`.`posterID` = `user`.`userID`)
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);

        /* EXECUTE STATEMENT */
        $call->execute();
        /* RETURN RESULT */
        //return the result
        $result = $call->fetchColumn();
        return $result;

//
    }



    /***************************************************************/
    /**********************QUERIES FUNCTIONS************************/
    /***************************************************************/

    /**********************************************
     * Function: GET_USER_CLOSEDTICKET_COUNT
     * Description: function query's the db selecting all user tickets that have been viewed
     * Parameter(s): $userid only get tickets for the user that is logged in
     * Return value(s):
     * $result  (int) - number of tickets
     ***********************************************/
    function GET_USER_CLOSEDTICKET_COUNT($userid)
    {
        //switch to correct db
        $this->getConn()->exec('USE ' . $this->maindb);
        /* PREPARE STATEMENT */
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // sql statement CALL calls the function pointed to in the db
        $call = $this->getConn()->prepare("SELECT COUNT(`ticketID`) FROM `ticket` INNER JOIN `collection` ON (`ticket`.`collectionID` = `collection`.`collectionID`) WHERE `posterID`=:userID AND `solveddate` IS NOT NULL AND `lastseen`<`solveddate`");
        // FROM `ticket` INNER JOIN `collection` ON (`ticket`.`collectionID` = `collection`.`collectionID`)
        //INNER JOIN `user` ON (`ticket`.`posterID` = `user`.`userID`)
        $call->bindParam(':userID',$userid,PDO::PARAM_INT);
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);

        /* EXECUTE STATEMENT */
        $call->execute();
        /* RETURN RESULT */
        //return the result
        $result = $call->fetchColumn();
        return $result;
//
    }

    /**********************************************
     * Function: GET_DOCUMENT_FILTEREDCOAST_COUNT
     * Description: function responsible for returning all documents that have a coastline
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * Return value(s):
     * $result  (array) - true if success, otherwise, false
     ***********************************************/
    function GET_DOCUMENT_FILTEREDCOAST_COUNT($collection)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select all documentID's that have values "hascoast = 1"
            $sth = $this->getConn()->prepare("SELECT COUNT(`documentID`) FROM `document` WHERE `hascoast`='1'");
            $sth->execute();
            //return the result
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }
    /**********************************************
     * Function: GET_DOCUMENT_FILTEREDNEEDSREVIEW0_COUNT_FIELDBOOK
     * Description: function responsible for returning all documents that have a coastline
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * Return value(s):
     * $result  (array) - true if success, otherwise, false
     ***********************************************/
    function GET_DOCUMENT_FILTEREDNEEDSREVIEW0_COUNT_FIELDBOOK($collection,$booktitle)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select booktitles where needs review = 0
            // AND `weeklyreport`.`collectionID` = ?'
            $sth = $this->getConn()->prepare("SELECT COUNT(`documentID`) FROM `document` WHERE `needsreview`='0' AND `booktitle`=:bookTitle");
            $sth->bindParam(':bookTitle',$booktitle,PDO::PARAM_INT);
            $sth->execute();
            //return the result
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }
	/**********************************************
     * Function: GET_DOCUMENT_FILTEREDNEEDSREVIEW0_COUNT_JOBFOLDER
     * Description: function responsible for returning all documents that have a coastline
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * Return value(s):
     * $result  (array) - true if success, otherwise, false
     ***********************************************/
    function GET_DOCUMENT_FILTEREDNEEDSREVIEW0_COUNT_JOBFOLDER($collection,$foldername)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select foldernames where needs review = 0
            // AND `weeklyreport`.`collectionID` = ?'
            $sth = $this->getConn()->prepare("SELECT COUNT(`documentID`) FROM `document` WHERE `needsreview`='0' AND `foldername`=:folderName");
            $sth->bindParam(':folderName',$foldername,PDO::PARAM_STR);
            $sth->execute();
            //return the result
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }

    function GET_DOCUMENT_MATCHBOOKTITLE_COUNT_FIELDBOOK($collection,$booktitle)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select booktitles where needs review = 0
            // AND `weeklyreport`.`collectionID` = ?'
            $sth = $this->getConn()->prepare("SELECT COUNT(`documentID`) FROM `document` WHERE `booktitle`=:bookTitle");
            $sth->bindParam(':bookTitle',$booktitle,PDO::PARAM_INT);
            $sth->execute();
            //return the result
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }
	 function GET_DOCUMENT_MATCHBOOKTITLE_COUNT_JOBFOLDER($collection,$foldername)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select booktitles where needs review = 0
            // AND `weeklyreport`.`collectionID` = ?'
            $sth = $this->getConn()->prepare("SELECT COUNT(`documentID`) FROM `document` WHERE `foldername`=:folderName");
            $sth->bindParam(':folderName',$foldername,PDO::PARAM_STR);
            $sth->execute();
            //return the result
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }
    function GET_DOCUMENT_MATCHBOOKTITLE_PDFSTAGE($collection,$booktitle)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select booktitles where needs review = 0
            // AND `weeklyreport`.`collectionID` = ?'
            $sth = $this->getConn()->prepare("SELECT `RdyForPdf` FROM `document` WHERE `booktitle`=:bookTitle");
            $sth->bindParam(':bookTitle',$booktitle,PDO::PARAM_INT);
            $sth->execute();
            //return the result
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }
	function GET_DOCUMENT_MATCHFOLDERNAME_PDFSTAGE($collection,$foldername)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select booktitles where needs review = 0
            // AND `weeklyreport`.`collectionID` = ?'
            $sth = $this->getConn()->prepare("SELECT `RdyForPdf` FROM `document` WHERE `foldername`=:folderName");
            $sth->bindParam(':folderName',$foldername,PDO::PARAM_STR);
            $sth->execute();
            //return the result
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }

    function GET_DOCUMENT_FILTEREDNEEDSREVIEW1_COUNT($collection,$booktitle)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select booktitles where needs review = 1
            // AND `weeklyreport`.`collectionID` = ?'
            $sth = $this->getConn()->prepare("SELECT COUNT(`documentID`) FROM `document` WHERE `needsreview`='1' AND `booktitle`=:bookTitle");
            $sth->bindParam(':bookTitle',$booktitle,PDO::PARAM_INT);
            $sth->execute();
            //return the result
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }


    /**********************************************
     * Function: GET_DOCUMENT_FILTEREDTITLE_COUNT
     * Description: function responsible for returning all documents that have a title
     * Parameter(s):
     * $collection (in String) - Name of the collection
     * Return value(s):
     * $result  (array) - true if success, otherwise, false
     ***********************************************/
    function GET_DOCUMENT_FILTEREDTITLE_COUNT($collection)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select the number of documentID's where the libraryindex has a title
            $sth = $this->getConn()->prepare("SELECT COUNT(`documentID`) FROM `document` WHERE `libraryindex` = `title`");
            $sth->execute();
            //return statement
            $result = $sth->fetchColumn();
            return $result;
        } else return false;
    }

    /**********************************************
     * Function: SP_WEEKLYREPORT_INSERT
     * Description: INSERT INTO WEEKLYREPORT TABLE FROM COUNTING ENTRIES IN LOG TABLE
     * Parameter(s):
     * $iYear (in int) - year
     * $iWeek (in int) - week (1-52)
     * $iCollectionID (in int) - collectionID
     * Return value(s): true if success, false if fail
     ***********************************************/
    function SP_WEEKLYREPORT_INSERT($iYear,$iWeek,$iCollectionID)
    {
        //get appropriate DB
        $this->getConn()->exec('USE ' . $this->maindb);
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // sql statement CALL calls the function pointed to in the db
        $call = $this->getConn()->prepare("CALL SP_WEEKLYREPORT_INSERT(?,?,?)");
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        //binds variables to the above SQL statement
        $call->bindParam(1, $iYear, PDO::PARAM_INT);
        $call->bindParam(2, $iWeek, PDO::PARAM_INT);
        $call->bindParam(3, $iCollectionID,PDO::PARAM_INT);
        /* EXECUTE STATEMENT */
        $ret = $call->execute();
        return $ret;
    }


    //****************************Where the Map Functions used to be

    //******************************* STATISTICS FUNCTIONS ******************//

    /**********************************************
     * Function: GET_WEEKLYREPORT
     * Description: attempts to return the weeklyreport from the db of the requested parameters
     * Parameter(s):
     * $iYear (in int) - year
     * $iCollectionID (in int) - specifies the collection id to search
     * Return value(s): return array if success, false if fail
     ***********************************************/
    function GET_WEEKLYREPORT($iYear,$iCollectionID)
    {
        //get appropriate db
        $this->getConn()->exec('USE ' . $this->maindb);
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // selects the weeks from weeklyreport db that satisfy the year and collection id parameters
        $sth = $this->getConn()->prepare('SELECT `weeklyreport`.`week`,`weeklyreport`.`count` FROM `weeklyreport` WHERE `weeklyreport`.`year` = ? AND `weeklyreport`.`collectionID` = ?');
        //bind parameters to the sql statement
        $sth->bindParam(1,$iYear,PDO::PARAM_INT);
        $sth->bindParam(2,$iCollectionID,PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**********************************************
     * Function: GET_WEEKLY_TRANSCRIPTION_REPORT
     * Description: calls the SP_LOG_WEEKLYTRANSCRIPTIONREPORT_COUNT function in phpadmin
     * Parameter(s):
     * $iYear (in int) - year
     * $iCollectionID (in int) - specifies the collection id to search
     * Return value(s): return array if success, false if fail
     ***********************************************/
    function GET_WEEKLY_TRANSCRIPTION_REPORT($iYear,$iCollectionID)
    {
        //get appropriate db
        $this->getConn()->exec('USE ' . $this->maindb);
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // selects the weeks from weeklyreport db that satisfy the year and collection id parameters
        $call = $this->getConn()->prepare("CALL SP_LOG_WEEKLYTRANSCRIPTIONREPORT_COUNT(:iYear,:iColID)");
        //bind variables to the above sql statement
        $call->bindParam(':iYear',$iYear,PDO::PARAM_INT);
        $call->bindParam(':iColID',$iCollectionID,PDO::PARAM_INT);
        $call->execute();
        return $call->fetchAll(PDO::FETCH_NUM);
    }

    /**********************************************
     * Function: SELECT_USER_PERFORMANCE_BY_MONTH
     * Description: selects all usernames in the passed year, month and action, then counts them returning a number of actions
     * Parameter(s):
     * $iYear (in int) - year
     * $iCollectionID (in int) - specifies the collection id to search
     * Return value(s): return array if success, false if fail
     ***********************************************/
    function SELECT_USER_PERFORMANCE_BY_MONTH($iYear,$iMonth,$iUserID, $iAction)
    {
        //get appropriate db
        $this->getConn()->exec('USE ' . $this->maindb);
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // selects the weeks from weeklyreport db that satisfy the year and collection id parameters
        $call = $this->getConn()->prepare("SELECT COUNT(*), u1.`username` FROM `log` LEFT JOIN `user` AS u1 ON `log`.`userID` = u1.`userID` WHERE MONTH(`log`.`timestamp`) = :iMonth AND YEAR(`log`.`timestamp`) = :iYear AND `log`.`action`= :iAction AND `log`.`status` = 'success' AND `log`.`userID` = :iUserID");

           //bind variables to the above sql statement
        //bind variables to the above sql statement
        $call->bindParam(':iYear',$iYear,PDO::PARAM_INT);
        $call->bindParam(':iMonth',$iMonth,PDO::PARAM_INT);
        $call->bindParam(':iUserID',$iUserID,PDO::PARAM_INT);
        $call->bindParam(':iAction',$iAction,PDO::PARAM_STR);
        $call->execute();
        return $call->fetch(PDO::FETCH_NUM);
    }

    function SELECT_DOCID_BY_SUBJECT($iSubject, $iCollection)
    {
        $bolDB = $this->SWITCH_DB($iCollection);
        if($bolDB){
            /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
            // selects the weeks from weeklyreport db that satisfy the year and collection id parameters
            $call = $this->getConn()->prepare("SELECT `documentID`, `libraryindex` FROM `document` WHERE `libraryindex` = :iSubject");


            //bind variables to the above sql statement
            //bind variables to the above sql statement
            $call->bindParam(':iSubject',$iSubject,PDO::PARAM_STR);
            $call->execute();
            return $call->fetch(PDO::FETCH_NUM);
        }
    }

    /**********************************************
     * Function: GET_ACTION_COUNT
     * Description: counts the number of actions specified User has done.
     * Parameter(s):
     * $iYear (in int) - year
     * $iCollectionID (in int) - specifies the collection id to search
     * $iUserID (in int) - the user id of a specific user
     * Return value(s): count of actions from the user
     ***********************************************/
    function GET_ACTION_COUNT($iYear,$iCollectionID,$iUserID)
    {
        //get appropriate db
        $this->getConn()->exec('USE ' . $this->maindb);
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // selects the weeks from weeklyreport db that satisfy the year and collection id parameters
        $call = $this->getConn()->prepare("CALL SP_LOG_MONTHLY_USERCATALOG_COUNT(:iYear,:iColID,:iUserID)");
        //bind variables to the above sql statement
        $call->bindParam(':iYear',$iYear,PDO::PARAM_INT);
        $call->bindParam(':iColID',$iCollectionID,PDO::PARAM_INT);
        $call->bindParam(':iUserID',$iUserID,PDO::PARAM_INT);
        $call->execute();

        return $call->fetch(PDO::FETCH_NUM);
    }

    /**********************************************
     * Function: GET_MONTHLY_TRANSCRIPTION_REPORT
     * Description: attempts to return the monthlyreport from the db of the requested parameters
     *              has better performance than GET_MONTHLYREPORT (this query only searches on the table one time)
     * Parameter(s):
     * $iYear (in int) - year
     * $iCollectionID (in int) - specifies the collection id to search
     * Return value(s): return array if success, false if fail
     ***********************************************/
    function GET_MONTHLY_TRANSCRIPTION_REPORT($iYear,$iCollectionID)
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // sql statement CALL calls the function pointed to in the db
        $call = $this->getConn()->prepare("CALL SP_LOG_MONTHLYTRANSCRIPTIONREPORT_COUNT(:iYear,:iColID)");
        //bind variables to the above sql statement
        $call->bindParam(':iYear',$iYear,PDO::PARAM_INT);
        $call->bindParam(':iColID',$iCollectionID,PDO::PARAM_INT);
        $call->execute();
        return $call->fetchAll(PDO::FETCH_NUM);
    }

//    //DEPRECATED: SEE GET_MONTHLYREPORT2($iYear,$iCollectionID)
//    /**********************************************
//     * Function: GET_MONTHLYREPORT
//     * Description: attempts to return the monthlyreport from the db of the requested parameters
//     * Parameter(s):
//     * $iYear (in int) - year
//     * $iCollectionID (in int) - specifies the collection id to search
//     * Return value(s): return array if success, false if fail
//     ***********************************************/
//    function GET_MONTHLYREPORT($iYear,$iCollectionID)
//    {
//        $this->getConn()->exec('USE ' . $this->maindb);
//        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
//        // selects the weeks from monthlyreport db that satisfy the year, month, and collection id satisfy the parameters
//        $sth = $this->getConn()->prepare("SELECT
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 1 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 2 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 3 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 4 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 5 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 6 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 7 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 8 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 9 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 10 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 11 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success'),
//            (SELECT COUNT(*) FROM `log` WHERE MONTH(`log`.`timestamp`) = 12 AND YEAR(`log`.`timestamp`) = :y AND `log`.`collectionID`= :collection AND `log`.`action`='transcribe' AND `log`.`status` = 'success')");
//            //bind variables to the above sql statement
//            $sth->bindParam(':y',$iYear,PDO::PARAM_INT);
//            $sth->bindParam(':collection',$iCollectionID,PDO::PARAM_INT);
//
//            $sth->execute();
//            return $sth->fetchAll(PDO::FETCH_NUM);
//    }

    /**********************************************
     * Function: GET_MONTHLYREPORT2
     * Description: attempts to return the monthlyreport from the db of the requested parameters
     *              has better performance than GET_MONTHLYREPORT (this query only searches on the table one time)
     * Parameter(s):
     * $iYear (in int) - year
     * $iCollectionID (in int) - specifies the collection id to search
     * Return value(s): return array if success, false if fail
     ***********************************************/
    function GET_MONTHLYREPORT2($iYear,$iCollectionID)
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        // sql statement CALL calls the function pointed to in the db
        $call = $this->getConn()->prepare("CALL SP_LOG_MONTHLYREPORT_COUNT(:iYear,:iColID)");
        //bind variables to the above sql statement
        $call->bindParam(':iYear',$iYear,PDO::PARAM_INT);
        $call->bindParam(':iColID',$iCollectionID,PDO::PARAM_INT);
        $call->execute();
        return $call->fetchAll(PDO::FETCH_NUM);
    }

    /**********************************************
     * Function: SET_DOCUMENT_TRANSCRIBED
     * Description: attempts to set the document transcribed flag in the specified document
     * Parameter(s):
     * $collection (in string) - specifies the collection where the document resides
     * $docID (in int) - specifies the document in which transcription was done
     * $val (in int) - specifies the collection id to search
     * Return value(s): true if success, false if fail
     ***********************************************/
    function SET_DOCUMENT_TRANSCRIBED($collection,$docID,$val)
    {
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        $sth = $this->getConn()->prepare("UPDATE `document` SET `transcribed` = :val WHERE `documentID` = :docID");
        $sth->bindParam(':val',$val,PDO::PARAM_INT,1);
        $sth->bindParam(":docID",$docID,PDO::PARAM_INT,11);
        $ret = $sth->execute();
        return $ret;
    }

    /**********************************************
     * Function: USER_UPDATE_INFO
     * Description: attempts to UPDATE user's email and fullname in the specified UserID
     * Parameter(s):
     * $iUserID (in int) - specifies the user id to update
     * $iEmail (in string) - specifies new email value to update
     * $iName (in string) - specifies the new fullname value to update
     * Return value(s): true if success, false if fail
     ***********************************************/
    function USER_UPDATE_INFO($iUserID,$iEmail,$iName)
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        $sth = $this->getConn()->prepare("UPDATE `user` SET `email` = :email,`fullname` = :name WHERE `userID` = :uID LIMIT 1");
        $sth->bindParam(':email',$iEmail,PDO::PARAM_STR);
        $sth->bindParam(':name',$iName,PDO::PARAM_STR);
        $sth->bindParam(':uID',$iUserID,PDO::PARAM_INT);
        $ret = $sth->execute();
        return $ret;
    }

    /**********************************************
     * Function: USER_UPDATE_PASSWORD
     * Description: attempts to UPDATE user's password in the specified UserID
     * Parameter(s):
     * $iUserID (in int) - specifies the user id to update
     * $iOldPassword (in string) - specifies old password value to update
     * $iNewPassword (in string) - specifies the new password value to update
     * Return value(s): 1 if success, false if fail, 0 if no userID found
     ***********************************************/
    function USER_UPDATE_PASSWORD($iUserID,$iOldPassword,$iNewPassword)
    {
        $isValidPassword = $this->USER_VERIFY_PWD($iUserID,$iOldPassword);
        if($isValidPassword) {
            $iNewPassword = password_hash(md5($iNewPassword), PASSWORD_DEFAULT);
            $this->getConn()->exec('USE ' . $this->maindb);
            $sth = $this->getConn()->prepare("UPDATE `user` SET `password` = :newpwd WHERE `userID` = :uID LIMIT 1");
            $sth->bindParam(':newpwd', $iNewPassword, PDO::PARAM_STR);
            $sth->bindParam(':uID', $iUserID, PDO::PARAM_INT);
            $ret = $sth->execute();
            if ($ret)
                $ret = $sth->rowCount(); //return number of rows affected (must be 1 or 0)
            return $ret;
        }
        return false;
    }

    /**********************************************
     * Function: USER_VERIFY_PWD
     * Description: validate if the input password is equivalent to password stored in the DB given specified user ID
     * Parameter(s):
     * $iUserID (in string) - input user ID
     * Return value(s): false if fail, pwd if success
     ***********************************************/
    function USER_VERIFY_PWD($iUserID,$iPassword)
    {
        $sth = $this->getConn()->prepare("SELECT `password` FROM `user` WHERE `userID` = :userID LIMIT 1");
        $sth->bindParam(':userID',$iUserID,PDO::PARAM_INT);
        $ret = $sth->execute();
        if(!$ret)
            return false;
        $passwordFromDB = $sth->fetchColumn();
        if(password_verify(md5($iPassword),$passwordFromDB))
            return true;
        return false;

    }

    /**********************************************
     * Function: USER_UPDATE_ADMIN_RESET_PASSWORD
     * Description: updates the password for a selected user, with a random generated string
     * Parameter(s): $iUserID,$iNewPassword
     * Return value(s): True or False value, "1" it worked, "0" it failed
     ***********************************************/
    function USER_UPDATE_ADMIN_RESET_PASSWORD($iUserID,$iNewPassword)
    {
        $iNewPassword = password_hash(md5($iNewPassword),PASSWORD_DEFAULT);
        $this->getConn()->exec('USE ' . $this->maindb);
        $sth = $this->getConn()->prepare("UPDATE `user` SET `password` = :newpwd WHERE `userID` = :uID LIMIT 1");
        $sth->bindParam(':newpwd',$iNewPassword,PDO::PARAM_STR);
        $sth->bindParam(':uID',$iUserID,PDO::PARAM_INT);
        $ret = $sth->execute();
        if($ret)
            $ret = $sth->rowCount(); //return number of rows affected (must be 1 or 0)
        return $ret;
    }
    //Under development
    /**********************************************
     * Function: GET_DOCUMENT_LISTS
     * Description: attemps to get list of document ID, library index, title of all maps in the specified collection
     * Parameter(s):
     * $collection (in string) - specifies the collection where the document resides
     * Return value(s): an associative array of document for the given collection
     ***********************************************/
    function GET_DOCUMENT_LISTS($collection)
    {
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        $sth = $this->getConn()->prepare("SELECT `documentID`,`libraryindex`,`title` FROM `document` ORDER BY `libraryindex`");
        $sth->execute();
        $ret = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $ret;
    }
	
	/********************************************* 
	 * SAM DEVELOPMENT
	 ********************************************/
	 
	 /**********************************************
     * Function: GET_DOCUMENT_BY_FILENAME
     * Description: function responsible for returning all documents that match the filename
     * Parameter(s):
     * $collection (in String) - Name of the collection
	 * $filename (in String) - Name of the file
     * Return value(s):
     * $result  (array) - true if success, otherwise, false
     ***********************************************/
    function GET_DOCUMENT_BY_FILENAME($collection, $filename)
    {
        //get appropriate db
        $dbname = $this->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $this->getConn()->exec('USE ' . $dbname);
        if ($dbname != null && $dbname != "")
        {
            //select the number of documentID's where the libraryindex has a title
            $sth = $this->getConn()->prepare("SELECT * FROM `document` WHERE `filename` = :filename");
			$sth->bindParam(':filename',$filename,PDO::PARAM_STR);
            $sth->execute();
            //return statement
            $ret = $sth->fetchAll(PDO::FETCH_ASSOC);
			//var_dump($ret);
            return $ret;
        } else return false;
    }
}
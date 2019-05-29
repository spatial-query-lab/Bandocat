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

class AnnouncementDBHelper extends DBHelper
{
    /**********************************************
     * Function: SP_ANNOUNCEMENT_INSERT
     * Description:
     * Insert announcements to the bandocatdb database announcement table
     * Parameter(s):
     * $iTitle (in string) - input announcement title
     * $iMessage (in string) - input announcement message
     * $iEndtime (in timestamp) - input time in which the announcement will be deleted
     * $iPosttime (in timestamp) - input time the announcement was posted
     * $iPosterID (in int) - input UserID that created the annoucement
     * Return value(s): NONE
     ***********************************************/

    function SP_ANNOUNCEMENT_INSERT($iTitle, $iMessage, $iEndtime, $iPosterID){
        /* PREPARE STATEMENT */
        /* Prepares the SQL query, and returns a statement handle to be used for further operations on the statement*/
        //The ? in the functions parameter list is a variable that we bind a few lines down.
        //CALL is sql for calling the function built into the db at localhost/phpmyadmin
        $call = $this->getConn()->prepare("CALL SP_ANNOUNCEMENT_INSERT(?,?,?,?)");
        //Error handling
        if (!$call)
            trigger_error("SQL failed: " . $this->getConn()->errorCode() . " - " . $this->conn->errorInfo()[0]);
        //bind parameters to the sql statement
        $call->bindParam(1, $iTitle, PDO::PARAM_STR, strlen($iTitle));
        $call->bindParam(2, $iMessage, PDO::PARAM_STR, strlen($iMessage));
        $call->bindParam(3, $iEndtime, PDO::PARAM_STR, strlen($iEndtime));
        $call->bindParam(4, $iPosterID, PDO::PARAM_INT, 11);
        /* EXECUTE STATEMENT */
        $call->execute();
        if ($call)
            return true;
        return false;

    }

    function SP_ANNOUNCEMENT_UPDATE($iTitle, $iMessage, $iEndtime, $iAnnouncementID)
    {
        $this->getConn()->exec('USE' . $this->maindb);
        var_dump("THETITLE: ", $iTitle, "  THEMESSAGE: ", $iMessage);
        $sth = $this->getConn()->prepare("UPDATE `announcement` SET `title` = :title,`message` = :message, `endtime` = :endtime WHERE `announcementID` = :AID");
        $sth->bindParam(':title',$iTitle,PDO::PARAM_STR);
        $sth->bindParam(':message',$iMessage,PDO::PARAM_STR);
        $sth->bindParam(':endtime',$iEndtime,PDO::PARAM_STR);
        $sth->bindParam(':AID',$iAnnouncementID,PDO::PARAM_INT);
        $ret=$sth->execute();
        if($ret)
            $ret = $sth->rowCount(); //return number of rows affected (must be 1 or 0)
        return $ret;
    }

    function GET_ANNOUNCEMENT_DATA()
    {
        $data = array();

        // Getting connection
        $mysqli = new mysqli($this->getHost(), $this->getUser(), $this->getPwd(), "bandocatdb");

        // Checking to see if the connection failed
        if($mysqli->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            return false;
        }
        $sql = "SELECT `announcementID`,`title`,`message`, `endtime`, `posttime`, `posterID` FROM `announcement` WHERE `endtime` >= CURRENT_DATE";
        $response = $mysqli->query($sql);

        if ($response)
        {
            while($row = mysqli_fetch_assoc($response))
            {
                $data[] = $row;
            }
        }
        else
        {
            echo "Error: " . $sql . "<br>" . $mysqli->error;
        }

        $mysqli->close();
        return $data;
    }
}
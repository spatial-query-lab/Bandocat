<?php
/**********************************************
Function:
Description:
Parameter(s):
Return value(s):
 ***********************************************/

//this class provides methods for Collection Creator and Template Creator, working with MYSQL database and CLI MYSQL
// to run Collection Creator, the path to mysql/bin must be added to the System Environments Variable
class CreatorHelper extends DBHelper
{
    /**********************************************
     * Function: GET_COLLECTION_AND_TEMPLATE
     * Description: return collection (parameter) name, display name, dbname, template ID, template description of all collections in `collection` and `template` table
     * Parameter(s): NONE
     * Return value(s): return assoc array of the collections
     ***********************************************/
    function GET_COLLECTION_AND_TEMPLATE()
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        $sth = $this->getConn()->prepare("SELECT `collection`.`name`,`collection`.`displayname`,`collection`.`dbname`,`collection`.`templateID`,t.`name`,t.`description` FROM `collection` LEFT JOIN `template` AS t ON `collection`.`templateID` = t.`templateID`");
        $ret = $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    /**********************************************
     * Function: GET_TEMPLATES
     * Description: get all info from `template` table
     * Parameter(s): NONE
     * Return value(s): return assoc array of the templates
     ***********************************************/
    function GET_TEMPLATES()
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        $sth = $this->getConn()->prepare("SELECT `template`.`templateID`,`template`.`name`,`template`.`description` FROM `template`");
        $ret = $sth->execute();
        return $sth->fetchAll(PDO::FETCH_NUM);
    }

    /**********************************************
     * Function: COLLECTION_VALIDATE_NEW_ENTRY
     * Description: check if the new collection names (parameter name, displayname, dbname) has existed or not
     * Parameter(s): $iParName (string) : parameter name of the new collection
     *               $iDisplayName (string)  : display name of the new collection
     *               $iDBName (string) : DB Name of the new collection
     * Return value(s): return number of collection that satisfies the query's conditions ( = 0 mean no match, > 0 means a name has existed)
     ***********************************************/
    function COLLECTION_VALIDATE_NEW_ENTRY($iParName,$iDisplayName,$iDBName)
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        $sth = $this->getConn()->prepare("SELECT COUNT(*) FROM `collection` WHERE `name` = :parname OR `displayname` = :dname OR `dbname` = :dbname");
        $sth->bindParam(':parname',$iParName,PDO::PARAM_STR);
        $sth->bindParam(':dname',$iDisplayName,PDO::PARAM_STR);
        $sth->bindParam(':dbname',$iDBName,PDO::PARAM_STR);
        $ret = $sth->execute();
        if($ret)
            $ret = $sth->fetchColumn();
        return $ret;
    }

    /**********************************************
     * Function: COLLECTION_INSERT
     * Description: Insert new collection entry into `collection` table
     * Parameter(s): $iParName (string) : parameter name of the new collection
     *               $iDisplayName (string)  : display name of the new collection
     *               $iDBName (string) : DB Name of the new collection
     *               $iStorageDir (string) : Storage directory (full server path)
     *               $iThumbnailDir (string) : Thumbnail directory (partial path)
     *               $iTemplateID (int): new collection's template ID
     *               $iGeoRecDir  (string): georectification directory (full server path)
     * Return value(s): return false or the ID of new collection in `collection` table
     ***********************************************/
    function COLLECTION_INSERT($iParName,$iDisplayName,$iDBName,$iStorageDir,$iThumbnailDir,$iTemplateID,$iGeorecDir)
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        $sth = $this->getConn()->prepare("INSERT INTO `collection`(`name`,`displayname`,`dbname`,`storagedir`,`thumbnaildir`,`templateID`,`georecdir`) VALUES(:parname,:dname,:dbname,:storagedir,:thumbdir,:tID,:georecdir)");
        $sth->bindParam(':parname',$iParName,PDO::PARAM_STR);
        $sth->bindParam(':dname',$iDisplayName,PDO::PARAM_STR);
        $sth->bindParam(':dbname',$iDBName,PDO::PARAM_STR);
        $sth->bindParam(':storagedir',$iStorageDir,PDO::PARAM_STR);
        $sth->bindParam(':thumbdir',$iThumbnailDir,PDO::PARAM_STR);
        $sth->bindParam(':tID',$iTemplateID,PDO::PARAM_INT);
        $sth->bindParam(':georecdir',$iGeorecDir,PDO::PARAM_STR);
        $ret = $sth->execute();
        if($ret)
            return $this->getConn()->lastInsertId();
        return $ret;
    }

    /**********************************************
     * Function: COLLECTION_DELETE
     * Description: Delete the collection from the `collection` table (FOR ROLLBACK if fail to create new collection)
     * Parameter(s): $iColID (int) - collection ID of the collection to be deleted
     * Return value(s): false if fail
     ***********************************************/
    function COLLECTION_DELETE($iColID)
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        $sth = $this->getConn()->prepare("DELETE FROM `collection` WHERE `collectionID` = :colID");
        $sth->bindParam(':colID',$iColID,PDO::PARAM_INT);
        $ret = $sth->execute();
        return $ret;
    }

    //limit1 => select only one db
    /**********************************************
     * Function: GET_DBNAME_FROM_TEMPLATEID
     * Description: Get a database name from a collection or collections with a specified templateID (for validation)
     * Parameter(s): $iTemplateID (int) -  specified templateID
     *               $limit1 (boolean - Default: true) - select only 1 row or all rows
     * Return value(s): false if fail, otherwise, return the database name
     ***********************************************/
    function GET_DBNAME_FROM_TEMPLATEID($iTemplateID,$limit1 = true)
    {
        $this->getConn()->exec('USE ' . $this->maindb);
        if($limit1 == true)
            $sth = $this->getConn()->prepare("SELECT `dbname` FROM `collection` WHERE `templateID` = :tID ORDER BY `collectionID` ASC LIMIT 1");
        else $sth = $this->getConn()->prepare("SELECT `dbname` FROM `collection` WHERE `templateID` = :tID ORDER BY `collectionID` ASC");
        $sth->bindParam(':tID',$iTemplateID,PDO::PARAM_INT);
        $ret = $sth->execute();
        if($ret)
            return $sth->fetchColumn();
        return false;
    }

    //TO DO:
    //add mysql/bin into Path
    //increase maximum execution time in php.ini
    /**********************************************
     * Function: DATABASE_CLONE_NEW
     * Description: Run command line MySQL to clone new collection from the existing template and reset the AutoIncrement ID of all the tables of the new database
     * Parameter(s): $iNewDBName (string) - new databasename of the clone
     *               $iExistingDBName (string) - existing databasename to be cloned from
     * Return value(s): true if success, false if fail
     ***********************************************/
    function DATABASE_CLONE_NEW($iNewDBName,$iExistingDBName)
    {
        $query = "CREATE DATABASE IF NOT EXISTS " . $iNewDBName;
        $sth = $this->getConn()->query($query);
        if($sth) {
            //export routines,triggers and schema (no data) of existing db
            $export = "mysqldump -d --routines --triggers -u " . $this->getUser() .  " -p" . $this->getPwd() . " " . $iExistingDBName . " > temp.sql 2>&1";
            exec($export,$output);
            $import = "mysql -u " . $this->getUser() . " -p" . $this->getPwd() . " " . $iNewDBName . " < temp.sql 2>&1";
            exec($import,$output);
            //delete temp file
            $delete = "del /f temp.sql 2>&1";
            exec($delete,$output);

            //reset Auto Increment on all tables (starts with 1)
            $this->DATABASE_RESET_TABLES_AUTOINCREMENT($iNewDBName);
            return true;
        }
        return false;
    }

    //unsafe
    /**********************************************
     * Function: DATABASE_RESET_TABLES_AUTOINCREMENT
     * Description: Reset AutoIncrement ID of all tables from the specified database (be cautious)
     * Parameter(s): $iDBName (string) - database name of the database to reset AI
     * Return value(s): true if success
     ***********************************************/
    function DATABASE_RESET_TABLES_AUTOINCREMENT($iDBName)
    {
        $this->getConn()->query("USE " . $iDBName);
        $sth = $this->getConn()->prepare("SHOW TABLES");
        $sth->execute();
        $table_lists = $sth->fetchAll(PDO::FETCH_NUM);
        foreach($table_lists as $table)
        {
            $sth = $this->getConn()->query("TRUNCATE TABLE " . $table[0]);
            $sth->execute();
        }
        return true;
    }

}
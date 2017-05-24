<?php
//This trait provides general functions to select or update fields related to TDL in `document` table of every collections
/**
 * Created by PhpStorm.
 * User: snguyen1
 * Date: 2/15/2017
 * Time: 12:22 PM
 */

//this interface is currently empty
interface TDLPublishDB
{
}

//Functions belong to trait can be used for Publishing of different templates
trait TDLPublishTrait
{
    /**********************************************
     * Function: PUBLISHING_DOCUMENT_GET_DSPACE_INFO
     * Description: Collect dspacePublished (Status), dspaceURI (TDL's URI), and dspaceID (TDL ID from published document) in `document` table of a given documentID. Note: need to switch to the currently working database
     * Parameter(s): $docID (int) - BandoCat's documentID in `document` table
     * Return value(s): an array  of dSpaceURI,dspaceID, and dspacePublished. return null if fail
     ***********************************************/
    function PUBLISHING_DOCUMENT_GET_DSPACE_INFO($docID)
    {
        $sth = $this->getConn()->prepare("SELECT `dspacePublished`,`dspaceURI`,`dspaceID` FROM `document` WHERE `documentID` = :docID LIMIT 1");
        $sth->bindParam(':docID',$docID,PDO::PARAM_INT);
        $ret = $sth->execute();
        if($ret)
            return $sth->fetch(PDO::FETCH_ASSOC);
        //print_r($sth->errorInfo());
        return null;
    }

    /**********************************************
     * Function: PUBLISHING_DOCUMENT_GET_NEXT_IN_QUEUE_ID
     * Description: get the ID of the next document in `document` table that has `dspacePublished` value = 2 (in Queue)
     * Parameter(s): None
     * Return value(s): documentID, return null if fail
     ***********************************************/
    function PUBLISHING_DOCUMENT_GET_NEXT_IN_QUEUE_ID()
    {
        $sth = $this->getConn()->prepare("SELECT `documentID` FROM `document` WHERE `dspacePublished` = 2 ORDER BY `documentID` ASC LIMIT 1");
        $ret = $sth->execute();
        if($ret)
            return $sth->fetchColumn();
        return null;
    }

    /**********************************************
     * Function: PUBLISHING_DOCUMENT_GET_PUBLISHING_ID
     * Description: get the ID of the next document in `document` table that has `dspacePublished` value = 10 (Uploading/Publishing), value = 11 (Abandoned Job/ Uploading)
     * Parameter(s): None
     * Return value(s): documentID, return null if fail
     ***********************************************/
    function PUBLISHING_DOCUMENT_GET_PUBLISHING_ID()
    {
            $sth = $this->getConn()->prepare("SELECT `documentID` FROM `document` WHERE `dspacePublished` = 10 OR `dspacePublished` = 11 ORDER BY `documentID` ASC LIMIT 1");
            $ret = $sth->execute();
            if($ret)
                return $sth->fetchColumn();
            return null;
    }

    //Status code: 2 = In publish Queue, 1 = Published, 0 = Not published, 10 = Publishing, 11 = Publishing with Error/Abandoned Job
    /**********************************************
     * Function: PUBLISHING_DOCUMENT_UPDATE_STATUS
     * Description: update the value of `dspacePublished` to the new status code ($status)
     * Parameter(s): $docID (int) - documentID to update the new status code
     *               $status (int) - new status code
     * Return value(s): false if fail
     ***********************************************/
    function PUBLISHING_DOCUMENT_UPDATE_STATUS($docID,$status){
        $sth = $this->getConn()->prepare("UPDATE `document` SET `dspacePublished` = :stat WHERE `documentID` = :docID");
        $sth->bindParam(':stat',$status,PDO::PARAM_INT);
        $sth->bindParam(':docID',$docID,PDO::PARAM_INT);
        $ret = $sth->execute();
//        if(!$ret)
//            print_r($sth->errorInfo());
        return $ret;
    }

    /**********************************************
     * Function: PUBLISHING_GET_PUBLISH_QUEUE
     * Description: return `documentID`, `libraryindex`, and `dspacePublished` status code from `document` table of documents in the Queue and publishing (status code = 2 or 10)
     * Parameter(s): None
     * Return value(s): false if fail, otherwise return an associative array of documents
     ***********************************************/
    function PUBLISHING_GET_PUBLISH_QUEUE()
    {
        $sth = $this->getConn()->prepare("SELECT `documentID`,`libraryindex`,`dspacePublished` FROM `document` WHERE `dspacePublished` = 2 OR `dspacePublished` = 10 ORDER BY `dspacePublished` DESC,`documentID` ASC");
        $ret = $sth->execute();
        //if(!$ret)
        //    print_r($sth->errorInfo());
        if($ret){
            return $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    //$hasGeoRec: for template that has GeoRectification feature such as map, needs to check if the maps has been either rectified or Not Rectifiable before pushing
    /**********************************************
     * Function: PUBLISHING_PUSH_TO_QUEUE
     * Description: Push a number of finalized documents (fully reviewed and rectified) in a collection (database) to the Queue
     * Parameter(s): $howMany (int - default: NULL) - number of documents to be pushed to the queue (null == push ALL)
     *               $hasGeoRec (boolean - default: false) - true if the collection has GeoRectification feature enabled, false if not
     * Return value(s): false if fail, otherwise return an associative array of documents
     ***********************************************/
    function PUBLISHING_PUSH_TO_QUEUE($howMany = null,$hasGeoRec = false)
    {
        $query = "UPDATE `document` SET `dspacePublished`  = 2 WHERE `dspacePublished` = 0 ";
        if($hasGeoRec)
        {
            $query .= " AND (`geoRecFrontStatus` = 1 OR `geoRecFrontStatus` = 2) AND ((`geoRecBackStatus` = 1 OR `geoRecBackStatus` = 2) OR (`filenameback` = '' OR `filenameback` IS NULL)) ";
        }
        $query .= "ORDER BY `documentID` ASC ";
        if($howMany != null)
            $query = $query . " LIMIT " . $howMany;
        //$howMany = null means publishing all documents in a collections
        $sth = $this->getConn()->prepare($query);
        $ret = $sth->execute();
        //if(!$ret)
        //    print_r($sth->errorInfo());
        return $ret;
    }

    /**********************************************
     * Function: PUBLISHING_RESET_QUEUE
     * Description: Set `dspacePublished` code of ALL In Queue documents to 0 (not published)
     * Parameter(s): None
     * Return value(s): False if fail
     ***********************************************/
    function PUBLISHING_RESET_QUEUE()
    {
        $sth = $this->getConn()->prepare("UPDATE `document` SET `dspacePublished` = 0 WHERE `dspacePublished` = 2");
        $ret = $sth->execute();
        //if(!$ret)
        //    print_r($sth->errorInfo());
        return $ret;
    }

    /**********************************************
     * Function: PUBLISHING_DOCUMENT_TDL_UPDATE
     * Description: Update `dspaceID`, `dspaceURI` or a document given `documentID`
     * Parameter(s):
     *      $docID (int) - documentID in `document` table
     *      $dspaceID (int) - TDL ID return from the return header of the CURL request
     *      $dspaceURI (string) - TDL URL from the return header of the CURL request
     * Return value(s): False if fail
     ***********************************************/
    function PUBLISHING_DOCUMENT_TDL_UPDATE($docID,$dspaceID,$dspaceURI)
    {
        $sth = $this->getConn()->prepare("UPDATE `document` SET `dspaceID` = :dspaceID,`dspaceURI` = :dspaceURI WHERE `documentID` = :docID");
        $sth->bindParam(':dspaceID',$dspaceID,PDO::PARAM_INT);
        $sth->bindParam(':dspaceURI',$dspaceURI,PDO::PARAM_STR);
        $sth->bindParam(':docID',$docID,PDO::PARAM_INT);
        $ret = $sth->execute();
        //if(!$ret)
            //print_r($sth->errorInfo());
        return $ret;
    }

}
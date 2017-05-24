<?php

interface GeoRectificationDB
{

}
//Functions belong to trait can be used for georectification of different templates
trait GeoRectificationTrait
{

    /**********************************************
    Function: GEOREC_ENTRIES_SELECT
    Description: Get all entries in georectification table from a document ID and isback (type of map: front/back)
    Parameter(s):
     *$docID (int) - unique document ID shared between document table and georectification table
     * $isback (bool) - true/1 means it is a back scan, false/0 if it is a front scan
    Return value(s): return an array of information, return false if error occurs
     ***********************************************/
    public function GEOREC_ENTRIES_SELECT($docID,$isBack)
    {
        $sth = $this->getConn()->prepare("SELECT `pointnumber`,ST_ASTEXT(`maplatlong`),ST_ASTEXT(`rasterlatlong`),ST_ASTEXT(`rasterXY`) FROM `georectification` WHERE `documentID` = :docID AND `isback` = :isback");
        $sth->bindParam(":docID",$docID,PDO::PARAM_INT);
        $sth->bindParam(":isback",$isBack,PDO::PARAM_BOOL);
        $ret = $sth->execute();
        if(!$ret)
            //print_r($sth->errorInfo());
            return false;
        $retval = $sth->fetchAll(PDO::FETCH_NUM);
        $output = array();
        //return: array of array(pointnumber,maplat,maplong,rasterlat,rasterlong,rasterX,rasterY)
        foreach($retval as $val)
        {
            $maplat = substr($val[1],strpos($val[1],"(") + 1, strpos($val[1]," ") - strpos($val[1],"(") -1);
            $maplong = substr($val[1],strpos($val[1]," ") +1, strpos($val[1],")") - strpos($val[1]," ") - 1);
            $rasterlat = substr($val[2],strpos($val[2],"(") + 1, strpos($val[2]," ") - strpos($val[2],"(") - 1);
            $rasterlong = substr($val[2],strpos($val[2]," ") + 1, strpos($val[2],")") - strpos($val[2]," ") - 1);
            $rasterX = substr($val[3],strpos($val[3],"(") + 1, strpos($val[3]," ") - strpos($val[3],"(") - 1);
            $rasterY = substr($val[3],strpos($val[3]," ") + 1, strpos($val[3],")") - strpos($val[3]," ") - 1);
            array_push($output,array($val[0],$maplat,$maplong,$rasterlat,$rasterlong,$rasterX,$rasterY));
        }
        return $output;
    }
    //
    /**********************************************
    Function: GEOREC_ENTRIES_DELETE
    Description: Delete all entries in georectification table if it has documentID equal to $docID
    Parameter(s):
     * $docID (int) - rows that have this documents ID in georectification will be deleted
    Return value(s): return number of row affected (deleted), 0 means there is no such row
     ***********************************************/
    public function GEOREC_ENTRIES_DELETE($docID)
    {
        $sth = $this->getConn()->prepare("DELETE FROM `georectification` WHERE `documentID` = :docID");
        $sth->bindParam(":docID",$docID,PDO::PARAM_INT);
        $ret = $sth->execute();
        return $ret;
    }
    /**********************************************
    Function: GEOREC_ENTRY_INSERT
    Description: Insert new entry into georectification table
    Parameter(s):
     * $docID (int) - document ID to be inserted
     * $isBack (int) - 1 = back, 0 = front
     * $pointNumber (int)
     * $iMapLat (decimal/string): latitude of map coordinate
     * $iMapLong (decimal/string): longitude of map coordinate
     * $iRasterLat (decimal/string): latitude of raster coordinate
     * $iRasterLong (decimal/string): longitude of raster coordinate
     * $iRasterX (decimal/string): X coordinate of raster container point
     * $iRasterY (decimal/string): Y coordinate of raster container point
    Return value(s): return false if fail
     ***********************************************/
    public function GEOREC_ENTRY_INSERT($docID,$isBack,$pointNumber,$iMapLat,$iMapLong,$iRasterLat,$iRasterLong,$iRasterX,$iRasterY)
    {
        //create POINT string from ($iLat $iLong) and ($iRasterX,$iRasterY)
        $mapLatLong = "POINT( $iMapLat $iMapLong )";
        $rasterLatLong = "POINT( $iRasterLat $iRasterLong )";
        $rasterXY =  "POINT( $iRasterX $iRasterY)";

        $sth = $this->getConn()->prepare("INSERT INTO `georectification`(`documentID`,`pointnumber`,`isback`,`maplatlong`,`rasterlatlong`,`rasterXY`) VALUES(:docID,:pointnumber,:isback,ST_GeomFromText(:maplatlong),ST_GeomFromText(:rasterlatlong),ST_GeomFromText(:rasterXY))");
        $sth->bindParam(":docID",$docID,PDO::PARAM_INT);
        $sth->bindParam(":pointnumber",$pointNumber,PDO::PARAM_INT);
        $sth->bindParam(":isback",$isBack,PDO::PARAM_BOOL);
        $sth->bindParam(":maplatlong",$mapLatLong,PDO::PARAM_STR);
        $sth->bindParam(":rasterlatlong",$rasterLatLong,PDO::PARAM_STR);
        $sth->bindParam(":rasterXY",$rasterXY,PDO::PARAM_STR);
        $ret = $sth->execute();
        //if(!$ret)
        //    print_r($sth->errorInfo());
        return $ret;
    }
    /**********************************************
    Function: DOCUMENT_GEORECSTATUS_UPDATE
    Description: Update status of geoRecStatus field in document table
    Parameter(s):
     * $docID (int) - update info of row that has documentID = $docID on document table
     * $isback (int) - Identify the type of map (true == back, false == front)
     * $iStatusCode(tinyint) - (0 = not rectified, 1 = rectified, 2 = not rectifiable, 3 = needs review)
    Return value(s): return false if fail
     ***********************************************/
    public function DOCUMENT_GEORECSTATUS_UPDATE($docID,$isback,$iStatusCode)
    {
        if($isback)
            $sth = $this->getConn()->prepare("UPDATE `document` SET `geoRecBackStatus` = :statuscode WHERE `documentID` = :docID");
        else $sth = $this->getConn()->prepare("UPDATE `document` SET `geoRecFrontStatus` = :statuscode WHERE `documentID` = :docID");
        $sth->bindParam(":docID",$docID,PDO::PARAM_INT);
        $sth->bindParam(":statuscode",$iStatusCode,PDO::PARAM_INT);
        $ret = $sth->execute();
        return $ret;
    }
    /**********************************************
    Function: DOCUMENT_GEORECSTATUS_SELECT
    Description: GET status of geoRecStatus field in document table
    Parameter(s):
     * $docID (int) - GET info of row that has documentID = $docID on document table
     * $isback (int) - Identify the type of map (true == back, false == front)
    Return value(s): false if error occurs, else return geo rec status number  (0 = not rectified, 1 = rectified, 2 = not rectifiable, 3 = needs review)
     ***********************************************/
    public function DOCUMENT_GEORECSTATUS_SELECT($docID,$isback)
    {
        if($isback)
            $sth = $this->getConn()->prepare("SELECT `geoRecBackStatus` FROM `document` WHERE `documentID` = :docID");
        else $sth = $this->getConn()->prepare("SELECT `geoRecFrontStatus` FROM `document` WHERE `documentID` = :docID");
        $sth->bindParam(":docID",$docID,PDO::PARAM_INT);
        $ret = $sth->execute();
        if(!$ret)
            return false;
        return $sth->fetchColumn();
    }
    /**********************************************
    Function: DOCUMENT_GEORECPATHS_UPDATE
    Description: Update KMZ and GeoTIFFs directory path for this document in document table
    Parameter(s):
     * $docID (int) - document to be updated on document table
     * $FrontKMZPath (string) - directory path for KMZ of front scan
     * $FrontGeoTIFFPath (string) - directory path for GeoTIFF of front scan
     * $BackKMZPath (string) - directory path for KMZ of back scan
     * $BackGeoTIFFPathPath (string) - directory path for GeoTIFF of back scan
    Return value(s): return false if fail
     ***********************************************/
    public function DOCUMENT_GEORECPATHS_UPDATE($docID,$FrontKMZPath,$FrontGeoTIFFPath,$BackKMZPath,$BackGeoTIFFPath)
    {
        if($FrontKMZPath == null && $FrontGeoTIFFPath == null) //update back
        {
            $sth = $this->getConn()->prepare("UPDATE `document` SET `georecBackDirKMZ` = :backKMZ,`georecBackDirGeoTIFF` = :backGeoTIFF WHERE `documentID` = :docID");
            $sth->bindParam(":backKMZ",$BackKMZPath,PDO::PARAM_STR);
            $sth->bindParam(":backGeoTIFF",$BackGeoTIFFPath,PDO::PARAM_STR);
        }
            else //update front
        {
            $sth = $this->getConn()->prepare("UPDATE `document` SET `georecFrontDirKMZ` = :frontKMZ,`georecFrontDirGeoTIFF` = :frontGeoTIFF WHERE `documentID` = :docID");
            $sth->bindParam(":frontKMZ",$FrontKMZPath,PDO::PARAM_STR);
            $sth->bindParam(":frontGeoTIFF",$FrontGeoTIFFPath,PDO::PARAM_STR);
        }
        $sth->bindParam(":docID",$docID,PDO::PARAM_INT);

        $ret = $sth->execute();
        return $ret;
    }

    /**********************************************
    Function: DOCUMENT_GEOREC_INFO_SELECT
    Description: returning assoc array of status, geotiff & kmz paths of the front and back (if available) of the document, given the document ID
    Parameter(s):
     * $docID (int) - document to be selected on document table
    Return value(s): return false if fail, return an assoc array of georec info if success
     ***********************************************/
    public function DOCUMENT_GEOREC_INFO_SELECT($docID)
    {
        $sth = $this->getConn()->prepare("SELECT `geoRecFrontStatus`,`georecFrontDirKMZ`,`georecFrontDirGeoTIFF`,`geoRecBackStatus`,`georecBackDirKMZ`,`georecBackDirGeoTIFF` FROM `document` WHERE `documentID` = :docID");
        $sth->bindParam(":docID",$docID,PDO::PARAM_INT);
        $ret = $sth->execute();
        if($ret)
            return $sth->fetch(PDO::FETCH_ASSOC);
        return false;
    }

}
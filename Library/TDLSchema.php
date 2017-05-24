<?php
//This class provides functions to convert document schema from internal BandoCat to TDL
class TDLSchema
{
    //members:
    //This fields will be attached to the metadata of EVERY item posted to TDL server
    public $rightsholder1 = "Special Collections and Archives, Mary and Jeff Bell Library, Texas A&M University-Corpus Christi.";
    public $rightsholder2 = "Conrad Blucher Institute, Texas A&M University-Corpus Christi.";
    public $rightsstatement = "This material is made available for use in research, teaching, and private study, pursuant to U.S. Copyright law. The user assumes full responsibility for any use of the materials, including but not limited to, infringement of copyright and publication rights of reproduced materials. Any materials used should be fully credited with the source. Permission for publication of scanned images, GeoTIFF, KMZ, and CSV files must be secured with the Head of Special Collections and Archives.";
    function __construct()
    {
    }


    /**********************************************
     * Function: convertSchema
     * Description: A global Schema convert function. each CASE (template) in the switch statement will performed different convert function.
     * //NOTE: to create new convert schema for a new template, specify the templateID in a new CASE statement and create a new function called: convert[NEWTEMPLATE]schema($doc) in this class
     * Parameter(s): $templateID (int) - $templateID of the collection return from `collection` table in bandocatdb
     *               $doc (int) - document ID (internal BandoCat `documentID` in `document` table)
     *              $isPOST (boolean - DEFAULT: TRUE) - true if post new item, false if update item (different ways to convert)
     * Return value(s): return the converted schema
     ***********************************************/
    function convertSchema($templateID,$doc,$isPOST = true)
    {
        switch($templateID)
        {
            case "1": //map
                $array =  $this->convertMapSchema($doc);
                break;
            case "2":
                $array =  $this->convertJobFolderWholeSchema($doc); //should be publishing PDF file of a whole job folder
                break;
            case "3":
                $array = $this->convertFieldBookWholeSchema($doc); //should be publishing PDF file of a whole Field Book
                break;
            case "4": //indices template
                break;
            default:
                return false;
        }

        //POST = PUBLISH ($isPOST = true)
        //PUT = UPDATE ($isPOST = false)
        if($isPOST) //POST
        {
            $output["metadata"] = $array;
            return json_encode($output);
        }
        else return json_encode($array); //PUT = UPDATE
    }


    //$document is an associative array
    /**********************************************
     * Function: convertMapSchema
     * Description: convert a BandoCat's Map document into Map's TDL schema
     * Parameter(s): $doc (int) - document ID (internal BandoCat `documentID` in `document` table)
     * Return value(s): return the converted schema
     ***********************************************/
    function convertMapSchema($doc)
    {
        //preprocess
//        if($doc["FieldBookNumber"] == 0)
//            $doc["FieldBookNumber"] = "";
//        if($doc["FieldBookPage"] == 0)
//            $doc["FieldBookPage"] = "";

        //only update a field when it has data (not empty)

        $numberOfPages = "1 page";
        if($doc["FileNameBack"] != "")
            $numberOfPages = "2 pages";

        $fieldbookrelation = "";
        if($doc["FieldBookNumber"] != "" && trim($doc["FieldBookNumber"]) != "0")
        {
            $fieldbookrelation = "Book " . $doc["FieldBookNumber"] . " ";
            if($doc["FieldBookPage"] != "")
                $fieldbookrelation .= "Page " . $doc["FieldBookPage"];
        }


        $output = array();
        $this->addField($output,"dc.identifier.other",str_replace("-_","-",$doc['LibraryIndex']));
        $this->addField($output,"dc.title",str_replace("-_","-",$doc['Title']));
        $this->addField($output,"dc.coverage.temporal",$this->mergeStrings($this->convertDate($doc["StartDate"]),$this->convertDate($doc["EndDate"])));
        $this->addField($output,"dc.type",$doc["Type"]);
            //array("key" => "dc.relation.isbasedon","value" => $this->mergeStrings($doc["FieldBookNumber"],$doc["FieldBookPage"])),
        $this->addField($output,"dc.relation",$fieldbookrelation);
        $this->addField($output,"dc.format.extent",$numberOfPages);
        $this->addField($output,"dc.format.medium",$doc["Medium"]);
        $this->addField($output,"dc.format.mimetype","image/jpeg");
        $this->addField($output,"dc.rights.holder",$this->rightsholder1);
        $this->addField($output,"dc.rights.holder",$this->rightsholder2);
        $this->addField($output,"dc.rights",$this->rightsstatement);
        $this->addField($output,"mc.subtitle",$doc["Subtitle"]);
        $this->addField($output,"mc.mapscale",$doc["MapScale"]);
        $this->addField($output,"mc.note",$doc["Comments"]);
        $this->addField($output,"mc.customer",$doc["CustomerName"]);
        $this->addField($output,"mc.collection",$doc["TDLCollection"]);
        $this->addField($output,"dc.identifier.citation",$doc['TDLcitation']);
        $this->addField($output,"mc.collectionid",$doc['TDLnumber']);
        $this->addField($output,"mc.collection.sub",$doc['TDLsubgroup']);

        $this->addField($output,"dc.contributor",$doc["TDLAuthorName"]);
        $this->addField($output,"dc.contributor",$doc["CompanyName"]);


        //need FieldBook and Job Folder relation (dc.relation title) and PDF (dc.relation.url)

        return $output;
    }

    //$document is an associative array
    //NOT FINISHED!!!!
    /**********************************************
     * Function: convertJobFolderWholeSchema
     * Description: convert a BandoCat's folder document into JobFolder's TDL schema
     * Parameter(s): $doc (int) - document ID (internal BandoCat `documentID` in `document` table)
     * Return value(s): return the converted schema
     ***********************************************/
    function convertJobFolderWholeSchema($doc)
    {
        //preprocess

        $numberOfPages = "1 page";
        if($doc["FileNameBack"] != "")
            $numberOfPages = "2 pages";

        $output = array(
            array("key" => "dc.identifier.other", "value" => str_replace("-_","-",$doc['LibraryIndex'])),
            array("key" => "dc.title", "value" => str_replace("-_","-",$doc['Title'])),
            array("key" => "dc.coverage.temporal","value" => $this->mergeStrings($this->convertDate($doc["StartDate"]),$this->convertDate($doc["EndDate"]))),
            //array("key" => "dc.relation.isbasedon","value" => $this->mergeStrings($doc["FieldBookNumber"],$doc["FieldBookPage"])),
            array("key" => "dc.format.extent","value" => $numberOfPages),
            array("key" => "mc.classification","value" => $doc['Classification']),
            array("key" => "mc.classification.note","value" => $doc['ClassificationComment']),
            array("key" => "dc.format.mimetype","value" => ""),
            array("key" => "dc.rights.holder","value" => $this->rightsholder),
            array("key" => "dc.rights","value" => $this->rightsstatement),
            array("key" => "mc.note","value" => $doc["Comments"]),
            array("key" => "mc.collection","value" => $doc["TDLCollection"]),
            array("key" => "dc.identifier.citation","value" => $doc['TDLcitation']),
            array("key" => "mc.collectionid","value" => $doc['TDLnumber']),
            array("key" => "mc.collection.sub","value" => $doc['TDLsubgroup'])
        );
        //adding multiple authors
        foreach($doc["Authors"] as $author)
            array_push($output,array("key" => "dc.contributor.author","value" => $author[0]));

        //related fieldbook (PDF URL) and title
        //related Map (title and URL)
        return $output;
    }

    //NOT FINISHED!!!!
    /**********************************************
     * Function: convertFieldBookWholeSchema
     * Description: convert a BandoCat's Field Book document into Field Book's TDL schema
     * Parameter(s): $doc (int) - document ID (internal BandoCat `documentID` in `document` table)
     * Return value(s): return the converted schema
     ***********************************************/
    function convertFieldBookWholeSchema($doc)
    {
        //extend: number of page in what??? Book or number of scan???

        //preprocess
        $output = array();

        $this->addField($output,"dc.identifier.other",str_replace("-_","-",$doc['LibraryIndex']));
        $this->addField($output,"dc.relation.ispartof",$doc["BookTitle"]);
        $this->addField($output,"mc.collection",$doc["Collection"]);
        $this->addField($output,"dc.coverage.temporal",$this->mergeStrings($this->convertDate($doc["StartDate"]),$this->convertDate($doc["EndDate"])));
            //array("key" => "dc.relation.isbasedon","value" => $this->mergeStrings($doc["FieldBookNumber"],$doc["FieldBookPage"])),
        $this->addField($output,"mc.pagenumber",$doc["IndexedPage"]);
        $this->addField($output,"dc.relation.ispartof",$doc['JobNumber']);
        $this->addField($output,"dc.relation.ispartof",$doc['JobTitle']);
        $this->addField($output,"dc.format.mimetype","image/jpeg");
        $this->addField($output,"dc.rights.holder",$this->rightsholder);
        $this->addField($output,"dc.rights",$this->rightsstatement);
        $this->addField($output,"mc.note",$doc["Comments"]);
        $this->addField($output,"mc.collection",$doc["TDLCollection"]);
        $this->addField($output,"dc.identifier.citation",$doc['TDLcitation']);
        $this->addField($output,"mc.collectionid",$doc['TDLnumber']);
        $this->addField($output,"mc.collection.sub",$doc['TDLsubgroup']);

        //adding multiple crew members
        foreach($doc["Crews"] as $crew)
            array_push($output,array("key" => "dc.contributor.author","value" => $crew[0]));
        //missing: relation to Map and Job Folder (URL to the PDF and title)

        return $output;
    }

    //ignore if the field is empty
    /**********************************************
     * Function: addField
     * Description: add a Field into the array of converted schema - if the field is empty, it will not be pushed (except special cases in the SWITCH / CASE statement)
     * Parameter(s): $&arr (array - reference) - the field will be added to this array (pass by reference)
     *               $key (string) - key name of the field
     *               $val (string) - value of the $key field
     * Return value(s): None
     ***********************************************/
    function addField(&$arr,$key,$val)
    {
        if(trim($val) !== "")
            array_push($arr,array("key" => $key,"value" => $val));
        else //empty
        {
            switch($key)
            {
                case "dc.coverage.temporal": //start date & end date
                    array_push($arr,array("key" => $key,"value" => "undated"));
                    break;
                default:break;
            }
        }
    }


    //convert date from MM/DD/YYYY
    //NEW: Convert to YYYY
    //return "" if date is 00/00/0000
    /**********************************************
     * Function: convertDate
     * Description: convert date from MM/DD/YYYY to YYYY-DD-MM
     * Parameter(s): $date (string) - the date to be converted
     * Return value(s): return "" if date is 00/00/0000
     ***********************************************/
    function convertDate($date)
    {
        if($date == "00/00/0000")
            return "";
        $dateArr = explode("/",$date); // [0] = MM, [1] = DD, [2] = YYYY
        if($dateArr[2] == "0000" )
            return "";
        else
        {
            if($dateArr[0] == "00" && $dateArr[1] == "00") return $dateArr[2]; //return YYYY
            if($dateArr[1] == "00") return $dateArr[2] . "-" . $dateArr[0]; //return YYYY-MM
            return $dateArr[2] . "-" . $dateArr[0] . "-" . $dateArr[1]; //return YYYY-DD-MM
        }
        return $dateArr[2] . "-" . $dateArr[0] . "-" . $dateArr[1]; //return YYYY-DD-MM
    }

    /**********************************************
     * Function: mergeStrings
     * Description: merge strings such as (Start Date,EndDate) to StartDate/EndDate, if one of them is empty, return StartDate or EndDate
     * Parameter(s): $str1 (string) - first input string
     *               $str2 (string) - second input string
     * Return value(s): return the merged string
     ***********************************************/
    function mergeStrings($str1,$str2)
    {
        $temp1 = str_replace(" ","",$str1);
        $temp2 = str_replace(" ","",$str2);
        if($temp1 == "" && $temp2 != "")
            return $temp2;
        if($temp2 != "" && $temp2 == "")
            return $temp1;
        if($temp1 == "" && $temp2 == "")
            return "";
        if($temp1 != "" && $temp2 != "")
            return $temp1 . "/" . $temp2;
    }

    //SUGGESTION: THIS FUNCTION CAN BE REPLACED LATER BY using json_decode($header) and return the value with the specified key using json_decode($header)[$key}
    /**********************************************
     * Function: getHeaderValueFromKey
     * Description: extracted the value of a key from the HTTP returned header
     * Parameter(s): $header (string) - the returned header
     *               $key (string) - the value in the header
     * Return value(s): return the value of the $key in the $header
     ***********************************************/
    function getHeaderValueFromKey($header,$key)
    {
        $temp = strpos($header,$key,0);
        $temp2 = strpos($header,":",$temp) + 1;
        $temp3 = strpos($header,",",$temp2);

        return str_replace('"',"",substr($header,$temp2,$temp3 - $temp2));
    }

}
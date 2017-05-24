<?php

/**
 * Created by PhpStorm.
 * User: snguyen1
 * Date: 10/13/2016
 * Time: 1:38 PM
 */
class ControlsRender
{

    /**
     * Functions constructor.
     */
    public function __construct()
    {
    }

    /**********************************************
     * Function: getDataList
     * Description: gets an array of data items
     * Parameter(s):
     * $array (in array) - array of data items
     * Return value(s): DDL options
     ***********************************************/
    //Render HTML controls
    function getDataList($array)
    {
        foreach($array as $item)
            echo '<option value="' . $item[0]. '">' . $item[0] . '</option>';
    }

    /**********************************************
     * Function: GET_DDL_MONTH
     * Description: gets an the month of the DDL
     * Parameter(s):
     * $input (in array) -
     * Return value(s): string
     ***********************************************/
    function GET_DDL_MONTH($input)
    {
        if($input == null || $input == '00' || $input == '0' || $input == '')
            echo '<option selected="selected" value="00">Month</option>';
        else echo '<option value="00">Month</option>';
        for($num = 1; $num <= 12; $num++)
        {
            if($num < 10)
                $num = '0' . $num;
            if($input == $num)
                echo '<option selected = "selected" >'.$input.'</option>';
            else
                echo '<option value="'.$num.'">'.$num.'</option>';
        }

    }
    /**********************************************
     * Function: GET_DDL_DAY
     * Description: gets an the day of the DDL
     * Parameter(s):
     * $input (in array) -
     * Return value(s): string
     ***********************************************/
    function GET_DDL_DAY($input)
    {
        if($input == null || $input == '00' || $input == '0' || $input == '')
            echo '<option selected="selected" value="00">Day</option>';
        else echo '<option value="00">Day</option>';
        for($num= 1; $num<=31; $num++)
        {
            if($num < 10)
                $num = '0' . $num;
            if($input == $num)
                echo '<option selected = "selected" >'.$input.'</option>';
            else
                echo '<option value="'.$num.'">'.$num.'</option>';
        }
    }
    /**********************************************
     * Function: GET_DDL_YEAR
     * Description: gets an the year of the DDL
     * Parameter(s):
     * $input (in array) -
     * Return value(s): string
     ***********************************************/
    function GET_DDL_YEAR($input)
    {
        if($input == null || $input == '0000' || $input == '0' || $input == '')
            echo '<option selected="selected" value="0000">Year</option>';
        else echo '<option value="0000">Year</option>';
        $current = date("Y");
        for($num=1750; $num<=$current; $num++)
        {
            if($input == $num)
                echo '<option selected = "selected" >'.$input.'</option>';
            else
                echo '<option value="'.$num.'">'.$num.'</option>';
        }
    }

    /**********************************************
     * Function: GET_DDL
     * Description: gets selected ddl option
     * Parameter(s):
     * $input (in array) -
     * Return value(s): string
     ***********************************************/
    function GET_DDL($array,$selected)
    {
        echo '<option value="">Select</option>';
        foreach ($array as $item) {
            if ($selected == $item[0])
                echo '<option value="' . $item[0] . '" selected>' . $item[0] . '</option>';
            else echo '<option value="' . $item[0] . '">' . $item[0] . '</option>';
        }
    }

    /**********************************************
     * Function: GET_DDL_ROLE
     * Description: gets DDL role
     * Parameter(s):
     * $array (in array) -
     * $selected (in array) -
     * Return value(s): string
     ***********************************************/

    function GET_DDL_ROLE($array,$selected)
    {
        echo '<option value="">Select</option>';
        foreach ($array as $item)
        {
            if ($selected == $item['name'])
                echo '<option value="' . $item['roleID'] . '" selected>' . $item['name'] . '</option>';
            else echo '<option value="' . $item['roleID'] . '">' . $item['name'] . '</option>';
        }
    }

    //FETCH FROM PHP ARRAY
    function GET_DDL2($array,$selected)
    {
        echo '<option value="">Select</option>';
        foreach ($array as $item) {
            if ($selected == $item)
                echo '<option value="' . $item . '" selected>' . $item . '</option>';
            else echo '<option value="' . $item . '">' . $item . '</option>';
        }
    }

    //FETCH FROM DB, $item[0] = id, $item[1] = name
    function GET_DDL3($array,$selected)
    {
        echo '<option value="">Select</option>';
        foreach ($array as $item) {
            if ($selected == $item[0])
                echo '<option value="' . $item[0] . '" selected>' . $item[1] . '</option>';
            else echo '<option value="' . $item[0] . '">' . $item[1] . '</option>';
        }
    }

    //Render collection dropdown
    function GET_DDL_COLLECTION($array,$selected)
    {
        echo '<option value="">Select</option>';
        foreach ($array as $item) {
            if ($selected == $item['name'])
                echo '<option value="' . $item['name'] . '" selected>' . $item['displayname'] . '</option>';
            else echo '<option value="' . $item['name'] . '">' . $item['displayname'] . '</option>';
        }
    }
    //Render User dropdown
    function GET_DDL_USER($array,$selected)
    {
        foreach ($array as $item) {
            if ($selected == $item)
                echo '<option value="' . $item['userID'] . '" selected>' . $item['username']. '</option>';
            else echo '<option value="' . $item['userID'] . '">' . $item['username']. '</option>';
        }
    }
    //Render Mapkind dropdown
    function GET_DDL_MAPKIND($array,$selected)
    {
        foreach ($array as $item) {
            if ($selected == $item)
                echo '<option value="' . $item['mapkindID'] . '" selected>' . $item['mapkindname']. '</option>';
            else echo '<option value="' . $item['mapkindID'] . '">' . $item['mapkindname']. '</option>';
        }
    }
    /*Function: DISPLAY_LOG_INFO
     *Description: Receive an array of logs of a document as the parameter and render html element on the web page
     *              This function is only used for review.php of every Template
     *             Note: jQuery and jQueryUI javascript libraries are needed in your review.php page
     *Parameter(s): $arrayLogInfo (array) - array of logs (array([0] -> action, [1]->username, [2]->timestamp)
     *Return value(s): None
    */
    function DISPLAY_LOG_INFO($arrayLogInfo)
    {
        echo '<div id="documentHistory" class="ui-widget-content" style="text-align: center;max-height:350px;overflow:auto">';
            echo "<p>Document History</p><table><thead><tr><th>Action</th><th>Username</th> <th>Timestamp</th></tr></thead><tbody>";

                        $user = [];
                        $length = count($arrayLogInfo);
                        for ($x = 0; $x < $length; $x++) {
                            $action[$x] = $arrayLogInfo[$x][0];
                            $user[$x] = $arrayLogInfo[$x][1];
                            $time[$x] = $arrayLogInfo[$x][2];
                            echo "<tr><td>$action[$x]</td><td>$user[$x]</td><td id='timeStamp'>$time[$x]</td></tr>";
                        }
                        echo "</tbody></table></div>";
    }

    /**********************************************
     * Function: NAME_INDICES_FOLDER
     * Description:
     * Parameter(s):
     * fileName (in string) - name of the file
     * bookArray (books) - pass in books
     * Return value(s):
     * (assoc array) - return a template info in an associative array
     ***********************************************/
    function NAME_INDICES_FOLDER($fileName, $bookArray){
        $file_name = $fileName;
        $posSpc = strpos($file_name, ' ');
        if ($posSpc === false)
            $char = '_';
        else
            $char = ' ';
        $tempFilename = explode($char, $file_name);
        $libIndexPfx = $tempFilename[2];

        foreach ($bookArray as $bookname){
            $exbookname = explode(' ', $bookname[0]);
            if ($exbookname[2] == $libIndexPfx){
                $indicesFolder = implode('', $exbookname);
                return $indicesFolder;
            }
        }
    }
}
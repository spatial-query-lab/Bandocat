<?php include '../../Library/ControlsRender.php';
$dateControl = new ControlsRender();
$input = null;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Input Form</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <script type="text/javascript" src="ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>

</head>
<body>
<style>

    footer {
        position: fixed;
        min-width: 100%;
    }

    /*Account Stylesheet Adaptation from Collection Name */
    .Account{
        border-radius: 2%;
        box-shadow: 0px 0px 4px;
    }

    .Account_Table{
        background-color: white;
        padding: 3%;
        border-radius: 6%;
        box-shadow: 0px 0px 2px;
        margin: auto;
        font-family: verdana;
        text-align: left;
        margin-top: 2%;
        margin-bottom: 4%;

    }

    .Account_Table .Account_Title{
        margin-top: 2px;
        margin-bottom: 12px;
        color: #008852;
    }

    .Account_Table .Collection_data{
        width: 50%;
    }
    }
    #col1{float:left;width:460px;height:100%;padding-left:80px;}
    #col2{float:left;width:500px;height:100%;padding-left:5px;}
    #row{float:bottom;width:2000px;height:52px;background-color: #ccf5ff;}

    .cell
    {
        min-height: 52px;
    }

    .label
    {
        float:left;
        width:150px;
        min-width: 195px;
        padding-top:2px;
    }
    .labelradio
    {
        float:left;
        width:150px;
        min-width: 195px;
    }
    mark {
        background-color: #ccf5ff;
    }
    span.labelradio:hover p{
        z-index: 10;
        display: inline;
        position: absolute;
        border: 1px solid #000000;
        background: #bfe9ff;
        font-size: 14px;
        font-style: normal;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px; -o-border-radius: 3px;
        border-radius: 3px;
        -webkit-box-shadow: 4px 4px 4px #36c476;
        -moz-box-shadow: 4px 4px 4px #36c476;
        box-shadow: 4px 4px 4px #36c476;
        width: 200px;
        padding: 10px 10px;
    }

    .bluebtn{
        font-size: 0.5em;
        padding: 5px;
    }

    #innerLeftTable td{
        padding-top: 2%;
    }

    #innerRightTable td{
        padding-top: 2%;
    }
</style>

<script type="text/javascript" src="PasswordMatch.js"></script>

<table id = "thetable">
    <tr>
        <td class="menu_left" id="thetable_left">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ?>
        </td>
        <td class="Account" id="thetable_right">
            <h2>Input Form</h2>
            <table class="Account_Table">
            <!--Left Table-->
                <tr>
                    <td id="col1">
                        <table id="innerLeftTable" style="width: 180%">

                <!--Library Index field-->
                            <tr class="cell">
                                <td>
                                    <span class="label"><span style = "color:red;"> * </span>Library Index:</span>
                                    <input type = "text" name = "libraryindex" id = "libraryindex" size="26" value="" required="true" /><span class = "errorInput" id = "libraryindexErr"></span>
                                </td>
                            </tr>

                <!--Document Title-->
                            <tr class="cell">
                                <td>
                                    <span class="label"><span style = "color:red;"> * </span>Document Title:</span>
                                    <input type = "text" name = "documenttitle" id = "documenttitle" size="26" value="" required="true" /><span class = "errorInput" id = "documenttitleErr"></span>
                                </td>
                            </tr>

                <!--Needs Review-->
                            <tr class="cell">
                                <td>
                                    <form style="margin-top: 0% !important;">
                                        <span class="labelradio" >
                                            <mark>Needs Review:</mark>
                                            <p hidden><b></b>This is to signal if a review is needed</p>
                                        </span>
                                        <input type = "radio" name = "needsreview" id = "needsreview_yes" size="26" value=1 />Yes
                                        <input type = "radio" name = "needsreview" id = "needsreview_no" size="26" value=0 checked/>No
                                    </form>
                                </td>
                            </tr>

                <!--In A Subfolder-->
                            <tr class="cell">
                                <td>
                                    <form style="margin-top: 0% !important;">
                                        <span class="labelradio" >
                                            <mark>In A Subfolder:</mark>
                                            <p hidden><b></b>This document belongs in a subfolder</p>
                                        </span>
                                        <input type = "radio" name = "inasubfolder" id = "inasubfolder_yes" size="26" value=1/>Yes
                                        <input type = "radio" name = "inasubfolder" id = "inasubfolder_no" size="26" value=0 checked/>No
                                    </form>
                                </td>
                            </tr>


                    <!--Subfolder Comments-->
                            <tr class="cell">
                                <td>
                                    <span class="label">Subfolder Comments:</span>
                                    <textarea rows = "2" cols = "35" id="subfolderComments"/></textarea>
                                </td>
                            </tr>
                    <!--Classification-->
                            <tr class="cell">
                                <td>
                                    <span class="label">Classification:</span>
                                    <select>
                                        <option>

                                        </option>
                                    </select>
                                </td>
                            </tr>

                    <!--Classification Comments-->
                            <tr class="cell">
                                <td>
                                    <span class="label">Classification Comments:</span>
                                    <textarea rows = "2" cols = "35" id="classificationComments"/></textarea>
                                </td>
                            </tr>

                    <!--Scan of Front-->
                            <tr>
                                <td>
                                    <span class="label">Scan of Front</span></br>
                                    <img src="../../Images/Sample_Thumbnails/general_index_E-K_287.jpg">
                                </td>
                    <!--Scan of Back-->
                                <td>
                                    <span style="margin-left: -42%" class="label">Scan of Back</span></br>
                                    <img style="margin-left: 16%" src="../../Images/Sample_Thumbnails/general_index_E-K_259.jpg">
                                </td>
                            </tr>

                        </table>
                    </td>



                    <!--Right Table-->
                    <td id="col2">
                        <table id="innerRightTable">

                    <!--Document Start Date-->
                            <tr class="cell">
                                <td>
                                    <span class="label">Document Start Date:</span>
                                    <select>
                                        <?php
                                            $dateControl->GET_DDL_MONTH($input);
                                        ?>
                                    </select>
                                    <select>
                                        <?php
                                            $dateControl->GET_DDL_DAY($input)
                                        ?>
                                    </select>
                                    <select>
                                        <?php
                                            $dateControl->GET_DDL_YEAR($input)
                                        ?>
                                    </select>
                                </td>
                            </tr>

                        <!--Document End Date-->
                            <tr class="cell">
                                <td>
                                    <span class="label">Document End Date:</span>
                                    <select>
                                        <?php
                                        $dateControl->GET_DDL_MONTH($input);
                                        ?>
                                    </select>
                                    <select>
                                        <?php
                                        $dateControl->GET_DDL_DAY($input)
                                        ?>
                                    </select>
                                    <select>
                                        <?php
                                        $dateControl->GET_DDL_YEAR($input)
                                        ?>
                                    </select>
                                </td>
                            </tr>

                        <!--Document Authors-->
                            <tr>
                                <td>
                                    <table id="authorsTable">
                                        <tr>
                                            <td>
                                                <span class="label">Document Authors:</span>
                                            </td>

                                            <td>
                                                <input type="button" class= "bluebtn" id="plusAuthor" value="+" onclick="addAuthor()">
                                                <input type="button" class= "bluebtn" id="minusAuthor" value="-" onclick="subtractAuthor()">
                                            </td>

                                            <td>
                                                <input type="text" class="documentAuthors">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        <!--Comments-->
                            <tr>
                                <td>
                                    <span class="label">Comments:</span>
                                    <textarea rows = "2" cols = "35" id="comments"/></textarea>
                                </td>
                            </tr>
                         </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<script>
    //Authors table global variables
    var table = document.getElementById("authorsTable");
    var numTableRows = table.rows.length;

//function called in the addAuthor function that allows for only 3 rows of data.
    function rangeTable(rows) {
        if (rows <= 3){
            var row = table.insertRow(rows);

            for (i = 0; i < table.rows[0].cells.length; i++) {
                var cell = row.insertCell(i);
            }
        }
        return rows;
    }
    //function that add rows and an input text element as row data
function addAuthor() {
    numRows = table.rows.length;

    if(numRows> 0){
        numRows = rangeTable(numRows);
        for(i = 0; i < numRows; i++){
            idNum = i + 1;
            table.rows[numRows].cells[2].innerHTML = '<input type="text" class="documentAuthors">';

        }

        //docAthClass is the variable that stores the array of elements with the class name documentAuthors
        var docAthClass = document.getElementsByClassName('documentAuthors');

    }
}

    /*function that removes rows from the authors' table*/
function subtractAuthor() {
    numRows = table.rows.length;

    if (numRows > 1){
        var row= table.deleteRow(numRows-1);
    }
}
</script>

<?php include '../../Master/footer.php'; ?>

</body>
</html>

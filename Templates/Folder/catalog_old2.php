<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
//get collection name from passed variables col and doc
if(isset($_GET['col']) && isset($_GET['doc']))
{
    $collection = $_GET['col'];
    $docID = $_GET['doc'];
}
else header('Location: ../../');

require '../../Library/DBHelper.php';
require '../../Library/FolderDBHelper.php';
require '../../Library/DateHelper.php';
require '../../Library/ControlsRender.php';
$Render = new ControlsRender();
$DB = new FolderDBHelper();
//get appropriate DB
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
//select fieldbook document
$document = $DB->SP_TEMPLATE_FOLDER_DOCUMENT_SELECT($collection, $docID);
$date = new DateHelper();
//select authors by document
$authors = $DB->GET_FOLDER_AUTHORS_BY_DOCUMENT_ID($collection,$docID);
?>

<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Catalog Form</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel="stylesheet" type="text/css" href="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.css">
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.js"></script>
    <script type="text/javascript" src="../../Master/master.js"></script>
</head>
<!-- END HTML HEADER -->
<body>
<!--  HTML BODY -->
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ?>
        </div>
        <div id="divright">
            <h2 id="page_title"><?php echo $config['DisplayName'];?> Catalog Form</h2>
            <div id="divscroller">
                <form id="theform" name="theform" enctype="multipart/form-data" >
                <table class="Account_Table">
                    <tr>
                        <!-- Populates the control with data -->
                            <datalist id="lstAuthor">
                                <?php $Render->getDataList($DB->GET_AUTHOR_LIST($collection)); ?>
                            </datalist>
            <td id="col1">
                <div class="cell">
                    <!-- LIBRARY INDEX -->
                    <span class="label"><span style = "color:red;"> * </span>Library Index:</span>
                    <input type = "text" name = "txtLibraryIndex" id = "txtLibraryIndex" size="26" value='<?php echo htmlspecialchars($document['LibraryIndex'],ENT_QUOTES);?>' required />
                </div>
                <div class="cell">
                    <!-- TITLE -->
                    <span class="label"><span style = "color:red;"> * </span>Document Title:</span>
                    <input type = "text" name = "txtTitle" id = "txtTitle" size="26" required="true" value='<?php echo htmlspecialchars($document['Title'],ENT_QUOTES);?>' />
                </div>
                <div class="cell">
                    <!-- NEEDS REVIEW -->
                    <span class="labelradio" >
                        <mark>Needs Review:</mark>
                        <p hidden><b></b>This is to signal if a review is needed</p>
                    </span>
                    <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_yes" size="26" value="1" <?php if($document['NeedsReview'] == 1) echo "checked"; ?> />Yes
                    <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_no" size="26" value="0" <?php if($document['NeedsReview'] == 0) echo "checked"; ?>  />No
                </div>
                <div class="cell">
                    <!-- SUB FOLDER -->
                    <span class="labelradio" >
                        <mark>In A Subfolder:</mark>
                        <p hidden><b></b>This document belongs in a subfolder</p>
                    </span>
                    <input type = "radio" name = "rbInASubfolder" id = "rbInASubfolder_yes" size="26" value="1" <?php if($document['InSubfolder'] == 1) echo "checked"; ?> />Yes
                    <input type = "radio" name = "rbInASubfolder" id = "rbInASubfolder_no" size="26" value="0" <?php if($document['InSubfolder'] == 0) echo "checked"; ?> />No
                </div>
                <div class="cell">
                    <!-- SUBFOLDER COMMENTS -->
                    <span class="label">Subfolder Comments:</span>
                    <textarea cols = "35" name="txtSubfolderComments" id="txtSubfolderComments"/><?php echo $document['SubfolderComment']; ?></textarea>
                </div>
                <div class="cell">
                    <!-- CLASSIFICATION -->
                    <span class="label">Classification:</span>
                    <select id="ddlClassification" name="ddlClassification" style="width:215px">
                        <?php
                        $Render->GET_DDL($DB->GET_FOLDER_CLASSIFICATION_LIST($collection),$document['Classification']);
                        ?>
                    </select>
                </div>
                <div class="cell">
                    <!-- CLASSIFICATION COMMENTS-->
                    <span class="label">Classification Comments:</span>
                    <textarea rows = "2" cols = "35" id="txtClassificationComments" name="txtClassificationComments"/><?php echo $document['ClassificationComment']; ?></textarea>
                </div>
                <div class="cell">
                    <!-- GET START DDL MONTH -->
                    <select name="ddlStartMonth" id="ddlStartMonth" style="width:60px">
                        <?php $Render->GET_DDL_MONTH($date->splitDate($document['StartDate'])['Month']); ?>
                    </select>
                    <span class="label">Document Start Date:</span>
                    <!-- GET START DDL DAY -->
                    <select name="ddlStartDay" id="ddlStartDay" style="width:60px">
                        <?php $Render->GET_DDL_DAY($date->splitDate($document['StartDate'])['Day']); ?>
                    </select>
                    <!-- GET START DDL YEAR -->
                    <select id="ddlStartYear" name="ddlStartYear" style="width:85px">
                        <?php $Render->GET_DDL_YEAR($date->splitDate($document['StartDate'])['Year']); ?>
                    </select>

                </div>
                <div class="cell">
                    <!-- GET END DDL MONTH -->
                    <select name="ddlEndMonth" id="ddlEndMonth" style="width:60px">
                        <?php $Render->GET_DDL_MONTH($date->splitDate($document['EndDate'])['Month']); ?>
                    </select>
                    <span class="label">Document End Date:</span>
                    <!-- GET END DDL DAY -->
                    <select name="ddlEndDay" id="ddlEndDay" style="width:60px">
                        <?php $Render->GET_DDL_DAY($date->splitDate($document['EndDate'])['Day']); ?>
                    </select>
                    <!-- GET END DDL YEAR -->
                    <select name="ddlEndYear" id="ddlEndYear" style="width:85px">
                        <?php $Render->GET_DDL_YEAR($date->splitDate($document['EndDate'])['Year']); ?>
                    </select>
                </div>
                <div class="cell">
                    <!-- DOCUMENT AUTHOR -->
                    <span class="label">Document Author:</span>
                    <input type="text" id="txtAuthor" name="txtAuthor[]" size="26" list="lstAuthor" value="<?php if(count($authors) > 0){echo htmlspecialchars($authors[0][0],ENT_QUOTES);} ?>"/><span style="padding-right:5px"></span><input type="button" id="more_fields" onclick="add_fields(null);" value="+"/>
                    <span id="authorcell"></span>
                </div>
            </td>
            <td id="col2" style="padding-left:40px">
                <div class="cell">
                    <span class="label">Comments:</span>
                    <!-- COMMENTS-->
                    <textarea rows = "4" cols = "35" id="txtComments" name="txtComments"/><?php echo $document['Comments']; ?></textarea>
                    <br><br><br>
                </div>
                <div class="cell">
                    <table>
                        <tr>
                            <td style="text-align: center">
                                <!--SCAN OF FRONT-->
                                <span class="label" style="text-align: center">Scan of Front</span><br>
                                <?php
                                echo "<a id='download_front' href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\"><br><img src='" .  '../../' . $config['ThumbnailDir'] . str_replace(".tif",".jpg",$document['FileName']) . " ' alt = Error /></a>";
                                echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNamePath'])/1024/1024, 2) . " MB";
                                echo "<br><a href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\">(Click to download)</a>";
                                ?>
                            </td>
                            <td style="padding-right:20px"></td>
                            <td style="text-align: center">
                                <!--SCAN OF BACK-->
                                <?php
                                if($document['FileNameBack'] != '') //has Back Scan
                                {

                                    echo '<span class="label" style="text-align: center">Scan of Back</span><br>';
                                    echo "<a id='download_front' href=\"download.php?file=$config[StorageDir]$document[FileNameBackPath]\"><br><img src='" . '../../' . $config['ThumbnailDir'] . str_replace(".tif", ".jpg", $document['FileNameBack']) . " ' alt = Error /></a>";
                                    echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNameBackPath']) / 1024 / 1024, 2) . " MB";
                                    echo "<br><a href=\"download.php?file=$config[StorageDir]$document[FileNameBackPath]\">(Click to download)</a>";
                                }
                                else
                                {
                                    echo '<span class="label" style="text-align: center">No Scan of Back</span><br>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                        </tr>
                    <tr>
                        <td colspan="2">
                            <div class="cell" style="text-align: center;padding-top:20px">
                                <!-- Hidden inputs that are passed when the update button is hit -->
                                <span><input type="reset" id="btnReset" name="btnReset" value="Reset" class="bluebtn"/></span>
                                <input type = "hidden" id="txtDocID" name = "txtDocID" value = "<?php echo $docID;?>" />
                                <input type = "hidden" id="txtAction" name="txtAction" value="catalog" />  <!-- catalog or review -->
                                <input type = "hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
                                <span>
                                <?php if($session->hasWritePermission())
                                {echo "<input type='submit' id='btnSubmit' name='btnSubmit' value='Update' class='bluebtn'/>";}
                                ?>
                                <div class="bluebtn" id="loader" style="display: none">
                                    Updating
                                    <img style="width:4%" src='../../Images/loader.gif'/></div>
                                    </span>
                            </div>
                        </td>
                    </tr>
                    </table>
                </form>
                </div>
            </div>
        </div>
    </div>

<?php include '../../Master/footer.php'; ?>
</body>
<!-- END HTML BODY -->
<script>
    /**********************************************
     * Function: add_fields
     * Description: adds more fields for authors
     * Parameter(s):
     * val (in String ) - name of the author
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    var max = 5;
    var author_count = 0;
    function add_fields(val) {
        if(val == null)
            val = "";
        if(author_count >= max)
            return false;
        author_count++;
        var objTo = document.getElementById('authorcell');
        var divtest = document.createElement("div");
        divtest.innerHTML = '<br><span class="label">Document Author ' + (author_count+1) + '</span><input type = "text" name = "txtAuthor[]" autocomplete="off" id = "txtAuthor" size="26" value="' + val + '" list="lstAuthor" />';
        objTo.appendChild(divtest);
    }

    $( document ).ready(function() {
        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 55);
        //Parse out the authors read in to the add_fields function
        var authors = <?php echo json_encode($authors); ?>;
        for(var i = 1; i < authors.length; i++)
        {
            add_fields(authors[i][0]);
        }
        /* attach a submit handler to the form */
        $('#theform').submit(function (event)
        {
            event.preventDefault();
            /* stop form from submitting normally */
            //This attaches the entire "#theform" in addition to the crews to the post
            var formData = new FormData($(this)[0]);

            //Append Authors data to the form
            var authors = $('[name="txtAuthor[]');
            var array_authors = [];
            for(var i = 0; i < authors.length; i++)
                array_authors.push(authors[i].value);
            formData.append("authors",JSON.stringify(array_authors));
                /*jquery that displays the three points loader*/
                $('#btnSubmit').css("display", "none");
                $('#loader').css("display", "inherit");

                /* Send the data using post */
                $.ajax({
                    type: 'post',
                    url: 'form_processing.php',
                    data:  formData,
                    processData: false,
                    contentType: false,
                    success:function(data){
                        var json = JSON.parse(data);
                        var msg = "";
                        var result = 0;
                        for(var i = 0; i < json.length; i++)
                        {
                            msg += json[i] + "\n";
                        }
                        for (var i = 0; i < json.length; i++){
                            if (json[i].includes("Success")) {
                                result = 1;
                            }
                            else if(json[i].includes("Fail") || json[i].includes("EXISTED"))
                            {
                                $('#btnSubmit').css("display", "inherit");
                                $('#loader').css("display", "none");
                            }
                        }
                        alert(msg);
                        if (result == 1){
                            $('#btnSubmit').css("display", "inherit");
                            $('#loader').css("display", "none");
                            window.close();
                        }

                    }
                });

        });
    });
</script>
<style>


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

</style>
</html>

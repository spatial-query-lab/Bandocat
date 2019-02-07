<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(isset($_GET['col']) && isset($_GET['doc']))
{
    $collection = $_GET['col'];
    $docID = $_GET['doc'];
    require('../../Library/ControlsRender.php');
    $Render = new ControlsRender();
    require '../../Library/DBHelper.php';
    require('../../Library/MapDBHelper.php');
    $DB = new MapDBHelper();
    //get appropriate DB
    $config = $DB->SP_GET_COLLECTION_CONFIG($collection);
    //find the document by passing collection and docid
     if($collection != "pennyfenner")
    {
        $document = $DB->SP_TEMPLATE_MAP_DOCUMENT_SELECT($collection,$docID);
    }
    else if($collection == "pennyfenner")
    {
        $document = $DB->SP_TEMPLATE_MAP_DOCUMENT_WITH_JOBNUMBER_SELECT($collection,$docID);
    }
    //var_dump($document);
}
else header('Location: ../../');

include '../../Library/DateHelper.php';
$date = new DateHelper();
?>
<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Review Form</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel="stylesheet" type="text/css" href="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.css">
    <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>

    <script type="text/javascript" src="../../Master/master.js"></script>

</head>
<!-- END HTML HEADER -->
<!-- HTML BODY -->
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php';
                        if($session->isAdmin()) //if user is Admin, render the Document History (Log Info)
                            $Render->DISPLAY_LOG_INFO($DB->GET_LOG_INFO($collection, $docID));
            ?>
        </div>
        <div id="divright">
            <h2><?php echo $config['DisplayName'];?> Review Form</h2>
            <div id="divscroller">
                <form id="theform" name="theform" method="post">
            <table id="table2">
                    <tr>
                        <td>
                            <span class="label"><span style = "color:red;"> * </span>Library Index:</span>
                        </td>
                        <td>
                            <!-- LIBRARY INDEX -->
                            <input type = "text" name = "txtLibraryIndex" id = "txtLibraryIndex" size="26" value='<?php echo htmlspecialchars($document['LibraryIndex'],ENT_QUOTES); ?>' required="true" />
                        </td>
                        <td>
                            <span class="label">Customer Name:</span>
                        </td>
                        <td>
                            <!-- CUSTOMER NAME -->
                            <input type = "text" list="lstCustomer" name = "txtCustomer" id = "txtCustomer" size="26" value="<?php echo htmlspecialchars($document['CustomerName'],ENT_QUOTES); ?>" />
                            <datalist id="lstCustomer">
                                <!-- POPULATE DDL WITH CUSTOMER NAME -->
                                <?php $Render->getDataList($DB->GET_CUSTOMER_LIST($collection)); ?>
                            </datalist>
                        </td>
                        <tr>
                             <!-- DOCUMENT TITLE-->
                            <td> <span class="label"><span style = "color:red;"> * </span>Document Title:</span></td>
                            <td><input type = "text" name = "txtTitle" id = "txtTitle" size="26" value='<?php echo htmlspecialchars($document['Title'],ENT_QUOTES); ?>' required />
                            </td>
                            <td>
                                <!-- DOCUMENT START DATE -->
                                <span class="label">Document Start Date:</span>
                            </td>
                            <td>
                                <!-- DDL START MONTH -->
                                <select name="ddlStartMonth" id="ddlStartMonth" style="width:60px">
                                    <?php $Render->GET_DDL_MONTH($date->splitDate($document['StartDate'])['Month']); ?>
                                </select>
                                <!-- DDL START DAY -->
                                <select name="ddlStartDay" id="ddlStartDay" style="width:60px">
                                    <?php $Render->GET_DDL_DAY($date->splitDate($document['StartDate'])['Day']); ?>
                                </select>
                                <!-- DDL START YEAR -->
                                <select id="ddlStartYear" name="ddlStartYear" style="width:85px">
                                    <?php $Render->GET_DDL_YEAR($date->splitDate($document['StartDate'])['Year']); ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <!-- DOCUMENT SUBTITLE -->
                                <span class="label">Document Subtitle:</span>
                            </td>
                            <td>
                                <input type = "text" name = "txtSubtitle" id = "txtSubtitle" size="26" value='<?php echo htmlspecialchars($document['Subtitle'],ENT_QUOTES); ?>' />
                            </td>
                            <td>
                                <!-- DOCUMENT END DATE -->
                                <span class="label">Document End Date:</span>
                            </td>
                            <td>
                                <!-- DDL END MONTH -->
                                <select name="ddlEndMonth" id="ddlEndMonth" style="width:60px">
                                    <?php $Render->GET_DDL_MONTH($date->splitDate($document['EndDate'])['Month']); ?>
                                </select>
                                <!-- DDL END DAY -->
                                <select name="ddlEndDay" id="ddlEndDay" style="width:60px">
                                    <?php $Render->GET_DDL_DAY($date->splitDate($document['EndDate'])['Day']); ?>
                                </select>
                                <!-- DDL END YEAR -->
                                <select name="ddlEndYear" id="ddlEndYear" style="width:85px">
                                    <?php $Render->GET_DDL_YEAR($date->splitDate($document['EndDate'])['Year']); ?>
                                </select>
                            </td>
                        </tr>

                        <tr> <!-- row 1 -->
                            <!-- MAP SCALE -->
                            <td><span class="label">Map Scale:</span></td>
                            <td> <input type = "text" name = "txtMapScale" id = "txtMapScale" size="26" value="<?php echo htmlspecialchars($document['MapScale'],ENT_QUOTES); ?>"  />
                            </td>
							 <?php if($collection == "pennyfenner") : ?>
                                <td>
                                    <!-- FIELD BOOK NUMBER -->
                                    <span class="label">Job Number:</span>
                                </td>
                                <td>
                                    <input type = "text" name = "txtJobNumber" id = "txtJobNumber" size="26" value="<?php if($document['JobNumber'] != 0 && $document['JobNumber'] != null) {echo htmlspecialchars($document['JobNumber'],ENT_QUOTES);} ?>"/><span class = "errorInput" id = "customernameErr"></span>
                                </td>

                            <?php endif; ?>
                            <td>
                                <!-- FIELD BOOK NUMBER -->
                                <span class="label">Field Book Number:</span>
                            </td>
                            <td>
                                <input type = "text" name = "txtFieldBookNumber" id = "txtFieldBookNumber" size="26" value="<?php if($document['FieldBookNumber'] != 0 && $document['FieldBookNumber'] != null) {echo htmlspecialchars($document['FieldBookNumber'],ENT_QUOTES);} ?>"/><span class = "errorInput" id = "customernameErr"></span>
                            </td>
                        </tr>

                         <tr>
                             <!-- IS MAP -->
                             <td><span class="labelradio"><mark>Is Map:</mark><p hidden><b></b>This is to signal if it is a map</p></span>
                             </td>
                             <td>
                                 <input type = "radio" name = "rbIsMap" id = "rbIsMap_yes" size="26" value="1" <?php if($document['IsMap'] == 1) echo "checked"; ?>/>Yes
                                 <input type = "radio" name = "rbIsMap" id = "rbIsMap_no" size="26" value="0"  <?php if($document['IsMap'] == 0) echo "checked"; ?>/>No
                             </td>
                             <!--FIELD BOOK PAGE-->
                             <td><span class="label">Field Book Page:</span></td>
                             <td><input type = "text" name = "txtFieldBookPage" id = "txtFieldBookPage" size="26" value="<?php echo htmlspecialchars($document['FieldBookPage'],ENT_QUOTES); ?>" />
                             </td>
                         </tr>
                        <tr>
                            <td>
                                <span class="labelradio" ><mark>Needs Review:</mark><p hidden><b></b>This is to signal if a review is needed</p></span>
                            </td>
                            <td>
                                <!-- NEEDS REVIEW -->
                                <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_yes" size="26" value="1" <?php if($document['NeedsReview'] == 1) echo "checked"; ?>/>Yes
                                <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_no" size="26" value="0" <?php if($document['NeedsReview'] == 0) echo "checked"; ?>/>No
                            </td>
                            <td>
                                <!--RECTIFIABILITY -->
                                <?php $readrec = array("POOR","GOOD","EXCELLENT"); ?>
                                <span class="label"><span style = "color:red;"> * </span>Readability:</span>
                            </td>
                            <td>
                                <!-- READABILITY -->
                                <select id="ddlReadability" name="ddlReadability" required style="width:215px">
                                    <?php
                                    $Render->GET_DDL2($readrec,$document['Readability']);
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <!-- HAS NORTH ARROW -->
                                <td><span class="labelradio"><mark>Has North Arrow:</mark><p hidden><b></b>This is to signal if it has a North Arrow</p></span>
                                </td>
                                <td>
                                    <input type = "radio" name = "rbHasNorthArrow" id = "rbHasNorthArrow_yes" size="26" value="1" <?php if($document['HasNorthArrow'] == 1) echo "checked"; ?>/>Yes
                                    <input type = "radio" name = "rbHasNorthArrow" id = "rbHasNorthArrow_no" size="26" value="0"  <?php if($document['HasNorthArrow'] == 0) echo "checked"; ?>/>No
                                </td>
                                <td>
                                     <span class="label"><span style = "color:red;"> * </span>Rectifiability:</span>
                                </td>
                                <td>
                                    <!-- POPULATE THE DDL RECTIFIABILITY -->
                                <select id="ddlRectifiability" name="ddlRectifiability" required style="width:215px">
                                    <?php
                                    $Render->GET_DDL2($readrec,$document['Rectifiability']);
                                    ?>
                                </select>
                                </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="labelradio"><mark>Has Street:</mark><p hidden><b></b>This is to signal if a Street(s) are present</p></span>
                            </td>
                            <td>
                                <!-- HAS STREETS -->
                                <input type = "radio" name = "rbHasStreets" id = "rbHasStreets_yes" size="26" value="1" <?php if($document['HasStreets'] == 1) echo "checked"; ?>/>Yes
                                <input type = "radio" name = "rbHasStreets" id = "rbHasStreets_no" size="26" value="0" <?php if($document['HasStreets'] == 0) echo "checked"; ?> />No
                            </td>
                            <td>
                                <span class="label">Company Name:</span>

                            </td>
                            <td>
                                <!-- POPULATE DDL WITH COMPANY NAMES -->
                                <input type = "text" list="lstCompany" name = "txtCompany" id = "txtCompany" size="26" value="<?php echo $document['CompanyName'];?>" />
                                <datalist id="lstCompany">
                                    <?php $Render->getDataList($DB->GET_COMPANY_LIST($collection)); ?>
                                </datalist>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <!-- HAS POINT OF INTEREST -->
                                <span class="labelradio"><mark>Has POI:</mark><p hidden><b></b>This is to signal if a Point of Interest is present</p></span>
                            </td>
                            <td>
                                <input type = "radio" name = "rbHasPOI" id = "rbHasPOI_yes" size="26" value="1" <?php if($document['HasPOI'] == 1) echo "checked"; ?>/>Yes
                                <input type = "radio" name = "rbHasPOI" id = "rbHasPOI_no" size="26" value="0"  <?php if($document['HasPOI'] == 0) echo "checked"; ?>/>No
                            </td>
                            <td>
                                <!-- DOCUMENT TYPE-->
                                <span class="label">Document Type:</span>
                            </td>
                            <td>
                                <input type = "text" name = "txtType" id = "txtType" size="26" value="<?php echo htmlspecialchars($document['Type'],ENT_QUOTES);?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <!-- HAS COORDINATES-->
                                <span class="labelradio"><mark>Has Coordinates:</mark><p hidden><b></b>This is to signal if Coordinates are visible</p></span>
                            </td>
                            <td>
                                <input type = "radio" name = "rbHasCoordinates" id = "rbHasCoordinates_yes" size="26" value="1" <?php if($document['HasCoordinates'] == 1) echo "checked"; ?> />Yes
                                <input type = "radio" name = "rbHasCoordinates" id = "rbHasCoordinates_no" size="26" value="0"  <?php if($document['HasCoordinates'] == 0) echo "checked"; ?>/>No
                            </td>
                            <td>
                                <span class="label"><span style = "color:red;"> * </span>Document Medium:</span>
                            </td>
                            <td>
                                <!-- POPULATES DDL WITH MEDIUM -->
                                <select id="ddlMedium" name="ddlMedium" style="width:215px" required>
                                    <?php
                                    $Render->GET_DDL($DB->GET_TEMPLATE_MAP_MEDIUM_FOR_DROPDOWN($collection),$document['Medium']);
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="labelradio"><mark>Has Coast:</mark><p hidden><b></b>This is to signal if a Coast line is present</p></span>
                            </td>
                            <td>
                                <!-- HAS A COAST -->
                                <input type = "radio" name = "rbHasCoast" id = "rbHasCoast_yes" size="26" value="1" <?php if($document['HasCoast'] == 1) echo "checked"; ?>/>Yes
                                <input type = "radio" name = "rbHasCoast" id = "rbHasCoast_no" size="26" value="0" <?php if($document['HasCoast'] == 0) echo "checked"; ?> />No
                            </td>
                            <td>
                                <!-- DOCUMENT AUTHOR -->
                                <span class="label">Document Author:</span>
                            </td>
                            <td>
                                <!-- POPULATE DDL WITH AUTHOR LIST -->
                                <input type = "text" list="lstAuthor" name = "txtAuthor" id = "txtAuthor" size="26" value="<?php echo $document['AuthorName']; ?>" /></span>
                                <datalist id="lstAuthor">
                                    <?php $Render->getDataList($DB->GET_AUTHOR_LIST($collection)); ?>
                                </datalist>
                            </td>
                        </tr>
                        <tr style="vertical-align: top">
                            <td>
                                <!-- SCAN OF FRONT-->
                                <span class="label"><span style = "color:red;"> * </span>Scan Of Front:</span>

                            </td>
                            <td style="text-align:center">
                                <?php echo "<a href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\">(Click to download)</a><br>";
                                echo "<a id='download_front' href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\"><br><img src='" .  '../../' . $config['ThumbnailDir'] . str_replace(".tif",".jpg",$document['FileName']) . " ' alt = Error /></a>";
                                echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNamePath'])/1024/1024, 2) . " MB";
                                ?>
                            </td>
                            <td>
                                <!-- SCAN OF BACK -->
                                <span class="label">Scan Of Back:</span>
                            </td>
                            <td rowspan="2" style="text-align: center">
                                <?php
                                if($document['FileNameBack'] == '' || $document['FileNameBackPath'] == '')
                                    echo 'No file uploaded';
                                else {
                                    echo "<a href=\"download.php?file=$config[StorageDir]$document[FileNameBackPath]\">(Click to download)</a><br>";
                                    echo "<a id='download_back' href=\"download.php?file=$config[StorageDir]$document[FileNameBackPath]\"><br><img src='" . '../../' . $config['ThumbnailDir'] . str_replace(".tif", ".jpg", $document['FileNameBack']) . " ' alt = Error /></a>";
                                    echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNameBackPath'])/1024/1024, 2) . " MB";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top">
                                <!-- COMMENTS -->
                                <span class="label"><br>Comments:</span>
                            </td>
                            <td>
                                <textarea name = "txtComments" rows = "5" cols = "35" id="txtComments"/><?php echo $document['Comments']; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top">
                                <!-- GEOREC DOWNLOAD URL -->
                            </td>
                            <td>
                                <?php
                                    $georecinfo = $DB->DOCUMENT_GEOREC_INFO_SELECT($docID);
                                    if($georecinfo['geoRecFrontStatus'] == 1)
                                {
                                    echo "<a href=\"download.php?file=$config[GeoRecDir]$georecinfo[georecFrontDirKMZ]\">Front KMZ</a>";
                                    echo " | ";
                                    echo "<a href=\"download.php?file=$config[GeoRecDir]$georecinfo[georecFrontDirGeoTIFF]\">Front GeoTIFF</a>";
                                }
                                if($georecinfo['geoRecBackStatus'] == 1)
                                {
                                    echo "<a href=\"download.php?file=$config[GeoRecDir]$georecinfo[georecBackDirKMZ]\">Back KMZ</a>";
                                    echo " | ";
                                    echo "<a href=\"download.php?file=$config[GeoRecDir]$georecinfo[georecBackDirGeoTIFF]\">Back GeoTIFF</a>";
                                }


                                ?>
                            </td>
                        </tr>
                <tr>
                    <td colspan="4" style="text-align: center">
                        <!-- Hidden inputs that are passed when the update button is hit -->
                        <input type = "hidden" id="txtDocID" name = "txtDocID" value = "<?php echo $docID;?>" />
                        <input type = "hidden" id="txtAction" name="txtAction" value="review" />  <!-- catalog or review -->
                        <input type = "hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
                        <span class="update">
                        <?php if($session->hasWritePermission())
                            {echo "<input type='submit' id='btnSubmit' name='btnSubmit' value='Update' class='bluebtn'/>";}
                        ?>
                            <div class="bluebtn" id="loader" style="display: none;">
                                Uploading
                                <img style="width: 2%;;" src='../../Images/loader.gif'/></div>
                            </div>
                        </span>
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
<style>
    #table2{
        width:100%;
        background-color: white;
        padding: 40px;
        border-radius: 6%;
        box-shadow: 0px 0px 2px;
        margin: auto;
        font-family: verdana;
        text-align: left;
        margin-top: 10px;
        margin-bottom: 50px;
    }
    #table2 td,tr{line-height:42px;}


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
        line-height:20px;
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
        font-size: 13px;
        font-style: normal;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px; -o-border-radius: 3px;
        border-radius: 3px;
        -webkit-box-shadow: 4px 4px 4px #000000;
        -moz-box-shadow: 4px 4px 4px #000000;
        box-shadow: 4px 4px 4px #000000;
        width: 200px;
        padding: 10px 10px;
    }

    #thetable{height:100%;}
</style>

<script>
    $( document ).ready(function()
    {
        /* attach a submit handler to the form */
        $('#theform').submit(function (event)
        {
            //validates the library index
            //TODO:: removed underscore validation for library index
//            if(validateFormUnderscore("txtLibraryIndex") == true)
//            {
                //Library index was found having a "_" in the string
                /* stop form from submitting normally */
                $('#btnSubmit').css("display", "none");
                $('#loader').css("display", "inherit");
                event.disabled;

                event.preventDefault();
                /* Send the data using post */
                $.ajax(
                    {
                    type: 'post',
                    url: 'form_processing.php',
                    data: $('#theform').serializeArray(),
                    success: function (data) {
                        var json = JSON.parse(data);
                        var msg = "";
                        var result = 0;
                        for (var i = 0; i <= json.length - 1; i++) {
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
                            self.close();
                        }
                    }
                })
//            }
//            else
//            {
//                //No _ was found in the string
//                alert("Library Index does not contain an underscore character.                            " +
//                    "Please check Library Index.");
//            }



        });

        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 55);
        $("#divleft").height($("#divscroller").height());
		

    });


</script>
</html>
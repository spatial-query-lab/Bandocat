<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();

//get collection name from passed variable col
if(isset($_GET['col']))
{
    $collection = $_GET['col'];
}
else header('Location: ../../');
require '../../Library/DBHelper.php';
require '../../Library/MapDBHelper.php';
require '../../Library/DateHelper.php';
require '../../Library/ControlsRender.php';
$Render = new ControlsRender();
$DB = new MapDBHelper();
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
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

    <title>Input Form</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel="stylesheet" type="text/css" href="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.css">
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.js"></script>
    <script type="text/javascript" src="../../Master/master.js"></script>
</head>
<!-- END HTML HEADER -->
<!--  HTML BODY -->
<body>
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
        margin-bottom: 9%;

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
        min-height: 45px;
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
        -webkit-box-shadow: 4px 4px 4px #000000;
        -moz-box-shadow: 4px 4px 4px #000000;
        box-shadow: 4px 4px 4px #000000;
        width: 200px;
        padding: 10px 10px;
    }

</style>
<div id="wrap">
    <div id="main">
<div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ?>
</div>
<div id="divright">
            <h2><?php echo $config['DisplayName'];?> Catalog Form</h2>
    <div id="divscroller">
            <table class="Account_Table">

                <form id="theform" name="theform" method="post" enctype="multipart/form-data" >
                    <tr>
                        <td id="col1">
                            <div class="cell">
                                <!-- LIBRARY INDEX -->
                                <span class="label"><span style = "color:red;"> * </span>Library Index:</span>
                                <input type = "text" name = "txtLibraryIndex" id = "txtLibraryIndex" size="26" value="" required />
                            </div>
                            <div class="cell">
                                <!-- TITLE -->
                                <span class="label"><span style = "color:red;"> * </span>Document Title:</span>
                                <input type = "text" name = "txtTitle" id = "txtTitle" size="26" value="" required />
                            </div>
                            <div class="cell">
                                <!-- SUBTITLE -->
                                <span class="label">Document Subtitle:</span>
                                <input type = "text" name = "txtSubtitle" id = "txtSubtitle" size="26" value="" />
                            </div>
                            <div class="cell">
                                <!-- MAP SCALE -->
                                <span class="label">Map Scale:</span>
                                <input type = "text" name = "txtMapScale" id = "txtMapScale" size="26" value=""  />
                            </div>
                            <div class="cell">
                                <!-- IS MAP -->
                                <span class="labelradio"><mark>Is Map:</mark><p hidden><b></b>This is to signal if it is a map</p></span>
                                <input type = "radio" name = "rbIsMap" id = "rbIsMap_yes" size="26" value="1" checked="true"/>Yes
                                <input type = "radio" name = "rbIsMap" id = "rbIsMap_no" size="26" value="0"  />No
                            </div>
                            <div class="cell" >
                                <!-- NEEDS REVIEW -->
                                <span class="labelradio" ><mark>Needs Review:</mark><p hidden><b></b>This is to signal if a review is needed</p></span>
                                <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_yes" size="26" value="1" checked="true"/>Yes
                                <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_no" size="26" value="0" />No
                            </div>
                            <div class="cell">
                                <!-- HAS A NORTH ARROW -->
                                <span class="labelradio"><mark>Has North Arrow:</mark><p hidden><b></b>This is to signal if it has a North Arrow</p></span>
                                <input type = "radio" name = "rbHasNorthArrow" id = "rbHasNorthArrow_yes" size="26" value="1" checked="true"/>Yes
                                <input type = "radio" name = "rbHasNorthArrow" id = "rbHasNorthArrow_no" size="26" value="0"  />No
                            </div>
                            <div class="cell">
                                <!-- HAS STREETS -->
                                <span class="labelradio"><mark>Has Street:</mark><p hidden><b></b>This is to signal if a Street(s) are present</p></span>
                                <input type = "radio" name = "rbHasStreets" id = "rbHasStreets_yes" size="26" value="1" />Yes
                                <input type = "radio" name = "rbHasStreets" id = "rbHasStreets_no" size="26" value="0" checked="true" />No
                            </div>
                            <div class="cell">
                                <!-- HAS POINT OF INTEREST -->
                                <span class="labelradio"><mark>Has POI:</mark><p hidden><b></b>This is to signal if a Point of Interest is present</p></span>
                                <input type = "radio" name = "rbHasPOI" id = "rbHasPOI_yes" size="26" value="1"/>Yes
                                <input type = "radio" name = "rbHasPOI" id = "rbHasPOI_no" size="26" value="0"  checked="true"  />No
                            </div>
                            <div class="cell">
                                <!-- HAS COORDINATES -->
                                <span class="labelradio"><mark>Has Coordinates:</mark><p hidden><b></b>This is to signal if Coordinates are visible</p></span>
                                <input type = "radio" name = "rbHasCoordinates" id = "rbHasCoordinates_yes" size="26" value="1"  />Yes
                                <input type = "radio" name = "rbHasCoordinates" id = "rbHasCoordinates_no" size="26" value="0"checked="true"  />No
                            </div>
                            <div class="cell">
                                <!-- HAS A COAST -->
                                <span class="labelradio"><mark>Has Coast:</mark><p hidden><b></b>This is to signal if a Coast line is present</p></span>
                                <input type = "radio" name = "rbHasCoast" id = "rbHasCoast_yes" size="26" value="1" />Yes
                                <input type = "radio" name = "rbHasCoast" id = "rbHasCoast_no" size="26" value="0" checked="true" />No
                            </div>
                            <div class="cell">
                                <!-- FILE UPLOAD -->
                                <span class="label"><span style = "color:red;"> * </span>Scan Of Front:</span>
                                <input type="file" name="fileUpload" id="fileUpload" accept=".tif" required />
                            </div>
                            <div class="cell">
                                <!-- COMMENTS -->
                                <span class="label"><span style = "color:red;"> &nbsp; </span>Comments:</span>
                                <textarea name = "txtComments" rows = "5" cols = "35" id="txtComments"/></textarea>
                            </div>
                        </td>
                        <td id="col2">
                            <div class="cell">
                                <!-- CUSTOMER NAME -->
                                <span class="label">Customer Name:</span>
                                <input type = "text" list="lstCustomer" name = "txtCustomer" id = "txtCustomer" size="26" value="" />
                                <datalist id="lstCustomer">
                                    <!-- POPULATES THE DDL WITH CUSTOMER NAMES -->
                                    <?php $Render->getDataList($DB->GET_CUSTOMER_LIST($collection)); ?>
                                </datalist>
                            </div>
                            <div class="cell">
                                <select name="ddlStartMonth" id="ddlStartMonth" style="width:60px">
                                    <!-- POPULATES THE DDL WITH START MONTHS -->
                                    <?php $Render->GET_DDL_MONTH(null); ?>
                                </select>
                                <span class="label">Document Start Date:</span>
                                <select name="ddlStartDay" id="ddlStartDay" style="width:60px">
                                    <!-- POPULATES THE DDL WITH START DAYS -->
                                    <?php $Render->GET_DDL_DAY(null); ?>
                                </select>

                                <select id="ddlStartYear" name="ddlStartYear" style="width:85px">
                                    <!-- POPULATES THE DDL WITH START YEARS -->
                                    <?php $Render->GET_DDL_YEAR(null); ?>
                                </select>

                            </div>
                            <div class="cell">

                                <select name="ddlEndMonth" id="ddlEndMonth" style="width:60px">
                                    <!-- POPULATES THE DDL WITH END MONTHS -->
                                    <?php $Render->GET_DDL_MONTH(null); ?>
                                </select>
                                <span class="label">Document End Date:</span>
                                <select name="ddlEndDay" id="ddlEndDay" style="width:60px">
                                    <!-- POPULATES THE DDL WITH END DAYS -->
                                    <?php $Render->GET_DDL_DAY(null); ?>
                                </select>
                                <select name="ddlEndYear" id="ddlEndYear" style="width:85px">
                                    <!-- POPULATES THE DDL WITH END YEARS -->
                                    <?php $Render->GET_DDL_YEAR(null); ?>
                                </select>
                            </div>
                            <?php if($collection == "pennyfenner") : ?>
                                <div class="cell">
                                    <!-- Job Number33334 NUMBER -->
                                    <span class="label">Job Number:</span>
                                    <input type = "text" name = "txtJobNumber" id = "txtJobNumber" size="26" value=""/>
                                </div>
                            <?php endif; ?>
                            <div class="cell">
                                <!-- FIELD BOOK NUMBER -->
                                <span class="label">Field Book Number:</span>
                                <input type = "text" name = "txtFieldBookNumber" id = "txtFieldBookNumber" size="26" value=""/>
                            </div>
                            <div class="cell">
                                <!-- FIELD BOOK PAGE -->
                                <span class="label">Field Book Page:</span>
                                <input type = "text" name = "txtFieldBookPage" id = "txtFieldBookPage" size="26" value="" />
                            </div>
                            <div class="cell">
                                <!-- READABILITY -->
                                <?php $readrec = array("POOR","GOOD","EXCELLENT"); ?>
                                <span class="label"><span style = "color:red;"> * </span>Readability:</span>
                                <select id="ddlReadability" name="ddlReadability" required style="width:215px">
                                    <?php
                                    $Render->GET_DDL2($readrec,null);
                                    ?>
                                </select>
                            </div>
                            <div class="cell" >
                                <!-- RECTIFIABILITY -->
                                <span class="label"><span style = "color:red;"> * </span>Rectifiability:</span>
                                <select id="ddlRectifiability" name="ddlRectifiability" required style="width:215px">
                                    <?php
                                    $Render->GET_DDL2($readrec,null);
                                    ?>
                                </select>
                            </div>
                            <div class="cell">
                                <!-- COMPANY LIST -->
                                <span class="label">Company Name:</span>
                                <input type = "text" list="lstCompany" name = "txtCompany" id = "txtCompany" size="26" value="" />
                                <datalist id="lstCompany">
                                    <!-- POPULATES DDL FOR COMPANY -->
                                    <?php $Render->getDataList($DB->GET_COMPANY_LIST($collection)); ?>
                                </datalist>
                            </div>
                            <?php if($collection != "pennyfenner") : ?>
                                <div class="cell">
                                    <!-- DOCUMENT TYPE-->
                                    <span class="label">Document Type:</span>
                                    <input type = "text" name = "txtType" id = "txtType" size="26" value="" />
                                </div>
                            <?php endif; ?>
                            <div class="cell">
                                <!-- DOCUMENT MEDIUM -->
                                <span class="label"><span style = "color:red;"> * </span>Document Medium:</span>
                                <select id="ddlMedium" name="ddlMedium" style="width:215px" required>
                                    <!-- GET MAP MEDIUM FOR DDL-->
                                    <?php
                                    $Render->GET_DDL($DB->GET_TEMPLATE_MAP_MEDIUM_FOR_DROPDOWN($collection),null);
                                    ?>
                                </select>
                            </div>
                            <div class="cell">
                                <!-- DOCUMENT AUTHOR -->
                                <span class="label">Document Author:</span>
                                <input type = "text" list="lstAuthor" name = "txtAuthor" id = "txtAuthor" size="26" value="" /></span>
                                <datalist id="lstAuthor">
                                    <!-- POPULATE AUTHOR LIST-->
                                    <?php $Render->getDataList($DB->GET_AUTHOR_LIST($collection)); ?>
                                </datalist>
                            </div>
                            <div class="cell">
                                <!-- FILE UPLOAD BACK-->
                                <span class="label">Scan Of Back:</span>
                                <input type="file" name="fileUploadBack" id="fileUploadBack" accept=".tif" /></span>
                            </div>
                            <div class="cell" style="text-align: center;padding-top:20px">
                                <!-- Hidden inputs that are passed when the update button is hit -->
                                <span><input type="reset" id="btnReset" name="btnReset" value="Reset" class="bluebtn"/></span>
                                <input type = "hidden" id="txtDocID" name = "txtDocID" value = "" />
                                <input type = "hidden" id="txtAction" name="txtAction" value="catalog" />  <!-- catalog or review -->
                                <input type = "hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
                                <span>
                                    <?php if($session->hasWritePermission())
                                    {echo "<input type='submit' id='btnSubmit' name='btnSubmit' value='Upload' class='bluebtn'/>";}
                                    ?>
                                    <div class="bluebtn" id="loader" style="display: none;">
                                        Uploading
                                        <img style="width: 4%;;" src='../../Images/loader.gif'/></div>
                                    </div>
                                </span>
                        </td>
                    </tr>
                </form>
            </table>
        </div>
    </div>
</div>
</div>
<?php include '../../Master/footer.php'; ?>
<script type="text/javascript" src="../../Master/errorHandling.js"></script>
</body>
<script>
    $( document ).ready(function() {
        /* attach a submit handler to the form */
        $('#theform').submit(function (event) {
            /* stop form from submitting normally */
            var formData = new FormData($(this)[0]);
            /*jquery that displays the three points loader*/

            var error = errorHandling($('#txtLibraryIndex'), '<?php echo $collection ?>');
            if(error.answer){
                for(i = 0; i < error.desc.length; i++) {
                    alert(error.desc[i].message)
                }
                return false
            }
            var eScale = errorHandling($('#txtMapScale'), '<?php echo $collection ?>');
            if(eScale.answer){
                for(i = 0; i < eScale.desc.length; i++) {
                    alert(eScale.desc[i].message)
                }
                return false
            }

             //TODO:: removed libraryindex underscore validation
//            if(validateFormUnderscore("txtLibraryIndex") == true)
//            {
                $('#btnSubmit').css("display", "none");
                $('#loader').css("display", "inherit");
                event.disabled;

                event.preventDefault();
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
                            window.location.href = "./catalog.php?col=<?php echo $_GET['col']; ?>";
                        }

                    }
                });
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
    });
</script>
</html>

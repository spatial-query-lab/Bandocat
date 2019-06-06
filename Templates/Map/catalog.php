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
$readrec = array("POOR","GOOD","EXCELLENT");
$userRole = $session->getRole();
?>
<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.0/css/all.css" integrity="sha384-aOkxzJ5uQz7WBObEZcHvV5JvRW3TUc2rNPA7pe3AwnsUohiw1Vj2Rgx2KSOkF5+h" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Font Awesome CDN CSS -->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <title>Input Form</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
</head>
<body>
<?php include "../../Master/bandocat_mega_menu.php"; ?>

<div class="container pad-bottom">
    <div class="row">
        <div class="col">
            <!-- Put Page Contents Here -->
            <h1 class="text-center"><?php echo $config["DisplayName"]; ?> Catalog Form</h1>
            <hr>

            <div class="d-flex justify-content-center">
                <!-- Card -->
                <div class="card" style="width: 75em;">
                    <div class="card-body">
                        <form id="theform" name="theform" method="post" enctype="multipart/form-data" class="needs-validation" novalidate >
                            <div class="row">
                                <!-- These are used the most often -->
                                <div class="col-6">
                                    <!-- Library Index -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtLibraryIndex">Library Index:</label>
                                        <div class="col-sm-8" id="libraryIndex">
                                            <input title="REQUIRED: Please enter a front scan to fill this text box." type = "text" class="form-control text-center" name = "txtLibraryIndex" id = "txtLibraryIndex" value="" required readonly />
                                        </div>
                                    </div>
                                    <!-- Document Title -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtTitle">Document Title:</label>
                                        <div class="col-sm-8" id="docTitle">
                                            <input type = "text" class="form-control" name = "txtTitle" id = "txtTitle" value="" required />
                                        </div>
                                    </div>
                                    <!-- Subtitle -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtSubtitle">Document Subtitle:</label>
                                        <div class="col-sm-8">
                                            <input type = "text" class="form-control" name = "txtSubtitle" id = "txtSubtitle" value="" />
                                        </div>
                                    </div>
                                    <!-- Document start -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtSubtitle">Document Start Date:</label>
                                        <div class="col-sm-8">
                                            <div class="d-flex">
                                                <select class="form-control" name="ddlStartMonth" id="ddlStartMonth">
                                                    <!-- POPULATES THE DDL WITH START MONTHS -->
                                                    <?php $Render->GET_DDL_MONTH(null); ?>
                                                </select>
                                                <select class="form-control" name="ddlStartDay" id="ddlStartDay">
                                                    <!-- POPULATES THE DDL WITH START DAYS -->
                                                    <?php $Render->GET_DDL_DAY(null); ?>
                                                </select>

                                                <select class="form-control" id="ddlStartYear" name="ddlStartYear">
                                                    <!-- POPULATES THE DDL WITH START YEARS -->
                                                    <?php $Render->GET_DDL_YEAR(null); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Document end -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtSubtitle">Document End Date:</label>
                                        <div class="col-sm-8">
                                            <div class="d-flex">
                                                <select class="form-control" name="ddlEndMonth" id="ddlEndMonth">
                                                    <!-- POPULATES THE DDL WITH END MONTHS -->
                                                    <?php $Render->GET_DDL_MONTH(null); ?>
                                                </select>
                                                <select class="form-control" name="ddlEndDay" id="ddlEndDay">
                                                    <!-- POPULATES THE DDL WITH END DAYS -->
                                                    <?php $Render->GET_DDL_DAY(null); ?>
                                                </select>
                                                <select class="form-control" name="ddlEndYear" id="ddlEndYear">
                                                    <!-- POPULATES THE DDL WITH END YEARS -->
                                                    <?php $Render->GET_DDL_YEAR(null); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Document Author -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtAuthor">Document Author:</label>
                                        <div class="col-sm-8">
                                            <input type = "text" class="form-control" list="lstAuthor" name = "txtAuthor" id = "txtAuthor" value="" />
                                            <datalist id="lstAuthor">
                                                <!-- POPULATE AUTHOR LIST-->
                                                <?php $Render->getDataList($DB->GET_AUTHOR_LIST($collection)); ?>
                                            </datalist>
                                        </div>
                                    </div>
                                    <!-- Radio Buttons -->
                                    <!-- Has Scale -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Has Scale:</label>
                                        <div class="col-sm-8 radioButton">
                                            <div class="form-check form-check-inline">
                                                <input type = "radio" class="form-check-input" name ="hasScale" id ="hasScale_yes"
                                                       value="1"/>
                                                <label class="form-check-label" for="hasScale_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input type = "radio" class="form-check-input" name = "hasScale" id ="hasScale_no"
                                                       value="0" checked />
                                                <label class="form-check-label" for="hasScale_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Has Scale Bar-->
                                    <div class="form-group row" id="ScaleBar" hidden>
                                        <label class="col-sm-4 col-form-label">Has Scale Bar:</label>
                                        <div class="col-sm-8 mt-2">
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name ="hasScaleBar" id ="hasScaleBar_yes"
                                                       value="1"/>
                                                <label class="form-check-label" for="hasScaleBar_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "hasScaleBar" id ="hasScaleBar_no"
                                                       value="0" checked />
                                                <label class="form-check-label" for="hasScaleBar_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Map Scale -->
                                    <div class="form-group row" id="mapScale" hidden>
                                        <label class="col-sm-4 col-form-label" for="mapScale">Map Scale:</label>
                                        <div class="col-sm-8" id="mainScaleDiv">
                                            <div class="d-flex">
                                                <input type="number" min="1" class="form-control" id="numberLeft" name="numberLeft">
                                                <select class="form-control" id="unitLeft" name="unitLeft">
                                                    <option value="in">in</option>
                                                    <option value="ft">ft</option>
                                                    <option value="vrs">vrs</option>
                                                </select>
                                                <input type="text" value="=" class="form-control" disabled style="background-color: #FFFFFF; text-align: center; border: none;">
                                                <input type="number" min="1" class="form-control" id="numberRight" name="numberRight">
                                                <select class="form-control" id="unitRight" name="unitRight">
                                                    <option value="ft">ft</option>
                                                    <option value="vrs">vrs</option>
                                                    <option value="in">in</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- is Map -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Is Map:</label>
                                        <div class="col-sm-8">
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbIsMap" id = "rbIsMap_yes" value="1" checked/>
                                                <label class="form-check-label" for="rbIsMap_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbIsMap" id = "rbIsMap_no" value="0"  />
                                                <label class="form-check-label" for="rbIsMap_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Needs Review -->
                                    <div class="form-group row" id="needsReview" hidden>
                                        <label class="col-sm-4 col-form-label">Needs Review:</label>
                                        <div class="col-sm-8">
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbNeedsReview" id = "rbNeedsReview_yes" value="1" checked/>
                                                <label class="form-check-label" for="rbNeedsReview_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbNeedsReview" id = "rbNeedsReview_no" value="0"  />
                                                <label class="form-check-label" for="rbNeedsReview_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Has North Arrow -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Has North Arrow:</label>
                                        <div class="col-sm-8">
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasNorthArrow" id = "rbHasNorthArrow_yes" value="1" checked/>
                                                <label class="form-check-label" for="rbHasNorthArrow_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasNorthArrow" id = "rbHasNorthArrow_no" value="0"  />
                                                <label class="form-check-label" for="rbHasNorthArrow_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Has Street -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Has Street:</label>
                                        <div class="col-sm-8">
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasStreets" id = "rbHasStreets_yes" value="1" />
                                                <label class="form-check-label" for="rbHasStreets_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasStreets" id = "rbHasStreets_no" value="0" checked />
                                                <label class="form-check-label" for="rbHasStreets_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- POI -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Has POI:</label>
                                        <div class="col-sm-8">
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasPOI" id = "rbHasPOI_yes" value="1" />
                                                <label class="form-check-label" for="rbHasPOI_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasPOI" id = "rbHasPOI_no" value="0"  checked/>
                                                <label class="form-check-label" for="rbHasPOI_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- POI Description -->
                                    <div class="form-group row" id="POI" hidden>
                                        <label for="POIDescription" class="col-sm-4 col-form-label">POI Description:</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" cols="35" rows="2" name="POIDescription" id="POIDescription" ></textarea>
                                        </div>
                                    </div>
                                    <!-- Has Coordinates -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Has Coordinates:</label>
                                        <div class="col-sm-8">
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasCoordinates" id = "rbHasCoordinates_yes" value="1" />
                                                <label class="form-check-label" for="rbHasCoordinates_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasCoordinates" id = "rbHasCoordinates_no" value="0"  checked/>
                                                <label class="form-check-label" for="rbHasCoordinates_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Has Coast -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">Has Coast:</label>
                                        <div class="col-sm-8">
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasCoast" id = "rbHasCoast_yes" value="1" />
                                                <label class="form-check-label" for="rbHasCoast_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline radioButton">
                                                <input type = "radio" class="form-check-input" name = "rbHasCoast" id = "rbHasCoast_no" value="0" checked />
                                                <label class="form-check-label" for="rbHasCoast_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- the right side -->
                                <div class="col-6">
                                    <!-- Customer Name -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtCustomer">Customer Name:</label>
                                        <div class="col-sm-8">
                                            <input type = "text" class="form-control" list="lstCustomer" name = "txtCustomer" id = "txtCustomer" value="" />
                                            <datalist id="lstCustomer">
                                                <!-- POPULATES THE DDL WITH CUSTOMER NAMES -->
                                                <?php $Render->getDataList($DB->GET_CUSTOMER_LIST($collection)); ?>
                                            </datalist>
                                        </div>
                                    </div>
                                    <!-- Company Name -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtCompany">Company Name:</label>
                                        <div class="col-sm-8">
                                            <input type = "text" class="form-control" list="lstCompany" name = "txtCompany" id = "txtCompany" size="26" value="" />
                                            <datalist id="lstCompany">
                                                <!-- POPULATES DDL FOR COMPANY -->
                                                <?php $Render->getDataList($DB->GET_COMPANY_LIST($collection)); ?>
                                            </datalist>
                                        </div>
                                    </div>
                                    <!-- Document type or Job number -->
                                    <div class="form-group row">
                                        <?php if($collection != "pennyfenner") : ?>
                                            <label class="col-sm-4 col-form-label" for="txtType">Document Type</label>
                                            <div class="col-sm-8">
                                                <input type = "text" class="form-control" name = "txtType" id = "txtType" value="" />
                                            </div>
                                        <?php endif; ?>
                                        <?php if($collection == "pennyfenner") : ?>
                                            <label class="col-sm-4 col-form-label" for="txtJobNumber">Job Number</label>
                                            <div class="col-sm-8">
                                                <input type = "text" class="form-control" name = "txtJobNumber" id = "txtJobNumber" value=""/>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                    <!-- Field Book Number -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtFieldBookNumber">Field Book Number</label>
                                        <div class="col-sm-8">
                                            <input type = "text" class="form-control" name = "txtFieldBookNumber" id = "txtFieldBookNumber" value=""/>
                                        </div>
                                    </div>
                                    <!-- Field Book Page -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="txtFieldBookPage">Field Book Page</label>
                                        <div class="col-sm-8">
                                            <input type = "text" class="form-control" name = "txtFieldBookPage" id = "txtFieldBookPage" value="" />
                                        </div>
                                    </div>
                                    <!-- Document Medium -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="ddlMedium">Document Medium</label>
                                        <div class="col-sm-8" id="docMedium">
                                            <select id="ddlMedium" name="ddlMedium" class="form-control" required>
                                                <!-- GET MAP MEDIUM FOR DDL-->
                                                <?php
                                                $Render->GET_DDL($DB->GET_TEMPLATE_MAP_MEDIUM_FOR_DROPDOWN($collection),null);
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Readability -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="ddlReadability">Readability</label>
                                        <div class="col-sm-8" id ="readability">
                                            <select id="ddlReadability" name="ddlReadability" class="form-control">
                                                <?php
                                                $Render->GET_DDL2($readrec,null);
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Rectifiability -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="ddlRectifiability">Rectifiability</label>
                                        <div class="col-sm-8" id ="rectifiability">
                                            <select id="ddlRectifiability" name="ddlRectifiability" class="form-control">
                                                <?php
                                                $Render->GET_DDL2($readrec,null);
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Scan front -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="fileUpload">Front Scan:</label>
                                        <div class="col-sm-8" id="frontScan">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="fileUpload" id="fileUpload" accept=".tif" required />
                                                <label class="custom-file-label" for="fileUpload">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Scan Back -->
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label" for="fileUploadBack">Back Scan:</label>
                                        <div class="col-sm-8" id="backScan">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="fileUploadBack" id="fileUploadBack" accept=".tif" />
                                                <label class="custom-file-label" for="fileUploadBack">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Comments -->
                                    <div class="form-row">
                                        <div class="form-group col">
                                            <label for="txtComments" class="col-form-label">Comments:</label>
                                            <textarea class="form-control" cols="35" rows="5" name="txtComments" id="txtComments" placeholder="Example: Tract located in Corpus Christi, Nueces County" ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="form row">
                                <div class="col">
                                    <div class="d-flex justify-content-between">
                                        <input type="reset" id="btnReset" name="btnReset" value="Reset" onclick="history.go(0);" class="btn btn-secondary"/>
                                        <input type = "hidden" id="txtDocID" name = "txtDocID" value = "" />
                                        <input type = "hidden" id="txtAction" name="txtAction" value="catalog" />  <!-- catalog or review -->
                                        <input type = "hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
                                        <span>
                                    <?php if($session->hasWritePermission())
                                    {echo "<input type='submit' id='btnSubmit' name='btnSubmit' value='Upload' class='btn btn-primary'/>";}
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div> <!-- Card -->
            </div>
        </div> <!-- col -->
    </div> <!-- row -->
</div><!-- Container -->
<!-- Doesn't matter where these go, this is for overlay effect and loader -->
<div id="loader"></div>
<div id="overlay"></div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalTitle">Instant Feedback Report</h5>
                <input type="text" hidden value="" id="status">
                <button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="responseModalBody">

            </div>
        </div>
    </div>
</div>
<?php include "../../Master/bandocat_footer.php" ?>


<!-- Complete JavaScript Bundle -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<!-- JQuery UI cdn -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

<!-- Our custom javascript file -->
<script type="text/javascript" src="../../Master/master.js"></script>
<script type="text/javascript" src="../../Master/errorMessage.js"></script>
<script type="text/javascript" src="../../Master/maps-ErrorHandling.js"></script>
<!--<script type="text/javascript" src="../../Master/errorHandling.js"></script>-->

<!-- This Script Needs to Be added to Every Page, If the Sizing is off from dynamic content loading, then this will need to be taken away or adjusted -->
<script>
    $(document).ready(function() {
        var docHeight = $(window).height() - $('#megaMenu').height();
        var footerHeight = $('#footer').height();
        var footerTop = $('#footer').position().top + footerHeight;

        if (footerTop < docHeight)
            $('#footer').css('margin-top', 0 + (docHeight - footerTop) + 'px');
    });

    $(window).resize(function() {
        var docHeight = $(window).height() - $('#megaMenu').height();
        var footerHeight = $('#footer').height();
        var footerTop = $('#footer').position().top + footerHeight;

        if (footerTop < docHeight)
            $('#footer').css('margin-top', 0 + (docHeight - footerTop) + 'px');
    });
</script>

<script>
    $( document ).ready(function() {
        /* attach a submit handler to the form */
        $('#theform').submit(function (event) {
            /* stop form from submitting normally */
            var formData = new FormData($(this)[0]);
            /*jquery that displays the three points loader*/

            /*var error = errorHandling($('#txtLibraryIndex'), '<//?php echo $collection ?>');
            if(error.answer){
                for(i = 0; i < error.desc.length; i++) {
                    alert(error.desc[i].message)
                }

                return false
            }
            var eScale = errorHandling($('#txtMapScale'), '<//?php echo $collection ?>');
            if(eScale.answer){
                for(i = 0; i < eScale.desc.length; i++) {
                    alert(eScale.desc[i].message)
                }
                return false
            }*/

            //TODO:: removed libraryindex underscore validation
//            if(validateFormUnderscore("txtLibraryIndex") == true)
//            {
            /*$('#btnSubmit').css("display", "none");
            $('#loader').css("display", "inherit");
            $("#overlay").show();
            $("#loader").show();*/
            //event.disabled;
            /* Send the data using post */

            // Name and values of content on form taken and stored
            var data = $('#theform').serializeArray();
            // Manually adding front and back scan values due to Serialize function
            // not picking up file types in form
            var frontValue = document.getElementById("fileUpload").value;
            var backValue = document.getElementById("fileUploadBack").value;
            data.push({name: 'fileUpload', value: frontValue});
            data.push({name: 'fileUploadBack', value: backValue});

            // Display data of form on console for development purposes
            /*for(var i = 0; i < data.length; i++)
            {
                console.log("****** ", i, " ******");
                console.log("Name ", data[i].name);
                console.log("Value ", data[i].value);
            }*/

            $(".alert").remove(); // Clears old alerts for new ones

            if(handleError(data) == false)
            {
                // Will go into this segment if there are no errors
                $.ajax({
                    type: 'post',
                    url: 'form_processing.php',
                    data:  formData,
                    processData: false,
                    contentType: false,
                    success:function(data){
                        console.log(data);
                        //alert("Catalog Successful!");
                        $('#responseModalBody').empty();
                        $('#responseModalBody').append('<p style="text-align: center"><font style="color: green">Catalog Successful!! <br> ...Loading blank form</font></p>');
                        $('#responseModal').modal('show');
                    },
                    fail: function() {
                        $('#responseModalBody').empty();
                        $('#responseModalBody').append('<p style="text-align: center"><font style="color: red">ERROR: Catalog Unsuccessful. Please report error!!</font></p>');
                        $('#responseModal').modal('show');
                    }
                });
            }
            else
            {
                // Will go here if there are errors
                console.log("Errors were found");
            }

            event.preventDefault();
        });
    });

    $('#fileUpload').change(function() {
        // Name of file and placeholder
        var file = this.files[0].name;
        //var dflt = $(this).attr("placeholder");
        if($(this).val()!="")
        {
            $(this).next().text(file);
        }

        var filename = $('#fileUpload').val().replace(/C:\\fakepath\\/i, '');
        filename = filename.replace(/\.tif/, '');
        $('#txtLibraryIndex').val(filename);
    });

    //Back scan check
    $('#fileUploadBack').change(function() {
        // Name of file and placeholder
        var file = this.files[0].name;
        if($(this).val()!=""){
            $(this).next().text(file);
        }
    });

    // Enabled and disables the hidden Map Scale row
    $('#hasScale_yes, #hasScale_no').change(function() {
        var hasMapScale = $('#hasScale_yes').prop('checked');
        if(hasMapScale)
        {
            console.log("Map has scale...");
            $('#mapScale').prop('hidden', false);
            $('#ScaleBar').prop('hidden', false);

        }
        else
        {
            console.log("Map doesn't have scale ...");
            $('#numberLeft').val("");
            $('#numberRight').val("");
            $('#mapScale').prop('hidden', true);
            $('#ScaleBar').prop('hidden', true);
        }
    });

    // Enabled and disables the map scale
    $('#hasScaleBar_yes, #hasScaleBar_no').change(function() {
        var hasScaleBar = $('#hasScaleBar_yes').prop('checked');
        if(hasScaleBar)
        {
            console.log("Map has scale bar...");
            $('#numberLeft').prop('disabled', true);
            $('#unitLeft').prop('disabled', true);
            $('#numberRight').prop('disabled', true);
            $('#unitRight').prop('disabled', true);
        }
        else
        {
            $('#numberLeft').val("");
            $('#numberRight').val("");
            $('#numberLeft').prop('disabled', false);
            $('#unitLeft').prop('disabled', false);
            $('#numberRight').prop('disabled', false);
            $('#unitRight').prop('disabled', false);
        }
    });

    // Enables and disables the hidden POI Description row
    $('#rbHasPOI_yes, #rbHasPOI_no').change(function() {
        var hasPOI = $('#rbHasPOI_yes').prop('checked');
        if(hasPOI)
        {
            console.log("Map has POI...");
            $('#POI').prop('hidden', false);
        }
        else
        {
            $('#POIDescription').val("");
            console.log("Map doesn't have scale bar...");
            $('#POI').prop('hidden', true);
        }
    });

    // Hides and shows "needs review" option depending on user role
    $('#needsReview').ready(function(){
        var userRole = "<?php echo $userRole ?>";
        //console.log(userRole);
        if ((userRole === "Admin") || (userRole === "Super Admin")){
            console.log ('Display. User is admin!');
            $('#needsReview').prop('hidden', false);
        }
        else{
            console.log('Hide. User is not admin!');
        }
    });

    // Reset page
    $('#btnReset').click(function() {
        location.reload();
    });

    // Reloads page when response modal is exited out of or hidden
    $('#responseModal').on('hidden.bs.modal', function () {
        location.reload();
    });

</script>
</body>
</html>
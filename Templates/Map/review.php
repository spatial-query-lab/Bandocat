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
    $readrec = array("POOR","GOOD","EXCELLENT");
}
else header('Location: ../../');

include '../../Library/DateHelper.php';
$date = new DateHelper();
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

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <title>Review Form</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
</head>
<body>
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container">
    <!-- Put Page Contents Here -->
    <h1 class="text-center"><?php echo $config['DisplayName'];?> Review Form</h1>
    <hr>
    <div class="row">
        <div class="col"></div>
        <?php
        if($session->isAdmin()) //if user is Admin, render the Document History (Log Info)
        {
            $arrayLogInfo = $DB->GET_LOG_INFO($collection, $docID);
            echo "<div class=\"col\"><h3 class='text-center'>Document History</h3>";
            echo "<table class=\"table table-sm table-striped table-bordered\"  cellspacing=\"0\" data-page-length='20'><thead><tr><th>Action</th><th>Username</th> <th>Timestamp</th></tr></thead><tbody>";

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
        ?>
        <div class="col"></div>
    </div>
    <div class="row pad-bottom">
        <div class="col">
            <hr>
            <!-- Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-center">Document Meta Data</h3>
                </div>
                <div class="card-body">
                    <form id="theform" name="theform" method="post" enctype="multipart/form-data" >
                        <div class="row">
                            <!-- These are used the most often -->
                            <div class="col-6">
                                <!-- Library Index -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="txtLibraryIndex">Library Index:</label>
                                    <div class="col-sm-8">
                                        <input type = "text" class="form-control" name = "txtLibraryIndex" id = "txtLibraryIndex" value='<?php echo htmlspecialchars($document['LibraryIndex'],ENT_QUOTES); ?>' required />
                                    </div>
                                </div>
                                <!-- Document Title -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="txtTitle">Document Title:</label>
                                    <div class="col-sm-8">
                                        <input type = "text" class="form-control" name = "txtTitle" id = "txtTitle" value='<?php echo htmlspecialchars($document['Title'],ENT_QUOTES); ?>' required />
                                    </div>
                                </div>
                                <!-- Subtitle -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="txtSubtitle">Document Subtitle:</label>
                                    <div class="col-sm-8">
                                        <input type = "text" class="form-control" name = "txtSubtitle" id = "txtSubtitle"  value='<?php echo htmlspecialchars($document['Subtitle'],ENT_QUOTES); ?>' />
                                    </div>
                                </div>
                                <!-- Document start -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Document Start Date:</label>
                                    <div class="col-sm-8">
                                        <div class="d-flex">
                                            <select class="form-control" name="ddlStartMonth" id="ddlStartMonth">
                                                <!-- POPULATES THE DDL WITH START MONTHS -->
                                                <?php $Render->GET_DDL_MONTH($date->splitDate($document['StartDate'])['Month']); ?>
                                            </select>
                                            <select class="form-control" name="ddlStartDay" id="ddlStartDay">
                                                <!-- POPULATES THE DDL WITH START DAYS -->
                                                <?php $Render->GET_DDL_DAY($date->splitDate($document['StartDate'])['Day']); ?>
                                            </select>

                                            <select class="form-control" id="ddlStartYear" name="ddlStartYear">
                                                <!-- POPULATES THE DDL WITH START YEARS -->
                                                <?php $Render->GET_DDL_YEAR($date->splitDate($document['StartDate'])['Year']); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Document end -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Document End Date:</label>
                                    <div class="col-sm-8">
                                        <div class="d-flex">
                                            <select class="form-control" name="ddlEndMonth" id="ddlEndMonth">
                                                <!-- POPULATES THE DDL WITH END MONTHS -->
                                                <?php $Render->GET_DDL_MONTH($date->splitDate($document['EndDate'])['Month']); ?>
                                            </select>
                                            <select class="form-control" name="ddlEndDay" id="ddlEndDay">
                                                <!-- POPULATES THE DDL WITH END DAYS -->
                                                <?php $Render->GET_DDL_DAY($date->splitDate($document['EndDate'])['Day']); ?>
                                            </select>
                                            <select class="form-control" name="ddlEndYear" id="ddlEndYear">
                                                <!-- POPULATES THE DDL WITH END YEARS -->
                                                <?php $Render->GET_DDL_YEAR($date->splitDate($document['EndDate'])['Year']); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Map Scale -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="txtMapScale">Map Scale:</label>
                                    <div class="col-sm-8">
                                        <input type = "text" class="form-control" name = "txtMapScale" id = "txtMapScale" value="<?php echo htmlspecialchars($document['MapScale'],ENT_QUOTES); ?>"  />
                                    </div>
                                </div>
                                <!-- Document Author -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="txtAuthor">Document Author:</label>
                                    <div class="col-sm-8">
                                        <input type = "text" class="form-control" list="lstAuthor" name = "txtAuthor" id = "txtAuthor" value="<?php echo $document['AuthorName']; ?>" />
                                        <datalist id="lstAuthor">
                                            <!-- POPULATE AUTHOR LIST-->
                                            <?php $Render->getDataList($DB->GET_AUTHOR_LIST($collection)); ?>
                                        </datalist>
                                    </div>
                                </div>
                                <!-- Radio Buttons -->
                                <!-- is Map -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Is Map:</label>
                                    <div class="col-sm-8">
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbIsMap" id = "rbIsMap_yes" value="1" <?php if($document['IsMap'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbIsMap_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbIsMap" id = "rbIsMap_no" value="0" <?php if($document['IsMap'] == 0) echo "checked"; ?> />
                                            <label class="form-check-label" for="rbIsMap_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Needs Review -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Needs Review:</label>
                                    <div class="col-sm-8">
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbNeedsReview" id = "rbNeedsReview_yes" value="1" <?php if($document['NeedsReview'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbNeedsReview_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbNeedsReview" id = "rbNeedsReview_no" value="0" <?php if($document['NeedsReview'] == 0) echo "checked"; ?> />
                                            <label class="form-check-label" for="rbNeedsReview_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Has North Arrow -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Has North Arrow:</label>
                                    <div class="col-sm-8">
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasNorthArrow" id = "rbHasNorthArrow_yes" value="1" <?php if($document['HasNorthArrow'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbHasNorthArrow_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasNorthArrow" id = "rbHasNorthArrow_no" value="0"  <?php if($document['HasNorthArrow'] == 0) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbHasNorthArrow_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Has Street -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Has Street:</label>
                                    <div class="col-sm-8">
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasStreets" id = "rbHasStreets_yes" value="1" <?php if($document['HasStreets'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbHasStreets_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasStreets" id = "rbHasStreets_no" value="0" <?php if($document['HasStreets'] == 0) echo "checked"; ?> />
                                            <label class="form-check-label" for="rbHasStreets_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- POI, point of interest -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Has POI:</label>
                                    <div class="col-sm-8">
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasPOI" id = "rbHasPOI_yes" value="1" <?php if($document['HasPOI'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbHasPOI_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasPOI" id = "rbHasPOI_no" value="0"  <?php if($document['HasPOI'] == 0) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbHasPOI_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Has Coordinates -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Has Coordinates:</label>
                                    <div class="col-sm-8">
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasCoordinates" id = "rbHasCoordinates_yes" value="1" <?php if($document['HasCoordinates'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbHasCoordinates_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasCoordinates" id = "rbHasCoordinates_no" value="0"  <?php if($document['HasCoordinates'] == 0) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbHasCoordinates_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Has Coast -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Has Coast:</label>
                                    <div class="col-sm-8">
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasCoast" id = "rbHasCoast_yes" value="1" <?php if($document['HasCoast'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbHasCoast_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type = "radio" class="form-check-input" name = "rbHasCoast" id = "rbHasCoast_no" value="0" <?php if($document['HasCoast'] == 0) echo "checked"; ?> />
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
                                        <input type = "text" class="form-control" list="lstCustomer" name = "txtCustomer" id = "txtCustomer" value="<?php echo htmlspecialchars($document['CustomerName'],ENT_QUOTES); ?>" />
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
                                        <input type = "text" class="form-control" list="lstCompany" name = "txtCompany" id = "txtCompany" size="26" value="<?php echo $document['CompanyName'];?>" />
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
                                            <input type = "text" class="form-control" name = "txtType" id = "txtType" value="<?php echo htmlspecialchars($document['Type'],ENT_QUOTES);?>" />
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
                                        <input type = "text" class="form-control" name = "txtFieldBookNumber" id = "txtFieldBookNumber" value="<?php if($document['FieldBookNumber'] != 0 && $document['FieldBookNumber'] != null) {echo htmlspecialchars($document['FieldBookNumber'],ENT_QUOTES);} ?>"/>
                                    </div>
                                </div>
                                <!-- Field Book Page -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="txtFieldBookPage">Field Book Page</label>
                                    <div class="col-sm-8">
                                        <input type = "text" class="form-control" name = "txtFieldBookPage" id = "txtFieldBookPage" value="<?php echo htmlspecialchars($document['FieldBookPage'],ENT_QUOTES); ?>" />
                                    </div>
                                </div>
                                <!-- Document Medium -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="ddlMedium">Document Medium</label>
                                    <div class="col-sm-8">
                                        <select id="ddlMedium" name="ddlMedium" class="form-control" required>
                                            <!-- GET MAP MEDIUM FOR DDL-->
                                            <?php
                                            $Render->GET_DDL($DB->GET_TEMPLATE_MAP_MEDIUM_FOR_DROPDOWN($collection),$document['Medium']);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Readability -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="ddlReadability">Readability</label>
                                    <div class="col-sm-8">
                                        <select id="ddlReadability" name="ddlReadability" class="form-control">
                                            <?php
                                            $Render->GET_DDL2($readrec,$document['Readability']);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Rectifiability -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label" for="ddlRectifiability">Rectifiability</label>
                                    <div class="col-sm-8">
                                        <select id="ddlRectifiability" name="ddlRectifiability" class="form-control">
                                            <?php
                                            $Render->GET_DDL2($readrec,$document['Rectifiability']);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Scan front -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Front Scan:</label>
                                    <div class="col-sm-8">
                                        <!--<div class="custom-file">
                                            <input type="file" class="custom-file-input" name="fileUpload" id="fileUpload" accept=".tif" required />
                                            <label class="custom-file-label" for="fileUpload">Choose file</label>
                                        </div>-->
                                        <?php
                                        if($document['FileName'] == '' || $document['FileNamePath'] == '')
                                            echo "<label class='col-form-label'>No file uploaded</label>";
                                        else {
                                            echo "<a href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\">(Click to download)</a><br>";
                                            echo "<a id='download_back' href=\"download.php?file=$config[StorageDir]$document[FileNameBackPath]\"><br><img src='" . '../../' . $config['ThumbnailDir'] . str_replace(".tif", ".jpg", $document['FileName']) . " ' alt = Error /></a>";
                                            echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNamePath'])/1024/1024, 2) . " MB";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <!-- Scan Back -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Back Scan:</label>
                                    <div class="col-sm-8">
                                        <!--<div class="custom-file">
                                            <input type="file" class="custom-file-input" name="fileUploadBack" id="fileUploadBack" accept=".tif" />
                                            <label class="custom-file-label" for="fileUploadBack">Choose file</label>
                                        </div>-->
                                        <?php
                                        if($document['FileNameBack'] == '' || $document['FileNameBackPath'] == '')
                                            echo "<label class='col-form-label'>No file uploaded</label>";
                                        else {
                                            echo "<a href=\"download.php?file=$config[StorageDir]$document[FileNameBackPath]\">(Click to download)</a><br>";
                                            echo "<a id='download_back' href=\"download.php?file=$config[StorageDir]$document[FileNameBackPath]\"><br><img src='" . '../../' . $config['ThumbnailDir'] . str_replace(".tif", ".jpg", $document['FileNameBack']) . " ' alt = Error /></a>";
                                            echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNameBackPath'])/1024/1024, 2) . " MB";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <!-- Georec download url, front kmz and front geotiff -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Front KMZ:</label>
                                    <div class="col-sm-8">
                                        <div class="d-flex flex-row justify-content-between">
                                            <?php
                                            $georecinfo = $DB->DOCUMENT_GEOREC_INFO_SELECT($docID);
                                            if($georecinfo['geoRecFrontStatus'] == 1)
                                            {
                                                echo "<a class='btn btn-primary' href=\"download.php?file=$config[GeoRecDir]$georecinfo[georecFrontDirKMZ]\"><span class=\"fas fa-file-download\"></span> Front KMZ</a>";
                                                echo "<a class='btn btn-secondary' href=\"download.php?file=$config[GeoRecDir]$georecinfo[georecFrontDirGeoTIFF]\"><span class=\"fas fa-file-download\"></span> Front GeoTIFF</a>";
                                            }

                                            else
                                            {
                                                echo "<label class='col-form-label'>No file uploaded</label>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- Georec download url, back kmz and back geotiff -->
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Front KMZ:</label>
                                    <div class="col-sm-8">
                                        <div class="d-flex flex-row justify-content-between">
                                            <?php
                                            $georecinfo = $DB->DOCUMENT_GEOREC_INFO_SELECT($docID);
                                            if($georecinfo['geoRecFrontStatus'] == 1)
                                            {
                                                echo "<a class='btn btn-primary' href=\"download.php?file=$config[GeoRecDir]$georecinfo[georecBackDirKMZ]\"><span class=\"fas fa-file-download\"></span> Back KMZ</a>";
                                                echo "<a class='btn btn-secondary' href=\"download.php?file=$config[GeoRecDir]$georecinfo[georecBackDirGeoTIFF]\"><span class=\"fas fa-file-download\"></span> Back GeoTIFF</a>";
                                            }

                                            else
                                            {
                                                echo "<label class='col-form-label'>No file uploaded</label>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- Comments -->
                                <div class="form-row">
                                    <div class="form-group col">
                                        <label for="txtComments" class="col-form-label">Comments:</label>
                                        <textarea class="form-control" cols="35" rows="5" name="txtComments" id="txtComments" ><?php echo $document['Comments']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="form row">
                            <div class="col">
                                <div class="d-flex justify-content-between">
                                    <input type="reset" id="btnReset" name="btnReset" value="Reset" class="btn btn-secondary"/>
                                    <input type = "hidden" id="txtDocID" name = "txtDocID" value = "<?php echo $docID;?>" />
                                    <input type = "hidden" id="txtAction" name="txtAction" value="review" />  <!-- catalog or review -->
                                    <input type = "hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
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
    </div> <!-- Row -->
</div><!-- Container -->
<!-- Doesn't matter where these go, this is for overlay effect and loader -->
<div id="overlay"></div>
<?php include "../../Master/bandocat_footer.php" ?>

<!-- Complete JavaScript Bundle -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<!-- JQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

<!-- Our custom javascript file -->
<script type="text/javascript" src="../../Master/master.js"></script>

<!-- This Script Needs to Be added to Every Page, If the Sizing is off from dynamic content loading, then this will need to be taken away or adjusted -->
<script>
    $(document).ready(function() {

        var docHeight = $(window).height();
        var footerHeight = $('#footer').height();
        var footerTop = $('#footer').position().top + footerHeight;

        if (footerTop < docHeight)
            $('#footer').css('margin-top', 0 + (docHeight - footerTop) + 'px');
    });
</script>
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
            //$('#overlay').show();
            //event.disabled;

            event.preventDefault();
            /* Send the data using post */
            $.ajax(
                {
                    type: 'post',
                    url: 'form_processing.php',
                    data: $('#theform').serializeArray(),
                    success: function (data) {
                        console.log(data);
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
                                $('#overlay').removeAttr("style").hide();
                            }
                        }
                        alert(msg);

                        if (result == 1){
                            $('#overlay').removeAttr("style").hide();
                            self.close();
                        }
                    }
                })
        });
    });


</script>
</body>
</html>
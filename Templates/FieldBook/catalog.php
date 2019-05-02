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
require '../../Library/FieldBookDBHelper.php';
require '../../Library/DateHelper.php';
require '../../Library/ControlsRender.php';
$Render = new ControlsRender();
$DB = new FieldBookDBHelper();
$theDB = new DBHelper();
//get appropriate DB
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
//select fieldbook documents
$document = $DB->SP_TEMPLATE_FIELDBOOK_DOCUMENT_SELECT($collection, $docID);
$date = new DateHelper();
//select crew by document
$crews = $DB->GET_FIELDBOOK_CREWS_BY_DOCUMENT_ID($collection,$docID);
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

    <title>Catalog Form</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
</head>
<body>
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container" style="padding-bottom: 15px;">
    <div class="row">
        <div class="col">
            <!-- Put Page Contents Here -->
            <h1 class="text-center">Catalog Form</h1>
            <hr>

            <div class="d-flex justify-content-center">
                <div class="card">
                    <div class="card-body" style="width: 50em;">
                        <form id="theform" name="theform" method="post" enctype="multipart/form-data">
                            <!-- Populates the control with data -->
                            <datalist id="lstAuthor">
                                <?php $Render->getDataList($DB->GET_AUTHOR_LIST($collection)); ?>
                            </datalist>
                            <!-- Populates the control with data -->
                            <datalist id="lstCollection">
                                <?php $Render->getDataList($DB->GET_FIELDBOOK_COLLECTION_LIST($collection)); ?>
                            </datalist>
                            <!-- Populates the control with data -->
                            <datalist  id="lstCrew">
                                <?php $Render->getDataList($DB->GET_CREW_LIST($collection)); ?>
                            </datalist>
                            <!-- Library Index -->
                            <div class="form-group row">
                                <label for="txtLibraryIndex" class="col-sm-3 col-form-label">Library Index:</label>
                                <div class="col-sm-9">
                                    <input type = "text" rel="txtToolTip" data-toggle="tooltip" data-placement="left" title="If you want to edit this index, use the Edit/View operation in the menu" class="form-control-plaintext" name = "txtLibraryIndex" id = "txtLibraryIndex" value="<?php echo htmlspecialchars($document['LibraryIndex'],ENT_QUOTES);?>" readonly />
                                </div>
                            </div>
                            <!-- Book Title -->
                            <div class="form-group row">
                                <label for="txtBookTitle" class="col-sm-3 col-form-label">Book Title:</label>
                                <div class="col-sm-9">
                                    <input type = "text" class="form-control-plaintext" name = "txtBookTitle" id = "txtBookTitle" readonly />
                                </div>
                            </div>
                            <!-- Collection -->
                            <div class="form-group row">
                                <label for="txtFBCollection" class="col-sm-3 col-form-label">Collection:</label>
                                <div class="col-sm-9">
                                    <select id="txtFBCollection" name="txtFBCollection" class="form-control" required>
                                        <!-- GET MAP MEDIUM FOR DDL-->
                                        <?php $Render->GET_DDL_COLLECTION($theDB->GET_COLLECTION_FOR_DROPDOWN(),null);?>
                                    </select>
                                </div>
                            </div>
                            <!-- Job Number -->
                            <div class="form-group row">
                                <label for="txtJobNumber" class="col-sm-3 col-form-label">Job Number:</label>
                                <div class="col-sm-9">
                                    <input type = "text" class="form-control" name = "txtJobNumber" id = "txtJobNumber" value="<?php echo htmlspecialchars($document['JobNumber'],ENT_QUOTES);?>"  />
                                </div>
                            </div>
                            <!-- Job Title -->
                            <div class="form-group row">
                                <label for="txtJobTitle" class="col-sm-3 col-form-label">Job Title:</label>
                                <div class="col-sm-9">
                                    <input type = "text" class="form-control" name = "txtJobTitle" id = "txtJobTitle" value="<?php echo htmlspecialchars($document['JobTitle'],ENT_QUOTES);?>"  />
                                </div>
                            </div>
                            <!-- Indexed Page -->
                            <div class="form-group row">
                                <label for="txtIndexedPage" class="col-sm-3 col-form-label">Indexed Page:</label>
                                <div class="col-sm-9">
                                    <input type = "text" class="form-control" name = "txtIndexedPage" id = "txtIndexedPage" value="<?php echo htmlspecialchars($document['IndexedPage'],ENT_QUOTES);?>"  />
                                </div>
                            </div>
                            <!-- Radio Buttons -->
                            <div class="form-group row">
                                <!-- Blank Page -->
                                <div class="col">
                                    <label class="col col-form-label">Blank Page:</label>
                                    <div class="form-check col-sm-10">
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-check-input" name = "rbBlankPage" id = "rbBlankPage_yes" value="1" <?php if($document['IsBlankPage'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbBlankPage_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-check-input" name = "rbBlankPage" id = "rbBlankPage_no" value="0"  <?php if($document['IsBlankPage'] == 0) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbBlankPage_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Sketch -->
                                <div class="col">
                                    <label class="col col-form-label">Sketch:</label>
                                    <div class="form-check col-sm-10">
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-check-input" name = "rbSketch" id = "rbSketch_yes" value="1" <?php if($document['IsSketch'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbSketch_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-check-input" name = "rbSketch" id = "rbSketch_no" value="0" <?php if($document['IsSketch'] == 0) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbSketch_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Loose Document -->
                                <div class="col">
                                    <label class="col col-form-label">Loose Document:</label>
                                    <div class="form-check col-sm-10">
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-control-input" name = "rbLooseDocument" id = "rbLooseDocument_yes" value="1" <?php if($document['IsLooseDoc'] == 1) echo "checked"; ?>/>
                                            <label class="form-check-label" for="rbLooseDocument_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-control-input" name = "rbLooseDocument" id = "rbLooseDocument_no" value="0" <?php if($document['IsLooseDoc'] == 0) echo "checked"; ?> />
                                            <label class="form-check-label" for="rbLooseDocument_no">No</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Needs Review -->
                                <div class="col">
                                    <label class="col col-form-label">Needs Review:</label>
                                    <div class="form-check col-sm-10 bg-white">
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-control-input" name = "rbNeedsReview" id = "rbNeedsReview_yes" size="26" <?php if($document['NeedsReview'] == 1) echo "checked"; ?> />
                                            <label class="form-check-label" for="rbNeedsReview_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-control-input" name = "rbNeedsReview" id = "rbNeedsReview_no" value="0" <?php if($document['NeedsReview'] == 0) echo "checked"; ?> />
                                            <label class="form-check-label" for="rbNeedsReview_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Field Book Author -->
                            <div class="form-group row">
                                <label for="txtBookAuthor" class="col-sm-3 col-form-label">Field Book Author:</label>
                                <div class="col-sm-9">
                                    <input type = "text" class="form-control" name = "txtBookAuthor" id = "txtBookAuthor" list="lstAuthor" value="<?php echo htmlspecialchars($document['Author'],ENT_QUOTES);?>"  />
                                </div>
                            </div>
                            <div id="crewcell">
                                <!-- Field Crew Member -->
                                <div class="form-group row">
                                    <label for="txtCrew[]" class="col-sm-3 col-form-label">Field Crew Member:</label>
                                    <div class="col-sm-8">
                                        <input type = "text" class="form-control" name = "txtCrew[]" id = "txtCrew[]" value="<?php if(count($crews) > 0){echo htmlspecialchars($crews[0][0],ENT_QUOTES);} ?>" autocomplete="off" list="lstCrew" />
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="button" class="btn btn-primary" id="more_fields" onclick="add_fields(null);" value="+"/>
                                    </div>
                                </div>
                            </div>
                            <!-- Document Start Date -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Document Start Date:</label>
                                <!-- Month -->
                                <div class="col">
                                    <select name="ddlStartMonth" id="ddlStartMonth" class="form-control">
                                        <?php $Render->GET_DDL_MONTH($date->splitDate($document['StartDate'])['Month']); ?>
                                    </select>
                                </div>
                                <!-- Day -->
                                <div class="col">
                                    <select name="ddlStartDay" id="ddlStartDay" class="form-control">
                                        <?php $Render->GET_DDL_DAY($date->splitDate($document['StartDate'])['Day']); ?>
                                    </select>
                                </div>
                                <!-- Year -->
                                <div class="col">
                                    <select id="ddlStartYear" name="ddlStartYear" class="form-control">
                                        <?php $Render->GET_DDL_YEAR($date->splitDate($document['StartDate'])['Year']); ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Document End Date -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Document End Date:</label>
                                <!-- Month -->
                                <div class="col">
                                    <select name="ddlEndMonth" id="ddlEndMonth" class="form-control">
                                        <?php $Render->GET_DDL_MONTH($date->splitDate($document['EndDate'])['Month']); ?>
                                    </select>
                                </div>
                                <!-- Day -->
                                <div class="col">
                                    <select name="ddlEndDay" id="ddlEndDay" class="form-control">
                                        <?php $Render->GET_DDL_DAY($date->splitDate($document['EndDate'])['Day']); ?>
                                    </select>
                                </div>
                                <!-- Year -->
                                <div class="col">
                                    <select name="ddlEndYear" id="ddlEndYear" class="form-control">
                                        <?php $Render->GET_DDL_YEAR($date->splitDate($document['EndDate'])['Year']); ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Comments -->
                            <div class="form-group row">
                                <label for="txtComments" class="col-sm-3 col-form-label">Comments:</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" cols="35" rows="5" name="txtComments" id="txtComments" ><?php echo $document['Comments']?></textarea>
                                </div>
                            </div>
                            <!-- Scan of Page -->
                            <div class="form-group row">
                                <div class="col-sm-3 col-form-label">Scan of Page:</div>
                                <div class="col-sm-9">
                                    <?php echo "<a href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\">(Click to download)</a><br>";
                                    echo "<a id='download_front' href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\"><br><img src='" .  '../../' . $config['ThumbnailDir'] . $document['Thumbnail'] . " ' alt = Error /></a>";
                                    echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNamePath'])/1024/1024, 2) . " MB";
                                    ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col">
                                    <div class="d-flex justify-content-between">
                                        <div><input type="reset" id="btnReset" name="btnReset" value="Reset" class="btn btn-secondary"/></div>
                                        <div><?php if($session->hasWritePermission())
                                            {echo "<input type='submit' id='btnSubmit' name='btnSubmit' value='Update' class='btn btn-primary'/>";}
                                            ?></div>

                                        <input type="hidden" id="txtDocID" name = "txtDocID" value = "<?php echo $docID;?>" />
                                        <input type="hidden" id="txtAction" name="txtAction" value="catalog" />  <!-- catalog or review -->
                                        <input type="hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div> <!-- Card -->
            </div>
        </div> <!-- Col -->
    </div> <!-- row -->
</div><!-- Container -->
<!-- Doesn't matter where these go, this is for overlay effect and loader -->
<div id="overlay"></div>
<?php include "../../Master/bandocat_footer.php" ?>

<!-- Complete JavaScript Bundle -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script type="text/javascript" src="../../Master/errorHandling.js"></script>

<!-- This Script Needs to Be added to Every Page, If the Sizing is off from dynamic content loading, then this will need to be taken away or adjusted -->
<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        var docHeight = $(window).height();
        var footerHeight = $('#footer').height();
        var footerTop = $('#footer').position().top + footerHeight;

        if (footerTop < docHeight)
            $('#footer').css('margin-top', 0 + (docHeight - footerTop) + 'px');

        var n1 = document.getElementById('txtLibraryIndex').value;
        var newN1 = n1.substr(1);
        newN1 = newN1.substr(0, 3);
        document.getElementById('txtBookTitle').value = newN1;
    });
</script>
<script>
    /**********************************************
     * Function: add_fields
     * Description: adds more fields for the crew members
     * Parameter(s):
     * val (in String ) - name of the crew
     * Return value(s):
     * $result (assoc array) - return a document info in an associative array, or FALSE if failed
     ***********************************************/
    var max = 9;
    var crew_count = 1;
    function add_fields(val)
    {
        if(val == null)
            val = "";
        if(crew_count >= max)
            return false;
        crew_count++;
        var objTo = document.getElementById('crewcell');
        var div = document.createElement("div");

        div.innerHTML = '<div class="form-group row">\n' +
            '                                <label for="txtCrew[]" class="col-sm-3 col-form-label">Field Crew Member ' + crew_count + ':</label>\n' +
            '                                <div class="col-sm-9">\n' +
            '                                    <input type = "text" class="form-control" name = "txtCrew[]" id = "txtCrew[]" value="<?php if (count($crews) > 0) {
                echo htmlspecialchars($crews[0][0], ENT_QUOTES);
            } ?>" autocomplete="off" list="lstCrew" />\n' +
            '                                </div>\n' +
            '                            </div>';

        $("#crewcell").append(div);
    }
</script>
<script>
    $(document).ready(function()
    {
        var crews = <?php echo json_encode($crews); ?>;
        //Parse out the authors read in to the add_fields function
        for(var i = 1; i < crews.length; i++)
        {
            add_fields(crews[i][0]);
        }
        /* attach a submit handler to the form */
        $('#theform').submit(function (event)
        {
            event.preventDefault();
            /* stop form from submitting normally */
            //This attaches the entire "#theform" in addition to the crews to the post
            var formData = new FormData($(this)[0]);

            //Append Crews data to the form
            var crews = $('[name="txtCrew[]');
            var array_crews = [];
            for(var i = 0; i < crews.length; i++)
                array_crews.push(crews[i].value);
            formData.append("crews",JSON.stringify(array_crews));

            // Use jquery to show the overlay and the loading circle
            //$("#overlay").show();

            /* Send the data using post */
            $.ajax({
                type: 'post',
                url: 'form_processing.php',
                data:  formData,
                processData: false,
                contentType: false,
                success:function(data)
                {
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
                            $('#overlay').removeAttr("style").hide();
                        }
                    }
                    if (result == 1){
                        alert(msg);
                        $('#overlay').removeAttr("style").hide();
                        self.close();
                    }

                }
            });
        });
    });
</script>
</body>
</html>
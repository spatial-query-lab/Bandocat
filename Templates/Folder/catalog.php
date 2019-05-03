<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
//var_dump($session);
$userRole = $session->getRole();
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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.0/css/all.css" integrity="sha384-aOkxzJ5uQz7WBObEZcHvV5JvRW3TUc2rNPA7pe3AwnsUohiw1Vj2Rgx2KSOkF5+h" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Job Folder Catalog Form</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
</head>
<body onload="onloadChecks()">
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <!-- Put Page Contents Here -->
            <!-- <h1 class="text-center">Blank Page</h1> -->
            <div class="row">
                <!-- Start of description of Classification method chosen-->
                <div class="col-1" id="classificationCard">
                    <div class="card" id="card" style="width: 18rem; margin-left: 65px; margin-top: 250px;">
                        <div class="card-body">
                            <h5 class="card-title" style="text-align: center; font-size:18px; text-decoration: underline;">Classification Description:</h5>
                            <p class="card-text" id="descriptionText"></p>
                        </div>
                    </div>
                </div>
                <!-- End of description of Classification method chosen-->
                <div class="col">
                    <!-- Put Page Contents Here -->
                    <h1 class="text-center"><?php echo $config["DisplayName"]; ?> Catalog Form</h1>
                    <hr>
                    <div class="d-flex justify-content-center">
                        <!-- Card -->
                        <div class="card" style="width: 75em;">
                            <div class="card-body">
                                <form id="theform" name="theform" method="post" enctype="multipart/form-data" >
                                    <div class="row">
                                        <!-- These are used the most often -->
                                        <!-- The Left side -->
                                        <div class="col-6">
                                            <!-- Library Index -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtLibraryIndex">Library Index:</label>
                                                <div class="col-sm-8">
                                                    <input type = "text" class="form-control" name="txtLibraryIndex" id="txtLibraryIndex" value='<?php echo htmlspecialchars($document['LibraryIndex'],ENT_QUOTES);?>' required disabled/>
                                                </div>
                                            </div>
                                            <!-- Document Title -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtTitle">Document Title:</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="txtTitle" id="txtTitle" value='<?php echo htmlspecialchars($document['Title'],ENT_QUOTES);?>' required />
                                                </div>
                                            </div>
                                            <!-- Document Author -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtAuthor">Document Author:</label>
                                                <div class="col-sm-8">
                                                    <input type = "text" class="form-control" list="lstAuthor" name="txtAuthor" id="txtAuthor" value="" />
                                                    <datalist id="lstAuthor">
                                                        <!-- POPULATE AUTHOR LIST-->
                                                        <?php $Render->getDataList($DB->GET_AUTHOR_LIST($collection)); ?>
                                                    </datalist>
                                                </div>
                                            </div>
                                            <!-- Radio Buttons Start -->
                                            <!-- Needs Review -->
                                            <div class="form-group row" id="needsReview">
                                                <label class="col-sm-4 col-form-label">Needs Review:</label>
                                                <div class="col-sm-8">
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input" name="folderNeedsReview" id="folderNeedsReview_yes" value="1" checked />
                                                        <label class="form-check-label" for="folderNeedsReview_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input" name="folderNeedsReview" id="folderNeedsReview_no" value="0"/>
                                                        <label class="form-check-label" for="folderNeedsReview_no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- In a Subfolder -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">In a Subfolder:</label>
                                                <div class="col-sm-8">
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input" name="inSubfolder" id="inSubfolder_yes" value="1" />
                                                        <label class="form-check-label" for="inSubfolder_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input" name="inSubfolder" id="inSubfolder_no" value="0" checked />
                                                        <label class="form-check-label" for="inSubfolder_no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Radio Buttons End -->
                                            <!-- Classification -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="classificationMethod">Classification:</label>
                                                <div class="col-sm-8">
                                                    <select id="classificationMethod" name="classificationMethod" class="form-control" onchange="classificationDescription()" required>
                                                        <!-- GET FOLDER CLASSIFICATION LIST -->
                                                        <?php
                                                        $Render->GET_DDL_TOOLTIP($DB->GET_FOLDER_CLASSIFICATION_LIST($collection),$document['Classification']);
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Classification Comments -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtClassificationComments">Classification Comments:</label>
                                                <div class="col-sm-8" >
                                                    <textarea type="text" class="form-control" name="txtClassificationComments" id="txtClassificationComments"></textarea>
                                                </div>
                                            </div>
                                            <!-- Subfolder Comments -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtSubfolderComments">Subfolder Comments:</label>
                                                <div class="col-sm-8" >
                                                    <textarea class="form-control" name="txtSubfolderComments" id="txtSubfolderComments"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- The Right Side -->
                                        <div class="col-6">
                                            <!-- Document Start Date -->
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
                                            <!-- Document End Date -->
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

                                            <!-- Front Scan -->
                                            <div class="form-group row">

                                                <span class="label" style="text-align: center">Front Scan:</span><br>
                                                <?php
                                                echo "<a id='download_front' href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\"><br><img src='" .  '../../' . $config['ThumbnailDir'] . str_replace(".tif",".jpg",$document['FileName']) . " ' alt = Error /></a>";
                                                echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNamePath'])/1024/1024, 2) . " MB";
                                                echo "<br><a href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\">(Click to download)</a>";
                                                ?>
                                            </div>


                                            <!-- Front Scan -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Front Scan:</label>
                                                <div class="col-sm-8">
                                                    <div class="custom-file spinner-border text-dark" role="status">
                                                        <input type="file" class="custom-file-input" name="fileUpload" id="fileUpload" accept=".tif" onchange="frontUpload()" required/>
                                                        <label class="custom-file-label text-truncate" for="fileUpload">Choose file</label>
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Scan Back -->
                                            <div class="form-group row" >
                                                <label class="col-sm-4 col-form-label spinner-border text-dark">Back Scan:</label>
                                                <div class="col-sm-8">
                                                    <div class="custom-file spinner-border text-dark" role="status">
                                                        <input type="file" class="custom-file-input" name="fileUploadBack" id="fileUploadBack" accept=".tif" onchange="backUpload()" />
                                                        <label class="custom-file-label" for="fileUploadBack">Choose file</label>
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- General Comments -->
                                            <div class="form-row">
                                                <div class="form-group col">
                                                    <label for="txtComments" class="col-form-label">Comments:</label>
                                                    <textarea class="form-control" cols="35" rows="5" name="txtComments" id="txtComments" placeholder="Example: Job No. 4441, Sheet No. 74, with sketch."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Buttons -->
                                    <div class="form row">
                                        <div class="col">
                                            <div class="d-flex justify-content-between">
                                                <input type="reset" id="btnReset" name="btnReset" value="Reset" onclick="resetPage()" class="btn btn-secondary"/>
                                                <input type = "hidden" id="txtDocID" name = "txtDocID" value = "" />
                                                <input type = "hidden" id="txtAction" name="txtAction" value="catalog" />  <!-- catalog or review -->
                                                <input type = "hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
                                                <span>
                                                    <?php if($session->hasWritePermission())
                                                    {echo "<input type='submit' id='btnSubmit' name='btnSubmit' value='Submit' class='btn btn-primary'/>";}
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
        </div> <!-- col -->
    </div> <!-- row -->
</div><!-- Container -->

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

<!-- This Script Needs to Be added to Every Page, If the Sizing is off from dynamic content loading, then this will need to be taken away or adjusted -->
<!-- This Script Needs to Be added to Every Page, If the Sizing is off from dynamic content loading, then this will need to be taken away or adjusted -->
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
            //$('#loader').css("display", "inherit");
            $("#overlay").show();
            $("#loader").show();
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
                            //$('#loader').css("display", "none");
                            $('#overlay').removeAttr("style").hide();
                            $('#loader').removeAttr("style").hide();
                        }
                    }
                    alert(msg);
                    if (result == 1){
                        window.location.href = "./catalog.php?col=<?php echo $_GET['col']; ?>";
                    }

                }
            });
        });
    });

    // *****************************************************************************************************************
    $("[type=file]").on("change", function(){
        // Name of file and placeholder
        var file = this.files[0].name;
        var dflt = $(this).attr("placeholder");
        if($(this).val()!=""){
            $(this).next().text(file);
        } else {
            $(this).next().text(dflt);
        }
    });

    // *****************************************************************************************************************
    // AUTO POPULATING LIBRARY INDEX FIELD WITH NAME OF UPLOADED FILE. ALSO PERFORMS UPLOADED FILES VALIDATION.
    // UPLOADS THAT FAIL THE VALIDATION TEST ARE DISCARDED

    // Front scan check
    document.getElementById("fileUpload").onchange = frontUpload;
    function frontUpload() {
        var fileName = this.value;
        window.fileName = fileName;

        if ((fileName.includes("back") || fileName.includes("Back")) === true) {
            alert('Make sure to upload a front scan instead of a back scan.');
            document.getElementById('fileUpload').value = null;
            document.getElementById('txtLibraryIndex').value = null;
        }
        else if ((fileName.includes(" ") || fileName.includes(" - Copy") || fileName.includes("-Copy")) === true) {
            alert('Invalid file name. Change name to include version of copy (i.e. '+ fileName.substring(12, fileName.indexOf(' ')) + '.2)');
            document.getElementById('fileUpload').value = null;
            document.getElementById('txtLibraryIndex').value = null;
        }
        else{
            console.log('Valid File');
            document.getElementById('txtLibraryIndex').value = fileName.substring(12, fileName.indexOf('.tif'));
            document.getElementById('txtLibraryIndex').style.textAlign = 'center';
        }
    }

    // Back scan check
    document.getElementById("fileUploadBack").onchange = backUpload;
    function backUpload() {
        var backFileName = this.value;
        window.backFileName = backFileName;

        if ((backFileName.includes("back") || backFileName.includes("Back")) === false) {
            alert('Make sure to upload a back scan instead of a front scan.');
            document.getElementById('fileUploadBack').value = null;
        }
        else if ((backFileName.includes(" ") || backFileName.includes(" - Copy") || backFileName.includes("-Copy")) === true) {
            alert('Invalid file name. Change name to include version of copy (i.e. '+ backFileName.substring(12, backFileName.indexOf('(back)')) + '.2(back)');
            document.getElementById('fileUploadBack').value = null;
        }
        else{
            console.log('Valid File');
        }
    }

    // *****************************************************************************************************************
    function resetPage(){
        window.location.reload();
    }

    // *****************************************************************************************************************
    /************************* ONLOAD EVENTS (ADMIN CHECK AND CLASSIFICATION CARD VISIBILITY) ************************/
    // HIDES "NEEDS REVIEW" DIV IF CURRENT USER IS NOT AN ADMIN AND HIDES CLASSIFICATION CARD UNTIL AN OPTION IS SELECTED
    function onloadChecks(){
        // Checks if user is admin
        var userRole = "<?php echo $userRole ?>";
        if ((userRole === "Admin") || (userRole === "admin")){
            //document.getElementById('needsReview').style.display = 'yes';
            console.log('Display. User is admin');
        }
        else{
            document.getElementById('needsReview').style.display = 'none';
            console.log("Hide. User is not admin");
        }

        // Hides classification card when no viable classification method is selected
        var description = document.getElementById("classificationMethod").value;
        var values = ["Correspondence", "Envelope/Binding", "Field Note", "Folder Cover", "Legal Description", "Legal Document", "Legal Document Draft", "Map/Blueprint", "Note", "Separation Sheet", "Stencil", "Survey Calculation"]

        if ((description === values) === true){
            document.getElementById('classificationCard').style.visibility = "visible";
        }
        else{
            console.log('no classification chosen, hide card');
            document.getElementById('classificationCard').style.visibility = "hidden";
        }
    }

    // *****************************************************************************************************************
    /***************************************** CLASSIFICATION DESCRIPTION *********************************************/

    // Card with description of chosen classification
    function classificationDescription() {
        var description = document.getElementById("classificationMethod").value;

        // Correspondence
        if ((description === "Correspondence") === true){
            document.getElementById('descriptionText').innerHTML = "Correspondence: appears to be a conversation. Often an official telegram, but can still be messages left at hotels or offices.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Envelope/Binding
        else if ((description === "Envelope/Binding") === true){
            document.getElementById('descriptionText').innerHTML = "Envelope/Binding: anything from an envelope to a taped piece of paper used to bind documents. They are blank and contain no information.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Field note
        else if ((description === "Field Note") === true){
            document.getElementById('descriptionText').innerHTML = "Field note: actual page from a field book or a typed report of field book notes. Often titled 'Field Notes' or is a list of survey point information.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Folder cover
        else if ((description === "Folder Cover") === true){
            document.getElementById('descriptionText').innerHTML = "Folder cover: scanned copy of the original job folder.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        //Legal description
        else if ((description === "Legal Description") === true){
            document.getElementById('descriptionText').innerHTML = "Legal description: written geographical description of a property for the purpose of identifying the property for legal transactions.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Legal document
        else if ((description === "Legal Document") === true){
            document.getElementById('descriptionText').innerHTML = "Legal document: typed and signed documents pertaining to a survey, land tenure or sale, or work contract. Often contains an official stamp or notary.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Legal document draft
        else if ((description === "Legal Document Draft") === true){
            document.getElementById('descriptionText').innerHTML = "Legal document draft: legal document that has not been officiated or contains review marks.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Map/Blueprint
        else if ((description === "Map/Blueprint") === true){
            document.getElementById('descriptionText').innerHTML = "Map/Blueprint: large sized maps (excludes smaller map drafts because they are considered a sketch, therefore a 'Survey Calculation').";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // None
        else if ((description === "None") === true){
            document.getElementById('descriptionText').innerHTML = "None: No particular classification.";
            document.getElementById('classificationCard').style.visibility = "hidden";
        }

        // Note
        else if ((description === "Note") === true){
            document.getElementById('descriptionText').innerHTML = "Note: contains minimal information and cannot be otherwise classified.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Separation sheet
        else if ((description === "Separation Sheet") === true){
            document.getElementById('descriptionText').innerHTML = "Separation sheet: index sheet provided by the Mary & Jeff Bell Library at Texas A&M University - Corpus Christi denoting a document whose physical condition is too poor to be scanned. The original map or document can only be accessed on-site, in person.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Stencil
        else if ((description === "Stencil") === true){
            document.getElementById('descriptionText').innerHTML = "Stencil: document used to replicate specific fonts, symbols, or texts.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Survey calculation
        else if ((description === "Survey Calculation") === true){
            document.getElementById('descriptionText').innerHTML = "Survey calculation: recorded arithmetic pertaining to a survey. Often on a yellow paper and contains sketches.";
            document.getElementById('classificationCard').style.visibility = "visible";
        }

        // Otherwise...
        else {
            console.log("Nothing was selected");
            document.getElementById('classificationCard').style.visibility = "hidden";
        }

        // Classification description layout
        document.getElementById('descriptionText').style.textAlign = 'center';
        document.getElementById('descriptionText').style.fontSize = '13px';
    }

    document.getElementById('txtLibraryIndex').style.textAlign = 'center';
    document.getElementById('txtTitle').style.textAlign = 'center';

</script>
</body>
</html>
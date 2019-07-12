<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
//var_dump($session);
$userRole = $session->getRole();
//get collection name from passed variables col and doc
if(isset($_GET['col']) && isset($_GET['doc']))
{
    $collection = $_GET['col'];
    //$docID = $_GET['doc'];
    $docID = mt_rand(1, 1000);
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
$classification = $DB->GET_FOLDER_CLASSIFICATION_LIST($collection);
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
    <title>Job Folder Catalog Form Training</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
</head>
<body>
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <!-- Put Page Contents Here -->
            <!-- <h1 class="text-center">Blank Page</h1> -->
            <div class="row">
                <!-- Start of description of Classification method chosen-->
                <div class="col-1" id="classificationCard" style="display: none">
                    <div class="card" id="card" style="width: 18rem; margin-left: 65px; margin-top: 250px;">
                        <div class="card-body">
                            <h5 class="card-title" id="className" style="text-align: center; font-size:18px; text-decoration: underline;"></h5>
                            <p class="card-text" id="classDesc" style="text-align: center; font-size: 13px"></p>
                        </div>
                    </div>
                </div>
                <!-- Populates the control with data -->
                <datalist id="lstAuthor">
                    <?php $Render->getDataList($DB->GET_AUTHOR_LIST($collection)); ?>
                </datalist>
                <!-- End of description of Classification method chosen-->
                <div class="col">
                    <!-- Put Page Contents Here -->
                    <h1 class="text-center"><?php echo $config["DisplayName"]; ?> Catalog Form Training</h1>
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
                                                <label class="col-sm-4 col-form-label" for="txtLibraryIndex"><font style="color: red">* </font>Library Index:</label>
                                                <div class="col-sm-8">
                                                    <input type = "text" class="form-control" name="txtLibraryIndex" id="txtLibraryIndex" value='<?php echo htmlspecialchars($document['LibraryIndex'],ENT_QUOTES);?>' required readonly/>
                                                </div>
                                            </div>
                                            <!-- Document Title -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtTitle" data-toggle="tooltip" title="Keep in mind that the Document Title can be the same as the Library Index but doesn't always have to be (Can also be some form of description)"><font style="color: red">* </font>Document Title:</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="txtTitle" id="txtTitle" required />
                                                </div>
                                            </div>
                                            <!-- Document Author -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtAuthor" data-toggle="tooltip" title="Provide if information is given (MAX of 6)">Document Author:</label>
                                                <div class="col-sm-7">
                                                    <input class="form-control" type="text" id="txtAuthor" name="txtAuthor[]" size="26" list="lstAuthor" value="<?php if(count($authors) > 0){echo htmlspecialchars($authors[0][0],ENT_QUOTES);} ?>"/>
                                                </div>
                                                <div>
                                                    <input type="button" class="btn btn-primary" onclick="add_fields(null);" id="more_fields" value="+"/>
                                                </div>
                                            </div>
                                            <div class="form-group row" id="authorcell">

                                            </div>
                                            <!-- Document Start Date -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtSubtitle">Document Start Date:</label>
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
                                            <!-- Document End Date -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtSubtitle">Document End Date:</label>
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
                                            <!-- Radio Buttons Start -->
                                            <!-- Needs Review -->
                                            <div class="form-group row" id="needsReview">
                                                <label class="col-sm-4 col-form-label" data-toggle="tooltip" title="If document needs to be reviewed by an admin">Needs Review:</label>
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
                                                <label class="col-sm-4 col-form-label" data-toggle="tooltip" title="This will auto check if document has a period in its name. Otherwise keep it checked as no">In a Subfolder:</label>
                                                <div class="col-sm-8" id="subFolder">
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input" name="rbInASubfolder" id="inSubfolder_yes" value="1" />
                                                        <label class="form-check-label" for="inSubfolder_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" class="form-check-input" name="rbInASubfolder" id="inSubfolder_no" value="0" checked />
                                                        <label class="form-check-label" for="inSubfolder_no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Radio Buttons End -->
                                            <!-- Classification -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="classificationMethod" data-toggle="tooltip" title="Describes the document type"><font style="color: red">* </font>Classification:</label>
                                                <div class="col-sm-8">
                                                    <select id="classificationMethod" name="classificationMethod" class="form-control" required>
                                                        <!-- GET FOLDER CLASSIFICATION LIST -->
                                                        <?php
                                                        $Render->GET_DDL_TOOLTIP($DB->GET_FOLDER_CLASSIFICATION_LIST($collection),$document['None']);
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- Classification Comments -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtClassificationComments" data-toggle="tooltip" title="Further comments can be provided if necessary">Classification Comments:</label>
                                                <div class="col-sm-8" >
                                                    <textarea class="form-control" name="txtClassificationComments" id="txtClassificationComments"><?php echo $document["ClassificationComment"];?></textarea>
                                                </div>
                                            </div>
                                            <!-- Subfolder Comments -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtSubfolderComments" data-toggle="tooltip" title="Further comments can be provided if necessary">Subfolder Comments:</label>
                                                <div class="col-sm-8" >
                                                    <textarea class="form-control" name="txtSubfolderComments" id="txtSubfolderComments"><?php echo $document["SubfolderComment"]; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- The Right Side -->
                                        <div class="col-6">
                                            <!-- Scan of Front -->
                                            <div class="form-group row">
                                                <table>
                                                    <tr>
                                                        <td style="text-align: center;">
                                                            <!-- Scan of Front -->
                                                            <span class="label" style="text-align: center;" data-toggle="tooltip" title="Displays front scan"> Scan of Front:</span><br>
                                                            <?php
                                                            echo "<a id='download_front' href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\"><br><img src='" .  '../../' . $config['ThumbnailDir'] . str_replace(".tif",".jpg",$document['FileName']) . " ' alt = Error /></a>";
                                                            echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNamePath'])/1024/1024, 2) . " MB";
                                                            echo "<br><a href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\">(Click to download)</a>";
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- Scan of Back -->
                                            <div class="form-group row">
                                                <table>
                                                    <tr>
                                                        <td style="text-align: center;">
                                                            <span class="label" style="text-align: center;" data-toggle="tooltip" title="Displays back scan if any is provided"> Scan of Back:</span><br>
                                                            <?php
                                                            if($document['FileNameBack'] != '') //has Back Scan
                                                            {
                                                                echo "<a id='download_front' href=\"download.php?file=$config[StorageDir]$document[FileNameBackPath]\"><br><img src='" . '../../' . $config['ThumbnailDir'] . str_replace(".tif", ".jpg", $document['FileNameBack']) . " ' alt = Error /></a>";
                                                                echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNameBackPath']) / 1024 / 1024, 2) . " MB";
                                                                echo "<br><a href=\"download.php?file=$config[StorageDir]$document[FileNameBackPath]\">(Click to download)</a>";
                                                            }
                                                            else
                                                            {
                                                                echo '<span class="label" style="text-align: center;">No Scan of Back</span><br>';
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- General Comments -->
                                            <div class="form-row">
                                                <div class="form-group col">
                                                    <label for="txtComments" class="col-form-label">Comments:</label>
                                                    <textarea class="form-control" cols="35" rows="4" name="txtComments" id="txtComments" placeholder="Example: Job No. 4441, Sheet No. 74, with sketch."><?php echo $document["Comments"]; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Buttons -->
                                    <div class="form row">
                                        <div class="col">
                                            <div class="d-flex justify-content-between">
                                                <div class="row pl-3">
                                                    <input type="reset" id="btnReset" name="btnReset" value="New Document" onclick="resetPage()" data-toggle="tooltip" title="Will display a new document for training" class="btn btn-secondary"/>
                                                    <div class="pl-2">
                                                        <input type='button' id='help' name='help' value='Help' data-toggle="tooltip" title="Click here for tips!" class='btn btn-success'/>
                                                    </div>
                                                </div>
                                                <input type = "hidden" id="txtDocID" name = "txtDocID" value = "<?php echo $docID; ?>" />
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

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpModalTitle">Helpful Tips</h5>
                <input type="text" hidden value="" id="status">
                <button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="helpModalBody">
                <p>- Library Index is auto-populated</p>
                <p>- Fill in the missing information</p>
                <p>- Hover over text of difficult fields for a helpful description</p>
                <p>- Required fields have a red asterisk <font style="color: red">*</font></p>
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

<!-- This Script Needs to Be added to Every Page, If the Sizing is off from dynamic content loading, then this will need to be taken away or adjusted -->
<!-- This Script Needs to Be added to Every Page, If the Sizing is off from dynamic content loading, then this will need to be taken away or adjusted -->
<script>
    var max = 5;
    var author_count = 0;

    $( document ).ready(function() {

        // Initializing correct answers for checking when document is submitted from user
        var correctTitle = "<?php echo htmlspecialchars($document['Title'],ENT_QUOTES);?>";
        var correctClassification = "<?php echo htmlspecialchars($document['Classification'],ENT_QUOTES);?>";
        // Correct Document Dates
        var correctStartDay = "<?php echo htmlspecialchars($date->splitDate($document['StartDate'])['Day']);?>";
        var correctStartMonth = "<?php echo htmlspecialchars($date->splitDate($document['StartDate'])['Month']);?>";
        var correctStartYear = "<?php echo htmlspecialchars($date->splitDate($document['StartDate'])['Year']);?>";
        var correctStartDate = correctStartDay.concat("/", correctStartMonth, "/", correctStartYear);

        var correctEndDay = "<?php echo htmlspecialchars($date->splitDate($document['EndDate'])['Day']);?>";
        var correctEndMonth = "<?php echo htmlspecialchars($date->splitDate($document['EndDate'])['Month']);?>";
        var correctEndYear = "<?php echo htmlspecialchars($date->splitDate($document['EndDate'])['Year']);?>";
        var correctEndDate = correctEndDay.concat("/", correctEndMonth, "/", correctEndYear);

        var correctTitle = "<?php echo htmlspecialchars($document['Title'],ENT_QUOTES);?>";

        //Shows correct answers in user console log
        console.log("Document Title: ", correctTitle);
        console.log("Classification: ", correctClassification);
        console.log("Start Date: ", correctStartDate);
        console.log("End Date: ", correctEndDate);

        //Parse out the authors read in to the add_fields function
        var authors = <?php echo json_encode($authors); ?>;
        for(var i = 1; i < authors.length; i++)
        {
            add_fields(authors[i][0]);
        }

        //console.log("Authors: ", authors);

        for(var i = 0; i < authors.length; i++)
            console.log("Authors: ", authors[i][0]);

        //var numberAuth = </?php echo count($authors); ?>;
        //console.log("Authors: ", numberAuth);

        /* attach a submit handler to the form */
        $('#theform').submit(function (event) {
            /* stop form from submitting normally */
            var formData = new FormData($(this)[0]);

            //Append Authors data to the form
            var theAuthors = $('[name="txtAuthor[]');
            var array_authors = [];
            for(var i = 0; i < theAuthors.length; i++)
                array_authors.push(theAuthors[i].value);
            formData.append("authors",JSON.stringify(array_authors));

            for(var z = 0; z < authors.length; z++)
            {
                console.log("Authors: ", authors[z][0]);
            }

            console.log("****************************************");

            for(var i = 0; i < theAuthors.length; i++)
            {
                console.log("theAuthors: ", theAuthors[i].value);
            }

            ////////////////////////////////////////////////////////////////////// TRAINING CHECK //////////////////////////////////////////////////////////////////////

            var errors = 0;
            // Doc Title
            var title = $('#txtTitle').val();

            // Classification
            var classification = $('#classificationMethod').val();

            // User Dates entered that need to be checked
            var startDay = $('#ddlStartDay').val();
            var startMonth = $('#ddlStartMonth').val();
            var startYear = $('#ddlStartYear').val();
            var startDate = startDay.concat("/", startMonth, "/", startYear);

            var endDay = $('#ddlEndDay').val();
            var endMonth = $('#ddlEndMonth').val();
            var endYear = $('#ddlEndYear').val();
            var endDate = endDay.concat("/", endMonth, "/", endYear);

            //console.log("startDay: ", startDay);
            $('#responseModalBody').empty(); // Clearing response modal of previous data

            // Checking
            if(correctTitle != title) // Checks to see if the user entered title is the same as the stored title for this document (if correct)
            {
                errors++;
                $('#responseModalBody').append('<p style="text-align: center"><font style="color: red"><strong>Error: </strong>Incorrect Title. Please try again</font></p>');
            }
            if(correctClassification != classification) // Checks to see if the user entered Classification is the same as the stored Classification for this document (if correct)
            {
                errors++;
                $('#responseModalBody').append('<p style="text-align: center"><font style="color: red"><strong>Error: </strong>Incorrect Classification. Please try again</font></p>');
            }
            if(startDate != correctStartDate) // Checks to see if the user entered Start Date is the same as the stored Start Date for this document (if correct)
            {
                errors++;
                $('#responseModalBody').append('<p style="text-align: center"><font style="color: red"><strong>Error: </strong>Incorrect Start Date. Please try again</font></p>');
            }
            if(endDate != correctEndDate) // Checks to see if the user entered End Date is the same as the stored End Date for this document (if correct)
            {
                errors++;
                $('#responseModalBody').append('<p style="text-align: center"><font style="color: red"><strong>Error: </strong>Incorrect End Date. Please try again</font></p>');
            }


            // Authors Checking
            for(var x = 0; x < authors.length; x++) // Checks to see if the user entered Author is the same as the stored Author for this document (if correct)
            {
                if(authors[x][0] != theAuthors[x].value)
                {
                    errors++;
                    $('#responseModalBody').append('<p style="text-align: center"><font style="color: red"><strong>Error: </strong>Incorrect Author '+ x+1 +'. Please try again</font></p>');
                }
            }

            // If there weren't any errors, a success message will appear via modal, and a new page will be loaded
            if(errors == 0)
            {
                $('#responseModalBody').append('<p style="text-align: center"><font style="color: green">Job Folder Catalog Training Successful!! <br> ...Loading new form</font></p>');
            }

            event.preventDefault();

            // Shows the response modal with the appropriate appended information
            $('#responseModal').modal('show');
            ////////////////////////////////////////////////////////////////////// TRAINING CHECK END //////////////////////////////////////////////////////////////////////

        });

        ///// This section checks to see if document is in a subfolder /////
        var libIndex = $('#txtLibraryIndex').val();
        var decimal = /\./g;

        var decimalCheck = decimal.test(libIndex);

        /*console.log("Title", docTitle);
        console.log("check", decimalCheck);*/

        if(decimalCheck == true)
        {
            $('#subFolder').append('<font style="color: red">File must be subfolder </font>');
            $('#inSubfolder_yes').prop("checked", true);

            $('#inSubfolder_yes, #inSubfolder_no').change(function() {
                alert("ERROR: Must be checked yes when decimal is present!");
                $('#inSubfolder_yes').prop("checked", true);
            });
        }

        ///// This section displays the classification description /////
        var classList =  <?php echo json_encode($classification); ?>;
        $('#classificationCard').show();
        var classText = $('#classificationMethod option:selected').text();
        if(classText == "Select")
        {
            $('#classificationCard').hide();
        }

        $("#className").text(classText);
        for(var x = 0; x < classList.length; x++) {
            if(classList[x][0] == classText) {
                $('#classDesc').text(classList[x][1])
            }
        }
    });

    function add_fields(val)
    {
        if(val == null)
            val = "";
        if(author_count >= max)
            return false;
        author_count++;
        $('#authorcell').append('<label class="col-sm-4 col-form-label">Document Author ' + (author_count+1) + ':</label><div class="col-sm-8"><input class="form-control" type = "text" name = "txtAuthor[]" autocomplete="off" id = "txtAuthor" size="26" value="' + val + '" list="lstAuthor" /></div><br><br>');
    }

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

    // Text alignment properties for Library Index and Title
    document.getElementById('txtLibraryIndex').style.textAlign = "center";
    document.getElementById('txtTitle').style.textAlign = "center";

    // *****************************************************************************************************************
    // AUTO POPULATING LIBRARY INDEX FIELD WITH NAME OF UPLOADED FILE. ALSO PERFORMS UPLOADED FILES VALIDATION.
    // UPLOADS THAT FAIL THE VALIDATION TEST ARE DISCARDED


    // *****************************************************************************************************************
    function resetPage(){
        window.location.reload();
    }

    // *****************************************************************************************************************
    /***************************************** CLASSIFICATION DESCRIPTION *********************************************/

    // Card with description of chosen classification

    $('#classificationMethod').change(function () {
        var classList =  <?php echo json_encode($classification); ?>;
        $('#classificationCard').show();
        var classText = $('#classificationMethod option:selected').text();
        if(classText == "Select")
        {
            $('#classificationCard').hide();
        }

        $("#className").text(classText);
        for(var x = 0; x < classList.length; x++) {
            if(classList[x][0] == classText) {
                $('#classDesc').text(classList[x][1])
            }
        }

    });

    // displays the help modal
    $('#help').click(function() {
        $('#helpModal').modal('show');
    });

</script>
</body>
</html>
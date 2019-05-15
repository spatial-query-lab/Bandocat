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
    <title>Job Folder Catalog Form</title>

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
                                                    <input type = "text" class="form-control" name="txtLibraryIndex" id="txtLibraryIndex" value='<?php echo htmlspecialchars($document['LibraryIndex'],ENT_QUOTES);?>' required readonly/>
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
                                                <label class="col-sm-4 col-form-label" for="classificationMethod">Classification:</label>
                                                <div class="col-sm-8">
                                                    <select id="classificationMethod" name="classificationMethod" class="form-control" required>
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
                                                    <textarea class="form-control" name="txtClassificationComments" id="txtClassificationComments"><?php echo $document["ClassificationComment"];?></textarea>
                                                </div>
                                            </div>
                                            <!-- Subfolder Comments -->
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label" for="txtSubfolderComments">Subfolder Comments:</label>
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
                                                            <span class="label" style="text-align: center;"> Scan of Front:</span><br>
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
                                                            <span class="label" style="text-align: center;"> Scan of Back:</span><br>
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
                                                <input type="reset" id="btnReset" name="btnReset" value="Reset" onclick="resetPage()" class="btn btn-secondary"/>
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
        //Parse out the authors read in to the add_fields function
        var authors = <?php echo json_encode($authors); ?>;
        for(var i = 1; i < authors.length; i++)
        {
            add_fields(authors[i][0]);
        }

        /* attach a submit handler to the form */
        $('#theform').submit(function (event) {
            /* stop form from submitting normally */
            var formData = new FormData($(this)[0]);

            //Append Authors data to the form
            var authors = $('[name="txtAuthor[]');
            var array_authors = [];
            for(var i = 0; i < authors.length; i++)
                array_authors.push(authors[i].value);
            formData.append("authors",JSON.stringify(array_authors));
            /*jquery that displays the three points loader*/

            /*var error = errorHandling($('#txtLibraryIndex'), '</?php echo $collection ?>');
            if(error.answer){
                for(i = 0; i < error.desc.length; i++) {
                    alert(error.desc[i].message)
                }
                return false
            }
            var eScale = errorHandling($('#txtMapScale'), '</?php echo $collection ?>');
            if(eScale.answer){
                for(i = 0; i < eScale.desc.length; i++) {
                    alert(eScale.desc[i].message)
                }
                return false
            }*/

            console.log(formData);

            event.preventDefault();
            /* Send the data using post */
            $.ajax({
                type: 'post',
                url: 'form_processing.php',
                data:  formData,
                processData: false,
                contentType: false,
                success:function(data){
                    console.log("We here big dog\n");
                    console.log(data);
                    /*var json = JSON.parse(data);
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
                        window.location.href = "./catalog.php?col=</?php echo $_GET['col']; ?>";
                    }*/

                }
            });
        });
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

</script>
</body>
</html>
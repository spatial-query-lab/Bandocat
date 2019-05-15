<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
$userRole = $session->getRole();
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
//get appropriate db
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
//selelct template folder document
$document = $DB->SP_TEMPLATE_FOLDER_DOCUMENT_SELECT($collection, $docID);
$date = new DateHelper();
//get the authors by document id
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
    <title>Edit/View</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
</head>
<body onload="onloadChecks()">
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container pad-bottom">
    <div class="row">
        <div class="col">
            <!-- Put Page Contents Here -->
            <h1 class="text-center"><?php echo $config['DisplayName'];?> Review Form</h1>
            <hr>
            <div class="row">

                <!-- Start of description of Classification method chosen-->
                <div class="col-1" id="classificationCard" style="display: none">
                    <div class="card" id="card" style="width: 18rem; margin-left: 15px; margin-top: 250px;">
                        <div class="card-body">
                            <h5 class="card-title" id="className" style="text-align: center; font-size:18px; text-decoration: underline;"></h5>
                            <p class="card-text" id="classDesc" style="text-align: center; font-size: 13px"></p>
                        </div>
                    </div>
                </div>

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

            <div class="d-flex justify-content-center">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Document Meta Data</h3>
                    </div>
                    <div class="card-body" style="width: 50em;">
                        <form id="theform" name="theform" method="post" enctype="multipart/form-data">
                            <!-- Populates the control with data -->
                            <datalist id="lstAuthor">
                                <!-- Populates the control with data -->
                                <?php $Render->getDataList($DB->GET_AUTHOR_LIST($collection)); ?>
                            </datalist>
                            <!-- Library Index -->
                            <div class="form-group row">
                                <label for="txtLibraryIndex" class="col-sm-3 col-form-label">Library Index:</label>
                                <div class="col-sm-9">
                                    <input type = "text" class="form-control" name = "txtLibraryIndex" id = "txtLibraryIndex" style="text-align: center;" value="<?php echo htmlspecialchars($document['LibraryIndex'],ENT_QUOTES);?>" />
                                </div>
                            </div>
                            <!-- Document Title -->
                            <div class="form-group row">
                                <label for="txtTitle" class="col-sm-3 col-form-label">Document Title:</label>
                                <div class="col-sm-9">
                                    <input type = "text" class="form-control" name = "txtTitle" id = "txtTitle" style="text-align: center;" value="<?php echo htmlspecialchars($document['Title'],ENT_QUOTES);?>" required />
                                </div>
                            </div>
                            <!-- Radio Buttons -->
                            <div class="form-group row">
                                <!-- Loose Document -->
                                <div class="col">
                                    <label class="col col-form-label">In a Subfolder:</label>
                                    <div class="form-check col-sm-10">
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-control-input" name = "rbInASubfolder" id = "rbInASubfolder_yes" size="26" value="1" <?php if($document['InSubfolder'] == 1) echo "checked"; ?> />
                                            <label class="form-check-label" for="rbInASubfolder_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-control-input" name = "rbInASubfolder" id = "rbInASubfolder_no" size="26" value="0" <?php if($document['InSubfolder'] == 0) echo "checked"; ?> />
                                            <label class="form-check-label" for="rbInASubfolder_no">No</label>
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
                            <div id="authorcell">
                                <!-- Document Author(s) -->
                                <div class="form-group row">
                                    <label for="txtAuthor[]" class="col-sm-3 col-form-label">Document Author:</label>
                                    <div class="col-sm-8">
                                        <input type = "text" class="form-control" name = "txtAuthor[]" id = "txtAuthor[]" value="<?php if(count($authors) > 0){echo htmlspecialchars($authors[0][0],ENT_QUOTES);} ?>" autocomplete="off" list="lstAuthor" />
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="button" class="btn btn-primary" id="more_fields" onclick="add_fields(null);" value="+"/>
                                    </div>
                                </div>
                            </div>
                            <!-- Subfolder Comments -->
                            <div class="form-group row">
                                <label for="txtSubfolderComments" class="col-sm-3 col-form-label">Subfolder Comments:</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" cols = "35" name="txtSubfolderComments" id="txtSubfolderComments"/><?php echo $document['SubfolderComment']; ?></textarea>
                                </div>
                            </div>
                            <!-- Classification -->
                            <div class="form-group row">
                                <label for="ddlClassification" class="col-sm-3 col-form-label">Classification:</label>
                                <div class="col-sm-9">
                                    <select id="ddlClassification" class="form-control" name="ddlClassification" onchange="classificationDescription()" required>
                                        <?php
                                        $Render->GET_DDL($DB->GET_FOLDER_CLASSIFICATION_LIST($collection),$document['Classification']);
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Classification Comments -->
                            <div class="form-group row">
                                <label for="txtClassificationComments" class="col-sm-3 col-form-label">Classification Comments:</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows = "2" cols = "35" id="txtClassificationComments" name="txtClassificationComments"/><?php echo $document['ClassificationComment']; ?></textarea>
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
                            <!-- Thumbnail Front -->
                            <div class="form-group row">
                                <div class="col-sm-3 col-form-label">Scan of Front:</div>
                                <div class="col-sm-9">
                                    <?php
                                    echo "<a id='download_front' href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\"><br><img src='" .  '../../' . $config['ThumbnailDir'] . str_replace(".tif",".jpg",$document['FileName']) . " ' alt = Error /></a>";
                                    echo "<br>Size: " . round(filesize($config['StorageDir'] . $document['FileNamePath'])/1024/1024, 2) . " MB";
                                    echo "<br><a href=\"download.php?file=$config[StorageDir]$document[FileNamePath]\">(Click to download)</a>";
                                    ?>
                                </div>
                            </div>
                            <!-- Thumbnail Back -->
                            <div class="form-group row">
                                <div class="col-sm-3 col-form-label">Scan of Back:</div>
                                <div class="col-sm-9">
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
                                        echo "<label class='col-form-label'>No file uploaded</label>";
                                    }
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

                                        <input type = "hidden" id="txtDocID" name = "txtDocID" value = "<?php echo $docID;?>" />
                                        <input type = "hidden" id="txtAction" name="txtAction" value="review" />  <!-- catalog or review -->
                                        <input type = "hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
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
    });
</script>
<!-- This page's script -->
<script>
    /**********************************************
     * Function: add_fields
     * Description: adds more fields for the crew members
     * Parameter(s):
     * val (in String ) - name of the crew
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
        divtest.innerHTML = '<div class="form-group row">\n' +
            '                                <label for="txtAuthor[]" class="col-sm-3 col-form-label">Document Author ' + author_count + ':</label>\n' +
            '                                <div class="col-sm-9">\n' +
            '                                    <input type = "text" class="form-control" name = "txtAuthor[]" id = "txtAuthor" value="' + val + '" autocomplete="off" list="lstAuthor" />\n' +
            '                                </div>\n' +
            '                            </div>';
        //divtest.innerHTML = '<br><span class="label">Document Author ' + (author_count+1) + '</span><input type = "text" name = "txtAuthor[]" autocomplete="off" id = "txtAuthor" size="26" value="' + val + '" list="lstAuthor" />';
        objTo.appendChild(divtest);
    }

    $( document ).ready(function()
    {
        var authors = <?php echo json_encode($authors); ?>;
        for(var i = 1; i < authors.length; i++)
        {
            add_fields(authors[i][0]);
        }
        /* attach a submit handler to the form */
        $('#theform').submit(function (event) {
            event.preventDefault();
            /* stop form from submitting normally */
            var formData = new FormData($(this)[0]);

            //Append Authors data to the form
            var authors = $('[name="txtAuthor[]');
            var array_authors = [];
            for(var i = 0; i < authors.length; i++)
                array_authors.push(authors[i].value);
            formData.append("authors",JSON.stringify(array_authors));

            /*jquery that displays the three points loader*/
            $('#overlay').show();

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
                            $('#overlay').css("display", "none");
                        }
                    }
                    alert(msg);
                    if (result == 1){
                        $('#overlay').css("display", "none");
                        window.close();
                    }

                }
            });
        });
    });


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

</script>
</body>
</html>

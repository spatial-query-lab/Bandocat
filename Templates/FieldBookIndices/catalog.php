<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
//get collection name from passed variables col and doc
if(isset($_GET['col']))
{
    $collection = $_GET['col'];
}
else header('Location: ../../');

require '../../Library/DBHelper.php';
require '../../Library/IndicesDBHelper.php';
require '../../Library/DateHelper.php';
require '../../Library/ControlsRender.php';
$Render = new ControlsRender();
$DB = new IndicesDBHelper();
//get indices
$book = $DB->GET_INDICES_BOOK($collection);
//get appropriate DB
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
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
    <title>Catalog Form</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
</head>
<body>
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<!-- The CSS class pad-bottom comes from our custom bootstrap css file, its not actually bootstrap -->
<div class="container pad-bottom">
    <div class="row">
        <div class="col">
            <!-- Put Page Contents Here -->
            <h1 class="text-center"><?php echo $config["DisplayName"]; ?> Catalog Form</h1>
            <hr>

            <!-- Using flex to center the card onto the page -->
            <div class="d-flex justify-content-center">
                <!-- Card -->
                <div class="card" style="width: 40em;">
                    <div class="card-body">
                        <!-- We need to define a form so that the user can upload the metadata to the server -->
                        <!-- You don't necessarily have to use forms, but they usually make this process easier -->
                        <form id="theform" name="theform" method="post" enctype="multipart/form-data">
                            <!-- There are different ways to style a form, but we are going to copy Bandocat's -->
                            <!-- original page formatting. -->
                            <!-- Scan of the page -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Front Scan:</label>
                                <div class="col-sm-9">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="fileUpload" id="fileUpload" accept=".tif" required />
                                        <label class="custom-file-label" for="fileUpload">Choose file</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Library Index -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="txtLibraryIndex">Library Index:</label>
                                <div class="col-sm-9">
                                    <input type = "text" class="form-control" name = "txtLibraryIndex" id = "txtLibraryIndex" value="" required />
                                </div>
                            </div>
                            <!-- Book Title -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ddlBookTitle">Book Title:</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="ddlBookTitle" name="ddlBookTitle">
                                        <?php
                                        $Render->GET_DDL($book, "");
                                        ?>
                                    </select>
                                    <input type="hidden" name="ddlBookID" id="ddlBookID" value=""/>
                                </div>
                            </div>
                            <!-- Radio Buttons -->
                            <div class="form-group row">
                                <!-- Page Type -->
                                <div class="col">
                                    <label class="col col-form-label">Page Type:</label>
                                    <div class="form-check col-sm-10">
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-check-input" name = "rbPageType" id = "rbPageType_tableContent" value="General Index" checked/>
                                            <label class="form-check-label" for="rbPageType_tableContent">General Index</label>
                                        </div>
                                        <div class="form-check form-check">
                                            <input type = "radio" class="form-check-input" name = "rbPageType" id = "rbIsMap_generalIndex" value="Table of Contents"/>
                                            <label class="form-check-label" for="rbIsMap_generalIndex">Table of Contents</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Needs Review -->
                                <div class="col d-flex justify-content-end">
                                    <!-- This div is used to trick the html into forcing the needs review to the right side of the card -->
                                    <div>
                                        <label class="col col-form-label">Needs Review:</label>
                                        <div class="form-check col-sm-10">
                                            <div class="form-check form-check">
                                                <input type = "radio" class="form-check-input" name = "rbNeedsReview" id = "rbNeedsReview_yes" value="1" checked/>
                                                <label class="form-check-label" for="rbNeedsReview_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check">
                                                <input type = "radio"  class="form-check-input" name = "rbNeedsReview" id = "rbNeedsReview_no" value="0" />
                                                <label class="form-check-label" for="rbNeedsReview_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Page Number -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="txtPageNumber">Page Number:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="txtPageNumber" id="txtPageNumber"/>
                                </div>
                            </div>
                            <!-- Comments -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="txtComments">Comments:</label>
                                <div class="col-sm-9">
                                    <textarea cols="35" class="form-control" rows="5" name="txtComments" id="txtComments"></textarea>
                                </div>
                            </div>
                            <!-- Buttons -->
                            <div class="form-group row">
                                <div class="col">
                                    <div class="d-flex justify-content-between">
                                        <span><input type="reset" id="btnReset" name="btnReset" value="Reset" class="btn btn-secondary"/></span>
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
        </div> <!-- Col-9 -->
    </div> <!-- row -->
</div><!-- Container -->
<!-- Doesn't matter where these go, this is for overlay effect and loader -->
<div id="loader"></div>
<div id="overlay"></div>
<div class="container-fluid animate-bottom" style="display: none;" id="info">
    <h1>Your submission was successful!</h1>
</div>
<?php include "../../Master/bandocat_footer.php" ?>


<!-- Complete JavaScript Bundle -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<!-- Our custom javascript file -->
<script type="text/javascript" src="../../Master/master.js"></script>
<script type="text/javascript" src="../../Master/errorHandling.js"></script>

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
    /**********************************************
     * Function: setBookID
     * Description: Populates the DDL with books
     * Parameter(s):
     * Return value(s):
     * $result (assoc array) -
     ***********************************************/
    function setBookID()
    {
        var books = <?php echo json_encode($book); ?>;
        var bookValue = $("#ddlBookTitle :selected").val();
        for(var i = 0; i < books.length; i++) {
            if (books[i][0] == bookValue)
            {
                $('#ddlBookID').val(books[i][1]);
                return;
            }
        }
    }

    selLibraryIndex = document.querySelector("#txtLibraryIndex");
    //Event listener that it is triggered when a document is loaded to the document//
    document.addEventListener("DOMContentLoaded", init, false);

    /**********************************************
     * Function: init
     * Description: responsible for initializing the handlefileselect function when the content is loaded
     * Parameter(s):
     * Return value(s):
     ***********************************************/
    function init()
    {
        document.querySelector('#fileUpload').addEventListener('change', handleFileSelect, false);

    }
    /**********************************************
     * Function: handleFileSelect
     * Description: handles the selcected files
     * Parameter(s):
     * e (in files) - selected files
     * Return value(s):
     ***********************************************/
    function handleFileSelect(e)
    {
        var books = <?php echo json_encode($book); ?>;
        console.log(books);
        if(!e.target.files) return;

        var files = e.target.files;
        var fileName = files[0].name.replace(".tif","");
        //Library index value is changed to the file name of the document uploaded
        selLibraryIndex.value = fileName;

        /*Program that conditionally selects the value of the Book title by splitting the filename with a underscore
         * delimiter and setting the value of the select option if equal to the prefix string value*/


        var fileNameSplit = fileName.split("_");
        //var fileNameSplit = fileName.replace(/_/g,"");
        // console.log("Split info: " + fileNameSplit);
        var fileNameNoSpace = "";
        for(var i = 0; i < fileNameSplit.length - 1; i++)
        {
            fileNameNoSpace += fileNameSplit[i].toLowerCase();
        }
        //  console.log("filename with space " + fileNameNoSpace.toLowerCase());
        var found = false;
        for(var i = 0; i < books.length;i++)
        {
            if(fileNameNoSpace == books[i][0].replace(/ /g, '').toLowerCase())
            {
                found = true;
                $('select.libraryIndexSelect').val(books[i][0]);
                $('#ddlBookID').val(books[i][1]);
                return;
            }else
            {
                found = false;

            }
        }
        if(found == false)
        {
            alert("Could not find book title match for library index.");
            console.log("Failed to match book");
        }


    }

    $( document ).ready(function()
    {
        //Eventlistener that on change the new value of the drop down is send to the #ddlBookID hidden input for update
        $('#ddlBookTitle').on('change', function(e)
        {
            setBookID();
        });


        /* attach a submit handler to the form */
        $('#theform').submit(function (event) {
            event.preventDefault();
            //console.log(document.getElementsByClassName('dd')))
            /* stop form from submitting normally */
            var formData = new FormData($(this)[0]);
            /*jquery that displays the three points loader*/
            if(validateFormUnderscore("txtLibraryIndex") == true)
            {
                document.getElementById("overlay").style.display = "block";
                document.getElementById("loader").style.display = "block";
                $('#loader').css("display", "inherit");

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
                            else if(json[i].includes("FAIL") || json[i].includes("EXISTED"))
                            {
                                alert("Everything worked out!");
                                document.getElementById("overlay").style.display = "none";
                                document.getElementById("loader").style.display = "none";
                            }
                        }
                        alert(msg);
                        if (result == 1){

                            window.location.href = "./catalog.php?col=<?php echo $_GET['col']; ?>";
                        }

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown)
                    {
                        alert("Status: " + textStatus); alert("Error: " + errorThrown);
                    }
                });
            }
            else
            {
                //No _ was found in the string
                alert("Library Index does not contain an underscore character. " +
                    "Please check Library Index.");
            }

        });

    });

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
</script>
</body>
</html>

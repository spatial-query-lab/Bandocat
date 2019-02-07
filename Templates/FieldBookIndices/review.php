<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(isset($_GET['col']) && isset($_GET['doc']))
{
    $collection = $_GET['col'];
    $docID = $_GET['doc'];
}

else header('Location: ../../');

require '../../Library/DBHelper.php';
require '../../Library/IndicesDBHelper.php';
require '../../Library/DateHelper.php';
require '../../Library/ControlsRender.php';

$Render = new ControlsRender();
$DB = new IndicesDBHelper();
//get appropriate db
$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
//select template indices document
$document = $DB->SP_TEMPLATE_INDICES_DOCUMENT_SELECT($collection, $docID);
//get the indices book
$book = $DB->GET_INDICES_BOOK($collection);
//get indices info
$info = $DB->GET_INDICES_INFO($collection, $docID);
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
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.js"></script>
    <script type="text/javascript" src="../../Master/master.js"></script>
</head>
<!-- END HTML HEADER -->
<!-- HTML BODY -->
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ;
            if($session->isAdmin()) //if user is Admin, render the Document History (Log Info)
                $Render->DISPLAY_LOG_INFO($DB->GET_LOG_INFO($collection, $docID));
            ?>
        </div>
        <div id="divright">
            <h2><?php echo $config['DisplayName'];?> Review Form</h2>
            <div id="divscroller">
                <table class="Account_Table">

                    <form id="theform" name="theform" method="post" enctype="multipart/form-data" >
                        <tr>
                            <td id="col1">
                                <div class="cell">
                                    <!-- LIBRARY INDEX -->
                                    <span class="label"><span style = "color:red;"> * </span>Library Index:</span>
                                    <input type = "text" name = "txtLibraryIndex" id = "txtLibraryIndex" size="26" value='' required />
                                </div>
                                <div class="cell">
                                    <!-- BOOK TITLE -->
                                    <span class="label"><span style = "color:red;"> * </span>Book Title:</span>
                                    <select id="ddlBookTitle" name="ddlBookTitle" class="selectBookTitle">
                                        <?php
                                        $Render->GET_DDL($book, $document['BookName']);
                                        ?>
                                    </select>
                                    <input type="hidden" name="ddlBookID" id="ddlBookID" value=""/>
                                </div>

                                <div class="cell" id="pageType">
                                    <!-- PAGE TYPE -->
                                    <span class="labelradio"><mark>Page Type:</mark><p hidden><b></b>This is to signal if it is a map</p></span>
                                    <input type = "radio" name = "rbPageType" id = "rbPageType_tableContent" size="26" value="General Index" checked="true"/>General Index
                                    <input type = "radio" name = "rbPageType" id = "rbIsMap_generalIndex" size="26" value="Table of Contents"/>Table of Contents
                                </div>
                                <div class="cell">
                                    <!-- PAGE NUMBER -->
                                    <span class="label"><span style = "color:red;"> * </span>Page Number:</span>
                                    <input type="text" name="txtPageNumber" id="txtPageNumber" value="<?php echo htmlspecialchars($document['PageNumber'],ENT_QUOTES);?>"/>
                                </div>
                                <div class="cell" >
                                    <!-- NEEDS REVIEW -->
                                    <span class="labelradio" ><mark>Needs Review:</mark><p hidden><b></b>This is to signal if a review is needed</p></span>
                                    <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_yes" size="26" value="1" checked="true"/>Yes
                                    <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_no" size="26" value="0" />No
                                </div>
                                <div class="cell">
                                    <!--COMMENTS -->
                                    <span class="label"><span style = "color:red;"> </span>Comments:</span>
                                    <textarea cols="35" rows="5" name="txtComments" id="txtComments" ><?php echo $document['Comments']?></textarea>
                                </div>
                            </td>
                            <td id="col2">
                                <!-- SCAN OF PAGE -->
                                <div class="cell">
                                    <table>
                                        <tr>
                                            <td>
                                    <span class="label">Scan of Page:</span>
                                            </td>
                                    <?php
                                    $indicesFolder = $Render->NAME_INDICES_FOLDER($document['BookName'], $book);

                                    echo "<td><a href=\"download.php?file=$config[StorageDir]$indicesFolder/$document[FileName].tif\">(Click to download)</a><br></td></tr>";
                                    echo "<tr><td></td><td><a id='download_front' href=\"download.php?file=$config[StorageDir]$indicesFolder/$document[FileName].tif\"><br><img src='" .  '../../' . $config['ThumbnailDir'] .$indicesFolder.'/'. $document['FileName'].'.jpg' . " ' alt = Error /></a>";
                                    echo "<br>Size: " . round(filesize($config['StorageDir'].$indicesFolder."/". $document['FileName'].'.tif')/1024/1024, 2) . " MB</td></tr>";
                                    ?>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <!-- Hidden inputs that are passed when the update button is hit -->
                                <div class="cell" style="text-align: center;padding-top:20px">
                                    <span><input type="reset" id="btnReset" name="btnReset" value="Reset" class="bluebtn"/></span>
                                    <input type = "hidden" id="txtDocID" name = "txtDocID" value = "<?php echo $docID; ?>" />
                                    <input type = "hidden" id="txtAction" name="txtAction" value="review" />  <!-- catalog or review -->
                                    <input type = "hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
                                    <span>
                                    <?php if($session->hasWritePermission())
                                    {echo "<input type='submit' id='btnSubmit' name='btnSubmit' value='Update' class='bluebtn'/>";}
                                    ?>
                                        <div class="bluebtn" id="loader" style="display: none;">
                                        Updating
                                        <img style="width: 4%;;" src='../../Images/loader.gif'/></div>
                                        </span>
                                </div>
                            </td>
                        </tr>
                    </form>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../../Master/footer.php'; ?>

</body>
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

    $( document ).ready(function()
    {
        //LIBRARY INDEX
        /*$document: array object that contains the selection from the bandocat_indicesinventory database, document table*/
        //Library index information retrieved from document array object
        $("#txtLibraryIndex").val("<?php echo $document['LibraryIndex']; ?>");
        setBookID();

        //Eventlistener that on change the new value of the drop down is send to the #ddlBookID hidden input for update
        $('#ddlBookTitle').on('change', function(e){
            setBookID();
        });



        //PAGE TYPE
        //Retrieve PageType from PHP fetched data to check the Page Type input radio button element
        var pageType = '<?php echo $document['PageType'];?>';
        var pageElement = document.getElementsByName('rbPageType');
        if(pageType == 'General Index' )
            pageElement[0].checked = true;
        if(pageType == 'Table of Contents' )
          pageElement[1].checked = true;

        //NEEDS REVIEW
        //Retrieved NeedsReview from PHP fetched data to check dynamically the Needs Review input radio element
        var needsReview = <?php echo $document['NeedsReview']?>;
        var reviewElement = document.getElementsByName('rbNeedsReview');
        if (needsReview == 1)
            reviewElement[0].checked = true;
        if (needsReview == 0)
            reviewElement[1].checked = true;


        //SUBMIT
        /* attach a submit handler to the form */
        $('#theform').submit(function (event) {
            /* stop form from submitting normally */
            var formData = new FormData($(this)[0]);
            /*jquery that displays the three points loader*/
            if(validateFormUnderscore("txtLibraryIndex") == true)
            {
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
                            self.close();
                        }

                    }
                });
            }
            else
            {
                //No _ was found in the string
                alert("Library Index does not contain an underscore character.                            " +
                    "Please check Library Index.");
            }

        });
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 55);
        $("#divleft").height($("#divscroller").height());
    });
</script>
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
</html>

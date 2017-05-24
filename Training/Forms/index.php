<?php
include '../../Library/SessionManager.php';
require('../../Library/DBHelper.php');
require '../../Library/ControlsRender.php';

$Render = new ControlsRender();
$session = new SessionManager();

$username = $_GET["user"];
$collection = $_GET['col'];
$type = $_GET['type'];

/*print_r($_GET['user']);
if (isset($_GET["user"])) {
    $userfile = str_replace(".xml", "", $_GET["user"]);
    if ($userfile == "")
        $userfile = $username;
} else $userfile = $username;*/

if ($type == 'newbie') {
    include 'newbieClass.php';
} elseif ($type == 'inter') {
    include 'interClass.php';
}

include 'config.php';
include 'main.php';
include 'saveTrainingData.php';

if (!isset($_GET["id"])) {
    header('Location: list.php');
}

$doc_id = $_GET["id"];
	// $progress_id = $_GET["id"];
	// $userid = $_SESSION["user_id"];		
	// $input_id = getUserIDInput($id);

	//$doc_id = 2;
$userType = $username . '_' . $type;
$XMLfile = XMLfilename($userType);

$file = simplexml_load_file('../Training_Collections/' . $collection . '/'.$username.'/'. $XMLfile) or die("Cannot open file!");

foreach ($file->document as $a) {
    if ($a->id == $doc_id) {
        if ($a["collection"] == $collection) {
            $doc1 = new JobFolder($collection,'../Training_Collections/' . $collection . '/'.$username.'/'. $XMLfile, $username, $doc_id);
            break;
        }
    }
}
//$_SESSION['currentId'] = $doc_id;

	  

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>[Training] Job Folder</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.js"></script>
    <script type="text/javascript" src="../../Master/master.js"></script>
</head>
<body>
	<!--<header>
		<div id="logo"><img id = "image" src = "../BlucherScanning/Logos/4.png" /></div>
		<div id="top-nav"><a href = "javascript:history.back()" id = "link">Go Back </a><a href = "../JobFolder" id = "link">Job Folder </a><a href = "../BlucherScanning/logoutFunctions.php" id = "link">Log Out</a></div>
	</header>-->
    <div id="wrap">
        <div id="main">
            <div id="divleft">
                <?php include '../../Master/header.php';
                include '../../Master/sidemenu.php' ?>
            </div>
        </div>


        <div id="divright">
            <h2> INPUT TRAINING SESSION </h2>
            <div id="divscroller">
                <!--<p id="field"> (*) required field <br><br> (Hover mouse on 'Needs Review' to know instruction) </p>-->
                <form id="theform" name="theform" enctype="multipart/form-data">
                    <table class="Account_Table">
                        <td id="col1">
                            <!-- LIBRARY INDEX -->
                            <div class="cell">
                                <span class="label"><span style = "color:red;"> * </span>Library Index:</span>
                                <input type = "text" name = "txtLibraryIndex" id = "txtLibraryIndex" size="26" value='<?php echo $doc1->libraryindex; ?>' required />
                            </div>
                            <div class="cell">
                                <!-- TITLE -->
                                <span class="label"><span style = "color:red;"> * </span>Document Title:</span>
                                <input type = "text" name = "txtTitle" id = "txtTitle" size="26" required="true" value='<?php echo $doc1->title; ?>' />
                            </div>


                            <div class="cell">
                                <!-- NEEDS REVIEW -->
                                <span class="labelradio" >
                                <mark>Needs Review:</mark>
                                <p hidden><b></b>This is to signal if a review is needed</p>
                                </span>
                                <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_yes" size="26" value="1" <?php if($doc1->needsreview == 1) echo "checked"; ?> />Yes
                                <input type = "radio" name = "rbNeedsReview" id = "rbNeedsReview_no" size="26" value="0" <?php if($doc1->needsreview == 0) echo "checked"; ?>  />No
                            </div>
                            <div class="cell">
                                <!-- SUB FOLDER -->
                                <span class="labelradio" >
                                <mark>In A Subfolder:</mark>
                                <p hidden><b></b>This document belongs in a subfolder</p>
                                </span>
                                <input type = "radio" name = "rbInASubfolder" id = "rbInASubfolder_yes" size="26" value="1" <?php if($doc1->inasubfolder == 1) echo "checked"; ?> />Yes
                                <input type = "radio" name = "rbInASubfolder" id = "rbInASubfolder_no" size="26" value="0" <?php if($doc1->inasubfolder == 0) echo "checked"; ?> />No
                            </div>
                            <div class="cell">
                                <!-- SUBFOLDER COMMENTS -->
                                <span class="label">Subfolder Comments:</span>
                                <textarea cols = "35" name="txtSubfolderComments" id="txtSubfolderComments"/><?php echo $doc1->subfoldercomments; ?></textarea>
                            </div>
                            <div class="cell">
                                <!-- CLASSIFICATION -->
                                <span class="label">Classification:</span>
                                <select id="ddlClassification" name="ddlClassification" style="width:215px">
                                    <?php
                                    classification($classification_arr, $doc1->classification);
                                    ?>
                                </select>
                            </div>
                            <div class="cell">
                                <!-- CLASSIFICATION COMMENTS-->
                                <span class="label">Classification Comments:</span>
                                <textarea rows = "2" cols = "35" id="txtClassificationComments" name="txtClassificationComments"/><?php echo $doc1->classification; ?></textarea>
                            </div>
                            <div class="cell">
                                <!-- GET START DDL MONTH -->
                                <select name="ddlStartMonth" id="ddlStartMonth" style="width:60px">
                                    <?php $Render->GET_DDL_MONTH($doc1->startmonth); ?>
                                </select>
                                <span class="label">Document Start Date:</span>
                                <!-- GET START DDL DAY -->
                                <select name="ddlStartDay" id="ddlStartDay" style="width:60px">
                                    <?php $Render->GET_DDL_DAY($doc1->startday); ?>
                                </select>
                                <!-- GET START DDL YEAR -->
                                <select id="ddlStartYear" name="ddlStartYear" style="width:85px">
                                    <?php $Render->GET_DDL_YEAR($doc1->startyear); ?>
                                </select>

                            </div>
                            <div class="cell">
                                <!-- GET END DDL MONTH -->
                                <select name="ddlEndMonth" id="ddlEndMonth" style="width:60px">
                                    <?php $Render->GET_DDL_MONTH($doc1->endmonth); ?>
                                </select>
                                <span class="label">Document End Date:</span>
                                <!-- GET END DDL DAY -->
                                <select name="ddlEndDay" id="ddlEndDay" style="width:60px">
                                    <?php $Render->GET_DDL_DAY($doc1->endday); ?>
                                </select>
                                <!-- GET END DDL YEAR -->
                                <select name="ddlEndYear" id="ddlEndYear" style="width:85px">
                                    <?php $Render->GET_DDL_YEAR($doc1->endyear); ?>
                                </select>
                            </div>
                            <div class="cell">
                                <!-- DOCUMENT AUTHOR -->
                                <span class="label">Document Author:</span>
                                <input type="text" id="txtAuthor" name="txtAuthor[]" size="26" list="lstAuthor" value="<?php if(count($doc1->author1) > 0)echo $doc1->author1 ?>"/><span style="padding-right:5px"></span><input type="button" id="more_fields" onclick="add_fields(null);" value="+"/>
                                <span id="authorcell"></span>
                            </div>
                        </td>

                        <td id="col2" style="padding-left:40px">
                            <div class="cell">
                                <span class="label">Comments:</span>
                                <!-- COMMENTS-->
                                <textarea rows = "4" cols = "35" id="txtComments" name="txtComments"/><?php echo $doc1->comments; ?></textarea>
                                <br><br><br>
                            </div>
                        </td>
                        <div class="cell">
                            <table>
                                <tr>
                                    <td style="text-align: center">
                                        <!--SCAN OF FRONT-->
                                        <span class="label" style="text-align: center">Scan of Front</span><br>
                                        <?php
                                        echo "<a id='download_front' href=\"download.php?file=$doc1->frontimage\"><br><img src='". $doc1->frontthumbnail . " ' alt = Error /></a>";
                                        echo "<br>Size: " . round(filesize($doc1->frontimage)/1024/1024, 2) . " MB";
                                        echo "<br><a href=\"download.php?file=$doc1->frontimage\">(Click to download)</a>";
                                        ?>
                                    </td>
                                    <td style="padding-right:20px"></td>
                                    <td style="text-align: center">
                                        <!--SCAN OF BACK-->
                                        <?php
                                        if($doc1->backimage != '../Training_Newbie_Images/Images/') //has Back Scan
                                        {

                                            echo '<span class="label" style="text-align: center">Scan of Back</span><br>';
                                            echo "<a id='download_front' href=\"download.php?file=$doc1->backimage\"><br><img src='". $doc1->backthumbnail . " ' alt = Error /></a>";
                                            echo "<br>Size: " . round(filesize($doc1->backimage) / 1024 / 1024, 2) . " MB";
                                            echo "<br><a href=\"download.php?file=$doc1->backimage\">(Click to download)</a>";
                                        }
                                        else
                                        {
                                            echo '<span class="label" style="text-align: center">No Scan of Back</span><br>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <tr>
                            <td colspan="2">
                                <div class="cell" style="text-align: center;padding-top:20px">
                                    <!-- Hidden inputs that are passed when the update button is hit -->
                                    <span><input type="reset" id="btnReset" name="btnReset" value="Reset" class="bluebtn"/></span>
                                    <input type = "hidden" id="txtDocID" name = "txtDocID" value = "<?php echo $docID;?>" />
                                    <input type = "hidden" id="txtAction" name="txtAction" value="catalog" />  <!-- catalog or review -->
                                    <input type = "hidden" id="txtCollection" name="txtCollection" value="<?php echo $collection; ?>" />
                                    <span>
                                        <?php if($session->hasWritePermission()){
                                            echo "<input type='submit' id='btnSubmit' name='btnSubmit' value='Update' class='bluebtn'/>";
                                        }
                                        ?>
                                        <div class="bluebtn" id="loader" style="display: none">Updating
                                            <img style="width:4%" src='../../Images/loader.gif'/>
                                        </div>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>


	<!--<div id="drag_classification" class="ui-widget-content">-->
	<span id="title_class_desc">Classification Description</span>
	<form id="class_form" name="class_form" method="post">
		 <select id="ddl_class_desc" name="ddl_class_desc">
		 <?php
		 	for($i = 0; $i < count($classification_arr);$i++)
		 	{
		 		echo "<option value='" . $classification_arr[$i] . "'>" . $classification_arr[$i] . "</option>";
		 	}
		 ?>
		 </select>
		 <p id="txt_class_desc"></p>
		</div>
	</form>
	<div class = "navbar center">
	</div>
</body>


<?php


	if (isset($_POST['submit'])) 
	{
		$_SESSION[$userfile] = $doc_id;
	  	writeXMLtag($doc_id, "title", $_POST['txtTitle'], $userfile);
	  	writeXMLtag($doc_id, "needsreview", $_POST['rbNeedsReview'], $userfile);
	  	writeXMLtag($doc_id, "inasubfolder", $_POST['rbInASubfolder'], $userfile);
	  	writeXMLtag($doc_id, "author1", $_POST['author1'], $userfile);
	  	writeXMLtag($doc_id, "author2", $_POST['author2'], $userfile);
	  	writeXMLtag($doc_id, "author3", $_POST['author3'], $userfile);
	  	writeXMLtag($doc_id, "subfoldercomments", $_POST['txtSubfolderComments'], $userfile);
	  	writeXMLtag($doc_id, "classification", $_POST['ddlClassification'], $userfile);
	  	writeXMLtag($doc_id, "classificationcomments", $_POST['txtClassificationComments'], $userfile);
	  	writeXMLtag($doc_id, "comments", $_POST['comment'], $userfile);
	  	writeXMLtag($doc_id, "startmonth", $_POST['ddlStartMonth'], $userfile);
	  	writeXMLtag($doc_id, "startday", $_POST['ddlStartDay'], $userfile);
	  	writeXMLtag($doc_id, "startyear", $_POST['ddlStartYear'], $userfile);
	  	writeXMLtag($doc_id, "endmonth", $_POST['ddlEndMonth'], $userfile);
	  	writeXMLtag($doc_id, "endday", $_POST['ddlEndDay'], $userfile);
	  	writeXMLtag($doc_id, "endyear", $_POST['ddlEndYear'], $userfile);
	  	echo "<script>window.location = 'mapEditingProcess.php'</script>";

	}
 
?>
	  

<script>
	  $(function() {
	    $( "#drag_classification" ).draggable();
	    $( "#drag_classification" ).resizable();
  	});
	  var ar_class = <?php echo json_encode($classification_arr); ?>;
	 var ar_class_desc = <?php echo json_encode($classification_desc); ?>;
	  $("#ddl_class_desc").change(function()
	  {
	  	var ddl_value = document.getElementById("ddl_class_desc").value;
	  	
	  	if (ddl_value == "none")
	  		document.getElementById("txt_class_desc").innerHTML = "";
	  	else
	  	{ 
	  		for (var i = 0; i < ar_class_desc.length; i++)
	  		{
	  			if(ddl_value == ar_class[i])
	  				document.getElementById("txt_class_desc").innerHTML = ar_class_desc[i];
	  		}
	  	}
	  });

</script>
</body>
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
        margin-bottom: 4%;

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
    #row{float:bottom;width:2000px;height:52px;background-color: #ccf5ff;}

    .cell
    {
        min-height: 52px;
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
        -webkit-box-shadow: 4px 4px 4px #36c476;
        -moz-box-shadow: 4px 4px 4px #36c476;
        box-shadow: 4px 4px 4px #36c476;
        width: 200px;
        padding: 10px 10px;
    }

</style>
</html>
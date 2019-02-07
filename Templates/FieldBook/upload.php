<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
//get collection name from passed variable col
if(isset($_GET['col']))
{
    //get collection name passed in from side menu
    $collection = $_GET['col'];
    require '../../Library/DBHelper.php';
    require '../../Library/FieldBookDBHelper.php';
    $DB = new FieldBookDBHelper();
    //get appropriate DB
    $config = $DB->SP_GET_COLLECTION_CONFIG($collection);
}
else header('Location: ../../');
?>
<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- The title of the page -->
    <title><?php echo $config['DisplayName']; ?> Document Upload</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>

</head>
<!-- HTML BODY -->
<body>
<div id="wrap"></div>
<div id="main"></div>
<!-- Draw the header and Side Menu -->
<div id = "divleft">
    <?php include '../../Master/header.php';
    include '../../Master/sidemenu.php' ?>
</div>
<div id="divright">
    <!-- Title Displayed in Green style in master.css -->
    <h2><?php echo $config['DisplayName']; ?> Document Upload</h2>
	<div id ="divscroller">
    <table class="Collection_Table">
        <form id="frmUpload" name="frmUpload" method="post" enctype="multipart/form-data">
        <tr>
            <td class="Collection_data" style="height:50px">
                <!-- Upload Documents Button, Php code sends the collection name to upload.php -->
                <input type="file" name="file_array[]" id="file_array" class="bluebtn" accept=".tif" value="Input Map Information" multiple/>
            </td>
        </tr>
        <tr>
            <!-- table for displaying selected files -->
            <td class="Collection_data" >
                <div id="selectedFilesDiv">
                    <table >
                        <thead><span style="font-family: Algerian; font: message-box;">Selected Files</span>
                        <tr>
                            <th>File Name</th>
                            <th>File Size</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody id="selectedFilesTable">
                        <tr><td>No files selected</td></tr>
                        </tbody>
                        <tfoot id="selectedFilesTableFooter" style="background: #007F3E; color: white;"></tfoot>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td class="Collection_data">
                <input type="submit" class="bluebtn" value="Upload" id="btnUpload"/>
            </td>
        </tr>
        <tr>
            <td>
                <p style="color:red; font-size: .45em"><br>*Recommended number of files for uploading: 100 files<br> </p>
            </td>
        </tr>
        </form>
    </table>
	</div>
</div>


<script>
    var totalFsize = 0;

    // listener for when the document is loaded
    document.addEventListener("DOMContentLoaded", init, false);
    /**********************************************
     * Function: init
     * Description: responsible for initializing the handlefileselect function when the content is loaded
     * Parameter(s):
     * Return value(s):
     ***********************************************/
    function init()
    {
        //add listener to the choose files button and attach handlefileselect to the listener
        document.querySelector('#file_array').addEventListener('change', handleFileSelect, false);
        selTable = document.querySelector("#selectedFilesTable");
        selTableFooter = document.querySelector("#selectedFilesTableFooter");
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
        var total = 0;
        totalFsize = 0;
        if(!e.target.files) return;

        selTable.innerHTML = "";

        var files = e.target.files;
        var filenames = [];
        for(var i=0; i<files.length; i++)
        {
            var f = files[i];
            filenames.push(f.name);
            totalFsize += f.size/1000000;
            total = totalFsize.toFixed(2);
            var row = selTable.insertRow(i);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            cell1.innerHTML =  f.name;
            cell2.innerHTML =  (f.size/1000000).toFixed(2) + " MB";
            cell3.id = f.name;
            cell3.innerHTML = "Validating...";
        }
        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 55);
        $.ajax({
            //Checks if the filenames already exist in the DB
            url: 'upload_validating.php?col=<?php echo $collection; ?>',
            type: 'POST',
            data: {fileNames : filenames},
            success: function (data)
            {
                data = JSON.parse(data);
                for(var i = 0; i < data.length; i++)
                {
                    //if not found
                    if(data[i] == 0)
                    {
                        document.getElementById(filenames[i]).innerHTML = "Ready";
                        document.getElementById(filenames[i]).style.color = "green";
                    }
                    else
                    {
                        document.getElementById(filenames[i]).style.color = "red";
                        if (data[i] == 1)
                            document.getElementById(filenames[i]).innerHTML = "Existed";
                        else document.getElementById(filenames[i]).innerHTML = "Error";

                    }
                }
            },

        });

        //add rows and cells to table displaying file names
        var tableFooterLength = selTableFooter.rows.length;
        var row = selTableFooter.insertRow(0);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        cell1.innerHTML =  "Total number of files: " + files.length;
        cell2.innerHTML += "Total file size upload: " + total+" MB";

        if (tableFooterLength > 0){
            selTableFooter.deleteRow(1);
        }
    }
    //frmUpload is the form that holds the btn submit
    $("#frmUpload").submit(function(event)
    {
        //Change button to uploading, then disable it
        $("#btnUpload").val("Uploading...");
        $("#btnUpload").attr("disabled","true");
        event.preventDefault();
        var data = new FormData();
        //Javascript FormData sent to upload_processing via ajax
        jQuery.each(jQuery('#file_array')[0].files, function(i, file)
        {
            data.append('file:'+i, file);
        });
        $.ajax({
            url: 'upload_processing.php?col=<?php echo $collection; ?>',
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data)
            {
                data = JSON.parse(data);
                for(var i = 0; i < data.length; i++)
                {

                    document.getElementById(data[i][0]).innerHTML = data[i][1];
                    if(data[i][1] == "Uploaded")
                        document.getElementById(data[i][0]).style.color = "green";
                    else document.getElementById(data[i][0]).style.color = "red";
                }
                $("#btnUpload").val("Upload");
                $("#btnUpload").attr("disabled","false");
                alert("Upload completed!");
            },
        });

    });

</script>

<style>
    nav{margin-left: 8px;
        margin-top: 22px;}
    ul{
        text-align: left;
        margin:auto;
    }
    #selectedFilesDiv table { border-collapse: collapse; text-align: start; width: 100%;padding-left:2px; }
    #selectedFilesDiv {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #006699; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; }
    #selectedFilesDiv table td, #documentHistory table th { padding: 3px 10px; }
    #selectedFilesDiv table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #1b77cb), color-stop(1, #125490) );background:-moz-linear-gradient( center top, #006699 5%, #00557F 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#006699', endColorstr='#00557F');background-color:#006699; color:#FFFFFF; font-size: 13px; font-weight: bold; border-left: 1px solid #0070A8; }
    #selectedFilesDiv table tbody td { color: #001326; border-left: 1px solid #E1EEF4;font-size: 11.56px;font-weight: normal; width: 50%}
    #selectedFilesDiv table tbody tr:hover { background-color: #bce1ff; }
    div#dhtmlx_window_active, div#dhx_modal_cover_dv { position: fixed !important; }

    #file_array{
        height:90%;
        padding: 1%;
        overflow: auto;
    }
    pre{
        tab-size: 4;
    }
    #btnUpload{
        height:100%;padding-bottom:10px;}
    .bluebtn{
        font-size: 0.4em !important;
        padding-bottom: 24px;
    }

    #divright{
        font-size:45% !important;
        vertical-align: top;
        background: #f1f1f1;
        border-radius: 2%;
        box-shadow: 0px 0px 2px;
    }
    .Collection_Table .Collection_Button{
        font-size: 45% !important;
    }


</style>
<?php include '../../Master/footer.php'; ?>

</body>
</html>

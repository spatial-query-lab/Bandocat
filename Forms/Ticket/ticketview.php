<?php
//for admin to view ticket and update ticket status
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(isset($_GET['id'])) {
    $tID = $_GET['id']; //ticket ID
    require('../../Library/DBHelper.php');
    $DB = new DBHelper();
}
else header('Location: ../../');

$ticket = $DB->SP_ADMIN_TICKET_SELECT($tID); //assoc array contains ticket info
//var_dump($ticket); //uncomment this to display the array


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Ticket view</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>

</head>
<body>
<div id="wrap">
    <div id="main">
        <div class="menu_left" id="thetable_left" style="padding: 0px;float:left; width: 20%; overflow-y: auto; overflow-x: hidden">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ?>
        </div>
    </div>
</div>

<div class="Collection" id="thetable_right" style="float: left; width: 79%; overflow-x: hidden">
    <h2>Ticket View</h2>
    <div id="divscroller" style="overflow-x: hidden;overflow-y: auto; padding-right: 2px">
        <form id="frmTicket" name="frmTicket">
            <table class="Collection_Table" style="width: 95%; font-size: 15px; padding-top: 0%; margin-bottom: 2%; padding-bottom: 1%; margin-top: 4%; margin-bottom: 2%; overflow: auto;">
                <tr>
                    <td>
                        <div id="Left_Display" style="text-align: left">
                            <h3>Collection Name: <span id="Collection_Name"></span></h3>
                            <div id="libraryIndex">
                                <h3>Library Index/Subject:
                                    <span class="Subject" id="Subject0"></span>
                                </h3>
                                <table id="libraryIndexList" style="margin-left: 5%">
                                    <tr id="libraryIndexRow0"></tr>
                                </table>
                            </div>
                            <h3>Description: <span id="Description"></span></h3>
                            <h3>Status:
                                <input type="radio" value="0" name="Status"><span>Open</span>
                                <input type="radio" value="1" name="Status"><span>Closed</span>
                            </h3>
                            <h3>Notes:</h3>
                            <textarea rows="8" cols="75" id="Notes" name="txtNotes"></textarea>
                        </div>
                    </td>
                    <td>
                        <div id="Rigth_Display" style="text-align: left; padding-left: 35%; padding-bottom: 35%">
                            <h3>Submitter: <span id="Submitter"></span></h3>
                            <h3>Previously Solved by: <span id="Previously_Solvedby"></span></h3>
                        </div>
                    </td>


                </tr>
                <tr>
                    <td class="Collection_data">

                    </td>
                </tr>
                <tr>
                    <td class="Collection_data">

                    </td>
                </tr>
                <tr>
                    <td>
                        <input class="bluebtn" type="submit" name="btnSubmit" id="btnSubmit"/>
                    </td>

                </tr>
                <tr>
            </table>
        </form>
    </div>
</div>

            <?php include '../../Master/footer.php'; ?>

</body>

<script>
    //Window Height
    var windowHeight = window.innerHeight;
    $('#divscroller').height(windowHeight - (windowHeight * 0.2));

    $(window).resize(function (event) {
        windowHeight = event.target.innerHeight;
        $('#divscroller').height(windowHeight - (windowHeight * 0.2));
    });

    $( document ).ready(function() {
        //Variable that stores in a json the information of the ticket retrieved from the database.
        var data = <?php echo json_encode($ticket); ?>;
        //Series of document elements in which the data from the ticket is saved into their inner text.
        document.getElementById("Collection_Name").innerText = data.Collection;
        //JSON with the library index information
        var libIdxJSON = JSON.parse(data.LibraryIndex);
        //Switch statement that selects the collection name and file name to be used to link the ticket with its document
        switch(data.Collection) {
            case 'Blucher Maps':
                var dbCol = 'bluchermaps';
                var file = 'Map';
                break;
            case 'Green Maps':
                var dbCol = 'greenmaps';
                var file = 'Map';
                break;
            case 'Job Folder':
                var dbCol = 'jobfolder';
                var file = 'Folder';
                break;
            case 'Blucher Field Book':
                var dbCol = 'blucherfieldbook';
                var file = 'FieldBook';
                break;
            case 'Map Indices':
                var dbCol = 'mapindices';
                var file = 'Indices';
                break;
        }

        //Object that will be posted to ticketLink.php is initialized
        var ticketData = {};
        //Object property data is initialized as an array
        ticketData['data'] = [];
        //For each element from the JSON with the library index information by index and by element
        $.each(libIdxJSON, function (index, obj) {
            var libraryIndex = obj;
            /*Data pushed to the data object to be posted to ticketLink; library index collection, and
            library index name*/
            ticketData['data'].push({"subjectCol": dbCol, "subject": libraryIndex});
        });

        $.ajax({
            url: 'ticketLink.php',
            type: 'post',
            data: ticketData,
            /*If the function was executed correctly then it will return the document id and library index in a data
             object: RETURNED Object FORMAT: {"data":[[Document Id, Library Index],...[]]}*/
            success: function (libData) {
                var libInfo = JSON.parse(libData);
                $.each(libInfo, function (data, info) {
                    for(var ticket = 0; ticket < info.length; ticket++){
                        //Document ID
                        documentID = info[ticket][0];
                        //Library Index
                        documentLibIndex = info[ticket][1];

                        /*LINK*/
                        //If the document id was fetched from database
                        if(documentID !== false){
                            if(ticket > 0){
                                $('#libraryIndexList tr:last').html("<a href='../../Templates/" + file + "/review.php?doc=" + documentID + "&col=" + dbCol + "' target='_blank' >"+ documentLibIndex +"</a>");
                            }
                            else
                                $('#libraryIndexRow' + ticket).html("<a href='../../Templates/" + file + "/review.php?doc=" + documentID + "&col=" + dbCol + "' target='_blank' >"+ documentLibIndex +"</a>");
                            //Inserts a row to the last after the last row element in the table
                            var tc = ticket + 1;
                            $('#libraryIndexList tr:last').after('<tr id="libraryIndexRow"' + tc + '></tr>');
                        }

                        /*NO LINK*/
                        else{
                            if(ticket > 0){
                                $('#libraryIndexList tr:last').html(documentLibIndex);
                            }
                            else
                                $('#libraryIndexRow' + ticket).html(documentLibIndex);
                            //Inserts a row to the last after the last row element in the table
                            var tc = ticket + 1;
                            $('#libraryIndexList tr:last').after('<tr id="libraryIndexRow"' + tc + '></tr>');
                        }
                    }
                });
            }
        });

        //document.getElementById("Subject").innerText = data.LibraryIndex;
        document.getElementById("Description").innerText = data.Description;

        /*Input tags compared conditionally with the status data, from the ticket, to determine if it should be
         checked or not.*/
        if (document.getElementsByTagName("input")[0].value == "0") {
            if (data.Status == 0) {
                document.getElementsByTagName("input")[0].checked = true;
            }
        }

        if (document.getElementsByTagName("input")[1].value == "1") {
            if (data.Status == 1) {
                document.getElementsByTagName("input")[1].checked = true;
            }
        }

        document.getElementById("Notes").innerText = data.Notes;
        document.getElementById("Submitter").innerText = data.Submitter;
        document.getElementById("Previously_Solvedby").innerText = data.Solver;

        $("#btnSubmit").click(function (event) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: "./ticketview_processing.php?id=" + "<?php echo $tID;?>",
                data: $("#frmTicket").serializeArray(),
                success: function (data) {
                    //generate total chart
                    alert(data);
                }
        });
        });
    });

</script>

<style type="text/css">
    .Error_Input{margin-left: 10%; margin-top: 0%; background-color: #f1f1f1; border-radius: 10px; border-width: 0px; box-shadow: 0px 0px 2px #0c0c0c; padding-left: 8%; margin-right: 10%; padding-bottom: 5%; padding-top: 2.5%;}
    nav{margin: -1px 0px 40px 15px !important;}
    #thetable_left{padding-top: 8px}
    #thetable td{padding-top: 11px; padding-left: 1px}
    #Left_Display span{font-size: 14px; font-family: "Times New Roman"; font-style: italic;}
    #Rigth_Display span{font-size: 14px; font-family: "Times New Roman"; font-style: italic;}
</style>

</html>
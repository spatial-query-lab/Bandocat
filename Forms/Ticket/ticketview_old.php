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
<table id = "thetable">
    <tr>
        <td class="menu_left" id="thetable_left">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ?>
        </td>
        <td class="Collection" id="thetable_right">
            <h2>Ticket View</h2>
            <form id="frmTicket" name="frmTicket">
            <table class="Collection_Table" style="width: 95%; font-size: 15px; padding-top: 0%; margin-bottom: 2%; padding-bottom: 1%; margin-top: 4%; margin-bottom: 2%; overflow: auto;">
                <tr>
                    <td>
                        <div id="Left_Display" style="text-align: left">
                            <h3>Collection Name: <span id="Collection_Name"></span></h3>
                            <h3>Library Index/Subject: <span id="Subject"></span></h3>
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

            <?php include '../../Master/footer.php'; ?>

</body>

<script>
    $( document ).ready(function() {
        //Variable that stores in a json the information of the ticket retrieved from the database.
        var data = <?php echo json_encode($ticket); ?>;
        //Series of document elements in which the data from the ticket is saved into their inner text.
        document.getElementById("Collection_Name").innerText = data.Collection;
        document.getElementById("Subject").innerText = data.Subject;
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
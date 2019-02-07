<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(!$session->isAdmin()) {
    header('Location: ../../');
}
include '../../Library/DBHelper.php';
include '../../Library/IndicesDBHelper.php';
$DB = new DBHelper();
$DB1 = new IndicesDBHelper();
$collection='mapindices';
$arr = $DB1->GET_MAPKIND_TABLE($collection);

//Get Array for validation
$ola = $DB1->GET_INDICES_MAPKIND($collection);
$split = json_encode($ola);
$UpperArray = strtoupper($split);
echo $UpperArray;
//echo json_encode($arr);
include '../../Library/ControlsRender.php';
$Render = new ControlsRender();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Manage Map Kind</title>

    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.js"></script>
    <style>
        #frm_user{
            height:300px;
            width:600px;
            min-width:500px;
            margin:0 auto;
        }
        #frm_user table{margin-top:20px;}
        #ddl_user{line-height:40px;}
        label{font-size:1.2em;}
        select{height:30px;}
    </style>
    <style>
        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color:  #fefefe; /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content/Box */
        .modal-content {

            background-color: #fefefe;
            margin: margin: auto;
            float: left;
            padding: 20px;
            border: 1px solid #888;
            width: 30%; /* Could be more or less, depending on screen size */
            text-align: center;
        }
        label{font-size:1.2em;}
        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php';?>
        </div>

        <div id="divright">
            <h2 id="page_title">Manage Map Kind</h2>
                    <!-- Trigger/Open The Modal -->
                    <button id="myBtn">+</button>
            </div>
            <!-- Modal content -->
            <div id="myModal" class="modal">
            <div class="modal-content">
                <button id="closeBtn" style="float: right;">X</button>
                <div id="divscroller">
                    <form id="frm_mapkind" name="frm_mapkind" method="post">
                                <label>Existing Map Kinds:</label><br><br>
                                <select name="ddl_user" id="ddl_user" multiple style="height: 250px; width: 250px">
                                    <?php $Render->GET_DDL_MAPKIND($arr,null);
                                    //echo $arr?>
                                </select><br/>

                                <label>Input New Map Kind:</label><br><br>
                        <input type="text" required name="txt" id="txt"; return false;"><br>
                                <div style="text-align:center"><p style="font-size:1.2em;" hidden id="txtResult"></div>

                            <br>
                            <input type="submit" value="Create" id="btnNewMapkind" class="bluebtn" name="btnNewMapkind"/>

                        <br>
                    </form>


                    </div>

            </div>

            <?php include '../../Master/footer.php'; ?>
</body>
<script>
    $("#btnNewMapkind").click(function(event){
        $("#txtPrompt").hide();
        event.preventDefault();
        var bla = $('#mapkind').val(); //Debug
        $('#mapkind').val(bla) //Debug
        //alert(bla); //Debug
        $.ajax({
            type: 'post',
            url: "managemapkind_processing.php",
            data: $("#frm_mapkind").serializeArray()
        }).success(function (data) {
            $("#txtResult").show().text(data);
        });
    });
</script>
<script>
    $("input").keyup(function(){
        var bla = $('#txt').val(); //Debug
        $('#txt').val(bla)
        //alert(bla);
        $.ajax({
            type: 'post',
            url: "managemapkind_processing.php",
            data: $("#frm_mapkind").serializeArray()
        }).success(function (data) {
            console.log(data);
            $("#btnNewMapkind").hide();
            $("#txtResult").show().text(data);
            /*
            if(data == 1) {
                $("input").css("background-color", "red");
                $("#btnNewMapkind").hide();
            }
            if(data == 0) {
                $("input").css("background-color", "green");
            } */
        });
    });
</script>
<script>
    /*
    var ola = '';

    function checkInp() {
        var arr = [["Son"],["John"],["Juan"]];
        var entry1 = document.getElementById('txtRepeatPassword');
        var goodColor = "#66cc66";
        var badColor = "red";
        //Iterate Through Array
        var k = arr.indexOf(entry1.value);
        console.log(k);
        if (k >= 0) {
            entry1.style.backgroundColor = badColor;
            document.getElementById('btnNewMapkind').style.display = 'none';
            return true;
        } else {
            entry1.style.backgroundColor = "";
            return false;
        }
    }
    */
</script>
<script>
    // Get the modal
    var modal = document.getElementById('myModal');

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal
    var closebtn = document.getElementById("closeBtn");

    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    closebtn.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

</script>
<style>
    /*Account Stylesheet Adaptation from Collection Name */
    .Account{
        border-radius: 2%;
        box-shadow: 0px 0px 4px;
    }

    .Account_Table{
        background-color: #e8eaed;
        padding: 3%;
        border-radius: 6%;
        box-shadow: 0px 0px 2px;
        margin: auto;
        font-family: verdana;
        vertical-align: middle;
        margin-top: 4%;
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

</style>
</html>

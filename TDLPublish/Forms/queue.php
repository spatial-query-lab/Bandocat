<?php
//for admin use only

//Include all the classes from the Library directory
spl_autoload_register(function ($class_name) {
    require_once "../../Library/" . $class_name . '.php';});
$session = new SessionManager();

//for admin only
if($session->isAdmin()) {
    $DB = new DBHelper();
}
else header('Location: ../../');
$Render = new ControlsRender();

//DESCRIPTION: THIS PAGE DISPLAYS QUEUE OF TDLPUBLISH LIST
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>TDL Publishing Queue</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.js"></script>
</head>
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php';?>
        </div>
        <div id="divright">
            <h2>TDL Publishing Queue</h2>
            <table width="100%">
                <tr>
                        <td>
                        <!-- Form responsible for the select drop down menu -->
                        <form id = "form" name="form" method="post">
                            Select Collection:
                            <select name="ddlCollection" id="ddlCollection">
                                <!-- Renders the Dropdownlist with the collections -->
                                <?php $Render->GET_DDL_COLLECTION($DB->GET_COLLECTION_FOR_DROPDOWN_FROM_TEMPLATEID(array(4),false),"bluchermaps");?>
                            </select>
                        </form>
                        </td>
                </tr>
                <tr><td>
                    <button onclick="startBackgroundWorker()" id="btnStartWorker">Start Background Worker (Testing only)</button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <br/>
                    <button id="btnPush" name="btnPush" onclick="pushQueue();">Push</button>
                        <select id="ddlHowMany" name="ddlHowMany">
                            <option value="1">1</option>
                            <option value="20">20</option>
                            <option value="40">40</option>
                            <option value="60">60</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="">All</option>
                        </select>
                        <span style="min-width:20px"></span><button id="btnReset" name="btnReset" onclick="resetQueue();" >Reset Queue</button>
                    </td>
                </tr>
            </table>
            <div id="divscroller">
                <div id="divLoader">
                    <table id="tblQueue">
                    </table>
                </div>
            </div>
            <div id="divLog">
            </div>
        </div>
    </div>
</div>
<!--End of new user input form-->

<?php include '../../Master/footer.php'; ?>
</body>


<script>
    //testing only
    function startBackgroundWorker()
    {
        $.ajax({
            type: "POST",
            url: "queue_startworker_processing.php",
            data: {},
            success: function (data) {
            }
        });
    }

    //convert the DspacePublished status code into String
    function statusConverter(iStatus)
    {
        switch(iStatus)
        {
            case "2": return "In Queue";
            case "10": //publishing front
                return "Publishing";
            case "11": //publishing back
                return "Continue Publishing";
            default: return iStatus.toString();
        }
    }

    function loadQueue()
    {
        $.ajax({
            type: "POST",
            url: "queue_processing.php?action=load",
            data: {ddlCollection: $("#ddlCollection").val()},
            success: function (data) {
                var temp;
                data = JSON.parse(data);
                for(var i = 0; i < data.length; i++)
                    temp += "<tr><td>" + parseInt(i + 1) +"</td><td>" + data[i].documentID + "</td><td>" + data[i].libraryindex + "</td><td>" + statusConverter(data[i].dspacePublished)  +"</td></tr>";
                $("#tblQueue").html("<thead><td>#<td>ID</td><td>Library Index</td><td>Status</td></thead>" + temp);
            }
        });
    }

    function resetQueue()
    {
        $.ajax({
            type: "POST",
            url: "queue_processing.php?action=reset",
            data: {ddlCollection: $("#ddlCollection").val()},
            success: function (data) {
                loadQueue();
            }
        });
    }

    function pushQueue()
    {
        $.ajax({
            type: "POST",
            url: "queue_processing.php?action=push",
            data: {ddlCollection: $("#ddlCollection").val(), howMany: $("#ddlHowMany :selected").val()},
            success: function (data) {
                loadQueue();
            }
        });
    }
    //read log file and parse it to HTML #divLog
    function displayLog()
    {
        $.ajax({
            type: "POST",
            url: "queue_processing.php?action=displaylog",
            data: {ddlCollection: $("#ddlCollection").val()},
            success: function (data) {
                $("#divLog").html("<h2>Log</h2>" + data);
                $("#divLog").scrollTop($("#divLog")[0].scrollHeight); //scroll to bottom
            }
        });
    }

    /*Submit event that obtains teh information from the user form and calls the newuser_processing.php page, which links to
     a procedure in the database that insert the information into the bandocatdb database in the user table .*/
    $(document).ready(function () {
        displayLog();
        $( "#ddlCollection" ).change(function() {
            switch ($("#ddlCollection").val())
            {
                case "": break;
                default: loadQueue();
            }
            //resize height of the scroller
            $("#divscroller").height(450);
        });

        $("#ddlCollection").change(); //run this when the page is loaded

        //Reload queue after 10sec, reload Queue every 10sec
        window.setInterval(function(){
            loadQueue();
        }, 10000);
        //Reload queue after 18sec, reload Log every 10sec
        window.setInterval(function(){
            displayLog();
        }, 18000);

    });
</script>
<style>
    #divLoader{
        padding-top:5px;
        padding-left:10px;
    }
    #divLog,#divscroller{
        display: inline-block;
        vertical-align: top;
        margin-bottom:20px;
    }
    #divLog{margin-left:5%;max-height:450px;overflow: auto;max-width:600px;}
    #divLoader,#divscroller,#divLog{min-height:300px;}
    button{margin:5px;}
    #divscroller{padding:5px}
    #tblQueue thead td{width: 100px; font-weight: bold;}
    #tblQueue thead tr{background:#1b77cb;color:#fff;}
    #tblQueue tr:nth-child(even){background: #bce1ff;}
    }
</style>
</html>
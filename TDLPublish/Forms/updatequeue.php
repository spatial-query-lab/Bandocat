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
<!-- HTML HEADER -->
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.0/css/all.css" integrity="sha384-aOkxzJ5uQz7WBObEZcHvV5JvRW3TUc2rNPA7pe3AwnsUohiw1Vj2Rgx2KSOkF5+h" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Font Awesome CDN CSS -->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <title>Blank Page</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
</head>
<body>
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container">
    <div class="row">
        <div class="col">
            <!-- Put Page Contents Here -->
            <h1 class="text-center">TDL Update Queue</h1>
            <hr>
            <div id="divright">
                <table width="100%">
                    <tr>
                        <td>
                            <!-- Form responsible for the select drop down menu -->
                            <form id = "form" name="form" method="post">
                                Select Collection:
                                <select name="ddlCollection" id="ddlCollection">
                                    <!-- Renders the Dropdownlist with the collections -->
                                    <?php $Render->GET_DDL_COLLECTION($DB->GET_COLLECTION_FOR_DROPDOWN_FROM_TEMPLATEID(array(5),false),"");?>
                                </select>
                            </form>
                            <br>
                            <input id="chkboxAutoScroll" type = "checkbox" name ="QueueAutoScroll" value="Checked">Queue Auto Scroll
                        </td>
                        <td>

                        </td>
                    </tr>
                    <tr>
                        <table>
                            <td>
                                <button onclick="startBackgroundWorker()" id="btnStartWorker">Start AQAQC</button>
                            </td>
                            <td>
                                <button onclick="clearLog()" id="btnClearLog">Clear Log</button>
                            </td>
                        </table>
                    </tr>

                </table>
                <h2 style='width: 560px'>Log</h2>
                <div id="divscroller" style="border-style:solid">
                    <div id="divLog">

                    </div>
                </div>
            </div>
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
<script>
    //testing only
    var count = 0;
    function startBackgroundWorker()
    {
        console.log($("#ddlCollection").val());
        if(console.log($("#ddlCollection").val() == ""))
        {

        }
        else
        {
            document.getElementById("idupdatingfigure").style.display = "block";
            $.ajax({
                type: "POST",
                url: "updatequeue_startworker_processing.php",
                data: {ddlCollection: $("#ddlCollection").val()},
                success: function (data)
                {
                    console.log(data);
                    console.log("DATA HAS RETURNED");
                    if(data == "done!")
                    {
                        document.getElementById("idupdatingfigure").style.display = "none";
                    }
                }
            });
        }

    }

    function clearLog()
    {
        $.ajax({
            type: "POST",
            url: "updatequeue_processing.php?action=clearlog",
            data: {ddlCollection: $("#ddlCollection").val()},
            success: function (data)
            {


            }
        });

    }

    //read log file and parse it to HTML #divLog
    function displayLog()
    {
        $.ajax({
            type: "POST",
            url: "updatequeue_processing.php?action=displaylog",
            data: {ddlCollection: $("#ddlCollection").val()},
            success: function (data)
            {

                $("#divLog").html( "<p>" + data + "</p>");
                if( count == 0 || document.getElementById("chkboxAutoScroll").checked == true)
                {
                    count = count + 1;
                    $("#divscroller").scrollTop($("#divscroller")[0].scrollHeight); //scroll to bottom
                }
            }
        });
    }

    /*Submit event that obtains teh information from the user form and calls the newuser_processing.php page, which links to
     a procedure in the database that insert the information into the bandocatdb database in the user table .*/
    $(document).ready(function ()
    {
        //displayLog();
        $( "#ddlCollection" ).change(function()
        {
            if($("#ddlCollection").val() == "")
            {
                document.getElementById("btnStartWorker").disabled = true;
            }
            else if ($("#ddlCollection").val() == "jobfolder" || $("#ddlCollection").val() == "mapindices" || $("#ddlCollection").val() == "pennyfenner" || $("#ddlCollection").val() == "fieldbookindices" )
            {
                document.getElementById("btnStartWorker").disabled = true;
            }
            else
            {
                document.getElementById("btnStartWorker").disabled = false;
            }
            //resize height of the scroller
            $("#divscroller").height(450);
        });

        $("#ddlCollection").change(); //run this when the page is loaded

        //Reload queue after 10sec, reload Queue every 10sec

        //Reload queue after 18sec, reload Log every 10sec

        window.setInterval(function()
        {

            displayLog();

            //
        }, 5000);
        /* if($("#ddlCollection").val() == "")
        {
            document.getElementById("btnStartWorker").disabled = true;
        } */

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
        width:100%;

    }
    #divLog{margin-left:5%;max-height:450px; max-width:500px;}
    #divLoader,#divscroller,#divLog{min-height:300px;}
    button{margin:5px;}
    #divscroller{width:575px;}
    #tblQueue thead td{width: 100px; font-weight: bold;}
    #tblQueue thead tr{background:#1b77cb;color:#fff;}
    #tblQueue tr:nth-child(even){background: #bce1ff;}
    }
</style>
</body>
</html>

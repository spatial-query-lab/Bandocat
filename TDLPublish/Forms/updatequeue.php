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

    <title>TDL Update Queue</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.js"></script>
</head>
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php';?>
			<figure id="idupdatingfigure" style='display: none; background: white;border-radius: 10px;box-shadow: 0 0 20px; blur-radius:10px; padding: 10px; position:absolute; top:40%;left:42%'><img src='../../Images/loading2.gif'> <figcaption><b> Update In Progress...</b></figcaption></figure>
        </div>
        <div id="divright">
            <h2>TDL Update Queue</h2>
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
    </div>
</div>
<!--End of new user input form-->

<?php include '../../Master/footer.php'; ?>
</body>


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
</html>
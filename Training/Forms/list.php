<?php
include '../../Library/SessionManager.php';
require('../../Library/DBHelper.php');
$session = new SessionManager();
//Get collection name and action
	$username = $_SESSION["username"];
$collection = $_GET["col"];
	$userfile = "";

	include 'config.php';
$file_arr = array();

//list of files in the directory
if($_SESSION["role"] == 1)
{
    $pos = -1;
    $listfile = scandir(getcwd());

    //Conditions to save only xml files to the file_arr array
    foreach($listfile as $row)
    {
        $pos = -1;
        $pos = strpos($row,".xml");
        if($pos != 0){
            //echo '<a href="list.php?user='  . $row . '">' . $row . '</a>';
            array_push($file_arr,$row);
        }
    }

    if(isset($_GET["user"]))
    {
        $userfile = str_replace(".xml","",$_GET["user"]);
        if($userfile == "")
            $userfile = $username;
    }
    else $userfile = $username;
}
else $userfile = $username;
?>


<!DOCTYPE html>
<head>
	<title>Edit Map Information</title>
	<!-- <meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="pragma" content="no-cache" /> -->
	<meta http-equiv = "Content-Type" content = "text/html; charset = utf-8" />
    <!-- Style CSS -->
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="../../ExtLibrary/DataTables-1.10.12/css/jquery.dataTables_themeroller.css">
        <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <!--Script-->
    <!-- jQuery -->
    <script type="text/javascript" charset="utf8" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.js"></script>
    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="../../ExtLibrary/DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <div id="wrap">
        <div id="main">
            <div id="divleft">
                <?php include '../../Master/header.php';
                include '../../Master/sidemenu.php' ?>
            </div>
            <div id="divright">
                <h2 id="page_title">Training</h2>
                <div id="divscroller" style="display: none;">
                    <table id="dtable" class="display compact cell-border hover stripe" cellspacing="0" width="100%" data-page-length='20'>
                        <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Library Index</th>
                            <th>Document Title</th>
                            <th>Classification</th>
                            <th>Completed</th>
                        </tr>

                        </thead>

                        <tbody>
                        <!--Block of code that load the information from the xml file to the table-->
                        <?php
                        $training_parent = "../Training_Collections";
                        //Collection directory
                        $training_collection_dir = $training_parent.'/'.$collection;
                        //User directory
                        $training_user_dir = $training_collection_dir.'/'.$userfile;
                        $document = new DOMDocument();
                        if ($_GET['type'] == 'newbie') {
                            $document->load($training_user_dir.'/'.$userfile.'_newbie.xml');
                        } elseif ($_GET['type'] == 'inter') {
                            $document->load($training_user_dir.'/'.$userfile.'_inter.xml');
                        }

                        $nodes = $document->getElementsByTagName('document');
                        $count = 0;
                        //echo "<td align = 'center'><a href=\"index.php?id=$id&user=$userfile&col=$collection&type=$type\">$libraryindex</a></td>";
                        foreach ($nodes as $node) {
                            foreach ($node->childNodes as $child) {
                                if ($child->nodeName == 'libraryindex') {
                                    $libraryindex = $child->nodeValue;
                                }
                                elseif ($child->nodeName == 'title') {
                                    $title = $child->nodeValue;
                                }
                                elseif ($child->nodeName == 'classification') {
                                    $classification = $child->nodeValue;
                                }
                                elseif ($child->nodeName == 'needsreview') {
                                    $needsreview = $child->nodeValue;
                                }
                                elseif ($child->nodeName == 'id')
                                {
                                    $id = $child->nodeValue;
                                }
                            }
                            $type = $_GET['type'];


                            echo '<tr>';
                            echo "<td align = 'center'><a href=\"index.php?id=$id&user=$userfile&col=$collection&type=$type\">$libraryindex</a></td>";
                            echo "<td align = 'center'>$libraryindex</td>";
                            echo "<td align = 'center'>$title</td>";
                            echo "<td align = 'center'>$classification</td>";
                            echo "<td align = 'center'>".(($needsreview == 0) ? 'No' : 'Yes')."</td>";
                            echo '</tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <a href="#" id="newbieLink" onclick="displayBlock(0)"><img src="../../Images/beginner.jpg" id='newbie' style="margin: 6% -4% 25% 24%"></a>
    <a href="#" id="interLink" onclick="displayBlock(1)"><img src="../../Images/intermediate.png" id='intermediate' style="margin: 0% 0% 0% -23%"></a>

    <h1 id="welcomeMsg" style="display: none; position: absolute; left: 35%; top: 35%; z-index:2;">Welcome to the Document List</h1>


    <!--Table container-->
	<div id = "interContainer" style="display: none">
	 		<?php if($_SESSION["role"] == 'Admin'){ ?>
	 		 <form enctype="multipart/form-data" id="switch" name="switch" method="GET">
	 			<p>Switch View:
	 			<select id="ddl_switch" name="user">
	 				<option value="">Select a file...</option>
	 				<?php
	 					foreach($file_arr as $a)
	 					{
	 						if(isset($_GET['user']) && $_GET['user'] == $a && $a != "") 
	 							echo '<option selected value="' . $a . '">' . $a . '</option>';
	 						else echo '<option value="' . $a . '">' . $a . '</option>';
	 					}
	 				?>
	 			</select>
	 		</form>
	 		<?php } ?>
	 		<form enctype="multipart/form-data" onsubmit = "return confirmReset()" action = ""  method = "POST">
	 			<input  align="center" class="button button-blue" name="resetProfile" type="submit" value="Reset Training Session" />
	 		</form>

        <?php //Array with all the xml files in the directory


        ?>

	 		<?php
	 			if (isset($_POST['resetProfile'])) {
	 					if(copy("data.xml", $userfile . ".xml" ))
	 					{
	 						echo "<script>window.location = 'list.php'</script>";
	 					}
	 					else
	 					{
	 						print "<script type=\"text/javascript\">";
							print "alert('An error has occured')";
							print "</script>";
	 					}
	 			}
	 		 ?>


	     	<table id = "tableMap1" >

	    	</table>
	    <br><br>
	</div>

    <script type="text/javascript">

        var trainingCollectionsJSON =  {"col": '<?php echo $collection ?>', "user": '<?php echo $username?>', "loc": 'parent'};

        //Function that creates the training directory by collection, user, and training type and it is triggered when the document is ready
        $( document ).ready(function() {
            if("<?php echo $_GET['type']?>" == 'inter' || "<?php echo $_GET['type']?>" == 'newbie')
            {
                $('#newbie').css('display', 'none');
                $('#intermediate').css('display', 'none');
                $('#divscroller').css('display', 'block');
            }

            $.ajax({
                type: 'post',
                url: "collectionTrainingXML.php",
                data: trainingCollectionsJSON
            });
        });

        function confirmReset() {
            var x =confirm("Are you sure want to reset your Training session?");
            if ( x == true) {
                return true;
            } else {
                return false;
            }
        }

        function displayBlock(block) {
            if(block == 0) {

                var newType = {"col": '<?php echo $collection ?>', "type": 'newbie', "loc": "children", "user": '<?php echo $username?>'};
                var welcomeMsg = '<h1 id="welcomeMsg">Welcome to your training list<h1>';
                $('#newbieLink').attr('href', 'http://localhost/BandoCat/Training/Forms/list.php?col=jobfolder&action=training&type=newbie');

                $.ajax({
                    type: 'post',
                    url: "collectionTrainingXML.php",
                    data: newType
                });
            }

            if(block ==1){

                var interType = {"col": '<?php echo $collection ?>', "type": 'inter', "loc": "children", "user": '<?php echo $username?>'};
                $('#interLink').attr('href', 'http://localhost/BandoCat/Training/Forms/list.php?col=jobfolder&action=training&type=inter');

                $.ajax({
                    type: 'post',
                    url: "collectionTrainingXML.php",
                    data: interType
                });
            }
        }
        $(document).ready(function() {
            oTable = $('#dtable').dataTable({
                "bJQueryUI": true,
                "order": [[3, "desc"]],
                'sPaginationType': 'full_numbers'
            });
        } );

        $(function() {
            $('#ddl_switch').change(function() {
                this.form.submit();
            });
        });

    </script>
	</body>
	</html>
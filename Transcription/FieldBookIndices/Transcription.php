<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(isset($_GET['col'])) {
	$collection = $_GET['col'];
	require('../../Library/DBHelper.php');
	require('../../Library/IndicesDBHelper.php');
	require('../../Library/ControlsRender.php');
	$DB = new IndicesDBHelper();
	$config = $DB->SP_GET_COLLECTION_CONFIG($collection);
	$Render = new ControlsRender();
	$arr = $DB->GET_MAPKIND_TABLE($collection);
}
else header('Location: ../../');

?>

<!DOCTYPE html>
<html>
<head>
	<title>BandoCat Transcription</title>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
	<script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script><link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
	<script src="../../ExtLibrary/jQuery-Tags-Input/jquery.tagsinput.js"></script>
	<link rel="stylesheet" type="text/css" href="../../ExtLibrary/jQuery-Tags-Input/jquery.tagsinput.css"/>
	<link rel="stylesheet" type="text/css" href="css/Transcription_Status.css">
	<link rel="stylesheet" type="text/css" href="../../Master/master.css">
	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />
	<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
	<script src='javascript/rastercoords.js'></script>
	<script src='javascript/Transcription.js'></script>
	<script src='javascript/Leaflet.MakiMarkers.js'></script>

</head>

<body bgcolor = "#e5f1fd" onload = "getRectangleCoords()">

<h2 id="page_title"><?php echo $config['DisplayName']; ?> Transcription</h2>

<!-- ENTRIES AND DISPLAY BUTTONS FOR CORRECTION OR COMPLETION-->
<div id = "Entries">
	<form id = "myForm" >
		<input type="hidden" id="Collection" class="Input_Field" name="Collection" value="<?php echo $collection; ?>" />
		<input type = "hidden" id = "Document_ID" class= "Input_Field" name = "Document_ID">
		<input type = "hidden" id = "File_Name" class= "Input_Field" name = "File_Name">
		<input type="hidden" id = "Entry_Coordinates" class= "Input_Field"name="Entry_Coordinates">

		<!--<input type="hidden" id = "Field_Book_Info" class= "Input_Field"name="Entry_Coordinates">
            <input type="hidden" id = "Map_Table_Info" class= "Input_Field"name="Entry_Coordinates">  -->

		<p class="fieldSubTitle" >Survey or Section:</p>
		<input type="text" id = "Survey_Or_Section" class= "Input_Field" name="Survey_Or_Section">
		<p class="fieldSubTitle" >Block or Tract:</p>
		<input type="text" class= "Input_Field" name="Block_Or_Tract" id ="Block_Or_Tract">
		<p class="fieldSubTitle" >Lot or Acres:</p>
		<input type= "text" class= "Input_Field" id = "Lot_Or_Acres" name = "Lot_Or_Acres">
		<p class="fieldSubTitle" >Description:</p>
		<input type = "text" class= "Input_Field" id = "Description" name = "Description">

		<!--Client-->
		<span class="tooltip fieldSubTitle"><mark><b>Client(s):</b></mark><p hidden>Use semicolon (;) to separate the values</p></span>
		<table id="Client_Table" name="Client_Table">
			<tr class="head">
				<th></th>
			</tr>
			<tr>
				<td><input type = "text" id = "Client" name = "Client"></td>
			</tr>
		</table>

		<!--Field Book Number-->
		<br>
		<div id="Table_Rows">
			<button type = 'button' onclick = "addFieldRow('Field_Book_Table')" id = "addFieldBookRow">+</button>
			<button type = 'button' onclick = "deleteTableRow('Field_Book_Table')" id = "deleteFieldBookRow">-</button>
		</div>
		<table id = "Field_Book_Table" name = 'Field_Book_Table'>
			<tr class = 'head'>
				<th><p class="fieldSubTitle" >Field Book Number:</p></th>
				<th><p class="fieldSubTitle" >Field Book Pages:</p></th>
			</tr>
			<tr>
				<td><input type="text" class= "Input_Field" id = "Field_Book_Number"></td>
				<td><input type="text" class= "Input_Field" id = "Field_Book_Page"></td>

			</tr>
		</table>

		<!--Related Papers-->
		<span class="tooltip fieldSubTitle"><mark><b>Related Papers File No.(s):</b></mark><p hidden>Use semicolon (;) to separate the values</p></span>
		<table id="RelatedPaper_Table" name="RelatedPaper_Table">
			<tr class="head">
				<th></th>
			</tr>
			<tr>
				<td><input type = "text" class= "Input_Field" id = "RelatedPaper" name = "RelatedPaper"></td>
			</tr>
		</table>

		<!--Maps-->
		<br>
		<div id="Table_Rows">
			<button type = 'button' onclick = "addMapRow('<?php echo $collection;?>','Map_Table')" id = "addMapTableRow">+</button>
			<button type = 'button' onclick = "deleteTableRow('Map_Table')" id = "deleteMapTableRow">-</button>
		</div>
		<table id = "Map_Table" name = 'Map_Table'>
			<tr class = 'head'>
				<th><p class="fieldSubTitle" >Map Number:</p></th>
				<th><p class="fieldSubTitle" >Map Kind:</p></th>
			</tr>
			<tr>
				<td><input type="text" class= "Input_Field" id = "Map_Number"></td>
				<td id= "Tabledata_Selection">
					<select id = 'Map_Kind' onchange = "Map_Kind_Dropdown()">
						<?php
						$Render->GET_DDL($DB->GET_INDICES_MAPKIND($collection),null);
						?>
					</select>
				</td>
				<?php
				if($session->isAdmin()) {
					$id =  'openModal("mapkindModal")';
					echo "<td ><button type = 'button' id = 'mapkindBtn' onclick =".$id." > +</button ></td>";
				}
				?>
			</tr>
		</table>

		<!--Date-->
		<div id="Table_Rows">
			<button type="button" onclick="addDateRow('Date_Table')" id="addDateTableRow">+</button>
			<button type = 'button' onclick = "deleteTableRow('Date_Table')" id = "deleteDateRow">-</button>
		</div>

		<table id="Date_Table" name="Date_Table">
			<tr class="head">
				<th><p class="fieldSubTitle" >Date:</p></th>
			</tr>
			<tr>
				<td>
					<select name = "monthStart" id = "Month" style = "width:75px;">
						<?php $Render->GET_DDL_MONTH('')?>
					</select>
				</td>
				<td>
					<select name = "dayStart" id = "Day" style = "width:60px;">
						<?php $Render->GET_DDL_DAY('')?>
					</select>
				</td>
				<td>
					<select name = "yearStart" id = "Year" style = "width:70px;">
						<?php $Render->GET_DDL_YEAR('')?>
					</select>
				</td>
			</tr>
		</table>

		<!--Job Number-->
		<br>
		<span class="tooltip fieldSubTitle"><mark><b>Job Number(s):</b></mark><p hidden>Use semicolon (;) to separate the values</p></span>
		<table id="JobNumber_Table" name="JobNumber_Table">
			<tr class="head">
				<th></th>
			</tr>
			<tr>
				<td>
					<input type="text" class= "Input_Field" id ="Job_Number" name = "Job_Number" >
				</td>
			</tr>
		</table>

		<!--Comments-->
		<p class="fieldSubTitle" >Comments:</p>
		<textarea class="Input_Field" id="Comments" name="Comments" maxlength="500" rows="6"></textarea>

		<!--Hidden input elements-->
		<!--	Clients	-->
		<input type="hidden" name="Client_Info" id="Client_Info">

		<!--	Field Books	-->
		<input type = "hidden" class= "Input_Field" name = "Field_Book_Info" id = "Field_Book_Info">

		<!--	Related Papers	-->
		<input type="hidden" name="Related_Papers_Info" id="Related_Papers_Info">

		<!--	Maps	-->
		<input type="hidden" class= "Input_Field" name="Map_Table_Info" id = "Map_Table_Info">

		<!--	Dates	-->
		<input type = "hidden" class= "Input_Field" name = "Date" id = "Date">

		<!--	Job Numbers	-->
		<input type="hidden" name="Job_Numbers_Info" id="Job_Numbers_Info">

		<!--hidden fields containing entry coordinates-->
		<input type="hidden" name ="x1" id = "x1" value="x1" disabled = "disabled">
		<input type="hidden" name ="y1" id = "y1" value="y1"disabled = "disabled">
		<input type="hidden" name ="x2" id = "x2" value="x2"disabled = "disabled">
		<input type="hidden" name ="y2" id = "y2" value="y2"disabled = "disabled">

		<input type = "button" id = "updateEntry" value = "Update Entry" onclick = "updateEntryData()">
		<input type = "button" id = "deleteEntry" value = "Delete Entry" onclick = "deleteSelected()">
		<input type="submit" value="Submit Entry" class = "Submit" id = "Submit" name = "submit">


	</form>
</div>

<!-- BUTTONS THAT ARE NOT PART OF THE FORM -->
<div id = "buttons" style="text-align:center">
	<button  onclick = "deletePrevious()" id = 'deletePrevious' name="deletePrevious" class="btn">Delete Active Rectangle</button>
	<button  id = "Complete_Transcription" onclick = "completeTranscription()" class="btn">Mark as Complete & Close</button>
	<button  id = "Incomplete_Transcription" onclick = "incompleteTranscription()" class="btn" >Close </button>
</div>

<!-- DOCUMENT CONTAINER WITH INDEX IMAGE DISPLAYED-->
<div id = "DOCUMENT_VIEW">
	<script>

		$("#Client_Table").tagsInput({
			'delimiter':[';'],
			'defaultText':''

		});

		$("#RelatedPaper_Table").tagsInput({
			'delimiter':[';'],
			'defaultText':''

		});

		$("#JobNumber_Table").tagsInput({
			'delimiter':[';'],
			'defaultText':''
		});


		Maki_Icon = tempIcon = L.MakiMarkers.icon({color: "#33c1ff", size: "m"});

		if (document.getElementById("Year").value == "Year" || document.getElementById("Month").value == "Month" || document.getElementById("Day").value == "Day")
		{
			var dateString ="0000-00-00";
			document.getElementById("Date").value = dateString;
		}
		// create the slippy map
		var map = L.map('DOCUMENT_VIEW',
			{
				minZoom: 1,
				maxZoom: 6,
				zoom: 1,
				crs: L.CRS.Simple,
				drawControl: true
			});

		var h = 5454;
		var w = 4544;

		img =
			[
				w,  // original width of raster
				h  // original height of raster
			];

		//defines our rasterCoords variable, see rasterCoords.js for more details, used for reprojection
		//of latlng values into image coordinates on the document
		var rc = new L.RasterCoords(map, img);

		//sets bounds of slippy map
		rc.setMaxBounds();
		var southWest = rc.unproject([0, h], map.getMaxZoom() - 1);
		var northEast = rc.unproject([w, 0], map.getMaxZoom() - 1);
		var bounds = new L.LatLngBounds(southWest, northEast);

		//sets view of map on the center to start
		map.setView(rc.unproject([img[0]/2, img[1]/2]), 2);

		//gets Document ID
		var docID = window.localStorage.getItem("docID");
		document.getElementById("Document_ID").value = docID;
		var fileName = window.localStorage.getItem("fileName");
		//gets fileName and writes it to hidden form, fileName in this case is the path of the image in the viewer
		document.getElementById("File_Name").value = fileName;


		//add image to slippy map
		L.imageOverlay(fileName, bounds).addTo(map);

		var markerCount = 0; 						// number of markers currently placed on slippy map
		var rectangleCount = 0; 					// number of rectangles currently placed on the slippy map
		var rectangleCoords = new Array(); 	// stores coordinates of created rectangles
		var rectangleArray = new Array();		// stores rectangle features
		var markerArray = new Array();			// stores the marker features


		/***************************************************************************************************************/
		function onMapClick(e)
		{
			// proccesses that take place after first marker is placed
			if(rectangleCount < 1)
			{
				//resets form so that new input can be entered
				document.getElementById("myForm").reset();
				deleteTable("Field_Book_Table");
				deleteTable("Map_Table");
				removeTags("Client_Table");
				removeTags("RelatedPaper_Table");
				removeTags('JobNumber_Table');
				addFieldRow("Field_Book_Table");
				addMapRow('<?php echo $collection;?>',"Map_Table");

				getFileName();

				//reset non-active rectangles to default color
				var defaultColor = {color: "#58d68d", weight: 1};
				for(var i = 0; i < rectangleArray.length; i++)
				{
					rectangleArray[i].setStyle(defaultColor);
				}
				rectangleSelected = false;

				//declares variables relevant to creating the active rectangle using markers
				var coords = rc.project(e.latlng);
				rectangleCoords.push(rc.unproject(coords));
				var marker = L.marker(rc.unproject(coords));
				marker.setIcon(tempIcon);
				map.addLayer(marker);
				markerArray.push(marker);
				markerCount++;

				//makes sure that the user doesnt place more than one active rectangle at a time
				if(markerCount % 2 == 0 )
				{
					//creates rectangle on slippy map and adds increments counters and pushes to appropriate arrays
					rectangleCount++;
					var rectangle = L.rectangle([[rectangleCoords[markerCount-2].lat, rectangleCoords[markerCount-2].lng],
						[rectangleCoords[markerCount-1].lat, rectangleCoords[markerCount-1].lng]],{color: "#28afd5", weight: 1});
					map.addLayer(rectangle);
					rectangleArray.push(rectangle);

					var point1 = rc.project(rectangle.getBounds()._southWest);
					var point2 = rc.project(rectangle.getBounds()._northEast);

					//create JSON containing image coordinates of rectangle southwest and northeast corners
					entryCoordinateObject = addEntryCoordinateObject(point1.x.toFixed(2), point1.y.toFixed(2), point2.x.toFixed(2), point2.y.toFixed(2));

					//write JSON as a string to hidden form "Entry_Coordinates"
					document.getElementById("Entry_Coordinates").value = JSON.stringify(entryCoordinateObject);
				}
			}
			else
				alert("Can't place more than one rectangle at a time");
		}

		//Hadnel on right click functions TODO: MOVE THIS LATER
		map.on('contextmenu', onMapClick);

		function addDateRow(id) {
			var table = document.getElementById(id);
			var row = table.insertRow(-1);
			var cell1 = row.insertCell(0);
			var cell2 = row.insertCell(1);
			var cell3 = row.insertCell(2);
			var ddlmonth = "<select name = 'monthStart' id = 'Month' class='MonthDate' style = 'width:75px;'>"
			ddlmonth += "<option value='00'>Month</option>";
			for(var j = 1; j <= 12; j++)
			{
				var temp = "";
				curmonth = j.toString();
				if(j < 10)
				{
					curmonth = '0' + j.toString();
				}
				temp = "<option value='" + curmonth + "'>" + curmonth + "</option>";

				ddlmonth += temp;
			}
			ddlmonth += "</select>";

			/************************************************************************************
			 * DAY
			 * ***********************************************************************************/

			var ddlday = "<select name = 'dayStart' id = 'Day' class='DayDate' style = 'width:60px;'>"

			ddlday += "<option value='00'>Day</option>";
			for(var j = 1; j <= 31; j++)
			{
				var temp = "";
				curday = j.toString();
				if(j < 10)
				{
					curday = '0' + j.toString();
				}
				temp = "<option value='" + curday + "'>" + curday + "</option>";

				ddlday += temp;
			}

			/************************************************************************************
			 * YEAR
			 * ***********************************************************************************/
			var ddlyear = "<select name = 'yearStart' id = 'Year' class='YearDate' style = 'width:70px;'>"
			var dYear = new Date();
			var cYear = dYear.getFullYear();
			ddlyear += "<option value='00'>Year</option>";

			for(var j = 1750; j <= cYear ; j++)
			{
				var temp = "";
				curyear = j.toString();
				if(j < 10)
				{
					curyear = '0' + j.toString();
				}

				temp = "<option value='" + curyear + "'>" + curyear + "</option>";

				ddlyear += temp;
			}

			cell1.innerHTML = ddlmonth;
			cell2.innerHTML = ddlday;
			cell3.innerHTML = ddlyear;

//        var month = document.getElementsByClassName('MonthDate');
//        var day = document.getElementsByClassName('DayDate');
//        var year = document.getElementsByClassName('YearDate');
//        for(i = 0; i < month.length; i++) {
//            month[i].innerHTML = '<?php //$Render->GET_DDL_MONTH('')?>//';
//            day[i].innerHTML = '<?php //$Render->GET_DDL_DAY('')?>//';
//            year[i].innerHTML = '<?php //$Render->GET_DDL_YEAR('')?>//';
//        }

		}

	</script>
</div>
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
		border-radius: 25px;
	}

	/* Modal Content/Box */
	.modal-content {
		border-radius: 25px;
		background-color: #fefefe;
		margin: margin: auto;
		float: right;
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
<!-- Modal content -->
<div id="mapkindModal" class="modal">
	<div class="modal-content">
		<span class="close" onclick = "closeModal('close','mapkindModal')">&times;</span>
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
	<script>
		/** button triggers addmapkind.php **/
		$("#btnNewMapkind").click(function(event){
			$("#txtPrompt").hide();
			event.preventDefault();
			$.ajax({
				type: 'post',
				url: "addmapkind.php",
				data: $("#frm_mapkind").serializeArray()
			}).success(function (data) {
				alert("Successfully Added Mapkind")
				window.location.reload(true);
			});
		});
	</script>
	<script>
		/** As user types the text is checked against current map kinds, it will prevent the user from adding if mapkind exists **/
		$('#txt').keyup(function(){
			var bla = $('#txt').val(); //Debug
			$('#txt').val(bla)
			//alert(bla);
			$.ajax({
				type: 'post',
				url: "mapkindvalidation.php",
				data: $("#frm_mapkind").serializeArray()
			}).success(function (data) {
				console.log(data);
				$("#btnNewMapkind").hide();
				if(data == 1) {
					$('#txt').css("background-color", "#FED4D4");
					$("#txtResult").show().text("Map Kind Already Exists");
				}
				if(data == 0) {
					$('#txt').css("background-color", '#D4FEEF');
					$("#btnNewMapkind").show();
					$("#txtResult").hide();
				}
			});
		});
	</script>
	<script>
		// Get the modal
		var modal = document.getElementById('mapkindModal');

		function openModal(id){
			var modal = document.getElementById(id);
			modal.style.display = "block";
		}
		function closeModal(spanid,modalid){
			var span = document.getElementsByClassName(spanid);
			var modal = document.getElementById(modalid);
			modal.style.display = "none";
		}
		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		}
	</script>
</body>
<style>
	mark {
		background-color: #ccf5ff;
	}
	span.tooltip:hover p{
		z-index: 10;
		display: inline;
		position: absolute;
		border: 1px solid #000000;
		background: #bfe9ff;
		font-size: 14px;
		font-weight: normal;
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
<script>
	//Script checks the users role. If the role is set as "Reader" Then certain functionality is hidden
	$( document ).ready(function()
	{
		var userRole = '<?php echo $session -> getRole(); ?>';

		if(userRole == "Reader")
		{


			document.getElementById("deleteEntry").disabled = true;
			document.getElementById("updateEntry").disabled = true;
			document.getElementById("Submit").disabled = true;
			document.getElementById('Complete_Transcription').disabled = true;
			document.getElementById('deletePrevious').disabled = true;

		}
	});
</script>
</html>
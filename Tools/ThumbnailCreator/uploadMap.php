<?php
	session_start();
	include 'config.php';
	if (!isset($_SESSION['logged_in']))			//prevent deep linking
	{
    	header("Location: index.php");
    }   

?>	

<!DOCTYPE html>
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv = "Content-Type" content = "text/html; charset = utf-8" />
	<title>Upload Map</title>
	<link rel = "stylesheet" type = "text/css" href = "newstyles.css" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<!--<script type="text/javascript" src="main.js"></script> <!--MAIN JS = ALL SUB JAVASCRIPT CODE-->
					<script type="text/javascript">
					//NOT WORKING
						function makeFileList() {
							var input = document.getElementById("file_array");
							var ul = document.getElementById("fileList");
							while (ul.hasChildNodes()) {
								ul.removeChild(ul.firstChild);
							}
							for (var i = 0; i < input.files.length; i++) {
								var li = document.createElement("li");
								li.innerHTML = input.files[i].name;
								ul.appendChild(li);
							}
							if(!ul.hasChildNodes()) {
								var li = document.createElement("li");
								li.innerHTML = 'No Files Selected';
								ul.appendChild(li);
							}
						}
					</script>




</head>
<body id = "bodyPage">
	<div id="header1">
		<div>
		<span style="float:left"><a href = "http://cbi.tamucc.edu"><img id = "image" src = "Logos/4.png" /></a></span>
		<span style="float:right"><a href = "menu.php" id = "link">Home Page </a><a href = "logoutFunctions.php" id = "link">Log Out</a></span>
		</div>
	</div>

	<div id="uploadsection">
	<div id="container">
		<h2>Map Upload</h2>

		<form action="parser.php" method = "post" enctype="multipart/form-data">
						<span style="float:left; padding-left:2%"><input type="submit" value="Upload files"></span><br><br>
						<span style="float:left; padding-left:2%"><input type="file" name="file_array[]" id="file_array" multiple ></span>						
						<br>
				<ul id="fileList"><li>No Files Selected</li></ul>
				

		</form>
	</div>
</div>
   
</body>
</html>

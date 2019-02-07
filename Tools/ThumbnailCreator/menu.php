<?php
	session_start();
	include 'config.php';
	if (!isset($_SESSION['logged_in']))
	{
    	header("Location: index.php");
    }
	
?>

<!DOCTYPE html>
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv = "Content-Type" content = "text/html; charset = utf-8" />
<title>Functions</title>

	<link rel = "stylesheet" type = "text/css" href = "styles.css" />
</head>

<body id = "bodyPage">
	<div id="header">
		<span style="float:left"><img id = "image" src = "Logos/4.png" /></span>
		<span style="float:right"><a href = "logoutFunctions.php" id = "link">Log Out</a></span>
	</div>	
	
	<div id = "containerFunction">
		<div id="left" class="column"></div>
		<div id="listFunc" class="column">   
			<h2 style="left: 70px;">MENU</h2>
				
			   	<ul>
			    	<li><a href = "uploadMap.php">Upload Maps</a></li>
			    	<li><br><a href = "editMap.php">View Map Information</a></li>
			    	<li><br><a href = "changePass.php">Change Password</a></li>
			    	<li><br><a href = "logoutFunctions.php">Log Out</a></li>
				</ul>
		</div>
    <div id="right" class="column"></div>
  

	<div id="footer"></div>

</body>



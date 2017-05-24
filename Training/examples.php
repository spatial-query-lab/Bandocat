<?php
		session_start();
	include 'config.php';
	include 'main.php';
	include 'class.php';
	if (!isset($_SESSION["logged_in"]) || !isset($_SESSION["username"]) || ($_SESSION["username"] == ""))
	{
		header('Location: ../BlucherScanning/');
	}
	$username = $_SESSION["username"];

	
		
		$fieldnote =  ["Field Book", "Official"];

		$envelope = ["Binding", "Cover", "Envelope", "Folder"];

		$corres = ["Casual" , "Official"];
	
		$description = ["Field", "Official"];

		$survey = ["Calculator" ,"Legal Paper", "Sketch Only", "With Sketch"]


?>
<script type="text/javascript">
		function onChange (dropdown) {
			var value = dropdown.options[dropdown.selectedIndex].value;
			window.location = "examples.php?exp="+value;
		}
</script>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Map Examples</title>
	<link rel="stylesheet" type="text/css" href="styles.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="./elevatezoom-master/jquery-1.8.3.min.js"></script>
	<script src="./elevatezoom-master/jquery.elevatezoom.js"></script>
</head>
<body>
<br>
	

	<form enctype="multipart/form-data" onsubmit = "" action = ""  method = "POST">
	<h3  style="display:inline-block" >Classification: </h3>
		<select  name = "classification" id = "classification" onchange="onChange(this.form.classification)">
		<?php classification($classification_arr,$_GET['exp']); ?></select>		
	</form>

	


	<?php 

		if (!empty($_GET['exp'])) {
			//fieldnote
			if ($_GET['exp'] == $classification_arr[1]) {
				foreach ($fieldnote as $temp) {
					echo "<h4>Type: $temp</h4>";

					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp']." (". $temp. ").png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp']." (". $temp. ").png'>" ;
					echo "<br>";
					echo "<br>";
					echo "<br>";				
				}
			}
			elseif ($_GET['exp'] == $classification_arr[2]) {
				foreach ($survey as $temp) {
					echo "<h4>Type: $temp</h4>";
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp']." (". $temp. ").png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp']." (". $temp. ").png'>" ;
					echo "<br>";	
					echo "<br>";
					echo "<br>";				
				}
			}
			elseif ($_GET['exp'] == $classification_arr[3]) {
				
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp'].".png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp'].".png'>" ;
					echo "<br>";					
				
			}
			elseif ($_GET['exp'] == $classification_arr[4]) {
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/MapBlueprint.png' alt='No Image'  data-zoom-image='./Examples/png/MapBlueprint.png'>" ;
					echo "<br>";					
				
			}
			elseif ($_GET['exp'] == $classification_arr[5]) {
				
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp'].".png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp'].".png'>" ;
					echo "<br>";					
				
			}
			elseif ($_GET['exp'] == $classification_arr[6]) {
				
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp'].".png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp'].".png'>" ;
					echo "<br>";					
				
			}
			elseif ($_GET['exp'] == $classification_arr[7]) {
				foreach ($envelope as $temp) {
					echo "<h4>Type: $temp</h4>";
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/Envelope-Binding (". $temp. ").png' alt='No Image'  data-zoom-image='./Examples/png/Envelope-Binding (". $temp. ").png'>" ;
					echo "<br>";	
					echo "<br>";
					echo "<br>";				
				}
			}
			elseif ($_GET['exp'] == $classification_arr[8]) {
				
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp'].".png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp'].".png'>" ;
					echo "<br>";					
				
			}
			elseif ($_GET['exp'] == $classification_arr[9]) {
				
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp'].".png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp'].".png'>" ;
					echo "<br>";					
				
			}
			elseif ($_GET['exp'] == $classification_arr[10]) {
				
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp'].".png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp'].".png'>" ;
					echo "<br>";					
				
			}
			elseif ($_GET['exp'] == $classification_arr[11]) {
				foreach ($corres as $temp) {
					echo "<h4>Type: $temp</h4>";
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp']." (". $temp. ").png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp']." (". $temp. ").png'>" ;
					echo "<br>";
					echo "<br>";
					echo "<br>";					
				}
			}
			elseif ($_GET['exp'] == $classification_arr[12]) {
				foreach ($description as $temp) {
					echo "<h4>Type: $temp</h4>";
					echo "<img class='zoom_01' style='min-width: 900px' src='./Examples/png-small/".$_GET['exp']." (". $temp. ").png' alt='No Image'  data-zoom-image='./Examples/png/".$_GET['exp']." (". $temp. ").png'>" ;
					echo "<br>";
					echo "<br>";
					echo "<br>";					
				}
			}

		}
		
		/*if (isset($_POST['classification_btn'])) {
			$_SESSION['exp_classification'] = $_POST['classification'];
			header('refresh:0');
		}*/

	 ?>
	 <script>
    $('.zoom_01').elevateZoom({
		zoomType: 'inner',
		cursor: "crosshair",
		zoomWindowFadeIn: 500,
		zoomWindowFadeOut: 750
   }); 
</script>
</body>
</html>
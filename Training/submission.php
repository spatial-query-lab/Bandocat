<!DOCTYPE html>
<html xmlns = "http://www.w3.org/1999/xhtml">
<?php
	session_start();
	include 'config.php';
	if (!isset($_SESSION["logged_in"]) || !isset($_SESSION["username"]) || ($_SESSION["username"] == ""))
	{
		header('Location: ../');
	}

	if(isset($_POST['submit']))
	{


		$progress_id = $_POST["idPage"];
		$userIDInput = mysql_real_escape_string($_POST['userIDInput']);
		SetProgress($userIDInput,$progress_id,0);
	
		$id = $_POST["idPage"];
		$library = mysql_real_escape_string($_POST['library']);		
		$docTitle = mysql_real_escape_string($_POST['docTitle']);		

		$needReview = $_POST['needReview'];
		$inSubfolder = $_POST['inSubfolder'];
		$SubfolderComment = $_POST['subfolder_comments'];
		$Classification = $_POST['ddlClassification'];
		$ClassificationID = getClassID($Classification);
		$ClassificationComment = $_POST['class_comments'];

		$comment = mysql_real_escape_string($_POST['comment']);

		$dayStart = mysql_real_escape_string($_POST['dayStart']);
		$monthStart = mysql_real_escape_string($_POST['monthStart']);
		$yearStart = mysql_real_escape_string($_POST['yearStart']);
		$dayEnd = mysql_real_escape_string($_POST['dayEnd']);
		$monthEnd = mysql_real_escape_string($_POST['monthEnd']);
		$yearEnd = mysql_real_escape_string($_POST['yearEnd']);


		$docAuthor1 = mysql_real_escape_string($_POST['author1']);
		$docAuthor2 = mysql_real_escape_string($_POST['author2']);
		$docAuthor3 = mysql_real_escape_string($_POST['author3']);
		
		if($dayStart == "Day")
			$dayStart = '00';
		if($monthStart == "Month")
			$monthStart = '00';
		if($yearStart == "Year")
			$yearStart = '0000';

		if($dayEnd == "Day")
			$dayEnd = '00';
		if($monthEnd == "Month")
			$monthEnd = '00';
		if($yearEnd == "Year")
			$yearEnd = '0000';

		$docStartDate = $monthStart ."/". $dayStart ."/". $yearStart;
		$docEndDate = $monthEnd ."/". $dayEnd ."/". $yearEnd;
	
		//Check and insert author fields
	    $author1 = InsertAuthor($docAuthor1);
	    $author2 = InsertAuthor($docAuthor2);
	    $author3 = InsertAuthor($docAuthor3);


        $needInput = 0;
		$sql = "UPDATE mapinformation SET library_index = '$library', document_title = '$docTitle', Author1_ID = '$author1', Author2_ID = '$author2', Author3_ID = '$author3', in_subfolder = '$inSubfolder', subfolder_comment = '$SubfolderComment', classification = '$ClassificationID', classification_comment = '$ClassificationComment' , need_input = '$needInput', need_review = '$needReview', user_id_input = '$userIDInput',
						Comments = '$comment',start_date = '$docStartDate', end_date = '$docEndDate'
				WHERE map_id = '$id'";
		$r = mysql_query($sql);
		if($r)
		{	
			?>
			<div class = "success message">
				<p>Edit successfully!!!</p>											 
			</div>

			<?php
		}
		else
		{
			?>
			<div class = "error message">
				<p>Fail to edit!</p>											 
			</div>

			<?php
		}
	}
	else
		echo "Can't isset submit\n";
	
?>
<br><a href = "editMap.php">Back</a>
<head>
<link rel = "stylesheet" type = "text/css" href = "styles.css" />
<script type="text/javascript">
	var myMessages = ['info','warning','error','success'];


	function showMessage(type)
	{
		$('.'+ type +'-trigger').click(function(){
							  
			  $('.'+type).animate({margin-top:"0"}, 500);
		});
	}

	$(document).ready(function(){
		 
		 		 
		 // Show message
		 for(var i=0;i<myMessages.length;i++)
		 {
			showMessage(myMessages[i]);
		 }
		 
		 
		 
	}); 

</script>
</head>
</html>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml"><head></head><body><div class="success message">
				<p>Edit successfully!!!</p>											 
			</div>

			<br><a href="Form/list.php">Back</a>
			<br>
			<?php
				session_start(); 
				if (isset($_SESSION['currentId'])) {					
				}
				else
				{
					header('Location: list.php');
				}
			?>

<link rel="stylesheet" type="text/css" href="../styles.css">
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

</body></html>
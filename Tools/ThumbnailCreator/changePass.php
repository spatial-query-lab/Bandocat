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

<?php
	session_start();
	require 'config.php';
	if (!isset($_SESSION['logged_in']))
	{
    	header("Location: index.php");
    }
    
	if(isset($_POST['submit']))
	{
	    $userID = $_SESSION['user_id'];
	    $oldPass = md5($_POST['oldPass']);
	   
		$password = md5($_POST['newPass']);
		
      	$result1 = mysql_query("SELECT password FROM user WHERE user_id = '$userID'");
      	while($row = mysql_fetch_array($result1))
			$passCheck = $row['password'];
		
		if($passCheck == $oldPass)
		{
			$result = mysql_query("UPDATE user SET password = '$password' WHERE user_id = '$userID'");
			
			if(!($result))
			{
			?>
				<div class="error message">
					<p>Can't change password 
						<?php $error =  mysql_error(); 
						echo "<p>".$error."</p>";?>
					</p>											 
				</div>
		  		<br><a href = "menu.php">Back</a>
			<?php							
			}
			else
			{	
			?>
				<div class="success message">
					<p>Your password has been changed!!!</p>											 
				</div>			
				<br><a href = "menu.php">Back</a>
			<?php
			}
		}
		else
		{
		?>
			<div class="error message">
				<p>The password you gave is incorrect. Please re-input it again.</p>											 
			</div>

	  		<br><a href = "changePass.php">Back</a>
		<?php
		}
	}
	else
	{
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv = "Content-Type" content = "text/html; charset = utf-8" />
<title>Change Password</title>

<script type = "text/javascript">
	
	function validateForm()
	{
		//var passCheck = document.getElementById("passCheck");		
		var oldPass = document.getElementById("oldPass");
		var newPass = document.getElementById("newPass");
		var newPassConfirm = document.getElementById("newPassConfirm");		

		if(oldPass.value == "")		
		{	
			alert("You did not enter your old password. \n"+"Please enter one now");
			return false;
		}

		/*if(passCheck.value != oldPass.value)
		{
			alert("The old password you gave is incorrect. \n"+"Please re-input it again.");
			return false;
		}*/

		if(newPass.value == "")		
		{	
			alert("You did not enter your new password \n"+"Please enter one now");
			return false;
		}

		if(newPass.value == oldPass.value)		
		{	
			alert("Your old password and new password cannot be the same\n"+"Please re-enter now");
			return false;
		}

		if(newPassConfirm.value == "")		
		{	
			alert("You did not confirm your new password \n"+"Please enter one now");
			return false;
		}

		if(newPass.value != newPassConfirm.value)		
		{	
			alert("Your new password and confirming new password have to be the same\n"+"Please enter one now");
			return false;
		}
	}	
</script>
	<link rel = "stylesheet" type = "text/css" href = "styles.css" />
</head>

<body id = "bodyPage">
	<form action = "" onsubmit = "return validateForm();" method = "post">
		<span style="float:left"><img id = "image" src = "Logos/4.png" /></span>
		<span style="float:right"><a href = "menu.php" id = "link">Home Page </a> <a href = "logoutFunctions.php" id = "link">Log Out</a></span>

		<div id = "container">
		<h2 style = "position: relative; left: -50px;">CHANGE MY PASSWORD</h2>
		<br>				   
		<table>
			<?php
				$userID = $_SESSION['user_id'];
				$result1 = mysql_query("SELECT password FROM user WHERE user_id = '$userID'");
		      	while($row = mysql_fetch_array($result1))
					$passCheck = $row['password'];
			?>
			<tr>
				<td> <br>Your old password: </td>
				<td> <br><input type = "password" name = "oldPass" id = "oldPass" size = "30" /></td>
				<td> <input type = "hidden" name = "passCheck" id = "passCheck" value = "<?php echo $passCheck ?>" /></td>
			</tr>
			<tr>
				<td> <br>Your new password: </td>
				<td> <br><input type = "password" name = "newPass" id = "newPass" size = "30" /></td>
			</tr>
            <tr>
				<td> <br>Confirm your new password: </td>
				<td> <br><input type = "password" name = "newPassConfirm" id = "newPassConfirm" size = "30" /></td>
			</tr>
			<tr>
				<td><br></td>
				<td><br><input type = "reset" value = "Reset" class="button button-blue">  <input type = "submit" name = "submit" value = "Submit" class="button button-blue" /></td>
			</tr>
				
		</table>
	</div>
		
    </form>

    
</body>
</html>
<?php

}
?>
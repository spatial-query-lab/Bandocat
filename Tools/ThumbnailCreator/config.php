<?php
	mysql_connect("localhost","root","root");
	mysql_select_db("bluchermaps2") or die('Could not connect to the database');
	
	function checkUser($userName,$pass)
	{
		$pass = md5($pass);

		$q = "SELECT * FROM user WHERE username ='$userName' AND password = '$pass' ";
		$r = mysql_query($q);
		if ($r)
		{
			$user = mysql_fetch_assoc($r);
			if(!empty($user['user_id']) && $user['user_id'] != '')
				return $user;			
		}
		return false;	
	}



?>






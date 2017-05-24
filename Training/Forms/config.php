<?php
	// mysql_connect("localhost","root","root");
	// mysql_select_db("jobfolderinventory") or die('Could not connect to the database');
	
	// function checkUser($userName,$pass)
	// {
	// 	$pass = md5($pass);

	// 	$q = "SELECT * FROM user WHERE username ='$userName' AND password = '$pass' ";
	// 	$r = mysql_query($q);
	// 	if ($r)
	// 	{
	// 		$user = mysql_fetch_assoc($r);
	// 		if(!empty($user['user_id']) && $user['user_id'] != '')
	// 			return $user;			
	// 	}
	// 	return false;	
	// }

	
	function customerName()
	{
		$q = "SELECT * FROM customer";
		$r = mysql_query($q);
		if($r)
		{
			while($data = mysql_fetch_assoc($r))
			{
				echo '<option>'.$data['CustomerName'].'</option>';
				//echo '<option value="'.$data['CustomerID'].'">'.$data['CustomerName'].'</option>';
			}
			
		}

	}

	function companyName()
	{
		$q = "SELECT * FROM company";
		$r = mysql_query($q);
		if($r)
		{
			
			while($data = mysql_fetch_assoc($r))
			{
				echo '<option>'.$data['CompanyName'].'</option>';
				//echo '<option value="'.$data['CompanyID'].'">'.$data['CompanyName'].'</option>';
		
			}
			
		}
	}

	function authorName()
	{
		$q = "SELECT * FROM documentauthor";
		$r = mysql_query($q);
		if($r)
		{
			
			while($data = mysql_fetch_assoc($r))
			{
				echo '<option>'.$data['DocumentAuthor'].'</option>';
				//echo '<option value="'.$data['CompanyID'].'">'.$data['CompanyName'].'</option>';
		
			}
			
		}
	}

	function classification($arr,$input)
	{
		for($i = 0; $i < count($arr);$i++)
		{
				if( $input == $arr[$i])
				{
					if ($input == "")
						echo '<option selected = "selected" value="" >'. "None" .'</option>';
					else echo '<option selected = "selected" >'.$arr[$i].'</option>';
				}
				else echo '<option>'.$arr[$i].'</option>';
		}
			

	}

	function medium($input)
	{
		$q = "SELECT * FROM documentmedium";
		$r = mysql_query($q);
		if($r)
		{
			
			while($data = mysql_fetch_assoc($r))
			{				
				
				if( $input == $data['MediumName'])
					echo '<option selected = "selected" >'.$data['MediumName'].'</option>';
				else
					echo '<option>'.$data['MediumName'].'</option>';
			}
			
		}

	}
	
	function month($input)
	{
		
		for($num = 1; $num <= 12; $num++)
		{
			
			if($input == $num)
				echo '<option selected = "selected" >'.$input.'</option>';
			else
				echo '<option value="'.$num.'">'.$num.'</option>';
		}

	}

	function day($input)
	{
		for($num= 1; $num<=31; $num++)
		{
			if($input == $num)
				echo '<option selected = "selected" >'.$input.'</option>';
			else
				echo '<option value="'.$num.'">'.$num.'</option>';
		}
	}

	function year($input)
	{
		for($num=1800; $num<=2020; $num++)
		{
			if($input == $num)
				echo '<option selected = "selected" >'.$input.'</option>';
			else
				echo '<option value="'.$num.'">'.$num.'</option>';
		}
	}



//echo a datalist from array
function getDatalist($array)
{
	foreach ($array as $key => $value)
	{
		echo '<option value="'.$value.'">'.$value.'</option>';
	}
}

//getAuthors (1,2,3)
function getAuthor($author_id)
{
	$temp_author = "";
	$query_author = mysql_query("SELECT documentauthor_name from jobfolderinventory.documentauthor WHERE documentauthor_id = '$author_id'");
	if ($query_author)
	{
		while($author_fetch = mysql_fetch_assoc(($query_author)))
		{
			$temp_author = $author_fetch['documentauthor_name'];
		}
	}
	echo $temp_author; 
}


	//ADDING DOCUMENT AUTHOR

		function InsertAuthor($newauthor)
		{

	       	$searchAuthor_res = mysql_query("SELECT * from jobfolderinventory.documentauthor where documentauthor_name = '$newauthor' ");
	       	if(mysql_num_rows($searchAuthor_res) < 1)
	        {
	        	$qAuthor = "INSERT INTO jobfolderinventory.documentauthor (documentauthor_name) VALUES ('$newauthor')";	            

	            if(mysql_query($qAuthor))
					return mysql_insert_id();
	       	}
	       	else
	       	{	          
	          	$dataAuthor = mysql_fetch_assoc($searchAuthor_res);
	          	return $dataAuthor['documentauthor_id'];
	       	}
	    }

	    //RESET IN_PROGRESS TO ZERO - USED WHEN LOGGING IN
	    function ResetProgress($userid)
	    {
	    	$cmd = mysql_query("UPDATE jobfolderinventory.mapinformation SET in_progress = 0 WHERE user_id_in_progress = '$userid'");
	    }

	    //Set In_progress when executing the document
	    function SetProgress($userid,$mapid,$status)
	    {
	    	$cmd = mysql_query("UPDATE jobfolderinventory.mapinformation SET in_progress = '$status', user_id_in_progress = '$userid' WHERE map_id = '$mapid'");

	    }



	    //GET CLASSIFICATION_ID from Name
	   	function getClassID($name)
	   	{
	   		$temp = "";
	   		$cmd = mysql_query("SELECT classification_id FROM jobfolderinventory.documentclassification WHERE classification_name = '$name'");
	   		if ($cmd)
	   		{
	   			while($exec = mysql_fetch_assoc($cmd))
	   			{
	   				$temp = $exec['classification_id'];
	   			}
	   		}
	   		return $temp;
	   	}

	   	//Get classification id,name,description
	   	function getClassificationArray()
	   	{
	   		$arr = [];
	   		$cmd = mysql_query("SELECT * FROM jobfolderinventory.documentclassification WHERE classification_id != 0");
	   		while($exec = mysql_fetch_assoc($cmd))
	   		{
	   			array_push($arr,$exec);
	   		}
	   		return $arr;
	   	}
		
		
		//prevent mutex
		function check_access($interrupt_id,$map_id)
	   	{
	   		$user_id = null;
	   		$progress = null;
	   		$cmd = mysql_query("SELECT * FROM jobfolderinventory.mapinformation WHERE map_id = '$map_id'");
	   		if ($cmd)
	   		{
	   			while($exec = mysql_fetch_assoc($cmd))
	   			{
	   				$progress = $exec['in_progress'];
	   				$user_id = $exec['user_id_in_progress'];
	   			}
	   		}
	   		if ($user_id != $interrupt_id && $progress == 1)
	   			return false;
	   		else return true;
	   	}
		
		function getUserIDInput($map_id)
	   	{
	   		$q = mysql_query("SELECT user_id_input from jobfolderinventory.mapinformation WHERE map_id = '$map_id' limit 1");
	   		if ($q)
	   		{
	   			$result = mysql_fetch_row($q);
	   			return $result[0];
	   		}
	   	}
?>






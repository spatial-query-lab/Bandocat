<?php
	session_start();
	include 'config.php';
	if (!isset($_SESSION['logged_in']))			//prevent deep linking
	{
    	header("Location: index.php");
    }   

    $allowedExts = array("gif", "jpeg", "jpg", "png", "tif");

if(isset($_FILES['file_array']))
{
	//To Insert empty data into table
	$library = "";
    $docTitle = "";

	$name_array = $_FILES['file_array']['name'];
	$tmp_name_array = $_FILES['file_array']['tmp_name'];
	$type_array = $_FILES['file_array']['type'];
	$size_array = $_FILES['file_array']['size'];
	$error_array = $_FILES['file_array']['error'];

	$pathThumbnail = "C:/uploads/thumbnails/";
	//THUMBNAIL DIRECTORY CHECK
	if(!(is_dir($pathThumbnail)))
	{
		mkdir($pathThumbnail,0777);
	}


	//FOR LOOP
	for ($i = 0; $i < count($tmp_name_array); $i++)
	{
		    $error_code = 0; 
		    /*
		    ERROR CODE LIST:
		    0: No Error
		    222: File front (1) already existed
		    223: File back (2) already existed
		    333: Thumbnail front (1) already existed
		    334: Thumbnail back (2) already existed
		    */


		//check file name
			$fileName1 = $_FILES['file_array']['name'][$i];
		//get string before '-'
			$exp = explode ("-", $fileName1);
			$name = $exp[0]; //to create new folder

			$path = "C:/uploads/" . $name . "/"; //NEW PATH****
			$img1Path = $path . $fileName1; //full-path

			//for thumbnails
			$infoFile1 = pathinfo($fileName1);
			$nameFile1NoExt = basename($fileName1, '.'.$infoFile1['extension']);

			//get extension and compare
			$ext1 = pathinfo($fileName1, PATHINFO_EXTENSION); //get extension
			//*******need compare*******
			//************************************

			//if the directory uploads/$name  does not exist
			if(!(is_dir($path)))
			{
				mkdir($path,0777);
			}
			//check error: Existed img and thumbnail
			if(file_exists($img1Path))
			{
				$error_code = 222;
			}
			else if (file_exists($pathThumbnail . $nameFile1NoExt . ".jpg"))
			{
				$error_code = 333;
			}

		//upload files 
		if(move_uploaded_file($tmp_name_array[$i], $path .$name_array[$i]) && $error_code == 0)
		{

			//If insert to Database sucessfully , generate a thumbnail

				$exec1 = "convert " . $img1Path . " -deskew 40% -fuzz 50% -trim +repage -resize 200 " . $pathThumbnail . $nameFile1NoExt . ".jpg";						    
				exec($exec1, $yaks1);
				
					//INSERT data to DATABASE
            		//$sql = "INSERT INTO mapinformation (library_index,document_title,file_name) 
                 	//VALUES ('$library','$docTitle','$img1Path')";
					
					//$r = mysql_query($sql);
					//if($r)
					//{
						echo $name_array[$i]." uploaded successfully<br>";
						echo "Type: ". $type_array[$i]."<br>";
						echo "Size:" . $size_array[$i]/1024/1024 ." MB<br><br>";
					//}
					//else echo "File " .$name_array[$i]." failed to upload to the database!<br>";


		} //END IF (MOVE_UPLOADED_FILE)....
		else
		{
			echo "File " .$name_array[$i]." failed to upload!<br>";
			if($error_code == 222)
				echo "Reason: File already existed.<br>";
			else if ($error_code == 333)
				echo "Reason: Thumbnail already existed.<br>";
			echo "<br>";
		}
	}//END FOR LOOP
}//END IF ISSET


?>
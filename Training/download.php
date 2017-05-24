<?php
	$input = $_GET['file'];
	$name = pathinfo($input);
	$filename = $name['basename'];
	if(!$input)
	{
		die('File not found');
	}
	else
	{
		// set headers
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Type: image/tif");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Transfer-Encoding: binary");
		ob_clean();
    	flush();
		readfile($input);
	}
?>
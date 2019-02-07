<?php
	
	$collectionNameFile = "../UpdateCron/currentCollection.txt";
	
	if(!file_exists($collectionNameFile))
		$collectionName = fopen($collectionNameFile, "w"); // creates collection file to store which collection the user has selected
	else $collectionName = fopen($collectionNameFile,"w"); //open collection file to read which collection the user wants scanned
		
	fwrite($collectionName,$_POST['ddlCollection']); //Write the selected collection to the DDL
	fclose($collectionName);
	 //Start UpdateCron, redirecting output to output.txt and logging errors in ERRORS.txt
	pclose(popen('start /B cmd /C "cd ../UpdateCron & UPDATECRON >output.txt 2>ERRORS.txt"', 'r'));
echo "done!";
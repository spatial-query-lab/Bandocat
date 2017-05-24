<?php 


function writeXMLtag($id, $tag, $data, $username){
	$document  = new DOMDocument();

//Determine if the file exist so to create a new folder with the new xml files; newbie and intern
    $filename = $username . ".xml";

    if (file_exists($filename)) {
        var_dump("does exist");
    }

	$document->load($username.'.xml');
	$tag = $document->getElementsByTagName($tag);

	if ($tag->length > 0) {
		if ($id > $tag->length && isset($_SESSION[$username])) {
			$_SESSION[$username] = 1;
			return;
		}
		else
		{
				$tag->item($id-1)->nodeValue = $data;
				//echo $data." ".$id."<br>";
		}
	}

	$document->save($username.'.xml');
}


 

 ?>
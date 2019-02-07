<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require '../../Library/DBHelper.php';
$DB = new DBHelper();

if(isset($_POST['submit'])){
    if(!empty($_POST['Collection'])) {

        //As output of $_POST['Collection'] is an array we have to use foreach Loop to generate a CSV for each Selected Collection
        foreach ($_POST['Collection'] as $collection)
        {
            //Set Name for CSV Document based on the Collection
            $name = $DB->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DisplayName'];

            //Retrieve the Index and Document Title for the selected collections
		$dbname = $DB->SP_GET_COLLECTION_CONFIG(htmlspecialchars($collection))['DbName'];
        $DB->getConn()->exec('USE ' . $dbname);
        $sth = $DB->getConn()->prepare("SELECT `libraryindex`,`title` FROM `document` WHERE `libraryindex` LIKE 'I%' OR `libraryindex` LIKE 'X%' OR (`libraryindex` LIKE 'V%' AND `libraryindex` NOT LIKE 'VF%')");
        $sth->execute();
        $list = $sth->fetchAll(PDO::FETCH_ASSOC);
            // Create CSV Filename based on the Collection
            $filename = $collection . "_" . date('Ymd') . ".csv";
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: application/vnd.ms-excel");

            $out = fopen("php://output", 'w');
            fwrite($out, "sep=\t".PHP_EOL);
            $delimiter = "\t";
            $current = "";

            //Generate First Line of Document specifying the collection
            //fputcsv($out, array("Library Index"=>strtoupper($name) . " COLLECTION","Document Title" => ""),$delimiter);
			fputcsv($out, array("Library Index"=>"Library Index","Document Title" => "Document Title"),$delimiter);
            foreach ($list as $line)
            {

                $temp = $line['libraryindex'];
                //
                $folder = explode('-',$temp)[0];
                if(strcmp($folder,$current) != 0)
                {
                    $current = $folder;
                }

                $title = $line['title'];
                if(strpos($line['title'],'"') >= 0) ;
                else $title = '="' . $line['title'] . '"';
                fputcsv($out, array("Library Index"=>'="' . $line['libraryindex'] . '"',"Document Title" => $title),$delimiter);
            }

            fclose($out);

        }
    }
    //No Collection Selected, prompt message
    else { echo "Please Select a Collection.";}
}
?>
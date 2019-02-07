<?php
//
include '../../Library/SessionManager.php';
$session = new SessionManager();
if($session->isAdmin()) {
    require('../../Library/DBHelper.php');
    $DB = new DBHelper();
}
$ticket = $_POST;
//Array that will contain all the ticket id and library index properties.
$ticketData = [];

/*POST FORMAT*/
/*array(1) {
["data"]=>
  array(n, number of ticket elements) {
        [0]=>
    array(2) {
            ["subjectCol"]=>
      string() ""
            ["subject"]=>
      string() ""
    }
  }
}*/

foreach($ticket['data'] as $property){
    //Retrieves the document id and library index  from a library index//
    /*To avoid any misarrangement of a ticket's properties the database function
    retrieves the document id and library index and sends them in a set.*/
    $docID = $DB->SELECT_DOCID_BY_SUBJECT($property['subject'], $property['subjectCol']);

    /*If the retrieved document id values was not found the returned value from the
    database function is false*/
    if ($docID == false)
        //A set is organized with the retrieved false document id and its library index
      array_push($ticketData,[false, $property['subject']]);

    else
        //A set is organized with the retrieved document id and library index
        array_push($ticketData, $docID);
}

//RETURNED JSON FORMAT: {"data":[[Document Id, Library Index],...[]]}
$objJSON = new \stdClass();
$objJSON -> data = $ticketData;

echo json_encode($objJSON)



?>
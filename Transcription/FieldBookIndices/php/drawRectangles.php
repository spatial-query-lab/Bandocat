<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/IndicesDBHelper.php');
$DB = new IndicesDBHelper();

//$draw = 'convert ../images/test.jpg -stroke black -strokewidth 10 -fill "rgba( 40, 213, 187, 0.3 )" -draw "';

$sql = "SELECT x1, y1, x2, y2 FROM transcription";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $draw = $draw . ' rectangle '. $row["x1"]. "," . $row["y1"]. " " . $row["x2"]. "," . $row['y2'];
    }
}
$conn->close();

$draw = $draw . '" ../images/output.jpg';

exec($draw);

	
?>
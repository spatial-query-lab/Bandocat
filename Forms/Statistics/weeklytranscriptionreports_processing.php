<?php
function getIsoWeeksInYear($year) {
    $date = new DateTime;
    $date->setISODate($year, 8);
    return ($date->format("W") === "53" ? 53 : 52);
}


// this function gets the start date of the weekly chart on statistics page
function getStartAndEndDate($week, $year) {
    $month = date("M");

    //Switch statement sets the month to two months behind the current month so past progress can be displayed.
    switch ($month) {
        case "Jan":
            $month = "Nov";
            break;
        case "Feb":
            $month = "Dec";
            break;
        case "Mar":
            $month = "Jan";
            break;
        case "Apr":
            $month = "Feb";
            break;
        case "May":
            $month = "Mar";
            break;
        case "June":
            $month = "Apr";
            break;
        case "July":
            $month = "May";
            break;
        case "Aug":
            $month = "June";
            break;
        case "Sep":
            $month = "July";
            break;
        case "Oct":
            $month = "Aug";
            break;
        case "Nov":
            $month = "Sep";
            break;
        case "Dec":
            $month = "Oct";
            break;

    }


    $dayOfMonth = date("d")  ;
    $time = strtotime("$dayOfMonth $month $year", time());
    $day = date('w', $time);
    $time += ((7*$week)+1-$day)*24*3600;
    $return[0] = date('n/j', $time);
    $time += 6*24*3600;
    $return[1] = date('n/j', $time);
    return $return[0] . " - " . $return[1];
}



require '../../Library/DBHelper.php';
$DB = new DBHelper();
$collections = $DB->GET_COLLECTION_FOR_DROPDOWN();
$year =  $_GET['year'];
$week_upperbound = 9;
if($year == date("Y"))
    $week_upperbound = 9;

$array = array();
foreach($collections as $col) {
    $counter = 0;
    for($i = 0; $i < $week_upperbound; $i++)
    {
        $wk = $i + 1;
        $temp[$i] = array('week'=>strval($wk),'count'=>'0');
    }

    $merge_arr = array_merge($temp,$DB->GET_WEEKLY_TRANSCRIPTION_REPORT($year,$col['collectionID']));

    $unique_arr = array();
    foreach($merge_arr as $key => $val){
        if(!array_key_exists('week', $unique_arr)){
            $unique_arr[$val['week']] = $val;
        }
    }
    $subarray = array();
    foreach($unique_arr as $ua) {
        array_push($subarray,$ua['count']);
    }
    array_push($array,$subarray);
}

//labels array
$labels = array();
for($i = 0; $i < $week_upperbound; $i++) {
    array_push($labels,getStartAndEndDate(($i + 1),$year));
}

$final_array = array("labels" => $labels,"datasets" => $array);
echo json_encode($final_array);

?>
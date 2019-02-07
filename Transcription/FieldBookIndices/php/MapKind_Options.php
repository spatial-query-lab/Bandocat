<?php
include '../../../Library/SessionManager.php';
$session = new SessionManager();
require('../../../Library/DBHelper.php');
require('../../../Library/IndicesDBHelper.php');
require('../../../Library/ControlsRender.php');
$Render = new ControlsRender();
$DB = new IndicesDBHelper();
$ret = '<td><input type="text" class= "Input_Field"' . 'value = "' . $_POST['mapNumber'] . '"></td><td>' . "<select id='Map_Kind" . $_POST['id'] . "' onchange = 'Map_Kind_Dropdown()'>";
    $array = $DB->GET_INDICES_MAPKIND($_POST['collection']);
    $selected = $_POST['mapKind'];
$ret = $ret . '<option value="">Select</option>';
foreach ($array as $item) {
    if ($selected == $item[0])
        $ret = $ret . '<option value="' . $item[0] . '" selected>' . $item[0] . '</option>';
    else $ret = $ret . '<option value="' . $item[0] . '">' . $item[0] . '</option>';
}

    $ret = $ret . "</select>" . "</td>";

echo $ret;
?>
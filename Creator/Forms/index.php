<?php
//Super Admin only???
include '../../Library/SessionManager.php';
$session = new SessionManager();
require '../../Library/DBHelper.php';
require '../../Library/ControlsRender.php';

//temporary CreatorHelper class
require '../../Library/CreatorHelper.php';
$DB = new CreatorHelper();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Creator</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel="stylesheet" type="text/css" href="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.css">
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.js"></script>
</head>

<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php';
            ?>

        </div>
        <div id="divright">
            <h2 id="page_title">Collection Creator</h2>
            <div id="divscroller">
                        <label>Select from Dropdown: </label>
                        <select id="ddlOptions" name="ddlOptions" required>
                            <option value="">Select</option>
                            <option value="Existing">Create new collection based on existing templates</option>
                            <option value="New">Create & Customize a new template</option>
                        </select>

                        <div id="divLoader"></div>
            </div>
        </div>
    </div>
</div>
<?php include '../../Master/footer.php'; ?>
 </body>
<script>
    $(document).ready(function () {
        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 55);

        $("#ddlOptions").change(function(event){
            event.preventDefault();
            var selectedval = $("#ddlOptions :selected").val();

            switch(selectedval)
            {
                case "New":
                    $("#divLoader").load("./templatecreator.php");
                    break;
                case "Existing":
                    $("#divLoader").load("./collectioncreator.php");
                    break;
                default: break;
            }
        });
    });
</script>
<style>
    #divLoader{
        width:80%;
        margin:5px;
        padding:10px;
    }
</style>
</html>


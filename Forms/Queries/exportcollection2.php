<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require '../../Library/DBHelper.php';
$DB = new DBHelper();
require('../../Library/ControlsRender.php');
$Render = new ControlsRender();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Account Settings</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
</head>
<body>
<style>
    /*Account Stylesheet Adaptation from Collection Name */
    .Account{
        border-radius: 2%;
        box-shadow: 0px 0px 4px;
    }

    .Account_Table{
        background-color: white;
        padding: 3%;
        border-radius: 6%;
        box-shadow: 0px 0px 2px;
        margin: auto;
        font-family: verdana;
        vertical-align: middle;
        margin-top: 4%;
        margin-bottom: 9%;
    }

    .Account_Table .Account_Title{
        margin-top: 2px;
        margin-bottom: 12px;
        color: #008852;
    }

    .Account_Table .Collection_data{
        width: 50%;
    }

</style>
<script type="text/javascript" src="PasswordMatch.js"></script>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php';?>
        </div>
        <div id="divright">
            <h2 id="page_title">Export Index</h2>
            <div id="divscroller" >
                <table class="Account_Table" style ="background-color: #e6e6e6">
                    <form method="post" action="exportcollection_processing2.php">
                        <tr>
                            <td colspan="2" style="text-align: center">
                                <h4 class="Account_Title">Export Collection Index :</h4>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <!---------Select Option Fields starts here------>
                                <select name="Collection[]" multiple style="width: 250px">
                                    <?php $Render->GET_DDL_COLLECTION($DB->GET_COLLECTION_FOR_DROPDOWN(),null);?>
                                </select><br/><br/>
                                <hr/>
                            </td>
                        </tr>
                        <td colspan="2" style="text-align: center">
                            <br>
                            <input type="submit" name="submit" value="Get Selected Values" class="bluebtn"/>
                        </td>
                    </form>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../../Master/footer.php'; ?>

</body>
</html>

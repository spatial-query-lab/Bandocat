<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if($session->isAdmin() == false) {
    header('Location: ../../');
}
include '../../Library/DBHelper.php';
$DB = new DBHelper();
include '../../Library/ControlsRender.php';
$Render = new ControlsRender();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Manage Users</title>

    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.js"></script>
    <style>
        #frm_user{
            height:300px;
            width:600px;
            min-width:500px;
            margin:0 auto;
        }
        #frm_user table{margin-top:20px;}
        #ddl_user{line-height:40px;}
        label{font-size:1.2em;}
        select{height:30px;}
    </style>
</head>
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php';?>
        </div>
        <div id="divright">
            <h2 id="page_title">Manage Users</h2>
            <div id="divscroller">
                <table class="Account_Table">
    <form id="frm_user" name="frm_user" method="post">
        <tr>
            <td colspan="2" style="text-align: center">
                <h4 class="Account_Title">Select User:</h4>
        <!---------Select Option Fields starts here------>
        <select name="ddl_user" id="ddl_user" multiple style="height: 250px; width: 250px">
            <?php $Render->GET_DDL_USER($DB->GET_ACTIVE_USER_TABLE(),null); ?>
        </select><br/>
            </td>
        </tr>
        <td colspan="2" style="text-align: center">
            <br>
            <input type="submit" value="Update Role" id="btnUpdateRole" class="bluebtn" name="btnUpdateRole"/>
            <input type="submit" value="Reset Password" id="btnResetPwd" class="bluebtn" name="btnResetPwd"/><br>
        </td>
        <tr><td style="width:150px">
            <input type="radio" name="rd_Role" id="rd_Role_admin" value="2"/><label>Admin</label></td>
            <td><input type="radio" name="rd_Role" id="rd_Role_reader" value="4"/><label>Reader</label></td></tr>
            <td><input type="radio" name="rd_Role" id="rd_Role_ru" value="3"/><label>Regular</label></td>
            <td><input type="radio" name="rd_Role" id="rd_Role_inactive" value="0"/><label>Inactive</label></td></tr>
            <br>

    </form></table>
                <div style="text-align:center"><p style="font-size:1.2em;color:#00BC65;" hidden id="txtPrompt">User's Temporary password is <span style="font-size:1.2em;font-weight:bold;color:#2e6da4" id="txtNewPassword"></span><br>Please notify the user to change the password once they're logged in.</p></div>

</div>
<?php include '../../Master/footer.php'; ?>
</body>
<script>

    $("#btnUpdateRole").click(function(event){
        $("#txtPrompt").hide();
        if($("#ddl_user :selected").text() == "Select")
            return false;
            event.preventDefault();
            $.ajax({
                type: 'post',
                url: "updaterole_processing.php",
                data: $("#frm_user").serializeArray()
            }).success(function (data) {
                alert($("#ddl_user :selected").text() + "'s role has been updated!");
                location.reload();
            });
    });

    $("#btnResetPwd").click(function(event){
        $("#txtPrompt").hide();
            event.preventDefault();
        if($("#ddl_user :selected").text() == "Select")
            return false;

            $.ajax({
                type: 'post',
                url: "resetpassword_processing.php",
                data: $("#frm_user").serializeArray()
            }).success(function (data) {
                $("#txtNewPassword").text(JSON.parse(data));
                $("#txtPrompt").show();
            });
    });

    $("#ddl_user").change(function(event){
        $("#txtPrompt").hide();
        event.preventDefault();
        $.ajax({
            type: 'post',
            url: "getrole_processing.php?user=" + $("#ddl_user :selected").val() ,
            data: {userID: $("#ddl_user :selected").val() },
        }).success(function (data) {
            var ret = data;
            $("#btnUpdateRole").prop("disabled",false);
            switch(ret)
            {
                case "Admin":
                    $("#rd_Role_admin").prop("checked",true);
                    break;5
                case "Writer":
                    $("#rd_Role_ru").prop("checked",true);
                    break;
                case "Reader":
                    $("#rd_Role_reader").prop("checked",true);
                    break;
//                case "Inactive":
//                    $("#rd_Role_inactive").prop("checked",true);
//                    break;
                case "Super Admin":
                    $("#rd_Role_admin").prop("checked",true);
                    break;
                default:break;

            }
        });
 });



</script>
<style>
    /*Account Stylesheet Adaptation from Collection Name */
    .Account{
        border-radius: 2%;
        box-shadow: 0px 0px 4px;
    }

    .Account_Table{
        background-color: #e8eaed;
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
</html>

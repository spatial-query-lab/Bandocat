<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require '../../Library/DBHelper.php';
$DB = new DBHelper();
$userinfo = $DB->GET_USER_INFO($session->getUserID());
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
        background-color: #f1f1f1; border-radius: 2px;  box-shadow: 0px 0px 3px #0c0c0c;
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
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php';?>
        </div>
    <div id="divright">
            <h2 id="page_title">Account Settings</h2>
        <div id="divscroller">
            <table class="Account_Table">
                <form id="frmChangePassword" name="frmChangePassword" method="post" enctype="multipart/form-data">
                <tr>
                    <td colspan="2" style="text-align: center">
                        <h4 class="Account_Title">Change Password</h4>
                    </td>
                </tr>
                <tr>
                    <td><label class="unselectable" for="txtOldPassword"><span class="required">*</span>Current Password:</label>
                    </td>
                    <td>
                        <input type="password" id="txtOldPassword" name="txtOldPassword" required >
                    </td>
                </tr>
                <tr>
                    <td><label class="unselectable" for="txtPassword"><span class="required">*</span>New Password:</label></td>
                    <td>
                        <input type="password" name="txtPassword" id="txtPassword" required>
                    </td>

                </tr>
                <tr>
                    <td><label class="unselectable" for="txtRepeatPassword"><span class="required">*</span>Confirm Password:</label></td>
                    <td>
                        <input type="password" required name="txtRepeatPassword" id="txtRepeatPassword" onkeyup="checkPass(); return false;">
                    </td>
                </tr>
                    <td colspan="2" style="text-align: center">
                    <br>
                    <input type="submit" name = "btnSubmitChangePassword" id="btnSubmitChangePassword" value="Update" class="bluebtn"/>
                </td>
                </form>
                <form id="frmUserInformation" name="frmUserInformation" method="post" enctype="multipart/form-data">
                <tr>
                    <td colspan="2" style="text-align: center">
                        <br><br><h4 class="Account_Title">User Information</h4>
                    </td>
                </tr>
                    <tr>
                        <td><label class="unselectable" for="txtEmail" required>Email:</label></td>
                        <td>
                            <input type="text" id="txtEmail" name="txtEmail" required value="<?php echo $userinfo['email']; ?>">
                        </td>
                    </tr>
                <tr>
                    <td><label class="unselectable" for="txtName">Name:</label></td>
                    <td>
                        <input type="text" id="txtName" name="txtName" value="<?php echo $userinfo['fullname']; ?>">
                    </td>
                </tr>
                <td colspan="2" style="text-align: center">
                    <br>
                    <input type="submit" name = "btnSubmitUserInformation" id="btnSubmitUserInformation" value="Update" class="bluebtn"/>
                </td>
                </form>
            </table>
            </div>
        </div>
    </div>
</div>
<?php include '../../Master/footer.php'; ?>
</body>
<script>
    $(document).ready(function(){
    });
    //update user Info
    $("#frmUserInformation").submit(function(event){
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "index_processing.php?action=updateUserInfo",
            data: {txtEmail: $("#txtEmail").val(),txtName: $("#txtName").val()},
            success: function (data) {
                alert(data);
            }
        });
    });

    //update password
    $("#frmChangePassword").submit(function(event){
        event.preventDefault();
        if(checkPass() == false) {
            alert("Passwords must match!");
            return;
        }
        $.ajax({
            type: "POST",
            url: "index_processing.php?action=updatePassword",
            data: {txtOldPassword: $("#txtOldPassword").val(),txtPassword: $("#txtPassword").val()},
            success: function (data) {
                    switch(data)
                    {
                        case "1":
                            alert("Success!");
                            break;
                        case "0":
                            alert("Fail to update password.\nPlease make sure your old password is correct.");
                            break;
                        default:
                            alert("Fail to connect to database!\n Please contact administrator");
                            break;
                    }
            }
        });

    });

    function checkPass() {
        var pass1 = document.getElementById('txtPassword');
        var pass2 = document.getElementById('txtRepeatPassword');
        var goodColor = "#66cc66";
        var badColor = "red";

        if (pass1.value == pass2.value) {
            pass2.style.backgroundColor = goodColor;
            return true;
        } else {
            pass2.style.backgroundColor = badColor;
            return false;
        }
    }
</script>
</html>

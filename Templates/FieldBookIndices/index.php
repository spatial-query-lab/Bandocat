<?php
//Menu
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(isset($_GET['col']))
{
    //get collection name passed in from side menu
    $collection = $_GET['col'];
    require('../../Library/DBHelper.php');
    $DB = new DBHelper();
    //get appropriate DB
    $config = $DB->SP_GET_COLLECTION_CONFIG($collection);
}
else header('Location: ../../');
?>
    <!doctype html>
    <html lang="en">
    <!-- HTML HEADER -->
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <!-- The title of the page -->
        <title><?php echo $config["DisplayName"] . " Menu"; ?></title>
        <link rel = "stylesheet" type = "text/css" href = "CSS/Map_Collection.css" >
        <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
        <script type="text/javascript" src="ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
        <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
        <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.js"></script>
        <script type="text/javascript" src="../../Master/master.js"></script>

    </head>
    <!-- HTML BODY -->
    <body>
    <table id = "thetable">
        <tr>
            <!-- Draw the header and Side Menu -->
            <td class="menu_left" id="thetable_left">
                <?php include '../../Master/header.php';
                include '../../Master/sidemenu.php' ?>
            </td>
            <td class="Collection" id="thetable_right">
                    <h2>Menu</h2>
                    <table class="Collection_Table">
                        <tr>
                            <td>
                                <!-- Title Displayed in Green style in master.css -->
                                <h4 class="Collection_Title"><?php echo $config["DisplayName"]; ?></h4>
                            </td>
                        </tr>
                        <tr>
                            <td class="Collection_data">
                                <!-- Catalog Documents Button, Php code sends the collection name to list.php and send variable action=catalog -->
                                    <a class="Collection_Button" id="catalogBtn"  href="./catalog.php?col=<?php echo $collection; ?>" style="text-decoration: none; color: white; display: block">Catalog Document</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="Collection_data">
                                <!-- Edit/View Documents Button, Php code sends the collection name to list.php and send variable action=review -->
                                    <a class="Collection_Button" id="editBtn" href="./list.php?col=<?php echo $collection; ?>" style="text-decoration: none; color: white; display: block;">Edit/Review Document</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="Collection_data" >
                                <!-- Transcribe Documents Button, Php code sends the collection name to transcription page of this collection -->
                                <a class="Collection_Button"  id="transcribeBtn" href="../../Transcription/Indices/list.php?col=<?php echo $collection; ?>" style="text-decoration: none; color: white; display: block;">Transcribe Document</a>
                            </td>
                        </tr>
    </table>

    <?php include '../../Master/footer.php'; ?>

    </body>
    <!-- END BODY -->
    <style type="text/css">
        nav{margin: -1px 0px 40px 15px !important;}
        #thetable td{padding-top: 11px; padding-left: 1px}
    </style>
    <script>
        //Script checks the users role. If the role is set as "Reader" Then certain functionality is hidden
        $( document ).ready(function()
        {
            var userRole = '<?php echo $session -> getRole(); ?>';

            if(userRole == "Reader")
            {

                document.getElementById('catalogBtn').className = "disabledClass";
               // catalogBtnElement.disabled = true;

                $('#catalogBtn').click(function(e)
                {
                    e.preventDefault();

                });

            }
        });
    </script>
</html>

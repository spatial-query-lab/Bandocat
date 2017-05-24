<nav>
    <div class="menu-item alpha">
        <h4><a href="../../">Main Menu</a></h4>
    </div>
    <!-- Add admin section to side menu -->
    <?php
    //create a new unique instance of DBheler so we can use it for tickets
    require_once '../../Library/DBHelper.php';
    $DB1 = new DBHelper();
    //if user is admin, then add Admin section to the menu
    $userid = $session-> getUserID();
    $userticketCount = $DB1->GET_USER_CLOSEDTICKET_COUNT($userid);
    $ticketCount = 0;
    $admin = $session->isAdmin();
    if($session->isAdmin())
    {
        //queries the database for the number of tickets currently active
        $ticketCount = $DB1->GET_ADMIN_OPENTICKET_COUNT();
        echo '<div class="menu-item menu-item_sub4">
            <!--class for the visuals, data-badge to pass the number of tickets to the text in the badge -->
            <h4><a class="notificationBadge" data-badge='.$ticketCount.' id="adminNotificationBadge" href="">Admin </a></h4>    
             <div></div>
            <ul>           
            <li><a href="../../Forms/ActivityLog/index.php">Activity Log</a></li>
            <li><a class="notificationBadge" data-badge='.$ticketCount.' id="adminNotificationBadge2" href="../../Forms/Ticket/">View Tickets </a></li>
            <li><a href="../../Forms/ManageUser/">Manage User</a></li>
            <li><a href="../../Forms/NewUser/">Create New User</a></li>
            </ul>
        </div>
        <div class="menu-item menu-item_sub2">
        <h4><a href="#">TDL Publishing</a></h4>
        <ul>
            <li><a href="../../TDLPublish/Forms/index.php">Listing</a></li>
            <li><a href="../../TDLPublish/Forms/queue.php">Queue</a></li>
        </ul>
    </div>';
    }
    ?>
    <script>

    </script>
    <!-- Collections Tab -->
    <div class="menu-item menu-item_sub5">
        <h4><a href="#">Collections</a></h4>
        <ul>
            <li><a href="../../Templates/Map/index.php?col=bluchermaps">Blucher Maps</a></li>
            <li><a href="../../Templates/FieldBook/index.php?col=blucherfieldbook">Field Book</a></li>
            <li><a href="../../Templates/Map/index.php?col=greenmaps">Green Maps</a></li>
            <li><a href="../../Templates/Indices/index.php?col=mapindices">Indices</a></li>
            <li><a href="../../Templates/Folder/index.php?col=jobfolder">Job Folder</a></li>
        </ul>
    </div>
    <!-- Indices Transcription Tab -->
    <div class="menu-item">
        <h4><a href="../../Transcription/Indices/list.php?col=mapindices">Indices Transcription</a></h4>
    </div>
    <div class="menu-item menu-item_sub2">
        <h4><a href="#">GeoRectification</a></h4>
        <ul>
            <li><a href="../../GeoRec/Map/index.php?col=bluchermaps">Blucher Maps</a></li>
            <li><a href="../../GeoRec/Map/index.php?col=greenmaps">Green Maps</a></li>
        </ul>
    </div>
    <!-- Queries Tab -->
    <div class="menu-item menu-item_sub4">
        <h4><a href="#">Queries</a></h4>
        <ul>
            <li><a href="../../Forms/Queries/hascoast.php">Coastal Maps</a></li>
            <li><a href="../../Forms/Queries/exportcollection.php">Export Document Index</a></li>
            <li><a href="../../Forms/Queries/mapswithouttitle.php">Maps Without Titles</a></li>
            <li><a href="../../Forms/Queries/manage_authorname.php">Manage TDL Author</a></li>
            <li><a href="#">Supplied Title Procedure</a></li>
        </ul>
    </div>
    <!-- Statistics Tab -->
    <div class="menu-item">
        <h4><a href="../../Forms/Statistics/">Statistics</a></h4>
    </div>
    <!-- My Account Tab -->
    <div class="menu-item">
        <h4><a href="../../Forms/AccountSettings/">My Account</a></h4>
    </div>

    <?php


    echo '<div class="menu-item menu-item_sub2">
        <h4><a class="notificationBadge" data-badge='.$userticketCount.' id="userNotificationBadge" href="#">Ticket </a></h4>
        <ul>
            <li><a class="notificationBadge" data-badge='.$userticketCount.' id="userNotificationBadge2" href="../../Forms/UserTicket/">View Tickets </a></li>   
            <li><a href="../../Forms/TicketsSubmission/" target="_blank">Submit Ticket</a></li>
        </ul>
    </div>';


    if($session->isSuperAdmin())
    {
        echo '<div class="menu-item">
        <h4><a href="../../Creator/">Create New Collection</a></h4>
    </div>';
    }
    ?>

    <!-- Help Tab -->
    <div class="menu-item menu-item_sub2">
        <h4><a href="#">Help</a></h4>
        <ul>
            <li><a href="../../Procedures/Documents">Procedures</a></li>
            <li><a href="../../Procedures/Utilities">Support Software</a></li>
        </ul>
    </div>
    <!-- Logout Tab -->
    <div class="menu-item">
        <h4><a href="../../Forms/Logout/" id="sidemenu_logout">Logout as <?php echo $session->getUsername(); ?></a></h4>
    </div>

    <script>
        //Admin
        $( document ).ready(function()
        {
            var count = '<?php echo $ticketCount ?>';
            if(count > 0) {
                document.getElementById("adminNotificationBadge2").className = "notificationBadge";
                document.getElementById("adminNotificationBadge").className = "notificationBadge";
            }
            if(count < 1)
            {
                var admin = 0;
                try
                {
                    admin = '<?php echo $admin ?>';
                }catch(e)
                {
                    console.log("Error: "+ e);
                }
                //Handle our admin notification
                if(admin != '') {
                    document.getElementById("adminNotificationBadge2").className = "";
                    document.getElementById("adminNotificationBadge").className = "";
                }
                else {//Do nothing}
                }
            }

            var count2 = '<?php echo $userticketCount; ?>';
            if(count2 > 0)
            {
                document.getElementById("userNotificationBadge2").className = "notificationBadge";
                document.getElementById("userNotificationBadge").className = "notificationBadge";
            }
            if(count2 < 1)
            {
                document.getElementById("userNotificationBadge2").className = "";
                document.getElementById("userNotificationBadge").className = "";
            }
        });
    </script>
</nav>
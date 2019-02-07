<?php
include '../../Library/SessionManager.php';
require '../../Library/DBHelper.php';
require '../../Library/AnnouncementDBHelper.php';
    $session = new SessionManager();
    $announcement = new AnnouncementDBHelper();
    $announcementData = $announcement->GET_ANNOUNCEMENT_DATA();
    $announcementLenght = count($announcementData);
    $announcementJSON = json_encode($announcementData);
    $userID = $session->getUserID();
    $admin = $session->isAdmin();
?>
<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Welcome to BandoCat!</title>

    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel="stylesheet" type="text/css" href="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.css">

    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.js"></script>
    <script type="text/javascript" src="Greetings.js"></script>
</head>
<!-- HTML BODY -->
<body>

<!-- TABLE FOR LAYOUT OF PAGE -->
<table id="thetable">
    <tr id="trTop">
        <th class="menu_left" id="thetable_left">
            <?php include '../../Master/header.php'?>
        </th>
        <th class="tg-chpy" colspan="2">
            <span id= "lblUsername" class="Username"><a href="../../Forms/AccountSettings"><?php echo $session->getUserName(); ?></a></span>,&nbsp;<span id="Time_Day"></span><span id="Greetings"></span>
        </th>
    </tr>
    <tr style="height: 630px">
        <td class="menu_left" id="thetable_left">
            <?php include '../../Master/sidemenu.php' ?>
        </td>
        <td class="tg-zhyu"><h2>BandoCat</h2>
            <div>
                <object type="text/html" data="http://spatialquerylab.com/category/map-scanning/"" width="100%" height="680px" style="overflow:auto">
                </object>
            </div>
        </td>
        <td class="tg-0za1"><h2>Announcements</h2>
            <div id="divscroller" style="text-align: center">
            <div id="post"></div>
            </div>
        </td>
    </tr>
</table>

<?php include '../../Master/footer.php'; ?>
</body>
<!-- Funny Greeting -->
<script>
    /*Program that will get the time in hours from the Date function. Then, a conditional statement will determine what
    time of the day it is; morning or afternoon*/
        var d = new Date();
        var n = d.getHours();
        if(n >= 12)
        {
            document.getElementById("Time_Day").innerHTML = "Good Afternoon! ";
        }

        else{
            document.getElementById("Time_Day").innerHTML= "Good Morning! ";
        }
    /*Program that will obtain a random number from the random function. Then, it will multiply it by the length of the
    Greetings javascript array, which is saved in an external document. Finally the integer number retrieved is used to
    select an index with a greeting that will be displayed at the top of the page.*/
        var Random = Math.random();
        var Generic_Number = Random * (Greetings.length);
        var Integer = parseInt(Generic_Number);
        document.getElementById("Greetings").innerHTML = Greetings[Integer];



        //Prepends an announcement button to the scrolling div if the user has admin privileges
        var admin = '<?php echo $admin; ?>';
        if(admin == 1) {
            $('#divscroller').prepend('<input class="bluebtn" id="announcer" type="button" onclick="createAnnouncement()" value="CREATE ANNOUNCEMENT" style="font-size: 0.775vw;">')
        }

    /***************************************************
     * <A    N   N   O   U   N   C   E   M   E   N   T>
     ***************************************************/
    //Announcement information for post in JSON format
    var announcementPost = '<?php echo $announcementJSON ?>';


    /**********************************************
     * Function: specialCharReplace
     * Description: Function that translates the special characters that were converted in announcement_processing.php to
     * regular characters.
     * Parameter(s): text (string) -text with special characters.
     * Return value(s): annoText (string) -text sanitized for display.
     ***********************************************/
    function specialCharReplace(text) {
        //Statement that conditions the precense of special characters in the text
            if(text.includes("&amp;") || text.includes("&quot;") || text.includes("&#039;") || text.includes("&lt;") || text.includes("&gt;")) {
                //If special character found replace it with its corresponding translation
                var annoText = text.replace(/&amp;/g,"&");
                annoText = annoText.replace(/&quot;/g,'"');
                annoText = annoText.replace(/&#039;/g,"'");
                annoText = annoText.replace(/&lt;/g,"<");
                annoText = annoText.replace(/&gt;/g,">");
            }
            //Otherwise retain the same text without translation
            else{
                annoText = text;
            }
        return annoText
    }



    //JSON object with the announcement information
    var post = JSON.parse(announcementPost);

    /***********************
     * Constant Elements
     ***********************/
    //Announcement text element
    var annoText = $("<textarea id='annoText' style='height: 85px; width: 85%' required></textarea>");
    //Announcement title element
    var annoTitle = $("<input type='text' id='annoTitle' required>");
    //Image that it is used to trigger a close function
    var closeImg = $("<img src='../../Images/Close_Button.png' height='20' width='20' class='closeAnno' style='margin: 1% -50% -4% 50%; position: inherit;'>");
    //Post announcement
    var postButton = $("<input id='postAnno' class='bluebtn' type='submit' value='Post' onclick='postAnno()' style='position:relative; padding: 2%;'>");


    /***********************
     * Post Announcement
     * -Post element id = {post}-{post index}
     * Components
     * 1. Title element id = {title}-{post index}
     * 2. Message element id = {message}-{post index}
     ***********************/
    /**********************************************
     * Function: loadAnnouncement
     * Description: Function that will retrieve and display an announcement from the database if the date condition is true
     * Parameter(s): NONE
     * Return value(s): NONE
     ***********************************************/
    function loadAnnouncement()
    //resize height of the scroller
    {$("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#trTop").outerHeight() - $("#h2Announcement").outerHeight() - 60);
        //Length of the JSON object to know how many announcements should be posted
        var announcementLength = '<?php echo $announcementLenght ?>';
        if(announcementLength > 0) {
            for(var i = 0; i < announcementLength; i++) {
                //Post element and components appended to the post div with information obtained from the database
                $("#post").append("<div id = 'post-" + i + "' class='anno'><h2 id ='title-" + i + "' ondblclick='editAnnouncement(" + 0 + ")'></h2><p id='message-" + i + "' ondblclick='editAnnouncement(" + 1 + ")' style='padding: 5%;'></p></div> ");
                titleText = post[i].title;
                messageText = post[i].message;
                //$("#title-" + i).text(specialCharReplace(titleText));
                $("#title-" + 0).text(specialCharReplace(titleText));
                $("#message-" + 0).text(specialCharReplace(messageText));
            }
        }
    }
    //Post announcement when window loads
    $( window ).load(loadAnnouncement);

    /**********************************************
     * Function: createAnnouncement
     * Description:
     * Function that it is triggered when the input button id = announcer is clicked, which appends a div class = anno
     * to a div id = post.
     * Parameter(s): NONE
     * Return value(s): NONE
     ***********************************************/
    function createAnnouncement() {
        if(admin == 1) {
            //The number of elements with the id = annoCreate is retrieved
            var annoLenght = $("div[id*='annoCreate']").length;
            //If the number of id = annoCreate is greater than one a new announcement won't be created
            //Prevents the creation of two new announcement containers
            if(annoLenght < 1){
                //Prepends to the div id = post the create announcement container id = annoCreate with all of its elements
                $("#post").prepend("<div id='annoCreate' class='anno'>" +
                    "<h2 id='h2Announcement'>Announcement</h2>" +
                    "<form id='announcement'>" +
                    "<input type='text' id='annoTitle' value='Title' required>"+
                    "<textarea id='annoText' style='height: 50px; width: 85%' maxlength='800' required></textarea>"+
                    "<p style='margin: 2%;'>Expiration Date: <input type='text' id='deleteDate' required></p>"+
                    "</form>" +
                    "</div>");

                //Additional elements that are appended to the div id = annoCreate
                $("#annoCreate").append(closeImg);
                $("#annoCreate").append(postButton);


                //Event listeners that call the functions to close and post  the announcement
                closeImg.click(closeAnno);
                postButton.click(postAnno);

                //jQueryUI function that displays a dynamic calendar for user input
                $(function () {
                    $("#deleteDate").datepicker();
                });
            }
        }

    }

    /**********************************************
     * Function: closeAnno
     * Description: Function that closes the create announcement div.
     * Parameter(s): NONE
     * Return value(s): NONE
     ***********************************************/
        function closeAnno() {
            $('#annoCreate').remove();
            $(".closeAnno").remove();
        }

    /**********************************************
     * Function: postAnno
     * Description: Function that closes the announcement container and requests, and posts announcementData to
     * announcement_processing.php for database insertion.
     * Parameter(s): NONE
     * Return value(s): NONE
     ***********************************************/
    function postAnno() {
        //The data for post is retrieved
        var title = $('#annoTitle').val();
        var message = $('#annoText').val();
        var date = $('#deleteDate').val();
        var user = '<?php echo $userID ?>';
        //The create announcement container and closing image are removed
           $('#annoCreate').remove();
           $(".closeAnno").remove();

           //Data stored in a JSON format for post
          var announcementData = {
              "title": title,
              "message": message,
              "endDate": date,
              "user": user,
              "action": 1
          };

          //Data posted to announcement_processing.php to be inserted to the database
        $.ajax({
            type: 'post',
            url: "announcement_processing.php",
            data: announcementData,
            success: function (data) {
                $('#annoCreate').remove();
                postDivs = $("#post > div").length;
                dismissCreate(postDivs, title, message);
            }
        });
    }

    /**********************************************
     * Function: getPostIndex
     * Description: Function that gets the index of the post from its attribute id
     * Posts' parent and children id name format: {id name}-{id index}
     * Parameter(s): target, the target element of the event; event.target
     * Return value(s): postIndex, integer of the post from which the event was triggered
     ***********************************************/
    function getPostIndex(target) {
        var elemEvent = target;
        //Retrieves the element attribute id
        var elemID = $(elemEvent).attr('id');
        //Splits the id string by a '-' character
        var splitElem = elemID.split('-');
        //Parse the last index, of the split method, into an integer
        var postIndex = parseInt(splitElem[1]);
        return postIndex;
    }

    /**********************************************
     * Function: editAnnouncement
     * Description: Function that edits the announcement already posted on a double click event
     * Parameter(s): int, title = 0
     * Return value(s): NONE
     ***********************************************/
    function editAnnouncement(element){
        var postIndex =getPostIndex(event.target);
        var elemEvent = event.target;
        var elemID = $(elemEvent).attr('id');
        var elemParent = $(elemEvent).parent().attr('id');
        var message = post[postIndex].message;
        var title = post[postIndex].title;
        var date = post[postIndex].endtime;

        if(element == 1){
            $('#'+elemID).remove();
            $('#'+elemParent).append("<textarea id='editMessage-"+ postIndex +"' class='edit' style='height: 50px; width: 85%' maxlength='800'></textarea>");
            $('#editMessage-' + postIndex).text(specialCharReplace(message));
        }
        if(element == 0){
            $('#'+elemID).remove();
            $('#'+elemParent).prepend("<h2><input id='editTitle-"+ postIndex +"' class='edit'></h2>");
            $('#editTitle-'+postIndex).val(specialCharReplace(title));

        }

            var editLength = $("#updateAnnouncement-"+postIndex).length;
            if(editLength <= 0) {
                $('#'+elemParent).append("<p id = 'textEdit-"+ postIndex +"' style='margin: 3% 2% -4% 2%;'>Expiration Date: <input id='editDeleteDate-"+ postIndex +"' style='width: 40%; text-align: center' value=" + date + "></p>" + "<br>" +
                    "<input id = 'updateAnnouncement-"+ postIndex +"' class='bluebtn' type ='submit' onclick='updateAnnouncement("+ postIndex +")' value='Edit' style='margin-top: 5%'>" +
                "<input id = 'cancelEdit-" + postIndex + "' class='bluebtn' type='button' onclick='dismissEdit(event.target, post[" + postIndex + "].title, post[" + postIndex + "].message)' value='Cancel'>");
                $(function () {
                    $("#editDeleteDate-"+postIndex).datepicker();
                });
            }
    }

    /**********************************************
     * Function: updateAnnouncement
     * Description: Function that is triggered when the Edit submit button is clicked, which sets the new announcement information
     * Parameter(s): parent int, post index of the targeted element
     * Return value(s): NONE
     ***********************************************/
    function updateAnnouncement(parent) {
        //Array of childrens, or components, elements of the post container

        childrens = $('#post-' + parent).children();
        var target = event.target;
        var postIndex = getPostIndex(target);

        //Identify which elements are in editing mode by obtaining their ids.
        titleID = childrens[0].id;
        messageID = childrens[1].id;


        //Title is in h2 tag
        if(titleID == "title-"+postIndex)
            var title = $('#title-' + postIndex).text();

        //Title is in input tag for editing
        else
            var title = $('#editTitle-'+postIndex).val();

        //Message is in p tag
        if(messageID == "message-"+postIndex)
            var message = $('#message-' + postIndex).text();
        //Message is in textarea tag for editing
        else
            var message = $('#editMessage-' + postIndex).val();

        //Expiration date value
        var date = $('#editDeleteDate-' + postIndex).val();
        //User id value
        var user = '<?php echo $userID ?>';
        //Announcement ID value
        var announcementID = post[postIndex].announcementID;

//Posted JSON object
        var announcementDataEdit = {
            "title": title,
            "message": message,
            "endDate": date,
            "user": user,
            "announcementID": announcementID,
            "action": 2
        };

        $.ajax({
            type: 'post',
            url: "announcement_processing.php",
            data: announcementDataEdit,
            success: function(data) {
                dismissEdit(target, title, message);
            }
        });
    }

    /**********************************************
     * Function: dismissCreate
     * Description: Function that is triggered when a new announcement has been submitted for post it appends the post
     * announcement with the new submitted announcement information.
     * Parameter(s): i int, title string, message string; i: post index, title: title of the post, message: message of the post
     * Return value(s): NONE
     ***********************************************/
    function dismissCreate(i, title, message){
        $("#post").append("<div id = 'post-" + i + "' class='anno'><h2 id ='title-" + i + "' ondblclick='editAnnouncement(" + 0 + ")'></h2><p id='message-" + i + "' ondblclick='editAnnouncement(" + 1 + ")' style='padding: 5%;'></p></div> ");
        $("#title-" + i).html(title);
        $("#message-" + i).html(message);
    }
    /**********************************************
     * Function: dismissEdit
     * Description: Function that is triggered when a new announcement has been edited it appends the post
     * announcement with the new edited announcement information.
     * Parameter(s): (target) element object, (title) string, (message) string; target: targeted element of the event, title: title of the post, message: message of the post
     * Return value(s): NONE
     ***********************************************/
    function dismissEdit(target, title, message) {
        var postIndex = getPostIndex(target);
        $('#annoCreate').remove();
        $('#editTitle-' + postIndex).remove();
        $('#editMessage-' + postIndex).remove();
        $('#editDeleteDate-' + postIndex).remove();
        $('#updateAnnouncement-' + postIndex).remove();
        $('#cancelEdit-' + postIndex).remove();
        $('#textEdit-' + postIndex).remove();
        $('#title-' + postIndex).remove();
        $('#message-' + postIndex).remove();

            //A div is appended to a div id = post with a title header and message
                $("#post-"+postIndex).prepend("<h2 id ='title-" + postIndex + "' ondblclick='editAnnouncement(" + 0 + ")'></h2><p id='message-" + postIndex + "' ondblclick='editAnnouncement(" + 1 + ")' style='padding: 5%;'></p></div> ");
                $("#title-" + postIndex).html(title);
                $("#message-" + postIndex).html(message);
    }

    /***************************************************
     * </A    N   N   O   U   N   C   E   M   E   N   T>
     ***************************************************/
</script>

<!-- Page Style -->
<style>

    #thetable {min-height:100% !important;}

    #lblUsername{
        text-decoration: underline;
    }

    .tg-0za1:hover{
        opacity: 0.95;
        box-shadow: none;
        outline: solid;
        outline-width: 1.0px;
        outline-color: #A8A8A8;
    }
    .tg-zhyu:hover{
        opacity: 0.95;
        box-shadow: none;
        outline: solid;
        outline-width: 1.0px;
        outline-color: #A8A8A8;
    }
    h2{
        font-size: 15px !important;
        font-family: sans-serif;
    }
    nav{margin: 12px 0px 40px 16px !important;}
    .tg  {border-collapse:collapse;border-spacing:0; width: 100%}
    #thetable td{font-family:Arial, sans-serif;font-size:14px; padding-top: 0px; padding-left: 0px; overflow:hidden;word-break:normal;}
    #thetable th{width: 1%}
    #thetable .tg-chpy{font-size:20px;font-family:serif !important;;text-align:center;vertical-align:middle; width: 77%; background-color: #f1f1f1; border-radius: 2px;  box-shadow: 0px 0px 3px #0c0c0c;}
    #thetable .tg-0za1{font-size:13px;font-family:serif !important;;vertical-align:top; background-color: #f1f1f1; border-radius: 6; border-width: 0px; box-shadow: 0px 0px 2px #0c0c0c;
        overflow: auto}
    #thetable .tg-yw4l{vertical-align:top; border-style: none}
    #thetable .tg-zhyu{font-size:13px;font-family:serif !important;;vertical-align:top; background-color: #f1f1f1; border-radius: 2px; border-width: 0px; box-shadow: 0px 0px 2px #0c0c0c; width: 55%}
    a:hover {color: white !important; decoration: underline}

    .anno {
         text-align: center;
         background-color: white;
         width: 80% !important;
         height: 200px !important;
         margin-top: 5%;
         margin-left: 10%;
         border-radius: 5%;
         box-shadow: 0px 0px 8px 1px;
     }

    .meet {
        text-align: center;
        background-color: white;
        width: 80% !important;
        height: 150px !important;
        margin-top: 5%;
        margin-left: 10%;
        border-radius: 5%;
        box-shadow: 0px 0px 8px 1px;
    }

</style>

</html>





/**
 * Created by snguyen1 on 1/18/2017.
 */
//Description: Javascript functions and jQuery Events function to handle add-ons or additional features

//Include this into html <head> tag on your web page:
/* <script type="text/javascript" src="../../Master/master.js"></script> */


/*
jQuery Events for Document History add-ons in review.php
*Note: Include this into html <head> tag on your web page:
 <link rel="stylesheet" type="text/css" href="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.css">
 <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.js"></script>
*/
//**********************************************************************/
//jQuery that allows the visibility of the draggable element if checked
$(document).ready(function() {
    //jQuery function that drags the draggable element
    $(function () {
        $("#documentHistory").draggable({helper: 'clone'});
    });
    //$('[data-toggle="tooltip"]').tooltip();
});

//*********************************************************************/

//*********************************************************************
// Takes in a string and check whether the string contains an underscore
// return true if it does
// return false if it does not
function validateFormUnderscore(str)
{

        if(document.getElementById(str).value.includes("_") == true)
        {
            return true;
        }
        else
        {
            return false;
        }

}

// This is for the bug reporting form in the navigation bar
$('#bugReport').submit(function(event) {
    var entry = $(this).serializeArray();
    var data = [];

    // Creating array
    // Looks like this: [url: "http://localhost/Bandocat/Forms/Main/main.php", username: "hreeves", userid: "58", error: "1", errorMessage: "test"]
    for(var i = 0; i < entry.length; i++)
    {
        data[entry[i].name] = entry[i].value;
    }

    $.ajax({
        url: "../../Library/processErrorMessage.php",
        method: "post",
        data: entry,
        success:function(response)
        {
            // 0 is the message
            // 1 is the status
            response = JSON.parse(response);

            // Hiding report modal
            $('#bugReportModal').modal("hide");
            $('#bugReport').trigger("reset");

            $('#answer').text(response[0]);

            // Checking status
            if(response[1] === false)
            {
                $('#answerModalTitle').text("Error!");
            }

            else
            {
                $('#answerModalTitle').text("Success!");
            }

            // Showing modal
            $('#answerModal').modal('show');
        }
    });

    // Preventing default actions of a form
    event.preventDefault();
});



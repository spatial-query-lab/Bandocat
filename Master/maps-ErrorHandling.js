/**********************************************
 Function: handleError
 Description: Error handling for map cataloging
 Parameter(s): data(Serialized array from given form)
 Return value(s): N/A
 ***********************************************/
function handleError(data)
{
    ///////////////////////////// Initializing variables from serialized array /////////////////////////////
    /*************************************** Library Index ***************************************/
    /*var libIndexObj = data.filter(data => (data.name === 'txtLibraryIndex'));
    var libIndexValue = libIndexObj[0].value;*/

    /**************************************** Doc Title ****************************************/
    var docTitleObj = data.filter(data => (data.name === 'txtTitle'));
    var docTitleValue = docTitleObj[0].value;

    /************************************* Map Scale Units *************************************/
    var unitLeftObj = data.filter(data => (data.name === 'unitLeft'));
    var unitRightObj = data.filter(data => (data.name === 'unitRight'));
    var unitLeftValue = unitLeftObj[0].value;
    var unitRightValue = unitRightObj[0].value;

    /**************************************** Doc Medium ****************************************/
    var docMediumObj = data.filter(data => (data.name === 'ddlMedium'));
    var docMediumValue = docMediumObj[0].value;

    /**************************************** Readability ****************************************/
    var docReadabilityObj = data.filter(data => (data.name === 'ddlReadability'));
    var docReadabilityValue = docReadabilityObj[0].value;


    /************************************** Rectifiability **************************************/
    var docRectifiabilityObj = data.filter(data => (data.name === 'ddlRectifiability'));
    var docRectifiabilityValue = docRectifiabilityObj[0].value;

    /**************************************** Front Scan ****************************************/
    var fileUploadObj = data.filter(data => (data.name === 'fileUpload'));
    var fileUploadValue = fileUploadObj[0].value;

    /**************************************** Back Scan ****************************************/
    var fileUploadBackObj = data.filter(data => (data.name === 'fileUploadBack'));
    var fileUploadBackValue = fileUploadBackObj[0].value;


    ///////////////////////////// Variable Initialization Ends Here //////////////////////////////////////

    ////////////////////// Displaying dismissible error messages if needed //////////////////////////////

    // Variables contain words or symbols that have to be checked in user input
    var dashUnderScoreCheck = /-_/g;
    var backCheck = /back|Back/g;
    var correctBackFormat = /\(back\)/g;
    var copyCheck = /copy|Copy/g;

    // Flag to hold error status
    var flag = false; //false = no errors / true = errors

    /*************************************** Library Index ***************************************/
    /*if(libIndexValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Required text field\n'
        errorReport("libraryIndex", message, "danger");
    }
    else if(dashUnderScoreCheck.test(libIndexValue) == false) // if value doesn't contain -_ pattern
    {
        var message = '<strong>ERROR:</strong> File doesn\'t have -_ pattern\n'
        errorReport("libraryIndex", message, "danger");
    }*/

    /*************************************** Doc Title ***************************************/
    if(docTitleValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Document title required\n'
        errorReport("docTitle", message, "danger");
        flag = true;
    }
    /************************************ Map Scale Units ************************************/

    if (unitLeftValue == unitRightValue)
    {
        var message = '<strong>ERROR:</strong> Units cannot be the same\n';
        errorReport("mainScaleDiv", message, "danger");
        flag = true;
    }

    /*************************************** Doc Medium ***************************************/
    if(docMediumValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Please make a selection\n'
        errorReport("docMedium", message, "danger");
        flag = true;
    }

    /*************************************** Readability ***************************************/
    if(docReadabilityValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Please make a selection\n'
        errorReport("readability", message, "danger");
        flag = true;
    }

    /*************************************** Rectifiability ***************************************/
    if(docRectifiabilityValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Please make a selection\n'
        errorReport("rectifiability", message, "danger");
        flag = true;
    }

    /*************************************** Front Scan ***************************************/
    // Variables for easier testing with if statements
    var frontDashUnderCheck = dashUnderScoreCheck.test(fileUploadValue);
    var frontCopyCheck = copyCheck.test(fileUploadValue);
    var frontBackCheck = backCheck.test(fileUploadValue);

    // Function counts the amount of dashes in the given value
    function frontDashCount (fileUploadValue) {

        for (var frontDashCheck = /-/g, frontDashCount = 0; frontDashCheck.test(fileUploadValue); frontDashCount++) ;

        return frontDashCount;
    }

    if(fileUploadValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> File must be uploaded\n'
        errorReport("frontScan", message, "danger");
        flag = true;
    }
    else if(frontDashUnderCheck == false)
    {
        var message = '<strong>ERROR:</strong> File doesn\'t have -_ pattern\n'
        errorReport("frontScan", message, "danger");
        $('#txtLibraryIndex').val(null);
        flag = true;
    }
    else if(frontCopyCheck == true)
    {
        var message = '<strong>ERROR:</strong> File is a copy?\n'
        errorReport("frontScan", message, "danger");
        $('#txtLibraryIndex').val(null);
        flag = true;
    }
    else if(frontDashCount(fileUploadValue) > 1)
    {
        var message = '<strong>ERROR:</strong> File contains more than 1 hyphen\n'
        errorReport("frontScan", message, "danger");
        $('#txtLibraryIndex').val(null);
        flag = true;
    }
    else if(frontBackCheck == true) // if value does have "back"
    {
        var message = '<strong>ERROR:</strong> Wrong File. Possibly Back Scan?\n'
        errorReport("frontScan", message, "danger");
        $('#txtLibraryIndex').val(null);
        flag = true;
    }

    /*************************************** Back Scan ***************************************/
    // Re-initializing variables for further testing
    dashUnderScoreCheck = /-_/g;
    backCheck = /back|Back/g;
    correctBackFormat = /\(back\)/g;
    copyCheck = /copy|Copy/g;

    var backDashUnderCheck = dashUnderScoreCheck.test(fileUploadBackValue);
    var backBackCheck = backCheck.test(fileUploadBackValue);
    var backCopyCheck = copyCheck.test(fileUploadBackValue);
    var correctFormatCheck = correctBackFormat.test(fileUploadBackValue);
    var absentBackScan = false;

    // Function counts the amount of dashes in the given value
    function backDashCount (fileUploadBackValue) {

        for (var backDashCheck = /-/g, backDashCount = 0; backDashCheck.test(fileUploadBackValue); backDashCount++) ;

        return backDashCount;
    }

    if(fileUploadBackValue == "") // if value is empty
    {
        console.log("No back scan...");
        absentBackScan = true;
    }
    else if(backDashUnderCheck == false && backBackCheck == false) // if value doesn't have either
    {
        var message = '<strong>ERROR:</strong> File doesn\'t have -_ pattern or contain back\n'
        errorReport("backScan", message, "danger");
        flag = true;
    }
    else if(backCopyCheck == true)
    {
        var message = '<strong>ERROR:</strong> File is a copy?\n'
        errorReport("backScan", message, "danger");
        flag = true;
    }
    else if(backDashCount(fileUploadBackValue) > 1)
    {
        var message = '<strong>ERROR:</strong> File contains more than 1 dash\n'
        errorReport("backScan", message, "danger");
        flag = true;
    }
    else if(backDashUnderCheck == false) // if value doesn't contain -_ pattern
    {
        var message = '<strong>ERROR:</strong> File doesn\'t have -_ pattern\n'
        errorReport("backScan", message, "danger");
        flag = true;
    }
    else if(backBackCheck == false) // if value doesn't contain "back"
    {
        var message = '<strong>ERROR:</strong> File doesn\'t contain back\n'
        errorReport("backScan", message, "danger");
        flag = true;
    }
    else if(correctFormatCheck == false)
    {
        var message = '<strong>ERROR:</strong> Syntax wrong! Try (back)\n'
        errorReport("backScan", message, "danger");
        flag = true;
    }

    /********************************** Front/Back Scan Check **********************************/
    // This section compares the values from the front and back scan to see if they are the same
    var theFrontScan = fileUploadValue.substr(0,fileUploadValue.length-4);
    var theBackScan = fileUploadBackValue.substr(0,fileUploadBackValue.length-10);

    // Only performs this check if the front and back scan are both present
    if(absentBackScan == false)
    {
        if(theFrontScan != theBackScan)
        {
            var message = '<strong>ERROR:</strong> front and back scan must be the same\n'
            errorReport("frontScan", message, "danger");
            errorReport("backScan", message, "danger");
            flag = true;
        }
    }

    /*************************************** Doc Medium ***************************************/
    if(docMediumValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Required text field\n'
        errorReport("docMedium", message, "danger");
        flag = true;
    }

    /************************************ Map Scale Units ************************************/

    if (unitLeftValue == unitRightValue)
    {
        var message = '<strong>ERROR:</strong> Units cannot be the same\n';
        errorReport("mainScaleDiv", message, "danger");
        flag = true;
    }

    ///////////////////////////////// Error Displaying Ends Here ///////////////////////////////////////

    return flag;
}
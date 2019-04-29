/**********************************************
 Function: handleError
 Description: Error handling for map cataloging
 Parameter(s): data(Serialized array from given form)
 Return value(s): N/A
 ***********************************************/
function handleError(data)
{
    ///////////// Checking to see if form provided has values we want to check for errors ////////////////
    /*************************************** Library Index ***************************************/
    if(data.filter(data => (data.name === 'txtLibraryIndex')).length > 0)
    {
        var libIndexObj = data.filter(data => (data.name === 'txtLibraryIndex'))
        var libIndexValue = libIndexObj[0].value;
    }
    /*************************************** Doc Title ***************************************/
    if(data.filter(data => (data.name === 'txtTitle')).length > 0)
    {
        var docTitleObj = data.filter(data => (data.name === 'txtTitle'))
        var docTitleValue = docTitleObj[0].value;
    }
    /*************************************** Front Scan ***************************************/
    if(data.filter(data => (data.name === 'fileUpload')).length > 0)
    {
        var fileUploadObj = data.filter(data => (data.name === 'fileUpload'))
        var fileUploadValue = fileUploadObj[0].value;
    }
    /*************************************** Back Scan ***************************************/
    if(data.filter(data => (data.name === 'fileUploadBack')).length > 0)
    {
        var fileUploadBackObj = data.filter(data => (data.name === 'fileUploadBack'))
        var fileUploadBackValue = fileUploadBackObj[0].value;
    }
    /*************************************** Doc Medium ***************************************/
    if(data.filter(data => (data.name === 'ddlMedium')).length > 0)
    {
        var docMediumObj = data.filter(data => (data.name === 'ddlMedium'))
        var docMediumValue = docMediumObj[0].value;
    }
    ///////////////////////////////// Value Checking Ends Here //////////////////////////////////////////

    ////////////////////// Displaying dismissible error messages if needed //////////////////////////////

    // Variables contain words or symbols that have to be checked in user input
    var dashUnderScoreCheck = /-_/g;
    var backCheck = /back|Back/g;
    var copyCheck = /copy|Copy/g;

    /*************************************** Library Index ***************************************/
    /*if(libIndexValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Required text field\n'
        errorReport("libraryIndex", message, "danger");
    }*/
     /*if(dashUnderScoreCheck.test(libIndexValue) == false) // if value doesn't contain -_ pattern
    {
        var message = '<strong>ERROR:</strong> File doesn\'t have -_ pattern\n'
        errorReport("libraryIndex", message, "danger");
    }*/

    /*************************************** Doc Title ***************************************/
    if(docTitleValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Required text field\n'
        errorReport("docTitle", message, "danger");
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
        var message = '<strong>ERROR:</strong> Required text field\n'
        errorReport("frontScan", message, "danger");
    }
    else if(frontDashUnderCheck == false)
    {
        var message = '<strong>ERROR:</strong> File doesn\'t have -_ pattern\n'
        errorReport("frontScan", message, "danger");
    }
    else if(frontCopyCheck == true)
    {
        var message = '<strong>ERROR:</strong> File is a copy?\n'
        errorReport("frontScan", message, "danger");
    }
    else if(frontDashCount(fileUploadValue) > 1)
    {
        var message = '<strong>ERROR:</strong> File contains more than 1 dash\n'
        errorReport("frontScan", message, "danger");
    }
    else if(frontBackCheck == true) // if value does have "back"
    {
        var message = '<strong>ERROR:</strong> Wrong File. Possibly Back Scan?\n'
        errorReport("frontScan", message, "danger");
    }




    /*************************************** Back Scan ***************************************/
    // Back scan has to check for "back" and "-_" so I set them to variables here for easier
    // testing with if statements.
    var backDashUnderCheck = dashUnderScoreCheck.test(fileUploadBackValue);
    var backBackCheck = backCheck.test(fileUploadBackValue);
    var backCopyCheck = copyCheck.test(fileUploadBackValue);

    // Function counts the amount of dashes in the given value
    function backDashCount (fileUploadBackValue) {

        for (var backDashCheck = /-/g, backDashCount = 0; backDashCheck.test(fileUploadBackValue); backDashCount++) ;

        return backDashCount;
    }

    if(fileUploadBackValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Required text field\n'
        errorReport("backScan", message, "danger");
    }
    else if(backDashUnderCheck == false && backBackCheck == false) // if value doesn't have either
    {
        var message = '<strong>ERROR:</strong> File doesn\'t have -_ pattern or contain back\n'
        errorReport("backScan", message, "danger");
    }
    else if(backCopyCheck == true)
    {
        var message = '<strong>ERROR:</strong> File is a copy?\n'
        errorReport("backScan", message, "danger");
    }
    else if(backDashCount(fileUploadBackValue) > 1)
    {
        var message = '<strong>ERROR:</strong> File contains more than 1 dash\n'
        errorReport("backScan", message, "danger");
    }
    else if(backDashUnderCheck == false) // if value doesn't contain -_ pattern
    {
        var message = '<strong>ERROR:</strong> File doesn\'t have -_ pattern\n'
        errorReport("backScan", message, "danger");
    }
    else if(backBackCheck == false) // if value doesn't contain "back"
    {
        var message = '<strong>ERROR:</strong> File doesn\'t contain back\n'
        errorReport("backScan", message, "danger");
    }

    /*************************************** Doc Medium ***************************************/
    if(docMediumValue == "") // if value is empty
    {
        var message = '<strong>ERROR:</strong> Required text field\n'
        errorReport("docMedium", message, "danger");
    }
    ///////////////////////////////// Error Displaying Ends Here ///////////////////////////////////////
}
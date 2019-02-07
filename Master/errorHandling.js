//JSON array object of all the input elements of the form by name and value
var form = $('form').serializeArray();
//Maximum numbers of underscore characters
var maxUscr = 0;

/**********************************************
 * Function: errorHandling
 * Description: Function that uses a single input form element to validate its input to the set of procedures guidelines.
 * Parameter(s): element (obj) input element to validate for
 * collection (string) Collection name of the catalog/review document
 * Return value(s): errorJSON (JSON) The JSON object contains the description of the element id and analyzed error massage.
 ***********************************************/
function errorHandling(element, collection) {
    //Element id
    var name = element[0].id;
    //Element value
    var value = element[0].value;
    //errorArray contains the results of all the validations in boolean format
    var errorArray = [];
    //Returned value with the element id and error message
    var errorJSON = {desc: []};
    //The type of error collection to validate for
    var eCollection = '';

    //Switch statement that calls the collection function for validation. Also, it sets the maximum amount
    //of underscores that you can have for collection in the libray index
    switch (collection) {
        case 'bluchermaps':
            eCollection = 'largemap';
            maxUscr = 1;
            largeMap();
            break;
        case 'greenmaps':
            eCollection = 'largemap';
            maxUscr = 1;
            largeMap();
            break;
        case 'jobfolder':
            eCollection = 'jobfolder';
            maxUscr = 2;
            jobfolder();
            break;
        case 'blucherfieldbook':
            eCollection = 'fieldbook';
            fieldbook();
            break;
    }

    /**********************************************
     * Function: jobfolder
     * Description: Function that validates for jobfolder collections
     * Parameter(s): element (obj) input element to validate for
     * collection (string) Collection name of the catalog/review document
     * Return value(s): errorJSON (JSON) The JSON object contains the description of the element id and analyzed error massage.
     ***********************************************/
    function jobfolder() {
        //Folder Covers
        //Parenthesis regex
        rePtheChar = /(\(|\))/g;
        //Test for parenthesis
        if (rePtheChar.test(value)) {
            //Test for two parenthesis if fails error message is stored to errorJSON
            if (value.match(rePtheChar).length > 1) {
                //Test for envelope characters
                envlChar(name, value, errorJSON, errorArray);
                //Test for back characters
                backChar(name, value, eCollection, errorJSON, errorArray)
            }
            else {
                //Error message
                var ems = 'The ' + name + ' does not contain the right amount of parenthesis';
                //Highlight function
                highlight(name);
                //constructs the error JSON object
                errorObjects(name, ems, errorJSON, errorArray)
            }
        }

        //Folder Documents
        //Test for no parenthesis
        else if (!rePtheChar.test(value)) {
            //Test for whitespace
            whiteSpace(name, value, errorJSON, errorArray);
            //Test for special characters
            unrecognizedChar(name, value, errorJSON, errorArray);
            //Test for dash
            dashChar(name, value, errorJSON, errorArray);
            //Test for Under Score
            uscrChar(name, value, errorJSON, errorArray);
            //Test for dot character
            dotChar(name, value, errorJSON, errorArray);
            //Test for back characters
            backChar(name, value, eCollection, errorJSON, errorArray)
        }
    }

    /**********************************************
     * Function: largeMap
     * Description: Function that validates for large maps collections
     * Parameter(s): element (obj) input element to validate for
     * collection (string) Collection name of the catalog/review document
     * Return value(s): errorJSON (JSON) The JSON object contains the description of the element id and analyzed error massage.
     ***********************************************/
    function largeMap() {
        //Test for special characters
        unrecognizedChar(name, value, errorJSON, errorArray);
        //Validates between library index and scale form inputs
        switch (name){
            case 'txtLibraryIndex':
                //Test for white space
                whiteSpace(name, value, errorJSON, errorArray);
                //Test for undesr score characters
                uscrChar(name, value, errorJSON, errorArray);
                //Test for dash characters
                dashChar(name, value, errorJSON, errorArray);

                //Parenthesis regex
                var rePtheChar = /(\(|\))/g;
                //Back regex
                var reBackChar = /[back]/g;
                //Parenthesis flag
                var ptheFlag = rePtheChar.test(value);
                //Back character array flag
                var backFlag = reBackChar.test(value);
                //If there is a back
                if(backFlag) {
                    //But no parenthesis
                    if(!ptheFlag) {
                        var ems = 'The ' + name + ' is missing a set of parenthesis within the back characters';
                        highlight(name);
                        errorObjects(name, ems, errorJSON, errorArray)
                    }
                }
                //Test for parenthesis
                if (ptheFlag) {
                    //Test for two parenthesis
                    pthesisNum = value.match(rePtheChar).length % 2;
                    if (pthesisNum == 0) {
                        //Test for back
                        backChar(name, value, eCollection, errorJSON, errorArray);

                    }
                    //If uneven amount of parenthesis
                    else {
                        var ems = 'The ' + name + ' does not contain the right amount of parenthesis';
                        highlight(name);
                        errorObjects(name, ems, errorJSON, errorArray)
                    }
                }
                break;


            case 'txtMapScale':
                //Test for scale
                scale(name, value, errorJSON, errorArray);
                break;
        }
    }

    /**********************************************
     * Function: fieldbook
     * Description: Function that validates for the field book collection
     * Parameter(s): element (obj) input element to validate for
     * collection (string) Collection name of the catalog/review document
     * Return value(s): errorJSON (JSON) The JSON object contains the description of the element id and analyzed error massage.
     ***********************************************/
    function fieldbook() {
        //Test for white space
        whiteSpace(name, value, errorJSON, errorArray);
        //Test for dash characters
        dashChar(name, value, errorJSON, errorArray)
    }

    /*IMPORTANT*/
    //Runs through all the validations tests and if a true error value is found the errorJSON answer will be true.
    //The errorJSON is returned with the function answer for error and the error message.
    for(var e = 0; e < errorArray.length; e++) {
        if(errorArray[e] == true){
            errorJSON.answer = true;
            return errorJSON
        }
        else
            errorJSON.answer = false;
    }
    return errorJSON
}

/**********************************************
 * Function: highlight
 * Description: Function that highlights the element with an error
 * Parameter(s): elemName (string) element id
 * Return value(s): None
 * **********************************************/
function highlight(elemName) {
    $('#'+elemName).css('outline-style', 'solid').css('outline-color', 'orange');
}

/**********************************************
 * Function: errorObjects
 * Description: Functions that constructs a JSON error object with the function answer to the validation and the error
 * message
 * Parameter(s): name (string) element name
 * ems (string) Error message
 * errorJSON (JSON object) JSON format {desc: []}
 * errorArray (array) Push a true error test value to the array
 * Return value(s): None
 * **********************************************/
function errorObjects(name, ems, errorJSON, errorArray) {
    errorJSON['desc'].push({'elemName': name, 'message': ems});
    errorArray.push(true);
}

/**********************************************
 * Function: whiteSpace
 * Description: Function that test the existence of white space characters in the input value
 * Parameter(s): name (string) element name
 * value (string) element value
 * errorJSON (JSON object) JSON that contains the error description and message
 * errorArray (array) Array that contains the test results
 * Return value(s): None
 * **********************************************/
function whiteSpace(name, value, errorJSON, errorArray){
    //white space regex
    var reWhiteSpace = /\s/g;
    //If the input value contains a white space an error message is stored to the errorJSON with the element name
    if (reWhiteSpace.test(value)) {
        var ems = 'The ' + name + ' contains white space; review string for empty spaces';
        highlight(name);
        errorObjects(name, ems, errorJSON, errorArray);
    }
    //Otherwise, no white spaces were found
    else
        errorArray.push(false);
}

/**********************************************
 * Function: dashChar
 * Description: Function that test the existence of dash characters in the input value
 * Parameter(s): name (string) element name
 * value (string) element value
 * errorJSON (JSON object) JSON that contains the error description and message
 * errorArray (array) Array that contains the test results
 * Return value(s): None
 * **********************************************/
function dashChar(name, value, errorJSON, errorArray) {
    //Dash character regex
    var reDashChar = /[-]/g;
    //If the input value contains a dash no error
    if (reDashChar.test(value)) {
        errorArray.push(false);
    }
    //Otherwise the input value is missing a dash character and an error is stored to the errorJSON with the element name
    else{
        var ems = 'The ' + name + ' is missing a dash (-) character';
        highlight(name);
        errorObjects(name, ems, errorJSON, errorArray)
    }
}

/**********************************************
 * Function: uscrChar
 * Description: Function that test the existence of under score characters in the input value
 * Parameter(s): name (string) element name
 * value (string) element value
 * errorJSON (JSON object) JSON that contains the error description and message
 * errorArray (array) Array that contains the test results
 * Return value(s): (boolean) if the maximum number of under scores was reached
 * **********************************************/
function uscrChar(name, value, errorJSON, errorArray) {
    //Under score regex
    var reUscrChar = /[_]/g;
    //If the input value contains an under score no error
    if (reUscrChar.test(value)) {
        errorArray.push(false);
        //Under score and dash regex
        var unsrDashChar = /_-/g;
        //Under score and dash flag
        unsrDashFlag = unsrDashChar.test(value);
        //If the input value contains an under score and dash an error is stored to the errorJSON with the element name
        if(unsrDashFlag){
            var ems = "The " + name + ' element dividers are disarranged; proper order (-_)';
            highlight(name);
            errorObjects(name, ems, errorJSON, errorArray);
        }
        //Number of under scores in the input value
        var uscrNum = value.match(reUscrChar).length;
        //If the number of under scores is greater than the number of maximum number of under scores an error is stored
        //to the errorJSON with the element name
        if(uscrNum > maxUscr) {
            var ems = "The " + name + ' contains a maximum number of under score (_) characters';
            highlight(name);
            errorObjects(name, ems, errorJSON, errorArray);
        }
        //If the number of under scores is equal to the number of maximum underscores true is returned
        else if(uscrNum == maxUscr) {
            return true
        }
        //Otherwise false
        else
            return false
    }
    //Otherwise the input value is missing an under score character and an error is stored into the errorJSON with the
    // element name
    else{
        var ems = 'The ' + name + ' is missing an under score (_) character';
        highlight(name);
        errorObjects(name, ems, errorJSON, errorArray);
    }
}

/**********************************************
 * Function: envlChar
 * Description: Function that test the existence of an envelope word in the input value
 * Parameter(s): name (string) element name
 * value (string) element value
 * errorJSON (JSON object) JSON that contains the error description and message
 * errorArray (array) Array that contains the test results
 * Return value(s): None
 * **********************************************/
function envlChar(name, value, errorJSON, errorArray) {
    //Envelope regex
    var reEnvlChar = /envlope/g;
    //If the input value contains an envelope word
    if(reEnvlChar.test(value)){
        //Test for white space
        whiteSpace(name, value, errorJSON, errorArray);
        //Test for special characters
        unrecognizedChar(name, value, errorJSON, errorArray);
        //Test for dash characters
        dashChar(name, value, errorJSON, errorArray);
        errorArray.push(false);
    }
    //Otherwise, the input value contains the wrong envelope characters and an error is stored into the errorJSON
    //with the element name
    else{
        var ems = 'The ' + name + ' contains an error or is missing the envelope name';
        highlight(name);
        errorObjects(name, ems, errorJSON, errorArray)
    }
}

/**********************************************
 * Function: backChar
 * Description: Function that test the existence of a back character array input value
 * Parameter(s): name (string) element name
 * value (string) element value
 * errorJSON (JSON object) JSON that contains the error description and message
 * errorArray (array) Array that contains the test results
 * Return value(s): None
 * **********************************************/
function backChar(name, value, collection, errorJSON, errorArray) {
    /*********************************************
     * Two different types of back inputs types;
     * jobfolder: _back
     * largemaps: (back)
    ***********************************************/
    if(collection == 'jobfolder'){
        //The jobfolder second under score character is followed by back characters
        var underScoreMax = uscrChar(name, value, errorJSON, errorArray);
        //If the maximum number of under scores
        if(underScoreMax){
            //Back regex
            var reBackChar = /[back]/g;
            //Back flag
            var backFlag = reBackChar.test(value);
            //If the input value contains a back array characters
            if(backFlag){
                errorArray.push(false);
                //If the input value contains the four back characters
                if(value.match(reBackChar).length == 4)
                    errorArray.push(false);
                //Otherwise, the input value contains the wrong back characters and an error is stored into the errorJSON
                else{
                    var ems = 'The ' + name + ' contains an error in the back name';
                    highlight(name);
                    errorObjects(name, ems, errorJSON, errorArray)
                }
            }
            //Otherwise, the input value is missing the back characters after under score and the error is stored into
            //the errorJSON
            else{
                var ems ='The ' + name + ' is missing back characters after under score character (_)';
                highlight(name);
                errorObjects(name, ems, errorJSON, errorArray)
            }
        }
        //Otherwise, the input value is missing the under score character before back and the error is stored into the
        //errorJSON
        else
        {
            //Back array regex
            var reBackChar = /[back]/g;
            //Back array flag test
            var backFlag = reBackChar.test(value);
            //If not the maximum number of under score and back characters then there is a missing under score an the
            //error is stored into the errorJSON
            if(backFlag) {
                var ems ='The ' + name + ' is missing an underscore character (_) before back';
                highlight(name);
                errorObjects(name, ems, errorJSON, errorArray)
            }
            else
                errorArray.push(false);
        }
    }
    if(collection == 'largemap'){
        //Back array regex
        var reBackChar = /[back]/g;
        //Back array flag test
        var backFlag = reBackChar.test(value);
        //If the input value contains any back characters
        if(backFlag){
            errorArray.push(false);
            //if the input contains four back array indexes
            if(value.match(reBackChar).length == 4)
                errorArray.push(false);
            //Otherwise, if the input contains less or more than the four back characters then the error is stored into
            //errorJSON
            else{
                var ems = 'The ' + name + ' contains an error in the back name';
                highlight(name);
                errorObjects(name, ems, errorJSON, errorArray)
            }
        }
    }
}

/**********************************************
 * Function: dotChar
 * Description: Function that test the existence of a dot character in the input value form and test for the In a subfolder
 * selection to test for a valid input
 * Parameter(s): name (string) element name
 * value (string) element value
 * errorJSON (JSON object) JSON that contains the error description and message
 * errorArray (array) Array that contains the test results
 * Return value(s): None
 * **********************************************/
function dotChar(name, value, errorJSON, errorArray) {
    //Loops through all the input elements in the form
    for(var e = 0; e < form.length; e++) {
        //Input form Element name
        var elementName = form[e].name;
        //Input form Element value
        var elementValue = form[e].value;
        //In the loop, if the element name is In a subfolder radio input
        if(elementName == 'rbInASubfolder'){
            //If radio input value is 1
            if(elementValue == '1'){
                //dot regex
                var reDotChar = /\./g;
                //dot flag test
                var flag = reDotChar.test(value);
                //If dot found no error
                if(flag){
                    errorArray.push(false);
                }
                //Otherwise, no dot found an error is stored into the errorJSON
                else{
                    var ems = 'The ' + name + ' is missing a dot (.) character';
                    highlight(name);
                    errorObjects(name, ems, errorJSON, errorArray)
                }

            }
            //If In a subfolder selected
            else{
                var reDotChar = /\./g;
                var flag = reDotChar.test(value);
                //If dot character not found in the input value the error is stored into the errorJSON
                if(flag){
                    var ems = 'The ' + name + ' should not contain a dot (.) character because it has not being selected as a subfolder';
                    highlight(name);
                    errorObjects(name, ems, errorJSON, errorArray)
                }
                else
                    errorArray.push(false);
            }
        }
    }
}

/**********************************************
 * Function: unrecognizedChar
 * Description: Function that test the existence of a special character in the input value
 * Parameter(s): name (string) element name
 * value (string) element value
 * errorJSON (JSON object) JSON that contains the error description and message
 * errorArray (array) Array that contains the test results
 * Return value(s): None
 * **********************************************/
function unrecognizedChar(name, value, errorJSON, errorArray) {
    //Primarily unrecognized special characters
    reUnrecChar = /[!@#$%^&*;'"{}[\]+`~]/g;
    //Special characters flag test
    var unrecFlag = reUnrecChar.test(value);
    //If the input value contains unrecognized special characters the error is stored into the errorJSON
    if(unrecFlag) {
        var ems = 'The ' + name + ' contains unrecognized special characters';
        highlight(name);
        errorObjects(name, ems, errorJSON, errorArray)
    }
    else
        errorArray.push(false);
}

/**********************************************
 * Function: scale
 * Description: Function that test the input value for the scale element input
 * Parameter(s): name (string) element name
 * value (string) element value
 * errorJSON (JSON object) JSON that contains the error description and message
 * errorArray (array) Array that contains the test results
 * Return value(s): None
 * **********************************************/
function scale(name, value, errorJSON, errorArray){
    //If the input value is empty no validation is need it
    if(value == "")
        errorArray.push(false);
    //Otherwise, if the input is not empty
    else {
        //Scale regex unit measure allowed prefixes
        reInch = /[ch]/gi;
        reFeet = /[e]/gi;
        reFoot = /[o]/gi;
        reVaras = /varas/gi;
        reYards = /yard/gi;
        reMiles = /[le]/gi;

        //unit measure prefixes tests
        inchFlag = reInch.test(value);
        feetFlag = reFeet.test(value);
        footFlag = reFoot.test(value);
        varasFlag = reVaras.test(value);
        yardFlag = reYards.test(value);
        milesFlag = reMiles.test(value);

        //If the prefix is not identified an error is stored into the errorJSON
        /**********************
         * 1.Inch           4.Varas
         * 2.Feet           5.Yards
         * 3.Foot           6.Miles
         **********************/
        //1
        if(inchFlag) {
            var ems = 'The ' + name + ' inch must be abbreviated to in';
            highlight(name);
            errorObjects(name, ems, errorJSON, errorArray)
        }
        else
            errorArray.push(false);
        //2
        if(feetFlag) {
            var ems = 'The ' + name + ' feet must be abbreviated to ft';
            highlight(name);
            errorObjects(name, ems, errorJSON, errorArray)
        }
        else
            errorArray.push(false);
        //3
        if(footFlag) {
            var ems = 'The ' + name + ' feet must be abbreviated to ft';
            highlight(name);
            errorObjects(name, ems, errorJSON, errorArray)
        }
        else
            errorArray.push(false);
        //4
        if(varasFlag) {
            var ems = 'The ' + name + ' varas must be abbreviated to vars';
            highlight(name);
            errorObjects(name, ems, errorJSON, errorArray)
        }
        else
            errorArray.push(false);
        //5
        if(yardFlag) {
            var ems = 'The ' + name + ' yard must be abbreviated to yd';
            highlight(name);
            errorObjects(name, ems, errorJSON, errorArray)
        }
        else
            errorArray.push(false);
        //6
        if(milesFlag) {
            var ems = 'The ' + name + ' varas must be abbreviated to mi';
            highlight(name);
            errorObjects(name, ems, errorJSON, errorArray)
        }
        else
            errorArray.push(false);
    }
}
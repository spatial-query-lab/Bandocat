//Global Variables
var rectangleSelected = false;			//boolean keeping track of if the user has an active rectangle placed on the viewer 
var rectangleArray = new Array();		// array containing all the rectangles drawn by drawRectangles funciton

//This function calls drawRectanglesLeaflet.php and then calls drawRectangles() with JSON returned by drawRectanglesLeaflet.php as parameter.
function getRectangleCoords()
{
	var collection = document.getElementById('Collection').value;
	var fileName = window.localStorage.getItem('fileName');
	var docID = window.localStorage.getItem('docID');
	var rectangleCoords;
	
	$.ajax({
		type: 'post',
		url: 'php/drawRectanglesLeaflet.php',
		data: {"fileName": fileName, "docID": docID,"collection": collection},
		success:function(data){
			if(data == "") return;
			else {
                rectangleCoords = JSON.parse(data);
                drawRectangles(collection, rectangleCoords);
            	}
			}
		});


}

//This function draws rectangles on document viewer window using the JSON passed to it from getRectangleCoords() 
//it also initiates the click event on each drawn rectangle. 
function drawRectangles(collection,coords)
{
	var latlng = new Array();
	var latlng2 = new Array();
	
	//iterate through entire coords array unprojecting values and storing them 
	var i = 0;
	while(coords[i] != null)
	{
		latlng.push(rc.unproject([coords[i].x1, coords[i].y1]));
		latlng2.push(rc.unproject([coords[i].x2, coords[i].y2]));
		i++;
	}
	
	//iterate through entire coords array
	var j = 0;
	while(coords[j] != null)
	{
		var entryData; // This may not be in use anymore 
		//create a rectangle object based on unprojected values of coords
		//add that rectangle object to viewer
		var rectangle = L.rectangle([[latlng[j].lat, latlng[j].lng],
		[latlng2[j].lat, latlng2[j].lng]],{color: "	 #58d68d", weight: 1});
		rectangleArray.push(rectangle);
		map.addLayer(rectangleArray[j]);
		
		//click function that highlights clicked object and calls getEntryData.php 
		//passes JSON from getEntryData.php as parameter to displayEntryData()
		rectangle.on('click', function()
		{ 
			// if the user has a marker drawn on the map exit click function early 
			if(markerCount > 0)
				return;
			
			rectangleSelected = true; 
			
			var highlight = {color: "#FF0000", weight: 1};
			var defaultColor = {color: "#58d68d", weight: 1};			

			for(var i = 0; i < rectangleArray.length; i++)
			{
				rectangleArray[i].setStyle(defaultColor);
			}
			
			this.setStyle(highlight);

			removeTags("Client_Table");
			removeTags("RelatedPaper_Table");
			removeTags('JobNumber_Table');
			
			//get projected bounds latlng values of clicked rectangle to be used for querying database 
			point1 = rc.project(this.getBounds()._southWest);
			point2 = rc.project(this.getBounds()._northEast);
			//initiates entryObject with coordinates and path of selected rectangle. 
			//for use in getEntryData.php to query for other entry field date.
			entryObject = addEntryObject(collection,docID,point1.x, point1.y, point2.x, point2.y, fileName);
			$.ajax({
				type: 'post',
				url: 'php/getEntryData.php',
				data: {"entryObject": JSON.stringify(entryObject)},
				success:function(data){
					entryData = JSON.parse(data);
					displayEntryData(entryData);
				}
			});
		});
		j++;
	}
}

//This function takes entryData JSON as parameter and writes values to appropriate HTML form location.
function displayEntryData(entryData)
{
	//write entry data to form
	document.getElementById("Document_ID").value = entryData[0].documentID;
	//document.getElementById("File_Name").value = entryData[0].Document;
	document.getElementById("Entry_Coordinates").value = "SouthWest(" + entryData[0].x1 + ", " + entryData[0].y1 + ")"
		+ " " + "NorthEast(" + entryData[0].x2 + ", " +  entryData[0].y2 + ")";
	document.getElementById("Survey_Or_Section").value = entryData[0].surveyorsection;
	document.getElementById("Block_Or_Tract").value = entryData[0].blockortract;
	document.getElementById("Lot_Or_Acres").value = entryData[0].lotoracres;
	document.getElementById("Description").value = entryData[0].description;
	displayClientTableInfo(entryData[0].client, "Client_Table");
	displayFieldBookTableInfo(entryData[0].fieldbookinfo, "Field_Book_Table");
	displayRelatedPaperTableInfo(entryData[0].relatedpapersfileno, "RelatedPaper_Table");
	displayMapTableInfo(document.getElementById("Collection").value,entryData[0].mapinfo, "Map_Table");
	displayDateTableInfo(entryData[0].date, "Date_Table");
	displayJobNumberTableInfo(entryData[0].jobnumber, "JobNumber_Table");

	//document.getElementById("Field_Book_Info").value = entryData[0].Field_Book_Info;
	document.getElementById("Related_Papers_Info").value = entryData[0].relatedpapersfileno;


	/*document.getElementById("Map_Info").value = entryData[0].Map_Info;
	document.getElementById("Date").value = entryData[0].month, entryData[0].day, entryData[0].year;
	document.getElementById("Map_Table_Info").value = entryData[0].mapinfo;

	var Time = entryData[0].date.split("-");
	if (Time[1] == 00)
	{
		document.getElementById("Month").value = "Month";
	}
	else
	{
		var Month = parseInt(Time[1]);
		document.getElementById("Month").value = Month;
	}

	if (Time[2] == 00)
	{
		document.getElementById("Day").value = "Day";
	}
	else
	{
		var Day = parseInt(Time[2]);
		document.getElementById("Day").value = Day;
	}

	if (Time[0] == 0000)
	{
		document.getElementById("Year").value = "Year";
	}
	else
	{
		var Year = parseInt(Time[0]);
		document.getElementById("Year").value = Year;
	}*/

	document.getElementById("Job_Number").value = entryData[0].jobnumber;
	document.getElementById("Comments").value = entryData[0].comments;

	//write to hidden forms
	document.getElementById("x1").value = entryData[0].x1;
	document.getElementById("y1").value = entryData[0].y1;
	document.getElementById("x2").value = entryData[0].x2;
	document.getElementById("y2").value = entryData[0].y2;
}

function removeTags(id) {
	var clientsTag = $("#"+id).siblings("#"+id+"_tagsinput").children(".tag");
		for(var i=0; i < clientsTag.length; i++) {
			var selecTags = [];
			selecTags.push($(clientsTag[i]).text().substr(0, $(clientsTag[i]).text().length - 1).trim());
			$("#" + id).removeTag(selecTags);
		}
}

/*************************************************
 ****** DISPLAY FUNCTIONS ON RECTANGLE CLICK *****
 *************************************************/

/*************************************************************************
 * CLIENT
 * @param string, id;
 * Results: On rectangle click triggers an event that listens to a div change
 * that removes any tags in the container and adds the clients stored in that
 * rectangle as tags.
 */

function displayClientTableInfo(string, id) {
	var object = JSON.parse(string);

	for(var i = 0 ; i < object.length; i++) {
		$("#"+id).addTag(object[i].client);
	}
}

/*************************************************************************
 * FIELD BOOK
 * @param string, id;
 * Results: On rectangle click a function is called that deletes the Field
 * book table's rows, and creates the field book number and page stored in
 * that rectangle as rows.
 */
function displayFieldBookTableInfo(string, id)
{
	//creates JSON from JSON string that is passed to it in the "string" parameter
	object = JSON.parse(string);
	var table = document.getElementById(id);

	deleteTable(id);

	for(var i = 0 ; i < object.length; i++)
	{
		//if the table is already the correct size leave for loop
		if(table.rows.length-1 == object.length)
			break;

		//inserts rows and populates cells with values
		var row = table.insertRow(-1)
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);

		cell1.innerHTML = '<input type="text" class= "Input_Field"' + 'value = "' + object[i].bookNumber + '">';
		cell2.innerHTML = '<input type="text" class= "Input_Field"' + 'value = "' + object[i].pageNumbers + '">';
	}
}

/*************************************************************************
 * RELATED PAPERS
 * @param string, id;
 * Results: On rectangle click a function is called that deletes the Related
 * papers table's rows, and creates new rows with the information of the
 * related papers input with the related papers' values.
 */
function displayRelatedPaperTableInfo(string, id) {
	var object = JSON.parse(string);

	for(var i = 0 ; i < object.length; i++) {
		$("#"+id).addTag(object[i].relatedpapersfileno);
	}
}
/*************************************************************************
 * MAP
 * @param string, id;
 * Results: On rectangle click a function is called that deletes the map
 * table's rows, and creates new rows with the information of the
 * maps' input values.
 */
function displayMapTableInfo(collection,string, id)
{
	object = JSON.parse(string);
	var table = document.getElementById(id);

	deleteTable(id);

//$.get("php/MapKind_Options.php",{collection: collection})
	for(var i = 0 ; i < object.length; i++)
	{
		//if the table is already the correct size leave for loop
		if(table.rows.length-1 == object.length)
			break;

		$.ajax({
			url: 'php/MapKind_Options.php', //This is the current doc
			type: "POST",
			//dataType:'json', // add json datatype to get json
			data: {"collection": collection,"mapKind": object[i].mapKind,"mapNumber": object[i].mapNumber,"id": i},
			success: function(data) {
				table.innerHTML += data;
			}
		});
	}
}

/************************************************************************************
 * DISPLAY DATE ON CLICK FOR THE FIRST SET OF SELECT ELEMENTS
 * ***********************************************************************************/
function displayDateTableInfo(string, id) {
	deleteTable(id);

    var dateJSON = JSON.parse(string);

        // for(var i = 0; i < dateJSON.length-1; i++) {
        //
        //     var dateStr = dateJSON[i].Date;
        //     var dateArr = dateStr.split("/");
        //     var monthOptions = document.getElementById("Month").childNodes;
        //     for(i=0;i<monthOptions.length;i++) {
			// 	if (monthOptions[i].value == dateArr[0]){
			// 		monthOptions[i].selected = true;
			// 	}
        //     }
        //     var dayOptions = document.getElementById("Day").childNodes;
        //     for(i=0;i<dayOptions.length;i++) {
        //         if (dayOptions[i].value == dateArr[1]){
        //             dayOptions[i].selected = true;
        //         }
        //     }
        //     var yearOptions = document.getElementById("Year").childNodes;
        //     for(i=0;i<yearOptions.length;i++) {
        //         if (yearOptions[i].value == dateArr[2]){
        //             yearOptions[i].selected = true;
        //         }
        //     }
        // }
    /************************************************************************************
     * MONTH
     * ***********************************************************************************/
       // if(dateJSON.length > 1) {
        for(var i = 0; i < dateJSON.length; i++) {
            var dateStr = dateJSON[i].Date;
            var dateArr = dateStr.split("/");

            var table = document.getElementById(id);
            var row = table.insertRow(-1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);

			var ddlmonth = "<select name = 'monthStart' id = 'Month' class='MonthDate' style = 'width:75px;'>"
			ddlmonth += "<option value='00'>Month</option>";
			for(var j = 1; j <= 12; j++)
			{
				var temp = "";
				curmonth = j.toString();
				if(j < 10)
				{
					curmonth = '0' + j.toString();
				}
				if(curmonth == dateArr[0])
				{
                    temp = "<option value='" + curmonth + "' selected>" + curmonth + "</option>";
				}
				else temp = "<option value='" + curmonth + "'>" + curmonth + "</option>";

				ddlmonth += temp;
			}
			ddlmonth += "</select>";

			/************************************************************************************
			 * DAY
			 * ***********************************************************************************/

            var ddlday = "<select name = 'dayStart' id = 'Day' class='DayDate' style = 'width:60px;'>"

			ddlday += "<option value='00'>Day</option>";
            for(var j = 1; j <= 31; j++)
            {
                var temp = "";
                curday = j.toString();
                if(j < 10)
                {
                    curday = '0' + j.toString();
                }
                if(curday == dateArr[1])
                {
                    temp = "<option value='" + curday + "' selected>" + curday + "</option>";
                }
                else temp = "<option value='" + curday + "'>" + curday + "</option>";

                ddlday += temp;
            }

            /************************************************************************************
             * YEAR
             * ***********************************************************************************/
            var ddlyear = "<select name = 'yearStart' id = 'Year' class='YearDate' style = 'width:70px;'>"
			var dYear = new Date();
            var cYear = dYear.getFullYear();
            ddlyear += "<option value='00'>Year</option>";

            for(var j = 1750; j <= cYear ; j++)
            {
                var temp = "";
                curyear = j.toString();
                if(j < 10)
                {
                    curyear = '0' + j.toString();
                }
                if(curyear == dateArr[2])
                {
                    temp = "<option value='" + curyear + "' selected>" + curyear + "</option>";
                }
                else temp = "<option value='" + curyear + "'>" + curyear + "</option>";

                ddlyear += temp;
            }

            //cell1.innerHTML = "<select name = 'monthStart' id = 'Month' class='MonthDate' style = 'width:75px;'></select>";
            cell1.innerHTML = ddlmonth;
            cell2.innerHTML = ddlday;
            cell3.innerHTML = ddlyear;
        }
   // }
}

function displayJobNumberTableInfo(string, id) {
	var object = JSON.parse(string);

	for(var i = 0 ; i < object.length; i++) {
		$("#"+id).addTag(object[i].jobnumber);
	}
}



//This function is called when form is submitted performs proccesses to prepare page for next entry.
function submitEntry(results)
{	
	//Error handler that obtains the parameter from the ajax function to determine if the rectangle layer should be added and the markers deleted
	//or the rectangle should not be added and the point markers erased. 
	if (results == "New record created successfully"){
		document.getElementById("myForm").reset();
		location.reload();
	}
	else
		deletePrevious();
	
}

//form submit function
$(function () 
{	
	$("#myForm").submit(function(e)
	{
		if(markerCount < 2)
		{
			alert("Select area on document before submitting");
			e.preventDefault();
			return;			
		}
		getClientTableJSON();
        getFieldBookTableJSON();
        getMapTableJSON();
		getRelatedPaperTableJSON();

		getJobNoJSON();

        var entrycoordinates = JSON.parse($("#Entry_Coordinates").val());
         x1 = entrycoordinates.x1;
         y1 = entrycoordinates.y1;
         x2 = entrycoordinates.x2;
         y2 = entrycoordinates.y2;


        //retrieve values from form and set them to appropriate variables
        collection = document.getElementById("Collection").value;
        docID = document.getElementById("Document_ID").value;
        fileName = document.getElementById("File_Name").value;
        surveyOrSection = document.getElementById("Survey_Or_Section").value ;
        blockOrTract = document.getElementById("Block_Or_Tract").value;
        lotOrAcres = document.getElementById("Lot_Or_Acres").value;
        description = document.getElementById("Description").value ;
        client = document.getElementById("Client_Info").value ;
        fieldBookInfo = document.getElementById("Field_Book_Info").value ;
        relatedPapersFileNo = document.getElementById("Related_Papers_Info").value;
		mapInfo = document.getElementById("Map_Table_Info").value;
		date = getDateJSON();
        jobNumber = document.getElementById("Job_Numbers_Info").value;
        comments = document.getElementById("Comments").value;

        //creates JSON that contains all information from form to update the db
        var newobject = addEntryObject(collection,docID,x1, y1, x2, y2, fileName, surveyOrSection, blockOrTract, lotOrAcres, description, client,
            fieldBookInfo, relatedPapersFileNo, mapInfo, date, jobNumber,comments);

		$.ajax({
			type: 'post',
			url: 'php/submitEntry.php',
			data: {"newobject": JSON.stringify(newobject)},
			success: function(results){
				alert(results);
				submitEntry(results);
				}
		});
	e.preventDefault();
	});
});

//This function obtains the information from the select form dropdown for 
function Map_Kind_Dropdown(){
		Map_Kind_Options = document.getElementById("Map_Kind").options;
	}

//This function retrieves fileName which in this case is the path to the image and writes result to hidden form field.
function getFileName()
{
	var fileName = window.localStorage.getItem('fileName');
	document.getElementById("File_Name").value = fileName;
	return fileName;
}

//This function is binded to the button "Delete Active Rectangle" this deletes the 
//rectangle in blue that the user has drawn but not submitted. 
function deletePrevious()
{	
	if(markerCount == 2)
	{
		map.removeLayer(rectangleArray[rectangleArray.length-1]);
		rectangleArray.splice(rectangleArray.length, 1);
		rectangleCoords.splice(rectangleCount-1, 2);
		rectangleCount--;
		
		map.removeLayer(markerArray[markerCount-1]);
		map.removeLayer(markerArray[markerCount-2]);
		markerArray.splice(markerCount-2, 2);
		markerCount = markerCount-2;
		
		document.getElementById('Entry_Coordinates').value = "";
	}
	else if(markerCount == 1)
	{
		rectangleCoords.splice(0, 1)
		
		map.removeLayer(markerArray[markerCount-1]);
		markerArray.splice(-1,1 );
		markerCount--;
	}
	else
		return null;
}


//This function creates "updateObject" containing all data from HTML form, 
//and calls updateEntryData.php to update database with newly created updateObject values.
function updateEntryData()
{
	if (rectangleSelected == false)
	{
		alert("No rectangled selected");
		return null;
	}
	getClientTableJSON();
	getFieldBookTableJSON();
	getMapTableJSON();
	getRelatedPaperTableJSON();
	getJobNoJSON();
	
	//retrieve values from form and set them to appropriate variables
	collection = document.getElementById("Collection").value;
	docID = document.getElementById("Document_ID").value;
	fileName = document.getElementById("File_Name").value;
	x1 = document.getElementById("x1").value;
	y1 = document.getElementById("y1").value;
	x2 = document.getElementById("x2").value;
	y2 = document.getElementById("y2").value;
	surveyOrSection = document.getElementById("Survey_Or_Section").value ;
	blockOrTract = document.getElementById("Block_Or_Tract").value;
	lotOrAcres = document.getElementById("Lot_Or_Acres").value;
	description = document.getElementById("Description").value ;
	client = document.getElementById("Client_Info").value ;
	fieldBookInfo = document.getElementById("Field_Book_Info").value ;
	relatedPapersFileNo = document.getElementById("Related_Papers_Info").value;
	date = getDateJSON();
	jobNumber = document.getElementById("Job_Numbers_Info").value;
	mapInfo = document.getElementById("Map_Table_Info").value;
	comments = document.getElementById("Comments").value;
	
	/*Conditional statement that allows to update the entry by selecting a value from the Date table, so if the
	value selected is not a numeric value like; Month, Day, or Year, the value that will be stored in the 
	database will be a 0000 value for a year, or 00 for month and day*/
	/*if (document.getElementById("Year").value == "Year")
		{
			var Year = "0000";
		}
		else
			Year = document.getElementById("Year").value;
		
		if(document.getElementById("Month").value == "Month")
		{
				var Month = "00"
		}
		else if(document.getElementById("Month").value < 10)
			Month = "0" + document.getElementById("Month").value;
		else
			Month = document.getElementById("Month").value;
			
		if (document.getElementById("Day").value == "Day")
		{
			var Day = "00";
		}
		else if(document.getElementById("Day").value < 10)
			Day = "0" + document.getElementById("Day").value;
		else
			Day = document.getElementById("Day").value;
		
		var dateString = Year + "-" + Month + "-" + Day;
		date = dateString;*/

	
	
	//creates JSON that contains all information from form to update the db
	updateObject = addEntryObject(collection,docID,x1, y1, x2, y2, fileName, surveyOrSection, blockOrTract, lotOrAcres, description, client,
						fieldBookInfo, relatedPapersFileNo, mapInfo, date, jobNumber,comments);
	$.ajax({
		type: 'post',
		url: 'php/updateEntryData.php',
		data: {"updateObject": JSON.stringify(updateObject)},
		success:function(data){
			alert(data);
		}
	});	
}

//This function deletes the object that is selected from the viewer and reloads the page.
function deleteSelected()
{
	if (rectangleSelected == false)
	{
		alert("No rectangled selected");
		return null;
	}
	var collection = document.getElementById("Collection").value;
	var docID = document.getElementById("Document_ID").value;
	var x1 = document.getElementById("x1").value;
	var y1 = document.getElementById("y1").value;
	var x2 = document.getElementById("x2").value;
	var y2 = document.getElementById("y2").value;
	
	//creates object that contains location of selected rectangle so that the db can be queried and delete it 
	var deleteObject = addEntryObject(collection,docID,x1, y1, x2, y2);
	
	$.ajax({
		type: 'post',
		url: 'php/deleteEntry.php',
		data: {"deleteObject": JSON.stringify(deleteObject)},
		success:function(data){
			alert("Entry Deleted");
			location.reload();
		}
	});
}





//This function is an auxillary function for displayFieldBookTableInfo() and displayMapTableInfo().
//The purpose of this function is to delete the table completely before the new table is created so avoid having unused rows .
function deleteTable(id)
{
	var table = document.getElementById(id);
	if(table.rows.length == 1)
		return;
	
	table.deleteRow(table.rows.length-1);
	
	//recursive call 
	deleteTable(id);
}

/*************************************************
 ************* SUBMIT JSON CONVERSION ************
 * 1.CLIENT
 * 2.FIELD BOOK
 * 3.RELATED PAPER
 * 4.MAP
 * 5.DATE
 * 6.JOB NUMBER
 *************************************************/

/*****************************************************************************
 * 1.CLIENTS
 *****************************************************************************/
//Creates stringify JSON from values in Client Table
function getClientTableJSON() {
	var clitTag = $("#Client_Table_tagsinput").children(".tag");
	var tagCliArray = [];
	//Loop to iterate through each row of the table populating ObjectArray with values
	for(var i= 0; i < clitTag.length; i++) {
		var Object = new clientJSON($(clitTag[i]).text().substr(0, $(clitTag[i]).text().length - 1).trim());
		tagCliArray.push(Object);

	}

	if(clitTag.length == 0) {
		Object = new clientJSON("");
		tagCliArray.push(Object);
	}

	//sets value of form to stringified JSON of clienttArray
	document.getElementById("Client_Info").value = JSON.stringify(tagCliArray);
}

function clientJSON(client) {
	this.client = client;
}

/*****************************************************************************
 * 2.FIELD BOOKS
 *****************************************************************************/

//creates stringify JSON from values in Field_Book_Table
function getFieldBookTableJSON()
{
	table = document.getElementById("Field_Book_Table");
	var fieldBookObjectArray = [];
	
	//loop to iterate through each row of table populating fieldBookObjectArray with values 
	for(var i = 1; i < table.rows.length; i++)
	{
		fieldBookObject = new fieldBookJSON($('#Field_Book_Table tr:eq('+i+') td:eq(0) input').val(), $('#Field_Book_Table tr:eq('+i+') td:eq(1) input').val());
		fieldBookObjectArray.push(fieldBookObject);
	}


	//sets value of form to stringified JSON of fieldBookObjectArray
	document.getElementById("Field_Book_Info").value = JSON.stringify(fieldBookObjectArray);
}

function fieldBookJSON(bookNumber, pageNumbers)
{
	this.bookNumber = bookNumber;
	this.pageNumbers = pageNumbers;
}

/*****************************************************************************
 * 3.RELATED PAPERS
 *****************************************************************************/
//Creates stringify JSON from values in Client Table
function getRelatedPaperTableJSON()
{
	var relPapTag = $("#RelatedPaper_Table_tagsinput").children(".tag");
	var tagRelPapArray = [];
	//Loop to iterate through each row of the table populating ObjectArray with values
	for(var i= relPapTag.length; i--;) {
		var Object = new relatedPaperJSON($(relPapTag[i]).text().substr(0, $(relPapTag[i]).text().length - 1).trim());
		tagRelPapArray.push(Object);

	}

	if(relPapTag.length == 0) {
		Object = new relatedPaperJSON("");
		tagRelPapArray.push(Object);
	}

	//sets value of form to stringified JSON of clienttArray
	document.getElementById("Related_Papers_Info").value = JSON.stringify(tagRelPapArray);
}

function relatedPaperJSON(relatedPapers) {
	this.relatedpapersfileno = relatedPapers;
}

/*****************************************************************************
 * 4.MAPS
 *****************************************************************************/
//creates stringify JSON from values in Map_Table
function getMapTableJSON()
{
	table = document.getElementById("Map_Table");
	var mapObjectArray = new Array();
	
	for(var i = 1; i < table.rows.length; i++)
	{
		mapObject = addMapObject(document.getElementById("Map_Table").rows[i].cells[0].firstChild.value,
											document.getElementById("Map_Table").rows[i].cells[1].firstChild.value);
		mapObjectArray.push(mapObject);
	}

	document.getElementById("Map_Table_Info").value = JSON.stringify(mapObjectArray);

}

function addMapObject(mapNumber, mapKind)
{
	var mapObject = new mapObjectConstructor(mapNumber, mapKind);
	return mapObject;
}

function mapObjectConstructor(mapNumber, mapKind)
{
	this.mapNumber = mapNumber;
	this.mapKind = mapKind;
}

/*****************************************************************************
 * 5.DATE
 *****************************************************************************/
function getDateJSON() {
	var table = document.getElementById("Date_Table");
	var objectArray = [];

	for(var i = 1; i < table.rows.length; i++)
	{
		dateObject = addDateObject($('#Date_Table tr:eq(' + i + ') td:eq(0) option:selected').val(),
			$('#Date_Table tr:eq(' + i + ') td:eq(1) option:selected').val(),
			$('#Date_Table tr:eq(' + i + ') td:eq(2) option:selected').val());
		objectArray.push(dateObject);
	}
	return JSON.stringify(objectArray);

}

function addDateObject(Month, Day, Year)
{
	var dateObject = new dateObjectConstructor(Month, Day, Year);
	return dateObject;
}

function dateObjectConstructor(Month, Day, Year)
{
	this.Date = Month + "/" + Day + "/" + Year;
}

/*****************************************************************************
 * 6.Job Numbers
 *****************************************************************************/
function getJobNoJSON() {
	var jobNoTag = $("#JobNumber_Table_tagsinput").children(".tag");
	var tagJobNoArray = [];
	//Loop to iterate through each row of the table populating ObjectArray with values
	for(var i= jobNoTag.length; i--;) {
		var Object = new jobNoObjectConstructor($(jobNoTag[i]).text().substr(0, $(jobNoTag[i]).text().length - 1).trim());
		tagJobNoArray.push(Object);

	}

	if(jobNoTag.length == 0) {
		Object = new jobNoObjectConstructor("");
		tagJobNoArray.push(Object);
	}

	//sets value of form to stringified JSON of clienttArray
	document.getElementById("Job_Numbers_Info").value = JSON.stringify(tagJobNoArray);
}

function jobNoObjectConstructor(jobNo)
{
	this.jobnumber = jobNo;
}

/************************************************************************************/
function addFieldRow(id)
{
 var table = document.getElementById(id);
		var row = table.insertRow(-1);
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		cell1.innerHTML = '<input type="text" class= "Input_Field">';
		cell2.innerHTML = "<input type= 'text' class= 'Input_Field' id = 'Field_Book_Page'>";
}

//functionality for "+" button 
function addMapRow(collection,id)
{
    var table = document.getElementById(id);

    $.ajax({
        url: 'php/MapKind_Options.php', //This is the current doc
        type: "POST",
        //dataType:'json', // add json datatype to get json
        data: {"collection": collection,"mapKind": "","mapNumber": "","id":""},
        success: function(data) {
            var row = table.insertRow(-1);
            row.innerHTML += data;
        }
    });
}

//functionality for "-" button
function deleteTableRow(id)
{
	if(id == 'Field_Book_Table')
	{
		var table = document.getElementById("Field_Book_Table");
		if(table.rows.length == 2)
			return;
		table.deleteRow(-1);
	}
	else if(id == 'Map_Table')
	{
		var table1 = document.getElementById("Map_Table");
		if(table1.rows.length == 2)
			return;
		table1.deleteRow(-1);
	}
	else if(id == 'Client_Table'){
		var table2 = document.getElementById('Client_Table');
		if(table2.rows.length == 2)
			return;
		table2.deleteRow(-1);
	}
	else if(id == 'RelatedPaper_Table'){
		var table3 = document. getElementById('RelatedPaper_Table');
		if(table3.rows.length == 2)
			return;
		table3.deleteRow(-1);
	}
	else if(id == "Date_Table"){
		var table4 = document.getElementById("Date_Table");
		if(table4.rows.length == 2)
			return;
		table4.deleteRow(-1);
	}

	else if(id == 'JobNumber_Table') {
		var table5 = document.getElementById("JobNumber_Table")
		if(table5.rows.length == 2)
			return;
		table5.deleteRow(-1);
	}
}
//cleaning temporary workspace
function incompleteTranscription()
{
    var collection = document.getElementById("Collection").value;
    var id = document.getElementById("Document_ID").value;

    $.ajax({
        url: 'php/incompleteTranscription.php',
        type: "POST",
        data: {"collection": collection,"docID": id,"fileName": window.localStorage.getItem('fileName')},
        success: function(data) {
            window.close();
        }
    });
}

function completeTranscription()
{
    var collection = document.getElementById("Collection").value;
	var id = document.getElementById("Document_ID").value;

    $.ajax({
        url: 'php/completeTranscription.php',
        type: "POST",
        data: {"collection": collection,"docID": id,"fileName": window.localStorage.getItem('fileName')},
        success: function(data) {
            incompleteTranscription();
        }
    });
}

//called on refresh of page 
$(document).ready(function () {
    resetForms();
});

//resets every field in the form
function resetForms() {
    document.forms['myForm'].reset();
}

//Object Construction Functions
function addEntryObject(collection,docID,x1,y1, x2, y2, fileName, surveyOrSection, blockOrTract, lotOrAcres, description, client,
						fieldBookInfo, relatedPapersFileNo, mapInfo, entryDate, jobNumber, comments)
{
	var entryObject = new entryObjectConstructor(collection,docID,x1,y1, x2, y2, fileName, surveyOrSection, blockOrTract, lotOrAcres, description, client,
						fieldBookInfo, relatedPapersFileNo, mapInfo, entryDate, jobNumber,comments);
	return entryObject;
}

function entryObjectConstructor(collection,docID,x1,y1, x2, y2, fileName,surveyOrSection, blockOrTract, lotOrAcres, description, client,
						fieldBookInfo, relatedPapersFileNo, mapInfo, entryDate, jobNumber,comments)
{
	this.collection = collection;
	this.docID = docID;
	this.x1 = x1;
	this.y1 = y1;
	this.x2 = x2;
	this.y2 = y2;
	this.fileName = fileName;
	this.surveyOrSection = surveyOrSection;
	this.blockOrTract = blockOrTract;
	this.lotOrAcres = lotOrAcres;
	this.description = description;
	this.client = client;
	this.fieldBookInfo = fieldBookInfo;
	this.relatedPapersFileNo = relatedPapersFileNo;
	this.mapInfo = mapInfo;
	this.entryDate = entryDate;
	this.jobNumber = jobNumber;
    this.comments = comments;
}

function addEntryCoordinateObject(x1,y1, x2, y2)
{
	var entryCoordinateObject = new entryCoordinateObjectConstructor(x1,y1, x2, y2);
	return entryCoordinateObject;
}

function entryCoordinateObjectConstructor(x1,y1, x2, y2)
{
	this.x1 = x1;
	this.y1 = y1;
	this.x2 = x2;
	this.y2 = y2;
}


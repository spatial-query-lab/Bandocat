function validateForm()			//validate 
{	
	
	var library = document.getElementById("library");
	var docTitle = document.getElementById("docTitle");

	var comment = document.getElementById("comment");
	var subfolder_cmt = document.getElementById("subfolder_comments");
	var subfolder = document.getElementById("inSubfolder");
	var ddlclass = document.getElementById("ddlClassification");
	var class_cmt = document.getElementById("class_comments");
			
	var numbers = /^[0-9]+$/;
	
	var dayStart = document.getElementById("dayStart");
	var monthStart = document.getElementById("monthStart");
	var yearStart = document.getElementById("yearStart");

	var dayEnd = document.getElementById("dayEnd");
	var monthEnd = document.getElementById("monthEnd");
	var yearEnd = document.getElementById("yearEnd");

	var errors = 0;

	if(library.value == "")		
	{	
		document.getElementById("librarySub").innerHTML = "<br>*This field is required";
		errors++;
	}
	else document.getElementById("librarySub").innerHTML = "";
	
	if(docTitle.value == "")		
	{	
		document.getElementById("docTitleSub").innerHTML = "<br>*This field is required";
		errors++;
	}
	else document.getElementById("docTitleSub").innerHTML = "";
	

	if(monthStart.value != 'Month' && dayStart.value != 'Day' && yearStart.value != 'Year')
	{
		if(dayStart.value>28 && monthStart.value==2 && !( yearStart.value % 400 == 0 || ( yearStart.value % 4 == 0 && yearStart.value % 100 != 0 )))
		{
			document.getElementById("docStartDateSub").innerHTML = "Invalid input";
			errors++;		
		}
		else if(dayStart.value>29 && monthStart.value==2 && ( yearStart.value % 400 == 0 || ( yearStart.value % 4 == 0 && yearStart.value % 100 != 0 )))	
		{
			document.getElementById("docStartDateSub").innerHTML = "Invalid input";
			errors++;
		}
		else if(dayStart.value>30 && (monthStart.value==4 ||monthStart.value==6||monthStart.value==9||monthStart.value==11))
		{
			document.getElementById("docStartDateSub").innerHTML = "Invalid input";
			errors++;
		}
		else document.getElementById("docStartDateSub").innerHTML = "";
		
	}

	if(monthEnd.value != 'Month' && dayEnd.value != 'Day' && yearEnd.value != 'Year')
	{
		if(dayEnd.value>28 && monthEnd.value==2 && !( yearEnd.value % 400 == 0 || ( yearEnd.value % 4 == 0 && yearEnd.value % 100 != 0 )))
		{
			document.getElementById("docEndDateSub").innerHTML = "Invalid input";
			errors++;		
		}
		else if(dayEnd.value>29 && monthEnd.value==2 && ( yearEnd.value % 400 == 0 || ( yearEnd.value % 4 == 0 && yearEnd.value % 100 != 0 )))	
		{
			document.getElementById("docEndDateSub").innerHTML = "Invalid input";
			errors++;
		}
		else if(dayEnd.value>30 && (monthEnd.value==4 ||monthEnd.value==6||monthEnd.value==9||monthEnd.value==11))
		{
			document.getElementById("docEndDateSub").innerHTML = "Invalid input";
			errors++;
		}
		else document.getElementById("docEndDateSub").innerHTML = "";
		
	}


	if (subfolder.checked == true && subfolder_cmt.value == "")
	{
		document.getElementById("subfolderSub").innerHTML = "<br>*This field is required <br>if the document is in a subfolder";
		errors++;
	}
	else document.getElementById("subfolderSub").innerHTML = "";

	
	if (ddlclass.value == "Folder Cover" && (class_cmt.value == "" || class_cmt.value == null))
	{
		document.getElementById("classcmtSub").innerHTML = "<br>*If the classification is Folder Cover, <br>this field must not be empty";
		errors++;
	}
	else document.getElementById("classcmtSub").innerHTML = "";
	
	if (ddlclass.value == "None")
	{
		document.getElementById("ClassSub").innerHTML = "<br>*Classification must not be None";
		errors++;
	}
	else document.getElementById("ClassSub").innerHTML = "";


	if(errors > 0)
	{
		return false;
	}
	else if(errors == 0)
		return true;
}

$(function() {
           
	  $('.tooltip').mouseover(function(){              
	        $(this).children('span').show();                
	      }).mouseout(function(){
	        $(this).children('span').hide();
	      })

});
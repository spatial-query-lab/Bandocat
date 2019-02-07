
function validateForm()			//validate 
{	
	
	var library.value = "";
	var docTitle.value = "";

	var fileName.value = document.getElementById("fileName");

			

	var numbers = /^[0-9]+$/;
	

	var file1 = document.getElementById("file1");
	var valid_extensions = /(.jpg|.jpeg|.png|.tif)$/i;  
	var errors = 0;
	


	if(file1.value == "")		
	{	
		document.getElementById("file1Sub").innerHTML = "*";
		errors++;
	}
	else
	{	
		
			if(!valid_extensions.test(file1.value))
		{
			document.getElementById("file1Sub").innerHTML = "Invalid file";
			errors++;
		}
		else
		{
			document.getElementById("file1Sub").innerHTML = "";		
		}
			
	}



$(function() {
if ($.browser.msie && $.browser.version.substr(0,1)<7)
{            
  $('.tooltip').mouseover(function(){              
        $(this).children('span').show();                
      }).mouseout(function(){
        $(this).children('span').hide();
      })
}
});        



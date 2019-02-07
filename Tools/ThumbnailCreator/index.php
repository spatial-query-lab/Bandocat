<?php
/* PHP INCLUDES */
include '../../Library/SessionManager.php';
$session = new SessionManager();
require('../../Library/DBHelper.php');
$DB = new DBHelper();
require('../../Library/ControlsRender.php');
$Render = new ControlsRender();
?>
<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Thumbnail Creator</title>

    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel = "stylesheet" type="text/css" href="../../ExtLibrary/DataTables-1.10.12/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
</head>
<!-- END HEADER -->
<!-- HTML BODY -->
<body>
<div id="wrap">

    <div id="main">



            <!-- HTML Header and Side Menu -->
        <div id="divleft">
                    <?php include '../../Master/header.php';
                    include '../../Master/sidemenu.php';?>
        </div>
        <div id="divright">
                    <h2 id="page_title">Thumbnail Creator</h2>

                    <table width="100%" id="table-header_right">
                        
                        <tr>
                            <td style="margin-left: 45% ;font-size:14px" colspan="5%"
                            <td style="float:left;font-size:14px" colspan="5%">
                                <!-- Form responsible for the select drop down menu -->
                                <form id = "form" name="form" method="post">
                                    Select Collection:
									<!-- <select name="ddlCollection" id="ddlCollection" onchange="Calculate(this.value)"> -->
                                    <select name="ddlCollection" id="ddlCollection">

                                        <!-- Renders the Dropdownlist with the collections -->
                                        <?php $Render->GET_DDL_COLLECTION($DB->GET_COLLECTION_FOR_DROPDOWN_FROM_TEMPLATEID(array(1),true),null);?>

                                    </select>
                                </form>
                                <!-- Displays the count of maps -->
                                <h4 id="txt_counter" ></h4>
                        </tr>
                    </table>

                    <!-- Table responsible for displaying returned db items in a table format -->
                    <div id="divscroller">
                        <table id="dtable" class="display compact cell-border hover stripe" cellspacing="0" width="100%" data-page-length='20'>
                            <thead>
							<tr>
								<th></th>
								<th width="100px">Library Index</th>
								<th>Document Title</th>
								
	
								<th width="125px"></th>
							</tr>
							</thead>
							<tfoot>
							<tr>
								<th></th>
								<th width="100px"></th>
								<th></th>
								
	
								<th width="125px"></th>
							</tr>
							</tfoot>
							
							
                        </table>
                    </div>

        </div>

    </div>

</div>
<?php include '../../Master/footer.php'; ?>
</body>
<!-- END BODY -->
<script>
    /**************************************
     * Calculate is responsible for counting
     * the number of maps in the selected item
     * that have the requirements needed. I.E
     * searching blutchermaps would return a
     * number 782 have coasts out of 6911. Once
     * The calculation is complete, output into
     * txt_counter
     * ************************************/
//


</script>

<script>
    var selected = false;
	 var table;
    /*******************************************
     * Function responsible for calling Jquery.
     * DataTables to render and load the database
     * items.
     *******************************************/
    function SSP_DataTable(collection)
    {
		
		 console.log(collection);
		 
       // document.getElementById("loader_2").style.visibility = "visible";
        //create new DataTable with 6 parameters and assign table to #dtable
        //options can be found at https://datatables.net/reference/option/    
             table = $('#dtable').DataTable( {
                //Enables display of a processing indicator
                "processing": true,
                //Toggles serverside processing
                "serverSide": true,
                //Specifys the entries in the length dropdown select list
                "lengthMenu": [20, 40 , 60, 80, 100],
                "bStateSave": false,
                //Initialise a datatable as usual, but if there is an existing table which matches the selector
                //it will be destroyed and replaced with the new table
                "destroy": true,
                //Allows you to assign specific options to columns in the table               
                "columnDefs":
				[
                    //column Document Index: Replace with Hyperlink
                    {
                        "render": function ( data, type, row ) {
                            return "<a target='_blank' href=''>Edit/View</a>" ;
                        },
                        "targets": 0
                    },
                    { "searchable": false, "targets": 0 },
					{ "visible": false, "targets": 0 },
                    //column Title
                    {
                        "render": function ( data, type, row ) {
                            return data;
                        },
                        "targets": 2
                    },
					 { "searchable": true, "targets": 2 },
                   
                   // { "searchable": false, "targets": 7 },
                    {
                        "render": function ( data, type, row ) {
                        return "<a href='#' onclick=''>Create Thumbnail</a>";
                        },
                        "targets": 3
                    },

                ],
                //Use ajax to pass data to the table. collection contains the db info

                "ajax":
                {
                    url: "thumbnail_processing.php?col=" +collection 
                    , dataType: "json"
                    ,  complete: function() {
                  //  document.getElementById("loader_2").style.visibility = "hidden";
                }
                    , error: function (xhr, error, thrown)
                {
                    alert("An error occurred while attempting to retrieve data via ajax.\n"+thrown );
                }

                },
				"initComplete": function() {
                this.api().columns().every( function () 
				{
                    var column = this;
                    switch(column[0][0]) //column number
                    {
                        //cases: search textbox
                        case 0:
						case 1:
							var input = $('<input type="text" style="width:100%" placeholder="Search..." value=""></input>')
                                .appendTo( $(column.footer()).empty() )
                                .on( 'keyup change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search(val)
                                        .draw();
                                } );
                            break;
                        case 2:
							var input = $('<input type="text" style="width:100%" placeholder="Search..." value=""></input>')
                                .appendTo( $(column.footer()).empty() )
                                .on( 'keyup change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search(val)
                                        .draw();
                                } );
                            break;
                            //case search by status
                       
                    }
                } );
            },






            } );
        
       



        //table.column(0).visible(false);


        // select row on single click
        $('#dtable tbody').on( 'click', 'tr', function () {
            if ( $(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        } );
        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#table-header_right").outerHeight() - $("#page_title").outerHeight() - 55);

    }

    //On page ready, pass elementIDddlCollections value into the SSP_DataTable Function
    $(document).ready(function()
    {

        $( "#ddlCollection" ).change(function() {
            switch ($("#ddlCollection").val())
            {



                case "": break;
                default: SSP_DataTable($("#ddlCollection").val());



            }
        });
       

    });
    function onClickCalculate(event,Query) 
	{
		//This function is responsible for generating thumbnails
       
    }
	
	
	
</script>

</html>
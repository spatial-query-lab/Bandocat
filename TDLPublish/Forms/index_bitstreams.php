<?php
/* PHP INCLUDES */
spl_autoload_register(function ($class_name) {
    require_once "../../Library/" . $class_name . '.php';});
$session = new SessionManager();
$DB = new DBHelper();

$Render = new ControlsRender();

if(isset($_GET['docID']) && isset($_GET['col']))
{
    $ini_dir = "BandoCat_config\\tdlconfig.ini";
    $docID = $_GET['docID'];
	$col = $_GET['col'];
	//switch to the currently working Database
	$DB->SWITCH_DB($col);
    $TDL = new TDLPublishJob();					
	$dspaceDocInfo = $DB->PUBLISHING_DOCUMENT_GET_DSPACE_INFO($docID);
	$dspaceID = $dspaceDocInfo['dspaceID'];	
	$bitstreams = $TDL->TDL_GET_ITEM_BITSTREAMS($dspaceID); 
	
	//var_dump(json_encode($bitstreams));
	$json_bitstreams = json_encode($bitstreams);
	$root = substr(getcwd(),0,strpos(getcwd(),"htdocs\\")); //point to xampp// directory
	$config = parse_ini_file($root . $ini_dir);
    $baseUrl = $config['baseURL'];
	

}
//This page allows to push/pop documents to the TDL publishing Queue in BandoCat, Unpublish/Update Published document in TDL Server
?>
<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>TDL Publishing</title>

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
        <!-- HTML HEADER and SIDE MENU -->
       <div id="divleft">
                    <?php include '../../Master/header.php';
                    include '../../Master/sidemenu.php' ?>
       </div>
        <div id="divright">
                    <h2 id="page_title">TDL Publishing</h2>
                    <table width="100%" id="table-header_right">
                        <tr>
                            <td style="margin-left: 45% ;font-size:14px" colspan="20%"
                            <td style="float:left;font-size:14px" colspan="20%">
                                <!-- Form responsible for the select drop down menu 
                                 <form id = "form" name="form" method="post" style="padding:0;margin:0">
                                    Select Collection:
                                    <select name="ddlCollection" id="ddlCollection">
                                        <!-- Renders the Dropdownlist with the collections
                                        <?php $Render->GET_DDL_COLLECTION($DB->GET_COLLECTION_FOR_DROPDOWN_FROM_TEMPLATEID(array(4),false),"bluchermaps");?>
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
                                <th width="140px">uuid</th>
                                <th width="70px">name</th>
								<th width="70px">type</th>
                                <th width="70px">bundleName</th>
								<th width="70px">format</th>							
								<th width="70px">sizeBytes</th>
								<th width="150px">Action</th>
                                                    
                            </tr>
                            </thead>
                            <tfoot>
                                <tr> 
							           
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
    /*******************************************
     * Function responsible for calling Jquery.
     * DataTables to render and load the database
     * items.
     *******************************************/
    function SSP_DataTable()
    {
		
		var testdata = <?php echo $json_bitstreams; ?>;
		console.log(testdata);
		testdata = JSON.parse(testdata);
		//console.log ( typeof testdata);
		//console.log ( testdata);
        //create new DataTable with 6 parameters and assign table to #dtable
        //options can be found at https://datatables.net/reference/option/
        var table = $('#dtable').DataTable( {
        	"data": testdata,
			"columns": [
				{ "data": "uuid" },
				{ "data": "name" },
				{ "data": "type" },
				{ "data": "bundleName" },
				{ "data": "format" },				
				{ "data": "sizeBytes" },
			],
			 "columnDefs": 
			 [
			    {
                    "className": "dt-right",
                    "render": function ( data, type, row ) {
                        return data;
                    },
                   "width": "5%", "targets": 0
                },
				{
                    "className": "dt-left",
                    "render": function ( data, type, row ) {
                        return data;
                    },
                   "width": "15%", "targets": 1
                },
				{
                    "className": "dt-left",
                    "render": function ( data, type, row ) {
                        return data;
                    },
                   "width": "5%", "targets": 2
                },
				{
                    "className": "dt-left",
                    "render": function ( data, type, row ) {
                        return data;
                    },
                   "width": "5%", "targets": 3
                },
				{
                    "className": "dt-left",
                    "render": function ( data, type, row ) {
                        return data;
                    },
                   "width": "5%", "targets": 4
                },
				{
                    "className": "dt-left",
                    "render": function ( data, type, row ) {
                        return data;
                    },
                   "width": "5%", "targets": 5
                },			
				{
                    "className": "dt-left",
                    "render": function ( data, type, row ) 
					{
                        
                        //console.log(data);
                       return "<a href='' onclick='performAction(event," + '"view"'+")'>View</a>" + " | <a href='' onclick='performAction(event," + '"delete"'+")'>Delete</a>" ;//publish
                     
                            
                        
                    },
                   "width": "5%", "targets": 6
                },
			 ],
        } );


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
		
		SSP_DataTable();
    });

    //Description: pass action and data to index_actionprocessing.php
    //Parameter: event: to prevent Default event of the action
    //           action: type of action
    //           docID: target document ID
    function performAction(event,action)
    {
        event.preventDefault();
		 var bitId = event.path[2].firstChild.textContent;
		if(action == "view")
		{
			var url = "<?php echo $baseUrl; ?>";
			
			window.open(url + "bitstreams/" + bitId)
		}
		else
		{
			console.log(event);
			console.log(action);
			console.log(bitId);
			var col = "<?php echo $col; ?>";
			 $.ajax({
            type: "POST",
            url: "index_bitprocessing.php",
            data: {ddlCollection: col,bitID:bitId, action: action},
            success: function (data) {
                $('#dtable').DataTable().draw();
            } 
        }); 
		}
		  
		  // var datatable = table.rows().data();
           	//console.log(filename);
		
		
        
    }


</script>
</html>
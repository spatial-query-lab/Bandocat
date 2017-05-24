<?php
/* PHP INCLUDES */
spl_autoload_register(function ($class_name) {
    require_once "../../Library/" . $class_name . '.php';});
$session = new SessionManager();
$DB = new DBHelper();
$Render = new ControlsRender();
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
                                <!-- Form responsible for the select drop down menu -->
                                <form id = "form" name="form" method="post" style="padding:0;margin:0">
                                    Select Collection:
                                    <select name="ddlCollection" id="ddlCollection">
                                        <!-- Renders the Dropdownlist with the collections -->
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
                                <th width="70px">ID</th>
                                <th>Library Index</th>
                                <th width="100px">Status</th>
                                <th width="100px">Dspace URI</th>
                                <th width="85px">Dspace ID</th>
                                <th width="150px">Action</th>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
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
    function SSP_DataTable(collection)
    {
        //create new DataTable with 6 parameters and assign table to #dtable
        //options can be found at https://datatables.net/reference/option/
        var table = $('#dtable').DataTable( {
            //Enables display of a processing indicator
            "processing": true,
            //Toggles serverside processing
            "serverSide": true,
            //Specifys the entries in the length dropdown select list
            "lengthMenu": [20, 40 , 60, 80, 100],
            "stateSave": true,
            //Initialise a datatable as usual, but if there is an existing table which matches the selector
            //it will be destroyed and replaced with the new table
            "destroy": true,
            "columnDefs": [
                //column Document Index: Replace with Hyperlink
                {
                    "className": "dt-right",
                    "render": function ( data, type, row ) {
                        return data;
                    },
                    "targets": 0
                },
                {
                    "render": function ( data, type, row ) {
                        switch(data)
                        {
                            case "0": return "Not Published";
                            case "1": return "Published";
                            case "2": return "In Queue";
                            case "-1": return ""; // Unpublished???
                            case "10": //publishing front
                                return "Publishing...";
                            case "11":  //publishing back
                                return "Continue Publishing...";
                            default: return "Unknown code: " + data;
                        }
                    },
                    "targets": 2
                },
                {
                    "className": "dt-right",
                    "render": function ( data, type, row ) {
                        return data;
                    },
                    "targets": 3
                },
                {
                    "className": "dt-right",
                    "render": function ( data, type, row ) {
                        if(data == "0")
                            return "";
                        return data;
                    },
                    "targets": 4
                },
                {
                    "render": function ( data, type, row ) {
                        switch(row[2])
                        {
                            case "0": return "<a href='' onclick='performAction(event," + '"push"' + "," + row[0] +")'>Push</a>";//publish
                            case "1": return "<a href='' onclick='performAction(event," + '"update"' + "," + row[0] +")'>Update</a>  | <a href='' onclick='performAction(event," + '"unpublish"' + "," + row[0] +")'>Unpublish</a>";
                            case "2": return "<a href='' onclick='performAction(event," + '"pop"' + "," + row[0] +")'>Pop</a>"; //in publish queue
                            case "-1": return "<a href=''>Pop</a>"; //in unpublish queue????
                            case "10": //publishing front map
                            case "11": //publishing back map
                                return "Publishing... | <a href='' onclick='performAction(event," + '"unpublish"' + "," + row[0] +")'>Unpublish</a>"; //publishing
                            default: return "";
                        }
                    },
                    "targets": -1
                },
            ],
            //Use ajax to pass data to the table. collection contains the db info
            "ajax": "list_processing.php?col=" + collection,
            "initComplete": function() {
                this.api().columns().every( function () {
                    var column = this;
                    switch(column[0][0]) //column number
                    {
                        //cases: search textbox
                        case 0:
                        case 1:
                        case 3:
                        case 4:
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
                        case 2:
                            var select = $('<select style="width:100%"><option value="">Filter...</option><option value="0">Not Published</option><option value="1">Published</option><option value="2">In Queue</option><option value="10|11">Publishing</option></select>')
                                .appendTo( $(column.footer()).empty() )
                                .on( 'change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search(val)
                                        .draw();
                                } );
                            break;
                            break;
                    }
                } );
            },
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
    $(document).ready(function() {

        $( "#ddlCollection" ).change(function() {
            switch ($("#ddlCollection").val())
            {
                case "": break;
                default: SSP_DataTable($("#ddlCollection").val());
            }
        });

        $("#ddlCollection").change();
    });

    //Description: pass action and data to index_actionprocessing.php
    //Parameter: event: to prevent Default event of the action
    //           action: type of action
    //           docID: target document ID
    function performAction(event,action,docID)
    {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "index_actionprocessing.php",
            data: {ddlCollection: $("#ddlCollection").val(),docID: docID, action: action},
            success: function (data) {
                $('#dtable').DataTable().draw();
            }
        });
    }


</script>
</html>
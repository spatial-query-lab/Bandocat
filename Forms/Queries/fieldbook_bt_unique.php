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

    <title>Maps With Coast</title>

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

                    <h2 id="page_title">Convert and Compress To PDF</h2>
                    <table width="100%" id="table-header_right">
                        <tr>
                            <td style="margin-left: 45% ;font-size:14px" colspan="5%"
                            <td style="float:left;font-size:14px" colspan="5%">
                                <!-- Form responsible for the select drop down menu -->
                                <form id = "form" name="form" method="post">
                                    Select Collection:
                                    <select name="ddlCollection" id="ddlCollection" onchange="Calculate(this.value)">

                                        <!-- Renders the Dropdownlist with the collections -->
                                        <?php $Render->GET_DDL_COLLECTION($DB->GET_COLLECTION_FOR_DROPDOWN_FROM_TEMPLATEID(array(3),true),null);?>
                                    </select>
                                </form>
                                <td>

                                <form id = "form2" name="form2" method="post">
                                    Select Stage:
                                    <select name="ddlCollection2" id="ddlCollection2" onchange="Calculate(document.getElementById('ddlCollection').value)">
                                    <option value="0">Needs Review</option>
                                    <option value="1">Ready for PDF</option>
                                    <option value="2">Completed</option>
                                    </select>
                                </form>
                                </td>

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
                                <th width="50px"></th>
                                <th id="tablequery" width="40px"></th>
                            </tr>
                            </thead>
                        </table>
                    </div>

        </div>

    </div>
    <div id="modal_2">
        <img style=" visibility: hidden; position: absolute;
  left: 0;
  top: 0;
  right: 0;
  bottom: 0;
  margin: auto; /* presto! */" id="loader_2" src="../../Images/loading.gif" />
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
    /*******************************************
     * Function responsible for calling Jquery.
     * DataTables to render and load the database
     * items.
     *******************************************/
    function SSP_DataTable(collection)
    {
        document.getElementById("loader_2").style.visibility = "visible";
        //create new DataTable with 6 parameters and assign table to #dtable
        //options can be found at https://datatables.net/reference/option/
        var table = $('#dtable').DataTable( {
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
                    {

                        "render": function ( data, type, row )
                        {
                            //print_r(data);
                          //  var_dump(data);
                           // var data = table.row( $(this).parents('tr') ).data();
                          //  console.log(row[1]);
                           // alert(row[1]);
                            //alert( data );  alert('weee');
                            //NEEDS REVIEW

                            if(document.getElementById('ddlCollection2').value == 0)
                            {

                                //return "<a href='' onclick=onClickCalculate(event,document.getElementById('ddlCollection').value)>Select</a>" ;
                                return "<a target='_blank'  href='/BandoCat/Templates/FieldBook/index.php?&col=" + $('#ddlCollection').val() + "&pagekey=review'>Edit/View</a>"
                            }
                            //READY FOR PDF
                            if(document.getElementById('ddlCollection2').value == 1)
                            {
                                
                                //return "<a href='' onclick=onClickCalculate(event,document.getElementById('ddlCollection').value)>Select</a>" ;
                                return "<a href='' onclick=onClickCalculate(event,document.getElementById('ddlCollection').value)>Select</a>" ;
                            }
                            //COMPLETED
                            if(document.getElementById('ddlCollection2').value == 2)
                            {

                                //return "<a href='' onclick=onClickCalculate(event,document.getElementById('ddlCollection').value)>Select</a>" ;
                                return "<a target='_blank'  href='/BandoCat/Templates/FieldBook/index.php?&col=" + $('#ddlCollection').val() + "&pagekey=review'>Edit/View</a>"
                            }

                        },
                        "targets": 2
                    },
                {
                    "searchable": false, "targets": [0,2],
                }
                ],
            //Use ajax to pass data to the table. collection contains the db info

                "ajax":
                {
                    url: "fieldbook_bt_unique_processing.php?col=" +collection + "&stage=" + document.getElementById('ddlCollection2').value
                    , dataType: "json"
                    ,  complete: function() {
                    document.getElementById("loader_2").style.visibility = "hidden";
                }
                    , error: function (xhr, error, thrown)
                {
                    alert("An error occurred while attempting to retrieve data via ajax.\n"+thrown );
                }

                },






        } );


        table.column(0).visible(false);


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

//    function SSP_DataTable_Selected(collection, booktitle)
//    {
//        //create new DataTable with 6 parameters and assign table to #dtable
//        //options can be found at https://datatables.net/reference/option/
//        var table = $('#dtable').DataTable( {
//            //Enables display of a processing indicator
//            "processing": true,
//            //Toggles serverside processing
//            "serverSide": true,
//            //Specifys the entries in the length dropdown select list
//            "lengthMenu": [20, 40 , 60, 80, 100],
//            "bStateSave": false,
//            //Initialise a datatable as usual, but if there is an existing table which matches the selector
//            //it will be destroyed and replaced with the new table
//            "destroy": true,
//            //Allows you to assign specific options to columns in the table
//            "columnDefs":
//                [
//                    //column Document Index: Replace with Hyperlink
//                    {
//
//                        "render": function ( data, type, row )
//                        {
//                            return "<a target='_blank'  href='../../index.php?doc=" + data + "&col=" + $('#ddlCollection').val() + "&pagekey=review'>Edit/View</a>" ;
//                        },
//                        "targets": 0
//                    },
//                ],
//            //Use ajax to pass data to the table. collection contains the db info
//
//            "ajax": "fieldbook_bt_unique_selected_processing.php?col=" + collection + "&booktitle=" + booktitle
//
//
//        } );
//
//
//        table.column(0).visible(true);
//
//
//        // select row on single click
//        $('#dtable tbody').on( 'click', 'tr', function () {
//            if ( $(this).hasClass('selected') ) {
//                $(this).removeClass('selected');
//            }
//            else {
//                table.$('tr.selected').removeClass('selected');
//                $(this).addClass('selected');
//            }
//        } );
//        //resize height of the scroller
//        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#table-header_right").outerHeight() - $("#page_title").outerHeight() - 55);
//
//    }
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
        $( "#ddlCollection2" ).change(function()
        {

            switch ($("#ddlCollection").val())
            {


                case "": break;
                default: SSP_DataTable($("#ddlCollection").val());


            }
        });

    });
    function onClickCalculate(event,Query) {
       // event.path[2].firstChild.textContent;
        //The above event is passed with the "onclick" function each row has
        //event.path points to 14 things passed into the event.
        //event.path[2] points to the TR (row) of the event being fired
        //event.path[2].firstChild points to the first TD(column) of the TR(row)
        //event.path[2].firstChild.textContent returns the first column of the selected rows contents.
        event.preventDefault();


        document.getElementById("loader_2").style.visibility = "visible";
       // alert(tableAADataForContributors.fnGetPosition( $(this).closest('tr')[0]));
        if (Query.length == 0) {
            document.getElementById("txt_counter").innerHTML = "";
            return;
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200)
                {
                    alert(xmlhttp.responseText);
                   // document.getElementById("txt_counter").innerHTML = this.responseText;
                }
                if (xhr.readyState == XMLHttpRequest.DONE) {
                    alert(xhr.responseText);
                }

            };
            var Column = "needsreview";
            var BookTitle = event.path[2].firstChild.textContent;
            xmlhttp.open("GET", "QueryHelper.php?col=" + Query + "&column=" + Column + "&booktitle=" + BookTitle + "&stage=2", true);
            xmlhttp.send();



            selected = true;

            SSP_DataTable($("#ddlCollection").val());

        }
        //HMM
    }
</script>
</html>
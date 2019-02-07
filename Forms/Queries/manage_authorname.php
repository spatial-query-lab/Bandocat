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

    <title>Manage TDL Author Name</title>

    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel = "stylesheet" type="text/css" href="../../ExtLibrary/DataTables-1.10.12/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/jQueryUI-1.11.4/jquery-ui.min.js"></script>
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
            <h2 id="page_title">Manage TDL Author Name</h2>
            <table width="100%" id="table-header_right">
                <tr>
                    <td style="margin-left: 45% ;font-size:14px" colspan="20%"
                    <td style="float:left;font-size:14px" colspan="20%">
                        <!-- Form responsible for the select drop down menu -->
                        <form id = "form" name="form" method="post">
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
                        <th width="80px">Author ID</th>
                        <th>Author Name</th>
                        <th>TDL Name</th>
                        <th width="30px"></th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div id="PopupControl" hidden>
                <form id="frmEditTDLName" name="frmEditTDLName">
                    <h2>Edit TDL Name</h2>
                    <table>
                        <tr>
                            <td width="150px"><label>Author ID</label></td>
                            <td width="300px"><label id="lblAuthorID"></label></td>
                        </tr>
                        <tr>
                            <td><label>Author Name</label></td>
                            <td><label id="lblAuthorName"></label></td>
                        </tr>
                        <tr>
                            <td><label>TDL Name</label></td>
                            <td><input type="text" name="txtTDLName" id="txtTDLName" value="" required/></td>
                        </tr>
                        <tr style="text-align: center;line-height:70px">
                          <td colspan="2"><input type="submit" onclick="btnUpdate_onclick(event)" name="btnUpdate" id="btnUpdate" value="Update" class="bluebtn">
                              <input type="button" class="bluebtn" onclick="btnNext_onclick(event)" id="btnNext" value="Next" name="btnNext">
                              <input type="button" class="bluebtn" onclick="closePopup(event)" id="btnClose" value="Close" name="btnClose">
                          </td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>
    </div>
</div>
<?php include '../../Master/footer.php'; ?>
</body>
<!-- END BODY -->
<style>
    #PopupControl{
        position:absolute;
        z-index:9999;
        width:100%;
        height:100%;
        top:30%;
        left:10%;
        background-color: transparent;
        opacity: 0.9;
    }
    #frmEditTDLName{
        border-radius:10px;
        width:470px;
        padding: 10px;
        height:220px;
        background-color:#f1f1f1;
        margin-left:40%;
        vertical-align: middle;
    }
</style>
<script>
    //Hit enter on txtTDLName will trigger Update button, hit tab will trigger loadPopupInfo
    $("#txtTDLName").keyup(function(event){
        event.preventDefault();
        if(event.keyCode == 13)
        {$("#btnUpdate").click();return false; }//hit enter
    });

    function closePopup(e)
    {
        e.preventDefault();
        $("#PopupControl").hide();
    }
    /*******************************************
     * Function is called when user hit Edit link on the fourth column
     *******************************************/
    function showPopup(e,collection,id)
    {
        e.preventDefault();
        loadPopupInfo('load',collection,id);
        $("#PopupControl").show();
    }

    function loadPopupInfo(action,collection,id)
    {
        $.ajax({
            type: 'post',
            url: 'manage_authorname_processing.php?action=' + action + '&col=' + collection + '&id=' + id,
            success:function(data) {
                var jsonData = JSON.parse(data)[0];
                $("#lblAuthorID").text(jsonData.authorID);
                $("#lblAuthorName").text(jsonData.authorname);
                $("#txtTDLName").val(jsonData.TDLname);

                $("#txtTDLName").focus(); //focus on this textbox
            }
        });
    }
    function btnNext_onclick(e){
        e.preventDefault();
        loadPopupInfo('loadnext',$("#ddlCollection").val(),$("#lblAuthorID").text());
    }

    function btnUpdate_onclick(e){
        e.preventDefault();
        var collection = $("#ddlCollection").val();
        var authorID = $("#lblAuthorID").text();
        var authorName = $("#lblAuthorName").text();
        var TDLName = $("#txtTDLName").val();

        if(TDLName.trim() == "") {
            alert("TDLName must not be empty!");
            return false;
        }
        $.ajax({
            type: 'post',
            url: 'manage_authorname_processing.php?action=update&col=' + collection,
            data: {authorID: authorID , authorname: authorName, TDLname: TDLName },
            success:function(data) {
                var retval = JSON.parse(data);
                if(retval == true) {
                    alert("Success!");
                    loadPopupInfo('loadnext',collection,authorID);
                    $('#dtable').DataTable().draw(); //rerender table
                }
                else alert("Update failed!");
            }
        });
    }

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
            "bStateSave": true,
            //Initialise a datatable as usual, but if there is an existing table which matches the selector
            //it will be destroyed and replaced with the new table
            "destroy": true,
            //Allows you to assign specific options to columns in the table
            "order": [[ 0, "asc" ],[2, "desc"]],
            "columnDefs":
                [
                    {
                        "className": "dt-body-right",
                        "targets": [0]
                    },
                    //column Edit
                    {
                        "render": function ( data, type, row ) {
                            return "<a href='#' onclick='showPopup(event," + JSON.stringify(collection) + "," + row[0] +")'>Edit</a>";
                        },
                        "targets": 3
                    }
                ],
            //Use ajax to pass data to the table. collection contains the db info
            "ajax": "manage_authorname_processing.php?col=" + collection,
            "initComplete": function() {
                this.api().columns().every( function () {
                    var column = this;
                    switch(column[0][0]) //column number
                    {
                        //search text box
                        case 0:
                        case 1:
                        case 2:
                            var input = $('<input type="text" style="width:100%" placeholder="Search..." value=""/>')
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
        table.column(3).visible(false);
        <?php if($session->hasWritePermission()){ ?> table.column(3).visible(true); <?php } ?>


        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#table-header_right").outerHeight() - $("#page_title").outerHeight() - 55);

    }
    //**********************************************************************************************************
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
        $("#popupcontrol").hide();
    });

</script>
</html>
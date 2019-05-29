<?php
/**
 * Created by PhpStorm.
 * User: rortiz18
 * Date: 3/28/2019
 * Time: 3:24 PM
 */

include '../../Library/SessionManager.php';
$session = new SessionManager();
$test = [1, 2, 3];
if($session->isSuperAdmin()) {
    require('../../Library/DBHelper.php');
    $DB = new DBHelper();
}
else
{
    header('Location: ../../Forms/Main');
}
?>
<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.0/css/all.css" integrity="sha384-aOkxzJ5uQz7WBObEZcHvV5JvRW3TUc2rNPA7pe3AwnsUohiw1Vj2Rgx2KSOkF5+h" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- Bootstrap CDN Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css" crossorigin="anonymous">

    <!-- Font Awesome CDN CSS -->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">


    <title>Database Manager</title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">
    <!-- Custom CSS for loading gif -->
</head>
<body>
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container-fluid pl-5 pr-5">
    <div class="row">
        <div class="col">
            <!-- Put Page Contents Here -->
            <h1 class="text-center">Database Manager</h1>
            <hr>


            <div class="form-group row" align="center">
                <!-- DDL for Database Selection -->
                <label class="col-sm-1 col-form-label" for="ddlDatabases">DataBase:</label>
                <div class="col-sm-4">
                    <div class="d-flex">
                        <select class="form-control" name="ddlDatabases" id="ddlDatabases">
                            <!--<option value=""></option>-->
                            <!-- POPULATES THE DDL WITH BANDOCAT DATABASES -->
                            <?php $DB->SHOW_DATABASES(); ?>
                        </select>
                    </div>
                </div>

                <!-- DDL for Table Selection -->
                <label class="col-sm-1 col-form-label" for="ddlTables">Table:</label>
                <div class="col-sm-4">
                    <div class="d-flex">
                        <select class="form-control" name="ddlTables" id="ddlTables">
                            <!-- TABLES APPENDED HERE -->
                        </select>
                    </div>
                </div>
            </div>

            <hr>

            <?php include "../../Master/load.php"; ?>

            <div id="divTable">
                <!-- Data-Table -->
                <table id="dtable" class="table table-bordered table-hover" width="100%" cellspacing="0" data-page-length='20'>

                </table>
            </div>
        </div> <!-- col -->
    </div> <!-- row -->
</div><!-- Container -->

<!-- Modal -->
<div id="Modal">
    <div class="modal fade" id="rowModal" tabindex="-1" role="dialog" aria-labelledby="rowModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rowModalTitle">Row Content</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateDataBase">
                    <div class="modal-body" id="rowModalBody">
                        <!-- CONTENT APPENDED HERE -->
                    </div>
                    <div class="modal-footer">
                        <!--<input type="submit" value="Save Changes" class="btn btn-primary" id="submit">-->
                        <input type="button" value="Delete" class="btn btn-danger" id="delete">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalTitle">Instant Feedback Report</h5>
                <input type="text" hidden value="" id="status">
                <button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="responseModalBody">

            </div>
        </div>
    </div>
</div>

<?php include "../../Master/bandocat_footer.php" ?>


<!-- Complete JavaScript Bundle -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<!-- JQuery UI cdn -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>

<!-- Datatables CDN -->
<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>

<!-- Bootstrap JS files for datatables CDN -->
<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>

<!-- Exporting CDNS -->
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https:////cdn.datatables.net/plug-ins/1.10.19/api/processing().js"></script>

<!-- Our custom javascript file -->
<script type="text/javascript" src="../../Master/master.js"></script>

<script>
    var count = 0;
    $(document).ready(function() {
        // Function gets DDL values to populate our datatable
        getTableList();

        // Adding modal attributes
        $('#loaderModalContent').append('<div class="modal-body" id="loaderModalBody"></div>');
        $('#loaderModalBody').append('<div class="d-flex justify-content-center"><img src="../../Images/loading2.gif"></div><h5 class="text-center">Processing Table...</h5>');
    });

    // When database DDL is changed
    $('#ddlDatabases').change(function() {
        getTableList();
    });

    // When tables DDL is changed
    $('#ddlTables').change(function() {
        getUserInput();
    });

    // Reloads page when response modal is exited out of or hidden
    $('#responseModal').on('hidden.bs.modal', function () {
        location.reload();
    });

    /***************************************************************
     Function: removeTable
     Description: Function removes the old table and appends a new
                  empty table.
     ***************************************************************/
    function removeTable()
    {
        // Delaying the loading gif
        count = 0;
        setTimeout(loading, 50);
        // Remove old table and append new one for dynamic content to be added
        $('#dtable').remove();
        $('#dtable_wrapper').remove();
        $('#divTable').append('<table id="dtable" class="table table-bordered table-hover" width="100%" cellspacing="0" data-page-length=\'20\'>\n' +
            '                \n' +
            '            </table>');
    }

    /***************************************************************
     Function: loading
     Description: Function displays a loading gif if the desired
                  datatable takes too long to load.
     ***************************************************************/
    function loading()
    {
        console.log(count);
        if(count == 0)
        {
            // Showing loader modal
            $("#loaderModal").modal("show");
        }
    }

    /***************************************************************
     Function: getTableList
     Description: Appends html of selected data-table to DDL
     ***************************************************************/
    function getTableList()
    {
        // Get desired database
        var dbname = $('#ddlDatabases').val();

        $.ajax({
            url: "./show_tables.php",
            method: "POST",
            data: {dbname: dbname},
            success:function(response)
            {
                $('#ddlTables').empty();
                $('#ddlTables').append(response);
                getUserInput();
            }
        });
    }

    /***************************************************************
     Function: getUserInput
     Description: Passes User Input to change datatable when new
                  fields are selected from the DDLs
     ***************************************************************/
    function getUserInput()
    {
        // Get selected value
        var dbname = $('#ddlDatabases').val();
        var tblname = $('#ddlTables').val();
        removeTable();

        $.ajax({
            url: "./table_processing.php",
            method: "POST",
            data: {dbname: dbname, tblname: tblname},/*
            beforeSend: function() {

            },*/
            success:function(response)
            {
                showTable(JSON.parse(response));
            }
        });
    }

    /*******************************************************************
     Function: showTable
     Description: Function displays a dynamic data-table of the chosen
                  database and table.
     *******************************************************************/
    function showTable(response)
    {
        var data = response["data"];
        var columns = response["columns"];

        // Example dtable using this method: https://datatables.net/examples/ajax/objects.html
        var table = $('#dtable').DataTable({
            processing: true,
            serverside: true,
            lengthMenu: [20, 40, 60, 80, 100],
            destroy: true,
            order: [],
            scrollX: true,
            scrollY: "30rem",
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            initComplete: function( settings, json ) {
                //console.log("complete bruh");
                //$('#loadingContainer').hide();
                // Showing loader modal
                $("#loaderModal").modal("hide");
                count = count + 1;
            },

            // Dynamically making table
            data:data,
            columns: columns,
        });

        modals(table, columns);
    }

    /*******************************************************************
     Function: modals
     Description: Mother of Modals function. Allows modals to appear
                  when a row is clicked on.
                  - Calls functions that
                  destroys/creates modal templates.
                  - Calls function that
                  fills modal with dynamic content from rows.
                  - Handles deleting row content with modals
                  - Destroys/recreates response modals for instant
                  feedback reports.
     *******************************************************************/
    function modals(table, columns)
    {
        // When the user clicks on a row on the data table
        $('#dtable tbody').on('click', 'tr', function () {
            var rowData = table.row( this ).data();

            // Shows modals
            appendModal();
            $('#rowModal').modal('show');
            fillModal(rowData);

            // Getting ID name to dynamically delete any row the admin wishes
            // (EX: authorID = author || documentID = document) for dynamic SQL delete statement
            var idLength = columns[0]["data"].length;
            var columnID = columns[0]["data"].substr(0,idLength-2);
            //console.log(columnID);
            var idNumber = rowData[columnID+"ID"];

            $('#delete').click(function() {
                var answer = confirm("Are you sure you want to delete this ticket?");

                if(answer)
                {
                    // INPUT AJAX HERE
                    console.log("true bruh");
                    var dbname = $('#ddlDatabases').val();
                    var tblname = $('#ddlTables').val();

                    $.ajax({
                        url: "./modal_processing.php",
                        method: "POST",
                        data: {delete: true, columnID: columnID, dbname: dbname, tblname: tblname, idNumber: idNumber},
                        success:function(response)
                        {
                            $('#status').val(true);

                            // Adding text to response modal
                            $('#responseModalBody').empty();
                            $('#responseModalBody').append('<p>Database: '+ dbname +'</p>');
                            $('#responseModalBody').append('<p>Table: '+ tblname +'</p>');
                            $('#responseModalBody').append('<p>'+ columnID +'ID:  '+ idNumber +'</p>');
                            $('#responseModalBody').append('<p>Row deleted successful! Thank you!</p>');
                            $("#rowModal").modal('hide');
                            $('#responseModal').modal('show');
                            console.log(response);
                        }
                    });

                }
                else
                {
                    $('#responseModalBody').empty();
                    $('#responseModalBody').append('<p>The content could not be deleted! The server is not responding properly, please report this bug.</p>');
                    $("#rowModal").modal('hide');
                    $('#responseModal').modal('show');
                }
            });
        } );
    }
    /***************************************************************
     Function: appendModal
     Description: Removes any past modals and appends a new template
                  for dynamic content to be placed.
     ***************************************************************/
    function appendModal()
    {
        // Appending modal
        $('#rowModal').remove();
        $('#Modal').append('<div class="modal fade" id="rowModal" tabindex="-1" role="dialog" aria-labelledby="rowModal" aria-hidden="true">\n' +
            '        <div class="modal-dialog modal-lg" role="document">\n' +
            '            <div class="modal-content">\n' +
            '                <div class="modal-header">\n' +
            '                    <h5 class="modal-title" id="rowModalTitle">Row Content</h5>\n' +
            '                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
            '                        <span aria-hidden="true">&times;</span>\n' +
            '                    </button>\n' +
            '                </div>\n' +
            '                <form id="updateDataBase">\n' +
            '                    <div class="modal-body" id="rowModalBody">\n' +
            '                        \n' +
            '                    </div>\n' +
            '                    <div class="modal-footer">\n' +
            //'                        <input type="submit" value="Save Changes" class="btn btn-primary" id="submit">\n' +
            '                        <input type="button" value="Delete" class="btn btn-danger" id="delete">\n' +
            '                    </div>\n' +
            '                </form>\n' +
            '            </div>\n' +
            '        </div>\n' +
            '    </div>');
    }

    /***************************************************************
     Function: fillModal
     Description: Dynamically fills the modal with the column names
                  and the content in the selected row
     ***************************************************************/
    function fillModal(rowData)
    {
        var dbname = $('#ddlDatabases').val();
        var tblname = $('#ddlTables').val();
        //console.log(rowData);

        // iterates for every column and fills modal
        for(var property in rowData)
        {
            // Apply a link here
            if(tblname == "document" && property == "libraryindex")
            {
                var response = $.ajax({
                    url: "./link_processing.php",
                    method: "POST",
                    async: false,
                    data: {dbname: dbname},
                    success:function(response)
                    {
                        return response;
                    }
                });
                //console.log(response);

                html = ' <div class="form-group row">\n' +
                    '                        <label style="" class="col-sm-3 col-form-label" for="txtSubject1">' + property + '</label>\n' +
                    '                        <div class="col-sm-8">\n' +
                    '                            <a target="_blank" rel="noopener noreferrer" href="../../Templates/Map/review.php?doc=' + rowData["documentID"] + '&col=' + response["responseText"] + '">' + rowData["libraryindex"] + '</a>\n' +
                    '                        </div>\n' +
                    '                    </div>'
            }
            else // Else normal text field
            {
                html = ' <div class="form-group row">\n' +
                    '                        <label style="" class="col-sm-3 col-form-label" for="txtSubject1">' + property + '</label>\n' +
                    '                        <div class="col-sm-8">\n' +
                    '                            <input type="text" name="txtSubject" id="txtSubject1" size="32" class="form-control" value="' + rowData[property] + '" required readonly/>\n' +
                    '                        </div>\n' +
                    '                    </div>'
            }

            $("#rowModalBody").append(html);
            //console.log(property);
        }
    }
</script>
</body>
</html>
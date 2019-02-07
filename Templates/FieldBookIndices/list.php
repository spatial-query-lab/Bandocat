<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
//Get collection name and action
if(isset($_GET['col']))
{
    $collection = $_GET['col'];
    require('../../Library/DBHelper.php');
    $DB = new DBHelper();
    $config = $DB->SP_GET_COLLECTION_CONFIG($collection);
}
else header('Location: ../../');

?>
<!doctype html>
<html lang="en">
<!-- HTML HEADER -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title><?php echo $config['DisplayName']; ?></title>

    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel = "stylesheet" type="text/css" href="../../ExtLibrary/DataTables-1.10.12/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
    <script>
        /**********************************************
         * Function:  DeleteDocument
         * Description: deletes the document from the database
         * Parameter(s):
         * col (in string) - name of the collection
         * id (in Int) - document id
         * Return value(s):
         * $result true if success, false if failed
         ***********************************************/
        function DeleteDocument(col,id)
        {
            $response = confirm('Are you sure you want to delete this document?');
            if($response)
            {
                $.ajax({
                    type: 'post',
                    url: 'form_processing.php',
                    data: {"txtAction": "delete", "txtCollection": col, "txtDocID": id},
                    success:function(data){
                        var json = JSON.parse(data);
                        var msg = "";
                        for(var i = 0; i < json.length; i++)
                            msg += json[i] + "\n";
                        alert(msg);
                        $('#dtable').DataTable().ajax.reload();
                    }
                });
            }
        }
        //When the document is ready, begin the rendering of the datatable
        $(document).ready(function()
        {
            var collection_config = <?php echo json_encode($config); ?>;
            $('#page_title').text(collection_config.DisplayName);

            var table = $('#dtable').DataTable( {
                "processing": true,
                "serverSide": true,
                "lengthMenu": [20, 40 , 60, 80, 100],
                "bStateSave": false,
                "order": [[ 0, "desc" ]],
                "columnDefs": [
                    //column Document Index: Replace with Hyperlink
                    {
                        "render": function ( data, type, row ) {
                            return "<a target='_blank' href='review.php?doc=" + data + "&col=" + collection_config['Name'] + "'>Edit/View</a>" ;
                        },
                        "targets": 0
                    },
                   // { "searchable": false, "targets": 0 },
                    //column : Page Number
                    {
                        "render": function ( data, type, row ) {
                            return data;
                        },
                        "targets": 4
                    },
                    //column : NeedsReview
                    {
                        "render": function ( data, type, row ) {
                            if(data == 1)
                                return "Yes";
                            return "No";
                        },
                        "targets": 5
                    },
                    {
                        "render": function ( data, type, row ) {
                            return "<a href='#' onclick='DeleteDocument(" + JSON.stringify(collection_config.Name) + "," + row[0] + ")'>Delete</a>";
                        },
                        "targets": 6
                    },

                ],
                "ajax": "list_processing.php?col=" + collection_config.Name,
                "initComplete": function() {
                    this.api().columns().every( function () {
                        var column = this;
                        switch(column[0][0]) //column number
                        {
                            //case: use dropdown filtering for column that has boolean value (Yes/No or 1/0)
                            case 5: //column needsreview
                            case 6: //column completed?
                                var select = $('<select style="width:100%"><option value="">Filter...</option><option value="1">Yes</option><option value="0">No</option></select>')
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
                            //case: dropdown table of contents/general index
                            case 1: //column page type
                                var select = $('<select style="width:100%"><option value="">Filter...</option><option value="Table of Contents">Table of Contents</option><option value="General Index">General Index</option></select>')
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
                                //search text box
                            case 2:
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
                        }
                    } );
                },
            } );

            //hide first column (DocID)
            table.column(0).visible(true);
            table.column(6).visible(false);
            <?php if($session->isAdmin()){ ?> //table.column(6).visible(true); <?php } ?>


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
            $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 55);
        });
    </script>

</head>
<!-- HTML BODY -->
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ?>
        </div>
        <div id="divright">
            <h2 id="page_title">Title</h2>
            <div id="divscroller">
                <table id="dtable" class="display compact cell-border hover stripe" cellspacing="0" width="100%" data-page-length='20'>
                    <thead>
                    <tr>
                        <th width="70px"></th>
                        <th width="100px">Page Type</th>
                        <th>Library Index</th>
                        <th width="280px">Book Title</th>
                        <th width="50px">Page Number</th>
                        <th width="30px">Needs Review</th>
                        <th width="40px"></th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th width="70px"></th>
                        <th width="110px">Page Type</th>
                        <th>Library Index</th>
                        <th width="100px">Book Title</th>
                        <th width="50px">Page Number</th>
                        <th width="50px" class="thBoolean">Needs Review</th>
                        <th width="40px"></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../../Master/footer.php'; ?>
</body>
</html>

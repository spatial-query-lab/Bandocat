<?php
//for admin use only
include '../../Library/SessionManager.php';
$session = new SessionManager();
if($session->isAdmin()) {
    require('../../Library/DBHelper.php');
    $DB = new DBHelper();
}
else header('Location: ../../');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Ticket</title>

    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel = "stylesheet" type="text/css" href="../../ExtLibrary/DataTables-1.10.12/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var docID = '';

            var table = $('#dtable').DataTable( {
                "processing": true,
                "serverSide": true,
                "lengthMenu": [20, 40 , 60, 80, 100],
                "bStateSave": false,
                "aaSorting": [ [5,'asc'], [3,'desc'] ],
                "columnDefs": [
                    //column Ticket Index: Replace with Hyperlink
                    {
                        "render": function ( data, type, row ) {
                            return "<a href='ticketview.php?id=" + data + "' target='_blank' >View</a>" ;
                        },
                        "targets": 0
                    },
                    //column Collection
                    {
                        "render": function ( data, type, row ) {
                            //Object that stores the collection name
                            colData = data;
                            return data;
                        },
                        "targets": 1
                    },
                    //column Subject
                    {
                        "render": function ( data, type, row ) {
                            //Stores the collection name to the subject collection variable
                            switch(colData) {
                                case 'Blucher Maps':
                                    var dbCol = 'bluchermaps';
                                    var file = 'Map';
                                    break;
                                case 'Green Maps':
                                    var dbCol = 'greenmaps';
                                    var file = 'Map';
                                    break;
                                case 'Job Folder':
                                    var dbCol = 'jobfolder';
                                    var file = 'Folder';
                                    break;
                                case 'Blucher Field Book':
                                    var dbCol = 'blucherfieldbook';
                                    var file = 'FieldBook';
                                    break;
								case 'PennyFenner':
                                    var dbCol = 'pennyfenner';
                                    var file = 'Map';
                                    break;
                                case 'Map Indices':
                                    var dbCol = 'mapindices';
                                    var file = 'Indices';
                                    break;
                            }
                            //Object with subject collection and subject/library index
                            var subCol = {"data":[{"subjectCol": dbCol, "subject": data}]};
							console.log(subCol);
                            $.ajax({
                                url: 'ticketLink.php',
                                type: 'post',
                                data: subCol,
                                success: function (id) {
                                    id = JSON.parse(id);
                                    var td = $('td:contains('+data+')')[0];
                                    if(id.data[0][0] != false)
                                        $(td).html("<a href='../../Templates/" + file + "/review.php?doc=" + id.data[0][0] + "&col=" + dbCol + "' target='_blank' >"+ id.data[0][1] +"</a>");
                                }
                            });
                            return data
                        },
                        "targets": 2
                    },
                    //column : Submitted Date
                    {
                        "render": function ( data, type, row ) {
                            return data;
                        },
                        "targets": 3
                    },
                    //column : Poster
                    {
                        "render": function ( data, type, row ) {
                            return data;
                        },
                        "targets": 4
                    },
                    //column : Status
                    {
                        "render": function ( data, type, row ) {
                            if(data == 1)
                                return 'Closed';
                            return 'Open';
                        },
                        "targets": 5
                    },

                ],
                "ajax": "list_processing.php",
                "initComplete": function() {
                    this.api().columns().every( function () {
                        var column = this;
                        switch(column[0][0]) //column number
                        {
                            //case: use dropdown filtering for column that has boolean value (Yes/No or 1/0)
                            case 5: //Status column
                                var select = $('<select style="width:100%"><option value="">Filter...</option><option value="0">Open</option><option value="1">Closed</option></select>')
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
                            case 1:
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

            //sorted by submission date
//            table
//                .column( '3:visible' )
//                .order( 'desc' )
//                .draw();

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
            $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 35);
        });
    </script>
</head>
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
                    <?php include '../../Master/header.php';
                    include '../../Master/sidemenu.php' ?>
        </div>
        <div id="divright">
                    <h2 id="page_title">Ticket</h2>
                    <div id="divscroller">
                        <table id="dtable" class="display compact cell-border hover stripe" cellspacing="0" width="100%" data-page-length='20'>
                            <thead>
                            <tr>
                                <th></th>
                                <th>Collection</th>
                                <th>Subject / Library Index</th>
                                <th>Submitted Date</th>
                                <th>Submitter</th>
                                <th>Status</th>
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
</html>

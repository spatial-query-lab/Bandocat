<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
    if(isset($_GET['col']))
    {
        $collection = $_GET['col'];
        require('../../Library/DBHelper.php');
        $DB = new DBHelper();
        //get appropriate db
        $config = $DB->SP_GET_COLLECTION_CONFIG($collection);
    }
    else header('Location: ../../');

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

    <title>Edit/View <?php echo $config["DisplayName"]; ?></title>

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">

    <!-- END HTML HEADER -->
</head>
<!--  HTML BODY -->
<body>
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container" style="">
    <div class="row">
        <div class="col" style="padding-bottom: 15px;">
            <!-- Put Page Contents Here -->
            <h1 class="text-center"><?php echo $config["DisplayName"]; ?></h1>
            <hr>
            Show/Hide Subtitle <input name="checkbox_subtitle" type="checkbox" id="checkbox_subtitle" />
            <!-- The Datatable -->
            <table id="dtable" class="table table-striped table-bordered" width="100%" cellspacing="0" data-page-length='20'>
                <thead>
                <tr>
                    <th></th>
                    <th>Library Index</th>
                    <th>Document Title</th>
                    <th>Document Subtitle</th>
                    <th>Customer</th>
                    <th>Author</th>
                    <th>End Date</th>
                    <th>Has Coast</th>
                    <th>Needs Review</th>
                    <th></th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th></th>
                    <th>Library Index</th>
                    <th>Document Title</th>
                    <th>Document Subtitle</th>
                    <th>Customer</th>
                    <th>Author</th>
                    <th>End Date</th>
                    <th>Has Coast</th>
                    <th>Needs Review</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>

        </div> <!-- Col-9 -->
    </div> <!-- row -->
</div><!-- Container-fluid -->
<?php include "../../Master/bandocat_footer.php"; ?>
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

<script>
    $(window).resize(function() {
        var docHeight = $(window).height() - $('#megaMenu').height();
        var footerHeight = $('#footer').height();
        var footerTop = $('#footer').position().top + footerHeight;

        if (footerTop < docHeight)
            $('#footer').css('margin-top', 0 + (docHeight - footerTop) + 'px');
    });
</script>

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
                { "searchable": false, "targets": 0 },
                //column Title
                {
                    "render": function ( data, type, row ) {
                        return data;
                    },
                    "targets": 2
                },
                //column Subtitle
                {
                    "render": function ( data, type, row ) {
                        if(data.length > 38)
                            return data.substr(0,38) + "...";
                        return data;
                    },
                    "targets": 3
                },
                //{ "searchable": false, "targets": 3 },
                //column : Date
                {
                    "render": function ( data, type, row ) {
                        if(data == "00/00/0000")
                            return "";
                        return data;
                    },
                    "targets": 6
                },
                //column : HasCoast
                {
                    "render": function ( data, type, row ) {
                        if(data == 1)
                            return "Yes";
                        return "No";
                    },
                    "targets": 7
                },
                // { "searchable": false, "targets": 6 },
                //column : NeedsReview
                {
                    "render": function ( data, type, row ) {
                        if(data == 1)
                            return "Yes";
                        return "No";
                    },
                    "targets": 8
                },
                // { "searchable": false, "targets": 7 },
                {
                    "render": function ( data, type, row ) {
                        return "<a href='#' onclick='DeleteDocument(" + JSON.stringify(collection_config.Name) + "," + row[0] + ")'>Delete</a>";
                    },
                    "targets": 9
                },

            ],
            "ajax": "list_processing.php?col=" + collection_config.Name,
            "initComplete": function()
            {
                this.api().columns().every( function () {
                    var column = this;
                    switch(column[0][0]) //column number
                    {
                        //case: use dropdown filtering for column that has boolean value (Yes/No or 1/0)
                        case 7: //column hascoast
                        case 8: //column needsreview
                            var select = $('<select class="form-control"><option value="">Filter...</option><option value="1">Yes</option><option value="0">No</option></select>')
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

                        case 1: //lib index
                        case 2: //title
                        case 3: //subtitle
                        case 4: //customer
                        case 5: //author
                        case 6: //enddate
                            var input = $('<input type="text" class="form-control" placeholder="Search..." value=""></input>')
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
        //hides the columns responsible for need's input
        table.column(9).visible(false);
        <?php if($session->isAdmin()){ ?> table.column(9).visible(true); <?php } ?>
        // show or hide subtitle
        table.column(3).visible(false);
        $('#checkbox_subtitle').change(function (e) {
            e.preventDefault();
            // Get the column API object
            var column = table.column(3);
            // Toggle the visibility
            column.visible( ! column.visible() );
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
    });
</script>
</body>
</html>

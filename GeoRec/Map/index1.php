<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(isset($_GET['col'])) {
    $collection = $_GET['col'];
    require('../../Library/DBHelper.php');
    require('../../Library/MapDBHelper.php');
    $DB = new MapDBHelper();
    $config = $DB->SP_GET_COLLECTION_CONFIG($collection);
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

    <title>GeoRectification - <?php echo $config['DisplayName']; ?></title>

    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <link rel = "stylesheet" type = "text/css" href = "css/styles.css" >
    <link rel = "stylesheet" type="text/css" href="../../ExtLibrary/DataTables-1.10.12/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var collection_config = <?php echo json_encode($config); ?>;
            $('#page_title').text(collection_config.DisplayName + " Georectification");

            var table = $('#dtable').DataTable( {
                "processing": true,
                "serverSide": true,
                "lengthMenu": [20, 40 , 60, 80, 100],
                "bStateSave": false,
                "order": [[ 0, "desc" ]],
                "columnDefs": [
                    //column Document Index: Replace with Hyperlink, allows user to edit/review the document
                    {
                        "render": function ( data, type, row ) {
                            return "<a target='_blank' href='../../Templates/Map/review.php?doc=" + data + "&col=" + collection_config['Name'] + "'>Edit/View</a>" ;
                        },
                        "targets": 0
                    },
                    { "searchable": false, "targets": 0 }, //disable searching by documentID
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
                    //{ "searchable": false, "targets": 3 }, //disable searching by subtitle
                    //column : Date
                    {
                        "render": function ( data, type, row ) {
                            if(data == "00/00/0000")
                                return "";
                            return data;
                        },
                        "targets": 5
                    },
                    //column : HasCoast
                    {
                        "render": function ( data, type, row ) {
                            if(data == 1)
                                return "Yes";
                            return "No";
                        },
                        "targets": 6
                    },
                   // { "searchable": false, "targets": 6 },
                    //column : Filename of Front Scan (hidden)
                    {
                        "render": function ( data, type, row ) {
                            return data;
                        },
                        "targets": 7
                    },
                    //column : Filename of Back Scan (hidden)
                    {
                        "render": function ( data, type, row ) {
                            return data;
                        },
                        "targets": 8
                    },
                    { "searchable": false, "targets": 7 }, //disable search for file name
                    { "searchable": false, "targets": 8 }, //disable search for file name back
                    //column : HasPOI
                    {
                        "render": function ( data, type, row ) {
                            if(data == 1)
                                return "Yes";
                            return "No";
                        },
                        "targets": 9
                    },
                    //column : Rectifiability
                    {
                        "render": function ( data, type, row ) {
                            switch(data)
                            {
                                case "POOR":
                                    return "<span style='color:lightgray'>" + data + "</span>";
                                case "GOOD":
                                    return "<span style='color:'>" + data + "</span>";
                                case "EXCELLENT":
                                    return "<span style='color:#00BC65'>" + data + "</span>";
                                default: return data;
                            }
                        },
                        "targets": 10
                    },
                    //column georec status
                    //This column translates the GeoRec Front Status from int value to string value
                    {
                        "render": function ( data, type, row ) {
                            switch(data)
                            {
                                case "0": return "<span>Not Rectified</span>";
                                case "1": return "<span style='color:#00BC65'>Rectified</span>";
                                case "2": return "<span style='color:red'>Not Rectifiable</span>";
                                case "3": return "<span style='color:darkkhaki'>Needs Review</span>";
                                case "4": return "<span style='color:darkred'>Research Required</span>";
                                default: return "<span>Unknown</span>";
                            }
                        },
                        "targets": 11
                    },
                    //This column translates the GeoRec Back Status from int value to string value
                    {
                        "render": function ( data, type, row ) {
                            if(row[8] == "")
                                return "";
                            switch(data)
                            {
                                case "0": return "<span>Not Rectified</span>";
                                case "1": return "<span style='color:#00BC65'>Rectified</span>";
                                case "2": return "<span style='color:red'>Not Rectifiable</span>";
                                case "3": return "<span style='color:darkkhaki'>Needs Review</span>";
                                case "4": return "<span style='color:darkred'>Research Required</span>";
                                default: return "<span>Unknown</span>";
                            }
                        },
                        "targets": 12
                    },
                    //columnn georectify
                    {
                        "render": function ( data, type, row ) {
                            switch(row[8]) //based on Georec Status column (column 8)
                            {
                                default:
                                    var type1 = "front";
                                    var type2 = "back";
                                    return "<a href='' id='aRecFront' onclick='makeTiles(" + '"' + collection_config['Name'] + '"' + "," + row[0] + "," + '"' + type1 + '"' + ");event.preventDefault();'>Front</a>" +
                                      " | "  + "<a href='' id='aRecFront' onclick='makeTiles(" + '"' + collection_config['Name'] + '"' + "," + row[0] + "," + '"' + type2 + '"' + ");event.preventDefault();'>Back</a>";
                                case "": //no back
                                    var type1 = "front";
                                    return "<a href='' id='aRecFront' onclick='makeTiles(" + '"' + collection_config['Name'] + '"' + "," + row[0] + "," + '"' + type1 + '"' + ");event.preventDefault();'>Front</a>";
                            }
                        },
                        "targets": 13
                    }


                ],
                "ajax": "list_processing.php?col=" + collection_config.Name,
                "initComplete": function() {
                    this.api().columns().every( function () {
                        var column = this;
                        switch(column[0][0]) //column number
                        {
                            //case: use dropdown filtering for column that has boolean value (Yes/No or 1/0)
                            case 6: //column hascoast
                            case 9: //column POI
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
                            //case: GeoRec Front/Back status columns
                            case 11: //column GeoRec Front Status
                            case 12: //column GeoRec Back Status
                                var select = $('<select style="width:100%"><option value="">Filter...</option><option value="0">Not Rectified</option><option value="1">Rectified</option><option value="2">Not Rectifiable</option><option value="3">Needs Review</option><option value="4">Research Required</option></select>')
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
                            //case: columns have limited unique values
                            case 10:
                                var select = $('<select style="width:100%"><option value="">Filter...</option></select>')
                                    .appendTo( $(column.footer()).empty() )
                                    .on( 'change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex(
                                            $(this).val()
                                        );

                                        column
                                            .search(val)
                                            .draw();
                                    } );

                                column.data().unique().sort().each( function ( d, j ) {
                                    select.append( '<option value="'+d+'">'+d+'</option>' )
                                } );
                                break;
                            case 1:
                            case 2:
                            case 3:
                            case 4:
                            case 5:
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
            table.column(7).visible(false); //hide file name col
            table.column(8).visible(false); // hide file name back col
            <?php if($session->hasWritePermission()){ ?> table.column(10).visible(true); <?php }//geo rectify only visible for writer ?>
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
            //resize height of the scroller
            $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 55);
        });
    </script>
</head>
<div id="fade_3"></div>
<div id="modal_3">
    <img id="loader_3" src="../../Images/loading.gif" />
    <div id = "text" >Generating Tiles... <br> (this may take a moment)</div>
</div>
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ?>
        </div>
        <div id="divright">
            <h2 id="page_title">Title</h2>
            <table width="100%">
                <tr>
                    <td style="float:right;font-size:13px" colspan="100%"><input name="checkbox_subtitle" type="checkbox" id="checkbox_subtitle" />Show/Hide Subtitle</td>
                </tr>
            </table>
            <div id="divscroller">
                <table id="dtable" class="display compact cell-border hover stripe" cellspacing="0" width="100%" data-page-length='20'>
                    <thead>
                    <tr>
                        <th></th>
                        <th width="100px">Library Index</th>
                        <th>Document Title</th>
                        <th width="280px">Document Subtitle</th>
                        <th width="150px">Customer</th>
                        <th width="70px">End Date</th>
                        <th width="40px">Has Coast</th>
                        <th>Front Map</th>
                        <th>Back Map</th>
                        <th width="40px">Has POI</th>
                        <th>Rectifiability</th>
                        <th width="120px">GeoRec Front Status</th>
                        <th width="120px">GeoRec Back Status</th>
                        <th width="60px">GeoRectify</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th width="100px">Library Index</th>
                        <th>Document Title</th>
                        <th width="280px">Document Subtitle</th>
                        <th width="150px">Customer</th>
                        <th width="70px">End Date</th>
                        <th width="40px">Has Coast</th>
                        <th>Front Map</th>
                        <th>Back Map</th>
                        <th>Has POI</th>
                        <th>Rectifiability</th>
                        <th width="120x">GeoRec Front Status</th>
                        <th width="120px">GeoRec Back Status</th>

                    </tr>

                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../../Master/footer.php'; ?>
</body>
<script>
    //This function generate tiles and return information about the image for georectification
    //Parameters:
    //collection: collection's parameter name
    //docID: unique value, use to access document information on server side
    //type : specify front or back scan of the document
    function makeTiles(collection,docID,type){
        openModal(3);
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "php/tileCreator.php",
            data: {"collection": collection,"docID": docID,"type": type},
            success:function(data) {
           window.localStorage.setItem("imageInfo", JSON.stringify(data));
                closeModal(3);
                window.open("georec.php?col="+collection+"&docID="+docID+"&type="+type);
                //alert(data);
            },
            error:function(requestObject, error, errorThrown) {
                closeModal(3);
                alert("Error!");
            }
        });
    }
    //this function shows modal (usually contains a spinner gif) to notify user that a process is loading
    function openModal(num) {
        document.getElementById('modal_' + num).style.display = 'block';
        document.getElementById('fade_' + num).style.display = 'block';
    }
    //this function hides the loading modal
    function closeModal(num) {
        document.getElementById('modal_' + num).style.display = 'none';
        document.getElementById('fade_' + num).style.display = 'none';
    }
</script>
</html>

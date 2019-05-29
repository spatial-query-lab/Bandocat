<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
require('../../Library/DBHelper.php');
$DB = new DBHelper();
require('../../Library/ControlsRender.php');
$Render = new ControlsRender();

$collections = $DB->GET_COLLECTION_TABLE();
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

    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../../Master/bandocat_custom_bootstrap.css">

    <!-- Bootstrap CDN Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css" crossorigin="anonymous">
    <title>Statistics</title>

    <!-- END HTML HEADER -->
</head>
<!--  HTML BODY -->
<body>
<?php include "../../Master/bandocat_mega_menu.php"; ?>
<div class="container-fluid" style="">
            <!-- Container -->
            <div class="container">
                <!-- Put Page Contents Here -->
                <h1 class="text-center">Statistics</h1>
                <hr>
                <div class="row">
                    <!-- Main -->
                        <!-- Select Year -->
                        <div class="pad-bottom">
                            Select Year: &nbsp<select id="ddlYear" name="ddlYear""
                            <?php
                            echo "<option value=''>Select</option>";



                            for($i = date("Y");$i >= 2015;$i--) {
                                if($i == date("Y")) echo "<option selected value='$i'>$i</option>";
                                else
                                    echo "<option value='$i'>$i</option>";
                            }

                            ?>
                            </select>
                        </div>
                </div>

                <div class="row">
                    <div class="col">
                        <nav>
                            <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-catalogPerformance-tab" data-toggle="tab" href="#nav-catalogPerformance" role="tab" aria-controls="nav-catalogPerformance" aria-selected="true">Cataloging Performance</a>
                                <a class="nav-item nav-link" id="nav-transcriptionPerformance-tab" data-toggle="tab" href="#nav-transcriptionPerformance" role="tab" aria-controls="nav-transcriptionPerformance" aria-selected="false">Transcription Performance</a>
                                <?php if($session->isAdmin()) {
                                    echo "<a class=\"nav-item nav-link\" id=\"nav-individualPerformance-tab\" data-toggle=\"tab\" href=\"#nav-individualPerformance\" role=\"tab\" aria-controls=\"nav-individualPerformance\" aria-selected=\"false\">Individual Performance</a>";
                                };?>
                            </div>
                        </nav>

                        <!-- Cataloging Performance -->
                        <div class="tab-content bg-white" id="nav-tabContent">
                            <!-- Cataloging Performance -->
                            <div class="tab-pane fade show active" id="nav-catalogPerformance" role="tabpanel" aria-labelledby="nav-catalogPerformance-tab">
                                <!--<div class="container-fluid">
                                    <div class="row">
                                        <div class="col">
                                            <canvas id="chartWeeklyReport" height="350"><canvas>
                                        </div>
                                        <div class="col">
                                            <canvas id="chartMonthlyReport" height="350"></canvas>
                                        </div>
                                    </div>
                                </div>-->
                                <div class="d-flex flex-column">
                                    <div><canvas id="chartWeeklyReport" height="75"><canvas></div>
                                    <div><canvas id="chartMonthlyReport" height="75"></canvas></div>
                                </div>
                            </div>
                            <!-- Transcription Performance -->
                            <div class="tab-pane fade" id="nav-transcriptionPerformance" role="tabpanel" aria-labelledby="nav-transcriptionPerformance-tab">
                                <div class="d-flex flex-column">
                                    <div><canvas id="tranChartWeeklyReport" height="100"></canvas></div>
                                    <div><canvas id="tranChartMonthlyReport" height="100"></canvas></div>
                                </div>
                            </div>
                            <!-- Indiviual Performance Tab -->
                            <div class="tab-pane fade" id="nav-individualPerformance" role="tabpanel" aria-labelledby="nav-individualPerformance-tab">
                                <div id="IndividualPerformance" class="container">
                                    <br>
                                    <h3>Individual Performance</h3>
                                    <hr>
                                    <!-- Flex Container -->
                                    <div class="d-flex justify-content-between w-50">
                                        <!-- Month -->
                                        <div>Select Month: <select id="ddlMonth" name="ddlMonth"">
                                            <?php
                                            echo "<option value=''>Select</option>";


                                            for($i = 1;$i <= 12;$i++)
                                            {
                                                if($i == date("n"))
                                                {
                                                    $monthName = date('F', mktime(0, 0, 0, $i, 10));
                                                    echo "<option selected value='$i'>$monthName</option>";
                                                }
                                                else
                                                {
                                                    $monthName = date('F', mktime(0, 0, 0, $i, 10));
                                                    echo "<option value='$i'>$monthName</option>";
                                                }

                                            }
                                            ?>
                                            </select>
                                        </div>
                                        <!-- Action -->
                                        <div>Select Action: <select id="ddlAction" name="ddlAction"">
                                            <?php
                                            $Render -> GET_DDL($DB -> GET_ACTION_UNIQUE(),"catalog");
                                            ?>
                                            </select>
                                        </div>
                                    </div> <!-- Flex container -->
                                    <br>
                                    <div id="divIndividualPerformanceTableHolder"></div>
                                    <table id="dptable" class="table table-striped table-bordered" width="100%" cellspacing="0" data-page-length='20'>
                                        <thead>
                                        <tr>
                                            <th width="50px">Count</th>
                                            <th width="50px">User</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div> <!-- Individual Performance Tab -->
                        </div>
                    </div>
                </div>
            <!-- Information and Options -->

                <br>

                <!-- Storage Capacity -->
                <div class="row">
                    <div class="col-7">
                        <h3>Storage Capacity</h3>
                        <hr>
                        <!-- The Datatable -->
                        <table id="storageTable" class="table table-striped table-bordered" width="100%" cellspacing="0" data-page-length='20'>
                            <thead>
                            <tr>
                                <th>Collection</th>
                                <th>Size</th>
                                <th>Date Stored</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="col-5">
                        <h3 id="totalMaps"></h3>
                        <hr>
                        <canvas id="chartDocumentCount"></canvas>
                        <!-- Legend -->
                        <!--<h3>Legend</h3>
                        <ol class="list-unstyled">
                            <li style="color: #FFD700;">Blucher Maps</li>
                            <li style="color: #66CDAA">Green Maps</li>
                            <li style="color: #DDA0DD">Job Folder</li>
                            <li style="color: #6495ED">Blucher Field Books</li>
                            <li style="color: #787878"> Map Indices</li>
                            <li style="color: #BC8F8F">PennyFenner</li>
                            <li style="color: #FF0000">Field Book Indices</li>
                        </ol>-->
                    </div>
                </div> <!-- Storage Capacity -->
                <div class="row pad-bottom">
                    <!-- Storage Capacity Button -->
                    <div class="col">
                        <button class="btn btn-primary" id="storage_button">Update Storage Capacity</button>
                    </div>
                </div>

               <!-- <div class="row">
                    <div class="col">-->
                        <!-- Total Maps Main Content -->
                        <!--<h3>Collection Distribution</h3>
                        <hr>-->
                        <!-- The Datatable -->
                        <!--<table id="collectionDistributionTable" class="table table-striped table-bordered" width="100%" cellspacing="0" data-page-length='20'>
                            <thead>
                            <tr>
                                <th>Container</th>
                                <th>Memory Size</th>
                                <th>Date Last Updated</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>-->
            </div>
    <?php include "../../Master/bandocat_footer.php"; ?>
</div><!-- Container-fluid -->

<!-- Complete JavaScript Bundle -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<!-- Datatables CDN -->
<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>

<!-- Bootstrap JS files for datatables CDN -->
<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>

<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

<script>
    var collections = <?php echo json_encode($collections); ?>;
    var collection_color = ['#FFD700','#66CDAA','#DDA0DD','#6495ED','#787878','#BC8F8F', "#FF0000"];
    var collection_highlight = ['#FF5A5E','#7FFF55','#3e3e3e','#26A6D0','#244400','#D4D4FF','#D4D4FF'];

    // Charts
    var ctx_weekly_performance = document.getElementById("chartWeeklyReport");
    var ctx_monthly_performance = document.getElementById("chartMonthlyReport");
    var ctx_transcription_weekly_performance = document.getElementById("tranChartWeeklyReport");
    var ctx_transcirption_monthly_performance = document.getElementById("tranChartMonthlyReport");
    var weeklyPerformance;
    var monthlyPerformance;
    var tranWeeklyPerformance;
    var tranMonthlyPerformance;

    $(document).ready(function() {
        showTable();
        getCollectionCount();
        createWeeklyPerformance();
        createMonthlyCataloging();
        createWeeklyTranscription();
        createMonthlyTranscription();
        loadIndividualPerformance();
    });

    // If the ddl for the action changes
    $("#ddlMonth").change(function() {
        console.log("month");
        loadIndividualPerformance();
    });

    $("#ddlAction").change(function() {
        console.log("action");
        loadIndividualPerformance();
    });

    //  weekly cataloging
    $("#ddlYear").change(function()
    {
        //reset canvas
        /*$('#chartWeeklyReport').remove();*/
        //$('#divWeeklyCanvasHolder').append('<canvas id="chartWeeklyReport"><canvas>');

        var year = $("#ddlYear").val();
        if(year !== "")
        {
            // Deleting charts first
            weeklyPerformance.destroy();
            monthlyPerformance.destroy();
            tranWeeklyPerformance.destroy();
            tranMonthlyPerformance.destroy();

            // Recreating charts
            createWeeklyPerformance();
            createMonthlyCataloging();
            createWeeklyTranscription();
            createMonthlyTranscription();
            loadIndividualPerformance();
        }
    });

    // This function is used to create the line chart for weekly performance
    function createWeeklyPerformance()
    {
        // Getting the year
        var year = $("#ddlYear").val();

        // Making an ajax call to create line chart
        $.ajax({
            type: "POST",
            url: "./weeklyreports_processing.php?year=" + year,
            success: function (data) {
                var JSONdata = JSON.parse(data);
                //generate total chart
                var weeklyData = [];
                for (var i = 0; i < collections.length; i++) {
                    var array =
                        {
                            label: collections[i].displayname,
                            backgroundColor: "transparent",
                            borderColor: collection_color[i],
                            pointColor: collection_color[i],
                            strokeColor: collection_color[i],
                            borderWidth: 2,
                            hoverBorderColor: "rgba(151,187,205,1)",
                            data: JSONdata.datasets[i]
                        };
                    weeklyData.push(array);
                    array = null;
                }
                //generate weekly input chart
                var dat = {
                    labels: JSONdata.labels,
                    datasets: weeklyData
                };
                //var ctx = new Chart(document.getElementById("chartWeeklyReport").getContext("2d")).Line(dat);

                // Creating line chart
                weeklyPerformance = new Chart(ctx_weekly_performance, {
                    type: "line",
                    data: dat,
                    options: {
                        title: {
                            display: true,
                            text: 'Weekly Performance ' + year
                        },
                        legend: {
                            display: false
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Week'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                }
                            }]
                        }
                    }
                });
            }
        });
    }

    // This function is used to create teh cataloging monthly performance
    function createMonthlyCataloging()
    {
        var year = $("#ddlYear").val();
        // Making an ajax call to get the data that the chart will be filled by
        $.ajax({
            type: "POST",
            url: "./monthlyreports_processing.php?year=" + year,
            success: function (data) {
                var JSONdata = JSON.parse(data);
                //generate total chart
                var monthlyData = [];
                for (var i = 0; i < collections.length; i++) {
                    var array = {
                        label: collections[i].displayname,
                        backgroundColor: "transparent",
                        borderColor: collection_color[i],
                        pointColor: collection_color[i],
                        strokeColor: collection_color[i],
                        borderWidth: 2,
                        hoverBorderColor: "rgba(151,187,205,1)",
                        data: JSONdata[i][0]
                    };
                    monthlyData.push(array);
                    array = null;
                }
                //generate monthly input chart
                var dat = {
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                    datasets: monthlyData
                };
                monthlyPerformance = new Chart(ctx_monthly_performance, {
                    type: "line",
                    data: dat,
                    options: {
                        title: {
                            display: true,
                            text: 'Cataloging Monthly Performance ' + year,
                        },
                        legend: {
                            display: false,
                            position: "bottom"
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                }
                            }]
                        }
                    }
                });
            }
        });
    }

    function getCollectionCount()
    {
        $.ajax({
            type: "POST",
            url: "./collectioncount_processing.php",
            success: function (data)
            {
                var JSONdata = JSON.parse(data);

                //generate total chart
                var pieData = [];
                var pieLabels = [];
                var total = 0;

                for (var i = 0; i < JSONdata.length; i++)
                {
                    pieData.push(JSONdata[i].count);
                    pieLabels.push(JSONdata[i].collection);
                    total += parseInt(pieData[i]);
                }

                var canvasPie = document.getElementById("chartDocumentCount");
                var ctx1 = canvasPie.getContext("2d");
                var PieChart = new Chart(ctx1,{ type: 'pie',
                    data: {
                        datasets: [{
                            data: pieData,
                            backgroundColor: collection_color
                        }],

                        labels: pieLabels
                    },
                    options: {
                        legend: {
                            display: true
                        }
                    }
                });

                //calculate total counts of all collections = SUM of pieData array
                //document.getElementById("totalCount").innerHTML = "Total Maps: " + total;
                document.getElementById("totalMaps").innerHTML = "Total Maps: " + total;
            }
        });
    }

    // TODO: Weekly transcription is not working on the server, this isn't a priority but it needs to be fixed later
    // The problem is in weeklytranscriptionreports_processing.php, its not returning the right data type. Returning an
    // incorrect object
    function createWeeklyTranscription()
    {
        // Getting the year
        var year = $("#ddlYear").val();

        $.ajax({
            type: "POST",
            url: "./weeklytranscriptionreports_processing.php?year=" + year,
            success: function (data) {
                var JSONdata = JSON.parse(data);
                console.log(JSONdata);

                //generate total chart
                var weeklyData = [];
                for (var i = 0; i < collections.length; i++) {
                    var array =
                        {
                            label: collections[i].displayname,
                            backgroundColor: "transparent",
                            borderColor: collection_color[i],
                            pointColor: collection_color[i],
                            strokeColor: collection_color[i],
                            borderWidth: 2,
                            hoverBackgroundColor: collection_highlight[i],
                            hoverBorderColor: "rgba(151,187,205,1)",
                            data: JSONdata.datasets[i]
                        };
                    weeklyData.push(array);
                    array = null;
                }
                //generate weekly input chart
                var dat = {
                    labels: JSONdata.labels,
                    datasets: weeklyData,
                };

                console.log(dat);

                tranWeeklyPerformance = new Chart(ctx_transcription_weekly_performance, {
                    type: "line",
                    data: dat,
                    options: {
                        title: {
                            display: true,
                            text: 'Weekly Transcription Performance ' + year
                        },
                        legend: {
                            display: false,
                            position: "bottom"
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Weeks'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Amount'
                                }
                            }]
                        }
                    }
                });
            }
        });
    }

    function createMonthlyTranscription()
    {
        // Getting the year
        var year = $("#ddlYear").val();

        $.ajax({
            type: "POST",
            url: "./monthlytranscriptionreports_processing.php?year=" + year,
            success: function (data) {
                var JSONdata = JSON.parse(data);
                //generate total chart
                var monthlyData = [];
                for (var i = 0; i < collections.length; i++) {
                    var array = {
                        label: collections[i].displayname,
                        backgroundColor: "transparent",
                        borderColor: collection_color[i],
                        pointColor: collection_color[i],
                        strokeColor: collection_color[i],
                        borderWidth: 2,
                        hoverBackgroundColor: collection_highlight[i],
                        hoverBorderColor: "rgba(151,187,205,1)",
                        data: JSONdata[i][0]
                    };
                    monthlyData.push(array);
                    array = null;
                }
                //generate monthly input chart
                var dat = {
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                    datasets: monthlyData,
                };

                tranMonthlyPerformance = new Chart(ctx_transcirption_monthly_performance, {
                    type: "line",
                    data: dat,
                    options: {
                        title: {
                            display: true,
                            text: 'Monthly Transcription Performance ' + year
                        },
                        legend: {
                            display: false
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Amount'
                                }
                            }]
                        }
                    }
                });
            }
        });
    }

    // If the ddl for the month changes
    /*$("ddlYear").change(function() {

       loadIndividualPerformance();
    });*/



    // Updating the table
    function loadIndividualPerformance()
    {
        var year = $("#ddlYear").val();
        var month = $("#ddlMonth").val();
        var action = $("#ddlAction").val();
        var table = $('#dptable').DataTable(
            {

                "processing": false,
                "serverSide": false,
                "destroy": true,
                "lengthMenu": [20, 40, 60, 80, 100],
                "bStateSave": false,
                "order": [[0, 'desc'], [1, 'asc']],
                "ajax": "individual_processing.php?year=" + year + "&month=" + month + "&action=" + action
            });
    }
</script>
<script>
    // Used to show the datatable
    function showTable() {
        // Creating data table and defining its properties
        var table = $('#storageTable').DataTable({
            "processing": true,
            "serverside": true,
            "lengthMenu": [20, 40, 60, 80, 100],
            "destroy": true,

            "initComplete": function ()
            {
                console.log("Table done loading...");
            },

            // Getting select statement
            ajax:
            {
                url: "./storagestatistics_processing.php"
            },

            columns: [
                {data: 'collection'},
                {data: 'size'},
                {data: 'storage_date'}
            ]
        });
    }
</script>
</body>
</html>

<?php
//$time = microtime();
//$time = explode(' ', $time);
//$time = $time[1] + $time[0];
//$start = $time;
//?>

<?php
//for admin use only
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Statistics</title>
    <link rel = "stylesheet" type = "text/css" href = "../../Master/master.css" >
    <script type="text/javascript" src="../../ExtLibrary/jQuery-2.2.3/jquery-2.2.3.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/Chart.js"></script>
    <link rel = "stylesheet" type="text/css" href="../../ExtLibrary/DataTables-1.10.12/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../../ExtLibrary/DataTables-1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="./fireworks.js"></script>
</head>
<body>
<div id="wrap">
    <div id="main">
        <div id="divleft">
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ?>
        </div>
        <div id="divright">
            <h2 id="page_title">Statistics</h2>
            <div id="table-header_right">Select Year: <select id="ddlYear" name="ddlYear"">
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
            <div id="divscroller">
                <table><tr>
                        <td style="vertical-align: top">
                            <!------------------------------------------------->
                            <ul class="tab">
                                <li><a href="javascript:void(0)" class="tablinks" id="idMonthlyPerformance" onclick="openTab(event, 'MonthlyPerformance');"> Cataloging Performance</a></li>
                                <li><a href="javascript:void(0)" class="tablinks" id="idTranscriptionPerformance" onclick="openTab(event, 'Transcription')">Transcription Performance</a></li>
                                <?php if($session->isAdmin()) {
                                    echo '<li ><a href = "javascript:void(0)" class="tablinks" id = "idIndividualPerformance" onclick = "openTab(event,' .  "'IndividualPerformance')" . '"> Individual Performance </a ></li >';
                                };?>
                            </ul>
                            <div id="MonthlyPerformance" class="tabcontent">
                                <h3>Cataloging Monthly Performance <span class="spanYear"></span></h3>
                                <div id="divMonthlyPerformanceCanvasHolder"></div>

                            </div>

                            <div id="Transcription" class="tabcontent">
                                <h3>Transcription Monthly Performance</h3>
                                <div id="divTranscriptionCanvasHolder"></div>
                            </div>

                            <div id="IndividualPerformance" class="tabcontent">
                                <h3>Individual Performance</h3>
                                <div style="display:inline"  id="table-header_left">Select Month: <select id="ddlMonth" name="ddlMonth"">
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
                                    </select></div>
                                <div style="display:inline" id="table-header_right">Select Action: <select id="ddlAction" name="ddlAction"">
                                    <?php
                                    $Render -> GET_DDL($DB -> GET_ACTION_UNIQUE(),"catalog");
                                    ?>
                                    </select></div>
                                <div id="divIndividualPerformanceTableHolder"></div>
                                <table id="dptable" class="display compact cell-border hover stripe" cellspacing="0" width="100%" data-page-length='20'>
                                    <thead>
                                    <tr>
                                        <th width="50px">Count</th>
                                        <th width="50px">User</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                            <!------------------------------------------------>


                        </td>
                        <td style="padding-left:60px;vertical-align: top">
                            <h3 id="titleDocumentCount">Total Maps/Documents per Collection </h3>
                            <canvas id="chartDocumentCount" height="325" width="350"></canvas>
                            <div style="text-align: center;font-weight: bold;margin-top:15px">Total: <span id="spanTotalCount">100000</span> documents  </div>
                            <div id="storage_capacity">
                                <h3 id="storage_header">Storage Capacity</h3>
                                <div id="storage_info" style="border-size: 1px; border-width: thin; border-style:solid; border-color:LightGray; padding:5px">
                                    <img src="../../Images/loading.gif"  style="width:15px;height:15px;">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <h3 id="weeklyPerfHeader">Weekly Performance <span class="spanYear"></span></h3>
                            <div class="thisdiv" id="divWeeklyCanvasHolder"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>


    </div>
</div>
<?php include '../../Master/footer.php'; ?>
<div id="divscroller"></div>
</body>
<script>
    function openTab(event, tabName)
    {

        var i;
        var tabContent;
        var tabLinks;

        tabContent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabContent.length; i++) {
            tabContent[i].style.display = "none";
        }
        tabLinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tabLinks.length; i++) {
            tabLinks[i].className = tabLinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        event.currentTarget.className += " active";

        switch(tabName)
        {
            case "Transcription":
                $('#chartWeeklyReport').remove();
                $('#chartWeeklyTranscriptionReport').remove();
                document.getElementById("weeklyPerfHeader").style.display = 'none';
                break;
//                //reset canvas
//                $('#chartWeeklyReport').remove();
//                $('#chartWeeklyTranscriptionReport').remove();
//                $('#divWeeklyCanvasHolder').append('<canvas id="chartWeeklyTranscriptionReport" height="500" width="1000"><canvas>');
//
//                var year = $("#ddlYear").val();
//                if (year != "") {
//                    $(".spanYear").text(year);
//                    $.ajax({
//                        type: "POST",
//                        url: "./weeklytranscriptionreports_processing.php?year=" + year,
//                        success: function (data)
//                        {
//                            var JSONdata = JSON.parse(data);
//                            //generate total chart
//                            var weeklyData = [];
//                            for (var i = 0; i < collections.length; i++) {
//                                var array = {
//                                    label: collections[i].displayname,
//                                    backgroundColor: "transparent",
//                                    borderColor: collection_color[i],
//                                    pointColor: collection_color[i],
//                                    strokeColor: collection_color[i],
//                                    borderWidth: 2,
//                                    hoverBackgroundColor: collection_highlight[i],
//                                    hoverBorderColor: "rgba(151,187,205,1)",
//                                    data: JSONdata.datasets[i]
//                                };
//                                weeklyData.push(array);
//                                array = null;
//                            }
//                            //generate weekly input chart
//                            var dat = {
//                                labels: JSONdata.labels,
//                                datasets: weeklyData,
//                            };
//                            //var ctx = new Chart(document.getElementById("chartWeeklyReport").getContext("2d")).Line(dat);
//                            var canvas = document.getElementById("chartWeeklyTranscriptionReport");
//                            var ctx = canvas.getContext("2d");
//                            var LineChart = new Chart(ctx, {
//                                type: "line",
//                                data: dat,
//                                options: {responsive: true}
//                            });
//                        },error:function(exception){alert('Exeption:'+exception);}
//                    });
//                }
//                break;
            case "MonthlyPerformance":
                document.getElementById("weeklyPerfHeader").style.display = 'block';
                $('#chartWeeklyReport').remove();
                $('#chartWeeklyTranscriptionReport').remove();
                $('#divWeeklyCanvasHolder').append('<canvas id="chartWeeklyReport" height="500" width="1000"><canvas>');

                break;
            case "IndividualPerformance":
                document.getElementById("weeklyPerfHeader").style.display = 'none';
                $('#chartWeeklyReport').remove();
                $('#chartWeeklyTranscriptionReport').remove();
                //$("#ddlMonth").change();


                //resize height of the scroller
                $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 50);
                $("#fireworksContainer").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 50);
                break;

            default:
                $('#chartWeeklyReport').remove();
                $('#chartWeeklyTranscriptionReport').remove();
                break;

        }
        $("#ddlYear").change();
    }
</script>
<script>
    var collections = <?php echo json_encode($collections); ?>;
    var collection_color = ['#0067C5','#00BC65','#FFAA2A','#26A6D0','#787878','#00000'];
    var collection_highlight = ['#FF5A5E','#7FFF55','#3e3e3e','#26A6D0','#244400','#D4D4FF'];
    function getCollectionCount()
    {
        $('#divDocumentCount').show();
        $.ajax({
            type: "POST",
            url: "./collectioncount_processing.php",
            success: function (data)
            {
                var JSONdata = JSON.parse(data);
                //generate total chart
                var pieData = [];
                var pieLabels = [];
                for (var i = 0; i < JSONdata.length; i++) {
                    pieData.push(JSONdata[i].count);
                    pieLabels.push(JSONdata[i].collection);
                }
                var canvasPie = document.getElementById("chartDocumentCount");
                var ctx1 = canvasPie.getContext("2d");
                var PieChart = new Chart(ctx1,{ type: 'pie',
                    data: {
                        datasets: [{
                            data: pieData,
                            backgroundColor: collection_color,
                            hoverBackgroundColor: collection_highlight,
                            label: "Total Maps/Documents per Collection"
                        }],
                        labels: pieLabels

                    },
                    options: { responsive:true}
                    //console.log(PieChart);
                });
                //console.log(PieChart);
                //calculate total counts of all collections = SUM of pieData array
                //$("#spanTotalCount").html(pieData.reduce(function(prev,curr){return parseInt(prev)+ parseInt(curr);}));
            }
        });
    }
    //  weekly cataloging
    $("#ddlYear").change(function()
    {
        //get the current ACTIVE tab. Check the ID of the tab to verify which tab is open.
        //then check if the correct tab is open, update the cooresponding weekly chart
        var tab = document.getElementsByClassName('tablinks active')[0];

        if(tab.id == "idMonthlyPerformance")
        {
            //reset canvas
            $('#chartWeeklyReport').remove();
            $('#divWeeklyCanvasHolder').append('<canvas id="chartWeeklyReport" height="500" width="1000"><canvas>');

            var year = $("#ddlYear").val();
            if(year != "") {
                $(".spanYear").text(year);
                $.ajax({
                    type: "POST",
                    url: "./weeklyreports_processing.php?year=" + year,
                    success: function (data) {
                        var JSONdata = JSON.parse(data);
                        //generate total chart
                        var weeklyData = [];
                        for (var i = 0; i < collections.length; i++) {
                            var array = {
                                label: collections[i].displayname,
                                backgroundColor: "transparent",
                                borderColor: collection_color[i],
                                pointColor: collection_color[i],
                                strokeColor: collection_color[i],
                                borderWidth: 2,
                                hoverBackgroundColor : collection_highlight[i],
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
                        //var ctx = new Chart(document.getElementById("chartWeeklyReport").getContext("2d")).Line(dat);
                        var canvas = document.getElementById("chartWeeklyReport");
                        var ctx = canvas.getContext("2d");
                        var LineChart = new Chart(ctx,{ type: "line",
                            data: dat,
                            options: {responsive:true}
                        });
                    }
                });
            }
        }
//                if(tab.id == "idTranscriptionPerformance")
//                {
//                    $('#chartWeeklyTranscriptionReport').remove();
//                    $('#divWeeklyCanvasHolder').append('<canvas id="chartWeeklyTranscriptionReport" height="500" width="1000"><canvas>');
//
//                    var year = $("#ddlYear").val();
//                    if(year != "")
//                    {
//                        $(".spanYear").text(year);
//                        $.ajax({
//                            type: "POST",
//                            url: "./weeklytranscriptionreports_processing.php?year=" + year,
//                            success: function (data)
//                            {
//                                var JSONdata = JSON.parse(data);
//                                //generate total chart
//                                var weeklyData = [];
//                                for (var i = 0; i < collections.length; i++)
//                                {
//                                    var array =
//                                    {
//                                        label: collections[i].displayname,
//                                        backgroundColor: "transparent",
//                                        borderColor: collection_color[i],
//                                        pointColor: collection_color[i],
//                                        strokeColor: collection_color[i],
//                                        borderWidth: 2,
//                                        hoverBackgroundColor : collection_highlight[i],
//                                        hoverBorderColor: "rgba(151,187,205,1)",
//                                        data: JSONdata.datasets[i]
//                                    };
//                                    weeklyData.push(array);
//                                    array = null;
//                                }
//                                //generate weekly input chart
//                                var dat =
//                                {
//                                    labels: JSONdata.labels,
//                                    datasets: weeklyData,
//                                };
//                                //var ctx = new Chart(document.getElementById("chartWeeklyReport").getContext("2d")).Line(dat);
//                                var canvas = document.getElementById("chartWeeklyTranscriptionReport");
//                                var ctx = canvas.getContext("2d");
//                                var LineChart = new Chart(ctx,{ type: "line",
//                                    data: dat,
//                                    options: {responsive:true}
//                                });
//                            }
//                        });
//                    }
//                }

    });
    //weekly transcription

    //Monthly Cataloging
    $("#ddlYear").change(function()
    {
        var tab = document.getElementsByClassName('tablinks active')[0];

        if (tab.id == "idMonthlyPerformance") {
            //reset canvas
            $('#chartMonthlyReport').remove();
            $('#divMonthlyPerformanceCanvasHolder').append('<canvas id="chartMonthlyReport" height="470" width="600"><canvas>');

            var year = $("#ddlYear").val();
            if (year != "") {
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
                                hoverBackgroundColor: collection_highlight[i],
                                hoverBorderColor: "rgba(151,187,205,1)",
                                data: JSONdata[i][0]
                            };
                            monthlyData.push(array);
                            array = null;
                        }
                        //generate monthly input chart
                        var dat2 = {
                            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                            datasets: monthlyData,
                        };

                        var canvas2 = document.getElementById("chartMonthlyReport");
                        var ctx2 = canvas2.getContext("2d");
                        var LineChart2 = new Chart(ctx2, {
                            type: "line",
                            data: dat2,
                            options: {responsive: true}
                        });
                        console.log(LineChart2);

                        //ctx2.strokeRect(222, 100 + 20 / 2, 30, 0);
                    }
                });
            }
        }
        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#table-header_right").outerHeight() - $("#page_title").outerHeight() - 55);
    });
    //Monthly Transcription
    $("#ddlYear").change(function()
    {
        var tab = document.getElementsByClassName('tablinks active')[0];

        if(tab.id == "idTranscriptionPerformance") {
            //reset canvas
            $('#chartTranscriptionReport').remove();
            $('#divTranscriptionCanvasHolder').append('<canvas id="chartTranscriptionReport" height="470" width="600"><canvas>');

            var year = $("#ddlYear").val();
            if (year != "") {
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
                        var dat2 = {
                            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                            datasets: monthlyData,
                        };

                        var canvas2 = document.getElementById("chartTranscriptionReport");
                        var ctx2 = canvas2.getContext("2d");
                        var LineChart2 = new Chart(ctx2, {
                            type: "line",
                            data: dat2,
                            options: {responsive: true}
                        });
                    }
                });
            }
            //resize height of the scroller
            $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#table-header_right").outerHeight() - $("#page_title").outerHeight() - 55);
        }
    });


    //DDL FOR INDIVIDUAL PERFORMANCE YEAR
    $("#ddlYear").change(function() {
        var tab = document.getElementsByClassName('tablinks active')[0];

        if (tab.id == "idIndividualPerformance")
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
        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#table-header_right").outerHeight() - $("#page_title").outerHeight() - 55);
    });
    //DDL FOR INDIVIDUAL PERFORMANCE MONTH
    $("#ddlMonth").change(function()
    {
        var year = $("#ddlYear").val();
        var month = $("#ddlMonth").val();
        var action = $("#ddlAction").val();

        var table = $('#dptable').DataTable(
            {
                "processing": false,
                "serverSide": false,
                "destroy": true,
                "lengthMenu": [20, 40 , 60, 80, 100],
                "bStateSave": false,
                "order": [[ 0, 'desc' ], [ 1, 'asc' ]],
                "ajax": "individual_processing.php?year=" + year + "&month=" + month + "&action=" + action
            } );

        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#table-header_right").outerHeight() - $("#page_title").outerHeight() - 55);
    });
    $("#ddlAction").change(function()
    {
        var year = $("#ddlYear").val();
        var month = $("#ddlMonth").val();
        var action = $("#ddlAction").val();

        var table = $('#dptable').DataTable(
            {
                "processing": false,
                "serverSide": false,
                "destroy": true,
                "lengthMenu": [20, 40 , 60, 80, 100],
                "bStateSave": false,
                "order": [[ 0, 'desc' ], [ 1, 'asc' ]],
                "ajax": "individual_processing.php?year=" + year + "&month=" + month + "&action=" + action
            } );

        //resize height of the scroller
        $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#table-header_right").outerHeight() - $("#page_title").outerHeight() - 55);
    });
    $(document).ready(function()
    {

        //Initialize the current tab before we call for it to change
        document.getElementById("idMonthlyPerformance").click();
        //$("#ddlYear").change();
        getCollectionCount();
        $.get('ajax/calculatefilesize_processing.php', function(data)
        {
            console.log(data);
            $('#storage_info').html(data);
        })


    });



</script>
<!--<?php
//$time = microtime();
//$time = explode(' ', $time);
//$time = $time[1] + $time[0];
//$finish = $time;
//$total_time = round(($finish - $start), 4);
//echo 'Page generated in '.$total_time.' seconds.';
//?>-->
</html>

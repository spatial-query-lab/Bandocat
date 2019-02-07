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


    <style>

        .BlucherMapsLabel {

            height: 35px;
            width: 35px;
            background-color: #FFD700;

        }


        .GreenMapsLabel {

            height: 35px;
            width: 35px;
            background-color: #66CDAA;

        }


        .JobFoldersLabel {

            height: 35px;
            width: 35px;
            background-color: #DDA0DD;

        }


        .BlucherFieldBookLabel {

            height: 35px;
            width: 35px;
            background-color: #6495ED;

        }


        .MapIndices {

            height: 35px;
            width: 35px;
            background-color: #787878;

        }


        .PennyFennerLabel {

            height: 35px;
            width: 35px;
            background-color: #BC8F8F;

        }



        .FieldBookIndicesLabel {

            height: 35px;
            width: 35px;
            background-color: #DCDCDC;

        }



    </style>



</head>
<body>

<script>

    Chart.defaults.global.legend.display = false;
    window.onresize = function() {location.reload();}

</script>

<div id="wrap">
    <div id="main">
        <div id="divleft"  >
            <?php include '../../Master/header.php';
            include '../../Master/sidemenu.php' ?>
        </div>
        <div id="divright">
            <h2 id="page_title">Statistics</h2>
            <div id = top>




                <div  id="table-header_right" style = "" > Select Year: <select id="ddlYear" name="ddlYear""

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



                <div >

                <ul class="tab" >
                    <li><a href="javascript:void(0)" class="tablinks" id="idMonthlyPerformance" onclick="openTab(event, 'MonthlyPerformance');"> Cataloging Performance</a></li>
                    <li><a href="javascript:void(0)" class="tablinks" id="idTranscriptionPerformance" onclick="openTab(event, 'Transcription')">Transcription Performance</a></li>
                    <?php if($session->isAdmin()) {
                        echo '<li ><a href = "javascript:void(0)" class="tablinks" id = "idIndividualPerformance"  onclick = "openTab(event,' .  "'IndividualPerformance')" . '"> Individual Performance </a ></li >';
                    };?>
                </ul>

                </div>

            </div>

        <div id="divscroller" >
                <table id = "tableLayout"    style = "table-layout: fixed; width: 100%;  border: 1px solid black;  "  >

                    <tr  style=" border: solid; height:25%; "   >


                        <td style = "height: 25%;">

                            <div id = "weeklyPerformanceRow"  style = "vertical-align: top"  >



                                <h3 id="weeklyPerfHeader" style = "font-size: 25px;" >Weekly Performance <span class="spanYear"></span></h3>
                                <h3 id="weeklyTranscriptionPerfHeader" style = "font-size: 25px;" >Weekly Transcription Performance <span class="spanYear"></span></h3>
                            <div   id="divWeeklyCanvasHolder"></div>
                            </div>





                            <div id="IndividualPerformance" class="tabcontent">
                                <h3>Individual Performance</h3>
                                <div style="display:inline; "  id="table-header_left">Select Month: <select id="ddlMonth" name="ddlMonth"">
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
                                <table id="dptable" class="display compact cell-border hover stripe" cellspacing="0"  width="100%" data-page-length='20'>
                                    <thead>
                                    <tr>
                                        <th width="50px">Count</th>
                                        <th width="50px">User</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>



                        </td>

                        <td  style = " height: 25%; vertical-align: top">


                            <div  id="Transcription" class="tabcontent">
                                <h3 style = "font-size: 25px;">Transcription Monthly Performance</h3>
                                <div id="divTranscriptionCanvasHolder"       ></div>
                            </div>

                            <div  id="MonthlyPerformance" class="tabcontent">
                                <h3 style = "font-size: 25px;">Cataloging Monthly Performance <span class="spanYear"></span></h3>
                                <div id="divMonthlyPerformanceCanvasHolder"></div>

                            </div>

                        </td>

                    </tr>
                    </table>

                     <table   style = " width: 100%; border: 1px solid black;  "  >

                    <tr >


                        <td     id = mapStatGroup style = " vertical-align:top; margin-left:60px; float:right ;  ">


                            <div id = mapColumn     ">
                                <h3 id="titleDocumentCount"  style = "margin-bottom: 40px; margin-top:0"> Total Maps per Collection  </h3>
                                <canvas id="chartDocumentCount" style= "margin-left: -60px;"
                                ></canvas>
                                <div class="pulse" id="displayTotal" style = "margin-top: 20px;" >Total: 100000 documents   </div>

                            </div>
                        </td>

                        <td style = "; vertical-align: top; align-content: center;">
                            <h3 style = "margin-top:0; font-size: 25px; ">Collections Legend</h3>

                            <table style = "margin-left: 15%; vertical-align: top;">
                                <tr  >
                                    <td ><div class = "BlucherMapsLabel"</td> <td>Blucker Maps</td>
                                    <td><div class = "GreenMapsLabel"</td> <td>Green Maps</td>
                                </tr>
                                <tr>
                                    <td><div class = "JobFoldersLabel"</td> <td>Job Folders</td>
                                    <td><div class = "BlucherFieldBookLabel"</td> <td>Blucher Field Book</td>
                                </tr>
                                <tr>
                                    <td><div class = "MapIndices"</td><td>Map Indices</td>
                                    <td><div class = "PennyFennerLabel"</td><td>PennyFenner</td>
                                </tr>
                                <tr>
                                    <td><div class = "FieldBookIndicesLabel"</td><td>Field Book Indices</td>
                                </tr>

                            </table>

                        </td>



						<figure id="loadinggif" style='background: white;border-radius: 10px;box-shadow: 0 0 20px; blur-radius:10px; padding: 10px; position:absolute; top:40%;left:42%; display: none;'><img src='../../Images/loading2.gif'> <figcaption><b> Loading. Please wait...</b></figcaption></figure>
                        <td style = "vertical-align:top;  " >

                            <div id="storage_capacity" style=" margin-top: 0px; border-size: 1px; ">
                                <h3 id="storage_header" style = "text-align: center; font-size: 25px; margin-top: 0; margin-bottom: 20px;" >Storage Capacity</h3>




                                <table style = " border: solid; border-width: thin;    display:flex; justify-content: center;   ">


                                 <tr>  
									<td id="storage_totals"  style= " border-color:LightGray; padding:0px; ">

                                    </td>
                                 </tr>

                                 <tr></tr>
                                 <tr></tr>


                                </table>
                                <div style = "text-align: center;  padding-top: 10px; padding-bottom:20px;  ">
                                <button  id = "storage_button" type ="button" style = "text-align: center; ">Update
                                </button>
                                </div>
                            </div>

                        </td>

                    </tr>



                </table>
            </div>
        </div>


    </div>
</div>




<?php include '../../Master/footer.php';



?>
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
                document.getElementById("weeklyTranscriptionPerfHeader").style.display = 'block';
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
                document.getElementById("weeklyTranscriptionPerfHeader").style.display = 'none';
                document.getElementById("weeklyPerfHeader").style.display = 'block';
                $('#chartWeeklyReport').remove();
                $('#chartWeeklyTranscriptionReport').remove();
                $('#divWeeklyCanvasHolder').append('<canvas id="chartWeeklyReport" height=25% width=50%><canvas>');


                break;
            case "IndividualPerformance":
                document.getElementById("weeklyTranscriptionPerfHeader").style.display = 'none';
                document.getElementById("weeklyPerfHeader").style.display = 'none';
                $('#chartWeeklyReport').remove()
                $('#chartWeeklyTranscriptionReport').remove();

                //$("#ddlMonth").change();


                //resize height of the scroller
                $("#divscroller").height($(window).outerHeight() - $(footer).outerHeight() - $("#page_title").outerHeight() - 50);
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
    var collection_color = ['#FFD700','#66CDAA','#DDA0DD','#6495ED','#787878',' #BC8F8F '];
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




                    },
                    options: { responsive:true}
                    //console.log(PieChart);
                });
                //console.log(PieChart);
                //calculate total counts of all collections = SUM of pieData array
                $("#spanTotalCount").html(pieData.reduce(function(prev,curr){return parseInt(prev)+ parseInt(curr);}));
            }
        });
    }






    //  weekly cataloging
    $("#ddlYear").change(function()
    {
        //get the current ACTIVE tab. Check the ID of the tab to verify which tab is open.
        //then check if the correct tab is open, update the cooresponding weekly chart
        var tab = document.getElementsByClassName('tablinks active')[0];

        if(tab.id == "idMonthlyPerformance"  )
        {
            //reset canvas
            $('#chartWeeklyReport').remove();
            $('#divWeeklyCanvasHolder').append('<canvas id="chartWeeklyReport"  height=25% width=50%><canvas>');

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
                            var array =
                            {
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
                            options: {

                                legend: {
                                    display: false
                                },
                                tooltips: {
                                    enabled: false
                                }


                            }
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
//                                var canvas = document.getElementById("chartWeeklyTranscriptionReport");
//                                //var ctx = new Chart(document.getElementById("chartWeeklyReport").getContext("2d")).Line(dat);
//                                var ctx = canvas.getContext("2d");
//                                var LineChart = new Chart(ctx,{ type: "line",
//                                    data: dat,
//                                    options: {responsive:true  }
//                                });
//                            }
//                        });
//                    }
//                }

    });


    //weekly transcription

    $("#ddlYear").change(function()
    {
        //get the current ACTIVE tab. Check the ID of the tab to verify which tab is open.
        //then check if the correct tab is open, update the cooresponding weekly chart
        var tab = document.getElementsByClassName('tablinks active')[0];

        if( tab.id == "idTranscriptionPerformance"  )
        {
            //reset canvas
            $('#chartWeeklyReport').remove();
            $('#divWeeklyCanvasHolder').append('<canvas id="chartWeeklyReport"  height=25% width=50%><canvas>');

            var year = $("#ddlYear").val();
            if(year != "") {
                $(".spanYear").text(year);
                $.ajax({
                    type: "POST",
                    url: "./weeklytranscriptionreports_processing.php?year=" + year,
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
                            options: {

                                legend: {
                                    display: false
                                },
                                tooltips: {
                                    enabled: false
                                }


                            }
                        });
                    }
                });
            }
        }



    });




    //Monthly Cataloging
    $("#ddlYear").change(function()
    {
        var tab = document.getElementsByClassName('tablinks active')[0];

        if (tab.id == "idMonthlyPerformance") {

            //reset canvas


            $('#chartMonthlyReport').remove();

                $('#divMonthlyPerformanceCanvasHolder').append('<canvas id="chartMonthlyReport" height = 25% width= 50%><canvas>');


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
                                borderWidth: 4,
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
                            options: {
                                legend: {
                                    display: false
                                },
                                tooltips: {
                                    enabled: false
                                }

                            }
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
            $('#divTranscriptionCanvasHolder').append('<canvas id="chartTranscriptionReport" height="25%" width="50%"><canvas>');



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
                            options: {legend: {display: false}, tooltips: {enabled: false }}
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
            $('#chartTranscriptionReport').remove()
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


    var button_storage = document.getElementById("storage_button");


    $(document).ready(function ()
    {

        //Initialize the current tab before we call for it to change
        document.getElementById("idMonthlyPerformance").click();
        //$("#ddlYear").change();

        getCollectionCount();




        $.get('ajax/storagetotalsdisplay.php', function (data) {
            console.log(data);
            $('#storage_totals').html(data);
        })



        button_storage.onclick = function()
        {
			
			document.getElementById("loadinggif").style.display = 'block';
			
            $.get('ajax/calculatefilesize_processing.php', function (data)
			{

                $('#storage_totals').html(data);
                console.log(data);
				document.getElementById("loadinggif").style.display = 'none';
                


            })






        };


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
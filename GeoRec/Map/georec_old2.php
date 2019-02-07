<?php
include '../../Library/SessionManager.php';
$session = new SessionManager();
if(!isset($_GET['col']) || !isset($_GET['docID']) || !isset($_GET['type'])) {
    //if either one of three above param is not set, alert the user, then close the windows
    echo "<script>alert('Forbidden access!')</script>";
    echo "<script>window.close()</script>";
}

//needs control render class to render dropdownlist of divGeoRecStatus
require_once('../../Library/ControlsRender.php');
$Render = new ControlsRender();
require_once('../../Library/DBHelper.php');
require_once('../../Library/MapDBHelper.php');
$DB = new MapDBHelper();

//get collection information from bandocatdb table (to get DisplayName mostly)
$collection = $DB->SP_GET_COLLECTION_CONFIG($_GET['col']);

$DB->SWITCH_DB($_GET['col']); //switch to this collection's database
$isBack = $_GET['type'] == "back" ? true : false; //identify if this map is a front or a back scan

//get georec information for this map from georectification table
$georec_entries = $DB->GEOREC_ENTRIES_SELECT($_GET['docID'],$isBack);
//get georec status for this map from document table
$georec_status = $DB->DOCUMENT_GEORECSTATUS_SELECT($_GET['docID'],$isBack);
//Old Working Server version with added esri code.
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $collection['DisplayName']; ?> Georectification</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
    <link href='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.css' rel='stylesheet' />
    <link href='css/styles.css' rel='stylesheet' />
    <link rel="stylesheet" href= "css/l.geosearch.css"/>
    <link rel="stylesheet" href= "../../Master/master.css"/>
    <link rel="stylesheet" href="../../ExtLibrary/L.Control.MousePosition/L.Control.MousePosition.css">
    <link rel="stylesheet" href="../../ExtLibrary/PolylineMeasure/PolylineMeasure.css" />
    <script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>
    <script src='../../ExtLibrary/rastercoords.js'></script>
    <script src="../../ExtLibrary/Leaflet.MakiMarkers.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
    <script src="javascript/l.control.geosearch.js"></script>
    <script src="javascript/l.geosearch.provider.esri.js"></script>
    <script type="text/javascript" src="javascript/bandocatRectification.js"></script>
    <script src="../../ExtLibrary/PolylineMeasure/PolylineMeasure.js"></script>
    <script src="../../ExtLibrary/L.GeometryUtil/L.GeometryUtil.js"></script>
    <script type="text/javascript" src="../../ExtLibrary/L.Control.MousePosition/L.Control.MousePosition.js"></script>
</head>
<style>
    /*    Styles for popup Update Status */
    #divUpdateGeoRecStatus{
        position:absolute;
        z-index: 1000;
        width:100%;
        height:90%;
        background: #fff;
        opacity:0.9;
        padding:100px;
    }
    #frmUpdateStatus
    {
        font-size:1.5em;
        padding-top:180px;
        line-height: 3em;
        width:500px;
        margin:0 auto;
    }
</style>
<body>

<!--Title Section/Loading Modal Divs-->

<div id = 'title'><?php echo $collection['DisplayName']; ?> Georeferencer</div>
<div id = 'logo'> <img src = "../../Images/Logos/bando-logo-small.png"></div>
<h1 id = 'fileName'></h1>

<div id="fade_1"></div>
<div id="modal_1">
    <img id="loader_1" src="../../Images/loading.gif" />
    <div>Rectifying...</div>
</div>
<div id="fade_2"></div>
<div id="modal_2">
    <img id="loader_2" src="../../Images/loading.gif" />
    <div>Cleaning Temporary Workspace...</div>
</div>

<!--Buttons and Button Functionality-->
<!--<form id = "Tutorial">-->
<!--    <button href= "#" onclick="Tutorial(); return false;">Tutorial</button>-->
<!--</form>-->

<div id = 'buttons'>
    <button onclick = "deletePrevious()" id = 'deletePrevious' name="deletePrevious" class="bluebtn">Delete Previous</button>
    <button onclick="document.getElementById('divUpdateGeoRecStatus').style.visibility = 'visible';" class="bluebtn" id = setStatus> Set Status </button>
    <button onclick = "rectify()" id = "rectify" class="bluebtn"> Rectify / Update </button>
    <button onclick = "cancel()" id = "cancel" class="bluebtn"> Cancel </button>
</div>

<!-- Map Display and Functionality -->

<div id="map"></div>
<script id = "test" language="javascript" type="text/javascript">

    var map = L.mapbox.map("map");
    L.control.mousePosition().addTo(map);
    //pk.eyJ1Ijoic2FsbHJlZCIsImEiOiJjajN1N3pjbzkwMDUwMnFsaTZhNGxvcnpsIn0.YcXPcOqQeZ556qHY4B5o8A
    //old pk.eyJ1Ijoic3BhdGlhbHF1ZXJ5bGFiIiwiYSI6ImNpeW43eHZ2YTAwMTgzMnBjNGF4bWVuaHIifQ.H-IzkkctQwbBRjhS9VLddA
    //Base layers with leaflet layer control and access token
    var street = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v10/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1Ijoic2FsbHJlZCIsImEiOiJjajN1N3pjbzkwMDUwMnFsaTZhNGxvcnpsIn0.YcXPcOqQeZ556qHY4B5o8A', {
        maxZoom: 20,
        maxNativeZoom: 18,
        attribution: '&copy; <a href="https://www.mapbox.com/">Mapbox</a> contributors'
    }).addTo(map);

    var satellite = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/satellite-streets-v10/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1Ijoic2FsbHJlZCIsImEiOiJjajN1N3pjbzkwMDUwMnFsaTZhNGxvcnpsIn0.YcXPcOqQeZ556qHY4B5o8A', {
        maxZoom: 20,
        maxNativeZoom: 18,
        attribution: '&copy; <a href="https://www.mapbox.com/">Mapbox</a> contributors'
    });
    mapLink = '<a href="http://www.esri.com/">Esri</a>';
    wholink = 'i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community';
    var esriSat =  L.tileLayer(
        'http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; '+mapLink+', '+wholink,
            maxZoom: 20,
        });
    var Boundaries =  L.tileLayer(
        'http://server.arcgisonline.com/arcgis/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; '+mapLink+', '+wholink,
            maxZoom: 20,
        });
    var esriTransportation = L.tileLayer(
        'http://server.arcgisonline.com/arcgis/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; '+mapLink+', '+wholink,
            maxZoom: 20,
        });
    var items = new L.FeatureGroup([esriSat,esriTransportation]);
    var baseMaps = {
        "Street": street,
        "Satellite": satellite,
        "Esri Sat": esriSat
    };

    var overlayMaps = {
        "Esri Transportation": esriTransportation
    }
    L.control.layers(baseMaps, overlayMaps).addTo(map);


    //adds geocoder to map
    new L.Control.GeoSearch({
        provider: new L.GeoSearch.Provider.Esri(),
        showMarker: false
    }).addTo(map);

    L.control.polylineMeasure({
        position: 'topright',                    // Position to show the control. Possible values are: 'topright', 'topleft', 'bottomright', 'bottomleft'
        imperial: true,                        // Show imperial or metric distances
        title: '',                              // Title for the control
        innerHtml: '&#8614;',                   // HTML to place inside the control
        classesToApply: [],                     // Classes to apply to the control
        backgroundColor: '#8f8',                // Background color for control when selected
        cursor: 'crosshair',                    // Cursor type to show when creating measurements
        clearMeasurementsOnStop: true,          // Clear all the measurements when the control is unselected
        showMeasurementsClearControl: false,    // Show a control to clear all the measurements
        clearControlTitle: 'Clear',             // Title text to show on the clear measurements control button
        clearControlInnerHtml: '&times',        // Clear control inner html
        clearControlClasses: [],                // Collection of classes to add to clear control button
        tempLine: {                             // Styling settings for the temporary dashed line
            color: '#00f',                      // Dashed line color
            weight: 2                           // Dashed line weight
        },
        line: {                                 // Styling for the solid line
            color: '#006',                      // Solid line color
            weight: 2                           // Solid line weight
        },
        startingPoint: {                        // Style settings for circle marker indicating the starting point of the polyline
            color: '#000',                      // Color of the border of the circle
            weight: 1,                          // Weight of the circle
            fillColor: '#0f0',                  // Fill color of the circle
            fillOpacity: 1,                     // Fill opacity of the circle
            radius: 8                           // Radius of the circle
        },
        lastPoint: {                            // Style settings for circle marker indicating the last point of the polyline
            color: '#000',                      // Color of the border of the circle
            weight: 1,                          // Weight of the circle
            fillColor: '#fa8d00',               // Fill color of the circle
            fillOpacity: 1,                     // Fill opacity of the circle
            radius: 8                           // Radius of the circle
        },
        endPoint: {                             // Style settings for circle marker indicating the last point of the polyline
            color: '#000',                      // Color of the border of the circle
            weight: 1,                          // Weight of the circle
            fillColor: '#f00',                  // Fill color of the circle
            fillOpacity: 1,                     // Fill opacity of the circle
            radius: 8                           // Radius of the circle
        }
    }).addTo(map);

    var markerColors = new Array('#e74c3c','#2980b9', '#2ecc71','#ffee58','#9b59b6',
        '#FE9A2E', '#f1948a', '#80deea', '#abebc6',  '#f9e79f','#b388ff', '#ffcc80'); // array of possible colors of markers
    var colorCount = 0;						//counter that increments when ever a new color from markerColors needs to be used
    var count = 0;							//might be identical to colorCount and currently be unnecessary
    var mapSelected = true;				//boolean keeping track of if a point on the map was placed most recently
    var marker;								//variable that contains the marker to be placed
    markerArray = new Array();		//array that holds all the markers that are created on the map
    var gcpList = [];							// array of JSON that contains latlng and index of gcp from both map and raster
    var UTM = "+proj=utm +zone=14 +ellps=WGS84 +datum=WGS84 +units=m +no_defs"

    map.setView([27.73725068372226, -97.43062019348145], 12);
    var measureControl = L.control.polylineMeasure();

    function gcpMarker(markerLayer, markerType) {
        Maki_Icon = icon = L.MakiMarkers.icon({icon: colorCount+1, color: markerColors[colorCount], size: "m"});
        if(markerType == 'measureMarker'){
            if(!mapSelected) {
                //creates new object of gcpList
                addGCP(count, markerLayer._latlng.lat, markerLayer._latlng.lng, rasterCoords[rasterCount - 1].rlat, rasterCoords[rasterCount - 1].rlong, rasterCoords[rasterCount - 1].x, rasterCoords[rasterCount - 1].y);

                //creates marker and popup
                marker = L.marker(markerLayer._latlng, {icon: icon});
                markerArray.push(marker);
                map.addLayer(marker);

                var markerIndex = marker.on('click', function clickIndex(event) {
                    if (measureControl._measureModeGet() == true) {

                    }
                    else{
                        marker.bindPopup("Edit").openPopup().closePopup;
                        for (i = 0; i < gcpList.length; i++) {
                            if (gcpList[i].lat == event.latlng.lat) {
                                markerIndex = i;
                            }
                        }
                        event.target.dragging.enable();
                        return markerIndex;
                    }
                });

                marker.on('dragend', function (event) {
                    var marker = event.target;
                    var latitude = marker._latlng.lat;
                    gcpList[markerIndex].lat = latitude;
                    var longitude = marker._latlng.lng;
                    gcpList[markerIndex].lng = longitude;
                    $('#markerLat-' + markerIndex).text(gcpList[markerIndex].lat);
                    $('#markerLong-' + markerIndex).text(gcpList[markerIndex].lng);
                });


                //alert(colorCount);
                colorCount++;
                if (colorCount > 11)
                    alert("Maximum amount of points reached");

                count++;
                rasterSelected = false;
                mapSelected = true;

                //creates table
                var table = document.getElementById("table");
                var row = table.insertRow(count + 1);
                var cell1 = row.insertCell(0);
                $(cell1).attr('id', 'marker-'+gcpList[count-1].id);
                var cell2 = row.insertCell(1);
                $(cell2).attr('id', 'markerLat-'+gcpList[count-1].id);
                var cell3 = row.insertCell(2);
                $(cell3).attr('id', 'markerLong-'+gcpList[count-1].id);
                var cell4 = row.insertCell(3);
                $(cell4).attr('id', 'rasterLat-'+gcpList[count-1].id);
                var cell5 = row.insertCell(4);
                $(cell5).attr('id', 'rasterLong-'+gcpList[count-1].id);
                var cell6 = row.insertCell(5);
                $(cell6).attr('id', 'rasterX-'+gcpList[count-1].id);
                var cell7 = row.insertCell(6);
                $(cell7).attr('id', 'rasterY-'+gcpList[count-1].id);
                cell1.innerHTML = "<button id = 'zoomToMarker' onclick = 'zoomToMarker(" + count + ")' style = 'background-color:" + markerColors[colorCount - 1] + "'>" + count + "</button>";
                cell2.innerHTML = gcpList[count - 1].lat;
                cell3.innerHTML = gcpList[count - 1].lng;
                cell4.innerHTML = gcpList[count - 1].rlat;
                cell5.innerHTML = gcpList[count - 1].rlong;
                cell6.innerHTML = gcpList[count - 1].x;
                cell7.innerHTML = gcpList[count - 1].y;
                //hide column raster lat and raster long
                cell4.style.display = "none";
                cell5.style.display = "none";
            }else {
                alert("Select point from raster");
            }
            return;
        }

        if(markerType == 'mapMarker'){
            if(!mapSelected) {
                //creates new object of gcpList
                addGCP(count, markerLayer.latlng.lat, markerLayer.latlng.lng, rasterCoords[rasterCount - 1].rlat, rasterCoords[rasterCount - 1].rlong, rasterCoords[rasterCount - 1].x, rasterCoords[rasterCount - 1].y);

                //creates marker and popup
                marker = L.marker(markerLayer.latlng, {icon: icon});
                marker.bindPopup("Edit").openPopup().closePopup;
                markerArray.push(marker);
                map.addLayer(marker);

                var markerIndex = marker.on('click', function clickIndex(event) {
                    for (i = 0; i < gcpList.length; i++) {
                        if (gcpList[i].lat == event.latlng.lat) {
                            markerIndex = i;
                        }
                    }
                    event.target.dragging.enable();
                    return markerIndex;
                });

                marker.on('dragend', function (event) {
                    var marker = event.target;
                    var latitude = marker._latlng.lat;
                    gcpList[markerIndex].lat = latitude;
                    var longitude = marker._latlng.lng;
                    gcpList[markerIndex].lng = longitude;
                    $('#markerLat-' + markerIndex).text(gcpList[markerIndex].lat);
                    $('#markerLong-' + markerIndex).text(gcpList[markerIndex].lng);
                });


                //alert(colorCount);
                colorCount++;
                if (colorCount > 11)
                    alert("Maximum amount of points reached");

                count++;
                rasterSelected = false;
                mapSelected = true;

                //creates table
                var table = document.getElementById("table");
                var row = table.insertRow(count + 1);
                var cell1 = row.insertCell(0);
                $(cell1).attr('id', 'marker-'+gcpList[count-1].id);
                var cell2 = row.insertCell(1);
                $(cell2).attr('id', 'markerLat-'+gcpList[count-1].id);
                var cell3 = row.insertCell(2);
                $(cell3).attr('id', 'markerLong-'+gcpList[count-1].id);
                var cell4 = row.insertCell(3);
                $(cell4).attr('id', 'rasterLat-'+gcpList[count-1].id);
                var cell5 = row.insertCell(4);
                $(cell5).attr('id', 'rasterLong-'+gcpList[count-1].id);
                var cell6 = row.insertCell(5);
                $(cell6).attr('id', 'rasterX-'+gcpList[count-1].id);
                var cell7 = row.insertCell(6);
                $(cell7).attr('id', 'rasterY-'+gcpList[count-1].id);
                cell1.innerHTML = "<button id = 'zoomToMarker' onclick = 'zoomToMarker(" + count + ")' style = 'background-color:" + markerColors[colorCount - 1] + "'>" + count + "</button>";
                cell2.innerHTML = gcpList[count - 1].lat;
                cell3.innerHTML = gcpList[count - 1].lng;
                cell4.innerHTML = gcpList[count - 1].rlat;
                cell5.innerHTML = gcpList[count - 1].rlong;
                cell6.innerHTML = gcpList[count - 1].x;
                cell7.innerHTML = gcpList[count - 1].y;
                //hide column raster lat and raster long
                cell4.style.display = "none";
                cell5.style.display = "none";
            }else {
                alert("Select point from raster");
            }
        }

    }

    //executes on click of map
    map.addEventListener("click", function(event)
    {
        if(measureControl._measureModeGet() == true) {

        }
        else{
            gcpMarker(event, 'mapMarker');
        }
    })

</script>
<!-- Raster Display and Functionality -->

<div id = "raster">
    <script>
        //retrieve relevant raster information from index page
        file_data = window.localStorage.getItem("imageInfo");									//retrieves name of file from local storage set by index page
        rasterJSON = JSON.parse(file_data);													//parses and assigns to variable stringifiied JSON also set by index page
        document.getElementById("fileName").innerHTML = "File name: <a target='_blank' href='../../Templates/Map/review.php?col=" + rasterJSON.collection + "&doc=" + rasterJSON.docID + "'>" + rasterJSON.fileName + "</a>";	// writes name of file to top of raster viewer

        var rasterCount = 0;							//counter that increments every time a point is placed on raster viewer
        var rasterSelected = false;					//boolean that keeps track of if the raster viewer was most recently selected
        rasterMarkerArray = new Array();		// array that holds all of the points placed on the raster viewer
        rasterCoords = new Array();			// array that holds all the coordinates of points on raster viewer

        var minZoom = 0,
            maxZoom = 5,

            img = [
                rasterJSON.width,  // original width of raster
                rasterJSON.height  // original height of raster
            ];

        // create the raster
        var raster = L.map("raster",{
            minZoom: minZoom,
            maxZoom: maxZoom
        });

        // assign raster and raster dimensions
        rc = new L.RasterCoords(raster, img);

        // set the bounds on raster
        rc.setMaxBounds();

        //sets starting view of raster viewer to center of the document
        raster.setView(rc.unproject([img[0]/2, img[1]/2]), 2);

        var tiles = './Temp/Tiles/' + rasterJSON.tempSubDirectory + '/{z}/{x}/{y}.png';	// the tile layer containing the raster generated with gdal2tiles --leaflet ...

        //add tiles to raster viewer
        L.tileLayer(tiles, {
            noWrap: true,
            attribution: '<a href="http://spatialquerylab.com/">Spatial Query Lab {SQL}</a>'
        }).addTo(raster);

        L.control.polylineMeasure({
            position: 'topleft',                    // Position to show the control. Possible values are: 'topright', 'topleft', 'bottomright', 'bottomleft'
            imperial: false,                        // Show imperial or metric distances
            title: '',                              // Title for the control
            innerHtml: '&#8614;',                   // HTML to place inside the control
            classesToApply: [],                     // Classes to apply to the control
            backgroundColor: '#8f8',                // Background color for control when selected
            cursor: 'crosshair',                    // Cursor type to show when creating measurements
            clearMeasurementsOnStop: true,          // Clear all the measurements when the control is unselected
            showMeasurementsClearControl: false,    // Show a control to clear all the measurements
            clearControlTitle: 'Clear',             // Title text to show on the clear measurements control button
            clearControlInnerHtml: '&times',        // Clear control inner html
            clearControlClasses: [],                // Collection of classes to add to clear control button
            tempLine: {                             // Styling settings for the temporary dashed line
                color: '#00f',                      // Dashed line color
                weight: 2                           // Dashed line weight
            },
            line: {                                 // Styling for the solid line
                color: '#006',                      // Solid line color
                weight: 2                           // Solid line weight
            },
            startingPoint: {                        // Style settings for circle marker indicating the starting point of the polyline
                color: '#000',                      // Color of the border of the circle
                weight: 1,                          // Weight of the circle
                fillColor: '#0f0',                  // Fill color of the circle
                fillOpacity: 1,                     // Fill opacity of the circle
                radius: 3                           // Radius of the circle
            },
            lastPoint: {                            // Style settings for circle marker indicating the last point of the polyline
                color: '#000',                      // Color of the border of the circle
                weight: 1,                          // Weight of the circle
                fillColor: '#fa8d00',               // Fill color of the circle
                fillOpacity: 1,                     // Fill opacity of the circle
                radius: 3                           // Radius of the circle
            },
            endPoint: {                             // Style settings for circle marker indicating the last point of the polyline
                color: '#000',                      // Color of the border of the circle
                weight: 1,                          // Weight of the circle
                fillColor: '#f00',                  // Fill color of the circle
                fillOpacity: 1,                     // Fill opacity of the circle
                radius: 3                           // Radius of the circle
            }
        }).addTo(raster);

        //Raster container control that displays coordinates location on mouse hover
        L.control.mousePosition().addTo(raster);

        //executes on click event on raster viewer
        raster.on("click", function(event)
        {
            Maki_Icon = icon = L.MakiMarkers.icon({icon: colorCount+1, color: markerColors[colorCount], size: "m"});

            if(!rasterSelected) {
                //pushes data from click into appropriate arrays
                var coords = rc.project(event.latlng);
                var unproject_coords = rc.unproject(coords);
                rasterCoords.push({rlat:unproject_coords.lat,rlong:unproject_coords.lng,x:coords.x,y:coords.y});

                //rasterCoords.push(coords);
                //creates marker and popup
                var rasterMarker = L.marker(rc.unproject(coords), {icon: icon});
                rasterMarkerArray.push(rasterMarker);
                raster.addLayer(rasterMarker);
                rasterMarker.bindPopup("Edit").openPopup;

                var rastermarkerIndex = rasterMarker.on('click', function clickIndex(event) {
                    for (i = 0; i < gcpList.length; i++) {
                        if (gcpList[i].rlat == event.latlng.lat) {
                            rastermarkerIndex = i;
                        }
                    }
                    event.target.dragging.enable();
                    return rastermarkerIndex;
                });


                rasterMarker.on('dragend', function (event) {
                    var marker = event.target;
                    //var latitude = marker._latlng.lat;
                    gcpList[rastermarkerIndex].rlat = marker._latlng.lat;
                    //var longitude = marker._latlng.lng;
                    gcpList[rastermarkerIndex].rlong = marker._latlng.lng;
                    rCoords =rc.project(event.target._latlng);

                    //Georec Bug
                    gcpList[rastermarkerIndex].x = rCoords.x;
                    gcpList[rastermarkerIndex].y = rCoords.y;
                    $('#rasterX-' + rastermarkerIndex).text(gcpList[rastermarkerIndex].x);
                    $('#rasterY-' + rastermarkerIndex).text(gcpList[rastermarkerIndex].y);
                    $('#rasterLat-' + rastermarkerIndex).text(gcpList[rastermarkerIndex].rlat);
                    $('#rasterLong' + rastermarkerIndex).text(gcpList[rastermarkerIndex].rlong);
                });

                //increment counters and adjust booleans
                rasterCount++;
                rasterSelected = true;
                mapSelected = false;
            }

            else
                alert("Select point on map");

        });


    </script>
</div>

<!-- GCP Table -->

<div id = "Table_Container">
    <table class = "tg" id ="table" style = "width: 100%; height:23%; bottom: 0;">
        <tbody style = "align-items: center; background-color: #ccffff">
        <tr style = "background-color: #ccffcc">
            <th>Point Number</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th style="display: none">Raster Latitude</th>
            <th style="display: none">Raster Longitude</th>
            <th>Raster X</th>
            <th>Raster Y</th>
        <tr/>
    </table>
</div>
<div id="divUpdateGeoRecStatus" style="visibility: hidden">
    <form id="frmUpdateStatus" name="frmUpdateStatus">
        <table>
            <tr>
                <td width="300px">Update GeoRec Status</td>
                <td><select style="height: 2.5em" id="ddlGeoStatus" name="ddlGeoStatus" required>
                        <?php
                        $arrGeoStatus = array(array(0,"Not Rectified"),array(2,"Not Rectifiable"),array(4,"Research Required"));
                        $Render->GET_DDL3($arrGeoStatus,$georec_status);
                        ?>
                        ?>
                    </select>
                    <input type="hidden" name="txtDocID" id="txtDocID" value="<?php echo $_GET['docID']; ?>">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center"><input type="Submit" class="bluebtn" name="btnUpdate" id="btnUpdate" value="Update">
                    &nbsp;&nbsp;
                    <button type="button" onclick="document.getElementById('divUpdateGeoRecStatus').style.visibility = 'hidden';" class="bluebtn">Close</button></td>
            </tr>
        </table>
    </form>
</div>
</body>
<script>
    //this will be fired when the tab is reloaded/redirected or close
    window.onbeforeunload = function (e) {

    };


    $(document).ready(function(){

        //check if the user is a reader
        var userRole = '<?php echo $session -> getRole(); ?>';

        if(userRole == "Reader")
        {

            document.getElementById('deletePrevious').disabled = true;
            document.getElementById('setStatus').disabled = true;
            document.getElementById('rectify').disabled = true;




        }
        //load the points into the map and raster if there is any
        var entries = <?php echo json_encode($georec_entries); ?>;

        for(var i = 0; i < entries.length; i++)
        {
            var maplatlng = L.latLng(entries[i][1],entries[i][2]);

            var rasterXY = L.Point(entries[i][5],entries[i][6]);
            var rasterLatLng = L.latLng(entries[i][3],entries[i][4]);
            //var rasterXY = ;


            raster.fireEvent('click',{latlng:rasterLatLng,containerPoint:rasterXY});
            map.fireEvent('click',{latlng:maplatlng});
        }

    });
    //action when submitting popup Update GeoRec Status
    $("#frmUpdateStatus").submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "php/updateGeoStatus_processing.php?col=" + rasterJSON.collection + "&type=" + '<?php echo $_GET['type']; ?>',
            data: $("#frmUpdateStatus").serializeArray(),
            success: function (data) {
                if(data == 1){ //success
                    alert("Status updated successfully!");
                    cancel();
                }

            }
        });
    });
</script>
</html>

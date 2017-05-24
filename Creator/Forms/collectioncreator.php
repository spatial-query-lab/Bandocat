<?php
//Super Admin only???
require_once '../../Library/SessionManager.php';
$session = new SessionManager();
require_once '../../Library/DBHelper.php';
require_once '../../Library/ControlsRender.php';

//temporary CreatorHelper class
require_once '../../Library/CreatorHelper.php';
$DB = new CreatorHelper();
$Render = new ControlsRender();
$arrayTemplates = $DB->GET_TEMPLATES();

?>
<style>
    /* Tooltip container */
    .tooltip {
        position: relative;
        display: inline-block;
        text-decoration: none;
        background-color:lightblue;
    }

    /* Tooltip text */
    .tooltip .tooltiptext {
        visibility: hidden;
        width: 400px;
        background-color: #1b77cb;
        color: #fff;
        padding: 5px;
        border-radius: 6px;

        /* Position the tooltip text - see examples below! */
        position: absolute;
        z-index: 1;
    }

    /* Show the tooltip text when you mouse over the tooltip container */
    .tooltip:hover .tooltiptext {
        visibility: visible;
    }

    legend{
        color:gray;
        font-weight:bold;
    }
    fieldset{
        border:1px solid lightgrey;
        margin:10px;
    }
    .divHolder{
        min-height:50px;
        min-width:50px;
        padding:10px;
    }

    #tbInput td:first-child{width:150px}

    #tbConfirmation{
        font-weight: bold;
    }
    #tbConfirmation td:first-child{
        width:200px;
    }
    #tbConfirmation td:nth-child(2){
        color: #00BC65;

    }
</style>
<form id="frm" name="frm" enctype="multipart/form-data">
    <fieldset>
    <legend>Template Selection</legend>
    <label><span class="required">*</span>Select A Template:</label>
    <select id="ddlTemplate" name="ddlTemplate" required>
    <?php $Render->GET_DDL3($arrayTemplates,null);  ?>
    </select>
    </fieldset>
    <fieldset>
        <legend>Template Description</legend>
    <div id="divDescription" class="divHolder"></div>
    </fieldset>
    <fieldset id="fsInput">
        <legend>New Collection Information</legend>
        <div class="divHolder">
            <table id="tbInput">
                <tr>
                    <td class="tooltip"><label><span class="required">*</span>Display Name:<span class="tooltiptext">Enter the name you want to display on this collection's pages. Example: Blucher Maps, Green Maps.</span></label></td>
                    <td><input type="text" name="txtDisplayName" id="txtDisplayName" required>
                    <input type="checkbox" name="cbAutofill" id="cbAutofill" checked> Autofill other fields</td>
                </tr>
                <tr>
                    <td class="tooltip"><label><span class="required">*</span>Parameter Name:<span class="tooltiptext">This will be used as parameter to pass through URL between pages. Name should lower case and have no spaces. Example: bluchermaps,greenmaps,...</span></label></td>
                    <td><input type="text" name="txtParameterName" id="txtParameterName" required></td>
                </tr>
                <tr>
                    <td class="tooltip"><label><span class="required">*</span>Database Name:<span class="tooltiptext">First textbox is database prefix (default is 'bandocat_'). Second textbox is Parameter name + 'inventory'. Example: bandocat_newcollectioninventory.</span></label></td>
                    <td><input type="text" name="txtDatabasePrefix" id="txtDatabasePrefix" value="bandocat_" required>&nbsp;<input type="text" name="txtDatabaseName" id="txtDatabaseName" required></td>
                </tr>
                <tr>
                    <td class="tooltip"><label><span class="required">*</span>Storage Directory:<span class="tooltiptext">Specify server's directory path where the image files of this collection will be uploaded to (use forward slash '/' with a trailing slash).</span></label></td>
                    <td><input type="text" name="txtStorageDir" id="txtStorageDir" required></td>
                </tr>
<!--                <tr>-->
<!--                    <td><label>Public Directory:</label></td>-->
<!--                    <td><input type="text" name="txtPublicDir" id="txtPublicDir" ></td>-->
<!--                </tr>-->
                <tr>
                    <td class="tooltip"><label><span class="required">*</span>Thumbnail Directory:<span class="tooltiptext">Specify thumbnail's directory path where the thumbnails of this collection will be stored. Default: 'Thumbnails/parametername/' (use forward slash '/' with a trailing slash).</span></label></td>
                    <td><input type="text" name="txtThumbnailDir" id="txtThumbnailDir" required></td>
                </tr>
                <tr>
                    <td class="tooltip"><label>Georectification Storage Directory (optional):<span class="tooltiptext">Applicable for Map Template only</span></label></td>
                    <td><input type="text" name="txtGeorecStorageDir" id="txtGeorecStorageDir"></td>
                </tr>
            </table>
        </div>
    </fieldset>
    <fieldset id="fsConfirm"><table id="tbConfirmation">
            <tr>
                <td>Display Name:</td>
                <td id="tdDisplayName"></td>
            </tr>
            <tr>
                <td>Parameter Name:</td>
            <td id="tdParamterName"></td>
            </tr>
            <tr>
                <td>Database Name:</td>
                <td id="tdDatabaseName"></td>
            </tr>
            <tr>
                <td>Storage Directory:</td>
                <td id="tdStorageDirectory"></td>
            </tr>
            <tr>
                <td>Thumbnail Directory:</td>
                <td id="tdThumbnailDirectory"></td>
            </tr>
            <tr>
                <td>Geo-Rectification Storage Directory:</td>
                <td id="tdGeorecStorageDirectory"></td>
            </tr>
        </table></fieldset>
    <fieldset>
        <input type="submit" name="btnSubmit" id="btnSubmit" value="Submit" hidden class="bluebtn"/>
        <input type="button" name="btnConfirm" id="btnConfirm" value="Confirm" class="bluebtn"/>
    </fieldset>
</form>
<script>
    $(document).ready(function(event){
        $("#tdDisplayName").text($("").val());
        $("#tdDatabaseName").text($("").val());
        $("#tdStorageDirectory").text($("").val());
        $("#tdThumbnailDirectory").text($("").val());
        $("#tdParamterName").text($("").val());
        $("#tdGeorecStorageDirectory").text($("").val());
        $("#tbConfirmation").hide();
    });


    //Display template description once the ddlTemplate is changed
    var arrayTemplates = <?php echo json_encode($arrayTemplates); ?>;
    $("#ddlTemplate").change(function(event){
        var selectedval = $("#ddlTemplate :selected").val();
        if(selectedval == "") {
            $("#divDescription").text("");
            return;
        }
        for(var i = 0; i < arrayTemplates.length;i++)
        {
            if(selectedval == arrayTemplates[i][0])
            {
                $("#divDescription").text(arrayTemplates[i][2]);
                break;
            }
        }
    });

    //auto fill on txtDisplayName keyup event
    $("#txtDisplayName").keyup(function(event){
       event.preventDefault();

        if($("#cbAutofill").prop('checked')) { // if autofill is checked
            var dispName = $("#txtDisplayName").val();
            switch (dispName) {
                case "":
                    var parName = "";
                    var dbName = "";
                    var storDir = "";
                    var thumbDir = "";
                    var georecdir = "";
                    break;
                default:
                    var parName = dispName.toLowerCase().replace(/\s/g, '');
                    var dbName = parName + "inventory";
                    var storDir = "M:/" + dispName.replace(/\s/g, '') + "/uploads/";
                    var thumbDir = "Thumbnails/" + parName + "/";
                    var georecdir = "M:/" + dispName.replace(/\s/g, '') + "/georec/";
                    break;
            }
            $("#txtParameterName").val(parName);
            $("#txtDatabaseName").val(dbName);
            $("#txtThumbnailDir").val(thumbDir);
            $("#txtStorageDir").val(storDir);
            $("#txtGeorecStorageDir").val(georecdir);
        }
    });

    //run validation function
    $("#btnConfirm").click(function (event)
    {
        event.preventDefault();
        if($("#ddlTemplate :selected").val() == "")
        {
            alert("Please select a template!");
            return;
        }
        /* Send the data using post */
        $.ajax({
            type: 'post',
            url: 'collection_validate_processing.php',
            data:  {txtParameterName : $("#txtParameterName").val(), txtDatabaseName : $("#txtDatabasePrefix").val() + $("#txtDatabaseName").val(), txtDisplayName :$("#txtDisplayName").val()},
            success:function(data){
                switch(data)
                {
                    case "1": //existed
                        alert("Display Name, Parameter Name, and Database Name must be unique!");
                        break;
                    case "0": //good
                        $("#tdDisplayName").text($("#txtDisplayName").val());
                        $("#tdDatabaseName").text($("#txtDatabasePrefix").val() + $("#txtDatabaseName").val());
                        $("#tdStorageDirectory").text($("#txtStorageDir").val());
                        $("#tdThumbnailDirectory").text($("#txtThumbnailDir").val());
                        $("#tdParamterName").text($("#txtParameterName").val());
                        $("#tdGeorecStorageDirectory").text($("#txtGeorecStorageDir").val());
                        $("#tbConfirmation").show();
                        $("#fsInput").hide();
                        $("#btnConfirm").hide();
                        $("#btnSubmit").show();
                        break;
                    default: break;
                }
            }
        });
    });

    //submit
    $("#btnSubmit").click(function(event)
    {
        event.preventDefault();
        if($("#ddlTemplate :selected").val() == "")
        {
            alert("Please select a template!");
            return;
        }

        $("#btnSubmit").val("Submitting...");

        $.ajax({
            type: 'post',
            url: 'collection_create_processing.php',
            data:  {txtParameterName : $("#txtParameterName").val(), txtDatabaseName : $("#txtDatabasePrefix").val() + $("#txtDatabaseName").val(), txtDisplayName :$("#txtDisplayName").val(), txtStorageDir: $("#txtStorageDir").val(), txtThumbnailDir: $("#txtThumbnailDir").val(), ddlTemplate: $("#ddlTemplate :selected").val(), txtGeorecStorageDir:$("#txtGeorecStorageDir").val() },
            success:function(data){
                alert(data);
                $("#btnSubmit").val("Submit");
                }
            });
        });
</script>
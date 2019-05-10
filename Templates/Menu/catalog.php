<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 1/16/2019
 * Time: 2:14 PM
 */?>
<!--<div class="card" style="width: 18rem;">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><a href="../../Templates/Map/catalog.php?col=bluchermaps">Blucher Maps</a></li>
        <li class="list-group-item"><a href="../../Templates/FieldBook/list.php?col=blucherfieldbook&action=catalog">Field Book</a></li>
        <li class="list-group-item"><a href="../../Templates/FieldBookIndices/catalog.php?col=fieldbookindices">Field Book Indices</a></li>
        <li class="list-group-item"><a href="../../Templates/Map/catalog.php?col=greenmaps">Green Maps</a></li>
        <li class="list-group-item"><a href="../../Templates/Indices/catalog.php?col=mapindicies">Map Indices</a></li>
        <li class="list-group-item"><a href="../../Templates/Folder/list.php?col=jobfolder&action=catalog">Job Folder</a></li>
        <li class="list-group-item"><a href="../../Templates/Map/catalog.php?col=pennyfenner">Pennyfenner Maps</a></li>
    </ul>
</div>-->

<div class="card" style="width: 18rem;">
    <div class="list-group list-group-flush">
        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#blucher">Blucher</a>
        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#green">Green</a>
        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#penny">Pennyfenner</a>
    </div>
</div>

<!-- Blucher Modal -->
<div class="modal fade" id="blucher" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Blucher Collection</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="text-center">Please select an option</h5>
                <div class="list-group">
                    <a class="list-group-item list-group-item-action" href="../../Templates/Map/catalog.php?col=bluchermaps">Maps</a>
                    <a class="list-group-item list-group-item-action" href="../../Templates/FieldBook/list.php?col=blucherfieldbook&action=catalog">Field Book</a>
                    <a class="list-group-item list-group-item-action" href="../../Templates/FieldBookIndices/catalog.php?col=fieldbookindices">Field Book Indices</a>
                    <a class="list-group-item list-group-item-action" href="../../Templates/Indices/catalog.php?col=mapindices">Map Indices</a>
                    <a class="list-group-item list-group-item-action" href="../../Templates/Folder/list.php?col=jobfolder&action=catalog">Job Folder</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Green Modal -->
<div class="modal fade" id="green" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Green Collection</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="text-center">Please select an option</h5>
                <div class="list-group">
                    <a class="list-group-item list-group-item-action" href="../../Templates/Map/catalog.php?col=greenmaps">Maps</a>
                    <a class="list-group-item list-group-item-action disabled" href="#">Field Book</a>
                    <a class="list-group-item list-group-item-action disabled" href="#">Field Book Indices</a>
                    <a class="list-group-item list-group-item-action disabled" href="#">Map Indices</a>
                    <a class="list-group-item list-group-item-action disabled" href="#">Job Folder</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Pennyfenner Modal -->
<div class="modal fade" id="penny" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Pennyfenner Collection</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="text-center">Please select an option</h5>
                <div class="list-group">
                    <a class="list-group-item list-group-item-action" href="../../Templates/Map/catalog.php?col=pennyfenner">Maps</a>
                    <a class="list-group-item list-group-item-action" href="../../Templates/FieldBook/list.php?col=pennyfennerfieldbook&action=catalog">Field Book</a>
                    <a class="list-group-item list-group-item-action disabled" href="#">Field Book Indices</a>
                    <a class="list-group-item list-group-item-action disabled" href="#">Map Indices</a>
                    <a class="list-group-item list-group-item-action disabled" href="#">Job Folder</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
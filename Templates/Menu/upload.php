<?php
/**
 * Created by PhpStorm.
 * User: hreeves
 * Date: 1/16/2019
 * Time: 2:20 PM
 */?>
<!--<div class="card" style="width: 18rem;">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><a href="../../Templates/FieldBook/upload.php?col=blucherfieldbook">Field Book</a></li>
        <li class="list-group-item"><a href="../../Templates/Folder/upload.php?col=jobfolder">Job Folder</a></li>
    </ul>
</div>-->


<div class="card" style="width: 20rem; border-color: #0c0c0c; border-width: 3px">
    <div class="list-group list-group-flush">
        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#blucher">Blucher</a>
        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#green">Green</a>
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
                    <a class="list-group-item list-group-item-action" href="../../Templates/FieldBook/upload.php?col=blucherfieldbook">Field Book</a>
                    <a class="list-group-item list-group-item-action" href="../../Templates/Folder/upload.php?col=jobfolder">Job Folder</a>
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
                    <a class="list-group-item list-group-item-action" href="../../Templates/FieldBook/upload.php?col=greenfieldbook">Field Book</a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


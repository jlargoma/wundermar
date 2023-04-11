<?php
use \Carbon\Carbon;
setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
    <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
</button>
<div class="row">
    <div class="col-md-12 not-padding">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center font-w300">
                    Cupos por Tamaño de Aptos
                </h2>
            </div>
            <div class="col-xs-12">
                <div class="col-md-8 col-md-offset-2 content-sizes">
                    @include('backend.rooms.table_size_aptos_summary')
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 content-aptos-table" >
                @include('backend.rooms.table_rooms_order_fast_payment')
            </div>
        </div>
    </div>
</div>


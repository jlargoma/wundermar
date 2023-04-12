<?php   use \Carbon\Carbon;  
        setlocale(LC_TIME, "ES"); 
        setlocale(LC_TIME, "es_ES"); 
?>
@extends('layouts.admin-master')

@section('title') Contabilidad  @endsection

@section('externalScripts') 
	<style type="text/css">
        .selectCash.selected{
            font-weight: 800;
            color: #1f7b00;
        }
    </style>
@endsection

@section('content')
<div class="container padding-5 sm-padding-10">
	<div class="row bg-white">
		<div class="col-md-12 col-xs-12">

			<div class="col-md-3 col-md-offset-3 col-xs-12">
				<h2 class="text-center">
					CAJA
				</h2>
			</div>
			<div class="col-md-2 col-xs-12 sm-padding-10" style="padding: 10px">
				@include('backend.years._selector')
			</div>
		</div>
	</div>
  <div class="row mb-1em">
         @include('backend.sales._button-contabiliad')
        </div>
</div>
<div class="container-fluid">

    <div class="row bg-white">
        <div class="col-lg-10 col-md-12 col-xs-12">
            @include('backend.sales.cashbox._formCashBox')
        </div>
    </div>
	
	<div class="row bg-white">
       <div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-xs-12">
                <h3 class="text-center selectCash" data-type="jorge" style="cursor: pointer;">
                    <?php $totalJorge = 0;//$saldoInicial->import; ?>
                    <?php foreach ($cashboxJor as $key => $cash): ?>
                        <?php if ($cash->type == 1): ?>
                            <?php $totalJorge -= $cash->import ?>
                        <?php endif ?>
                        <?php if ($cash->type == 0): ?>
                            <?php $totalJorge += $cash->import ?>
                        <?php endif ?>
                        
                        
                    <?php endforeach ?>
                    CAJA JORGE (<?php echo number_format($totalJorge, 0, ',','.') ?>€)
                </h3>
        </div>
	    <div class="col-md-12 col-xs-12 contentCashbox table-responsive" style="border: 0px;">
           
           @include('backend.sales.cashbox._tableMoves', ['cashbox' => $cashboxJor, 'saldoInicial' => $saldoInicial ])
	       
        </div>
	</div>
</div>
	
@endsection	


@section('scripts')
<script type="text/javascript">

    $('.selectCash').click(function(event) {
        var type = $(this).attr('data-type');
        var year = "{{ $year->year }}";

        $('.selectCash').each(function() {
            $(this).removeClass('selected');
        });

        $(this).addClass('selected');

        $('.contentCashbox').empty().load('/admin/caja/getTableMoves/'+year+'/'+type);

        // 


    });


</script>
@endsection
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
<div class="box-btn-contabilidad">
	<div class="row bg-white">
		<div class="col-md-12 col-xs-12">

			<div class="col-md-3 col-md-offset-3 col-xs-12">
				<h2 class="text-center">
					 BANCO (<?php echo number_format($totals, 0, ',','.') ?>€)
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
	    <div class="col-md-12 col-xs-12 contentBank">
           
           @include('backend.sales.bank._tableMoves', ['bank' => $bankItems, 'saldoInicial' => $saldoInicial ])
	       
        </div>
	</div>
</div>
	
@endsection	


@section('scripts')
<script type="text/javascript">

	$('#fecha').change(function(event) {
	    
	    var year = $(this).val();
	    window.location = '/admin/banco/'+year;

	});

</script>
@endsection
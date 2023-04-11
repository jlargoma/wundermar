@extends('layouts.admin-master')

@section('title') Liquidacion @endsection

@section('externalScripts') 
	
	<link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
	<link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />
<style>
	.table>thead>tr>th {
		padding:0px!important;
	}
	.totales>thead>tr>th{
		border:solid 1px black;
	}
	.totales>tbody>tr>td{
		border: solid 1px black;
	}
	th{
		/*font-size: 15px!important;*/
	}
	td{
		font-size: 11px!important;
		padding: 10px 5px!important;
	}
	.pagos{
		background-color: rgba(255,255,255,0.5)!important;
	}

	td[class$="bi"] {border-left: 1px solid black;}
	td[class$="bf"] {border-right: 1px solid black;}
	
	.coste{
		background-color: rgba(200,200,200,0.5)!important;
	}
	th.text-center.bg-complete.text-white{
		padding: 10px 5px;
		font-weight: 300;
		font-size: 12px!important;
		text-transform: capitalize!important;
	}
	
	.red{
		color: red;
	}
	.blue{
		color: blue;
	}
</style>
@endsection

@section('content')
<?php use \Carbon\Carbon; 
setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>


<div class="container-fluid padding-5 sm-padding-10">

    <div class="row">
    	<div class="col-xs-12 text-center">
    		<h2>Liquidación</h2>
    	</div>
        <div class="col-xs-12">
        	<div class="col-xs-12 pull-right table-responsive" style="padding: 15px 5px 15px 5px;margin-bottom: 10px">
    			<table class="table table-hover demo-table-search table-responsive totales" >
					<thead> 
						<th class ="text-center bg-complete text-white" style="width: 10%!important">Total</th>
						<th class ="text-center bg-complete text-white" style="width: 10%!important">Coste total</th>
						<th class ="text-center bg-complete text-white" style="width: 10%!important">C. banco</th>
						<th class ="text-center bg-complete text-white" style="width: 10%!important">C. Jorge</th>
						<th class ="text-center bg-complete text-white" style="width: 10%!important">C. Jaime</th>
						<th class ="text-center bg-complete text-white" style="width: 10%!important">Pendiente </th>
						<th class ="text-center bg-complete text-white" style="width: 10%!important">B. Jorge</th>
						<th class ="text-center bg-complete text-white" style="width: 10%!important">B. Jaime</th>
						<th class ="text-center bg-complete text-white" style="width: 10%!important">Limpieza</th>
					</thead>
					<tbody>
						<tr>
							<td class="text-center"><?php echo number_format($totales["total"],2,',','.') ?>€</td>
							<td class="text-center"><?php echo number_format($totales["coste"],2,',','.') ?>€</td>
							<td class="text-center"><?php echo number_format($totales["banco"],2,',','.') ?>€</td>
							<td class="text-center"><?php echo number_format($totales["jorge"],2,',','.') ?>€</td>
							<td class="text-center"><?php echo number_format($totales["jaime"],2,',','.') ?>€</td>
							<td class="text-center"><?php echo number_format($totales["pendiente"],2,',','.') ?>€</td>
							<td class="text-center"><?php echo number_format($totales["benJorge"],2,',','.') ?>€</td>
							<td class="text-center"><?php echo number_format($totales["benJaime"],2,',','.') ?>€</td>
							<td class="text-center"><?php echo number_format($totales["limpieza"],2,',','.') ?>€</td>
						</tr>
					</tbody>
    			</table>
        	</div>
        	<div style="clear: both;"></div>

			<div class="tab-content table-responsive">
				<div class="pull-left">
			        <div class="col-xs-12" style="margin-bottom: 10px;margin-top: 10px">
			            <input type="text" id="search-table" class="form-control pull-right" placeholder="Buscar">
			        </div>
			    </div>
				

			    <div class="tab-pane active" id="tabPrices">
			        <table class="table table-hover demo-table-search table-responsive" id="tableWithSearch" >
			        	<thead>
			        		<th class ="text-center bg-complete text-white" style="width: 7%">Nombre</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Pax</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Apto</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">IN</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">OUT</th>
			        		<th class ="text-center bg-complete text-white" style="width: 2%"><i class="fa fa-moon-o"></i></th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">PVP</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Cob <br> Banco</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Cob <br> Jorge</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Cob <br> Jaime</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Pend</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Ingreso <br> Total</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">%Ben</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Coste <br> Total</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Coste <br> Apto</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Park</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Lujo</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Limp</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Agencia</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Ben <br> Jorge</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">% <br> Jorge</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">Ben <br> Jaime</th>
			        		<th class ="text-center bg-complete text-white" style="width: 5%">% <br> Jaime</th>
			        	</thead>
			        	<tbody >
			        		<?php foreach ($books as $book): ?>
			        			<tr >
				        			<td class="text-center">
										<a class="update-book" data-id="<?php echo $book->id ?>"  title="Editar Reserva"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>"><?php  echo $book->customer['name'] ?></a>
									</td>
				        			<td class="text-center"><?php echo $book->pax ?></td>
				        			<td class="text-center"><?php echo $book->room->name ?></td>
				        			<td class="text-center">
				        				<?php 
    										$start = Carbon::createFromFormat('Y-m-d',$book->start);
    										echo $start->formatLocalized('%d %b');
    									?>
				        			</td>
				        			<td class="text-center">
				        				<?php 
    										$finish = Carbon::createFromFormat('Y-m-d',$book->finish);
    										echo $finish->formatLocalized('%d %b');
    									?>
				        			</td>
				        			<td class="text-center"><?php echo $book->nigths ?></td>
				        			<td class="text-center"><?php echo number_format($book->total_price,2,',','.') ?> €</td>

				        			<td class="text-center pagos bi"><?php echo number_format($book->getPayment(2),2,',','.'); ?> €</td>
				        			<td class="text-center pagos"><?php echo number_format($book->getPayment(0),2,',','.'); ?> €</td>
				        			<td class="text-center pagos"><?php echo number_format($book->getPayment(1),2,',','.'); ?> €</td>
									<td class="text-center pagos pendiente"><?php echo number_format(($book->total_price - $book->getPayment(4)),2,',','.')." €"; ?></td>
				        			<td class="text-center coste bi" style="border-left: 1px solid black;"><?php echo number_format($book->total_ben,2,',','.') ?> €</td>
				        			<td class="text-center coste"><?php echo number_format($book->inc_percent,0)." %" ?></td>
				        			<td class="text-center coste"><?php echo number_format($book->cost_total,2,',','.')?> €</td>
				        			<td class="text-center coste"><?php echo number_format($book->cost_apto,2,',','.')?> €</td>
				        			<td class="text-center coste"><?php echo number_format($book->cost_park,2,',','.')?> €</td>
				        			<td class="text-center coste" ><?php echo number_format($book->cost_lujo,2,',','.')?> €</td>
				        			<td class="text-center coste">		<?php echo number_format($book->sup_limp,2,',','.') ?>€</td>
				        			<td class="text-center coste bf">	<?php echo number_format($book->agency,2,',','.') ?>€</td>
				        			<td class="text-center"><?php echo number_format($book->ben_jorge,2,',','.') ?></td>
				        			<?php if ($book->total_ben > 0): ?>
				        				<td class="text-center"><?php echo number_format(($book->total_ben/$book->ben_jorge)*100)."%" ?></td>
				        			<?php else: ?>
				        				<td class="text-center"> 0%</td>
				        			<?php endif ?>
				        			
				        			<td class="text-center"><?php echo number_format($book->ben_jaime,2,',','.') ?></td>
				        			<?php if ($book->total_ben > 0): ?>
				        				<td class="text-center"><?php echo number_format(($book->total_ben/$book->ben_jaime)*100)."%" ?></td>
				        			<?php else: ?>
				        				<td class="text-center"> 0%</td>
				        			<?php endif ?>
				        		</tr>
			        		<?php endforeach ?>
			        		
			        	</tbody>
			        </table>
			    </div>
			</div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript"></script>
<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/lodash.min.js"></script>

<script src="/assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script type="text/javascript" src="/assets/plugins/jquery-autonumeric/autoNumeric.js"></script>
<script type="text/javascript" src="/assets/plugins/dropzone/dropzone.min.js"></script>
<script type="text/javascript" src="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>
<script src="/assets/plugins/bootstrap-form-wizard/js/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/assets/plugins/summernote/js/summernote.min.js" type="text/javascript"></script>
<script src="/assets/plugins/moment/moment.min.js"></script>
<script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="/assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
<script src="/assets/plugins/bootstrap-typehead/typeahead.bundle.min.js"></script>
<script src="/assets/plugins/bootstrap-typehead/typeahead.jquery.min.js"></script>
<script src="/assets/plugins/handlebars/handlebars-v4.0.5.js"></script>

<script type="text/javascript">
		// var colorPendienteCobro = function(){
		// 	var pendientes  = $('.pendiente');


		// 	for(ind in pendientes){
	  			
	 //  			var pendCobro = pendientes[ind];

	 //  			if ($(pendCobro).text() == '0,00 €') {
	 //  				$(pendCobro).addClass("blue");
	 //  			}else{
	 //  				$(pendCobro).addClass("red");
	 //  			};
		// 	}
		// }

	// $(document).ready(function() {
		
			
	// 	colorPendienteCobro();
	// 	$('.dataTables_paginate').click(function(event) {
	// 		colorPendienteCobro();
	// 	});
	// });
</script>
@endsection
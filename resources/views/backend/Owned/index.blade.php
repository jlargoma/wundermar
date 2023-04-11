@extends('layouts.admin-master')

@section('title') Seccion Propietarios @endsection

@section('externalScripts')
	<link href="/assets/css/font-icons.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />

	<link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('/assets/plugins/bootstrap-datepicker/css/datepicker3.css')}}" type="text/css" >
    <link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css">

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
    <style type="text/css">
    
        .botones{
            padding-top: 0px!important;
            padding-bottom: 0px!important;
        }
        td{
            margin: 0px;
            padding: 0px!important;
            vertical-align: middle!important;
        }
        a {
            color: black;
            cursor: pointer;
        }
        .S, .D{
            background-color: rgba(0,0,0,0.2);
            color: red;
        }
        .active>a{
            color: white!important;
        }
        .bg-info-light>li>a{
            color: white;
        }
        .active.res{
            background-color: #295d9b !important; 
        }
        .active.bloq{
            background-color: orange !important; 
        }
        .active.pag{
            background-color: green !important; 
        }
        .res,.bloq,.pag{
            background-color: rgba(98,108,117,0.5);
        }
        .nav-tabs > li > a:hover, .nav-tabs > li > a:focus{
            color: white!important;
        }

        .fechas > li.active{
            background-color: rgb(81,81,81);
        }
        .nav-tabs ~ .tab-content{
            padding: 0px;
        }
        .paginate_button.active>a{
            color: black!important;
        }
        .table.table-hover tbody tr:hover td {
            background: #99bce7 !important;
        }

        .table.table-striped tbody tr.Reservado td select.minimal{
            background-color: rgba(0,200,10,0.0)  !important;
            color: black!important;
            font-weight: bold!important;
        }

            
        
        .table.table-striped tbody tr.Bloqueado td select.minimal{
            background-color: #D4E2FF  !important;
            color:red!important;
            font-weight: bold!important;

        }
        .nav-tabs-simple > li.active a{
            font-weight: 800;
        }
        span.numPaymentLastBooks{
            position: absolute;
            top: -10px;
            right: -10px;
            background: red;
            border-radius: 100%;
            padding: 0px 7px;
            z-index: 15;
        }
.btn-nav {
    color: #fff;
    padding: 0.35em 0.7em;
    font-size: 1.2em;
    cursor: pointer;
}
        li.btn-nav.active {
    background-color: #51b1f7;
}
    </style>
@endsection
     
@section('nav_link')

@include('backend.owned._nav_links')

@endsection
@section('content')

@include('backend.owned._content')

@endsection

@section('scripts')

	<script src="/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
	<script src="/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
	<script src="/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript"></script>
	<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
   	<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/lodash.min.js"></script>
	<script src="/assets/plugins/moment/moment.min.js"></script>
	
	<script type="text/javascript" src="{{asset('/frontend/js/components/moment.js')}}"></script>
	<script type="text/javascript" src="{{asset('/frontend/js/components/daterangepicker.js')}}"></script>
	
	<script src="/assets/js/notifications.js" type="text/javascript"></script>

	<script type="text/javascript">

			$(function() {
			  $(".daterange1").daterangepicker({
			    "buttonClasses": "button button-rounded button-mini nomargin",
			    "applyClass": "button-color",
			    "cancelClass": "button-light",
			    locale: {
			        format: 'DD MMM, YY',
			        "applyLabel": "Aplicar",
			          "cancelLabel": "Cancelar",
			          "fromLabel": "From",
			          "toLabel": "To",
			          "customRangeLabel": "Custom",
			          "daysOfWeek": [
			              "Do",
			              "Lu",
			              "Mar",
			              "Mi",
			              "Ju",
			              "Vi",
			              "Sa"
			          ],
			          "monthNames": [
			              "Enero",
			              "Febrero",
			              "Marzo",
			              "Abril",
			              "Mayo",
			              "Junio",
			              "Julio",
			              "Agosto",
			              "Septiembre",
			              "Octubre",
			              "Noviembre",
			              "Diciembre"
			          ],
			          "firstDay": 1,
			      },
			      
			  });
			});

		$(document).ready(function() {
			
			$('.bloq-fecha').click(function(event) {
				
				var x = document.getElementById('bloq');
				    if (x.style.display === 'none') {
				        x.style.display = 'block';
				    } else {
				        x.style.display = 'none';
				    }
			});
			$('.liquidacion').click(function(event) {
				
				var x = document.getElementById('liquidacion');
				    if (x.style.display === 'none') {
				        x.style.display = 'block';
				    } else {
				        x.style.display = 'none';
				    }
			});
			$('#fecha').change(function(event) {
			    
			    var year = $(this).val();
			    window.location = '/admin/propietario/<?php echo $room->nameRoom ?>/'+year;
			});

			$('#fechas').change(function(event) {
				$('.bloquear').attr('disabled', false);
			});

			$('.bloquear').click(function(event) {
				
				var id = $(this).attr('data-id');
				var fechas = $('.daterange1').val();

				$.get('/admin/propietario/bloquear', {room: id, fechas: fechas}).success(function( data ) {

					$('.notification-message').val(data);
					document.getElementById("boton").click();
					if (data == "Reserva Guardada") {
                                          $('#dateBlockContent').html('<h5 class="alert alert-success text-center">Bloqueo de fechas guardado</h5>')
                                          setTimeout('document.location.reload()',1000);
					}else{
                          $('#dateBlockContent').html('<h5 class="alert alert-warning text-center">No se pueden reservar las fechas solicitadas<br/>Para mas información, pongase en contacto con la Administración</h5>');
                    } 
				});
			});


			$('.btn-content').click(function(event) {
				var url = $(this).attr('data-url');
				$('button.btn-content').css('background-color', '#10cfbd');
//				$('#btn-back').css('background-color', '#10cfbd');
				$('.btn-nav').removeClass('active');
				$(this).addClass('active');
				$.get(url, function(data) {

					$('#content-info').empty().append(data);
					$('#content-info-ini').hide();
					$('#content-info').show();

					$('#btn-back').show();
				});
			});


			$('#btn-back').click(function(event) {
				$('#content-info').hide();
				$('#content-info-ini').show();
				$('#content-info').empty();
				
				$(this).css('background-color', '#0a7d72');
				$('.btn-content').css('background-color', '#10cfbd');
			});

			$('.btn-blocks').click(function(event) {
				var block = $(this).attr('data-block');

				$('.blocks').hide();
				$("."+block).show();

				$('#content-info').hide();
				$('#content-info-ini').show();
				$('#content-info').empty();

				$('.btn-nav').removeClass('active');
				$(this).addClass('active');
//				$('.btn-blocks').css('background-color', '#10cfbd');
//				$(this).css('background-color', '#0a7d72');
//				$('.btn-content').css('background-color', '#10cfbd')
				

			});

			$('.btn-fechas-calendar').click(function(event) {
                event.preventDefault();
                $('.btn-fechas-calendar').css({
                    'background-color': '#899098',
                    'color': '#fff'
                });
                $(this).css({
                    'background-color': '#10cfbd',
                    'color': '#fff'
                });
                var target = $(this).attr('data-month');
                var targetPosition = $('.content-calendar #month-'+target).position();
                // alert("Left: "+targetPosition.left+ ", right: "+targetPosition.right);
                $('.content-calendar').animate({ scrollLeft: "+="+targetPosition.left+"px" }, "slow");
            });

            $('#btn-active').trigger('click');

		});
		
	</script>

	<script type="text/javascript">
	$(document).ready(function() {
		/* GRAFICA INGRESOS/GASTOS */
			var data = {
			    labels: [{!! $estadisticas['ingresos']['label'] !!}],
			    datasets: [
                                        {
                                            label: "Ingresos",
                                            backgroundColor: 'rgba(67, 160, 71, 0.3)',
                                            borderColor: 'rgba(67, 160, 71, 1)',
                                            borderWidth: 1,
                                            data: [{!! $estadisticas['ingresos']['val'] !!}]
                                        }
                                    ]
			};

			var myBarChart = new Chart('barChart', {
			    type: 'line',
			    data: data,
			});



		/* GRAFICA CLIENTES */

			/* La configuracion de posicion funciona con la siguiente que se escriba*/
			Chart.defaults.global.legend.position = 'top';
			Chart.defaults.global.legend.labels.usePointStyle = true;
			/*Fin de configuracion de posicion*/
			var dataClient = {
			    labels: [{!! $estadisticas['ingresos']['label'] !!}],
			    datasets: [
			        {
			            label: "Clientes por mes",
			            backgroundColor: 'rgba(54, 162, 235, 0.2)',
			            borderColor: 'rgba(54, 162, 235, 1)',
			            borderWidth: 1,
			            data: [{!! $estadisticas['ingresos']['val'] !!}],
			        }
			    ]
			};

			var myBarChartClient = new Chart('barChartClient', {
			    type: 'bar',
			    data: dataClient,
			});
	});
</script>
@endsection
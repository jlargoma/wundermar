<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>
@extends('layouts.admin-master')

@section('title') Payland  @endsection

@section('externalScripts')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<script type="text/javascript" src="{{asset('/js/bootbox.min.js')}}"></script>
<style>
  .table-responsive .table-resumen th,
  .table-responsive .table-resumen td{
    white-space: nowrap;
    text-align: center;
  }
  .table-responsive .table-resumen th.static,
  .table-responsive .table-resumen td.static{
    width: 170px;
    min-width: 170px;
  }
  .table-responsive .table-resumen th.first-col,
  .table-responsive .table-resumen td.first-col{
    padding-left: 180px !important;
  }
  
  
  .paginate span {
    border: none !important;
    font-weight: 800;
  }
  .paginate {
      min-width: 225px;
      text-align: center;
  }
</style>
@endsection

@section('content')
<div class="box-btn-contabilidad">
  <div class="row bg-white">
    <div class="col-md-12 col-xs-12">

      <div class="col-md-3 col-md-offset-3 col-xs-12">
        <h2 class="text-center">Payland</h2>
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
  <div class="row">
    <div class="col-md-8 col-xs-12">
      <div class="row push-10">
        <div class="col-md-8">
          <h2 class="text-left font-w800">
            Resumen liquidación: <span id="payland_month_2"></span> 
          </h2>
        </div>
        <div class="col-md-4 pull-right">
          <h2 class="text-left font-w800 text-danger">
          Comisión: <span id="comision_resume"></span>
          </h2>
        </div>
        
      </div>
      <div class="col-md-12 col-xs-12" style="padding-right: 0;">
        <div class="month_select-box">
          <div class="month_select" id="ms" data-month="" data-year="{{$year->year}}">Todos</div>
          @foreach($months_obj as $m)
          <div class="month_select" id="ms_{{$m['id']}}" data-month="{{$m['month']}}" data-year="{{$m['year']}}">{{$m['name']}} {{$m['year']}}</div>
          @endforeach
        </div>
        <div class="table-responsive" id="payland_table">
          <table class="table">
            <thead >
            <th class ="text-center bg-complete text-white td-date-payland">Fecha</th>
            <th class ="bg-complete text-white td-name-payland">Nombre</th>
            <th class ="text-center bg-complete text-white td-mount-payland">Importe</th>
            <th class ="text-center bg-complete text-white ">Estado</th>
            <th class ="text-center bg-complete text-white">Card</th>
            </thead>
            <tbody id="tableItems">
            </tbody>
          </table>
        </div>
        <div class="paginate">
          <span id="payland_first" class="action"> << </span>
          <span id="payland_ant" class="action"><</span>
          <span id="payland_page"> 1 </span>
          <span id="payland_sgt" class="action">></span>
          <span id="payland_last" class="action">>></span>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-xs-12">
      <div class="row bg-white push-30">
        <div class="col-md-4 bordered text-center">
          <h4 class="hint-text">Hoy</h4>
            <h3 ><span id="payland_today"></span></h3>
        </div>
        <div class="col-md-4 bordered text-center">
          <h4 class="hint-text">Mes</h4>
          <h3 ><span id="payland_month"></span></h3>
        </div>
        <div class="col-md-4 bordered text-center">
          <h4 class="hint-text">Temporada</h4>
            <h3 ><span id="payland_season"></span></h3>
        </div>
        <div class="col-md-4 bordered text-center">
          <h4 class="hint-text">Total de <br/>pagos</h4>
          <div class="p-l-20">
            <h3 ><span id="payland_total"></span></h3>
          </div>
        </div>
        <div class="col-md-4 bordered text-center">
          <h4 class="hint-text">Promedio x<br/>pagos</h4>
            <h3 ><span id="payland_average"></span>€</h3>
        </div>
        <div class="col-md-4 bordered text-center">
          <h4 class="hint-text">Comsión <br/>estimada</h4>
            <h3 ><span id="payland_comision"></span>€</h3>
        </div>
      </div>
      <div>
        <canvas id="barChartTemp" style="width: 100%; height: 250px;"></canvas>
      </div>
      <div class="col-md-12 table-responsive">
        <h3>cobrado por TPV</h3>
        
        <div class="table-responsive">
          <table class="table table-resumen">
            <thead>
              <tr class="resume-head">
                <th class="static">Total</th>
                <?php $class = 'class="first-col"'; ?>
                @foreach($months_obj as $m)
                  <th {!!$class!!}>
                    {{$m['name']}} {{$m['year']}}
                  </th>
                  <?php $class = ''; ?>
                @endforeach
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="static">
                  <?php 
                  $total = 0;
                  foreach($months_obj as $m) $total += $m['t_pvp'];
                  ?>
                  Cobrado ({{moneda($total)}})
                </td>
                <?php $class = 'class="first-col"'; ?>
                @foreach($months_obj as $m)
                <td {!!$class!!} ><?php echo number_format($m['t_pvp'], 0, ',', '.') ?> €</td>
                <?php $class = ''; ?>
                @endforeach
              </tr>
              <tr>
                <td class="static">
                  Comisión ({{moneda(paylandCost($total),true,1)}})
                </td>
                <?php $class = 'class="first-col"'; ?>
                @foreach($months_obj as $m)
                <td {!!$class!!} >{{moneda(paylandCost($m['t_pvp']),true,1)}}</td>
                <?php $class = ''; ?>
                @endforeach
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">


/////////////////////////////////////////


var select_year = 0;
var select_month = 0;
var dataTable = function(year, month){
$('#year').val(year);
$('#month').val(month);
//$('.month_select.active').removeClass('active');
select_year = year;
select_month = month;
$('#loadigPage').show('slow');
$.ajax({
url: '/admin/getOrders-payland',
        type:'POST',
        data: {year:year, month:month, '_token':"{{csrf_token()}}"},
        success: function(response){
        if (response.status === 'true'){

//        $('#ms_' + year + '_' + month).addClass('active');
        $('#tableItems').html('');
        $('#payland_month').text(response.total_month);
        $('#payland_month_2').text(response.total_month);
        $('#comision_resume').text(response.comision);
        var totalPages = Math.ceil(response.respo_list.length/50);
        $('#payland_last').data('val',totalPages);
        
        var count = 0;
        $.each((response.respo_list), function(index, val) {

        count++;
        var page = Math.ceil(count/50);

        var row = '';
        var row = '<tr class="payland_page'+page+'" '
        if (page == 1) row += '>';
        else row += 'style="display: none">';
        
        row += '<td class="td-date-payland">' + val.date + '</td>';
        row += '<td class="td-name-payland">' + val.customer_name + '<br>' + val.customer + '</td>';
        row += '<td class="td-mount-payland">' + val.amount + ' ' + val.currency + '</td>';
        row += '<td class="text-center pay-status-' + val.status + '">' + val.status + '</td>';
        row += '<td class="text-center">' + val.sourceType + '<br>' + val.pan + '</td>';
        $('#tableItems').append(row);
        });
        } else{
        window.show_notif('ERROR', 'danger', 'El listado está vacío no ha sido guardado.');
        }
        $('#loadigPage').hide('slow');
        },
        error: function(response){
        window.show_notif('ERROR', 'danger', 'No se ha podido obtener los detalles de la consulta.');
        $('#loadigPage').hide('slow');
        }
});
}


var dataPaylandSummary = function(){
$.ajax({
url: '/admin/get-summary-payland',
        type:'GET',
        success: function(response){
        if (response.status === 'true'){
          $('#payland_today').text(response.today);
          
          $('#payland_season').text(response.season);
          $('#payland_total').text(response.count.SUCCESS);
          $('#payland_comision').text(response.comision);
          $('#payland_average').text(response.average);
          
          
        new Chart(document.getElementById("barChartTemp"), {
        type: 'line',
                data: {
                labels: [{!!$months_label!!}],
                        datasets: [
                        {
                        data: response.result.SUCCESS,
                                label: 'Pagadas',
                                borderColor: "rgba(104, 255, 0, 1)",
                                fill: false
                        },
                        {
                        data: response.result.REFUSED,
                                label: 'Rechazadas',
                                borderColor: "rgba(232, 142, 132, 1)",
                                fill: false
                        }
                        ]
                },
                options: {
                title: {
                display: false,
                        text: ''
                }
                }
        });
        } else{
        window.show_notif('ERROR', 'danger', 'El listado está vacío no ha sido guardado.');
        console.log('error');
        }
        $('#loadigPage').hide('slow');
        },
        error: function(response){
        window.show_notif('ERROR', 'danger', 'No se ha podido obtener los detalles de la consulta.');
        console.log('error');
        $('#loadigPage').hide('slow');
        }
});
}

var currentPage = 1;
$(document).ready(function() {
  var dt = new Date();
  dataTable({!!$selected!!});
  dataPaylandSummary();
$('#ms_{{$selectedID}}').addClass('active');

  $('.month_select').on('click', function(){
    $('.month_select').removeClass('active');
    $(this).addClass('active');
    dataTable($(this).data('year'), $(this).data('month'));
  });
  
  $('#payland_first').on('click', function(){
    if (currentPage>1){
      $('.payland_page'+currentPage).hide('150');
      $('.payland_page1').show();
      currentPage = 1;
      $('#payland_page').text(currentPage);
    }
  });
    $('#payland_last').on('click', function(){
      $('.payland_page'+currentPage).hide('150');
      currentPage = $(this).data('val');
      $('.payland_page'+currentPage).show();
      $('#payland_page').text(currentPage);
  });
  $('#payland_ant').on('click', function(){
    if (currentPage>1){
      $('.payland_page'+currentPage).hide('150');
      $('.payland_page'+(currentPage-1)).show();
      currentPage--;
      $('#payland_page').text(currentPage);
    }
  });
  $('#payland_sgt').on('click', function(){
    $('.payland_page'+currentPage).hide('150');
    $('.payland_page'+(currentPage+1)).show();
    currentPage++;
    $('#payland_page').text(currentPage);
  });
});

</script>
@endsection
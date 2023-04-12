<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>
@extends('layouts.admin-master')

@section('title') Limpiezas  @endsection

@section('externalScripts')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<script type="text/javascript" src="{{asset('/js/bootbox.min.js')}}"></script>
<style>
  #limpieza .month_select {
    min-width: 4.5em;
  }
  #limpieza_summ th{
    text-align: center;
    background-color: #1f7b00;
    color: #FFF;
  }
  #limpieza_summ td{
    text-align: center;
    text-align: center;
    font-weight: 800;
    padding: 4px !important;
  }
  #limpieza_table td.link{
    cursor: pointer;
  }
  #limpieza_table td.link:hover{
    color: #9b59ff;
  }
  th.text-center.bg-complete.text-white.col-md-2.static {
    width: 140px;
    height: 5em;
    margin-top: 0;
    line-height: 3.1;
  }

  td.link.static {
    width: 10.5em !important;
    height: 49px;
    line-height: 2.2;
    opacity: 1;
  }
  th.first-col {
    background-color: #51b0f7;
  }
  .table-responsive input {
    max-width: 80px;
  }
  @media only screen and (max-width: 991px){

    .table-resumen .fix-col-data{
      width:120px;overflow-x: scroll;
      white-space: nowrap;
    }
  }
</style>
@endsection

@section('content')
<div class="box-btn-contabilidad">
  <div class="row bg-white">
    <div class="col-md-12 col-xs-12">

      <div class="col-md-3 col-md-offset-3 col-xs-12">
        <h2 class="text-center">Limpiezas</h2>
      </div>
      <div class="col-md-2 col-xs-12 sm-padding-10" style="padding: 10px">
        @include('backend.years._selector')
      </div>
    </div>
  </div>
  <?php if (Auth::user()->role !== "limpieza"): ?>
    <div class="row mb-1em">
      @include('backend.sales._button-contabiliad')
    </div>
  <?php endif ?>
</div>



<div class="container-fluid" id="limpieza">
  <div class="row">
    <div class="col-md-8 col-xs-12">
      <div class="row push-10">
        <div class="col-md-6">
          <h2 class="text-left font-w800">
            Resumen liquidación
            <form action="{{ URL::to('admin/limpiezas/pdf') }}" method="POST" style="display: inline-block;">
              <input type="hidden" id="year" name="year" value="1999">
              <input type="hidden" id="month" name="month" value="1">
              <input type="hidden" id="_token" name="_token" value="{{csrf_token()}}">
              <button class="btn-pdf" title="Exportar a PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>
            </form>
          </h2>
        </div>
        <div class="col-md-3 col-sm-6">
          <h5 class="text-center text-danger bold">Total acumulado Limpiezas: {{number_format($totalCostBooks, 0, ',', '.')}}€</h5>
        </div>
        <div class="col-md-3 col-sm-6">
          <h5 class="text-center text-danger bold">Total acumulado Extras: {{number_format($extraCostBooks, 0, ',', '.')}}€</h5>
        </div>

      </div>
      <div class="col-md-12 col-xs-12" style="padding-right: 0;">
        <div class="month_select-box">
          <div class="month_select" id="ms_{{$year->year}}_00" data-month="0" data-year="{{$year->year}}">Todos</div>
          @foreach($months_obj as $m)
          <div class="month_select" id="ms_{{$m['id']}}" data-month="{{$m['month']}}" data-year="{{$m['year']}}">{{$m['name']}} {{$m['year']}}</div>
          @endforeach
        </div>
        <div class="table-responsive" id="limpieza_summ">
          <table class="table">
            <tr>
              <th>Acumulado Limpieza</th>
              <th>Acumulado Extras</th>
              <th>TOTAL MES EN CURSO</th>
              <th>Nro de Reservas</th>
            </tr>
            <tr>
              <td class="sum_limp"></td>
              <td class="sum_ext"></td>
              <td class="sum_total"></td>
              <td class="sum_count"></td>
            </tr>
          </table>
        </div>
        <div class="table-responsive table-resumen" id="limpieza_table">
          <table class="table">
            <thead >
            <th class ="text-center bg-complete text-white col-md-2 static">Nombre</th>
            <th class="first-col"></th>
            <th class ="text-center bg-complete text-white col-md-1">tipo</th>
            <th class ="text-center bg-complete text-white col-md-1">Pax</th>
            <th class ="text-center bg-complete text-white col-md-1">apto</th>
            <th class ="text-center bg-complete text-white col-md-2">in - out</th>
            <th class ="text-center bg-complete text-white col-md-2"><i class="fa fa-moon"></i></th>
            <th class ="text-center bg-complete text-white col-md-2">Limpieza<br><b id="t_limp"></b></th>
            <th class ="text-center bg-complete text-white col-md-2">Extras<br><b id="t_extr"></b></th>
            </thead>
            <tbody id="tableItems">
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-xs-12">
      <div>
        <canvas id="barChartMonth" style="width: 100%; height: 250px;"></canvas>
      </div>
      <div>
        <canvas id="barChartTemp" style="width: 100%; height: 250px;"></canvas>
      </div>

      <div class="row table-responsive">
        <table class="table table-resumen">
          <thead>
            <tr class="resume-head">
              <th class="static">Concepto</th>
              <th class="first-col">Total</th>
              @foreach($t_month as $item)
              <th>{{$item['label']}}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="static">Limpieza</td>
              <td class="first-col"><?php echo number_format($totalCostBooks, 0, ',', '.') ?> €</td>
              @foreach($t_month as $item)
              <td><?php echo number_format($item['limp'], 0, ',', '.'); ?>€</td>
              @endforeach
            </tr>
            <tr>
              <td class="static">Extras</td>
              <td class="first-col"><?php echo number_format($extraCostBooks, 0, ',', '.') ?> €</td>
              @foreach($t_month as $item)
              <td><?php echo number_format($item['extra'], 0, ',', '.'); ?>€</td>
              @endforeach
            </tr>
          </tbody>
        </table>
      </div>



    </div>
  </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">

var myBarChart = new Chart('barChartMonth', {
type: 'bar',
        data: {
        labels: [{!!$months_1['months_label']!!}],
                datasets: [
                {
                label: "Total Limpieza:",
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 0.2)',
                        borderWidth: 1,
                        data: [{!!$months_1['months_val']!!}],
                }
                ]
        },
});
new Chart(document.getElementById("barChartTemp"), {
type: 'line',
        data: {
        labels: [{!!$months_1['months_label']!!}],
                datasets: [
                {
                data: [{!!$months_1['months_val']!!}],
                        label: '{{$months_1['year']}} - {{$months_1['year']-1}}',
                        borderColor: "rgba(54, 162, 235, 1)",
                        fill: false
                },
                @if($months_2)
                {
                data: [{!!$months_2['months_val']!!}],
                        label: '{{$months_2['year']}} - {{$months_2['year']-1}}',
                        borderColor: "rgba(104, 255, 0, 1)",
                        fill: false
                },
                @endif
                @if($months_3)
                {
                  data: [{!!$months_3['months_val']!!}],
                  label: '{{$months_3['year']}} - {{$months_3['year']-1}}',
                  borderColor: "rgba(232, 142, 132, 1)",
                  fill: false
                },
                @endif
                ]
        },
        options: {
        title: {
        display: false,
                text: ''
        }
        }
});
/////////////////////////////////////////


var limp_year = 0;
var limp_month = 0;
var dataTable = function(year, month){
$('#year').val(year);
$('#month').val(month);
$('.month_select.active').removeClass('active');
limp_year = year;
limp_month = month;
$('#loadigPage').show('slow');
$('#limp_fix').val('');
$('#extr_fix').val('');
$.ajax({
url: '/admin/limpiezasLst',
        type:'POST',
        data: {year:year, month:month, '_token':"{{csrf_token()}}"},
        success: function(response){
        if (response.status === 'true'){
        if (month < 10) month = '0' + parseInt(month);
        $('#ms_' + year + '_' + month).addClass('active');
        $('#t_limp').text(response.total_limp);
        $('#t_extr').text(response.total_extr);
        $('#limp_fix').val(response.month_cost);
        $('#monthly_extr').text(0);
        $('#tableItems').html('');
        var summ = $('#limpieza_summ');
        summ.find('.sum_limp').text(response.total_limp);
        summ.find('.sum_ext').text(response.total_extr);
        summ.find('.sum_total').text(response.total_summ);
        summ.find('.sum_count').text(response.count);
        $.each((response.month_cost), function(index, val) {
        var row = '<tr><td colspan="6"><strong>' + val.concept + ' (' + val.date_text + ')</strong></td>';
        row += '<td class="text-center"><input id="limp_' + val.id + '" data-id="' + val.id + '" data-date="' + val.date + '" type="text" class="form-control limpieza_upd" value="' + val.import + '"></td>';
        row += '<td class="text-center"></td>';
        $('#tableItems').append(row);
        });
        $.each((response.respo_list), function(index, val) {
        var row = '';
        if (val.agency){
        var name = '<img style="width: 20px;" src="' + val.agency + '" align="center" />' + val.name;
        }
        var name = val.name;

        var row = '<tr><td class="link static" data-k="' + val.id + '">' + name + '</td>';
        row += '<td class="first-col"></td>';
        row += '<td class="text-center">' + val.type + '</td>';
        row += '<td class="text-center">' + val.pax + '</td>';
        row += '<td class="text-center">' + val.apto + '</td>';
        row += '<td class="text-center">' + val.check_in + ' - ' + val.check_out + '</td>';
        row += '<td class="text-center">' + val.nigths + '</td>';
        row += '<td class="text-center"><input id="limp_' + val.id + '" data-id="' + val.id + '" type="text" class="form-control limpieza_upd" value="' + val.limp + '"></td>';
        row += '<td class="text-center"><input id="extr_' + val.id + '" data-id="' + val.id + '" type="text" class="form-control limpieza_upd" value="' + val.extra + '"></td>';
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
$(document).ready(function() {
var dt = new Date();
dataTable({!!$selected!!});
$('.month_select').on('click', function(){
dataTable($(this).data('year'), $(this).data('month'));
});
$('#limpieza_table').on('click', '.link', function(){
var id = $(this).data('k');
console.log('/admin/reservas/update/' + id);
window.open('/admin/reservas/update/' + id, '_blank').focus();
});
$('#limpieza_table').on('change', '.limpieza_upd', function(){
var id = $(this).data('id');
var date = $(this).data('date');
var row = $(this).closest('tr');
var data = {
'id':id,
        'year':limp_year,
        'month':limp_month,
        'date':date,
        '_token':"{{csrf_token()}}",
        'limp_value': row.find('#limp_' + id).val(),
        'extr_value': row.find('#extr_' + id).val(),
}
$('#loadigPage').show('slow');
$.ajax({
url: '/admin/limpiezasUpd',
        type:'POST',
        data: data,
        success: function(response){
        if (response.status === 'true'){
        window.show_notif('OK', 'success', 'Registro Guardado.');
        } else{
        window.show_notif('ERROR', 'danger', response.msg);
        }
        $('#loadigPage').hide('slow');
        },
        error: function(response){
        window.show_notif('ERROR', 'danger', 'No se ha podido obtener los detalles de la consulta.');
        $('#loadigPage').hide('slow');
        }
});
});
});

</script>
@endsection
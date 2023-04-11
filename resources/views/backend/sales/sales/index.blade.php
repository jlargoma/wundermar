<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>
@extends('layouts.admin-master')

@section('title') Informe Conversión  @endsection

@section('externalScripts')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<script type="text/javascript" src="{{asset('/js/bootbox.min.js')}}"></script>
<link href="{{ assetV('/css/custom-backend.css')}}" rel="stylesheet" type="text/css" />

<style>

  #main_table th {
    padding: 6px 11px !important;
    white-space: nowrap;
  }
  #tableItems tr td{
    text-align: center;
    padding-bottom: 7px !important;
    padding-top: 7px !important;
  }
  
  

  @media only screen and (max-width: 769px){
    #main_table tr .first-col {
      padding-left: 120px !important;
    }
    #main_table tr th.static{
      background-color: #fafafa;
      padding: 1px 7px !important;
      width: 115px;
    }
    #tableItems tr td.static {
      text-align: left;
      padding-left: 6px !important;
      background-color: #FFF;
      width: 115px;
    }

  }
</style>
@endsection

@section('content')
<div class="row">

    <div class="col-md-2 col-xs-12 mt-3em">
        <div class="col-xs-12"></div>
    </div>
 <div class="col-md-8 col-xs-12">
        <div class="row bg-white mt-2em">
            <div class="col-md-6 col-xs-6 text-right">
                <h2 class="text-center">INFORMES EMPLEADOS</h2>
            </div>
            <div class="col-md-4 col-xs-4 sm-padding-10" style="padding: 10px">
                @include('backend.years._selector')
            </div>
            <div class="col-md-12 col-xs-12 mb-1em text-center">
                @include('backend.revenue._buttons')
            </div>
        </div>
    </div>
    </div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-xs-12">
      <div class="row push-10">
        <div class="col-md-4">
          <h2 class="text-left font-w800">Resumen</h2>
        </div>
        <div class="col-sm-4">
          <label for="type">Empleado</label>
          <select class="form-control" id="u_id" name="u_id">
            <option value="0" >Todos</option>
            @foreach($users as $uID=>$uName)
            <option value="{{ $uID }}" >{{ $uName }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-4">
          <h5 class="text-center text-danger bold">Total Convertidas: <span id="totalConv"></span></h5>
          <h5 class="text-center text-danger bold">Total acumulado (extras): <span id="totalMonth"></span></h5>
        </div>
      </div>
      <div class="col-md-12 col-xs-12" style="padding-right: 0;">
        <div class="month_select-box">
          <div class="month_select" id="ms_{{$year_sel.'_0'}}" data-month="0" data-year="{{$year_sel}}">
            Todos
          </div>
          @foreach($lstMonths as $k=>$v)
          <div class="month_select" id="ms_{{$v['y'].'_'.$v['m']}}" data-month="{{$v['m']}}" data-year="{{$v['y']}}">
            {{$v['name']}}
          </div>
          @endforeach
        </div>
        <div class="table-responsive" id="main_table">
          <table class="table">
            <thead >
            <th class="th-bookings static">  
              Empleado
            </th>
            <th class="th-bookings first-col"></th> 
            <th class="th-bookings">Conver.</th>
            <th class="th-bookings">%</th>
            @if($extTyp)
            @foreach($extTyp as $k=>$name)
            <th class="th-bookings">{{$name}}</th>
            <th class="th-bookings">%</th>
            @endforeach
            @endif
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
    </div>
  </div>

</div>
<?php $index = 0;?>
@endsection
@section('scripts')
<script type="text/javascript">
  
  

new Chart(document.getElementById("barChartMonth"), {
type: 'line',
        data: {
        labels: [{!! implode(',', $months_label) !!}],
                datasets: [
                        @if ($extrasGroup)
<?php $index = 0; ?>
                @foreach($extrasGroup as $uID => $item)
                {
                <?php unset($item[0])?>
                data: [{{implode(',', $item)}}],
                        label: '{{show_isset($users,$uID,"usr")}}',
                        borderColor: "{{printColor($index)}}",
                        fill: false
                },
<?php $index++; ?>
                @endforeach
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
var year_sel = 0;
var month_sel = 0;
var user_sel = 0;
var dataTable = function(){

$('.month_select.active').removeClass('active');
$('#loadigPage').show('slow');
$.ajax({
url: '/admin/salesLst',
        type:'POST',
        data: {year:year_sel, month:month_sel, user_id:user_sel, '_token':"{{csrf_token()}}"},
        success: function(response){
        if (response.status === 'true'){

        $('#ms_' + year_sel + '_' + month_sel).addClass('active');
        $('#totalMonth').text(response.totalMonth);
        $('#totalConv').text(response.totalConv);
        $('#tableItems').html('');
        if (response.result.length>0){
          $.each((response.result), function(index, val) {
            var row = '<tr>';
            console.log(val);
            row += '<td class="static">' + val[0] + '</td><td class="first-col"></td>';
            for (var i in val){
              if (i > 0) row += '<td>' + val[i] + '</td>';
            }
            row += '</tr>';
            $('#tableItems').append(row);
          });
        } else{
          var row = '<tr><td colspan="20" class="table-warning">El listado está vacío</td></tr>';
          $('#tableItems').append(row);
        }
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
year_sel = {{$year_sel}};
month_sel = {{$month_sel}};
user_sel = 0;
dataTable();
$('.month_select').on('click', function(){
year_sel = $(this).data('year');
month_sel = $(this).data('month');
dataTable();
});
$('#u_id').on('change', function(){
user_sel = $(this).val();
dataTable();
});
});

</script>
@endsection
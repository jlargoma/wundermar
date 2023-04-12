<?php   
use \Carbon\Carbon;
use \App\Classes\Mobile;
setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$mobile = new Mobile();
$isMobile = $mobile->isMobile();
?>
@extends('layouts.admin-master')

@section('title') Caja  @endsection

@section('externalScripts')
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
  <style>
    .table-resumen .first-col {
      white-space: nowrap;
    }

    tr.text-center.contab-site,
    tr.text-center.contab-site td{
      color: #fff;
      font-weight: 600;
      background-color: #004a2f;
    }
    tr.text-center.contab-ch {
        color: blue;
        font-weight: 600;
    }

    i.fas.fa-plus-circle.toggle-contab-site,
    i.fas.fa-plus-circle.toggle-contab-extra,
    i.fas.fa-plus-circle.toggle-contab {
      padding: 5px;
      cursor: pointer;
    }
    
    .contab-ch.tr-close,
    .contab-extras.tr-close,
    .contab-room.tr-close{
      display: none;
    }
    .pieChart{
      max-width: 270px;
      margin: 1em auto;
    }
    button.del_expense.btn.btn-danger.btn-xs {
      margin: 3px 14px;
    }
    
    .table-responsive>.table>tbody#tableItems>tr>td{
       white-space: normal;
      border-left: solid 1px #cacaca;
      padding: 8px !important;
    }
    .table-responsive>.table>tbody#tableItems>tr.selected {
      color: #000;
    }
    i.fa.cajaDelete {
      color: #ee5653;
      font-size: 17px;
      cursor: pointer;
    }
    .table.table-parkings th{
      color: #000;
    }
    .table.table-parkings th,
    .table.table-parkings td{
      padding: 4px;
      border-top: 1px solid red !important;
      border: 1px solid red;
      text-align: center;
    }
    .table.table-parkings th.border-b-none{
      border-bottom: none;
    }
    
    @media only screen and (max-width: 425px) {
      .butons-add .btn {
        width: 32%;
        padding: 6px 0;
      }
    }
  </style>
@endsection

@section('content')
<div class="box-btn-contabilidad">
  <div class="row show-mobile">
    <div class="col-xs-8">
      <h2>Caja</h2>
    </div>
    <div class="col-xs-4">
      @if($isMobile)  @include('backend.years._selector', ['minimal' => true]) @endif
    </div>
  </div>
  <div class="row bg-white hidden-mobile">
    <div class="col-md-12 col-xs-12">

      <div class="col-md-3 col-md-offset-3 col-xs-12">
        <h2 class="text-center">Caja</h2>
      </div>
      <div class="col-md-2 col-xs-12 sm-padding-10" style="padding: 10px">
        @if(!$isMobile) @include('backend.years._selector')@endif
      </div>
    </div>
  </div>
  <div class="row mb-1em">
    @include('backend.sales._button-contabiliad')
  </div>
</div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4 butons-add">
          <button type="button" class="btn btn-success btn-green" id="addNew_ingr" type="button" data-toggle="modal" data-target="#modalAddIngr">
            <i class="fas fa-plus-circle toggle-contab-site"></i> <span class="hidden-mobile">Añadir</span> Ingresos</button>
          <button type="button" class="btn btn-danger" id="addNew_gasto" type="button" data-toggle="modal" data-target="#modalAddGasto">
            <i class="fas fa-plus-circle toggle-contab-site"></i> <span class="hidden-mobile">Añadir</span> Gastos</button>
          <button type="button" class="btn btn-info" id="addNew_arqueo" type="button" data-toggle="modal" data-target="#modalAddArqueo">
            <i class="fas fa-plus-circle toggle-contab-site"></i> <span class="hidden-mobile">Añadir</span> Arqueo</button>
        </div>
        <div class="col-md-4">
          <div class="row">
            <h5 class="col-md-6">SALDO MES <span id="t_month">0</span></h5>
            <h5 class="col-md-6">SALDO ANUAL {{moneda($totalYear)}}</h5>
          </div>
        </div>
        <div class="col-md-4 table-responsive">
          @include('backend.sales.caja._table_parkings')
        </div>
      </div>
        <div class="col-md-12 col-xs-12" style="padding-right: 0; min-height: 43em;">
        <div class="month_select-box">
          <div class="month_select" id="ms_{{$year->year}}_0" data-month="0" data-year="{{$year->year}}">
          Todos
          </div>
        @foreach($lstMonths as $k=>$v)
        <div class="month_select" id="ms_{{$v['y'].'_'.$v['m']}}" data-month="{{$v['m']}}" data-year="{{$v['y']}}">
          {{getMonthsSpanish($v['m'])}}
        </div>
        @endforeach
        </div>
          <input type="hidden" id="year" value="">
          <input type="hidden" id="month" value="">
          <div class="table-responsive">
          <table class="table">
            <thead >
              <th class="text-center bg-complete text-white col-md-1">Fecha</th>
              <th class="text-center bg-complete text-white col-md-2">Concepto</th>
              <th class="text-center bg-complete text-white col-md-2">Tipo</th>
              <th class="text-center bg-complete text-white col-md-1">Debe</th>
              <th class="text-center bg-complete text-white col-md-1">Haber</th>
              <th class="text-center bg-complete text-white col-md-1">Saldo</th>
              <th class="text-center bg-complete text-white col-md-2">Sitio</th>
              <th class="text-center bg-complete text-white col-md-2">Comentario</th>
              <th class="text-center bg-complete text-white col-md-1"></th>
            </thead>
            <tbody id="tableItems" class="text-center">
            </tbody>
          </table>
        </div>
      </div>
      
<div class="modal fade" id="modalAddGasto" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <strong class="modal-title" id="modalChangeBookTit" style="font-size: 1.4em;">Añadir Gasto</strong>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">@include('backend.sales.gastos._form')</div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalAddIngr" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <strong class="modal-title" id="modalChangeBookTit" style="font-size: 1.4em;">Añadir Ingreso</strong>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">@include('backend.sales.caja._form_ingresos')</div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalAddArqueo" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <strong class="modal-title" id="modalChangeBookTit" style="font-size: 1.4em;">Arqueo</strong>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">@include('backend.sales.caja._form_arqueo')</div>
    </div>
  </div>
</div>
</div>
    
@endsection

<!---->
@section('scripts')
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="{{ asset('/css/components/daterangepicker.css')}}" type="text/css" />
<script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/assets/plugins/moment/moment.min.js"></script>
<script type="text/javascript">
  $('.datepicker').datepicker();
</script>
<script type="text/javascript">
      
  var expense_year  = 0;
  var expense_month = 0;
  var dataTable = function(year, month){

    $('#year').val(year);
    $('#month').val(month);
   
    $('.month_select.active').removeClass('active');
    expense_year  = year;
    expense_month = month;
    $('#loadigPage').show('slow');
    $.ajax({
        url: '/admin/caja/cajaLst',
        type:'POST',
        data: {year:year, month:month, '_token':"{{csrf_token()}}"},
        success: function(response){
          if (response.status === 'true'){

            $('#ms_'+year+'_'+month).addClass('active');
            $('#tableItems').html('');
            $('#t_month').html(response.total);
            
            
             var row = '<tr><td></td>';
              row += '<td>SALDO '+response.month_prev+'</td>';
              row += '<td>--</td>';
              row += '<td>--</td>';
              row += '<td>--</td>';
              row += '<td>' + response.totalPrev.toFixed(2) + '</td>';
              row += '<td>--</td>';
              row += '<td>Saldo mes anterior</td>';
              row += '<td>---</td></tr>';
              $('#tableItems').append(row);
            
            var saldo = 0;
            $.each((response.respo_list), function(index, val) {
              if (val.debe != '--')  saldo += parseFloat(val.debe);
              if (val.haber != '--') saldo -= parseFloat(val.haber);
              var row = '<tr><td>' + val.date + '</td>';
              row += '<td>' + val.concept + '</td>';
              row += '<td>' + val.type + '</td>';
              row += '<td>' + val.debe + '</td>';
              row += '<td>' + val.haber+ '</td>';
              row += '<td>' + saldo.toFixed(2) + '</td>';
              row += '<td>' + val.site + '</td>';
              row += '<td>' + val.comment + '</td>';
              row += '<td><i class="fa fa-trash cajaDelete" data-key="' + val.key + '" ></i></td></tr>';
              $('#tableItems').append(row);
            });
          } else{
            $('#tableItems').html('<tr><td colspan="7">El listado está vacío.</td></tr>');
            window.show_notif('ERROR','danger','El listado está vacío.');
          }
          $('#loadigPage').hide('slow');
        },
        error: function(response){
          window.show_notif('ERROR','danger','No se ha podido obtener los detalles de la consulta.');
          $('#loadigPage').hide('slow');
        }
    });
  }
  $(document).ready(function() {
    var dt = new Date();
    dataTable({!!$current!!});
    $('.month_select').on('click', function(){
    dataTable($(this).data('year'),$(this).data('month'));
    });
    
    $('#tableItems').on('click','.del_expense', function(){
      if (confirm('Eliminar el registro definitivamente?')){
        var id = $(this).data('id');
        $.ajax({
          url: '/admin/gastos/del',
          type:'POST',
          data: {id:id, '_token':"{{csrf_token()}}"},
          success: function(response){
             dataTable($('#year').val(),$('#month').val());
          }
        });
      }
    });
    
    
    $('#modalAddGasto').on('click','#reload', function(e){
      location.reload();
    });
    $('#modalAddGasto').on('submit','#formNewExpense', function(e){
      e.preventDefault();
      $.ajax({
          url: $(this).attr('action'),
          type:'POST',
          data: $( this ).serializeArray(),
          success: function(response){
            if (response == 'ok'){
              $('#import').val('');
              $('#concept').val('');
              $('#comment').val('');
              alert('Gasto Agregado');
            }
            else alert(response);
          }
        });
    });
  });

$("#tableItems").on('click','tr',function(){
   $(this).addClass('selected').siblings().removeClass('selected');    
});
$("#tableItems").on('click','.cajaDelete',function(){
  if (confirm('Borrar el registro de la caja?')){
    var key = $(this).data('key');
    $.ajax({
      url: '/admin/caja/del-item',
      type:'POST',
      data: {key:key, '_token':"{{csrf_token()}}"},
      success: function(response){
        if (response.status == 'OK'){
          dataTable($('#year').val(),$('#month').val());
        } else {
          window.show_notif('ERROR','danger',response.msg);
        }
      }
    });
  }
   
});
    
    </script>
@endsection
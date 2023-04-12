<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>
@extends('layouts.admin-master')

@section('title') Extras  @endsection

@section('externalScripts')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<script type="text/javascript" src="{{asset('/js/bootbox.min.js')}}"></script>
<style>
 #tableItems tr td{
      text-align: center;
    }
 
  #tableItems tr.extr_header {
    background-color: #004a2f;
    color: #fff;
}

  #tableItems tr.extr_header td .fa-plus-circle,
  #tableItems tr.extr_header td .fa-minus-circle{
    cursor: pointer;
    padding: 5px;
  }
  #tableItems tr.extr_header td{
background-color: transparent;
  }
  
  .extr_book.tr-close{
    display: none;
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
        <h2 class="text-center">
          Excursiones
        </h2>
      </div>
      <div class="col-md-2 col-xs-12 sm-padding-10" style="padding: 10px">
        @include('backend.years._selector')
      </div>
    </div>
  </div>
  <?php if (getUsrRole() !== "limpieza"): ?>
  <div class="row mb-1em">
         @include('backend.sales._button-contabiliad')
        </div>
  <?php endif ?>
</div>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 col-xs-12">
      <div class="row push-10">
        <div class="col-md-6">
          <h2 class="text-left font-w800">
            Resumen liquidación
            
          </h2>
        </div>
        <div class="col-sm-6">
          <h5 class="text-center text-danger bold">Total acumulado Extras: <span id="totalMonth"></span></h5>
        </div>
        
      </div>
      <div class="col-md-12 col-xs-12" style="padding-right: 0;">
        <div class="month_select-box">
        @foreach($lstMonths as $k=>$v)
        <div class="month_select" id="ms_{{$v['y'].'_'.$v['m']}}" data-month="{{$v['m']}}" data-year="{{$v['y']}}">
          {{$v['name']}}
        </div>
        @endforeach
        </div>
        <div class="table-responsive" id="limpieza_table">
          <table class="table">
            <thead >
            <th  class ="text-center bg-complete text-white col-md-2">Nombre</th>
            <th class ="text-center bg-complete text-white col-md-2">in - out</th>
            <th class ="text-center bg-complete text-white col-md-1"><i class="fa fa-moon"></i></th>
            <th class ="text-center bg-complete text-white col-md-2">APTO</th>
            <th class ="text-center bg-complete text-white col-md-1">Unidad</th>
            <th class ="text-center bg-complete text-white col-md-1">PVP</th>
            <th class ="text-center bg-complete text-white col-md-1">COSTE</th>
            <th class ="text-center bg-complete text-white col-md-1">%VENTA</th>
            <th class ="text-center bg-complete text-white col-md-2">VENDEDOR</th>
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
      <div class="mt-1em">
        @include('backend.sales.extras.resume-by-extras')
      </div>
      <div class="mt-1em" id="analysisExcursion">
        
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
          labels: [{!! implode(',',$months_label) !!}],
              datasets: [
              {
                label: "Total Limpieza:",
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 1,
                data: [{{implode(',',$months_1)}}]
              }
              ]
      },
  });
  
   new Chart(document.getElementById("barChartTemp"), {
        type: 'line',
        data: {
          labels: [{!! implode(',',$months_label) !!}],
          datasets: [
            {
            data:[{{implode(',',$months_1)}}],
            label: '{{$year->year}}',
            borderColor: "rgba(232, 142, 132, 1)",
            fill: false
            },
            {
            data: [{{implode(',',$months_2)}}],
            label: '{{$year->year-1}}',
            borderColor: "rgba(54, 162, 235, 1)",
            fill: false
            },
            {
            data: [{{implode(',',$months_3)}}],
            label: '{{$year->year-2}}',
            borderColor: "rgba(104, 255, 0, 1)",
            fill: false
            },
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
  
  
  var limp_year  = 0;
  var limp_month = 0;
  var dataTable = function(year, month){
    $('#year').val(year);
    $('#month').val(month);
   
    $('.month_select.active').removeClass('active');
    limp_year  = year;
    limp_month = month;
    $('#loadigPage').show('slow');
    $('#limp_fix').val('');
    $('#extr_fix').val('');
    $.ajax({
        url: '/admin/excursionesLst',
        type:'POST',
        data: {year:year, month:month, '_token':"{{csrf_token()}}"},
        success: function(response){
          if (response.status === 'true'){

            $('#ms_'+year+'_'+month).addClass('active');

            $('#t_limp').text(response.total_limp);
            $('#t_extr').text(response.total_extr);
            $('#totalMonth').text(response.totalMonth);
            $('#monthly_extr').text(0);
            $('#tableItems').html('');
            $('#analysisExcursion').html(response.excursionsHTML);
            $.each((response.extras), function(index, val) {
              
              
              var row = '<tr class="extr_header"><td colspan="4" class="text-left extr_group" data-id="'+index+'"> <i class="fas fa-plus-circle"></i>' + val.name + '</td>';
              row += '<td>' + val.qty + '</td>';
              row += '<td>' + parseInt(val.price) + '</td>';
              row += '<td>' + parseInt(val.cost) + '</td>';
              row += '<td> -- </td>';
              row += '<td> -- </td></tr>';
              
              $('#tableItems').append(row);
              
              if (typeof response.respo_list[index] !== 'undefined'){
                
                $.each((response.respo_list[index]), function(index2, val2) {
                      console.log(index,val2);
                     var row = '<tr class="extr_book extr_group_'+index+' tr-close">';
                     row += '<td class="text-left"><a href="/admin/reservas/update/'+val2.book.id+'">' + val2.book.customer + '</a></td>';
                     row += '<td>' + val2.book.date + '</td>';
                     row += '<td>' + val2.book.nigth + '</td>';
                     row += '<td>' + val2.book.room + '</td>';
                     row += '<td>' + val2.qty + '</td>';
                     row += '<td>' + val2.price + '</td>';
                     row += '<td>' + val2.cost + '</td>';
                     row += '<td>' + val2.percent + '%</td>';
                     row += '<td>' + val2.vdor + '</td></tr>';
                     $('#tableItems').append(row);
                   });
              } else {
                var row = '<tr class="warning extr_book extr_group_'+index+' tr-close">';
                row += '<td colspan="9">No hay resgistros</td></tr>';
                $('#tableItems').append(row);
              }
            });
          } else{
            window.show_notif('ERROR','danger','El listado está vacío no ha sido guardado.');
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
  dataTable({!!$selected!!});
  $('.month_select').on('click', function(){
  dataTable($(this).data('year'),$(this).data('month'));
  });

  $('#limpieza_table').on('change','.limpieza_upd', function(){
    var id = $(this).data('id');
    var row = $(this).closest('tr');
    var data = {
        'id':id,
        'year':limp_year,
        'month':limp_month,
        '_token':"{{csrf_token()}}",
        'limp_value': row.find('#limp_'+id).val(),
        'extr_value': row.find('#extr_'+id).val(),
      }
    $('#loadigPage').show('slow');
    
    $.ajax({
          url: '/admin/limpiezasUpd',
          type:'POST',
          data: data,
          success: function(response){
            if (response.status === 'true'){
              window.show_notif('OK','success','Registro Guardado.');
            } else{
              window.show_notif('ERROR','danger',response.msg);
            }
            $('#loadigPage').hide('slow');
          },
          error: function(response){
            window.show_notif('ERROR','danger','No se ha podido obtener los detalles de la consulta.');
            $('#loadigPage').hide('slow');
          }
    });
  });

 $('#tableItems').on('click','.extr_group',function(){
        var id = $(this).data('id');
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');
          $('.extr_group_'+id).addClass('tr-close');
        } else {
          $(this).addClass('open');
          $(this).find('i').addClass('fa-minus-circle').removeClass('fa-plus-circle');
          $('.extr_group_'+id).removeClass('tr-close');
        }
      });

});

</script>
@endsection
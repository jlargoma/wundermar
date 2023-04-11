<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$isMobile = config('app.is_mobile');
?>
@extends('layouts.admin-master')

@section('title') Perdidas y ganancias  @endsection

@section('externalScripts') 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<style>
  @media only screen and (max-width: 991px){
    .resume-box .col-md-4{
      width: 33% !important;
      float: left;
      padding: 2px;
      text-align: center;
    }
  }

  td{
    height: 3em !important;
    padding: 7px 9px !important;
  }
  @media only screen and (max-width: 768px){

    td.static {
      position: absolute !important;
      white-space: nowrap;
    }
    .col-ingr{
      width: 180px;
      overflow-x: scroll;
      text-align: left !important;
      margin-top: 1px;
      padding: 13px 9px !important;
    }
    tr:last-child td{
      height: 4em !important;
    }
  }
  @media only screen and (max-width: 425px){
    .resume-box .col-md-4 .p-l-20{
      padding: 1em 0px !important;
    }

    .resume-box .col-md-4 h3{
      font-size: 22px;
    }
    .resume-box .col-md-4 h5{
      font-size: 15px;
    }
    .box-resumen{
      font-size: 21px !important;
    }
  }
  
  div#modalShowDetail .modal-content {
    min-width: 62vw;
  }
  .fa.result{
    font-size: 1.13em
  }
  .box-resumen{
    background-color: #a94442;
    padding: 5px 26px;
    font-size: 2em;
    color: #FFF;
    font-weight: 800;
    min-height: 5em;
  }
  .light-blue{
    background-color: #48b0f7
  }
  input.inputIngr {
    width: 7em;
    padding: 4px 5px;
    border: none;
    background-color: #e4e4e4;
  }
  .editable.tSelect select.form-control.selects {
    width: 7em;
    padding: 0 5px;
    margin: 0;
    display: block;
    margin: 0 auto;
  }
  td.open_detail {
    cursor: pointer;
  }
  td.open_detail:hover {
    color: #295d9b;
  }
</style>
@endsection

@section('content')
<div class="box-btn-contabilidad">
  <div class="row show-mobile">
    <div class="col-xs-8">
      <h2>PERDIDAS Y GANANCIAS</h2>
    </div>
    <div class="col-xs-4">
      @if($isMobile)  @include('backend.years._selector', ['minimal' => true]) @endif
    </div>
  </div>
  <div class="row bg-white hidden-mobile">
    <div class="col-md-12 col-xs-12">

      <div class="col-md-3 col-md-offset-3 col-xs-12">
        <h2 class="text-center">PERDIDAS Y GANANCIAS</h2>
      </div>
      <div class="col-md-2 col-xs-12 sm-padding-10" style="padding: 10px">
        @if(!$isMobile)   @include('backend.years._selector') @endif
      </div>
    </div>
  </div>
  <div class="row mb-1em">
    @include('backend.sales._button-contabiliad')
  </div>
</div>
<div class="container-fluid">   
  <div class="row bg-white push-30">
    <div class="col-lg-3 col-md-4 col-xs-12">
      <div>
        <canvas id="barChart" style="width: 100%; height: 250px;"></canvas>
      </div>
    </div>

    <div class="col-lg-9 col-md-8">
      <div class="row resume-box">
        <div class="col-md-3 m-b-10 col-xs-6">
          <div class="box-resumen" style="background-color: #46c37b">
            <h5 class="no-margin p-b-5 text-white ">
              <b>INGRESOS</b>
            </h5>
              {{moneda($totalIngr)}}
          </div>
        </div>
        
        <div class="col-md-3 m-b-10 col-xs-6">
          <div class="box-resumen" style="background-color: #a94442">
            <h5 class="no-margin p-b-5 text-white "><b>GASTOS</b></h5>
              {{moneda($totalGasto)}}
          </div>
        </div>
        <div class="col-md-3 m-b-10 col-xs-6">
          <div class="box-resumen" style="background-color: #2c5d9b">
            <h5 class="no-margin p-b-5 text-white ">
              <b>BENEFICIO BRUTO</b>
            </h5>
            {{moneda($ingr_bruto)}}
            <?php if (($totalIngr-$totalGasto) > 0 ): ?>
                    <i class="fa fa-arrow-up text-success result"></i>
            <?php else: ?>
                    <i class="fa fa-arrow-down text-danger result"></i>
            <?php endif ?>
          </div>
        </div>
        <div class="col-md-3 m-b-10 col-xs-6">
          <div class="box-resumen light-blue" >
            <h5 class="no-margin p-b-5 text-white ">
              <b>BENEFICIO NETO</b>
            </h5>
            {{moneda($ingr_bruto+$totalPendingIngr-$totalPendingGasto-$totalPendingImp)}}
            <?php if (($ingr_bruto+$totalPendingIngr-$totalPendingGasto-$totalPendingImp) > 0 ): ?>
                    <i class="fa fa-arrow-up text-success result"></i>
            <?php else: ?>
                    <i class="fa fa-arrow-down text-danger result"></i>
            <?php endif ?>
          </div>
        </div>
      </div>
      
      <div style="clear: both;"></div>
        @include('backend.sales._tableSummaryBoxes',['hide'=>['rvas','bnf','t_day_1']])
      
    </div>
  </div>
  <div class="row bg-white">
    <div class="col-md-12 col-xs-12">
      <div class="row table-responsive" style="border: 0px!important">
        @include('backend.sales._tablePerdidasGanancias')
      </div>
    </div>

  </div>
</div>
<div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <strong class="modal-title modalChangeBookTit" style="font-size: 1.4em;">Detalle del Gasto</strong>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"></div>
    </div>
  </div>
</div>
@endsection	


@section('scripts')
<script type="text/javascript">
  /* GRAFICA INGRESOS/GASTOS */
  var data = {
          labels: [@foreach($lstMonths as $month) "{{$month['name']}}", @endforeach],
          datasets: [
          {
          label: "Ingresos",
                  backgroundColor: 'rgba(67, 160, 71, 0.3)',
                  borderColor:'rgba(67, 160, 71, 1)',
                  borderWidth: 1,
                  data: [
                    @foreach($tIngByMonth as $k=>$v) {{round($v)}}, @endforeach
                  ],
          },
          {
          label: "Gastos",
                  backgroundColor: 'rgba(229, 57, 53, 0.3)',
                  borderColor: 'rgba(229, 57, 53, 1)',
                  borderWidth: 1,
                  data: [
                    @foreach($tGastByMonth as $k=>$v) {{round($v)}}, @endforeach
                  ],
          }

          ]
  };
  var myBarChart = new Chart('barChart', {
  type: 'line',
          data: data,
  });

$(document).ready(function () {
  const hTable = $('#tableItems');

  function edit (currentElement,key) {
    var select = $('<select>', {class:' form-control selects'});
    var current = currentElement.data('current');
    
    
    select.data('key', key);
    var option = $('<option></option>');
    option.attr('value', 'hide');
    option.text('N/A');
    select.append(option);

    var option = $('<option></option>');
    option.attr('value', 'show');
    option.text(currentElement.data('val'));
    select.append(option);

    currentElement.data('value',currentElement.html());
//    select.val(currentElement.data('current'));
    
    if (current == 0) select.val('hide');
    else select.val('show');
      
    currentElement.html(select);
  }
  
  function editInput (currentElement) {
    var key = currentElement.data('key');
    var c_val = currentElement.data('val').replace( /[^\d]/g , '' );
    var input = $('<input>', {type: "text",class: 'inputIngr'}).val(c_val)
    currentElement.html(input);
    input.focus(); 
  }

  hTable.on('click','.editable', function () {
    var that = $(this);
    if (!that.hasClass('tSelect')){
      clearAll();
      that.addClass('tSelect')

      var key = $(this).data('key');
      edit($(this),key);
    }
    
  });
  
  hTable.on('click','.editable_ingr', function () {
    var that = $(this);
    if (!that.hasClass('tSelect')){
      clearAll();
      that.addClass('tSelect')
      editInput($(this));
    }
  });


  hTable.on('keyup','.inputIngr',function (e) {
     
    if (e.keyCode == 13) {
      var obj = $(this).closest('td');
      var key  = obj.data('key');
      var month  = obj.data('month');
      var input = $(this).val();
           
      var url = "/admin/perdidas-ganancias/upd-ingr";
      $.ajax({
        type: "POST",
        method : "POST",
        url: url,
        data: {_token: "{{ csrf_token() }}",key: key,month:month, input: input},
        success: function (response)
        {
          if (response == 'OK') {
            location.reload();
          } else {
            clearAll();
            window.show_notif('Error','danger','Registro NO Actualizado');
          }
        }
      });
    
//      console.log(key,month,input);


    } else {
      $(this).val($(this).val().replace( /[^\d|^.]/g , '' ));
      e.preventDefault();
      return false;
    }
  });
  
  hTable.on('change','.selects',function (e) {

      var key = $(this).closest('td').data('key');
      var input = $(this).val();

      var url = "/admin/perdidas-ganancias/show-hide";
      $.ajax({
      type: "POST",
      method : "POST",
      url: url,
      data: {_token: "{{ csrf_token() }}",key: key, input: input},
      success: function (response)
      {
        if (response == 'OK') {
          location.reload();
        } else {
          clearAll();
          window.show_notif('Error','danger','Registro NO Actualizado');
        }
      }
    });
  });
  var clearAll= function(){
    hTable.find('.tSelect').each(function() {
//      $(this).html(
console.log($(this).data('val'),$(this));
       $(this).html($(this).data('val')).removeClass('tSelect');
//       $(this).html($(this).data('value')).removeClass('tSelect');
     });
     
   }
   
   
   
   $('#tableItems').on('click','.open_detail', function(){
     
    var id = $(this).data('key');
    var tit = $(this).text();
    $.get('/admin/perdidas-ganancias/show-detail/'+id, function(data) {
      
      $('#modalShowDetail').find('.modalChangeBookTit').text(tit);
      $('#modalShowDetail').modal('show'); 
      $('#modalShowDetail').find('.modal-body').empty().append(data);
    });
   });
});
</script>

@endsection
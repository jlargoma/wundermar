<?php   
use \Carbon\Carbon;
use \App\Classes\Mobile;
setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$mobile = new Mobile();
$isMobile = $mobile->isMobile();
?>
@extends('layouts.admin-master')

@section('title') Gastos  @endsection

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
    .table th.static-2 {
      height: 42px;
      background-color: #328f13;
      padding: 10px !important;
      margin: 5px auto;
      border-right: none;
    }
    .table.table-resumen .first-col {
        padding-left: 9em !important;
    }
    .table.table-resumen th.static,
    .table.table-resumen td.static {
        width: 9em;
    }
        
    .table-responsive th select{
      padding: 6px 11px;
      background: transparent;
      width: 80%;
      font-weight: 800;
      letter-spacing: 1.7px;
      border-color: #fff;
    }
    .table-responsive th select option {
     color: #008ff7;
    }
    .table .static-2{
      left: 9em !important;
      text-align: center !important;
    }
    th.first-col-2 {
      min-width: 15em!important;
    }
  </style>
@endsection

@section('content')
   <div class="box-btn-contabilidad">
     <div class="row show-mobile">
       <div class="col-xs-8">
         <h2>Gastos</h2>
       </div>
       <div class="col-xs-4">
         @if($isMobile)  @include('backend.years._selector', ['minimal' => true]) @endif
       </div>
     </div>
     <div class="row bg-white hidden-mobile">
       <div class="col-md-12 col-xs-12">

         <div class="col-md-3 col-md-offset-3 col-xs-12">
           <h2 class="text-center">Gastos</h2>
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
      <button type="button" class="btn btn-success" id="addNew_ingr" type="button" data-toggle="modal" data-target="#modalAddNew"><i class="fas fa-plus-circle toggle-contab-site"></i> Añadir</button>
        <div class="row">
          <div class="col-md-6 col-xs-12">
             @include('backend.sales.gastos.resume-by-month')
          </div>
          <div class="col-md-6 col-xs-12">
             @include('backend.sales.gastos.resume-by-site')
          </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-xs-12">
              <canvas id="barTemporadas" style="width: 100%; height: 250px;"></canvas>
            </div>
            <div class="col-lg-4 col-md-6 col-xs-12">
                    <?php $dataChartYear = \App\Rooms::getPvpByMonth(($year->year - 1 )) ?>
                    <?php $dataChartPrevYear = \App\Rooms::getPvpByMonth(($year->year - 2 )) ?>
                    <canvas id="chartTotalByMonth" style="width: 100%; height: 250px;"></canvas>
                </div>
            <div class="col-lg-4 col-md-6 col-xs-12">
                  <canvas id="barChartMonth" style="width: 100%; height: 250px;"></canvas>
            </div>

        </div>

       <br/> <br/> <br/>
      
          <div class="col-md-8 col-xs-12">
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
          </div>
          <div class="col-md-2 col-xs-6">
            <h3>Total Selec. <span id="totalMounth">0</span></h3>
          </div>
          <div class="col-md-2 col-xs-6">
            <h3>Total Año {{moneda($total_year_amount)}}
          </div>
          
        <div class="col-md-12 col-xs-12" style="padding-right: 0; min-height: 0.43em;">
          <input type="hidden" id="year" value="">
          <input type="hidden" id="month" value="">
          <div class="table-responsive">
          <table class="table">
            <thead >
              <th class="text-center bg-complete text-white col-md-1">Fecha</th>
              <th class="text-center bg-complete text-white col-md-2">Concepto</th>
              <th class="text-center bg-complete text-white col-md-2">
               <select id="s_type">
                  <option value="-1">Tipo</option>
                  @foreach($gType as $k=>$v)
                  <option value="{{$k}}">{{$v}}</option>
                  @endforeach
                </select>
              </th>
              <th class="text-center bg-complete text-white col-md-1">
                <select id="s_payment">
                  <option value="-1">Método de pago</option>
                  @foreach($typePayment as $k=>$v)
                  <option value="{{$k}}">{{$v}}</option>
                  @endforeach
                </select>
              </th>
              <th class="text-center bg-complete text-white col-md-2">Importe</th>
              <th class="text-center bg-complete text-white col-md-2">
                <select id="s_sitio">
                  <option value="-1">Sitio</option>
                  @foreach($allSites as $k=>$v)
                  <option value="{{$k}}">{{$v}}</option>
                  @endforeach
                </select>
                </th>
              <th class="text-center bg-complete text-white">#</th>
              <th class="text-center bg-complete text-white col-md-2">Comentario</th>
            </thead>
            <tbody id="tableItems" class="text-center">
            </tbody>
          </table>
        </div>
      </div>
      
<div class="modal fade" id="modalAddNew" tabindex="-1" role="dialog"  aria-hidden="true">
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
       
<div class="col-md-12 col-xs-12" style="min-height:43em;"> 
  <div class="clearfix">
    <textarea id="importExcel" rows="1" placeholder="Pegar los registros del Excel"></textarea>
    <div class="btnImportExcel">Importar</div>
    <form method="post" action="/admin/gastos/importar">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <table class="table" id="excel_table"></table>
      <button class="btn btn-success btnSendImportExcel" >Enviar</button>
    </form>
</div>
</div>
    
@endsection

<!---->
@section('scripts')
<style>
  .btnImportExcel{background-color: #10cfbd;float: left;padding: 7px;color: #FFF;}
  #importExcel{
    width: 80%;
    float: left;
    padding: 6px;
    margin: 0px 14px 0px 0px;
    height: 6em;
  }
  .btnSendImportExcel{clear: both; display: none;}
  select.colExcel {
    width: 100%;
    background-color: #FFF;
    color: #000;
    padding: 7px;
}
#excel_table{
  margin-top: 1em;
}
#excel_table th {
    padding: 0px !important;
}
#excel_table td input {
  border: none;
    width: 100%;
    padding: 7px;
}
</style>
<script type="text/javascript" src="/js/backend/import-excel.js"></script>
    <script type="text/javascript">
      
      var myBarChart = new Chart('barTemporadas', {
        type: 'bar',
        data: {
          labels: [
              <?php foreach ($totalYear as $k=>$v){ echo "'" . $k. "'," ;} ?>
          ],
          datasets: [
            {
              label: "Gastos por Temp",
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1,
              data: [
                  <?php foreach ($totalYear as $k=>$v){ echo "'" . round($v). "'," ;} ?>
              ],
            }
          ]
          }
      });




      new Chart(document.getElementById("chartTotalByMonth"), {
        type: 'line',
        data: {
          labels: [
            <?php foreach ($dataChartMonths as $key => $value) echo "'" . $key . "',";?>
          ],
          datasets: [
            {
              data: [
		<?php 
                foreach ($yearMonths[$year->year-2] as $key => $value){
                  if($key>0) echo "'" . round($value) . "',";
                }  
                ?>
              ],
              label: '<?php echo $year->year - 2 ?>',
              borderColor: "rgba(232, 142, 132, 1)",
              fill: false
            },
            
            {
              data: [
		<?php 
                foreach ($yearMonths[$year->year-1] as $key => $value){
                  if($key>0) echo "'" . round($value) . "',";
                }  
                ?>
              ],
              label: '<?php echo $year->year - 1 ?>',
              borderColor: "rgba(104, 255, 0, 1)",
              fill: false
            },
            {
              data: [
                <?php 
                foreach ($yearMonths[$year->year] as $key => $value){
                  if($key>0) echo "'" . round($value) . "',";
                }  
                ?>
              ],
              label: '<?php echo $year->year ?>',
              borderColor: "rgba(54, 162, 235, 1)",
              fill: false
            },
            
          ]
        },
        options: {
          title: {
            display: true,
            text: 'Total x Año'
          }
        }
      });
      
      var myBarChart = new Chart('barChartMonth', {
        type: 'line',
        data: {
          labels: [
            <?php foreach ($dataChartMonths as $key => $value) echo "'" . $key . "',"; ?>
          ],
          datasets: [
          {
            label: "wundermar",
            borderColor: '#004a2f',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[1]}}],
          }
        ]

        },
      });
      $('.toggle-contab-site').on('click',function(){
        var id = $(this).data('id');
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contab-ch-'+id).addClass('tr-close');
          $('.contab-rsite-'+id).addClass('tr-close');
        } else {
          $(this).addClass('open');
          $('.contab-ch-'+id).removeClass('tr-close');
        }
      });
      $('.toggle-contab').on('click',function(){
        var id = $(this).data('id');
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contab-room-'+id).addClass('tr-close');
          
        } else {
          $(this).addClass('open');
          $('.contab-room-'+id).removeClass('tr-close');
        }
      });
      $('.toggle-contab-extra').on('click',function(){
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contab-extras').addClass('tr-close');
          
        } else {
          $(this).addClass('open');
          $('.contab-extras').removeClass('tr-close');
        }
      });




          
          
  new Chart(document.getElementById("chart_1"), {
    type: 'pie',
    data: {
      labels: [<?php foreach($listGasto_g as $k=>$item) echo '"' . $gTypeGroup[$k] . '",'; ?>],
      datasets: [{
          backgroundColor: [
          <?php 
          $auxStart = 455;
          foreach($gastos as $k=>$item){
            $auxStart +=50;
            echo '"#' . dechex($auxStart) . '",';
          } ?>
          ],
          data: [<?php foreach($listGasto_g as $k=>$item) echo "'" .round($item[0]). "',"; ?>]
        }]
    },
    options: {
      title: {display: false},
      legend: {display: false},
    }
  });

        
    
  new Chart(document.getElementById("chart_2"), {
    type: 'pie',
    data: {
      labels: [<?php foreach($gastosSitio as $site => $data) echo '"' . $data['t'] . '",'; ?>],
      datasets: [{
          backgroundColor: ["#004a2f", "red",'green'],
          data: [<?php foreach($gastosSitio as $site => $data) echo "'" .round($data['months'][0]). "',"; ?>]
        }]
    },
    options: {
      title: {display: false},
      legend: {display: false},
    }
  });
      
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
        url: '/admin/gastos/gastosLst',
        type:'POST',
        data: {year:year, month:month, '_token':"{{csrf_token()}}"},
        success: function(response){
          if (response.status === 'true'){

            $('#ms_'+year+'_'+month).addClass('active');
            $('#tableItems').html('');
            $('#totalMounth').html(response.totalMounth);
            $('#totalMounth').data('orig',response.totalMounth);
            $.each((response.respo_list), function(index, val) {
              var row = '<tr data-id="' + val.id + '" data-import="' + val.import+ '"><td>' + val.date + '</td>';
              row += '<td class="editable" data-type="concept">' + val.concept + '</td>';
              row += '<td class="editable selects stype" data-type="type" data-current="'+ val.type_v +'" >' + val.type + '</td>';
              row += '<td class="editable selects spayment" data-type="payment" data-current="'+ val.typePayment_v +'" >' + val.typePayment + '</td>';
              row += '<td class="editable" data-type="price">' + val.import+ '</td>';
              row += '<td class="s_site editable selects" data-type="site" data-current="'+ val.site_id +'">' + val.site + '</td>';
              row += '<td><button data-id="' + val.id + '" type="button" class="del_expense btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>';
              row += '<td class="editable" data-type="comm">' + val.comment + '</td>';
              $('#tableItems').append(row);
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
    
    
    $('#modalAddNew').on('click','#reload', function(e){
      location.reload();
    });
    $('#modalAddNew').on('submit','#formNewExpense', function(e){
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


$(document).ready(function () {
  const hTable = $('#tableItems');
     
     
      function edit (currentElement,type) {
        switch(type){
          case 'price':
             var input = $('<input>', {type: "number",class: type})
            .val(currentElement.html())
            currentElement.data('value',currentElement.html());
            currentElement.html(input);
            input.focus(); 
          break;
          case 'type':
            var select = $('<select>', {class:' form-control'});
            select.data('t','type');
            <?php
                foreach ($gType as $k=>$v){
                  echo "var option = $('<option></option>');
                              option.attr('value', '$k');
                              option.text('$v');
                              select.append(option);";
                }
            ?>
            currentElement.data('value',currentElement.html());
            select.val(currentElement.data('current'));
            currentElement.html(select);
          break;
          case 'site':
            var select = $('<select>', {class:' form-control'});
            select.data('t','site');
            <?php
                foreach ($allSites as $k=>$v){
                  echo "var option = $('<option></option>');
                              option.attr('value', '$k');
                              option.text('$v');
                              select.append(option);";
                }
            ?>
            currentElement.data('value',currentElement.html());
            select.val(currentElement.data('current'));
            currentElement.html(select);
          break;
          case 'payment':
            var select = $('<select>', {class:' form-control'});
            select.data('t','payment');
            <?php
                foreach ($typePayment as $k=>$v){
                  echo "var option = $('<option></option>');
                              option.attr('value', '$k');
                              option.text('$v');
                              select.append(option);";
                }
            ?>
            currentElement.data('value',currentElement.html());
            select.val(currentElement.data('current'));
            currentElement.html(select);
          break;
          default:
             var input = $('<input>', {type: "text",class: type})
            .val(currentElement.html())
            currentElement.data('value',currentElement.html());
            currentElement.html(input);
            input.focus(); 
          break;
        }
     
      }
      hTable.on('click','.editable', function () {
        var that = $(this);
        if (!that.hasClass('tSelect')){
          clearAll();
          that.data('val',that.text());
          that.addClass('tSelect')
          var type = $(this).data('type');
          edit($(this),type);
        }
      });
      

      hTable.on('keyup','.tSelect',function (e) {
        if (e.keyCode == 13) {
          var id = $(this).closest('tr').data('id');
          var input = $(this).find('input');

          updValues(id,input.attr('class'),input.val(),$(this));
        } else {
          hTable.find('.tSelect').find('input').val($(this).find('input').val());
        }
      });
      
      hTable.on('change','.selects',function (e) {
          var id = $(this).closest('tr').data('id');
          var input = $(this).find('select');
          updValues(id,input.data('t'),input.val(),$(this),$(this).find('option:selected').text());
      });
      
      var clearAll= function(){
         hTable.find('.tSelect').each(function() {
            $(this).text($(this).data('value')).removeClass('tSelect');
          });
        }
        
      var updValues = function(id,type,value,obj,text=null){
        var url = "/admin/gastos/update";
        $.ajax({
          type: "POST",
          method : "POST",
          url: url,
          data: {_token: "{{ csrf_token() }}",id: id, val: value,type:type},
          success: function (response)
          {
            if (response == 'ok') {
              clearAll();
              window.show_notif('OK','success','Registro Actualizado');
              if (text) obj.text(text);
              else  obj.text(value);
            } else {
              window.show_notif('Error','danger','Registro NO Actualizado');
            }
          }
        });
    
      }
        
      var filters = {
        type : -1,
        paym : -1,
        site : -1,
      };
      var filterTable = function(){
        var all = false;
        if (filters.type == -1 && filters.paym == -1 && filters.site == -1 ){
          all = true;
        }
        var total = 0;
        $('#tableItems tr').each(function(){
          $(this).show();
          if (!all){
            //filter by type
            if (filters.type != -1){
              var cell = $(this).find('.stype');
              if (cell.data('current') != filters.type){
                cell.closest('tr').hide();
                return; 
              }
            }
            //filter by type payment
            if (filters.paym != -1){
              var cell = $(this).find('.spayment');
              if (cell.data('current') != filters.paym){
                cell.closest('tr').hide();
                return; 
              }
            }
             //filter by site
            if (filters.site != -1){
              var cell = $(this).find('.s_site');
              if (cell.data('current') != filters.site){
                cell.closest('tr').hide();
                return; 
              }
            }
            total += parseFloat($(this).data('import'));
          }
        });
        if (all)   $('#totalMounth').text($('#totalMounth').data('orig'));
        else $('#totalMounth').text(window.formatterEuro.format(total));
      }
      $('#s_type').on('change', function(){
        var value = $(this).val();
        filters.type = value;
        filterTable();

      });
      $('#s_payment').on('change', function(){
        var value = $(this).val();
        filters.paym = value;
        filterTable();

      });
      $('#s_sitio').on('change', function(){
        var value = $(this).val();
        filters.site = value;
        filterTable();

      });
      
      $('.month_select').on('click', function(){
        $('#s_sitio').val(-1);
        $('#s_payment').val(-1);
        $('#s_type').val(-1);
        filters = {
          type : -1,
          paym : -1,
          site : -1,
        };
//        filterTable();
      });
        
});

    

    
    </script>
@endsection
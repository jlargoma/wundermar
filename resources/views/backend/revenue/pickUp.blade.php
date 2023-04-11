@extends('layouts.admin-master')

@section('title') Revenue @endsection

@section('externalScripts')
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css"/>
<link rel="stylesheet" href="{{ asset('/assets/css/font-icons.css')}}" type="text/css"/>
<link rel="stylesheet" href="{{ asset('/css/backend/revenue.css')}}" type="text/css"/>

<script type="text/javascript" src="{{asset('/frontend/js/components/moment.js')}}"></script>
<script type="text/javascript" src="{{asset('/frontend/js/components/daterangepicker.js')}}"></script>
<script type="text/javascript" src="{{ asset('/js/datePicker01.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    var sendFormRevenue = function(){
      $('#revenu_filters').submit();
    }
    $('.daterange02').on('change',function (event) {
      var date = $(this).val();

      var arrayDates = date.split('-');
      var res1 = arrayDates[0].replace("Abr", "Apr");
      var date1 = new Date(res1);
      var start = date1.getTime();
      var res2 = arrayDates[1].replace("Abr", "Apr");
      var date2 = new Date(res2);

      $('#start').val(date1.yyyymmmdd());
      $('#finish').val(date2.yyyymmmdd());
      sendFormRevenue();
    });
    $('#site').on('change',function (event) {
      sendFormRevenue();
    });
    $('#sel_mes').on('change',function (event) {
      sendFormRevenue();
    });
    $('.sm').on('click',function (event) {
      $('#sel_mes').val($(this).data('k'));
       sendFormRevenue();
    });
    $('.tabChannels').on('click',function (event) {
      
      $('#ch_sel').val($(this).data('k'));
       sendFormRevenue();
    });
    
  });
</script>


@endsection

@section('content')

<div class="box-btn-contabilidad">
  <div class="row bg-white">
    <div class="col-md-12 col-xs-12">

      <div class="col-md-3 col-md-offset-3 col-xs-7 text-right">
        <h2 class="text-center">
          Revenue
        </h2>
      </div>
      <div class="col-md-2 col-xs-4 sm-padding-10" style="padding: 10px">
        @include('backend.years._selector')
      </div>
    </div>
  </div>
  <div class="row mb-1em text-center">
    @include('backend.revenue._buttons')
  </div>
</div>
<div class="row">
  <div class="col-md-3 col-xs-12 mt-1em">
    <div class="text-center">
          @include('backend.revenue.pick-up._actions')
        </div>
  </div>
  <div class="col-md-9 col-xs-12 ">
  <div class="table-responsive">
  <table class="tableMonths" >
    <tr>
      @foreach($months as $k=>$v)
      @if($k>0)
      <td data-k="{{$k}}" class="sm <?php if($sel_mes == $k) echo 'active' ?> ">{{$v}}</td>
      @endif
      @endforeach
    </tr>
  </table>  
    </div>
</div>
</div>
<div class="row">

    <div class="col-md-3 col-xs-12">
        <div class="text-center clearfix mt-3em">
          @include('backend.revenue.pick-up._filters')
      </div>
    </div>
    <div class="col-md-5 col-xs-12">
  <div class="row bg-white">
      <div class="col-md-12 mb-1em text-center">
        @include('backend.revenue.pick-up._summary')
       </div>
    </div>
  </div>
    <div class="col-md-4 col-xs-12 mt-3em">
        @include('backend.revenue.pick-up.byAgenci')
        @include('backend.revenue.pick-up.graf')
    </div>
  
</div>
<div class=" contenedor c-pickup">
  <div class="row">
    @include('backend.revenue.pick-up._tableItems')
  </div>
  Reservado - stripe / Pagada-la-señal / Reserva Propietario / ATIPICAS / Blocked-ical
</div>
@endsection

@section('scripts')
<script type="text/javascript">

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
        var url = "/admin/revenue/PickUp/update";
        $.ajax({
          type: "POST",
          method : "POST",
          url: url,
          data: {_token: "{{ csrf_token() }}",id: id, val: value,type:type,date:obj.data('time')},
          success: function (response)
          {
            if (response == 'OK') {
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

<style>
  #summaryLeft,
  #summaryRigth{
   cursor: pointer;
  }
    table.tableMonths {
    margin: 1em 0;
    border: 1px solid;
}
table.tableMonths tr td {
    padding: 7px !important;
    font-weight: bold;
    cursor: pointer;
}
  table.tableMonths tr td:hover,
  table.tableMonths tr td.active {
  background-color: #6d5cae;
    color: #FFF;
  }
@media only screen and (max-width: 767px) {
  .summary{
    padding: 7px 0;
  }
  .filter-field {
    width: 44%;
    overflow: hidden;
  }
  .filter-field .form-control{
    width: 98% !important;
  }
  
  .table-responsive.summary .table{
    overflow: hidden;
  }
  
  th.first-col, td.first-col {
    padding-left: 5em !important;
    width: 0;
  }
  th.static{
    padding: 0px !important;
    border: none !important;
    height: 28px;
    margin: 0;
    width: 5em;
  }
  td.static{
    background-color: #FFF;
    width: 5em;
  }
}
</style>
@endsection
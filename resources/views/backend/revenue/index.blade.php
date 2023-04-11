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
      $('.daterange02').remove()
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
      $('#month').remove();
      sendFormRevenue();
    });
    $('#site').on('change',function (event) {
      sendFormRevenue();
    });
    $('.tabChannels').on('click',function (event) {
      
      $('#ch_sel').val($(this).data('k'));
       sendFormRevenue();
    });
    $('.month_select').on('click',function (event) {
      
      $('#month').val($(this).data('month'));
      $('#start').remove();
      $('#finish').remove();
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
          Revenue DASHBOARD
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

<div class=" contenedor c-dashboard">
  <div class="row">
     <div class="col-md-3">
       @include('backend.revenue.dashboard._filters')
       <div class="clearfix text-center">
         <h3>
           <?php 
              if (date('m', strtotime($start)) == date('m', strtotime($finish))){
                echo getMonthsSpanish(date('m', strtotime($finish)),false);
              } else {
                echo convertDateToShow_text($start).' - '.convertDateToShow_text($finish);
              }
           ?>
         </h3>
       @include('backend.revenue.dashboard._summary')
       </div>
       <div class="clearfix text-center">
         <h3>Anual</h3>
       @include('backend.revenue.dashboard._summary-annual')
       </div>
    </div>
    <div class=" col-md-7">
       @include('backend.revenue.pick-up._tableItems');
    </div>
    <div class=" col-md-2">
      <div class="text-right">
         @include('backend.revenue.pick-up._actions')
      </div>
      <div class="clearfix text-right">
      
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<style>
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
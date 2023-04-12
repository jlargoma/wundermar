@extends('layouts.admin-master')

@section('title') Precios de apartamentos @endsection

@section('externalScripts') 
<link href='/vendors/fullcalendar-4.3.1/packages/core/main.min.css' rel='stylesheet' />
<link href='/vendors/fullcalendar-4.3.1/packages/daygrid/main.min.css' rel='stylesheet' />
<script src='/vendors/fullcalendar-4.3.1/packages/core/locales/es.js'></script>
<script src='/vendors/fullcalendar-4.3.1/packages/core/main.min.js'></script>
<script src='/vendors/fullcalendar-4.3.1/packages/interaction/main.min.js'></script>
<script src='/vendors/fullcalendar-4.3.1/packages/daygrid/main.min.js'></script>
<!--<script src='/vendors/fullcalendar-4.3.1/packages/daygrid/resourceDayGrid.min.js'></script>-->

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="{{ assetV('/css/backend/planning.css')}}" rel="stylesheet" type="text/css" />

<style type="text/css" media="screen"> 
  .daterangepicker{
    z-index: 10000!important;
  }
  .fa fa-close{
    font-size: 45px!important;
    color: white!important;
  }
  .calend_red{
    background-color: rgba(255, 210, 210, 0.3);
  }
  .calend_select{
        background-color: #e8f5f7;
  }
  @media only screen and (max-width: 767px){
    .daterangepicker {
      left: 12%!important;
      top: 3%!important; 
    }
  }

</style>

<!--<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="{{ asset('/css/components/daterangepicker.css')}}" type="text/css" />-->
@endsection

@section('content')
<div class="container-fluid padding-25 sm-padding-10">
  <div class="row">
    <div class="col-xs-12 col-md-3">
      <h3>Listado de Precios:</h3>
    </div>
    <div class="col-xs-12 col-md-9">
      @include('backend.prices._navs')
    </div>
  </div>
  <div class="row">
    <div class="col-md-7">
          
      <div class="row">
        <div class="form-material pt-1 col-xs-12 col-md-6">
          <label class="hidden-mobile">Apartamento</label>
          <select class="form-control" id="room_list">
            @foreach($rooms as $k=>$name)
            <option value="{{$k}}" @if($room == $k) selected @endif>{{$name}}</option>
            @endforeach
          </select>
        </div>     
        <div class="form-material pt-1 col-xs-12 col-md-6">
          
          <table class="table-prices">
            <tr>
             
              <td><span class="price-booking">{{$price_booking}}</span></td>
              <td><span class="price-airbnb">{{$price_airbnb}}</span></td>
              <td><span class="price-expedia">{{$price_expedia}}</span></td>
              <td><span class="price-google">{{$price_google}}</span></td>
              <td><span class="disp-layout">Disponib</span></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="calendar-blok">
      <div id='calendar'></div>   
      </div>
    </div>
    <div class="col-md-5">
      <p class="mobile-tit">Editar Precios:</p>
      <div class="row">
        <form method="POST" action="{{route('channel.price.cal.upd',$room)}}" id="channelForm">
          <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
          <div class="pt-1 col-md-12">
            <div class="row">
              <div class="col-md-3 col-xs-5 pt-1"><label>Rango de Fechas</label></div>
              <div class="col-md-9 col-xs-7">
                <input type="text" class="form-control daterange1" id="date_range" name="date_range" value="">
                <input type="hidden" id="date_start" name="date_start">
                <input type="hidden" id="date_end" name="date_end">
              </div>
            </div>

          </div>
          <div class="pt-1 row">
            <button id="selAllDays" type="button"  class="btn_days">Todos</button>
            <button id="selWorkdays" type="button" class="btn_days">Laborales</button>
            <button id="selHolidays" type="button" class="btn_days">Fin de semana</button>
          </div>
          <div class="pt-1 row">
            @foreach($dw as $k=>$v)
            <div class="weekdays">
              <label>
                <input type="checkbox" name="dw_{{$k}}" id="dw_{{$k}}" checked="checked"/>
                <span> {{$v}}</span>
              </label>
            </div> 
            @endforeach
          </div>
          <div class="pt-1 col-xs-6">
            <label>Precio por día (€)</label>
            <input type="number" class="form-control" name="price" id="price" step="any">
          </div>
          <div class="pt-1 col-xs-6">
            <label>Estancia Mín.</label>
            <input type="number" class="form-control" name="min_estancia" id="min_estancia">
          </div>
          <div class=" pt-1 col-md-12" style="clear:both;">
            <button class="btn btn-primary m-t-20">Guardar</button>
          </div>
        </form>
      </div>
      <div class="row pt-1">
        <p class="alert alert-danger" style="display:none;" id="error"></p>
        <p class="alert alert-success" style="display:none;" id="success"></p>
      </div>

      
      <div class="row content-calendar push-20" style="min-height: 515px;">
        <div class="col-xs-12 text-center sending" style="padding: 120px 15px;">
          <i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br>
          <h2 class="text-center">CARGANDO CALENDARIO</h2>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection

@section('scripts')


<script type="text/javascript">
$(document).ready(function () {





  $(".daterange1").daterangepicker({
    "buttonClasses": "button button-rounded button-mini nomargin",
    "applyClass": "button-color",
    "cancelClass": "button-light",
    autoUpdateInput: true,
//    locale: 'es',
    locale: {
      firstDay: 2,
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
    },

  });

  Date.prototype.ddmmmyyyy = function () {
    var mm = this.getMonth() + 1; // getMonth() is zero-based
    var dd = this.getDate();
    return [
      (dd > 9 ? '' : '0') + dd,
      (mm > 9 ? '' : '0') + mm,
      this.getFullYear()
    ].join('/');
  };
  Date.prototype.yyyymmmdd = function () {
    var mm = this.getMonth() + 1; // getMonth() is zero-based
    var dd = this.getDate();
    return [
      this.getFullYear(),
      (mm > 9 ? '' : '0') + mm,
      (dd > 9 ? '' : '0') + dd
    ].join('-');
  };
  var render_yyyymmmdd = function (dates) {
    var date = dates.trim().split('/');
    return date[2] + '-' + date[1] + '-' + date[0];
  };
  
    
  Date.prototype.addDays = function(days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
  }


  $('.daterange1').change(function (event) {
    var date = $(this).val();

    var arrayDates = date.split(' - ');
    var res1       = arrayDates[0].replace("Abr", "Apr");
    var date1      = new Date(res1);
    var res2       = arrayDates[1].replace("Abr", "Apr");
    var date2      = new Date(res2);
       
    $('#date_start').val(date1.yyyymmmdd());
    $('#date_end').val(date2.yyyymmmdd());
    
    pintar(date1, date2);
    allDayW();
  });



  $('#datepicker_start,#datepicker_end').datepicker();

  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: ['interaction', 'dayGrid', 'resourceDayGridMonth', 'resourceDayGridWeek'],
    selectable: true,
    header: {
      left: '',
      center: 'title',
      right: 'today prev,next'
    },
    textEscape: false,
    eventRender: function (info) {
      info.el.querySelector('.fc-title').innerHTML = info.event.title;
    },
    events: function (info, callback, failureCallback) {
      var start = new Date(info.start);
      var end = new Date(info.end);

      $.get('{{route("channel.price.cal.list",$room)}}',
              {"start": start.toLocaleDateString("es-ES"), "end": end.toLocaleDateString("es-ES")},
              function (data) {
                var el_lst = $(".fc-bg");
                var total = data.redDays.length;
                
                for(var i=0;i<total;i++){
                  el_lst.find("[data-date='" + data.redDays[i] + "']").addClass('calend_red'); 
                }
//                console.log(data,total);
                callback((data.priceLst));
              });
    },
//    events: '{{route("channel.price.cal.list",$room)}}'

  });

  calendar.setOption('locale', 'es');

  calendar.on('select', function (info) {
    
    var start = new Date(info.start);
    var end = new Date(info.end);
    end.setDate(end.getDate() - 1);
    $('#date_range').data('daterangepicker').setStartDate(start);
    $('#date_range').data('daterangepicker').setEndDate(end);
    allDayW();
    
  });



  calendar.render();


  var allDayW = function(){
    $('.btn_days').removeClass('active');
    for(i=0; i<7; i++){
      $('#dw_'+i).prop("checked", true);
    }
  }

  var pintar = function (start, end) {
    
    var el_lst = $(".fc-bg");
    var limit = 30;
//    var oneDay = 24*60*60;
    $('.calend_select').removeClass('calend_select');
    // seconds * minutes * hours * milliseconds = 1 day 
    var day = 60 * 60 * 24 * 1000;
    start = new Date(start.getTime());
    end = new Date(end.getTime());

    while( limit && start <= end){
      el_lst.find("[data-date='" + start.yyyymmmdd() + "']").addClass('calend_select'); 
      start = new Date(start.getTime() + day);
//       console.log(start.getTime(),day,start.yyyymmmdd());
      limit--;
    }
  }

  $('#room_list').on('change', function (e) {
    var rId = $(this).val();
    location.href = '{{route('channel.price.cal')}}/' + rId;
  });

  $('#selAllDays').on('click', function (e) {
    $('.btn_days').removeClass('active');
    $(this).addClass('active');
    allDayW();
  });
  $('#selWorkdays').on('click', function (e) {
     $('.btn_days').removeClass('active');
    $(this).addClass('active');
    allDayW();
    $('#dw_5').prop("checked", false);
    $('#dw_6').prop("checked", false);
  });
   $('#selHolidays').on('click', function (e) {
      $('.btn_days').removeClass('active');
    $(this).addClass('active');
    for(i=0; i<7; i++){
      $('#dw_'+i).prop("checked", false);
    }
    $('#dw_5').prop("checked", true);
    $('#dw_6').prop("checked", true);
  });

  $('#channelForm').on('submit', function (event) {

    event.preventDefault();
    $('#error').text('').hide();
    $('#success').text('').hide();
    var form_data = $(this).serialize();
    var url = $(this).attr('action');
    $('#loadigPage').show('slow');
    $.ajax({
      type: "POST",
      url: url,
      data: form_data, // serializes the form's elements.
      success: function (data)
      {
        if (data.status == 'OK') {
          $('#success').text(data.msg).show();
          let event = calendar.getEventById('event_edit');
          if (event)
            event.remove()
          calendar.refetchEvents();
        } else {
          $('#error').text(data.msg).show();
        }
        $('#loadigPage').hide('slow');
//        console.log(data.msg); // show response from the php script.
      }
    });
  });



//alert(now.toLocaleDateString("es-ES"));


/**************************************************************************************/
  window["URLCalendar"] = '/getCalendarMobile/';
   var cal_move = false;
   var moveCalendar = function(){
      if(cal_move) return;
      cal_move = true;
      $('.btn-fechas-calendar').css({
      'background-color': '#899098',
      'color': '#fff'
      });
      $('#btn-active').css({
        'background-color': '#10cfbd',
        'color': '#fff'
      });
      var target = $('#btn-active').attr('data-month');
      var targetPosition = $('.content-calendar #month-' + target).position();
      $('.content-calendar').animate({scrollLeft: "+=" + targetPosition.left + "px"}, "slow");
   }
    setTimeout(function () { moveCalendar();},200);
//   $('#btn-active').trigger('click');

  $('.content-calendar').on('click','.reloadCalend', function(){
    var time = $(this).attr('data-time');
    cal_move = false;
    $('.content-calendar').empty().load(
            '/getCalendarChannel/{{$room}}/'+time, 
            function(){ moveCalendar();}
            );
  });
  
  $('.content-calendar').empty().load('/getCalendarChannel/{{$room}}',function(){
    var target = $('#btn-active').attr('data-month');
    var targetPosition = $('.content-calendar #month-' + target).position();
    $('.contentCalendar').animate({scrollLeft: "+=" + targetPosition.left + "px"}, "slow");
  });

});
</script>
<style>
  .fc-event, .fc-event-dot{
    background-color: transparent;
    border: none;
    text-align: center;
    font-size: 1.2em;
    color: inherit;
  }
  .fc-event:hover, .fc-event-dot:hover{
    color: red;
  }
  .fc-day-grid-event .fc-time {
    display: none;
  }
  .weekdays:first-child {
    margin-left: 10px;
  }
  .weekdays {
    float: left;

    width: 14%;
  }
  .col-xs-5.pt-1 {
    padding-top: 7px;
  }
  .pt-1.col-md-12.row{
    margin: auto;
  }
  .weekdays label {
    display: block;
    padding-left: 10px;
    text-indent: -17px;
    color:#a7a7a7;
  }

  .weekdays input {
    width: 1px;
    height: 1px;
    padding: 0;
    margin: 0;
    vertical-align: bottom;
    position: relative;
    top: -1px;
  }


  input:checked + span {
    color: #2b5d9b;
  }

  .btn_days {
    padding: 4px 15px;
  }
  .btn_days.active {
    background-color: #2b5d9b;
    color: #fff;
  }

  p.mobile-tit {
    padding: 7px 11px;
    margin: 8px auto 2px;
    background-color: #004a2f;
    color: #fff;
    font-size: 1.4em;
    box-shadow: 1px 3px 2px #00132b;
    font-weight: 600;
  }
  .fc-center h2 {
    font-weight: 800;
  }
  a.fc-day-grid-event.fc-h-event.fc-event.fc-start.fc-end.availibility {
    position: absolute;
    top: 0;
    font-size: 13px;
    color: #fff;
    padding: 1px 3px;
    margin: 3px 50px 9px;
  }
  a.fc-day-grid-event.fc-h-event.fc-event.fc-start.fc-end.availibility.yes {
    background-color: rgba(32, 113, 0, 0.4);
  }
  a.fc-day-grid-event.fc-h-event.fc-event.fc-start.fc-end.availibility.no {
    background-color: rgba(255, 0, 0, 0.40);
  }


  div#calendar {
    min-width: 706px;
  }

  .disp-layout{
    background-color: #9ec4a0;
    padding: 5px !important;
    color: #fff;
    border-radius: 4px;
  }
  span.fc-day-number {
    font-weight: 800;
    color: #000;
  }
  th.fc-day-header.fc-widget-header {
    font-size: 18px;
    color: #272727;
    padding: 5px;
    background-color: #e0e0e0;
    border: 1px solid #000;
  }
  .fc-dayGrid-view .fc-body .fc-row {
     min-height: 5px !important; 
     max-height: 80px;
  }
  table.t-otas {
      top: 0px;
  }
  @media only screen and (max-width: 768px) {
    .calendar-blok{
      width: 100%;
      overflow: scroll;
    }
    .daterangepicker {
      top: auto !important;
    }
  }
.calendar-blok {
    overflow: auto;
}
.row.content-calendar.push-20 {
    width: 95%;
    margin-left: 3%;
}
  @media only screen and (max-width: 425px) {
    .select-site{
      clear: both;
      float: none;
      padding-top: 1em;
    }
    .buttons-lst{
      width: 42em;
    }
    .weekdays {
      width: 13%;
    }
    form label {
      font-size: 11px;
    }
  }
</style>
@endsection
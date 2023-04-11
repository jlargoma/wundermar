@extends('layouts.admin-master')

@section('title') Precios de apartamentos @endsection

@section('externalScripts') 
<link href='/vendors/fullcalendar/core/main.min.css' rel='stylesheet' />
<link href='/vendors/fullcalendar/dayGrid/main.min.css' rel='stylesheet' />

<script src='/vendors/fullcalendar/core/main.min.js'></script>
<script src='/vendors/fullcalendar/dayGrid/main.min.js'></script>
<!--<script src='/vendors/fullcalendar/dayGrid/resourceDayGrid.min.js'></script>-->

<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css" />
@endsection

@section('content')
<div class="container-fluid padding-25 sm-padding-10">
  <div class="row">
    <div class="col-md-10 col-md-offset-1 ">
      <h3>Listado de Disponibilidad:</h3>
      <div class="row">
        <div class="form-material pt-1 col-xs-10 col-md-6">
          <label>Apartamento</label>
          <select class="form-control" id="room_list">
            @foreach($aptos as $i=>$n)
            <option value="{{$i}}" @if($apto == $i) selected @endif>{{$n}}</option>
            @endforeach
          </select>
        </div>   
        <div class="col-xs-2 col-md-2" style="padding-top:2.7em;">
          <a href="" class="btn btn-danger" 
            onclick="return confirm('Confirmar que desea enviar al Channel Manager la disponibilidad actual de los Aptos asociados a la OTA seleccionada?');">
            Actualizar Disponbilidad
          </a>
        </div>
      </div>
      <div class="">
        <div class="row pt-1">
          @foreach($roomsName as $i=>$n)
          <div class="tag-room" >
            @if(isset($colors[$i]))
            <span style="background-color:{{$colors[$i]}}">&nbsp;</span>
            @else
            <span >&nbsp;</span>
            @endif
            {{$n}}
          </div>
          @endforeach
        </div>
        <div id='calendar' class=" pt-1"></div>   
      </div>  
    </div>

  </div>
</div>


@endsection

@section('scripts')


<script type="text/javascript">
$(document).ready(function () {

  $('#datepicker_start,#datepicker_end').datepicker();

  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: ['dayGrid'],
    header: {
//      left: 'dayGridMonth,timeGridWeek,timeGridDay',
      left: 'title',
      center: '',
      right: 'today,prevYear,prev,next,nextYear'
    },
    selectable: true,
    events: '{{route("channel.cal.list",$apto)}}'
  });

  calendar.setOption('locale', 'es');

  calendar.on('select', function (info) {
    var start = new Date(info.start);
    var end = new Date(info.end);
    end.setDate(end.getDate() - 1);
    $('#date_start').datepicker('setDate', start);
    $('#date_end').datepicker('setDate', end);
//    $('#date_start').val(start.toLocaleDateString("es-ES"));
  });



  calendar.render();
  
  var pintar = function(start,end){
    let event = calendar.getEventById( 'event_edit' );
    if (event){
      if (start)  event.setStart(start);
      event.setEnd(end);
    } else {
      calendar.addEventSource( 
          {
            events: [
              {
                id : 'event_edit',
                title: 'EDITANDO',
                start: start,
                end: end
              },
            ],
            color: 'yellow',   // an option!
            textColor: 'black' // an option!
          }
        );
    }
  
  }
  $('#datepicker_start').on('changeDate', function (e) {
     var start = new Date(e.date);
     var end = new Date(e.date);
     end.setDate(start.getDate() + 1);
     pintar(start,end);
  });
  $('#datepicker_end').on('changeDate', function (e) {
     var end = new Date(e.date);
     end.setDate(end.getDate() + 1);
     pintar(null,end);
  });
  
  
  $('#room_list').on('change', function (e) {
     var rId = $(this).val();
     location.href = '{{route('channel.price.cal')}}/'+rId;
  });
  
  
  $('#upd_avail').on('click', function (e) {
     e.preventDefault();
     location.href = '{{route('channel.price.cal')}}/'+rId;
  });
  
  function upd_avail(){
    return 
  }
  
  
  
//alert(now.toLocaleDateString("es-ES"));
});
</script>
<style>
  .fc-event, .fc-event-dot{
    color: #000;
    padding: 5px 1em;
    margin: 5px;
    font-weight: 600;
  }
  .fc-event:hover, .fc-event-dot:hover{
    color: inherit;
  }
  .tag-room{
    float: left;
    min-width: 10em;
  }
  .tag-room span {
      display: block;
      width: 17px;
      float: left;
      border-radius: 50%;
      margin-right: 7px;
  }
  .fc-ltr .fc-dayGrid-view .fc-day-top .fc-day-number {
    float: right;
    font-weight: 800;
  }
</style>
@endsection
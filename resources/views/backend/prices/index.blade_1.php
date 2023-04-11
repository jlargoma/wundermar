@extends('layouts.admin-master')

@section('title') Channel Manager @endsection

@section('externalScripts') 
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

@endsection

@section('content')
<div class="container-fluid padding-25 sm-padding-10">
  @if($errors->any())
  <p class="alert alert-danger">{{$errors->first()}}</p>
  @endif
  @if (\Session::has('success'))
  <p class="alert alert-success">{!! \Session::get('success') !!}</p>
  @endif
  <div class="row">
    <div class="col-md-10 col-md-offset-1 ">
      
      <h3>Listado de Apartamentos:</h3>
      <div class="row">
        <div class="form-material pt-1 col-xs-12">

          @foreach($aptos as $k=>$item)
          <div class="block-otas">
            <h2 class="mobile-tit">{{$item->name}}</h2>
            <div class="row">
              <div class="col-md-6">
                <ol>
                  @foreach($item->rooms as $room)
                  <li>
                    {{$room->name}}
                    @if(isset($channels[$room->channel]))
                    (<b>{{$channels[$room->channel]}}</b> - ID {{$room->propID}}/{{$room->roomID}})
                    @endif
                  </li>
                  @endforeach
                </ol>
              </div>
              <div class="col-md-6">
                <h3>Enviar disponibilidad:</h3>
                <form method="POST" action="{{route('channel.sendAvail',$k)}}" id="channelForm">
                  <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
                  <div class="row">
                    <div class="col-md-3 col-xs-5 pt-1"><label>Rango de Fechas</label></div>
                    <div class="col-md-6 col-xs-7">
                      <input type="text" class="form-control daterange1" id="date_range" name="date_range" value="">
                    </div>
                    <div class="col-md-3 col-xs-7">
                      <button class="btn btn-primary" 
                              onclick="return confirm('Confirmar que desea enviar al Channel Manager la disponibilidad actual de los Aptos asociados a la OTA seleccionada?');">
                        Enviar
                      </button>
                    </div>
                  </div>
                </form>

              </div>
            </div>
          </div>
          @endforeach
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
      locale: {
        format: 'DD/MM/YYYY',
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
        "firstDay": 1,
      },

    });
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
  h2.mobile-tit {
    padding: 7px 11px;
    margin: 8px auto 19px;
    background-color: rgba(72, 134, 210, 0.3);
    font-size: 1.4em;
    box-shadow: 1px 3px 2px #00132b;
    font-weight: 600;
  }
  .block-otas {
    margin-bottom: 2em;
    padding-bottom: 2em;
    box-shadow: 2px 2px 1px #c1c1c1;
  }
</style>
@endsection
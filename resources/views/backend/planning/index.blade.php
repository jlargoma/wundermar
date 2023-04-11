<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$uRole = getUsrRole();
$is_mobile = $mobile;
?>
@extends('layouts.admin-master')

@section('title') Administrador de reservas @endsection

@section('externalScripts')

<link href="/assets/css/font-icons.css" rel="stylesheet" type="text/css" />
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="{{ asset('/css/components/daterangepicker.css')}}" type="text/css" />
<link href="{{ assetV('/css/backend/planning.css')}}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{{ assetV('/js/backend/partee.js')}}"></script>
<script type="text/javascript" src="{{ assetV('/js/backend/buzon.js')}}"></script>
<style>
      div#contentEmailing {
          overflow: auto !important;
          max-height: 88vh !important;
      }
      #modalLastBooks .btn.active{
        background-color: #1e416c;
        color: #FFF;
      }
      #modalLastBooks tr.cancel,
      #modalLastBooks tr.cancel a{
          color: red;
      }
      input#minDay.danger {
        border: 1px solid red;
        box-shadow: 1px 1px 4px 1px red;
      }
      .schedule{
        padding: 3px 26px;
        background-color: #FFF;
        width: 600px;
      }
      .schedule h2{
        text-align: center;
        background-color: #4c417a;
        color: #FFF;
        margin-bottom: -0.7em;
      }
      .schedule h5{
        margin-bottom: -0.7em;
      }
      iframe#contentEmailing {
          width: 100%;
          min-height: 88vh;
          border: none;
          margin-top: -22px;
      }
    </style>
@endsection

@section('content')
@if ($errors->any())
<div class="alert alert-danger">
  {{ implode('', $errors->all(':message')) }}
</div>
@endif
<?php if (!$is_mobile): ?>
  <div class="container-fluid  p-l-15 p-r-15 p-t-20 bg-white">
    @include('backend.years.selector', ['minimal' => false])
    @include('backend.planning.blocks._buttons_top',[
    'alarms'=>$lowProfits,
    'lastBooksPayment'=>$lastBooksPayment,
    'alert_lowProfits'=>$alert_lowProfits,
    'parteeToActive'=>$parteeToActive,
    'ff_pendientes'=>$ff_pendientes
    ])

  </div>
  <div class="row">
  <div class="col-md-7" >
    <div class="btn-tabs">
      @include('backend.planning.blocks.buttons_table_tabs')
    </div>
    <div class="my-5px">
      <input id="nameCustomer" type="text" name="searchName" class="searchabled form-control" placeholder="Buscar por nombre del cliente" />
    </div>
    <div class="col-xs-12" id="resultSearchBook" style="display: none; padding-left: 0;"></div>
    <div class="col-xs-12 content-tables" style="padding-left: 0;"></div>
  </div>
  <div class="col-md-5 py-1em" style="overflow: auto;">
    <!-- www.tutiempo.net - Ancho:446px - Alto:89px -->
    <div id="TT_FyTwLBdBd1arY8FUjfzjDjjjD6lUMWzFrd1dEZi5KkjI3535G"> </div>
    <div class="row content-calendar push-20" style="min-height: 515px;">
      <div class="col-xs-12 text-center sending" style="padding: 120px 15px;">
        <i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br>
        <h2 class="text-center">CARGANDO CALENDARIO</h2>
      </div>
    </div>
  </div>
</div>
  <!-- NUEVAS RESERVAS -->
  <div class="modal fade slide-up in" id="modalNewBook" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content-wrapper">
        <div class="modal-content contentNewBook">

        </div>
      </div>
    </div>
  </div>


  <!-- ÚLTIMAS RESERVAS -->
  <div class="modal fade slide-up in" id="modalLastBooks" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
      <div class="modal-content-wrapper">
        <div class="modal-content">

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade slide-up in" id="modalLowProfits" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
      <div class="modal-content-wrapper">
        <div class="modal-content">
          @include('backend.planning._alarmsLowProfits', ['alarms' => $lowProfits])
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade slide-up in" id="modalParteeToActive" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
      <div class="modal-content-wrapper">
        <div class="modal-content" id="_alarmsPartee">

        </div>
      </div>
    </div>
  </div>
  <!-- ALERTAS DE BOOKING -->
  <div class="modal fade slide-up in" id="modalAlertsBooking" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg" style="float: left; margin-left: 5%;">
      <div class="modal-content-wrapper">
        <div class="modal-content">

        </div>
      </div>
    </div>
  </div>

  <!-- IMAGENES POR PISO -->
  <div class="modal fade slide-up in" id="modalRoomImages" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg" style="width: 85%;">
      <div class="modal-content-wrapper">

        <div class="modal-content" style="max-height: 800px; overflow-y: auto;">

        </div>
      </div>
    </div>
  </div>

  <!-- CALENDARIO DE BOOKING -->
  <div class="modal fade slide-up in" id="modalCalendarBooking" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg" style="float: left; margin-left: 5%;">
      <div class="modal-content-wrapper">

        <div class="modal-content">

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade slide-up in" id="modalCuposVtn" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
      <div class="modal-content-wrapper">
        <div class="modal-content" id="content-cupos">
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal fade slide-up in" id="modalTareasPRogramadas" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog">
      <div class="modal-content-wrapper">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
            <i class="pg-close fs-20" style="color: #000!important;"></i>
        </button>
        <div class="schedule">
          
          <h1>Tareas Programadas</h1>
          {!!$schedule!!}
        </div>
      </div>
    </div>
  </div>
<?php else: ?>
  <script type="text/javascript">
  $(document).ready(function () {
    $('.calend').click(function (event) {
      $('html, body').animate({
        scrollTop: $(".calendar-mobile").offset().top
      }, 2000);
    });
    $('.money-stripe ').click(function (event) {
      $('html, body').animate({
        scrollTop: $(".stripe-mobile").offset().top
      }, 2000);
    });
  });
  </script>
  <div class="container-fluid  p-l-15 p-r-15 p-t-20 bg-white">
    <div class="row">
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-xs-5 title-year-selector">
            <h2>Planning</h2>
          </div>
          <div class="col-md-12 col-xs-7">
            @include('backend.years._selector')
          </div>
        </div>
      </div>
      @include('backend.planning.blocks._buttons_top',[
      'alarms'=>$lowProfits,
      'lastBooksPayment'=>$lastBooksPayment,
      'alert_lowProfits'=>$alert_lowProfits,
      'parteeToActive'=>$parteeToActive,
      ])
    </div>
    <div class="row push-20">
      <div class="col-md-7">
        <div class="row push-10">
          <div class="col-md-5 col-xs-12">

          </div>
        </div>
        <div class="row text-left push-0" style="overflow-x:auto;">
          <div class="btn-tabs">
            @include('backend.planning.blocks.buttons_table_tabs')
          </div>
        </div>

      </div>
      <div class="row">
        <div class="col-xs-5">
          <input id="nameCustomer" type="text" name="searchName" class="searchabled form-control" placeholder="Buscar por nombre del cliente" />
        </div>
        <div class="col-xs-7">
          <button class="btn btn-blue btn_intercambio btn-cons minimal" >
            <span class="bold">intercambio</span>
          </button>
          <?php if (getUsrRole() == "admin"): ?>
          <a class="btn btn-primary minimal" href="/admin/sales" style="padding: 4px 1em !important;">
                <span class="bold">Informes</span>
              </a>
            <?php endif ?>
        </div>
      </div>
      <div class="row" id="resultSearchBook" style="display: none;"></div>
      <div class="row content-tables" >

      </div>
      <div class="col-md-5" style="overflow: auto;">
        <div class="row content-calendar calendar-mobile" style="min-height: 485px;">
          <div class="col-xs-12 text-center sending" style="padding: 120px 15px;">
            <i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br>
            <h2 class="text-center">CARGANDO CALENDARIO</h2>
          </div>
        </div>

        <div class="col-xs-12">
          <!-- www.tutiempo.net - Ancho:446px - Alto:89px -->
          <div id="TT_FyTwLBdBd1arY8FUjfzjDjjjD6lUMWzFrd1dEZi5KkjI3535G"></div>
        </div>
      </div>
    </div>

    <!-- NUEVAS RESERVAS -->
    <div class="modal fade slide-up in" id="modalNewBook" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content-wrapper">
          <div class="modal-content contentNewBook">

          </div>
        </div>
      </div>
    </div>


    <!-- ÚLTIMAS RESERVAS -->
    <div class="modal fade slide-up in" id="modalLastBooks" tabindex="-1" role="dialog" aria-hidden="true" >
      <div class="modal-dialog modal-lg" >
        <div class="modal-content-wrapper">
          <div class="modal-content" style="width: 90%;">

          </div>
        </div>
      </div>
    </div>

    <!-- ALERTAS DE BOOKING -->
    <div class="modal fade slide-up in" id="modalAlertsBooking" tabindex="-1" role="dialog" aria-hidden="true" >
      <div class="modal-dialog modal-lg" style="margin: 0;">
        <div class="modal-content-wrapper">
          <div class="modal-content">

          </div>
        </div>
      </div>
    </div>

    <div class="modal fade slide-up in" id="modalLowProfits" tabindex="-1" role="dialog" aria-hidden="true" >
      <div class="modal-dialog modal-xs">
        <div class="modal-content-wrapper">
          <div class="modal-content">
            @include('backend.planning._alarmsLowProfits', ['alarms' => $lowProfits])
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade slide-up in" id="modalParteeToActive" tabindex="-1" role="dialog" aria-hidden="true" >
      <div class="modal-dialog modal-xs">
        <div class="modal-content-wrapper">
          <div class="modal-content" id="_alarmsPartee">
          </div>
        </div>
      </div>
    </div>
    <!-- IMAGENES POR PISO -->
    <div class="modal fade slide-up in" id="modalRoomImages" tabindex="-1" role="dialog" aria-hidden="true" >
      <div class="modal-dialog modal-lg" style="width: 95%;">
        <div class="modal-content-wrapper">

          <div class="modal-content" style="max-height: 800px; overflow-y: auto;">

          </div>
        </div>
      </div>
    </div>

    <!-- CALENDARIO DE BOOKING -->
    <div class="modal fade slide-up in" id="modalCalendarBooking" tabindex="-1" role="dialog" aria-hidden="true" >
      <div class="modal-dialog modal-lg" style="float: left; margin-left: 5%;">
        <div class="modal-content-wrapper">

          <div class="modal-content">

          </div>
        </div>
      </div>
    </div>


<?php endif ?>
        
@if($alarmsCheckPaxs)
<div class="modal fade slide-up in" id="modalPAXs" tabindex="-1" role="dialog" aria-hidden="true" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        @include('backend.planning._alarmsPAXs', ['alarms' => $alarmsCheckPaxs])
      </div>
    </div>
  </div>
</div>
@endif
<?php if ($uRole != "agente"): ?>
    <!-- GENERADOR DE LINKS PAYLAND  -->
    <div class="modal fade slide-up in" id="modalLinkStrip" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-xd">
        <div class="modal-content-classic">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
            <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
          </button>
          @include('backend.stripe.link')
        </div>
      </div>
    </div>
<?php endif ?>

    
  @include('backend.planning.modals')
    
@if($otasDisconect)
  @include('backend.planning._alarmsDisconect', ['alarms' => $otasDisconect])
@endif

  @endsection

  @section('scripts')

  <script type="text/javascript" src="{{asset('/js/components/moment.js')}}"></script>
  <script type="text/javascript" src="{{asset('/js/components/daterangepicker.js')}}"></script>

  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>
  <script src="/assets/js/notifications.js" type="text/javascript"></script>
  
  <script type="text/javascript">
    window["csrf_token"] = "{{ csrf_token() }}";
    window["uRole"] = "{{ $uRole }}";
    window["update"] = 0;
    window["usr_email"] = "{{Auth::user()->email}}";
    window["URLCalendar"] = '/getCalendarMobile/';
   </script>
  <script src="{{assetV('/js/backend/planning.js')}}" type="text/javascript"></script>
  <script src="{{assetV('/js/backend/booking_script.js')}}" type="text/javascript"></script>
  <?php if (Auth::user()->defaultTable != ''): ?>
  <script type="text/javascript">
    $(document).ready(function() {
      var type = '<?php echo Auth::user()->defaultTable ?>';
      $('button[data-type="'+type+'"]').trigger('click');
    });
  </script>
  <?php endif ?>

  <script>
    /**************************************************************/
      /****   SEND VISA           **********************************/
$(document).ready(function () {
  var sendVisa = true;
  $('body').on('change','.cc_upd',function(event) {
    if (sendVisa){
      sendVisa = false;
      var that = $(this);
      var bID  = that.data('book_id');
      var idCustomer = that.data('customer_id');
      $('#loadigPage').show('slow');

      var params = {
        data: $('#visa_'+bID).val(),
        _token: "{{ csrf_token() }}",
        bID: bID
      };

      $.post("{{route('booking.save_creditCard')}}",params, function (data) {
        window.show_notif(data.title,data.status,data.response);
        $('#loadigPage').hide('slow');
        sendVisa = true;
      });
    }
  });   
  
    $('body').on('click', '.toggleDeliver', function (event) {
    var obj = $(this);
    //invierto la var delivered
    var delivered = (obj.data('delivered') == 1) ? 0 : 1;
    var data = {
          _token: "{{ csrf_token() }}",
          bID : obj.data('id'),
          delivered: delivered
         }
         
      $.post('/admin/limpieza/extr-deliver', data, function (data) {
        if (data == 'OK'){
          window.show_notif('OK','success','Registro Actualizado.');
          obj.data('delivered',delivered);
          if(delivered){
              obj.find('i').removeClass('fa-bell').addClass('fa-bell-slash');
          } else {
              obj.find('i').removeClass('fa-bell-slash').addClass('fa-bell');
          }
        } else {
          window.show_notif('ERROR','danger','Registro no encontrado');
        }
      });   
  });

var tableAlertExtr = true;
$('.btnSuplementos').on('click', function(){
  if (tableAlertExtr){
       setTimeout(function(){
        var dataTable = $('.tableAlertExtr').DataTable({
          "columnDefs": [
            {"targets": [1,5,6,7,8], "orderable": false }
          ],
          order: [[ 3, "asc" ]],
           @if($is_mobile)
            scrollX: true,
            scrollY: false,
            scrollCollapse: true,
            fixedColumns:   {
              leftColumns: 1
            },
            @endif
        });
        },350);
     tableAlertExtr = false;
   }
     });
     
          
    $('#goOtasPrices').on('click', function(){
          window.location.href = "/admin/channel-manager/controlOta";
      })
     
           
    // LOGs OTAs
      $('#btnOTAsLogs').click(function(event) {
        $('#modalBasic_title').text('Logs Errores Api OTAs');
        $('#modalBasic_content').empty().load('/admin/reservas/api/getOTAsLogs');
        $('#modalBasic').modal('show');
      });

});   
</script>
  @include('backend.planning.calendar.scripts');

  @endsection

<div class="row btn-mb-1">
  <button class="btn btn-success btn-cons btn-newBook" type="button" data-toggle="modal" data-target="#modalNewBook">
    <i class="fa fa-plus-square" aria-hidden="true"></i> <span class="bold hidden-mobile" >Nueva Reserva</span>
  </button>
  <?php if (getUsrRole() != "agente"): ?>
    <button id="lastBooks" class="btn btn-success btn-cons" type="button" data-toggle="modal" data-target="#modalLastBooks">
      <span class="bold hidden-mobile">Últimas Confirmadas</span>
      <span class="bold show-mobile">Últi Conf.</span>
      <?php if ($lastBooksPayment > 0): ?>
        <span class="numPaymentLastBooks"><?php echo $lastBooksPayment; ?></span>
      <?php endif; ?>
    </button>
    <button class="btn btn-success btn-cons" type="button" data-toggle="modal" data-target="#modalLinkStrip">
      <i class="fa fa-money" aria-hidden="true"></i> <span class="bold hidden-mobile">Cobros TPV</span>
    </button>
  <?php endif ?>
  <button class="btn btn-success btn-calcuteBook btn-cons" type="button">
    <span class="bold hidden-mobile"><i class="fa fa-calendar-alt" aria-hidden="true"></i>&nbsp;Calcular reserva</span>
    <span class="bold show-mobile">$</span>
  </button>
  <?php if (getUsrRole() != "agente"): ?>
    @if ($alarmsPayment > 0)
    <button id="lastBooksPendientes"  class="btn btn-danger btn-cons btn-blink btn-alarms" type="button">
    <i class="fa fa-bell" aria-hidden="true"></i> <span class="bold">COBROS PDTES</span>
    <span class="numPaymentLastBooks"><?php echo $alarmsPayment; ?></span>
    </button>
    @endif
    <button class="btn btn-danger btn-cons btn-blink <?php if ($alert_lowProfits) echo 'btn-alarms'; ?> "  id="btnLowProfits" type="button" data-toggle="modal" data-target="#modalLowProfits">
      <i class="fa fa-bell" aria-hidden="true"></i> 
      <span class="bold hidden-mobile">BAJO BENEFICIO</span>
      <span class="numPaymentLastBooks" data-val="{{$alert_lowProfits}}">{{$alert_lowProfits}}</span>
    </button>
    <button class="btn btn-danger btn-cons btn-blink <?php if ($parteeToActive > 0) echo 'btn-alarms'; ?> "  id="btnParteeToActive" test-target="#modalParteeToActive">
      <i class="fa fa-file-powerpoint" aria-hidden="true"></i> <span class="bold hidden-mobile">PARTEE</span>
      <span class="numPaymentLastBooks"><?php echo $parteeToActive; ?></span>
    </button>
    <button class="btn btn-success btn-tables hidden-mobile" style="background-color: #96ef99; color: black;padding: 7px 18px;width: auto !important;border: none;" type="button" data-type="confirmadas">
      <span >RVA({{$totalReserv}}) <?php echo number_format($amountReserv, 0, ',', '.') ?>€</span>
    </button>

    @if(is_array($overbooking) && count($overbooking)>0)
    <button class="btn btn-success btn-tables btn-OverBooking"  type="button" data-type="overbooking">
      <span >OverBooking({{count($overbooking)}})</span>
    </button>
    @endif

    <button class="btn btn-danger btn-cons btn-blink"  id="btnBookSafetyBox" >
      <i class="fa fa-lock" aria-hidden="true"></i>
      <span class="bold hidden-mobile">CAJAS DE SEGURIDAD</span>
    </button>
    
    <button class="btn btn-danger btn-cons btn-blink"  id="btnBookBlockAll" >
      <i class="fa fa-key" aria-hidden="true"></i>
      <span class="bold hidden-mobile">Bloqueo</span>
    </button>


  <?php endif ?>

  <button class="btn btn-primary btn-sm calend show-mobile cargar_calend" type="button" >
    <span class="bold"><i class="fa fa-calendar"></i></span>
  </button>
  @if(!$is_mobile)
  <?php if (getUsrRole() == "admin"): ?>
    <button class="btn btn-primary btn-cupos btn-cons" type="button" data-toggle="modal" data-target="#modalTareasPRogramadas">
      <span class="bold">Tareas Programadas</span>
    </button>
  <?php endif ?>
  <button class="btn btn-blue btn_intercambio btn-cons" >
    <span class="bold">Intercambio</span>
  </button>
  @endif
  <?php if (getUsrRole() == "admin"): ?>
    <button class="btn btn-success btn-orange @if($bookings_without_Cvc>0) btn-alarms @endif" id="btnBookingsWithoutCvc">
      <span class="bold hidden-mobile">SIN VISA</span>
      <span class="bold show-mobile">S/VISA</span>
      @if($bookings_without_Cvc>0)
      <span class="numPaymentLastBooks" data-val="{{$bookings_without_Cvc}}">{{$bookings_without_Cvc}}</span>
      @endif
    </button>
  <?php endif ?>
 @if(is_array($urgentes) && count($urgentes)>0)
  <div class="box-alerts-popup">
    <div class="content-alerts">
      <h2>Alertas Urgentes</h2>
      <button type="button" class="close" id="closeUrgente" >
        <i class="fa fa-times fa-2x" ></i>
      </button>
      @foreach($urgentes as $item)
      <div class="items">
        @if(isset($item['onlyText']))
          <?php echo $item['onlyText']; ?>
        @else
          <button {!! $item['action'] !!}><i class="fa fa-bell" aria-hidden="true"></i> </button>
          {{$item['text']}}
        @endif
      </div>
      @endforeach 
    </div>
  </div>
 @endif
  <button class="btn btn-danger btn-cons btnSuplementos <?php if ($toDeliver>0) echo 'btn-alarms'; ?> "  type="button" data-toggle="modal" data-target="#modalNextsExtrs">
      <i class="fa fa-bell" aria-hidden="true"></i> 
      <span class="bold hidden-mobile">Extras</span>
      @if ($toDeliver>0)
      <span class="numPaymentLastBooks" data-val="{{$toDeliver}}">{{$toDeliver}}</span>
      @endif
    </button>
    <a class="btn btn-primary" href="/admin/revenue/DASHBOARD">
      <span class="bold">DASHB<span class="bold hidden-mobile">OARD</span></span>
    </a>
   <button class="btn btn-success btn-orange <?php if ($ota_errLogs > 0) echo 'btn-alarms'; ?>" id="btnOTAsLogs">
    <span class="bold"><i class="fa fa-exclamation-triangle show-mobile" style="font-size: 12px;"></i> OTAs</span><span class="bold hidden-mobile"> Errors</span>
    @if($ota_errLogs>0)
    <span class="numPaymentLastBooks" data-val="{{$ota_errLogs}}">{{$ota_errLogs}}</span>
    @endif
  </button>
 
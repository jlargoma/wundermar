<?php
use \Carbon\Carbon;
use App\Classes\Mobile;
$mobile = new Mobile();
$uRole = getUsrRole();
?>
@extends('layouts.admin-master')

@section('title') Administrador de reservas @endsection

@section('externalScripts')
<link href="/assets/css/font-icons.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="{{ assetV('/js/backend/partee.js')}}"></script>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css"
      media="screen">

<link rel="stylesheet" href="{{ asset('/css/components/daterangepicker.css')}}" type="text/css"/>
<script src="//js.stripe.com/v3/"></script>
<style>
  .pgn-wrapper[data-position$='-right'] {
    right: 82% !important;
  }

  input[type=number]::-webkit-outer-spin-button,
  input[type=number]::-webkit-inner-spin-button {

    -webkit-appearance: none;

    margin: 0;

  }

  input[type=number] {

    -moz-appearance: textfield;

  }

  #overlay {
    position: absolute;
    left: 0;

    opacity: .1;
    background-color: blue;
    height: 35px;
    width: 100%;
  }

  .StripeElement {
    background-color: white;
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid transparent;
    box-shadow: 0 1px 3px 0 #e6ebf1;
    -webkit-transition: box-shadow 150ms ease;
    transition: box-shadow 150ms ease;
  }

  .StripeElement--focus {
    box-shadow: 0 1px 3px 0 #cfd7df;
  }

  .StripeElement--invalid {
    border-color: #fa755a;
  }

  .StripeElement--webkit-autofill {
    background-color: #fefde5 !important;
  }

  .stripe-price {
    background-color: white !important;
    padding: 8px 12px !important;
    border-radius: 4px !important;
    border: 1px solid transparent !important;
    box-shadow: 0 1px 3px 0 #e6ebf1 !important;
    -webkit-transition: box-shadow 150ms ease !important;
    transition: box-shadow 150ms ease !important;
  }
  table.table.table-hover.demo-table-search tr td{
    padding: 7px !important;
  }
</style>
@endsection

@section('content')
<?php

use \Carbon\Carbon;
use App\Classes\Mobile;

$mobile = new Mobile();
?>
@if($errors->any())
<p class="alert alert-danger">{{$errors->first()}}</p>
@endif
@if (\Session::has('success'))
<p class="alert alert-success">{!! \Session::get('success') !!}</p>
@endif
<div class="container-fluid padding-10 sm-padding-10">
  <div class="row">
    <div class="col-md-12 col-xs-12 center text-left0">
      <div class="col-md-6">
        <div class="col-md-9 col-xs-12">
          @if( url()->previous() != "" )
          @if( url()->previous() == url()->current() )
          <a href="{{ url('/admin/reservas') }}" class=" m-b-10" style="min-width: 10px!important">
            <img src="{{ asset('/img/iconos/close.png.png') }}" style="width: 20px"/>
          </a>
          @else
          <a href="{{ url()->previous() }}" class=" m-b-10" style="min-width: 10px!important">
            <img src="{{ asset('/img/iconos/close.png.png') }}" style="width: 20px"/>
          </a>
          @endif
          @else
          <a href="{{ url('/admin/reservas') }}" class=" m-b-10"  style="min-width: 10px!important">
            <img src="{{ asset('/img/iconos/close.png.png') }}" style="width: 20px"/>
          </a>
          @endif

          <h4 class="" style="line-height: 1; letter-spacing: -1px">
<?php echo "<b>" . strtoupper($book->customer->name) . "</b>" ?> creada el
<?php $fecha = Carbon::createFromFormat('Y-m-d H:i:s', $book->created_at); ?>
            <br>
            <span class="font-s18"><?php echo $fecha->copy()->formatLocalized('%d %B %Y') . " Hora: " . $fecha->copy()->format('H:m') ?></span>
          </h4>
          <h5>Creado por <?php echo "<b>" . strtoupper($book->user->name) . "</b>" ?></h5>
<?php if ($book->type_book == 2): ?>
            <div class="col-md-2 col-xs-3 icon-lst">
              <a href="{{ url('/admin/pdf/pdf-reserva/'.$book->id) }}">
                <img src="/img/pdf.png"
                     style="width: 50px; float:left; margin: 0 auto;">
              </a>
            </div>
           
<?php endif ?>
          <div class="col-md-2 col-xs-3 icon-lst">
            <a href="tel:<?php echo $book->customer->phone ?>"
               style="width: 50px; float:left;">
              <i class="fa fa-phone  text-success"
                 style="font-size: 48px;"></i>
            </a>
          </div>
          <div class="col-md-2 col-xs-3 icon-lst hidden-lg hidden-md">
            <h2 class="text-center"
                style="font-size: 18px; line-height: 18px; margin: 0;">
<?php $text = "En este link podrás realizar el pago de la señal por el 25% del total." . "\n" . " En el momento en que efectúes el pago, te legará un email confirmando tu reserva - https://www.apartamentosierranevada.net/reservas/stripe/pagos/" . base64_encode($book->id);
?>

              <a href="whatsapp://send?text=<?php echo $text; ?>"
                 data-action="share/whatsapp/share">
                <i class="fa fa-eye fa-2x" aria-hidden="true"></i>
              </a>
            </h2>
          </div>
        </div>
        <div class="col-md-3 col-xs-12 content-guardar" style="padding: 20px 0;">
          <div id="overlay" style="display: none;"></div>
          @if($book->type_book == 0)
          <select class="form-control" disabled style="font-weight: 600;">
            <option style=""><strong></strong>Eliminado</option>
          </select>
          @else
                <?php echo $book->getStatus($book->type_book) ?>
          @endif
          <h5 class="guardar" style="font-weight: bold; display: none; font-size: 15px;"></h5>
        </div>
      </div>
      <div class="col-md-6 col-xs-12" style="max-height: 195px; overflow: auto;">
        @if(Request::has('msg_type'))
        <div class="col-lg-12 col-xs-12 content-alert-error2">
          <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"
                    style="right: 0">×
            </button>
            <h3 class="font-w300 push-15"><?php echo str_replace('_', ' ', $_GET['msg_text']) ?></h3>
          </div>
        </div>
        @endif
        <div class="col-md-12 text-center col-xs-12 push-20">
          <input type="hidden" id="shareEmailImages" value="<?php echo $book->customer->email; ?>">
          <input type="hidden" value="<?php echo $book->id; ?>" id="registerData">
          <div class=" col-md-4 col-md-offset-4 col-xs-12 text-center">
            <button class="btn btn-complete btn-md" id="sendShareImagesEmail">
              <i class="fa fa-eye"></i> Enviar
            </button>
          </div>
        </div>
        <div class=" col-md-8 col-md-offset-2 col-xs-12 text-left">
          <?php $logSendImages = $book->getSendPicture(); ?>
          <?php if ($logSendImages): ?>
            <?php foreach ($logSendImages as $index => $logSendImage): ?>
              <?php
              $roomSended = \App\Rooms::find($logSendImage->room_id);
              $adminSended = \App\User::find($logSendImage->admin_id);
              $dateSended = Carbon::createFromFormat('Y-m-d H:i:s', $logSendImage->created_at)
              ?>
              <div class="col-xs-12 push-5">
                <p class="text-center" style="font-size: 18px; ">
                  <i class="fa fa-eye"></i>
                  Fotos <b><?php echo strtoupper($roomSended->nameRoom) ?></b> enviadas
                  por <b><?php echo strtoupper($adminSended->name) ?></b> el
                  <b><?php echo $dateSended->formatLocalized('%d %B de %Y') ?></b>
                </p>
              </div>
  <?php endforeach; ?>
<?php endif; ?>
        </div>
      </div>

    </div>
  </div>
    <div class="row center text-center">
      <!-- DATOS DE LA RESERVA -->
      <div class="col-md-6 col-xs-12" id="reserva_formBox">
        <div class="overlay loading-div" style="background-color: rgba(255,255,255,0.6); ">
          <div style="position: absolute; top: 50%; left: 35%; width: 40%; z-index: 1011; color: #000;">
            <i class="fa fa-spinner fa-spin fa-5x"></i><br>
            <h3 class="text-center font-w800" style="letter-spacing: -2px;">CALCULANDO...</h3>
          </div>
        </div>
        @if($book->type_book == 1)
        <form role="form" id="updateForm"
              action="{{ url('/admin/reservas/saveUpdate') }}/<?php echo $book->id ?>" method="post">
       @endif
          <textarea id="computed-data" style="display: none"></textarea>
          <input id="bkgID" type="hidden" name="book_id" value="{{ $book->id }}">
          <!-- DATOS DEL CLIENTE -->
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
          <input type="hidden" name="customer_id" value="<?php echo $book->customer->id ?>">
          <div class="col-xs-12 bg-white padding-block push-0" style="padding-bottom:0">
            <div class="col-xs-12 bg-black push-20">
              <h4 class="text-center white">
                DATOS DEL CLIENTE
              </h4>
            </div>

            <div class="col-md-4 push-10">
              <label for="name">Nombre</label>
              <input class="form-control cliente" type="text" name="nombre"
                     value="<?php echo $book->customer->name ?>"
                     data-id="<?php echo $book->customer->id ?>"  <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
            </div>
            <div class="col-md-4 push-10">
              <label for="email">Email</label>
              <input class="form-control cliente" type="email" name="email"
                     value="<?php echo $book->customer->email ?>"
                     data-id="<?php echo $book->customer->id ?>" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
            </div>
            <div class="col-md-4 push-10">
              <label for="phone">Telefono</label>
              <?php if ($book->customer->phone == 0): ?>
    <?php $book->customer->phone = "" ?>
  <?php endif ?>
              <input class="form-control only-numbers cliente" type="text" name="phone"
                     value="<?php echo $book->customer->phone ?>"
                     data-id="<?php echo $book->customer->id ?>" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
            </div>
          </div>
          <div class="col-xs-12 bg-white">
            <div class="col-md-3 col-xs-12 push-10">
              <label for="dni">DNI</label>
              <input class="form-control cliente" type="text" name="dni"
                     value="<?php echo $book->customer->DNI ?>" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
            </div>
            <div class="col-md-3 col-xs-12 push-10">
              <label for="address">DIRECCION</label>
              <input class="form-control cliente" type="text" name="address"
                     value="<?php echo $book->customer->address ?>" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
            </div>
                        <div class="col-xs-3  push-10 row-mobile">
              <label for="country">PAÍS</label>
              <?php $c_country = ($book->customer->country) ? strtolower($book->customer->country):'es'; ?>
              <select class="form-control country minimal" name="country" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
                <option value="">--Seleccione país --</option>
                <?php 
                foreach (\App\Countries::orderBy('code', 'ASC')->get() as $country): 
                  ?>
                <option value="<?php echo $country->code ?>" <?php  if (strtolower($country->code) == $c_country){echo "selected";}?>>
                  <?php echo $country->country ?> 
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          <div class="col-xs-3  push-10 content-cities row-mobile" <?php if($c_country != 'es') echo ' style="display: none;" '; ?>>
              <label for="city">PROVINCIA</label>
              <?php $book_prov = ($book->customer->province) ?  $book->customer->province : 28 ; ?>
              <select class="form-control province minimal" name="province" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
                <option>--Seleccione --</option>
                <?php foreach (\App\Provinces::orderBy('province', 'ASC')->get() as $prov): ?>
                  <option value="<?php echo $prov->code ?>" <?php if ($prov->code == $book_prov) { echo "selected";}?>>
                    {{$prov->province}}
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <!-- DATOS DE LA RESERVA -->
          <div class="col-md-12 col-xs-12 bg-white padding-block" style="padding-bottom:0">
            <div class="col-xs-12 bg-black push-20">
              <h4 class="text-center white">
                DATOS DE LA RESERVA
                <i class="fas fa-sync-alt" id="reset"
                   style="cursor:pointer; position:absolute; right:2rem"></i>
              </h4>
            </div>
            <div class="col-md-4 push-10">
              <label>Entrada</label>
              <div class="input-prepend input-group">
                <?php
                $start1 = Carbon::createFromFormat('Y-m-d', $book->start)->format('d M, y');
                // $start1 = str_replace('Apr','Abr',$start->format('d M, y'));
                $finish1 = Carbon::createFromFormat('Y-m-d', $book->finish)->format('d M, y');
                // $finish1 = str_replace('Apr','Abr',$finish->format('d M, y'));
                ?>

                <input type="text" class="form-control daterange1" id="fechas" name="fechas" required=""
                       style="cursor: pointer; text-align: center; backface-visibility: hidden;min-height: 28px;"
                       value="<?php echo $start1; ?> - <?php echo $finish1 ?>" readonly="" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>

              </div>
            </div>
            <div class="col-md-1 col-xs-3 push-10 p-l-0">
              <label>Noches</label>
              <input type="number" class="form-control nigths" name="nigths" style="width: 100%" disabled
                     value="<?php echo $book->nigths ?>">
              <input type="hidden" class="form-control nigths" name="nigths" style="width: 100%"
                     value="<?php echo $book->nigths ?>">
            </div>
            <div class="col-md-2 col-xs-3">
              <label>Pax</label>
              <select class=" form-control pax minimal" name="pax" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
                <?php for ($i = 1; $i <= 14; $i++): ?>
                    <option value="<?php echo $i ?>" <?php echo ($i == $book->pax) ? "selected" : ""; ?>>
                      <?php echo $i ?>
                    </option>
                <?php endfor; ?>
              </select>

            </div>
            <div class="col-md-3 col-xs-6 push-10">
              <label>Apartamento</label>

              <select class="form-control full-width minimal newroom" name="newroom" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>
                      id="newroom" <?php if (isset($_GET['saveStatus']) && !empty($_GET['saveStatus'])): echo "style='border: 1px solid red'";
  endif ?>>
                <?php foreach ($rooms as $room): ?>
                  <option data-size="<?php echo $room->sizeApto ?>"
                          data-luxury="<?php echo $room->luxury ?>"
                          <?php if ($room->state==0) echo 'disabled'; ?>
                          value="<?php echo $room->id ?>" {{ $room->id == $book->room_id ? 'selected' : '' }} >
    <?php echo substr($room->nameRoom . " - " . $room->name, 0, 15) ?>
                  </option>
  <?php endforeach ?>
              </select>
            </div>
            <div class="col-md-2 col-xs-6 push-20">
              <label>IN</label>
              <select id="schedule" class="form-control minimal" style="width: 100%;" name="schedule" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
                <option>-- Sin asignar --</option>
                          <?php for ($i = 0; $i < 24; $i++): ?>
                  <option value="<?php echo $i ?>" <?php
                        if ($i == $book->schedule) {
                          echo 'selected';
                        }
                        ?>>
                    <?php if ($i < 10): ?>
                      <?php if ($i == 0): ?>
                        --
                      <?php else: ?>
                        0<?php echo $i ?>
                    <?php endif ?>

    <?php else: ?>
      <?php echo $i ?>
    <?php endif ?>
                  </option>
  <?php endfor ?>
              </select>
            </div>
            <div class="col-md-2 col-xs-6 push-20">
              <label>Out</label>
              <select id="scheduleOut" class="form-control minimal" style="width: 100%;"
                      name="scheduleOut" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
                <option>-- Sin asignar --</option>
                          <?php for ($i = 0; $i < 24; $i++): ?>
                  <option value="<?php echo $i ?>" <?php
                    if ($i == $book->scheduleOut) {
                      echo 'selected';
                    }
                    ?>>
                    <?php if ($i < 10): ?>
                      <?php if ($i == 0): ?>
                        --
                    <?php else: ?>
                        0<?php echo $i ?>
      <?php endif ?>

    <?php else: ?>
      <?php echo $i ?>
    <?php endif ?>
                  </option>
  <?php endfor ?>
              </select>
            </div>
          </div>
          <div class="col-xs-12 bg-white">
            <div class="col-md-6 col-xs-12 push-20 not-padding">
              <div class="col-md-6 col-xs-6 push-10">
                <label>Agencia</label>
                <select class="form-control full-width agency minimal" name="agency" >
              @include('backend.blocks._select-agency', ['agencyID'=>$book->agency,'book' => $book])
                </select>
              </div>
              <div class="col-md-6 col-xs-6 push-10">
                <label>Cost Agencia</label>
  <?php if ($book->PVPAgencia == 0.00): ?>
                  <input type="number" step='0.01' class="agencia form-control" name="agencia" value="" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
  <?php else: ?>
                  <input type="number" step='0.01' class="agencia form-control" name="agencia" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>
                         value="<?php echo $book->PVPAgencia ?>">
  <?php endif ?>
              </div>
            </div>
           

            <div class="col-md-12 col-xs-12 push-20 not-padding">
              <div class="col-md-3 col-xs-12 text-center boxtotales" style="background-color: #0c685f;">
                <label class="font-w800 text-white" for="">PVP</label>
                <input type="number" step='0.01' class="form-control total m-t-10 m-b-10 white" disabled
                       value="<?php echo $book->total_price ?>" name="total" >
              </div>
            </div>
            <div class="col-md-12 col-xs-12 push-20 not-padding">
              <p class="personas-antiguo" style="color: red">
  <?php if ($book->pax < $book->room->minOcu): ?>
                  Van menos personas que la ocupacion minima del apartamento.
  <?php endif ?>
              </p>
            </div>
            <div class="col-xs-12 bg-white padding-block">
              <div class="col-md-4 col-xs-12">
                <label>Comentarios Cliente </label>
                <textarea class="form-control" name="comments" rows="5"
                          data-idBook="<?php echo $book->id ?>"
                          data-type="1"><?php echo $book->comment ?></textarea>
              </div>
              <div class="col-md-4 col-xs-12">
                <label>Comentarios Internos</label>
                <textarea class="form-control book_comments" name="book_comments" rows="5"
                          data-idBook="<?php echo $book->id ?>"
                          data-type="2"><?php echo $book->book_comments ?></textarea>
              </div>
            </div>
          </div>
            @if($book->type_book == 1)
            <div class="row push-40 bg-white padding-block">
              <div class="col-md-4 col-md-offset-4 text-center">
                <button class="btn btn-complete font-s24 font-w400 padding-block" type="submit"
                        style="min-height: 50px;width: 100%;" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>Guardar
                </button>
              </div>
            </div>
        </form>
            @endif
      </div>
      <div class="col-md-6 col-xs-12 padding-block">
  <?php if (getUsrRole() != "limpieza"): ?>
          <div class="row">
            <div class="col-xs-12 bg-black push-0">
              <h4 class="text-center white">
                {{ $totalpayment }}€ COBRADO
              </h4>
            </div>
            <table class="table table-hover demo-table-search" style="margin-top: 0;">
              <thead>
                <tr>
                  <th class="text-center bg-success text-white" style="width:25%">fecha</th>
                  <th class="text-center bg-success text-white" style="width:25%">importe</th>
                  <th class="text-center bg-success text-white" style="width:20%">comentario</th>
                </tr>
              </thead>
              <tbody><?php $total = 0; ?>
    <?php if (count($payments) > 0): ?>

      <?php foreach ($payments as $payment): ?>
                    <tr>
                      <td class="text-center">
        <?php
        $fecha = new Carbon($payment->datePayment);
        echo $fecha->format('d-m-Y')
        ?>
                      </td>
                      <td class="text-center">
                        <?php echo $payment->import ?> &nbsp;€
                      </td>
                      <td class="text-center "><?php echo $payment->comment ?></td>
                    </tr>
        <?php $total = $total + $payment->import ?>
      <?php endforeach ?>
    <?php endif ?>
                <tr>
                  <td></td>
                  <?php if ($total < $book->total_price): ?>
                    <td class="text-center"><p
                        style="color:red;font-weight: bold;font-size:15px"><?php echo $total - $book->total_price ?>
                        €</p></td>
                    <td class="text-left" colspan="2"><p style="color:red;font-weight: bold;font-size:15px">
                        Pendiente de pago</p></td>
    <?php elseif ($total > $book->total_price): ?>
                    <td class="text-center"><p
                        style="color:black;font-weight: bold;font-size:15px"><?php echo $total - $book->total_price ?>
                        €</p></td>
                    <td class="text-left" colspan="2">Sobrante</td>
    <?php else: ?>
                    <td class="text-center"><p style="color:black;font-weight: bold;font-size:15px">0€</p></td>
                    <td class="text-left" >Al corriente de pago</td>
    <?php endif ?>

                </tr>
              </tbody>
            </table>
          </div>
          <div class="row">
            @include('Paylands.payment', ['routeToRedirect' => route('payland.thanks.payment',
            ['id' => $book->id]), 'id' => $book->id, 'customer' => $book->customer->id])
          </div>
  <?php endif ?>
      </div>
    </div>

  @endsection

  @section('scripts')

  <script type="text/javascript" src="{{asset('/js/components/moment.js')}}"></script>
  <script type="text/javascript" src="{{asset('/js/components/daterangepicker.js')}}"></script>

  <script src="/assets/js/notifications.js" type="text/javascript"></script>
  @include('backend.planning._bookScripts', ['update' => 1])
  <script>
    calculate(null, false);
  </script>

  <script type="text/javascript">
    $(document).ready(function () {
      $('#sendShareImagesEmail').click(function (event) {
        if (confirm('¿Quieres reenviar las imagenes')) {
          var email = $('#shareEmailImages').val();
          var register = $('#registerData').val();
          var roomId = $('#newroom').val();

          $.get('/admin/sendImagesRoomEmail', {email: email, roomId: roomId, register: register, returned: '1'},
                  function
                          (data) {
                    location.reload();
                  });
        }
      });


      $('.openFF').on('click', function (event) {
        event.preventDefault();
        var id = $(this).data('booking');
        $.post('/admin/forfaits/open', { _token: "{{ csrf_token() }}",id:id }, function(data) {
          console.log(data);
          var formFF = $('#formFF');
          formFF.attr('action', data.link);
          formFF.find('#admin_ff').val(data.admin);
          formFF.submit();

        });
      });

     
@if($book->type_book != 1)
$( "#reserva_formBox :input" ).each(function( index ) {
  $( this ).prop('disabled', true);
});
@endif
    });
  
  </script>
  <style>
    button.partee-cp {
      position: relative;
      background-color: #fff;
      color: #10cfbd;
      font-size: 2.52em;
      border: none;
    }
    button.partee-cp:hover {
      /*background-color: #ff5a5f;*/
      color:#00d8c4;
    }
    .tooltip .tooltiptext::after {
      content: "";
      position: absolute;
      top: 100%;
      left: 15%;
      margin-left: -5px;
      border-width: 5px;
      border-style: solid;
      border-color: #929292 transparent transparent transparent;
    }

    .tooltip:hover .tooltiptext {
      visibility: visible;
      opacity: 1;
    }
    button.partee-cp .tooltip.show{
      display: block;
      opacity: 1;
      width: 100%;
      top: -3em;
      left: 0;
    }
    .tooltiptext {
      position: absolute;
      font-size: 11px;
      color: #fff;
      background-color: rgba(0, 0, 0, 0.42);
      padding: 2px 5px;
      width: 10em;
      border-radius: 7px;
    }
    span#loadchatbox {
      float: right;
      font-size: 0.75em;
      color: #daeffd;
      cursor: pointer;
    }
    .partee-icon .pf-icon {
        float: left;
        width: 50%;
        text-align: center;
        display: block;
        font-size: 20px;
    }
    .partee-icon .policeman{
      display: block;
      float: left;
      width: 50%;
      background-repeat: no-repeat;
      height: 2.3em;
      margin-top: 00;
    }
  </style>
  @endsection
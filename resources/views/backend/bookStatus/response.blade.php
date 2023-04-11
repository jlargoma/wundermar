<div class="col-xs-12">
  <div class="row" style="font-size: 1.3em;">
    <div class="col-xs-12  mb-1em" >
      <div class="row">
        <div class="col-md-4"> Nombre: <b>{{ucfirst($name)}}</b></div>
        <div class="col-md-5"> <b>{{$email}}</b></div>
        <div class="col-md-3"> Tel.: <b><a href="tel:+{{$phone}}">{{$phone}}</a></b></div>
      </div>
      
    </div>
    <div class="col-xs-6 col-md-6 mb-1em">
      Fechas: <b><?php echo convertDateToShow_text($start) . ' - ' . convertDateToShow_text($finish); ?></b>
    </div>
    <div class="col-xs-6 col-md-3 mb-1em text-center">
      <b>{{$nigths}} </b>noches
    </div>
    <div class="col-xs-6 col-md-3 mb-1em text-center">
      <b> <?php echo $pax ?> <?php if ($pax == 1): ?>Per<?php else: ?>Pers <?php endif ?>	</b>
    </div>
  </div>

  <div class="line" style="margin-bottom: 10px;"></div>


  <input type="hidden" id="calc_username" value="{{$name}}">
  <input type="hidden" id="calc_start" value="{{$start}}">
  <input type="hidden" id="calc_finish" value="{{$finish}}">
  <input type="hidden" id="calc_pax" value="{{$pax}}">

  <div class="table-responsive" style="overflow-y: hidden;">
    <table class="table table-mobile">
      <thead>
        <tr class ="text-center bg-success text-white">
          <th class="th-bookings text-center th-2">Disp.</th>
          <th class="th-bookings">Apto.</th>
          <th class="th-bookings text-center th-2">Precio</th>
          <th class="th-bookings text-center th-2">Desc</th>
          <th class="th-bookings text-center th-2">Promo</th>
          <th class="th-bookings text-center th-2">Supl Limp</th>
          <th class="th-bookings text-center th-2">total</th>
          <th class="th-bookings text-center th-2">&nbsp;</th>
          <th class="th-bookings text-center th-2">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
      @foreach($rooms as $roomSites)
        @foreach($roomSites as $room)
        <tr >
          <td class="text-center">{{$room['avail']}}</td>
          <td class="th-bookings text-left">{{$room['tiposApto']}}</td>
          <td class="text-center"><b >{{moneda($room['pvp_init'],false,2)}}</b></td>
          <td class="text-center text-danger"><b ><?php echo ($room['disc_pvp']>0)? '-'.moneda($room['disc_pvp'],false,2) : ''; ?></b></td>
          <td class="text-center text-danger"><b ><?php echo ($room['pvp_promo']>0)? '-'.moneda($room['pvp_promo'],false,2) : ''; ?></b></td>
          <td class="text-center"><b >{{moneda($room['limp'],false,2)}}</b></td>
          <td class="text-center"><b >{{moneda($room['price'],false,2)}}</b></td>
          <td> 
            <?php if (Auth::user()->role != "agente"): ?>
              <button 
                type="button" 
                class="btn btn-success text-white calc_createNew"
                data-room="{{$room['roomID']}}"
                data-luxury="{{$room['luxury']}}"
                data-info="{{serialize($room)}}"
                >RESERVAR</button>
              <?php endif; ?>
          </td>
          <td>
            @if (isset($urlsGH[$room['site_id']]))
            <a href="{{$urlsGH[$room['site_id']]}}" target="_blank" class="">GHotels</a>
            @endif
          </td>
        </tr>
          @if($room['msg'])
          <tr>
            <td colspan="3"><p class="text-danger">{{$room['msg']}}</p></td>
          </tr>
          @endif
        @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="col-md-6">
    <button class="btn btn-danger btn-lg btn-cons  text-white center hvr-grow-shadow btn-back-calculate">VOLVER</button>
  </div>

</div>
<script type="text/javascript">
  $(document).ready(function () {
    $('.btn-back-calculate').click(function (event) {
      $('#content-book-response .back').empty();
      $("#content-book-response .back").hide();
      $("#content-book-response .front").show();
    });
  });
</script>

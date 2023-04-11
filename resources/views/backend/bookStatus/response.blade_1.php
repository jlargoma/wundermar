<div class="col-xs-12">
  <div class="col-xs-12 col-md-12">

    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Nombre:</span>
      <span class="font-w800 center push-10 font-s18 black pull-right"><?php echo ucfirst($name) ?></span>
    </div>
    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Numº Pers:</span>
      <span class="font-w800 center push-10 font-s18 black pull-right">
        <?php echo $pax ?> <?php if ($pax == 1): ?>Per<?php else: ?>Pers <?php endif ?>	
      </span>
    </div>

    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Apartamento:</span>
      <span class="font-w800 center push-10 font-s18 black font-w300 pull-right"><?php echo $apto ?></span>
    </div>
    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Noches:</span>
      <span class="center push-10 font-s18 black font-w300 pull-right"><span class="font-w800"><?php echo $nigths ?></span> Noches</span>
    </div>
    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Fechas:</span> 
      <span class="push-10 font-s18 black font-w300 pull-right"><b><?php echo convertDateToShow_text($start) . ' - ' . convertDateToShow_text($finish); ?></b></span>
    </div>
    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Sup. Lujo:<?php if ($luxury > 0): ?>(SI)<?php else: ?>(NO)<?php endif; ?></span>
      <span class="center push-10 font-s18 black font-w300 pull-right"><span class="font-w800"><?php echo number_format($luxury, 0, '', '.') ?>€</span></span>
    </div>
    <!-- <div class="row push-10">
            <span class="push-10 font-s18 black font-w300 pull-left">Parking:<?php if ($priceParking > 0): ?>(SI)<?php else: ?>(NO)<?php endif; ?></span>
            <span class="center push-10 font-s18 black font-w300 pull-right"><span class="font-w800"><?php echo number_format($priceParking, 0, '', '.') ?>€</span></span>
    </div> -->
  </div>
  <div class="line" style="margin-bottom: 10px;"></div>
  <div class="row push-10">
    <div class="col-xs-12">
      <?php if (Auth::user()->role != "agente"): ?>
        <form method="post" action="{{url('/admin/reservas/create')}}" id="confirm-book">
        <?php else: ?>
          <form method="post" action="">
          <?php endif ?>
          @if(false)
          <div class="row text-center push-10">
            <div class="col-xs-12">
              <div class="col-xs-6 text-left push-10">
                <input id="price" class="radio-style" name="priceDiscount" type="radio"  value="no" @if(!$setting || empty($setting->value)) checked @endif>
                       <label for="price" class="radio-style-3-label">Precio Apto:</label>
              </div>
              <div class="col-xs-6 push-10">
                <p class="text-black push-10 font-s16 font-w300 text-right" style="line-height: 1">
                  <span class="font-w800" style="font-size: 20px; @if($setting and !empty($setting->value)) text-decoration:line-through; @endif"><?php echo number_format($total, 0, '', '.') ?>€</span>
                </p>
              </div>
            </div>
            @if($setting and !empty($setting->value) && $isFastPayment)
            <div class="col-xs-12">
              <div class="col-xs-6 text-left">
                <input id="price-discount" class="radio-style" name="priceDiscount" type="radio"  value="yes" checked>
                <label for="price-discount" class="radio-style-3-label"> Descuento en Precio Apto:</label>
              </div>
              <div class="col-xs-6">
                <p class="text-black push-10 font-s16 font-w300 text-right" style="line-height: 1">
                  <span class="font-w800" style="font-size: 20px;"><?php echo number_format(($total - $setting->value), 0, '', '.') ?>€</span>
                </p>
              </div>
            </div>
            @endif
          </div>
          @else
          <input id="price" name="priceDiscount" type="hidden"  value="no">
          <div class="row">
            <div class="col-xs-12">
              <div class="col-xs-6 text-left push-10">
                <label for="price">Precio Apto:</label>
              </div>
              <div class="col-xs-6 push-10">
                <p class="text-black push-10 font-s16 font-w300 text-right" style="line-height: 1">
                  <span class="font-w800" style="font-size: 20px;"><?php echo number_format($total, 0, '', '.') ?>€</span>
                </p>
              </div>
            </div>
          </div>
          @endif
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
          <input type="hidden" name="newroom" value="<?php echo $id_apto; ?>">
          <input type="hidden" name="name" value="<?php echo $name; ?>">
          <input type="hidden" name="email" value="<?php echo $email; ?>">
          <input type="hidden" name="phone" value="<?php echo $phone; ?>">
          <input type="hidden" name="start" value="<?php echo $start ?>">
          <input type="hidden" name="finish" value="<?php echo $finish ?>">
          <input type="hidden" name="pax" value="<?php echo $pax; ?>">
          <input type="hidden" name="nigths" value="<?php echo $nigths; ?>">
          <input type="hidden" name="parking" value="<?php echo $parking; ?>">
          <input type="hidden" name="agencia" value="0">
          <input type="hidden" name="lujo" value="<?php echo $luxury ?>">
          <input type="hidden" name="total" value="<?php echo $total ?>">
          <input type="hidden" name="book_comments" value="">
          <?php if ($luxury > 0): ?>
            <input type="hidden" name="type_luxury" value="1">
          <?php else: ?>
            <input type="hidden" name="type_luxury" value="2">
          <?php endif; ?>

          @if($setting and !empty($setting->value))
          <input type="hidden" name="discount" value="{{$setting->value}}">
          @else
          <input type="hidden" name="discount" value="0">
          @endif
          <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
              <?php if (Auth::user()->role != "agente"): ?>
                <div class="col-md-6">
                  <button type="submit" class="btn btn-success text-white btn-lg btn-cons center hvr-grow-shadow ">RESERVAR</button>
                </div>
              <?php endif; ?>
              <div class="col-md-6">
                <button class="btn btn-danger btn-lg btn-cons  text-white center hvr-grow-shadow btn-back-calculate">VOLVER</button>
              </div>
            </div>
          </div>
        </form>
    </div>
  </div>
  @if($msg)
  <p class="alert alert-warning">{{$msg}}</p>
  @endif
</div>
<script type="text/javascript">
  $(document).ready(function () {
    $('.btn-back-calculate').click(function (event) {
      $('#content-book-response .back').empty();
      $("#content-book-response .back").hide();
      $("#content-book-response .front").show();
    });
  });
</script><div class="col-xs-12">
  <div class="col-xs-12 col-md-12">

    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Nombre:</span>
      <span class="font-w800 center push-10 font-s18 black pull-right"><?php echo ucfirst($name) ?></span>
    </div>
    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Numº Pers:</span>
      <span class="font-w800 center push-10 font-s18 black pull-right">
        <?php echo $pax ?> <?php if ($pax == 1): ?>Per<?php else: ?>Pers <?php endif ?>	
      </span>
    </div>

    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Apartamento:</span>
      <span class="font-w800 center push-10 font-s18 black font-w300 pull-right"><?php echo $apto ?></span>
    </div>
    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Noches:</span>
      <span class="center push-10 font-s18 black font-w300 pull-right"><span class="font-w800"><?php echo $nigths ?></span> Noches</span>
    </div>
    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Fechas:</span> 
      <span class="push-10 font-s18 black font-w300 pull-right"><b><?php echo convertDateToShow_text($start) . ' - ' . convertDateToShow_text($finish); ?></b></span>
    </div>
    <div class="row push-10">
      <span class="push-10 font-s18 black font-w300 pull-left">Sup. Lujo:<?php if ($luxury > 0): ?>(SI)<?php else: ?>(NO)<?php endif; ?></span>
      <span class="center push-10 font-s18 black font-w300 pull-right"><span class="font-w800"><?php echo number_format($luxury, 0, '', '.') ?>€</span></span>
    </div>
    <!-- <div class="row push-10">
            <span class="push-10 font-s18 black font-w300 pull-left">Parking:<?php if ($priceParking > 0): ?>(SI)<?php else: ?>(NO)<?php endif; ?></span>
            <span class="center push-10 font-s18 black font-w300 pull-right"><span class="font-w800"><?php echo number_format($priceParking, 0, '', '.') ?>€</span></span>
    </div> -->
  </div>
  <div class="line" style="margin-bottom: 10px;"></div>
  <div class="row push-10">
    <div class="col-xs-12">
      <?php if (Auth::user()->role != "agente"): ?>
        <form method="post" action="{{url('/admin/reservas/create')}}" id="confirm-book">
        <?php else: ?>
          <form method="post" action="">
          <?php endif ?>
          @if(false)
          <div class="row text-center push-10">
            <div class="col-xs-12">
              <div class="col-xs-6 text-left push-10">
                <input id="price" class="radio-style" name="priceDiscount" type="radio"  value="no" @if(!$setting || empty($setting->value)) checked @endif>
                       <label for="price" class="radio-style-3-label">Precio Apto:</label>
              </div>
              <div class="col-xs-6 push-10">
                <p class="text-black push-10 font-s16 font-w300 text-right" style="line-height: 1">
                  <span class="font-w800" style="font-size: 20px; @if($setting and !empty($setting->value)) text-decoration:line-through; @endif"><?php echo number_format($total, 0, '', '.') ?>€</span>
                </p>
              </div>
            </div>
            @if($setting and !empty($setting->value) && $isFastPayment)
            <div class="col-xs-12">
              <div class="col-xs-6 text-left">
                <input id="price-discount" class="radio-style" name="priceDiscount" type="radio"  value="yes" checked>
                <label for="price-discount" class="radio-style-3-label"> Descuento en Precio Apto:</label>
              </div>
              <div class="col-xs-6">
                <p class="text-black push-10 font-s16 font-w300 text-right" style="line-height: 1">
                  <span class="font-w800" style="font-size: 20px;"><?php echo number_format(($total - $setting->value), 0, '', '.') ?>€</span>
                </p>
              </div>
            </div>
            @endif
          </div>
          @else
          <input id="price" name="priceDiscount" type="hidden"  value="no">
          <div class="row">
            <div class="col-xs-12">
              <div class="col-xs-6 text-left push-10">
                <label for="price">Precio Apto:</label>
              </div>
              <div class="col-xs-6 push-10">
                <p class="text-black push-10 font-s16 font-w300 text-right" style="line-height: 1">
                  <span class="font-w800" style="font-size: 20px;"><?php echo number_format($total, 0, '', '.') ?>€</span>
                </p>
              </div>
            </div>
          </div>
          @endif
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
          <input type="hidden" name="newroom" value="<?php echo $id_apto; ?>">
          <input type="hidden" name="name" value="<?php echo $name; ?>">
          <input type="hidden" name="email" value="<?php echo $email; ?>">
          <input type="hidden" name="phone" value="<?php echo $phone; ?>">
          <input type="hidden" name="start" value="<?php echo $start ?>">
          <input type="hidden" name="finish" value="<?php echo $finish ?>">
          <input type="hidden" name="pax" value="<?php echo $pax; ?>">
          <input type="hidden" name="nigths" value="<?php echo $nigths; ?>">
          <input type="hidden" name="parking" value="<?php echo $parking; ?>">
          <input type="hidden" name="agencia" value="0">
          <input type="hidden" name="lujo" value="<?php echo $luxury ?>">
          <input type="hidden" name="total" value="<?php echo $total ?>">
          <input type="hidden" name="book_comments" value="">
          <?php if ($luxury > 0): ?>
            <input type="hidden" name="type_luxury" value="1">
          <?php else: ?>
            <input type="hidden" name="type_luxury" value="2">
          <?php endif; ?>

          @if($setting and !empty($setting->value))
          <input type="hidden" name="discount" value="{{$setting->value}}">
          @else
          <input type="hidden" name="discount" value="0">
          @endif
          <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
              <?php if (Auth::user()->role != "agente"): ?>
                <div class="col-md-6">
                  <button type="submit" class="btn btn-success text-white btn-lg btn-cons center hvr-grow-shadow ">RESERVAR</button>
                </div>
              <?php endif; ?>
              <div class="col-md-6">
                <button class="btn btn-danger btn-lg btn-cons  text-white center hvr-grow-shadow btn-back-calculate">VOLVER</button>
              </div>
            </div>
          </div>
        </form>
    </div>
  </div>
  @if($msg)
  <p class="alert alert-warning">{{$msg}}</p>
  @endif
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
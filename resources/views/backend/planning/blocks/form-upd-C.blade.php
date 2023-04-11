<?php 
$uRole = getUsrRole();
$disabl_limp = ($uRole == "limpieza") ? 'disabled' : '';
?>
<div class="row col-xs-12 push-20">
  <div class="col-md-3 col-xs-12 text-center boxtotales" style="background-color: #0c685f;">
    <label class="font-w800 text-white" for="">PVP</label>
    <input type="number" step='0.01' class="form-control total m-t-10 m-b-10 white" {{$disabl_limp}}
           name="total" value="<?php echo $book->total_price ?>">
  </div>
<?php if ($uRole == "admin"): ?>
    <div class="col-md-3 col-xs-12 text-center boxtotales" style="background: #99D9EA;">
      <label class="font-w800 text-white" for="">COSTE TOTAL</label>
      <input  readonly=""  step='0.01' class="form-control cost m-t-10 m-b-10 white" value="{{$book->cost_total}}">
    </div>
    <div class="col-md-3 col-xs-12 text-center boxtotales" style="background: #91cf81;">
      <label class="font-w800 text-white" for="">COSTE APTO</label>
      <input type="number" step='0.01' class="form-control costApto m-t-10 m-b-10 white"
             name="costApto" value="<?php echo $book->cost_apto ?>">
    </div>
    <div class="col-md-3 col-xs-12 text-center boxtotales" style="background: #337ab7;">
      <label class="font-w800 text-white" for="">COSTE PARKING</label>
      <input type="number" step='0.01' class="form-control costParking m-t-10 m-b-10 white"
             name="costParking" value="<?php echo $book->cost_park ?>">
    </div>
    <div class="col-md-3 col-xs-12 text-center boxtotales not-padding"
         style="background: #ff7f27;">
      <label class="font-w800 text-white" for="">BENEFICIO</label>
      <span class="beneficio">{{$book->total_ben}}</span>
      <span class="beneficio-text">{{$book->inc_percent}}%</span>
    </div>
<?php endif ?>

</div>
<?php if ($uRole == "admin" && false): ?>
  <div class="col-md-12 col-xs-12 push-20 not-padding calculos">
      <p>Cáculos desde nueva fórmula</p>
      <?php
      $price = $book->getPriceBook($book->start, $book->finish, $book->room_id, $book->real_pax);
      if ($price['status'] == 'error') {
        echo '<p class="alert alert-warning">' . $price['msg'] . '</p>';
      }
      ?>
      <div class="col-xs-3 text-white" id="changeRealPrice" style="background-color: #c1c1c1;">
          TOTAL PVP<br><span id="realPrice">{{$price['price_total']}}</span><br/>
      </div>
      <div class="col-xs-3 text-white" style="background-color: #c1c1c1;">
          PVP<br><span id="realPVP">{{$price['pvp']}}</span>
      </div>
      <div class="col-xs-3 text-white" style="background-color: #c1c1c1;">
          EXTRAS FIJOS<br><span id="realExta">{{$price['extra_fixed']+$price['limp']}}</span>
      </div>
      <div class="col-xs-3 text-white" style="background-color: #c1c1c1;">
          SUPL EXTRAS<br><span id="realDynamic">{{$price['extra_dynamic']}}</span>
      </div>
  </div>
<?php endif ?>
      
<div class="col-md-12 col-xs-12 not-padding">
    <p class="personas-antiguo" style="color: red">
      <?php if ($book->pax < $book->room->minOcu): ?>
        Van menos personas que la ocupacion minima del apartamento.
      <?php endif ?>
    </p>
    <p class="precio-antiguo font-s18">
    <!--El precio asignado-<b>El precio asignado <?php echo $book->total_price ?> y el precio de tarifa es <?php echo $book->real_price ?></b> -->
    </p>
</div>

<?php if ($uRole != "agente"): ?>
  <p class="text-center">Precio que se muestra al público</p>
  <div class="col-md-12 col-xs-12 push-20 not-padding" >
    <div class="col-md-3 col-xs-6 box-info">
      <input type="hidden" id="confirm_publ_total" value="{{$priceBook['pvp']}}">
      PVP Final<br><span  id="publ_total">{{$priceBook['pvp']}}</span>
    </div>
    <div class="col-md-3 col-xs-6 box-info">
      <input type="hidden" id="confirm_publ_price" value="{{$priceBook['pvp_init']}}">
      PVP Inicial<br><span  id="publ_price">{{$priceBook['pvp_init']}}</span><br/>
    </div>
    <div class="col-md-2 col-xs-4 box-info">
      <input type="hidden" id="confirm_publ_disc" value="{{$priceBook['discount_pvp']}}">
      DESC<br><span  id="publ_disc">{{$priceBook['discount_pvp']}}</span>
    </div>
    <div class="col-md-2 col-xs-4 box-info">
      <input type="hidden" id="confirm_publ_promo" value="{{$priceBook['promo_pvp']}}">
      PROMO<br><span  id="publ_promo">{{$priceBook['promo_pvp']}}</span>
    </div>
    <div class="col-md-2 col-xs-4 box-info">
      <input type="hidden" id="confirm_publ_limp" value="{{$priceBook['price_limp']}}">
      SUPL LIMP<br><span  id="publ_limp">{{$priceBook['price_limp']}}</span>
    </div>
  </div>
<?php endif ?>
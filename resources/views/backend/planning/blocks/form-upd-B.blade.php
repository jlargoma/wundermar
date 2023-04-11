<?php
use \Carbon\Carbon;
$disabl_limp = ($uRole == "limpieza") ? 'disabled' : '';
?>
<!-- DATOS DE LA RESERVA -->
<div class="col-md-12 col-xs-12 bg-white padding-block" style="padding-bottom:0">
  <div class="col-xs-12 bg-black push-20">
    <h4 class="text-center white">
      DATOS DE LA RESERVA
      <i class="fas fa-sync-alt" id="reset"
         style="cursor:pointer; position:absolute; right:2rem"></i>
    </h4>
  </div>
  <div class="inputs input-1">
    <label>Entrada</label>
    <div class="input-prepend input-group input_dates">
      <?php
      $start1 = Carbon::createFromFormat('Y-m-d', $book->start)->format('d M, y');
      $finish1 = Carbon::createFromFormat('Y-m-d', $book->finish)->format('d M, y');
      ?>
      <input type="text" class="form-control daterange1 minimal" id="fechas" name="fechas" required="" style="cursor: pointer; text-align: center; backface-visibility: hidden;min-height: 28px;" value="<?php echo $start1; ?> - <?php echo $finish1 ?>" readonly="" <?php if (Auth::user()->role == "limpieza"): ?>disabled<?php endif ?>>
      <input type="hidden" class="date_start" id="start" name="start" value="{{$book->start}}">
      <input type="hidden" class="date_finish" id="finish" name="finish" value="{{$book->finish}}">

    </div>
  </div>
  <div class="inputs input-2">
    <label>Min. Est.</label>
    <input class="form-control minimal  <?php if($book->nigths<$minStay) echo 'danger'; ?> " disabled  id="minDay" value="{{$minStay}}">
  </div>
  <div class="inputs input-2">
    <label>Noches</label>
    <input type="number" class="form-control nigths minimal" name="nigths" style="width: 100%" disabled value="<?php echo $book->nigths ?>">
    <input type="hidden" class="form-control nigths" name="nigths" style="width: 100%" value="<?php echo $book->nigths ?>">
  </div>
  <div class="inputs input-2">
    <label>Pax</label>
    <select class=" form-control pax minimal minimal" name="pax" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
      <?php for ($i = 1; $i <= 14; $i++): ?>
          <option value="{{$i}}" <?php echo ($i == $book->pax) ? "selected" : ""; ?>><?php echo $i ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="inputs input-2">
    <label style="color: red">Pax-Real</label>
    <select class=" form-control real_pax minimal" name="real_pax" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
      <?php for ($i = 1; $i <= 14; $i++): ?>
        <option value="{{$i}}" <?php echo ($i == $book->real_pax) ? "selected" : ""; ?> style="color: red"><?php echo $i ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="inputs input-1">
    <label>Alojamiento</label>
    <select class="form-control full-width minimal newroom" name="newroom" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>    id="newroom" <?php if (isset($_GET['saveStatus']) && !empty($_GET['saveStatus'])): echo "style='border: 1px solid red'";endif ?>>
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
  <div class="clearfix"></div>
  <div class="inputs input-2">
    <label>IN</label>
    <select id="schedule" class="form-control minimal" style="width: 100%;" name="schedule" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
      @include('backend.planning.blocks.times',['s'=>$book->schedule])
    </select>
  </div>
  <div class="inputs input-2">
    <label>Out</label>
    <select id="scheduleOut" class="form-control minimal" style="width: 100%;"  name="scheduleOut" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
      @include('backend.planning.blocks.times',['s'=>$book->scheduleOut])
      <option value="24" <?php if(24 == $book->scheduleOut) { echo 'selected';}?>>CHECKOUT</option>
    </select>
  </div>
    <div class="inputs input-3">
      <label>Agencia</label>
      <select class="form-control full-width agency minimal" name="agency" >
    @include('backend.blocks._select-agency', ['agencyID'=>$book->agency,'book' => $book])
      </select>
    </div>
    <div class="inputs input-3">
      <label>Cost Agencia</label>
        <?php if ($book->PVPAgencia == 0.00): ?>
          <input type="number" step='0.01' class="agencia form-control" name="agencia" value="" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
        <?php else: ?>
          <input type="number" step='0.01' class="agencia form-control" name="agencia" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?> value="<?php echo $book->PVPAgencia ?>">
        <?php endif ?>
    </div>
  <div class="inputs input-3">
    <label>promoción</label>
    <input type="number" step='0.01' class="promociones only-numbers form-control" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?> name="promociones" value="<?php echo ($book->promociones > 0) ? $book->promociones : "" ?>">
  </div>

<?php if ($book->book_owned_comments != "" && $book->promociones != 0): ?>
    <div class="col-md-2 col-xs-6 push-10 content_image_offert">
      <img src="/pages/oferta.png" style="width: 90px;">
    </div>
<?php endif ?>
 </div>
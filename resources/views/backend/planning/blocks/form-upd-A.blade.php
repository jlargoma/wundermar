<?php 
$disLimp = (getUsrRole() == 'limpieza') ? 'disabled' : '' ?>
<div class="col-xs-12 padding-block push-0" style="padding-bottom:0">
    <div class="col-xs-12 bg-black push-20">
        <h4 class="text-center white">DATOS DEL CLIENTE</h4>
    </div>
    <div class="col-xs-4 push-10 row-mobile">
        <label for="name">Nombre</label>
        <input class="form-control cliente" type="text" name="nombre" id="c_name" value="<?php echo $book->customer->name ?>" {{$disLimp}}>
    </div>
    <div class="col-xs-4 push-10 row-mobile">
        <label for="email">Email</label>
        <input class="form-control cliente" type="email" name="email" id="c_email" value="<?php echo $book->customer->email ?>" {{$disLimp}}>
    </div>
    <div class="col-xs-4 push-10 row-mobile">
        <label for="phone">Telefono</label>
        <?php if ($book->customer->phone == 0) {
          $book->customer->phone = "";
        } ?>
        <input class="form-control cliente" type="text" name="phone" id="c_phone" value="<?php echo $book->customer->phone ?>" {{$disLimp}}>
    </div>
</div>
<div class="col-xs-12">
<div class="col-xs-3 push-10 row-mobile">
    <label for="dni">DNI</label>
    <input class="form-control cliente" type="text" name="dni" id="c_dni"  value="<?php echo $book->customer->DNI ?>" {{$disLimp}}>
</div>
<div class="col-xs-3  push-10 row-mobile">
    <label for="address">DIRECCION</label>
    <input class="form-control cliente" type="text" name="address" id="c_address"  value="<?php echo $book->customer->address ?>" {{$disLimp}}>
</div>
<div class="col-xs-3  push-10 row-mobile">
    <label for="country">PAÍS</label>
        <?php $c_country = ($book->customer->country) ? strtolower($book->customer->country) : 'es'; ?>
    <select class="form-control country minimal" name="country" {{$disLimp}}>
        <option value="">--Seleccione país --</option>
        <?php
        foreach (\App\Countries::orderBy('code', 'ASC')->get() as $country):
          ?>
          <option value="<?php echo $country->code ?>" <?php if (strtolower($country->code) == $c_country) {
              echo "selected";
            } ?>>
  <?php echo $country->country ?> 
          </option>
    <?php endforeach; ?>
    </select>
</div>
<div class="col-xs-3  push-10 content-cities row-mobile" <?php if ($c_country != 'es') echo ' style="display: none;" '; ?>>
    <label for="city">PROVINCIA</label>
<?php $book_prov = ($book->customer->province) ? $book->customer->province : 28; ?>
    <select class="form-control province minimal" name="province" {{$disLimp}}>
        <option>--Seleccione  --</option>
        <?php foreach (\App\Provinces::orderBy('province', 'ASC')->get() as $prov): ?>
          <option value="<?php echo $prov->code ?>" <?php if ($prov->code == $book_prov) {
          echo "selected";
        } ?>>
              {{$prov->province}}
          </option>
<?php endforeach; ?>
    </select>
</div>
    </div>
<div class="box">
  <h2>Precio Pax Extras </h2>
  <form role="form" action="{{ route('settings.extr_pax_price') }}" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="row">
      <div class="col-md-6">
        Precio por cada PAX extra (por día)
      </div>
      <div class="col-md-3">
        <input type="number" class="form-control" name="price"
               placeholder="Precio" required=""
               value="{{$priceExtrPax}}"
               aria-required="true" aria-invalid="false">
      </div>
      <div class="col-md-3">
        <button class="btn btn-complete" type="submit">Guardar</button>
      </div>
    </div>
  </form>
</div>
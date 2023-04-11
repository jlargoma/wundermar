<form action="{{ url('/admin/arqueo/create') }}" method="post"  >
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <div class="row">
    <div class="col-lg-4 col-md-6 col-xs-12">
      <label for="date">Fecha</label>
      <div id="datepicker-component" class="input-group date">
          <input type="text" class="form-control datepicker" name="fecha" value="<?php echo date('d/m/Y') ?>" style="font-size: 12px">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12">
      <label for="import">Importe</label>
      <input  type="number" step="0.01" name="import" id="import" class="form-control" required />
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12">
      <label for="observ">Observaciones</label>
      <input type="text" name="observ" id="observ" class="form-control" required/>
    </div>
  </div>
  <div class="row mt-1em">
    <div class="col-xs-12 text-center">
      <button class="btn btn-success" type="submit">Añadir</button>
    </div>
  </div>
</form>
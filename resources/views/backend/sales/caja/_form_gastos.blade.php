<form action="{{ url('/admin/gastos/create') }}" method="post"  id="formNewExpense">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <div class="row">
    <div class="col-lg-4 col-md-6 col-xs-12 mb-1em">
      <label for="date">Fecha</label>
      <div id="datepicker-component" class="input-group date col-xs-12">
          <input type="text" class="form-control datepicker" name="fecha" id="fecha" value="<?php echo date('d/m/Y') ?>" style="font-size: 12px">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12 mb-1em">
      <label for="concept">Concepto</label>
      <input  type="text" class="form-control" name="concept" id="concept" />
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12 mb-1em">
      <label for="type">T. Gasto</label>
        <select class="form-control" id="type" name="type"  data-placeholder="Seleccione un tipo" required >
        @foreach($gType as $k=>$v)
        <option value="{{$k}}">{{$v}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12">
      <label for="import">Importe</label>
      <input  type="number" step="0.01" name="import" id="import" class="form-control" required />
    </div>
    <div class="col-lg-4  col-md-6 col-xs-12">
      <label for="pay_for">Met de pago</label>
      <select class="js-select2 form-control" id="type_payment" name="type_payment" style="width: 100%;" data-placeholder="Seleccione una" required>
        <option value="0"> CASH </option>
        <option value="2"> TPV </option>
      </select>
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12">
      <label for="type">Imputacion</label>
      <select class="form-control" id="site_id" name="site_id" style="width: 100%;" data-placeholder="Seleccione un tipo" required >
        <option value="0">Generíco</option>
        <option value="1">Riad</option>
        <option value="2">Hotel Rosa D'Oro</option>
        <option value="3">Gloria</option>
      </select>
    </div>
    <div class="col-md-6 col-xs-12 mt-1em">
      <label for="comment">Observaciones</label>
      <textarea class="form-control" name="comment" id="comment"></textarea>
    </div>
    <div class="col-md-6 col-xs-12 mt-1em">
      <button class="btn btn-success" type="submit">Añadir</button>
      <button class="btn btn-secondary" type="button" id="reload">Refrescar Pantalla</button>
    </div>
  </div>
</form>
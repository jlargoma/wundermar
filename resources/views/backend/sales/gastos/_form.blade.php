<?php 
$s_pt = 3;
if(isset($page)){
  $s_pt = 2;
}
$sty = 'style="min-height: 5em;"';
?>
<form action="{{ url('/admin/gastos/create') }}" method="post"  id="formNewExpense">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <div class="row">
    <div class="col-lg-4 col-md-6 col-xs-12 mb-1em" <?= $sty; ?>>
      <label for="date">Fecha</label>
      <div id="datepicker-component" class="input-group date col-xs-12">
          <input type="text" class="form-control" name="fecha" id="fecha" value="<?php echo date('d/m/Y') ?>" style="font-size: 12px">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12 mb-1em" <?= $sty; ?>>
      <label for="concept">Concepto</label>
      <input  type="text" class="form-control" name="concept" id="concept" />
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12 mb-1em" <?= $sty; ?>>
      <label for="type">T. Gasto</label>
        <select class="form-control" id="type" name="type"  data-placeholder="Seleccione un tipo" required >
        @foreach($gType as $k=>$v)
        <option value="{{$k}}">{{$v}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12" <?= $sty; ?>>
      <label for="import">Importe</label>
      <input  type="number" step="0.01" name="import" id="import" class="form-control" required />
    </div>
    <div class="col-lg-4  col-md-6 col-xs-12" <?= $sty; ?>>
      <label for="pay_for">Met de pago</label>
      <select class="js-select2 form-control" id="type_payment" name="type_payment" style="width: 100%;" data-placeholder="Seleccione una" required>
         @foreach($typePayment as $k=>$v)
        <option value="{{$k}}" <?php echo ($s_pt == $k) ? 'selected' : ''; ?>>{{$v}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12" <?= $sty; ?>>
      <label for="type">Imputacion</label>
      <select class="form-control" id="site_id" name="site_id" style="width: 100%;" data-placeholder="Seleccione un tipo" required >
        <option value="0">Generíco</option>
        @foreach(\App\Sites::allSites() as $k=>$v)
        <option value="{{$k}}">{{$v}}</option>
        @endforeach
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
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="{{ asset('/css/components/daterangepicker.css')}}" type="text/css" />
<script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/assets/plugins/moment/moment.min.js"></script>
<script type="text/javascript">
  $('#fecha').datepicker();
</script>

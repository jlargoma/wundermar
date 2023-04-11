<form action="{{ url('/admin/ingresos/create') }}" method="post" id="formAddIngr" >
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <div class="row">
    <div class="col-lg-4 col-md-6 col-xs-12">
      <label for="date">Fecha</label>
      <div id="datepicker-component" class="input-group date">
        <input type="text" class="form-control" id="fecha" name="fecha" value="<?php echo date('d/m/Y') ?>" style="font-size: 12px">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-xs-12">
      <label for="Tipo">Tipo</label>
      <select class="form-control" id="type" name="type" required="">
        <option value="extr">EXTRAORDINARIOS</option>
        <option value="others">OTROS</option>
      </select>
    </div>
    
    <div class="col-lg-4 col-md-6 col-xs-12">
      <label for="import">Importe</label>
      <input  type="number" step="0.01" name="import" id="import" class="form-control" required />
    </div>
  </div>
  <div class="row mt-1em">
    <div class="col-md-7 col-xs-8">
      <label for="import">Concepto</label>
      <input type="text" name="concept" id="concept" class="form-control" />
    </div>
    <div class="col-md-5 col-xs-4 text-center" style="margin-top: 1.69em;">
      <button class="btn btn-success" type="submit">Añadir</button>
      <button class="btn btn-info editIngrs" type="button">Editar Ingrs</button>
    </div>
  </div>
</form>
<div class="table-responsive" id="tableEdit">
  <table class="table">
    <tr class="resume-head">
      <th>Fecha</th>
      <th>Tipo</th>
      <th>Concepto</th>
      <th>Importe</th>
      <th>Eliminar</th>
    </tr>
    @foreach($incomesLst as $v)
    <tr>
      <td>{{convertDateToShow($v->date)}}</td>
      <td><?php echo ($v->type == 'type') ? 'EXTRAORDINARIOS' : 'OTROS';?></td>
      <td>{{$v->concept}}</td>
      <td>{{$v->import}}</td>
      <td><i class="fa fa-trash delIngr" data-k="{{$v->id}}" data-n="{{$v->concept}}"></i></td>
    </tr>
    @endforeach
  </table>
  <button class="btn btn-info addIngrs" type="button">volver</button>
</div>

<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css" />
<script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/assets/plugins/moment/moment.min.js"></script>
<script type="text/javascript">
  $('#fecha').datepicker();
  $('.editIngrs').on('click',function(){
    $('#formAddIngr').hide();
    $('#tableEdit').show();
  });
  $('.addIngrs').on('click',function(){
    $('#formAddIngr').show();
    $('#tableEdit').hide();
  });
  
  $('.delIngr').on('click',function(){
    var obj = $(this);
    if(confirm('Eliminar el ingreso '+obj.data('n')+'?')){
      $.post('/admin/delIngr', {_token: "{{csrf_token()}}", id: obj.data('k')}, function (data) {
        if (data == 1) {
          obj.closest('tr').remove();
          window.show_notif('Registro eliminado','success','');
        } else { 
          window.show_notif('Error al eliminar el ingreso','error','');
        }
      });
  
    }
  });
  
</script>
<style>
  #tableEdit{
    display: none;
  }
  #tableEdit th,
  #tableEdit td{
    text-align: center;
  }
  .delIngr{
    cursor: pointer;
    color: red;
  }
</style>

<form action="{{route('revenue.generatePickUp')}}" method="post" style="display: inline-block; max-width: 70%;">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <button class="btn btn-primary" style="white-space: normal;" onclick="return confirm('Adventencia: ésta acción sobrescribirá los datos actuales de la temporada. Desea continuar?')">Generar Datos DB</button>
</form>
<form action="{{route('revenue.donwlPickUp')}}" method="post" style="display: inline-block">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="site" value="{{$site}}">
  <input type="hidden" name="start" value="{{$start}}">
  <input type="hidden" name="finish" value="{{$finish}}">
  <button class="btn btn-complete" >Excel</button>
</form>
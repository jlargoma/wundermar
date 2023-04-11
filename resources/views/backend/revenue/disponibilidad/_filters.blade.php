
<form id="revenu_filters" method="get" action="{{route('revenue.disponibilidad')}}" class="col-xs-6">
  <div class="filter-field">
      <label>Mes</label>
      <select name="month" id="month" class="form-control">
          @foreach($lstMonths as $k=>$n)
          <option value="{{$k}}" @if($month == $k) selected @endif>{{$n}}</option>
          @endforeach
      </select>
  </div>
</form>
<form method="post" action="{{route('revenue.donwlDisponib')}}" class="col-xs-6">
    <input id="month" name="month" value="{{$month}}"  type="hidden">
    <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
    <button class="btn btn-primary" style="margin-top: 25px;"> Descargar</button>
</form>
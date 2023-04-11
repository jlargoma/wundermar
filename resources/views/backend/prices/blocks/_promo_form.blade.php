<form method="POST" action="{{route('channel.promotions.new')}}" id="channelForm">
  <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" id="itemID" name="itemID" value="">
  <div class="row">
    <div class="col-md-6">
      <div>
        <label>Nombre Promo</label>
        <input type="text" class="form-control" id="name" name="name" value="">
      </div>
      <div class="pt-1">
        <label>Rango de Fechas</label>
        <input type="text" class="form-control daterange01" id="range" name="range" value="">
      </div>
      <div class="pt-1">
        <input type="hidden" id="weekday" name="weekday" value="all">
        <button class="btn btn_weekday weekday_all active" type="button" data-val="all">Todos</button>
        <button class="btn btn_weekday weekday_working" type="button" data-val="working">Laborables</button>
        <button class="btn btn_weekday weekday_end" type="button"  data-val="end">Fin de Semana</button>
      </div>
    </div>
    <div class="col-md-6 blockType">
      <div class="row mb-1em promo_type checked">
        <div class="col-xs-4">
          <input type="radio" class="form-check-input radioType" id="type_perc" name="type" value="perc" checked="">
          <label class="form-check-label" >Descuento</label>
        </div>
        <div class="col-xs-8">
          <input type="number" class="form-control" name="discount" id="discount" value="15">
        </div>
      </div>
      <div class="row promo_type">
        <div class="col-xs-4 mt-2em">
          <input type="radio" class="form-check-input radioType" id="type_nights" name="type" value="nights">
          <label class="form-check-label" >x Noches</label>
        </div>
        <div class="col-xs-4">
          <label>Cada</label>
          <input type="number" class="form-control" name="nights" id="nights" value="">
        </div>
        <div class="col-xs-4">
          <label>Paga</label>
          <input type="number" class="form-control" name="night_apply" id="night_apply" value="">
        </div>
      </div>
    </div>
  </div>

  <div class="pt-1 col-xs-12">
    <div class="row">
      @if($ch_group)
      <div class="form-group row">
        <label class="col-md-12">Apartamentos  <a href="#" class="selAll">(Todos)</a></label>
        @foreach($ch_group as $k=>$v)
        <div class="form-check col-md-6">
          <input type="checkbox" class="form-check-input aptos_check" id="apto{{$k}}" name="apto{{$k}}">
          <label class="form-check-label" >{{$v}}</label>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </div>
  <div class="pt-1 col-xs-12">
    <label>Excluir Fechas <i class="fa fa-plus-circle text-primary" id="add"></i></label>
    <div class="row">
      <div id="datebox" class="col-xs-9">
      </div>
    </div>
  </div>

  <div class="mt-2em col-xs-12">
    <button class="btn btn-primary">Guardar</button>
    <button class="btn btn-primary" id="new" type="button">Nueva</button>
  </div>
</form>
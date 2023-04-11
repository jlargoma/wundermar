<div class="box">
  <h2>DIAS ESTANCIA MINIMA</h2>
  <div class="row">
    <form method="POST" action="{{route('channel.price.cal.upd','ALL')}}" id="channelFormMinStay">
      <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
      <div class="pt-1 col-md-12">
        <div class="row">
          <div class="col-md-3 col-xs-5"><label>Rango de Fechas</label></div>
          <div class="col-md-9 col-xs-7">
            <input type="text" class="form-control daterange1" id="date_range" name="date_range" value="">
            <input type="hidden" id="date_start" name="date_start">
            <input type="hidden" id="date_end" name="date_end">
          </div>
        </div>

      </div>
      <div class="pt-1 col-md-12 row">
        <button id="selAllDays" type="button"  class="btn_days">Todos</button>
        <button id="selWorkdays" type="button" class="btn_days">Laborales</button>
        <button id="selHolidays" type="button" class="btn_days">Fin de semana</button>
      </div>
      <div class="pt-1 col-md-12 row">
        @foreach($dw as $k=>$v)
        <div class="weekdays">
          <label>
            <input type="checkbox" name="dw_{{$k}}" id="dw_{{$k}}" checked="checked"/>
            <span> {{$v}}</span>
          </label>
        </div> 
        @endforeach
      </div>
      
      <div class="pt-1 col-xs-12">
        <label>Edificio</label>
        <div class="row">
          <?php foreach ($aSites as $k => $v): ?>
          <div class="col-sm-6" style="padding-left: 7px;">
              <div class="form-check">
                <input type="checkbox" class="form-check-input checkSites" name="site_ids[]" value="<?php echo $k; ?>">
                <label class="form-check-label" for="exampleCheck1">{{$v}}</label>
              </div>
          </div>
          <?php endforeach ?>
        </div>
      </div>
      
      
  
      
      <div class="pt-1 col-xs-6">
        <label>Estancia Mín.</label>
        <input type="number" class="form-control" name="min_estancia" id="min_estancia">
      </div>
      <div class=" pt-1 col-xs-6">
        <button class="btn btn-primary m-t-20">Guardar</button>
      </div>
    </form>
  </div>
  <div class="row pt-1">
    <p class="alert alert-danger" style="display:none;" id="error"></p>
    <p class="alert alert-success" style="display:none;" id="success"></p>
  </div>
  
  @if(count($logMinStays))
  <div class="table-responsive table-logs">
    <table class="table">
      <thead>
        <tr>
          <th >Fecha</th>
          <th >Min. Stay</th>
          <th >Días</th>
          <th >Edificios</th>
          <th >Usuario</th>
        </tr>
      </thead>
      @if($logMinStays)
      <tbody>
        @foreach($logMinStays as $item)
        <tr>
          <td class="nowrap">{{$item['start']}} / {{$item['end']}}</td>
          <td class="nowrap">{{$item['min_stay']}}</td>
          <td class="">{{$item['weekDays']}}</td>
          <td class="text-left">
            <?php 
              foreach ($item['sites'] as $sID) 
                if (isset($aSites[$sID]))
                  echo '<div class="nowrap">'.$aSites[$sID],'</div>';
            ?>
          </td>
          <td class="nowrap">{{$item['user']}}</td>
        </tr>
        @endforeach
        </tbody>
      @endif
    </table>
  </div>
  @endif
</div>
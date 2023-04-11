<?php 
$aux = $aSites;
$aux[0] = 'Todos';
$tDays = $mDays[0];
foreach ($aux as $k=>$v):

  $sSS = \App\Settings::findOrCreate('revenue_disponibilidad_'.$year, $k);
  $sSS = json_decode($sSS->content,true);
//  dd($sSS,$aRatios);
  $d = $aRatios[$k][0];
  $ocupPerc  = prOcup2($disp[$k],$tDays,$d['n']);
  $pvpMd     = prADR2($d);
  //---------------------------------------------------------//
  $p_pvp = isset($sSS['p_pvp']) ? $sSS['p_pvp'] : 0;
  $p_med_pvp = isset($sSS['p_med_pvp']) ? $sSS['p_med_pvp'] : 0;
  $p_hab = isset($sSS['p_n_hab']) ? $sSS['p_n_hab'] : 0;
  $p_habPercent = prOcup2($disp[$k],$tDays,$p_hab) ;
  //---------------------------------------------------------//
  $f_pvp = isset($sSS['foresc_pvp']) ? $sSS['foresc_pvp'] : 0;
  $f_med_pvp = isset($sSS['foresc_med_pvp']) ? $sSS['foresc_med_pvp'] : 0;
  $f_hab = isset($sSS['foresc_n_hab']) ? $sSS['foresc_n_hab'] : 0;
  $f_habPercent = prOcup2($disp[$k],$tDays,$f_hab) ;
  
   ?>

<div class="table-responsive yois yoi_{{$k}}" >
  <table class="table table-resumen summary">
    <thead>
      <tr class="resume-head">
        <th class="static">ANUAL</th>
        <th class="first-col"></td>
        <th>Nº Hab.</th>
        <th>% Ocup.</th>
        <th>Precio Medio</th>
        <th>Revenue</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="static text-left">Real</td>
        <td class="first-col"></td>
        <td>{{$d['n']}}</td>
        <td>{{$ocupPerc}}%</td>
        <td>{{moneda($pvpMd)}}</td>
        <td>{{moneda($d['p'])}}</td>
      </tr>
      <tr>
        <td class="static text-left">Pesupuesto</td>
        <td class="first-col"></td>
        <td><input class="editable" data-k="p_n_hab" data-s="{{$k}}" value="{{$p_hab}}"></td>
        <td>{{$p_habPercent}}%</td>
        <td><input class="editable" data-k="p_med_pvp" data-s="{{$k}}" value="{{$p_med_pvp}}"></td>
        <td><input class="editable" data-k="p_pvp" data-s="{{$k}}" value="{{$p_pvp}}"></td>
      </tr>
      <tr>
        <td class="static text-left">Diferencial</td>
        <td class="first-col"></td>
        <td>{{$d['n']-$p_habPercent}}</td>
        <td>{{$ocupPerc-$p_habPercent}}%</td>
        <td>{{moneda($pvpMd-$p_med_pvp)}}</td>
        <td>{{moneda($d['p']-$p_pvp)}}</td>
      </tr>
      <tr>
        <td class="static text-left">Forescating</td>
        <td class="first-col"></td>
        <td><input class="editable" data-k="foresc_n_hab" data-s="{{$k}}" value="{{$f_hab}}"></td>
        <td>{{$f_habPercent}}%</td>
        <td><input class="editable" data-k="foresc_med_pvp" data-s="{{$k}}" value="{{$f_med_pvp}}"></td>
        <td><input class="editable" data-k="foresc_pvp" data-s="{{$k}}" value="{{$f_pvp}}"></td>
      </tr>
      <tr>
        <td class="static text-left">Diferencial</td>
        <td class="first-col"></td>
        <td>{{$d['n']-$f_hab}}</td>
        <td>{{$ocupPerc-$f_habPercent}}%</td>
        <td>{{moneda($pvpMd-$f_med_pvp)}}</td>
        <td>{{moneda($d['p']-$f_pvp)}}</td>
      </tr>
    </tbody>
    <tfoot>
      <tr class="resume-head">
        <th class="static" style="height: 35px;">Dif YOY</th>
        <th class="first-col"></td>
        <td class="danger">-0</th>
        <th class="danger">-0%</th>
        <th class="success">0</th>
        <th class="danger"><b>-</b></th>
      </tr>
    </tfoot>
  </table>
</div>
<small class="yois yoi_{{$k}}">
<?php
  $roomsCount = $disp[$k];
  echo "Nº HAB. Disp: $roomsCount Habitaciones X $tDays Días = ".($roomsCount*$tDays); 
?>
</small>
<?php
endforeach;
?>


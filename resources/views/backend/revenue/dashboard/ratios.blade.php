<?php
//--BEGIN: Resumen -------//
$total = $tPaxs = $tCost = $vProp = $tnigth = 0;
$tSite = $aNight = $disp = [];

foreach ($aSites as $k => $v) {
  $aNight[$k] = 0;
  $disp[$k] = \App\Rooms::availSite($k);
}
$disp[0] = array_sum($disp);

if ($books) {
  foreach ($books as $b) {
    $total += $b->pvp;
    $tnigth++;
    if (isset($roomSite[$b->room_id])) {
      $site = $roomSite[$b->room_id];
      if (!isset($tSite[$site]))
        $tSite[$site] = 0;
      $tSite[$site] += $b->pvp;
      if (isset($aNight[$site])) {
        $aNight[$site]++;
      } //else  dd($b);
    } //else dd($b);
  }
  $total = $total;
}
//---------------------------------------------------------------------//
//--BEGIN: RATIOS -------//
/**
 * Auxiliar functions to view
 */
function prADR($v){
  return $v['n']>0 ? moneda($v['p']/$v['n']) : moneda($v['p']);
}
function prOcup($disp,$days,$v){
  $ocup  = $disp*$days;
  $night =  $v['n'];
  return ($night > 0) ? round(($night / $ocup)*100) : 0;
}
//--END: RATIOS -------//
?>
<div class="row">
  <div class="col-md-8">
    <div class="table-responsive">
    <table class="table table-summary" style="max-width:940px">
      <td>Total: {{moneda($total)}}</td>
      <?php
      foreach ($aSites as $k => $v) {
        echo '<td>' . $aSites[$k] . ': ';
        if (isset($tSite[$k]))
          echo moneda($tSite[$k]) . '</td>';
        else
          echo '0 </td>';
      }
      ?>
    </table>
</div>
<div class="box">
  <div class="table-responsive">
    <table class="table">
      <th class="btn_ratio ratio0" data-k="0">Todos</th>
      @foreach($aSites as $k=>$v)
      <th class="btn_ratio ratio{{$k}}" data-k="{{$k}}">{{$v}}</th>
      @endforeach
    </table>
  </div>
  @foreach($aRatios as $k=>$v)
  <div class="ratios ratio_{{$k}} table-responsive">
    <table class="table">
      <tr class="thead">
        <th class="static" style="background-color: #fafafa;height: 36px;"></th>
        <td class="first-col"></td>
        <th>Total</th>
        @foreach ($months as $k2=>$v2)
        <th>{{$v2}}</th>
        @endforeach
      </tr>
      <tr>
        <th class="static">Ventas</th>
        <td class="first-col"></td>
        <td>{{moneda($v[0]['p'])}}</td>
        @foreach ($months as $k2=>$v2)
        <td>{{moneda($v[$k2]['p'])}}</td>
        @endforeach
      </tr>
      <tr>
        <th class="static">Noches</th>
        <td class="first-col"></td>
        <td>{{$v[0]['n']}}</td>
        @foreach ($months as $k2=>$v2)
        <td>{{$v[$k2]['n']}}</td>
        @endforeach
      </tr>
      <tr>
        <th class="static">Ocupación</th>
        <td class="first-col"></td>
        <td>{{prOcup($disp[$k],$mDays[0],$v[0])}}%</td>
        @foreach ($months as $k2=>$v2)
        <td>{{prOcup($disp[$k],$mDays[$k2],$v[$k2])}}%</td>
        @endforeach
      </tr>
      <tr>
        <th class="static">ADR</th>
        <td class="first-col"></td>
        <td>{{prADR($v[0])}}</td>
        @foreach ($months as $k2=>$v2)
        <td>{{prADR($v[$k2])}}</td>
        @endforeach
      </tr>
      <tr>
        <th class="static">REV PAV</th>
        <td class="first-col"></td>
        <td>--</td>
        @foreach ($months as $k2=>$v2)
        <td>--</td>
        @endforeach
      </tr>
      <tr>
        <th class="static">GOpPar</th>
        <td class="first-col"></td>
        <td>-</td>
        @foreach ($months as $k2=>$v2)
        <td>-</td>
        @endforeach
      </tr>
    </table>
  </div>
  @endforeach
</div>
</div>


  <div class="col-md-4">
    <div>
      <h3>Indicaciones de Ocupación</h3>
      @include('backend.revenue.dashboard._tableSummaryBoxes')
    </div>

    @foreach($aNight as $k=>$v)
    @if($k>0 && $k != 4)
    <div class="dispPKI">
      <h5>{{$aSites[$k]}}</h5>
      <?php
      $ocup = $disp[$k] * $yDays;
      $perc = ($v > 0) ? $v / $ocup : 0;
      ?>
      @include('backend.blocks.arcChar',['perc'=>$perc]);
      <div style="margin-top: -16px;">Ocupación</div>
    </div>
    @endif
    @endforeach 

  </div>
</div>
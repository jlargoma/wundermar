<?php
//--BEGIN: Resumen -------//
$tSite = $aNight = $disp = [];
foreach ($aSites as $k => $v) {
  $aNight[$k] = $aRatios[$k][0]['n'];
  $disp[$k] = \App\Rooms::availSite($k);
}
$disp[0] = array_sum($disp);
$tDays = $mDays[0];
//---------------------------------------------------------------------//
//--BEGIN: RATIOS -------//
/**
 * Auxiliar functions to view
 */
function prADR_3($v, $k1, $k2) {
  return $v[$k1] > 0 ? moneda($v[$k2] / $v[$k1]) : moneda($v[$k2]);
}

function prOcup_3($disp, $days, $night) {
  $ocup = $disp * $days;
  return ($night > 0) ? round(($night / $ocup) * 100) : 0;
}

//--END: RATIOS -------//
if (isset($comparativaAnual[$year]))
  $AUXsummay = $comparativaAnual[$year];
else $AUXsummay = [0=>0];



$totalesComp = ['vtas'=>0,'nigths'=>0,'pvp'=>0];
foreach($comparativaAnual as $k=>$v){
  $totalesComp['vtas'] += $v[0];
  $totalesComp['nigths'] += isset($v['nigths']) ? $v['nigths'] : 0;
  $totalesComp['pvp'] += isset($v['pvp']) ? $v['pvp'] : 0;
}
?>

<div class="row" id="comparativaAnual">
  <div class="col-md-8">
    <div class="table-responsive">
      <table class="table table-summary" style="max-width:940px">
        <td>Total: {{moneda($AUXsummay[0])}}</td>
        @foreach($aSites as $k2=>$v2)
          <td>
            <?php 
            echo $v2.': ';
            echo isset($AUXsummay[$k2]) ? moneda($AUXsummay[$k2]) : 0;
            ?>
          </td>
        @endforeach
        <td>
            <?php 
            echo 'Otros: ';
            echo isset($AUXsummay[99]) ? moneda($AUXsummay[99]) : 0;
            ?>
          </td>
      </table>
    </div>
    <div class="box">
      <div class="table-responsive">
        <table class="table">
          <th class="btn_ratio_comp ratio_comp0" data-k="0">Todos</th>
          @foreach($aSites as $k=>$v)
          <th class="btn_ratio_comp ratio_comp{{$k}}" data-k="{{$k}}">{{$v}}</th>
          @endforeach
        </table>
      </div>
      @foreach($aRatios as $k=>$v)
      <div class="ratio_comps_comp ratio_comp_{{$k}} table-responsive">
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
            <td>{{prOcup_3($disp[$k],$mDays[0],$v[0]['n'])}}%</td>
            @foreach ($months as $k2=>$v2)
            <td>{{prOcup_3($disp[$k],$mDays[$k2],$v[$k2]['n'])}}%</td>
            @endforeach
          </tr>
          <tr>
            <th class="static">ADR</th>
            <td class="first-col"></td>
            <td>{{prADR_3($v[0],'n','p')}}</td>
            @foreach ($months as $k2=>$v2)
            <td>{{prADR_3($v[$k2],'n','p')}}</td>
            @endforeach
          </tr>
          <tr>
            <th class="static">ADR LAB</th>
            <td class="first-col"></td>
            <td>{{prADR_3($v[0],'c_s','t_s')}}</td>
            @foreach ($months as $k2=>$v2)
            <td>{{prADR_3($v[$k2],'c_s','t_s')}}</td>
            @endforeach
          </tr>
          <tr>
            <th class="static">ADR FIN</th>
            <td class="first-col"></td>
            <td>{{prADR_3($v[0],'c_f','t_f')}}</td>
            @foreach ($months as $k2=>$v2)
            <td>{{prADR_3($v[$k2],'c_f','t_f')}}</td>
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
    <div class="boxChar"><div class="contentChar">
    <canvas id="barRatioComp" width="500" height="150"></canvas>
      </div></div>
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

<?php
$comparativaAnualTotal = [0=>0];
foreach($aSites as $k2=>$v2) $comparativaAnualTotal[$k2] = 0;
foreach($comparativaAnual as $k=>$v){
  $comparativaAnualTotal[0] += $v[0];
  foreach($aSites as $k2=>$v2) $comparativaAnualTotal[$k2] += $v[$k2];
}
?>
<div class="row" id="comparativaAnuales" style="display:none;">
  <div class="col-md-8">
    <div class="table-responsive">
      <table class="table table-summary" style="max-width:940px">
      <thead>
        <tr class="thead">
          <td>Año</td>
          <td>Total <br>{{moneda($comparativaAnualTotal[0])}}</td>
          @foreach($aSites as $k2=>$v2)
          <td>{{$v2}} <br>{{moneda($comparativaAnualTotal[$k2])}}</td>
          @endforeach
        </tr>
      </thead>
        @foreach($comparativaAnual as $k=>$v)
        <tr>
          <th>{{$k}}</th>
          <td>{{moneda($v[0])}}</td>
          @foreach($aSites as $k2=>$v2)
          <td>{{moneda($v[$k2])}}</td>
          @endforeach
        </tr>
        @endforeach
      </table>
    </div>
    
    <div class="box">
      <div class="table-responsive">
        <table class="table">
          <th class="btn_ratio_comp ratio_comp0" data-k="0">Todos</th>
          @foreach($aSites as $k=>$v)
          <th class="btn_ratio_comp ratio_comp{{$k}}" data-k="{{$k}}">{{$v}}</th>
          @endforeach
        </table>
      </div>
      @foreach($aRatios as $k=>$v)
      <div class="ratio_comps_comp ratio_comp_{{$k}} table-responsive">
        <table class="table">
          <tr class="thead">
            <th class="static" style="background-color: #fafafa;height: 36px;"></th>
            <td class="first-col"></td>
            <th>Total</th>
            @foreach ($months as $k2=>$v2)
            <th>{{$v2}}</th>
            @endforeach
          </tr>
          @foreach($comparativaAnual as $k2=>$v2)
          <tr>
            <th class="static">Ventas {{$k2}}</th>
            <td class="first-col"></td>
            @foreach($v2['months'][$k] as $k3=>$v3)
            <td>{{moneda($v3)}}</td>
            @endforeach
          </tr>
          @endforeach
        </table>
      </div>
      @endforeach
    </div>

  </div>

  <div class="col-md-4">
    <h3>Indicaciones de Ocupación</h3>
      
    <div class="table-responsive">
      <table class="table">
        <tr class="thead">
          <td></td>
          <th>Total</th>
          @foreach($comparativaAnual as $k=>$v)
          <th>{{$k}}</th>
          @endforeach
        </tr>
        <tr>
          <th>Ventas</th>
          <td>{{moneda($totalesComp['vtas'])}}</td>
          @foreach($comparativaAnual as $k=>$v)
          <td>{{moneda($v[0])}}</td>
          @endforeach
        </tr>
        <tr>
          <th>Noches</th>
          <td>{{$totalesComp['nigths']}}</td>
          @foreach($comparativaAnual as $k=>$v)
          <td>{{$v['nigths']}}</td>
          @endforeach
        </tr>
        <tr>
          <th>Ocupación</th>
          <td>{{prOcup_3($disp[0],$tDays,$totalesComp['nigths'])}}%</td>
          @foreach($comparativaAnual as $k=>$v)
          <td>{{prOcup_3($disp[0],$tDays,$v['nigths'])}}%</td>
          @endforeach
        </tr>
        <tr>
          <th>ADR</th>
          <td>{{prADR_3($totalesComp,'nigths','vtas')}}</td>
          @foreach($comparativaAnual as $k=>$v)
          <td>{{prADR_3($v,'nigths','pvp')}}</td>
          @endforeach
        </tr>
      </table>
    </div>
        <canvas id="barRatioCompYear" width="400" height="250"></canvas>
  </div>
</div>
<script type="text/javascript">

  $(document).ready(function () {

    //----------------------------------------------------------------//
    $('.ratio_comps_comp').hide();
    $('.ratio_comp_0').show();
    $('.ratio_comp0').addClass('active');
    $('.btn_ratio_comp').on('click', function () {
      $('.btn_ratio_comp').removeClass('active');
      $('.ratio_comps_comp').hide();
      var k = $(this).data("k");
      $('.ratio_comp_' + k).show();
      $('.ratio_comp' + k).addClass('active');
    });
 /* GRAFICA */
 
 
    var data = {
      labels: [@foreach($months as $v_m) "{{$v_m}}", @endforeach],
      datasets: [
        @foreach($aRatios as $k_r=>$v_r)
        {
          label: "<?php echo (isset($aSites[$k_r])) ? $aSites[$k_r] : 'TODOS'; ?>",
          borderColor:"{{printColor($k_r)}}",
          fill: false,
          data: [
            @foreach ($months as $k2=>$v2) {{round($v_r[$k2]['p'])}}, @endforeach
            ],
        },
        @endforeach
      ]
    };
    var barBalance = new Chart('barRatioComp', {
    type: 'line',
            data: data,
    });

    var data = {
      labels: [@foreach($months as $v_m) "{{$v_m}}", @endforeach],
      datasets: [
        @foreach($comparativaAnual as $k_r=>$v_r)
        {
          label: "{{$k_r}}",
          borderColor:"{{printColor($k_r)}}",
          fill: false,
          data: [
            @foreach ($v_r['months'][0] as $k2=>$v2) 
              @if($k2>0)
              {{round($v2)}}, 
              @endif
            @endforeach
            ],
        },
        @endforeach
      ]
    };
    var barBalance = new Chart('barRatioCompYear', {
    type: 'line',
            data: data,
    });














  });

</script>

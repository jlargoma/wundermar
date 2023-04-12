<?php
$auxMonts = [];
foreach ($mDays as $k => $v)
  $auxMonts[$k] = 0;
$grafPresupSite = [0=>['ing' => $auxMonts, 'gastos' => $auxMonts, 'result' => $auxMonts]];
$monthName = getMonthsSpanish($month, false);
foreach ($aSites as $k => $v) {
  $aNight[$k] = 0;
  $grafPresupSite[$k] = ['ing' => $auxMonts, 'gastos' => $auxMonts, 'result' => $auxMonts];
  $disp[$k] = \App\Rooms::availSite($k);
}
$disp[0] = array_sum($disp);

/**
 * Auxiliar functions to view
 */
function prADR2($v) {
  return $v['n'] > 0 ? ($v['p'] / $v['n']) : ($v['p']);
}

function promADR($p, $n) {
  return $n > 0 ? moneda($p / $n) : moneda($p);
}

function prOcup2($disp, $days, $night) {
  $ocup = $disp * $days;
  return ($night > 0) ? round(($night / $ocup) * 100) : 0;
}
$meses = getMonthsSpanish(null, true, true);
unset($meses[0]);
?>
<div class="table-responsive">
 <table class="table">
   <th class="btn_presup presup_0" data-k="0" data-month="{{$month}}">Todos&nbsp;&nbsp;<i class="fa fa-refresh"></i></th>
  @foreach($aSites as $k=>$v)
  <th class="btn_presup presup_{{$k}}" data-k="{{$k}}">{{$v}}</th>
  @endforeach
</table>
</div>

<?php 
$aSites2 = [0 => 'Todos'];
foreach ($aSites as $k=>$v) $aSites2[$k] = $v;
?>
@foreach ($aSites2 as $k=>$v)
<div class="presupuesto presup_{{$k}}" >
  <div class="row">
    <div class="col-md-4 table-responsive ">
      <table class="table">
        <tr><th colspan="3" style="font-size: 1.4em;background-color: #ed2402;color: #FFF;">COSTES FIJOS EDIFICIO</th></tr>
        <tr class="grey">
          <td>CONCEPTO</td>
          <td></td>
        </tr>
        <?php
        $cftM = 0;
        foreach ($FCItems as $key => $concept):
          if ($k==0): //Todos los sitios
            $v1 = 0;
          foreach ($fixCosts as $S=>$item){
            if (isset($item[$key])) {
              $val = $item[$key];
              if(isset($val[$month])){
                $v1 += $val[$month];
                $cftM += $val[$month];
              }
              foreach ($val as $m=>$v) {
                //BEGIN: datos para los graficos
                $grafPresupSite[0]['gastos'][$m] += $v;
              }
            }
          }
          ?>
          <tr>
            <td>{{$concept}}</td>
            <td class="text-right Todofixcost {{$key}}">{{numero($v1)}}€</td>
          </tr>
          <?php
          else:
          $v1 = '';
          if (isset($fixCosts[$k][$key])) {
            $val = $fixCosts[$k][$key];
            if(isset($val[$month])){
              $v1 = $val[$month];
              $cftM += $val[$month];
            }
            foreach ($val as $m=>$v) {
              //BEGIN: datos para los graficos
              $grafPresupSite[$k]['gastos'][$m] += $v;
            }
          }
          ?>
          <tr>
            <td>{{$concept}}</td>
            @if($month == 0)
            <td class="text-right">{{numero($v1)}}€</td>
            @else
            <td class="text-right"><input class="fixcost" data-k="{{$key}}" data-site="{{$k}}" data-y="{{$year}}"  data-m="{{$month}}" value="{{$v1}}">€</td>
            @endif
          </tr>
          <?php
          endif;
        endforeach;
        ?>

        <tr class="tr_footer">
          <th>COSTE FIJO</th>
          <th class="text-right"><span id="tFC{{$k.'_'.$year.$month}}">{{numero($cftM)}}</span>€</th>
        </tr>
      </table>
      <div class="text-center">
      <button type="button" class="btn btn-load" onclick="load_costesFijos({{$year}})">carga masiva</button>
      </div>
    </div>
    <div class="col-md-4 table-responsive ">
      <?php
      $aux_ratios1 = $aRatios[$k][$month];
      $aux_ratios2 = $aRatios[$k][0];

      //BEGIN: datos para los graficos
      foreach ($aRatios[$k] as $m2 => $v2)
        $grafPresupSite[$k]['ing'][$m2] += round($v2['p']);
      //END: datos para los graficos

      $ingrT_M = $aux_ratios1['p'];
      $ingrT_Y = $aux_ratios2['p'];
      ?>
      <table class="table">
        <tr><th colspan="3" style="font-size: 1.4em;background-color: #92d050;color: #FFF;">INGRESOS EDIFICIO</th></tr>
        <tr class="grey">
          <td>CONCEPTO</td>
          <td>{{$monthName}}</td>
        </tr>
        <tr>
          <td>REVENUE</td>
          <td class="text-right">{{moneda($aux_ratios1['p'])}}</td>
        </tr>
        <tr>
          <td>Nº Hab Vendidas</td>
          <td class="text-right">{{$bookingCount[$k][$month]}}</td>
        </tr>
        <tr>
          <td>Nº Noches</td>
          <td class="text-right">{{($aux_ratios1['n'])}}</td>
        </tr>
        <tr>
          <td>% Ocupación</td>
          <td class="text-right">{{prOcup2($disp[$k],$mDays[$month],$aux_ratios1['n'])}}%</td>
        </tr>
        <tr>
          <td>ADR (precio medio)</td>
          <td class="text-right">{{promADR($aux_ratios1['p'],$aux_ratios1['n'])}}</td>
        </tr>
        <tr>
          <td>REV PAR</td>
          <td class="text-right"></td>
        </tr>
        <tr>
          <td>GO PAR</td>
          <td class="text-right"></td>
        </tr>
        <tr class="tr_footer">
          <th>INGRESOS</th>
          <th class="text-right">{{moneda($ingrT_M)}}</th>
        </tr>
      </table>
    </div>
    <div class="col-md-4 table-responsive ">
      <?php
      $yearMin = $year - 2000;
      if ($month == 0){
        $limpM = array_sum($monthlyLimp[$k]);
        $otaCommM = array_sum($monthlyOta[$k]);
        $ComisTPV_M = array_sum($comisionesTPV[$k]);
      } else {
        
        $month2 = $month < 10 ? '0' . $month : $month;
        $limpM = isset($monthlyLimp[$k]["$month2-$yearMin"]) ? round($monthlyLimp[$k]["$month2-$yearMin"]) : 0;
        $otaCommM = isset($monthlyOta[$k]["$month2-$yearMin"]) ? $monthlyOta[$k]["$month2-$yearMin"] : 0;
        $ComisTPV_M = isset($comisionesTPV[$k][$month]) ? $comisionesTPV[$k][$month] : 0;
      }
      //BEGIN: datos para los graficos
      foreach ($mDays as $m2 => $v2) {
        $m3 = $m2 < 10 ? '0' . $m2 : $m2;
        $aux1 = isset($monthlyLimp[$k]["$m3-$yearMin"]) ? $monthlyLimp[$k]["$m3-$yearMin"] : 0;
        $aux2 = isset($monthlyOta[$k]["$m3-$yearMin"]) ? $monthlyOta[$k]["$m3-$yearMin"] : 0;
        $aux3 = isset($comisionesTPV[$k][$m2]) ? $comisionesTPV[$k][$m2] : 0;

        $grafPresupSite[$k]['result'][$m2] = $grafPresupSite[$k]['ing'][$m2] - $grafPresupSite[$k]['gastos'][$m2] - $aux1 - $aux2;
      }


      $rentabTotM = $grafPresupSite[$k]['result'][$month];
      $rentabTotY = array_sum($grafPresupSite[$k]['result']);
      //END: datos para los graficos
      $totalCostM = $otaCommM + $limpM + $ComisTPV_M + $cftM;
      if ($totalCostM == 0)
        $totalCostM = 1;
      ?>

      <table class="table">
        <tr><th colspan="5" style="font-size: 1.4em;background-color: #50b0f0;color: #FFF;">RENTABILIDAD EDIFICIO</th></tr>
        <tr class="grey">
          <td>CONCEPTO</td>
          <td>{{$monthName}}</td>
          <td class="text-right">%</td>
        </tr>
        <tr>
          <td>INGRESOS</td>
          <td class="text-right">{{moneda($ingrT_M)}}</td>
          <td class="text-right">-</td>
        </tr>
        <tr>
          <td>COSTES FIJOS</td>
          <td class="text-right">{{moneda($cftM)}}</td>
          <td class="text-right"><?php echo ($cftM > 0) ? round($cftM / $totalCostM * 100) : 0; ?>%</td>
        </tr>
        <tr>
          <td>LIMPIEZA / LAVANDERÍA</td>
          <td class="text-right">{{moneda($limpM)}}</td>
          <td class="text-right"><?php echo ($limpM > 0) ? round($limpM / $totalCostM * 100) : 0; ?>%</td>
        </tr>
        <tr>
          <td>COMISIONES OTA's</td>
          <td class="text-right">{{moneda($otaCommM)}}</td>
          <td class="text-right"><?php echo ($otaCommM > 0) ? round($otaCommM / $totalCostM * 100) : 0; ?>%</td>
        </tr>
        <tr>
          <td>COMISIONES TPV</td>
          <td class="text-right">{{moneda($ComisTPV_M)}}</td>
          <td class="text-right"><?php echo ($ComisTPV_M > 0) ? round($ComisTPV_M / $totalCostM * 100) : 0; ?>%</td>
        </tr>
        <tr class="tr_footer">
          <th>RENTABILDAD</th>
          <th class="text-right">{{moneda($rentabTotM)}}</th>
          <th><?php echo ($rentabTotM > 0) ? round( (1 - ($totalCostM/$ingrT_M)) * 100)  : 0; ?>%</th>
        </tr>
      </table>
    </div>
  </div>
  <div class="boxChar"><div class="contentChar">
  <canvas id="barRentabilidad_{{$k}}" style="width: 100%; height: 250px;"></canvas>
    </div></div>
</div>

@endforeach

<div class="modal fade slide-up in" id="cargaCostesFijos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute;right: 0;z-index: 3;">
              <i class="fa fa-close"></i>
            </button>
          </div>
          <div class="block">
            <div class="row">
              <h2 class="col-md-5">COSTES FIJOS EDIFICIO</h2>
              <div class="col-md-3">
                <select class="form-control" id="year_costesFijosEdificio">
                  @for($i=$year-5;$i<$year+2;$i++) 
                  <option value="{{$i}}" <?php echo ($i==$year) ? 'selected' : ''; ?>>{{$i}}</option>
                  @endfor
                </select>
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-success" id="copyConsteFijoNextYear">Copiar todo al <span>{{$year+1}}</span></button>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table">
                @foreach($aSites as $k=>$v)
                <th class="btn_presup presup_{{$k}}" data-k="{{$k}}">{{$v}}</th>
                @endforeach
              </table>
            </div>
            <div id="FixedcostsAnual"></div>
            <div class="text-center">
              <button onclick="reloadPresup()" class="btn btn-load">Refrescar Pantalla</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .presupuesto input.fixcost,
  .presupuesto input.fixcostMdl {
    padding: 0px;
    width: 50px;
    min-width: 40px !important;
    text-align: right;
    border: none;
    font-size: 15px;
  }
  #blockPresup .table {
        border: 1px solid #c1c1c1;
  }
  #blockPresup .table tbody tr td {
    font-size: 15px;
    padding: 6px 3px !important;
    text-align: left !important;
  }
  #blockPresup .table tbody tr td.text-right {
    text-align: right !important;
        white-space: nowrap;
    border-left: 1px solid #cacaca;
  }
  #blockPresup .table tbody tr td.tcenter {
    text-align: center !important;
  }
  .table tbody tr.grey td {
    background-color: #d9d9d9;
    color: #000;
    font-weight: 800;
  }
  tr.borders td {
    border: 1px solid #000 !important;
    padding: 0 7px 0 0 !important;
  }
  tr.tr_footer th {
    font-size: 1.15em;
    text-align: right !important;
    color: #000;
  }
  #cargaCostesFijos .block {
    padding: 30px 11px;
}
button.btn.btn-load {
    background-color: #6d5cae;
    color: #FFF;
}
#cargaCostesFijos .table tbody tr td {
    font-size: 15px;
}
#FixedcostsAnual .table tbody tr td.text-right {
  max-width: 80px;
  width: 80px;
}
.presupuesto input.fixcostMdl {
   width: 100%;
   cursor: pointer;
}
</style>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<script>
  
  
  function reloadPresup(){
    $('#cargaCostesFijos').modal('toggle');
    setTimeout(function(){$('.presup_0').trigger('click');},650);
    
  };
  
  function load_costesFijos(year,selSite=1){
    
    $('#FixedcostsAnual').html('').load('/admin/revenue/getFixedcostsAnual/'+year,function(){
      $('.presupuesto').hide();
      $('.btn_presup').removeClass('active');
      $('.presup_'+selSite).show();
      $('.presup_'+selSite).addClass('active');
    });
    $('#cargaCostesFijos').modal();
  }
  
  $('#year_costesFijosEdificio').on('change', function(){
    var year = parseInt($(this).val());
    $('#copyConsteFijoNextYear').find('span').text(year+1);
    load_costesFijos(year,PresupSelect);
  });
  
  $('#copyConsteFijoNextYear').on('click', function(){
    var year = parseInt($('#year_costesFijosEdificio').val());
    var site = $('#cargaCostesFijos .btn_presup.active').data('k');
    year++;
    if (confirm("Copiar todos los valores al año "+year+"? (se pisaran los valores actuales)")){
      $.post('/admin/revenue/copyFixedcostsAnualTo/'+year+'/'+site,{_token: "{{csrf_token()}}"})
        .done(function (resp) {
          if (resp == 'OK') {
            window.show_notif('Registro modificado', 'success', '');
            $('#year_costesFijosEdificio').val(year);
            load_costesFijos(year,site);
          } else {
            window.show_notif(resp, 'danger', '');
          }
        });
        
      
    }
  });

$('.fixcost').on('click', function(){
    
    $('.fixcost').each(function( index ) {
      var obj = $(this);
      if (obj.val() == ''){
        obj.val(obj.data('old'));
      }
    });
    var obj = $(this);
    obj.data('old',obj.val());
    obj.val('');
  });

  function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
  }

  $('.fixcost').on('change', function(){
    var obj = $(this);
    var value = obj.val();
    var key  = obj.data('k');
    var site = obj.data('site');
    var y = obj.data('y');
    var m = obj.data('m');
    if (value == ''){
      obj.val(obj.data('old'));
      return ;
    }
    var data = {
      val: obj.val(),
      _token: "{{csrf_token()}}",
      site: site,
      key: key,
      y: y,
      m: m,
    }
    var ktotal = '#tFC' + site + '_' + y + '' + m;
    $.post("/admin/revenue/upd-fixedcosts", data).done(function (resp) {
      if (resp.status == 'OK') {
        window.show_notif('Registro modificado', 'success', '');
        
        $(ktotal).text(formatNumber(resp.totam_mensual));
        
        //---------------------------------------------------------//
        var tCol = 0;
        $('.fixCol'+site+y+m).each(function( index ) {
          if ($(this).val()) tCol += parseInt($(this).val());
        });
        $('#mdlFC'+site+'_'+m+'_'+y).text(tCol+' €');
        //---------------------------------------------------------//
        var tCol = 0;
        $('.fixcostMdl.'+site+key).each(function( index ) {
          console.log($(this).val());
          if ($(this).val())  tCol += parseInt($(this).val());
        });
        $('.fixColTtalMdl.'+site+'.'+key).text(tCol+' €');
        $('.fixColTtalMdl.'+site+'.'+key).data('v',tCol);
        //---------------------------------------------------------//
        
      } else {
        window.show_notif(resp, 'danger', '');
      }
    });
  });
  
    $('.presupuesto').hide();
    $('.presup_0').show();
    $('.presup_0').addClass('active');
    var PresupSelect = 1;
    $('.btn_presup').on('click',function(){
      PresupSelect = $(this).data("k");
      if (PresupSelect > 0){
        $('.btn_presup').removeClass('active');
        $('.presupuesto').hide();
        $('.presup_'+PresupSelect).show();
        $('.btn_presup.presup_'+PresupSelect).addClass('active');
      }
    });
   
    
    
    
@foreach ($aSites2 as $k => $v)
    /* GRAFICA INGRESOS/GASTOS */
    var data = {
    labels: [
            @foreach ($meses as $m2) "{{$m2}}", @endforeach
    ],
            datasets: [
            {
            label: "Ingresos",
                    backgroundColor: 'rgb(67, 160, 71)',
                    data: [
                            @foreach ($mDays as $m2 => $v2)
                            @if ($m2 > 0) "{{round($grafPresupSite[$k]['ing'][$m2])}}", @endif
                            @endforeach
                    ],
            },
            {
            label: "Gastos",
                    backgroundColor: 'rgb(237, 36, 2)',
                    data: [
                            @foreach ($mDays as $m2 => $v2)
                            @if ($m2 > 0) "{{round($grafPresupSite[$k]['gastos'][$m2])}}", @endif
                            @endforeach
                    ],
            },
            {
            label: "Rentabilidad",
                    backgroundColor: 'rgb(80, 176, 240)',
                    data: [
                            @foreach ($mDays as $m2 => $v2)
                            @if ($m2 > 0) "{{round($grafPresupSite[$k]['result'][$m2])}}", @endif
                            @endforeach
                    ],
            },
            ]
    };
    var barBalance = new Chart('barRentabilidad_{{$k}}', {
      type: 'bar',
      data: data,
    });
@endforeach











</script>






<input type="hidden" id="yoiYear" value="{{$year}}">
<input type="hidden" id="yoiMonth" value="{{$month}}">

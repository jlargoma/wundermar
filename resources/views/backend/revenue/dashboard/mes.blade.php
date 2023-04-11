<?php 
$total = 0; 
$tSite = [];
$pki = ['pvp'=>0,'rva'=>0,'nigth'=>0,'vProp'=>0,'vAgen'=>0];
$aPKI = [0=>$pki];
$disp = [0=>0];
foreach ($aSites as $k=>$v){
  $aPKI[$k] = $pki;
  $disp[$k] = \App\Rooms::availSite($k);
}

$disp[0] = array_sum($disp);

if($books){
  foreach ($books as $b){
    $total += $b->pvp;
    if (isset($roomSite[$b->room_id])){
      $site = $roomSite[$b->room_id];
      if (!isset($tSite[$site])) $tSite[$site] = 0;
      $tSite[$site] += $b->pvp;
      if (isset($aPKI[$site])){
        $aPKI[$site]['pvp'] += $b->pvp;
        if ($b->agency>0) $aPKI[$site]['vAgen'] += $b->pvp;
        else $aPKI[$site]['vProp'] += $b->pvp;
      } //else dd($b);
      
    } //else dd($b);
  }

  foreach ($nights as $site=>$nights){
    $aPKI[$site]['nigth'] = $nights;
    $aPKI[0]['nigth'] += $nights;
  }
  
  foreach ($rvas as $site=>$t){
    $aPKI[$site]['rva'] = $t;
    $aPKI[0]['rva'] += $t;
  }
  
  $aPKI[0]['pvp'] = $total;
  foreach ($aSites as $k=>$v){
    $aPKI[0]['vAgen'] += $aPKI[$k]['vAgen'];
    $aPKI[0]['vProp'] += $aPKI[$k]['vProp'];
  }
   
}
?>
<div class="table-responsive">
  <table class="tableMonths" >
    <tr>
      <td data-k="0" class="sm <?php if($month == 0) echo 'active' ?>" >AÑO</td>
      @foreach($months as $k=>$v)
      <td data-k="{{$k}}" class="sm <?php if($month == $k) echo 'active' ?> ">{{$v}}</td>
      @endforeach
    </tr>
  </table>  
</div>
<div class="table-responsive">
<table class="table table-summary" style="max-width:940px">
    <td>Total: {{moneda($total)}}</td>
    <?php 
    foreach ($aSites as $k=>$v){
      echo '<td>'.$aSites[$k].': ';
      if (isset($tSite[$k])){
        echo moneda($tSite[$k]);
        if ($total>0){
          $auxPerc = round(($tSite[$k]/$total)*100);
          echo ' <span class="perc">'.$auxPerc.'%<span>';
        }
      }
      else echo 0;
      echo '</td>';
    }
    ?>
  </table>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="box ">
    <table class="table">
      <tr>
        <th>Mes</th>
        <th>Noches</th>
        <th>ADR LAB</th>
        <th>ADR FIND</th>
        <th>PVP RVAS</th>
      </tr>
      <tr>
        <td class="td-h1">
          <?php 
          echo $month>0 ? $months[$month] : 'Todos'; 
          $auxADR = $ADR_finde[0];
          ?>
        </td>
        <td class="td-h1">{{$aPKI[0]['nigth']}}</td>
        <td class="td-h1"><?php echo ($auxADR['c_s']>0) ? moneda($auxADR['t_s']/$auxADR['c_s']) : '-'; ?></td>
        <td class="td-h1"><?php echo ($auxADR['c_f']>0) ? moneda($auxADR['t_f']/$auxADR['c_f']) : '-'; ?></td>
        <td class="td-h1">{{moneda($aPKI[0]['pvp'])}}</td>
      </tr>
    </table>
            
  </div>
     @foreach($aPKI as $k=>$v)
      @if($k>0 && $k != 4)
      <div class="dispPKI">
        <h5><?php
          echo isset($aSites[$k]) ? $aSites[$k] : 'Todos';
          $auxADR = $ADR_finde[$k];
        ?></h5>
        <table class="table">
          <tr>
            <th>PVP<br/>{{moneda($v['pvp'])}}</th>
            <th>Noches<br/>{{$v['nigth']}}</th>
            <th>ADR LAB<br/><?php echo ($auxADR['c_s']>0) ? moneda($auxADR['t_s']/$auxADR['c_s']) : '-'; ?></th>
            <th>ADR FIND<br/><?php echo ($auxADR['c_f']>0) ? moneda($auxADR['t_f']/$auxADR['c_f']) : '-'; ?></th>
          </tr>
        </table>
        <?php
        $ocup = $disp[$k]*$days;
        $perc = ($v['nigth']>0) ? $v['nigth']/$ocup : 0;
        ?>
        @include('backend.blocks.arcChar',['perc'=>$perc]);
        <div style="margin-top: -16px;">Ocupación</div>
    </div>
      @endif
  @endforeach 
   
  </div>
<div class="col-md-6">
  <div class="box">
    <div class=" table-responsive">
  <table class="table">
    <th>KPI - Indicador Clave</th>
    @foreach($aPKI as $k=>$v)
    @if(isset($aSites[$k]) || $k==0)
    <th class="btn_kpi bkpi{{$k}}" data-k="{{$k}}">
    <?php
      echo isset($aSites[$k]) ? $aSites[$k] : 'Todos';
    ?>
    </th>
    @endif
     @endforeach
  </table>
      </div>
  @foreach($aPKI as $k=>$v)
  <div class="kpis kpi_{{$k}}">
  <table class="table">
    <tr>
      <th>Total Vnts <?php echo $month>0 ? $months[$month] : 'Todos'; ?></th>
      <th>Total Reservas</th>
      <th>ADR LAB</th>
      <th>ADR FIND</th>
      <th>Ocupación</th>
    </tr>
    <tr>
      <?php $auxADR = $ADR_finde[$k]; ?>
      <td  class="td-h1">{{moneda($v['pvp'])}}</td>
      <td  class="td-h1">{{$v['rva']}}</td>
      <td class="td-h1">
        <?php echo ($auxADR['c_s']>0) ? moneda($auxADR['t_s']/$auxADR['c_s']) : '-'; ?></td>
      <td class="td-h1">
        <?php echo ($auxADR['c_f']>0) ? moneda($auxADR['t_f']/$auxADR['c_f']) : '-'; ?></td>
      <td>
        <?php
        $ocup = $disp[$k]*$days;
        if ($v['nigth']>0):
          $perc = $v['nigth']/$ocup;
        ?>
        @include('backend.blocks.arcChar',['perc'=>$perc]);
        <?php
        else: echo '-';
        endif;
        ?>
      </td>
    </tr>
  </table>
    <table class="table">
    <tr>
      <th>Total Noches</th>
      <th>Estancia media</th>
      <th>Vnt Propia</th>
      <th>Vnt Agencia</th>
    </tr>
    <tr>
      <td class="td-h1">{{$v['nigth']}}</td>
      <td class="td-h1"> 
        <?php 
        if ($v['rva']>0)
          echo round($v['nigth']/$v['rva']);
        else echo '-';
        ?>
      </td>
      <td class="td-h1">
        <?php 
        $percent = ($v['pvp']>0) ? round(($v['vProp']/$v['pvp'])*100) : 0; 
        echo $percent.'%';
        ?>
      </td>
      <td class="td-h1">
        <?php 
        $percent = ($v['pvp']>0) ? 100-$percent : 0; 
        echo $percent.'%';
        ?>
      </td>
    </tr>
  </table>
    </div>
  @endforeach
</div>
  </div>
</div>
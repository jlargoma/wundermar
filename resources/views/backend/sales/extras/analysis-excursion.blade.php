<?php 
$totals = [
  'pvp'=>0,  
  'cost'=>0,  
  'comision'=>0,  
  'rent'=>0,  
];
?>
<h3>Análisis excursiones - <span>{{$month_name}}</span></h3>
<div class=" table-responsive">
  <table class="table table-resumen">
    <thead>
      <tr class="resume-head">
        <th>Nombre</th>
        <th>PVP</th>
        <th>COSTE</th>
        <th title="Comisión de venta">COMISION</th>
        <th title="Rentabilidad">RENT.</th>
        <th>%</th>
      </tr>
    </thead>
    <tbody>
      @foreach($excusion_months as $k=>$item)
      <tr>
        <td class="text-left">{{$item['name']}}</td>
        <td>{{moneda($item['pvp'],false)}}</td>
        <td>{{moneda($item['cost'],false)}}</td>
        <?php 
          $comision = $item['pvp']*0.15;
          $rent = $item['pvp']-$item['cost']-$comision;
          $percent_rent = 0;
          if ($item['pvp'] > 0){
            $percent_rent = round($rent/$item['pvp']*100);
          }
          $totals['pvp'] += $item['pvp'];
          $totals['cost'] += $item['cost'];
          $totals['comision'] += $comision;
          $totals['rent'] += $rent;
        ?>
        <td>{{moneda($comision,false)}}</td>
        <td>{{moneda($rent,false)}}</td>
        <td>{{$percent_rent}}%</td>
      </tr>
      @endforeach
      <tr class="totals">
        <td class="text-left">Total</td>
        <td>{{moneda($totals['pvp'],false)}}</td>
        <td>{{moneda($totals['cost'],false)}}</td>
        <td>{{moneda($totals['comision'],false)}}</td>
        <td>{{moneda($totals['rent'],false)}}</td>
        <td>
          <?php 
            echo ($totals['pvp']>0) ? round($totals['rent']/$totals['pvp']*100).'%' : '--';
          ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<?php 
$totals = [
  'pvp'=>0,  
  'cost'=>0,  
  'comision'=>0,  
];
?>

<div class="mt-1em">
  <h3>Análisis Vendedores - <span>{{$month_name}}</span></h3>
<div class=" table-responsive">
  <table class="table table-resumen">
    <thead>
      <tr class="resume-head">
        <th>Vendedor</th>
        <th>PVP</th>
        <th>COSTE</th>
        <th>%</th>
      </tr>
    </thead>
    <tbody>
      @foreach($monthsVdor as $k=>$item)
      <tr>
        <td class="text-left">{{$k}}</td>
        <td>{{moneda($item['pvp'],false)}}</td>
        <td>{{moneda($item['cost'],false)}}</td>
        <?php 
          $comision = $item['pvp']*0.15;
          $totals['pvp'] += $item['pvp'];
          $totals['cost'] += $item['cost'];
          $totals['comision'] += $comision;
        ?>
        <td>{{moneda($comision,false)}}</td>
      </tr>
      @endforeach
      <tr class="totals">
        <td class="text-left">Total</td>
        <td>{{moneda($totals['pvp'],false)}}</td>
        <td>{{moneda($totals['cost'],false)}}</td>
        <td>{{moneda($totals['comision'],false)}}</td>
      </tr>
    </tbody>
  </table>
</div>
  
</div>
 
<style>
  .table tbody tr.totals td{
    background-color: #51b1f7;
    font-weight: 600;
    color: #FFF;
  }
</style>
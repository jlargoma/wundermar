    
<?php
$tNigh = $tavail = $tPvp = 0;
$tDays = count($lstBySite[1]['days']);
foreach($allSites as $k=>$v):
    $tNigh += $lstBySite[$k]['tNigh'];
    $tavail += $lstBySite[$k]['avail'];
    $tPvp += $lstBySite[$k]['tPvp'];
endforeach;

?>
<div class="table-responsive ">
  <table class="table table-resumen summary">
    <thead>
      <tr class="resume-head">
        <th class="static"></th>
        <th class="first-col"></th>
        <th>TOTAL</th>
        @foreach($allSites as $k=>$v)
        <th>{{$v}}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="static text-left">Nº NOCHES</td>
        <td class="first-col"></td>
        <td>{{$tNigh}}</td>
        @foreach($allSites as $k=>$v)
        <td>{{$lstBySite[$k]['tNigh']}}</td>
        @endforeach
      </tr>
      <tr>
        <td class="static text-left">% OCUPACION</td>
        <td class="first-col"></td>
        <td>{{round($tNigh/($tavail*$tDays)*100)}}%</td>
        @foreach($allSites as $k=>$v)
        <td>
        <?php
            $aux = $lstBySite[$k]['tNigh'];
            $aux2 = $lstBySite[$k]['avail']*$tDays;
            if ($aux2<1) $aux2 = 1;
            echo round($aux/($aux2)*100).'%';
            ?>
        </td>
        @endforeach
      </tr>
      <tr>
        <td class="static text-left">ADR</td>
        <td class="first-col"></td>
        <td>@if($tNigh) {{moneda($tPvp/$tNigh)}} @endif</td>
        @foreach($allSites as $k=>$v)
        <td>
            <?php
            $aux = $lstBySite[$k]['tNigh'];
            if ($aux<1) echo moneda($lstBySite[$k]['tPvp']);
            else {
                echo moneda($lstBySite[$k]['tPvp'] / $aux);
            }
            ?>
         </td>
        @endforeach
      </tr>
      <tr>
        <td class="static text-left"><b>TOTAL PVP</b></td>
        <th class="first-col"></th>
        <th>{{moneda($tPvp)}}</th>
        @foreach($allSites as $k=>$v)
        <th>{{moneda($lstBySite[$k]['tPvp'])}}</th>
        @endforeach
      </tr>
    </tbody>
  </table>
</div>
<p><small>Reservas SIN contar bloqueos, ni OVERBOOKING</small></p>
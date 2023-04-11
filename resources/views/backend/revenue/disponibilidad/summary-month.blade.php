<div class="table-responsive ">
  <table class="table table-resumen summary-month">
    <thead>
      <tr class="resume-head">
        <th class="static">Ocupación</th>
        <th class="first-col"></th>
          @if($aLstDaysMin)
          @foreach($aLstDaysMin as $d=>$w)
          <th style="width: 20px !important;min-width: auto !important;">{{$w}}<br>
            <?php 
            $aux = explode('_', $d);
            echo $aux[0].' '.$mmonths[intVal($aux[1])]; ?>
          </th>
          @endforeach
          @endif
        </tr>
      </thead>
      <tbody>
       @foreach($allSites as $k=>$v)
        <tr>
            <td class="static">{{$v}}</td>
            <th class="first-col"></th>
            <?php
                $aux_day   = $lstBySite[$k]['days'];
                $aux_avail = $lstBySite[$k]['avail'];
                if ($aux_avail<1) $aux_avail = 1;
                foreach($aLstDaysMin as $d=>$w):
                    $class = 's-grey';
                    $libres = 100-ceil($aux_day[$d]/$aux_avail*100);
                    if ($libres==0) $class = 's-red';
                    if ($libres>0) $class = 's-orange';
                    if ($libres>40) $class = 's-yellow';
                    if ($libres>60) $class = 's-green';
//                    echo '<td class="'.$class.'">'.$libres.'-'.$aux_day[$d].'-'.$aux_avail.'</td>';
                    echo '<td class="'.$class.'">'.($aux_avail - $aux_day[$d]).'</td>';
                endforeach;
            ?>
        </tr>
        @endforeach
      </tbody>
    </table>
</div>
<div class="table-responsive ">
<table class="table summary-month">
    <tr>
      <th class="s-green">>60% Disponible</th>
      <th class="s-yellow ">60 - 40% Disponible</th>
      <th class="s-orange"><40% Disponible</th>
      <th class="s-red">0% Disponible</th>
    </tr>
  </table>
</div>

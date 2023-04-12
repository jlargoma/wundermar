<div class="row">
  <div class="col-md-9">
    <div class=" table-responsive" >
      <table class="table tIngrMes">
        <thead>
          <tr>
            <th class="text-center bg-complete text-white static">Apto</th>
            <th class="text-center bg-complete text-white first-col"></th>
            <th class="text-center bg-complete text-white">total<br/>
              <?php echo number_format($t_all_rooms, 0, ',', '.'); ?>€
            </th>
            <th class="text-center bg-complete text-white">%</th>
            @foreach($lstMonths as $k => $month)
            <th class="text-center bg-complete text-white">
              {{getMonthsSpanish($month['m'])}}<br/>
              <?php
              if (isset($t_room_month[$month['m']]) && $t_room_month[$month['m']] > 1) {
                echo number_format($t_room_month[$month['m']], 0, ',', '.') . '€';
              } else {
                echo '--';
              }
              ?>
            </th>
            @endforeach
        <tbody>
          <tr class="text-center">
            <td class="text-left static">
              <i class="fas fa-plus-circle toggle-contab-extra"></i> Extras
            </td>
            <th class="text-center first-col"></th>
            <th class="text-center ">  
              {{moneda($months_extras[0])}}
            </th>
            <th class="text-center">&nbsp;</th>
            @foreach($lstMonths as $k => $month)
            <th class="text-center">
              <?php echo isset($months_extras[$month['m']]) ? moneda($months_extras[$month['m']], false) : 0; ?>
            </th>
            @endforeach
          </tr>
          @foreach($extrasList as $extra_id => $item)
          <tr class="text-center contab-extras tr-close">
            <td class="text-left static">{{$extraTit[$extra_id]}}</td>
            <th class="text-center first-col"></th>
            <th class="text-center ">  
              {{moneda($item[0])}}
            </th>
            <td class="text-center">
              <?php
              $percent = ($months_extras[0] > 0) ? ($item[0] / $months_extras[0]) * 100 : 0;
              echo round($percent) . '%';
              ?>
            </td>
            @foreach($lstMonths as $k => $month)
            <th class="text-center">{{moneda( $item[$month['m']],false)}}</th>
            @endforeach
          </tr>
          @endforeach

         

          @foreach($siteRooms as $site => $data)
          <tr class="text-center contab-site" data-id="{{$site}}">
            <td class="text-left static">  
              <i class="fas fa-plus-circle toggle-contab-site" data-id="{{$site}}"></i>{{$data['t']}}
            </td>
            <th class="text-center first-col"></th>
            <th class="text-center ">  
              {{moneda($data['months'][0])}}
            </th>
            <td class="text-center">
              <?php
              $percent = 0;
              if ($t_all_rooms>0) $percent = ($data['months'][0] * 100 / $t_all_rooms);
              ?>
              {{round($percent)}}%
            </td>
            @foreach($lstMonths as $k => $month)
            <th class="text-center">
              <?php
              $k_month = $month['m'];
              if (isset($data['months'][$k_month]) && $data['months'][$k_month] > 1) {
                echo moneda($data['months'][$k_month]);
              } else {
                echo '--';
              }
              ?>
            </th>
            @endforeach
          </tr>

          <!-- BEGIN: channels-->                               
          @foreach($data as $ch => $data2)
          <?php
          if ($ch == 't' || $ch == 'months' || $ch == 'channels' || trim($ch) == '') {
            continue;
          }
          ?>
          <tr class="text-center contab-ch contab-ch-{{$site}} tr-close">
            <td class="text-left static">  
              <i class="fas fa-plus-circle toggle-contab" data-id="{{$ch}}"></i>{{$channels[$ch]}}
            </td>
            <th class="text-center first-col"></th>
            <th class="text-center ">  
              {{moneda($data2['months'][0])}}
            </th>
            <td class="text-center">
              <?php
              $percent = 0;
              if ($data2['months'] > 1 && $t_all_rooms>0)
                $percent = ($data2['months'][0] * 100 / $t_all_rooms);
              ?>
              {{round($percent)}}%
            </td>
            @foreach($lstMonths as $k => $month)
            <th class="text-center">
              <?php
              $k_month = $month['m'];
              if (isset($data2['months'][$k_month]) && $data2['months'][$k_month] > 1) {
                echo moneda($data2['months'][$k_month]);
              } else {
                echo '--';
              }
              ?>
            </th>
            @endforeach
          </tr>
          <!-- BEGIN: ROOMS-->   
          @foreach($data2['rooms'] as $roomID => $name)
          <tr class="text-center contab-room contab-room-{{$ch}} contab-rsite-{{$site}}  tr-close">
            <td class="text-left static">{!!$name!!}</td>
            <th class="text-center first-col"></th>
            <th class="text-center ">  
              <?php
              $totalRoom = 0;
              if (isset($t_rooms[$roomID]) && $t_rooms[$roomID] > 1) {
                $totalRoom = $t_rooms[$roomID];
                echo moneda($totalRoom);
              } else {
                echo '--';
              }
              ?>
            </th>
            <td class="text-center">
              <?php
              $percent = 0;
              if ($t_all_rooms>0)
                $percent = ($totalRoom / $t_all_rooms) * 100;
              echo round($percent) . '%';
              ?>
            </td>
            @foreach($lstMonths as $k => $month)
            <th class="text-center">
              <?php
              $k_month = $month['m'];
              if (isset($sales_rooms[$roomID]) && isset($sales_rooms[$roomID][$k_month]) && $sales_rooms[$roomID][$k_month] > 1) {
                echo moneda($sales_rooms[$roomID][$k_month]);
              } else {
                echo '--';
              }
              ?>
            </th>
            @endforeach
          </tr>
          @endforeach
          <!-- END: ROOMS-->                      
          @endforeach
          <!-- END: channels-->                      
          @endforeach

        </tbody>
      </table>
    </div>
    <small><b>Nota:</b> Los ingresos por edificio ya incluyen los Extras asociados</small>
    <?php 
    $trimestre = [[],[],[],[]];
    $trimestreText = ['1er','2do','3er','4to'];
    $count = 0;
    foreach($lstMonths as $k => $month){
      $aux = ($count/3);
      if (!isset($trimestre[$aux])) $trimestre[$aux] = [];
      $trimestre[$aux][] = $month['m'];
      $count++;
    }
    ?>
    <div class=" table-responsive" >
      <table class="table tableTrimestres">
          <tr>
            <th>VENTAS TRIMESTRES</th>
            <th>TOTAL <br/>{{moneda($t_all_rooms)}}</th>
            <?php
            foreach ($trimestre as $t=>$meses):
              $tAux = 0;
              foreach ($meses as $m):
                if (isset($t_room_month[$m]) && $t_room_month[$m] > 1) {
                  $tAux += $t_room_month[$m];
                }
              endforeach
              ?>
                <th>{{$trimestreText[$t]}} TRIM. <br/>{{moneda($tAux)}}</th>
              <?php
            endforeach
              ?>
          </tr>
      </table>
    </div>
  </div>
  <div class="col-md-3">
    <h3>Ingresos Anual</h3>
    <div class="boxChar"><div class="contentChar">
    <canvas id="ingrChar" style="width: 100%; height: 250px;"></canvas>
      </div></div>
    <h3>Ingresos Por Sitio</h3>
    <div class="boxChar"><div class="contentChar">
    <canvas id="ingrCharSite" style="width: 100%; height: 250px;"></canvas>
      </div></div>
  </div>
</div>
<?php $count=0;?>
<script type="text/javascript">
  /* GRAFICA INGRESOS */
  var data = {
  labels: [@foreach($months as $month) "{{$month}}", @endforeach],
      datasets: [
          {
            <?php $count++; ?>
            borderColor: '{{printColor($count)}}',
            label: "{{$k}}",
            borderWidth: 1,
            data: [
              @foreach($ingrMonths as $k2 => $v2) {{round($v2)}}, @endforeach
            ],
          },
      ]
  };
  var ingrChar = new Chart('ingrChar', {
    type: 'line',
    data: data,
    options: {
    legend: {
        display: false
    },
    tooltips: {
        callbacks: {
           label: function(tooltipItem) {
                  return tooltipItem.yLabel;
           }
        }
    }
}
  });
  <?php $count=0;?>
  /* GRAFICA INGRESOS */
  var data = {
  labels: [@foreach($months as $month) "{{$month}}", @endforeach],
      datasets: [
        
        @foreach($ingrSite as $k => $v)
          {
            
            <?php $count++; ?>
            borderColor: '{{printColor($count)}}',
            label: "{{$k}}",
            borderWidth: 1,
            data: [
              @foreach($v as $k2 => $v2) {{round($v2)}}, @endforeach
            ],
          },
        @endforeach
      ]
  };
  var ingrChar = new Chart('ingrCharSite', {
    type: 'line',
    data: data,
  });
</script>
<style>
  .tIngrMes thead th,
  .tableTrimestres th{
    color: #FFF !important;
    background-color: #1f7b00;
    text-align: center;
  }
  .table.tableTrimestres tr th{
    font-size: 22px;
  }
</style>

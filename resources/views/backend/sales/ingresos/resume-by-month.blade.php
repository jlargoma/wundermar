
<h3>Resumen Ingresos / Mes</h3>
<div class=" table-responsive">
  <table class="table table-resumen">
    <thead>
      <tr class="resume-head">
        <th class="static text-left">Tipo</th>
        <th class="static-2">Total</th>
        <th class="first-col"></th>
        <td style="padding-left: 4em !important"></td>
        @foreach($lstMonths as $k => $month)
        <th>{{getMonthsSpanish($month['m'])}}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php $auxEtr = $ingrMonths['extras']; ?>
        <td class="static">Aptos</td>
        <td class="static-2" > {{moneda( $t_all_rooms-$auxEtr[0],false)}}</td>
        <td class="first-col"></td>
        <td style="padding-left:  4em !important"></td>
        @foreach($lstMonths as $k => $month)
        <td class="nowrap">
          <?php
          $val = 0;
          $auxM = $month['m'];
          if (isset($t_room_month[$auxM]) && $t_room_month[$auxM] > 1) {
            $val = $t_room_month[$auxM];
          }
          if (isset($auxEtr[$auxM]) && $auxEtr[$auxM] > 1) {
            $val -= $auxEtr[$auxM];
          }
          echo moneda($val);
          ?>
        </td>
        @endforeach
      </tr>
      @foreach($ingrMonths as $k=>$item)
      <tr>
        <td class="static">{{$ingrType[$k]}}</td>
        <td class="static-2" >{{moneda($item[0],false)}}</td>
        <td class="first-col"></td>
        <td style="padding-left:  4em !important"></td>
        @foreach($lstMonths as $month=>$val)
        <td class="nowrap">{{moneda($item[$val['m']],false)}}</td>
        @endforeach
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

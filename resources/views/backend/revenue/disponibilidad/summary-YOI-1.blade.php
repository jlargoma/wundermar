<div class="table-responsive ">
  <table class="table table-resumen summary">
    <thead>
      <tr class="resume-head">
        <th class="static">{{show_isset($lstMonths,$month)}}</th>
        <th class="first-col">Nº Hab.</th>
        <th>% Ocup.</th>
        <th>Precio Medio</th>
        <th>Revenue</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="static text-left">Real</td>
        <td class="first-col">{{$totalMonthOcc}}</td>
        <td>{{$occupPerc}}%</td>
        <td>{{moneda($medPVP)}}</td>
        <td>{{moneda($monthPVP)}}</td>
      </tr>
      <tr>
        <td class="static text-left">Pesupuesto</td>
        <td class="first-col"><input class="editable" data-id="pres_n_hab" data-key="{{$month_key}}" value="{{$sSummaryMonth['pres_n_hab']}}"></td>
        <td><span id="pres_n_hab_percent_{{$month_key}}" data-total="{{$totalMonth}}">{{$sSummaryMonth['pres_n_hab_perc']}}</span>%</td>
        <td><input class="editable" data-id="pres_med_pvp" data-key="{{$month_key}}" value="{{$sSummaryMonth['pres_med_pvp']}}"></td>
        <td><input class="editable" data-id="pres_pvp" data-key="{{$month_key}}" value="{{$sSummaryMonth['pres_pvp']}}"></td>
      </tr>
      <tr>
        <td class="static text-left">Diferencial</td>
        <td class="first-col">{{$totalMonthOcc-$sSummaryMonth['pres_n_hab']}}</td>
        <td>{{$occupPerc-$sSummaryMonth['pres_n_hab_perc']}}%</td>
        <td>{{moneda($medPVP-$sSummaryMonth['pres_med_pvp'])}}</td>
        <td>{{moneda($monthPVP-$sSummaryMonth['pres_pvp'])}}</td>
      </tr>
      <tr>
        <td class="static text-left">Forescating</td>
         <td class="first-col"><input class="editable" data-id="foresc_n_hab" data-key="{{$month_key}}" value="{{$sSummaryMonth['foresc_n_hab']}}"></td>
        <td><span id="foresc_n_hab_percent_{{$month_key}}" data-total="{{$totalMonth}}">{{$sSummaryMonth['foresc_n_hab_perc']}}</span>%</td>
        <td><input class="editable" data-id="foresc_med_pvp" data-key="{{$month_key}}" value="{{$sSummaryMonth['foresc_med_pvp']}}"></td>
        <td><input class="editable" data-id="foresc_pvp" data-key="{{$month_key}}" value="{{$sSummaryMonth['foresc_pvp']}}"></td>
      </tr>
      <tr>
        <td class="static text-left">Diferencial</td>
        <td class="first-col">{{$totalMonthOcc-$sSummaryMonth['foresc_n_hab']}}</td>
        <td>{{$occupPerc-$sSummaryMonth['foresc_n_hab_perc']}}%</td>
        <td>{{moneda($medPVP-$sSummaryMonth['foresc_med_pvp'])}}</td>
        <td>{{moneda($monthPVP-$sSummaryMonth['foresc_pvp'])}}</td>
      </tr>
    </tbody>
    <tfoot>
      <tr class="resume-head">
        <th class="static">Dif YOY</th>
        <th class="first-col danger">-0</th>
        <th class="danger">-0%</th>
        <th class="success">0.41</th>
        <th class="danger"><b>-</b></th>
      </tr>
    </tfoot>
  </table>
</div>

<div class="table-responsive ">
  <table class="table table-resumen summary">
    <thead>
      <tr class="resume-head">
        <th class="static">ANUAL</th>
        <th class="first-col">Nº Hab.</th>
        <th>% Ocup.</th>
        <th>Precio Medio</th>
        <th>Revenue</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="static text-left">Real</td>
        <td class="first-col">{{$totalSummaryOcc}}</td>
        <td>{{$occupPercSession}}%</td>
        <td>{{moneda($medPVPSession)}}</td>
        <td>{{moneda($summaryPVP)}}</td>
      </tr>
      <tr>
        <td class="static text-left">Pesupuesto</td>
        <td class="first-col"><input class="editable" data-id="pres_n_hab" data-key="{{$year->year}}" value="{{$sSummarySeasson['pres_n_hab']}}"></td>
        <td><span id="pres_n_hab_percent_{{$year->year}}" data-total="{{$totalSummary}}">{{$sSummarySeasson['pres_n_hab_perc']}}</span>%</td>
        <td><input class="editable" data-id="pres_med_pvp" data-key="{{$year->year}}" value="{{$sSummarySeasson['pres_med_pvp']}}"></td>
        <td><input class="editable" data-id="pres_pvp" data-key="{{$year->year}}" value="{{$sSummarySeasson['pres_pvp']}}"></td>
      </tr>
      <tr>
        <td class="static text-left">Diferencial</td>
        <td class="first-col">{{$totalSummaryOcc-$sSummarySeasson['pres_n_hab']}}</td>
        <td>{{$occupPercSession-$sSummarySeasson['pres_n_hab_perc']}}%</td>
        <td>{{moneda($medPVPSession-$sSummarySeasson['pres_med_pvp'])}}</td>
        <td>{{moneda($summaryPVP-$sSummarySeasson['pres_pvp'])}}</td>
      </tr>
      <tr>
        <td class="static text-left">Forescating</td>
         <td class="first-col"><input class="editable" data-id="foresc_n_hab" data-key="{{$year->year}}" value="{{$sSummarySeasson['foresc_n_hab']}}"></td>
        <td><span id="foresc_n_hab_percent_{{$year->year}}" data-total="{{$totalSummary}}">{{$sSummarySeasson['foresc_n_hab_perc']}}</span>%</td>
        <td><input class="editable" data-id="foresc_med_pvp" data-key="{{$year->year}}" value="{{$sSummarySeasson['foresc_med_pvp']}}"></td>
        <td><input class="editable" data-id="foresc_pvp" data-key="{{$year->year}}" value="{{$sSummarySeasson['foresc_pvp']}}"></td>
      </tr>
      <tr>
        <td class="static text-left">Diferencial</td>
        <td class="first-col">{{$totalSummaryOcc-$sSummarySeasson['foresc_n_hab']}}</td>
        <td>{{$occupPercSession-$sSummarySeasson['foresc_n_hab_perc']}}%</td>
        <td>{{moneda($medPVPSession-$sSummarySeasson['foresc_med_pvp'])}}</td>
        <td>{{moneda($summaryPVP-$sSummarySeasson['foresc_pvp'])}}</td>
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

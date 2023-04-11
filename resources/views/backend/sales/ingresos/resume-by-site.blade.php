<div class="row">
  <div class="col-md-8 col-xs-12">
    <h3>Resumen Edificios</h3>
    <div class=" table-responsive">
      <table class="table table-resumen">
        <thead>
          <tr class="resume-head">
            <th class="static">Concepto</th>
            <th class="first-col"></th>
            <th >Total</th>
            @foreach($lstMonths as $k => $month)
            <th>{{getMonthsSpanish($month['m'])}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($siteRooms as $site => $data)
          <tr>
            <td class="static">{{$data['t']}}</td>
            <td class="first-col"></td>
            <td >{{moneda($data['months'][0])}}</td>
            @foreach($lstMonths as $k => $month)
            <td>
              {{moneda( $data['months'][$month['m']],false)}}
            </td>
            @endforeach
          </tr>
          @endforeach

        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-4 col-xs-12">
    <div class="pieChart">
    <canvas id="chart_2"></canvas>
    </div>
  </div>
</div>
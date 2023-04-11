<div class="row">
  <div class="col-md-8 col-xs-12">
    <h3>Resumen Gastos / Mes</h3>
    <div class=" table-responsive">
      <table class="table table-resumen">
        <thead>
          <tr class="resume-head">
            <th class="static">Concepto</th>
            <th class="static-2">Total</th>
            <th class="first-col-2"></th>
            @foreach($lstMonths as $k => $month)
            <th>{{getMonthsSpanish($month['m'])}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($listGasto_g as $k=>$item)
          <tr>
            <td class="static">{{$gTypeGroup[$k]}}</td>
            <td class="static-2">{{moneda($item[0],false)}}</td>
            <th class="first-col-2"></th>
            @foreach($lstMonths as $k_month=>$month)
            <td>{{moneda($item[$month['m']],false)}}</td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    </div>
  <div class="col-md-4 col-xs-12">
      <div class="pieChart">
        <canvas id="chart_1"></canvas>
      </div>
    </div>
</div>

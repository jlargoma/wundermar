<div class="row">
  <div class="col-md-8 col-xs-12">
    <h3>Resumen Extras PVP</h3>
    <div class=" table-responsive">
      <table class="table table-resumen">
        <thead>
          <tr class="resume-head">
            <th class="static">Concepto</th>
            <th class="first-col"></th>
            <th>Total</th>
            @foreach($lstMonths as $k => $month)
            <th>{{getMonthsSpanish($month['m'])}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($extrasGroup as $k=>$item)
          <tr>
            <td class="static">{{$extTyp[$k]}}</td>
            <td class="first-col"></td>
            @foreach($item as $month=>$val)
            <td>{{moneda($val,false)}}</td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-4 col-xs-12">
    <div class="pieChart">
    <canvas id="chart_3"></canvas>
    </div>
  </div>
</div>
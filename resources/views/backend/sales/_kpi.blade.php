<h4>KPI Hotel Rosa D'Oro</h4>
<div class="row">
  <div class="col-xs-6">
    <div class="box">
      <h5>Ocupación</h5>
      <table class="table">
        @foreach($kpi_data['ocupacion'] as $m=>$v)
        <tr>
          <td>{{getMonthsSpanish($m)}}</td>
          <th>{{round($v)}} %</th>
        </tr>
        @endforeach
      
      </table>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="box">
      <h5>ADR</h5>
      <table class="table">
        @foreach($kpi_data['ADR'] as $m=>$v)
        <tr>
          <td>{{getMonthsSpanish($m)}}</td>
          <th>{{$v}}</th>
        </tr>
        @endforeach
      
      </table>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="box">
      <h5>RevPAR</h5>
      <table class="table">
        @foreach($kpi_data['RevPAR'] as $m=>$v)
        <tr>
          <td>{{getMonthsSpanish($m)}}</td>
          <th>{{$v}}</th>
        </tr>
        @endforeach
      
      </table>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="box">
      <h5>GopPAR</h5>
      <table class="table">
        @foreach($kpi_data['GopPAR'] as $m=>$v)
        <tr>
          <td>{{getMonthsSpanish($m)}}</td>
          <th>{{$v}}</th>
        </tr>
        @endforeach
      
      </table>
    </div>
  </div>
</div>

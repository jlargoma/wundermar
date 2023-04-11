<div class="row">
  <div class="table-responsive">
    <table class="table liq-agencia" border="1">
      <thead>
        <tr>
          <th>AGENCIA</th>
          <th>Vtas</th>
          <th>Vtas. %</th>
          <th>Reservas<br>(noches)</th>
          <th>Res. %</th>
          <th>Comisión</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data['data'] as $k=>$v)
      <tr>
        <th>{{$agencyBooks[$k]}}</th>
        <td>{{moneda($v['total'])}}</td>
        <td>{{$v['total_rate']}}%</td>
        <td>{{$v['reservations']}}</td>
        <td>{{$v['reservations_rate']}}%</td>
        <td>{{moneda($v['commissions'])}}</td>
      </tr>
      @endforeach
      </tbody>
      <tfoot style="    background-color: #c7c7c7;">
      <th>Total</th>
      <th colspan="2">{{moneda($data['totals']['total'])}}</th>
      <th colspan="2">{{$data['totals']['reservations']}}</th>
      <th>{{moneda($data['totals']['commissions'])}}</th>
      </tfoot>
    </table>
  </div>
</div>
<div class="col-md-12">
  <div class="pieChart agencies">
    <canvas id="chart_agency"></canvas>
  </div>
</div>
<style>
  .pieChart.agencies {
    max-width: 260px;
    margin: 1em auto;
    text-align: center;
}
</style>
<script type="text/javascript">
  new Chart(document.getElementById("chart_agency"), {
    type: 'pie',
    data: {
      labels: [<?php foreach ($data['data'] as $k=>$v)
  echo '"' . $agencyBooks[$k] . '",'; ?>],
      datasets: [{
          backgroundColor: [<?php 
          $n=0;
          foreach ($data['data'] as $k=>$v){
            $n++;
            echo '"'.printColor($n).'",';
          }
            ?>],
          data: [<?php foreach ($data['data'] as $k=>$v)
  echo "'" . round($v['total_rate']) . "',"; ?>]
        }]
    },
    options: {
      title: {display: false},
      legend: {display: false},
//      legend: { position: 'bottom'}
    }
  });
</script>
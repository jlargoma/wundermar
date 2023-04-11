<canvas id="barBalance" style="width: 100%; height: 250px;"></canvas>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<script type="text/javascript">
  /* GRAFICA INGRESOS/GASTOS */
  var data = {
  labels: [
          @if (count($allRevenue))
            @foreach($allRevenue as $d => $r) "{{$d+1}}", @endforeach
          @endif],
          datasets: [
          {
            label: "Reservas netas",
            backgroundColor: 'rgba(67, 160, 71, 0.3)',
            borderColor:'rgba(67, 160, 71, 1)',
            borderWidth: 1,
            data: [
              @if (count($allRevenue))
                @foreach($allRevenue as $d => $r)
                  {{$r->ocupacion+$r->llegada-$r->cancelaciones}},
                @endforeach
              @endif
            ],
          }

          ]
          };
  var barBalance = new Chart('barBalance', {
  type: 'line',
          data: data,
          });
</script>

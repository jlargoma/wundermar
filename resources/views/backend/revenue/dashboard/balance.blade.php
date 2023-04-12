<div >
    <div class="col-md-6 col-xs-12">
      <h2 class="line"><span>Beneficio temporar actual</span></h2>
      <?php 
      $tIngr = array_sum($ingr)+$ingrExt;
      $tGast = array_sum($gastos);
      $tBene = $tIngr-$tGast;
      ?>
      <div class="row resume-box">
        <div class="col-md-4 m-b-10 col-xs-6">
          <div class="box-resumen" style="background-color: #46c37b">
            <h5 class="no-margin p-b-5 text-white ">
              <b>INGRESOS</b>
            </h5>
              {{moneda($tIngr)}}
          </div>
        </div>
        
        <div class="col-md-4 m-b-10 col-xs-6">
          <div class="box-resumen" style="background-color: #a94442">
            <h5 class="no-margin p-b-5 text-white "><b>GASTOS</b></h5>
              {{moneda($tGast)}}
          </div>
        </div>
        <div class="col-md-4 m-b-10 col-xs-6">
          <div class="box-resumen" style="background-color: #2c5d9b">
            <h5 class="no-margin p-b-5 text-white ">
              <b>BENEFICIO BRUTO</b>
            </h5>
            {{moneda($tBene)}}
            <?php if ($tBene > 0 ): ?>
                    <i class="fa fa-arrow-up text-success result"></i>
            <?php else: ?>
                    <i class="fa fa-arrow-down text-danger result"></i>
            <?php endif ?>
          </div>
        </div>
      </div>
      <div class="boxChar"><div class="contentChar">
      <canvas id="barBalance" style="width: 100%; height: 250px;"></canvas>
        </div></div>
 
    </div>
    <div class="col-md-6 col-xs-12">
      <h2 class="line"><span>Ingresos Años Anteriores</span></h2>
      @include('backend.revenue.dashboard._by_season')
      <div class="col-lg-6 col-md-6 col-xs-12">
        <div class="boxChar"><div class="contentChar">
            <canvas id="barChart" style="width: 100%; height: 250px;"></canvas>
        </div>
        </div>
      </div>
      <div class="col-lg-6 col-md-6  col-xs-12">
        <div class="boxChar"><div class="contentChar">
            <canvas id="barChart2" style="width: 100%; height: 250px;"></canvas>
        </div>
        </div>
      </div>
    </div>
       <div class="row">
        <div class="col-md-6">
          @include('backend.revenue.dashboard.resume-by-site')
        </div>
        <div class="col-md-6">
          @include('backend.revenue.dashboard.resume-by-extras')
        </div>
      </div>
</div>
<script type="text/javascript">
  /* GRAFICA INGRESOS/GASTOS */
  var data = {
          labels: [@foreach($lstMonths as $month) "{{$month}}", @endforeach],
          datasets: [
          {
          label: "Ingresos",
                  backgroundColor: 'rgba(67, 160, 71, 0.3)',
                  borderColor:'rgba(67, 160, 71, 1)',
                  borderWidth: 1,
                  data: [
                    @foreach($ingr as $k=>$v) {{round($v)}}, @endforeach
                  ],
          },
          {
          label: "Gastos",
                  backgroundColor: 'rgba(229, 57, 53, 0.3)',
                  borderColor: 'rgba(229, 57, 53, 1)',
                  borderWidth: 1,
                  data: [
                    @foreach($gastos as $k=>$v) {{round($v)}}, @endforeach
                  ],
          }

          ]
  };
  var barBalance = new Chart('barBalance', {
  type: 'line',
          data: data,
  });


      <?php $totalYearSite = \App\Rooms::getPvpLastYears_site($year->year); ?>
      var data = {
        labels: [

        <?php 
        $auxY = $year->year-3;
	    for ($i=1; $i <= 4; $i++): 
              echo "'$auxY',";
              $auxY++;
        endfor; 
        ?>
        ],
        datasets: [
          {
            label: "wundermar",
            borderColor: '#004a2f',
            borderWidth: 1,
            fill: false,
            data: [{{$totalYearSite[1]}}],
          }
        ]
      };
       var myBarChart = new Chart('barChart', {
        type: 'line',
        data: data,
      });
                 
      var data = {
        labels: [
          <?php 
          $auxY = $year->year-3;
          for ($i=1; $i <= 4; $i++): 
                echo "'$auxY',";
                $auxY++;
          endfor; 
          ?>
        ],
        datasets: [
          {
            label: "Ingresos por Temp",
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            data: [
	          <?php 
                $auxY = $year->year-3;
                for ($i=1; $i <= 4; $i++):
                  $totalYear = \App\Rooms::getPvpByYear($auxY);
                  echo "'" . $totalYear. "',";
                  $auxY++;
                endfor; ?>
            ],
          }
        ]
      };

        

      var myBarChart = new Chart('barChart2', {
        type: 'bar',
        data: data,
      });

</script>


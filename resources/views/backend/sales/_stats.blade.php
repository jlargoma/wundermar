
<style>
  .table-ingresos , .table-ingresos >tbody> tr > td{
    background-color: #92B6E2!important;
    margin: 0px ;
    padding: 5px 8px;
  }
  .table-cobros , .table-cobros >tbody> tr > td{
    background-color: #38C8A7!important;
    margin: 0px ;
    padding: 5px 8px;
  }
  .tr-cobros:hover{
    background-color: #2ca085!important;
  }
  .tr-cobros:hover td {
    background-color: #2ca085!important;
  }
  .fa-arrow-up{
    color: green;
  }
  .fa-arrow-down{
    color: red;
  }
  .bg-complete-grey{
    background-color: #92B6E2!important;
  }
  .bordered{
    padding: 15px;
    border:1px solid #e8e8e8;
    background: white;
  }
  .t-ingresos,
  .t-ingresos-nro{
    padding: 5px 10px !important;
  }
  .t-ingresos{
    width: 50%;
  }
   .t-ingresos-nro{
     text-align: center;
   }
</style>

<?php 
//$dataStats = \App\http\Controllers\LiquidacionController::getSalesByYear(); 
$pending = ($vendido-$cobrado);
if ($pending<0) $pending = 0;
?>
<div class="col-md-6 col-xs-12 mb-1em">
  <div class="row">
  <div class="col-md-12 col-xs-9">
  <table class="table table-hover table-striped table-ingresos" style="background-color: #92B6E2">
    <thead class="bg-complete" style="background: #d3e8f7">
    <th colspan="2" class="text-black text-center"> Ingresos Reservas</th>
    </thead>
    <tbody>
      <tr>
        <td class="t-ingresos" style="background-color: #d3e8f7!important;"><b>VENTAS TEMPORADA</b></td>
        <td class="t-ingresos-nro" style="background-color: #d3e8f7!important;">
          <b><?php echo number_format(round($vendido), 0, ',', '.') ?> €</b>
        </td>
      </tr>
      <tr>
        <td class="t-ingresos text-white" style="background-color: #38C8A7!important;">
          Cobrado Temporada
        </td>
        <td class="t-ingresos-nro text-white" style="background-color: #38C8A7!important;">
          <?php echo number_format(round($cobrado), 0, ',', '.') ?> € 
        </td>
      </tr>
      <tr style="background-color: #8e5ea2;">
        <td class="t-ingresos text-white" style="background-color: #8e5ea2!important;">Pendiente Cobro</td>
        <td class="t-ingresos-nro text-white" style="background-color: #8e5ea2!important;">
          <?php echo number_format(round($pending), 0, ',', '.') ?> €
        </td>
      </tr>
    </tbody>
  </table>
  </div>
  <div class="col-md-12 col-xs-3">
    <div class="pieChart">
    <canvas id="pieIng"></canvas>
    </div>
  </div>
    </div>
</div>

<div class="col-md-6 col-xs-12 mb-1em">
  <div class="row">
  <div class="col-md-12 col-xs-9">
  <table class="table table-hover table-striped table-cobros" style="background-color: #38C8A7">
    <thead style="background-color: #38C8A7">
    <th colspan="2" class="text-white text-center">Cobros Reservas</th>
    </thead>
    <tbody style="background-color: #38C8A7">
      <tr class="tr-cobros">
        <th class="t-ingresos text-white" style="background-color: #38C8A7!important;">TOTAL COBRADO</th>
        <th class="t-ingresos-nro text-white" style="background-color: #38C8A7!important;">
          <?php echo number_format(round($cobrado), 0, ',', '.') ?> €
        </th>
      </tr>
      <tr class="tr-cobros">
        <td class="t-ingresos text-white" style="background-color: #2ba840!important;">Metalico</td>
        <td class="t-ingresos-nro text-white" style="background-color: #2ba840!important;">
          <?php echo number_format(round($metalico), 0, ',', '.') ?> €
        </td>
      </tr>
      <tr class="tr-cobros">
        <td class="t-ingresos text-white" style="background-color: #2ca085!important;">Banco</td>
        <td class="t-ingresos-nro text-white" style="background-color: #2ca085!important;">
          <?php echo number_format(round($banco), 0, ',', '.') ?> €
        </td>
      </tr>

    </tbody>
  </table>
  </div>
  <div class="col-md-12 col-xs-3">
    <div class="pieChart">
    <canvas id="pieCobros"></canvas>
    </div>
  </div>
  </div>
</div>

<script type="text/javascript">

  new Chart(document.getElementById("pieIng"), {
    type: 'pie',
    data: {
      labels: ["Cobrado", "Pendiente", ],
      datasets: [{
          label: "Population (millions)",
          backgroundColor: ["#38C8A7", "#8e5ea2"],
          data: [

            //Comprobamos si existen cobros
<?php echo round($cobrado) ?>,
<?php echo round($pending) ?>,
          ]
        }]
    },
    options: {
      title: {
        display: false,
        text: 'Ingresos de la temporada'
      },
      legend: {display: false},
    }
  });
  
  new Chart(document.getElementById("pieCobros"), {
    type: 'pie',
    data: {
      labels: ["Metalico", "Banco", ],
      datasets: [{
          backgroundColor: ["#2ba840", "#2ca085"],
          data: [
            //Comprobamos si existen cobros
<?php echo round($metalico) ?>,
<?php echo round($banco) ?>,
          ]
        }]
    },
    options: {
      title: {
        display: false,
        text: ''
      },
      legend: {display: false},
    }
  });
  
</script>

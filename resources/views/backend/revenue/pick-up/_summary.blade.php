<!--<div class="">
  <span id="summaryLeft"><i class="fa fa-arrow-left"></i></span>
  <span id="summaryMonth"></span>    
  <span id="summaryRigth"><i class="fa fa-arrow-right"></i></span>
</div>-->
 
<div class="table-responsive summary" style="margin: 1em auto;">
  <table class="table">
    <tr>
      <th class="static" style="background-color: #FFF;"></th>
      <th class="first-col"></th>
      <th>Nº Habitación</th>
      <th>% Ocupación</th>
      <th>Precio Medio</th>
      <th>Revenue</th>
    </tr>
    <tr>
      <td class="static">Real <span id="summaryMonth"></span> </td>
      <td class="first-col"></td>
      <td id="tDisp"></td>
      <td id="pOcup"></td>
      <td id="pMed"></td>
      <td id="revenue"></td>
    </tr>
  </table>
</div>
<h4>Resumen Anual</h4>
<div class="table-responsive summary" style="margin: 1em auto;">
  <table class="table">
    <tr>
      <th class="static" style="background-color: #FFF;"></th>
      <th class="first-col"></th>
      <th>Nº Habitación</th>
      <th>% Ocupación</th>
      <th>Precio Medio</th>
      <th>Revenue</th>
    </tr>
    <tr>
      <td class="static">Real</td>
      <td class="first-col"></td>
      <td>{{$tDisp}}</td>
      <td>{{round( $tOcup*100/$tDisp)}} % </td>
      <td>@if($tOcup>0){{moneda($tIng/$tOcup)}}@endif</td>
      <td>{{moneda($tIng)}}</td>
    </tr>
  </table>
</div>

<script type="text/javascript">
  var iSumm = [{},
    <?php 
    if (isset($summMonth)){
      foreach ($summMonth as $m=>$v):
      echo '{';
      foreach ($v as $k1=>$v1):
        echo "$k1 : '$v1',";
      endforeach;
      echo '},';
      endforeach; 
    }
    ?>
  ];
  
  var months = ['','Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

  $(document).ready(function () {
    // current month Summary
    var cmSumm = {{$sel_mes}};
    loadData();
    
    $('#summaryLeft').on('click', function (){
      cmSumm--;
      if (cmSumm < 1) cmSumm = 12;
      loadData();
    });
    
    $('#summaryRigth').on('click', function (){
      cmSumm++;
      if (cmSumm > 12) cmSumm = 1;
      loadData();
    });
    
    
    function loadData(){
      $("#summaryMonth").text(months[cmSumm]);
      $("#tDisp").text(iSumm[cmSumm].tDisp);
      $("#pOcup").text(iSumm[cmSumm].perc+'%');
      $("#pMed").text(iSumm[cmSumm].pm);
      $("#revenue").text(iSumm[cmSumm].tIng);
    }
    
      });
</script>
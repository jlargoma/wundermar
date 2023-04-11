<div class="row">
  <div class="col-md-6">
    <div class="table-responsive summary" style="margin: 1em auto;">
      <table class="table">
        <tr>
          <th style="width:120px;"></th>
          <th>Nº NOCHES</th>
          <th>ADR</th>
          <th>PVP RVAS</th>
        </tr>
        <tr>
          <td >
            <strong id="summaryMonth"></strong>
          </td>
          <td id="smm_nights"></td>
          <td id="smm_adr"></td>
          <td id="smm_pvp"></td>
        </tr>
      </table>
    </div>
  </div>
  <div class="col-md-6">
    <div class="table-responsive summary" style="margin: 1em auto;">
      <table class="table">
        <tr>
          <th>ANUAL</th>
          <th>Nº NOCHES</th>
          <th>ADR</th>
          <th>PVP RVAS</th>
        </tr>
        <tr>
          <td>{{$range}}</td>
          <td>{{$t_n}}</td>
          <td>{{moneda($t_adr)}}</td>
          <td>{{moneda($t_tp)}}</td>
        </tr>
      </table>
    </div>
  </div>
</div>


<script type="text/javascript">
  var iSumm = [{},
    <?php 
    if (isset($aTotal)){
      foreach ($aTotal as $m=>$v):
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
    
//    $('#summaryLeft').on('click', function (){
//      cmSumm--;
//      if (cmSumm < 1) cmSumm = 12;
//      loadData();
//    });
//    
//    $('#summaryRigth').on('click', function (){
//      cmSumm++;
//      if (cmSumm > 12) cmSumm = 1;
//      loadData();
//    });
    
    function loadData(){
      $("#summaryMonth").text(months[cmSumm]);
      $("#smm_nights").text(iSumm[cmSumm].n);
      $("#smm_adr").text(iSumm[cmSumm].adr);
      $("#smm_pvp").text(iSumm[cmSumm].tp);
    }
    
      });
</script>




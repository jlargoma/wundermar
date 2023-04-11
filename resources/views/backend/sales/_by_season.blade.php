<div class="row ">
  <?php 
  $oldTotalPVP = 0;
  $totalPVP = 0; 
  
  $arrayColors = ['bg-info', 'bg-complete', 'bg-primary',];
  $year = $year->year-2;
  for ($i = 0; $i < 3; $i++):
    $yearAux = $year+$i;
    $totalPVP = \App\Rooms::getPvpByYear($yearAux);
    $totalExtras = \App\BookExtraPrices::getTotalByYear($yearAux); 
    
    ?>
    <div class="col-xs-4 m-b-10">

      <div class="widget-9 no-border <?php echo $arrayColors[$i] ?> widget-loader-bar">
        <div class="full-height d-flex flex-column">
          <div style="width: 94%;margin: 2px auto;">
            <h4 class="no-margin p-b-5 text-white ">
              Temp  <b>{{$yearAux}}</b>
            </h4>
            <div class="row">
              <div class="col-xs-10">
                <h5 class="text-white" >
                  <?php echo number_format($totalPVP, 0, ',', '.'); ?> € 
                  <div style="border-bottom: 1px solid;"> </div>
                  <?php echo number_format($totalExtras, 0, ',', '.'); ?> € <span style="font-size:9px;">(Extras)</span>
                </h5>
              
              
              </div>
              <div class="col-xs-2">
                <span style="font-size: 14px;">
                  <?php if ($i > 1 && $totalPVP > $oldTotalPVP): ?>
                    <i class="fa fa-arrow-up text-success" style="font-size: 20px;"></i>
                  <?php else: ?>
                    <i class="fa fa-arrow-down text-danger" style="font-size: 20px;"></i>
                  <?php endif ?>
              </span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

<?php endfor; ?>

</div>

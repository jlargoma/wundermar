<?php
$val = round($perc*100);
$circ = round(180*$perc);
$keyArc = rand();
?>
<div class="pie-container">
  <div class="pie-wrapper">
     <div class="arc" data-value="100"></div>
     <div class="arc value val_kpi_{{$keyArc}}"></div>
     <div class="legend">{{$val}}%</div>
  </div>
    <?php 
    echo '<style>
    .arc.value.val_kpi_'.$keyArc.'{
      -moz-transform: rotate('.$circ.'deg);
      -ms-transform: rotate('.$circ.'deg);
      -webkit-transform: rotate('.$circ.'deg);
      transform: rotate('.$circ.'deg);
    }
    </style>';
    ?>
</div>
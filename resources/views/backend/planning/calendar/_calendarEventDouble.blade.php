<?php 
use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$inicio = $inicio->copy()->format('Y-m-d');
if (!is_array($calendars)):
  
else:
  foreach ($calendars as $calendar):
    if ($calendar->type_book != 5):
      
      if($calendar->finish == $inicio): ?>
        <a <?php echo $calendar->href; ?> title="<?php echo $calendar->titulo ?>" >
          <div class="<?php echo $calendar->class ;?> end" style="width: 45%;float: left;">  &nbsp; </div>
        </a>
      <?php elseif ($calendar->start == $inicio ): ?>
        <a <?php echo $calendar->href; ?> title="<?php echo $calendar->titulo ?>" >
          <div class="<?php echo $calendar->class ;?> start" style="width: 45%;float: right;">&nbsp;</div>
        </a>
      <?php else: ?>
          <?php if ($calendar->type_book != 9 ): ?>
          <a <?php echo $calendar->href; ?> title="<?php echo $calendar->titulo ?>" >
            <div class="<?php echo $calendar->class ;?> total">&nbsp;</div>
          </a>
          <?php endif ?>
      <?php endif ?>
    <?php endif ?>
  <?php endforeach; ?>
<?php endif;

?>
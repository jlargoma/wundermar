<?php 
use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$inicio = $inicio->copy()->format('Y-m-d');
if (!is_array($calendars)):
  
else:
  foreach ($calendars as $calendar):
    $class = $calendar->getStatus($calendar->type_book);
    if ($class == "Contestado(EMAIL)"){ $class = "contestado-email";}
//    $class .= ' td-calendar ';
    $agency = ($calendar->agency != 0) ? "Agencia: ".$calendar->getAgency($calendar->agency) : "";
    $titulo = $calendar->customer['name'].'&#10'.
            'Pax-real '.$calendar->real_pax.'&#10;'.
            Carbon::createFromFormat('Y-m-d',$calendar->start)->formatLocalized('%d %b').
            ' - '.Carbon::createFromFormat('Y-m-d',$calendar->finish)->formatLocalized('%d %b')
            .'&#10;';

    $href = '';
    if ($uRole != "agente" && $uRole != "limpieza"){
      $titulo .='PVP:'.$calendar->total_price.'&#10';
      $href = ' href="'.url ('/admin/reservas/update').'/'.$calendar->id.'" ';
    }

    $titulo .= $agency;

    ?>

  <?php if($calendar->finish == $inicio && $calendar->type_book != 5): ?>
      <a <?php echo $href; ?> title="<?php echo $titulo ?>" >
        <div class="<?php echo $class ;?> end" style="width: 45%;float: left;">  &nbsp; </div>
      </a>
    <?php elseif ($calendar->start == $inicio && $calendar->type_book != 5 ): ?>
      <a <?php echo $href; ?> title="<?php echo $titulo ?>" >
        <div class="<?php echo $class ;?> start" style="width: 45%;float: right;">&nbsp;</div>
      </a>
    <?php else: ?>
        <?php if ($calendar->type_book != 9 && $calendar->type_book != 5): ?>
        <a <?php echo $href; ?> title="<?php echo $titulo ?>" >
          <div class="<?php echo $class ;?>" style="width: 100%;float: left;">&nbsp;</div>
        </a>
        <?php endif ?>
    <?php endif ?>
  <?php endforeach; ?>
<?php endif;

?>
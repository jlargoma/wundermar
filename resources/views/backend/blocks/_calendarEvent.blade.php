<!-- Si no hay dos reservas el mismo dia  -->
<?php 

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$inicio = $inicio->copy()->format('Y-m-d');
$calendar = ($calendars[0]) ? $calendars[0] : $calendars;
$class = $calendar->getStatus($calendar->type_book);
if ($class == "Contestado(EMAIL)"){ $class = "contestado-email";}
//$class .= ' td-calendar ';
$classTd = ' class="td-calendar" ';
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


  if ($calendar->start == $inicio):
    $agency = ($calendar->agency != 0)? "Agencia: " . $calendar->getAgency($calendar->agency) : "";
  ?> 
<td title="<?php echo $titulo ?>"  <?php echo $classTd; ?>>
      <a <?php echo $href; ?> title="<?php echo $titulo ?>" >
       <div class="<?php echo $class ;?> start" style="width: 45%;float: right; cursor: pointer;">&nbsp;</div>
       </a>
    </td>
  <?php 
  elseif($calendar->finish == $inicio): 
    ?>  
    <td title="<?php echo $titulo ?>" <?php echo $classTd; ?>>
      <a <?php echo $href; ?> title="<?php echo $titulo ?>" >
      <div class="<?php echo $class ;?> end" style="width: 45%;float: left;cursor: pointer;">
          &nbsp;
      </div>
        </a>
    </td>
    <?php else: ?>
      <td   title="<?php echo $titulo ?> "  <?php echo $classTd; ?> >
        <?php if ($calendar->type_book == 9): ?>
        <div class="<?php echo $class ;?>" style="width: 100%;height: 100%; cursor: pointer;">
           &nbsp;
        </div>
        <?php else: ?>
        <a <?php echo $href; ?> title="<?php echo $titulo ?>" class="<?php echo $class ;?>" style="display:block;">
           <div style="width: 100%;height: 100%; cursor: pointer;">
               &nbsp;
           </div>
        </a>
        <?php endif ?>
      </td>
    <?php endif ?>
                                         
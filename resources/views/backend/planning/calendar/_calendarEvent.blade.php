<!-- Si no hay dos reservas el mismo dia  -->
<?php
$inicio = $inicio->copy()->format('Y-m-d');
$calendar = ($calendars[0]) ? $calendars[0] : $calendars;
//dd($calendar['start']);
if ($calendar->start == $inicio):
  ?> 
  <td title="<?php echo $calendar->titulo ?>"  <?php echo $calendar->classTd; ?>>
    <a <?php echo $calendar->href; ?> title="<?php echo $calendar->titulo ?>" >
      <div class="<?php echo $calendar->class; ?> start">&nbsp;</div>
    </a>
  </td>
  <?php
elseif ($calendar->finish == $inicio):
  ?>  
  <td title="<?php echo $calendar->titulo ?>" <?php echo $calendar->classTd; ?>>
    <a <?php echo $calendar->href; ?> title="<?php echo $calendar->titulo ?>" >
      <div class="<?php echo $calendar->class; ?> end">
        &nbsp;
      </div>
    </a>
  </td>
<?php else: ?>
  <td   title="<?php echo $calendar->titulo ?> "  <?php echo $calendar->classTd; ?> >
    <?php if ($calendar->type_book == 9): ?>
      <div class="<?php echo $calendar->class; ?> total">
        &nbsp;
      </div>
    <?php else: ?>
      <a <?php echo $calendar->href; ?> title="<?php echo $calendar->titulo ?>" class="<?php echo $calendar->class; ?>" style="display:block;">
        <div class="total">
          &nbsp;
        </div>
      </a>
    <?php endif ?>
  </td>
<?php endif ?>
                                         
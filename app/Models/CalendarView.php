<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

/**
 * Description of prepareYearPricesAndMinStay
 *
 * @author cremonapg
 */
class CalendarView {

  public function __construct() {
    
  }

  function printEventSimple($date, $calendar) {
    $inicio = $date->format('Y-m-d');
    if ($calendar->start == $inicio) {
      ?> 
      <td <?php echo $calendar->classTd; ?>>
        <a <?php echo $calendar->href; ?> class="tip" >
          <div class="<?php echo $calendar->class; ?> start">&nbsp;</div>
          <span data-id="<?= $calendar->key ?>"></span>
        </a>
      </td>
      <?php
    } else {
      if ($calendar->finish == $inicio) {
        ?>  
        <td <?php echo $calendar->classTd; ?>>
          <a <?php echo $calendar->href; ?> class="tip" >
            <div class="<?php echo $calendar->class; ?> end">
              &nbsp;
            </div>
              <span data-id="<?= $calendar->key ?>"></span>
          </a>
        </td>
        <?php } else {
        ?>
        <td <?php echo $calendar->classTd; ?> >
        <?php if ($calendar->type_book == 9) { ?>
            <div class="<?php echo $calendar->class; ?> total">
              &nbsp;
            </div>
        <?php } else { ?>
            <a <?php echo $calendar->href; ?> class=" tip <?php echo $calendar->class; ?>" style="display:block;">
              <div class="total">
                &nbsp;
              </div>
                <span data-id="<?= $calendar->key ?>"></span>
            </a>
        <?php } ?>
        </td>
        <?php
        }
      }
    }

    
    function printEventDoble($date, $calendars) {
      $inicio = $date->format('Y-m-d');
     
      if (is_array($calendars)){
        foreach ($calendars as $calendar){
          
          if ($calendar->type_book == 5)      continue;
            if($calendar->finish == $inicio){ 
              $class = str_contains($calendar->classTd,'bordander') ? 'bordander' : '';
              ?>
              <a <?php echo $calendar->href; ?> class="tip <?php echo $class; ?>" >
                <div class="<?php echo $calendar->class ;?> end" style="width: 45%;float: left;">  &nbsp; </div>
                <span data-id="<?= $calendar->key ?>"></span>
              </a>
              <?php 
            }elseif ($calendar->start == $inicio ){ 
              ?>
              <a <?php echo $calendar->href; ?> class="tip" >
                <div class="<?php echo $calendar->class ;?> start" style="width: 45%;float: right;">&nbsp;</div>
                <span data-id="<?= $calendar->key ?>"></span>
              </a>
              <?php 
            }else{ 
                if ($calendar->type_book != 9 ){ 
                ?>
                <a <?php echo $calendar->href; ?> class="tip" >
                  <div class="<?php echo $calendar->class ;?> total">&nbsp;</div>
                  <span data-id="<?= $calendar->key ?>"></span>
                </a>
                <?php 
                }
            }
          }
        }
      }
    }
  
<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$uRole = getUsrRole();
?>
<div class="col-md-12 col-xs-12">
  <div class="panel panel-mobile">
    <div class="row">
        <?php $dateAux = $startYear->copy(); ?>
        <?php $diffInMonths = $startYear->diffInMonths($endYear) + 1; ?>
      <div class="col-12" style="overflow-x: auto;">
          <?php for ($i = 1; $i <= $diffInMonths; $i++) : ?>
            <?php $monthAux = $dateAux->copy()->format('n');?>
            <button <?php if($monthAux == date('n')): ?>id="btn-active"<?php endif?> class='btn btn-rounded btn-sm btn-default btn-fechas-calendar' data-month="<?php echo $monthAux; ?>">
                <?php echo getMonthsSpanish($monthAux).' '.ucfirst($dateAux->copy()->formatLocalized('%y'))?>
            </button>
            <?php $dateAux->addMonth(); ?>
          <?php endfor; ?>
      </div>
    </div>
<?php $inicioAux = $startYear->copy(); ?>
    <div class="tab-content" style="padding: 0px 5px;">
      <div class="tab-pane active" id="tab1">
        <div class="row">
          <div class="table-responsive content-calendar">
            <table class="fc-border-separate calendar-table" style="width: 100%">
              <thead>
                <tr>
                  <td  style="width: 1%!important"></td>
                  <td style='display: block;width: 20px;'>&nbsp;</td>
<?php foreach ($arrayMonths as $key => $daysMonth): ?>
  <?php 
//  $monthX = Carbon::createFromFormat('m', str_pad($key, 2, "0", STR_PAD_LEFT))->formatLocalized('%B');
    $monthX = getMonthsSpanish($key,false);
  ?>
                    <td id="month-<?php echo $key ?>" colspan="<?php echo $daysMonth ?>" class="text-center months" style="border-right: 1px solid black;border-left: 1px solid black;padding: 5px 10px;">
                      <?php if ($key != 2): ?>
                        <span class="font-w600 pull-left" style="padding: 5px;"> <?php echo $monthX ?> </span>
                        <span class="font-w600" style="padding: 5px;"> <?php echo $monthX ?> </span>
                        <span class="font-w600 pull-right" style="padding: 5px;"> <?php echo $monthX ?> </span>
                      <?php else: ?>
                        <span class="font-w600 pull-left" style="padding: 5px;"> febrero </span>
                        <span class="font-w600" style="padding: 5px;"> febrero </span>
                        <span class="font-w600 pull-right" style="padding: 5px;"> febrero </span>
  <?php endif ?>
                    </td>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td style='display: block;width: 20px;'>&nbsp;</td>
                  <td rowspan="2" style="width: 1%!important"></td>
                  <?php foreach ($arrayMonths as $key => $daysMonth): ?>
                    <?php for ($i = 1; $i <= $daysMonth; $i++): ?>
                      <td style='border:1px solid black;width: 24px; height: 20px;font-size: 10px;padding: 5px!important' class="text-center min-w25">
    <?php echo $i ?>
                      </td>
                    <?php endfor; ?>
                  <?php endforeach ?>
                </tr>
                <tr>
                  <td style='display: block;width: 20px;'>&nbsp;</td>
                  <?php foreach ($arrayMonths as $key => $daysMonth): ?>
                    <?php for ($i = 1; $i <= $daysMonth; $i++): ?>
                      <td style='border:1px solid black;width: 24px; height: 20px;font-size: 10px;padding: 5px!important' class="text-center <?php echo $days[$key][$i] ?> min-w25">
    <?php echo $days[$key][$i] ?>
                      </td>
                  <?php endfor; ?>
                <?php endforeach ?>
                </tr>
              </thead>
              <tbody>
                <?php $luxAux = 1;
                $typeAux = 2; ?>
                <?php foreach ($roomscalendar as $key => $room): ?>
                  <?php $inicio = $inicioAux->copy() ?>

                  <?php if ($room->luxury != $luxAux || $room->sizeApto != $typeAux): ?>
                    <?php $line = "line-divide"; ?>
                  <?php else: ?>
                    <?php $line = ""; ?>
  <?php endif ?>
  <?php
  $luxAux = $room->luxury;
  $typeAux = $room->sizeApto;
  ?>
                  <tr class="<?php echo $line ?>">

                    <td class="text-center fixed-td">
                      <button class="font-w800 btn btn-xs getImages" type="button" data-toggle="modal" data-target="#modalRoomImages" style="z-index: 99; border: none; background-color: white; color:black;padding: 0;" data-id="<?php echo $room->id; ?>">
                        <i class="fa fa-eye"></i>
                      </button>
                      <b style="cursor: pointer;" data-placement="right" title="" data-toggle="tooltip" data-original-title="<?php echo $room->name ?>">
                    <?php echo substr($room->nameRoom, 0, 5) ?>
                      </b>

                    </td>
                    <td style='width: 20px;'>&nbsp;</td>
                    <?php foreach ($arrayMonths as $key => $daysMonth): ?>
                      <?php for ($i = 01; $i <= $daysMonth; $i++): ?>
                        <!-- Si existe la reserva para ese dia -->
                          <?php if (isset($arrayReservas[$room->id][$inicio->copy()->format('Y')][$key][$i])): ?>

                            <?php $calendars = $arrayReservas[$room->id][$inicio->copy()->format('Y')][$key][$i] ?>
                                                   <!-- Si hay una reserva que sale y una que entra  -->
                          <?php if (count($calendars) > 1): ?>
                            <td style='border:1px solid grey;width: 24px; height: 20px;'>
                              @include('backend.blocks._calendarEventDouble', ['calendars' => $calendars,'inicio'=>$inicio ])
                            </td>
                          <?php else: ?>
                              @include('backend.blocks._calendarEvent', ['calendars' => $calendars,'inicio'=>$inicio ])
                          <?php endif ?>
                          <?php else: ?>
                          <!-- Si no existe nada para ese dia -->
                          <td class="<?php echo $days[$key][$i] ?>" style='border:1px solid grey;width: 24px; height: 20px;'>
                          </td>

                        <?php endif; ?>
                          <?php $inicio = $inicio->addDay(); ?>

    <?php endfor; ?>
  <?php endforeach ?>
                  </tr>

                  <?php endforeach; ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<script type="text/javascript">

//  $('.btn-fechas-calendar').click(function (event) {
//    event.preventDefault();
//    $('.btn-fechas-calendar').css({
//      'background-color': '#899098',
//      'color': '#fff'
//    });
//    $(this).css({
//      'background-color': '#10cfbd',
//      'color': '#fff'
//    });
//    var target = $(this).attr('data-month');
//    var targetPosition = $('.content-calendar #month-' + target).position();
//     alert("Left: "+targetPosition.left+ ", right: "+targetPosition.right);
//    $('.content-calendar').animate({scrollLeft: "+=" + targetPosition.left + "px"}, "slow");
//  });




  // Ver imagenes por piso

  $('.getImages').click(function (event) {
    var idRoom = $(this).attr('data-id');
    $.get('/admin/rooms/api/getImagesRoom/' + idRoom, function (data) {
      $('#modalRoomImages .modal-content').empty().append(data);
    });
  });

  $('#btn-active').trigger('click');
</script>
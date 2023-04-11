<tfoot>

  <tr>
    <td style='display: block;width: 20px;'>&nbsp;</td>
    <td rowspan="2" style="width: 1%!important"></td>
    <?php foreach ($arrayMonths as $key => $daysMonth): ?>
      <?php for ($i = 1; $i <= $daysMonth; $i++): ?>
        <td class="td-month">
          <?php echo $i ?>
        </td>
      <?php endfor; ?>
    <?php endforeach ?>
  </tr>
  <tr>
    <td style='display: block;width: 20px;'>&nbsp;</td>
    <?php foreach ($arrayMonths as $key => $daysMonth): ?>
      <?php for ($i = 1; $i <= $daysMonth; $i++): ?>
        <td class="td-month <?php echo $days[$key][$i] ?>">
          <?php echo $days[$key][$i] ?>
        </td>
      <?php endfor; ?>
    <?php endforeach ?>
  </tr>
    <tr>
    <td  style="width: 1%!important"></td>
    <td style='display: block;width: 20px;'>&nbsp;</td>
    <?php foreach ($arrayMonths as $key => $daysMonth): ?>
      <?php
      $monthX = getMonthsSpanish($key, false);
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
</tfoot>
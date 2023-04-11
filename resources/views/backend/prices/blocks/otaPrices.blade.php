<?php
$auxI = 0;

while ($auxI < $tDays) {
  $colspan = 3;
  if ($auxI + $colspan > $tDays) {
    $colspan = $tDays - $auxI;
    if ($colspan<1) break;
  }
  $auxI += $colspan;
  ?>
  <td colspan="{{$colspan}}"><span class="price-booking">{{$item['price_booking']}}</span></td>
  <?php
  $colspan = 3;
  if ($auxI + $colspan > $tDays) {
    $colspan = $tDays - $auxI;
    if ($colspan<1) break;
  }
  $auxI += $colspan;
  ?>
  <td colspan="{{$colspan}}"><span class="price-airbnb">{{$item['price_airbnb']}}</span></td>
  <?php
  $colspan = 3;
  if ($auxI + $colspan > $tDays) {
    $colspan = $tDays - $auxI;
    if ($colspan<1) break;
  }
  $auxI += $colspan;
  ?>
  <td colspan="{{$colspan}}"><span class="price-expedia">{{$item['price_expedia']}}</span></td>
  <?php
  $colspan = 3;
  if ($auxI + $colspan > $tDays) {
    $colspan = $tDays - $auxI;
    if ($colspan<1) break;
  }
  $auxI += $colspan;
  ?>
  <td colspan="{{$colspan}}"><span class="price-google">{{$item['price_google']}}</span></td>
  <?php
  $colspan = 3;
  if ($auxI + $colspan > $tDays) {
    $colspan = $tDays - $auxI;
    if ($colspan<1) break;
  }
  $auxI += $colspan;
  ?>
  <td colspan="{{$colspan}}"></td>
  <?php
}
?>
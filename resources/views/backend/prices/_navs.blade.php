<style>
  .buttons-lst .active{
    color: #6d5cae !important;
    border: 1px solid;
  }
  .buttons-lst a.text-white{
        padding: 4px;
    font-size: 13px;
  }
  @media only screen and (max-width: 425px){
    .buttons-lst {
        width: 63em !important
    }
  }
</style>

<?php
$countLogLines = \App\PricesOtas::count();
$route = \Request::route()->getName();
$lstBtn = [
    'precios.base'=>'PRECIO BASE X TEMP',
    'channel.price.cal'=>'UNITARIA',
    'channel.price.site'=>'EDIFICIO',
    'channel.promotions'=>'PROMOCIONES',
    'precios.pricesOTAs'=>'PRECIOS OTAs',
    'channel.index'=>'DISPONIBILIDAD',
    'channel.price.diff'=>"OTA Control($countLogLines)"
];
?>
<div class="buttons-box">
  <div class="buttons-lst">
    <?php foreach ($lstBtn as $k=>$v): ?>
      <?php if ($route == $k): ?>
        <a class="btn btn-md text-white active" href="#" disabled>{{$v}}</a>
      <?php else: ?>
        <a class="text-white btn btn-md btn-primary" href="{{route($k)}}">{{$v}}</a>
      <?php endif ?>	
  <?php endforeach; ?>
  <a class="text-white btn btn-md btn-primary" href="/procesarReservasTemporada">Procesar Reservas</a>
    <?php if (isset($extr)) echo $extr; ?>
  </div>
</div>
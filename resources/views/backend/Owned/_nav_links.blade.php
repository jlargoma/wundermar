<?php
$uri_propiet = url('admin/propietario').'/'.$room->nameRoom ;
?>

<li class="btn-nav btn-blocks"  data-block="resumen">Area Propietario</li>
<li class="btn-nav btn-blocks"  data-block="reservas">Reservas</li>
<li class="btn-nav btn-blocks"  data-block="estadisticas">Estadisticas</li>
<li class="btn-nav btn-blocks"  type="button" data-toggle="modal" data-target="#modalBloq">Bloq. fechas</li>
<li class="btn-nav btn-blocks"  type="button" data-toggle="modal" data-target="#modalLiquidation">Liquidación</li>
<li class="btn-nav btn-content"  data-url="{{ $uri_propiet }}/operativa">Operativa</li>
<li class="btn-nav btn-content"  data-url="{{ $uri_propiet }}/tarifas">Tarifas</li>
<li class="btn-nav btn-content"  data-url="{{ $uri_propiet }}/descuentos">Descuentos</li>
<li class="btn-nav btn-content"  data-url="{{ $uri_propiet }}/fiscalidad">Fiscalidad</li>
<li class="btn-nav btn-content"  data-url="{{ $uri_propiet }}/facturas">Facturas</li>

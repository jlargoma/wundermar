<?php use \Carbon\Carbon; setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); $controller = new
\App\Http\Controllers\PaylandsController()?>
Hola "<?php echo $book->customer->name ?>" hemos bloqueado parcialmente un apartamento en respuesta a tu solicitud:<br/><br/>

<h2><u>Pago de la reserva</u></h2>
<b>Dispones de un plazo de 4 horas para realizar el pago de la señal </b>
<?php
	$percent = 0.5;
	$date = Carbon::createFromFormat('Y-m-d', $book->start);
	$now = Carbon::now();
	$rule = \App\RulesStripe::find(1);
	$diff = $now->diffInDays($date);

	if ( $diff <= $rule->numDays ) {
		$percent = ($rule->percent/100);
	}else{
        $percent = 0.50;
    }
?>
<b style="color: red"><?php echo $percent*100; ?>% del total =  <?php echo number_format(($book->total_price*$percent),2,',','.') ?> €</b>
a través de nuestra pasarela de pago para tarjetas.<br><br>
	<?php

	$urlToRedirect        = $controller->generateOrderPayment([
                                                        'customer_id' => $book->customer->id,
                                                        'amount'      => (round($book->total_price * $percent)),
                                                        'url_ok'      => route('payland.thanks.payment', ['id' => $book->id]),
                                                        'url_ko'      => route('payland.thanks.payment', ['id' => $book->id]),
														]);
	?>
	<a href="{{ $urlToRedirect }}">
		PAGA AHORA
	</a>
<br><br>

<b><strong>Una vez recibamos el pago de la señal, el apartamento quedará bloquedo y tu recibiras un email con la confirmación.</strong><br>

Consulta nuestras condiciones de contratación <a href="{{ url('/condiciones-generales') }}">aquí</a></b><br><br>

<hr><br>

<b>Nombre: <?php echo $book->customer->name ?> .<br><br>
Teléfono: <a href="tel:<?php echo $book->customer->phone ?>"><?php echo $book->customer->phone ?></a>.<br><br>
Email: <?php echo $book->customer->email ?>.<br><br>
Apartamento: <?php echo $book->room->sizeRooms->name ?> // <?php echo ($book->type_luxury == 1)? "Lujo" : "Estandar" ?><br><br>
Nº: <?php echo $book->pax ?> Pers <br><br>
Fechas: <?php echo Carbon::createFromFormat('Y-m-d',$book->start)->format('d-M') ?> - <?php echo Carbon::createFromFormat('Y-m-d',$book->finish)->format('d-M') ?> <br><br>
Noches: <?php echo $book->nigths ?>  <br><br>
<?php if ($book->type_luxury != 2): ?>
	Sup. Lujo: <?php echo number_format($book->sup_lujo,2,',','.') ?> €<br><br>
<?php endif ?>
Precio total: <?php echo number_format($book->total_price,2,',','.') ?> € <br><br></b>
El precio te incluye todo, piscina climatizada, gimnasio, taquilla guarda esquíes <?php if ($book->type_park != 2): ?>y parking cubierto <?php endif ?>  . <br>
<br>
En todas nuestras reservas están incluidas las Sábanas y toallas. <br>
<br>

<hr style="width: 100%">

<h2><b><u>Condiciones generales de Alquiler</u></b></h2>
Para realizar una reserva se debe de abonar el 50% del importe total.<br>
El segundo pago con el 50% restante, se realizará 15 días antes de la entrada.<br><br>

<b>Hora de Entrada: La entrega de llaves la realizamos en el propio edifico entre las 17.30 a 19.30 Horas</b><br><br>
La entrega de llaves fuera de horario puede llevar gastos por el tiempo de espera.<br><br>

10€ Si llegas entre 20:00 h de las 22.00<br><br>

20€ Si llegas más tarde de de las 22 h<br><br>

No se entregan llaves a partir de las 00.00 sin previo aviso (el día anterior a la entrada)<br><br>

El cargo se le abonan directamente en metálico a la persona que te entrega las llaves.<br><br>

Nos sabe muy mal tener que cobrarte este recargo, Esperamos que entiendas que es solo para compensar el tiempo de espera de esta persona.<br><br>

<b>Hora de Salida: La vivienda deberá ser desocupada antes de las 11,59 a.m.</b> (de lo contrario se podrá cobrará una noche más de alquiler apartamento según tarifa apartamento y ocupación.<br><br>

La plaza de garaje debe quedar libre a esta hora o bien pagar la estancia de un nuevo día. (según tarifa 20€ / día.)<br><br>

<b>Fianza:</b> El día de llegada se pedirá una tarjeta para la fianza por importe de 300€, no se captura saldo, tan solo se hace una “foto” que desaparecerá a la entrega de llaves, una vez revisada la vivienda.<br><br>

<b>Nº de personas:</b> El apartamento no podrá ser habitado por más personas de las camas que dispone y/o de las que han sido contratadas.<br><br>

<b>No se admiten animales.</b><br><br>

<b>Sabanas y Toallas están incluidas</b><br><br>

En el caso de NO cumplir con lo establecido no se podrá ocupar la vivienda.<br><br>

<b>Consulta nuestras condiciones de contratación <a href="{{ url('/condiciones-generales') }}">aquí</a></b>

<hr style="width: 100%">

<h2><b><u>Servicios Adicionales</u></b></h2>

Te ofrecemos sin coste añadido precios especiales que hemos pactado con el proveedor para vosotros:<br><br>

*<b>Forfaits: te los llevamos a tu apartamento, evitando colas</b><br>

*<b>Clases de esquí</b><br>

*<b>Alquiler de material</b><br>

Para solicitar alguno de estos servicios solo es necesario que rellenes un formulario pinchando <a href="{{ url('/forfait') }}">aquí</a>

Para tu comodidad <b>te llevamos el forfait a tu apartamento</b>, no tienes que esperar colas<br>

<hr style="width: 100%"><br>
Gracias por confiarnos tus vacaciones, haremos todo lo posible para que pases unos días agradables.<br>
<a href="www.apartamentosierranevada.net">www.apartamentosierranevada.net</a>

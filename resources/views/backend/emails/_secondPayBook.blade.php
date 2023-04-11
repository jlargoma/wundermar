<?php 
	use \Carbon\Carbon;
	setlocale(LC_TIME, "ES"); 
    setlocale(LC_TIME, "es_ES"); 
?>
<?php
	$totalPayment = 0;
	$payments = \App\Payments::where('book_id', $book->id)->get();
	if ( count($payments) > 0) {

		foreach ($payments as $key => $pay) {
			$totalPayment += $pay->import;
		}

	}
	$percent = round(($totalPayment/$book->total_price) * 100);
?>
	Hola , te enviamos este email para recordate que tienes que realizarnos el pago del <?php echo 100 - $percent ?>% restante de tu reserva :<br><br>

	<b>Nombre:</b> <?php echo strtoupper($book->customer->name) ?><br><br>

	<b>Fecha entrada:</b> <?php echo Carbon::CreateFromFormat('Y-m-d',$book->start)->formatLocalized('%d %b') ?><br><br>

	<b>Fecha salida:</b> <?php echo Carbon::CreateFromFormat('Y-m-d',$book->finish)->formatLocalized('%d %b') ?><br><br>

	<b>Noches:</b> <?php echo $book->nigths ?><br><br>

	<b>Ocupantes:</b> <?php echo $book->pax ?><br><br>

	<b>Apartamento: </b> <?php echo $book->room->sizeRooms->name ?> // <?php echo ($book->type_luxury == 1)? "Lujo" : "Estandar" ?><br><br>

	<b>Total reserva:</b> <?php echo number_format($book->total_price,2,',','.') ?>€<br><br>
	

	<b>-------------------------</b><br>
	<b>Cobrado: </b><?php echo number_format($totalPayment,2,',','.') ?>€<br>
	<b>-------------------------</b><br>
	<?php $pendiente = ($book->total_price - $totalPayment);?>
	<h2 style="color:red"><b>Pendiente: </b><?php echo number_format(($book->total_price - $totalPayment),2,',','.') ?>€</h2><br>
	<b>-------------------------</b><br>
	Para realizar el pago del restante <?php echo 100 - $percent ?>% haz clic en el siguiente link <br><br>

	<a target="_blank" href="https://miramarski.com/reservas/stripe/pagos/<?php echo base64_encode($book->id) ?>/<?php echo base64_encode(round($pendiente)); ?>">
        https://miramarski.com/reservas/stripe/pagos/<?php echo base64_encode($book->id) ?>/<?php echo base64_encode(round($pendiente)); ?>
    </a>

	<br><br>
	Si no se recibe el pago 15 días antes de tu entrada, se podrá porcedera cancelar la misma<br><br>
	Consulta nuestras condiciones de contratación <a href="https://www.apartamentosierranevada.net/condiciones-generales">aquí</a><br><br>

	Muchas Gracias !!!.<br><br>

	Un cordial saludo.<br><br>

	<hr><br><b>Hora de Entrada:</b> Desde las <b>17,00h a 19,00h.</b> Si vas a llegar más tarde tienes que avisarnos y podrías tener un cargo adicional por las horas de espera. Consultar  <a href="https://www.apartamentosierranevada.net/condiciones-generales">link</a> condiciones<br><br>

	<b>Hora de Salida:</b> La vivienda deberá ser desocupada antes de las <b>12,00 a.m</b>.<br><br>

	<b>Fianza:</b> Además del precio del alquiler el día de llegada <b>se pedirá una fianza por el importe de 300€</b> a la entrega de llaves para garantizar el buen uso de la vivienda. La fianza se devolverá a la entregada de llaves, una vez revisada la vivienda <br><br>

	<b>Sabanas y Toallas: </b>En las reservas confirmadas las sabanas y toallas <b>ESTAN INCLUIDAS</b> <br><br>

	Pago: La cantidad total de la reserva deberá estar desembolsada para poder ocupar el apartamento.<br><hr><br><strong>Servicios adicionales:</strong><br>Para tu comodidad te ofrecemos los siguientes servicios<br><br>

	<strong>Para tu comodidad te ofrecemos sin coste añadido los siguientes servicios:</strong><br><br>

	<strong>*Tramitar tu forfait para que no esperes colas</strong><br><strong>*Gestionar tus clases de ski (Escuela Española Ski)</strong><br><strong>*Alquiler de material</strong><br><br>

	Para solicitar uno de estos servicios solo es necesario que rellenes un fomulario <a href="https://www.apartamentosierranevada.net/forfait">pinchando aqui</a><br><br>

	A la entrega de llaves se pedirá una fianza por 300 €.<br> 
    Ver condiciones alquiler en en este <a href="https://www.apartamentosierranevada.net/condiciones-generales">link</a><h3>Queremos que disfrutes de tu estancia.</h3>
</body>
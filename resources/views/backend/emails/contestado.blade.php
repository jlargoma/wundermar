<?php use \Carbon\Carbon;  setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); ?>

Hola <?php echo $book->customer->name ?>, <b>Si hay disponibilidad para tu reserva en apartamento <?php echo $book->room->sizeRooms->name ?> // <?php echo ($book->type_luxury == 1)? "Lujo" : "Estandar" ?>.</b><br>
<br>

Nombre: <b><?php echo $book->customer->name ?></b> .<br><br>
<u>Teléfono</u>: <b><a href="tel:<?php echo $book->customer->phone ?>"><?php echo $book->customer->phone ?></a></b>.<br><br>
Email: <b><?php echo $book->customer->email ?></b>.<br><br>
Apartamento: <b><?php echo $book->room->sizeRooms->name ?> // <?php echo ($book->type_luxury == 1)? "Lujo" : "Estandar" ?></b><br><br>
Nº: <b><?php echo $book->pax ?> Pers </b><br><br>
Fechas: <b><?php echo Carbon::createFromFormat('Y-m-d',$book->start)->format('d-M') ?> - <?php echo Carbon::createFromFormat('Y-m-d',$book->finish)->format('d-M') ?></b> <br><br>
Noches: <b><?php echo $book->nigths ?> </b> <br><br>
<?php if ($book->type_luxury != 2): ?>
	Sup. Lujo: <b><?php echo number_format($book->sup_lujo,2,',','.') ?> €</b><br><br>
<?php endif ?>
<b>Precio total: <?php echo number_format($book->total_price,2,',','.') ?> € </b><br><br>
El precio te incluye todo, piscina climatizada, gimnasio, taquilla guarda esquíes <?php if ($book->type_park != 2): ?>y parking cubierto <?php endif ?>  . <br>
<br>
En todas nuestra reservas están incluidas las Sábanas y toallas. <br>
<br>

Posteriormente a tu contratación <b>te ofrecemos descuentos para la compra de tus forfaits, en cursillos de esquí o alquiler de material.</b><br><br>

Quedamos a la espera de tu respuesta <br><br>

Un saludo <br><br>

<hr style="width: 100%">

<h3>Gracias por confiarnos tus vacaciones, haremos todo lo posible para que pases unos días agradables. </h3>

<a href="www.apartamentosierranevada.net">www.apartamentosierranevada.net</a>
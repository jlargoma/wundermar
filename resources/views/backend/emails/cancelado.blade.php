<?php use \Carbon\Carbon;  setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); ?>
Hola, como estas.<br>
Lo sentimos mucho, todos nuestros apartamentos están completos para esas fechas.<br><br>

Nombre: <b><?php echo $book->customer->name ?></b> .<br><br>

<u>Teléfono</u>: <b><a href="tel:<?php echo $book->customer->phone ?>"><?php echo $book->customer->phone ?></a></b>.<br><br>

Email: <b><?php echo $book->customer->email ?></b>.<br><br>

Apartamento: <b><?php echo $book->room->sizeRooms->name ?> // <?php echo ($book->type_luxury == 1)? "Lujo" : "Estandar" ?></b><br><br>

Nº: <b><?php echo $book->pax ?> Pers </b><br><br>

Fechas: <b>
            <?php echo Carbon::createFromFormat('Y-m-d',$book->start)->format('d-M') ?> -
            <?php echo Carbon::createFromFormat('Y-m-d',$book->finish)->format('d-M') ?>
        </b> <br><br>

Noches: <b><?php echo $book->nigths ?> </b> <br><br>

<?php if ($book->type_luxury != 2): ?>
    Sup. Lujo: <b><?php echo number_format($book->sup_lujo,2,',','.') ?> €</b><br><br>
<?php endif ?>
<b>Precio total: <?php echo number_format($book->total_price,2,',','.') ?> € </b><br><br>

Si tienes posibilidad para modificar tus fechas, por favor escríbenos a este email  <a href="mailto:reservas@apartamentosierranevada.net">reservas@apartamentosierranevada.net</a>  o llámanos a este teléfono <a href="tel:687768363">687768363</a> y trataremos de ayudarte.<br><br>
Un cordial saludo<br><br>
Gracias por confiarnos tus vacaciones, haremos todo lo posible para que pases unos días agradables.

<?php use \Carbon\Carbon;  setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); ?>

El propietario <?php echo $book->user->name ?> ha bloqueado su apartamento .<br><br>

Fechas: <b><?php echo Carbon::createFromFormat('Y-m-d',$book->start)->formatLocalized('%d %B') ?> - <?php echo Carbon::createFromFormat('Y-m-d',$book->finish)->formatLocalized('%d %B') ?></b> <br><br>

Gestiona la reserva desde : <a href="www.apartamentosierranevada.net/admin">www.apartamentosierranevada.net/admin</a>
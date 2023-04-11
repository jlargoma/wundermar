<?php use \Carbon\Carbon ;?>

<h2>Reserva de Propietario</h2>

Hola Jaime, un propietario ha bloqueado su apartamento "<?php echo $book->room->name ?>".<br><br>

<?php $start = Carbon::createFromFormat('Y-m-d',$book->start) ?>
<?php $finish = Carbon::createFromFormat('Y-m-d',$book->finish) ?>
<b>Nombre:</b> <?php echo $book->customer->name ?> <br /><br />
<b>Fecha entrada:</b> <?php echo $start->format('d-m-Y') ?>  <br /><br />
<b>Fecha salida:</b> <?php echo $finish->format('d-m-Y') ?> <br /><br />
<b>Noches:</b> <?php echo $book->nights ?> <br /><br />
<b>Ocupantes:</b> <?php echo $book->pax ?> <br /><br />
<b>Observaciones cliente:</b> <?php echo $book->comment ?>  <br/><br/>      
<b>Observaciones internas :</b> <?php echo $book->book_comments ?>  <br/><br/>    

                
<hr/>
<br/>Gestiona la solicitud <a href='http://www.apartamentosierranevada.net/admin'>pinchando aquí</a><br/>
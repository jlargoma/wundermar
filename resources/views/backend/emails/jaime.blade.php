<?php use \Carbon\Carbon ;?>
<?php $start = Carbon::createFromFormat('Y-m-d',$book->start) ?>
<?php $finish = Carbon::createFromFormat('Y-m-d',$book->finish) ?>
<b>Nombre:</b> <?php echo $book->customer->name ?> <br /><br />
<b>Fecha entrada:</b> <?php echo $start->format('d-m-Y') ?>  <br /><br />
<b>Fecha salida:</b> <?php echo $finish->format('d-m-Y') ?> <br /><br />
<b>Noches:</b> <?php echo $book->nights ?> <br /><br />
<b>Ocupantes:</b> <?php echo $book->pax ?> <br /><br />
<b>Observaciones cliente:</b> <?php echo $book->comment ?>  <br/><br/>      
<b>Observaciones internas :</b> <?php echo $book->book_comments ?>  <br/><br/>    

Hola Jaime dime si tienes disponibilidad para esta solicitud:<br /><br />
                
<hr/>
<br/>Gestiona la solicitud <a href='http://www.admin.apartamentosierranevada.net/index.php/booking/book/planning'>pinchando aquí</a><br/>
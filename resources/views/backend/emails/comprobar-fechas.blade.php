<?php use \Carbon\Carbon ;?>
<h1>comprobacion de fechas</h1>

<?php $isRooms = \App\Book::where('room_id',$book->room_id)->whereIn('type_book',[1,2,4,6,7,8])->where('id','!=' ,$book->id)->orderBy('start','ASC')->get(); ?>

<?php echo "fecha actual ".$book->start." - ".$book->finish ?>
<?php 
	$e1 = Carbon::createFromFormat('Y-m-d',$book->start)->format('U');
	$s1 = Carbon::createFromFormat('Y-m-d',$book->finish)->format('U');
 ?><br>

	<?php foreach ($isRooms as $room): ?>
		<?php $start = Carbon::createFromFormat('Y-m-d',$room->start); ?>
		<?php $finish = Carbon::createFromFormat('Y-m-d',$room->finish); ?>

		<?php $e2 = $start->copy()->format('U') ?>
		<?php $s2 = $finish->copy()->format('U') ?>

		<?php if ($e2 < $e1 && $e1 < $s2): ?>
			<?php echo "se pisan" ?>
		<?php elseif($e1 < $e2 && $e2 < $s1): ?>
			<?php echo "se pisan" ?>
		<?php elseif($e2 < $e1 && $e1 < $e2): ?>
			<?php echo "se pisan" ?>
		<?php elseif($e1 < $e2 && $e2 < $s1): ?>
			<?php echo "se pisan" ?>
		<?php endif ?>
	<?php endforeach ?>

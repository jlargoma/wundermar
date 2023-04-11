@if (getUsrRole() == "limpieza")
    @foreach ($rooms as $room)
      @if ($room->id == $select)
      <?php echo substr($room->nameRoom . " - " . $room->name, 0, 15) ?>
      @endif
    @endforeach
@else
  <select class="room form-control minimal" data-order="{{$bookID}}" data-id="{{$bookID}}"   >
    <?php if (getUsrRole() != "agente"): ?>
      <?php foreach ($rooms as $room): ?>
          <option value="<?php echo $room->id ?>"
             <?php if ($room->id == $select) echo ' selected '; ?>     
             <?php if ($room->state == 0) echo ' disabled '; ?>     
           >
            <?php echo substr($room->nameRoom . " - " . $room->name, 0, 15) ?>
          </option>
      <?php endforeach ?>
    <?php else: ?>
      <option selected value="<?php echo $book->room_id ?>" data-id="<?php echo $book->room->name ?>">
        <?php echo substr($book->room->nameRoom . " - " . $book->room->name, 0, 15) ?>
      </option>
    <?php endif ?>
  </select>
@endif
<ul>
  @if($books)
    @foreach($books as $book)
    <li class="item_interc_{{$block}}"  data-id="{{$book->id}}">
      <div>
        {{$book->customer->name}}
      </div>
      <div>
        <?php
        if ($book->room) {
          $room = $book->room;
          echo '<b>' . substr($room->nameRoom . " - " . $room->name, 0, 15) . '</b>';
        }
        ?>
        <?php echo dateMin($book->start) . ' - ' . dateMin($book->finish) ?>
      </div>
    </li>
    @endforeach
  @else
    @if($book)
    <li class="item_interc_{{$block}}" data-id="{{$book->id}}">
      <div>{{$book->customer->name}}</div>
      <div>
        <?php
        if ($book->room) {
          $room = $book->room;
          echo '<b>' . substr($room->nameRoom . " - " . $room->name, 0, 15) . '</b>';
        }
        ?>
        <?php echo dateMin($book->start) . ' - ' . dateMin($book->finish) ?>
      </div>
    </li>
    @endif
  @endif
</ul>
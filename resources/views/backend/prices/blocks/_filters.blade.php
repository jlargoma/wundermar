<form id="prom_filters" method="get" action="">
  <input type="hidden" id="room_gr_sel" name="room_gr_sel" value="{{$room_gr_sel}}">
  <div class="filter-field">
    <label>OTA</label>
    <select name="ch_sel" id="ch_sel" class="form-control">
      <option value=""> -- </option>
      <?php foreach ($aChannels as $id=>$name): ?>
        <option value="{{$id}}" @if($id == $ch_sel) selected @endif>{{$name}}</option>
      <?php endforeach ?>
    </select>
  </div>
  <div class="filter-field">
    <label>Edificio</label>
    <select name="site" id="site" class="form-control">
      <option value=""> -- </option>
      <?php foreach ($aSites as $id=>$name): ?>
        <option value="{{$id}}" @if($id == $site) selected @endif>{{$name}}</option>
      <?php endforeach ?>
    </select>
  </div>
</form>
<div class="clearfix"><br/></div>
<br/>
<span class="tabRooms @if(!$room_gr_sel) active @endif" data-k="">
  TODOS
</span>
@foreach($aRooms as $room_gr=>$name)
<span class="tabRooms mb-3 @if($room_gr == $room_gr_sel) active @endif"  data-k="{{$room_gr}}">
  {{$name}}
</span>
@endforeach
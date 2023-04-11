<div class="table-responsive" style="padding: 1em;">
  <table class="table ">
    <thead>
      <tr>
        <th>Tipo de Habitacion</th>
        <th class="th-1">Nombre</th>
        <th class="th-2">Min PAX</th>
        <th class="th-2">Max PAX</th>
        <th class="th-3">URL Web</th>
        <th class="th-2">URL SLUG</th>
      </tr>
    </thead>
    <tbody>
      @if($ch_group)
      @foreach($ch_group as $k=>$name)
      <tr>
        <td>{{$name}}</td>
        
        <td class="text-center">
          <input class="editable  th-1" data-id="{{$k}}"  data-type="title" value="{{$title[$k]}}" >
        </td>
        <td class="text-center">
          <input class="editable  th-2" data-id="{{$k}}"  data-type="minPax" value="{{$minPax[$k]}}" >
        </td>
        <td class="text-center ">
          <input class="editable th-2" data-id="{{$k}}" data-type="maxPax" value="{{$maxPax[$k]}}" >
        </td>
        <td class="text-center ">
          <input class="editable th-3" data-id="{{$k}}" data-type="url" value="{{$url[$k]}}" >
        </td>
        <td class="text-center ">
          <input class="editable th-2" data-id="{{$k}}" data-type="slug" value="{{$slug[$k]}}" >
        </td>
      </tr>
      @endforeach
      @endif
    </tbody>
  </table>
</div>

<style>
  .editable{
    background-color: #f3f3f3;
    margin: 2px 7px;
    border-style: none;
    padding: 0px 12px;
    cursor: pointer;
  }
  .th-1 {width: 21em;}
  .th-2 {width: 6em;}
  .th-3 {width: 15em;}
</style>

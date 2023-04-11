<table class="table table-parkings">
  <thead>
    <tr>
      <th rowspan="2">PARKING</th>
      <th rowspan="2">SALDO</th>
      <th rowspan="2">VENTAS</th>
      <th colspan="3" class="text-center">COMPRAS</th>
    </tr>
    <tr>
      <th>UNID</th>
      <th>P.UNIT</th>
      <th>IMP.TOTAL</th>
    </tr>
  </thead>
  <tbody>
    @if($pakingsLst)
    @foreach($pakingsLst as $item)
    <tr>
      <td>{{$item['name']}}</td>
      <td>{{$item['qtySaldo']}}</td>
      <td>{{$item['qtySold']}}</td>
      <td>{{$item['qtyBuy']}}</td>
      <td>{{$item['cost']}}</td>
      <td>{{$item['buy']}}</td>
    </tr>
    @endforeach
    @endif
    <tr>
      
    </tr>
  </tbody>
</table>
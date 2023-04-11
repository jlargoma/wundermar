<div class="table-responsive">
  <table class="table" id="tableItems">
    <tr>
      <th class="static" style="background-color: #FFF;height: 21px;margin-top: 10px !IMPORTANT;">Creada</th>
      <th class="first-col"></th>
      <th>CLIENTE</th>
      <th>Check In</th>
      <th>Check Out</th>
      <th>Edificio</th>
      <th>Nº NOCHES</th>
      <th>ADR</th>
      <th>PVP RVA</th>
      <th>ESTADO DE RESERVA</th>
      <th>CANAL</th>
      <th>ORIGEN: Cliente</th>
    </tr>
    <tbody>
      @if(count($lstResul))
      @foreach($lstResul as $r)
      <tr>
          
        
                
        <th class="static"  style="background-color: #fafafa;">{{$r['create']}}</th>
        <td class="first-col"></td>
        <td class="text-left">{{$r['name']}}</td>
        <td>{{$r['in']}}</td>
        <td>{{$r['end']}}</td>
        <td>{{$r['site_id']}}</td>
        <td>{{$r['nigth']}}</td>
        <td>{{moneda($r['adr'])}}</td>
        <td>{{moneda($r['price'])}}</td>
        <td>{{$r['status']}}</td>
        <td>{{$r['ch']}}</td>
        <td>{{$r['country']}}</td>
      </tr>
      @endforeach
      @endif
    </tbody>
  </table>
</div>
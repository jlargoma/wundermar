<div class="table-responsive">
  <table class="table" id="tableItems">
    <tr>
      <th class="static" style="background-color: #FFF;height: 21px;margin-top: 10px !IMPORTANT;">Días</th>
      <th class="first-col"></th>
      <th>Eventos</th>
      <th>Pick Up</th>
      <th>Cancelaciones</th>
      <th>LLEGADAS</th>
      <th>LIBRES</th>
      <th>Nº Hab</th>
      <th>% Ocup</th>
      <th>Precio Medio</th>
      <th>Revenue</th>
    </tr>
    <tbody>
      @if(count($allRevenue))
      @foreach($allRevenue as $r)
      <tr>
        <th class="static"  style="background-color: #fafafa;">{{convertDateToShow_text($r->day)}}</th>
        <td class="first-col"></td>
        <td class="editable" data-type="event" data-time="{{$r->day}}">{{show_isset($lstPickUpEvents,$r->day)}}</td>
        <td>{{$r->ocupacion+$r->llegada}}</td>
        <td @if($r->cancelaciones>0) class="text-danger" @endif>{{$r->cancelaciones}}</td>
        <td>{{$r->llegada}}</td>
        <td>{{$r->get_libre()}}</td>
        <td>{{$r->disponibilidad}}</td>
        <td>{{$r->get_ocup_percent()}}</td>
        <td>{{moneda($r->get_ADR())}}</td>
        <td>{{moneda($r->ingresos)}}</td>
      </tr>
      @endforeach
      @endif
    </tbody>
  </table>
</div>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
  <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
</button>
<h4 class="text-center">Revisar PAXs: </h4>
<div class="table-responsive">
  <table class="table" >
    <tbody>
      <?php 
      
      $lst = $lst2 = [];
      foreach ($alarms as $v){
        if (!isset($lst[$v['link_id']])){
          $lst[$v['link_id']] = [0=>$v];
          $lst2[$v['link_id']] = [$v['customer'],$v['adults'],$v['children'],$v['reser_id']];
        } else {
          $lst[$v['link_id']][] = $v;
        }
      } 
      foreach ($lst as $k=>$v): ?>
      
        <tr>
          <th>{{$lst2[$k][0]}}</th>
          <th>PAX Total: {{$lst2[$k][1]+$lst2[$k][2]}}</th>
          <th>Adultos: {{$lst2[$k][1]}}</th>
          <th>Niños: {{$lst2[$k][2]}}</th>
          <th></th>
          <th>Reserva OTA ID: {{$lst2[$k][3]}}</th>
        </tr>
        
        <?php foreach ($v as $v2): ?>
        <tr data-link='{{$v2['link_id']}}' data-id='{{$v2['bookID']}}'>
          <td>Reserva {{$v2['bookID']}}</td>
          <td>PAX: {{$v2['pax']}}</td>
          <td>Adultos: {{$v2['pax']}}</td>
          <td>Niños: --</td>
          <td>CHANNEL: {{$v2['channel_group']}}</td>
          <td>
            <a href="/admin/reservas/update/{{$v2['bookID']}}/" target="_black" title="editar reserva"><i class="fa fa-eye"></i></a>
            <button class="btn btn-danger removeAlertPax"><i class="fa fa-trash"></i></button>
          </td>
      <?php 
        endforeach; 
      endforeach; 
      ?>
    </tbody>
  </table>
  <p style="padding: 7px;">
    En las reservas múltiples de <b>Booking.com</b>, el nro de niños no se cuentan como <b>PAX</b>, 
    por lo que hay que buscar dicha reserva y ajustar el PAX a sus equivalentes en el admin (y el desayuno, si tuviera).<br/>
    
    <b>Notas:</b><br/>
     - En el buscador de <b>Booking.com</b> se puede buscar por <b>Reserva OTA ID</b><br/>
     - El nro de PAXs coincide con el nro de adultos en el apartamento reservado de <b>Booking.com</b><br/>
    </ul>
  </p>
</div>

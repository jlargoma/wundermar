<div class="modal fade slide-up in" id="modalICalImport" tabindex="-1" role="dialog" aria-hidden="true" >
  <div class="modal-dialog modal-xd">
    <div class="modal-content-classic">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
        <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
      </button>
      <div class="row">
        <div class="col-md-7"  id="modal_ical_content"></div>
        <div class="col-md-5">
          <button id="syncr_ical" class="btn btn-primary">Sincronizar <i class="fa fa-refresh"></i></button>
          <a href="/admin/ical/importFromUrl?detail"class="btn btn-secondary">iCal con LOGs</a>
        </div>
      </div>
      <p class="alert alert-success" id="syncr_ical_succss" style="display: none;">Sincronización enviada</p>
    </div>
  </div>
</div>

<div class="modal fade slide-up in" id="modalSendPartee" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xd">
    <div class="modal-content-classic">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
        <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
      </button>
      <h3 id="modalSendPartee_title"></h3>
      <div class="row" id="modalSendPartee_content" style="margin-top:1em;">
      </div>
    </div>
  </div>
</div>
<form method="post" id="formFF" action=""  <?php
if (!$is_mobile) {
  echo 'target="_blank"';
}
?>>
  <input type="hidden" name="admin_ff" id="admin_ff">
</form>

<div class="modal fade slide-up in" id="modalSafetyBox" tabindex="-1" role="dialog" aria-hidden="true" style=" z-index: 9999;">
  <div class="modal-dialog modal-xd">
    <div class="modal-content-classic">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
        <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
      </button>
      <h3 id="modalSafetyBox_title"></h3>
      <div class="row" id="modalSafetyBox_content" style="margin-top:1em;">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalChangeBook" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <strong class="modal-title" id="modalChangeBookTit" style="font-size: 1.4em;">Cambiar Reserva</strong>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="btnChangeBook" value="">
        <div id="modalChangeBook_room" style="display:none;">
<?php foreach ($rooms as $room): ?>
  <?php if ($room->state == 0) continue; ?>
            <button 
              class="btn btnChangeRoom" 
              id="btn_CR{{$room->id}}"
              data-id="{{$room->id}}" 
              >
            <?php echo substr($room->nameRoom . " - " . $room->name, 0, 15) ?>
            </button>
<?php endforeach ?>
        </div>

        <div id="modalChangeBook_status" style="display:none;">
<?php $bookAux = new App\Book(); ?>
<?php for ($i = 1; $i < 13; $i++): ?> 
            <button 
              class="btn btnChangeStatus" 
              id="btn_CS{{$i}}"
              data-id="{{$i}}" 
              >
            <?php echo $bookAux->getStatus($i) ?>
            </button>
<?php endfor ?>
          <button 
            class="btn btnChangeStatus" 
            id="btn_CS99"
            data-id="99" 
            >
<?php echo $bookAux->getStatus(99) ?>
          </button>
          <button 
            class="btn btnChangeStatus" 
            id="btn_CS98"
            data-id="98" 
            >
<?php echo $bookAux->getStatus(98) ?>
          </button>
        </div>



      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalIntercambio" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <strong class="modal-title" style="font-size: 1.4em;">Intercambio de Habitaciones</strong>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body contentModalIntercambio">
      </div>
    </div>
  </div>
</div>
<div class="modal fade slide-up in" id="modalBookSafetyBox" tabindex="-1" role="dialog" aria-hidden="true" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content" id="_BookSafetyBox">
      </div>
    </div>
  </div>
</div>
@if (count($lstExtrs)>0)
<?php $t_class = ($is_mobile) ? '' : 'th-bookings'; ?>
<div class="modal fade slide-up in" id="modalNextsExtrs" tabindex="-1" role="dialog" aria-hidden="true" >
  <div class="modal-dialog modal-md">
    <div class="modal-content-wrapper">
      <div class="modal-content" style="padding: 7px;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
          <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
        </button>
        <h3 id="modal_bloqueo_title">Extras de reservas</h3>
        <div class="blockTableAlert">
          <table class="table tableAlertExtr" >
            <thead>
              <tr class ="text-center text-white" style="background-color: #448eff;">
                <th class="{{$t_class}} th-name" >Cliente</th>
                <th class="th-bookings"> 
                  @if($is_mobile) <i class="fa fa-phone"></i> @else Telefono @endif
                </th>
                <th class="{{$t_class}} th-6">Apart</th>
                <th class="{{$t_class}} th-4">IN</th>
                <th class="{{$t_class}} th-4">OUT</th>
                <th class="{{$t_class}} th-2">Desayuno</th>
                <th class="{{$t_class}} th-2">Parking</th>
                <th class="{{$t_class}} th-2">Excursiones</th>
                <th class="{{$t_class}} th-2"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($lstExtrs as $e)
              <tr>
                <td>{{$e->cli}}</td>
                <td>{{$e->phone}}</td>
                <td>{{$e->room}}</td>
                <td>{{$e->in}}</td>
                <td>{{$e->out}}</td>
                <td><?php echo ($e->breakfast) ? '<i class="fas fa-coffee"></i> ' . $e->breakfast : '-' ?></td>
                <td><?php echo ($e->parking) ? '<i class="fas fa-parking"></i> ' . $e->parking : '-' ?></td>
                <td><?php echo ($e->excursion) ? '<i class="fas fa-guitar"></i> ' . $e->excursion : '-' ?></td>
                <td>
                  <button data-id="{{$e->bID}}" 
                          data-delivered="{{$e->delivered}}"
                          class="btn btn-xs btn-default toggleDeliver" 
                          type="button" 
                          data-toggle="tooltip" title="" 
                          data-original-title="Activa / Desactiva Alerta de Entrega" 
                          >
                    @if($e->delivered == 1)
                    <i class="fa fa fa-bell-slash" aria-hidden="true"></i>
                    @else
                    <i class="fa fa-bell" aria-hidden="true"></i>
                    @endif
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
 </div>
@endif
  
  <div class="modal fade slide-up in" id="modalBasic" tabindex="-1" role="dialog" aria-hidden="true" style=" z-index: 9999;">
  <div class="modal-dialog modal-xd">
    <div class="modal-content-classic">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
        <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
      </button>
      <h3 id="modalBasic_title"></h3>
      <div id="modalBasic_content" style="margin-top:1em;">
      </div>
    </div>
  </div>
</div>
  
      <!-- RESPUESTA POR EMAIL AL CLIENTE  -->
    <button style="display: none;" id="btnContestado" class="btn btn-success btn-cons m-b-10" type="button" data-toggle="modal" data-target="#modalContestado"> </button>
    <div class="modal fade slide-up in" id="modalContestado" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content-wrapper">
          <div class="modal-content contestado">
            <div class="modal-content">
              <div class="modal-header clearfix text-left">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14" style="font-size: 40px!important;color: black!important"></i>
                </button>
               
              </div>
              <div class="modal-body">
            <iframe id="contentEmailing"></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
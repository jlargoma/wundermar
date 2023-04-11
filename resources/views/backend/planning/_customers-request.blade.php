<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
  <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
</button>

<div class="col-md-12 not-padding content-last-books">
  <div class="alert alert-info fade in alert-dismissable" style="max-height: 600px; overflow-y: auto;position: relative;">
    <h4 class="text-center">CONSULTAS REALIZADAS POR WEB</h4>
    <div id="customerRequestTable">
      @if(count($items)>0)
      <div class="">
        <button type="button" class="btn filterSite active" data-key="0">Todos</button>
        <button type="button" class="btn filterSite" data-key="1">wundermar</button>
        <button type="button" class="btn filterSite" data-key="2">Rosa D'Oro</button>
        <button type="button" class="btn filterSite" data-key="5">Siloé Plaza</button>
      </div>

      <div class="table-responsive" style="    overflow-y: hidden;">
        <table class="table table-mobile">
          <thead>
            <tr class ="text-center bg-success text-white">
              @if($isMobile)
              <th class="th-bookings static" style="width: 130px; padding: 14px !important;background-color: #10cfbd;">  
                Nombre
              </th>
              <th class="th-bookings first-col" style="padding-left: 130px!important"></th>
              @else
              <th class="th-bookings static" style="background-color: #10cfbd;">  
                Nombre
              </th>
              <th class="th-bookings first-col"></th> 
              @endif
              <th class="th-bookings text-center th-2">Tel.</th>
              <th class="th-bookings text-center th-2">Email</th>
              <th class="th-bookings text-center th-2">Pax</th>
              <th class="th-bookings text-center" style="width:120px !important">IN - OUT </th>
              <th class="th-bookings text-center th-2">Sitio</th>
              <th class="th-bookings text-center th-2">Estado</th>
              <th class="th-bookings text-center th-1">Observ</th>
              <th class="th-bookings text-center th-1">€ Medio</th>
            </tr>
          </thead>
          <tbody id="CR_lstITems">
            <?php foreach ($items as $item): ?>
              <tr data-site='{{$item->site_id}}' id="tr_CRT_{{$item->id}}">
                @if($isMobile)
                <td class ="text-left static" style=" width: 130px;color: black;overflow-x: scroll;padding: 18px 6px !important;height: 5em;">  
                  @else
                <td class ="text-left" style="position: relative; padding: 7px !important;">  
                  @endif
                  <span class="editCustomerRequest cursor" data-id="{{$item->id}}">
                      <?php echo ($item->name && trim($item->name) != '') ? $item->name : '--'; ?>
                  </span>
                </td>
                @if($isMobile)
                <td class="text-center first-col" style="height: 2em;padding: 5px; padding-left: 130px!important">
                  @else
                <td class="text-center">
                  @endif
                </td>
                <td >
                  <a href="tel:<?php echo $item->phone ?>">{{$item->phone}}</a>
                </td>
                <td >{{$item->email}}</td>
                <td >{{$item->pax}}</td>
                <td data-order="{{$item->start}}"  class="nowrap">
                  <b>{{dateMin($item->start)}}</b>
                  <span>-</span>
                  <b>{{dateMin($item->finish)}}</b>
                </td>
                <td class ="text-center">
                  {{show_isset($aSites,$item->site_id)}}
                </td>
                <td class="text-center">
                  @if($item->user_id)
                    {{show_isset($aUsers,$item->user_id)}}
                  @else
                  <span class="text-danger">Sin Contestar</span>
                  @endif
                </td>
                <td class="text-center" >
                  @if(trim($item->comment) != '')
                  <i class="fa fa-commenting  seeContentPop" data-id="cr_comm{{$item->id}}" data-content="{{$item->comment}}" style="color: #000;" aria-hidden="true" >
                  </i>
                  @endif
                </td>
                <td class="text-center">
                  <?php 
                  $mediaPvp = $item->getMediaPrice();
                  if(isset($urls[$item->site_id])): ?>
                  <a class="btn" href="{{$urls[$item->site_id]}}" target="_black">{{moneda($mediaPvp)}}</a>
                  <?php else: 
                    echo moneda($mediaPvp);
                  endif ?>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>

      <div id="conteiner_msg_lst">
        <div class="box-msg-lst">
          <div id="box_msg_lst"></div>
          <button type="button" class="btn btn-default" id="box_msg_close">Cerrar</button>
        </div>
      </div>
      @else
      <p class="alert alert-warning">
        No existen registros.
      </p>
      @endif
    </div>
    <div id="customerRequestEdit" class="row" style="display: none;">
      <div class="col-md-6 col-sm-12">
        <div><b>Nombre: </b><span id="CRE_name"></span></div>
        <div><b>Email: </b><span id="CRE_email"></span></div>
        <div><b>Pax: </b><span id="CRE_pax"></span></div>
        <div><b>Teléfono: </b><span id="CRE_phone"></span></div>
        <div><b>In-Out: </b><span id="CRE_date"></span></div>
        <div><b>Edificio: </b><span id="CRE_site"></span></div>
        <div><b>Estado: </b><span id="CRE_status"></span> <span id="CRE_booking"></span></div>
        <div><b>Precio Medio: </b><span id="CRE_price"></span></div>
      </div>
      <div class="col-md-6 col-sm-12">
        <div class="form-group">
          <label>Usuario</label>
          <select id="CRE_user" class="form-control">
            @foreach($aUsers as $uid => $name)
            <option value="{{$uid}}">{{$name}}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Comentario</label>
          <textarea class="form-control" id="CRE_comment" rows="5"></textarea>
        </div>
        <div class="form-check" style="border: 1px solid #000;padding: 5px;margin: 8px 0px;">
          <input type="checkbox" class="form-check-input" id="CRE_send_mail" >
          <label class="form-check-label" for="CRE_send_mail">Enviar comentario al mail del cliente</label>
        </div>
      </div>
      <button class="btn btn-success" id="convertCustomerRequest" type="button">Reservar</button>
      <button class="btn" id="hideCustomerRequest" type="button"><i class="fa fa-eye-slash" ></i></button>
      <button class="btn" id="cancelCustomerRequest" type="button">Volver</button>
      <button class="btn btn-primary pull-right" id="saveCustomerRequest" type="button">Guardar</button>
      
      <div class="table-responsive">
        <h2 style="width: 100%;text-align: center;background-color: #000;color: #efefef;margin: 1em 0 0 0;">Histótico Emails con el cliente</h2>
        <table class="table">
          <thead>
            <tr>
              <th  class="text-center">Usuario</th>
              <th  class="text-center">Fecha</th>
              <th  class="text-center">Desde</th>
              <th  class="text-center">Mail</th>
            </tr>
          </thead>
          <tbody id="tableSentMails">
            
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div> 


<style>
  i.fa.fa-commenting.seeComment {
    position: relative;
}
.seeComment_body {
    position: absolute;
    width: 23em;
    background-color: black;
    left: -12em;
    color: #FFF;
    padding: 7px;
    z-index: 9;
    display: none;
    white-space: nowrap;
}
  i.fa.fa-commenting.seeComment:hover .seeComment_body{
    display: block;
  }
  .table tbody#tableSentMails td {
    text-align: center;
    padding: 5px 12px !important;
}
</style>
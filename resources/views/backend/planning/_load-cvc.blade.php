<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
  <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
</button>

<div class="col-md-12 not-padding content-last-books">
  <div class="alert alert-info fade in alert-dismissable" style="max-height: 600px; overflow-y: auto;position: relative;">
    <h4 class="text-center">RESERVAS SIN VISA</h4>
    <div id="customerRequestTable">
      @if($bookLst && count($bookLst)>0)
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
              <th class="th-bookings text-center th-2">Estado</th>
              <th class="th-bookings text-center" style="width:120px !important">IN - OUT </th>
              <th class="th-bookings text-center th-2">Comentario</th>
              <th class="th-bookings text-center th-2">VISA</th>
              <th class="th-bookings text-center th-1"></th>
            </tr>
          </thead>
          <tbody id="CR_lstITems">
            <?php foreach ($bookLst as $item): 
              ?>
              <tr id="tr_CRT_{{$item->id}}">
                @if($isMobile)
                <td class ="text-left static" style=" width: 130px;color: black;overflow-x: scroll;padding: 18px 6px !important;height: 5em;">  
                  @else
                <td class ="text-left" style="position: relative; padding: 7px !important;">  
                  @endif
                  <a href="/admin/reservas/update/{{$item->id}}">
                      <?php echo ($item->customer->name && trim($item->customer->name) != '') ? $item->customer->name : '--'; ?>
                  </a>
                </td>
                @if($isMobile)
                <td class="text-center first-col" style="height: 2em;padding: 5px; padding-left: 130px!important">
                  @else
                <td class="text-center">
                  @endif
                </td>
                <td >
                  <a href="tel:<?php echo $item->customer->phone ?>">{{$item->customer->phone}}</a>
                </td>
                <td >{{$item->customer->email}}</td>
                <td >{{$item->pax}}</td>
                <td >{{$item->getStatus($item->type_book) }}</td>
                <td data-order="{{$item->start}}"  class="nowrap">
                  <b>{{dateMin($item->start)}}</b>
                  <span>-</span>
                  <b>{{dateMin($item->finish)}}</b>
                </td>
                <td class="text-center" >
                  @if(trim($item->book_comments) != '')
                  <i class="far fa-comment-dots  seeContentPop" data-id="cr_comm{{$item->id}}" data-content="{{$item->book_comments}}" style="color: #000;" aria-hidden="true" >
                  </i>
                  @endif
                </td>
                <td class="text-center">
                  <textarea type="text" class="form-control cc_upd"  id="visa_{{$item->id}}" data-book_id="{{$item->id}}" data-customer_id="{{$item->customer_id}}">{!!show_isset($aVisasData,$item->id)!!}</textarea>
                </td>
                <td class="text-center">
                  @if ($item->agency == 1)
                  <a href="https://admin.booking.com/hotel/" target="_black"><img src="/pages/booking.png" alt="alt" width="40px"/></a>
                  @endif
                
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
  </div>
</div> 


<style>
  i.fa.fa-commenting.seeComment {
    position: relative;
  }
  .seeComment_body {
    position: fixed;
    background-color: black;
    color: #FFF;
    padding: 7px;
    z-index: 9;
    display: none;
    text-align: left;
  }
  i.fa.fa-commenting.seeComment:hover .seeComment_body{
    display: block;
  }
  .table tbody#tableSentMails td {
    text-align: center;
    padding: 5px 12px !important;
  }
  div#customerRequestEdit {
    background-color: white;
    padding: 15px;
    margin-bottom: 2em;
  }
</style>
<script>
$(document).ready(function () {
  $('body').on('change','.cc_upd',function(event) {
    var that = $(this);
    var id = that.data('book_id');
    var idCustomer = that.data('customer_id');
    var cc_cvc = $('#cc_cvc'+id).val();
    var cc_number = $('#cc_number'+id).val();
    $('#loadigPage').show('slow');
    $.post('/admin/reservas/upd-visa', { _token: "{{ csrf_token() }}",id:id,idCustomer:idCustomer,cc_cvc:cc_cvc,cc_number:cc_number }, function(data) {
          if (data.status == 'success') {
            window.show_notif('',data.status,data.response);
//            that.closest('tr').remove();
          } else {
            window.show_notif('Error:',data.status,data.response);
          }
          $('#loadigPage').hide('slow');
      });
    });
});   
</script>
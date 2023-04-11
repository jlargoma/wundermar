@if($type != 'eliminadas')
<div class="box-btn-sites">
  <div class="btn-sites">
    <?php $s_active='active'; ?>
    @foreach($bSite as $k=>$v)
    <button class="btn select_site {{$s_active}} {{$type}}" data-k="{{$k}}">
      {{$v[0]}} 
      @if(isset($v[1]) && $v[1]>0)<span>{{$v[1]}}</span>@endif
    </button>
    <?php $s_active=''; ?>
    @endforeach
  </div>
</div>
<input type="hidden" value="" id="cal_site_id">
@endif
<div class="planning_tables">
<?php 
  $columnDefs = null;
  $orden = null;
  switch ($type):
    case 'pendientes':
    case 'reservadas':
    case 'overbooking':
    case 'cancel-xml':
    case 'blocks':
      ?> @include('backend.planning.listados._pendientes', ['books' => $books ])<?php
      if ($uRole != "agente"){
        $columnDefs = '0,1,2,3,4,10,11';
      }
      $orden = '  "order": [[ 0, "desc" ]],';
      break;
    case 'especiales':
      ?> @include('backend.planning.listados._especiales', ['books' => $books ])<?php
      $columnDefs = '0,1,2,3,6,7,8,9';
      break;
    case 'confirmadas':
    case 'blocked-ical':
      ?> @include('backend.planning.listados._pagadas', ['books' => $books ])<?php
      if ( $uRole != "agente"){
        $columnDefs = '0,1,2,3,4,9,10';
      }
      break;
    case 'checkin':
    case 'ff_pdtes':
      ?> @include('backend.planning.listados._checkin', ['books' => $books ])<?php
      if  ($uRole != "agente" && $uRole != "limpieza"){
        $columnDefs = '0,1,2,3,4,5,8,9,10';
      }
      $orden = '  "order": [[ 6, "asc" ]],';
      break;
    case 'checkout':
      ?> @include('backend.planning.listados._checkout', ['books' => $books ])<?php
      if ($uRole != "limpieza"){
        $columnDefs = '0,1,2,5,6';
      }
      $orden = '  "order": [[ 3, "asc" ]],';
      break;
    case 'eliminadas':
      ?> @include('backend.planning.listados._eliminadas', ['books' => $books ])<?php
      break;
  endswitch;
?>
      </div>
      
<script type="text/javascript">

  $(document).ready(function() {
    @if($columnDefs)
      var dataTable = $('.table-data').DataTable({
          "paging":   false,
          "columnDefs": [
            {"targets": [{{$columnDefs}}], "orderable": false }
          ],
          @if($orden) {!!$orden!!}@endif
          paging:  true,
          pageLength: 30,
          pagingType: "full_numbers",
          @if($isMobile)
            scrollX: true,
            scrollY: false,
            scrollCollapse: true,
            fixedColumns:   {
              leftColumns: 1
            },
          @endif

        });

      
    @endif





    $('.getImagesCustomer').click(function(event) {
      var idRoom = $(this).attr('data-id');
      var idCustomer = $(this).attr('data-idCustomer');
      $.get('/admin/rooms/api/getImagesRoom/'+idRoom+'/'+idCustomer, function(data) {
        $('#modalRoomImages .modal-content').empty().append(data);
      });
    });
    /*Cambiamos las reservas*/
    $('.status, .room').change(function(event) {
        var id = $(this).attr('data-id');
        var clase = $(this).attr('class');

        if (clase == 'status form-control minimal') {
            var status = $(this).val();
            var room = "";

        }else if(clase == 'room form-control minimal'){
            var room = $(this).val();
            var status = "";
        }
        if (status == 5) {

            $('.modal-content.contestado').empty().load('/admin/reservas/ansbyemail/'+id);
            $('#btnContestado').trigger('click');      

        }else{

          $.get('/admin/reservas/changeBook/'+id, {status:status,room: room}, function(data) {
              window.show_notif(data.title,data.status,data.response);
              var type = $('.table-data').attr('data-type');
              var year = $('#fecha').val();
              $.get('/admin/reservas/api/getTableData', { type: type, year: year }, function(data) {
                  $('.content-tables').empty().append(data);
              });
              $('.content-calendar').empty().append('<div class="col-xs-12 text-center sending" style="padding: 120px 15px;"><i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br><h2 class="text-center">CARGANDO CALENDARIO</h2></div>');
              $('.content-calendar').empty().load('/getCalendarMobile');
           }); 
        }
    });

	

	/* Comentarios flotantes*/
	$('.icons-comment').hover(function() {
		var content = $(this).attr('data-class-content');
		$('.'+content).show();
	}, function() {
		var content = $(this).attr('data-class-content');
		$('.'+content).hide();
	});

	

	$('.customer-phone').change(function(event) {
		var idCustomer = $(this).attr('data-id');
		var phone = $(this).val();
		$.get('/admin/customer/change/phone/'+idCustomer+'/'+phone, function(data) {
                  window.show_notif(data.title,data.status,data.response); 
                  /* recargamos la actual tabla*/
                  var type = $('.table-data').attr('data-type');
                  var year = $('#fecha').val();
                  $.get('/admin/reservas/api/getTableData', { type: type, year: year }, function(data) {
                      $('.content-tables').empty().append(data);
                  });
		});
	});

    $('.restoreBook').click(function(event) {
    	var id = $(this).attr('data-id');
    	$.get('/admin/reservas/restore/'+id, function(data) {
            window.show_notif(data.title,data.status,data.response); 
            /*recargamos la actual tabla*/
            var type = $('.table-data').attr('data-type');
            var year = $('#fecha').val();
            $.get('/admin/reservas/api/getTableData', { type: type, year: year }, function(data) {
                $('.content-tables').empty().append(data);
            });

            /* recargamos el calendario*/
            $('.content-calendar').empty().append('<div class="col-xs-12 text-center sending" style="padding: 120px 15px;"><i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br><h2 class="text-center">CARGANDO CALENDARIO</h2></div>');
            $('.content-calendar').empty().load('/getCalendarMobile');
        });
    });

 $('.select_site').on('click',function(event) {
    var siteID = $(this).data('k');
    $('.select_site').removeClass('active');
    $(this).addClass('active');
    $('#cal_site_id').val(siteID);
    if (siteID>0){
      var classFilter = 'site'+siteID;
      $('.contentCalendar tbody tr').each(function(v,i){
        if($(this).hasClass(classFilter)){
          $(this).show();
        } else {
          $(this).hide();
        }
      });
      console.log(classFilter);
      dataTable.column(0).search(classFilter).draw();
    } else {
      $('.contentCalendar tbody tr').show();
      dataTable.column(0).search('').draw();
    }
            
 });
        
      });
</script>
<style>
  .btn-sites .btn{
    color: #FFF;
    background-color: {{$bg_color}};
    position: relative;
    padding-right: 3em;
    z-index: 99;
  }
  .btn-sites .btn.active{
    color: {{$bg_color}};
    background-color: #FFF !important;
    border-color:{{$bg_color}};
  }

  td.btn-xs-table .btn,
  td.btn-xs-table div {
    height: 2em;
    padding: 4px !important;
    margin: 0;
}
  select.schedule {
      max-width: 35px;
  }
@media only screen and (max-width: 425px){
  .DTFC_LeftBodyLiner,
  .DTFC_LeftBodyWrapper,
  .DTFC_LeftHeadWrapper{
    max-width: 150px !important;
  }

}
</style>
<?php 
	use \App\Classes\Mobile;
	$mobile = new Mobile();
?>
@extends('layouts.admin-master')

@section('title') Administrador de reservas @endsection

@section('externalScripts') 
<script src="{{ asset('/vendors/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
  .name-back{
    background-color: rgba(72,176,247,0.5)!important;
  }
  .name-back input{
    background-color: transparent;
    color: black;
    font-weight: 800;
  }
  .ocupation-back{
    background-color: rgba(72,176,247,0.5)!important;
  }
  .ocupation-back input{
    background-color: transparent;
    color: black;
    font-weight: 800;
  }
</style>
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10 table-responsive">
  <div class="row">
    <div class="col-md-12 text-center">
      <h2>LISTADO DE <span class="font-w800">APARTAMENTOS</span></h2>
    </div>
    <div class="col-md-12 text-center">
      @if($errors->any())
      <p class="alert alert-danger">{{$errors->first()}}</p>
      @endif
      @if (\Session::has('success'))
      <p class="alert alert-success">{!! \Session::get('success') !!}</p>
      @endif
    </div>
    <div class="col-md-2 col-xs-6 push-20">
      <input type="text" id="searchRoomByName" class="form-control" placeholder="Buscar..." />
    </div>
    <div class="col-md-2 col-xs-6 push-20">
      <select class="form-control minimal" id="channel_group" placeholder="ZODOMUS Apto">
        <option value=""> - OTA Apto -</option>
        <?php foreach ($otaAptos as $id=>$data): ?>                                   
          <option value="{{$id}}" <?php echo ($id == $channel_group) ? "selected" : "" ?>>
            {{$data}}
          </option>
        <?php endforeach ?>
      </select>
    </div>
    <div class="col-md-2 col-xs-6 push-20">
      <select class="form-control minimal" id="channel_site" placeholder="Sitio">
        <option value=""> - Sitio -</option>
        <?php foreach ($sites as $data): ?>                                   
          <option value="{{$data->id}}" <?php echo ($data->id == $site) ? "selected" : "" ?>>
            {{$data->name}}
          </option>
        <?php endforeach ?>
      </select>
    </div>
    <div class="col-md-1 col-xs-4 push-20">
      <button class="btn btn-success btn-cons" type="button" data-toggle="modal" data-target="#modalNewSize">
        <i class="fa fa-plus-square" aria-hidden="true"></i> <span class="bold">Crear tamaño</span>
      </button>
    </div>
    <div class="col-md-1 col-xs-4 push-20">
      <button class="btn btn-success btn-cons" type="button" data-toggle="modal" data-target="#modalNewTypeApto">
        <i class="fa fa-plus-square" aria-hidden="true"></i> <span class="bold">Tipo Apto.</span>
      </button>
    </div>
    <div class="col-md-1 col-xs-4 push-20">
      <button class="btn btn-success btn-cons" type="button" data-toggle="modal" data-target="#modalNewApto">
        <i class="fa fa-plus-square" aria-hidden="true"></i> <span class="bold">Nuevo Apto.</span>
      </button>
    </div>
    <div class="col-md-1 col-xs-4 push-20">
      <button class="btn btn-success btnRoomsTypes" type="button" data-toggle="modal" data-target="#modalRoomTypes">
        <span class="bold">Widget de Habitaciones</span>
      </button>
    </div>

    <div class="clearfix"></div>
    <div class="row">
      <div class="col-md-6 col-xs-12 content-table-rooms" style="max-height:960px; overflow-y: auto;">
        @include('backend.rooms._tableRooms', ['rooms' => $rooms, 'roomsdesc' => $roomsdesc])
      </div>
      <div class="col-md-6 col-xs-12 push-20">
        <div class="row contentUpdateForm" style="border: 2px dashed black;">
          <div class="col-xs-12 push-20" >
            <h2 class="text-center">
              HAZ CLIC PARA CARGAR LOS DATOS DE UN APTO.
            </h2>
          </div>
        </div>
    </div>
  </div>
</div>
</div>


<div class="modal fade slide-up in" id="modalFiles" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xs">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close fs-14" style="font-size: 40px!important;color: black!important"></i>
            </button>
            <h2 class="text-center">
              Subida de archivos
            </h2>
          </div>

          <div class="container-xs-height full-height">
            <div class="row-xs-height">
              <div class="modal-body col-xs-height col-middle text-center   ">
                <div class="upload-body">
                </div>
              </div>
            </div>
          </div>

        </div>


      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

  <div class="modal fade slide-up in" id="modalHeaders" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xs">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close fs-14" style="font-size: 40px!important;color: black!important"></i>
            </button>
            <h2 class="text-center">
              Subida de Imágenes de cabeceras
            </h2>
          </div>
          <div class="container-xs-height full-height">
            <div class="row-xs-height">
              <div class="modal-body col-xs-height col-middle text-center   ">
                <div class="upload-body">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

  
<div class="modal fade slide-up in" id="modalEmailing" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content emailing"></div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<div class="modal fade slide-up in" id="modalRoomTypes" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="min-width:68%;"> 
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close fs-14" style="font-size: 40px!important;color: black!important"></i>
            </button>
            <h2 class="text-center"> Widget Habitaciones</h2>
          </div>
          <div class="container-xs-height full-height">
            <div class="row-xs-height">
              <div class="modal-body">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade slide-up in" id="modalNewSize" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100;">
          <i class="fa fa-close fs-20" ></i>
        </button>
        <div class="panel-body">
          <div class="panel panel-default" style="margin-top: 15px;">
            <div class="panel-heading">
              <div class="panel-title col-md-12">Tamaño de  Apartamento
              </div>
            </div>
            <div class="panel-body">
              <div class="col-md-6">
                <form role="form"  action="{{ url('/admin/apartamentos/create-size') }}" method="post">
                  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                  <div class="input-group transparent">
                    <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                    </span>
                    <input type="text" class="form-control" name="name" placeholder="nombre" required="" aria-required="true" aria-invalid="false">
                  </div>
                  <br>
                  <div class="input-group">
                    <button class="btn btn-complete" type="submit">Guardar</button>
                  </div>
                </form>
              </div>
              <div class="col-md-6">
                <?php foreach ($sizes as $size): ?>
  <?php echo $size->name ?><br>
<?php endforeach ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade slide-up in" id="modalNewTypeApto" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100;">
          <i class="fa fa-close fs-20" ></i>
        </button>
        <div class="panel-body">
          <div class="panel panel-default" style="margin-top: 15px;">
            <div class="panel-heading">
              <div class="panel-title col-md-12">Tipo de  Apartamento
              </div>
            </div>
            <div class="panel-body">
              <div class="col-md-6">
                <form role="form"  action="{{ url('/admin/apartamentos/create-type') }}" method="post">
                  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                  <div class="input-group transparent">
                    <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                    </span>
                    <input type="text" class="form-control" name="name" placeholder="nombre" required="" aria-required="true" aria-invalid="false">
                  </div>
                  <br>
                  <div class="input-group">
                    <button class="btn btn-complete" type="submit">Guardar</button>
                  </div>
                </form>
              </div>
              <div class="col-md-6">
                <?php foreach ($types as $type): ?>
  <?php echo $type->name ?><br>
<?php endforeach ?>
              </div>
            </div>
          </div>
        </div> 
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

@include('backend.rooms.blocks.modal_add_room')
  
  <div class="modal fade slide-up in" id="modalTexts" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xs">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close fs-14" style="font-size: 40px!important;color: black!important"></i>
            </button>
            <h2 class="text-center">Descripción Apto</h2>
          </div>
          <div class="container-xs-height full-height" id="error_apto">
            <p class="alert alert-danger"></p>
          </div>
          <div class="container-xs-height full-height" id="content_apto">
            <h3 id="name_apto" class="text-center"></h3>
              <form enctype="multipart/form-data" action="{{ url('/admin/aptos/edit-room-descript') }}" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="room" id="room"  value="">
                <div class="form-group col-md-12">
                  <label for="Nombre">Descripción</label>
                  <textarea class="" name="apto_descript" id="apto_descript" rows="10" cols="80"></textarea>
                </div>
                <div class="form-group col-md-12">
                  <input type="submit" value="Enviar" class="btn btn-primary" />
                </div>
              </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade slide-up in" id="modalPercentApto" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">						
      <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100;">
          <i class="fa fa-close fs-20" ></i>
        </button>
        <div class="percent-body">

        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!--<form role="form">
  <div class="form-group form-group-default required" >
    <label class="highlight">Message</label>
    <input type="text" hidden="" class="form-control notification-message" placeholder="Type your message here" value="This notification looks so perfect!" required>
  </div>
  <button class="btn btn-success show-notification " id="boton">Show</button>
</form>-->


@endsection

@section('scripts')

<script src="/assets/js/notifications.js" type="text/javascript"></script>

<script type="text/javascript">

$(document).ready(function () {
  $('body').on('click','.btn-emiling',function (event) {
    var id = $(this).attr('data-id');
    $('.modal-content.emailing').empty().load('/admin/apartamentos/email/' + id);
  });
  $('body').on('change','.percentage',function (event) {

			
			var id = $(this).attr('data-id');
			var tipo = $(this).attr('name');
			var percent = $(this).val();

    var id = $(this).attr('data-id');
    var tipo = $(this).attr('name');
    var percent = $(this).val();

    $.get('/admin/apartamentos/update-Percent', {id: id, tipo: tipo, percent: percent}, function (data) {
      $('.notification-message').val(data);
      $("#boton").click();
      setTimeout(function () {
        $('.alert-info .close').trigger('click');
      }, 1500);

    });

  });

<?php if (!$mobile->isMobile()): ?>
      $('body').on('click','.aptos',function (event) {
      var id = $(this).attr('data-id');

      $.get('/admin/rooms/getUpdateForm', {id: id}, function (data) {
        $('.contentUpdateForm').empty().append(data)
      });
    });
<?php else: ?>
    $('body').on('click','.aptos',function (event) {
      var id = $(this).attr('data-id');

      $.get('/admin/rooms/getUpdateForm', {id: id}, function (data) {
        $('.contentUpdateForm').empty().append(data);
        $('html,body').animate({
          scrollTop: $(".contentUpdateForm").offset().top},
                'slow');
      });
    });
<?php endif ?>
  
  $('#searchRoomByName').keyup(function (event) {
    var searchString = $(this).val();
    $('#channel_site').val('');
    $('#channel_group').val('');
    $.get('/admin/rooms/search/searchByName', {searchString: searchString}, function (data) {

      $('.content-table-rooms').empty().append(data);

    });
  });
    
   $('#channel_group').on('change',function (event) {
    var channel_group = $(this).val();
    $('#channel_site').val('');
    $('#searchRoomByName').val('');
    $.get('/admin/rooms/search/searchByName', {channel_group: channel_group}, function (data) {

      $('.content-table-rooms').empty().append(data);

    });
  });
   $('#channel_site').on('change',function (event) {
    var channel_site = $(this).val();
    $('#channel_group').val('');
    $('#searchRoomByName').val('');
    $.get('/admin/rooms/search/searchByName', {channel_site: channel_site}, function (data) {

      $('.content-table-rooms').empty().append(data);

    });
  });
  
  CKEDITOR.replace('apto_descript',
          {
            toolbar:
                    [
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
                '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
            '/',
            { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
            { name: 'colors', items : [ 'TextColor','BGColor' ] },
            { name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
                    ]
          });
       
  $('body').on('click','.editAptoText',function (event) {
    var id = $(this).data('id');
    $('#error_apto').hide();
    $('#content_apto').hide();
    $.get('/admin/aptos/edit-room-descript/' + id, function (data) {
      if ( data.result == 'ok'){
        $('#content_apto').show();
        $('#room').val(id);
        $('#name_apto').text(data.name);
        CKEDITOR.instances.apto_descript.setData(data.text, function () {
          this.checkDirty();
        });
      } else {
        $('#error_apto').show();
        $('#error_apto').find('p').text(data.msg);
//        window.show_notif('Error','error',data.msg);
      }
    });
  });

  $('body').on('click','.btnRoomsTypes',function (event) {
    $('#modalRoomTypes .modal-body').empty().load('/admin/rooms/rooms-type');
  });

  $('#modalRoomTypes').on('change','.editable',function (event) {
    var data = {  
      id: $(this).data('id'),
      type: $(this).data('type'),
      val: $(this).val(),
      _token: "{{csrf_token()}}"
    };
    $.post('/admin/rooms/rooms-type', data, function(resp) {
      if (resp == 'OK') {
        window.show_notif('Registro modificado','success','');
      } else {
        window.show_notif(resp,'danger','');
      }
    });
  });
});
</script>
@endsection

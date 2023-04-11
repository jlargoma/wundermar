<div class="col-md-12 text-center">
  <div class="row">
    <ul id="sortable">
      @if($photos)
      @foreach ($photos as $photo)
      <li class="photo_sortable" id="{{$photo->id}}">
        <img src="{{ $photo->file_rute }}/thumbnails/{{ $photo->file_name }}" alt="{{$roomName}}">
        <button class="btn btn-danger btn_remove" type="button" data-toggle="tooltip" data-id="{{$photo->id}}"  title="" data-apto="{{$roomName}}" data-gal="{{$key_gal}}" data-original-title="Eliminar Reserva" onclick="return confirm('¿Quieres Eliminar la reserva?');">
          <i class="fa fa-trash-o"></i>
        </button>
        <i class="fas fa-check btn_main <?php if ($photo->main) echo 'active'; ?>" data-id="{{$photo->id}}"></i>
      </li>
      @endforeach
      @endif
    </ul>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" action="{{ url('admin/apartamentos/uploadFile') }}" method="POST" class="form-photo">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      @if($key_gal)
      <input type="hidden" name="key_gal" id="key_gal"  value="<?php echo $key_gal; ?>">
      @else
      <input type="hidden" name="room" id="room"  value="<?php echo $roomName; ?>">
      @endif
      <input name="uploadedfile[]" type="file" multiple class="custom-file-input" />
      <p class="text-danger">Recomendado 1024*680 px</p>
      <input type="submit" value="Subir archivo" class="btn btn-primary" />
    </form>
  </div>
</div>

<script type="text/javascript">

  $(function () {


    function showFloatMsg(type, text) {
      $('#bottom_msg').addClass(type);
      $('#bottom_msg_text').text(text);
      $('#bottom_msg').show('slow', function () {
        setTimeout(function () {
          hideFloatMsg(type)
        }, 3000);
      });
    }
    function hideFloatMsg(type) {
      $('#bottom_msg').hide('slow', function () {
        $('#bottom_msg').removeClass(type);
        $('#bottom_msg_text').text('');
      });
    }

    $('.photo_sortable').on('click', '.btn_remove', function () {

      var id = $(this).data('id');
      var apto = $(this).data('apto');
      var item = $(this).closest('.photo_sortable');
      var data = {
        'id': id,
        'apto': apto,
        '_token': "{{csrf_token()}}",
      }

      $.ajax({
        url: '/admin/apartamentos/deletePhoto',
        type: 'POST',
        data: data,
        success: function (response) {
          if (response.status === 'ok') {
            item.remove();
            showFloatMsg('success', 'Registro Eliminado.');
          } else {
            showFloatMsg('error', response.msg);
          }
        },
        error: function (response) {
          showFloatMsg('error', 'No se ha podido obtener los detalles de la consulta.');
        }
      });

    });

    $('.photo_sortable').on('click', '.btn_main', function () {
      if ($(this).hasClass('active')) {
        return;
      }
      var that = $(this);
      var data = {
        'id': that.data('id'),
        '_token': "{{csrf_token()}}",
      }

      $.ajax({
        url: '/admin/apartamentos/photo_main',
        type: 'POST',
        data: data,
        success: function (response) {
          if (response.status === 'ok') {
            $('.btn_main.active').removeClass('active');
            that.addClass('active');
            showFloatMsg('success', 'Registro Actualizado.');
          } else {
            showFloatMsg('error', response.msg);
          }
        },
        error: function (response) {
          showFloatMsg('error', 'No se ha podido obtener los detalles de la consulta.');
        }
      });
    });


    function savePhotoOrden() {
      var idsInOrder = $("#sortable").sortable("toArray");
      //-----------------^^^^
      var data = {
        'id': "{{$roomName}}",
        'galley': "{{$key_gal}}",
        'order': idsInOrder.join('-'),
        '_token': "{{csrf_token()}}",
      }

      $.ajax({
        url: '/admin/apartamentos/photo_orden',
        type: 'POST',
        data: data,
        success: function (response) {
          if (response.status === 'ok') {
            showFloatMsg('success', 'Orden Actualizado.');
          } else {
            showFloatMsg('error', response.msg);
          }
        },
        error: function (response) {
          showFloatMsg('error', 'No se ha podido obtener los detalles de la consulta.');
        }
      });
    }

    $("#sortable").sortable({
      placeholder: "ui-photo-highlight",
      update: function ( ) {
        savePhotoOrden();
      }
    });
    $("#sortable").disableSelection();



  });
</script>
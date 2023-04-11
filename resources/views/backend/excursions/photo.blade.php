<h3>{{$obj->title}}</h3>
<div class="col-md-12 text-center">
  <div class="row">
    <ul id="sortable">
      @if($photos)
      @foreach ($photos as $k=>$photo)
      <li class="photo_sortable" id="{{$k}}">
        <img src="{{ $photo->file_rute }}/{{ $photo->file_name }}" alt="{{$obj->title}}">
        <button class="btn btn-danger btn_remove" type="button" data-toggle="tooltip" data-id="{{$k}}"  title="" data-apto="{{$obj->title}}" data-gal="{{$obj->id}}" data-original-title="Eliminar imagen" onclick="return confirm('¿Quieres Eliminar la imagen?');">
          <i class="fa fa-trash-o"></i>
        </button>
      </li>
      @endforeach
      @endif
    </ul>
  </div>
  <div class="row">
    <form enctype="multipart/form-data" action="{{ route('excursions.uploadImages') }}" method="POST" class="form-photo">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="id_obj" id="id_obj"  value="{{$obj->id}}">
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
        'id_obj': "{{$obj->id}}",
        '_token': "{{csrf_token()}}",
      }

      $.ajax({
        url: '{{route("excursions.deleteImage")}}',
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

  function savePhotoOrden() {
      var idsInOrder = $("#sortable").sortable("toArray");
      //-----------------^^^^
      var data = {
        'id_obj': "{{$obj->id}}",
        'order': idsInOrder.join('-'),
        '_token': "{{csrf_token()}}",
      }

      $.ajax({
        url: '{{route("excursions.updOrderImages")}}',
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
<div class="col-md-12 text-center">
  
    <form enctype="multipart/form-data" action="{{ url('admin/apartamentos/upload-img-header') }}" method="POST" class="form-photo">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="id" id="id"  value="<?php echo $id; ?>">
      <input type="hidden" name="type" id="type"  value="<?php echo $type; ?>">
      
      <div class="row">
        <b>Imagen cabecera Escritorio</b>
        @if($photo)
        <img src="{{ $photo->img_desktop }}" alt="{{$roomName}}" width="100%">
        @endif
        <div class="row">
          <div class="col-md-6"><input name="img_desktop" type="file" class="custom-file-input" /></div>
          <div class="col-md-6"><p class="text-danger pt-1">Recomendado: 1024px * 310px</p></div>
        </div>
      </div>
      <div class="row pt-1">
        <b>Imagen cabecera Mobil</b>
        @if($photo)
        <img src="{{ $photo->img_mobile }}" alt="{{$roomName}}" width="100%">
        @endif
        <div class="row">
          <div class="col-md-6"><input name="img_mobile" type="file" class="custom-file-input" /></div>
          <div class="col-md-6"><p class="text-danger  pt-1">Recomendado: 425px * 260x</p></div>
        </div>
      </div>
      <input type="submit" value="Subir archivo" class="btn btn-primary" />
    </form>
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

  
  });
</script>
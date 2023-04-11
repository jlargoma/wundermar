@extends('layouts.popup')

@section('externalScripts')


<?php if (!$mobile): ?> 
  <link href="/assets/plugins/summernote/css/summernote.css" rel="stylesheet" type="text/css" media="screen">
<?php endif; ?>
<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>
<script src="/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="{{ asset('/vendors/ckeditor/ckeditor.js') }}"></script>
<script src="{{ assetV('/js/custom.js') }}"></script>
<style>
  div#contentEmailing {
    overflow: auto !important;
    max-height: 88vh !important;
  }
</style>
@endsection

@section('content')
<div>
  <h2>Mensaje para <span class="semi-bold"><?php echo $book->customer->name ?></span></h2>
  <div class="loading" style="display: none;  position: absolute;top: 0;width: 100%;background-color: rgba(255,255,255,0.6);z-index: 15;min-height: 600px;left: 0;padding: 210px 0;">
    <div class="col-xs-12 text-center sending" style="display: none;">
      <i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br>
      <h2 class="text-center">ENVIANDO</h2>
    </div>

    <div class="col-xs-12 text-center sended" style="display: none;">
      <i class="fa fa-check-circle-o text-black" aria-hidden="true"></i><br>
      <h2 class="text-center">ENVIADO</h2>
    </div>
  </div>
  <form  action="{{ url('/admin/reservas/sendEmail') }}" method="post" id="formSendEmail">
    <div class="summernote-wrapper" style="margin-bottom: 30px;">
      <textarea class="ckeditor" name="textEmail" id="textEmail">{{$mailContent}}</textarea>
    </div>
    <div class="wrapper push-20" style="text-align: center;">
      <button type="submit" class="btn btn-lg btn-success"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> Contestar</button>
      <button type="button" class="btn btn-lg btn-info btnCopyCKEDITOR" data-instance="textEmail"><i class="fa fa-copy" aria-hidden="true"></i> Copiar</button>
    </div>
    <textarea id="copyCKEditorCode" style="height: 1px;width: 1px;border: none;"></textarea>
  </form>
</div>


<script type="text/javascript">

$(window).on('load', function () {
  //  $( '#textEmail' ).ckeditor();
    
//    CKEDITOR.replace('textEmail', {toolbar: 'Basic'});
});


function sending() {
    $('.loading').show();
    $('.loading .sending').show();
}

function sended() {
    $('.loading').hide();
//  $('.loading .sendend').show();
}


$('#formSendEmail').submit(function (event) {
    event.preventDefault();
    var textEmail = CKEDITOR.instances['textEmail'].getData();
    sending();

    var formURL = $(this).attr("action");
    var id = <?php echo $book->id; ?>;

    var type = 1;
    $.post(formURL, {_token: "{{csrf_token()}}", textEmail: textEmail, id: id, type: type}, function (data) {
        if (data == 'OK') {
//      var type = $('.table-data').attr('data-type');
//      var year = $('#fecha').val();
//      $.get('/admin/reservas/api/getTableData', {type: type, year: year}, function (data) {
//        $('.content-tables').empty().append(data);
//      });
            $('body').find('.container').html('<p class="alert alert-success">Correo enviado. <br/>No olvide actualizar la web para ver el cambio en la reserva. (Tecla F5)</p>')
        } else {
            alert('Error al guardar estado: ' + data);
        }

        sended();

    });
});
</script>
@endsection
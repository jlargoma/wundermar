<div class="row loading-emailImages text-center"
     style="position: absolute; top: 50%; left: 50%; z-index: 1010;display: none;">
    <i class="fa fa-spinner fa-5x fa-spin text-black" aria-hidden="true"></i><br>
    <h2 class="text-center text-black">ENVIANDO</h2>
</div>
<div class="row sended-emailImages text-center"
     style="position: absolute; top: 50%; left: 50%; z-index: 1010;display: none;">
    <i class="fa fa-check-circle-o text-black" aria-hidden="true"></i><br>
    <h2 class="text-center text-black">ENVIADO</h2>
</div>
<div class="row content-loading">
    <div class="col-md-3 col-xs-12 pull-right">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="pg-close fs-14" style="color: black!important"></i>
        </button>
    </div>
    <div class="col-xs-12" style="padding: 0">
        <div class="col-md-4 col-xs-12">
            <div class="col-xs-12">
                <h2 class="text-left font-w300" style="margin: 0;">
                    ENVIAR POR <span class="font-w800">EMAIL</span>:
                </h2>

            </div>
            <div class="col-xs-8 col-md-9 push-20">
				<?php if ($book != null): ?>
                    <input type="email" id="shareEmailImages" class="form-control minimal" placeholder="Email..."
                    value="<?php echo $book->customer->email; ?>">
                    <input type="hidden" name="register_data" value="<?php echo $book->id; ?>" id="registerData">
				<?php else: ?>
                    <input type="email" id="shareEmailImages" class="form-control minimal" placeholder="Email...">
                    <input type="hidden" name="register_data" value="0" id="registerData">

                <?php endif;?>

            </div>
            <div class="col-xs-4 col-md-3 push-20">
                <button class="btn btn-primary btn-md" id="sendShareImagesEmail">
                    <i class="fa fa-envelope"></i> Enviar
                </button>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <h2 class="text-center">
                <span class="font-w800"><?php echo $room->nameRoom?></span>
				<?php echo $room->sizeRooms->name ?> // <?php echo ($room->luxury == 1) ? "Lujo" : "Estandar" ?>
                <span class="font-w800">(<?php echo $room->minOcu?>/<?php echo $room->maxOcu?> Pers)</span>
            </h2>
            <p class="text-justify">
				<?php echo $room->description; ?>
            </p>
        </div>
    </div>
    <div class="col-xs-12">
      
      @if($photos)
        @foreach ($photos as $photo)
        <div class="col-md-2 col-xs-12 push-10" style="overflow: hidden;">
          <img src="{{ $photo->file_rute }}/thumbnails/{{ $photo->file_name }}" alt="{{$room->nameRoom}}" style="height: 200px">
        </div>
        @endforeach
      @endif
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function () {
    $('#sendShareImagesEmail').click(function (event) {
      $(".content-loading").css({opacity: 0.5});
      $('.loading-emailImages').show();
      var email = $('#shareEmailImages').val();
      var register = $('#registerData').val();
      var roomId = <?php echo $room->id; ?>

      $.get('/admin/sendImagesRoomEmail', {email: email, roomId: roomId, register: register}, function (data) {
        $('.loading-emailImages').hide();
        $('.sended-emailImages').show();

        $(".content-loading").css({opacity: 1});
        $('#shareEmailImages').val('');
        $('.close').trigger('click');
      });
    });
  });
</script>
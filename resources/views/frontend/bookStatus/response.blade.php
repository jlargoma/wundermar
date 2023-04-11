<?php $mobile = new \App\Classes\Mobile();?>
<div id="loading-book" style="display:none; position: absolute;width: 100%;top: 0;padding-top: 150px;height: 100%;z-index: 99;background-color: rgba(63, 81, 181, 0.5);">
	<div class="col-padding">
		<div class="heading-block center nobottomborder nobottommargin">
			<div class="fbox-icon white">
				<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
			</div>
		</div>
	</div>
</div>
<div class="col-xs-12" id="content-response">
	<div class="row">
		<h2 class="white text-center" style="text-transform: uppercase;">Solicita tu reserva</h2>
	</div>
	<div class="row">
		<div class="col-xs-12 push-10">
			<span class="white push-10 font-s18 font-w300 pull-left">Nombre:</span>
			<span class="font-w800 white center push-10 font-s18 pull-right"><?php echo ucfirst($name) ?></span>
		</div>
		<div class="col-xs-12 push-10">
			<span class="white push-10 font-s18 font-w300 pull-left">Numº Pers:</span>
			<span class="font-w800 white center push-10 font-s18 pull-right">
				<?php echo $pax ?> <?php if ($pax == 1 ): ?>Per<?php else: ?>Pers <?php endif ?>	
			</span>
		</div>

		<div class="col-xs-12 push-10">
			<span class="white push-10 font-s18 font-w300 pull-left">Apartamento:</span>
			<span class="font-w800 white center push-10 font-s18 font-w300 pull-right"><?php echo $apto ?></span>
		</div>
		<div class="col-xs-12 push-10">
			<span class="white push-10 font-s18 font-w300 pull-left">Noches:</span>
			<span class="white center push-10 font-s18 font-w300 pull-right"><span class="font-w800"><?php echo $nigths ?></span> Noches</span>
		</div>
		<div class="col-xs-12 push-10">
			<span class="white push-10 font-s18 font-w300 pull-left">Fechas:</span> 
			<span class="white push-10 font-s18 font-w300 pull-right"><b><?php echo $start->copy()
                                                                                          ->format('d-M') ?> - <?php echo $finish->copy()
                                                                                                                                 ->format('d-M') ?></b></span>
		</div>
		<div class="col-xs-12 push-10">
			<span class="white push-10 font-s18 font-w300 pull-left">Sup. Lujo:<?php if($luxury > 0): ?>(SI)<?php else: ?>(NO)<?php endif; ?></span>
			<span class="white center push-10 font-s18 font-w300 pull-right"><span class="font-w800"><?php echo number_format($luxury, 0, '', '.')?>€</span></span>
		</div>
		
	</div>
	<div class="line" style="margin-bottom: 15px; border-top: 2px solid #FFF;"></div>

	<div class="row push-10">
		<div class="col-xs-12">
			<form method="post" action="{{url('/admin/reservas/create')}}" id="confirm-book">
                          @if(false)
			    <div class="row text-center">
                    <div class="col-xs-12">
                        <div class="col-xs-6 text-left push-10">
                            <input id="price" class="radio-style" name="price-discount" type="radio" value="no" @if(!$setting || empty($setting->value)) checked @endif>
                            <label for="price" class="radio-style-3-label">Precio Apto:</label>
                        </div>
                        <div class="col-xs-6 push-10">
                            <p class="white push-10 font-s16 font-w300 text-right" style="line-height: 1">
                                <span class="font-w800" style="font-size: 20px; @if($setting and !empty($setting->value)) text-decoration:line-through; @endif"><?php echo number_format($total, 0, '', '.') ?>€</span>
                            </p>
                        </div>
                    </div>
                    @if($setting and !empty($setting->value) )
                        <div class="col-xs-12">
                            <div class="col-xs-6 text-left">
                                <input id="price-discount" class="radio-style" name="price-discount" type="radio" value="yes" checked>
                                <label for="price-discount" class="radio-style-3-label"> Descuento en Precio Apto:</label>
                            </div>
                            <div class="col-xs-6">
                                <p class="white push-10 font-s16 font-w300 text-right" style="line-height: 1">
                                    <span class="font-w800" style="font-size: 20px;"><?php echo number_format(($total - $setting->value), 0, '', '.') ?>€</span>
                                </p>
                            </div>
                       </div>
                    @endif
                </div>
                          @else
                          <input id="price" name="price-discount" type="hidden" value="no">
                          <div class="row">
                            <div class="col-xs-12">
                            <div class="col-xs-6 text-left push-10">
                                <label for="price">Precio Apto:</label>
                            </div>
                              
                            <div class="col-xs-6 push-10">
                               <p class="white push-10 font-s16 font-w300 text-right" style="line-height: 1">
                                <span class="font-w800" style="font-size: 20px;"><?php echo number_format($total, 0, '', '.') ?>€</span>
                                </p>
                            </div>
                            </div>
                          </div>
                          @endif
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="newroom" value="<?php echo $room->id; ?>">
				<input type="hidden" name="name" value="<?php echo $name; ?>">
				<input type="hidden" name="email" value="<?php echo $email; ?>">
				<input type="hidden" name="phone" value="<?php echo $phone; ?>">
				<input type="hidden" name="fechas" value="<?php echo $start->copy()
                                                                           ->format('d M, y') ?> - <?php echo $finish->copy()
                                                                                                                     ->format('d M, y') ?>">
				<input type="hidden" name="pax" value="<?php echo $pax; ?>">
				<input type="hidden" name="nigths" value="<?php echo $nigths; ?>">
				<input type="hidden" name="comments" value="<?php echo $comment; ?>">
				<input type="hidden" name="from" value="frontend">
				<input type="hidden" name="parking" value="<?php echo $parking; ?>">
				<input type="hidden" name="agencia" value="0">
				<input type="hidden" name="lujo" value="<?php echo $luxury ?>">
				<input type="hidden" name="dni" value="<?php echo $dni ?>">
				<input type="hidden" name="address" value="<?php echo $address ?>">
				<input type="hidden" name="book_comments" value="">
                <?php if($luxury > 0): ?>
                <input type="hidden" name="type_luxury" value="1">
                <?php else: ?>
                <input type="hidden" name="type_luxury" value="2">
                <?php endif; ?>
                @if($setting and !empty($setting->value))
                    <input type="hidden" name="discount" value="{{$setting->value}}">
                @else
                    <input type="hidden" name="discount" value="0">
                @endif
                <div class="col-xs-12">
                    @if($isFastPayment)
                        <input type="hidden" name="status" value="99">
                        <div class="col-md-6 col-md-offset-3 text-center">
							<button type="submit" class="button button-rounded button-reveal button-large tright center hvr-grow-shadow font-s16 fastPayment submitBookForm" style="letter-spacing: 1px; background-color: #59BA41;">
								<i class="icon-angle-right"></i><span style=" font-size: 16px">RESERVA YA</span>
							</button>
						</div>
                    @else
                        <div class="col-md-6 col-md-offset-3">
                            <button type="submit" class="button button-rounded button-reveal button-large button-blue tright center hvr-grow-shadow font-s16 request submitBookForm" style="letter-spacing: 1px;">
                                <i class="icon-angle-right"></i><span style=" font-size: 16px">SOLICITAR</span>
                            </button>
                        </div>
                    @endif
                    <div class="col-md-6 col-md-offset-3">
                      <button type="button" class="button button-rounded backBooking"  style="letter-spacing: 1px;">
                          Volver
                        </button>
                        </div>
                    
				</div>
				<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
			</form>
		</div>
	</div>
        @if(false)
	<div class="row push-10">
		<div class="col-xs-12 text-left">
		     <span style="font-size:13px; color: #FFFFFF; font-weight: bold;">
                    * Compranos tus fortfaits y paga menos por el apartamento.<br/>
                    * Te los llevamos a casa<br/>
                    * Los fortfaits tiene el mismo precio en toda la estación<br/>
                </span>
        </div>
          @endif
	</div>
</div>
<script type="text/javascript">
	function showLoad() {
      $('#loading-book').show();
    }

    function hideLoad() {
      $('#loading-book').hide();
    }

    $('.fastPayment').click(function () {
      $("#confirm-book input[name='fastPayment']").remove();
      $('#confirm-book').append('<input type="hidden" name="fastPayment" value="1" />');
    });

    $('.request').click(function () {
      $("#confirm-book input[name='fastPayment']").remove();
      $('#confirm-book').append('<input type="hidden" name="fastPayment" value="0" />');
    });

    $('#confirm-book').submit(function (event) {

      event.preventDefault();
      showLoad();

      var _token = $('input[name="_token"]').val();
      var newroom = $('input[name="newroom"]').val();
      var name = $('input[name="name"]').val();
      var email = $('input[name="email"]').val();
      var phone = $('input[name="phone"]').val();
      var fechas = $('input[name="fechas"]').val();
      var pax = $('input[name="pax"]').val();
      var nigths = $('input[name="nigths"]').val();
      var comments = $('input[name="comments"]').val();
      var from = $('input[name="from"]').val();
      var parking = $('input[name="parking"]').val();
      var agencia = $('input[name="agencia"]').val();
      var agency = 0;
      var book_comments = $('input[name="book_comments"]').val();
      var lujo = $('input[name="lujo"]').val();
      var type_luxury = $('input[name="type_luxury"]').val();
      var fast_payment = $('input[name="fastPayment"]').val();
      var discount = $('input[name="discount"]').val();
      var status = $('input[name="status"]').val();
      var priceDiscount = $("input[name='price-discount']:checked").val();
      var url = $(this).attr('action');

      public_key = 'recaptcha';

<?php
if(config('app.env') == 'VIRTUAL'){
?> 

                    $.post(url, {
                      _token: _token,
                      newroom: newroom,
                      name: name,
                      email: email,
                      phone: phone,
                      fechas: fechas,
                      pax: pax,
                      nigths: nigths,
                      comments: comments,
                      from: from,
                      parking: {{ $parking }},
                      agencia: agencia,
                      agency: agency,
                      book_comments: book_comments,
                      Suplujo: lujo,
                      type_luxury: type_luxury,
                      fast_payment: fast_payment,
                      discount: discount,
                      status: status,
                      priceDiscount: priceDiscount
                    }, function (data) {
                        hideLoad();
                        var objAux = $('#content-book-payland');
                        if (objAux.length<=0){
                          objAux = $('#content-book-response');
                        }
                        objAux.empty().append(data).fadeIn('300');
                        <?php if ($mobile->isMobile() || $mobile->isTablet()): ?>
                            $('html, body').animate({
                              /*scrollTop: $("section#content").offset().top*/
                              scrollTop: objAux.offset().top - 30
                            }, 2000);
                        <?php endif; ?>
                    });
   
 <?php  
} else {
  ?>
  grecaptcha.ready(function () {
        grecaptcha.execute(public_key, {action: 'launch_form_submit'})
            .then(function (token) {
              // Verify the token on the server.

              var recaptchaResponse = document.getElementById('recaptchaResponse');
              recaptchaResponse.value = token;

              $.ajax({
                type: "POST",
                url: "/ajax/checkRecaptcha",
                data: {token: token, public_key: public_key},
                dataType: 'json',
                success: function (response) {
//                            price = JSON.stringify(response).replace('.',',');
//                                console.log(response.status);
//                                alert(response.status);
                  if (response.status == 'true') {
                    $.post(url, {
                      _token: _token,
                      newroom: newroom,
                      name: name,
                      email: email,
                      phone: phone,
                      fechas: fechas,
                      pax: pax,
                      nigths: nigths,
                      comments: comments,
                      from: from,
                      parking: {{ $parking }},
                      agencia: agencia,
                      agency: agency,
                      book_comments: book_comments,
                      Suplujo: lujo,
                      type_luxury: type_luxury,
                      fast_payment: fast_payment,
                      discount: discount,
                      status: status,
                      priceDiscount: priceDiscount
                    }, function (data) {
                        hideLoad();
                        var objAux = $('#content-book-payland');
                        if (objAux.length<=0){
                          objAux = $('#content-book-response');
                        }
                        objAux.empty().append(data).fadeIn('300');
                        <?php if ($mobile->isMobile() || $mobile->isTablet()): ?>
                            $('html, body').animate({
                              /*scrollTop: $("section#content").offset().top*/
                              scrollTop: objAux.offset().top - 30
                            }, 2000);
                        <?php endif; ?>
                    });
                  }
                },
                error: function (response) {
                  //                    console.log(response);
                }
              });
            });
      });  
  <?php
}
?>

    });
</script>
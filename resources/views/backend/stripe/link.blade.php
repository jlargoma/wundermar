<div class="row">
  <div class="col-md-12 text-left"> 
    <b>GENERADOR DE LINKS PAYLAND</b>
  </div>
  <div class="col-md-8  col-xs-6">
    <input type="number" class="form-control only-numbers" name="importe_stripe" id="importe_stripe" placeholder="importe..." @if(isset($book)) value="{{ $book->total_price * 0.5 }}" @endif/>
    <?php if (isset($book)): ?>
    <input type="hidden" name="subject_payment" id="subject_payment" value="" />
    <input type="hidden" name="book" id="book" value="{{ $book->id }}" />
    <?php else: ?>
    <input type="hidden" name="book" id="book" value="0" />
    <label style="margin-top: 1em">Asunto del pago:</label>
    <input type="text" class="form-control" name="subject_payment" id="subject_payment" placeholder="Asunto del pago..." value="COBRO ESTANDAR"/>
    <?php endif; ?>
  </div>
  <div class="col-md-4 col-xs-6 push-20">
    <button id="btnGenerate" class="btn btn-success" type="button">Generar</button>
  </div>
</div>
<div class="row content-importe-stripe" id="paymentDataLink">
  
  
  
</div>
<script type="text/javascript">
	$(document).ready(function() {

		$('#btnGenerate').click(function(event) {
                    var importe = $('#importe_stripe').val();
                    var subject = $('#subject_payment').val();
                    var book = $('#book').val();

			if (importe == '') {
				alert('Rellena el importe a generar');
			}else{

				$.get('/admin/links-payland-single',{ importe: importe,subject: subject, book: book }, function(data) {
					$('.content-importe-stripe').empty().append(data);
				});
			}

		});

		$('#paymentDataLink').on("click","#copyLinkStripe", function(){
    var element = 'textPayment';
    window.getSelection().removeAllRanges();
    let range = document.createRange();
    range.selectNode(typeof element === 'string' ? document.getElementById(element) : element);
    window.getSelection().addRange(range);
    document.execCommand('copy');

  });

		
	});
</script>
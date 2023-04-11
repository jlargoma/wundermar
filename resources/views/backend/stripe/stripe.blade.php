<style type="text/css"> 
.StripeElement {
	background-color: white;
	padding: 8px 12px;
	border-radius: 4px;
	border: 1px solid transparent;
	box-shadow: 0 1px 3px 0 #e6ebf1;
	-webkit-transition: box-shadow 150ms ease;
	transition: box-shadow 150ms ease;
}

.StripeElement--focus {
	box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
	border-color: #fa755a;
}

.StripeElement--webkit-autofill {
	background-color: #fefde5 !important;
}
.stripe-price{
	background-color: white!important;
	padding: 8px 12px!important;
	border-radius: 4px!important;
	border: 1px solid transparent!important;
	box-shadow: 0 1px 3px 0 #e6ebf1!important;
	-webkit-transition: box-shadow 150ms ease!important;
	transition: box-shadow 150ms ease!important;
}
</style>
<div class="row alert alert-info fade in alert-dismissable" style="margin-top: 30px; background-color: #daeffd!important;">
	<h3 class="text-center font-w300">
		Cobrar mediante stripe <span class="font-w800">BOOKING.COM</span>
	</h3>
	<?php if (!isset($jsStripe)): ?>
		<script src="//js.stripe.com/v3/"></script>
	<?php endif ?>
	
	<div class="row">
		<form action="{{ url('admin/reservas/stripe/paymentsBooking') }}" method="post" id="payment-form">
			<?php if ($bookTocharge != null): ?>
				<?php 
					if ( count($payments) == 0) {
						$priceToCharge = ($book->total_price * 0.25);

					}elseif(count($payments) == 1){

						$priceToCharge = ($book->total_price * 0.25);

					}elseif(count($payments) > 1){

						$priceToCharge = ($book->total_price * 0.5);
					}

				?>

				<input type="hidden" name="id_book" value="<?php echo $bookTocharge->id; ?>">
				<div class="col-md-6 col-xs-12 text-left push-20">
					<label for="email">Email</label>
					<input type="email" class="form-control stripe-price" name="email" value="<?php echo $book->customer->email ?>" />
				</div>
				<div class="col-md-6 col-xs-12 text-left push-20">
					<label for="importe">Importe a cobrar</label>
					<input type="text" class="form-control only-numbers stripe-price" name="importe" value="<?php echo  $priceToCharge ?>" />
				</div>
				<div class="form-row col-xs-12 push-20">
					<label for="card-element">
						Datos de la tarjeta
					</label>
					<div id="card-element">
						<!-- a Stripe Element will be inserted here. -->
					</div>

					<!-- Used to display form errors -->
					<div id="card-errors" role="alert"></div>
				</div>
				<div class="col-xs-12 text-center">
					<button class="btn btn-primary">Cobrar</button>
				</div>
			<?php else: ?>
				<div class="col-md-6 col-xs-12 text-left push-20">
					<label for="email">Email</label>
					<input type="email" class="form-control stripe-price" name="email" placeholder="example@example.com" />
				</div>
				<div class="col-md-6 col-xs-12 text-left push-20">
					<label for="importe">Importe a cobrar</label>
					<input type="text" class="form-control stripe-price only-numbers" name="importe" placeholder="12345" />
				</div>
				<div class="form-row col-xs-12 push-20">
					<label for="card-element">
						Datos de la tarjeta
					</label>
					<div id="card-element">
						<!-- a Stripe Element will be inserted here. -->
					</div>

					<!-- Used to display form errors -->
					<div id="card-errors" role="alert"></div>
				</div>
				<div class="col-xs-12 text-center">
					<button class="btn btn-primary">Cobrar</button>
				</div>
			<?php endif ?>


		</form>
	</div>

	<script type="text/javascript">

        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
        // Create a Stripe client
        var stripe = Stripe('<?php echo $stripe['publishable_key'] ?>');

        // Create an instance of Elements
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
        	base: {
        		color: '#32325d',
        		lineHeight: '24px',
        		fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        		fontSmoothing: 'antialiased',
        		fontSize: '16px',
        		'::placeholder': {
        			color: '#aab7c4'
        		}
        	},
        	invalid: {
        		color: '#fa755a',
        		iconColor: '#fa755a'
        	}
        };

        // Create an instance of the card Element
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function(event) {
        	var displayError = document.getElementById('card-errors');
        	if (event.error) {
        		displayError.textContent = event.error.message;
        	} else {
        		displayError.textContent = '';
        	}
        });

        // Handle form submission
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
        	event.preventDefault();

        	stripe.createToken(card).then(function(result) {
        		if (result.error) {
              // Inform the user if there was an error
              var errorElement = document.getElementById('card-errors');
              errorElement.textContent = result.error.message;
          } else {
              // Send the token to your server
              stripeTokenHandler(result.token);
          }
      });
        })
    </script>
</div>
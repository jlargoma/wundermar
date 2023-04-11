@extends('layouts.admin-master')

@section('title') Administrador de reservas @endsection

@section('externalScripts') 
<script src="//js.stripe.com/v3/"></script>

@endsection
@section('content')
<div class="row content-fianza">
    <?php if ( count($hasFiance) == 0): ?>
        <div class="col-md-8 col-md-offset-2 alert alert-info fade in alert-dismissable" style="margin-top: 30px; background-color: #10cfbd70!important;">
            <h3 class="text-center font-w300">
                GUARDAR DATOS DEL CLIENTE PARA LA FIANZA
            </h3>
            
            <div class="row">
                <form action="{{ url('admin/reservas/stripe/save/fianza') }}" method="post" id="paymentFianza-form">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    
                    <input type="hidden" name="id_book" value="<?php echo $book->id; ?>">
                    <div class="col-md-6 col-xs-12 text-left push-20">
                        <label for="email">Email</label>
                        <input type="email" class="form-control stripe-price" name="email" value="<?php echo $book->customer->email ?>" />
                    </div>
                    <div class="col-md-6 col-xs-12 text-left push-20">
                        <label for="importe">Importe de la fianza</label>
                        <input type="number" class="form-control stripe-price" name="importe" value="300" />
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
                </form>
            </div>

            <script type="text/javascript">
                function stripeTokenHandler(token) {
                    // Insert the token ID into the form so it gets submitted to the server
                    var form = document.getElementById('paymentFianza-form');
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
                var form = document.getElementById('paymentFianza-form');
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    stripe.createToken(card).then(function(result) {
                        if (result.error) {
                            // Inform the user if there was an error
                            var errorElement = document.getElementById('paymentFianza-formcard-errors');
                            errorElement.textContent = result.error.message;
                        } else {
                            // Send the token to your server
                            stripeTokenHandler(result.token);
                        }
                    });
                })
            </script>
        </div>
    <?php else: ?>
        <div class="col-md-6 col-md-offset-3 alert alert-info fade in alert-dismissable" style="margin-top: 30px; background-color: #10cfbd70!important;">
            <h3 class="text-center font-w300">
                CARGAR LA FIANZA DE <?php echo ($hasFiance->amount/100) ?>
            </h3>
            <div class="row">
                <form action="{{ url('admin/reservas/stripe/pay/fianza') }}" method="post">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="id_fianza" value="<?php echo $hasFiance->id; ?>">
                    <div class="col-xs-12 text-center">
                        <button class="btn btn-primary">COBRAR</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif ?>
</div>
@endsection
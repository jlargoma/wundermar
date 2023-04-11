<?php if (getUsrRole() != "limpieza" && false): ?>
  @include('backend.planning.blocks.payments-update')
<?php endif ?>
        <?php if (getUsrRole() != "limpieza"){
          echo '<div id="payments_block"></div>';
        } ?>
        
        <div class="row">

          <div class="col-xs-12 push-20 ">
            <?php if ($book->type_book == 2): ?>
              <?php if (!$hasFiance): ?>
                  <button class="btn btn-primary btn-lg" type="button" id="fianza"> COBRAR FIANZA</button>
    <?php else: ?>
                  <a class="btn btn-primary btn-lg"
                     href="{{ url('/admin/reservas/fianzas/cobrar/'.$book->id) }}"> RECOGER FIANZA</a>
    <?php endif ?>
  <?php endif ?>
          </div>
  <?php if ($book->type_book == 2): ?>
            <div class="row content-fianza" >
    <?php if ($hasFiance): ?>
                <div class="col-md-6 col-md-offset-3 alert alert-info fade in alert-dismissable" style="margin-top: 30px; background-color: #10cfbd70!important;">
                  <h3 class="text-center font-w300"> CARGAR LA FIANZA DE <span class="font-w800"><?php echo($hasFiance->amount / 100) ?> €</span>
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
  <?php endif; ?>
        </div>
  <?php if (getUsrRole() != "limpieza"): ?>
          <div class="row">
            @include('Paylands.payment', ['routeToRedirect' => route('payland.thanks.payment',
            ['id' => $book->id]), 'id' => $book->id, 'customer' => $book->customer->id, 'percent' => $paymentPercent,'payment_pend'=>$payment_pend])
          </div>
  <?php endif ?>
        <div class="col-xs-12 bg-black push-0">
          <h4 class="text-center white">HISTORICO EMAILS CON EL CLIENTE <span id="loadchatbox">desplegar</span></h4>
        </div>
        <div id="chatbox" class="chat-lst" style="display: none;">
        </div>
  <button class="btn btn-success btn-cons m-b-10" type="button"
          data-toggle="modal" data-target="#modalResponseEmail">Enviar Nueva Respuesta</button>
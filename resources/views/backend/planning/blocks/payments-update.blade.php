<?php if (getUsrRole() != "limpieza"): ?>
  <div class="row">
    <div class="col-xs-12 bg-black push-0">
      <h4 class="text-center white">
        {{ $totalpayment }}€ COBRADO
      </h4>
    </div>
    <div class="col-xs-12 not-padding payment-resume">
      <div class="col-xs-4 not-padding bg-success text-white text-center" style="min-height: 50px">
        <span class="font-s18">Total:</span><br>
        <span class="font-w600 font-s18"><?php echo number_format($book->total_price, 2, ',', '.') ?>
          €</span>
      </div>
      <div class="col-xs-4 not-padding bg-primary text-white text-center" style="min-height: 50px">
        <span class="font-s18">Cobrado:</span><br>
        <span class="font-w600 font-s18"><?php echo number_format($totalpayment, 2, ',', '.') ?>
          €</span>
      </div>
      <div class="col-xs-4 not-padding bg-danger text-white text-center" style="min-height: 50px">
        <span class="font-s18">Pendiente:</span><br>
        <!-- si esta pendiente nada,.si esta de mas +X -->
        <span class="font-w600 font-s18"><?php
          echo ($book->total_price - $totalpayment) >= 0 ? "" : "+";
          echo number_format($totalpayment - $book->total_price, 2, ',', '.')
          ?>
          €</span>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table" style="margin-top: 0;">
        <thead>
          <tr>
            <th class="text-center bg-success text-white">fecha</th>
            <th class="text-center bg-success text-white">importe</th>
            <th class="text-center bg-success text-white">Tipo</th>
            <th class="text-center bg-success text-white">comentario</th>
            <th class="text-center bg-success text-white">Eliminar</th>

          </tr>
        </thead>
        <tbody><?php $total = 0; ?>
          <?php if (count($book->payments) > 0): ?>

            <?php foreach ($book->payments as $payment): ?>
              <tr>
                <td class="text-center p-t-25">
                  <?php
                  echo convertDateToShow_text($payment->datePayment);
                  ?>
                </td>
                <td class="text-center">
                  <input class="editable payment-{{$payment->id}} m-t-5 only-numbers" type="text" name="cost"  data-id="{{$payment->id}}" value="{{$payment->import}}">€
                </td>
                <td class="text-center p-t-25"><?php echo $book->getTypeCobro($payment->type) ?> </td>

                <td class="text-center p-t-25"><?php echo $payment->comment ?></td>
                <td>
                  <a href="{{ url('/admin/reservas/deleteCobro/')}}/<?php echo $payment->id ?>"
                     class="btn btn-tag btn-danger" type="button" data-toggle="tooltip" title=""
                     data-original-title="Eliminar Cobro"
                     onclick="return confirm('¿Quieres Eliminar el obro?');">
                    <i class="fa fa-trash"></i>
                  </a>
                </td>
              </tr>
              <?php $total = $total + $payment->import ?>
            <?php endforeach ?>
          <?php endif; ?>
          <tr>
            <td class="text-center">
              <input type="text" class="form-control fecha-cobro datepicker2 " name="start" data-date-format="dd-mm-yyyy" value="{{date('d-m-Y')}}">
            </td>
            <td class="text-center">
              <input class="importe m-t-5 only-numbers" type="text" name="importe" style="border: 1px solid #dedede;">
            </td>
            <td class="text-center">
              <select class="full-width input-payment type_payment"
                      data-init-plugin="select2" name="type_payment" tabindex="-1" aria-hidden="true">
                <option value="2"><?php echo $book->getTypeCobro(2) ?></option>
                <option value="0"><?php echo $book->getTypeCobro(0) ?></option>
                <option value="4"><?php echo $book->getTypeCobro(4) ?></option>
              </select>
            </td>
            <td class="text-center">
              <input class="comment m-t-5" type="text" name="comment"
                     style="width: 100%;text-align: center;border-style: none">
            </td>
            <td></td>
          </tr>
          <tr>
            <td></td>
            <?php 
            $color = ($payment_pend>0) ? 'color:red;' : ''; 
            $statusPaymentText = 'Al corriente de pago';
            if ($payment_pend>0) $statusPaymentText = 'Pendiente de pago';
            if ($payment_pend<0) $statusPaymentText = 'Sobrante';
              ?>
              <td class="text-center">
                <p style="{{$color}} font-weight: bold;font-size:15px">{{moneda($payment_pend,true,2)}}</p></td>
              <td class="text-left" colspan="2"><p style="color:red;font-weight: bold;font-size:15px">
                  {{$statusPaymentText}}</p></td>
              <td></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-xd-12 mb-1em over-auto">
      <div class="col-xs-8" id="sendNotifPayment">
        <input type="checkbox" class="form-control" id="send_notif" {{$send_notif}} data-id="{{$book->id}}">
        <label class="checkbox">Enviar Recibos</label>
        <input type="text" class="form-control" id="email_notif" value="{{$email_notif}}" data-id="{{$book->id}}">
      </div>
      <div class="col-xs-4">
        <input type="button" name="cobrar" class="btn btn-success  m-t-10 cobrar" value="COBRAR"
           data-id="<?php echo $book->id ?>">
      </div>
    </div>

  </div>
  <?php endif
?>

<script>
  $(document).ready(function () {
    
    
    $('#sendNotifPayment').on("change", "#send_notif,#email_notif", function () {
      var url = "{{route('booking.changeMailNotif')}}";
      var data = {
        send_notif : $('#send_notif').is(':checked'),
        email_notif: $('#email_notif').val(),
        _token: "{{csrf_token()}}",
        booking: $(this).data('id')
        
      }
      $.post(url, data, function (data) {
        window.show_notif(data.title,data.status,data.response);
      });
    });
  
  
    $(".datepicker2").datepicker();

    $(".only-numbers").keydown(function (e) {
      console.log(e.keyCode);
      // Allow: backspace, delete, tab, escape, enter and .
      if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 188,109,189]) !== -1 ||
              // Allow: home, end, left, right, down, up
                      (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return;
      }
      // Ensure that it is a number and stop the keypress
      if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
      }
    });

  });
</script>
<style>
  .input-payment{
    border: none;
    background-color: #eaeaea;
    border-radius: 5px;
    padding: 4px 20px;
    margin-top: 9px;
    margin-left: 22px;
    width: 103px;
  }
  .fecha-cobro{
    border: none;
    text-align: center;
    margin-top: 7px;
    color: #5a5a5a !important;
  }
  .editable,
  .importe{
    border-style: none;
    text-align: center;
  }
</style>
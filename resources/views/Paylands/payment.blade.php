<div class="row payment-box ">
  <div class="col-xs-12">
    <div class="row btn-percents">
      <div class="col-sm-8 col-xs-7">
        <h2 >
          GENERADOR DE LINKS y PAGOS PAYLAND
        </h2>
      </div>
      <div class="col-sm-4 col-xs-5 ">
        <button onclick="load_amount(1)" class="btn btn-success" type="button">100%</button>
        <button onclick="load_amount(0.5)" class="btn btn-success" type="button">50%</button>
      </div>
    </div>
  </div>
  <div class="col-md-8  col-xs-4">
    <input type="hidden" id="payland_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" id="total_price" value="<?php echo $payment_pend; ?>">
    <input type="hidden" id="payland_booking" value="{{ encriptID($customer) }}-{{ encriptID($id) }}">
    <input type="number" class="form-control only-numbers" id="payland_paymentAmount" placeholder="importe..." required @if(isset($book)) value="{{ $payment_pend * $percent }}" @endif>
  </div>
  <div class="col-md-4 col-xs-8 push-20 btn-payment">
    <button onclick="_createPayment('link')" class="btn btn-success" type="button" id="_createPaymentLink">Link</button>
    <button onclick="_createPayment('form')" class="btn btn-success" type="button" id="_createPaymentForm">Pago</button>
    <button class="btn  @if(isset($hasVisa) && $hasVisa) btn-blue @else btn-info @endif" type="button" id="_getPaymentVisa">Visa</button>
  </div>
 <div class="col-md-5 " style="overflow: auto;">
    <div class="hidden">
      {!!$visaHtml!!}
    </div>
      <div class="text-left">
    @if ($uRole != "agente" && $uRole != "limpieza")
    <label>Datos Tarjeta</label>
    <textarea id="creditCardData" class="form-control" rows="5">{{$creditCardData}}</textarea>
    </div>
   <div class="row">
     <div class="col-md-6">
      <div class="btn btn-blue mt-1em" type="button" id="save_creditCardData" data-id="{{$id}}">Guardar</div>
     </div>
     <div class="col-md-6">
<!--      <div class="btn btn-blue mt-1em" type="button" id="seeOrder">
        <i class="fa fa-eye"></i> Ordenes
      </div>-->
     </div>
     <div class="col-md-12 mt-1em" id="seeOrderbox" style="display:none;">
       <input type="text" id="seeOrderID" class="form-control" placeholder="ORDER TOKEN + ENTER">
     </div>
   </div>
    @endif
  </div>
  <div class="col-md-7" id="paymentDataContent"></div>
</div>
<script>
  $('#seeOrder').on('click',function(){
    $('#seeOrderbox').toggle();
  });
  
  $("#seeOrderID").keyup(function(event) {
    if (event.keyCode === 13) {
      $.get('/admin/getOrderID/'+$(this).val(), function(resp){
        $('#seeOrderbox').append(resp);
      });
    }
  });



  
  function _createPayment(type) {
    var url = "{{route('payland.get_payment')}}";
    var amount = $('#payland_paymentAmount').val();
    var booking = $('#payland_booking').val();
    var _token = $('#payland_token').val();
    $.post(url, {
      _token: _token,
      type: type,
      booking: booking,
      amount: amount
    }, function (data) {
      $('#paymentDataContent').empty().append(data).fadeIn('300');

    });

  }

  function copyElementToClipboard(element) {
    window.getSelection().removeAllRanges();
    let range = document.createRange();
    range.selectNode(typeof element === 'string' ? document.getElementById(element) : element);
    window.getSelection().addRange(range);
    document.execCommand('copy');
//    window.getSelection().removeAllRanges();
  }


  function load_amount(percent) {
    var total_price = $('#total_price').val();
    $('#payland_paymentAmount').val(total_price * percent);
  }

  $('#paymentDataContent').on("click", "#copyLinkStripe", function () {
    copyElementToClipboard('textPayment');
//    var link = $(this).find('#cpy_link');
//    document.getElementById("cpy_link").style.display = "block";
//    document.getElementById("cpy_link").select();
//    document.execCommand("copy");
//    document.getElementById("cpy_link").style.display = "none";

  });


  $('#_getPaymentVisa').on("click", function () {

    if ($('#visaDataContent').hasClass('open')) {
      $('#visaDataContent').fadeOut('300').removeClass('open');
    } else {
      var url = "{{route('booking.get_visa')}}";
      var booking = $('#payland_booking').val();
      var _token = $('#payland_token').val();
      $.post(url, {
        _token: _token,
        booking: booking,
      }, function (data) {
        $('#visaDataContent').empty().append(data).fadeIn('300').addClass('open');

      });
    }

  });

  $('#visaDataContent').on("click", '.copy_data', function () {
    var copyText = $(this).closest('div').find('input');
    copyText.select();
//  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

    /* Copy the text inside the text field */
    document.execCommand("copy");

  });



  $('#visaDataContent').on("click", '#_getPaymentVisaForce', function () {

    if (confirm('Refrescar datos de la targeta? (sólo se puede hacer una vez)')) {
      var url = "{{route('booking.get_visa')}}";
      var booking = $('#payland_booking').val();
      var _token = $('#payland_token').val();
      $.post(url, {
        _token: _token,
        booking: booking,
        force: true,
      }, function (data) {
        $('#visaDataContent').empty().append(data).fadeIn('300').addClass('open');

      });
    }

  });





  $('.only-numbers').keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl/cmd+A
                    (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: Ctrl/cmd+C
                            (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
                            // Allow: Ctrl/cmd+X
                                    (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
                                    // Allow: home, end, left, right
                                            (e.keyCode >= 35 && e.keyCode <= 39)) {
                              // let it happen, don't do anything
                              return;
                            }
                            // Ensure that it is a number and stop the keypress
                            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                              e.preventDefault();
                            }
                          });
                          
                          
  $('#save_creditCardData').on("click", function () {

      var url = "{{route('booking.save_creditCard')}}";
      var creditCardData = $('#creditCardData').val();
      var _token = $('#payland_token').val();
      var bID = $(this).data('id');
      $.post(url, {
        _token: _token,
        data: creditCardData,
        bID: bID,
      }, function (data) {
        window.show_notif(data.title,data.status,data.response);
      });
  });
</script>
<style>
  #visaDataContent div{
    clear: both;
    display: block;
    overflow: auto;
    margin: 1em 0;
  }
  #visaDataContent label{
    width: 20%;
    float: left;
    text-align: left;
    font-weight: 600;
    padding-top: 4px;
  }
  #visaDataContent input{
    width: 60%;
    border: none;
    float: left;
    padding: 4px;
  }
  #visaDataContent button{
    width: 12%;
    float: left;
    padding: 4px;
    margin-top: 2px;
  }
  input#send_notif {
    width: 21px;
    margin: 2px 1px 1px 5px;
  }
  .sendNotif label.checkbox {
    width: 5em !important;
    margin-top: -4px;
  }
  .btn-percents h2{
    font-size: 20px;
    text-align: left;
  }
  .btn-percents button {
    width: 46%;
    margin: 16px 2% 0;
    float: left;
    padding: 2px 7px;
    font-weight: 700;
    color: #000
  }
  div#textPayment {
    text-align: left;
    background-color: transparent;
  }
  
</style>
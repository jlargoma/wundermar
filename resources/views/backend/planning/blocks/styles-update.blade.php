
<link href="/assets/css/font-icons.css" rel="stylesheet" type="text/css"/>

<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css"
      media="screen">

<link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css"/>
<link rel="stylesheet" href="{{ assetV('/css/backend/updateBooking.css')}}" type="text/css"/>
<script type="text/javascript" src="{{ assetV('/js/backend/partee.js')}}"></script>

<script type="text/javascript" src="{{asset('/frontend/js/components/moment.js')}}"></script>
<script type="text/javascript" src="{{asset('/frontend/js/components/daterangepicker.js')}}"></script>
<script type="text/javascript" src="{{ assetV('/js/backend/buzon.js')}}"></script>
<script src="{{ asset('/vendors/ckeditor/ckeditor.js') }}"></script>
<script src="/assets/js/notifications.js" type="text/javascript"></script>
@include('backend.planning._bookScripts', ['update' => 1])
<script type="text/javascript">
$(document).ready(function () {
  window.onscroll = function() {fixedHeader()};
  var header = document.getElementById("headerFixed");
  var sticky = header.offsetTop;
  function fixedHeader() {
    if (window.pageYOffset > sticky) {
      header.classList.add("mobile-fixed");
    } else {
      header.classList.remove("mobile-fixed");
    }
  }
  window.getPayments = function(){
    $('#payments_block').load('{{route("booking.paymentBlock",$book->id)}}');
  }
  @if( $uRole == "admin" || $uRole == "subadmin")
  $('body').on('change','.cc_upd',function(event) {
    var id = {{$book->id}};
    var idCustomer = {{$book->customer_id}};
    var cc_cvc = $('#cc_cvc').val();
    var cc_number = $('#cc_number').val();
    $('#loadigPage').show('slow');
    $.post('/admin/reservas/upd-visa', { _token: "{{ csrf_token() }}",id:id,idCustomer:idCustomer,cc_cvc:cc_cvc,cc_number:cc_number }, function(data) {
        if (data.status == 'success') {
          window.show_notif('Ok',data.status,data.response);
        } else {
          window.show_notif('Error:',data.status,data.response);
        }
        $('#loadigPage').hide('slow');
    });
  });
  @endif
  
  
  $('.copyLinkSupl').on("click",function () {
    var element = document.getElementById('textLinkSupl');
    element.setAttribute("style", "display: block;");
    window.getSelection().removeAllRanges();
    let range = document.createRange();
    range.selectNode(element);
    console.log(range);
    window.getSelection().addRange(range);
    document.execCommand('copy');
    element.setAttribute("style", "display: none;");
     window.show_notif('Texto de suplementos copiado','success');
  });
    
});    
</script>
<script type="text/javascript">
   window["is_mobile"] = <?php echo config('app.is_mobile') ? 1 : 0; ?>;
</script>
<script type="text/javascript" src="{{ assetV('/js/backend/chatBooking.js')}}"></script>  
<style>
    .mobile-scroll{
      overflow: hidden !important;
    }
    
div#textLinkSupl {
    height: 0;
    width: 0;
    display: none;
    /*overflow: hidden;*/
}
iframe.contentEmailing {
    width: 100%;
    min-height: 88vh;
    border: none;
    margin-top: -22px;
}
</style>


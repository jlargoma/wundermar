@include('backend.invoices.invoice-content')
<?php printInvoiceContent($oInvoice,$items,$site); ?>
<div class="row col-xs-12  text-center">
 <input type="hidden" id="_token_mail" value="<?php echo csrf_token(); ?>">  
<button class="btn btn-complete" type="button" id="sendInvoiceEmail" data-id="{{$oInvoice->id}}" data-email="{{$oInvoice->email}}">Enviar</button>
<button class="btn btn-success" type="button" id="backEditInvoice" data-book_id="{{$oInvoice->book_id}}" >Volver</button>
</div>
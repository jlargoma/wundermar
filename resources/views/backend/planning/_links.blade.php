<?php
    use \Carbon\Carbon;
?>
<?php
    if ($import == 0) {
        $multipler = 0.50;
        if (count($payments) > 0 ){
            $dateStart = Carbon::createFromFormat('Y-m-d', $book->start);
            $now = Carbon::now();

            if ( $now->diffInDays($dateStart) <= 15 ) {
                $multipler = 0.50;
            }elseif($now->diffInDays($dateStart) <= 7){
                $multipler = 1;
            }
        }else{
            if( count($payments) == 1){
                $multipler = 0.50;
            }elseif( count($payments) > 1 ){
                $multipler = 0.50;
            }
        }

        $price = $book->total_price * $multipler;
    }else{
        $price = $import;
        $multipler =  $import / $book->total_price;
    }
    $multipler = round($multipler * 100)

?>

<div class="col-md-4 col-xs-12">
    <h2 class="text-center font-w300" style="letter-spacing: -1px;">
        LINKS PAGOS  STRIPE
    </h2>
    <div class="col-md-8 col-xs-12 push-20">
        <input type="number" class="form-control only-numbers" name="importe_stripe" id="importe_stripe" placeholder="importe..." value="<?php echo $price ?>" data-idBook="<?php echo $book->id?>" />
    </div>
    <div class="col-md-4 col-xs-12 push-20">
        <button id="btnGenerate" class="btn btn-success" type="button">Generar</button>
    </div>
</div>
<div class="col-md-8 col-xs-12" style="padding: 0 15px;">
    <h2 class="text-left" id="stripe-text" style="font-size: 20px; line-height: 15px;">
        <span style="font-size: 18px; line-height: 15px;">En este link podrás realizar el pago de la señal por el {{ $multipler }}% del total.<br> En el momento en que efectúes el pago, te llegará un email confirmando tu reserva.</span><br>
        <a target="_blank" href="https://www.miramarski.com/reservas/stripe/pagos/<?php echo base64_encode($book->id) ?>/<?php echo base64_encode($price) ?>">
            https://www.miramarski.com/reservas/stripe/pagos/<?php echo base64_encode($book->id) ?>/<?php echo base64_encode($price) ?>
        </a>
    </h2>
    <button class="btn btn-cons" type="button" id="copy-link-stripe" data-link="https://www.miramarski.com/reservas/stripe/pagos/<?php echo base64_encode($book->id) ?>/<?php echo base64_encode($price) ?>">
        <span class="bold">Copiar Link</span>
    </button>
</div>
<script type="text/javascript">
    $('button#btnGenerate').click(function(event) {
        var importe = $('#importe_stripe').val();
        var book = $('#importe_stripe').attr('data-idBook');
        $('.content-link-stripe').empty().load('/admin/books/getStripeLink/'+book+'/'+importe);
    });

    $(document).on("click","#copy-link-stripe", function(){
        var text = $('#stripe-text').text().replace(/\s\s+/g, '');
        var text = text.replace('http', "\n http");
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
    });

</script>

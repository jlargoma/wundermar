<?php   use \Carbon\Carbon;  
        setlocale(LC_TIME, "ES"); 
        setlocale(LC_TIME, "es_ES");  
?>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
    <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
</button>
<?php if (!$mobile ): ?>
<div class="col-md-12 not-padding content-last-books">
    <div class="alert alert-info fade in alert-dismissable" style="max-height: 600px; overflow-y: auto;">
        <!-- <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a> -->
        <!-- <strong>Info!</strong> This alert box indicates a neutral informative change or action. -->
        <h4 class="text-center"> ALARMAS DE ENTRADA/PAGO </h4>
        <table class="table" style="margin-top: 0;">
            <tbody>
                <?php foreach ($alarms as $key => $book): ?>
                    <tr>
                        <td class="text-center"  style="width: 30px; padding: 5px 0!important">
                            <?php if ($book->agency != 0): ?>
                                <img style="width: 20px;margin: 0 auto;" src="/pages/<?php echo strtolower($book->getAgency($book->agency)) ?>.png" align="center" />
                            <?php endif ?>
                        </td>
                        <td class ="text-center" style="color: black;padding: 5px!important;">  
                            <a class="update-book" data-id="<?php echo $book->id ?>"  title="Editar Reserva"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>">
                                <?php echo $book->customer->name ?>
                            </a> 
                            
                        </td>
                        <td class="text-center" style="color: black; padding: 5px!important">   
                            <?php echo substr($book->room->nameRoom,0,5) ?>       
                        </th>
                        <td class="text-center" style="color: black; padding: 5px!important">   
                            <a href="tel:<?php echo $book->customer->phone ?>"><?php echo $book->customer->phone ?>
                        </th>
                        <td class="text-center" style="color: black;padding: 5px 10px!important">   
                            <b>
                                <?php
                                    $start = Carbon::createFromFormat('Y-m-d',$book->start);
                                    echo $start->formatLocalized('%d %b');
                                ?>        
                            </b> - 
                            <b>
                                <?php
                                    $finish = Carbon::createFromFormat('Y-m-d',$book->finish);
                                    echo $finish->formatLocalized('%d %b');
                                ?>        
                            </b>           
                        </td>
                        <td class="text-center" style="color: black;padding: 5px!important;">
                             <b><?php echo $book->total_price ?>€</b>
                        </td>
                        <td class="text-center" style="color: black;">  

                            <?php $payments = \App\Payments::where('book_id', $book->id)->get(); ?>
                            <?php $paymentBook = 0; ?>
                            <?php $fromStripe = false; ?>
                            <?php if ( count($payments) > 0): ?>
                                <?php foreach ($payments as $key => $payment): ?>
                                    <?php $paymentBook += $payment->import; ?>
                                    <?php if (preg_match('/stripe/i', $payment->comment)): ?>
                                        <?php $fromStripe = true; ?>
                                    <?php endif ?>
                                    
                                <?php endforeach ?>
                            <?php endif ?>
                            <?php echo  $paymentBook." €" ?> <br>
                            <?php if ($paymentBook != 0 && $book->total_price>0): ?>
                                <b><?php echo  round(($paymentBook/$book->total_price)*100)?>%</b>
                            <?php else: ?>
                                <b>0%</b>
                            <?php endif ?>
                            
                        </td>
                        <td class="text-center">
                            <?php if ($fromStripe): ?>
                                <a target="_blank" href="https://dashboard.stripe.com/payments"><img src="/img/stripe-icon.jpg" style="width: 20px;"></a>
                            <?php endif ?> 
                        </td>
                        <td class="text-center">

                            <?php if ($book->send == 1): ?>
                                <button data-id="<?php echo $book->id ?>" class="btn btn-xs btn-default sendSecondPay" type="button" data-toggle="tooltip" title="" data-original-title="Enviar recordatorio segundo pago" data-sended="1">
                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                </button> 
                            <?php else: ?>
                                <button data-id="<?php echo $book->id ?>" class="btn btn-xs btn-primary sendSecondPay" type="button" data-toggle="tooltip" title="" data-original-title="Enviar recordatorio segundo pago" data-sended="0">
                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                </button> 
                            <?php endif ?>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 

<?php else: ?>

    <div class="row" >
        <div class="col-xs-12" style="background-color: #daeffd!important; max-height: 600px; overflow-y: auto;">
            <div class="row">
                <h5 class="text-center"> ALARMAS DE ENTRADA/PAGO </h5>
            </div>
            <div class="row">
                <div class="table-responsive" style="border: 0px;">
                    <table class="table table-striped" style="margin-top: 0;">
                        <tbody>
                            <?php foreach ($alarms as $key => $book): ?>
                                <tr>
                                    <td class ="text-left static" style="width: 100px;color: black;overflow-x: scroll; padding: 7px 1px !important; ">  
                                      <?php if ( $book->agency != 0): ?>
                              <img src="/pages/<?php echo strtolower($book->getAgency($book->agency)) ?>.png" class="img-agency" />
                            <?php endif ?>
                                        <a class="update-book" data-id="<?php echo $book->id ?>"  title="Editar Reserva"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>">
                                            <?php echo substr($book->customer->name, 0, 10) ?>
                                        </a> 
                                        
                                    </td>
                                    <td class="text-center first-col" style="padding-left: 100px!important">     
                                        <?php echo substr($book->room->nameRoom,0,5) ?>       
                                    </td>
                                    <td class="text-center" style="color: black; padding: 5px!important">   
                                        <a href="tel:<?php echo $book->customer->phone ?>"><i class="fa fa-phone"></i></a>      
                                    </td>
                                    
                                    <td class="text-center" style="color: black;padding: 5px 10px!important">   
                                        <b>
                                            <?php
                                                $start = Carbon::createFromFormat('Y-m-d',$book->start);
                                                echo $start->formatLocalized('%d %b');
                                            ?>        
                                        </b>
                                    </td>

                                    <td class="text-center" style="color: black;padding: 5px 10px!important">   
                                        <b>
                                            <?php
                                                $finish = Carbon::createFromFormat('Y-m-d',$book->finish);
                                                echo $finish->formatLocalized('%d %b');
                                            ?>        
                                        </b>           
                                    </td>
                                    <td class="text-center" style="color: black;padding: 5px!important;font-size: 12px;">
                                        <b><?php echo round($book->total_price) ?>€</b>
                                    </td>
                                    <td class="text-center" style="color: black;font-size: 12px;">  

                                        <?php $payments = \App\Payments::where('book_id', $book->id)->get(); ?>
                                        <?php $paymentBook = 0; ?>
                                        <?php $fromStripe = false; ?>
                                        <?php if ( count($payments) > 0): ?>
                                            <?php foreach ($payments as $key => $payment): ?>
                                                <?php $paymentBook += $payment->import; ?>
                                                <?php if (preg_match('/stripe/i', $payment->comment)): ?>
                                                    <?php $fromStripe = true; ?>
                                                <?php endif ?>
                                                
                                            <?php endforeach ?>
                                        <?php endif ?>
                                        <?php echo  $paymentBook." €" ?> <br>
                                        <b class="text-danger">
                                            <?php
                                                if($paymentBook != 0 && $book->total_price != 0){
                                                    echo round(($paymentBook/$book->total_price)*100);
                                                }else{
                                                    echo '';
                                                }
                                            ?>%
                                        </b>
                                    </td>
                                   
                                    <td class="text-center">

                                        <?php if ($book->send == 1): ?>
                                            <button data-id="<?php echo $book->id ?>" class="btn btn-xs btn-default sendSecondPay" type="button" data-toggle="tooltip" title="" data-original-title="Enviar recordatorio segundo pago" data-sended="1">
                                                <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                            </button> 
                                        <?php else: ?>
                                            <button data-id="<?php echo $book->id ?>" class="btn btn-xs btn-primary sendSecondPay" type="button" data-toggle="tooltip" title="" data-original-title="Enviar recordatorio segundo pago" data-sended="0">
                                                <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                            </button> 
                                        <?php endif ?>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> 
<?php endif ?>

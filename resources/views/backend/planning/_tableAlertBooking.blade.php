<?php   
    use \Carbon\Carbon;  
    use App\Classes\Mobile;
    setlocale(LC_TIME, "ES"); 
    setlocale(LC_TIME, "es_ES");

    if (!isset($mobile)) {

        $mobile = new Mobile();
     } 

?>
<div class="row">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
        <i class="fa fa-close fs-20" style="color: #000!important;"></i>
    </button>
</div>
<?php $notifications = \App\BookNotification::all(); ?>
<?php $isNotify = false; ?>
<?php $countNotify = 0; ?>
<?php foreach ($notifications as $key => $notify): ?>
    <?php if ($notify->book->type_book != 3 || $notify->book->type_book != 5 || $notify->book->type_book != 6):?>
        <?php $isNotify = true; $countNotify ++; ?>
    <?php endif; ?>
<?php endforeach ?>
<?php if ($isNotify): ?>
    <div class="row " id="content-table-alert">
        <div class="col-xs-12">
            <div class="alert alert-info fade in alert-dismissable" style="background-color: #daeffd!important;">
                <h4 class="text-center">Alertas BOOKING</h4> 
                <h5 class="text-center">Recuerda eliminar los dias de las siguientes reservas de booking.com</h5> 
                
                <?php if (count($notifications) > 0): ?>
                    <?php if (!$mobile ): ?>
                        <div class="col-xs-12">
                            <button class="btn btn-danger text-white pull-right deleteAll" style="display: none">Borrar</button>
                        </div>
                        <table class="table table-condensed tableAlert" style="margin-top: 0;">
                            <tbody>
                            <?php foreach ($notifications as $key => $notify): ?>
                                <?php if ($notify->book->type_book == 1 || $notify->book->type_book == 2 || $notify->book->type_book == 7  || $notify->book->type_book == 8): ?>
                                <tr>

                                    <td class="text-center" style="padding: 5px 0!important">
                                        <input type="checkbox" class="alertsChecks" data-id="<?php echo $notify->id ?>"  name = "alertsChecks[]">
                                    </td>
                                    <td class="text-center" style="width: 30px; padding: 5px 0!important">
                                        <?php if ($notify->book->agency != 0): ?>
                                            <img src="/pages/booking.png" style="width: 20px;"/>
                                        <?php else: ?>

                                        <?php endif ?>
                                    </td>
                                    <td class="text-center" style="padding: 5px 0!important">
                                        <?php echo $notify->book->customer->name; ?></td>
                                    <td class="text-center" style="padding: 5px 0!important">
                                        <?php
                                            $start = Carbon::createFromFormat('Y-m-d',$notify->book->start);
                                            echo $start->formatLocalized('%d %b');
                                        ?> - 
                                        <?php
                                            $finish = Carbon::createFromFormat('Y-m-d',$notify->book->finish);
                                            echo $finish->formatLocalized('%d %b');
                                        ?>
                                    </td>
                                    <td class="text-center" style="padding: 5px 0!important">
                                        <b><?php echo $notify->book->room->nameRoom ?> - <?php echo substr($notify->book->room->name,0,5) ?></b>                                               
                                    </td>
                                    <td class="text-center" style="padding: 5px 0!important">
                                        <b><?php echo $notify->book->getStatus($notify->book->type_book)?> </b>                                               
                                    </td>
                                    <td class="text-center" style="padding: 5px 0!important">
                                        <b><?php echo number_format($notify->book->total_price,2, ',', ".") ?> €</b>                                               
                                    </td>
                                </tr>
                                <?php endif ?>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <button class="btn btn-danger text-white pull-right deleteAll push-20" style="display: none" >Borrar</button>
                            </div>
                            <div class="row table-responsive">
                                <table class="table tableAlert" style="margin-top: 0;">
                                    <tbody>
                                    <?php foreach ($notifications as $key => $notify): ?>
                                        <?php if ($notify->book->type_book == 1 || $notify->book->type_book == 2 || $notify->book->type_book == 7  || $notify->book->type_book == 8): ?>
                                        <tr>
                                            <td class="text-center" style="padding: 5px!important">
                                                <input type="checkbox" class="alertsChecks" data-id="<?php echo $notify->id ?>"  name = "alertsChecks[]">
                                            </td>
                                            <td class="text-center" style="padding: 0 5px!important">
                                                <?php echo substr($notify->book->customer->name, 0, 8); ?>...
                                                    
                                            </td>
                                            <td class="text-center" style="padding: 0 5px!important">
                                                <?php
                                                    $start = Carbon::createFromFormat('Y-m-d',$notify->book->start);
                                                    echo $start->formatLocalized('%d %b');
                                                ?> - <?php
                                                    $finish = Carbon::createFromFormat('Y-m-d',$notify->book->finish);
                                                    echo $finish->formatLocalized('%d %b');
                                                ?>
                                            </td>
                                            <td class="text-center" style="padding: 0 5px!important">
                                                <b><?php echo $notify->book->room->nameRoom ?> - <?php echo substr($notify->book->room->name,0,3) ?></b>                                              
                                            </td>
                                            <td class="text-center" style="padding: 0 5px!important">
                                                <b><?php echo substr($notify->book->getStatus($notify->book->type_book), 0, -10)?> </b>                                               
                                            </td>
                                        </tr>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif ?>
                <?php else: ?>
                    <h2 class="text-center"> No hay alertas</h2>
                <?php endif ?>
                <p class="text-justify">
                    Puedes acceder a booking haciendo click <a href="#">aquí</a>
                </p>
            </div>   
        </div>
    </div>
<?php endif ?>
<script type="text/javascript">
    $('.alertsChecks').change(function(event) {
        var check = $('.tableAlert').find('input[type=checkbox]:checked').length;
        if (check > 0) {
            $('.deleteAll').show();
        } else {
            $('.deleteAll').hide();
        }
        
    });

    $('.deleteAll').click(function(event) {
         
        $('input[type=checkbox]:checked').each(function () {
            var url = "/admin/delete/nofify/"+$(this).attr('data-id');
            $.get(url, function(data) {
                $('#content-table-alert').empty().append(data);
            });
        });
    });

</script>
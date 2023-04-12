<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
$total_pvp = 0;
$total_coste = 0;
$today = Carbon::now();

$isMobile = config('app.is_mobile');

?>
<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100">
  <i class="fa fa-times fa-2x" style="color: #000!important;"></i>
</button>

<div class="col-md-12 not-padding content-last-books">
  <div class="alert alert-info fade in alert-dismissable" style="max-height: 600px; overflow-y: auto;position: relative;">
    <!-- <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a> -->
    <!-- <strong>Info!</strong> This alert box indicates a neutral informative change or action. -->
    <h4 class="text-center"> ALARMAS DE PARTEE 
      <a href="https://app.partee.es/#/" target="_black" title="Ir a Partee">
        <i class="fa fa-external-link" aria-hidden="true"></i>
      </a>
      <a href="{{route('partee.sinc')}}"  class="btn btn-primary" title="Sincronizar Partee">
        Sincronizar <i class="fa fa-refresh"></i>
      </a>

    </h4>

    @if(count($books)>0)

    <div class="table-responsive" style="    overflow-y: hidden;">
      <table class="table" id="table_partee" >
        <thead>
          <tr class ="text-center bg-success text-white">
            @if($isMobile)
            <th class="th-bookings static" style="width: 130px; padding: 14px !important;background-color: #57d0bd;">  
              Nombre
            </th>
            <th class="th-bookings first-col" style="padding-left: 130px!important">Tel.</th>
            @else
            <th class="th-bookings static" style="background-color: #57d0bd;">  
              Nombre
            </th>
            <th class="th-bookings first-col">Tel.</th> 
            @endif
            <th class="th-bookings th-2">Pax</th>
            <th class="th-bookings">Apart</th>
            <th class="th-bookings th-2"><i class="fa fa-moon-o"></i> </th>
            <th class="th-bookings th-2"><i class="fa fa-clock-o"></i></th>
            <th class="th-bookings  th-4">IN - OUT </th>
            @if($isMobile)
            <th class="th-bookings th-6" style="min-width: 110px;">
            @else
            <th class="th-bookings th-6">  
            @endif
              Precio      </th>
<?php if (getUsrRole() != "limpieza"): ?>
              <th class="th-bookings th-6">   a      </th>
<?php endif ?>
            <th class="th-bookings th-2">&nbsp;</th>

          </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
              <?php $class = ( $book->start <= $today) ? "blurred-line" : '' ?>
            <tr class="<?php echo $class; ?>">
                <?php
                $dateStart = Carbon::createFromFormat('Y-m-d', $book->start);
                ?>
              @if($isMobile)
              <td class ="text-left static" style="width: 130px;color: black;overflow-x: scroll;    padding: 30px 6px !important; ">  
                @else
              <td class ="text-left" style="position: relative;">  
                @endif
  <?php if ($payment[$book->id] == 0): ?>
                  <?php if ($today->diffInDays($dateStart) <= 15): ?>
                    <span class=" label label-danger alertDay heart text-white">
                      <i class="fa fa-bell"></i>
                    </span>
                  <?php elseif ($today->diffInDays($dateStart) <= 7): ?>
                    <span class=" label label-danger alertDay heart text-white">
                      <i class="fa fa-bell"></i>
                    </span>
                  <?php endif; ?>
                <?php else: ?>
                  <?php $percent = 100 / ( $book->total_price / $payment[$book->id] ); ?>
    <?php if ($percent <= 25): ?>

                    <?php if ($today->diffInDays($dateStart) <= 15): ?>
                      <span class=" label label-danger alertDay heart text-white">
                        <i class="fa fa-bell"></i>
                      </span>
                    <?php elseif ($today->diffInDays($dateStart) <= 7): ?>
                      <span class=" label label-danger alertDay heart text-white">
                        <i class="fa fa-bell"></i>
                      </span>
      <?php endif; ?>

                  <?php endif ?>


                <?php endif ?>
                <?php if ($book->agency != 0): ?>
                  <img src="/pages/<?php echo strtolower($book->getAgency($book->agency)) ?>.png" class="img-agency" />
                <?php endif ?>
                <?php if (isset($payment[$book->id])): ?>
                  <a class="update-book" data-id="<?php echo $book->id ?>"  title="<?php echo $book->customer['name'] ?> - <?php echo $book->customer['email'] ?>"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>" style="color: red"><?php echo $book->customer['name'] ?></a>
                <?php else: ?>
                  <a class="update-book" data-id="<?php echo $book->id ?>"  title="<?php echo $book->customer['name'] ?> - <?php echo $book->customer['email'] ?>"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>" ><?php echo $book->customer['name'] ?></a>
                <?php endif ?>
                <?php if (getUsrRole() != "limpieza"): ?>
                  <?php if (!empty($book->comment) || !empty($book->book_comments)): ?>
                    <?php
                    $textComment = "";
                    if (!empty($book->comment)) {
                      $textComment .= "<b>COMENTARIOS DEL CLIENTE</b>:" . "<br>" . " " . $book->comment . "<br>";
                    }
                    if (!empty($book->book_comments)) {
                      $textComment .= "<b>COMENTARIOS DE LA RESERVA</b>:" . "<br>" . " " . $book->book_comments;
                    }
                    ?>
                    @if($isMobile)
                    <i class="fa fa-commenting msgs fa-2x" 
                       style="color: #000;" 
                       aria-hidden="true"
                       data-msg="{{$textComment}}"
                       ></i>
                    @else
                    <div class="tooltip-2">
                      <i class="fa fa-commenting" style="color: #000;" aria-hidden="true"></i>
                      <div class="tooltiptext" style="left: 0;top: 0;"><p class="text-left"><?php echo $textComment ?></p></div>
                    </div>
                    @endif
                  <?php endif ?>
                <?php endif ?>
              </td>
              @if($isMobile)
              <td class="text-center first-col" style="padding: 35px 0px !important; padding-left: 130px!important">
              @else
              <td class="text-center">
                @endif
  <?php if ($book->customer->phone != 0 && $book->customer->phone != ""): ?>
                  <a href="tel:<?php echo $book->customer->phone ?>">
                    <i class="fa fa-phone"></i>
                  </a>
                <?php endif ?>
              </td>
              <td class ="text-center" >
                <?php if ($book->real_pax > 6): ?>
                  <?php echo $book->real_pax ?><i class="fa fa-exclamation" aria-hidden="true" style="color: red"></i>
  <?php else: ?>
    <?php echo $book->pax ?>
                <?php endif ?>

              </td>
              <td class ="text-center">
  <?php echo $book->room->nameRoom ?>
              </td>
              <td class ="text-center"><?php echo $book->nigths ?></td>
              <td class="text-center sm-p-t-10 sm-p-b-10">
                {{$book->schedule}}
              </td>

  <?php $start = Carbon::createFromFormat('Y-m-d', $book->start); ?>
  <?php $finish = Carbon::createFromFormat('Y-m-d', $book->finish); ?>
              <td class ="text-center" data-order="<?php echo strtotime($start->copy()->format('Y-m-d')) ?>"  style="width:20%!important">
                <b><?php echo $start->formatLocalized('%d %b'); ?></b>
                <span>-</span>
                <b><?php echo $finish->formatLocalized('%d %b'); ?></b>
              </td>
                
              @if($isMobile)
              <td class ="text-center" style="min-width: 110px;"> 
              @else
              <td class ="text-center">
              @endif
            
                  <?php if (getUsrRole() != "limpieza"): ?>
                  <div class="col-md-6 col-xs-6 not-padding">
                      <?php echo round($book->total_price) . "€" ?><br>
                    <?php if (isset($payment[$book->id])): ?>
                      <p style="color: <?php if ($book->total_price == $payment[$book->id]): ?>#008000<?php else: ?>red<?php endif ?>;">
                      <?php echo $payment[$book->id] ?> €
                      </p>
                  <?php else: ?>
                  <?php endif ?>
                  </div>

    <?php if (isset($payment[$book->id])): ?>
                    <?php if ($payment[$book->id] == 0): ?>
                      <div class="col-md-5 col-xs-6 not-padding bg-success">
                        <b style="color: red;font-weight: bold">0%</b>
                      </div>
      <?php else: ?>
                      <div class="col-md-5  col-xs-6 not-padding">
        <?php $total = number_format(100 / ($book->total_price / $payment[$book->id]), 0); ?>
                        <p class="text-white m-t-10">
                          <b style="color: <?php if ($total == 100): ?>#008000<?php else: ?>red<?php endif ?>;font-weight: bold"><?php echo $total . '%' ?></b>
                        </p>
                      </div>

      <?php endif; ?>
                  <?php else: ?>
                    <div class="col-md-5 col-xs-6 not-padding bg-success">
                      <b style="color: red;font-weight: bold">0%</b>
                    </div>
                  <?php endif ?>
  <?php else: ?>
                <?php echo round($book->total_price) . "€" ?>
              <?php endif ?>
              </td>

                <?php if (getUsrRole() != "limpieza"): ?>
                <td class="text-center sm-p-t-10 sm-p-b-10">

                  <?php if ($book->send == 1): ?>
                    <button data-id="<?php echo $book->id ?>" class="btn btn-xs btn-default sendSecondPay" type="button" data-toggle="tooltip" title="" data-original-title="Enviar recordatorio segundo pago" data-sended="1">
                      <i class="fa fa-paper-plane" aria-hidden="true"></i>
                    </button> 
                  <?php else: ?>
                    <button data-id="<?php echo $book->id ?>" class="btn btn-xs btn-primary sendSecondPay" type="button" data-toggle="tooltip" title="" data-original-title="Enviar recordatorio segundo pago" data-sended="0">
                      <i class="fa fa-paper-plane" aria-hidden="true"></i>
                    </button> 
                  <?php endif ?>
                  <?php
                  $partee = $book->partee();
                  if ($partee):
                    echo $partee->print_status($book->id, $book->start, $book->pax, true);
                  endif;
                  ?>
                </td>
  <?php endif ?>
              <td class="text-center">
  <?php if ($book->promociones > 0): ?>
                  <span class="icons-comment" data-class-content="content-commentOwned-<?php echo $book->id ?>">
                    <img src="/img/oferta.png" style="width: 40px;">
                  </span>
                  <div class="comment-floating content-commentOwned-<?php echo $book->id ?>" style="display: none;"><p class="text-left"><?php echo $book->book_owned_comments ?></p></div>

  <?php endif ?>
              </td>


            </tr>
<?php endforeach ?>
        </tbody>
      </table>
      <div id="conteiner_msg_lst">
        <div class="box-msg-lst">
          <div id="box_msg_lst"></div>
          <button type="button" class="btn btn-default" id="box_msg_close">Cerrar</button>
        </div>
      </div>


      @else
      <p class="alert alert-warning">
        No existen registros.
      </p>
      @endif
    </div>
  </div> 



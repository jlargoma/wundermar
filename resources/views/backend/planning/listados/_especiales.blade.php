<?php

use \Carbon\Carbon;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");
?>

<div class="tab-pane " id="tabEspeciales">
  <div class="table-responsive">
    <table class="table table-data table-striped"  data-type="especiales">
      <thead>
        <tr>  
          <th class ="text-center Reserva Propietario text-white" >   Cliente     </th>
          <th class ="text-center Reserva Propietario text-white" >   
            @if($isMobile) <i class="fa fa-phone"></i> @else Telefono @endif    
          </th>
          <th class ="text-center Reserva Propietario text-white" style="width: 7%!important">   Pax         </th>
          <th class ="text-center Reserva Propietario text-white" style="width: 10%!important">   Apart       </th>
          <th class ="text-center Reserva Propietario text-white" style="width: 6%!important">   IN     </th>
          <th class ="text-center Reserva Propietario text-white" style="width: 8%!important">   OUT      </th>
          <th class ="text-center Reserva Propietario text-white" style="width: 6%!important">  <i class="fa fa-moon-o"></i> </th>
          <th class ="text-center Reserva Propietario text-white" >   Precio      </th>
          <th class ="text-center Reserva Propietario text-white" style="width: 17%!important">   Estado      </th>
          <th class ="text-center Reserva Propietario text-white" style="width: 6%!important">A</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($books as $book): ?>
          <?php $class = ucwords($book->getStatus($book->type_book)) ?>
  <?php if ($class == "Contestado(EMAIL)"): ?>
    <?php $class = "contestado-email" ?>
  <?php endif ?>

          <tr class="<?php echo strtolower($class); ?>" data-id="{{$book->id}}" >
            <td class="fix-col td-b1" data-filter="site{{$book->room->site_id}}">
              <div class=" fix-col-data">
                <?php if (isset($payment[$book->id])): ?>
                  <a class="update-book" data-id="<?php echo $book->id ?>"  title="<?php echo $book->customer->name ?> - <?php echo $book->customer->email ?>"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>" style="color: red"><?php echo $book->customer['name'] ?></a>
  <?php else: ?>
                  <a class="update-book" data-id="<?php echo $book->id ?>"  title="<?php echo $book->customer->name ?> - <?php echo $book->customer->email ?>"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>" ><?php echo $book->customer['name'] ?></a>
  <?php endif ?> 
              </div>
            </td>
            @if($isMobile)
            <td >
              <?php if ($book->customer->phone != 0 && $book->customer->phone != ""): ?>
                <a href="tel:<?php echo $book->customer->phone ?>">
                  <i class="fa fa-phone"></i>
                </a>
              <?php endif ?>
            </td>
            @else
            <td >
              <?php if ($book->customer->phone != 0 && $book->customer->phone != ""): ?>
                <a href="tel:<?php echo $book->customer->phone ?>"><?php echo $book->customer->phone ?></a>
  <?php else: ?>
                <input type="text" class="only-numbers customer-phone" data-id="<?php echo $book->customer->id ?>"/>
              <?php endif ?>
            </td>
            @endif
            <td  >
              <?php if ($book->real_pax > 6): ?>
                <?php echo $book->real_pax ?><i class="fa fa-exclamation" aria-hidden="true" style="color: red"></i>
  <?php else: ?>
                <?php echo $book->pax ?>
              <?php endif ?>
            </td>
            <td >
              <?php
              if ($book->room) {
                $room = $book->room;
                ?>
                <button type="button" class="btn changeRoom" data-c="{{$room->id}}">
                <?php echo substr($room->nameRoom . " - " . $room->name, 0, 15); ?>
                </button>  
    <?php
  }
  ?>
            </td>
            <td class="td-date" data-order="{{$book->start}}">
              <?php echo dateMin($book->start) ?>
            </td>
            <td class="td-date" data-order="{{$book->finish}}">
              <?php echo dateMin($book->finish) ?>
            </td>
            <td ><?php echo $book->nigths ?></td>
            <td >
               <?php 
                if ($uRole != "limpieza"):
                  echo $book->showPricePlanning($payment);
                else:
                  echo round($book->total_price) . "€";
                endif;
                ?>
            </td>
            <td >
              <button type="button" class="btn changeStatus" data-c="{{$book->type_book}}">
                {{$book->getStatus($book->type_book)}}
              </button>
            </td>
            <td > 
              <button data-id="<?php echo $book->id ?>" class="btn btn-xs btn-danger deleteBook" type="button" data-toggle="tooltip" title="" data-original-title="Eliminar Reserva" >
                <i class="fa fa-trash"></i>
              </button>
            </td>
          </tr>
<?php endforeach ?>
      </tbody>
    </table> 
  </div>
</div>
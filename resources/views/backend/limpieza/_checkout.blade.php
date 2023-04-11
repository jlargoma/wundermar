<?php   use \Carbon\Carbon;  
        setlocale(LC_TIME, "ES"); 
        setlocale(LC_TIME, "es_ES"); 
?>
<?php $startWeek = Carbon::now()->startOfWeek(); ?>
<?php $endWeek = Carbon::now()->endOfWeek(); ?>
<div class="table-responsive">
    <table class="table tableCheckOut table-data  table-striped" >
        <thead>
          <th class="bg-primary text-white" >Cliente</th>
          <th class="bg-primary text-white text-center">
            @if($isMobile) <i class="fa fa-phone"></i> @else Telefono @endif
          </th>
            <th class="bg-primary text-white text-center">Pax</th>
            <th class="bg-primary text-white text-center">Out</th>
            <th class="bg-primary text-white text-center">Apto</th>
            <th class="bg-primary text-white text-center"><i class="fa fa-clock-o" aria-hidden="true"></i>Salida</th>
            <th class="bg-primary text-white text-center"></th>
        </thead>
        <tbody>
            <?php $count = 0 ?>
            <?php foreach ($checkout as $book): ?>
                <?php if ( $book->start >= $startWeek->copy()->format('Y-m-d') && $book->start <= $endWeek->copy()->format('Y-m-d')): ?>
                    <?php $class = "blurred-line" ?>
                <?php else: ?>
                    <?php $class = "lined"; $count++ ?>
                <?php endif ?>
                <tr class="<?php if($count <= 1){echo $class;} ?>" data-id="{{$book->id}}" >
                  
                    <td class="text-left py-1">
                        {{$book->customer->name}}
                    </td>
                    @if($isMobile)
                    <td class="text-center" style="max-width: 10px !important;">
                      <?php if ($book->customer->phone != 0 && $book->customer->phone != ""): ?>
                        <a href="tel:<?php echo $book->customer->phone ?>">
                          <i class="fa fa-phone"></i>
                        </a>
                      <?php endif ?>
                    @else
                    <td class="text-center"  style="max-width: 80px !important;">
                      <?php if ($book->customer->phone != 0 && $book->customer->phone != ""): ?>
                        <a href="tel:<?php echo $book->customer->phone ?>"><?php echo $book->customer->phone ?></a>
                      <?php endif ?>
                    @endif
                    </td>
                    <td class ="text-center" >
                            <?php echo $book->pax ?>
                    </td>
                    <td class="text-center sm-p-t-10 sm-p-b-10" data-order="{{$book->finish}}">
                      <?php echo dateMin($book->finish) ?>
                    </td>
                    <td class="text-center sm-p-t-10 sm-p-b-10">
                      <?php if ($isMobile): ?>
                        <b><?php echo substr($book->room->nameRoom, 0, 8);?></b>
                      <?php else:?>
                        <b><?php echo substr($book->room->nameRoom." - ".$book->room->name, 0, 15)  ?></b>
                      <?php endif;?>
                    </td>
                    <td class="text-center sm-p-t-10 sm-p-b-10">
                      <?php 
                        if ($book->scheduleOut == 24){
                          echo '<b>CheckOUT</b>';
                        } else {
                          echo $book->scheduleOut.'0Hrs.';
                        }
                        ?>
                    </td>
                    <td class="text-center sm-p-t-10 sm-p-b-10">
                        <?php $book->printExtraIcon(); ?>
                    </td>

                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
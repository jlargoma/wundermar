<?php   use \Carbon\Carbon;  
        setlocale(LC_TIME, "ES"); 
        setlocale(LC_TIME, "es_ES"); 
$startWeek = Carbon::now()->startOfWeek();
$endWeek = Carbon::now()->endOfWeek(); 
$t_class = ($isMobile) ? '' : 'th-bookings';
?>
<div class="tab-pane" id="tabPagadas">
    <div class="table-responsive">
        <table class="table tableBlock table-data  table-striped" >
            <thead>
                <tr class ="text-center text-white" style="background-color: #448eff;">
                    <th class="{{$t_class}} th-name" >Cliente</th>
                    <th class="th-bookings"> 
                      @if($isMobile) <i class="fa fa-phone"></i> @else Telefono @endif
                    </th>
                    <th class="{{$t_class}} th-6">Apart</th>
                    <th class="{{$t_class}} th-3">  <i class="fa fa-moon-o"></i> </th>
                    <th class="{{$t_class}} th-4">IN</th>
                    <th class="{{$t_class}} th-4">OUT</th>
                    <th class="{{$t_class}} th-2">&nbsp;</th>
                    <th class="{{$t_class}} th-2">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 0 ?>
                <?php foreach ($bloqueada as $book): ?>
                    <?php if ( $book->start >= $startWeek->copy()->format('Y-m-d') && $book->start <= $endWeek->copy()->format('Y-m-d')): ?>

                       <?php if ( $book->start <= Carbon::now()->copy()->subDay()->format('Y-m-d') ): ?>
                           <?php $class = "blurred-line" ?>
                       <?php else: ?>
                           <?php $class = "" ?>
                       <?php endif ?>

                   <?php else: ?>

                       <?php if ( $book->start <= Carbon::now()->copy()->subDay()->format('Y-m-d') ): ?>
                           <?php $class = "blurred-line" ?>
                       <?php else: ?>
                           <?php $class = "lined"; $count++ ?>
                       <?php endif ?>
                   <?php endif ?>

                    <tr class="<?php if($count <= 1){echo $class;} ?>" data-id="{{$book->id}}" >
                        <?php 
                            $dateStart = Carbon::createFromFormat('Y-m-d', $book->start);
                            $now = Carbon::now();
                        ?>
                         <td class="fix-col td-b1">
                          <div class="fix-col-data" >
                            {{$book->customer['name']}}
                          </div>
                        </td>
                        @if($isMobile)
                          <td class="text-center">
                            <?php if ($book->customer->phone != 0 && $book->customer->phone != ""): ?>
                              <a href="tel:<?php echo $book->customer->phone ?>">
                                <i class="fa fa-phone"></i>
                              </a>
                            <?php endif ?>
                          @else
                          <td class="text-center">
                            <?php if ($book->customer->phone != 0 && $book->customer->phone != ""): ?>
                              <a href="tel:<?php echo $book->customer->phone ?>"><?php echo $book->customer->phone ?></a>
                            <?php endif ?>
                          @endif
                          </td>
                        <td class ="text-center">
                          <?php 
                          if ($book->room){
                            $room = $book->room;
                            echo substr($room->nameRoom . " - " . $room->name, 0, 15);
                          }
                          ?>
                        </td>
                        <td class ="text-center"><?php echo $book->nigths ?></td>
                        <td class="text-center" data-order="{{$book->start}}">{{dateMin($book->start)}}</td>
                        <td class="text-center" data-order="{{$book->finish}}">{{dateMin($book->finish)}}</td>
                        <td class="text-center">
                          @if($book->book_comments)
                          <div class="show-comment">
                          <i class="fa fa-commenting" style="color: #000;" aria-hidden="true"></i>
                          <div class="comment-floating content-commentOwned-<?php echo $book->id?>" style="display: none;"><p class="text-left"><?php echo $book->book_comments ?></p></div>
                          </div>
                          @endif
                        </td>
                        <td>
                          <button data-id="<?php echo $book->id ?>" class="btn  btn-xs btn-danger deleteBook" title="Eliminar Reserva">
                            <i class="fa fa-trash"></i>
                          </button>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>  
    </div>
</div>

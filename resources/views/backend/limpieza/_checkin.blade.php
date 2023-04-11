<?php   use \Carbon\Carbon;  
        setlocale(LC_TIME, "ES"); 
        setlocale(LC_TIME, "es_ES"); 
$startWeek = Carbon::now()->startOfWeek();
$endWeek = Carbon::now()->endOfWeek(); 
$t_class = ($isMobile) ? '' : 'th-bookings';
?>
<div class="tab-pane" id="tabPagadas">
    <div class="table-responsive">
        <table class="table tableCheckIn table-data  table-striped" >
            <thead>
                <tr class ="text-center bg-success text-white">
                    <th class="{{$t_class}} th-name" >Cliente</th>
                    <th class="th-bookings"> 
                      @if($isMobile) <i class="fa fa-phone"></i> @else Telefono @endif
                    </th>
                    <th class="{{$t_class}} th-2">Pax</th>
                    <th class="{{$t_class}} th-6">Apart</th>
                    <th class="{{$t_class}} th-3">  <i class="fa fa-moon-o"></i> </th>
                    <th class="{{$t_class}} th-3"> <i class="fa fa-clock-o"></i></th>
                    <th class="{{$t_class}} th-4">IN</th>
                    <th class="{{$t_class}} th-4"><i class="fas fa-bed"></i></th>
                    <th class="{{$t_class}} th-4">OUT</th>
                    <th class="{{$t_class}} th-2">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 0 ?>
                <?php foreach ($checkin as $book): ?>
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
                        
                        <td class ="text-center" >
                           {{$book->pax}}
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
                        <td class="text-center sm-p-t-10 sm-p-b-10">
                           {{$book->schedule}} Hrs.
                        </td>
                        <td class="text-center" data-order="{{$book->start}}">{{dateMin($book->start)}}</td>
                        <?php 
                          $orderBeds = 0; $icon = '';
                          if (in_array($book->id, $cliHas[1])){
                            $orderBeds = 1;
                            $icon .= '<span class="cliHas active" title="CON CUNA"><i class="fas babyCarriage"></i></span>';
                          }
                          if (in_array($book->id, $cliHas[0])){
                              $icon .= '<span class="red" title=" CON CAMAS SUPLETORIAS" ><i class="fas fa-bed"></i></span>';
                              $orderBeds = 1;
                          } 
                          if($orderBeds == 0) {
                            $icon = '<span class="grey" title="Sin CAMAS SUPLETORIAS" ><i class="fas fa-bed"></i></span>';
                          }
                        ?>
                        <td class="td-date mobil-pad-x3" data-order="{{$orderBeds}}">
                            <?php echo $icon; ?>
                        </td>
                        <td class="text-center" data-order="{{$book->finish}}">{{dateMin($book->finish)}}</td>
                        <td class="text-center">
                          <?php $book->printExtraIcon(); ?>
                          
                           <?php if (!empty($book->comment) || !empty($book->book_comments) || !empty($book->book_owned_comments)): ?>
                          
                            <div data-booking="<?php echo $book->id; ?>" class="showBookComm col-xs-4" >
                              <i class="fa fa-commenting" style="color: #000;" aria-hidden="true"></i>
                              <div class="BookComm tooltiptext"></div>
                            </div>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>  
    </div>
</div>

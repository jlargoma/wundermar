<?php   use \Carbon\Carbon;  
        setlocale(LC_TIME, "ES"); 
        setlocale(LC_TIME, "es_ES"); 
?>
<?php $today = Carbon::now()->format('Y-m-d'); ?>
<?php $startWeek = Carbon::now()->startOfWeek(); ?>
<?php $endWeek = Carbon::now()->endOfWeek(); ?>
<div class="table-responsive">
    <table class="table table-data table-striped"  style="margin: 0;">
        <thead>
          <th class="bg-primary text-white" style="min-width: 100px !important;">Cliente</th>
          <th class="bg-primary text-white text-center" style="max-width: 80px !important;">
            @if($isMobile) <i class="fa fa-phone"></i> @else Telefono @endif
          </th>
            <th class="bg-primary text-white text-center">Pax</th>
            <th class="bg-primary text-white text-center">Out</th>
            <th class="bg-primary text-white text-center">Apto</th>
            <th class="bg-primary text-white text-center"><i class="fa fa-clock-o" aria-hidden="true"></i>@if(!$isMobile) Salida @endif</th>
            <?php if (getUsrRole() != "limpieza"): ?><th class="bg-primary text-white text-center">A</th><?php endif ?>
        </thead>
        <tbody>
            <?php $count = 0 ?>
            <?php foreach ($books as $book): ?>
                <?php if ( $book->finish < $today): ?>
                    <?php $class = "blurred-line" ?>
                <?php else: ?>
                    <?php $class = "lined";?>
                <?php endif ?>
                <tr class="<?php echo $class; ?>" data-id="{{$book->id}}" >
                  
                    <td class="text-left sm-p-t-10 sm-p-b-10" data-filter="site{{$book->room->site_id}}">
                      @if($book->leads)<i class="fa fa-star" style="color: #c5cc00;"></i>@endif
                        <a class="update-book" data-id="<?php echo $book->id ?>"  title="Editar Reserva"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>">
                            <?php echo substr($book->customer->name, 0, 10) ?>
                        </a> 
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
                      <?php else: ?>
                        <input type="text" class="only-numbers customer-phone" data-id="<?php echo $book->customer->id ?>"/>
                      <?php endif ?>
                    @endif
                    
                    <div class="row table-icon-row">
                      <?php $book->printExtraIcon(); ?>
                        <?php if (getUsrRole() != "limpieza" && (!empty($book->comment) || !empty($book->book_comments))): ?>
                            <?php 
                                $textComment = "";
                                if (!empty($book->comment)) {
                                    $textComment .= "<b>COMENTARIOS DEL CLIENTE</b>:"."<br>"." ".$book->comment."<br>";
                                }
                                if (!empty($book->book_comments)) {
                                    $textComment .= "<b>COMENTARIOS DE LA RESERVA</b>:"."<br>"." ".$book->book_comments;
                                }
                            ?>
                            <span class="icons-comment" data-class-content="content-comment-<?php echo $book->id?>">
                                <i class="fa fa-commenting" style="color: #000;" aria-hidden="true"></i>
                            </span>
                            <div class="comment-floating content-comment-<?php echo $book->id?>" style="display: none;"><p class="text-left"><?php echo $textComment ?></p></div>
                        <?php endif ?>
                      </div>
                    
                    </td>
                    <td class ="text-center" >
                        <?php if ($book->real_pax > 6 ): ?>
                            <?php echo $book->real_pax ?><i class="fa fa-exclamation" aria-hidden="true" style="color: red"></i>
                        <?php else: ?>
                            <?php echo $book->pax ?>
                        <?php endif ?>
                            
                    </td>
                    <td class="text-center sm-p-t-10 sm-p-b-10" data-order="<?php echo Carbon::CreateFromFormat('Y-m-d',$book->finish)->format("U") ?>">
                      <?php echo dateMin($book->finish) ?>
                    </td>
                    <td class="text-center sm-p-t-10 sm-p-b-10">
                      <?php if ($isMobile): ?>
                        <b><?php echo substr($book->room->nameRoom, 0, 8);?></b>
                      <?php else:?>
                        <b><?php echo substr($book->room->nameRoom." - ".$book->room->name, 0, 15)  ?></b>
                      <?php endif;?>
                    </td>
                    <td class="text-center sm-p-t-10 sm-p-b-10" style=" min-width: 59px;">
                        <select name="scheduleOut" class=" schedule <?php if(!$isMobile ): ?>form-control minimal<?php endif; ?>" style="width: 100%;max-width: inherit;padding: 2px;" data-type="out" data-id="<?php echo $book->id ?>" <?php if (getUsrRole() == "limpieza"): ?>disabled<?php endif ?>>
                            <option>---</option>
                            <?php for ($i = 0; $i < 24; $i++): ?>
                                <option value="<?php echo $i ?>" <?php if($i == $book->scheduleOut) { echo 'selected';}?>>
                                    <?php if ($i != 12): ?><b><?php endif ?>
                                    <?php if ($i < 10): ?>
                                        0<?php echo $i ?>
                                    <?php else: ?>
                                        <?php echo $i ?>
                                    <?php endif ?>
                                    <?php if ($i != 12): ?></b><?php endif ?>
                                </option>
                            <?php endfor ?>
                                <option value="24" <?php if(24 == $book->scheduleOut) { echo 'selected';}?>>CHECKOUT</option>
                        </select>
                       <!--  <?php if (isset($payment[$book->id])): ?>
                            <?php echo number_format($book->total_price - $payment[$book->id],2,',','.') ?> €
                        <?php else: ?>
                            <?php echo number_format($book->total_price,2,',','.') ?> €
                        <?php endif ?> -->
                    </td>

                    <?php if (getUsrRole() != "limpieza"): ?>
                    <td class="text-center">
                      <button class="btn  open_modal_encuesta <?php echo in_array($book->id, $pullSent) ? 'btn-warning' : ''; ?>" type="button" data-id="{{$book->id}}" title="Encuesta mail" >
                      </button>
                    </td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
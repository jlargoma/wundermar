<?php 
use \Carbon\Carbon;  setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES");
?>
<?php if (count($books) > 0): ?>
  <div class="table-responsive">
        <table class="table table-data table-striped"  data-type="pendientes" style="margin-top: 0;">
            <thead>
                <tr>  
                  <th class="bg-danger text-center text-white"> Cliente</th>
                  <th class="bg-danger text-center text-white">
                    @if($isMobile) <i class="fa fa-phone"></i> @else Telefono @endif
                  </th>
                  <th class ="bg-danger text-center text-white" style="width: 7%!important">   Pax         </th>
                  <th class ="bg-danger text-center text-white" style="width: 10%!important">   Apart       </th>
                  <th class ="bg-danger text-center text-white" style="width: 6%!important">   IN     </th>
                  <th class ="bg-danger text-center text-white" style="width: 8%!important">   OUT      </th>
                  <th class ="bg-danger text-center text-white" style="width: 6%!important">  <i class="fa fa-moon-o"></i> </th>
                  <th class ="bg-danger text-center text-white" >   Precio      </th>
                  <th class ="bg-danger text-center text-white" style="width: 17%!important">   Estado      </th>
                  <th class ="bg-danger text-center text-white" style="width: 6%!important">&nbsp;</th>
                  <th class ="bg-danger text-center text-white" style="width: 6%!important">A</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <?php $class = ucwords($book->getStatus($book->type_book)) ?>
                    <tr class="<?php echo $class ;?>" data-id="{{$book->id}}" >
                      <td class="text-left fix-col" style="padding: 10px 5px!important" data-filter="site{{$book->room->site_id}}">
                          <div class=" fix-col-data">
                          <?php if ($book->agency != 0): ?>
                              <img style="width: 20px;margin: 0 auto;" src="/pages/<?php echo strtolower($book->getAgency($book->agency)) ?>.png" align="center" />
                          <?php endif ?>
                              <a class="update-book" data-id="<?php echo $book->id ?>" title="<?php echo $book->customer->name ?> - <?php echo $book->customer->email ?>"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>" style="color: red">
                                  <?php echo substr( $book->customer->name , 0, 8)  ?>
                              </a>
                          </div>
                      </td>

                    @if($isMobile)
                    <td>
                      <?php if ($book->customer->phone != 0 && $book->customer->phone != ""): ?>
                        <a href="tel:<?php echo $book->customer->phone ?>">
                          <i class="fa fa-phone"></i>
                        </a>
                      <?php endif ?>
                    @else
                    <td>
                      <?php if ($book->customer->phone != 0 && $book->customer->phone != ""): ?>
                        <a href="tel:<?php echo $book->customer->phone ?>"><?php echo $book->customer->phone ?></a>
                      <?php else: ?>
                        <input type="text" class="only-numbers customer-phone" data-id="<?php echo $book->customer->id ?>"/>
                      <?php endif ?>
                    @endif
                      <?php if ($uRole != "limpieza" && (!empty($book->comment) || !empty($book->book_comments))): ?>
                          <?php 
                              $textComment = "";
                              if (!empty($book->comment)) {
                                  $textComment .= "<b>COMENTARIOS DEL CLIENTE</b>:"."<br>"." ".$book->comment."<br>";
                              }
                              if (!empty($book->book_comments)) {
                                  $textComment .= "<b>COMENTARIOS DE LA RESERVA</b>:"."<br>"." ".$book->book_comments;
                              }
                          ?>
                          <?php if( preg_match( '/Antiguos cobros/i', $book->comment ) ): ?>
                              <span>
                                  <i class="fa fa-dollar-sign" style="color: #000;" aria-hidden="true"></i>
                              </span>
                          <?php else: ?>
                              <span class="icons-comment" data-class-content="content-comment-<?php echo $book->id?>">
                                  <i class="fa fa-comments" style="color: #000;" aria-hidden="true"></i>
                              </span>
                          <?php endif;?>
                          <div class="comment-floating content-comment-<?php echo $book->id?>" style="display: none;"><p class="text-left"><?php echo $textComment ?></p></div>
                      <?php endif ?>
                    </td>

                        <td>
                            <?php if ($book->real_pax > 6 ): ?>
                                <?php echo $book->real_pax ?><i class="fa fa-exclamation" aria-hidden="true" style="color: red"></i>
                            <?php else: ?>
                                <?php echo $book->pax ?>
                            <?php endif ?>
                                
                        </td>

                        <td>
                          <?php 
                          if ($book->room){
                            $room = $book->room;
                            ?>
                          <button type="button" class="btn changeRoom" data-c="{{$room->id}}">
                          <?php echo substr($room->nameRoom . " - " . $room->name, 0, 15);?>
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
                        <td><?php echo $book->nigths ?></td>
                        <td><?php echo round($book->total_price)."€" ?><br>
                        </td>
                        @if($uRole != "agente" )
                        <td>
                          <button type="button" class="btn changeStatus" data-c="{{$book->type_book}}">
                            {{$book->getStatus($book->type_book)}}
                          </button>
                        </td>
                         @endif
                        <td>
                            <?php if (!empty($book->book_owned_comments)): ?>
                                <span class="icons-comment" data-class-content="content-commentOwned-<?php echo $book->id?>">
                                    <img src="/img/oferta.png" style="width: 40px;">
                                </span>
                                <div class="comment-floating content-commentOwned-<?php echo $book->id?>" style="display: none;"><p class="text-left"><?php echo $book->book_owned_comments ?></p></div>
                                
                            <?php endif ?>
                        </td>

                        <td>                                                         
                            <button data-id="<?php echo $book->id ?>" class="btn btn-xs btn-primary restoreBook" type="button" data-toggle="tooltip" title="" data-original-title="Restaurar Reserva" onclick="return confirm('¿Quieres restaurar la reserva?');">
                               <i class="fa fa-undo" aria-hidden="true"></i>
                            </button>                            

                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table> 
   </div>
<?php else: ?>
    <h2 class="text-center font-w300">
        Lo sentimos, no hay reservas <span class="font-w800">ELIMINADAS</span> aún.
    </h2>
<?php endif ?>

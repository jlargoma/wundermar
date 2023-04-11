<?php

use \Carbon\Carbon;
use \App\Classes\Mobile;

$mobile = new Mobile();
$is_mobile = $mobile->isMobile();
$oRole = getUsrRole();
?>
@extends('layouts.admin-master')

@section('title') Liquidacion @endsection

@section('externalScripts')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<style>
  .table thead tr.text-white th{
    color: #fff !important;
  }
  .fix-col-data{
    min-width: 150px;
  }
  .table.table-data tbody td{
    text-align: center;
  }
  .td-b1{
    padding: 10px 5px!important;
    text-align: left !important;
  }
  .tooltiptext {
    position: absolute;
    font-size: 11px;
    color: #fff;
    background-color: rgba(0, 0, 0, 0.42);
    padding: 2px 5px;
    width: 10em;
    border-radius: 7px;
  }
  .showBookComm{
    cursor: pointer;
    padding: 6px;
  }
  .tooltiptext.FF_resume,
  .BookComm.tooltiptext {
    padding: 11px;
    background-color: #333;
    font-size: 1em;
    text-align: left;
    z-index: 9;
    width: 22em;
    display: none;
  }
  .BookComm.tooltiptext {
    color: #fff !important;
  }
  .tooltiptext.FF_resume {
    color: inherit !important;
    right: 26px;
    bottom: -70px;
  }
  a.openFF.showFF_resume {
      position: relative;
  }

  .showFF_resume:hover .tooltiptext.FF_resume,
  .showBookComm:hover .tooltiptext.BookComm{
    display: block;
  }

  .tooltiptext.FF_resume .table tbody tr td{
    padding: 8px !important;
    font-size: 1.3em;
    font-weight: 600;
  }
  .tooltiptext.FF_resume span.Pendiente {
    color: red;
  }
  
 
  @media only screen and (max-width: 991px){
    .t-ff{
      float: none;
    }
    .t-ff td,
    .t-ff th{
      height: 6em;
    }
    .t-ff a{
      white-space: initial !important;
    }
  }
</style>
@endsection

@section('content')

<div class="container-fluid padding-25 sm-padding-10 table-responsive">
    <div class="row text-center">
      <h2 class="col-md-6 text-right">Liquidación Forfaits
        </h2>
      <div class="col-md-2 text-left" style="padding: 10px">
        @include('backend.years._selector')
      </div>
    </div>
  <div class="row">
    <div class="col-md-12 text-center">
      <div class="btn-contabilidad">
        <?php if (Request::path() == 'admin/forfaits/orders'): ?>
          <button class="btn btn-md text-white active"  disabled>Control FF</button>
        <?php else: ?>
          <a class="text-white btn btn-md btn-primary" href="{{url('/admin/forfaits/orders')}}">Control FF</a>
<?php endif ?>	
      </div>

      <div class="btn-contabilidad">
        <?php if (Request::path() == 'admin/forfaits'): ?>
          <button class="btn btn-md text-white active"  disabled>Items FF</button>
        <?php else: ?>
          <a class="text-white btn btn-md btn-primary" href="{{url('/admin/forfaits')}}">Items FF</a>
<?php endif ?>	
      </div>
    </div>
  </div>

  
  
  
<div class="clearfix"></div>
  <div class="row">
    <div class="col-md-3 col-xs-8">
      <table class="table table-hover table-striped table-ingresos" style="background-color: #92B6E2">
        <thead class="bg-complete" style="background: #d3e8f7">
        <th colspan="2" class="text-black text-center"> Ingresos Temporada</th>
        </thead>
        <tbody>
          <tr>
            <td class="" style="padding: 5px 8px!important; background-color: #d3e8f7!important;"><b>VENTAS TEMPORADA</b></td>
            <td class=" text-center" style="padding: 5px 8px!important; background-color: #d3e8f7!important;">
              <b><?php echo number_format(round($totals['totalSale']), 0, ',', '.') ?> €</b>
            </td>
          </tr>
          <tr style="background-color: #38C8A7;">
            <td class="text-white" style="padding: 5px 8px!important;background-color: #38C8A7!important;">
              Cobrado Temporada
            </td>
            <td class="text-white text-center" style="padding: 5px 8px!important;background-color: #38C8A7!important;">
              <?php echo number_format(round($totals['totalPayment']), 0, ',', '.') ?> € 
            </td>
          </tr>
          <tr style="background-color: #ef6464;">
            <td class="text-white" style="padding: 5px 8px!important;background-color: #ef6464!important;">Pendiente Cobro</td>
            <td class="text-white text-center" style="padding: 5px 8px!important;background-color: #ef6464!important;">
              <?php echo number_format(round($totals['totalToPay']), 0, ',', '.') ?> €
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-2 col-xs-4">
      <canvas id="pieIng" style="width: 100%; height: 250px;"></canvas>
    </div>
    <div class="col-md-4 col-xs-7">
       <div class="row bg-white push-30">
        <div class="col-md-6 bordered text-center">
          <h4 class="hint-text">Cobrado Temporada</h4>
          <h3 ><?php echo number_format(round($totals['totalPayment']), 0, ',', '.') ?> €</h3>
        </div>
        <div class="col-md-6 bordered text-center">
          <h4 class="hint-text bold">Vendido Temporada</h4>
            <h3 ><?php echo number_format(round($totals['totalSale']), 0, ',', '.') ?> €</h3>
        </div>
        <div class="col-md-6 bordered text-center">
          <h4 class="hint-text">Total de Ordenes</h4>
          <div class="p-l-20">
            <h3 ><?php echo $totalOrders; ?></h3>
          </div>
        </div>
        <div class="col-md-6 bordered text-center">
          <h4 class="hint-text">Promedio por Orden</h4>
            <h3 >
              <?php 
              $promedio = 0;
              if ($totals['orders']>0){
                $promedio = round($totals['totalPrice'])/$totals['orders'];
              }
              echo number_format(round($promedio), 0, ',', '.')
              ?>
              €</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-xs-5 text-center">
      <div class="bordered bg-white p-8 ">
        <h4 class="hint-text bold black">Wallet de Forfait Express</h4>
        <h3 class="<?php if($ff_mount<100) echo 'text-danger';?>"><?php echo number_format($ff_mount, 0, ',', '.')?>€</h3>
      </div>
      <div class="bordered bg-white p-8 ">
        <h4 class="hint-text bold black">% FORFAITS/RVAS CHECK IN</h4>
        <h3>{{$ff_checkin}}%</h3>
      </div>
    </div>
</div>
  
  
  <div class="clearfix"></div>
  <div class="row">
    <div class="col-md-9 col-xs-12  t-ff">
      <div class="table-responsive">
      <table class="table table-data-ff">
        <thead>
          
          <tr class ="text-center bg-complete text-white">
            <th class="th-bookings th-name">Cliente</th>
            <th class="th-bookings">
              @if($is_mobile)
                <i class="fa fa-phone fa-2x"></i>
              @else                                  
                Telefono
              @endif
            </th>
            <th class="th-bookings th-2">Pax</th>
            <th class="th-bookings">Apart</th>
            <th class="th-bookings th-2"><i class="fa fa-moon-o"></i> </th>
            <th class="th-bookings th-2"><i class="fa fa-clock-o"></i></th>
            <th class="th-bookings th-4">IN</th>
            <th class="th-bookings th-4">OUT</th>
            <th class="th-bookings th-6">
              TOTAL<br/><?php echo number_format($totals['totalPrice'], 0, ',', '.') ?> €</th>
            <th class="th-bookings th-6">
              FF<br/><?php echo number_format($totals['forfaits'], 0, ',', '.') ?> €</th>
            <th class="th-bookings th-6">
              ALQ<br/><?php echo number_format($totals['material'], 0, ',', '.') ?> €</th>
            <th class="th-bookings th-6">
              CLASS<br/><?php echo number_format($totals['class'], 0, ',', '.') ?> €</th>
            <th class="th-bookings th-6">
              OTROS<br/><?php echo number_format($totals['quick_order'], 0, ',', '.') ?> €</th>
            <th class="th-bookings th-6">
              COBRADO<br/><?php echo number_format($totals['totalPayment'], 0, ',', '.') ?> €</th>
            <th class="th-bookings th-2">FF</th>
            <th class="th-bookings th-2" title="Reservas hechas en Forfait Express">FFExpress</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($orders as $order):
            $book = $order['book'];
          ?>
          <tr>
            <?php if ($book): ?>
            <td class="fix-col td-b1">
              <div class="fix-col-data">
                <?php if ( $book->agency != 0): ?>
              <img src="/pages/<?php echo strtolower($book->getAgency($book->agency)) ?>.png" class="img-agency" />
            <?php endif ?>
              <div class="th-name">
                                <?php if (isset($payment[$book->id])): ?>
                                    <a class="update-book" data-id="<?php echo $book->id ?>"  title="<?php echo $book->customer['name'] ?> - <?php echo $book->customer['email'] ?>"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>" style="color: red"><?php echo $book->customer['name']  ?></a>
                                <?php else: ?>
                                    <a class="update-book" data-id="<?php echo $book->id ?>"  title="<?php echo $book->customer['name'] ?> - <?php echo $book->customer['email'] ?>"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>" ><?php echo $book->customer['name']  ?></a>
                                <?php endif ?>
                               
              </div>
                           </div>
                            </td>
                            <td class="text-center">
                              <?php if ($book->customer->phone != 0 && $book->customer->phone != "" ): ?>
                                <a href="tel:<?php echo $book->customer->phone ?>">
                                @if($is_mobile)
                                  <i class="fa fa-phone fa-2x"></i>
                                @else
                                      <?php echo $book->customer->phone ?>
                                @endif
                                </a>
                              <?php endif ?>
                              <?php if ($oRole != "limpieza" && ( !empty($book->comment) || !empty($book->book_comments)) ): ?>
                                  <div data-booking="<?php echo $book->id; ?>" class="showBookComm" >
                                    <i class="fa fa-commenting" style="color: #000;" aria-hidden="true"></i>
                                    <div class="BookComm tooltiptext"></div>
                                  </div>
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
                              <?php foreach ($rooms as $room): ?>
                                  <?php if ($room->id == $book->room_id): ?>
                                         <?php echo substr($room->nameRoom." - ".$room->name, 0, 15)  ?>
                                  <?php endif ?>
                              <?php endforeach ?>
                            </td>
                            <td class ="text-center"><?php echo $book->nigths ?></td>
                            <td class="text-center sm-p-t-10 sm-p-b-10">
                                {{$book->schedule}}
                            </td>
                           <td class="td-date" data-order="{{$book->start}}">
                              <?php echo dateMin($book->start) ?>
                            </td>
                            <td class="td-date" data-order="{{$book->finish}}">
                              <?php echo dateMin($book->finish) ?>
                            </td>
            <?php else: ?>
                              <td class="fix-col td-b1">
                                <div class="fix-col-data">
                                
                                <?php echo $order['name'].'<br>'.$order['email']; ?>
                              </div></td>
                            <td class="text-center">
                              <?php if ( $order['phone'] != 0 &&  $order['phone'] != "" ): ?>
                                <a href="tel:<?php echo $order['phone']?>">
                                @if($is_mobile)
                                <i class="fa fa-phone fa-2x"></i>
                                @else
                                      <?php echo $order['phone']?>
                                @endif
                                </a>
                              <?php endif ?>
                            </td>
                            <td class="text-center"> - </td>
                            <td class="text-center">
                              <button type="button" class="btn btn-default changeBook" data-id="<?php echo $order['id']; ?>">
                                Asignar Reserva
                              </button>
                            </td>
                            <td class="text-center"> - </td>
                            <td class="text-center"> - </td>
                            <td class="text-center"> - </td>
                            <td class="text-center"> - </td>
            <?php endif ?>
                         
            <td class="text-center"><b><?php echo number_format($order['totalPrice'], 0, ',', '.') ?> €</b></td>
            <td class="text-center"><?php echo number_format($order['forfaits'], 0, ',', '.') ?> €</td>
            <td class="text-center"><?php echo number_format($order['material'], 0, ',', '.') ?> €</td>
            <td class="text-center"><?php echo number_format($order['class'], 0, ',', '.') ?> €</td>
            <td class="text-center"><?php echo number_format($order['quick_order'], 0, ',', '.') ?> €</td>
            <td class="text-center">
              <div class="col-md-6">
                <?php echo number_format($order['totalPayment'], 0, ',', '.') ?> €
                 <?php 
                  $porcent = 0;
                  $color = 'style="color: red;"';
                  if ($order['totalPrice']>0){
                    if ($order['totalPayment']>0){
                      $porcent = ceil(($order['totalPayment']/$order['totalPrice'])*100);
                      if ($porcent>=100){
                        $color = 'style="color: #008000;"';
                        $porcent = 100;
                      }
                    }
                  } else {
                    $color = 'style="color: #008000;"';
                    $porcent = 100;
                  }
              
//               text-danger
              ?>
                <p <?php echo $color; ?>><?php echo number_format($order['totalToPay'], 0, ',', '.');  ?>€</p>
              </div>
             
              <div class="col-md-6"><span <?php echo $color; ?>><?php echo $porcent; ?>%</span></div>
              
            </td>
            <td class="text-center">
             <a data-booking="<?php echo $order['id']; ?>" class="openFF showFF_resume" title="Ir a Forfaits" >
              <?php
                $ff_status = $order['status'];
                
                  if ($ff_status['icon']) {
                    echo '<img src="' . $ff_status['icon'] . '" style="max-width:30px;" alt="' . $ff_status['name'] . '"/>';
                  } else {
                     echo '<img src="/img/miramarski/ski_icon_status_transparent.png" style="max-width:30px;" alt="Externo"/>';
                  }
                
              ?>
                <div class="FF_resume tooltiptext"></div>
              </a>
              
            </td>  
            <td class="text-center">
                <?php echo $order['ff_sent'].'/'.$order['ff_item_total']; ?>
            </td>
          </tr>
          <?php
          endforeach;
          ?>
        </tbody>
      </table>
        </div>
    </div>
    <div class="col-md-3" >
       <canvas id="barChartMounth" style="width: 100%; height: 250px;"></canvas>
       <?php 
        $t_forfaits = $t_equipos = $t_clases = $t_otros = 0;
       ?>
       
       <div class="row table-responsive">
         <table class="table table-resumen">
           <thead>
             <tr class="resume-head">
               <th class="static">Concepto</th>
               <th class="first-col">Total</th>
               @foreach($months_obj as $item)
               <th>{{$item['name']}} {{$item['year']}}</th>
                <?php 
                  $t_forfaits += $item['data']['forfaits'];
                  $t_equipos  += $item['data']['equipos'];
                  $t_clases   += $item['data']['clases'];
                  $t_otros    += $item['data']['otros'];
                ?>
               @endforeach
             </tr>
          </thead>
          <tbody>
             <tr>
               <td class="static">Forfaits</td>
               <td class="first-col"><?php echo number_format($t_forfaits, 0, ',', '.') ?> €</td>
               @foreach($months_obj as $item)
               <td><?php echo number_format($item['data']['forfaits'], 0, ',', '.'); ?>€</td>
               @endforeach
             </tr>
             <tr>
               <td class="static">Materiales</td>
               <td class="first-col"><?php echo number_format($t_equipos, 0, ',', '.') ?> €</td>
               @foreach($months_obj as $item)
               <td><?php echo number_format($item['data']['equipos'], 0, ',', '.'); ?>€</td>
               @endforeach
             </tr>
             <tr>
               <td class="static">Clases</td>
               <td class="first-col"><?php echo number_format($t_clases, 0, ',', '.') ?> €</td>
               @foreach($months_obj as $item)
               <td><?php echo number_format($item['data']['clases'], 0, ',', '.'); ?>€</td>
               @endforeach
             </tr>
             <tr>
               <td class="static">Otros</td>
               <td class="first-col"><?php echo number_format($t_otros, 0, ',', '.') ?> €</td>
               @foreach($months_obj as $item)
               <td><?php echo number_format($item['data']['otros'], 0, ',', '.'); ?>€</td>
               @endforeach
             </tr>
            </tbody>
         </table>
       </div>
       
       
       <div class="box-errors" style="display:none;">
         <h3 class="text-danger">Errores Forfaits</h3>
        <?php 
        if($errors):
          foreach ($errors as $er): 
        ?>
         <div class="item-error row">
           <div class="col-md-8">
              <?php echo $er->detail; ?><br/>
              <small><?php echo date('d M H:i', strtotime($er->created_at)); ?></small>
           </div>
           <div class="col-md-4">
             <a data-booking="<?php echo $er->book_id; ?>" class="openFF text-danger" title="Ir a Forfaits" >
              Ver Forfait
              </a>
           </div>
           
         </div>
        <?php 
          endforeach; 
        endif;
        ?>
       </div>
    </div>
  </div>
</div>
<form method="post" id="formFF" action="" target="_blank">
              <input type="hidden" name="admin_ff" id="admin_ff">
            </form>
<div class="modal fade" id="modalChangeBook" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <strong class="modal-title" style="font-size: 1.4em;">Asociar Reserva</strong>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="first">
          <label>ID de la reserva:</label>
          <input type="number" id="changeBook_bookID" value="" class="form-control">
          <div style="width:100%; margin: 1em auto; text-align: center">
          <button type="button" class="btn btn-success" id="changeBook_find">Buscar</button>
          </div>
        </div>
        <div id="seccond" style="display:none; text-align: center">
          <h5>Asignar la reserva de: 
          <div id="changeBook_bookTit"></div>
          </h5>
          <button type="button" class="btn btn-success" id="changeBook_send">Asignar</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function () {
      $('.openFF').on('click', function (event) {
        event.preventDefault();
        var id = $(this).data('booking');
        $.post('/admin/forfaits/open', { _token: "{{ csrf_token() }}",order_id:id }, function(data) {
          var formFF = $('#formFF');
          formFF.attr('action', data.link);
          formFF.find('#admin_ff').val(data.admin);
          formFF.submit();

        });
      });
      
      new Chart(document.getElementById("pieIng"), {
        type: 'pie',
        data: {
          labels: ["Cobrado", "Pendiente", ],
          datasets: [{
              label: "Population (millions)",
              backgroundColor: ["#38C8A7", "#ef6464"],
              data: [
                //Comprobamos si existen cobros
              <?php echo round($totals['totalPayment']) ?>,
              <?php echo round($totals['totalToPay']) ?>,
              ]
            }]
        },
        options: {
          title: {
            display: false,
            text: 'Ingresos de la temporada'
          }
        }
      });
      
      new Chart(document.getElementById("barChartMounth"), {
        type: 'line',
                data: {
                labels: [{!!$months_label!!}],
                        datasets: [
                        {
                        data: [{!!$monthValue!!}],
                                label: 'Pagadas',
                                borderColor: "rgba(104, 255, 0, 1)",
                                fill: false
                        },
                     
                        ]
                },
                options: {
                title: {
                display: false,
                        text: ''
                }
                }
        });
        
      var FFID = null;
      $('body').on('click','.changeBook',function(){
          FFID = $(this).data('id');
          $('#first').show();
          $('#seccond').hide();
          $('#modalChangeBook').modal('show'); 
      });
      $('#changeBook_find').on('click',function(event) {
        var bID = $('#changeBook_bookID').val();
        $('#first').hide();
        $('#seccond').show();
        $.get('/admin/forfaits/getBookData/'+bID, function(data) {
          console.log(data);
          $('#changeBook_bookTit').html(data);
        });
      });
      // Cambiamos las reservas
	$('#changeBook_send').on('click',function(event) {
	    var bID = $('#changeBook_bookID').val();
            
            $.get('/admin/forfaits/changeBook/'+bID+'/'+FFID,function(data) {
              $('#modalChangeBook').modal('hide'); 
              if (data.status == 'danger') {
                window.show_notif(data.title,data.status,data.response);
              } else {
                window.show_notif(data.title,data.status,data.response);
              }
              
              setTimeout(function(){location.reload();},3000);
                    
	           
	       }); 
	});
        
        
        
        $('.table-data-ff').dataTable({
          "searching": true,
          "paging":   false,
          "order": [[ 6, "desc" ]],
          "columnDefs": [
            {"targets": ['1,2,11,12,13'], "orderable": false }
          ],
          @if($is_mobile)
            paging:  true,
            pageLength: 30,
            pagingType: "full_numbers",
            scrollX: true,
            scrollY: false,
            scrollCollapse: true,
            fixedColumns:   {
              leftColumns: 1
            },
          @endif

        });

      var load_comment = true;
      $('body').on('mouseover','.showBookComm',function(){
        var id = $(this).data('booking');
          if (load_comment != id){
            var tooltip = $(this).find('.BookComm');
            tooltip.load('/ajax/get-book-comm/'+id);
            load_comment = id;
          }
      });
      
      var loadFF_resume = true;
      $('body').on('mouseover','.showFF_resume',function(){
        var id = $(this).data('booking');
          if (loadFF_resume != id){
            var tooltip = $(this).find('.FF_resume');
            tooltip.load('/admin/forfaits/resume/'+id);
            loadFF_resume = id;
          }
      });
      
    });
  </script>
@endsection
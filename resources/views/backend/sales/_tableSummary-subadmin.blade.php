<?php
    use \Carbon\Carbon;
    use \App\Classes\Mobile;
    setlocale(LC_TIME, "ES");
    setlocale(LC_TIME, "es_ES");
    $mobile = new Mobile();
?>
<?php if ( !$mobile->isMobile() ): ?>
    <style type="text/css">
        @media screen and (min-width: 998px){
            .summary-text{ width: 22%; }
        }
    </style>
<?php endif;?>
    <div class="col-md-12 pull-right push-20">
        <div class="col-md-11 col-xs-12">
            <h2 class="text-left">
                Indicadores de ocupación
            </h2>
        </div>
        <div class="col-md-1 col-xs-12 pull-right">
            <label style="width: 100%; float:left;">Benef critico</label>
            <input class="form-control text-black font-w400 text-center percentBenef" value="<?php echo $percentBenef ?>" style="border: none; font-size: 32px;margin: 10px 0;color:red!important; width: 70%; float:left;"/> <span class="font-w800" style="font-size: 32px">%</span>
        </div>
        <div style="clear: both;"></div>
        @include('backend.sales._tableSummaryBoxes-subadmin', ['totales' => $totales, 'books' => $books, 'data' => $data, 'year'=> $year])
    </div>
    <div class="col-xs-12">
        <div class="row push-10">
           <h2 class="text-left font-w800">
              Resumen liquidación
          </h2>
        </div>
        <div class="col-md-12 col-xs-12 table-responsive" style="padding-right: 0;">
            <table class="table table-striped" id="tableOrderable">
                <thead >
                    <th class ="text-center bg-complete text-white sorting_disabled" style="width: 7% !important; font-size:
                    10px!important">Nombre</th>
                    <th class ="text-center bg-complete text-white" style="width: 3% !important;font-size:10px!important">&nbsp;&nbsp;&nbsp;Tipo&nbsp;&nbsp;&nbsp;</th>
                    <th class ="text-center bg-complete text-white" style="width: 2% !important;font-size:10px!important">Pax</th>
                    <th class ="text-center bg-complete text-white" style="width: 1% !important;font-size:10px!important">Apto</th>
                    <th class ="text-center bg-complete text-white" style="width: 10% !important;font-size:10px!important">IN - OUT</th>
                    <th class ="text-center bg-complete text-white" style="width: 2% !important;font-size:10px!important"><i class="fa fa-moon"></i></th>
                    <th class ="text-center bg-complete text-white" style="width: 5% !important;font-size:10px!important">
                      Ventas <br/><b><?php echo number_format($totales["total"],0,',','.') ?> €</b>
                    </th>
                    <th class ="text-center bg-complete text-white" style="width: 5% !important;font-size:10px!important">
                      BANCO<br/>
                      <b>
                        <?php $aux = $totales["bancoJorge"] + $totales["bancoJaime"]; ?>
			<?php if ($aux == 0): ?>
                        ----
                        <?php else: ?>
                        <?php echo number_format($aux,0,',','.') ?> €
                        <?php endif ?>
                      </b>
                    </th>
                    <th class ="text-center bg-complete text-white" style="width: 5% !important;font-size:10px!important">CAJA<br/>
                      <b>
                        <?php $aux = $totales["jorge"] + $totales["jaime"]; ?>
			<?php if ($aux == 0): ?>
                        ----
                        <?php else: ?>
                        <?php echo number_format($aux,0,',','.') ?> €
                        <?php endif ?>
                      </b>
                    </th>
                    <th class ="text-center bg-complete text-white" style="width: 5% !important;font-size:10px!important">
                      Pend<br/><b>{{ $totales['pendiente'] ? number_format($totales["pendiente"],0,',','.') . ' €' : '----' }}</b>
                    </th>
                </thead>
                <tbody >
                    <!-- Totales -->

                    <?php foreach ($books as $book): ?>
                        <tr >
                            <td class="text-center">
                                <span style="display: none;"><?php echo strtotime($book->start);?></span>
                                <div class="col-xs-2">
                                    <?php if ($book->agency != 0): ?>
                                        <img style="width: 20px;margin: 0 auto;position: absolute; left: 0px;" src="/pages/<?php  echo strtolower($book->getAgency($book->agency)) ?>.png" align="center" />
	                               <?php endif ?>

                                </div>
                                <div class="col-xs-8">
                                    <a class="update-book" data-id="<?php echo $book->id ?>"  title="Editar Reserva"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>">
                                        <?php  echo $book->customer->name ?>
                                    </a>
                                </div>
                                <div class="col-xs-2">
                                    <?php if (!empty($book->book_owned_comments) && $book->promociones != 0 ): ?>
                                        <img src="/pages/oferta.png" style="width: 40px;" title="<?php echo $book->book_owned_comments ?>">
                                    <?php endif ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <!-- type -->
                                <b>
                                <?php
                                    switch ($book->type_book){
                                        case 2:
                                        	echo "C";
                                        	break;
	                                    case 7:
                                            echo "P";
                                            break;
	                                    case 8:
		                                    echo "A";
		                                    break;
                                    }
                                ?>
                                </b>
                            </td>
                            <td class="text-center">
                                <!-- pax -->

                                <?php echo $book->pax ?>
                            </td>
                            <td class="text-center">
                                <!-- apto -->

                                <?php echo $book->room->nameRoom ?>
                            </td>
                            <td class="text-center">
                                <?php
                                    $start = Carbon::createFromFormat('Y-m-d',$book->start);
                                    echo $start->formatLocalized('%d %b');
                                ?> -
                                <?php
                                    $finish = Carbon::createFromFormat('Y-m-d',$book->finish);
                                    echo $finish->formatLocalized('%d %b');
                                ?>
                            </td>
                            <td class="text-center">
                                <?php echo $book->nigths ?>
                            </td>
                            <td class="text-center coste" style="border-left: 1px solid black;">
                                <input class="updatePVP" type="number" step="0.01" value="<?php echo round($book->total_price);?>" data-idBook="<?php echo $book->id; ?>"/>
                            </td>
                            <td class="text-center coste banco" style="border-left: 1px solid black;">
                              <?php $aux = $book->getPayment(2) + $book->getPayment(3); ?>
                              <?php echo ($aux == 0)? '---' : number_format($aux,0,',','.').' €'; ?>
                            </td>
                            <td class="text-center coste caja" style="border-left: 1px solid black;">
                              <?php $aux = $book->getPayment(0) + $book->getPayment(1); ?>
                              <?php echo ($aux == 0)? '---' : number_format($aux,0,',','.').' €'; ?>
                            </td>
                            <td class="text-center coste pagos pendiente red <?php if($book->pending > 0){ echo
                            'alert-limp'; }?>" style="border-left: 1px solid black;" >

                                {{ $book->pending > 0 ? number_format( $book->pending,0,',','.') . ' €' : '----' }}

                            </td>
                        </tr>
                    <?php endforeach ?>

                </tbody>
            </table>
        </div>
    </div>
<script src="/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript"></script>
<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/lodash.min.js"></script>
<script type="text/javascript" src="/assets/js/datatables.js"></script>
<script src="/assets/js/scripts.js" type="text/javascript"></script>
<script>

	$('#tableOrderable').dataTable({
	"searching": false,
	"ordering": true,
	"paging":   false,
//	"columnDefs": [
//	                {
//	                    "targets": [0,2,3,4,5,6,7,8,9,10,11,12,14,15,16,17,18,19,20,21,22,23], // column or
//	                  // columns numbers
//	                    "orderable": false,  // set orderable for selected columns
//	                }
//	            ],

	});

  	$('.updateLimp').change(function(){
		var id = $(this).attr('data-idBook');
		var limp = $(this).val();
		$.get( "/admin/sales/updateLimpBook/"+id+"/"+limp).done(function( data ) {

		});
 	});

  	$('.updateExtraCost').change(function(){
    	var id = $(this).attr('data-idBook');
    	var extraCost = $(this).val();
    	$.get( "/admin/sales/updateExtraCost/"+id+"/"+extraCost).done(function( data ) {console.log(data)});
    });


	$('.updateCostApto').change(function(){
    	var id = $(this).attr('data-idBook');
    	var costApto = $(this).val();
    	$.get( "/admin/sales/updateCostApto/"+id+"/"+costApto).done(function( data ) {console.log(data)});

    });

	$('.updateCostPark').change(function(){
    	var id = $(this).attr('data-idBook');
    	var costPark = $(this).val();
    	$.get( "/admin/sales/updateCostPark/"+id+"/"+costPark).done(function( data ) {console.log(data)});

    });

    $('.updateCostTotal').change(function(){
    	var id = $(this).attr('data-idBook');
    	var costTotal = $(this).val();
    	$.get( "/admin/sales/updateCostTotal/"+id+"/"+costTotal).done(function( data ) {console.log(data)});

    });
    $('.updatePVP').change(function(){
      var id = $(this).attr('data-idBook');
      var pvp = $(this).val();
      $.get( "/admin/sales/updatePVP/"+id+"/"+pvp).done(function( data ) {console.log(data)});

    });

</script>
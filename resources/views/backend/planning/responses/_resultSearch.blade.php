<?php   use \Carbon\Carbon;  
        setlocale(LC_TIME, "ES"); 
        setlocale(LC_TIME, "es_ES");  
        $mobile = new \App\Classes\Mobile();
?>

<?php if (!$mobile): ?>
	<table class="table  table-condensed table-striped" style="margin-top: 0;">
		<thead>
			<tr>  
				<th class ="text-center bg-complete text-white" style="width: 4%!important">&nbsp;</th> 
				<th class ="text-center bg-complete text-white" >   
					Cliente     
				</th>
				<th class ="text-center bg-complete text-white" >   
					Telefono     
				</th>
				

				<th class ="text-center bg-complete text-white" style="width: 7%!important">
					Pax         
				</th>
				<th class ="text-center bg-complete text-white" style="width: 10%!important">
					Apart       
				</th>
				<th class ="text-center bg-complete text-white" style="width: 6%!important">
					IN     
				</th>
				<th class ="text-center bg-complete text-white" style="width: 8%!important">
					OUT      
				</th>
				<th class ="text-center bg-complete text-white" style="width: 6%!important">
					<i class="fa fa-moon-o"></i> 
				</th>
				<th class ="text-center bg-complete text-white" >
					Precio      
				</th>
				<th class ="text-center bg-complete text-white" style="width: 17%!important">
					Estado      
				</th>
				<th class ="text-center bg-complete text-white" style="width: 6%!important"> 
					A
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($books as $book): ?>
				
				<tr> 
					<td class="text-center">
					    <?php if ($book->agency != 0): ?>
					        <img style="width: 20px;margin: 0 auto;" src="/pages/booking.png" align="center" />
					    <?php endif ?>
					</td>
					<td class ="text-center"  style="padding: 10px 15px!important">
						<a class="update-book" data-id="<?php echo $book->id ?>"  title="<?php echo $book->customer->name ?> - <?php echo $book->customer->email ?>"  href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>" style="color: black; text-decoration: underline;">
							<?php echo $book->customer['name']  ?>
									
						</a>                   
					</td>
					<td class ="text-center"> 
                        <?php if ($book->customer->phone != 0 && $book->customer->phone != "" ): ?>
                            <a href="tel:<?php echo $book->customer->phone ?>"><?php echo $book->customer->phone ?>
                        <?php else: ?>
                            <input type="text" class="only-numbers customer-phone" data-id="<?php echo $book->customer->id ?>"/>
                        <?php endif ?>
					</td>


					<td class ="text-center" >
						<?php if ($book->real_pax > 6 ): ?>
							<?php echo $book->real_pax ?><i class="fa fa-exclamation" aria-hidden="true" style="color: red"></i>
						<?php else: ?>
							<?php echo $book->pax ?>
						<?php endif ?>

					</td>

					<td class ="text-center" >
						<select class="room form-control minimal" data-id="<?php echo $book->id ?>"  >
						
						    <?php foreach (\App\Rooms::where('state', 1)->get() as $room): ?>
						        <?php if ($room->id == $book->room_id): ?>
						            <option selected value="<?php echo $book->room_id ?>" data-id="<?php echo $room->name ?>">
						               <?php echo substr($room->nameRoom." - ".$room->name, 0, 8)  ?>
						            </option>
						        <?php else:?>
						            <option value="<?php echo $room->id ?>"><?php echo substr($room->nameRoom." - ".$room->name, 0, 8)  ?></option>
						        <?php endif ?>
						    <?php endforeach ?>

						</select>
					</td>

					<td class ="text-center"  style="width: 20%!important">
						<?php
						$start = Carbon::createFromFormat('Y-m-d',$book->start);
						echo $start->formatLocalized('%d %b');
						?>
					</td>

					<td class ="text-center"  style="width: 20%!important">
						<?php
						$finish = Carbon::createFromFormat('Y-m-d',$book->finish);
						echo $finish->formatLocalized('%d %b');
						?>
					</td>

					<td class ="text-center" ><?php echo $book->nigths ?></td>

					<td class ="text-center font-w800" >
						<div class="col-md-6 col-xs-12 not-padding">
                            <?php echo round($book->total_price)."€" ?><br>
                            <?php if (isset($payment[$book->id])): ?>
                                <?php echo "<p style='color:red'>".$payment[$book->id]."€</p>" ?>
                            <?php else: ?>
                            <?php endif ?>
                        </div>

                        <?php if (isset($payment[$book->id])): ?>
                            <?php if ($payment[$book->id] == 0): ?>
                                <div class="col-md-5 col-xs-12 not-padding bg-success">
                                	<b style="color: red;font-weight: bold">0%</b>
                                </div>
                            <?php else:?>
                                <div class="col-md-5  col-xs-12 not-padding">
                                    <p class="text-white m-t-10"><b style="color: red;font-weight: bold"><?php echo number_format(100/($book->total_price/$payment[$book->id]),0).'%' ?></b></p>
                                </div> 
                                                                                           
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="col-md-5 col-xs-12 not-padding bg-success">
                                <b style="color: red;font-weight: bold">0%</b>
                            </div>
                        <?php endif ?>

					</td>

					<td class ="text-center">
						<!-- 1,3,4,5,6 -->
						<?php if ($book->type_book == 1 || $book->type_book == 3 || $book->type_book == 4 || $book->type_book == 5 || $book->type_book == 6 ): ?>
							<b>PENDIENTE</b>
						<?php elseif($book->type_book == 2 ): ?>
							<b>PAGADA</b>
						<?php elseif($book->type_book == 7  ): ?>
							<b>RESERV. PROPIETARIO</b>
						<?php elseif($book->type_book == 8 ): ?>
							<b>SUBCOMUNIDAD</b>
						<?php endif ?>
						

					</td>
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
                        
                    </td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>  
<?php else: ?>
	<div class="table-responsive" style="border: none!important">
	    <table class="table table-striped" style="margin-bottom: 10px; margin-top:0;">
	        <thead>
	            <th class="bg-complete text-white text-center" ></th>
	            <th class="bg-complete text-white text-center" >Nombre</th>
	            <th class="bg-complete text-white text-center">Tel</th>
	            <th class="bg-complete text-white text-center">Pax</th>
	            <th class="bg-complete text-white text-center" style="min-width:50px ">&nbsp;Apto&nbsp;</th>
	            <th class="bg-complete text-white text-center" style="min-width:50px">&nbsp;In</th>
	            <th class="bg-complete text-white text-center" style="min-width:50px ">&nbsp;Out</th>
	            <th class="bg-complete text-white text-center"><i class="fa fa-moon-o"></i></th>
	            <th class="bg-complete text-white text-center" style="min-width:65px">PVP</th>
	            <th class="bg-complete text-white text-center" style="min-width:60px">EST</th>
	            <th class="bg-complete text-white text-center" style="min-width:60px">a</th>
	        </thead>
	        <tbody>
	            <?php $count = 0 ?>
	            <?php foreach ($books as $book): ?>

	                <tr class="">
	                    
	                    <td class="text-center">
	                        <?php if ($book->agency != 0): ?>
	                            <img style="width: 15px;margin: 0 auto;" src="/pages/booking.png" align="center" />
	                        <?php endif ?>
	                    </td>
	                    <td class="text-left">
	                        <a href="{{url ('/admin/reservas/update')}}/<?php echo $book->id ?>">
	                            <?php echo str_pad(substr($book->customer->name, 0, 10), 10, " ")  ?> 
	                        </a>
	                    </td>
	                    <td class="text-center">
	                        <a href="tel:<?php echo $book->customer->phone ?>"><i class="fa fa-phone"></i></a>
	                    </td>
	                    <td class ="text-center" >
	                        <?php if ($book->real_pax > 6 ): ?>
	                            <?php echo $book->real_pax ?><i class="fa fa-exclamation" aria-hidden="true" style="color: red"></i>
	                        <?php elseif($book->pax != $book->real_pax): ?>
	                            <?php echo $book->real_pax ?><i class="fa fa-exclamation-circle" aria-hidden="true" style="color: red"></i>
	                        <?php else: ?>
	                            <?php echo $book->pax ?>
	                        <?php endif ?>
	                            
	                    </td>
	                    <td class="text-center sm-p-t-10 sm-p-b-10">
	                        <select class="room form-control minimal" data-id="<?php echo $book->id ?>"  >
	                            
	                            <?php foreach (\App\Rooms::where('state', 1)->get() as $room): ?>
	                                <?php if ($room->id == $book->room_id): ?>
	                                    <option selected value="<?php echo $book->room_id ?>" data-id="<?php echo $room->name ?>">
	                                       <?php echo substr($room->nameRoom." - ".$room->name, 0, 8)  ?>
	                                    </option>
	                                <?php else:?>
	                                    <option value="<?php echo $room->id ?>"><?php echo substr($room->nameRoom." - ".$room->name, 0, 8)  ?></option>
	                                <?php endif ?>
	                            <?php endforeach ?>

	                        </select>
	                    </td>
	                    <td class="text-center"><?php echo Carbon::CreateFromFormat('Y-m-d',$book->start)->formatLocalized('%d %b') ?></td>
	                    <td class="text-center"><?php echo Carbon::CreateFromFormat('Y-m-d',$book->finish)->formatLocalized('%d %b') ?></td>

	                    <td class="text-center"><?php echo $book->nigths ?></td>
	                    <td class="text-center">
	                       <div class="col-md-6">
	                           <?php echo round($book->total_price)."€" ?><br>
	                           <?php if (isset($payment[$book->id])): ?>
	                               <?php echo "<p style='color:red'>".$payment[$book->id]."€</p>" ?>
	                           <?php else: ?>
	                           <?php endif ?>
	                       </div>
	                    </td>
	                    <td class="text-center sm-p-t-10 sm-p-b-10">
	                        <?php if ($book->type_book == 1 || $book->type_book == 3 || $book->type_book == 4 || $book->type_book == 5 || $book->type_book == 6 ): ?>
	                                <b>PEND...</b>
	                            <?php elseif($book->type_book == 2 ): ?>
	                                <b>PAG...</b>
	                            <?php elseif($book->type_book == 7  ): ?>
	                                <b>PROPI...</b>
	                            <?php elseif($book->type_book == 8 ): ?>
	                                <b>SUBCOM...</b>
	                            <?php endif ?>
	                    </td>
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
                            
                        </td>
	                </tr>
	            <?php endforeach ?>
	        </tbody>
	    </table>
	</div>
<?php endif ?>
<script type="text/javascript">
	$('.customer-phone').change(function(event) {
	    var id = $(this).attr('data-id');
	    var phone = $(this).val();
	    $.get('/admin/customer/change/phone/'+id+'/'+phone, function(data) {


	        $.notify({
	            title: '<strong>'+data.title+'</strong>, ',
	            icon: 'glyphicon glyphicon-star',
	            message: data.response
	        },{
	            type: data.status,
	            animate: {
	                enter: 'animated fadeInUp',
	                exit: 'animated fadeOutRight'
	            },
	            placement: {
	                from: "top",
	                align: "left"
	            },
	            offset: 80,
	            spacing: 10,
	            z_index: 1031,
	            allow_dismiss: true,
	            delay: 60000,
	            timer: 60000,
	        });
	    });

	    var type = $('.table-data').attr('data-type');
	    var year = $('#fecha').val();
	    $.get('/admin/reservas/api/getTableData', { type: type, year: year }, function(data) {
	
	        $('.content-tables').empty().append(data);

	    });
	});

	$('.status, .room').change(function(event) {
	    var id = $(this).attr('data-id');
	    var clase = $(this).attr('class');
	    
	    if (clase == 'status form-control minimal') {
	        var status = $(this).val();
	        var room = "";

	    }else if(clase == 'room form-control minimal'){
	        var room = $(this).val();
	        var status = "";
	    }



	    if (status == 5) {

	        $('.modal-content.contestado').empty().load('/admin/reservas/ansbyemail/'+id);
	        $('#btnContestado').trigger('click');      

	    }else{
	        
	       	$.get('/admin/reservas/changeBook/'+id, {status:status,room: room}, function(data) {

	            if (data.status == 'danger') {
	                $.notify({
	                    title: '<strong>'+data.title+'</strong>, ',
	                    icon: 'glyphicon glyphicon-star',
	                    message: data.response
	                },{
	                    type: data.status,
	                    animate: {
	                        enter: 'animated fadeInUp',
	                        exit: 'animated fadeOutRight'
	                    },
	                    placement: {
	                        from: "top",
	                        align: "left"
	                    },
	                    offset: 80,
	                    spacing: 10,
	                    z_index: 1031,
	                    allow_dismiss: true,
	                    delay: 60000,
	                    timer: 60000,
	                }); 
	            } else {
	                $.notify({
	                    title: '<strong>'+data.title+'</strong>, ',
	                    icon: 'glyphicon glyphicon-star',
	                    message: data.response
	                },{
	                    type: data.status,
	                    animate: {
	                        enter: 'animated fadeInUp',
	                        exit: 'animated fadeOutRight'
	                    },
	                    placement: {
	                        from: "top",
	                        align: "left"
	                    },
	                    allow_dismiss: false,
	                    offset: 80,
	                    spacing: 10,
	                    z_index: 1031,
	                    delay: 5000,
	                    timer: 1500,
	                }); 
	            }

	            var type = $('.table-data').attr('data-type');
		        var year = $('#fecha').val();
		        $.get('/admin/reservas/api/getTableData', { type: type, year: year }, function(data) {
		    
		            $('.content-tables').empty().append(data);

		        });

		        $('.content-calendar').empty().append('<div class="col-xs-12 text-center sending" style="padding: 120px 15px;"><i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br><h2 class="text-center">CARGANDO CALENDARIO</h2></div>');

                $('.content-calendar').empty().load('/getCalendarMobile');

	       }); 
	    }
	});

</script>
@extends('layouts.admin-master')

@section('title') Seccion Propietarios @endsection

@section('externalScripts')  

    <link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />

	<link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('/assets/plugins/bootstrap-datepicker/css/datepicker3.css')}}" type="text/css" >
    <link rel="stylesheet" href="{{ asset('/frontend/css/components/daterangepicker.css')}}" type="text/css">
@endsection
     
@section('content')

<?php use \Carbon\Carbon;  setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); ?>

<style type="text/css"> 

	.S, .D{
	    background-color: rgba(0,0,0,0.2)!important;
	    color: red!important;
	}
	.total{
		border-right: 2px solid black !important;
		border-left: 2px solid black !important;
		font-weight: bold;
		color: black;
		background-color: rgba(0,100,255,0.2) !important;
	}

    .botones{
        padding-top: 0px!important;
        padding-bottom: 0px!important;
    }
    .nuevo{
        background-color: lightgreen;
        color: black;
        border-radius: 11px;
        width: 50px;
    }
	.table-hover > tr > td{
		padding: 3px!important;
	}
    a {
        color: black;
        cursor: pointer;
    }
    .btn-success2{
    	background-color: rgb(70, 195, 123)!important; 
    	font-size: 20px !important; 
    	border: rgb(70, 195, 123) !important; 
    	box-shadow: rgba(70, 195, 123, 0.5) 0px 0px 3px 2px !important; 
    	display: inline-block;
    	color: white!important;
    }

    .bloq-cont{
    	padding: 30px;
    	border: 2px solid #999999;
    	-moz-border-radius: 6px;
    	-webkit-border-radius: 6px;
    	border-radius: 6px;
    	box-shadow: inset 1px 1px 0 white, 1px 1px 0 white;
    	background: #f7f7f7;
    	margin-top: 15px;
    }
    .btn-danger2{
    	display:none;font-size: 20px !important;
    	background-color: rgb(228, 22, 22)!important;
    	border: rgb(201, 53, 53) !important;
    	box-shadow: 0px 0px 3px 2px rgba(228, 22, 22, 0.5)!important;"
    	color: white!important;
    }
    .daterangepicker.dropdown-menu{
    	z-index: 3000!important;
    }
    .btn-cons {
        margin-right: 5px!important;
        min-width: 150px!important;
    }
</style>
<?php if (!$mobile->isMobile()): ?>
	<div class="container-fluid padding-10 sm-padding-10">
	    <div class="row">
	    	<div class="col-md-12 push-20 text-center">

	    	    <div class="col-md-12">
	    	    	<div class="col-md-6 col-md-offset-4">
		    			<div class="col-md-6">
		    				<h2 class="text-center"><b>Planning de reservas</b>  Fechas:</h2>
		    			</div>
		    		        
		    		    <div class="col-md-2" style="padding: 15px;">  
		    		        <select id="fecha" class="form-control minimal">
		    		            <?php $fecha = $date->copy()->SubYear(); ?>
		    		            <?php if ($fecha->copy()->format('Y') < 2015): ?>
		    		                <?php $fecha = new Carbon('first day of September 2015'); ?>
		    		            <?php endif ?>
		    		        
		    		            <?php for ($i=1; $i <= 3; $i++): ?>                           
		    		                <option value="<?php echo $fecha->copy()->format('Y'); ?>" {{ $date->copy()->format('Y') == $fecha->format('Y') ? 'selected' : '' }}>
		    		                    <?php echo $fecha->copy()->format('Y')."-".$fecha->copy()->addYear()->format('Y'); ?> 
		    		                </option>
		    		                <?php $fecha->addYear(); ?>
		    		            <?php endfor; ?>
		    		        </select>
		    		    
		    			</div> 
	    	    	</div>       
	    		</div>
			
				<div class="col-md-4 col-md-offset-4">
					<div class="col-md-12 text-center">
						<h1 class="text-complete font-w800"><?php echo strtoupper($room->user->name) ?> <?php echo strtoupper($room->nameRoom) ?></h1>
					</div>

				</div>
				<div style="clear: both;"></div>
				<div class="col-md-1">
					<a class="btn btn-success btn-cons m-b-10" type="button" data-toggle="modal" data-target="#modalBloq">
	                    <span class="bold">Bloquear fechas</span>
	                </a>
		        </div>
				<div class="col-md-1">
					<a class="btn btn-success btn-cons m-b-10" type="button" data-toggle="modal" data-target="#modalLiquidation">
	                    <span class="bold">Liquidación</span>
	                </a>
				</div>
				<div class="col-md-1">
						<a class="btn btn-success btn-cons text-white" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/operativa" ?>">
							Opertaiva
						</a>
				</div>
				<div class="col-md-1">
						<a class="btn btn-success btn-cons text-white" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/tarifas" ?>">
							Tarifas
						</a>
				</div>
				<div class="col-md-1">
					<a class="btn btn-success btn-cons text-white" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/descuentos" ?>">
						Descuentos
					</a>
				</div>
				<div class="col-md-1">
					<a class="btn btn-success btn-cons text-white" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/fiscalidad" ?>">
						Fiscalidad
					</a>
				</div>
				<div class="col-md-12">
					<div class="col-md-12">
						
		                
						
					</div>
					<div class="col-xs-12">
						<div class="col-md-3">
							
						</div>
						<div class="col-md-3">
							
						</div>
						<div class="col-md-3">
							
						</div>
						<div class="col-md-3">
						    
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 push-20 text-center">
				<?php if (count($room) > 0): ?>
					<div class="col-md-6">
						<div class="col-md-6 pull-right" style="padding-left: 45px;">
							<table class="table table-hover  no-footer" id="basicTable" role="grid">
								<tr>
									<th class ="text-center bg-complete text-white">ING. PROP</th>
									<th class ="text-center bg-complete text-white">Apto</th>
									<th class ="text-center bg-complete text-white">Park</th>
									<?php if ($room->luxury == 1): ?>
										<th class ="text-center bg-complete text-white">Sup.Lujo</th>
									<?php else: ?>
									<?php endif ?>
								</tr>
								<tr>
									<td class="text-center total">
										<?php if ($total > 0): ?>
											<?php echo number_format($total,2,',','.'); ?>€
										<?php else: ?>
											--- €
										<?php endif ?>												
									</td>
									<td class="text-center">
										<?php if ($apto > 0): ?>
											<?php echo number_format($apto,2,',','.'); ?>€
										<?php else: ?>
											--- €
										<?php endif ?>
									</td>
									<td class="text-center">
										<?php if ($park > 0): ?>
											<?php echo number_format($park,2,',','.'); ?>€
										<?php else: ?>
											--- €
										<?php endif ?>
									</td>
									<?php if ($room->luxury == 1): ?>
										<td class="text-center">
											<?php if ($lujo > 0): ?>
												<?php echo number_format($lujo,2,',','.'); ?>€
											<?php else: ?>
												--- €
											<?php endif ?>
										</td>
									<?php else: ?>
									<?php endif ?>
								</tr>
							</table>
						</div>
						<div class="col-md-12">
							<table class="table table-hover  no-footer " id="basicTable" role="grid" >
								
								<thead>
									<th class ="text-center bg-complete text-white" style="width: 25%">Cliente</th>
									<th class ="text-center bg-complete text-white" style="width: 5%">Personas</th>
									<th class ="text-center bg-complete text-white">Entrada</th>
									<th class ="text-center bg-complete text-white">Salida</th>
									<th class ="text-center bg-complete text-white">ING. PROP</th>
									<th class ="text-center bg-complete text-white">Apto</th>
									<th class ="text-center bg-complete text-white">Parking</th>
									<?php if ($room->luxury == 1): ?>
										<th class ="text-center bg-complete text-white">Sup.Lujo</th>
									<?php else: ?>
									<?php endif ?>
										
									
								</thead>
								<tbody>
									<?php foreach ($books as $book): ?>
										<tr>
											<td class="text-center"><?php echo ucfirst(strtolower($book->Customer->name)) ?> </td>
											<td class="text-center"><?php echo $book->pax ?> </td>
											<td class="text-center">
												<?php 
													$start = Carbon::CreateFromFormat('Y-m-d',$book->start);
													echo $start->formatLocalized('%d-%b');
												?> 
											</td>
											<td class="text-center">
												<?php 
													$finish = Carbon::CreateFromFormat('Y-m-d',$book->finish);
													echo $finish->formatLocalized('%d-%b');
												?> 
											</td>
											<td class="text-center total">
												<?php if ($book->cost_total > 0): ?>
													<?php echo number_format($book->cost_total,2,',','.') ?> €
												<?php else: ?>
													---€	
												<?php endif ?>
											</td>
											<td class="text-center">
												<?php if ($book->cost_apto > 0): ?>
													<?php echo number_format($book->cost_apto,2,',','.') ?> €
												<?php else: ?>
													---€	
												<?php endif ?>
											</td>
											<td class="text-center">
												<?php if ($book->cost_park > 0): ?>
													<?php echo number_format($book->cost_park,2,',','.') ?> €
												<?php else: ?>
													---€	
												<?php endif ?>
											</td>
											<?php if ($room->luxury == 1): ?>
												<td class="text-center">
													<?php if ($book->cost_lujo > 0): ?>
														<?php echo $book->cost_lujo ?> €
													<?php else: ?>
														---€	
													<?php endif ?>
												</td>
											<?php else: ?>
											<?php endif ?>
										</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-6">
						<div class="col-md-12 col-xs-12">
							<div class="panel">
								<ul class="nav nav-tabs nav-tabs-simple bg-info-light fechas" role="tablist" data-init-reponsive-tabs="collapse">
									<?php $dateAux = $date->copy(); ?>
									<?php for ($i=1; $i <= 9 ; $i++) :?>
										<li <?php if($i == 4 ){ echo "class='active'";} ?>>
											<a href="#tab<?php echo $i?>" data-toggle="tab" role="tab" style="padding:10px">
												<?php echo ucfirst($dateAux->copy()->formatLocalized('%b %y'))?>
											</a>
										</li>
										<?php $dateAux->addMonth(); ?>
									<?php endfor; ?>
								</ul>
								<div class="tab-content">
									<?php for ($z=1; $z <= 9; $z++):?>
										<div class="tab-pane <?php if($z == 4){ echo 'active';} ?>" id="tab<?php echo $z ?>">
											<div class="row">
												<div class="col-md-12">
													<table class="fc-border-separate" style="width: 100%">
														<thead>
															<tr >
																<td class="text-center" colspan="<?php echo $arrayMonths[$date->copy()->format('n')]+1 ?>">
																	<?php echo  ucfirst($date->copy()->formatLocalized('%B %Y'))?>
																</td> 
															</tr>
															<tr>
																<td rowspan="2" style="width: 1%!important"></td>
																<?php for ($i=1; $i <= $arrayMonths[$date->copy()->format('n')] ; $i++): ?> 
																	<td style='border:1px solid black;width: 3%;font-size: 10px' class="text-center">
																		<?php echo $i?> 
																	</td> 
																<?php endfor; ?>
															</tr>
															<tr>

																<?php for ($i=1; $i <= $arrayMonths[$date->copy()->format('n')] ; $i++): ?> 
																	<td style='border:1px solid black;width: 3%;font-size: 10px' class="text-center <?php echo $days[$date->copy()->format('n')][$i]?>">
																		<?php echo $days[$date->copy()->format('n')][$i]?> 
																	</td> 
																<?php endfor; ?> 
															</tr>
														</thead>
														<tbody>
															<tr>
																<?php $date = $date->startOfMonth() ?>
																<td class="text-center">
																	<b title="<?php echo $room->name ?>"><?php echo substr($room->nameRoom, 0,5)?></b>
																</td>

																<?php for ($i=01; $i <= $arrayMonths[$date->copy()->format('n')] ; $i++): ?> 
																	<!-- Si existe la reserva para ese dia -->
																	<?php if (isset($reservas[$room->id][$date->copy()->format('Y')][$date->copy()->format('n')][$i])): ?>
												
																		<?php $calendars = $reservas[$room->id][$date->copy()->format('Y')][$date->copy()->format('n')][$i] ?>
																			<?php if ($calendars->start == $date->copy()->format('Y-m-d')): ?>
																				<td style='border:1px solid grey;width: 3%'>

																					<div class="<?php echo $book->getStatus($calendars->type_book) ?> start" style="width: 100%;float: left;">
																						&nbsp;
																					</div>

																				</td>    
																			<?php elseif($calendars->finish == $date->copy()->format('Y-m-d')): ?>
																				<td style='border:1px solid grey;width: 3%'>
																					<div class="<?php echo $book->getStatus($calendars->type_book) ?> end" style="width: 100%;float: left;">
																						&nbsp;
																					</div>


																				</td>
																			<?php else: ?>

																				<td 
																				style='border:1px solid grey;width: 3%' 
																				title="
																				<?php echo $calendars->customer['name'] ?> 

																				<?php echo 'PVP:'.$calendars->total_price ?>
																				<?php if (isset($payment[$calendars->id])): ?>
																					<?php echo 'PEND:'.($calendars->total_price - $payment[$calendars->id])?>
																				<?php else: ?>
																				<?php endif ?>" 
																				class="<?php echo $book->getStatus($calendars->type_book) ?>"
																				>
																					<?php if ($calendars->type_book == 9): ?>
																						<div style="width: 100%;height: 100%">
																							&nbsp;
																						</div>
																					<?php else: ?>
																						<a href="{{url ('/admin/reservas/update')}}/<?php echo $calendars->id ?>">
																							<div style="width: 100%;height: 100%">
																								&nbsp;
																							</div>
																						</a>
																					<?php endif ?>


																				</td>

																			<?php endif ?>
																	<!-- Si no existe nada para ese dia -->
																	<?php else: ?>
																	
																		<td class="<?php echo $days[$date->copy()->format('n')][$i]?>" style='border:1px solid grey;width: 3%'>

																		</td>

																	<?php endif; ?>
																	
																	<?php if ($date->copy()->format('d') != $arrayMonths[$date->copy()->format('n')]): ?>
					                                                    <?php $date = $date->addDay(); ?>
					                                                <?php else: ?>
					                                                    <?php $date = $date->startOfMonth() ?>
					                                                <?php endif ?>
																<?php endfor; ?> 
															</tr>
														</tbody>
													</table>
												</div>

											</div>
										</div>
										<?php $date = $date->addMonth(); ?>
									<?php endfor ?>
								</div>
							</div>

						</div>
					</div>
				<?php else: ?>
					<div class="col-md-12">
						
						<div class="text-center"><h1 class="text-complete">NO TIENES UNA HABITACION ASOCIADA</h1></div>

					</div>
				<?php endif ?>
			</div>
		</div>

		<form role="form">
		    <div class="form-group form-group-default required" style="display: none">
		        <label class="highlight">Message</label>
		        <input type="text" hidden="" class="form-control notification-message" placeholder="Type your message here" value="This notification looks so perfect!" required>
		    </div>
		    <button class="btn btn-success show-notification hidden" id="boton">Show</button>
		</form>
		<div class="modal fade slide-up in" id="modalBloq" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg">
		        <div class="modal-content-wrapper">
		            <div class="modal-content">
		            	<div class="block">
		            		<div class="block-header">
		            			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14" style="font-size: 40px!important;color: black!important"></i>
								</button>
		            			<h2 class="text-center">
		            				Bloqueo de fechas
		            			</h2>
		            		</div>
		            		
		            		<div class="row" style="padding:20px">
		            			<div class="col-md-4 col-md-offset-4">
									<input type="text" class="form-control daterange1" id="fechas" name="fechas" required="" style="cursor: pointer; text-align: center;min-height: 28px;" readonly="">
									<div class="input-group col-md-12 padding-10 text-center">
									    <button class="btn btn-complete bloquear" data-id="<?php echo $room->id ?>">Guardar</button>
									</div> 
		            			</div>
		            		</div>
		            	</div>

		            	
		            </div>
		        </div>
		      <!-- /.modal-content -->
		    </div>
		  <!-- /.modal-dialog -->
		</div>
		<div class="modal fade slide-up in" id="modalLiquidation" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg">
		        <div class="modal-content-wrapper">
		            <div class="modal-content">
		            	<div class="block">
		            		<div class="block-header">
		            			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14" style="font-size: 40px!important;color: black!important"></i>
		            			</button>
		            			<h2 class="text-center">
		            				Liquidación
		            			</h2>
		            		</div>
		            		<div class="block block-content" style="padding:20px">
		            			<table class="table table-hover  no-footer" id="basicTable" role="grid">
		            				<thead>
		            					
		            					<th class="bg-complete text-white text-center">Pagos</th>
		            					<th class="bg-complete text-white text-center">Facturado</th>
		            					<th class="bg-complete text-white text-center">Pagado</th>
		            					<th class="bg-complete text-white text-center">Pendiente</th>
		            				</thead>
		            				<tbody>
		            					<tr>
		            						<?php if (count($pagos)> 0): ?>
		            							
		            							<td style="padding: 0;border-left: 1px solid black;border-right: 1px solid black">
		            							<?php foreach ($pagos as $pago): ?>

		            							
		            								<table style="width: 100%">
		            									<tr>
		            										<td style="border:none"><?php echo Carbon::createFromFormat('Y-m-d',$pago->datePayment)->format('d-m-Y')?></td>
		            										<td style="border:none"><?php echo $pago->comment ?></td>
		            										<td style="border:none"><?php echo number_format($pago->import,2,',','.') ?>€</td>
		            									</tr>
		            								</table>							
		            							
		            								
		            								
		            							<?php endforeach ?>
		            							</td>
		            							<td class="text-center" style="padding-top: 20px!important;vertical-align: middle;"><?php echo number_format($total,2,',','.'); ?>€</td>
		            							<td class="text-center" style="padding-top: 20px!important;vertical-align: middle;">
		            								<?php echo number_format($pagototal,2,',','.') ?>€
		            							</td>

		            							<td class="text-center" style="padding-top: 20px!important;vertical-align: middle;">
		            								<?php echo number_format($total-$pagototal,2,',','.'); ?>€
		            							</td>
		            						<?php else: ?>
		            							<td class="text-center" colspan="4">Aun no hay pagos realizados</td>
		            						<?php endif ?>
		            						
		            					</tr>
		            				</tbody>
		            			</table>
		            		</div>
		            	</div>

		            	
		            </div>
		        </div>
		      <!-- /.modal-content -->
		    </div>
		  <!-- /.modal-dialog -->
		</div>
	</div>
<?php else: ?>
	<style type="text/css">
		.nav-tabs-simple > li.active{
			font-weight: 600;
			background: #e8e8e8;
		}
		table.calendar-table thead > tr > td {
		    width: 20px!important;
		    padding: 0px 5px!important;
		}
		button.minimal{
		    background-image: linear-gradient(45deg, transparent 50%, gray 50%),linear-gradient(135deg, gray 50%, transparent 50%),linear-gradient(to right, #ccc, #ccc)!important;
			background-position: calc(100% - 20px) calc(1em + 2px),calc(100% - 15px) calc(1em + 2px),calc(100% - 2.5em) 0.5em!important;
			 
			background-size: 5px 5px,5px 5px,1px 1.5em!important;
			background-repeat: no-repeat;
		}
		.nav-tabs > li > a:hover, .nav-tabs > li > a:focus{
		    color: white!important;
		}

		.fechas > li.active{
		    background-color: rgb(81,81,81);
		}
		.fechas > li > a{
			color: white!important;
		}
		.nav-tabs ~ .tab-content{
		    padding: 0px;
		}
	</style>
	<div class="container-fluid padding-10 sm-padding-10">
	    <div class="row">
	    	<div class="col-md-12 push-20 text-center">

	    	    <div class="col-md-12">
	    	    	<div class="row">
		    			<div class="col-xs-7 not-padding">
		    				<h4 class="text-center push-10" style="font-size: 19px;"><b>Planning de reservas</b></h4>
		    			</div>
		    		        
		    		    <div class="col-xs-5 m-t-10 p-r-0" style="padding:0 15px;">  
		    		        <select id="fecha" class="form-control minimal">
		    		            <?php $fecha = $date->copy()->SubYear(); ?>
		    		            <?php if ($fecha->copy()->format('Y') < 2015): ?>
		    		                <?php $fecha = new Carbon('first day of September 2015'); ?>
		    		            <?php endif ?>
		    		        
		    		            <?php for ($i=1; $i <= 3; $i++): ?>                           
		    		                <option value="<?php echo $fecha->copy()->format('Y'); ?>" {{ $date->copy()->format('Y') == $fecha->format('Y') ? 'selected' : '' }}>
		    		                    <?php echo $fecha->copy()->format('Y')."-".$fecha->copy()->addYear()->format('Y'); ?> 
		    		                </option>
		    		                <?php $fecha->addYear(); ?>
		    		            <?php endfor; ?>
		    		        </select>
		    		    
		    			</div> 
	    	    	</div>       
	    		</div>
			
				<div class="col-md-4 col-md-offset-4">
					<div class="col-md-12 text-center">
						<h1 class="text-complete font-w800"><?php echo strtoupper($room->user->name) ?> <?php echo strtoupper($room->nameRoom) ?></h1>
					</div>
					<div class="col-md-12">
						<div class="dropdown dropdown-default">
							<button class="btn btn-secondary dropdown-toggle text-center minimal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 138px;">
								Paginas
							</button>
							<div class="dropdown-menu" style="width: 138px;">
								<a class="dropdown-item" data-toggle="modal" data-target="#modalBloq"><span class="bold">Bloquear fechas</span></a><br>
								<a class="dropdown-item" data-toggle="modal" data-target="#modalLiquidation"><span class="bold">Liquidación</span></a><br>
								<a class="dropdown-item" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/operativa" ?>">Opertaiva</a><br>
								<a class="dropdown-item" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/tarifas" ?>">Tarifas</a><br>
								<a class="dropdown-item" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/descuentos" ?>">Descuentos</a><br>
								<a class="dropdown-item" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/fiscalidad" ?>">Fiscalidad</a><br>
							</div>
						</div>
						<!-- <button class="btn btn-success btn-cons push-20" type="button" data-toggle="modal" data-target="#modalBloq">
		                    <span class="bold">Bloquear fechas</span>
		                </button>
						<button class="btn btn-success btn-cons push-20" type="button" data-toggle="modal" data-target="#modalLiquidation">
		                    <span class="bold">Liquidación</span>
		                </button> -->
					</div>
					<!-- <div class="col-xs-12">

						<div class="col-xs-6 push-20">
							<a class="btn btn-success btn-cons text-white" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/operativa" ?>"><span class="bold">Opertaiva</span></a>
						</div>
						<div class="col-xs-6 push-20">
							<a class="btn btn-success btn-cons text-white" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/tarifas" ?>"><span class="bold">Tarifas</span></a>
						</div>
						<div class="col-xs-6 push-20">
							<a class="btn btn-success btn-cons text-white" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/descuentos" ?>"><span class="bold">Descuentos</span></a>
						</div>
						<div class="col-xs-6 push-20">
						    <a class="btn btn-success btn-cons text-white" href="{{ url('admin/propietario') }}/<?php echo $room->nameRoom."/fiscalidad" ?>"><span class="bold">Fiscalidad</span></a>
						</div>
					</div> -->
				</div>
			</div>
			<div class="row push-20 text-center">
				<?php if (count($room) > 0): ?>
					<div class="col-xs-12 push-20">
						<h2 class="text-center push-10" style="font-size: 24px;"><b>Resumen</b></h2>
						<div class="row" style="border: none;">
							<table class="table table-hover   no-footer" id="basicTable" role="grid">
								<tr>
									<th class ="text-center bg-complete text-white">ING. PROP</th>
									<th class ="text-center bg-complete text-white">APTO</th>
									<th class ="text-center bg-complete text-white">PARK</th>
									<?php if ($room->luxury == 1): ?>
										<th class ="text-center bg-complete text-white">SUP.LUJO</th>
									<?php else: ?>
									<?php endif ?>
								</tr>
								<tr>
									<td class="text-center total">
										<?php if ($total > 0): ?>
											<?php echo number_format($total,2,',','.'); ?>€
										<?php else: ?>
											--- €
										<?php endif ?>												
									</td>
									<td class="text-center">
										<?php if ($apto > 0): ?>
											<?php echo number_format($apto,2,',','.'); ?>€
										<?php else: ?>
											--- €
										<?php endif ?>
									</td>
									<td class="text-center">
										<?php if ($park > 0): ?>
											<?php echo number_format($park,2,',','.'); ?>€
										<?php else: ?>
											--- €
										<?php endif ?>
									</td>
									<?php if ($room->luxury == 1): ?>
										<td class="text-center">
											<?php if ($lujo > 0): ?>
												<?php echo number_format($lujo,2,',','.'); ?>€
											<?php else: ?>
												--- €
											<?php endif ?>
										</td>
									<?php else: ?>
									<?php endif ?>
								</tr>
							</table>
						</div>
						<div class="row table-responsive" style="border: none;">
							<h2 class="text-center push-10" style="font-size: 24px;"><b>Reservas</b></h2>

							<table class="table table-hover no-footer" id="basicTable" role="grid" style="margin-bottom: 17px!important">
								
								<thead>
									<th class ="text-center bg-complete text-white" style="width: 25%">Cliente</th>
									<th class ="text-center bg-complete text-white" style="width: 5%">Personas</th>
									<th class ="text-center bg-complete text-white">Entrada</th>
									<th class ="text-center bg-complete text-white">Salida</th>
									<th class ="text-center bg-complete text-white">ING. PROP</th>
									<th class ="text-center bg-complete text-white">Apto</th>
									<th class ="text-center bg-complete text-white">Parking</th>
									<?php if ($room->luxury == 1): ?>
										<th class ="text-center bg-complete text-white">Sup.Lujo</th>
									<?php else: ?>
									<?php endif ?>
										
									
								</thead>
								<tbody>
									<?php foreach ($books as $book): ?>
										<tr>
											<td class="text-center"><?php echo ucfirst(strtolower($book->Customer->name)) ?> </td>
											<td class="text-center"><?php echo $book->pax ?> </td>
											<td class="text-center">
												<?php 
													$start = Carbon::CreateFromFormat('Y-m-d',$book->start);
													echo $start->formatLocalized('%d-%b');
												?> 
											</td>
											<td class="text-center">
												<?php 
													$finish = Carbon::CreateFromFormat('Y-m-d',$book->finish);
													echo $finish->formatLocalized('%d-%b');
												?> 
											</td>
											<td class="text-center total">
												<?php if ($book->cost_total > 0): ?>
													<?php echo number_format($book->cost_total,2,',','.') ?> €
												<?php else: ?>
													---€	
												<?php endif ?>
											</td>
											<td class="text-center">
												<?php if ($book->cost_apto > 0): ?>
													<?php echo number_format($book->cost_apto,2,',','.') ?> €
												<?php else: ?>
													---€	
												<?php endif ?>
											</td>
											<td class="text-center">
												<?php if ($book->cost_park > 0): ?>
													<?php echo number_format($book->cost_park,2,',','.') ?> €
												<?php else: ?>
													---€	
												<?php endif ?>
											</td>
											<?php if ($room->luxury == 1): ?>
												<td class="text-center">
													<?php if ($book->cost_lujo > 0): ?>
														<?php echo $book->cost_lujo ?> €
													<?php else: ?>
														---€	
													<?php endif ?>
												</td>
											<?php else: ?>
											<?php endif ?>
										</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row"> 
						<div class="col-md-12 col-xs-12">
							<div class="panel">
								<ul class="nav nav-tabs nav-tabs-simple bg-info-light fechas" role="tablist" data-init-reponsive-tabs="collapse">
									<?php $dateAux = $date->copy()->addMonths(3); ?>
									<?php for ($i=4; $i <= 8 ; $i++) :?>

										<li <?php if($i == 4 ){ echo "class='active'";} ?>>
											<a href="#tab<?php echo $i?>" data-toggle="tab" role="tab" style="padding:10px">
												<?php echo ucfirst($dateAux->copy()->formatLocalized('%b %y'))?>
											</a>
										</li>
										<?php $dateAux->addMonth(); ?>
									<?php endfor; ?>
								</ul>
								<div class="tab-content " style="padding: 0 15px;">
									<?php for ($z=1; $z <= 9; $z++):?>
										<div class="tab-pane <?php if($z == 4){ echo 'active';} ?>" id="tab<?php echo $z ?>">
											<div class="row table-responsive p-b-20" style="border: none;">
												<table class="fc-border-separate calendar-table" style="width: 100%">
													<thead>
														<tr>
															<td rowspan="2" style="width: 1%!important"></td>
															<?php for ($i=1; $i <= $arrayMonths[$date->copy()->format('n')] ; $i++): ?> 
																<td style='border:1px solid black;width: 3%;font-size: 10px' class="text-center">
																	<?php echo $i?> 
																</td> 
															<?php endfor; ?>
														</tr>
														<tr>

															<?php for ($i=1; $i <= $arrayMonths[$date->copy()->format('n')] ; $i++): ?> 
																<td style='border:1px solid black;width: 3%;font-size: 10px' class="text-center <?php echo $days[$date->copy()->format('n')][$i]?>">
																	<?php echo $days[$date->copy()->format('n')][$i]?> 
																</td> 
															<?php endfor; ?> 
														</tr>
													</thead>
													<tbody>
														<tr>
															<?php $date = $date->startOfMonth() ?>
															<td class="text-center">
																
															</td>

															<?php for ($i=01; $i <= $arrayMonths[$date->copy()->format('n')] ; $i++): ?> 
																<!-- Si existe la reserva para ese dia -->
																<?php if (isset($reservas[$room->id][$date->copy()->format('Y')][$date->copy()->format('n')][$i])): ?>
											
																	<?php $calendars = $reservas[$room->id][$date->copy()->format('Y')][$date->copy()->format('n')][$i] ?>
																		<?php if ($calendars->start == $date->copy()->format('Y-m-d')): ?>
																			<td style='border:1px solid grey;width: 3%'>

																				<div class="<?php echo $book->getStatus($calendars->type_book) ?> start" style="width: 100%;float: left;">
																					&nbsp;
																				</div>

																			</td>    
																		<?php elseif($calendars->finish == $date->copy()->format('Y-m-d')): ?>
																			<td style='border:1px solid grey;width: 3%'>
																				<div class="<?php echo $book->getStatus($calendars->type_book) ?> end" style="width: 100%;float: left;">
																					&nbsp;
																				</div>


																			</td>
																		<?php else: ?>

																			<td 
																			style='border:1px solid grey;width: 3%' 
																			title="
																			<?php echo $calendars->customer['name'] ?> 

																			<?php echo 'PVP:'.$calendars->total_price ?>
																			<?php if (isset($payment[$calendars->id])): ?>
																				<?php echo 'PEND:'.($calendars->total_price - $payment[$calendars->id])?>
																			<?php else: ?>
																			<?php endif ?>" 
																			class="<?php echo $book->getStatus($calendars->type_book) ?>"
																			>
																				<?php if ($calendars->type_book == 9): ?>
																					<div style="width: 100%;height: 100%">
																						&nbsp;
																					</div>
																				<?php else: ?>
																					<a href="{{url ('/admin/reservas/update')}}/<?php echo $calendars->id ?>">
																						<div style="width: 100%;height: 100%">
																							&nbsp;
																						</div>
																					</a>
																				<?php endif ?>


																			</td>

																		<?php endif ?>
																<!-- Si no existe nada para ese dia -->
																<?php else: ?>
																
																	<td class="<?php echo $days[$date->copy()->format('n')][$i]?>" style='border:1px solid grey;width: 3%'>

																	</td>

																<?php endif; ?>
																
																<?php if ($date->copy()->format('d') != $arrayMonths[$date->copy()->format('n')]): ?>
				                                                    <?php $date = $date->addDay(); ?>
				                                                <?php else: ?>
				                                                    <?php $date = $date->startOfMonth() ?>
				                                                <?php endif ?>
															<?php endfor; ?> 
														</tr>
													</tbody>
												</table>

											</div>
										</div>
										<?php $date = $date->addMonth(); ?>
									<?php endfor ?>
								</div>
							</div>

						</div>
					</div>
				<?php else: ?>
					<div class="col-md-12">
						
						<div class="text-center"><h1 class="text-complete">NO TIENES UNA HABITACION ASOCIADA</h1></div>

					</div>
				<?php endif ?>
			</div>
		</div>

		<form role="form">
		    <div class="form-group form-group-default required" style="display: none">
		        <label class="highlight">Message</label>
		        <input type="text" hidden="" class="form-control notification-message" placeholder="Type your message here" value="This notification looks so perfect!" required>
		    </div>
		    <button class="btn btn-success show-notification hidden" id="boton">Show</button>
		</form>
		<div class="modal fade slide-up in" id="modalBloq" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg">
		        <div class="modal-content-wrapper">
		            <div class="modal-content">
		            	<div class="block">
		            		<div class="block-header">
		            			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14" style="font-size: 40px!important;color: black!important"></i>
								</button>
		            			<h2 class="text-center">
		            				Bloqueo de fechas
		            			</h2>
		            		</div>
		            		
		            		<div class="row" style="padding:20px">
		            			<div class="col-md-4 col-md-offset-4">
									<input type="text" class="form-control daterange1" id="fechas" name="fechas" required="" style="cursor: pointer; text-align: center;min-height: 28px;" readonly="">
									<div class="input-group col-md-12 padding-10 text-center">
									    <button class="btn btn-complete bloquear" data-id="<?php echo $room->id ?>">Guardar</button>
									</div> 
		            			</div>
		            		</div>
		            	</div>

		            	
		            </div>
		        </div>
		      <!-- /.modal-content -->
		    </div>
		  <!-- /.modal-dialog -->
		</div>
		<div class="modal fade slide-up in" id="modalLiquidation" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog modal-lg">
		        <div class="modal-content-wrapper">
		            <div class="modal-content">
		            	<div class="block">
		            		<div class="block-header">
		            			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14" style="font-size: 40px!important;color: black!important"></i>
		            			</button>
		            			<h2 class="text-center">
		            				Liquidación
		            			</h2>
		            		</div>
		            		<div class="block block-content" style="padding:20px">
		            			<table class="table table-hover  no-footer" id="basicTable" role="grid">
		            				<thead>
		            					
		            					<th class="bg-complete text-white text-center">Pagos</th>
		            					<th class="bg-complete text-white text-center">Facturado</th>
		            					<th class="bg-complete text-white text-center">Pagado</th>
		            					<th class="bg-complete text-white text-center">Pendiente</th>
		            				</thead>
		            				<tbody>
		            					<tr>
		            						<?php if (count($pagos)> 0): ?>
		            							
		            							<td style="padding: 0;border-left: 1px solid black;border-right: 1px solid black">
		            							<?php foreach ($pagos as $pago): ?>

		            							
		            								<table style="width: 100%">
		            									<tr>
		            										<td style="border:none"><?php echo Carbon::createFromFormat('Y-m-d',$pago->datePayment)->format('d-m-Y')?></td>
		            										<td style="border:none"><?php echo $pago->comment ?></td>
		            										<td style="border:none"><?php echo number_format($pago->import,2,',','.') ?>€</td>
		            									</tr>
		            								</table>							
		            							
		            								
		            								
		            							<?php endforeach ?>
		            							</td>
		            							<td class="text-center" style="padding-top: 20px!important;vertical-align: middle;"><?php echo number_format($total,2,',','.'); ?>€</td>
		            							<td class="text-center" style="padding-top: 20px!important;vertical-align: middle;">
		            								<?php echo number_format($pagototal,2,',','.') ?>€
		            							</td>

		            							<td class="text-center" style="padding-top: 20px!important;vertical-align: middle;">
		            								<?php echo number_format($total-$pagototal,2,',','.'); ?>€
		            							</td>
		            						<?php else: ?>
		            							<td class="text-center" colspan="4">Aun no hay pagos realizados</td>
		            						<?php endif ?>
		            						
		            					</tr>
		            				</tbody>
		            			</table>
		            		</div>
		            	</div>

		            	
		            </div>
		        </div>
		      <!-- /.modal-content -->
		    </div>
		  <!-- /.modal-dialog -->
		</div>
	</div>
<?php endif ?>


@endsection

@section('scripts')
	
	<script src="/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
	<script src="/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
	<script src="/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript"></script>
	<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
   	<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/lodash.min.js"></script>
	<script src="/assets/plugins/moment/moment.min.js"></script>
	
	<script type="text/javascript" src="{{asset('/frontend/js/components/moment.js')}}"></script>
	<script type="text/javascript" src="{{asset('/frontend/js/components/daterangepicker.js')}}"></script>
	
	<script src="/assets/js/notifications.js" type="text/javascript"></script>

	<script type="text/javascript">

		$(function() {
		  $(".daterange1").daterangepicker({
		    "buttonClasses": "button button-rounded button-mini nomargin",
		    "applyClass": "button-color",
		    "cancelClass": "button-light",            
		    "startDate": '01 Dec, 17',
		    locale: {
		        format: 'DD MMM, YY',
		        "applyLabel": "Aplicar",
		          "cancelLabel": "Cancelar",
		          "fromLabel": "From",
		          "toLabel": "To",
		          "customRangeLabel": "Custom",
		          "daysOfWeek": [
		              "Do",
		              "Lu",
		              "Mar",
		              "Mi",
		              "Ju",
		              "Vi",
		              "Sa"
		          ],
		          "monthNames": [
		              "Enero",
		              "Febrero",
		              "Marzo",
		              "Abril",
		              "Mayo",
		              "Junio",
		              "Julio",
		              "Agosto",
		              "Septiembre",
		              "Octubre",
		              "Noviembre",
		              "Diciembre"
		          ],
		          "firstDay": 1,
		      },
		      
		  });
		});



		$(document).ready(function() {
			
			$('.bloq-fecha').click(function(event) {
				
				var x = document.getElementById('bloq');
				    if (x.style.display === 'none') {
				        x.style.display = 'block';
				    } else {
				        x.style.display = 'none';
				    }
			});
			$('.liquidacion').click(function(event) {
				
				var x = document.getElementById('liquidacion');
				    if (x.style.display === 'none') {
				        x.style.display = 'block';
				    } else {
				        x.style.display = 'none';
				    }
			});
			$('#fecha').change(function(event) {
			    
			    var year = $(this).val();
			    window.location = '/admin/propietario/<?php echo $room->nameRoom ?>/'+year;
			});


			$('.bloquear').click(function(event) {
				
				var id = $(this).attr('data-id');
				var fechas = $('.daterange1').val();

				$.get('/admin/propietario/bloquear', {room: id, fechas: fechas}).success(function( data ) {

					$('.notification-message').val(data);
					document.getElementById("boton").click();
					if (data == "Reserva Guardada") {
						setTimeout('document.location.reload()',1000);
					}else{
                          
                    } 
				});
			});

		});
		
	</script>

@endsection
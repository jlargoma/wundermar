<?php use \Carbon\Carbon;  setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); ?>
<?php $dateStat = $startYear->copy(); ?>
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
    	box-shadow: 0px 0px 3px 2px rgba(228, 22, 22, 0.5)!important;
    	color: white!important;
    }
    .daterangepicker.dropdown-menu{
    	z-index: 3000!important;
    }
    .btn-cons {
        margin-right: 5px;
        min-width: 150px;
    }
    .nav-tabs-simple > li.active a{
		color: white;
		background-color: #3f3f3f;
    }
    @media only screen and (max-width: 1024px){
		.buttons .col-lg-1.col-md-1{
			width: 12.333%;
		}
		.buttons .btn-cons{
			min-width: 100%!important;
			margin-right: 0px!important;
			width: 100%!important;
    	}
	}
   @media only screen and (min-width: 1025px){
		.buttons .col-lg-1.col-md-1{
			width: 11%;
		}
    	.buttons .btn-cons{
			min-width: 100%!important;
			margin-right: 0px!important;
			width: 100%!important;
    	}
    	
    }
</style>
<?php if (!$mobile->isMobile()): ?>
	<div class="container-fluid padding-10 sm-padding-10">
	    <div class="row">
	    	<div class="col-md-12 push-20 text-center">

	    	    <div class="col-md-12">
	    	    	<div class="container">
		    			
		    		    <?php if (!preg_match('/propietario/i', getUsrRole())): ?>
		    		    	<div class="col-md-6 col-sm-8">
			    				<h2 class="text-center"><b>Planning de reservas</b></h2>
			    			</div>
	    		    	    <div class="col-md-2" style="padding: 15px;">  
	    		    	        @include('backend.years._selector')
	    		    		</div> 
	    		    	<?php else: ?>
	    		    		<div class="col-md-12 col-sm-8">
	    		    			<?php $fecha = $startYear->copy(); ?>
			    				<h2 class="text-center">
			    					<b>Planning de reservas</b> {{ $year->year }}-{{ $year->year + 1 }}
			    				</h2>
			    			</div>
		    		    <?php endif ?>
		    		   
	    	    	</div>       
	    		</div>
			
				<div class="container">
                                    <?php if (getUsrRole() == 'propietario'): ?>
                                    <?php $roomsForUser = \App\Rooms::where('owned', $room->user->id)->get();?>
                                    <?php if (count($roomsForUser) == 1): ?>
                                        <div class="col-md-12 text-center">
                                          <h1 class="text-complete font-w800"><?php echo strtoupper($room->user->name) ?> <?php echo strtoupper($room->nameRoom) ?></h1>
                                        </div>
                                      <?php else: ?>
                                        <div class="col-md-2 col-md-offset-4 text-center push-20">
                                                <!-- <h1 class="text-complete font-w800"><?php echo strtoupper($room->user->name) ?> <?php echo strtoupper($room->nameRoom) ?></h1> -->
                                          <select class="form-control full-width minimal selectorRoom">
                                            <?php foreach ($roomsForUser as $roomX): ?>
                                              <option value="<?php echo $roomX->nameRoom ?>" {{ $roomX->id == $room->id ? 'selected' : '' }} >
                                                <?php echo substr($roomX->nameRoom . " - " . $roomX->name, 0, 15) ?>
                                              </option>
                                            <?php endforeach ?>
                                          </select>
                                        </div>
                                      <?php endif ?>
                                    <?php else: ?>
                                      <div class="col-md-2 col-md-offset-4 text-center push-20">
                                              <!-- <h1 class="text-complete font-w800"><?php echo strtoupper($room->user->name) ?> <?php echo strtoupper($room->nameRoom) ?></h1> -->
                                        <select class="form-control full-width minimal selectorRoom">
                                          <?php foreach (\App\Rooms::where('state', 1)->orderBy('order', 'ASC')->get() as $roomX): ?>
                                            <option value="<?php echo $roomX->nameRoom ?>" {{ $roomX->id == $room->id ? 'selected' : '' }} >
                                              <?php echo substr($roomX->nameRoom . " - " . $roomX->name, 0, 15) ?>
                                            </option>
                                          <?php endforeach ?>
                                        </select>
                                      </div>

                                    <?php endif ?>
						
					

				</div>
				<div style="clear: both;"></div>
				
			</div>
			<div class="col-md-12 push-20 text-center" id="content-info" style="display: none;"></div>
			<div class="col-md-12 push-20 text-center" id="content-info-ini">
				<?php if ($room): ?>
					<div class="row">
						<div class="col-md-6 col-xs-12 resumen blocks">
							<div class="col-md-6 col-md-offset-3">
								<h2 class="text-center font-w800">Resumen</h2>
								<table class="table table-bordered table-hover  no-footer" id="basicTable" role="grid" >
									<tr>
										<th class ="text-center bg-complete text-white">ING. PROP</th>
										<th class ="text-center bg-complete text-white">Apto</th>
										<th class ="text-center bg-complete text-white">Park</th>
                                                                                <th class ="text-center bg-complete text-white">Sup.Lujo</th>
									</tr>
									<tr>
										<td class="text-center total">
											<?php if ($total > 0): ?>
												<?php echo number_format($total,0,',','.'); ?>€
											<?php else: ?>
												--- €
											<?php endif ?>												
										</td>
										<td class="text-center">
											<?php if ($apto > 0): ?>
												<?php echo number_format($apto,0,',','.'); ?>€
											<?php else: ?>
												--- €
											<?php endif ?>
										</td>
										<td class="text-center">
											<?php if ($park > 0): ?>
												<?php echo number_format($park,0,',','.'); ?>€
											<?php else: ?>
												--- €
											<?php endif ?>
										</td>
											<td class="text-center">
												<?php if ($lujo > 0): ?>
													<?php echo number_format($lujo,0,',','.'); ?>€
												<?php else: ?>
													--- €
												<?php endif ?>
											</td>
									</tr>
								</table>
							</div>
						</div>

						<div class="col-md-6 col-xs-12 reservas resumen blocks">
							<h2 class="text-center font-w800">Calendario</h2>
							<div class="col-md-12 col-xs-12">
								@include('backend.owned.calendar')
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-xs-12 reservas resumen blocks">
							<div class="row">
								<div class="col-md-12">
									<h2 class="text-center font-w800">Listado de reservas</h2>
								</div>
								<div class="col-md-12">
									<table class="table no-footer ">
										
										<thead>
											<th class ="text-center bg-complete text-white" style="width: 25%; padding: 4px 10px">Cliente</th>
											<th class ="text-center bg-complete text-white" style="width: 10%; padding: 4px 10px">Pers</th>
											<th class ="text-center bg-complete text-white" style="width: 10%; padding: 4px 10px">IN</th>
											<th class ="text-center bg-complete text-white" style="width: 10%; padding: 4px 10px">OUT</th>
											<th class ="text-center bg-complete text-white" style="width: 10%; padding: 4px 10px">ING. PROP</th>
											<th class ="text-center bg-complete text-white" style="width: 10%; padding: 4px 10px">Apto</th>
											<th class ="text-center bg-complete text-white" style="width: 10%; padding: 4px 10px">Park.</th>
                                                                                        <th class ="text-center bg-complete text-white" style="width: 10%">Sup.Lujo</th>
                        					<th class ="text-center bg-complete text-white" style="width: 50px!important">   &nbsp;      </th>

										</thead>
										<tbody>
											<?php foreach ($books as $book): ?>
												<tr>
													<td class="text-center" style="padding: 8px" data-id="<?php echo $book->id; ?>"><?php echo ucfirst(strtolower($book->customer->name)) ?> </td>
													<td class="text-center" style="padding: 8px"><?php echo $book->pax ?> </td>
													<td class="text-center" style="padding: 8px">
														<?php 
															$start = Carbon::CreateFromFormat('Y-m-d',$book->start);
															echo $start->formatLocalized('%d-%b');
														?> 
													</td>
													<td class="text-center" style="padding: 8px">
														<?php 
															$finish = Carbon::CreateFromFormat('Y-m-d',$book->finish);
															echo $finish->formatLocalized('%d-%b');
														?> 
													</td>
													<td class="text-center total" style="padding: 8px; ">
														<?php if ($book->type_book != 7 && $book->type_book != 8 ): ?>
															<?php $cost = ($book->cost_apto + $book->cost_park + $book->cost_lujo) ?>
															<?php if ($cost > 0 ): ?>
																<?php echo number_format($cost,0,',','.') ?>€
															<?php else: ?>
																---€	
															<?php endif ?>
														<?php else: ?>
															---€
														<?php endif ?>
														
													</td>
													<td class="text-center" style="padding: 8px; ">

														<?php if ($book->type_book != 7 && $book->type_book != 8 ): ?>
															<?php if ($book->cost_apto > 0 ): ?>
																<?php echo number_format($book->cost_apto,0,',','.') ?>€
															<?php else: ?>
																---€	
															<?php endif ?>
														<?php else: ?>
															---€
														<?php endif ?>

													</td>
													<td class="text-center" style="padding: 8px; ">
														<?php if ($book->type_book != 7 && $book->type_book != 8 ): ?>
															<?php if ($book->cost_park > 0 ): ?>
																<?php echo number_format($book->cost_park,0,',','.') ?>€
															<?php else: ?>
																---€	
															<?php endif ?>
														<?php else: ?>
																---€	
														<?php endif ?>
													</td>
														<td class="text-center" style="padding: 8px; ">
															<?php if ($book->type_book != 7 && $book->type_book != 8 ): ?>
																<?php $auxLuxury = $book->cost_lujo ?>
																<?php if ($auxLuxury > 0): ?>
																	<?php echo round($auxLuxury) ?>€
																<?php else: ?>
																	---€	
																<?php endif ?>
															<?php else: ?>
																---€	
															<?php endif ?>
														</td>
													<td class="text-center">
						                                <?php if (!empty($book->book_owned_comments) && $book->promociones != 0 ): ?>
				                                        	<img src="/pages/oferta.png" style="width: 40px;" title="<?php echo $book->book_owned_comments ?>">
						                                   
						                                    
						                                <?php endif ?>
						                            </td>
												</tr>
											<?php endforeach ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						
						<div class="col-md-6 col-xs-12 resumen estadisticas blocks">
							<div class="col-xs-12">
								<div class="row push-20">
									<h2 class="text-center font-w800">
										Estadísticas
									</h2>
								</div>
								<div class="col-lg-6 col-md-12 col-sm-12 not-padding">
									<canvas id="barChart" style="width: 100%; height: 250px;"></canvas>
								</div>
								<div class="col-lg-6 col-md-12 col-sm-12 not-padding">
									<canvas id="barChartClient" style="width: 100%; height: 250px;"></canvas>
								</div>
								<p class="font-s12 push-0">
									<span class="text-danger">*</span> <i>Estas estadisticas estan generadas en base a las reservas que ya tenemos pagadas</i>
								</p>
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
		            			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close fs-14" style="font-size: 40px!important;color: black!important"></i>
								</button>
		            			<h2 class="text-center">
		            				Bloqueo de fechas
		            			</h2>
		            		</div>
		            		
		            		<div class="row" style="padding:20px" id="dateBlockContent">
                                          <div class="col-md-4 col-md-offset-4">
		            				<h5 class="text-center"> Seleccione sus fechas</h5>
									<input type="text" class="form-control daterange1" id="fechas" name="fechas" required="" style="cursor: pointer; text-align: center;min-height: 28px;" readonly="" placeholder="Seleccione sus fechas">
									<div class="input-group col-md-12 padding-10 text-center">
									    <button class="btn btn-complete bloquear" disabled="" data-id="<?php echo $room->id ?>">Guardar</button>
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
		            			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close fs-14" style="font-size: 40px!important;color: black!important"></i>
		            			</button>
		            			<h2 class="text-center">
		            				Liquidación
		            			</h2>
		            		</div>
		            		<div class="block block-content" style="padding:20px">
		            			<div class="row" style=" max-height: 650px; overflow-y: auto;">
		            				@include('backend.owned._liquidation')
		            			</div>
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
		.daterangepicker.dropdown-menu {
		    z-index: 3000!important;
		    top: 0px!important;
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
		a.dropdown-item{
			padding: 0 5px;
			margin-bottom: 10px;
			background-color: none!important;
			width: 100%;
			text-align: center;
		}
	</style>
	<div class="container-fluid padding-10 sm-padding-10">
	    <div class="row">
	    	<div class="col-md-12 push-20 text-center">

	    	    <div class="col-md-12">
	    	    	<div class="row">
		    		    <?php if (!preg_match('/propietario/i', getUsrRole())): ?>
		    		    	<div class="col-md-6 col-xs-12">
			    				<h2 class="text-center"><b>Planning de reservas</b></h2>
			    			</div>
	    		    	    <div class="col-xs-6 col-xs-offset-3" style="padding: 15px;">  
	    		    	        @include('backend.years._selector')
	    		    		</div> 
	    		    	<?php else: ?>
	    		    		<div class="col-xs-6 col-xs-offset-3">
	    		    			<?php $fecha = $startYear->copy(); ?>
			    				<h2 class="text-center">
			    					<b>Planning de reservas</b> {{ $year->year }}-{{ $year->year + 1 }}
			    				</h2>
			    			</div>
		    		    <?php endif ?>
	    	    	</div> 
	    	    	<div class="row">
						<?php $roomsForUser = \App\Rooms::where('owned', $room->user->id)->get(); ?>
	    	    						
	    	    							
						<?php if ( count($roomsForUser)  == 1): ?>
							<?php if (getUsrRole() == 'propietario'): ?>
								<div class="col-xs-12 text-center">
									<h1 class="text-complete font-w800"><?php echo strtoupper($room->user->name) ?> <?php echo strtoupper($room->nameRoom) ?></h1>
								</div>
							<?php else: ?>
								<div class="col-xs-6 col-xs-offset-3 text-center push-20">
									<!-- <h1 class="text-complete font-w800"><?php echo strtoupper($room->user->name) ?> <?php echo strtoupper($room->nameRoom) ?></h1> -->
									<select class="form-control full-width minimal selectorRoom">
		                                <?php foreach (\App\Rooms::where('state', 1)->orderBy('order', 'ASC')->get() as $roomX): ?>
		                                    <option value="<?php echo $roomX->nameRoom ?>" {{ $roomX->id == $room->id ? 'selected' : '' }} >
		                                        <?php echo substr($roomX->nameRoom." - ".$roomX->name, 0, 15)  ?>
		                                    </option>
		                                <?php endforeach ?>
		                            </select>
								</div>
							<?php endif ?>
							
						<?php else: ?>
							<div class="col-xs-6 col-xs-offset-3 text-center push-20">
								<!-- <h1 class="text-complete font-w800"><?php echo strtoupper($room->user->name) ?> <?php echo strtoupper($room->nameRoom) ?></h1> -->
								<select class="form-control full-width minimal selectorRoom">
	                                <?php foreach (\App\Rooms::where('state', 1)->orderBy('order', 'ASC')->get() as $roomX): ?>
	                                    <option value="<?php echo $roomX->nameRoom ?>" {{ $roomX->id == $room->id ? 'selected' : '' }} >
	                                        <?php echo substr($roomX->nameRoom." - ".$roomX->name, 0, 15)  ?>
	                                    </option>
	                                <?php endforeach ?>
	                            </select>
							</div>
							
						<?php endif ?>
	    	    	</div>      
	    		</div>
			</div>
		</div>
		<div class="row push-20 text-center" id="content-info" style="display: none;"></div>
		<div class="row push-20 text-center" id="content-info-ini">
			<?php if ($room): ?>
				<div class="col-xs-12 push-20 resumen blocks">
					<h2 class="text-center push-10" style="font-size: 24px;"><b>Resumen</b></h2>

					<div class="row" style="border: none;">
						<table class="table table-bordered table-hover no-footer" >
							<tr>
								<th class ="text-center bg-complete text-white">TOT. ING</th>
								<th class ="text-center bg-complete text-white">APTO</th>
								<th class ="text-center bg-complete text-white">PARK</th>
                                                                <th class ="text-center bg-complete text-white">S.LUJO</th>
							</tr>
							<tr>
								<td class="text-center total" style="padding: 8px;">
									<?php if ($total > 0): ?>
										<?php echo number_format($total,0,',','.'); ?>€
									<?php else: ?>
										--- €
									<?php endif ?>												
								</td>
								<td class="text-center" style="padding: 8px;">
									<?php if ($apto > 0): ?>
										<?php echo number_format($apto,0,',','.'); ?>€
									<?php else: ?>
										--- €
									<?php endif ?>
								</td>
								<td class="text-center" style="padding: 8px;">
									<?php if ($park > 0): ?>
										<?php echo number_format($park,0,',','.'); ?>€
									<?php else: ?>
										--- €
									<?php endif ?>
								</td>
									<td class="text-center" style="padding: 8px;">
										<?php if ($lujo > 0): ?>
											<?php echo number_format($lujo,0,',','.'); ?>€
										<?php else: ?>
											--- €
										<?php endif ?>
									</td>
								
							</tr>
						</table>
					</div>
				</div>
				
				<div class="row reservas resumen blocks"> 
					<div class="col-md-12 col-xs-12">
						<h2 class="text-center push-10" style="font-size: 24px;"><b>Calendario</b></h2>
						@include('backend.owned.calendar')

					</div>
				</div>

				<div class="row reservas resumen blocks">
					<div style="clear:both;"></div>
					<h2 class="text-center push-10" style="font-size: 24px;"><b>Listado de Reservas</b></h2>
					<div class="row table-responsive" style="border: none;">
						<table class="table table-hover no-footer" id="basicTable" role="grid" style="margin-bottom: 17px!important">
							
							<thead>
								<th class ="text-center bg-complete text-white" style="width: 25%">Cliente</th>
								<th class ="text-center bg-complete text-white" style="width: 5%">Pers</th>
								<th class ="text-center bg-complete text-white">Entrada</th>
								<th class ="text-center bg-complete text-white">Salida</th>
								<th class ="text-center bg-complete text-white">Tot. Ing</th>
								<th class ="text-center bg-complete text-white">Apto</th>
								<th class ="text-center bg-complete text-white">Parking</th>
                                                                <th class ="text-center bg-complete text-white">Sup.Lujo</th>
								<th class ="text-center bg-complete text-white" style="width: 50px!important">   &nbsp;      </th>
								
							</thead>
							<tbody>
								<?php foreach ($books as $book): ?>
									<tr>
										<td class="text-center"><?php echo ucfirst(strtolower($book->customer->name)) ?> </td>
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
										<td class="text-center">
										<?php if ($book->type_book != 7 && $book->type_book != 8 ): ?>
											<?php $cost = ($book->cost_apto + $book->cost_park + $book->cost_lujo) ?>
											<?php if ($cost > 0 ): ?>
												<b><?php echo number_format($cost,0,',','.') ?>€</b>
											<?php else: ?>
												---€	
											<?php endif ?>
										<?php else: ?>
											---€
										<?php endif ?>
										</td>
										<td class="text-center">
											<?php if ($book->cost_apto > 0): ?>
												<?php echo number_format($book->cost_apto,0,',','.') ?> €
											<?php else: ?>
												---€	
											<?php endif ?>
										</td>
										<td class="text-center">
											<?php if ($book->cost_park > 0): ?>
												<?php echo number_format($book->cost_park,0,',','.') ?> €
											<?php else: ?>
												---€	
											<?php endif ?>
										</td>
                                                                                <td class="text-center">
                                                                                        <?php if ($book->cost_lujo > 0): ?>
                                                                                                <?php echo round($book->cost_lujo); ?> €
                                                                                        <?php else: ?>
                                                                                                ---€	
                                                                                        <?php endif ?>
                                                                                </td>
										<td class="text-center">
			                                <?php if (!empty($book->book_owned_comments)): ?>
	                                        	<img src="/pages/oferta.png" style="width: 40px;" title="<?php echo $book->book_owned_comments ?>">
			                                   
			                                    
			                                <?php endif ?>
			                            </td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>


				<div class="row resumen estadisticas blocks">
					<div class="col-xs-12">
						<div class="row push-20">
							<h2 class="text-center font-w800">
								Estadísticas
							</h2>
						</div>
						<div class="col-md-6 not-padding">
							<canvas id="barChart" style="width: 100%; height: 250px;"></canvas>
						</div>
						<div class="col-md-6 not-padding">
							<canvas id="barChartClient" style="width: 100%; height: 250px;"></canvas>
						</div>
						<p class="font-s12 push-0">
							<span class="text-danger">*</span> <i>Estas estadisticas estan generadas en base a las reservas que ya tenemos pagadas</i>
						</p>
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
	            			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close fs-14" style="font-size: 40px!important;color: black!important"></i>
							</button>
	            			<h2 class="text-center">
	            				Bloqueo de fechas
	            			</h2>
	            		</div>
	            		
	            		<div class="row" style="padding:20px" id="dateBlockContent">
	            			<div class="col-md-4 col-md-offset-4">
	            				<h5 class="text-center"> Seleccione sus fechas</h5>
								<input type="text" class="form-control daterange1" id="fechas" name="fechas" required="" style="cursor: pointer; text-align: center;min-height: 28px;" readonly="" placeholder="Seleccione sus fechas">
								<div class="input-group col-md-12 padding-10 text-center">
								    <button class="btn btn-complete bloquear" disabled data-id="<?php echo $room->id ?>">Guardar</button>
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
	            			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close fs-14" style="font-size: 40px!important;color: black!important"></i>
	            			</button>
	            			<h2 class="text-center">
	            				Liquidación
	            			</h2>
	            		</div>
	            		<div class="block block-content not-padding table-responsive" style=" max-height: 650px; overflow-y: auto;">
	            			@include('backend.owned._liquidation')
	            		</div>
	            	</div>

	            	
	            </div>
	        </div>
	      <!-- /.modal-content -->
	    </div>
	  <!-- /.modal-dialog -->
	</div>

<?php endif ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('.selectorRoom').change(function(event) {
			var apto = $(this).val();
			var url = "/admin/propietario/"+apto;

			window.location.href = url;
		});
	});
</script>
<?php use \Carbon\Carbon;  setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); ?>
<?php $pagototalProp = 0;?>
<?php if (!$mobile->isMobile()): ?>


	<div class="row push-20">
		<?php if (count($pagos)> 0): ?>
			<div class="col-md-12 ">
				<div class="col-md-3 not-padding" >
					<div class="col-xs-12  bg-complete push-0">
						<h5 class="text-left white">
							Fecha de pago
						</h5>
					</div>
				</div>

				<div class="col-md-3 not-padding" >
					<div class="col-xs-12   bg-complete push-0">
						<h5 class="text-left white">
							Concepto
						</h5>
					</div>
				</div>
				<div class="col-md-3 not-padding" >
					<div class="col-xs-12  bg-complete push-0">
						<h5 class="text-left white">
							Importe
						</h5>
					</div>
				</div>
				<div class="col-md-3 not-padding">
					<div class="col-xs-12   bg-complete push-0">
						<h5 class="text-left white">
							Pendiente
						</h5>
					</div>
				</div>
			</div>
			<?php $sumPagos = 0; ?>
			<?php foreach ($pagos as $pago): ?>
				<?php $sumPagos += $pago->import ?>
				<div class="col-md-12 push-0">
					<div class="col-md-3 not-padding" >
						<div class="col-xs-12 push-0">
							<h5 class="text-left"><?php echo Carbon::createFromFormat('Y-m-d',$pago->date)->format('d-m-Y')?></h5>
						</div>
					</div>
					<div class="col-md-3 not-padding" >
						<div class="col-xs-12 push-0">
							<h5 class="text-left"><?php echo $pago->concept ?></h5>
						</div>
					</div>
					<div class="col-md-3 not-padding" >
						<div class="col-xs-12 push-0">
							<?php
                            	$divisor = 0;
								if(preg_match('/,/', $pago->PayFor)){
                                    $aux = explode(',', $pago->PayFor);
									for ($i = 0; $i < count($aux); $i++){
									    if ( !empty($aux[$i]) ){
                                        	$divisor ++;
									    }
									}

								}else{
                                    $divisor = 1;
								}
								$expense = $pago->import / $divisor;
							?>
							<h5 class="text-center"><?php echo number_format($expense,2,',','.') ?>€</h5>
							<?php $pagototalProp += $expense;?>
						</div>
					</div>

					<div class="col-md-3 not-padding">
						<div class="col-xs-12 push-0" style="">
							<h5 class="text-left text-danger"><?php echo number_format($total - $pagototalProp,2,',','.'); ?>€</h5>
						</div>
					</div>
				</div>
				
			<?php endforeach ?>
		<?php else: ?>
			<div class="col-md-12 text-center">
				Aun no hay pagos realizados
			</div>
		<?php endif ?>
				
	</div>
	<div class="col-md-4 bg-complete push-20">
		<div class="col-md-6">
			<h5 class="text-center white">GENERADO</h5>
		</div>
		<div class="col-md-6 text-center text-white">
			<h5 class="text-center white"><strong><?php echo number_format($total,2,',','.'); ?>€</strong></h5>
		</div>
	</div>
	<div class="col-md-4 bg-success push-20">
		<div class="col-md-6">
			<h5 class="text-center white">PAGADO</h5>
		</div>
		<div class="col-md-6 text-center text-white">
			<h5 class="text-center white"><strong><?php echo number_format($pagototalProp,2,',','.'); ?>€</strong></h5>
		</div>
	</div>
	<div class="col-md-4 bg-danger push-20">
		<div class="col-md-6">
			<h5 class="text-center white">PENDIENTE</h5>
		</div>
		<div class="col-md-6text-center text-white">
			<h5 class="text-center white"><strong><?php echo number_format(($total - $pagototalProp),2,',','.'); ?>€</strong></h5>
		</div>
	</div>
<?php else: ?>
	<div class="col-xs-12">

		<div class="row">

			<?php if (count($pagos)> 0): ?>
				<table class="table table-condensed no-footer" id="basicTable" role="grid">
					<thead>
						
						<th class="bg-complete text-white text-center"><i class="fa fa-calendar" aria-hidden="true"></i></th>
						<th class="bg-complete text-white text-center">Tipo</th>
						<th class="bg-complete text-white text-center"><i class="fa fa-money" aria-hidden="true"></i></th>
						<th class="bg-complete text-white text-center">Pend</th>
					</thead>
					<tbody>

						<?php foreach ($pagos as $pago): ?>
						<tr>

							<td class="text-center"  style="padding: 8px!important">
								<?php $date = Carbon::createFromFormat('Y-m-d',$pago->date) ?>
								<?php echo $date->format('d')?>-<?php echo $date->format('M')?>-<?php echo $date->format('y')?>
							</td>
							<td class="text-center" style="padding: 8px!important">
                                <?php echo $pago->concept ?>
							</td>
							<td class="text-center" style="padding: 8px!important">
                                <?php
									$divisor = 0;
									if(preg_match('/,/', $pago->PayFor)){
										$aux = explode(',', $pago->PayFor);
										for ($i = 0; $i < count($aux); $i++){
											if ( !empty($aux[$i]) ){
												$divisor ++;
											}
										}

									}else{
										$divisor = 1;
									}
                                ?>
								<h5 class="text-left"><?php echo number_format(($pago->import / $divisor),2,',','.') ?>€</h5>
                                <?php $pagototalProp += ($pago->import / $divisor);?>
							</td>


							<td class="text-center" style="padding: 8px!important">
								<?php echo number_format($total-$pagototal,2,',','.'); ?>€
							</td>						
							
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php else: ?>
				<div class="col-md-12 text-center">
					Aun no hay pagos realizados
				</div>
			<?php endif ?>
		</div>
        <div class="col-xs-12 bg-complete">
            <div class="col-xs-6">
                <h5 class="text-center white">GENERADO</h5>
            </div>
            <div class="col-xs-6 text-center text-white">
                <h5 class="text-center white"><strong><?php echo number_format($total,2,',','.'); ?>€</strong></h5>
            </div>
        </div>
        <div class="col-xs-12 bg-success">
            <div class="col-xs-6">
                <h5 class="text-center white">PAGADO</h5>
            </div>
            <div class="col-xs-6 text-center text-white">
                <h5 class="text-center white"><strong><?php echo number_format($pagototalProp,2,',','.'); ?>€</strong></h5>
            </div>
        </div>
        <div class="col-xs-12 bg-danger">
            <div class="col-xs-6">
                <h5 class="text-center white">PENDIENTE</h5>
            </div>
            <div class="col-xs-6 text-center text-white">
                <h5 class="text-center white"><strong><?php echo number_format(($total - $pagototalProp),2,',','.'); ?>€</strong></h5>
            </div>
        </div>
    </div>
<?php endif; ?>
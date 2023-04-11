

<?php setlocale(LC_TIME, "ES"); setlocale(LC_TIME, "es_ES"); use \Carbon\Carbon;?>
<div>
	<div class="col-xs-9 padding-10">
	    <p>
	        <?php echo "<b>".strtoupper($book->customer->name)."</b>" ?> creada el 
	        <?php $fecha = Carbon::createFromFormat('Y-m-d H:i:s' ,$book->created_at);?>
	        <?php echo $fecha->copy()->format('d-m-Y')." Hora:".$fecha->copy()->format('H:m')?><br> 
	        Creado por <?php echo "<b>".strtoupper($book->user->name)."</b>" ?>
	    </p>
	</div>
	<div class="col-xs-3 padding-10">

	    <a class="close" data-dismiss="modal" aria-hidden="true" style="min-width: 10px!important;padding: 25px">
	        <img src="{{ asset('/img/miramarski/iconos/close.png') }}" style="width: 20px" />
	    </a>
	</div>
	<div class="col-xs-12">
	
		<h3>
			<a href="{{ url('/admin/pdf/pdf-reserva') }}/<?php echo $book->id ?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
			<?php echo $book->customer->name ?> 
			<a href="tel:<?php echo $book->customer->phone ?>"><i class="fa fa-phone"></i></a>	
		</h3>
	</div>

	<div>
		<div class="m-l-50">
			<table>
				<tr>
					<td><b>Reserva :</b></td>
					<td><?php echo Carbon::CreateFromFormat('Y-m-d',$book->start)->formatLocalized('%d-%b') ?><b style="font-size: 15px">-</b><?php echo Carbon::CreateFromFormat('Y-m-d',$book->finish)->formatLocalized('%d-%b') ?></td>
				</tr>
				<tr>
					<td><b>Apto : </b></td>
					<td><?php echo $book->room->name ?> <b> Pax :</b><?php echo $book->pax ?></td>
				</tr>
				<tr>
					<td><b>PVP : </b></td>
					<td><?php echo number_format($book->total_price,2,',','.') ?> €</td>
				</tr>
				<tr>
					<td><b>Pendiente : </b></td>
					<td>
						<?php if ($book->total_price - $pending > 0): ?>
							<b style="color:red;" ><?php echo number_format($book->total_price - $pending,2,',','.') ?> €</b><br>
						<?php else: ?>
							<?php echo number_format($book->total_price - $pending,2,',','.') ?> €<br>
						<?php endif ?>
					</td>
				</tr>
			</table>
		</div>
		

		
			

		<div class="col-xs-12 m-t-10" style="border:1px solid black"> 
			<div class="panel-heading p-t-5">
			    <div class="panel-title">
			        Cobros
			    </div>
			</div>
			<div class="col-xs-12" >
				<?php if (count($payments)>0): ?>
					<table style="font-size: 16px;width: 100%;">
						<thead>
							<th>Fecha</th>
							<th>Importe</th>
							<th>Metodo</th>
						</thead>
						<tbody>
							<?php foreach ($payments as $pago): ?>
								<tr>
									<td><?php echo Carbon::CreateFromFormat('Y-m-d',$pago->datePayment)->format('d-m-Y') ?></td>
									<td><?php echo number_format($pago->import,2,',','.') ?>€</td>
									<td><?php echo $book->getTypeCobro($pago->type) ?></td>
									
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				<?php endif ?>
			</div>
			
			<div class="col-xs-12 m-t-20" style="padding: 0">
				<form action="{{ url('/admin/reservas/saveCobro') }}">
					<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
					<input type="hidden" name="id" value="<?php echo $book->id ?>">
					<div class="col-xs-4"  style="padding-left: 0">
						Fecha: <br>
						<input type="text" name="fecha" class="form-control" value="<?php echo Carbon::now()->format('d-m-Y') ?>">
					</div>
					<div class="col-xs-3" style="padding-left: 0">
						Importe:<br>
						<input type="number" name="import" class="form-control">
					</div>
					<div class="col-xs-5 text-left" style="padding-left: 0">
						Metodo de pago:<br>
						<select name="tipo" id="tipo" class="m-t-5">
							<?php for ($i=0; $i <= 2 ; $i++):?>
								<option value="<?php echo $i ?>"><?php echo $book->getTypeCobro($i) ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div style="clear: both;"></div>
					<div class="text-center">
						<input type="submit" class="btn btn-success  m-t-10" value="Cobrar">
					</div>
				</form>
			</div>

		</div>

		<div class="col-xs-12 m-t-5" style="border:1px solid black">
			<div class="panel-heading p-t-5">
			    <div class="panel-title">
			        Fianza
			    </div>
			</div>
			<form action="{{ url('/admin/reservas/saveFianza') }}">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="id" value="<?php echo $book->id ?>">
				<div class="col-xs-6">
					Fecha: <br>
					<input type="text" name="fecha" class="form-control" value="<?php echo Carbon::now()->format('d-m-Y') ?>">
				</div>
				<div class="col-xs-6">
					Fianza:<br>
					<input type="number" name="fianza" class="form-control">
				</div>
				<div class="col-xs-6 text-left">
					Comentario: <br>
					<input type="text" name="comentario" class="form-control">
				</div>
				<div class="col-xs-6">
					Tipo: <br>
					<select name="tipo" id="tipo" class="m-t-5">
						<?php for ($i=0; $i <= 2 ; $i++):?>
							<option value="<?php echo $i ?>"><?php echo $book->getTypeCobro($i) ?></option>
						<?php endfor; ?>
					</select>
				</div>
				<div style="clear: both;"></div>
				<div class="text-center">
					<input type="submit" class="btn btn-primary  m-t-10" value="Fianza">
				</div>
			</form>
		</div>
	</div>
</div>


<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
<?php use \Carbon\Carbon; ?>
<table class="table table-bordered table-striped table-header-bg no-footer">
	<thead>
		<tr>
			<th class="text-center bg-complete text-white">#</th>
			<th class="text-center bg-complete text-white">Fecha</th>
			<th class="text-center bg-complete text-white">Concepto</th>
			<th class="text-center bg-complete text-white">Debe</th>
			<th class="text-center bg-complete text-white">Haber</th>
			<th class="text-center bg-complete text-white">Saldo</th>
			<th class="text-center bg-complete text-white">Comentario</th>
		</tr>
	</thead>	
	<tbody>
		<?php $total = 0;//$saldoInicial->import; ?>
		<?php foreach ($bank as $key => $b): ?>
			
			<tr>
				<td class="text-center" style="padding: 8px 5px!important">
					<?php echo $key+1 ?>
				</td>
				<td class="text-center" style="padding: 8px 5px!important">
					<?php $date = Carbon::createFromFormat('Y-m-d', $b->date); ?>
					<b><?php echo strtoupper($date->format('d-m-Y')); ?></b>
				</td>
				<td class="text-center" style="padding: 8px 5px!important">
					<?php echo $b->concept; ?>
				</td>

				
				
				<td class="text-center" style="padding: 8px 5px!important">
					<?php if ($b->type == 1): ?>
						<b class="text-danger">-<?php echo number_format($b->import,2,',','.'); ?> €</b>
						<?php $total -= $b->import ?>
					<?php endif ?>
					
				</td>
				<td class="text-center" style="padding: 8px 5px!important">
					<?php if ($b->type == 0): ?>
						<b class="text-success">+<?php echo number_format($b->import,2,',','.'); ?> €</b>
						<?php $total += $b->import ?>
					<?php endif ?>
					
				</td>
				<td class="text-center">
					<?php if ( preg_match('/SALDO INICIAL/i', $b->comment) ): ?>
						<input class="form-control text-center saldoInicial" type="number" step="0.01" name="import" value="<?php echo $total; ?>" data-type="<?php echo $b->typePayment; ?>" data-id="<?php echo $b->id; ?>">
					<?php else: ?>
						<b><?php echo number_format($total,2,',','.'); ?> €</b>
					<?php endif ?>
					
				</td>
				
				<td class="text-center" style="padding: 8px 5px!important">
					<?php echo $b->comment ?>
				</td>
				
				
			</tr>
		<?php endforeach ?>
	</tbody>			
</table>
<script type="text/javascript">
	$(document).ready(function() {
		$('.saldoInicial').change(function(event) {

			var type = $(this).attr('data-type');
			var id = $(this).attr('data-id');
			$.get('/admin/bank/updateSaldoInicial/'+id+'/'+type+'/'+$(this).val(), {type: type, importe: $(this).val() }, function(data) {
				if (data == "OK") {
					location.reload();
				}
			});
			
		});
	});
</script>
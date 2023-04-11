<div class="row">
    <div class="col-md-12 push-30">
        <div class="col-md-12">
		    <div class="row">

		        	<div class="col-xs-12 col-md-12 push-20">
		        		<h3 class="text-center">
		        			Reparto de Beneficios por tipo
		        		</h3>
		        	</div>
		        	<div class="clear"></div>
		        		
		        	<div class="col-md-12 col-xs-12 push-20">
		        		<table class="table table-condensed table-hover">
							<thead>
								<th class="text-center bg-complete text-white font-s12">Tipo</th>
								<th class="text-center bg-complete text-white font-s12">% Jorge</th>
								<th class="text-center bg-complete text-white font-s12">% Jaime</th>
							</thead>
							<tbody>
								<?php foreach ($typesApto as $typeApto): ?>
									<tr>
										<td><?php echo $typeApto->name ?></td>
										<td>
											<input class="percentage percentJorge-<?php echo $typeApto->id?>" type="text" name="Jorge" data-id="<?php echo $typeApto->id ?>" value="<?php echo $typeApto->PercentJorge?>" style="width: 100%;text-align: center;border-style: none none ">
										</td>
										<td><input class="percentage percentJaime-<?php echo $typeApto->id?>" type="text" name="Jaime" data-id="<?php echo $typeApto->id ?>" value="<?php echo $typeApto->PercentJaime?>" style="width: 100%;text-align: center;border-style: none none "></td>
									</tr>
								<?php endforeach ?>
							</tbody>
		        		</table>
		        	</div>
		    </div>
		</div>
	</div>
</div>
<script>
	
	$(document).ready(function() {

		$('.percent').change(function(event) {

			console.log('prueba');

			var id = $(this).attr('data-id');
			var tipo = $(this).attr('name');
			var percent = $(this).val();

			$.get('/admin/apartamentos/update-Percent', {  id: id, tipo: tipo, percent: percent}, function(data) {
				alert('cambiado');
			});
		});
	});

</script>
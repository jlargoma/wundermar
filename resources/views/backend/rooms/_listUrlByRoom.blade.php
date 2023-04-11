<?php if (count($urls) > 0): ?>
	<h3 class="text-center font-w300" style="letter-spacing: -2px;"> LISTADO URLS DISPONIBLES</h3>
	
	<table class="table table-striped table-condesed">
		<tr>
			<th class="text-center bg-complete text-white font-s12">
				PISO
			</th>
			<th class="text-center bg-complete text-white font-s12">
				URL
			</th>
			<th class="text-center bg-complete text-white font-s12">
				Acciones
			</th>
		</tr>
		<?php foreach ($urls as $key => $url): ?>
			<tr>
				<td class="text-center" style="padding: 5px!important;">
					<b><?php echo $url->room->nameRoom; ?></b>
				</td>
				<td class="text-left" style="padding: 5px!important;">
					<?php echo substr($url->url, 0, 65); ?>...
				</td>
				<td class="text-center">
					<button class="btn btn-xs btn-danger deleteUrl" data-id="<?php echo $url->id; ?>" data-idRoom="<?php echo $url->room_id; ?>">
						<i class="fa fa-times"></i>
					</button>
				</td>
			</tr>
		<?php endforeach ?>
	</table>
	
<?php else: ?>
	<h3 class="text-center font-w300" style="letter-spacing: -2px;"> NO HAY URLS DISPONIBLES</h3>
<?php endif ?>
<script type="text/javascript">
	$('.deleteUrl').click(function(event) {
		var id = $(this).attr('data-id');
		var idRoom = $(this).attr('data-idRoom');

		$.get( '/ical/urls/deleteUrl' , { id: id }, function(data) {
			$('#listUrl').empty().load('/ical/urls/getAllUrl/'+idRoom);
		});
		
	});
</script>
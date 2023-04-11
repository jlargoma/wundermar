@extends('layouts.admin-master')

@section('title') Liquidacion de Apartamentos @endsection

@section('externalScripts') 
	


@endsection


@section('content')
<?php use \Carbon\Carbon; ?>

<div class="container-fluid padding-5 sm-padding-10">

	<div class="col-md-12 text-center"> 
		<h2>
			Liquidacion de Apartamentos <?php echo $temporada->copy()->format('Y')."-".$temporada->copy()->AddYear()->format('Y') ?>
			<select id="date" >
				<?php $fecha = $temporada->copy()->SubYear(2); ?>
				<?php if ($fecha->copy()->format('Y') < 2015): ?>
					<?php $fecha = new Carbon('first day of September 2015'); ?>
				<?php else: ?>
					
				<?php endif ?>
			
                <?php for ($i=1; $i <= 4; $i++): ?>                           
                    <option value="<?php echo $fecha->copy()->format('Y'); ?>" {{ $temporada->copy()->format('Y') == $fecha->copy()->format('Y') ? 'selected' : '' }}>
                        <?php echo $fecha->copy()->format('Y')."-".$fecha->copy()->addYear()->format('Y'); ?> 
                    </option>
                    <?php $fecha->addYear(); ?>
                <?php endfor; ?>
            </select>
		</h2>
	</div>
	<div class="col-md-4 col-md-offset-2">
	   <table class="table table-hover demo-table-search table-block" id="tableWithSea">
	      <thead>
	          
	          <th class ="text-center bg-complete text-white"> PVP    </th>
	          <th class ="text-center bg-complete text-white"> Pendiente   </th>
	          <th class ="text-center bg-complete text-white"> Beneficio    </th>
	          <th class ="text-center bg-complete text-white"> Ben    </th>
	          <th class ="text-center bg-complete text-white"> Costes    </th>
	      </thead>
	      <tbody>
	        <tr>
	          
	          <td><?php echo number_format(array_sum($apartamentos["pvp"]),2,',','.')?>€</td>
	          <td><?php echo number_format(array_sum($pendientes),2,',','.')?>€</td>
	          <td><?php echo number_format(array_sum($apartamentos["beneficio"]),2,',','.')?>€</td>
	          <td><?php echo number_format(array_sum($apartamentos["beneficio"])/array_sum($apartamentos["pvp"])*100,2,',','.')?>€</td>
	          <td><?php echo number_format(array_sum($apartamentos["costes"]),2,',','.')?>€</td>
	        </tr>
	      </tbody>
	   </table>
	</div>
	<div style="clear: both;"></div>
    <div class="row">
        <div class="col-md-6">
			<div class="pull-left">
			        <div class="col-xs-12 ">
			            <input type="text" id="search-tableLiquidacion" class="form-control pull-right" placeholder="Buscar">
			        </div>
			    </div>
			
			    <div class="clearfix"></div>
		    <div class="tab-pane active" id="tabPrices">
		        <table class="table table-hover demo-table-search table-responsive" id="tableWithSearchLiquidacion" >
		        	<thead>
		        		<th class ="text-center bg-complete text-white">Apto</th>
		        		<th class ="text-center bg-complete text-white">Noches</th>
		        		<th class ="text-center bg-complete text-white">PVP</th>
		        		<th class ="text-center bg-complete text-white">Pendiente</th>
		        		<th class ="text-center bg-complete text-white">Beneficio</th>
		        		<th class ="text-center bg-complete text-white">%Ben</th>
		        		<th class ="text-center bg-complete text-white">Costes</th>
		        	</thead>
		        	<tbody>
		        		<?php foreach ($rooms as $room): ?>
		        			<?php if (isset($apartamentos["noches"][$room->id])): ?>
		        				<tr>
		        					<td class="text-center"><?php echo $room->name ?></td>
		        					<td class="text-center"><?php echo $apartamentos["noches"][$room->id]?></td>
		        					<td class="text-center"><?php echo number_format($apartamentos["pvp"][$room->id],2,',','.')?>€</td>
		        					<td class="text-center">
		        						<?php if (isset($pendientes[$room->id])): ?>
		        							<?php echo number_format($pendientes[$room->id],2,',','.') ?>€
		        						<?php else: ?>
		        							-----
		        						<?php endif ?>
		        					</td>
		        					<td class="text-center"><?php echo number_format($apartamentos["beneficio"][$room->id],2,',','.')?>€</td>
		        					<td class="text-center"><?php echo number_format($apartamentos["beneficio"][$room->id]/$apartamentos["pvp"][$room->id]*100,2,',','.')?>%</td>
		        					<td class="text-center"><?php echo number_format($apartamentos["costes"][$room->id],2,',','.')?>€</td>
		        				</tr>
		        			<?php endif ?>
		        			
		        		<?php endforeach ?>
		        	</tbody>
		        </table>
		    </div>
        </div>
        <div class="col-md-6">
        	<canvas id="mixed-chart" ></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript"></script>
<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/lodash.min.js"></script>

<script src="/assets/plugins/moment/moment.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>



<script type="text/javascript">


	$(document).ready(function() {
		
		$('#date').change(function(event) {
			var year = $(this).val();
			window.location = '/admin/liquidacion-apartamentos/'+year;
		});

		new Chart(document.getElementById("mixed-chart"), {
		    type: 'bar',
		    data: {
		      labels: [
		      	<?php foreach ($rooms as $room): ?>
		      		<?php echo "'".$room->name."'," ?>
		      	<?php endforeach ?>],
		      datasets: [		     
		        {
		        	label: "PVP",
		        	type: "line",
		        	borderColor: "#8e5ea2",
		        	data: [
		        		<?php foreach ($rooms as $key => $room): ?>
		        			<?php if (isset($apartamentos["pvp"][$room->id])): ?>
		        				<?php echo "'".$apartamentos["pvp"][$room->id]."'"?>,
		        			<?php else: ?>
		        				'0',
		        			<?php endif; ?>
		        		<?php endforeach ?>
		        		],
		        	fill: false,
		        },		        
		        
		        {
		          label: "Beneficio",
		          type: "bar",
		          backgroundColor: "rgba(0,0,0,0.2)",
		          backgroundColorHover: "#3e95cd",
		          data: [
		          		<?php foreach ($rooms as $key => $room): ?>
		          			<?php if (isset($apartamentos["beneficio"][$room->id])): ?>
		          				<?php echo "'".$apartamentos["beneficio"][$room->id]."'"?>,
		          			<?php else: ?>
		          				'0',
		          			<?php endif; ?>
		          		<?php endforeach ?>
		          		],
		        }
		      ]
		    },
		    options: {
		      title: {
		        display: true,
		        text: 'Relacion de PVP y Beneficio'
		      },
		      legend: { display: true },
		      elements: {
		              line: {
		                tension: 0
		              }
		      }
		    }
		});
	});
</script>
@endsection
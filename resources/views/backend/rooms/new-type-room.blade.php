
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">

<div class="row">
    <div class="col-md-12 push-30">
        <div class="col-md-12">
		    <div class="row">
		        <div class="block bg-white" style="padding: 20px;">
		        	<div class="col-xs-12 col-md-12 push-20">
		        		<h3 class="text-center">
		        			Formulario para añadir Tipo Apartamento
		        		</h3>
		        	</div>
		        	<div class="clear"></div>
		        	<div class="col-xs-12 col-md-12 push-20">
		        		<p>Tipo de apartamento para el calculo del porcentaje de division de beneficios</p>
		        	</div>
		        	<form class="form-horizontal" action="{{ url('/admin/apartamentos/create-type') }}" method="post">
		        		<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
		                <div class="col-md-7 col-xs-9 push-20">
		                    <div class="col-md-6  push-20">
		                        <div class="form-material">
		                            <input class="form-control" type="text" id="name" name="name" required>
		                            <label for="nombre">Tipo de  Apartamento</label>
		                        </div>
		                    </div>
		                </div>
		                <div class="col-xs-5 col-md-3" style="border-left: 1px solid black">
    		        		<label for="propietario">Existentes</label>
    		        		<?php foreach ($types as $type): ?>
                        		<p><?php echo $type->name ?></p>
                        	<?php endforeach ?>                      
    		        	</div>
		                <div class="col-md-12 col-xs-12 push-20 text-center">
							<button class="btn btn-success" type="submit">
	        					<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
	        				</button>
						</div>
		        	</form>
		        </div>
		    </div> 
        </div>
    </div>
</div>



<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script>
    jQuery(function () {
        App.initHelpers(['datepicker', 'select2','summernote','ckeditor']);
    });
</script>
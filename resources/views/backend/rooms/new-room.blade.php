
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
		        			Formulario para añadir Apartamento
		        		</h3>
		        	</div>
		        	<form class="form-horizontal" action="{{ url('/admin/apartamentos/create') }}" method="post">
		        		<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
		        		
		                <div class="col-md-12 col-xs-12 push-20">
		                    <div class="col-md-6  push-20">
		                        <div class="form-material">
		                            <input class="form-control" type="text" id="name" name="name" required>
		                            <label for="nombre">Nombre del Apartamento</label>
		                        </div>
		                    </div>
		                    <div class="col-md-6  push-20">
		                        <div class="form-material">
		                        <input class="form-control" type="text" id="nameRoom" name="nameRoom" required>
		                            <label for="propietario">Nombre Tecnico</label>
		                        </div>
		                    </div>
		                    <div class="col-md-6  push-20 ">
		                        <div class="form-material">
		                            <select class="js-select2 form-control" id="propietario" name="propietario" style="width: 100%; z-index: 1500" data-placeholder="Propietario..." required>
		                            	<?php foreach (\App\User::whereIn('role',['admin', 'subadmin', 'propietario'])->get() as $user): ?>
		                            		<option value="<?php echo $user->id ?>"><?php echo $user->name ?></option>
		                            	<?php endforeach ?>
	                            	</select>
		                            <label for="propietario">Propietario</label>
		                        </div>
		                    </div>
		                    <div class="col-md-6  push-20 ">
		                        <div class="form-material">
		                            <select class="js-select2 form-control" id="type" name="type" style="width: 100%; z-index: 1500" data-placeholder="Propietario..." required>
		                            	<?php foreach ($types as $type): ?>
		                            		<option value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
		                            	<?php endforeach ?>
	                            	</select>
		                            <label for="type">Tipo de Apartamento</label>
		                        </div>
		                    </div>
		                    <div class="col-md-6  push-20">
		                        <div class="form-material">		                     
		                            <input type="radio" name="lujo" value="0">No
		                            <input type="radio" name="lujo" value="1">Si 
	                            	<label>Lujo</label>
		                        </div>
		                    </div>
		                </div>

		                <div class="col-md-12 col-xs-12 push-20">
		                	
		                    <div class="col-md-6  push-20">
		                        <div class="form-material">		                     
		                            <select class="js-select2 form-control" id="size" name="size" style="width: 100%; z-index: 1500" required>
		                            	<?php foreach ($sizes as $size): ?>
		                            		<option value="<?php echo $size->id ?>"><?php echo $size->name ?></option>
		                            	<?php endforeach ?>
	                            	</select>
		                            <label for="size">Tamaño</label>
		                        </div>
		                    </div>
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
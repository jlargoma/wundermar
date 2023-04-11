<link href="/assets/plugins/summernote/css/summernote.css" rel="stylesheet" type="text/css" media="screen">
<?php 	use \Carbon\Carbon;
		setlocale(LC_TIME, "ES"); 
        setlocale(LC_TIME, "es_ES"); 
 ?>
 <style>
 	.note-editor, .note-editable{
 	    min-height: 500px!important;
 	}
 	.dropdown-toggle > i{
 		font-size: 10px!important;
 	}
 </style>
<div class="modal-content">
	<div class="modal-header clearfix text-left">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-close fs-14" style="font-size: 40px!important;color: black!important"></i>
		</button>
		<h5>Mensaje para <span class="semi-bold"><?php echo $user->name ?></span></h5>
	</div>
	<div class="modal-body">
		<div class="loading" style="display: none;  position: absolute;top: 0;width: 100%;background-color: rgba(255,255,255,0.6);z-index: 15;min-height: 750px;left: 0;padding: 210px 0;">
			<div class="col-xs-12 text-center sending" style="display: none;">
				<i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br>
				<h2 class="text-center">ENVIANDO</h2>
			</div>

			<div class="col-xs-12 text-center sended" style="display: none;">
				<i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br>
				<h2 class="text-center">ENVIANDO</h2>
			</div>
		</div>
		<form  action="{{ url('/admin/apartamentos/send/email/owned') }}" method="post" id="form-email">
			<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
			<input type="hidden" name="user" value="<?php echo $user->id ?>">
			
			<div class="col-md-12 push-40">
		        <div class="summernote-wrapper" style="margin-bottom: 30px;">
		          <div id="summernote">
					Hola <b><?php echo $user->name ?></b>, como estás.<br><br>

					Te hago llegar este email con los datos para que puedas ver las reservas de tu apartamento así como los importes que se van generando.<br><br>

					A través de este área privada a la que solo puedes acceder con usuario contraseña, cada  propietario recibe información actualizada sobre el plannning de ocupación de su apartamento de manera que tenga visibilidad total sobre el progreso de la temporada y del dinero que le corresponde por cada reserva. <br><br>

					<b>Si no dispones de contraseña aún, debes ingresar en esta url:</b><br><br>
					<a href="https://www.apartamentosierranevada.net/admin/propietario/create/password/<?php echo base64_encode($user->email) ?>">
						https://www.apartamentosierranevada.net/admin/propietario/create/password/<?php echo base64_encode($user->email) ?>
					</a><br><br>

					Despues debes acceder aquí para ver tus resumenes:<br><br>

					<?php foreach ($rooms as $key => $room): ?>
						<a href="https://www.apartamentosierranevada.net/admin/propietarios/dashboard/<?php echo $room->nameRoom; ?>">
							https://www.apartamentosierranevada.net/admin/propietarios/dashboard/<?php echo $room->nameRoom; ?>
						</a><br>
					<?php endforeach ?>
					<br>

					También os adjunto en este email el contrato de colaboración de esta temporada para vuestra revisión y firma si estáis conformes<br><br>

					Muchas gracias por vuestra confianza, espero que este año también quedemos todos satisfechos y podamos seguir creciendo juntos.<br><br>

					Un abrazo <br>
					Jorge Largo

		          </div>
		        </div>
	        </div>
	        <div class="col-md-12">
				<div class="col-md-6">
					<label class="inline">Enviar contrato adjunto</label>
                    <input type="checkbox" name="attachment" data-init-plugin="switchery" data-size="small" data-color="primary" checked="checked" />
				</div>
			</div>
	        <div class="wrapper push-20" style="text-align: center;">
	        	<button type="submit" class="btn btn-lg btn-success"><i class="fa fa-paper-plane-o" aria-hidden="true"></i> Enviar</button>
	        </div>
	    </form>
	</div>
</div>

<script src="/assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="/assets/plugins/modernizr.custom.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="/assets/plugins/bootstrapv3/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-bez/jquery.bez.min.js"></script>
<script src="/assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-actual/jquery.actual.min.js"></script>
<script src="/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script type="text/javascript" src="/assets/plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript" src="/assets/plugins/classie/classie.js"></script>
<script src="/assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>
<script src="/assets/plugins/bootstrap3-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script type="text/javascript" src="/assets/plugins/jquery-autonumeric/autoNumeric.js"></script>
<script type="text/javascript" src="/assets/plugins/dropzone/dropzone.min.js"></script>
<script type="text/javascript" src="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>
<script src="/assets/plugins/bootstrap-form-wizard/js/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/assets/plugins/summernote/js/summernote.min.js" type="text/javascript"></script>
<script src="/assets/plugins/moment/moment.min.js"></script>
<script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="/assets/plugins/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
<script src="/assets/plugins/bootstrap-typehead/typeahead.bundle.min.js"></script>
<script src="/assets/plugins/bootstrap-typehead/typeahead.jquery.min.js"></script>
<script src="/assets/plugins/handlebars/handlebars-v4.0.5.js"></script>
<!-- END VENDOR JS -->
<!-- BEGIN CORE TEMPLATE JS -->
<script src="/pages/js/pages.min.js"></script>
<!-- END CORE TEMPLATE JS -->
<!-- BEGIN PAGE LEVEL JS -->
<script src="/assets/js/form_elements.js" type="text/javascript"></script>
<script src="/assets/js/scripts.js" type="text/javascript"></script>
<script type="text/javascript">

	function sending(){
		$('.loading').show();
		$('.loading .sending').show();
	}

	function sended(){
		$('.loading .sending').hide();
		$('.loading .sendend').show();
	}


	$('#form-email').submit(function(event) {
		event.preventDefault();

		var formURL    = $(this).attr("action");
		var token      = $('input[name="_token"]').val();
		var textEmail  = $('.note-editable').html();
		var user       = $('input[name="user"]').val();
		var attachment = $('input[name="attachment"]').is(':checked');
            
            if (attachment == true) {
                type = 1;
            }else{
                type = 0;
            }

		$.post(formURL, {_token: token, textEmail: textEmail ,user: user, attachment: type}, function(data) {

			$('.pg-close').trigger('click');
			location.reload();

		});
	});
</script>
<div class="row" style="padding: 20px; border: 2px solid #000;">
    <div class="col-xs-12">
        <form action="{{ url('/admin/cashBox/create') }}" method="post" id="formAddCashBox">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" id="type_payment" name="type_payment" value="0" data-name="Caja Jorge">
            <div class="col-xs-12 col-md-10">

                <div class="col-xs-12 col-lg-2 col-md-3" >
                    <label for="date">fecha</label>
                     <div id="datepicker-component" class="input-group date col-xs-12">
                          <input type="text" class="form-control" name="fecha" id="fecha" value="<?php echo date('d/m/Y') ?>" style="font-size: 12px">
                          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    
                </div>
                <div class=" col-xs-12 col-lg-3 col-md-3">
                  <label for="concept">Concepto</label>
                  <input  type="text" class="form-control" name="concept" id="concept" />
                </div>
                <div class="col-xs-12 col-md-3">
                    <label for="type">T. Mov</label>
                    <select class="js-select2 form-control" id="type" name="type" style="width: 100%;" data-placeholder="Seleccione un tipo" required >
                        <option></option>
                        <option value="1">TRASPASO (SALIDA)</option>
                        <option value="0">TRASPASO (ENTRADA)</option>
                    </select>
                </div>
              
                <div class="col-xs-12 col-md-3">
                    <label for="import">Importe</label>
                    <input  type="number" step="0.01" name="import" id="import" class="form-control"  />
                </div>
               
              <div class="col-md-6 col-xs-12" style="margin:1em 0;">
                  <label for="comment">Comentario</label>
                  <textarea class="form-control" name="comment" id="comment" rowspan="1"></textarea>
                </div>
            </div>
            <div class="col-md-2 form-group text-center">
              <button class="btn btn-lg btn-success">Añadir</button>
            </div>
               
        </form>
    </div>
</div>
</div>
<script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="/assets/plugins/moment/moment.min.js"></script>

<script type="text/javascript">
	$('#datepicker-range, #datepicker-component, #datepicker-component2').datepicker();
	$(document).ready(function() {

		$('#formAddCashBox').submit(function(event) {
		    event.preventDefault();
		    var url        = $('#formAddCashBox').attr('action');
		    var _token = $('input[name="_token"]').val();
		    var fecha = $('input[name="fecha"]').val();
		    var concept = $('#concept').val();
		    var type = $('#type').val();
		    var type_payment = $('#type_payment').val();
		    var importe = $('#import').val();
		    var comment = $('#comment').val();
		  


		    $.post( url , { _token: _token, fecha: fecha, concept: concept, type: type, type_payment: type_payment, importe: importe, comment: comment }, function(data) {

		    		if (data == 'OK') {
		    			location.reload();
		    		}

		    });
		    

		}); 
	});
</script>
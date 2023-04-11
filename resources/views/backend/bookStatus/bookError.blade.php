<div class="col-padding">
  <div class="heading-block center nobottomborder nobottommargin" style="margin: 15px;">
    <h2 ><i class="fa fa-exclamation-triangle"></i>&nbsp; Uppss! Lo sentimos</h2>
    <span >No tenemos servicio para las fechas especificadas</span><br>
    <span >Por favor intentelo de nuevo con otras fechas.</span>
  </div>

  <button class="btn btn-danger btn-lg btn-cons  text-white center hvr-grow-shadow btn-back-calculate">VOLVER</button>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		 $('.btn-back-calculate').click(function(event) {
            $('#content-book-response .back').empty();
            $("#content-book-response .back").hide();
            $("#content-book-response .front").show();
        });
	});
</script>
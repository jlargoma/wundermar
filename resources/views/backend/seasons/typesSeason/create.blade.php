<div class="row">
    <h5 class="text-center">Añade un nuevo tipo de temporada</h5>
    <form role="form" action="{{ url('/admin/temporadas/create-type') }}" method="post">
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <div class="col-md-4 col-md-offset-3 col-xs-12">
            <input type="text" class="form-control" name="name" placeholder="nombre" required="" aria-required="true" aria-invalid="false">
        </div>
        <div class="col-md-3 col-xs-12">
            <button class="btn btn-complete" type="submit">Guardar</button>
        </div>
    </form>

</div>
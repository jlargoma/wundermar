<link rel="stylesheet" href="{{ asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min
.css') }}">
<link rel="stylesheet" href="{{ asset('/admin-css/assets/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker
.min.css') }}">
<link rel="stylesheet" href="{{ asset('/admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('/admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<style type="text/css">
    .input-group{
        width: 100%;
    }
</style>
<div class="container-fixed-lg">
    <div>
        <div style="width: 100%">
            <!-- START PANEL -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Actualizar usuario
                    </div>
                </div>
                <div class="panel-body">
                    <form role="form"  action="{{ url('/admin/usuarios/saveupdate') }}" method="post">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="id" value="<?php echo $user->id ?>">
                        <div class="input-group transparent">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="name" placeholder="Nombre" required="" aria-required="true" aria-invalid="false" value="<?php echo $user->name?>">

                        </div>
                        <br>
                        <div class="input-group">
                            <label>Correo</label>
                            <input type="email" class="form-control" name="email" placeholder="Email" required="" aria-required="true" aria-invalid="false" value="<?php echo $user->email?>">
                        </div>
                        <br>
                        <div class="input-group">
                            <label>Telefono</label>
                            <input type="number" class="form-control" name="phone" placeholder="Telefono" required="" aria-required="true" aria-invalid="false" value="<?php echo $user->phone ?>">
                        </div>
                        <br>
                        <div class="input-group">
                            <label>Cargo</label>
                            <select class="form-control full-width" data-init-plugin="select2" name="role">
                                <option value="<?php echo $user->role ?>" default><?php echo $user->role ?></option>
                                <option value="admin">admin</option>
                                <option value="subadmin">SubAdmin</option>
                                <option value="limpieza">Limpieza</option>
                                <option value="agente">Agente</option>
                                <option value="propietario">Propietario</option>
                                <option value="recepcionista">Recepcionista</option>
                            </select>
                        </div>
                        <br>
                        <div class="input-group">
                            <label>Contraseña</label>
                            <input type="password" class="form-control" name="password"  aria-required="true" aria-invalid="false" value="">
                        </div>
                        <br>
                        <div class="input-group">
                            <label>Razon social</label>
                            <input type="text" class="form-control" name="name_business" placeholder="Razon social" value="<?php echo $user->name_business?>">

                        </div>
                        <br>
                        <div class="input-group">
                            <label>NIF/CIF/DNI/NIE</label>
                            <input type="text" class="form-control" name="nif_business" placeholder="NIF/CIF/DNI/NIE" value="<?php echo $user->nif_business?>">

                        </div>
                        <br>
                        <div class="input-group">
                            <label>Dirección</label>
                            <input type="text" class="form-control" name="address_business" placeholder="Dirección"value="<?php echo $user->address_business?>">
                        </div>
                        <br>
                        <div class="input-group">
                            <label>Codigo postal</label>
                            <input type="text" class="form-control" name="zip_code_business" placeholder="Dirección"value="<?php echo $user->zip_code_business?>">
                        </div>
                        <br>

                        <div class="input-group">
                            <button class="btn btn-complete" type="submit">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END PANEL -->
        </div>
        <!-- END PANEL -->
    </div>
</div>


<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script>
  jQuery(function () {
    App.initHelpers(['datepicker', 'select2','summernote','ckeditor']);
  });
</script>
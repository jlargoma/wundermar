<div class="boxUserEdit">
  <h2>Actualizar usuario</h2>
  <form role="form"  action="{{ url('/admin/usuarios/saveupdate') }}" method="post">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="<?php echo $user->id ?>">
    <div class="col-field-2">
      <label>Nombre</label>
      <input type="text" class="form-control" name="name" placeholder="Nombre" required="" aria-required="true" aria-invalid="false" value="<?php echo $user->name ?>">
    </div>
    <div class="col-field-2">
      <label>Correo</label>
      <input type="email" class="form-control" name="email" placeholder="Email" required="" aria-required="true" aria-invalid="false" value="<?php echo $user->email ?>">
    </div>
   
    <div class="col-field-2">
      <label>Telefono</label>
      <input type="number" class="form-control" name="phone" placeholder="Telefono" required="" aria-required="true" aria-invalid="false" value="<?php echo $user->phone ?>">
    </div>
    <div class="col-field-2">
      <label>DNI</label>
      <input type="text" class="form-control" name="nif" placeholder="DNI" value="<?php echo $user->nif ?>">
    </div>
    <div class="col-field-2">
      <label>Cta. Cte.</label>
      <input type="text" class="form-control" name="iban" placeholder="Cta.Cte./IBAN" value="<?php echo $user->iban ?>">
    </div>
    <div class="col-field-2">
      <label>Cargo</label>
      <select class="form-control full-width" name="role">
        <option value="<?php echo $user->role ?>" default><?php echo $user->role ?></option>
        <option value="admin">admin</option>
        <option value="subadmin">SubAdmin</option>
        <option value="limpieza">Limpieza</option>
        <option value="agente">Agente</option>
        <option value="propietario">Propietario</option>
        <option value="recepcionista">Recepcionista</option>
        <option value="conserje">Conserje</option>
      </select>
    </div>
   
    <div class="col-field-2">
      <label>Contraseña</label>
      <input type="password" class="form-control" name="password"  aria-required="true" aria-invalid="false" value="" autocomplete="false">
    </div>
    <div class="col-field-2">
      <label>Repetir Contraseña</label>
      <input type="password" class="form-control" name="repassword"  aria-required="true" aria-invalid="false" value="" autocomplete="false">
    </div>
    <div class="col-field-2">
      <label>Razon social</label>
      <input type="text" class="form-control" name="name_business" placeholder="Razon social" value="<?php echo $user->name_business ?>">
    </div>
    <div class="col-field-2">
      <label>NIF/CIF/DNI/NIE</label>
      <input type="text" class="form-control" name="nif_business" placeholder="NIF/CIF/DNI/NIE" value="<?php echo $user->nif_business ?>">
    </div>
    <div class="col-field-2">
      <label>Dirección</label>
      <input type="text" class="form-control" name="address_business" placeholder="Dirección"value="<?php echo $user->address_business ?>">
    </div>
   
    <div class="col-field-2">
      <label>Codigo postal</label>
      <input type="text" class="form-control" name="zip_code_business" placeholder="Dirección"value="<?php echo $user->zip_code_business ?>">
    </div>
   

    <div class="col-btn">
      <button class="btn btn-complete" type="submit">Guardar</button>
    </div>
   
  </form>
  <form role="form"  action="{{ url('/admin/usuarios/delete') }}" method="post" onsubmit="return confirm('Eliminar usuario de manera permanente?')">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="<?php echo $user->id ?>">
     <div class="col-btn">
      <button class="btn btn-danger" type="submit">Eliminar Usuario</button>
    </div>
    </form>
</div>
<style>
  .boxUserEdit{
    padding: 0 1em;
  }
  .boxUserEdit h2 {
    margin: -40px 0 10px 6px;
    padding: 0px;
    font-weight: 700;
  }
  .boxUserEdit .col-btn{
    display: block;
    width: 100%;
    margin: 1em auto;
    text-align: center;
  }
  .col-field-2 {
    display: inline-block;
    width: 49%;
    padding: 6px 6px;
}
  </style>
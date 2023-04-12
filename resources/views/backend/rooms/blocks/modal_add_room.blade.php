<div class="modal fade slide-up in" id="modalNewApto" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="position: absolute; top: 0px; right: 10px; z-index: 100;">
          <i class="fa fa-close fs-20" ></i>
        </button>
        <div class="panel-body">
          <div class="panel panel-default" style="margin-top: 15px;">
            <div class="panel-heading">
              <div class="panel-title col-md-12">
                Agregar Apartamento
              </div>
            </div>
            <form role="form"  action="{{ url('/admin/apartamentos/create') }}" method="post">
              <div class="panel-body">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="row">
                  <div class=" col-md-6 mb-1em">
                    <div class="input-group transparent">
                      <span class="input-group-addon">
                        <i class="fa fa-user"></i>
                      </span>
                      <input type="text" class="form-control" name="name" placeholder="Nick" required="" aria-required="true" aria-invalid="false">
                    </div>
                  </div>
                  <div class=" col-md-6 mb-1em">
                    <div class="input-group transparent">
                      <span class="input-group-addon">
                        <i class="pg-home"></i>
                      </span>
                      <input type="text" class="form-control" name="nameRoom" placeholder="Piso" required="" aria-required="true" aria-invalid="false">
                    </div>
                  </div>
                  <div class=" col-md-3 mb-1em">
                    <div class="input-group transparent">
                      <span class="input-group-addon">
                        <i class="pg-minus_circle"></i>
                      </span>
                      <input type="text" class="form-control" name="minOcu" placeholder="Minima ocupacion" required="" aria-required="true" aria-invalid="false">
                    </div>
                  </div>
                  <div class=" col-md-3 mb-1em">
                    <div class="input-group transparent">
                      <span class="input-group-addon">
                        <i class="pg-plus_circle"></i>
                      </span>
                      <input type="text" class="form-control" name="maxOcu" placeholder="Maxima ocupacion" required="" aria-required="true" aria-invalid="false">
                    </div>
                  </div>
                  <div class=" col-md-6 mb-1em">
                    <div class="input-group">
                      <span class="input-group-addon">
                        Sitio
                      </span>
                      <select class="form-control minimal" name="site_id" required>
                        <option></option>
                        <?php foreach (\App\Sites::all() as $item): ?>
                          <option value="<?php echo $item->id ?>"><?php echo $item->name ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                  </div>
                  <div class=" col-md-4 mb-1em">
                    <div class="input-group">
                      <span class="input-group-addon">
                        Propietario
                      </span>
                      <select class="form-control minimal" name="owner" required>
                        <option></option>
                        <?php foreach (\App\User::whereIn('role', ['admin', 'subadmin', 'propietario'])->get() as $owner): ?>
                          <option value="<?php echo $owner->id ?>"><?php echo $owner->name ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                  </div>
                  <div class=" col-md-4 mb-1em">
                    <div class="input-group">
                      <span class="input-group-addon">
                        Tipo de apartamento
                      </span>
                      <select class="form-control minimal" name="type" required>
                        <option></option>
                        <?php foreach ($types as $type): ?>
                          <option value="<?php echo $type->id ?>"><?php echo $type->name ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                  </div>
                  <div class=" col-md-4 mb-1em">
                    <div class="input-group">
                      <span class="input-group-addon">
                        Tamaño de apartamento
                      </span>
                      <select class="form-control minimal" name="sizeRoom">
                        <option></option>
                        <?php foreach ($sizes as $size): ?>
                          <option value="<?php echo $size->id ?>"><?php echo $size->name ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                  </div>
                    <div class="input-group">
                      <label class="inline">Lujo</label>
                      <input type="checkbox" name="luxury" data-init-plugin="switchery" data-size="small" data-color="primary" checked="checked" />
                    </div>   
                  <div class="input-group">
                    <button class="btn btn-complete" type="submit">Guardar</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
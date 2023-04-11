<div class="row">
  <div class="col-md-12 text-center">
    <h2 class="font-w800">Temporadas</h2>
  </div>
  <div class="col-md-5">
    <div class="col-xs-12 mb-1em">
      <button class="btn btn-complete btn-inline" type="button" data-toggle="modal" data-target="#typeSeason">
        <i class="fa fa-plus"></i> TIPOLOGIA TEMP
      </button>
      <button class="btn btn-primary  btn-inline" type="button" data-toggle="modal" data-target="#season">
        <i class="fa fa-plus"></i> RANGO FECHAS
      </button>
      <button class="btn btn-secondary  btn-inline" type="button" data-toggle="modal" data-target="#temporadas">
        DEFINIR TEMPORADAS
      </button>
    </div>
    <table class="table table-hover  table-condensed table-striped" >
      <thead>
        <tr>
          <th class ="text-center hidden">id</th>
          <th class ="text-center bg-complete text-white">Tipo</th>
          <th class ="text-center bg-complete text-white">Inicio</th>
          <th class ="text-center bg-complete text-white">Fin</th>
          <th class ="text-center bg-complete text-white">Accion</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($seasonsTemp as $season): ?>
          <tr>
            <td class="text-center" style="padding: 10px!important">
              <span class="<?php echo $season->typeSeasons->name ?> font-w600"> <?php echo $season->typeSeasons->name ?></span>
            </td>
            <td class="text-center" hidden style="padding: 10px!important"><?php echo $season->id ?></td>
            <td class="text-center" style="padding: 10px!important">
              <?php echo date('d-M-Y', strtotime($season->start_date)) ?>
            </td>
            <td class="text-center" style="padding: 10px!important">
              <?php echo date('d-M-Y', strtotime($season->finish_date)) ?>
            </td>

            <td class="text-center" style="padding: 10px!important">
              <div class="btn-group">
                <button class="btn btn-primary btn-sm updateSeason" type="button"
                        data-toggle="modal" data-target="#season"
                        data-id="<?php echo $season->id ?>">
                  <i class="fa fa-edit"></i>
                </button>

                <a href="{{ url('/admin/temporadas/delete/')}}/<?php echo $season->id ?>" 
                   class="btn  btn-danger btn-sm" 
                   onclick="return confirm('¿Quieres eliminar la temporada?');">
                  <i class="fa fa-trash"></i>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
  <div class="col-md-7">
    @include('backend.seasons.calendar')
  </div>
</div>

<div class="box">
  <h2>Días minimos
    <button class="btn btn-primary" style="float:right;" type="button" data-toggle="modal" data-target="#segment">
      <i class="fa fa-plus"></i> Rango
    </button>
  </h2>
      <?php if (count($specialSegments) > 0): ?>
  <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th class="text-center">Inicio</th>
              <th class="text-center">Fin</th>
              <th class="text-center">Min Días</th>
              <th class="text-center">Accion</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($specialSegments as $segment): ?>
              <tr>
                <td class="text-center" style="padding: 12px 20px!important">
                  <?php echo $segment->start ?>
                </td>
                <td class="text-center" style="padding: 12px 20px!important">
                  <?php echo $segment->finish ?>
                </td>
                <td class="text-center" style="padding: 12px 20px!important">
                  <?php echo $segment->minDays; ?>
                  <?php if ($segment->minDays > 1): ?>
                    Días
                  <?php else: ?>
                    Día
                  <?php endif ?>
                </td>
                <td class="text-center" style="padding: 12px 20px!important">
                  <button class="btn btn-primary btn-sm updateSegment" type="button"
                          data-toggle="modal" data-target="#segment"
                          data-id="<?php echo $segment->id ?>">
                    <i class="fa fa-edit"></i>
                  </button>

                  <a class="btn btn-danger btn-sm"
                     href="{{ url('/admin/specialSegments/delete/'.$segment->id )}}"
                     title="Eliminar Segmento">
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
    </div>
      <?php else: ?>
        <h3 class="font-w300 text-center">
          No has establecido ningún Rango de días
        </h3>
      <?php endif ?>
</div>
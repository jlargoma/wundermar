<div class="box">
  <h2>Agentes - Rooms 
    <button class="btn btn-primary" style="float:right;" type="button" data-toggle="modal"
            data-target="#agentRoom">
      <i class="fa fa-plus"></i>
    </button></h2>
    <?php if (count($agentsRooms) > 0): ?>
  <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th class="text-center">ID</th>
            <th class="text-center">Agente</th>
            <th class="text-center">Apart</th>
            <th class="text-center">Agencia</th>
            <th class="text-center">Accion</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($agentsRooms as $agent): ?>
            <tr>
              <td class="text-center" style="padding: 12px 20px!important">
                <?php echo $agent->id ?>
              </td>
              <td class="text-center" style="padding: 12px 20px!important">
                <?php echo $agent->user->name ?>
              </td>
              <td class="text-center" style="padding: 12px 20px!important">
                <?php echo $agent->room->nameRoom; ?>
              </td>
              <td class="text-center" style="padding: 12px 20px!important">
                <?php echo \App\Book::getAgency($agent->agency_id) ?>
              </td>
              <td class="text-center" style="padding: 12px 20px!important">

                <a class="btn btn-danger btn-sm"
                   href="{{ url('/admin/agentRoom/delete/'.$agent->id )}}"
                   title="Eliminar">
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
        No has establecido ningún Agente para habitaciones
      </h3>
    <?php endif ?>
</div>
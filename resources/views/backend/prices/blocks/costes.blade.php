<div class="box">
  <h2>Costes Directos</h2>
  
    <h4>Nuevo Coste</h4>
    <form role="form" action="{{ route('settings.extr_price.create') }}" method="post">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="fixed" value="1">
      <div class="row">
        <div class="col-md-3">
          <input type="text" class="form-control" name="name"
                 placeholder="Nombre" required=""
                 aria-required="true" aria-invalid="false">
        </div>
        <div class="col-md-3">
          <input type="number" class="form-control" name="price"
                 placeholder="Precio" required=""
                 aria-required="true" aria-invalid="false">
        </div>
        <div class="col-md-3">
          <input type="number" class="form-control" name="cost"
                 placeholder="Coste" required=""
                 aria-required="true" aria-invalid="false">
        </div>
        <div class="col-md-3">
          <select class="form-control minimal" name="channel_group" placeholder="ZODOMUS Apto">
            <option value=""> - ZODOMUS Apto -</option>
            <?php foreach ($ch_group as $id=>$value): ?>                                   
              <option value="{{$id}}" >{{$value}}</option>
            <?php endforeach ?>
          </select>
        </div>
      </div>
      <div class="row text-center py-1">
        <button class="btn btn-complete" type="submit">Guardar</button>
      </div>
    </form>
  
  
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th class="text-center" > Nombre</th>
          <th class="text-center">PVP</th>
          <th class="text-center">Coste</th>
          <th class="text-center">ZODOMUS Apto</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($extp_fixed as $extra): ?>
          <tr>
            <td class="py-1">
              <?php echo $extra->name ?>
              <i class="fa fa-trash deleteSegment" data-id="{{$extra->id}}" data-name="{{$extra->name}}"></i>
            </td>
            <td class="text-center">
              <input class="extra-editable extra-price-<?php echo $extra->id ?>" type="text"
                     name="cost" data-id="<?php echo $extra->id ?>"
                     value="<?php echo $extra->price ?>"
                     style="width: 100%;text-align: center;border-style: none none">
            </td>
            <td class="text-center">
              <input class="extra-editable extra-cost-<?php echo $extra->id ?>" type="text"
                     name="cost" data-id="<?php echo $extra->id ?>"
                     value="<?php echo $extra->cost ?>"
                     style="width: 100%;text-align: center;border-style: none none">
            </td>
            <td>
            <select class="form-control minimal extra-editable" id="extra-apto-<?php echo $extra->id ?>" placeholder="ZODOMUS Apto"  data-id="<?php echo $extra->id ?>">
              <option value=""> - ZODOMUS Apto -</option>
              <?php foreach ($ch_group as $id=>$value): ?>                                   
                <option value="{{$id}}" @if($id==$extra->channel_group) selected @endif>{{$value}}</option>
              <?php endforeach ?>
            </select>
          </div>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>
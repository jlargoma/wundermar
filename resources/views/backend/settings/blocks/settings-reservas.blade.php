 <?php 
        $specials = ['parking_book_cost',
        'parking_book_price',
        'luxury_book_cost',
        'luxury_book_price'];
        $parking_book_cost  = ['id'=>null,'value'=>'','key'=>''];
        $parking_book_price = ['id'=>null,'value'=>'','key'=>''];
        $luxury_book_cost   = ['id'=>null,'value'=>'','key'=>''];
        $luxury_book_price  = ['id'=>null,'value'=>'','key'=>''];
        $settingS = \App\Settings::whereIn('key', $specials)->get();
        if ($settingS){
          foreach ($settingS as $s){
            ${$s->key} = ['id'=>$s->id,'value'=>$s->value,'key'=>$s->key];
          }
        }
//        dd($parking_book_cost);
?>
    
<div class="box">
  <h2>Settings - reservas </h2>
  <div class="table-responsive">
    <table class="table ">
      <thead>
        <tr>
          <th >Nombre</th>
          <th class="text-center">PVP</th>
          <th class="text-center">COST</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="py-1">
            <b>Sup Lujo</b>
          </td>
          <td class="text-center">
            <input class="setting-editable form-control" type="number" step="0.01"
                   data-code="{{ $luxury_book_price['key'] }}" 
                   data-id="{{ $luxury_book_price['key'] }}" placeholder="introduce un valor"
                   value="{{ $luxury_book_price['value'] }}" >
          </td>
          <td class="text-center">
            <input class="setting-editable form-control" type="number" step="0.01"
                   data-code="{{ $luxury_book_cost['key'] }}" 
                   data-id="{{ $luxury_book_cost['key'] }}" placeholder="introduce un valor"
                   value="{{ $luxury_book_cost['value'] }}" >
          </td>
        </tr>
        <tr>
          <td class="py-1">
            <b>Sup Park</b>
          </td>
           <td class="text-center">
            <input class="setting-editable form-control" type="number" step="0.01"
                   data-code="{{ $parking_book_price['key'] }}" 
                   data-id="{{ $parking_book_price['key'] }}" placeholder="introduce un valor"
                   value="{{ $parking_book_price['value'] }}" >
          </td>
           <td class="text-center">
            <input class="setting-editable form-control" type="number" step="0.01"
                   data-code="{{ $parking_book_cost['key'] }}" 
                   data-id="{{ $parking_book_cost['key'] }}" placeholder="introduce un valor"
                   value="{{ $parking_book_cost['value'] }}" >
          </td>
        </tr>
      </tbody>
      </table>
    
    
    <table class="table ">
      <thead>
        <tr>
          <th >Nombre</th>
          <th class="text-center">Valor</th>
        </tr>
      </thead>
      <tbody>
       <?php 
        foreach ($settingsBooks as $code => $name):
          if (in_array($code,$specials)){
            continue;
          }
        
        ?>
          <?php $setting = \App\Settings::where('key', $code)->first(); ?>
          <tr>
            <td class="py-1">
              <b><?php echo $name ?></b>
            </td>
            <td class="text-center">
              <input class="setting-editable" type="number" step="0.01"
                     data-code="{{ $code }}"
                     @if($setting != null) data-id="<?php echo $setting->id ?>" @else placeholder="introduce un valor" @endif
                     @if($setting != null) value="<?php echo $setting->value ?>" @endif
                     style="width: 100%;text-align: center; border-style: none none">
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>
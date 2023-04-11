<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th class ="text-center bg-complete text-white font-s12" style="width: 8%">#</th>
            <th class ="text-center bg-complete text-white font-s12" style="width: 20%;">APTO</th>
            <th class ="text-center bg-complete text-white font-s12" style="width: 8%">Tamaño</th>
            <th class ="text-center bg-complete text-white font-s12" style="width: 10%">Venta Instantanea</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rooms as $room): ?>
        <tr>
                <td class="text-center" >
                    <input class="orden-fast-payment order-<?php echo $room->id?>" type="number" name="orden" data-id="<?php echo $room->id ?>" value="<?php echo $room->order_fast_payment?>" style="width: 100%;text-align: center;border-style: none none">
                </td>
                <td class="text-left" >
                    <?php echo $room->name?> (<?php echo $room->nameRoom?>)
                </td>
                <td class="text-left" >
                    <?php echo $room->sizeRooms->name?>
                </td>
                <td class="text-center" >
                    <span class="input-group-addon bg-transparent">
                        <input type="checkbox" class="fastpayment" data-id="<?php echo $room->id ?>" name="fast_payment" data-init-plugin="switchery" data-size="small" data-color="primary" <?php echo ($room->fast_payment == 0) ? "" : "checked" ?>/>
                    </span>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<script>
    $('.fastpayment').change(function(event) {
      var id = $(this).attr('data-id');
      var state = $(this).is(':checked');

      if (state == true) {
        state = 1;
      }else{
        state = 0;
      }

      $.get('/admin/apartamentos/fast-payment', {  id: id, state: state}, function(data) {
        if (data == 0) {
          alert('No se puede cambiar')
          // $('.content-table-rooms').empty().load('/admin/apartamentos/rooms/getTableRooms');
        }else{
          alert('cambiado')
          // $('.content-table-rooms').empty().load('/admin/apartamentos/rooms/getTableRooms');
        }
      });
    });
    $('.orden-fast-payment').change(function(event) {
      var id = $(this).attr('data-id');
      var orden = $(this).val();

      $.get('/admin/apartamentos/update-order-payment', {  id: id, orden: orden}, function(data) {
        $('.content-aptos-table').empty().append(data);
      });

    });
</script>
<script src="{{ asset('/assets/plugins/switchery/js/switchery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/pages/js/pages.min.js') }}"></script>
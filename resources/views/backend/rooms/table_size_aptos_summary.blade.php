<table class="table table-condensed table-hover">
    <thead>
        <tr>
            <th class="text-center bg-complete text-white font-s12">Tipo</th>
            <th class="text-center bg-complete text-white font-s12">Total</th>
            <th class="text-center bg-complete text-white font-s12">En Vtn rapida</th>
            <th class="text-center bg-complete text-white font-s12">% Vtn rapida</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sizes as $key => $size)
            <tr>
            <td class="text-left">
                <b>{{ $size->name }}</b>
            </td>
            <td class="text-center"><?php echo count($size->rooms)?></td>
            <td class="text-center">
                <input class="size-aptos-avaliables" data-id="{{ $size->id }}" type="number" step="1" max="{{ $size->rooms->count() }}" value="{{ $size->num_aptos_fast_payment }}" style="width: 100%;text-align: center;border-style: none none ">
            </td>
            <td class="text-center">
                <?php
                $totalRooms = (count($size->rooms) > 0 ) ? count($size->rooms) : 1;
                $percent = ($size->num_aptos_fast_payment / $totalRooms) * 100;
                echo number_format($percent, 2, ',', '.') . "%";
                ?>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<script>
    $('.size-aptos-avaliables').change(function(event) {
      var id = $(this).attr('data-id');
      var num_aptos = $(this).val();

      $.get('/admin/sizeAptos/update-num-fast-payment', {  id: id, num_aptos: num_aptos}, function(data) {
        $('.content-sizes').empty().append(data);
      });

    });
</script>
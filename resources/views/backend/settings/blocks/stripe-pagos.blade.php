<div class="box">
  <h2 class="text-center">Weiland</h2>
  <table class="table table-hover  table-responsive">
    <thead>
      <tr>
        <th class="text-left">Sitio</th>
        <th class="text-center">%<br/><small>Primer pago</small></th>
        <th class="text-center">Dias<br/><small>Segundo pago</small></th>
        <th class="text-center">Fianza</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($payment_rule as $r)
        <?php $data = json_decode($r->content); ?>
      <tr>
        <td><?php echo isset($sites[$r->site_id]) ? $sites[$r->site_id] : 'todos' ?></td>
        <td class="text-center">
          <input class="form-control Weiland" id="w_percent_{{$r->id}}" type="text" data-id="{{$r->id}}" value="{{$data->percent ?? 0}}">
        </td>
        <td class="text-center">
          <input class="form-control Weiland" id="w_days_{{$r->id}}" type="text" data-id="{{$r->id}}" value="{{$data->days ?? 0}}">
        </td>
        <td class="text-center">
          <input class="form-control Weiland checked" id="w_fianza_{{$r->id}}" type="checkbox" data-id="{{$r->id}}"
                 <?php if(isset($data->fianza) && $data->fianza == 1) echo 'checked'; ?>>
        </td>
      </tr>
      @endforeach

    </tbody>
  </table>

</div>
<div class="table-responsive">
  <table class="table table-data table-striped" id="tableInvoices" >
    <thead>
      <tr>
        <th>F. Fact</th>
        <th># Fact</th>
        <th>Edificio</th>
        <th>Cliente</th>
        <th>DNI</th>
        <th>Importe</th>
        <th>Acciones</th>
      </tr>
    </thead>
  <tbody>
    @if($invoices)
    @foreach($invoices as $item)
    <tr>
      <td class="text-left" >
        <?php echo convertDateToShow_text($item->date, true); ?>
      </td>
      <td class="text-center"><?php echo $item->num?></td>
      <td class="text-center"><?php show_isset($sites,$item->site_id);?></td>
      <td class="text-center"><?php echo $item->name?></td>
      <td class="text-center"><?php echo $item->nif?></td>
      <td class="text-center">{{moneda($item->total_price,true,2)}}</td>
      <td class="text-center font-s16">
        <div class="btn-group">
          <a href="{{ route('invoice.edit',$item->id) }}" class="btn btn-xs btn-complete"><i class="fa fa-pencil"></i></a>
          <a href="{{ route('invoice.view',$item->id) }}" class="btn btn-xs btn-primary" target="_black"><i class="fa fa-eye"></i></a>
          <a href="{{ route('invoice.downl',$item->id) }}" class="btn btn-xs btn-success" target="_black"><i class="fa fa-download"></i></a>
        </div>
      </td>
    </tr>
    @endforeach
    @endif

  </tbody>
</table>
</div>
<h3>Extras PVP - Resumen anual</h3>
<div class=" table-responsive">
  <table class="table table-resumen">
    <thead>
      <tr class="resume-head">
        <th class="static">Concepto</th>
        <th class="first-col">Total</th>
        @foreach($lstMonths as $k => $month)
        <th>{{getMonthsSpanish($month['m'])}}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($extrasGroup as $k=>$item)
      <tr>
        <td class="static">{{$extTyp[$k]}}</td>
        <?php $auxClass = ' class="first-col" '; ?>
        @foreach($item as $month=>$val)
        <td {{$auxClass}} >{{moneda($val,false)}}</td>
        <?php $auxClass = ''; ?>
        @endforeach
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
 
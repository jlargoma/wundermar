<?php //dd($extrasList); ?>
<h3>Resumen Extras PVP</h3>
<div class=" table-responsive">
  <table class="table table-resumen t-r-extras">
    <thead>
      <tr class="resume-head">
        <th class="static">Concepto</th>
        <th class="first-col"></th>
        <th>Total</th>
        @foreach($lstMonths as $k => $month)
        <th>{{$month}}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($extrasList as $extra_id => $item)
      <tr class="text-center">
        <td class="static">{{$extraTit[$extra_id]}}</td>
        <td class="first-col"></td>
        <th class="text-center ">  
          {{moneda($item[0])}}
        </th>
        @foreach($lstMonths as $k => $month)
        <th class="text-center">{{moneda( $item[$k],false)}}</th>
        @endforeach
      </tr>
      @endforeach

    </tbody>
  </table>
</div>

<h3>Resumen Edificios</h3>
<div class=" table-responsive">
  <table class="table table-resumen">
    <thead>
      <tr class="resume-head">
        <th class="static">Concepto</th>
        <th class="first-col"></th>
        <th >Total</th>
        @foreach($lstMonths as $k => $month)
        <th>{{$month}}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($siteRooms as $site => $data)
      <tr>
        <td class="static">{{$data['t']}}</td>
        <td class="first-col"></td>
        <td >{{moneda($data['months'][0])}}</td>
        @foreach($lstMonths as $k => $month)
        <td>
          <?php
              if (isset($data['months'][$k]) && $data['months'][$k] > 1) {
                echo moneda($data['months'][$k]);
              } else {
                echo '--';
              }
              ?>
        </td>
        @endforeach
      </tr>
      @endforeach

    </tbody>
  </table>
</div>
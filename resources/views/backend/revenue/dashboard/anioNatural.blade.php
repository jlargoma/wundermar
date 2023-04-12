@extends('layouts.admin-onlybody')

@section('title') Revenue @endsection

@section('externalScripts')
<script type="text/javascript">
  $(document).ready(function () {
    function reloadNatureYear(){
      var url = '/admin/revenue/anioNatura/'+$('#selSite').val();
          var year = $('#selAnio').val();
          if (year) {
              url += '/' + year;
              var trimenst = $('#selTrim').val();
              if (trimenst) {
                  url += '/' + trimenst;
              }
          }
          window.location.href = url;
    }
      $('#selSite, #selAnio,#selTrim').on('change', function (event) {
         reloadNatureYear(); 
      });
      $('.btn_anio').on('click', function (event) {
          $('#selSite').val($(this).data('k'));
          reloadNatureYear();
      });

  });
</script>
<style>
  select.form-control{
    width: 250px;
  }
  .table-resumen.tAnioNatura .td_w1{
    width: 75px;
    min-width: 75px;
  }
  .table-resumen.tAnioNatura th.static,
  .table-resumen.tAnioNatura td.static{
    text-align: left;
    white-space: break-spaces;
    height: 45px;
  }
  .table-resumen.tAnioNatura td{
    height: 45px;
  }
  .col-select {
    float: left;
    width: 135px;
    margin-right: 10px;
}
  .col-select select.form-control {
    width: 125px;
  }
  table.table.table-resumen.tAnioNatura {
    width: auto;
}
.table.tableBtn{
   width: auto;
}
th.btn_anio {
    text-align: center;
    cursor: pointer;
    border: 1px solid;
        width: 168px;
}
.table.tableBtn th.btn_anio.active,
.table.tableBtn th.btn_anio:hover {
  background-color: #004a2f;
    color: #FFF;
}
select#selTrim {
  text-align: center;
}
@media (min-width: 1160px){
  .table-resumen.tAnioNatura th.static, .table-resumen.tAnioNatura td.static {
    min-width: 190px;
  }
  .table-resumen.tAnioNatura .first-col {
    padding-left: 168px !important;
  }
}
</style>
@endsection

@section('content')
<div class=" table-responsive">
  <table class="table tableBtn">
    <tr>
      <th class="btn_anio <?php echo ('all' == $site) ? 'active' : ''; ?>" data-k="all">Todos</th>
      @foreach($siteLst as $k=>$v)
      <th class="btn_anio <?php echo ($k == $site) ? 'active' : ''; ?>" data-k="{{$k}}" >{{$v}}</th>
      @endforeach
    </tr>
  </table>
</div>
<div class="row">
  <input type="hidden" id="selSite" name="selSite" value="{{$site}}">
 
  <div class="col-select">
    <label>Año</label>
    <select id="selAnio" class="form-control"> 
      <?php $endYear = (date('Y') + 3); ?>
      @for($i=2019;$i<$endYear;$i++)
      <option value="{{$i}}" <?php echo ($i == $year) ? 'selected' : ''; ?>>{{$i}}</option>
      @endfor
    </select>
  </div>
  <div class="col-select">
    <label>Trimestre</label>
    <select id="selTrim" class="form-control"> 
      <option value="">- TODOS -</option>
      @for($i=1;$i<5;$i++)
      <option value="{{$i}}" <?php echo ($i == $trim) ? 'selected' : ''; ?>>{{$i}}</option>
      @endfor
    </select>
  </div>
</div>

<div class="anioNatura  table-responsive">
  <table class="table table-resumen tAnioNatura">
    <thead>
      <tr class="resume-head">
        <th class="static">AÑO {{$year}}</th>
        <th class="first-col"></th>
        @foreach($monthNames as $mn)
        @if($mn != '') <th class="td_w1">{{$mn}}</th> @endif
        @endforeach
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="static">ING RESERVAS</td>
        <td class="first-col"></td>
        @foreach($monthBook as $mt)
        <td class="td_w1">{{numero($mt)}}</td>
        @endforeach
        <td><b>{{numero(array_sum($monthBook))}}</b></td>
      </tr>
      <tr>
        <td class="static">PAGO A PROPIETARIOS</td>
        <td class="first-col"></td>
        @foreach($monthProp as $mt)
        <td class="td_w1">{{numero($mt)}}</td>
        @endforeach
        <td><b>{{numero(array_sum($monthProp))}}</b></td>
      </tr>
      <tr>
        <td class="static">RLTDO INTERMEDIACIÓN</td>
        <td class="first-col"></td>
        @foreach($result as $mt)
        <td class="td_w1">{{numero($mt)}}</td>
        @endforeach
        <td><b>{{numero(array_sum($result))}}</b></td>
      </tr>
      <tr><th colspan="15">&nbsp;</th></tr>
      <tr>
        <td class="static">BASE IMP</td>
        <td class="first-col"></td>
        @foreach($base as $mt)
        <td class="td_w1">{{numero($mt)}}</td>
        @endforeach
        <td><b>{{numero(array_sum($base))}}</b></td>
      </tr>
      <tr>
        <td class="static">IVA 10%</td>
        <td class="first-col"></td>
        @foreach($iva as $mt)
        <td class="td_w1">{{numero($mt)}}</td>
        @endforeach
        <td><b>{{numero(array_sum($iva))}}</b></td>
      </tr>
      <tr>
        <td class="static">TOTAL TRAS INTERMEDIACIÓN INMOBILIARIA</td>
        <td class="first-col"></td>
        @foreach($tIva as $mt)
        <td class="td_w1">{{numero($mt)}}</td>
        @endforeach
        <td><b>{{numero(array_sum($tIva))}}</b></td>
      </tr>
    </tbody>
  </table>
</div>
@endsection
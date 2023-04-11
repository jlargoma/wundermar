@extends('layouts.admin-master')

@section('title') Estadísticas INE @endsection

@section('externalScripts') 
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="{{ asset('/css/components/daterangepicker.css')}}" type="text/css"/>
<link rel="stylesheet" href="{{ asset('/assets/css/font-icons.css')}}" type="text/css"/>

<script type="text/javascript" src="{{asset('/js/components/moment.js')}}"></script>
<script type="text/javascript" src="{{asset('/js/components/daterangepicker.js')}}"></script>
<script type="text/javascript" src="{{ asset('/js/datePicker01.js')}}"></script>
<script type="text/javascript">
  $(document).ready(function () {
    $('.daterange02').on('change',function (event) {
      var date = $(this).val();

      var arrayDates = date.split('-');
      var res1 = arrayDates[0].replace("Abr", "Apr");
      var date1 = new Date(res1);
      var start = date1.getTime();
      var res2 = arrayDates[1].replace("Abr", "Apr");
      var date2 = new Date(res2);

      $('#start').val(date1.yyyymmmdd());
      $('#finish').val(date2.yyyymmmdd());
              
    });
  });
</script>
@endsection

@section('content')

<style>
  .table-ine td{
    text-align: center;
   padding: 8px !important;
    border: solid 1px #c3c3c3;
  }
  .table-ine table {
    margin: 8px 0 2em;
  }
  .my-1{
    margin: 1em auto 5em;
  }
  form div{
    margin-bottom: 1em;
  }
  .table-ine table thead tr td {
    background-color: #bfaeff;
    font-weight: 600;
    color: #000;
  }
</style>


<div class="container-fluid padding-25 sm-padding-10">
  <h2 class="font-w800">
    Contenido de la encuesta / Periodo <?php echo $m.'-'.$y; ?>
  </h2>
  <div class="row">
    <div class="col-md-6 text-center">
      <form method="POST" action="">
        {{csrf_field()}}
      <div class="row">
        <div class="col-md-3">
          <label>Edificio</label>
          <select name="type" class="form-control">
            <option value="wundermar" @if( $type == 'wundermar') selected @endif >wundermar</option>
            <option value="rosa" @if( $type == 'rosa') selected @endif >Rosa D'Oro</option>
            <option value="gloria" @if( $type == 'gloria') selected @endif >Gloria</option>
          </select>
        </div>
        <div class="col-md-4">
          <label>Rango</label>
          <input type="text" class="form-control daterange02" id="fechas" name="fechas" required="" style="cursor: pointer; text-align: center;min-height: 28px;" readonly=""  value="{{$range}}">
          <input type="hidden" class="date_start" id="start" name="start" value="{{$start}}">
          <input type="hidden" class="date_finish" id="finish" name="finish" value="{{$finish}}">
        </div>
        <div class="col-md-4">
          <br>
          <button class="btn btn-primary">Cargar</button>
          <a href="{{$dwnl_url}}" class="btn btn-default">Descargar</a>
          <a href="https://arce.ine.es/ARCE/jsp/encuestaXml.jsp" class="btn btn-info" target="_black">INE</a>
        </div>
      </div>
        <table>
          <tr>
            <td class="text-left"><label>PERSONAL <br/>NO REMUNERADO</label></td>
            <td><input type="text" class="form-control" id="p_n_remun" name="p_n_remun" value="{{$force['p_n_remun']}}" style="margin: 8px;"></td>
          </tr>
          <tr>
            <td class="text-left"><label>PERSONAL <br/>REMUNERADO FIJO</label></td>
            <td><input type="text" class="form-control" id="p_remun_fijo" name="p_remun_fijo" value="{{$force['p_remun_fijo']}}" style="margin: 8px;"></td>
          </tr>
          <tr>
            <td class="text-left"><label>PERSONAL <br/>REMUNERADO EVENTUAL</label></td>
            <td><input type="text" class="form-control" id="p_remun_eventual" name="p_remun_eventual" value="{{$force['p_remun_eventual']}}" style="margin: 8px;"></td>
          </tr>
        </table>
      
        </form>
    </div>
    <div class="col-md-6">
      <form method="post" action="{{route('INE.sendEncuesta')}}" style="display:none;">
        {{csrf_field()}}
        <input type="hidden" value="<?php echo $type;?>" name="type">
        <input type="hidden" value="{{$start}}" name="start">
        <input type="hidden" value="{{$finish}}" name="finish">
          
        <input type="hidden" value="<?php echo base64_encode(json_encode($force));?>" name="force">
        <div class="row">
          <div class="col-md-5">
            <label>Número de Orden</label>
            <input type="text" value="" name="NumeroOrden" class="form-control">
          </div>
          <div class="col-md-4">
            <label>Código de Control</label>
            <input type="text" value="" name="CodigoControl" class="form-control  col-md-3">
          </div>
          <div class="col-md-3">
            <br>
            <button class="btn btn-primary">Enviar</button>
            
          </div>
        </div>
      </form>
      
    </div>
  </div>
  @if($encuesta)
  <br/>
  <hr>
  <br/>
  <div class="row my-1 ">
    <div class="col-md-6">
      <h2>Cabecera</h2>
      <?php 
       $obj = $encuesta['CABECERA'];
       foreach ($obj as $k=>$v):
        if (is_array($v)):
          echo $k;?>: <strong><?php echo $v['MES'].'-'.$v['ANYO']; ?> </strong><br/><?php 
        else:
         echo $k; ?>: <strong><?php echo $v; ?> </strong><br/><?php
        endif;
       endforeach;
      ?>
    </div>
    <div class="col-md-6">
      <h2>Personal Ocupado</h2>
      <?php 
       $obj = $encuesta['PERSONAL_OCUPADO'];
       foreach ($obj as $k=>$v):
        if (is_array($v)):
          
        else:
         echo $k; ?>: <strong><?php echo $v; ?> </strong><br/><?php
        endif;
       endforeach;
      ?>
      @if(isset($encuesta['CABECERA_APARTAMENTOS']))
      <h2>CABECERA APARTAMENTOS</h2>
       <?php 
       $obj = $encuesta['CABECERA_APARTAMENTOS'];
       foreach ($obj as $k=>$v):
        if (!is_array($v)):
         echo $k; ?>: <strong><?php echo $v; ?> </strong><br/><?php
        endif;
       endforeach;
      ?>
      @endif
    </div>
    @if(isset($encuesta['INFORMANTE']))
    <div class="col-md-6">
      <h2>INFORMANTE</h2>
       <?php 
       $obj = $encuesta['INFORMANTE'];
       foreach ($obj as $k=>$v):
         echo $k; ?>: <strong><?php echo $v; ?> </strong><br/><?php
       endforeach;
      ?>
    </div>
    @endif
    @if(isset($encuesta['EMPRESA_GESTORA']))
    <div class="col-md-6">
      <h2>EMPRESA GESTORA</h2>
       <?php 
       $obj = $encuesta['EMPRESA_GESTORA'];
       foreach ($obj as $k=>$v):
         echo $k; ?>: <strong><?php echo $v; ?> </strong><br/><?php
       endforeach;
      ?>
    </div>
    @endif
    <div class="col-md-6">
        <h2>Precios</h2>
        <?php
        $precios = $encuesta['PRECIOS'];
        if (isset($precios['ESTUDIOS'])){
          $aux1 = ['ESTUDIOS','APARTAMENTOS_2-4pax','APARTAMENTOS_4-6pax','OTROS'];
          $aux2 = [
            'TARIFA_NORMAL',
            'TARIFA_FIN_DE_SEMANA',
            'TARIFA_TOUROPERADOR',
            'TARIFA_OTRAS',
        ];
        ?>
        <div class="table-ine">
         <table class="table">
           <thead>
           <tr>
             <td>Tipo</td>
            <?php foreach ($aux1 as $a) echo "<td colspan='2'>$a</td>"; ?>
           </tr>
           </thead>
           <tbody>
           <?php foreach ($aux2 as $a2):?>
             <tr>
               <td><?php echo $a2; ?></td>
               <?php 
               foreach ($aux1 as $a){
                 echo '<td>'.$precios[$a][$a2].' €</td>'; 
                 echo '<td>'.$precios[$a]['PCTN_'.$a2].'%</td>'; 
               }
               ?>
             </tr>
           <?php  endforeach; ?>
           </tbody>
         </table>
        </div>
      <?php
    } else {
       ?>
      <div class="table-ine">
        <table class="table">
          <thead>
            <tr>
              <td class="text-left">Tipo</td>
             <td></td>
             <td></td>
            </tr>
          </thead>
          <tbody>
           <tr>
            <td class="text-left">RVAS RIRECTAS</td>
            <td>{{moneda($precios['ADR_INTERNET'])}}</td>
            <td>{{($precios['PCTN_HABITACIONES_OCUPADAS_INTERNET'])}}%</td>
           </tr>
           <tr>
            <td class="text-left">AGENCIA DE VIAJE ONLINE</td>
            <td>{{moneda($precios['ADR_AGENCIA_DE_VIAJE_ONLINE'])}}</td>
            <td>{{($precios['PCTN_HABITACIONES_OCUPADAS_AGENCIA_ONLINE'])}}%</td>
           </tr>
          </tbody>
         </table>
      </div>
      <?php
    }
    ?>
    </div>
    
    
  </div>
  <div class="row my-1 table-ine  table-responsive">
    <h2>Alojamientos</h2>
    <table class="table">
        <thead>
        <tr>
          <td class="text-left">Lugar</td>
          <td>T. Vis.</td>
          <?php foreach ($dias as $d) echo '<td>'.$d.'</td>'; ?>
        </tr>
        </thead>
        <tbody>
          <?php
          foreach($alojamientos as $a){
            ?>
          <tr>
            <td class="text-left">{{$a['lugar']}}</td>
            <td>{{$a['t_entradas']}}</td>
            <?php foreach ($a['mov'] as $m) echo '<td>'.$m.'</td>'; ?>
          </tr>
            <?php
          }
          ?>
        </tbody>
      </table>
  </div>
  <div class="row my-1 table-ine table-responsive">
    <h2>Movimientos Habitaciones</h2>
    <table class="table">
        <thead>
        <tr>
          <td class="text-left">Habitación</td>
          <td >Total</td>
          <?php foreach ($dias as $d) echo '<td>'.$d.'</td>'; ?>
        </tr>
        </thead>
        <tbody>
          <?php
          foreach($movApart as $k=>$m){
            ?>
          <tr>
            <td class="text-left">{{$movApartTit[$k]}}</td>
            <td>{{array_sum($m)}}</td>
            <?php foreach ($m as $m2) echo '<td>'.$m2.'</td>'; ?>
          </tr>
            <?php
          }
          ?>
        </tbody>
      </table>
     </div>
    @else 
    
    @endif
</div>


@endsection

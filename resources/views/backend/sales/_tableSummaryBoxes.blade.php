<?php
if (!isset($hide))
  $hide = ['t_day_2'];
//dd($hide);
?>
<style type="text/css">
  .bordered{
    padding: 15px;
    border:1px solid #e8e8e8;
    background: white;
  }
  .sumary.bordered {
    padding: 1em 1.5em;
    float: left;
    max-width: 20em;
    margin-right: 3px;
  }
  .sumary.bordered .seasonDays{
    border: none;
    font-size: 32px;
    margin: 10px 0;
    color:red!important
  }

  @media only screen and (max-width: 425px) {
    .sumary.bordered {
      width: 32%;
      text-align: center;
      padding: 5px;
      height: 100px;
      margin-bottom: 5px;
    }
    .sumary.bordered.mobil_1 {
      width: 23%;
    }
    .sumary.bordered.mobil_2 {
      width: 47%;
    }
    .sumary.bordered.mobil_2 label {
      height: 3em;
    }
    .sumary.bordered.min {
      width: 23%;
    }
    .sumary.bordered label {
      font-size: 12px;
    }
    .sumary.bordered h3 {
      font-size: 21px;
      font-weight: 600 !important;
    }
    .sumary.bordered .seasonDays,
    .sumary.bordered.min h3{
      font-size: 18px;
    }
  }

</style>
<div class="row">
  <div class="col-lg-8 col-md-12">
    <div class="row">
    <div class="sumary bordered mobil_1">
      <label>Total Reservas</label>
      <h3 class="text-black font-w400 text-center">{{$summary['total']}}</h3>
    </div>
    <div class="sumary bordered mobil_1">
      <label>Nº Inquilinos</label>
      <h3 class="text-black font-w400 text-center">{{$summary['pax']}}</h3>
    </div>
    @if(!in_array('rvas',$hide))
    <div class="sumary bordered mobil_2">
      <label>RVAS</label>
      <h3 class="text-black font-w400 text-center">{{moneda($summary['total_pvp'])}}</h3>
    </div>
    @endif
    @if(!in_array('bnf',$hide))
    <div class="sumary bordered mobil_2">
      <label>RTDO ESTIM x RVAS</label>
      <h3 class="text-black font-w400 text-center">{{moneda($summary['benef'])}}</h3>
    </div>
    @endif
    <div class="sumary bordered mobil_1">
      <label>% benef reservas</label>
      <h3 class="text-black font-w400 text-center">{{round($summary['benef_inc'])}}%</h3>
    </div>
    <div class="sumary bordered mobil_1">
      <label>Venta propia</label>
      <h3 class="text-black font-w400 text-center">{{$summary['vta_prop']}}%</h3>
    </div>
    <div class="sumary bordered min">
      <label>Venta agencia</label>
      <h3 class="text-black font-w400 text-center">{{$summary['vta_agency']}}%</h3>
    </div>
    <div class="sumary bordered min">
      <label>Estancia media</label>
      <h3 class="text-black font-w400 text-center">
        {{$summary['nights-media']}}
      </h3>
    </div>
    <div class="sumary bordered min">
      <label>Total Noches</label>
      <h3 class="text-black font-w400 text-center">{{$summary['nights']}}</h3>
    </div>
    <div class="sumary bordered min">
      <label>Dias totales</label>
      @if(!in_array('t_day_1',$hide))
      <input class="form-control text-black font-w400 text-center seasonDays" value="{{$summary['daysTemp']}}" />
      @endif
      @if(!in_array('t_day_2',$hide))
      <h3 class="text-black font-w400 text-center">{{$summary['daysTemp']}}</h3>
      @endif
    </div>
    </div>
    </div>
    <div class="col-lg-4 col-md-12">
      @if(isset($salesByUser))
      <table class="table">
        <tr>
          <th></th>
          @foreach($yearsLst as $year)
          <th>{{$year}}</th>
          @endforeach
        </tr>
        @foreach($salesByUser as $uid=>$data)
        <tr>
          <th><?php echo (isset($uIdName[$uid])) ? $uIdName[$uid] : 'Otros'; ?></th>
          @foreach($data as $year=>$val)
          <td>{{moneda($val)}}</td>
          @endforeach
        </tr>
        @endforeach
      </table>
      @endif
    </div>
  </div>

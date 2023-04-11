@extends('layouts.admin-master')

@section('title') Revenue @endsection


@section('content')
<div class="box-btn-contabilidad">
  <div class="row bg-white">
    <div class="col-md-12 col-xs-12">

      <div class="col-md-3 col-md-offset-3 col-xs-7 text-right">
        <h2 class="text-center">
          Revenue DASHBOARD
        </h2>
      </div>
      <div class="col-md-2 col-xs-4 sm-padding-10" style="padding: 10px">
        @include('backend.years._selector')
      </div>
    </div>
  </div>
  <div class="row mb-1em text-center">
    @include('backend.revenue._buttons')
  </div>
</div>

    @include('backend.revenue.dashboard.pieCss')

<div class=" contenedor c-dashboard">
  <h2 class="line"><span>Venta mes en curso</span></h2>
  <div id="blockMes">
  <?php echo $datosMes; ?>
  </div>
  <h2 class="line"><span>Disponibilidad por alojamiento</span></h2>
  <div id="blockDisp">
  <?php echo $disponiblidad; ?>
  </div>
    <h2 class="line"><span>Ventas por mes</span></h2>
  <?php echo $ingrMes; ?>
  <h2 class="line"><span>Ratios de Ocupación</span></h2>
  <div id="blockRatio">
  <?php echo $ratios; ?>
  </div>
  <h2 class="line"><span>Overview presupuesto anual</span></h2>
  <div class="row">
    <div class="col-md-7"><?php echo $presupuesto_head; ?></div>
    <div class="col-md-5"><?php echo $agencias; ?></div>
  </div>
  <h2 class="line"><span>Comparativa de ingresos anuales</span></h2>
  <div class="">
    <button type="button" class="btn btnChangeComparativa" data-k="comparativaAnuales">COMPARAR 5 ÚLTIMOS</button>
    <button type="button" class="btn btnChangeComparativa active anual" data-k="comparativaAnual">VER SÓLO UN AÑO</button>
    <select class="form-control" id="changeComparativaYear">
      @for($i=0;$i<5;$i++)
      <option value="{{$year-$i}}">{{$year-$i}}</option>
      @endfor
    </select>
</div>
  <div id="box_comparativaAnual">
  <?php echo $comp_ingresos_anuales; ?>
    </div>
  <hr>
  <?php echo $balance; ?>
</div>
    
    
    <h2 class="line"><span>Año Natural</span></h2>   
<iframe src="/admin/revenue/anioNatura" style="width: 100%; height: 40em; padding: 9px; border: none;"></iframe>

@endsection

@section('externalScripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<link rel="stylesheet" href="{{ assetV('/css/backend/revenue_dashboard.css')}}" type="text/css"/>

<script type="text/javascript">
  
  $(document).ready(function () {
    $('.kpis').hide();
    $('.kpi_0').show();
    $('.bkpi0').addClass('active');
    
    $('#blockMes').on('click','.btn_kpi',function(){
      $('.btn_kpi').removeClass('active');
      $('.kpis').hide();
      var k = $(this).data("k");
      $('.kpi_'+k).show();
      $(this).addClass('active');
    });
    
    $('.ratios').hide();
    $('.ratio_0').show();
    $('.ratio0').addClass('active');
    $('#blockRatio').on('click','.btn_ratio',function(){
      $('.btn_ratio').removeClass('active');
      $('.ratios').hide();
      var k = $(this).data("k");
      $('.ratio_'+k).show();
      $(this).addClass('active');
    });
    //----------------------------------------------------------------//
    $('#MonthsPresup').on('click','.sm',function(){
      $('#MonthsPresup').find('.sm').removeClass('active');
      $(this).addClass('active');
      $('#blockPresup').load('getOverview/'+$(this).data('k'));
    });
    
    $('#blockPresup').on('click','.presup_0',function(){
      if ($(this).data('month') !== undefined)
        $('#blockPresup').load('getOverview/'+$(this).data('month'));
    });
    
   
    $('#blockPresup').on('keyup', '.editable', function (e) {
      $(this).val($(this).val().replace(/[^\d|^.]/g, ''));
      if (e.keyCode == 13) {
        var Obj = $(this);
        var data = {
          k: Obj.data('k'),
          s: Obj.data('s'),
          y: $('#yoiYear').val(),
          m: Obj.data('m'),
          v: Obj.val(),
          ms: $('#yoiMonth').val(),
          _token: "{{ csrf_token() }}"
        }
      $.ajax({
      type: "POST",
          method : "POST",
          url: "upd-Overview",
          data: data,
          success: function (response)
          {
            $('#blockPresup').html(response);
            $('.presupuesto').hide();
            $('.presup_'+PresupSelect).show();
          }
        });
      }
    });
    //----------------------------------------------------------------//
    
    $('.select_site').on('click', function (event) {
      $('#site').val($(this).data('k'));
    });
    
    
$('.toggle-contab-site').on('click',function(){
        var id = $(this).data('id');
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contab-ch-'+id).addClass('tr-close');
          $('.contab-rsite-'+id).addClass('tr-close');
        } else {
          $(this).addClass('open');
          $('.contab-ch-'+id).removeClass('tr-close');
        }
      });
      $('.toggle-contab').on('click',function(){
        var id = $(this).data('id');
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contab-room-'+id).addClass('tr-close');
          
        } else {
          $(this).addClass('open');
          $('.contab-room-'+id).removeClass('tr-close');
        }
      });
      $('.toggle-contab-extra').on('click',function(){
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contab-extras').addClass('tr-close');
          
        } else {
          $(this).addClass('open');
          $('.contab-extras').removeClass('tr-close');
        }
      });
      
    $('#blockMes').on('click','.sm',function(){
      $('#blockMes').load('getMonthKPI/'+$(this).data('k'),function(){
        $('.kpis').hide();
        $('.kpi_0').show();
        $('.bkpi0').addClass('active');
      });
    });
    $('#blockDisp').on('click','.sm',function(){
      $('#blockDisp').load('getMonthDisp/'+$(this).data('k'),function(){
        
      });
    });
    
    
    
    //comparativaAnual
    $('.comparativaAnual').addClass('active');
    $('.btnChangeComparativa').on('click', function () {
      $('.btnChangeComparativa').removeClass('active');
      var k = $(this).data("k");
      if(k=='comparativaAnual'){
        $('#comparativaAnuales').hide();
        $('#comparativaAnual').show();
      }
      if(k=='comparativaAnuales'){
        $('#comparativaAnual').hide();
        $('#comparativaAnuales').show();
      }
      $(this).addClass('active');
    });

    $('#changeComparativaYear').on('change', function () {
      $('.btnChangeComparativa').removeClass('active');
      $('.btnChangeComparativa.anual').addClass('active');
      $('#box_comparativaAnual').load('/admin/revenue/getComparativaAnual/'+$(this).val());
    });
    
    
  });
 
</script>

@endsection
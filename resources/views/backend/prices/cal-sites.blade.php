@extends('layouts.admin-master')

@section('title') Precios de apartamentos @endsection

@section('externalScripts') 
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/selectable-table-plugin@latest/selectable.table.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="{{ assetV('/css/backend/planning.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<?php
$is_mobile = config('app.is_mobile');
$hcol = ($is_mobile) ? 1 : 15;
$tDays = count($days);
$extr = '<span class="btnCalendar btn btn-primary"  data-toggle="modal" data-target="#siteCalendar"><i class="fa fa-calendar openCalendar"></i></span>';
?>
<div class="container-fluid padding-25 sm-padding-10">
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-3 col-xs-12">
          <h3>Listado de Precios:</h3>
        </div>
        <div class="col-xs-12 col-md-7">
          @include('backend.prices._navs',['extr'=>$extr])
        </div>
        <div class="col-md-2 select-site">
          <select id="select_site" class="form-control">
            <?php foreach (\App\Sites::allSites() as $k=>$v): ?>
              <option value="<?php echo $k ?>" @if($k == $site) selected @endif><?php echo $v ?></option>
            <?php endforeach ?>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3 col-xs-12"></div>
        <div class="col-md-6 col-xs-12">
          <div class="select-month">
            <a href="/admin/channel-manager/price-site/{{$site}}/{{$prev}}"><i class="fa fa-arrow-alt-circle-left"></i> </a>
            <select id="currentMonth">
              @if($monthsLst)
              @foreach($monthsLst as $k=>$v)
              <option value="{{$k}}" <?php echo $k == $month ? 'selected' : ''; ?>>{{$v}}</option>
              @endforeach
              @endif
            </select>
            <select id="currentYear">
              <option value="{{$year-1}}" >{{$year-1}}</option>
              <option value="{{$year}}" selected>{{$year}}</option>
              <option value="{{$year+1}}" >{{$year+1}}</option>
            </select>
            <a href="/admin/channel-manager/price-site/{{$site}}/{{$next}}"><i class="fa fa-arrow-alt-circle-right"></i> </a>
          </div>
        </div>
        <div class="col-md-3 col-xs-12">
          <div class="col-md-6 col-xs-7">
            <label class="label_sel_col">Seleccionar Columna: </label>
          </div>
          <div class="col-md-6 col-xs-5">
            <select id="sel_by_column" class="form-control">
              <option value="1">Precios</option>
              <option value="2">Min. Ocupación</option>
            </select>
          </div>
        </div>
      </div>
      <div class="table-responsive table-resumen-content">
        <table class="table table-resumen" id="table_prices">
          <thead>
            <tr>
              <th class=" white">&nbsp;</th>
              @foreach($aMonth as $k=>$item)
              <th class="month" colspan="{{$item['colspan']}}">{{$item['text']}}</th>
              @endforeach
            </tr>
            <tr>
              <th class=" white">&nbsp;</th>
              @foreach($days as $k=>$day)
              <th class="day w select_column" data-day="{{$k}}">{{$dw[$day['w']]}}<br>{{$day['day']}}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($rooms as $kRoom=>$item)
            <?php if (!$item['data']) continue; ?>
            <tr class="room-name">
              <th>{{str_replace('ROSA - ','',$item['tit'])}}</th>
              @include('backend.prices.blocks.otaPrices')
            </tr>
            <tr>
              <th>Precio €</th>
              <?php $priceLst = $item['data']['priceLst']; ?>
              @foreach($days as $k=>$day)
              @if(isset($priceLst[$k]))
              <td class="day tPriceEdit price_{{$k}}" data-id="{{$kRoom.'@'.$k}}">{{$priceLst[$k][0]}}</td>
              @else
              <td class="day tPriceEdit price_{{$k}}" data-id="{{$kRoom.'@'.$k}}">-</td>
              @endif
              @endforeach
            </tr>
            <tr>
              <th>OTAs €</th>
              @foreach($days as $k=>$day)
              <td class="day text-center">
                @if(isset($priceLst[$k]))
                <i class="fa fa-question-circle">
                  <div class="t-otas-room">
                    <table class="">
                      <tr>
                        <td><span class="price-booking">{{$priceLst[$k]['booking']}}</span></td>
                        <td><span class="price-airbnb">{{$priceLst[$k]['airbnb']}}</span></td>
                        <td rowspan="2" class="benef">{{$priceLst[$k]['inc_percent']}}%</td>
                      </tr>
                      <tr><td><span class="price-expedia">{{$priceLst[$k]['expedia']}}</span></td><td><span class="price-google">{{$priceLst[$k]['google']}}</span></td></tr>
                    </table>
                  </div>
                </i>
                @else
                -
                @endif
              </td>
              @endforeach
            </tr>
            <tr>
              <th>Min. Ocup.</th>
              <?php $priceLst = $item['data']['priceLst']; ?>
              @foreach($days as $k=>$day)
              @if(isset($priceLst[$k]))
              <td class="day tMinEdit min_st_{{$k}}" data-id="{{$kRoom.'@'.$k}}">{{$priceLst[$k][1]}}</td>
              @else
              <td class="day tPriceEdit  min_st_{{$k}}" data-id="{{$kRoom.'@'.$k}}">-</td>
              @endif
              @endforeach
            </tr>
            <tr>
              <th>Disponibilidad</th>
              <?php
              $avail = $item['data']['avail'];
              $t_rooms = $item['data']['t_rooms'];
              ?>
              @foreach($days as $k=>$day)
              @if(isset($avail[$k]))
              <td class="day <?php if ($avail[$k] == 0) echo 'red'; ?>">{{$avail[$k]}}</td>
              @else
              <td class="day">--</td>
              @endif
              @endforeach
            </tr>
            <tr>
              <th>Reservado</th>
              @foreach($days as $k=>$day)
              @if(isset($avail[$k]))
              <td class="day">{{$t_rooms-$avail[$k]}}</td>
              @else
              <td class="day">--</td>
              @endif
              @endforeach
            </tr>
            <tr>
              <th>OTA Avail</th>
              @foreach($days as $k=>$day)
              <?php $aux = isset($otaAvail[$kRoom]) ? $otaAvail[$kRoom][$k] : '0'; ?>
              <td class="day <?php if ($avail[$k] != $aux) echo 'red'; ?>"">{{$aux}}</td>
              @endforeach
            </tr>
            @endforeach
            <tr>
              <td colspan="{{count($days)-$hcol}}" style="min-height: 5em;"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="calendar-blok">
        <div id='calendar'></div>   
      </div>
    </div>
    <div class="col-md-4">

    </div>
  </div>
  <small class="hidden-mobile">
  <span class="benef">33%</span>: Porcentaje de beneficio calculado sobre el precio de Google Hotel 
  y cuyo costo es la sumatoria del costo del apto + costo limpieza 
  + costo de parking + costo Exta (obsequio) + costo Lujo (si corresponde).<br/>
  No incluye costo de agencia o comisiones PVP.
</small>
</div>

<div class="modal fade slide-up in" id="siteCalendar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content-wrapper">
      <div class="modal-content">
        <div class="block">
          <div class="block-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                class="fa fa-close fs-14"
                style="font-size: 40px!important;color: black!important"></i>
            </button>
          </div>
          <div class="block block-content" style="width: 92vw;margin: 1em auto;">
            <div class="row content-calendar push-20" style="min-height: 515px;">
              <div class="col-xs-12 text-center sending" style="padding: 120px 15px;">
                <i class="fa fa-spinner fa-5x fa-spin" aria-hidden="true"></i><br>
                <h2 class="text-center">CARGANDO CALENDARIO</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




@endsection

@section('scripts')


<script type="text/javascript">
$(document).ready(function () {
  const hTable = $('#table_prices').find('tbody');


  function edit(currentElement) {
    var input = $('<input>', {class: "only-numbers"})
            .val(currentElement.html())
    currentElement.html(input)
    input.focus();
  }

  $('#table_prices').find('thead').find('th').click(function () {
    var that = $(this);
    console.log(that);
    if (that.hasClass('select_column')) {
      var date = that.data('day');
      var type = $('#sel_by_column').val();
//          var type = $("input[name='sel_by_column']:checked").val();
//          console.log(type);
      if (type == 1) {
        $('.tPriceEdit.price_' + date).trigger('click');
      }
      if (type == 2) {
        $('.tMinEdit.min_st_' + date).trigger('click');
      }

    }
  });
  hTable.find('td').click(function () {
    var that = $(this);
    /*** Edit prices       ****/
    if (that.hasClass('tPriceEdit')) {
      //Clear other input
      hTable.find('.tMinEdit').each(function () {
        var value = $(this).find('input').val();
        $(this).text(value).removeClass('tSelect');
      });

      //prepare input
      if (that.hasClass('tSelect')) {
        that.removeClass('tSelect')
        that.text(that.data('val'));
      } else {
        that.data('val', that.text());
        that.addClass('tSelect')
        edit($(this));
      }
    }
    /*** Edit Min. Ocup       ****/
    if (that.hasClass('tMinEdit')) {
      //Clear other input
      hTable.find('.tPriceEdit').each(function () {
        var value = $(this).find('input').val();
        $(this).text(value).removeClass('tSelect');
      });

      //prepare input
      if (that.hasClass('tSelect')) {
        that.removeClass('tSelect')
        that.text(that.data('val'));
      } else {
        that.data('val', that.text());
        that.addClass('tSelect')
        edit($(this));
      }

    }
  });

  hTable.on('keyup', '.tSelect', function (e) {
    if (e.keyCode == 13) {
      var data = new Array();
      var value = 0;
      var last = null;
      hTable.find('.tSelect').each(function () {
        value = $(this).find('input').val();
        data.push($(this).data('id'));
        $(this).text(value).removeClass('tSelect');
        last = $(this);
      });
      var type = 'minDay';
      if (last.hasClass('tPriceEdit'))
        type = 'price';
      updValues(data, value, type);
    } else {
      hTable.find('.tSelect').find('input').val($(this).find('input').val());
    }
  });

  var updValues = function (data, value, type) {
    var url = "{{route('channel.price.site.upd')}}";
    var sID = $('#select_site').val();

    $('#loadigPage').show('slow');
    $.ajax({
      type: "POST",
      url: url,
      data: {_token: "{{ csrf_token() }}", items: data, val: value, type: type, siteID: sID},
      success: function (response)
      {
        if (response.status == 'OK') {
          hTable.find('.tSelect').each(function () {
            $(this).text(value).removeClass('tSelect');
          });
          window.show_notif('OK', 'success', response.msg);
        } else {
          window.show_notif('Error', 'danger', response.msg);
        }
        $('#loadigPage').hide('slow');
      }
    });

  }


  $('#currentMonth').on('change', function () {
    location.href = '/admin/channel-manager/price-site/{{$site}}/' + $(this).val() + '/{{$year}}';
  });
  $('#currentYear').on('change', function () {
    location.href = '/admin/channel-manager/price-site/{{$site}}/{{$month}}/' + $(this).val();
  });
  $('#select_site').on('change', function () {
    location.href = '/admin/channel-manager/price-site/' + $(this).val() + '/{{$month}}/{{$year}}';
  });

  $("#table_prices").on('keydown', '.only-numbers', function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 188, 109]) !== -1 ||
            // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
      // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault();
    }
  });



  /**************************************************************************************/
  var cal_move = false;
  var moveCalendar = function () {
    if (cal_move)
      return;
    cal_move = true;
    $('.btn-fechas-calendar').css({
      'background-color': '#899098',
      'color': '#fff'
    });
    $('#btn-active').css({
      'background-color': '#10cfbd',
      'color': '#fff'
    });
    var target = $('#btn-active').attr('data-month');
    var targetPosition = $('.content-calendar #month-' + target).position();
    $('.content-calendar').animate({scrollLeft: "+=" + targetPosition.left + "px"}, "slow");
  }
  setTimeout(function () {
    moveCalendar();
  }, 200);
  $('.content-calendar').on('click', '.reloadCalend', function () {
    var time = $(this).attr('data-time');
    cal_move = false;
    $('.content-calendar').empty().load(
            '/getCalendarSite/{{$site}}/' + time,
            function () {
              moveCalendar();
            }
    );
  });

  $('.content-calendar').empty().load('/getCalendarSite/{{$site}}', function () {
    var target = $('#btn-active').attr('data-month');
    var targetPosition = $('.content-calendar #month-' + target).position();
    $('.contentCalendar').animate({scrollLeft: "+=" + targetPosition.left + "px"}, "slow");
  });


});
</script>
<style>
  i.fa.fa-question-circle {
    position: relative;
    cursor: pointer;
  }
  .t-otas-room {
    position: absolute;
    width: 10em;
    top: 27px;
    left: -14px;
    padding: 5px !important;
    display: none;
    background-color: #fafafa;
    box-shadow: 1px 1px 3px #000;
  }
  .t-otas-room table td{
    text-align: center;
  }
    .t-otas-room table td.benef,
  span.benef{
    padding: 0 3px 0 8px !important;
    color: #ff6b00;
    font-size: 18px;
  }
  .t-otas-room table{
    width: 98%;
    margin: 1%;
  }
  i.fa.fa-question-circle:hover  .t-otas-room{
    display: table;
  }
  select#currentMonth,
  select#currentYear {
    background-color: transparent;
    font-size: 21px;
  }
  .select-month{
    font-size: 2em;
    font-weight: 800;
    text-align: center;
  }

  .fa, .fas {
    font-size: 17px;
    font-weight: bold;
  }
  td.static {
    min-width: 12em;
    border: none !important;
  }
  td.day {
    min-width: 60px;
    border-left: 1px solid #000;
    padding: 4px !important;
    text-align: center;
  }
  td.month {
    border-left: 1px solid #000;
    padding: 4px !important;
    font-weight: 600;
  }
  td.day.w {
    font-weight: 600;
  }
  th.room-name{
    font-size: 1.7em;
  }
  .room-name table.table-prices {
    padding-top: 10px;
    display: block;
    max-width: 475px;
    padding-left: 2em;
  }
  td.day.red {
    background-color: #e49f9f;
    color: #fff;
  }
  .day.tPriceEdit,
  .day.tMinEdit{
    cursor: pointer;
    min-width: 65px;
  }
  .day.tPriceEdit.tSelect{
    background-color: yellow;
  }
  .day.tMinEdit.tSelect{
    background-color: #FF9800;
  }
  .day.tPriceEdit.tSelect input[type="number"],
  .day.tMinEdit.tSelect input[type="number"] {
    width: 55px;
    padding: 4px 2px;
    text-align: center;
    border: none;
    font-weight: 600;
  }

  #table_prices td.month {
    text-align: center;
  }
  #table_prices.table-resumen .static.white{
    background-color: #fff !important;
    height: 37px;
    min-width: 131px;
  }

  #table_prices.table-resumen th.room-name.static {
    min-height: 82px;
    background-color: #fff !important;
    border: none !important;
  }
  #table_prices.table-resumen .static{
    height: 37px;
    min-width: 138px;
    text-align: left;
    background-color: #f9f9f9 !important;
    overflow: hidden;
    margin: 0px auto;
    border-right: 2px solid #000;
    padding: 4px !important;
  }


  #table_prices.table-resumen tr.room-name {
    height: 65px;
  }

  #table_prices.table-resumen tr.room-name .col-2 {
    display: inline-block;
  }

  #table_prices.table-resumen tr.room-name .name{
    font-size: 1.7em;
    padding: 13px;
  }


  #table_prices.table-resumen tr.room-name th {
    background-color: #7a6bb3 !important;
    color: #fff;
    min-height: 65px;
    border: none !important;
    min-width: 6em;
    width: 19em;
  }
  #table_prices.table-resumen tr.room-name td {
    background-color: #7a6bb3 !important;
    color: #fff;
    min-height: 65px;
    border: none !important;
    min-width: 6em;
    width: 19em;
  }

  #table_prices.table-resumen .room-name table.table-prices td {
    text-align: left;
  }

  #table_prices.table-resumen tr.room-name td .col-md-8 {
    min-width: 4em;
    margin-top: 10px;
    padding-left: 7px;
    font-size: 23px;
  }

  td.room-name.static.static-header {
    width: 57em !important;
  }

  .table-responsive input{
    width: 55px;
  }
  .table-resumen-content{
    max-height: calc( 100vh - 25px);
    position: relative;
    overflow: scroll;
  }
  /*table#table_prices .col-2{
    width: 48%;
    float: left;
  }*/
  table#table_prices  thead th {
    position: -webkit-sticky; /* for Safari */
    position: sticky;
    top: 0;
    background: #6d5cae;
    color: #FFF;
    z-index: 10;
    text-align: center;
  }

  table#table_prices  tbody th {
    position: -webkit-sticky; /* for Safari */
    position: sticky;
    left: 0;
    background: #FFF;
    border-right: 1px solid #CCC;
    text-align: center;
    z-index: 999;
  }
  .select_column{
    cursor: pointer;
  }
  .day.tPriceEdit{
    cursor: pointer;
    min-width: 65px;
    font-weight: 800;
    color: #000;
  }

  table#table_prices tbody th.room-name.static.static-header {
    background-color: #7a6bb3 !important;
  }
  table#table_prices tbody th.room-name.mobile.static.static-header{
    z-index: 3;
  }
  table#table_prices tbody th.room-name.mobile .pricelist{
    text-align: left;
    color: #FFF;
    font-size: 12px;
    padding-top: 10px;
  }
  table#table_prices tbody th.room-name.mobile .pricelist div{
    display: inline-block;
    padding: 0 4px 0 7px;
  }

  #table_prices.table-resumen th.room-name.static{
    max-width: 275px;
  }

  #table_prices.table-resumen tr.room-name{
    height: 2em;
  }
  th.room-name .windows h4{
    margin: 0px;
  }

  #select_site{
    width: 80%;  
    margin-top: 0 !important
  }
    
  .buttons-box .btnCalendar{
    padding: 4px;
    min-width: 71px;
  }

  .select-site .openCalendar{
    float: left;
    margin-top: 7px;
    cursor: pointer;
  }

  .content-calendar .total {
    height: 20px !important;
  }

  .label_sel_col{
    font-size: 1.15em;font-weight: 800;text-align: right;
  }
  #table_prices.table-resumen tr.room-name td{
    text-align: left !important;
  }
  #table_prices.table-resumen tr.room-name td .price-booking:before{
    margin: 0 5px;
  }
  @media only screen and (max-width: 425px) {
    .select-site{
      clear: both;
      float: none;
      padding-top: 1em;
    }
    #table_prices.table-resumen tr.room-name {
      height: 49px;
    }
    #table_prices.table-resumen th.room-name.static{
      max-width: 175px;
    }
    /*  table#table_prices .col-2 {
          width: 310px;
          float: none;
      }*/
    .windows h3 {
      margin: 0px;
    }

    select#select_site {
      margin: 0 0 1em 2em;
    }
    .label_sel_col{
      padding: 1em 0;
    }
    select#sel_by_column {
      margin: 1em 0;
    }
  }

</style>
@endsection
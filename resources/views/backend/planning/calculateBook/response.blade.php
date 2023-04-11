<div class="col-xs-12">
  <div class="line" style="margin-bottom: 10px;"></div>
  <input type="hidden" id="calc_username" value="{{$name}}">
  <input type="hidden" id="calc_start" value="{{$start}}">
  <input type="hidden" id="calc_finish" value="{{$finish}}">
  <input type="hidden" id="calc_pax" value="{{$pax}}">
  
  <div class="table-responsive" style="overflow-y: hidden;">
    <table class="table table-resumen table-mobile_cr">
      <thead>
        <tr class ="text-center bg-success text-white">
          <th class="th-bookings text-center th-2 static">Disp.</th>
          <th class="th-bookings static-2" >Apto.</th>
          <th class="first-col"></th>
          <th class="th-bookings text-center th-2 BRight">Precio Final</th>
          <th class="th-bookings text-center th-2">Precio Inicial</th>
          <th class="th-bookings text-center th-2">Desc</th>
          <th class="th-bookings text-center th-2">Promo</th>
          <th class="th-bookings text-center th-2">Supl Limp</th>
          <th class="th-bookings text-center th-2">&nbsp;</th>
          <th class="th-bookings text-center th-2">&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        @if(count($rooms)>0)
        @foreach($rooms as $room)
        <tr >
          <td class="static">{{$room['availiable']}}</td>
          <td class="static-2">{{$room['title']}}</td>
          <td class="first-col"></td>
          <td class="text-center BRight"><b >{{moneda($room['pvp_1']+$room['extr_costs'])}}</b>
          <td class="text-center"><b >{{$room['price']}}</b></td>
          <td class="text-center text-danger"><b ><?php echo '-'.moneda($room['pvp_discount'],false); ?></b></td>
          <td class="text-center text-danger"><b ><?php echo ($room['pvp_promo']>0)? '-'.moneda($room['pvp_promo'],false) : '--'; ?></b></td>
          <td class="text-center"><b >{{moneda($room['extr_costs'])}}</b></td>
          
          </td>
          <td> 
            <?php if (Auth::user()->role != "agente"): ?>
              <button 
                type="button" 
                class="btn btn-success text-white calc_createNew"
                data-room="{{$room['code']}}"
                data-info="{{serialize($room)}}"
                >RESERVAR</button>
              <?php endif; ?>
          </td>
          <td>
            @if (isset($urlGH))
            <a href="{{$urlGH}}" target="_blank" class="">GHotels</a>
            @endif
          </td>
        </tr>
          @if($room['minStay']>$nigths)
          <tr>
            <td colspan="2" class="minStay BRight"><p class="text-danger">Estadía mínima {{$room['minStay']}}</p></td>
            <td colspan="6"></td>
          </tr>
          @endif
        @endforeach
        @else
        <tr>
          <td colspan="9" class="empty">
            No se encontraron resultados
          </td>
        </tr>
        @endif
      </tbody>
    </table>
  </div>
</div>
<style>
    div#calcReserv_result {
        background-color: white;
    }
    .table-resumen.table-mobile_cr th.static,
    .table-resumen.table-mobile_cr td.static {
        width: 15px !important;
        text-align: center !important;
        min-width: 42px;
        padding: 10px 0 0px 0px !important;
        min-height: 48px;
    }
    .table-resumen.table-mobile_cr td.static-2,
    .table-resumen.table-mobile_cr th.static-2 {
        position: absolute;
        background-color: white;
        border-right: 1px solid #efefef;
        z-index: 9;
        padding: 10px 0 0 6px !important;
        text-align: left;
        overflow-y: hidden;
        min-height: 48px;
        width: 163px;
        left: 48px;
    }
    td.minStay {
        text-align: left;
        padding: 6px 0 0 11px !important;
    }
    .table-resumen.table-mobile_cr th.th-bookings.static,
    .table-resumen.table-mobile_cr th.th-bookings.static-2 {
        height: 61px;
        background-color: #0fcfbd;
        border: none;
        text-align: center;
        padding: 15px 0 0 0px !important;
    }
    .table-resumen.table-mobile_cr th.th-bookings.static{
        padding-top: 11px !important;
    }
    .table-resumen.table-mobile_cr td.first-col,
    .table-resumen.table-mobile_cr th.first-col {
        margin-left: 62px;
        display: block;
        height: 48px;
        width: 1px;
        max-width: 1px;
        min-width: 1px;
    }

    .table-mobile_cr th.th-bookings.static {
      height: 40px !important;
    }
    .table-mobile_cr th.th-bookings.static-2 {
      height: 46px !important;
    }
    .table-resumen.table-mobile_cr td.static-2::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .table-resumen.table-mobile_cr td.static-2 {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    td.empty {
      background-color: orange !important;
      font-size: 22px !important;
      text-align: center;
    }
    .BRight{
  border-right: 1px solid;
}
    @media (min-width: 1760px){
        .table-resumen.table-mobile_cr td.static-2 {
            width: 238px;
        }
    }
</style>

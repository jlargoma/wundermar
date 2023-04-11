<div class="table-responsive">
  <table class="table perdidas_ganancias" id="tableItems">
    <thead>
      <tr>
        <th class="static" style="padding: 2em !important;"></th>
        <th class="first-col"></th>
        <th>Total</th>
        <th class="light-blue text-center">Pendiente</th>
        @foreach($lstMonths as $month) <th class="text-center">{{$month['name']}}</th> @endforeach
      </tr>
    </thead>
    <tbody>
      <tr class="pyg_ingresos">
        <th class="static" style="background-color:#4ec37b">INGRESOS</th>
        <th class="first-col"></th>
        <th class="text-center">{{moneda($totalIngr)}}</th>
        <th class="light-blue text-center">{{moneda($totalPendingIngr)}}</th>
        @foreach($tIngByMonth as $k=>$v)<th class="text-center"> {{moneda($v,false)}}</th> @endforeach
      </tr>
      @foreach($ingresos as $k=>$v)
      <tr>
        <td class="static">{{$ingrType[$k]}}</td>
        <td class="first-col"></td>
        <td class="text-center">{{moneda($lstT_ing[$k])}}</td>
        <td class="text-center">--</td>
        @foreach($lstMonths as $k_month=>$month) 
        @if($k == 'extr' || $k == 'others')
          <td class="text-center editable_ingr" data-key="{{$k}}" data-month="{{$k_month}}" data-val="{{moneda($ingresos[$k][$k_month],false)}}">
        @else
          <td class="text-center" >
        @endif
          {{moneda($ingresos[$k][$k_month],false)}}
        </td> 
        @endforeach
      </tr>
      @endforeach
      
      <tr class="pyg_gastos">
        <th class="static" style="background-color:#a94441">GASTOS</th>
        <th class="first-col"></th>
        <th class="text-center">{{moneda($totalGasto)}}</th>
        <th class="light-blue text-center">{{moneda($totalPendingGasto)}}</th>
        @foreach($tGastByMonth as $k=>$v)<th class="text-center"> {{moneda($v,false)}}</th> @endforeach
      </tr>
      @foreach($listGasto as $k=>$v)
      <tr>
        <td class="static open_detail" data-key="{{$k}}">{{$gastoType[$k]}}</td>
        <td class="first-col"></td>
        <td class="text-center">{{moneda($lstT_gast[$k])}}</td>
        @if($aExpensesPending[$k] === "N/A")
        <td class="text-center editable" data-current="0" data-key="{{$k}}" data-val="{{moneda($aExpensesPendingOrig[$k],false)}}">
          N/A
        </td>
        @else
        <td class="text-center editable" data-current="1" data-key="{{$k}}" data-val="{{moneda($aExpensesPendingOrig[$k],false)}}">
          {{moneda($aExpensesPending[$k],false)}}
        </td>
          @endif
        @foreach($lstMonths as $k_month=>$month) <td class="text-center">{{moneda($listGasto[$k][$k_month],false)}}</td> @endforeach
      </tr>
      @endforeach
      
      <tr class="pyg_beneficio">
        <th class="static">BENEFICIO BRUTO</th>
        <th class="first-col"></th>
        <th class="text-center">{{moneda($totalIngr-$totalGasto)}}</th>
        <th class="light-blue text-center">{{moneda($totalPendingIngr-$totalPendingGasto)}}</th>
        @foreach($lstMonths as $k_month=>$v)<th class="text-center"> {{moneda(($tIngByMonth[$k_month]-$tGastByMonth[$k_month]))}}</th> @endforeach
      </tr>
      <tr>
        <th class="static open_detail" data-key="impuestos"  style="background-color:#FFF">
          IMPUESTOS</th>
        <th class="first-col"></th>
        <td class="text-center">{{moneda($lstT_gast['impuestos'])}}</td>
        <td class="text-center">{{moneda($totalPendingImp)}}</td>
        @foreach($lstMonths as $k_month=>$month) 
          <td class="text-center">
            {{moneda($impuestos[$k_month],false)}}<br/>
          </td> 
        @endforeach
      </tr>
      <tr class="pyg_beneficio">
        <th class="static">BENEF NETO</th>
        <th class="first-col"></th>
        <th class="text-center">{{moneda($totalIngr-$totalGasto-$lstT_gast['impuestos'])}}</th>
        <th class="light-blue text-center">{{moneda($totalPendingIngr-$totalPendingGasto-$totalPendingImp)}}</th>
        @foreach($lstMonths as $k_month=>$v)<th class="text-center"> {{moneda(($tIngByMonth[$k_month]-$tGastByMonth[$k_month]-$impuestos[$k_month]))}}</th> @endforeach
      </tr>
    </tbody>
  </table>
</div>
<style>

  .perdidas_ganancias thead{
    background-color: #2b5d9b;
  }
  .table.perdidas_ganancias thead tr th{
    color: #fff;
  }
  
  .perdidas_ganancias .pyg_ingresos{
    background-color: #4ec37b;
  }
  .table.perdidas_ganancias tr.pyg_ingresos th{
    color: #fff;
  }
  .perdidas_ganancias .pyg_gastos{
    background-color: #a94441;
  }
  .table.perdidas_ganancias tr.pyg_gastos th{
    color: #fff;
  }
  .perdidas_ganancias .pyg_beneficio{
    background-color: #2c5d9b;
  }
  .table.perdidas_ganancias tr.pyg_beneficio th{
    color: #fff;
  }
  .perdidas_ganancias .pendientes{
    background-color: #48b0f7;
  }
  .table.perdidas_ganancias td,
  .table.perdidas_ganancias th{
   white-space: nowrap; 
  }
  th.static{
    background-color: #2c5d9b;
    height: auto;
    padding: 8px !important;
    margin: 1px auto;
    border: none !important;
  }
  .table.perdidas_ganancias .static{
    width: 9em;
    overflow: auto;
  }
  .first-col{
    padding-left: 10em !important;
  }
  </style>
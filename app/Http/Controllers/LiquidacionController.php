<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use \DB;
use App\Classes\Mobile;
use Excel;
use Auth;
use App\Book;
use App\BookDay;
use App\Liquidacion;
use App\Settings;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");

class LiquidacionController extends AppController {

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {

    $oLiq = new Liquidacion();
    $data = $this->getTableData();
    $oYear = $this->getActiveYear(); 
    $data['summary'] = $oLiq->summaryTemp($oYear);
    $data['stripeCost'] = $oLiq->getTPV($data['books']);
    $data['total_stripeCost'] = array_sum($data['stripeCost']);
    
     $cUser = Auth::user();
    /***************************************************************************/
    //es visible para Jaime ( subadministrador) y mariajo y jorge
    $salesByUser = [];
    if (in_array($cUser->id,[40,46,3])){
      $uIds = [40=>'Jorge',3=>'Mariajo',98=>'Web direct'];
      $lstYears = \App\Years::where('year','<=',$oYear->year)->orderBy('year','DESC')->limit(5)->get();
      $type_book = Book::get_type_book_sales(true,true);
      $salesByUser = [40=>[],3=>[],98=>[],0=>[]];
      $yearsLst = [];
      foreach ($lstYears as $year){
        $yearsLst[] = $year->year;
        foreach ($uIds as $uID => $name){
          $salesByUser[$uID][$year->year] = 0;
          $tPvp = Book::where_book_times($year->start_date,$year->end_date)
                  ->where('user_id',$uID)
                  ->whereIn('type_book',$type_book)->sum('total_price');
          if ($tPvp)  $salesByUser[$uID][$year->year] = $tPvp;
        }
        
        $tPvp = Book::where_book_times($year->start_date,$year->end_date)
                  ->whereNotIn('user_id', array_keys($uIds))
                  ->whereIn('type_book',$type_book)->sum('total_price');
        if ($tPvp)  $salesByUser[0][$year->year] = $tPvp;
        else  $salesByUser[0][$year->year] = 0;
      }
      $data['salesByUser'] = $salesByUser;
      $data['uIdName'] = $uIds;
      $data['yearsLst'] = $yearsLst;
      $tYear = [];
      foreach ($salesByUser as $k=>$v){
        foreach ($v as $k2=>$v2){
          if (!isset($tYear[$k2])) $tYear[$k2] = intVal($v2);
          else $tYear[$k2] += intVal($v2);
        }
      }
      $data['tYear'] = $tYear;
    }
    /***************************************************************************/
    if ($cUser->role != "admin"){
      return view('backend/sales/index-subadmin', $data);
    }
   
          
          
      return view('backend/sales/index', $data);
  }
  
  function getTableData($customerIDs=null,$agency=null,$roomID=null,$type=null){
        $totales = [
        "total" => 0,
        "coste" => 0,
        "bancoJorge" => 0,
        "bancoJaime" => 0,
        "jorge" => 0,
        "jaime" => 0,
        "costeApto" => 0,
        "costePark" => 0,
        "costeLujo" => 0,
        "costeLimp" => 0,
        "costeAgencia" => 0,
        "benJorge" => 0,
        "benJaime" => 0,
        "pendiente" => 0,
        "limpieza" => 0,
        "beneficio" => 0,
        "stripe" => 0,
        "obs" => 0,
        'adicionales'=>0
    ];
    
    $liquidacion = new \App\Liquidacion();

    $oYear = $this->getActiveYear();
    $startYear = new Carbon($oYear->start_date);
    $endYear = new Carbon($oYear->end_date);

    $qry_books = Book::where_type_book_sales(true,true)->with([
                        'customer',
                        'payments',
                        'room.type'
                    ])->whereYear('start','=', $oYear->year);

    if (is_array($customerIDs) && count($customerIDs)){
      $qry_books->whereIn('customer_id', $customerIDs);
    }
    
    if ($agency && $agency>0){
      $qry_books->where('agency', $agency);
    }
    if ($type && $type>0){
      $qry_books->where('type_book', $type);
    }
    if ($roomID && $roomID>0){
      $qry_books->where('room_id', $roomID);
    }
    
    $books = $qry_books->orderBy('start', 'ASC')->get();
            
    $alert_lowProfits = 0; //To the alert efect
    //$percentBenef = DB::table('percent')->find(1)->percent;
    $percentBenef     = Settings::getKeyValue('percentBenef');
    $lowProfits = [];

    $additionals = [];
    foreach ($books as $key => $book) {

      // if($book->type_book != 7 && $book->type_book != 8){
      $totales["total"] += $book->total_price;
      $totales["costeApto"] += $book->cost_apto;
      $totales["costePark"] += $book->cost_park;
      $totales["coste"] += $book->get_costeTotal();
      if ($book->room->luxury == 1) {
        $totales["costeLujo"] += $book->cost_lujo;
      }

      $totales["costeLimp"] += $book->cost_limp;
      $totales["costeAgencia"] += $book->PVPAgencia;
      $totales["bancoJorge"] += $book->getPayment(2);
      $totales["bancoJaime"] += $book->getPayment(3);
      $totales["jorge"] += $book->getPayment(0);
      $totales["jaime"] += $book->getPayment(1);
      $totales["benJorge"] += $book->getJorgeProfit();
      $totales["benJaime"] += $book->getJaimeProfit();
      $totales["limpieza"] += $book->sup_limp;
//      $totales["beneficio"] += $book->profit;
      $totales["stripe"] += $book->stripeCost;
      $totales["obs"] += $book->extraCost;
      $totales["pendiente"] += $book->pending;
      // }
      //Alarms
      $inc_percent = $book->get_inc_percent();
      if (round($inc_percent) <= $percentBenef) {
        if (!$book->has_low_profit) {
          $alert_lowProfits++;
        }
        $lowProfits[] = $book;
      }
      
      $additionals[$book->id] = null;
      $oAdditional = $book->extrasDynamicList();
      if (count($oAdditional) > 0){
        $breakfast = $excursion = $miniBar = $others = $t_addtional = 0;
        foreach ($oAdditional as $e){
          switch ($e->type){
            case 'breakfast': $breakfast += $e->cost; break;
            case 'excursion': $excursion += $e->cost; break;
            case 'minibar':   $miniBar   += $e->cost; break;
            default :   $others   += $e->cost; break;
          }
          $t_addtional += $e->cost;
        }
        $text_additional = 'Desayuno: '.round($breakfast)
                .' €<br/> Excursiones: '.round($excursion)
                .' €<br/>MiniBar: '.round($miniBar).' €';
        if ($others>0){
          $text_additional.='<br/> Otros: '.$others.' €';
        }
        $additionals[$book->id] = [
            'total' => round($t_addtional),
            'text' => $text_additional
        ];
        
        $totales["adicionales"] += $t_addtional;
//        $totales["coste"] += $t_addtional;
      }
    }

    $totales["beneficio"] = $totales["total"]-$totales["coste"];
    $totBooks = (count($books) > 0) ? count($books) : 1;
    $diasPropios = \App\Book::where('start', '>=', $startYear)
                    ->where('finish', '<=', $endYear)
                    ->whereIn('type_book', [
                        7,
                        8
                    ])->orderBy('created_at', 'DESC')->get();

    $countDiasPropios = 0;
    foreach ($diasPropios as $key => $book) {
      $start = Carbon::createFromFormat('Y-m-d', $book->start);
      $finish = Carbon::createFromFormat('Y-m-d', $book->finish);
      $countDays = $start->diffInDays($finish);

      $countDiasPropios += $countDays;
    }

    /* INDICADORES DE LA TEMPORADA */
    $data = [
        'days-ocupation' => 0,
        'total-days-season' => $oYear->getNumDays(),
        'num-pax' => 0,
        'estancia-media' => 0,
        'pax-media' => 0,
        'precio-dia-media' => 0,
        'dias-propios' => $countDiasPropios,
        'agencia' => 0,
        'propios' => 0,
    ];

    foreach ($books as $key => $book) {

      $start = Carbon::createFromFormat('Y-m-d', $book->start);
      $finish = Carbon::createFromFormat('Y-m-d', $book->finish);
      $countDays = $start->diffInDays($finish);

      /* Dias ocupados */
      $data['days-ocupation'] += $countDays;

      /* Nº inquilinos */
      $data['num-pax'] += $book->pax;


      if ($book->agency != 0) {
        $data['agencia'] ++;
      } else {
        $data['propios'] ++;
      }
    }

    $data['agencia'] = ($data['agencia'] / $totBooks) * 100;
    $data['propios'] = ($data['propios'] / $totBooks) * 100;

    /* Estancia media */
    $data['estancia-media'] = ($data['days-ocupation'] / $totBooks);

    /* Inquilinos media */
    $data['pax-media'] = ($data['num-pax'] / $totBooks);
    
    
     return [
          'books' => $books,
          'lowProfits' => $lowProfits,
          'alert_lowProfits' => $alert_lowProfits,
          'percentBenef' => $percentBenef,
          'totales' => $totales,
          'year' => $oYear,
          'data' => $data,
          'additionals' => $additionals
      ];
     
  }

  public function getFF_Data($startYear,$endYear) {
    $allForfaits = Forfaits::where('status','!=',1)
            ->where('created_at', '>=', $startYear)->where('created_at', '<=', $endYear)->get();
      
    $totalPrice = 0;
    $forfaitsIDs = $ordersID = array();
    if ($allForfaits){
      foreach ($allForfaits as $forfait){
        $allOrders = $forfait->orders()->get();
        if ($allOrders){
            foreach ($allOrders as $order){
              if ($order->status == 0 || $order->status == 3){ 
                continue; // cancel and open orders
              }
              $totalPrice += $order->total;
              $ordersID[] = $order->id;
            }
        }
      }
    }
    
    $totalPayment =  ForfaitsOrderPayments::whereIn('order_id', $ordersID)->where('paid',1)->sum('amount');
    if ($totalPayment>0){
      $totalPayment = $totalPayment/100;
    }
    $totalPayment2 =  ForfaitsOrderPayments::whereIn('forfats_id', $forfaitsIDs)->where('paid',1)->sum('amount');

    if ($totalPayment2>0){
      $totalPayment += $totalPayment2/100;
    }
    $totalToPay = $totalPrice - $totalPayment;
      
    return [
        'q'=>count($ordersID),
        'to_pay'=>$totalToPay,
        'total'=>$totalPrice,
        'pay'=>$totalPayment
    ];
      
  }
  
  public function contabilidad() {
    $data = $this->prepareTables();
    $months_empty = $data['months_empty'];
    $lstMonths = $data['lstMonths'];
    $t_room_month = $data['t_room_month'];
    $year = $data['year'];
    $channels = $data['channels'];
    $siteRooms = $data['siteRooms'];
    $sales_rooms = $data['sales_rooms'];
    $books = $data['books'];
    
    $cobrado = $metalico = $banco = $vendido = 0;
    foreach ($books as $key => $book) {
      if ($book->payments){
        foreach ($book->payments as $pay){
          $cobrado += $pay->import;
          if ($pay->type == 0 || $pay->type == 1) {
            $metalico += $pay->import;
          } else if ($pay->type == 2 || $pay->type == 3) {
            $banco += $pay->import;
          }
        }
      }
    }
    
    //First chart PVP by months
    $dataChartMonths = [];
    foreach ($lstMonths as $k=>$v){
      $val = isset($t_room_month[$k]) ? $t_room_month[$k] : 0;
      $dataChartMonths[getMonthsSpanish($v['m'])] = $val;
    }
   
    /// BEGIN: Extras
    $months_extras = $months_empty;
    
    $extTyp = \App\ExtraPrices::getTypes();
    $oExtras = \App\ExtraPrices::getDynamic();
    
    $extrasList =  array();
    $extraTit   = array();
    foreach ($oExtras as $item){
      $extrasList[$item->id] = $months_empty;
      $extraTit[$item->id] = $item->name;
    }
    $extrasGroup =  array();
    foreach ($extTyp as $k=>$v){
      $extrasGroup[$k] = $months_empty;
    }
 
    $extras = Book::where_type_book_sales()
            ->select('book_extra_prices.price','book_extra_prices.type','start','extra_id')
            ->Join('book_extra_prices','book_extra_prices.book_id','=','book.id')
              ->whereYear('start', '=', $year->year)
              ->where('book_extra_prices.deleted',0)
              ->get();
    if($extras){
      foreach ($extras as $e){
        $m = date('n', strtotime($e->start));
        $months_extras[$m] += $e->price;
        $months_extras[0] += $e->price;
        
        $type = isset($extrasGroup[$e->type]) ? $e->type : 'others';

        $extrasGroup[$type][$m] += $e->price;
        $extrasGroup[$type][0] += $e->price;
        if (isset($extrasList[$e->extra_id])){
          $extrasList[$e->extra_id][$m] += $e->price;
          $extrasList[$e->extra_id][0] += $e->price;
        }
      } 
    }
    
    /// BEGIN: Disponibilidad
    $book = new \App\Book();
    $ch_monthOcup = array();
    $ch_monthOcupPercent = array();
    $monthsDays = $months_empty;
    foreach ($monthsDays as $m=>$d){
      if ($m>0)
      $monthsDays[$m] = cal_days_in_month(CAL_GREGORIAN, $m, $year->year);
    }
    foreach ($channels as $ch=>$d){
      $ch_monthOcup[$ch] = $months_empty;
      $ch_monthOcupPercent[$ch] = $months_empty;
      $availibility = $book->getAvailibilityBy_channel($ch, $year->year.'-01-01', $year->year.'-12-31',true);
      foreach ($availibility[0] as $day=>$used){
        if ($used>0){
         $ch_monthOcup[$ch][date('n', strtotime($day))] += $used;
        }
      }
      foreach ($ch_monthOcup[$ch] as $k=>$avail){
      
        if ($k>0){
         
          $aux = $availibility[1]*$monthsDays[$k];
          if ($aux>0){   
            $aux2 = $aux-$avail;
//            $ch_monthOcupPercent[$ch][$k] = $avail.'+'.$aux;
            $ch_monthOcupPercent[$ch][$k] = round($aux2/$aux*100);
          }
        }
      }
    }
       
    $allSites = \App\Sites::allSites();
    $dispBySite = [];
    foreach ($allSites as $site_id=>$name){
      $dispBySite[$site_id] = ['c'=>0,'t'=>0];
    }
    foreach ($siteRooms as $sID=>$item){
      if (isset($item['channels']))
        foreach ($item['channels'] as $ch){
          if (isset($ch_monthOcupPercent[$ch])){
            foreach ($ch_monthOcupPercent[$ch] as $k=>$v){
              if($v){
                $dispBySite[$sID]['c']++;
                $dispBySite[$sID]['t'] += intval($v);
              }
            }
          }
        }
      
    }
    // END: Disponibilidad
    ///////////////////////////////
    //BEING: KPI
    // $kpi = $this->getKPIs(array_unique($siteRooms[2]['channels']));
    $kpi = null;
    //END: KPI     
  
    
    
    
    return view('backend/sales/contabilidad', [
        'year' => $year,
        'sales_rooms' => $sales_rooms,
        'lstMonths' => $lstMonths,
        't_rooms' => $data['t_rooms'],
        't_room_month' => $data['t_room_month'],
        't_all_rooms' => (is_numeric($data['t_all_rooms']) && $data['t_all_rooms']>0) ? $data['t_all_rooms']:1,
        'siteRooms'=>$data['siteRooms'],
        'channels'=>$data['channels'],
        'cobrado' => $cobrado,
        'metalico' =>$metalico,
        'banco' =>$banco,
        'vendido'=>$vendido,
        'dataChartMonths' => $dataChartMonths,
        'months_extras' => $months_extras,
        'ch_monthOcupPercent'=>$ch_monthOcupPercent,
        'dispBySite'=>$dispBySite,
        'kpi_data' => $kpi,
        'extrasGroup' => $extrasGroup,
        'extTyp' => $extTyp,
        'extrasList' => $extrasList,
        'extraTit' => $extraTit,
        ]);
  }
  
  
  public function gastosBy_month($month) {
    $gastos = \App\Expenses::where('date', '=', $year->year)->get();
    $gType = \App\Expenses::getTypes();
    if ($gastos){
      foreach ($gastos as $g){
        
      }
    }
  }

  public function gastosDel(Request $request) {
    $id = $request->input('id');
    \App\Expenses::where('id',$id)->delete(); 
    return 'ok';
  }
  public function gastos($current=null) {
    
    $year = $this->getActiveYear();
    
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    $diff = $startYear->diffInMonths($endYear) + 1;
    $lstMonths = lstMonths($startYear,$endYear);
    
    $months_empty = array();
    for($i=0;$i<13;$i++) $months_empty[$i] = 0;
    
    $yearMonths = [
        ($year->year)-2 => $months_empty,
        ($year->year)-1 => $months_empty,
        ($year->year) => $months_empty,
    ];
    
    $allSites = \App\Sites::allSites();
    $sites = [];
    foreach ($allSites as $site_id=>$name){
      $sites[$site_id] = ['t'=>$name,'y'=>[],'months'=>$months_empty];
    }

    $gastos = \App\Expenses::whereYear('date','=', $year->year)->get();
    $gType = \App\Expenses::getTypes();
    $gTypeGroup = \App\Expenses::getTypesGroup();
    $gTypeGroup_g = $gTypeGroup['groups'];
    
    $listGastos = array();
    if ($gType){
      foreach ($gType as $k=>$v){
        $listGastos[$k] = $months_empty;
      }
    }
    $listGastos_g = array();
    if ($gTypeGroup_g){
      foreach ($gTypeGroup_g as $k=>$v){
        $listGastos_g[$v] = $months_empty;
      }
      $listGastos_g['otros'] = $months_empty;
    }
    $totalYearAmount = 0;
    if ($gastos){
      foreach ($gastos as $g){
        $month = date('n', strtotime($g->date));
        
        if ($g->site_id>0){
          if (isset($sites[$g->site_id])){
            $sites[$g->site_id]['months'][$month] += $g->import;
            $sites[$g->site_id]['months'][0] += $g->import;
          }
        } else {
          $mounth = round(intval($g->import) / count($sites),2);
          foreach ($sites as $sID=>$v){
            $sites[$sID]['months'][$month] += $mounth;
            $sites[$sID]['months'][0] += $mounth;
          }
        }
        
        $totalYearAmount += $g->import;
        $yearMonths[$year->year][$month] += $g->import;
        
        $gTipe = isset($gTypeGroup_g[$g->type]) ? $gTypeGroup_g[$g->type] : 'otros';
        
        if (isset($listGastos_g[$gTipe])){
          $listGastos_g[$gTipe][$month] += $g->import;
          $listGastos_g[$gTipe][0] += $g->import;
        }
        
        if (isset($listGastos[$g->type])){
          $listGastos[$g->type][$month] += $g->import;
          $listGastos[$g->type][0] += $g->import;
        }
      }
    }
    $auxYear = ($year->year)-2;
    $gastos = \App\Expenses::whereYear('date','=', $auxYear)->get();
    if ($gastos){
      foreach ($gastos as $g){
        $month = date('n', strtotime($g->date));
        $yearMonths[$auxYear][$month] += $g->import;
      }
    }
    $auxYear = ($year->year)-1;
    $gastos = \App\Expenses::whereYear('date','=', $auxYear)->get();
    if ($gastos){
      foreach ($gastos as $g){
        $month = date('n', strtotime($g->date));
        $yearMonths[$auxYear][$month] += $g->import;
      }
    }
    
    
    
     //First chart PVP by months
    $dataChartMonths = [];
    foreach ($lstMonths as $k=>$v){
      $val = isset($listGastos[$k]) ? $listGastos[$k] : 0;
      $dataChartMonths[getMonthsSpanish($v['m'])] = $val;
    }
    
    $totalYear=[];
    foreach ($sites as $k=>$v){
      $aux_year = $year->year-3;
      for($i=0;$i<3;$i++){
        $aux_year += 1;
        $total = \App\Expenses::whereYear('date','=',$aux_year)
                ->where('site_id',$k)->sum('import');
        $sites[$k]['y'][$aux_year] = round($total);
        
        if (isset($totalYear[$aux_year])) $totalYear[$aux_year] += $total;
        else $totalYear[$aux_year] = $total;
       
      }
    }
        
    
    $totalYearSite = []; 
    foreach ($sites as $sID => $item){
      $aux = $item['months'];
      unset($aux[0]);
      $totalYearSite[$sID] = implode(',', $aux);
    }
    
    if (!$current){
      $current = ($year->year-2000).','.date('m');
    }
    
   
    return view('backend/sales/gastos/index', [
        'year' => $year,
        'lstMonths' => $lstMonths,
        'dataChartMonths' => $dataChartMonths,
        'gType' => $gType,
        'gastos' => $listGastos,
        'gTypeGroup' => $gTypeGroup['names'],
        'listGasto_g' => $listGastos_g,
        'gastosSitio' => $sites,
        'current' => $current,
        'totalYear' => $totalYear,
        'total_year_amount' => $totalYearAmount,
        'yearMonths' => $yearMonths,
        'totalYearSite' => $totalYearSite,
        'allSites' => \App\Sites::allSites(),
        'typePayment' => \App\Expenses::getTypeCobro()
    ]);
  }

  public function gastoCreate(Request $request) {

    $messages = [
      'concept.required' => 'El Concepto es requerido.',
      'import.required' => 'El Importe es requerido.',
      'fecha.required' => 'La Fecha es requerida.',
      'concept.min'    => 'El Concepto debe tener un mínimo de :min caracteres.',
      'import.min'    => 'El Importe debe ser mayor de :min.',
      'import.max'    => 'El Importe no debe ser mayor de :max.',
    ];
    
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'concept' => 'required|min:3',
            'import' => 'required|min:1|max:3000',
            'fecha' => 'required',
        ],$messages);

    if ($validator->fails()) {
      return $validator->errors()->first();
    }
        
    $gasto = new \App\Expenses();
    $gasto->concept = $request->input('concept');
    $gasto->date = Carbon::createFromFormat('d/m/Y', $request->input('fecha'))->format('Y-m-d');
    $gasto->import = $request->input('import');
    $gasto->typePayment = $request->input('type_payment');
    $gasto->type = $request->input('type');
    $gasto->comment = $request->input('comment');
    $gasto->site_id = $request->input('site_id',null);

    if ($gasto->save()) {
      return 'ok';
    } 
    
    return 'error';
    
  }

public function updateGasto(Request $request) {
    
    $id = $request->input('id');
    $type = $request->input('type');
    $val = $request->input('val');
    $gasto = \App\Expenses::find($id);
    if ($gasto){
      $save = false;
      switch ($type){
        case 'price':
          $gasto->import = $val;
          $save = true;
          break;
        case 'comm':
          $gasto->comment = $val;
          $save = true;
          break;
        case 'concept':
          $gasto->concept = $val;
          $save = true;
          break;
        case 'type':
          $gasto->type = $val;
          $save = true;
          break;
        case 'payment':
          $gasto->typePayment = $val;
          $save = true;
          break;
        case 'site':
          $gasto->site_id = $val;
          $save = true;
          break;
      }
      
      if ($save){
        if ($gasto->save()) {
          return "ok";
        }
      }
    }

    return 'error';
 
  }

  /**
   * Get the Gastos by month-years to ajax table
   * 
   * @param Request $request
   * @return Json-Objet
   */
  
  public function getTableGastos(Request $request, $isAjax = true) {

    $year = $request->input('year', null);
    $month = $request->input('month', null);
    if (!$year) {
      return response()->json(['status' => 'wrong']);
    }
    
    $sites = \App\Sites::allSites();
    if ($year<100) $year = '20'.$year;
    $qry = \App\Expenses::whereYear('date','=', $year);
    
    if ($month && $month>0)  $qry->whereMonth('date','=', $month);
            
     $gastos = $qry->orderBy('date')->get();
    $gType = \App\Expenses::getTypes();
    $response = [
        'status' => 'false',
        'respo_list' => [],
    ];
    $totalMounth = 0;
    $typePayment = \App\Expenses::getTypeCobro();
    if ($gastos){
      $respo_list = array();
      foreach ($gastos as $item){
        $respo_list[] = [
            'id'=> $item->id,
            'concept'=> $item->concept,
            'date'=> convertDateToShow_text($item->date),
            'typePayment'=> isset($typePayment[$item->typePayment]) ? $typePayment[$item->typePayment] : '--',
            'typePayment_v'=> $item->typePayment,
            'type'=> isset($gType[$item->type]) ? $gType[$item->type] : '--',
            'type_v'=> $item->type,
            'comment'=> $item->comment,
            'import'=> $item->import,
            'site'=> isset($sites[$item->site_id]) ? $sites[$item->site_id] : 'General',
            'site_id'=> $item->site_id,
        ];
        $totalMounth += $item->import;
      }
     
      $response = [
          'status' => 'true',
          'respo_list' => $respo_list,
          'totalMounth' => moneda($totalMounth),
      ];
    }
    
    if ($isAjax) {
      return response()->json($response);
    } else {
      return $response;
    }
  }

  public function prepareTables() {
    $year = $this->getActiveYear();
    return self::static_prepareTables($year);
  }
  static function static_prepareTables($year) {
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    $diff = $startYear->diffInMonths($endYear) + 1;
    $lstMonths = lstMonths($startYear,$endYear);
    
    $books = \App\BookDay::where_type_book_sales(true,true)
            ->whereYear('date', '=', $year->year)
            ->get();
    
    
    $months_empty = array();
    for($i=0;$i<13;$i++) $months_empty[$i] = 0;
    
        
    $channels = configZodomusAptos();
        
    $allSites = \App\Sites::allSites();
    $siteRooms = $aux = [];
    foreach ($allSites as $site_id=>$name){
      $siteRooms[$site_id] = ['t'=>$name,'months'=>$months_empty,'channels'=>[]];
      $aux[$site_id] = [];
    }
  
    foreach ($siteRooms as $sID=>$d){
      $rooms = \App\Rooms::where('site_id',$sID)->orderBy('order', 'ASC')->get();
      foreach ($rooms as $r){
        $channel_group = $r->channel_group;
        if (!isset($siteRooms[$sID][$channel_group])){
          $siteRooms[$sID][$channel_group] = ['rooms'=>[],'months'=>$months_empty];
        }
        $siteRooms[$sID]['channels'][] = $channel_group;

        $siteRooms[$sID][$channel_group]['rooms'][$r->id] = $r->name;
      }
    }
    
    $sales_rooms = [];
    foreach ($books as $key => $book) {
      $date = date('n', strtotime($book->date));
      if (!isset($sales_rooms[$book->room_id])) $sales_rooms[$book->room_id] = [];
      if (!isset($sales_rooms[$book->room_id][$date])) $sales_rooms[$book->room_id][$date] = 0;
      $sales_rooms[$book->room_id][$date] += $book->pvp;
      
    }
    //group Rooms
    foreach ($siteRooms as $sID=>$d1){
      $auxMonth = $months_empty;
      foreach ($d1 as $ch=>$d2){
        if ($ch != 't' && $ch != 'months' && $ch != 'channels'){
          $auxMonth2 = $months_empty;
          foreach ($d2['rooms'] as $roomID=>$d3 ){
            if (isset($sales_rooms[$roomID])){
              foreach ($sales_rooms[$roomID] as $date=>$total ){
                $auxMonth2[0] += $total;
                $auxMonth2[$date] += $total;
                $auxMonth[$date] += $total;
                $auxMonth[0] += $total;
              }
            }
            $siteRooms[$sID][$ch]['months'] = $auxMonth2;
          }
        }
        
      }
      $siteRooms[$sID]['months'] = $auxMonth;
    }
    //prepate Rooms Table
    $t_rooms = [];
    $t_room_month = [];
    $t_all_rooms = 0;
    foreach ($sales_rooms as $r => $data){
      foreach ($data as $month => $val){
        $t_all_rooms += $val;
        
        if (!isset($t_rooms[$r])) $t_rooms[$r] = 0;
        $t_rooms[$r] += $val;
        
        if (!isset($t_room_month[$month])) $t_room_month[$month] = 0;
        $t_room_month[$month] += $val;
        
      }
    }
    return [
        'books' => $books,
        'lstMonths' => $lstMonths,
        't_room_month' => $t_room_month,
        'months_empty' => $months_empty,
        'year' => $year,
        'sales_rooms' => $sales_rooms,
        't_rooms' => $t_rooms,
        't_room_month' => $t_room_month,
        't_all_rooms' => $t_all_rooms,
        'siteRooms' => $siteRooms,
        'channels' => $channels,
        ];
  }
  
            
            
  public function ingresos() {
    $data = $this->prepareTables();
    $months_empty = $data['months_empty'];
    $lstMonths = $data['lstMonths'];
    $t_room_month = $data['t_room_month'];
    $year = $data['year'];
    $books = $data['books'];
    
    //First chart PVP by months
    $dataChartMonths = [];
    foreach ($lstMonths as $k=>$v){
      $val = isset($t_room_month[$k]) ? $t_room_month[$k] : 0;
      $dataChartMonths[getMonthsSpanish($v['m'])] = $val;
    }
    
    
    /// BEGIN: Extras
    $months_extras = $months_empty;
    
    $extTyp = \App\ExtraPrices::getTypes();
    $oExtras = \App\ExtraPrices::getDynamic();
    $extrTypes = \App\ExtraPrices::whereNotNull('type')->pluck('type','id')->toArray();
    $extrasList =  array();
    $extraTit   = array();
    foreach ($oExtras as $item){
      $extrasList[$item->id] = $months_empty;
      $extraTit[$item->id] = $item->name;
    }
    
    $extrasGroup =  array();
    $suplResume  = $months_empty;
    foreach ($extTyp as $k=>$v){
      $extrasGroup[$k] = $months_empty;
    }
 
    $bIDs = [];//dd($extrTypes);
    foreach ($data['books'] as $b){
      if (!empty($b->extrs)){
        $extr = json_decode($b->extrs, true);
        if ($extr){
          $m = intval(substr($b->date, 5,2));
          foreach ($extr as $extID=>$v){
            $months_extras[$m] += $v;
            $months_extras[0] += $v;
            $type = isset($extrTypes[$extID]) ? $extrTypes[$extID] : 'others';
            if (!isset($extrasGroup[$type])) $type = 'others';
            $extrasGroup[$type][$m] += $v;
            $suplResume[$m] += $v;
            $extrasGroup[$type][0] += $v;
            
            if (isset($extrasList[$extID])){
              $extrasList[$extID][$m] += $v;
              $extrasList[$extID][0] += $v;
            }
              
          }
        }
      }
    }
    // BEGIN: Ingr X mes
    
    $ingrType = \App\Incomes::getTypes();
    $ingrMonths = array();
    foreach ($ingrType as $k=>$t){
      $ingrMonths[$k] = $months_empty;
    }
    $incomesLst = \App\Incomes::whereYear('date', '=', $year->year)
            ->where('type','!=','book')->orderBy('date')->get();
    if ($incomesLst){
      foreach ($incomesLst as $item){
        $date = date('n', strtotime($item->date));
        $type = isset($ingrType[$item->type]) ? $item->type : 'others';
        if (!isset($ingrMonths[$type][$date])) $ingrMonths[$type][$date] = 0;
        $ingrMonths[$type][$date] += $item->import;
        $ingrMonths[$type][0] += $item->import;
       
      }
    }
    $ingrType['extras'] = "SUPLEMENTOS";
    $ingrMonths['extras'] = $suplResume;
    $ingrMonths['extras'][0] = array_sum($suplResume);
    // END: Ingr X mes
    ///////////////////////////////
    return view('backend/sales/ingresos/index', [
        'year' => $year,
        'sales_rooms' => $data['sales_rooms'],
        'lstMonths' => $lstMonths,
        't_rooms' => $data['t_rooms'],
        't_room_month' => $data['t_room_month'],
        't_all_rooms' => (is_numeric($data['t_all_rooms']) && $data['t_all_rooms']>0) ? $data['t_all_rooms']:1,
        'dataChartMonths' => $dataChartMonths,
        'months_extras' => $months_extras,
        'siteRooms'=>$data['siteRooms'],
        'channels'=>$data['channels'],
        'extrasGroup' => $extrasGroup,
        'extTyp' => $extTyp,
        'ingrMonths' => $ingrMonths,
        'ingrType' => $ingrType,
        'extrasList' => $extrasList,
        'extraTit' => $extraTit,
        'incomesLst' => $incomesLst,
        ]);
  }
    

  public function ingresosUpd(Request $request) {
    
    $year = $this->getActiveYear();
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    
    
    $type = $request->input('k',null);
    $val  = $request->input('val',null);
    $m    = $request->input('m',null);
    $y    = $request->input('y',null);
               
    if (!is_numeric($val)) return 'error';
    if ($y) $y += 2000;
    if ($m<10) $m = "0$m";
    
    $ingreso = \App\Incomes::where('month',$m)
              ->where('year',$y)
              ->where('concept',$type)
              ->first();
    if (!$ingreso){
      $ingreso = new \App\Incomes();
      $ingreso->concept = $type;
      $ingreso->month = $m;
      $ingreso->year  = $y;
      if ($m<10) $m = '0'.$m;
      $ingreso->date = $y.'-'.$m.'-01';
    }
    
    $ingreso->import = $val;
    if ($ingreso->save()) {
      return 'ok';
    }
    return 'error';
  }
  public function delIngr(Request $request) {
    $id =  $request->input('id');
    $oIngr = \App\Incomes::find($id);
    if ($oIngr && $oIngr->id == $id){
      $oIngr->delete();
      return 1;
    }
    return 0;
  }
  public function ingresosCreate(Request $request) {
    $date =  $request->input('fecha');
    $aDate = explode('/', $date);
    $month = isset($aDate[1]) ? intval($aDate[1]) : null;
    $year  = isset($aDate[2]) ? $aDate[2] : null;
    $import = floatval($request->input('import',null));
    
    
    if ($month && $import){
//        $ingreso = \App\Incomes::where('month',$month)
//              ->where('year',$year)
//              ->where('concept',$concept)
//              ->first();
//        
//        if ($ingreso){
//          $ingreso->import = $ingreso->import+$import;
//        } else {
          $ingreso = new \App\Incomes();
          $ingreso->month = intval($month);
          $ingreso->year  = $year;
          $ingreso->concept  = $request->input('concept');
          $ingreso->type  = $request->input('type');
          $ingreso->date = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
          $ingreso->import = $import;
//        }
      
      if ($ingreso->save()) {
        return redirect()->back();
      }
    }
    return redirect()->back()->withErrors(['No se pudo cargar el ingreso.']);
  }

  public function caja($current=null) {
    
    $year = $this->getActiveYear();
    
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    $diff = $startYear->diffInMonths($endYear) + 1;
    $lstMonths = lstMonths($startYear,$endYear);
    
    $ingrType = \App\Incomes::getTypes();
    $gType = \App\Expenses::getTypes();
    
    $months_empty = array();
    for($i=0;$i<13;$i++) $months_empty[$i] = 0;
    
    $gastos = \App\Expenses::whereYear('date','=', $year->year)->where('typePayment',2)->sum('import');
    $incomesLst = \App\Incomes::whereYear('date', '=', $year->year)->sum('import');
    $arqueosLst = \App\Arqueos::whereYear('date', '=', $year->year)->sum('import');
    $totalYear = $incomesLst-$gastos+$arqueosLst;
   
    if (!$current){
      $current = ($year->year-2000).','.date('m');
    }
    
    /***************************************************/
    // Parkings
    $extrParking = \App\ExtraPrices::getParkings();
    $parkingSold = \App\ExtraPrices::getParkingsGroups($year->year);
    $gTypeParking = \App\Expenses::getTypesParking();
    $parkingBuy = [];
    $pakingsLst = [];
    $pakingsCostCode = [];
    
    foreach ($extrParking as $extr){
      $pakingsCostCode[$extr->code] = $extr->cost;
    }
    
    $parkingExpenses = \App\Expenses::whereYear('date','=', $year->year)
            ->whereIn('type',$gTypeParking)
            ->get();
    if ($parkingExpenses){
      foreach ($parkingExpenses as $item){
        if (!isset($parkingBuy[$item->type])) $parkingBuy[$item->type] = 0;
        $parkingBuy[$item->type] += $item->import;
      }
    }
    if($gTypeParking){
      foreach($gTypeParking as $code=>$key){
        $sold = isset($parkingSold[$code]) ? $parkingSold[$code] : 0;
        $buy = isset($parkingBuy[$key]) ? $parkingBuy[$key] : 0;
        $cost = isset($pakingsCostCode[$code]) ? $pakingsCostCode[$code] : 0;
        $qtyBuy = ($cost>0) ? round($buy/$cost) : 0;
        $pakingsLst[] = [
            'name' => isset($gType[$key]) ? $gType[$key] : '',
            'buy' =>$buy,
            'cost' =>$cost,
            'qtyBuy' =>$qtyBuy,
            'qtySold' =>$sold,
            'qtySaldo' =>$qtyBuy-$sold,
            ];
      }
    }
    /***************************************************/
    
    return view('backend/sales/caja/index', [
        'year' => $year,
        'lstMonths' => $lstMonths,
        'ingrType' => $ingrType,
        'gType' => $gType,
        'pakingsLst' => $pakingsLst,
        'current' => $current,
        'totalYear' => $totalYear,
        'page' => 'caja',
        'typePayment' => \App\Expenses::getTypeCobro()
    ]);
  }
  
  
    /**
   * Get the Caja by month-years to ajax table
   * 
   * @param Request $request
   * @return Json-Objet
   */
  
  public function getTableCaja(Request $request, $isAjax = true) {

    
    $year = $request->input('year', null);
    $month = $request->input('month', null);
    if (!$year) {
      return response()->json(['status' => 'wrong']);
    }
    $total = 0;
    $sites = \App\Sites::allSites();
    if ($year<100) $year = '20'.$year;
    
    //last month
    $year_prev = intval($year);
    $monthPrev = ($month>1) ? $month-1 : 12;
    if ($monthPrev == 12) $year_prev--;
    $totalPrev = 0;
    
    /********************************************/
    //// Expenses
    $qry = \App\Expenses::whereYear('date','=', $year);
            
    if ($month>0)   $qry->whereMonth('date','=', $month);
    
    $qry->where('typePayment',2);
    $gastos = $qry->orderBy('date')->get();
    $gType = \App\Expenses::getTypes();
    $ingrType = \App\Incomes::getTypes();
    $response = [
        'status' => 'false',
        'respo_list' => [],
    ];
    
    $respo_list = array();
    $typePayment = Book::getTypeCobro();
    if ($gastos){
      foreach ($gastos as $item){
        
        $total -= $item->import;
        if (!isset($respo_list[strtotime($item->date)])) $respo_list[strtotime($item->date)] = [];
        $respo_list[strtotime($item->date)][] = [
            'id'=> $item->id,
            'concept'=> $item->concept,
            'date'=> convertDateToShow_text($item->date),
            'type'=> isset($gType[$item->type]) ? $gType[$item->type] : '--',
            'haber'=> $item->import,
            'debe'=> '--',
            'comment'=> ($item->comment) ? $item->comment : '',
            'site'=> isset($sites[$item->site_id]) ? $sites[$item->site_id] : 'General',
            'key'=> 'gasto-'.$item->id
        ];
      }
     
     
    }
    
    $totalPrevExpenses = \App\Expenses::whereYear('date','=', $year_prev)
            ->whereMonth('date','=', $monthPrev)
            ->where('typePayment',2)->sum('import');
    if ($totalPrevExpenses){
      $totalPrev -= intval($totalPrevExpenses);
    }
    /********************************************/
    //// Incomes
    $qry = \App\Incomes::whereYear('date','=', $year);
    if ($month>0)   $qry->whereMonth('date','=', $month);
    $oIngr = $qry->orderBy('date')->get();
    if ($oIngr){
     
      foreach ($oIngr as $item){
        $total += $item->import;
        
        if (!isset($respo_list[strtotime($item->date)])) $respo_list[strtotime($item->date)] = [];
        
        $concept = $item->concept;
        if ($item->book_id>0){
          $concept .= ' <a href="'.route('book.update',$item->book_id).'" target="_blank">('.$item->book_id.')</a>';
        }
        
        $respo_list[strtotime($item->date)][] = [
            'id'=> $item->id,
            'concept'=> $concept,
            'date'=> convertDateToShow_text($item->date),
            'type'=> isset($ingrType[$item->type]) ? $ingrType[$item->type] : $item->type,
            'debe'=> $item->import,
            'haber'=> '--',
            'comment'=> ($item->comment) ? $item->comment : '',
            'site'=> isset($sites[$item->site_id]) ? $sites[$item->site_id] : 'General',
            'key'=> 'ingreso-'.$item->id
        ];
        
        
      }
    }
    $totalPrevIncomes = \App\Incomes::whereYear('date','=', $year_prev)
            ->whereMonth('date','=', $monthPrev)->sum('import');
    if ($totalPrevIncomes){
      $totalPrev += intval($totalPrevIncomes);
    }
    
    /***************************************************/
    // Arqueos
    $qry = \App\Arqueos::whereYear('date','=', $year);
    if ($month>0)   $qry->whereMonth('date','=', $month);
    $oObj = $qry->orderBy('date')->get();
    if ($oObj){
      foreach ($oObj as $item){
        $total += $item->import;
        
        if (!isset($respo_list[strtotime($item->date)])) $respo_list[strtotime($item->date)] = [];
        $respo_list[strtotime($item->date)][] = [
            'id'=> $item->id,
            'concept'=> $item->observ,
            'date'=> convertDateToShow_text($item->date),
            'type'=> 'Arqueo',
            'debe'=> ($item->import>0) ? ($item->import) : '--',
            'haber'=> ($item->import<0) ? ($item->import*-1) : '--',
            'comment'=> '',
            'site'=> "",
            'key'=> 'arqueo-'.$item->id
        ];
        
        
      }
    }
    
    $totalPrevArqueos = \App\Arqueos::whereYear('date','=', $year_prev)
            ->whereMonth('date','=', $monthPrev)->sum('import');
    if ($totalPrevArqueos){
      $totalPrev += intval($totalPrevArqueos);
    }
    

    /*******************************************/
    
    $return = [];
    if (count($respo_list)>0){
      ksort($respo_list);
      foreach ($respo_list as $d => $array){
        foreach ($array as $item){
          $return[] = $item;
        }
      }
      
     $response = [
          'status' => 'true',
          'total' => moneda($total),
          'respo_list' => $return,
          'totalPrev' => $totalPrev,
          'month_prev' => getMonthsSpanish($monthPrev,false)
      ];
    }
    
     
    
    if ($isAjax) {
      return response()->json($response);
    } else {
      return $response;
    }
  }
  
   

  public function getTableMoves($year, $type) {
    $year = $this->getActiveYear();
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);

    if ($type == 'jaime') {

      $cashbox = \App\Cashbox::where('typePayment', 1)->where('date', '>=', $startYear)
                      ->where('date', '<=', $endYear)
                      ->orderBy('date', 'ASC')->get();
      $saldoInicial = \App\Cashbox::where('concept', 'SALDO INICIAL')->where('typePayment', 1)->first();
    } else {
      $cashbox = \App\Cashbox::where('typePayment', 0)->where('date', '>=', $startYear)
                      ->where('date', '<=', $endYear)
                      ->orderBy('date', 'ASC')->get();

      $saldoInicial = \App\Cashbox::where('concept', 'SALDO INICIAL')->where('typePayment', 0)->first();
    }
    return view('backend.sales.cashbox._tableMoves', [
        'cashbox' => $cashbox,
        'saldoInicial' => $saldoInicial,
    ]);
  }

  public function cashBoxCreate(Request $request) {

    $data = $request->input();
    $data['date'] = Carbon::createFromFormat('d/m/Y', $data['fecha'])->format('Y-m-d');
    $data['import'] = $data['importe'];
    $data['typePayment'] = $data['type_payment'];
    if ($this->addCashbox($data)) {
      return "OK";
    }
  }

  static function addCashbox($data) {

    $cashbox = new \App\Cashbox();
    $cashbox->concept = $data['concept'];
    $cashbox->date = Carbon::createFromFormat('Y-m-d', $data['date']);
    $cashbox->import = $data['import'];
    $cashbox->comment = $data['comment'];
    $cashbox->typePayment = $data['typePayment'];
    $cashbox->type = $data['type'];
    if ($cashbox->save()) {
      return true;
    } else {
      return false;
    }
  }

  public function bank() {
    return view('backend.sales.bank.after-bank'); 
  }

  public function getTableMovesBank($year, $type) {
    if (empty($year)) {
      $date = Carbon::now();
      if ($date->copy()->format('n') >= 6) {
        $date = new Carbon('first day of June ' . $date->copy()->format('Y'));
      } else {
        $date = new Carbon('first day of June ' . $date->copy()->subYear()->format('Y'));
      }
    } else {
      $year = Carbon::createFromFormat('Y', $year);
      $date = $year->copy();
    }

    $inicio = new Carbon('first day of June ' . $date->copy()->format('Y'));
    if ($type == 'jaime') {

      $bank = \App\Bank::where('typePayment', 3)->where('date', '>=', $inicio->copy()->format('Y-m-d'))
                      ->where('date', '<=', $inicio->copy()->addYear()->format('Y-m-d'))
                      ->orderBy('date', 'ASC')->get();
      $saldoInicial = \App\Bank::where('concept', 'SALDO INICIAL')->where('typePayment', 3)->first();
    } else {
      $bank = \App\Bank::where('typePayment', 2)->where('date', '>=', $inicio->copy()->format('Y-m-d'))
              ->where('date', '<=', $inicio->copy()->addYear()->format('Y-m-d'))->orderBy('date', 'ASC')
              ->get();

      $saldoInicial = \App\Bank::where('concept', 'SALDO INICIAL')->where('typePayment', 2)->first();
    }
    return view('backend.sales.bank._tableMoves', [
        'bank' => $bank,
        'saldoInicial' => $saldoInicial,
    ]);
  }

  static function addBank($data) {

    $bank = new \App\Bank();
    $bank->concept = $data['concept'];
    $bank->date = Carbon::createFromFormat('Y-m-d', $data['date']);
    $bank->import = $data['import'];
    $bank->comment = $data['comment'];
    $bank->typePayment = $data['typePayment'];
    $bank->type = $data['type'];
    if ($bank->save()) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Perdidas y ganancias
   * 
   * @return type
   */
  public function perdidasGanancias() {
     
    $oYear = $this->getActiveYear();
    $oLiq = new Liquidacion();
    $startYear = new Carbon($oYear->start_date);
    $endYear = new Carbon($oYear->end_date);
    $diff = $startYear->diffInMonths($endYear) + 1;
    $lstMonths = lstMonths($startYear,$endYear,'ym',true);
    
     
    $totalPendingGasto = 0;
    $emptyMonths = [];
    foreach ($lstMonths as $k=>$m){
      $emptyMonths[$k] = 0;
    }
    
    $ingresos = ['ventas'=>$emptyMonths];
    $ingresos['extr_breakfast'] = $emptyMonths;
    $ingresos['extr_excursion'] = $emptyMonths;
    $ingresos['extr_minibar']   = $emptyMonths;
    $ingresos['extr_others']    = $emptyMonths;
    $ingresos['extr_parking']    = $emptyMonths;
    $ingresos['others'] = $emptyMonths;
    
    $lstT_ing = [
        'ff' => 0,
        'ventas' => 0,
    ];
    $lstT_gast = [];
    $listGastos = [];
    
    $summary = [
      'total'=>0,
      'inquilinos'=>0,
      'noches'=>0,
      'benef'=>0,
      'vta_prop'=>0,
      'vta_agenda'=>0,
      'daysTemp'=>$oYear->getNumDays()
    ];
    
   
    $tIngByMonth = $emptyMonths;
    $tGastByMonth = $emptyMonths;
    
    $ingrType = \App\Incomes::getTypes();
    foreach ($ingrType as $k=>$t){
      //quitamos los registros de CASH de Books (se calculan aparte)
      if ($k == 'book') continue;
      
      $ingresos[$k] = $emptyMonths;
      $lstT_ing[$k] = 0;
      $aIngrPending[$k] = 0;
    }
    
    $gType = \App\Expenses::getTypes();
    if ($gType){
      foreach ($gType as $k=>$v){
        $listGastos[$k] = $emptyMonths;
        $lstT_gast[$k] = 0;
      }
    }
   
    
    $lstT_ing['others'] = 0;
    $lstT_ing['extr_breakfast'] = 0;
    $lstT_ing['extr_excursion'] = 0;
    $lstT_ing['extr_minibar']   = 0;
    $lstT_ing['extr_others']    = 0;
    $lstT_ing['extr_parking']    = 0;
    
    $ingrType['extr_breakfast'] = 'DESAYUNOS';
    $ingrType['extr_excursion'] = 'EXCURSIONES';
    $ingrType['extr_minibar']   = 'MINIBAR';
    $ingrType['extr_parking']   = 'PARKINGS';
    $ingrType['extr_others']    = 'OTROS EXTRAS';
    
       
    $aIngrPending = [
        'ventas' => 0,
        ];
    /*************************************************************************/
    $books = \App\Book::where_type_book_sales(true,true)
            ->whereYear('start','=', $oYear->year)->get();
    
    //------------------------------------------------------------------//
    // BEGIN: Control
//      $control = [];
//      $tpvp = 0;
//      foreach ($books as $b){
//        $b_start = strtotime($b->start);
//        $b_finish = strtotime($b->finish);
//        $tpvp += $b->total_price;
//        $pvp = $b->total_price / $b->nigths;
//        $nigth = 0;
//        while ($b_start < $b_finish) {
//          $m = date('Y-m',$b_start);
//          if (!isset($control[$m])) $control[$m] = 0;
//            $control[$m] += $pvp;
//          $b_start = strtotime('+1 day', $b_start);
//        }
//
//      }
//      dd($control,$tpvp1,array_sum($control));
//    
    // END: Control
    //------------------------------------------------------------------//
    $aExpensesPending = $oLiq->getExpensesEstimation($books);
    $limp = $oLiq->getLimpiezaEstimation($oYear->year);
    $aExpensesPending['limpieza'] = $limp['limpieza'];
    $aExpensesPending['lavanderia'] = $limp['lavanderia'];
    $aux = $emptyMonths;
    $lstRvs = BookDay::where_type_book_sales(true,true)
            ->whereYear('date','=', $oYear->year)->get();
     
    $extrTypes = \App\ExtraPrices::whereNotNull('type')->pluck('type','id')->toArray();
    foreach ($lstRvs as $key => $b) {
      $m = date('ym', strtotime($b->date));
      $value = $b->pvp;
      /**************************************************************/
      $oAdditional = json_decode($b->extrs,true);
      if ($oAdditional && count($oAdditional) > 0){
        
        foreach ($oAdditional as $extID=>$v){
          $t = isset($extrTypes[$extID]) ? $extrTypes[$extID] : 'others';
          $type_ext = isset($ingresos['extr_'.$t]) ? 'extr_'.$t : 'extr_others';

          $ingresos[$type_ext][$m] += $v;
          $lstT_ing[$type_ext] += $v;
          $value-=$v;
        }
      }
      /**************************************************************/  
//      $tCosts += $b->costs;
      $aux[$m] += $value;
      $tIngByMonth[$m] += $b->pvp;
      $lstT_ing['ventas'] += $value;
      
    }
    //--------------------------------------------------------------------------------------------//
    $ingresos['ventas'] = $aux;
    /*************************************************************************/
//    dd($tCosts);
 
    $incomesLst = \App\Incomes::where('date', '>=', $startYear)
            ->Where('date', '<=', $endYear)
            ->where('type','!=','book')->get();
    
    if ($incomesLst){
      foreach ($incomesLst as $item){
        $m = date('ym', strtotime($item->date));
        $type = isset($ingrType[$item->type]) ? $item->type : 'others';
        $ingresos[$type][$m] += $item->import;
        if (isset($tIngByMonth[$m])) $tIngByMonth[$m] += $item->import;
        $lstT_ing[$type] += $item->import;
      }
    }
    
    $ingrType['ff'] = 'FORFAITS';
    $ingrType['ventas'] = 'VENTAS';
    $ingrType['others'] = 'OTROS INGRESOS';
    
    /*************************************************************************/
    /******       GASTOS                        ***********/
    $gastos = \App\Expenses::where('date', '>=', $startYear)
                    ->Where('date', '<=', $endYear)
                    ->WhereNull('PayFor')
                    ->Where('type','!=','prop_pay')
                    ->orderBy('date', 'DESC')->get();

    $lstT_gast = [];
    $listGastos = [];
    
    $gType = \App\Expenses::getTypes();
    if ($gType){
      foreach ($gType as $k=>$v){
        if ($k == 'parking_mercado_san_agustin'){
          $listGastos['parkings'] = $emptyMonths;
          $lstT_gast['parkings']  = 0;
        }
        $listGastos[$k] = $emptyMonths;
        $lstT_gast[$k] = 0;
      }
    }
    if ($gastos){
      foreach ($gastos as $g){
        $m = date('ym', strtotime($g->date));
        if (isset($listGastos[$g->type])){
          $listGastos[$g->type][$m] += $g->import;
          $lstT_gast[$g->type] += $g->import;
          if (isset($tGastByMonth[$m]) && $g->type != 'impuestos') $tGastByMonth[$m] += $g->import;
        }
      }
    }
    
    /// PARKINGs hardcode
    $gTypeGroup_aux = \App\Expenses::getTypesGroup();
    $gTypeGroup_aux = $gTypeGroup_aux['groups'];
    $parkings = [];
    $gType['parkings']  = 'PARKINGs';
    foreach ($gTypeGroup_aux as $k=>$v){
      if ($v === 'parkings') $parkings[] = $k;
    }
    
    foreach ($parkings as $key){
      if (isset($listGastos[$key])){
        foreach ($listGastos[$key] as $m=>$v){
          $listGastos['parkings'][$m] += $v;
        }
        $lstT_gast['parkings'] += $lstT_gast[$key];
        unset($lstT_gast[$key]);
        unset($listGastos[$key]);
      }
    }
    /// PARKINGs hardcode
    
    foreach ($gType as $k=>$v){
      if (isset($aExpensesPending[$k])){
        $aExpensesPending[$k] -= $lstT_gast[$k];
        if ($aExpensesPending[$k]<0) $aExpensesPending[$k] = 0;
      } else {
        $aExpensesPending[$k] = 0;
      }
    }
    $aExpensesPendingOrig = $aExpensesPending;
    
    $oData = \App\ProcessedData::findOrCreate('PyG_Hide');
    if ($oData){
      $PyG_Hide = json_decode($oData->content,true);
      if ($PyG_Hide && is_array($PyG_Hide)){
        foreach ($PyG_Hide as $k){
          if(isset($aExpensesPending[$k])){
            $aExpensesPending[$k] = 'N/A';
          }
        }
      }
    }
      
    foreach ($aExpensesPending as $k=>$v){
      if ($v != 'N/A') $totalPendingGasto += intval($v);
    }
   /*****************************************************************/
    
    $impuestos = $listGastos['impuestos'];
    unset($listGastos['impuestos']);
    
    $impEstimado = [];
    
    $gTypesImp = \App\Expenses::getTypesImp();
    if ($gTypesImp){
      foreach ($lstMonths as $k_m=>$m){
        $impuestoM = 0;
        foreach ($gTypesImp as $k_t=>$v){
          $impuestoM += $listGastos[$k_t][$k_m];
        }
        
        $impEstimado[$k_m] = ($tIngByMonth[$k_m]* 0.21 ) - ($impuestoM*0.21);
        
      }
      
    }
      
    $totalPendingImp = array_sum($impEstimado)-$lstT_gast['impuestos'];
//    ( T ingr * 0.21 ) - ( TGasto *0.21 )
    /*****************************************************************/

    $totalIngr = array_sum($lstT_ing);
    $totalGasto = array_sum($lstT_gast);
    $summary = $oLiq->summaryTemp($oYear);
    return view('backend/sales/perdidas_ganancias', [
        'summary' => $summary,
        'lstT_ing' => $lstT_ing,
        'totalIngr' => $totalIngr,
        'lstT_gast' => $lstT_gast,
        'totalGasto' => $totalGasto,
        'totalPendingGasto' => $totalPendingGasto,
        'totalPendingIngr' => array_sum($aIngrPending),
        'totalPendingImp' => $totalPendingImp,
        'ingresos' => $ingresos,
        'listGasto' => $listGastos,
        'impuestos' => $impuestos,
        'impEstimado' => $impEstimado,
        'aExpensesPending' => $aExpensesPending,
        'aExpensesPendingOrig' => $aExpensesPendingOrig,
        'aIngrPending' => $aIngrPending,
        'diff' => $diff,
        'lstMonths' => $lstMonths,
        'year' => $oYear,
        'tGastByMonth' => $tGastByMonth,
        'tIngByMonth' => $tIngByMonth,
        'ingrType' => $ingrType,
        'gastoType' => $gType,
        'ingr_bruto' => $totalIngr-$totalGasto-$lstT_gast['impuestos'],
        'summary' => $summary,
    ]);
  }

  public function perdidasGananciasShowHide(Request $request) {
    
    $key   = $request->input('key');
    $value = $request->input('input');
    
    
    $oData = \App\ProcessedData::findOrCreate('PyG_Hide');
    if ($oData) $PyG_Hide = json_decode($oData->content,true);
      
    if (!$PyG_Hide) $PyG_Hide = [];
    
    if ($value == 'hide'){
      if (!in_array($key, $PyG_Hide)){
        $PyG_Hide[] = $key;
        $oData->content = json_encode($PyG_Hide);
        $oData->save();
        return 'OK';
      }
    }
    
    if ($value == 'show'){
      if (in_array($key, $PyG_Hide)){
        if (($key_array = array_search($key, $PyG_Hide)) !== false) {
            unset($PyG_Hide[$key_array]);
          $oData->content = json_encode($PyG_Hide);
          $oData->save();
          return 'OK';
        }
      }
    }
    
    return 'error';
    
  }
    
  public function perdidasGananciasUpdIngr(Request $request) {
    
    $key     = $request->input('key');
    $value   = floatVal($request->input('input'));
    $month   = $request->input('month');
    $date_y  = '20'.$month[0].$month[1];
    $date_m  = intval($month[2].$month[3]);
    
    if ($value<0) return 'error';
    
    $obj = \App\Incomes::where('year',$date_y)
            ->where('month',$date_m)
            ->where('concept',$key)->first();
    if ($obj){
      $obj->import = ($value);
      if ($obj->save())  return 'OK';
    } else {
      $obj = new \App\Incomes();
      $obj->import  = ($value);
      $obj->concept = $key;
      $obj->year    = $date_y;
      $obj->month   = $date_m;
      $obj->date    = $date_y.'-'.$date_m.'-01';
      if ($obj->save())  return 'OK';
    }
   
    return 'error';
    
  }
  
  public function perdidasGananciasShowDetail($key) {
    
    // $year = \App\Years::getActive();
    $year = $this->getActiveYear();
    $typePayment = \App\Expenses::getTypeCobro();
    $qry = \App\Expenses::where('date', '>=', $year->start_date)
            ->Where('date', '<=', $year->end_date)
            ->orderBy('date', 'DESC');
    
    if ($key == "parkings") $qry->WhereIn('type',['parking_mercado_san_agustin','parking_puerta_real']);
      else $qry->Where('type',$key);
      
            
    if ($key != 'prop_pay')
      $qry->WhereNull('PayFor');
    
    $expense = $qry->orderBy('date', 'DESC')->get();
    $total = $qry->sum('import');
    return view('backend.sales.gastos._details',['items'=>$expense,'total'=>$total,'typePayment'=>$typePayment]);
  }
  
  
  static function getSalesByYear($year = "") {
    // $array = [0 =>"Metalico Jorge", 1 =>"Metalico Jaime",2 =>"Banco Jorge",3=>"Banco Jaime"];

    
    if ($year == "") {
      $year = self::getActiveYear();
      $startYear = new Carbon($year->start_date);
      $endYear = new Carbon($year->end_date);
    } else {
      $start = new Carbon('first day of September ' . $year);
      $end = $start->copy()->addYear();
      $startYear = $start->copy()->format('Y-m-d');
      $endYear = $end->copy()->format('Y-m-d');
    }

    $books = \App\Book::where_type_book_sales()->with('payments')->where('start', '>=', $startYear)
                    ->where('start', '<=', $endYear)
                    ->orderBy('start', 'ASC')->get();

    $result = [
        'ventas' => 0,
        'cobrado' => 0,
        'pendiente' => 0,
        'metalico' => 0,
        'banco' => 0
    ];
    foreach ($books as $key => $book) {
      $result['ventas'] += $book->total_price;

      foreach ($book->payments as $key => $pay) {
        $result['cobrado'] += $pay->import;

        if ($pay->type == 0 || $pay->type == 1) {
          $result['metalico'] += $pay->import;
        } else if ($pay->type == 2 || $pay->type == 3) {
          $result['banco'] += $pay->import;
        }
      }
    }

    $result['pendiente'] = ($result['ventas'] - $result['cobrado']);

    return $result;
  }

  static function getSalesByYearByRoomGeneral($year = "", $room = "all") {
    
    if (empty($year)) {
      $year = self::getActiveYear();
    } else {
      $year = self::getYearData($year);
    }
    
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);

    $total = 0;
    $metalico = 0;
    $banco = 0;
    $pagado = 0;
    if ($room == "all") {
      $rooms = \App\Rooms::where('state', 1)->get(['id']);
      $books = \App\Book::where_type_book_sales()
              ->whereIn('room_id', $rooms)
              ->where('start', '>=',$startYear)
              ->where('start', '<=', $endYear)
              ->orderBy('start', 'ASC')->get();


      foreach ($books as $key => $book) {
        $total += ($book->cost_apto + $book->cost_park + $book->cost_lujo); //$book->total_price;
      }

      $gastos = \App\Expenses::where('date', '>=',$startYear)
              ->where('date', '<=', $endYear)
              ->orderBy('date', 'DESC')->get();
      
      foreach ($gastos as $payment) {
        if ($payment->typePayment == 2 || $payment->typePayment == 1) {
          $metalico += $payment->import;
        } else {
          $banco += ($payment->import );
        }
        $pagado += ($payment->import);
      }
    } else {

      $books = \App\Book::where_type_book_sales()
              ->where('room_id', $room)
              ->where('start', '>=',$startYear)
              ->where('start', '<=', $endYear)
              ->orderBy('start', 'ASC')->get();
      
      foreach ($books as $key => $book) {
        $total += ($book->cost_apto + $book->cost_park + $book->cost_lujo); //$book->total_price;
      }

      $gastos = \App\Expenses::where('date', '>=',$startYear)
              ->where('date', '<=', $endYear)
              ->Where('PayFor', 'LIKE', '%' . $room . '%')
              ->orderBy('date', 'DESC')->get();

      foreach ($gastos as $payment) {
        if ($payment->typePayment == 2 || $payment->typePayment == 1) {
          $divisor = 0;
          if (preg_match('/,/', $payment->PayFor)) {
            $aux = explode(',', $payment->PayFor);
            for ($i = 0; $i < count($aux); $i++) {
              if (!empty($aux[$i])) {
                $divisor++;
              }
            }
          } else {
            $divisor = 1;
          }
          $metalico += ($payment->import / $divisor);
        } else {
          $divisor = 0;
          if (preg_match('/,/', $payment->PayFor)) {
            $aux = explode(',', $payment->PayFor);
            for ($i = 0; $i < count($aux); $i++) {
              if (!empty($aux[$i])) {
                $divisor++;
              }
            }
          } else {
            $divisor = 1;
          }
          $banco += ($payment->import / $divisor);
        }

        $divisor = 0;
        if (preg_match('/,/', $payment->PayFor)) {
          $aux = explode(',', $payment->PayFor);
          for ($i = 0; $i < count($aux); $i++) {
            if (!empty($aux[$i])) {
              $divisor++;
            }
          }
        } else {
          $divisor = 1;
        }

        $pagado += ($payment->import / $divisor);
      }
    }


    return [
        'total' => $total,
        'banco' => $banco,
        'metalico' => $metalico,
        'pagado' => $pagado,
        'metalico_jaime' => 0,
        'metalico_jorge' => 0,
        'banco_jorge' => 0,
        'banco_jaime' => 0,
    ];
  }

  public function getHojaGastosByRoom($year = "", $id) {
   
    if (empty($year)) {
      $year = self::getActiveYear();
    } else {
      $year = self::getYearData($year);
    }
    
    $start = new Carbon($year->start_date);
    $end = new Carbon($year->end_date);
    
    
    if ($id != "all") {
      $room = \App\Rooms::find($id);
      $gastos = \App\Expenses::where('date', '>=', $start)
                      ->Where('date', '<=', $end)
                      ->Where('PayFor', 'LIKE', '%' . $id . '%')->orderBy('date', 'DESC')->get();
    } else {
      $room = "all";
      $gastos = \App\Expenses::where('date', '>=', $start)
                      ->Where('date', '<=', $end)->orderBy('date', 'DESC')->get();
    }

    return view('backend.sales.gastos._expensesByRoom', [
        'gastos' => $gastos,
        'room' => $room,
        'year' => $start
    ]);
  }

  public function getTableExpensesByRoom($year = "", $id) {

    if (empty($year)) {
      $date = Carbon::now();
    } else {
      $year = Carbon::createFromFormat('Y', $year);
      $date = $year->copy();
    }
    $start = new Carbon('first day of September ' . $date->copy()->format('Y'));

    // return $start;
    $end = $start->copy()->addYear();
    if ($id != "all") {
      $room = \App\Rooms::find($id);
      $gastos = \App\Expenses::where('date', '>=', $start->copy()->format('Y-m-d'))
                      ->Where('date', '<=', $end->copy()->format('Y-m-d'))
                      ->Where('PayFor', 'LIKE', '%' . $id . '%')->orderBy('date', 'DESC')->get();
    } else {
      $room = "all";
      $gastos = \App\Expenses::where('date', '>=', $start->copy()->format('Y-m-d'))
                      ->Where('date', '<=', $end->copy()->format('Y-m-d'))->orderBy('date', 'DESC')->get();
    }

    return view('backend.sales.gastos._tableExpensesByRoom', [
        'gastos' => $gastos,
        'room' => $room,
        'year' => $start
    ]);
  }

  static function setExpenseLimpieza($status, $room_id, $fecha) {
    $room = \App\Rooms::find($room_id);
    $expenseLimp = 0;

    if ($room->sizeApto == 1) {
      $expenseLimp = 30;
    } else if ($room->sizeApto == 2 || $room->sizeApto == 9) {
      $expenseLimp = 50; //40;
    } else if ($room->sizeApto == 3 || $room->sizeApto == 4) {
      $expenseLimp = 100; //70;
    }

    $gasto = new \App\Expenses();
    $gasto->concept = "LIMPIEZA RESERVA PROPIETARIO. " . $room->nameRoom;
    $gasto->date = Carbon::createFromFormat('d/m/Y', $fecha)->format('Y-m-d');
    $gasto->import = $expenseLimp;
    $gasto->typePayment = 3;
    $gasto->type = 'LIMPIEZA';
    $gasto->comment = " LIMPIEZA RESERVA PROPIETARIO. " . $room->nameRoom;
    $gasto->PayFor = $room->id;
    if ($gasto->save()) {
      return true;
    } else {
      return false;
    }
  }

  public function searchByName(Request $request){
    return $this->searchByRoom( $request);
  }

  public function searchByRoom(Request $request) {
    $arrayCustomersId = null;
    $roomID = null;
    $type = null;
    $agency = null;
    if ($request->searchString != "") {
      $customers = \App\Customers::where('name', 'LIKE', '%' . $request->searchString . '%')->get();

      if (count($customers) > 0) {
        $arrayCustomersId = [];
        foreach ($customers as $key => $customer) {
          if (!in_array($customer->id, $arrayCustomersId)) {
            $arrayCustomersId[] = $customer->id;
          }
        }
      }
    }
    
    if ($request->searchRoom && $request->searchRoom != "all") {
      $roomID = $request->searchRoom;
    }

    if ($request->searchAgency && $request->searchAgency >0) {
      $agency = intval($request->searchAgency);
    }
    if ($request->searchType && $request->searchType >0) {
      $type = intval($request->searchType);
    }
       
      
    $data = $this->getTableData($arrayCustomersId,$agency,$roomID,$type);
     
    $oLiq = new Liquidacion();
//    $data['summary'] = $oLiq->summaryTemp();
    $data['stripeCost'] = $oLiq->getTPV($data['books']);
    $data['total_stripeCost'] = array_sum($data['stripeCost']);
             
    if (Auth::user()->role == "subadmin"){
      return view('backend/sales/_tableSummary-subadmin.blade', $data);
    }

    return view('backend/sales/_tableSummary', $data);
      
  }

  public function changePercentBenef(Request $request, $val) {
    Settings::where('key', 'percentBenef')->update(['value' => $val]);
    return "Cambiado";
  }

  public function exportExcel(Request $request) {
    $year = $this->getActiveYear();
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);

    if ($request->searchString != "") {
      $customers = \App\Customers::where('name', 'LIKE', '%' . $request->searchString . '%')->get();

      if (count($customers) > 0) {
        $arrayCustomersId = [];
        foreach ($customers as $key => $customer) {
          if (!in_array($customer->id, $arrayCustomersId)) {
            $arrayCustomersId[] = $customer->id;
          }
        }


        if ($request->searchRoom && $request->searchRoom != "all") {

          $books = \App\Book::whereIn('customer_id', $arrayCustomersId)
                          ->where('start', '>=', $startYear)
                          ->where('start', '<=', $endYear)
                          ->whereIn('type_book', [
                              2,
                              7,
                              8
                          ])->where('room_id', $request->searchRoom)->orderBy('start', 'ASC')->get();
        } else {

          $books = \App\Book::whereIn('customer_id', $arrayCustomersId)
                          ->where('start', '>=', $startYear)
                          ->where('start', '<=', $endYear)
                          ->whereIn('type_book', [
                              2,
                              7,
                              8
                          ])->orderBy('start', 'ASC')->get();
        }
      }
    } else {

      if ($request->searchRoom != "all") {

        $books = \App\Book::where('start', '>=', $startYear)
                        ->where('start', '<=', $endYear)
                        ->whereIn('type_book', [
                            2,
                            7,
                            8
                        ])->where('room_id', $request->searchRoom)->orderBy('start', 'ASC')->get();
      } else {

        $books = \App\Book::where('start', '>=', $startYear)
                        ->where('start', '<=', $endYear)
                        ->whereIn('type_book', [
                            2,
                            7,
                            8
                        ])->orderBy('start', 'ASC')->get();
      }
    }
    Excel::create('Liquidacion ' . $year->year, function ($excel) use ($books) {

      $excel->sheet('Liquidacion', function ($sheet) use ($books) {

        $sheet->loadView('backend.sales._tableExcelExport', ['books' => $books]);
      });
    })->download('xlsx');
  }

  ///////////////////////////////////////////////////////////////////////

  ///////////////////////////////////////////////////////////////////////
  
  
    
    
    public function getBookingAgencyDetails()
    {
      $oLiquidacion = new Liquidacion();
        $agencyBooks    = [
            'years'  => [],
            'data'   => [],
            'items'  => $oLiquidacion->getArrayAgency(),
            'totals' => []
        ];
                  
        $yearLst = [];
        $aux = [];
       
        
        $season    = self::getActiveYear();
        $yearFull  = $season->year;
        $yearLst[] = $yearFull;
        $year = $yearFull-2000;
        $dataSeason = $oLiquidacion->getBookingAgencyDetailsBy_date($season->start_date,$season->end_date);
        $agencyBooks['years'][$yearFull]    = $year . '-' . ($year + 1);
        $aux[$yearFull]     = $dataSeason['data'];
        $agencyBooks['totals'][$yearFull]   = $dataSeason['totals'];
        
      
        $season    = self::getYearData($yearFull-1);
        if ($season){
          $yearFull  = $season->year;
          $yearLst[] = $yearFull;
          $year = $yearFull-2000;
          $dataSeason = $oLiquidacion->getBookingAgencyDetailsBy_date($season->start_date,$season->end_date);
          $agencyBooks['years'][$yearFull]    = $year . '-' . ($year + 1);
          $aux[$yearFull]     = $dataSeason['data'];
          $agencyBooks['totals'][$yearFull]   = $dataSeason['totals'];
        }
        
        $season  = self::getYearData($yearFull-1);
        if ($season){
          $yearFull  = $season->year;
          $yearLst[] = $yearFull;
          $year = $yearFull-2000;
          $dataSeason = $oLiquidacion->getBookingAgencyDetailsBy_date($season->start_date,$season->end_date);
          $agencyBooks['years'][$yearFull]    = $year . '-' . ($year + 1);
          $aux[$yearFull]     = $dataSeason['data'];
          $agencyBooks['totals'][$yearFull]   = $dataSeason['totals'];
        }
        sort($yearLst);
        
        
        foreach ($agencyBooks['items'] as $k=>$n){
          $agencyBooks['data'][$k] = [];
          foreach ($yearLst as $y){
            if (isset($aux[$y])){
              $agencyBooks['data'][$k][$y] = $aux[$y][$k];
            }
          }
        }
//        dd($yearLst,$agencyBooks);
        echo json_encode(array(
                             'status'      => 'true',
                             'agencyBooks' => $agencyBooks,
                             'yearLst' => $yearLst,
                         ));
    }
    
    function getKPIs($ch_hotelRosa){
//      return ;
    $book = new Book();
   
    
    $startPKI = strtotime('-1 month');
    $next = strtotime('+1 month');
    $endPKI = strtotime('+2 month');
    $startPKI_date = date('Y-m',$startPKI).'-01';
    
    $months = [
        date('n',$startPKI)=>0,
        date('n')=>0,
        date('n',$next)=>0,
        date('n',$endPKI)=>0,
    ];
    
    $kpi = [
      'ocupacion'=> $months,
      'ingresos'=> $months,
//      'total_ben'=> $months,
      'ADR'=> $months,
      'RevPAR'=> $months,
      'GopPAR'=> $months,
    ];
   
    //Ocupación = Habitaciones vendidas / Habitación disponible
    $availMonth = $months;
    $totalMonth = $months;
    
    $aux = cal_days_in_month(CAL_GREGORIAN, date('m',$endPKI), date('Y',$endPKI));
    $endPKI_date = date('Y-m',$endPKI).'-'.$aux;
    foreach ($ch_hotelRosa as $k=>$ch){
      if (trim($ch) != ''){
        $availibility = $book->getAvailibilityBy_channel($ch, $startPKI_date, $endPKI_date,true);
        $disp = $availibility[1];
        foreach ($availibility[0] as $day=>$avail){
          $m_aux = date('n', strtotime($day));
          if ($avail>0){
            if (!isset($availMonth[$m_aux])) $availMonth[$m_aux] = 0;
            $availMonth[$m_aux] += $avail;
          }
          if (!isset($totalMonth[$m_aux])) $totalMonth[$m_aux] = 0;
          $totalMonth[$m_aux] += $disp;
        }
      } else {
        unset($ch_hotelRosa[$k]);
      }
    }
    
    foreach ($kpi['ocupacion'] as $m=>$v){
      if ($totalMonth[$m]>0)
        $kpi['ocupacion'][$m] = ( $totalMonth[$m] - $availMonth[$m] ) / $totalMonth[$m] * 100;
      else $kpi['ocupacion'][$m] = 0;
    }
    
    // ADR = Habitación Ingresos / Habitaciones vendidas
    
    $oRooms = \App\Rooms::whereIn('channel_group',$ch_hotelRosa)->pluck('id')->toArray();
    $totalRooms = count($oRooms);
    
    $kpi['ingresos'][date('n',$startPKI)] = $book::where_type_book_reserved()->whereIn('room_id',$oRooms)
            ->where('start','>=',$startPKI_date)
            ->where('start','<',date('Y-m').'-01')->sum('total_price');
    $kpi['ingresos'][date('n')] = $book::where_type_book_reserved()->whereIn('room_id',$oRooms)
            ->where('start','>=',date('Y-m').'-01')
            ->where('start','<',date('Y-m',$next).'-01')->sum('total_price');
    $kpi['ingresos'][date('n',$next)] = $book::where_type_book_reserved()->whereIn('room_id',$oRooms)
            ->where('start','>=',date('Y-m',$next).'-01')
            ->where('start','<',date('Y-m',$endPKI).'-01')->sum('total_price');
    $kpi['ingresos'][date('n',$endPKI)] = $book::where_type_book_reserved()->whereIn('room_id',$oRooms)
            ->where('start','>=',date('Y-m',$endPKI).'-01')
            ->where('start','<',$endPKI_date)->sum('total_price');
    
    $kpi['GopPAR'][date('n',$startPKI)] = $book::where_type_book_reserved()->whereIn('room_id',$oRooms)
            ->where('start','>=',$startPKI_date)
            ->where('start','<',date('Y-m').'-01')->sum('total_ben');
    $kpi['GopPAR'][date('n')] = $book::where_type_book_reserved()->whereIn('room_id',$oRooms)
            ->where('start','>=',date('Y-m').'-01')
            ->where('start','<',date('Y-m',$next).'-01')->sum('total_ben');
    $kpi['GopPAR'][date('n',$next)] = $book::where_type_book_reserved()->whereIn('room_id',$oRooms)
            ->where('start','>=',date('Y-m',$next).'-01')
            ->where('start','<',date('Y-m',$endPKI).'-01')->sum('total_ben');
    $kpi['GopPAR'][date('n',$endPKI)] = $book::where_type_book_reserved()->whereIn('room_id',$oRooms)
            ->where('start','>=',date('Y-m',$endPKI).'-01')
            ->where('start','<',$endPKI_date)->sum('total_ben');
    
    foreach ($kpi['ADR'] as $m=>$v){
      if ( ($totalMonth[$m] - $availMonth[$m]) >0 ){
        $kpi['ADR'][$m] = moneda($kpi['ingresos'][$m] / ( $totalMonth[$m] - $availMonth[$m] ));
      } else {
        $kpi['ADR'][$m] = 0;
      }
    }
    
    if ($totalRooms>0){
      //RevPAR = Ingresos por Habitaciones / Habitaciones disponibles
      foreach ($kpi['RevPAR'] as $m=>$v){
        $kpi['RevPAR'][$m] = moneda($kpi['ingresos'][$m] / $totalRooms);
      }
      //GopPAR = GOP (Beneficio Operativo Bruto) / Habitación Disponible
      foreach ($kpi['GopPAR'] as $m=>$v){
        $kpi['GopPAR'][$m] = moneda($kpi['GopPAR'][$m] / $totalRooms);
      }
    }
    
    return $kpi;
    }
    
  
  function getTPV($books) {
     $bIds = [];
    if($books){
      foreach ($books as $book){
        if ($book->stripeCost < 1){
          $bIds[] = $book->id;
        }
      }
    }
          
    $payments = \App\BookOrders::where('paid',1)->whereIn('book_id',$bIds)
            ->groupBy('book_id')->selectRaw('sum(amount) as sum, book_id')->pluck('sum','book_id');
    $stripeCost = [];
    if($books){
      foreach ($books as $book){
        $stripeCost[$book->id] = 0;
        if ($book->stripeCost < 1){
          if (isset($payments[$book->id])){
            $stripeCost[$book->id] = paylandCost($payments[$book->id]/100);
          }
        } else {
          $stripeCost[$book->id] = $book->stripeCost;
        }
      }
    }
    
    return $stripeCost;
  }
  
  function arqueoCreate(Request $request){
    $date =  $request->input('fecha');
    $aDate = explode('/', $date);
    $month = isset($aDate[1]) ? intval($aDate[1]) : null;
    $year  = isset($aDate[2]) ? $aDate[2] : null;
    $import = floatval($request->input('import',null));
    
    
    if ($month && $import){
          $arqueo = new \App\Arqueos();
          $arqueo->month = intval($month);
          $arqueo->year  = $year;
          $arqueo->observ  = $request->input('observ');
          $arqueo->date = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
          $arqueo->import = $import;
      if ($arqueo->save()) {
        return redirect()->back();
      }
    }
    return redirect()->back()->withErrors(['No se pudo cargar el Arqueo.']);
  }
  function delCajaItem(Request $request){
    $key = $request->input('key');
    if (!$key) return response()->json(['status'=>'Error','msg'=>'item no encontrado']);
    $aux = explode('-', $key);
    
    if (!(is_array($aux) && count($aux)==2))
      return response()->json(['status'=>'Error','msg'=>'item no encontrado']);
    
    $type = $aux[0];
    $id = $aux[1];
    $oObj = null;
    if ($type == 'ingreso') $oObj = \App\Incomes::find($id);
    if ($type == 'arqueo') $oObj = \App\Arqueos::find($id);
    if ($type == 'gasto') $oObj = \App\Expenses::find($id);

    if (!$oObj) 
      return response()->json(['status'=>'Error','msg'=>'item no encontrado']);
    
    
    if ($type == 'ingreso'){
      if ($oObj->book_id) 
        return response()->json(['status'=>'Error','msg'=>'No se puede eliminar un cobro de una reserva']);
    }
    
    if ($oObj->delete()){
      return response()->json(['status'=>'OK','msg'=>'Registro eliminado.']);
    }
    
    return response()->json(['status'=>'Error','msg'=>'Ocurrió un error']);
    
  }
  
  function gastos_import(Request $request){
    $data = $request->all();
   
    $campos = [
      'date' =>-1,  
      'concept' =>-1,  
      'type' =>-1, 
      'import' =>-1,  
      'typePayment' =>-1, 
      'site' =>-1,  
      'comment' =>-1,
      'filter' =>-1,
    ];
    
    foreach ($data as $k=>$v){
      if ($v != '' && !is_array($v)){
        preg_match('/column_([0-9]*)/', $k, $colID);
        if (isset($colID[1]) && isset($campos[$v])){
          $campos[$v] = $colID[1];
        }
      } 
    }
          
    $info = [];
    foreach ($campos as $k=>$v){
      if (isset($data['cell_'.$v])){
        $info[$k] = $data['cell_'.$v];
      }
    }
    if (count($info) == 0) return back();
    
    /********   FILTRAR REGISTROS   *********************/
    if (isset($info['filter'])){
      foreach ($info['filter'] as $k=>$v){
        if ($v == 1){
          foreach ($campos as $k2=>$v2){
            $info[$k2][$k]=null;
          }
          
        }
      }
    }
    /***************************************************/
    $sitesIDs = \App\Sites::allSitesKey();
    $expensesType = \App\Expenses::getTypes();
    /***************************************************/
    
    $campos = [
      'date' =>'Fecha',  
      'concept' =>'Concepto',  
      'type' =>'Tipo de Gasto', 
      'import' =>'Precio',  
      'typePayment' =>'Metodo de Pago', 
      'site' =>'Sitio',  
      'comment' =>'Comentario',
    ];
    
    $today = date('Y-m-d');
    $insert = [];
    $newEmpty = [
          'concept'=>null,'date'=>null,'import'=>null,'typePayment'=>null,
          'type'=>null,'comment'=>null,'site_id'=>null,
        ];
    
    
    $total = count(current($info));
    for($i = 0; $i<$total; $i++){
      $new = $newEmpty;
      foreach ($campos as $k=>$v){
        
        $value = '';
        if (!isset($info[$k])){ continue;}
        if (!isset($info[$k][$i])){  continue;}
        if (!($info[$k][$i])){  continue;}
        $variab = $info[$k][$i];
        
        switch ($k){
          case 'date':
            $new['date'] =  ($variab != '') ? convertDateToDB($variab) : $today;
            break;
          case 'import':
            $variab = floatval(str_replace(',','.',str_replace('.','', $variab)));
            $new['import'] = $variab;
            break;
          case 'typePayment':
            $aux = strtolower($variab);
            $idType = 0;
            if ($aux == 'banco'){ $idType = 3; }
            if ($aux == 'cash') { $idType = 2; }
            $new['typePayment'] = $idType;
            break;
          case 'site':
            $siteID = array_search($variab,$sitesIDs);
            $new['site_id'] = intval($siteID);
            break;
          case 'type':
            $type = array_search($variab,$expensesType);
            $new['type'] = $type;
            break;
          default:
            $new[$k] = $variab;
            break;
        }
      }
      $hasVal = false;
      foreach ($new as $value){
        if ($value) $hasVal = true;
      }
      if ($hasVal)  $insert[] = $new;
    }
//    dd($insert);
    $countInsert = count($insert);
    if ($countInsert>0)
      \App\Expenses::insert($insert);
    
    return back()->with(['success'=>$countInsert . ' Registros inportados']);
   
   
  }
  
}

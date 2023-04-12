<?php

namespace App\Http\Controllers;

use App\Classes\Mobile;
use Carbon\Carbon;
use App\Book;
use Illuminate\Http\Request;
use App\Http\Requests;

class ExtrasController extends AppController {

  public function index(Request $request, $year = "") {

    $year = $this->getActiveYear();
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    $diff = $startYear->diffInMonths($endYear) + 1;
    $lstMonths = lstMonths($startYear, $endYear);

    $months_empty = array();
    $months_label = array();
    for ($i = 0; $i < 13; $i++)
      $months_empty[$i] = 0;
    foreach ($lstMonths as $m => $v) {
      $aux = getMonthsSpanish($v['m']);
      $lstMonths[$m]['name'] = $aux;
      $months_label[] = "'$aux'";
    }

    /// BEGIN: Extras
    $months_extras = $months_empty;

    $extTyp = \App\ExtraPrices::getTypes();
    $oExtras = \App\ExtraPrices::getDynamic();

    $extrasList = array();
    $extraTit = array();
    $extraType = array();
    foreach ($oExtras as $item) {
      $extrasList[$item->id] = $months_empty;
      $extraTit[$item->id] = $item->name;
      $extraType[$item->id] = $item->type;
    }
    $extrasGroup = array();
    foreach ($extTyp as $k => $v) {
      $extrasGroup[$k] = $months_empty;
    }


    $extras = Book::where_type_book_reserved()
            ->select('book_extra_prices.price', 'book_extra_prices.type', 'start', 'extra_id')
            ->Join('book_extra_prices', 'book_extra_prices.book_id', '=', 'book.id')
            ->whereYear('start', '=', $year->year)
            ->where('book_extra_prices.deleted', 0)
            ->get();
    if ($extras) {
      foreach ($extras as $e) {
        $m = date('n', strtotime($e->start));
        $months_extras[$m] += $e->price;
        $months_extras[0] += $e->price;
        
        $type = isset($extraType[$e->extra_id]) ? $extraType[$e->extra_id] : 'others';

        $extrasGroup[$type][$m] += $e->price;
        $extrasGroup[$type][0] += $e->price;
        if (isset($extrasList[$e->extra_id])) {
          $extrasList[$e->extra_id][$m] += $e->price;
          $extrasList[$e->extra_id][0] += $e->price;
        }
      }
    }


    /* BEGIN: Past Years */
    $months_extras_1 = $months_extras;
    $months_extras_2 = $months_empty;
    $months_extras_3 = $months_empty;
    unset($months_extras_1[0]);
    unset($months_extras_2[0]);
    unset($months_extras_3[0]);
    $extras = Book::where_type_book_reserved()
            ->select('book_extra_prices.price', 'book_extra_prices.type', 'start', 'extra_id')
            ->Join('book_extra_prices', 'book_extra_prices.book_id', '=', 'book.id')
            ->whereYear('start', '=', $year->year - 1)
            ->where('book_extra_prices.deleted', 0)
            ->get();
    if ($extras) {
      foreach ($extras as $e) {
        $m = date('n', strtotime($e->start));
        $months_extras_2[$m] += $e->price;
      }
    }
    $extras = Book::where_type_book_reserved()
            ->select('book_extra_prices.price', 'book_extra_prices.type', 'start', 'extra_id')
            ->Join('book_extra_prices', 'book_extra_prices.book_id', '=', 'book.id')
            ->whereYear('start', '=', $year->year - 2)
            ->where('book_extra_prices.deleted', 0)
            ->get();
    if ($extras) {
      foreach ($extras as $e) {
        $m = date('n', strtotime($e->start));
        $months_extras_3[$m] += $e->price;
      }
    }


    /* END: Past Years */

    $selected = ($year->year - 2000) . ',' . date('m');
    $extraCostBooks = 0;

    return view('backend/sales/extras/index', [
        'year' => $year,
        'extrasGroup' => $extrasGroup,
        'extTyp' => $extTyp,
        'extrasList' => $extrasList,
        'extraTit' => $extraTit,
        'lstMonths' => $lstMonths,
        'extraCostBooks' => $extraCostBooks,
        'months_1' => $months_extras_1,
        'months_2' => $months_extras_2,
        'months_3' => $months_extras_3,
        'months_label' => $months_label,
        'selected' => $selected,
            ]
    );
  }

  /**
   * Get the Extras by month-years to ajax table
   * 
   * @param Request $request
   * @return Json-Objet
   */
  public function get_list(Request $request, $isAjax = true) {

    $year = $request->input('year', null);
    $month = $request->input('month', null);
    if (!$year || !$month) {
      return response()->json(['status' => 'wrong']);
    }

    $response = [
        'status' => 'false',
        'respo_list' => [],
    ];


    $excusion_months = [];
    $monthsVdor = [];
    $book = Book::where_type_book_reserved()
            ->whereYear('start', '=', '20' . $year)
            ->whereMonth('start', '=', $month)
            ->with('extrasBook')
            ->get();

    if ($book) {
      $list = [];
      $totals_pvp = [];
      $totals_cost = [];
      $extras = \App\ExtraPrices::getDynamic();

      $lstExtras = [];
      $extraTit = [];
      foreach ($extras as $e) {
        $lstExtras[$e->id] = ['name' => $e->name, 'qty' => 0, 'price' => 0, 'cost' => 0];
        $extraTit[$e->id] = $e->name;
      }

      $rooms = \App\Rooms::get();
      $roomLst = [];
      foreach ($rooms as $r) {
        $roomLst[$r->id] = $r->name;
      }


      $extTyp = \App\ExtraPrices::getTypes();
      $extrasGroup = array();
      foreach ($extTyp as $k => $v) {
        $extrasGroup[$k] = $v;
      }

      $totalMonth = 0;
      foreach ($book as $b) {
        if (count($b->extrasBook)) {

          $auxBook = [
              'id' => $b->id,
              'customer' => $b->customer->name,
              'date' => convertDateToShow_text($b->start) . ' - ' . convertDateToShow_text($b->finish),
              'nigth' => $b->nigths,
              'price' => $b->total_price,
              'room' => isset($roomLst[$b->room_id]) ? $roomLst[$b->room_id] : '--',
          ];


          foreach ($b->extrasBook as $extr) {

            if ($extr->deleted == 1 || $extr->fixed == 1)
              continue;


            if (!isset($list[$extr->extra_id]))
              $list[$extr->extra_id] = [];
            if (!isset($totals_pvp[$extr->extra_id])) {
              $totals_pvp[$extr->extra_id] = 0;
              $totals_cost[$extr->extra_id] = 0;
            }

            $totalMonth += $extr->price;

            $list[$extr->extra_id][] = [
                'book' => $auxBook,
                'qty' => $extr->qty,
                'price' => $extr->price,
                'cost' => $extr->cost,
                'vdor' => $extr->vdor ? $extr->vdor : '--',
                'percent' => round($extr->price / $auxBook['price'] * 100),
            ];

            if (isset($lstExtras[$extr->extra_id])) {
              $lstExtras[$extr->extra_id]['qty'] += $extr->qty;
              $lstExtras[$extr->extra_id]['price'] += $extr->price;
              $lstExtras[$extr->extra_id]['cost'] += $extr->cost;
            }

            //prepare analysis by excursion
            $type = isset($extrasGroup[$extr->type]) ? $extr->type : 'others';
            if ($type == 'excursion') {
              if (!isset($excusion_months[$extr->extra_id])) {
                $excusion_months[$extr->extra_id] = [
                    'name' => isset($extraTit[$extr->extra_id]) ? $extraTit[$extr->extra_id] : '--',
                    'pvp' => $extr->price,
                    'cost' => $extr->cost,
                ];
              } else {
                $excusion_months[$extr->extra_id]['pvp'] += $extr->price;
                $excusion_months[$extr->extra_id]['cost'] += $extr->cost;
              }
            }
            //prepare analysis by Vendor

            if (!isset($monthsVdor[$extr->vdor])) {
              $monthsVdor[$extr->vdor] = [
                  'pvp' => $extr->price,
                  'cost' => $extr->cost,
              ];
            } else {
              $monthsVdor[$extr->vdor]['pvp'] += $extr->price;
              $monthsVdor[$extr->vdor]['cost'] += $extr->cost;
            }
          }
        }
      }



      $excursionsHTML = view('backend.sales.extras.analysis-excursion', [
          'excusion_months' => $excusion_months,
          'monthsVdor' => $monthsVdor,
          'month_name' => getMonthsSpanish($month, false)
              ])->render();



      $response = [
          'status' => 'true',
          'extras' => $lstExtras,
          'respo_list' => $list,
          'totals_pvp' => $totals_pvp,
          'totals_cost' => $totals_cost,
          'excursionsHTML' => $excursionsHTML,
          'totalMonth' => moneda($totalMonth),
      ];
    }

    if ($isAjax) {
      return response()->json($response);
    } else {
      return $response;
    }
  }

  /**
   * Get Extras Objet by Year Object
   * 
   * @param Object $year
   * @return array
   */
  private function getMonthlyExtras($year) {


    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    $arrayMonth = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'];
    $lstMonthlyCost = [];


    $monthlyCost = \App\Book::getMonthSum('cost_limp', 'finish', $startYear, $endYear);
    foreach ($monthlyCost as $item) {
      $cMonth = intval(substr($item->new_date, 0, 2));
      $lstMonthlyCost[$cMonth] = floatval($item->total);
    }

    //Prepare objets to JS Chars
    $months_lab = '';
    $months_val = [];
    $months_obj = [];
    $thisMonth = date('m');
    $dates = getArrayMonth($startYear, $endYear);
    $selected = null;
    foreach ($dates as $d) {

      if ($thisMonth == $d['m']) {
        $selected = $d['y'] . ',' . $d['m'];
      }

      $months_lab .= "'" . $arrayMonth[$d['m'] - 1] . "',";
      if (!isset($lstMonthlyCost[$d['m']])) {
        $months_val[] = 0;
      } else {
        $months_val[] = $lstMonthlyCost[$d['m']];
      }
      //Only to the Months select
      $months_obj[] = [
          'id' => $d['y'] . '_' . $d['m'],
          'month' => $d['m'],
          'year' => $d['y'],
          'name' => $arrayMonthMin[$d['m'] - 1]
      ];
    }

    return [
        'year' => $year->year,
        'selected' => $selected,
        'months_obj' => $months_obj,
        'months_label' => $months_lab,
        'months_val' => implode(',', $months_val)
    ];
  }

  public function sales_index(Request $request, $year = "") {

    $year = $this->getActiveYear();
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    $diff = $startYear->diffInMonths($endYear) + 1;
    $lstMonths = lstMonths($startYear, $endYear);

    $months_empty = array();
    $months_label = array();
    for ($i = 0; $i < 13; $i++)
      $months_empty[$i] = 0;
    foreach ($lstMonths as $m => $v) {
      $aux = getMonthsSpanish($v['m']);
      $lstMonths[$m]['name'] = $aux;
      $months_label[] = "'$aux'";
    }

    /// BEGIN: Extras
    $months_extras = $months_empty;

    $extTypes = \App\ExtraPrices::getTypes();
    $oExtras  = \App\ExtraPrices::getDynamic();
    
//    $customersReq = \App\CustomersRequest::select('book_id')->whereNotNull('book_id')
//            ->whereYear('start', '=', $year->year)->pluck('book_id')->toArray();
     
    $extrasGroupUser = array();
    $extras = Book::where_type_book_reserved()
            ->select('book_extra_prices.price','book_extra_prices.user_id','start', 'extra_id')
            ->Join('book_extra_prices', 'book_extra_prices.book_id', '=', 'book.id')
            ->whereYear('start', '=', $year->year)
            ->where('book_extra_prices.deleted', 0)
            ->get();
    if ($extras) {
      foreach ($extras as $e) {
        
        $m = date('n', strtotime($e->start));
        $months_extras[$m] += $e->price;
        $months_extras[0] += $e->price;
        $userID = $e->user_id;
        
        if (!isset($extrasGroupUser[$userID])){
          $extrasGroupUser[$userID] = $months_empty;
        }
        $extrasGroupUser[$userID][$m] += $e->price;
      }
    }
    
    $book = Book::where_type_book_reserved()
            ->select('total_price','customers_requests.user_id','book.start')
            ->Join('customers_requests', 'customers_requests.book_id', '=', 'book.id')
            ->whereYear('book.start', '=', $year->year)
            ->get();
     
    if ($book) {
      foreach ($book as $b){
        $m = date('n', strtotime($b->start));
        $userID = $b->user_id;
        if (!isset($extrasGroupUser[$userID])){
          $extrasGroupUser[$userID] = $months_empty;
        }
        $extrasGroupUser[$userID][$m] += $b->total_price;
        
      }
    }
 
    $allUsers = \App\User::all();
    $users = [];
    foreach ($allUsers as $u) $users[$u->id] = $u->name;
      
    $colors = colors();
    return view('backend/sales/sales/index', [
        'year' => $year,
        'extrasGroup' => $extrasGroupUser,
        'users' => $users,
        'extTyp' => $extTypes,
        'lstMonths' => $lstMonths,
        'months_label' => $months_label,
        'month_sel' => date('m'),
        'year_sel' => ($year->year - 2000),
        'colors' => $colors,
        'isMobile' => config('isMobile'),
            ]
    );
  }

  /**
   * Get the SALES by month-years to ajax table
   * 
   * @param Request $request
   * @return Json-Objet
   */
  public function get_sales_list(Request $request, $isAjax = true) {

    $year = $request->input('year', null);
    $month = $request->input('month', null);
    $uID_sel = $request->input('user_id', null);
    if (!$year && !$month) {
      return response()->json(['status' => 'wrong']);
    }

    $response = [
        'status' => 'false',
        'respo_list' => [],
    ];


    $excusion_months = [];
    $monthsVdor = [];
    
 
    
    $qry_book = Book::where_type_book_reserved()
            ->whereYear('start', '=', '20' . $year);
    
    if ($month) $qry_book->whereMonth('start', '=', $month);
    
    $book = $qry_book->with('extrasBook')->get();
    
    if ($book) {
      $extTyp = \App\ExtraPrices::getTypes();
      $oExtras = \App\ExtraPrices::getDynamic();
      $extraType = array();
      foreach ($oExtras as $item) {
        $extraType[$item->id] = $item->type;
      }
      $extrasVal  = ['conv'=>0];
      $extrasUser = array();
      foreach ($extTyp as $k => $v) {
        $extrasVal[$k] = 0;
      }
      $totalType = $extrasVal;
      $uCR = []; // User - Customer Request
      $totalCR = 0;

      $totalMonth = 0;
      foreach ($book as $b) {
        if (count($b->extrasBook)) {
          foreach ($b->extrasBook as $e) {
            if ($e->deleted == 1 || $e->fixed == 1)
              continue;
            
            $userID = $e->user_id;
            if ($uID_sel && $uID_sel != $userID) continue;
            if (!isset($extrasUser[$userID])){
              $extrasUser[$userID] = $extrasVal;
            }
            $type = isset($extraType[$e->extra_id]) ? $extraType[$e->extra_id] : 'others';

            $extrasUser[$userID][$type] += $e->price;
            $totalType[$type] += $e->price;
        
            $totalMonth += $e->price;
          }
        }
      }
    
    /** Resrvas vendidas por sistema */
    
    $qry_book = Book::where_type_book_reserved()
        ->select('total_price','customers_requests.user_id','book.start')
        ->Join('customers_requests', 'customers_requests.book_id', '=', 'book.id')
        ->whereYear('book.start', '=', '20' . $year);
    if ($month) $qry_book->whereMonth('book.start', '=', $month);
    
    $book = $qry_book->get();
    
    if ($book) {
      foreach ($book as $b){
        $m = date('n', strtotime($b->start));
        $userID = $b->user_id;
        if ($uID_sel && $uID_sel != $userID) continue;
        if (!isset($extrasUser[$b->user_id])) $extrasUser[$b->user_id] = $extrasVal;
        $extrasUser[$b->user_id]['conv'] += $b->total_price;
        $totalType['conv'] += $b->total_price;
        
      }
    }
    /** Resrvas vendidas por sistema */
    
    
      $allUsers = \App\User::all();
      $users = [];
      foreach ($allUsers as $u) $users[$u->id] = $u->name;
      
      $result = [];
      if (count($extrasUser)>0){
        foreach ($extrasUser as $uId=>$v){
          
          $aux = [];
          $aux[] = isset($users[$uId]) ? $users[$uId] : 'Usuario';
          foreach ($v as $k2=>$v2){
            $aux[] = moneda($v2,false);
            $aux[] = ($totalType[$k2] > 0) ? intval($v2/$totalType[$k2]*100) : 0 ;
          }
          $result[] = $aux;
          
        }
      }
      $response = [
          'status' => 'true',
          'result' => $result,
          'totalType' => $totalType,
          'totalMonth' => moneda($totalMonth),
          'totalConv' => moneda($totalType['conv']),
      ];
    }

    if ($isAjax) {
      return response()->json($response);
    } else {
      return $response;
    }
  }

  /**
   * Get SALES Objet by Year Object
   * 
   * @param Object $year
   * @return array
   */
  private function getMonthlySales($year) {


    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    $arrayMonth = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'];
    $lstMonthlyCost = [];


    $monthlyCost = \App\Book::getMonthSum('cost_limp', 'finish', $startYear, $endYear);
    foreach ($monthlyCost as $item) {
      $cMonth = intval(substr($item->new_date, 0, 2));
      $lstMonthlyCost[$cMonth] = floatval($item->total);
    }

    //Prepare objets to JS Chars
    $months_lab = '';
    $months_val = [];
    $months_obj = [];
    $thisMonth = date('m');
    $dates = getArrayMonth($startYear, $endYear);
    $selected = null;
    foreach ($dates as $d) {

      if ($thisMonth == $d['m']) {
        $selected = $d['y'] . ',' . $d['m'];
      }

      $months_lab .= "'" . $arrayMonth[$d['m'] - 1] . "',";
      if (!isset($lstMonthlyCost[$d['m']])) {
        $months_val[] = 0;
      } else {
        $months_val[] = $lstMonthlyCost[$d['m']];
      }
      //Only to the Months select
      $months_obj[] = [
          'id' => $d['y'] . '_' . $d['m'],
          'month' => $d['m'],
          'year' => $d['y'],
          'name' => $arrayMonthMin[$d['m'] - 1]
      ];
    }

    return [
        'year' => $year->year,
        'selected' => $selected,
        'months_obj' => $months_obj,
        'months_label' => $months_lab,
        'months_val' => implode(',', $months_val)
    ];
  }

}

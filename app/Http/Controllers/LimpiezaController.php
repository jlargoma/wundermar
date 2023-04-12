<?php

namespace App\Http\Controllers;

use App\Classes\Mobile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Book;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LimpiezaController extends AppController {

  public function index(Request $req, $year = "") {
    
    $start = null;
    $dates = $req->input('filter');
    if ($dates){
      $dates = explode(' - ', $dates);
      if (count($dates) == 2){
        $start = Carbon::createFromFormat('d M, y', $dates[0]);
        $finish = Carbon::createFromFormat('d M, y', $dates[1]);
      }
    }
    if (!$start){
      $now = Carbon::now();
      $start = $now->copy()->subDays(3);
      $finish = $now->copy()->addMonth(3);
    }
    
    $dateFiltrer = $start->format('d M, y').' - '.$finish->format('d M, y');
    $start = $start->format('Y-m-d');
    $finish = $finish->format('Y-m-d');
    

    $noRooms = \App\Rooms::where('channel_group','')->pluck('id');
    $rooms = \App\Rooms::all();

    $booksQuery = Book::where('start', '>=', $start)->where('start', '<=', $finish)
                ->with('room', 'customer')
                ->whereNotIn('room_id', $noRooms)
                ->whereIn('type_book', [1, 2, 7, 8])->orderBy('start', 'ASC');
    $checkin = $booksQuery->get();
    $cliHas = Book::cliHas_lst($booksQuery->pluck('id'));
    
    $checkout =Book::where('finish', '>=', date('Y-m-d'))
                ->where('finish', '<=', $finish)->whereIn('type_book',[1,2,7,8,10])
                ->with('room','customer')
                ->whereNotIn('room_id',$noRooms)
                ->orderBy('finish', 'ASC')->get();
    
    
    
    
    //BEGIN: extrasBook
    $sExtrasPurchase = new \App\Services\Bookings\ExtrasPurchase();
    $aExtrs = $sExtrasPurchase->getAdminAlerts($noRooms);
    //BEGIN: BLOQUEOS
    $sqlBooksBlocks =Book::where('finish', '>=', $start)
                ->where('finish', '<=', $finish)->where('type_book', 4)
                ->with('room','customer')
                ->whereNotIn('room_id',$noRooms);
    $noCustomer = \App\Customers::whereIn('id',$sqlBooksBlocks->pluck('customer_id'))
            ->where('name','Bloqueo automatico')->pluck('id');
    $bloqueada = $sqlBooksBlocks->whereNotIn('customer_id',$noCustomer)
            ->orderBy('finish', 'ASC')->get();
    //END: BLOQUEOS -

    $isMobile = config('app.is_mobile');
    $uRole = getUsrRole();

      
    return view('backend.limpieza.index', [
        'mobile' => new Mobile(),
        'checkin' => $checkin,
        'checkout' => $checkout,
        'bloqueada' => $bloqueada,
        'rooms' => $rooms,
        'isMobile' => $isMobile,
        'uRole' => $uRole,
        'dateFiltrer' => $dateFiltrer,
        'lstExtrs'=>$aExtrs['lst'],
        'toDeliver'=>$aExtrs['toDeliver'],
        'cliHas'=>$cliHas
    ]);
  }

  /**
   * Get Limpieza index
   * 
   * @return type
   */
  public function limpiezas() {

    $year = $this->getActiveYear();
    $obj1 = $obj2 = $obj3 = null;
    $obj1 = $this->getMonthlyLimpieza($year);
    $year2 = $this->getYearData($year->year - 1);
    if ($year2)  $obj2 = $this->getMonthlyLimpieza($year2);
    $year3 = $this->getYearData($year2->year - 1);
    if ($year3)  $obj3 = $this->getMonthlyLimpieza($year3);

    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);

    // calculate total by month: limp and extra
    $dates = getArrayMonth($startYear, $endYear);
    $t_month = [];
    foreach ($dates as $d) {

      $t_month[$d['m'] . '-' . $d['y']] = [
          'limp' => 0,
          'extra' => 0,
          'label' => getMonthsSpanish($d['m']) . ' ' . $d['y']
      ];
    }


    $extraCostBooks = 0;
    $monthlyCost =Book::getMonthSum('extraCost', 'finish', $startYear, $endYear);
    if (count($monthlyCost)) {
      foreach ($monthlyCost as $item) {
        if (isset($t_month[$item->new_date])) {
          $t_month[$item->new_date]['extra'] = $item->total;
          $extraCostBooks += $item->total;
        }
      }
    }
    $totalCostBooks = 0;
    $monthlyCost =Book::getMonthSum('cost_limp', 'finish', $startYear, $endYear);
    if (count($monthlyCost)) {
      foreach ($monthlyCost as $item) {
        if (isset($t_month[$item->new_date])) {
          $t_month[$item->new_date]['limp'] = $item->total;
          $totalCostBooks += $item->total;
        }
      }
    }





    return view('backend/sales/limpiezas', [
        'year' => $year,
        'selected' => $obj1['selected'],
        'months_obj' => $obj1['months_obj'],
        'months_1' => $obj1,
        'months_2' => $obj2,
        'months_3' => $obj3,
        'totalCostBooks' => $totalCostBooks,
        'extraCostBooks' => $extraCostBooks,
        't_month' => $t_month
            ]
    );
  }

  /**
   * Get Limpieza Objet by Year Object
   * 
   * @param Object $year
   * @return array
   */
  private function getMonthlyLimpieza($year) {


    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);

    $arrayMonth = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $arrayMonthMin = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'];
    $lstMonthlyCost = [];

    $monthlyCost =Book::getMonthSum('cost_limp', 'finish', $startYear, $endYear);
    foreach ($monthlyCost as $item) {
//      $cMonth = intval(substr($item->new_date,0,2));
      $lstMonthlyCost[$item->new_date] = floatval($item->total);
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

      $id = $d['m'] . '-' . $d['y'];
      $months_lab .= "'" . $arrayMonth[$d['m'] - 1] . "',";
      if (!isset($lstMonthlyCost[$id])) {
        $months_val[] = 0;
      } else {
        $months_val[] = $lstMonthlyCost[$id];
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

  /**
   * Get the Limpieza by month-years to ajax table
   * 
   * @param Request $request
   * @return Json-Objet
   */
  public function get_limpiezas(Request $request, $isAjax = true) {

    $year = $request->input('year', null);
    $month = $request->input('month', null);

    $respo_list = [];
    $month_cost = [];
    $total_limp = 0; //start with the monthly cost
    $total_extr = 0;

    if (!$year) {
      return response()->json(['status' => 'wrong']);
    }
    if ($month) {
      // First day of a specific month
      $d = new \DateTime($year . '-' . $month . '-01');
      $d->modify('first day of this month');
      $startYear = $d->format('Y-m-d');
      // First day of a specific month
      $d = new \DateTime($year . '-' . $month . '-01');
      $d->modify('last day of this month');
      $endYear = $d->format('Y-m-d');
    } else {
      $oYear = \App\Years::where('year', $year)->first();
      $startYear = $oYear->start_date;
      $endYear = $oYear->end_date;
    }
  
    $lstBooks = \App\Services\Bookings\GetBooksLimp::getBooks($startYear,$endYear);
    
    $count = 0;
    foreach ($lstBooks as $key => $book) {
//      $agency = ($book->agency != 0) ? '/pages/' . strtolower($book->getAgency($book->agency)) . '.png' : null;
      $agency = null;
      $type_book = null;
      switch ($book->type_book) {
        case 1:
          $type_book = "R";
          break;
        case 2:
          $type_book = "C";
          break;
        case 4:
          $type_book = "B";
          break;
        case 7:
          $type_book = "P";
          break;
        case 8:
          $type_book = "A";
          break;
      }

      $start = Carbon::createFromFormat('Y-m-d', $book->start);
      $finish = Carbon::createFromFormat('Y-m-d', $book->finish);

      $respo_list[] = [
          'id' => $book->id,
          'name' => $book->getCustomerName(),
          'agency' => $agency,
          'type' => $type_book,
          'limp' => $book->cost_limp,
          'extra' => $book->extraCost,
          'pax' => $book->pax,
          'apto' => $book->room->nameRoom,
          'check_in' => $start->formatLocalized('%d %b'),
          'check_out' => $finish->formatLocalized('%d %b'),
          'nigths' => $book->nigths,
      ];

      $total_limp += floatval($book->cost_limp);
      $total_extr += floatval($book->extraCost);
      
      if ($book->cost_limp>0) $count++;
    }

    $response = [
        'status' => 'true',
        'month_cost' => $month_cost,
        'respo_list' => $respo_list,
        'total_limp' => moneda($total_limp),
        'total_extr' => moneda($total_extr),
        'total_summ' => moneda($total_limp+$total_extr),
        'count'      => $count
    ];
    if ($isAjax) {
      return response()->json($response);
    } else {
      return $response;
    }
  }

  /**
   * Update Limpieza or Extra values
   * 
   * @param Request $request
   * @return json
   */
  public function upd_limpiezas(Request $request) {
    $id = $request->input('id', null);
    $limp_value = $request->input('limp_value', null);
    $extr_value = $request->input('extr_value', null);
    $year = $request->input('year', null);
    $month = $request->input('month', null);


    if ($id) {

      if (strpos($id, 'expenses_') !== FALSE) {
        $date = $request->input('date', null);
        $id = str_replace('expenses_', '', $id);
        $monthItem = \App\Expenses::find($id);
        if ($monthItem) {
          $monthItem->import = floatval($limp_value);
          $monthItem->save();
        }
        return response()->json(['status' => 'true']);
      } else {
        if (!(is_numeric($limp_value) || empty($limp_value))) {
          return response()->json(['status' => 'false', 'msg' => "El valor de la Limpieza debe ser numérico"]);
        }
        if (!(is_numeric($extr_value) || empty($extr_value))) {
          return response()->json(['status' => 'false', 'msg' => "El valor Extra debe ser numérico"]);
        }
        $book =Book::find($id);
        if ($book) {

          $cost = $book->cost_total - ($book->cost_limp + $book->extraCost);
          $book->cost_limp = floatval($limp_value);
          $book->extraCost = floatval($extr_value);
          $book->cost_total = $cost + ($book->cost_limp + $book->extraCost);
          $book->save();

          return response()->json(['status' => 'true']);
        }
      }
    }

    return response()->json(['status' => 'false', 'msg' => "No se han encontrado valores."]);
  }

  public function export_pdf_limpiezas(Request $request) {

    $year = $request->input('year', null);
    $month = $request->input('month', null);

    $arrayMonth = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $file_name = 'Costos-de-limpieza-' . $month . '-20' . $year;

    if (is_numeric($month) && isset($arrayMonth[$month - 1])) {
      $title = $arrayMonth[$month - 1] . ' 20' . $year;
    } else {
      $title = ' 20' . $year;
    }
    $data = $this->get_limpiezas($request, false);
    $data['tit'] = $title;
    // Send data to the view using loadView function of PDF facade
    $pdf = \PDF::loadView('pdf.limpieza', $data);
    // Finally, you can download the file using download function
    return $pdf->download($file_name . '.pdf');
  }
  
  public function bloqueos(Request $request) {

    $start   = $request->input('start', null);
    $finish  = $request->input('finish', null);
    $newroom = $request->input('newroom', null);
    
    
    
    $oBookings = new Book();
    $customerID = null;
    $oRooms = \App\Rooms::find($newroom);
    if (!$oRooms) {
      return back()->withErrors(['Apartamento no encontrado']);
    }
    $oUser = Auth::user();
    if (!$oBookings->availDate($start, $finish, $oRooms->id))
      return back()->withErrors(['Apartamento no disponible']);
    
    
    $oCustomers = new \App\Customers();
    $oCustomers->user_id = $oUser ? $oUser->id : 1;
    $oCustomers->name = 'Bloqueo ' . ($oUser ? $oUser->name : 'Limpieza');
    $oCustomers->save();
    $customerID = $oCustomers->id;
          

    $book = new Book();
    $book->user_id = $oUser ? $oUser->id : 1;
    $book->customer_id = $customerID;
    $book->room_id = $oRooms->id;
    $book->start = $start;
    $book->finish = $finish;
    $book->book_comments = $request->input('book_comments', null);
    $book->nigths = calcNights($start, $finish);
    $book->type_book = 4;
    $book->save();


    $oBookings->sendAvailibility($oRooms->id, $start, $finish);
    
    return back()->with(['success'=>'Bloqueo realizado']);
  }
 
   public function bloqueos_delete($id) {

    try {
      $book = Book::find($id);
      if ($book->type_book != 4){
        return [
            'status' => 'danger',
            'title' => 'Error:',
            'response' => "La Reserva no es un bloqueo."
        ];
      }
      $book->type_book = 0;
      if ($book->save()) {
        $book->sendAvailibilityBy_status($book->start,$book->finish);
        return [
            'status' => 'success',
            'title' => 'OK',
            'response' => "Reserva enviada a eliminadas"
        ];
      }
    } catch (Exception $e) {

      return [
          'status' => 'danger',
          'title' => 'Error',
          'response' => "No se ha podido borrar la reserva error: " . $e->message()
      ];
    }
  }

   public function deliverExtra(Request $request) {
    $bID   = $request->input('bID', null);
    $delivered  = $request->input('delivered', 0);
    
    $oDeliver = \App\BookData::where('key','extr_delivered')
                ->where('book_id',$bID)->first();
    
    if (!$oDeliver){
        if (!$delivered) return 'OK';
        
         $oDeliver = new \App\BookData();
         $oDeliver->key = 'extr_delivered';
         $oDeliver->book_id = $bID;
         
    }
    $oDeliver->content = $delivered;
    $oDeliver->save();
    return 'OK';
  }
  

}

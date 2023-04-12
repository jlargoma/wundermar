<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Expenses;

class Liquidacion
{	

    function getTPV($books) {
      $bIds = [];
      if($books){
        foreach ($books as $book){
          if ($book->stripeCost < 1){
            $bIds[] = $book->id;
          }
        }
      }
          
      $payments = \App\Payments::whereIn('type',[2,3])->whereIn('book_id',$bIds)
              ->groupBy('book_id')->selectRaw('sum(import) as sum, book_id')->pluck('sum','book_id');
      $stripeCost = [];
      if($books){
        foreach ($books as $book){
          $stripeCost[$book->id] = 0;
          if (isset($payments[$book->id])){
            $stripeCost[$book->id] = paylandCost($payments[$book->id]);
          }
        }
      }

      return $stripeCost;
    }
  
    /**
     * 
     * @return type
     */
    public function summaryTemp($oYear = null) {
      return $this->get_summary(BookDay::getBy_temporada($oYear),true,$oYear);
    }

    /**
     * 
     * @param type $books
     * @return type
     */
    public function get_summary($lstRvs,$temporada=false,$oYear) {
      $bLstID = [];
      $t_pax = $t_nights = $t_pvp = $t_cost = $vta_agency = 0;
      foreach ($lstRvs as $key => $b) {
        $m = date('ym', strtotime($b->date));
        $t_pvp += $b->pvp;
        $t_nights++;
        if (!isset($bLstID[$b->book_id])){
          $bLstID[$b->book_id] = 1;
          if ($b->agency != 0) $vta_agency++;
          $t_pax += $b->pax;
        }
        $t_cost += $b->costs;
      }
      //------------------------------------
      $t_cost = $this->getExpensesPayments();
      $t_books = count($bLstID);
      $t_pvp = round($t_pvp);
      $benef = $benef_inc = 0;
      if($t_books>0){
        $benef = $t_pvp-$t_cost;
        $benef_inc = round(($benef)/$t_pvp*100);
      }
      //------------------------------------
      
      $summary = [
          'total'=>$t_books,
          'total_pvp'=>$t_pvp,
          'total_cost'=>$t_cost,
          'benef'=>$benef,
          'benef_inc'=>$benef_inc,
          'pax'=>$t_pax,
          'nights'=>$t_nights,
          'nights-media' => ($t_nights>0) ? ceil($t_nights/$t_books) : 0,
          'vta_prop'=>0,
          'vta_agency'=>0,
          'daysTemp'=>$oYear->getNumDays()
        ];
      if($t_books>0){
        $summary['vta_agency'] = round(($vta_agency / $t_books) * 100);
        $summary['vta_prop'] = 100-$summary['vta_agency'];    
      }
     
      return $summary;
    }
    
  /**
   * 
   * @param type $books
   * @return type
   */
  function getExpensesEstimation($books){
    
//    AGENCIAS(cta pyg) + 
//        AMENITIES (cta pyg)  + TPV (cta pyg)
//        +REPARACION Y CONSERVACION (cta pyg)
    
    $aExpensesPending = $aExpensesPayment = Expenses::getExpensesBooks();
    
    
    foreach ($books as $key => $book) {
//      $aExpensesPending['prop_pay']  += $book->get_costProp();
      $aExpensesPending['agencias']  += $book->PVPAgencia;
      $aExpensesPending['amenities'] += $book->extraCost;
      $aExpensesPending['excursion']  += $book->extrasDynamicCost('excursion');
    }

    $stripeCost = $this->getTPV($books);
    $aExpensesPending['comision_tpv'] = round(array_sum($stripeCost),2);
 
      
    return $aExpensesPending;
  }
  /**
   * 
   * @param type $books
   * @return type
   */
  function getLimpiezaEstimation($year){
    
//      1 => 'Reservado - stripe',
//      2 => 'Pagada-la-señal',
//      4 => 'Bloqueado',
//      7 => 'Reserva Propietario',
//      8 => 'ATIPICAS',
//     11 => 'blocked-ical',
    
    $noRooms = \App\Rooms::where('channel_group','')->pluck('id');
    $sqlBooks =Book::whereYear('finish', '=', $year)
                    ->whereNotIn('room_id',$noRooms)
                    ->whereIn('type_book', [1,2,4,7,8,11]);
    $noCustomer = \App\Customers::whereIn('id',$sqlBooks->pluck('customer_id'))
            ->where('name','Bloqueo automatico')->pluck('id');

    $books = $sqlBooks->whereNotIn('customer_id',$noCustomer)
            ->orderBy('finish', 'ASC')->get();
    $limpieza = 0;
    $lavanderia = 0;
    foreach ($books as $b) {
      $limpieza  += $b->cost_limp;
      $lavanderia += 6;
    }
    return ['limpieza'=>$limpieza,'lavanderia'=>$lavanderia];
  }
  
  private function filterEstimates($aExpensesPending) {
       
    
    $oData = \App\ProcessedData::findOrCreate('PyG_Hide');
    if ($oData){
      $PyG_Hide = json_decode($oData->content,true);
      if ($PyG_Hide && is_array($PyG_Hide)){
        foreach ($PyG_Hide as $k){
          if(isset($aExpensesPending[$k])) $aExpensesPending[$k] = 0;
        }
      }
    }
    return $aExpensesPending;
  }
  
  /**
   * 
   * @return type
   */
  function getExpensesPayments(){
    
//    ALQUILER INMUEBLES + AGENCIAS(cta pyg) + 
//        AMENITIES (cta pyg)  + TPV (cta pyg) + LAVANDERIA (cta pyg) 
//        + LIMPIEZA (cta pyg) +REPARACION Y CONSERVACION (cta pyg)
    
    $aExpensesPayment = Expenses::getExpensesBooks();
   // $activeYear = Years::getActive();
    $activeYear = getObjYear();
    return \App\Expenses::where('date', '>=', $activeYear->start_date)
                    ->Where('date', '<=', $activeYear->end_date)
                    ->WhereIn('type',array_keys($aExpensesPayment))
                    ->sum('import');
  }

  /**
   * Get payment to prop and others expenses to the prop
   * @return type
   */
  function getTotalPaymentsProp(){
    return 0;
  }
  
  
  
  /**
   * Obtener la HOJA DE GASTOS para propietarios
   * 
   * @param type $year
   * @param type $room
   * @return type
   */
   static function getSalesByYearByRoomGeneral($room = "all") {

    //$year = Years::getActive();
    $year = getObjYear();
    $startYear = $year->start_date;
    $endYear = $year->end_date;

    $total = 0;
    $tarjeta = 0;
    $metalico = 0;
    $banco = 0;
    $pagado = 0;
    if ($room == "all") {
//      $rooms = \App\Rooms::where('state', 1)->get(['id']);
      $books = \App\Book::where_type_book_sales()
//              ->whereIn('room_id', $rooms)
              ->where('start', '>=',$startYear)
              ->where('start', '<=', $endYear)
              ->orderBy('start', 'ASC')->get();


      foreach ($books as $key => $book) {
        $total += $book->get_costProp();
      }
      $gastos = \App\Expenses::where('date', '>=',$startYear)
              ->where('date', '<=', $endYear)
              ->WhereNotNull('PayFor')   
              ->orderBy('date', 'DESC')->get();
      
      foreach ($gastos as $payment) {
        switch ($payment->typePayment){
          case 0:
            $tarjeta += $payment->import;
            break;
          case 1:
          case 2:
            $metalico += $payment->import;
            break;
          case 3:
            $banco += $payment->import;
            break;
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
        $total += $book->get_costProp();
      }

      $gastos = \App\Expenses::where('date', '>=',$startYear)
              ->where('date', '<=', $endYear)
              ->Where('PayFor', 'LIKE', '%' . $room . '%')
              ->orderBy('date', 'DESC')->get();

      foreach ($gastos as $payment) {
        
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
         
        $import = round($payment->import / $divisor,2);
        switch ($payment->typePayment){
          case 0:
            $tarjeta += $import;
            break;
          case 1:
          case 2:
            $metalico += $import;
            break;
          case 3:
            $banco += $import;
            break;
        }
        
        $pagado += $import;
      }
    }


    return [
        'total' => $total,
        'banco' => $banco,
        'metalico' => $metalico,
        'tarjeta' => $tarjeta,
        'pagado' => $pagado,
    ];
  }
  
   function getBookingAgencyDetailsBy_date($start,$end,$roomsID=null) {
        
      $dataNode = [
            'reservations'      => 0,
            'total'             => 0,
            'commissions'       => 0,
            'reservations_rate' => 0,
            'total_rate'        => 0
        ];
      $data = [
                'fp'   => $dataNode, //  FAST PAYMENT
                'vd'   => $dataNode, // V. Directa
                'b'    => $dataNode, //Booking
                'ab'   => $dataNode, // AirBnb
                'ag'   => $dataNode, //Agoda
                'ex'   => $dataNode, //Expedia
                't'    => $dataNode, // Trivago
                'gh'   => $dataNode, //google-hotel
//                'bs'   => $dataNode, // Bed&Snow
//                'jd'   => $dataNode, // "Jaime Diaz",
//                'se'   => $dataNode, // S.essence
//                'c'   => $dataNode, //Cerogrados
                'none'   => $dataNode, // none
            ];
      $totals = ['total' => 0,'reservations' => 0,'commissions' => 0];
      $sqlBooks = \App\BookDay::where_type_book_sales(true,true)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end);
        
     if ($roomsID && count($roomsID)>0) $sqlBooks->whereIn('room_id',$roomsID);
        
      $books = $sqlBooks->get();
      //-------------------------------
      $oPVPAgencia = \App\Book::whereIn('id',$sqlBooks->groupBy('book_id')->pluck('book_id'))
              ->get();
      $commAge = [];
      if ($oPVPAgencia){
        foreach ($oPVPAgencia as $b){
          $commAge[$b->id] = ($b->nigths>0) ? $b->PVPAgencia / $b->nigths : $b->PVPAgencia;
        }
      }
      //-------------------------------
      
      if ($books){
        foreach ($books as $book){
          $agency_name = 'none';
          switch ($book->agency){
            case 1: $agency_name = 'b';  break;
            case 2: $agency_name = 't';  break;
            case 3: $agency_name = 'ag';  break;
            case 4: $agency_name = 'ab';  break;
//            case 5: $agency_name = 'jd';  break;
            case 6: $agency_name = 'ex';  break;
            case 7: $agency_name = 'gh';  break;
//            case 28: $agency_name = 'se';  break;
//            case 29: $agency_name = 'bs';  break;
            case 30: $agency_name = 'c';  break;
            case 31: $agency_name = 'vd';  break;
            default :
              if ($book->agency>0){
                $agency_name = 'none';
              }
              else $agency_name = 'vd';
            break;
          }
          $t = $book->pvp;
          $PVPAgencia = isset($commAge[$book->book_id]) ? $commAge[$book->book_id] : 0;
          $data[$agency_name]['total']        += $t;
          $data[$agency_name]['reservations'] += 1;
          $data[$agency_name]['commissions']  += $PVPAgencia;
          $totals['total']        += $t;
          $totals['reservations'] += 1;
          $totals['commissions']  += $PVPAgencia;
        
        
        }
        
        foreach ($data as $a=>$d){
          if ($d['reservations']>0 && $totals['reservations']>0)
            $data[$a]['reservations_rate'] = round($d['reservations']/$totals['reservations']*100);
          if ($d['total']>0 && $totals['total']>0)
            $data[$a]['total_rate'] = round($d['total']/$totals['total']*100);
        }
        
      }
      return  ['totals' => $totals,'data'=>$data];
    }
  
    function getArrayAgency(){
      return [
                'fp'   => 'FAST PAYMENT',
                'vd'   => 'V. Directa',
                'b'    => 'Booking',
                'ab'   => 'AirBnb',
                't'    => 'Trivago',
                'ag'   => 'Agoda',
                'ex'   => 'Expedia',
                'gh'   => 'GHotel',
//                'bs'   => 'Bed&Snow',
//                'jd'   => "Jaime Diaz",
//                'se'   => 'S.essence',
//                'c'    => 'Cerogrados',
                'none' => 'Otras'
            ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Repositories\BookRepository;
use App\Repositories\CachedRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use \Carbon\Carbon;
use Auth;
use Mail;
use Illuminate\Routing\Controller;

use App\Rooms;
use App\Book;
use App\Seasons;
use App\Prices;
use App\Traits\BookEmailsStatus;
use App\Traits\BookCentroMensajeria;
use App\Traits\BookLogsTraits;
use App\BookPartee;
use App\ExtraPrices;
use App\BookExtraPrices;

setlocale(LC_TIME, "ES");
setlocale(LC_TIME, "es_ES");

class BookController extends AppController
{
    use BookEmailsStatus, BookCentroMensajeria,BookLogsTraits;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $year      = $this->getActiveYear();
        $startYear = $year->start_date;
        $endYear   = $year->end_date;
        
        if (Auth::user()->role != "agente")
        {
          $rooms       = Rooms::orderBy('order')->get();
          $types       = Book::get_type_book_pending();
          $booksQry = Book::where('start', '>=', $startYear)
                  ->where('start', '<=', $endYear);
                  
            
        } else {
          $roomsAgents = \App\AgentsRooms::where('user_id', Auth::user()->id)->get(['room_id'])->toArray();
          $rooms       = Rooms::whereIn('id', $roomsAgents)->orderBy('order')->get();
          $types       = [1];

          if (!Auth::user()->agent)  return redirect('no-allowed');
          
          $booksQry = Book::whereIn('room_id', $roomsAgents)
                  ->where([
                            ['start','>=',$startYear],
                            ['start','<=',$endYear],
                            ['user_id',Auth::user()->id]
                          ])
                  ->orWhere('agency', Auth::user()->agent->agency_id);
        }
        
        $booksCount['pending'] = 0;
        $booksCount['special'] = 0;
        $booksCount['confirmed']= 0;
        $booksCount['deletes'] = 0;
        $booksCount['checkin'] = 0;
        $booksCount['checkout']=0;
        $query2 = clone $booksQry;
        $booksCount['pending'] = $query2->where('type_book', 3)->count();
        $query2 = clone $booksQry;
        $booksCount['special'] = $query2->whereIn('type_book', [7,8])->count();
        $query2 = clone $booksQry;
        $booksCount['cancel-xml'] = $query2->where('type_book',98)->count();
        $query2 = clone $booksQry;
        $booksCount['confirmed']    = $query2->where('type_book', 2)->count();
        $query2 = clone $booksQry;
        $booksCount['reservadas']    = $query2->where('type_book', 1)->count();
        $query2 = clone $booksQry;
        $booksCount['blocks']    = $query2->where('type_book', 4)->count();
        $query2 = clone $booksQry;
        $totalReserv = $query2->where('type_book', 1)->count();
        $query2 = clone $booksQry;
        $amountReserv = $query2->where('type_book', 1)->sum('total_price');
        
        $booksCount['deletes']      = Book::where('start', '>', $startYear)->where('finish', '<', $endYear)
                                               ->where('type_book', 0)->where("enable", "=", "1")->count();
        
        $booksCount['checkin']      = $this->getCounters('checkin');
        $booksCount['checkout']     = $this->getCounters('checkout');

        $books = $booksQry->whereIn('type_book', $types)->orderBy('created_at', 'DESC')->get();
        
        
        $stripe = null;
       
        $lastBooksPayment = \App\Payments::where('created_at', '>=', Carbon::today()->toDateString())
		                                 ->count();
       
        $mobile      = config('app.is_mobile');
        
        $alert_lowProfits = 0; //To the alert efect
        $percentBenef     = DB::table('percent')->find(1)->percent;
        $lowProfits       = $this->lowProfitAlert($startYear, $endYear, $percentBenef, $alert_lowProfits);

        $ff_pendientes = Book::where('ff_status',4)->where('type_book','>',0)->count();
        
        $parteeToActive = $this->countPartte();
        
        //BEGIN: Processed data
        $bookOverbooking = null;
        $alarmsCheckPaxs = null;
        $overbooking = [];
        $otasDisconect = [];
        $alarmsPayment = 0;
        $oData = \App\ProcessedData::whereIn('key',['overbooking','alarmsPayment','checkPaxs','otasDisconect'])->get();
        foreach ($oData as $d){
          switch ($d->key){
            case 'overbooking':
              $overbooking = json_decode($d->content,true);
              break;
            case 'alarmsPayment':
              if (trim($d->content) != '')
                $alarmsPayment = count(json_decode($d->content));
              break;
            case 'checkPaxs':
              if (trim($d->content) != '')
                $alarmsCheckPaxs = json_decode($d->content,true);
              break;
            case 'otasDisconect':
              if (trim($d->content) != '')
                $otasDisconect = json_decode($d->content,true);
              break;
          }
        }
        //END: Processed data

        $ff_mount = null;
        /**************************************************/
        $urgentes = null;
        if ($parteeToActive > 0){
          $urgentes[] = [
              'action' => 'class="btn btn-danger" id="btnParteeToActive2" test-target="#modalParteeToActive"',
              'text'   => 'Hay PARTEEs no enviados a la policía'
              ];
        }
        $countNotPaidYet = $this->countNotPaidYet();
        if($countNotPaidYet){
          $urgentes[] = [
              'action' => 'class="btn btn-danger  btn-tables" data-type="checkin"',
              'text'   => 'Hay Clientes alojados que no han abonado el 100%'
              ];          
        }
        if(is_array($overbooking) && count($overbooking)>0){
          $urgentes[] = [
              'action' => 'class="btn btn-danger btn-tables"  data-type="overbooking"',
              'text'   => 'Tienes un OVERBOOKING'
              ];          
        }
        if(is_array($alarmsCheckPaxs) && count($alarmsCheckPaxs)>0){
          $urgentes[] = [
              'action' => 'class="btn btn-danger"  type="button" data-toggle="modal" data-target="#modalPAXs"',
              'text'   => 'Se deben controlar el PAXs en reservas'
              ];          
        }
        if(is_array($otasDisconect) && count($otasDisconect)>0){
          $urgentes[] = [
              'action' => 'class="btn btn-danger"  type="button" data-toggle="modal" data-target="#modalOtasDisc"',
              'text'   => 'Se han desconectado Channels en las OTAS'
              ];          
        }
        /**************************************************/
        /*bookings_without_Cvc*/
        $bookings_without_Cvc = \App\ProcessedData::findOrCreate('bookings_without_Cvc');
        $bookings_without_Cvc = json_decode($bookings_without_Cvc->content,true);
        if ($bookings_without_Cvc){
          $bookings_without_Cvc = count($bookings_without_Cvc);
        } else {
          $bookings_without_Cvc = 0;
        }
        /****************************************************************/
        $errorsOtaPrices = \App\PricesOtas::count();
        if(($errorsOtaPrices)>0){
          $urgentes[] = [
              'action' => 'class="btn btn-danger"  type="button" id="goOtasPrices"',
              'text'   => 'Se deben controlar algunos precios en las OTAs'
              ];          
        }
        /****************************************************************/
        $schedule = file_get_contents(app_path().'/Console/listado');
        $schedule = nl2br($schedule);
        /****************************************************************/
        //BEGIN: extrasBook
        $noRooms = [];
        $sExtrasPurchase = new \App\Services\Bookings\ExtrasPurchase();
        $aExtrs = $sExtrasPurchase->getAdminAlerts($noRooms);
        
        $lstExtrs  = $aExtrs['lst'];
        $toDeliver = $aExtrs['toDeliver'];
        /****************************************************************/
        //BEGIN: LOGs OTA
        $ota_errLogs = $this->getOTAsLogErros_qty();     
        //BEGIN: BLOQUEOS
    
        return view('backend/planning/index',
                compact('books', 'mobile', 'stripe','rooms',
                        'booksCount','lowProfits','alarmsPayment','alarmsCheckPaxs','otasDisconect',
                        'alert_lowProfits','percentBenef','parteeToActive','lastBooksPayment',
                        'ff_pendientes','ff_mount','totalReserv','amountReserv','overbooking',
                        'urgentes','bookings_without_Cvc','schedule','lstExtrs','toDeliver','ota_errLogs')
		);
    }
    
    private function countNotPaidYet() {
      $today = Carbon::now();
      $roomsApto = Rooms::where('site_id',1)->get()->pluck('id');
      $lstBook =  Book::whereIn('room_id',$roomsApto)
              ->where('start', '>=', $today->copy()->subDays(3))
              ->where('start', '<=', $today->copy())
              ->whereIn('type_book',[1,2])->get();
      
      if ($lstBook){
        foreach ($lstBook as $book){
          if($book->pending > 1){
            return true;
          }
        }
      }
      
      return null;
    }
    
    private function countPartte() {
      $today = Carbon::now();
      return Book::Join('book_partees','book.id','=','book_id')
              ->where('start', '>=', $today->copy()->subDays(2))
              ->where('start', '<=', $today->copy())
              ->where('book_partees.status', '!=', 'FINALIZADO')
              ->where('type_book', 2)->orderBy('start', 'ASC')->count();
    }

    private function lowProfitAlert($startYear, $endYear, $percentBenef, &$alert)
    {

        $booksAlarms = Book::where('start', '>', $startYear)->where('finish', '<', $endYear)
                                ->whereIn('type_book', [
                                    2,
                                    7,
                                    8
                                ])->with('room','customer','payments')->orderBy('start', 'ASC')->get();
        
        $alarms = array();

        foreach ($booksAlarms as $key => $book)
        {
            $inc_percent = $book->get_inc_percent();
            if (round($inc_percent) <= $percentBenef)
            {
                if (!$book->has_low_profit)
                {
                    $alert++;
                }
                $alarms[] = $book;
            }
        }
        return $alarms;
    }

     public function newBook(Request $request){
        if (Auth::user()->role != "agente")
        {
            $rooms = \App\Rooms::where('state', '=', 1)->orderBy('order')->get();
        } else
        {
            $roomsAgents = \App\AgentsRooms::where('user_id', Auth::user()->id)->get(['room_id'])->toArray();
            $rooms       = \App\Rooms::where('state', '=', 1)->whereIn('id', $roomsAgents)->orderBy('order')->get();
        }
        
        $data = [
             'name'      => '',
             'email'     => '',
             'pax'       => '',
             'phone'     => "",
             'book_comments'   => "",
             'date'      => '',
             'start'     => '',
             'finish'    => '',
             'nigths'    => '',
             'newRoomID' => '',
           ];
         
        $form_data = $request->input('form_data',null);
        $info = $request->input('info',null);
        if ($form_data){
          foreach ($form_data as $k=>$v){
            $data[$v['name']] = $v['value'];
          }
          $data['pax'] =$data['quantity'];
          $data['nigths'] = calcNights($data['start'],$data['finish']);
        }
        if ($info){
          $info = unserialize($info);
          $data['pvp_promo']= $info['pvp_promo'];
          $data['disc_pvp'] = $info['pvp_discount'];
          $roomCode = trim($info['sugID']);
          $data['newRoomID'] = desencriptID($roomCode);
      
          
        }
        
        return view('backend/planning/_nueva', compact('rooms','data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $date_start  = $request->input('start',null);
        $date_finish  = $request->input('finish',null);
        //createacion del cliente
        $customer          = new \App\Customers();
        $customer->name    = $request->input('name');
        $customer->email   = $request->input('email',' ');
        $customer->phone   = ($request->input('phone')) ? $request->input('phone') : "";
        $customer->DNI     = ($request->input('dni')) ? $request->input('dni') : "";
        $customer->address = ($request->input('address')) ? $request->input('address') : "";
        $customer->country = ($request->input('country')) ? $request->input('country') : "";
        $customer->city    = ($request->input('city')) ? $request->input('city') : "";
        $customer->province    = ($request->input('province')) ? $request->input('province') : "";
             
        //Creacion de la reserva
        $book   = new \App\Book();
        $book->room_id       = $request->input('newroom');
        $book->start         = $date_start;
        $book->finish        = $date_finish;
        $book->comment       = ($request->input('comments')) ? ltrim($request->input('comments')) : "";
        $book->book_comments = ($request->input('book_comments')) ? ltrim($request->input('book_comments')) : "";
        $book->book_owned_comments = ($request->input('book_owned_comments')) ? $request->input('book_owned_comments') : "";
        $book->type_book     = ($request->input('status')) ? $request->input('status') : 3;
        $book->pax           = $request->input('pax',0);
        $book->real_pax      = $request->input('pax',0);
        $book->nigths        = calcNights($date_start,$date_finish); //$request->input('nigths',0);
        $book->agency        = $request->input('agency',0);
        $book->PVPAgencia    = ($request->input('agencia')) ? $request->input('agencia') : 0;
        $book->is_fastpayment = ($book->type_book == 99 ) ? 1:0;
                
        $room = \App\Rooms::find($request->input('newroom'));
        $totales = $book->getPriceBook($date_start,$date_finish,$room->id);
        if (!$room) die('error');
        
        $book->cost_apto  = $totales['cost'];
        $book->extraPrice = $totales['extra_fixed'];
        $book->extraCost  = $totales['cost_extra_fixed'];
        $book->sup_limp   = $totales['limp'];
        $book->cost_limp  = $totales['cost_limp'];
        $book->cost_total = $book->get_costeTotal();
        $book->real_price  = $totales['pvp'] + $book->sup_limp + $book->extraPrice;
        $isReservable = 0;
            
      if (in_array($book->type_book, $book->get_type_book_reserved())){
          if ($book->availDate($date_start, $date_finish, $request->input('newroom')))
          {
              $isReservable = 1;
          }
      } else { $isReservable = 1; }

      if ($isReservable == 1){

          //createacion del cliente
          $customer->user_id = (Auth::check()) ? Auth::user()->id : 23;
          if ($customer->save())
          {

            $book->user_id             = $customer->user_id;
            $book->customer_id         = $customer->id;

            if ($book->type_book == 8){
                  $book->sup_limp    = 0;
                  $book->cost_limp   = 0;
                  $book->sup_park    = 0;
                  $book->cost_park   = 0;
                  $book->sup_lujo    = 0;
                  $book->cost_lujo   = 0;
                  $book->cost_apto   = 0;
                  $book->cost_total  = 0;
                  $book->total_price = 0;
                  $book->real_price  = 0;
                  $book->total_ben   = 0;
                  $book->inc_percent = 0;

              }elseif ($book->type_book == 7){
                   $book->bookingProp($room);
              }else{
                $book->total_price = $request->input('total');
                if ($request->input('costApto')) $book->cost_apto = $request->input('costApto');
                if ($request->input('cost_total')) $book->cost_apto = $request->input('cost_total');
                if (isset($request->priceDiscount) && $request->input('priceDiscount') == "yes"){
                  $discount = \App\Settings::getKeyValue('discount_books');
                  $book->ff_status = 4;
                  $book->ff_discount = $discount;
                  $book->has_ff_discount = 1;
                  $book->real_price  -=  $request->input('discount');
                }

                $book->total_ben = intval($book->total_price) - intval($book->cost_total);
                $book->inc_percent = round($book->get_inc_percent());
              }

              $book->schedule    = ($request->input('schedule')) ? $request->input('schedule') : 0;
              $book->scheduleOut = ($request->input('scheduleOut')) ? $request->input('scheduleOut') : 12;
              $book->promociones = ($request->input('promociones')) ? $request->input('promociones') : 0;

              if ($book->save())
              {

                $book->sendAvailibilityBy_dates($book->start,$book->finish);
                  /* Creamos las notificaciones de booking */
                  /* Comprobamos que la room de la reserva este cedida a booking.com */
                  if ($room->isAssingToBooking())
                  {
                      $notification          = new \App\BookNotification();
                      $notification->book_id = $book->id;
                      $notification->save();
                  }
                  $customerRequest = $request->input('cr_id',null);
                  if ($customerRequest){
                    $oCustomerRequest = \App\CustomersRequest::find($customerRequest);
                    if ($oCustomerRequest){
                      $oCustomerRequest->book_id = $book->id;
                      $oCustomerRequest->status = 2;
                      $oCustomerRequest->save();
                    }
                  }
                  // MailController::sendEmailBookSuccess( $book, 0);
                  return redirect(route('book.update',$book->id));

              }
          }

      } else {
        if ( Auth::user()->role != "agente" ){
          return view('backend/planning/_formBook', [
              'request' => (object) $request->input(),
              'book'    => new \App\Book(),
              'rooms'   => \App\Rooms::where('state', '=', 1)->get(),
              'mobile'  => config('app.is_mobile')
          ]);
        } else {
          return redirect('admin/reservas')->withErrors(['Error: El apartamento ya tiene una reserva confirmada']);
        }
      }


    }

    public function update(Request $request, $id)
    {
      
      $updateBlade = '';
      $hasVisa = false;
      $visaHtml = null;
      $partee = null;
      $oUser = Auth::user();
      if ( $oUser->role != "agente"){
         
          $book  = Book::with('payments')->find($id);
          if (!$book){
            return redirect('/admin/reservas');
          }
          $rooms = \App\Rooms::orderBy('order')->get();
          if ( $oUser->role == "admin" || $oUser->role == "subadmin"){
          $oVisa = DB::table('book_visa')
                    ->where('book_id',$book->id)
                    ->first();
            if ($oVisa){
              $hasVisa = true;
              $visaData = json_decode($oVisa->visa_data,true);
              $fieldsCard = ["name",'date','type','expiration_date'];
              $fieldsName = ["name"=>'Nombre',"number"=>'Nro tarj','date'=>'Vto','expiration_date'=>'Vto',"cvc"=>'CVC','type'=>'Tipo'];
              foreach ($fieldsCard as $f){
                if (isset($visaData[$f])){
                  if ($f == 'date' || $f == 'expiration_date') $visaData[$f] = str_replace ('/20', ' / ', $visaData[$f]);
                  $visaHtml .= '
                    <div>
                    <label>'.$fieldsName[$f].'</label>
                    <input type="text" class="form-control" value="'.$visaData[$f].'" >
                    <button class="btn btn-success copy_data" type="button"><i class="fa fa-copy"></i></button>
                    </div>';
                }
              }
              $visaHtml .= '
                <div>
                <label>Nro tarj</label>
                <input type="text" class="form-control cc_upd" value="'.$oVisa->cc_number.'"  id="cc_number">
                <button class="btn btn-success copy_data" type="button"><i class="fa fa-copy"></i></button>
                </div>                
                <div>
                <label>CVC</label>
                <input type="text" class="form-control cc_upd" value="'.$oVisa->cvc.'"  id="cc_cvc">
                <button class="btn btn-success copy_data" type="button"><i class="fa fa-copy"></i></button>
                <a href="https://online.bnovo.ru/dashboard?q='.$book->external_id.'" class="btn btn-bnovo" target="_black"></a>
                </div>';  
              $visaHtml .=  '<div class="btn btn-blue" type="button" id="_getPaymentVisaForce">Refrescar datos</div>';
            }
            
          }
          
        if($book->type_book == 2 || $book->type_book == 1 ){
          $partee = $book->partee();
          if (!$partee){
            $partee = new \App\BookPartee();
          }  
        }
          
          
       } else {
         
          $updateBlade = '-agente';
          $roomsAgents = \App\AgentsRooms::where('user_id', $oUser->id)->get(['room_id'])->toArray();
          $rooms       = \App\Rooms::whereIn('id', $roomsAgents)->orderBy('order')->get();
          $types       = [1,2];
          
          $book = Book::with('payments')
                  ->whereIn('type_book',$types)
                  ->whereIn('room_id', $roomsAgents)
                  ->find($id);
       }

       if (!$book){
         return redirect('/admin/reservas');
       }
        $totalpayment = $book->sum_payments;
        $payment_pend = floatVal($book->total_price)-$totalpayment;
        // We are passing wrong data from this to view by using $book data, in order to correct data
        // an AJAX call has been made after rendering the page.
        $hasFiance = \App\Fianzas::where('book_id', $book->id)->first();

        /**
         * Check low_profit alert
         */
        $low_profit   = false;
        $inc_percent  = $book->get_inc_percent();
        $percentBenef = DB::table('percent')->find(1)->percent;

        if (round($inc_percent) <= $percentBenef)
        {
            if (!$book->has_low_profit)
            {
                $low_profit = true;
            }
        }

        //END: Check low_profit alert
        $paymentPercent = $book->getPaymentPercent();
        $extras = ExtraPrices::getDynamic();
        $extrasAsig =  BookExtraPrices::getDynamic($book->id);
        
        $totalExtras = 0;
        foreach ($extrasAsig as $e){
          $totalExtras += $e->price;
        }
        
        $minStay = $book->getMinStay();
        
        $priceBook = $book->getMetaContent('price_detail');
        
        if ($priceBook){
          $priceBook = unserialize($priceBook);
        } else {
          $priceBook = $book->room->getRoomPrice($book->start, $book->finish, $book->park);
        }
        
        $oBookData = \App\BookData::findOrCreate('creditCard',$book->id);
        $creditCardData = $oBookData->content;
        
        // SUPLEMENTOS ----------------------------------------------
        
        $linksExtr = \App\Services\Bookings\ExtrasPurchase::getLink($book);
       // $textSupl = $this->getMailData($book, 'book_email_supplements');
//        $textSupl = $this->clearVars($textSupl);
$textSupl = '';
        // ----------------------------------------------
        $site = \App\Sites::siteData($book->room->site_id);
                
        /*************************************/
        $cliHasPhotos = \App\BookData::findOrCreate('client_has_photos',$book->id);
        if ($cliHasPhotos)  $cliHasPhotos = $cliHasPhotos->content;
        $cliHasBed = \App\BookData::findOrCreate('client_has_beds',$book->id);
        if ($cliHasBed)  $cliHasBed = $cliHasBed->content;
        $cliHasBabyCarriage = \App\BookData::findOrCreate('client_has_babyCarriage',$book->id);
        if ($cliHasBabyCarriage)  $cliHasBabyCarriage = $cliHasBabyCarriage->content;
        
        /*************************************/
        $secondPayAlert = \App\BookLogs::where('book_id',$book->id)->where('action','second_payment_reminder')->orderBy('created_at','DESC')->first();
        if ($secondPayAlert) $secondPayAlert = convertDateTimeToShow_text($secondPayAlert->created_at);
        /*************************************/
        
        return view('backend/planning/update'.$updateBlade, [
            'book'         => $book,
            'low_profit'   => $low_profit,
            'hasVisa'      => $hasVisa,
            'visaHtml'     => $visaHtml,
            'rooms'        => $rooms,
            'extras'       => $extras,
            'extrasAsig'   => $extrasAsig,
            'totalExtras'   => $totalExtras,
            'start'        => Carbon::createFromFormat('Y-m-d', $book->start)->format('d M,y'),
            'totalpayment' => $totalpayment,
            'payment_pend' => $payment_pend,
            'mobile'       => config('app.is_mobile'),
            'hasFiance'    => $hasFiance,
            'minStay'      => $minStay,
            'priceBook'    => $priceBook,
            'paymentPercent' => $paymentPercent,
            'partee' => $partee,
            'creditCardData' => $creditCardData,
            'linksExtr' => $linksExtr,
            'textSupl' => $textSupl,
            'cliHasPhotos' => $cliHasPhotos,
            'cliHasBed' => $cliHasBed,
            'cliHasBabyCarriage' => $cliHasBabyCarriage,
            'site' => $site,
            'secondPayAlert' => $secondPayAlert,
        ]);
    }
    //Funcion para actualizar la reserva
  public function saveUpdate(Request $request, $id) {

    $IS_agente = false;
    if (Auth::user()->role != "agente") {
      $book = Book::find($id);
    } else {
      return [
          'status' => 'warning',
          'title' => 'Cuidado',
          'response' => "No puedes hacer cambios en la reserva"
      ];
    }

    if (!$book) {
      return [
          'status' => 'danger',
          'title' => 'ERROR',
          'response' => "RESERVA NO ENCONTRADA"
      ];
      return redirect('404');
    }

    $computedData = json_decode($request->input('computed_data'));

    $start = $request->input('start');
    $finish = $request->input('finish');
    $customer = \App\Customers::find($request->input('customer_id'));
    $customer->DNI = ($request->input('dni')) ? $request->input('dni') : "";
    $customer->address = ($request->input('address')) ? $request->input('address') : "";
    $customer->country = ($request->input('country')) ? $request->input('country') : "";
    $customer->province = ($request->input('province')) ? $request->input('province') : 28;
    $customer->save();


    if (!in_array($book->type_book, $book->typeBooksReserv) || Book::availDate($start, $finish, $request->input('newroom'), $book->id)) {

      $OldRoom = $book->room_id;
      $oldStart = $book->start;
      $oldFinish = $book->finish;
      $room = Rooms::find($request->input('newroom'));

      $user = Auth::user();
//      $book->user_id = Auth::user()->id;
      \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'bookUpd','Actualización',"IP ".getUserIpAddr().'<br>'.json_encode($book));
      $book->customer_id = $request->input('customer_id');
      $book->room_id = $request->input('newroom');

      $book->start = $start;
      $book->finish = $finish;
      $book->comment = ltrim($request->input('comments'));
      $book->book_comments = ltrim($request->input('book_comments'));
      $book->pax = $request->input('pax');
      $book->real_pax = $request->input('real_pax');
      $book->nigths =  calcNights($start,$finish); //$request->input('nigths');
      if (!$IS_agente) {
        $book->book_owned_comments = ($request->input('book_owned_comments')) ? $request->input('book_owned_comments') : "";
      }

      if ($book->type_book == 7) {
        $book->sup_limp = ($room->sizeApto == 1) ? 30 : 50;
        $book->cost_limp = ($room->sizeApto == 1) ? 30 : 40;
        $book->real_price = ($room->sizeApto == 1) ? 30 : 50;
      } else {
        if ($computedData) {
          $book->sup_limp = $computedData->totales->limp;
          $book->cost_limp = $computedData->costes->limp;
          $book->extraCost = $computedData->costes->extra_fixed;
          $book->extraPrice = $computedData->totales->extra_fixed;
          $book->real_price = $computedData->calculated->real_price;
        }
      }
    if ($book->type_book !== 8){  
      $book->agency = $request->input('agency');
      $book->PVPAgencia = $request->input('agencia') ?: 0;
      $book->total_price = $request->input('total_pvp'); // This can be modified in frontend
      if ($request->input('costApto')) $book->cost_apto = $request->input('costApto');
      if ($request->input('cost')) $book->cost_total = $request->input('cost');
      if ($request->input('costExtra')) $book->extraCost = $request->input('costExtra');
//      if ($request->input('beneficio'))  $book->total_ben = $request->input('beneficio');
      else { //A subadmin has change the PVP
        if ($book->cost_total > 0) {
          $profit = $book->total_price - $book->cost_total;
          $book->total_ben = $profit;
        }
      }

      if (!$IS_agente) {
       
        $book->schedule = $request->input('schedule');
        $book->scheduleOut = $request->input('scheduleOut');
        $book->promociones = ($request->input('promociones')) ? $request->input('promociones') : 0;

        $book->has_ff_discount = $request->input('has_ff_discount', 0);
        if (!$book->has_ff_discount && $book->ff_status == 4) {
          $book->ff_status = 0;
        } else {
          if ($book->has_ff_discount && $book->ff_status == 0) {
            $book->ff_status = 4;
          }
        }
        $book->ff_discount = $request->input('ff_discount', 0);
        if ($computedData) {
          $book->real_price = $computedData->calculated->real_price; // This cannot be modified in frontend
          $book->inc_percent = $computedData->calculated->profit_percentage; // This cannot be modified in frontend
          
        }
      }
    }
    if ($book->type_book == 8){
        $book->sup_limp    = 0;
        $book->cost_limp   = 0;
        $book->sup_park    = 0;
        $book->cost_park   = 0;
        $book->sup_lujo    = 0;
        $book->cost_lujo   = 0;
        $book->cost_apto   = 0;
        $book->cost_total  = 0;
        $book->total_price = 0;
        $book->real_price  = 0;
        $book->total_ben   = 0;
    }
      if ($book->save()) {
        //si esta reservada, cambio la disponibilidad
        if (in_array($book->type_book, $book->typeBooksReserv)) {

          $auxStart = $book->start;
          $auxFinish = $book->finish;
          if ($oldStart != $auxStart || $oldFinish != $auxFinish) {
            if ($oldStart < $auxStart)
              $date1 = $oldStart;
            else
              $date1 = $auxStart;
            if ($oldFinish > $auxFinish)
              $date2 = $oldFinish;
            else
              $date2 = $auxFinish;

            if ($OldRoom != $book->room_id) {
              $book->sendAvailibilityBy_Rooms($OldRoom, $date1, $date2);
            } else {
              $book->sendAvailibilityBy_dates($date1, $date2);
            }
          } else {
            if ($OldRoom != $book->room_id) {
              $book->sendAvailibilityBy_Rooms($OldRoom);
            }
          }
        }


        if ($book->room->isAssingToBooking()) {

          $isAssigned = \App\BookNotification::where('book_id', $book->id)->get();

          if (count($isAssigned) == 0) {
            $notification = new \App\BookNotification();
            $notification->book_id = $book->id;
            $notification->save();
          }
        } else {
          $deleted = \App\BookNotification::where('book_id', $book->id)->delete();
        }

        return [
            'status' => 'success',
            'title' => 'OK',
            'response' => "ACTUALIZACION CORRECTA"
        ];
      }
    } else {

      return [
          'status' => 'danger',
          'title' => 'ERROR',
          'response' => "NO HAY DISPONIBILIDAD EN EL PISO PARA LAS FECHAS SELECCIONADAS"
      ];
    }
  }

  public function changeBook(Request $request, $id){
    
    if (isset($request->room) && !empty($request->room)){

          $book = \App\Book::find($id);
          $oldRoom = $book->room_id;
          $isReservable = 0;
          if (in_array($book->type_book, $book->get_type_book_reserved())){
            if (book::availDate($book->start, $book->finish, $request->room,$book->id)){
               $isReservable = 1;
            } else return ['status' => 'danger', 'title' => 'Error', 'response' => 'Habitación No disponible','changed'=>false];
          } else  $isReservable = 1;

          if ($isReservable){
            $book->room_id = $request->room;
            $book->save();
            $book->sendAvailibilityBy_Rooms($oldRoom);
            return ['status' => 'success', 'title' => 'OK', 'response' => 'Habitación modificada','changed'=>false];
          }

          return ['status' => 'danger', 'title' => 'Error', 'response' => 'Habitación no modificada','changed'=>false];

      }

      if (isset($request->status) && !empty($request->status)){
        
          $book = Book::find($id);
          $oldStatus = $book->type_book;
          $response = $book->changeBook($request->status, "", $book);
          if ($response['changed']){
              $book->sendAvailibilityBy_status();
          }
          return $response;

      } else {
        
          return [
              'status'   => 'danger',
              'title'    => 'Error',
              'response' => 'No hay datos para cambiar, por favor intentalo de nuevo'
          ];
      }
  }

   

    //Funcion para Cobrar desde movil
    public function cobroBook($id)
    {
        $book     = Book::find($id);
        $payments = \App\Payments::where('book_id', $id)->get();
        $pending  = 0;

        foreach ($payments as $payment)
        {
            $pending += $payment->import;
        }

        return view('backend/planning/_updateCobro', [
            'book'     => $book,
            'pending'  => $pending,
            'payments' => $payments,
        ]);
    }

    // Funcion para Cobrar
    public function saveCobro(Request $request)
    {
        $payment = new \App\Payments();

        $payment->book_id     = $request->id;
        $payment->datePayment = Carbon::CreateFromFormat('d-m-Y', $request->fecha);
        $payment->import      = $request->import;
        $payment->type        = $request->tipo;

        if ($payment->save())
        {
            return redirect()->action('BookController@index');
        }

    }

    // Funcion para elminar cobro
    public function deleteCobro($id)
    {
        $payment = \App\Payments::find($id);

        if ($payment->delete())
        {
          \App\Incomes::deleteFromBook($id);
            return redirect()->back();
        }

    }

    //Funcion para gguardar Fianza
    public function saveFianza(Request $request)
    {
        $fianza = new \App\Bail();

        $fianza->id_book    = $request->id;
        $fianza->date_in    = Carbon::CreateFromFormat('d-m-Y', $request->fecha);
        $fianza->import_in  = $request->fianza;
        $fianza->comment_in = $request->comentario;
        $fianza->type       = $request->tipo;

        if ($fianza->save())
        {
            return redirect()->action('BookController@index');
        }

    }

    public function sendEmail(Request $request)
    {

        $book = Book::find($request->input('id'));
        if ($book){
          if (!$book->customer->email || trim($book->customer->email) == '') return 'El cliente no posee email';
          $this->sendEmail_ContestadoAdvanced($book,$request->input('textEmail'));
          \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'sendEmailDisp','Disponibilidad para tu reserva',$request->input('textEmail'));
          // $book->send = 1;
          $book->type_book = 5;
          if ($book->save()){
            return 'OK';
          } else  {
            return 'error al actualizar el estado de la reserva';
          }
        }
        return 'Reserva no encontrada';

    }

    public function ansbyemail($id)
    {
      $book = Book::find($id);
      
      $mailClientContent = $this->getMailData($book, 'reservation_state_mail_response');

      return view('backend/planning/_answerdByEmail', [
          'book'   => $book,
          'mailContent'   => $mailClientContent,
          'mobile' => config('app.is_mobile')
      ]);
    }

    
  public function delete($id) {

    try {
      $book = Book::find($id);
      if (count($book->pago) > 0) {
        $total = 0;
        foreach ($book->pago as $index => $pago){
          $total += $pago->import;
        }
        if ($total>0){
          return [
              'status' => 'danger',
              'title' => 'Error:',
              'response' => "La Reserva posee cargos asociados."
          ];
        }
      }
      foreach ($book->notifications as $key => $notification) {
        $notification->delete();
      }

      if ($book->type_book == 7 || $book->type_book == 8) {
        $expenseLimp = \App\Expenses::where('date', $book->finish)
                ->where('concept', "LIMPIEZA RESERVA PROPIETARIO. " . $book->room->nameRoom);

        if ($expenseLimp->count() > 0) {
          $expenseLimp->first()->delete();
        }
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

  public function searchByName(Request $request)
    {
        if ($request->searchString == '')
        {
            return response()->json('', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $year     = $this->getActiveYear();
        $dateFrom = $year->start_date;
        $dateTo   = $year->end_date;

        $aCustomer = explode(' ', $request->searchString);
        $customerIds = [];
        if (is_array($aCustomer)){
          $sql = \App\Customers::whereNotNull('id');
          foreach ($aCustomer as $name)
            $sql->where('name', 'LIKE', '%' . $name . '%');
          
          $customerIds = $sql->pluck('id')->toArray();
        }

        if (count($customerIds) <= 0)
        {
          $otaID = $request->searchString;
          
            $books = Book::with('payments')
                ->where('external_id', $otaID)
                ->orWhere('bkg_number', $otaID)
                ->orderBy('start', 'ASC')->get();
            if (count($books)==0){
              return "<h2>No hay reservas para este término '" . $request->searchString . "'</h2>";
            }
          
        } else {
          $books = Book::with('payments')
                ->whereIn('customer_id', $customerIds)
                ->where('start', '>=', $dateFrom)
                ->where('start', '<=', $dateTo)
                ->where('type_book', '!=', 9)->where('type_book', '!=', 0)
                ->orderBy('start', 'ASC')->get();
        }

        $payments = [];
        $paymentStatus = array();
        foreach ($books as $book)
        {
          $amount = $book->payments->pluck('import')->sum();
          $payments[$book->id] = $amount;
          if ($amount>=$book->total_price) $paymentStatus[$book->id] = 'paid';
           else {
             if ($amount>=($book->total_price/2)) $paymentStatus[$book->id] = 'medium-paid';
           }
        }
        
        
        return view('backend/planning/listados/_resultSearch', [
            'books'   => $books,
            'payment' => $payments,
            'paymentStatus'=>$paymentStatus
        ]);

    }

  public function changeCostes() {
    $books = Book::all();
    foreach ($books as $book) {
      if ($book->room->typeAptos->id == 1 || $book->room->typeAptos->id == 3) {

        $book->cost_total = $book->get_costeTotal();
        $book->total_ben = $book->total_price - $book->cost_total;
        $book->cost_apto = 0;
      }
      $book->ben_jorge = $book->total_ben * $book->room->typeAptos->PercentJorge / 100;
      $book->ben_jaime = $book->total_ben * $book->room->typeAptos->PercentJaime / 100;

      $book->save();
    }
  }

    public function getTableData(Request $request) {
    $isMobile = config('app.is_mobile');
    $uRole = getUsrRole();
    $year = self::getActiveYear();
    $startYear = $year->start_date;
    $endYear = $year->end_date;
    $books = $pullSent = [];
    $cliHas    = [];

    if ($uRole == "limpieza") {
      if (!($request->type == 'checkin' || $request->type == 'checkout' || $request->type == 'blocks')) {
        $request->type = 'checkin';
      }
    }
    
    $booksQuery = Book::where_book_times($startYear, $endYear)
            ->with('room','payments','customer','extrasBook','leads');

    $bg_color = '#6d5cae';
    switch ($request->type) {
      case 'pendientes':
        if ($uRole != "agente") {
          $types = Book::get_type_book_pending();
          unset($types[array_search(4,$types)]);
          $books = $booksQuery->whereIn('type_book', $types)
                  ->orderBy('created_at', 'DESC')->get();
        } else {
          $roomsAgents = \App\AgentsRooms::where('user_id', Auth::user()->id)->get(['room_id'])->toArray();
          $books = $booksQuery->where('type_book', 1)
                  ->where('user_id', Auth::user()->id)
                  ->whereIn('room_id', $roomsAgents)
                  ->orWhere('agency', Auth::user()->agent->agency_id)
                  ->with('room','payments')
                  ->orderBy('created_at', 'DESC')->get();
        }
        $bg_color = '#295d9b';
        break;
      case 'reservadas':
        $books = $booksQuery->where('type_book', 1)
                  ->orderBy('created_at', 'DESC')->get();
        $bg_color = '#53ca57';
        break;
      case 'blocks':
        $books = $booksQuery->where('type_book', 4)
                  ->orderBy('created_at', 'DESC')->get();
        $bg_color = '#448eff';
        break;
      case 'especiales':
        $books = $booksQuery->whereIn('type_book', [7,8])->orderBy('created_at', 'DESC')->get();
        $bg_color = 'orange';
        break;
      case 'confirmadas':

        if ($uRole != "agente") {
          $books = $booksQuery->with('LogImages')->whereIn('type_book', [2])->orderBy('created_at', 'DESC')->get();
        } else {
          $books = $booksQuery->where('type_book', 2)->whereIn('room_id', $roomsAgents)
                          ->where('user_id', Auth::user()->id)
                          ->orWhere('agency', Auth::user()->agent->agency_id)
                          ->orderBy('created_at', 'DESC')->get();
        }
        $bg_color = 'green';
        break;
      case 'ff_pdtes_2':
        $dateX = Carbon::now();
        $books = Book::where('ff_status', 4)->where('type_book', '>', 0)
                        ->where('start', '>=', $dateX->copy()->subDays(3))
                        ->with('room','payments','customer','extrasBook')
                        ->orderBy('start', 'ASC')->get();
        break;
      case 'checkin':
      case 'ff_pdtes':
        
                    
        $dateX = Carbon::now();
        // agregamos las especiales 7, 8
        $booksQuery = \App\Book::where('start', '>=', $dateX->copy()->subDays(3))
                        ->where('start', '<=', $year->end_date)
                        ->with('room','payments','customer','extrasBook')
                        ->whereIn('type_book', [1, 2, 7, 8])->orderBy('start', 'ASC');
        $books = $booksQuery->get();
        $cliHas = Book::cliHas_lst($booksQuery->pluck('id'));
        $bg_color = '#10cfbd';
        break;
      case 'checkout':
        $dateX = Carbon::now();
        $books = Book::where('finish', '>=', date('Y-m-d'))
                ->where('finish', '<', $year->end_date)->whereIn('type_book',[1,2,7,8,10])
                ->with('room','payments','customer','extrasBook','leads')
                ->orderBy('finish', 'ASC')->get();
        
         if ($books){
          $bList = [];
          foreach ($books as $book){
            $bList[] = $book->id;
          }

          if (count($bList) > 0){
            $pullSent = \App\BookData::whereIn('book_id',$bList)
              ->where('key','sent_poll')->pluck('book_id')->toArray();
          }
         }
      
       
        break;
      case 'eliminadas':
        $books = Book::where_book_times($startYear, $endYear)
              ->where('type_book', 0)
              ->with('room','payments','customer','extrasBook','leads')
              ->orderBy('updated_at', 'DESC')->get();
        $bg_color = '#f55753';
        break;
      case 'cancel-xml':
        $books = Book::where_book_times($startYear, $endYear)
              ->where('type_book', 98)
              ->with('room','payments','customer','extrasBook')
              ->orderBy('updated_at', 'DESC')->get();
        $bg_color = '#f55753';
        break;
      case 'blocked-ical':
        $dateX = Carbon::now();
        $books = Book::where('start', '>=', $dateX->copy()->subDays(3))
              ->where('finish', '<=', $endYear)
               ->with('room','payments','customer','extrasBook')
              ->whereIn('type_book', [11,12])->orderBy('updated_at', 'DESC')->get();
        $bg_color = '#e8afe7';
        break;
      case 'overbooking':
         //BEGIN: Processed data
        $bookOverbooking = null;
        $overbooking = [];
        $oData = \App\ProcessedData::where('key','overbooking')->first();
        if ($oData) $overbooking = json_decode($oData->content,true);
        $books = Book::whereIn('id',$overbooking)->orderBy('updated_at', 'DESC')->get();
        $bg_color = '#f55753';
        break;
    }


    $type = $request->type;

      $payment = array();
      $paymentStatus = array();
      foreach ($books as $book){
        $amount = $book->payments->pluck('import')->sum();
        $payment[$book->id] = $amount;
        if ($amount>=$book->total_price) $paymentStatus[$book->id] = 'paid';
         else {
           if ($amount>=($book->total_price/2)) $paymentStatus[$book->id] = 'medium-paid';
         }
      }
      /////////////////// 
      //filter by site
      $sites = \App\Sites::all();
      $bSite = \App\Sites::bSite();
      foreach ($books as $v){
        $sID = $v->room->site_id;
        $bSite[$sID][1]++;
      }
      foreach ($sites as $v){
        $bSite[$v->id][0] = $v->name;
      }
      $enableSite = \App\Sites::siteIDs();
      foreach ($bSite as $sID=>$data) 
        if (!in_array($sID, $enableSite))
                unset($bSite[$sID]);
      
      //filter by site
      /////////////////// 
    $buffer = ob_html_compress(view('backend/planning/listados/_table', compact('books', 'type','uRole', 'isMobile', 'payment','paymentStatus','bSite','bg_color','pullSent','cliHas')));
    return $buffer;
    
  }

  /**
   * /reservas/api/lastsBooks/{type?}
   * 
   * @param type $type
   * @return type
   */
    public function getLastBooks($type=null){
              
      $mobile = new \App\Classes\Mobile();

      $year      = self::getActiveYear();
      $startYear = new Carbon($year->start_date);
      $endYear   = new Carbon($year->end_date);
      $bMonth = [];
      $bWeek = [];
      $total = 0;
      $cW = date('W');
      $cMonth = date('n');
      
      $alarmsPayment = [];
      $oData = \App\ProcessedData::where('key','alarmsPayment')->first();
      if (trim($oData->content) != ''){
        $alarmsPayment = json_decode($oData->content);
      }
      /*************************************************************************************/
      $uRole     = getUsrRole();
      if ($uRole == "limpieza" || $uRole == "agente"){
        die;
      }
      /*************************************************************************************/
      //buscar pagos (idBook) utima semana o mes
      $qry_lst = Book::whereIn('type_book', [1,2,11])->where('start','>=',$startYear);
      if ($type == 'pendientes'){
          if (count($alarmsPayment)>0){
            $qry_lst->whereIn('id',$alarmsPayment);
          } 
      }
      $toDay = new \DateTime();
      $toDayEnd = date('Y-m-d');
      $daysToCheck = array();
      $payment_rule = \App\Settings::where('key', 'payment_rule')->get();
      foreach ($payment_rule as $r){
        $cont = json_decode($r->content);
        $daysToCheck[$r->site_id] = $cont->days;
      }
      
      $lst = $qry_lst->with('room','payments','customer','leads')
              ->orderBy('start','DESC')->get();

      $books = [];
      foreach ($lst as $book)
      {
        $aux = [
            'agency' => ($book->agency != 0) ? '/pages/'.strtolower($book->getAgency($book->agency)).'.png' : null,
            'id'=> $book->id,
            'name'=> $book->customer->name,
            'url'=> url ('/admin/reservas/update').'/'.$book->id,
            'room'=>substr($book->room->nameRoom,0,5),
            'startTime'=>strtotime($book->start),
            'start'=>convertDateToShow($book->start),
            'finish'=>convertDateToShow($book->finish),
            'pvp'=> moneda($book->total_price),
            'pvpVal'=> ($book->total_price),
            'status'=>'',
            'tbook'=>$book->type_book,
            'btn-send'=>($book->send == 1) ? 'btn-default' : 'btn-primary',
            'payment'=>0,
            'toPay'=>0,
            'percent'=>0,
            'week'=>'',
            'month'=>'',
            'datePay'=>'',
            'datePayTime'=>'',
            'lastUpd' => $book->updated_at
        ];
        $payments = \App\Payments::where('book_id', $book->id)->get();
        $paymentBook = 0;
        $countPays = 0;
        if ( count($payments) > 0){
          foreach ($payments as $key => $payment){
            $paymentBook += $payment->import;
            $countPays++;
            if ($aux['lastUpd'] < $payment->updated_at)  $aux['lastUpd'] = $payment->updated_at;
          }
        }
        
        $total += $paymentBook;
        $aux['payment'] = $paymentBook;
        $aux['toPay'] = $book->total_price-$paymentBook;
        $aux['percent'] = ($book->total_price>0) ? round(($paymentBook/$book->total_price)*100): 0;
        
        $aux['retrasado'] = false;
        //Pago retrasado
        if (!in_array($book->type_book, [98,7,8])){
          if ($toDayEnd >= $book->start) $aux['retrasado'] = true;
          else {
            $date2 = new \DateTime($book->start);
            $diff = $date2->diff($toDay);
            $dayLimit = isset($daysToCheck[$book->room->site_id]) ? $daysToCheck[$book->room->site_id] : 5;
            $aux['retrasado'] = ($diff->days<$dayLimit) ? true : false;
          }
        }

        
        if($countPays > 0){
            $aux['status'] = $countPays. 'º PAGO OK';
        } else {
          switch($book->type_book){
            case 1: $aux['status'] = 'RESERVADO'; break;
            case 2: $aux['status'] = 'PENDT COBRO'; break;
            case 7: $aux['status'] = 'PROPIETARIO'; break;
            case 8: $aux['status'] = 'ATIPICA'; break;
            case 98: $aux['status'] = 'CANCEL'; break;
            case 11: $aux['status'] = 'BLOCK-iCAL'; break;
          } 
        }
        $books[$book->id] = $aux;
      }

      
      return view('backend.planning._lastBookPayment', compact('books','bMonth','bWeek', 'mobile','total','type','alarmsPayment'));

    }

   
    function getAlertsBooking(Request $request)
    {
        return view('backend.planning._tableAlertBooking', compact('days', 'dateX', 'arrayMonths', 'mobile'));
    }

    public function getCalendarMobileView($month=null){
      return $this->getCalendarView($month,null);
    }
    public function getCalendarChannelView($chGr,$month=null){
      $roomIDs = Rooms::where('channel_group',$chGr)->pluck('id');
      return $this->getCalendarView($month,$roomIDs,false);
    }
    public function getCalendarSiteView($site_id,$month=null){
      $roomIDs = Rooms::where('site_id',$site_id)->pluck('id');
      return $this->getCalendarView($month,$roomIDs,false);
    }
    public function getCalendarView($month=null,$roomIDs=null,$showTotals=true)
    {

        $mes           = [];
        $arrayReservas = [];
        $arrayMonths   = [];
        $arrayDays     = [];
        $year          = $this->getActiveYear();
        $startYear     = new Carbon($year->start_date);
        $endYear       = new Carbon($year->end_date);

        $type_book_not = [0,3,6,12,98,99];
        $uRole = Auth::user()->role;
//        if ($uRole == "agente" || $uRole == "limpieza"){
//            $type_book_not[] = 8;
//        }
//        if (!$roomIDs){
//          $roomIDs = Rooms::whereNotIn('site_id',[2,4])->pluck('id');
//        }
        
        
        $isMobile = config('app.is_mobile');
        if (!$month){
          $month = strtotime($year->year.'-'.date('m').'-01');
          if (strtotime($startYear)>$month){
            $month = strtotime(($year->year+1).'-'.date('m').'-01');
          }
        }
        
        $totalSales = [0=>0];
        $allSites = \App\Sites::allSites();
        foreach ($allSites as $k=>$v) $totalSales[$k] = 0;
        $type_book_sales = [1,2,8,11];

        $type_book_sales = Book::get_type_book_sales(true,true);
                
        $currentM = date('n',$month);
        $startAux = new Carbon(date('Y-m-d', strtotime('-1 months',$month)));
        $endAux = new Carbon(date('Y-m-d', strtotime('+1 months',$month)));
        $startAux->firstOfMonth();
        $endAux->lastOfMonth();
        $sqlBook = Book::where_book_times($startAux,$endAux)
                ->select('book.*',DB::raw("book_data.content as 'creditCard'"),DB::raw("tphotos.content as 'client_has_photos'"))
                ->whereNotIn('type_book', $type_book_not)
                ->with('customer');
        
        if ($roomIDs){
          $sqlBook->whereIn('room_id', $roomIDs);
        }
        
        $sqlBook->leftJoin('book_data', function($join)
        {
            $join->on('book.id','=','book_data.book_id');
            $join->on('book_data.key','=',DB::raw("'creditCard'"));
        });

        $books = $sqlBook->leftJoin('book_data as tphotos', function($join)
                 {
                     $join->on('book.id','=','tphotos.book_id');
                     $join->on('tphotos.key','=',DB::raw("'client_has_photos'"));
                 })
        ->orderBy('start', 'ASC')->get();

         /****************************************/    
        $bookings_without_Cvc = \App\ProcessedData::findOrCreate('bookings_without_Cvc');
        $bookings_without_Cvc = json_decode($bookings_without_Cvc->content,true);
        if (!$bookings_without_Cvc || !is_array($bookings_without_Cvc)){
          $bookings_without_Cvc = [];
        }
        /****************************************/  
        
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");
        $uRole = Auth::user()->role;
        
        $allRoomsSite = Rooms::all()->pluck('site_id','id');
        $monthControl = date('n',$month);
        $eventDatas = [];
        foreach ($books as $book)
        {
          $start = strtotime($book->start);
          $finish = strtotime($book->finish);
          $salesMonth = 0;
          $pvpNight = ($book->nigths>0) ? $book->total_price / $book->nigths : $book->total_price;
          $aEvent = $this->calendarEvent($book,$uRole,$isMobile,$bookings_without_Cvc);
          $event = $aEvent['data'];
          $eventDatas[$aEvent['key']] = $aEvent['titulo'];

          while($start<$finish){
            $arrayReservas[$book->room_id][date('Y',$start)][date('n',$start)][date('j',$start)][] = $event;
            if (date('n',$start) ==  $currentM) $salesMonth += $pvpNight;
            $start = strtotime("+1 day", $start);
          }
          $arrayReservas[$book->room_id][date('Y',$start)][date('n',$start)][date('j',$start)][] = $event;
          //if (date('n',$start) ==  $currentM) $salesMonth += $pvpNight;
          if ($showTotals){
            if (!$book->nigths || $book->nigths == 0) $salesMonth = $pvpNight;
            if (in_array($book->type_book,$type_book_sales)){
              if (isset($allRoomsSite[$book->room_id])){
                $site_id = $allRoomsSite[$book->room_id];
                if (!isset($totalSales[$site_id])) $totalSales[$site_id] = 0;
                $totalSales[$site_id] += $salesMonth;
                $totalSales[0] += $salesMonth;
              }
            }
          }
          
        }
        if ($showTotals){
          //just to jlargo
          $usrAdmin = Auth::user()->email;
          if( $usrAdmin != "jlargo@mksport.es" && $usrAdmin != "info@eysed.es") $totalSales = null;
          else {  
            $aux_total = $totalSales[0];
            foreach ($totalSales as $k=>$v){
              if ($k == 0) $totalSales[0] = 'Total: '.moneda($v);
              else{
                if (isset($allSites[$k])) $totalSales[$k] = $allSites[$k].': '.moneda ($v);
                else $totalSales[$k] = 'Otros: '.moneda($v);
                if ($aux_total>0){
                  $auxPerc = round(($v/$aux_total)*100);
                  $totalSales[$k] .= ' <span class="perc">'.$auxPerc.'%<span>';
                }
              }
            }
          }
        } else {
          $totalSales = null;
        }
        $firstDayOfTheYear = $startAux->copy();
        for ($i = 1; $i < 4; $i++)
        {

            $mes[$firstDayOfTheYear->copy()->format('n')] = $firstDayOfTheYear->copy()->format('M Y');

            $startMonth = $firstDayOfTheYear->copy()->startOfMonth();
            $day        = $startMonth;

            $arrayMonths[$firstDayOfTheYear->copy()->format('n')] = $day->copy()->format('t');

            for ($j = 1; $j <= $day->copy()->format('t'); $j++)
            {

                $arrayDays[$firstDayOfTheYear->copy()->format('n')][$j] = Book::getDayWeek($day->copy()
                                                                                                    ->format('w'));
                $day                                                    = $day->copy()->addDay();

            }

            $firstDayOfTheYear->addMonth();

        }

        //unset($arrayMonths[6]);
        //unset($arrayMonths[7]);
        //unset($arrayMonths[8]);

        if (Auth::user()->role != "agente")
        {
            $rooms         = \App\Rooms::where('state', '=', 1)->get();
            $sqlRooms = \App\Rooms::where('state', '=', 1);
        } else
        {
            $roomsAgents   = \App\AgentsRooms::where('user_id', Auth::user()->id)->get(['room_id'])->toArray();
            $rooms         = \App\Rooms::where('state', '=', 1)->whereIn('id', $roomsAgents)->orderBy('order')->get();
            $sqlRooms = \App\Rooms::where('state', '=', 1)->whereIn('id', $roomsAgents);
        }
        
        if ($roomIDs){
          $sqlRooms->whereIn('id', $roomIDs);
        }
        
        $roomscalendar = $sqlRooms->orderBy('order', 'ASC')->get();
        
        $days = $arrayDays;

        

//ob_start("ob_html_compress");
  $buffer = ob_html_compress(view('backend.planning.calendar.content', compact('arrayMonths', 'rooms', 'roomscalendar', 'arrayReservas', 'mes', 'days', 'startYear', 'endYear','currentM','startAux','totalSales')));
//  $buffer = ob_get_contents();
//ob_end_flush();
//echo $buffer; die;
 return view('backend.planning.calendar.index',['content'=>$buffer,'eventDatas'=>$eventDatas]);
        return $buffer;
    }

    static function getCounters($type)
    {
        $year       = self::getActiveYear();
        $startYear  = new Carbon($year->start_date);
        $endYear    = new Carbon($year->end_date);
        $booksCount = 0;
        switch ($type)
        {
            case 'pendientes':
                $booksCount = Book::where('start', '>=', $startYear->copy()->format('Y-m-d'))
                                       ->where('finish', '<=', $endYear->copy()->format('Y-m-d'))
                                       ->whereIn('type_book', [
                                           3,
                                           11
                                       ])->count();
                break;
            case 'especiales':
                $booksCount = Book::where('start', '>=', $startYear->copy()->format('Y-m-d'))
                                       ->where('finish', '<=', $endYear->copy()->format('Y-m-d'))
                                       ->whereIn('type_book', [
                                           7,
                                           8
                                       ])->count();
                break;
            case 'confirmadas':
                $booksCount = Book::where('start', '>=', $startYear->copy()->format('Y-m-d'))
                                       ->where('finish', '<=', $endYear->copy()->format('Y-m-d'))
                                       ->whereIn('type_book', [2])->count();
                break;
            case 'checkin':
                $dateX      = Carbon::now();
                $booksCount = Book::where('start', '>=', $dateX->copy()->subDays(3))->where('start', '<=', $year->end_date)
                                  ->where('type_book', 2)->orderBy('start', 'ASC')->count();
//                $booksCount = Book::where('start', '>=', $dateX->copy()->subDays(3))->where('finish', '<=', $dateX)
//                                       ->where('type_book', 2)->count();
                break;
            case 'blocked-ical':
                $dateX      = Carbon::now();
                $booksCount = Book::where('start', '>=', $startYear->copy()->format('Y-m-d'))
                                       ->where('finish', '<=', $endYear->copy()->format('Y-m-d'))
                                       ->whereIn('type_book', [
                                           11,
                                           12
                                       ])->count();
                break;
            case 'checkout':
                $dateX      = Carbon::now();
                $booksCount = Book::where('finish', '>=', date('Y-m-d'))->where('finish', '<', $year->end_date)
                                  ->where('type_book', 2)->orderBy('finish', 'ASC')->count();
//                $booksCount = Book::where('start', '>=', $dateX->copy()->subDays(3))->where('start', '<=', $dateX)
//                                       ->where('type_book', 2)->count();
                break;
            case 'eliminadas':
                $dateX      = Carbon::now();
                $booksCount = Book::where('start', '>=', $startYear->copy()->format('Y-m-d'))
                                       ->where('finish', '<=', $endYear->copy()->format('Y-m-d'))
                                       ->whereIn('type_book', 0)->count();
                break;
        }

        return $booksCount;
    }

    public function sendSencondEmail(Request $request)
    {
        $book = Book::find($request->id);
        if (!empty($book->customer->email))
        {
            $book->send = 1;
            $book->save();
            
//            $sended = $this->sendEmail_secondPayBook($book, 'Recordatorio de pago Apto. de lujo Miramarski - ' . $book->customer->name);
            $subject = 'Recordatorio Pago '. config('app.name').' '.$book->customer->name;
            $sended = $this->sendEmail_secondPayBook($book, $subject);
            if ($sended)
            {
                return [
                    'status'   => 'success',
                    'title'    => 'OK',
                    'response' => "Recordatorio enviado correctamente"
                ];
            } else
            {
                return [
                    'status'   => 'danger',
                    'title'    => 'Error',
                    'response' => "El email no se ha enviado, por favor intentalo de nuevo"
                ];
            }
        } else
        {
            return [
                'status'   => 'warning',
                'title'    => 'Cuidado',
                'response' => "Este cliente no tiene email"
            ];
        }


    }

    /**
     * Enable/Disable alerts has_low_profit
     *
     * @param Request $request
     * @return type
     *
     */
    public function toggleAlertLowProfits(Request $request)
    {
        $book = Book::find($request->id);
        if ($book)
        {
            $book->has_low_profit = !$book->has_low_profit;
            $book->save();
            if ($book->has_low_profit)
            {
                return [
                    'status'   => 'success',
                    'title'    => 'OK',
                    'response' => "Alarma desactivada para  " . $book->customer->name
                ];
            } else
            {
                return [
                    'status'   => 'success',
                    'title'    => 'OK',
                    'response' => "Alarma activada para  " . $book->customer->name
                ];
            }
        } else
        {
            return [
                'status'   => 'warning',
                'title'    => 'Cuidado',
                'response' => "Registro no encontrado"
            ];
        }


    }

    /**
     * Enable alerts has_low_profit to all
     *
     * @param Request $request
     * @return type
     *
     */
    public function activateAlertLowProfits()
    {
        if (Auth::user()->role == 'admin')
        {
            $year      = $this->getActiveYear();
            $startYear = new Carbon($year->start_date);
            $endYear   = new Carbon($year->end_date);

            $book = Book::where('start', '>', $startYear)->where('finish', '<', $endYear)
                             ->where('has_low_profit', TRUE)->update(['has_low_profit' => FALSE]);
            return [
                'status'   => 'success',
                'title'    => 'OK',
                'response' => "Alarma activada para  todos los registros"
            ];
        } else
        {
            return [
                'status'   => 'warning',
                'title'    => 'Cuidado',
                'response' => "No tiene permisos para la acción que desea realizar"
            ];
        }

    }

    public function cobrarFianzas($id)
    {
        $book      = Book::find($id);
        $hasFiance = \App\Fianzas::where('book_id', $book->id)->first();
        $stripe    = StripeController::$stripe;
        return view('backend/planning/_fianza', compact('book', 'hasFiance', 'stripe'));
    }

    public function checkSecondPay()
    {

      return;
        $daysToCheck = \App\DaysSecondPay::find(1)->days;
        /* Esta funcion tiene una cron asosciada que se ejecuta los dia 1 y 15 de cada mes, es decir cad 2 semanas */
        $date  = Carbon::now();
        $books = Book::where('start', '>=', $date->copy())->where('finish', '<=', $date->copy()
                                                                                            ->addDays($daysToCheck))
                          ->where('type_book', 2)->where('send', 0)->orderBy('created_at', 'DESC')->get();


        foreach ($books as $key => $book)
        {
            if ($book->send == 0)
            {
                if (!empty($book->customer->email) && $book->id == 3908)
                {
                    $book->send = 1;
                    $book->save();
                    $this->sendEmail_secondPayBook($book, 'Recordatorio de pago Apto. de lujo '. config('app.name') .' - ' . $book->customer->name);
                    if ($sended = 1)
                    {
                        echo json_encode([
                                             'status'   => 'success',
                                             'title'    => 'OK',
                                             'response' => "Recordatorios enviados correctamente"
                                         ]);
                        echo "<br><br>";
                    } else
                    {
                        echo json_encode([
                                             'status'   => 'danger',
                                             'title'    => 'Error',
                                             'response' => "El email no se ha enviado, por favor intentalo de nuevo"
                                         ]);
                        echo "<br><br>";
                    }
                } else
                {
                    $book->send = 1;
                    $book->save();
                }
            }

        }

    }

    /**
     * /admin/api/reservas/getDataBook
     * @param Request $request
     * @return type
     */
    public function getAllDataToBook(Request $request)
    {
        $data = ['costes'=>[],'totales'=>[],'calculated'=>[],'public'=>[]];
        $loadedParking = $loadedCostRoom = false;
        $room = \App\Rooms::find($request->room);
        if (!$room){
          return null;
        }
        $start  = $request->input('start');
        $finish = $request->input('finish');
        $pax= $request->pax;
        if ($request->book_id){
          $book = Book::find($request->input('book_id'));
          $sammeDate = false;
          if ($request->start == $book->start && $request->finish  == $book->finish){
            if ($request->pax == $book->pax && $request->room == $book->room_id ){
              $loadedCostRoom = true;
              $data['costes']['book'] = $book->cost_apto;
              if ($request->park == $book->type_park){
                $loadedParking = true;
                $data['costes']['parking'] = $book->cost_park;
              }
            }
          }
        } else {
          $book = new Book();
        }

        $book->pax = $request->input('pax');
        
        $book->room = $room;
        $roomID = ($room) ? $room->id : -1;
        $totals = $book->getPriceBook($start,$finish,$roomID);
        $promotion = $request->promotion ? floatval($request->promotion) : 0;
        
        if (!$loadedCostRoom)
          $data['costes']['book']  = round($totals['cost']);
        
        $data['costes']['limp'] = round($totals['cost_limp']);
        $data['costes']['extra_fixed'] = round($totals['cost_extra_fixed']);
        $data['costes']['extra_dynamic'] = round($totals['cost_extra_dynamic']);
        $data['costes']['agencia']    = (float) $request->agencyCost;
        $data['costes']['promotion']  = $promotion;

        $data['totales']['extra_fixed']    = round($totals['extra_fixed']);
        $data['totales']['extra_dynamic']  = round($totals['extra_dynamic']);
        $data['totales']['limp']  = round($totals['limp']);
        $data['totales']['book']  = round($totals['pvp']);

        $data['aux']['minDay'] = 0;
        if ($room) $data['aux']['minDay'] = $room->getMin_estancia($start,$finish);

        $data['public'] = $room->getRoomPrice($start, $finish, $pax);
        $totalPrice     = $data['public']['pvp']+$data['totales']['extra_dynamic'];
        
        $totalCost = array_sum($data['costes']) - $promotion;
        $profit    = round($totalPrice - $totalCost);

        $data['calculated']['total_price']       = round($totalPrice);
        $data['calculated']['total_cost']        = round($totalCost);
        $data['calculated']['profit']            = $profit;
        $data['calculated']['profit_percentage'] = ($totalCost>0) ? round(($profit / $totalCost) * 100) : 0;
        $data['calculated']['real_price']        = round($totals['price_total']);

        return $data;
    }

    public function saveComment(Request $request, $idBook, $type)
    {
        $book = Book::find($idBook);

        switch ($type)
        {
            case '1':
                $book->comment = $request->value;
                break;

            case '2':
                $book->book_comments = $request->value;
                break;

            case '3':
                $book->book_owned_comments = $request->value;
                break;
        }

        if ($book->save())
        {
            return [
                'status'   => 'success',
                'title'    => 'OK',
                'response' => "Comentarios guardados"
            ];
        };


    }

    public static function getBookFFData(Request $request, $request_id)
    {

        $ff_request = [];
         $book     = Book::find($request_id);
         $customer = \App\Customers::find($book->customer_id);

                $forfaitItem = \App\ForfaitsUser::where('book_id',$request_id)->first();
                $ff_data = [
                    'forfait_data' => [],
                    'materials_data' => [],
                    'classes_data' => [],
                    'forfait_total' => null,
                    'materials_total' => null,
                    'classes_total' => null,
                    'total' => null,
                    'status' => null,
                    'created' => null,
                    'more_info' => null,
                    'id' => null,
                    'ffexpr_status' =>null,
                    'bookingNumber'=>null

                ];
                if ($forfaitItem){
                  $ff_data = [
                    'forfait_data' => json_decode($forfaitItem->forfait_data),
                    'materials_data' => json_decode($forfaitItem->materials_data),
                    'classes_data' => json_decode($forfaitItem->classes_data),
                    'forfait_total' => $forfaitItem->forfait_total,
                    'materials_total' => $forfaitItem->materials_total,
                    'classes_total' => $forfaitItem->classes_total,
                    'total' => $forfaitItem->total,
                    'status' => $forfaitItem->status,
                    'created' => $forfaitItem->created_at,
                    'more_info' => $forfaitItem->more_info,
                    'id' => $forfaitItem->id,
                    'ffexpr_status' =>$forfaitItem->ffexpr_status,
                    'bookingNumber' =>$forfaitItem->ffexpr_bookingNumber
                  ];
                }
                
              return view('/backend/planning/listados/_ff_popup')->with('book', $book)
		                                                   ->with('customer', $customer)
		                                                   ->with('pickupPointAddress','')
		                                                   ->with('ff_data', $ff_data);
	}

    public static function updateBookFFStatus(Request $request, $request_id, $status)
    {
        $book            = Book::find($request_id);
        $book->ff_status = $status;

        if ($book->save())
        {
            return redirect('/admin/reservas/ff_status_popup/' . $request_id);
        }
    }

    public function demoFormIntegration(Request $request)
    {
        $minMax = \App\Rooms::where('state', 1)->selectRaw('min(minOcu) as min, max(maxOcu) as max')->first();
        return view('backend.form_demo', ['minMax' => $minMax]);
    }

    public function apiCheckBook(Request $request)
    {
        $rooms   = [];
        $auxDate = explode('-', $request->input('result')['dates']);
        $start   = Carbon::createFromFormat('d M, y', trim($auxDate[0]));
        $finish  = Carbon::createFromFormat('d M, y', trim($auxDate[1]));
        $name    = $request->input('result')['name'];
        $email   = $request->input('result')['email'];
        $phone   = $request->input('result')['phone'];
        $dni     = $request->input('result')['dni'];
        $pax     = $request->input('result')['pax'];

        $roomsWithPax = \App\Rooms::where('state', 1)->where('minOcu', '<=', $pax)->where('maxOcu', '>=', $pax)->get();
        foreach ($roomsWithPax as $index => $roomsWithPax)
        {
            if (Book::existDate($start->copy()->format('d/m/Y'), $finish->copy()
                                                                             ->format('d/m/Y'), $roomsWithPax->id)) $rooms[] = $roomsWithPax;
        }

        $instantPayment = (\App\Settings::where('key', 'instant_payment')
                                        ->first()) ? \App\Settings::where('key', 'instant_payment')
                                                                  ->first()->value : false;

        return view('backend.api.response-book-request', [
            'rooms'          => $rooms,
            'start'          => $start,
            'finish'         => $finish,
            'pax'            => $pax,
            'name'           => $name,
            'email'          => $email,
            'phone'          => $phone,
            'dni'            => $dni,
            'instantPayment' => $instantPayment,
        ]);
    }

    
    
    
    
    public function getTotalBook(Request $request)
    {
        $start      = $request->input('start');
        $finish     = $request->input('finish');
        $pax        = $request->input('quantity');
        $size_apto  = $request->input('size_apto_id');
        $countDays  = calcNights($start,$finish);
        $site_id    = $request->input('site_id');

        $oGetRoomsSuggest = new \App\Services\Bookings\GetRoomsSuggest();
        if ($size_apto>0) $oGetRoomsSuggest->size_apto = $size_apto;
        if ($site_id>0) $oGetRoomsSuggest->set_siteID($site_id);
        $rooms = $oGetRoomsSuggest->getItemsSuggest($pax,$start,$finish);
        
        $oSetting = new \App\Settings();
        $url = $oSetting->getLongKeyValue('gha_sitio');
        foreach ($rooms as $k=>$v){
          unset($rooms[$k]['infoCancel']);
        }
        $sites = \App\Sites::allSites();
        return view('backend.planning.calculateBook.response', [
                'pax'   => $pax,
                'nigths'=> $countDays,
                'rooms' => $rooms,
                'urlGH' => $url,
                'name'  => $request->input('name'),
                'start' => $start,
                'finish'=> $finish,
                'sites'=> $sites,
            ]);
        
    }
    
    public function getComment($bookID) {
      $book = Book::find($bookID);
      if ($book){
        
        $textComment = "";
        if (!empty($book->comment)) {
            $textComment .= "<b>COMENTARIOS DEL CLIENTE</b>:"."<br>"." ".$book->comment."<br>";
        }
        if (!empty($book->book_comments)) {
            $textComment .= "<b>COMENTARIOS DE LA RESERVA</b>:"."<br>"." ".$book->book_comments."<br>";
        }
        if (!empty($book->book_owned_comments)) {
            $textComment .= "<b>COMENTARIOS PROPIETARIO</b>:"."<br>"." ".$book->book_owned_comments."<br>";
        }
        echo $textComment;
      } else {
        echo '<p>Sin datos</p>';
      }
    }

    /**
     * Prepare the event to show in the calendar
     * @param type $book
     * @param type $uRole
     * @return type
     */
    private function calendarEvent($book,$uRole,$isMobile,$bookings_without_Cvc) {
      
      global $countries,$provs;
      
      if (!$countries){
        $oCountries = \App\Countries::all();
        foreach ($oCountries as $c){
          $countries[$c->code] = $c->country;
        }
      }
      if (!$provs){
        $oProv = \App\Provinces::all();
        $provs = [];
        foreach ($oProv as $p){
          $provs[$p->code] = $p->province;
        }
      }
      
      
      
      $class = $book->getStatus($book->type_book);
      if ($class == "Contestado(EMAIL)"){ $class = "contestado-email";}
      $classTd = ' class="td-calendar" ';
      $titulo = '';
      $agency = '';
      $href = '';
      $vistaCompleta = in_array($uRole, ['admin','subadmin']);
     
      $href = 'href="'.url ('/admin/reservas/update').'/'.$book->id.'" ';
      if (!$isMobile || true){
      
      $titulo = $book->customer->name.'<br />';
      if ($book->client_has_photos)  $titulo.= '<i class="c_h_photo fas fa-camera"></i>';
      
      $titulo.= Carbon::createFromFormat('Y-m-d',$book->start)->formatLocalized('%d %b').
              ' - '.Carbon::createFromFormat('Y-m-d',$book->finish)->formatLocalized('%d %b')
              .'  | <b>Pax</b> '.$book->real_pax.'<br/>';
      
            
        $idText = '';
        if ($vistaCompleta){
          $titulo .='<b>PVP</b>:'.$book->total_price.' | ';
          $titulo .= substr(strtoupper($book->user->name), 0, 8);
          $idText  =' | <b>ID</b> '.$book->id;
           
        }
        if ( ($book->agency != 0)){
          $titulo .= "<br />Agencia: ".$book->getAgency($book->agency).$idText;
        }
      if (isset($countries[$book->customer->country])){
        $c_country = $countries[$book->customer->country];
        if (isset($provs[$book->customer->province])){
          $c_country = $provs[$book->customer->province]." ($c_country)";
        }
        $titulo .= '<br />'.$c_country;
      }
      
      
      
      if ($vistaCompleta && $book->agency == 1){
        if (in_array($book->id, $bookings_without_Cvc)){
          $titulo.= '<br /><b class="text-danger">FALTAN DATOS VISA</b>';
        } else {
          $ccvisa = $book->creditCard;
          $card = null;
          $cc = null;
          if ($ccvisa){
            $aCard = explode(PHP_EOL, $ccvisa);
            foreach ($aCard as $i){
              $nro = preg_replace('/\D+/', '', $i);
              if (is_numeric($nro) && $nro>1000000)
                $cc = $nro;
            }
            $card = check_cc($cc);
          }
          if ($cc){
            if ($card) $titulo.= '<br /><b>OK TARJ '. strtoupper($card).'</b>';
              else $titulo.= '<br /><b>OK TARJ VIRTUAL</b>';
          } else {
            $titulo.= '<br /><b class="text-danger">FALTAN DATOS VISA</b>';
          }
        }
      }
      if ($vistaCompleta && $book->type_book == 2){
          $amount = $book->payments->pluck('import')->sum();
          $falta = intval($book->total_price - $amount);
          if ($falta>5){
             $titulo.= '<br /><b class="text-danger">PENDIENTE PAGO: '. $falta .'€</b>';
             $classTd = ' class="td-calendar bordander" ';
          } else {
            $titulo.= '<br /><b>PENDIENTE PAGO: '. $falta .'€</b>';
          }
      }
        
      }
            
      if ($isMobile){
        $titulo .= '<div class="calLink" data-'.$href.'>IR</div>';
        $href = '#';
      }
      $key = $book->id;

      $return = json_encode([
          'start' => $book->start,
          'finish' => $book->finish,
          'type_book'=>$book->type_book,
          'key'  => $key,
          'titulo'  => '',
          'classTd' => $classTd,
          'href' => $href,
          'class' => $class,
      ]);
      return ['titulo' => $titulo,'key' => $key,'data'=>json_decode($return)];
      //return json_decode($return);
    }
    
       /**
     * Return the visa date
     * @param Request $request
     * @return string
     */
    function updVisa(Request $request){
      $bookingID = $request->input('id', null);
      $clientID = $request->input('idCustomer', null);
      $cc_cvc = $request->input('cc_cvc', null);
      $cc_number = $request->input('cc_number', null);
      
      $oUser = Auth::user();
      $response = [
                  'title' => 'Error',
                  'status' => 'warning',
                  'response' => 'Algo ha salido mal',
              ];
      if ( $oUser->role == "admin" || $oUser->role == "subadmin"){
          $oVisa = DB::table('book_visa')
                    ->where('book_id',$bookingID)
                    ->where('customer_id',$clientID)
                    ->first();
          
          if ($oVisa){
            DB::table('book_visa')
                          ->where('id', $oVisa->id)
                          ->update([
                              'cvc' => $cc_cvc,
                              'cc_number' => $cc_number,
                              'updated_at'=>date('Y-m-d H:m:s'),
                              'imported' => $oVisa->imported+1]);
               
              $response = [
                  'title' => 'OK',
                  'status' => 'success',
                  'response' => 'Dato Guardado',
              ];
            
            $lst = Book::whereNotNull('external_id')->join('book_visa','book_id','=','book.id')->whereNull('cvc')->pluck('book_id');
          }
      }
      
      return response()->json($response);
    }
    /**
     * Return the visa date
     * @param Request $request
     * @return string
     */
    function getVisa(Request $request){
        $booking = $request->input('booking', null);
        $force = $request->input('force', null);
        $imported = 0;
        $fieldsCard = ["name","number",'date',"cvc",'type'];
        if ($booking){
          $aux = explode('-', $booking);
          if (is_array($aux) && count($aux) == 2){
            $bookingID = desencriptID($aux[1]);
            $clientID = desencriptID($aux[0]);
            
            $oVisa = DB::table('book_visa')
                    ->where('book_id',$bookingID)
                    ->where('customer_id',$clientID)
                    ->first();
            
            if ($oVisa){
              if (!$force){
                $visaData = json_decode($oVisa->visa_data, true);
                if ($visaData){
                  foreach ($fieldsCard as $f){
                    if (isset($visaData[$f])){
                      if ($f == 'date') $visaData[$f] = str_replace ('/20', ' / ', $visaData[$f]);
                      echo '
                        <div>
                        <label>'.$f.'</label>
                        <input type="text" class="form-control" value="'.$visaData[$f].'" >
                        <button class="btn btn-success copy_data" type="button"><i class="fa fa-copy"></i></button>
                        </div>';
                    }
                  }
                  echo '<div class="btn btn-blue" type="button" id="_getPaymentVisaForce">Refrescar datos</div>';
                  return ;
                }
              } else {
                $imported = $oVisa->imported;
                if ($imported>11){
                   echo '<p class="alert alert-warning">Excedió el máximo de descargas para esta reserva.</p>';
                   return;
                }
              }
            }
            
            $booking = Book::find($bookingID);
            if ($booking && $booking->customer_id == $clientID){
              $visa_data = $this->getCreditCardData($booking);
//              $visa_data = json_encode(["name"=>'test',"number"=>112321,'date'=>'2020-12-02',"cvc"=>'','type'=>'']);
              if ($visa_data){
                if ($oVisa){
                  DB::table('book_visa')
                          ->where('id', $oVisa->id)
                          ->update([
                              'visa_data' => $visa_data,
                              'updated_at'=>date('Y-m-d H:m:s'),
                              'imported' => $imported+1]);

                } else {
                  DB::table('book_visa')->insert([
                    'book_id' =>$bookingID,
                    'user_id'=>Auth::user()->id,
                    'customer_id'=>$clientID,
                    'visa_data'=>($visa_data),
                    'imported' => 1,
                    'created_at'=>date('Y-m-d H:m:s'),
                    'updated_at'=>date('Y-m-d H:m:s'),
                   ]);
                }

                if ($visa_data){
                  $visaData = json_decode($visa_data, true);

                  foreach ($fieldsCard as $f){
                    if (isset($visaData[$f])){
                      if ($f == 'date') $visaData[$f] = str_replace ('/20', ' / ', $visaData[$f]);
                      echo '
                        <div>
                        <label>'.$f.'</label>
                        <input type="text" class="form-control" value="'.$visaData[$f].'" >
                        <button class="btn btn-success copy_data" type="button"><i class="fa fa-copy"></i></button>
                        </div>';
                    }
                  }
                  echo '<div class="btn btn-blue" type="button" id="_getPaymentVisaForce">Refrescar datos</div>';

                }else { echo '<p class="alert alert-warning">Error de servicio.</p>';}
              } else { echo '<p class="alert alert-warning">Error de servicio.</p>';}

              return ;
            }
          }
        }
        
        return 'Datos no encontrados';
    }
    
    private function getCreditCardData($booking) {
      $visa_data = null;
      return null;
      
      if (!$booking->propertyId) $booking->propertyId = 1542253; 
      if ($booking->external_id && $booking->propertyId){
        
        // Zodomus
        if ($booking->agency == 1 || $booking->agency == 6){
          $oZodomus = new \App\Services\Zodomus\Zodomus();
          $channelID = getChannelByPropID($booking->propertyId);
          $creditCard = $oZodomus->reservations_cc($channelID,$booking->propertyId,$booking->external_id);
          if ($creditCard && isset($creditCard->status) && $creditCard->status->returnCode == 200){
            $visa_data = json_encode($creditCard->customerCC);
          }
        }
        //Wubook
        if ($booking->agency == 4 || $booking->agency == 999999){
          $oWubook = new \App\Services\Wubook\WuBook();
          $oWubook->conect();
          $visa_data = json_encode($oWubook->getCC_Data($booking->room->site_id,$booking->external_id));
          $oWubook->disconect();
        }
      }
      return $visa_data;
    }
    
    function getIntercambio(){
      return view('backend.planning._bookIntercambio');
    }
    function getIntercambioSearch($block,$search){
      
      if (trim($search) == '') return '';

      $year     = $this->getActiveYear();
      $dateFrom = new Carbon($year->start_date);
      $dateTo   = new Carbon($year->end_date);

      $books = null;
      
      if (is_numeric($search)){
        $books = Book::find($search);
        if (!$books){
          return '';
        }
        return view('backend.planning.listados._bookIntercambio',['block'=>$block,'books'=>null,'book'=>$books]);
      } else {
        $customerIds = \App\Customers::where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('name', 'LIKE', '%' . $search . '%')->pluck('id')->toArray();
        if (count($customerIds) > 0)
        {
           
          $books = Book::whereIn('customer_id', $customerIds)->where('start', '>=', $dateFrom)
                        ->where('start', '<=', $dateTo)->where('type_book', '!=', 9)->where('type_book', '!=', 0)
                        ->orderBy('start', 'ASC')->get();
        }
      }
      return view('backend.planning.listados._bookIntercambio',['block'=>$block,'books'   => $books,'book'=>null]);
    }
    function intercambioChange(Request $request){
      
      $book_1 = $request->input('book_1',null);
      $book_2 = $request->input('book_2',null);
    
      if (!$book_1 || !$book_2){
        return response()->json(['status'=>'error','msg'=>'Debe seleccionar ambas reservas']);
      }
      if ($book_1 == $book_2){
        return response()->json(['status'=>'error','msg'=>'Ambas reservas no pueden ser la misma']);
      }
      
      $b1 = Book::find($book_1);
      $b2 = Book::find($book_2);
      
      if (!$b1 || !$b2){
        return response()->json(['status'=>'error','msg'=>'Debe seleccionar ambas reservas']);
      }
      
      $r1 = $b1->room_id;
      $r2 = $b2->room_id;
      
      
      $b1->room_id = $r2;
      $b1->save();
      
      $b2->room_id = $r1;
      $b2->save();
      
      return response()->json(['status'=>'ok']);
      
    }
    
    /**
     * 
     * @param type $bookID
     * @return type
     * /reservas/api/get-all-extras/{id}
     */
    function getDynamicExtraItems($bookID){
      $extras = ExtraPrices::getDynamic();
//      $status = BookExtraPrices::getStatus();
      
      $oBookingExtras = BookExtraPrices::getDynamic($bookID);
      $vdors = ExtraPrices::getVendrs();
      $total = 0;
      $content = '';
      if ($oBookingExtras){
        foreach ($oBookingExtras as $item){
          $total += $item->price;
          $content .= '<div class="extras_item row" data-id="'.$item->id.'">
                  <div class="col-xs-4">
                    <select class="extra_type form-control extras_val">
                      <option value="">--</option>';
          
          foreach ($extras as $e){
            $selected = '';
            if ($e->id == $item->extra_id) $selected = 'selected';
            $content .= '<option value="'.$e->id.'" '.$selected.'>'.$e->name.'</option>';
          }
          
          $content .= ' 
                    </select>
                  </div>
                  <div class="col-xs-2"><input type="number" class="form-control extra_qty extras_val" name="qty" value="'.$item->qty.'"></div>
                  <div class="col-xs-2 input-price">
                    <label>€</label>
                    <input type="number" class="form-control extra_price extras_val" name="price" value="'.$item->price.'">
                  </div>
                  <div class="col-xs-3">
                    <select class="extra_vdor form-control extras_val">
                      <option value="">--</option>';
          
          foreach ($vdors as $k=>$v){
            $selected = '';
            if ($k == $item->vdor) $selected = 'selected';
            $content .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
          }
          
          $content .= ' 
                    </select>
                  </div>
                  <div class="col-xs-1">
                    <i class="fa fa-trash btn-delete-extr text-danger action"></i>
                  </div>
                </div>';
        }
      }
  
      return response()->json(['total'=>$total,'content'=>$content,'count'=>count($oBookingExtras)]);
    }
    
    
    function getDynamicExtraPrice(Request $request){
      $oExtra = ExtraPrices::getDynamic($request->id);
      
      if ($oExtra->id = $request->id){
        return response()->json(['p'=>$oExtra->price,'c'=>$oExtra->cost]);
      }
      return 0;
    }
    
    function setDynamicExtraPrice(Request $request){
      
      
      $oExtra = ExtraPrices::getDynamic($request->type);
      $import = $request->price;
      if ($oExtra->id = $request->type){
        $oBookExtra = new BookExtraPrices();
        $oBookExtra->book_id = $request->bID;
        $oBookExtra->extra_id = $oExtra->id;
        $oBookExtra->qty = $request->qty;
        $oBookExtra->price = $request->price;
        $oBookExtra->cost = $oExtra->cost*$request->qty;
        $oBookExtra->status = $request->status;
        $oBookExtra->vdor = $request->vdor;
        $oBookExtra->user_id = $request->vdor;
        $oBookExtra->type = $oExtra->type;
        $oBookExtra->fixed = 0;
        $oBookExtra->deleted = 0;
        $oBookExtra->save();
        
        $book = Book::find($oBookExtra->book_id);
        if($book && $import>0){
          $book->total_price += $request->price;
          $book->real_price += $request->price;
          $book->cost_total += $request->cost;
          $book->save();
       
          //save the payment
          
          if ( !($request->status == 'CASH' || $request->status == 'TPV') )
            return 'ok';

          $payment = new \App\Payments();

          $payment->book_id = $book->id;
          $payment->datePayment = date('Y-m-d');
          $payment->import = $import;
          $payment->comment = $oExtra->name;
          $payment->type = $payment->getTypeId($request->status);

          $saved = $payment->save();

        //Send PAyment Notification
        if ($saved && $import>0){
          if ($book->customer->send_notif){
            $subject = translateSubject('RECIBO PAGO SUPLEMENTO',$book->customer->country);
            $subject .= ' '. $book->customer->name;
            $this->sendEmail_confirmCobros($book,$subject,floatval($import),$book->customer->email_notif);
          }
          if ($request->status == 'CASH'){
            $siteID = \App\Rooms::select('site_id')->where('id',$book->room_id)->first();
            \App\Incomes::generateFromBook($import,$payment->comment,$book->id,$siteID->site_id,$payment->id );
          }

          }
        }
        return 'ok';
      }
     
     
      return 0;
    }
    function updDynamicExtraPrice(Request $request){
      
      
      $oExtra = ExtraPrices::getDynamic($request->type);
      if ($oExtra->id == $request->type){
        $oBookExtra = BookExtraPrices::find($request->id);
        if ($oBookExtra->id == $request->id){
          $oldPrice = $oBookExtra->price;
          $oldCost = $oBookExtra->cost;
          $deleted = false;
          
          $oBookExtra->book_id = $request->bID;
          $oBookExtra->extra_id = $request->type;
          $oBookExtra->qty = $request->qty;
          $oBookExtra->price = $request->price;
          $oBookExtra->cost = $oExtra->cost*$request->qty;
          $oBookExtra->type = $oExtra->type;
          $oBookExtra->vdor = $request->vdor;
          $oBookExtra->user_id = $request->vdor;
//          $oBookExtra->status = $request->status;
          $oBookExtra->fixed = 0;
          if ($request->status == 'BORRAR'){
            $oBookExtra->deleted = 1;
            $deleted = true;
          }
          
          $oBookExtra->save();
          
          
          //change the total price of booking
          
          
          $book = Book::find($oBookExtra->book_id);
          if($book){
            $book->total_price -= $oldPrice;
            $book->real_price  -= $oldPrice;
            $book->cost_total  -= $oldCost;
              
            if (!$deleted){
              $book->total_price += $request->price;
              $book->real_price += $request->price;
              $book->cost_total += $request->cost;
            }
            $book->save();
          }
          
          
          return 'ok';
        }
      }
     
     
      return 0;
    }
    
    function getSizesBySite($site){
        
      $lst = [];
      $tiposApto = \App\SizeRooms::allSizeApto();
        
      if ($site){
        $rooms = Rooms::where('site_id',$site)->get();

        if($rooms){
          foreach ($rooms as $r){
            if (isset($tiposApto[$r->sizeApto]) && !isset($lst[$r->sizeApto]))
              $lst[$r->sizeApto] = $tiposApto[$r->sizeApto] ;
          }
        }
        
      } else {
        $lst = $tiposApto;
      }
      
      
      echo '<option value="0">Todos</option>';
      foreach ($lst as $k=>$v){
        echo '<option value="'.$k.'">'.$v.'</option>';
      }
    }
    
    function paymentBlock($bookingID){
      $book = Book::find($bookingID);
      if ($book){
        $totalpayment = $book->sum_payments;
        $payment_pend = floatVal($book->total_price)-$totalpayment;
        $email_notif = '';
        $send_notif = '';
        if ($book->customer_id ){
          $email_notif = $book->customer->email_notif ? $book->customer->email_notif : $book->customer->email;
          $send_notif = $book->customer->send_notif ? 'checked' : '';
        }
            
            
//        $payments     = $book->payments;
         return view('backend/planning/blocks/payments-update',
                compact('book','totalpayment','send_notif','email_notif','payment_pend')
		);
        
      }
    }
    
    
    /**
     * Enable alerts has_low_profit to all
     *
     * @param Request $request
     * @return type
     *
     */
    public function getAlertLowProfits()
    {
        if (Auth::user()->role == 'admin')
        {
            $year      = $this->getActiveYear();
            $alert_lowProfits = 0; //To the alert efect
            $percentBenef     = DB::table('percent')->find(1)->percent;
            $alarms = $this->lowProfitAlert($year->start_date, $year->end_date, $percentBenef, $alert_lowProfits);

            echo view('backend.planning.blocks.lowProfits',[
                'alarms'=>$alarms,
                'isMobile'=>config('app.is_mobile'),
                'percentBenef'=>$percentBenef]);
           
        }

    }
    
    
    /**
     * Change the data to notific
     * @param Request $request
     * @return string
     */
    function changeMailNotif(Request $request){
        $bookingID = $request->input('booking', null);
        if ($bookingID){
          $booking = Book::find($bookingID);
          if ($booking->customer_id>1){
            $booking->customer->email_notif = $request->input('email_notif', null);
            $booking->customer->send_notif = ($request->input('send_notif')=='true') ? 1:0;

            $booking->customer->save();
            return response()->json([
                'title' => 'OK',
                'status' => 'success',
                'response' => 'Datos de contacto cambiados',
            ]);
          }
        }
        return response()->json([
                  'title' => 'Error',
                  'status' => 'warning',
                  'response' => 'Algo ha salido mal',
              ]);
    }

      

     public function getCalendarBooking(Request $request)
    {
        $mobile      = config('app.is_mobile');
        $arrayMonths = array();
        $arrayDays   = array();
        if (empty($year))
        {
            $date = Carbon::now();
        } else
        {
            $year = Carbon::createFromFormat('Y', $year);
            $date = $year->copy();

        }
        $firstDayOfTheYear = new Carbon('first day of September ' . $date->copy()->format('Y'));
        $rooms             = \App\Rooms::where('state', 1)->orderBy('order', 'ASC')->get();
        $typesRoom         = [
            '2dorm-lujo'   => [
                'total'  => 0,
                'name'   => '2DL',
                'months' => []
            ],
            'estudio'      => [
                'total'  => 0,
                'name'   => 'EST',
                'months' => []
            ],
            '2dorm-stand'  => [
                'total'  => 0,
                'name'   => '2DS',
                'months' => []
            ],
            'chalet'       => [
                'total'  => 0,
                'name'   => 'CHL',
                'months' => []
            ],
            '10pers-stand' => [
                'total'  => 0,
                'name'   => '3DS',
                'months' => []
            ],

            '12pers-stand' => [
                'total'  => 0,
                'name'   => '4DS',
                'months' => []
            ]
        ];
        $book              = new \App\Book();
        $auxDate           = $firstDayOfTheYear->copy();
        foreach ($rooms as $key => $room)
        {
            if ($room->luxury == 1 && $room->sizeApto == 2)
            {
                $typesRoom['2dorm-lujo']['total'] += 1;
            }

            if ($room->luxury == 1 && $room->sizeApto == 1)
            {
                $typesRoom['estudio']['total'] += 1;
            }

            if ($room->luxury == 0 && $room->sizeApto == 9){
               $typesRoom['chalet']['total'] += 1;
            }
            if ($room->luxury == 0 && $room->sizeApto == 2)
            {
                $typesRoom['2dorm-stand']['total'] += 1;
            }

            if ($room->luxury == 0 && $room->sizeApto == 1)
            {
                $typesRoom['estudio']['total'] += 1;
            }


            if ($room->luxury == 0 && $room->sizeApto == 3)
            {
                $typesRoom['10pers-stand']['total'] += 1;
            }

            if ($room->luxury == 0 && $room->sizeApto == 4)
            {
                $typesRoom['12pers-stand']['total'] += 1;
            }
        }

        for ($i = 1; $i <= 12; $i++)
        {
            $startMonth = $auxDate->copy()->startOfMonth();
            $day        = $startMonth;

            $arrayMonths[$auxDate->copy()->format('n')] = $day->copy()->format('t');
            for ($j = 1; $j <= $day->copy()->format('t'); $j++)
            {
                $arrayDays[$auxDate->copy()->format('n')][$j] = $book->getDayWeek($day->copy()->format('w'));
                foreach ($typesRoom as $key => $room)
                {
                    $typesRoom[$key]['months'][$day->copy()->format('n')][$day->copy()
                                                                              ->format('j')] = $typesRoom[$key]['total'];
                }

                $day = $day->copy()->addDay();
            }
            $auxDate->addMonth();
        }

        $dateX = $date->copy();

        $reservas = Book::whereIn('type_book', [
            1,
            2,
            4,
            7
        ])->where('start', '>=', $firstDayOfTheYear->copy())->where('finish', '<=', $firstDayOfTheYear->copy()
                                                                                                      ->addYear())
                             ->get();

        foreach ($reservas as $reserva)
        {
            $room = \App\Rooms::find($reserva->room_id);

            $start  = Carbon::createFromFormat('Y-m-d', $reserva->start);
            $finish = Carbon::createFromFormat('Y-m-d', $reserva->finish);
            $diff   = $start->diffInDays($finish);
            $dia    = Carbon::createFromFormat('Y-m-d', $reserva->start);
            for ($i = 1; $i <= $diff; $i++)
            {
                if ($room->luxury == 1 && $room->sizeApto == 2)
                {
                    $typesRoom['2dorm-lujo']['months'][$dia->copy()->format('n')][$dia->copy()->format('j')] -= 1;
                }
                if ($room->luxury == 1 && $room->sizeApto == 1)
                {
                    $typesRoom['estudio']['months'][$dia->copy()->format('n')][$dia->copy()->format('j')] -= 1;
                }
                if ($room->sizeApto == 9){
                  $typesRoom['chalet']['months'][$dia->copy()->format('n')][$dia->copy()->format('j')] -= 1;
                }
                if ($room->luxury == 0 && $room->sizeApto == 2)
                {
                  $typesRoom['2dorm-stand']['months'][$dia->copy()->format('n')][$dia->copy()->format('j')] -= 1;
                }
                if ($room->luxury == 0 && $room->sizeApto == 1)
                {
                    $typesRoom['estudio']['months'][$dia->copy()->format('n')][$dia->copy()->format('j')] -= 1;
                }
                if ($room->luxury == 1 && $room->sizeApto == 3)
                {
                    $typesRoom['10pers-lujo']['months'][$dia->copy()->format('n')][$dia->copy()->format('j')] -= 1;
                }
                if ($room->luxury == 0 && $room->sizeApto == 3)
                {
                    $typesRoom['10pers-stand']['months'][$dia->copy()->format('n')][$dia->copy()->format('j')] -= 1;
                }
                if ($room->luxury == 1 && $room->sizeApto == 4)
                {
                    $typesRoom['12pers-lujo']['months'][$dia->copy()->format('n')][$dia->copy()->format('j')] -= 1;
                }
                if ($room->luxury == 0 && $room->sizeApto == 4)
                {
                    $typesRoom['12pers-stand']['months'][$dia->copy()->format('n')][$dia->copy()->format('j')] -= 1;
                }

                $dia->addDay();
            }
        }
        $days = $arrayDays;

        return view('backend.planning._calendarToBooking', compact('days', 'dateX', 'arrayMonths', 'mobile', 'typesRoom'));
    }
    /***********************************************************************/
  
    public function getBooksWithoutCvc() {

      $bookings_without_Cvc = \App\ProcessedData::findOrCreate('bookings_without_Cvc');
      $bookings_without_Cvc = json_decode($bookings_without_Cvc->content,true);
      $aVisasCVCLst = [];
      $aVisasNumLst = [];
      $aVisasData = [];
      $bookLst = null;
      if ($bookings_without_Cvc){
        $bookLst = Book::whereIn('id',$bookings_without_Cvc)->with('customer')->get();
        

        $oVisas = \App\BookData::where('key','creditCard')
                    ->whereIn('book_id',$bookings_without_Cvc)
                    ->get();
        if ($oVisas){
            foreach ($oVisas as $visa){
              $aVisasData[$visa->book_id] = $visa->content;
//                $aVisasCVCLst[$visa->book_id] = $visa->cvc;
//                $aVisasNumLst[$visa->book_id] = $visa->cc_number;
            }
        }
      }
      $isMobile = config('app.is_mobile');
      return view('backend/planning/_load-cvc',compact('bookLst','isMobile','aVisasData'));

  } 

  public function save_creditCard(Request $request){
      
      $oUser = Auth::user();
      if ( !( $oUser->role == "admin" || $oUser->role == "subadmin")){
            return [
                'status'   => 'danger',
                'title'    => 'ERROR',
                'response' => "No posees permisos"
            ];
      }
      $bookingID = $request->input('bID', null);
      $creditCardData = $request->input('data', null);
       

      $book = Book::find($bookingID);
      if (!$book){
        return [
                'status'   => 'danger',
                'title'    => 'ERROR',
                'response' => "RESERVA NO ENCONTRADA"
            ];
      }
       
      $oBookData = \App\BookData::findOrCreate('creditCard',$bookingID);
      $oBookData->content = $creditCardData;
      $oBookData->save();
      
              return [
                'status'   => 'success',
                'title'    => 'OK',
                'response' => "REGISTRO GUARDADO"
            ];
  }   
  
  function removeAlertPax(Request $request){
    $oUser = Auth::user();
    if ( !( $oUser->role == "admin" || $oUser->role == "subadmin")){
      return "No posees permisos";
    }
    $bookingID = $request->input('bID', null);
    $oData = \App\ProcessedData::where('key','checkPaxs')->first();
    if ($oData){
      $content = json_decode($oData->content,true);
      foreach ($content as $k=>$v){
        if ($v['bookID'] == $bookingID){
          unset($content[$k]);
        }
      }
      $oData->content = json_encode($content);
      $oData->save();
    }
      
    return 'OK';
  }
 function getOTAsLogErros_qty(){
    $resp = '';
    $dir = storage_path().'/logs/OTAs'.date('Ym').'.log';
    $count = 0;
    
    $lastView = strtotime('-1 weeks');
    $lastRead = \App\ProcessedData::findOrCreate('log_OTA_readed');
    if ($lastRead->content)  $lastView = $lastRead->content;
    
    /** TEST */
//    $dir = storage_path().'/logs/OTAs202110.log';
//    $lastView = strtotime('2021-10-26 13:52:48');
    /** TEST */
    
    if (file_exists($dir)) {
      $lines = file($dir);
      $lines = array_reverse($lines);
      foreach ($lines as $num => $lin) {
        $dTime = strtotime(substr($lin, 1,19));
        if ($lastView<$dTime){
            $count++;
        }
      }
    }
    return $count;
  }
  
  function getLogErros_notRead(){
    $resp = '<div class="e_logs">';//
    $dir = storage_path().'/logs/OTAs'.date('Ym').'.log';
    $count = 0;
    
    $lastView = strtotime('-1 weeks');
    $lastRead = \App\ProcessedData::findOrCreate('log_OTA_readed');
    if ($lastRead->content)  $lastView = $lastRead->content;
//    $lastView = strtotime('-2 days');
    
    if (file_exists($dir)) {
      $lines = file($dir);
      $lines = array_reverse($lines);
      foreach ($lines as $num => $lin) {
        $dTime = strtotime(substr($lin, 1, 19));
        if ($lastView < $dTime) {
          $data = explode('OtaGateway.ERROR: ', $lin);
          $dataJson = str_replace(' [] []', '', $data[1]);
          if (str_contains($lin, '} {')) {
            $aux = explode('} {', $dataJson);
            $dataJson = $aux[0] . '}';
          }

          $aData = json_decode($dataJson);

          if (isset($aData->message)) {
            $count++;
            $resp .= "<b>{$data[0]}</b>: " . ($aData->message) . "<br />";
          } else {
            if (isset($aData->errors)) {
              foreach ($aData->errors as $v) {
                if (isset($v->message)) {
                  $count++;
                  $resp .= "<b>{$data[0]}</b>: " . ($v->message) . "<br />";
                }
              }
            }
          }
        }
      }
    }
    
    if ($count == 0){
      $resp .= '<div class="alert alert-warning">No hay registros sin leer</div>';
    }
    
    echo $resp.'</div>';
        
    $lastRead->content = time();
    $lastRead->save();
    
    return;
  }
  
  function toggleCliHas(Request $request){
    $bID  = $request->input("bid");
    $type = $request->input("type");
    $oBook = Book::find($bID);
    if (!$oBook){
      return response()->json(['status'=>'error','result'=>'reserva no encontrada']);
    }
    
    switch ($type){
      case 'photos':
        $oData = \App\BookData::findOrCreate('client_has_photos',$bID);
        break;
      case 'beds':
        $oData = \App\BookData::findOrCreate('client_has_beds',$bID);
        break;
      case 'babyCarriage':
        $oData = \App\BookData::findOrCreate('client_has_babyCarriage',$bID);
        break;
    }
    
    
    $oData->content = ($oData->content) ? false : true;
    $oData->save();
    
    return response()->json(['status'=>'OK','result'=>$oData->content]);
  }
}

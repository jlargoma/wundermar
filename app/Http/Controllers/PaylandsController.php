<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\BookOrders;
use App\Traits\BookEmailsStatus;

class PaylandsController extends AppController
{
  use BookEmailsStatus;
    public function payment(Request $request){
          $booking = $request->input('booking', null);
          $amount = $request->input('amount', null);
          $is_deferred = $request->input('is_deferred', null); //fianza
          if ($booking && $amount){
            $aux = explode('-', $booking);
            if (is_array($aux) && count($aux) == 2){
              $bookingID = desencriptID($aux[1]);
              $clientID = desencriptID($aux[0]);
              
              $book = \App\Book::find($bookingID);
              if ($book){
                if ($book->customer_id == $clientID){
                  $client = $book->customer()->first();
                  $description = "COBRO RESERVA CLIENTE " . $client->name;
                  $client_email = 'no_email';
                  if ($client && trim($client->email)){
                    $client_email = $client->email;
                  }
                  $site_id = $book->room->site_id;
          
                 
                  $urlToRedirect = $this->generateOrderPaymentBooking(
                          $bookingID,
                          $clientID,
                          $client_email,
                          $description,
                          $amount,
                          $is_deferred,
                          $site_id
                          );
                  return view('backend.bookStatus.bookPaylandPay', [ 'url' => $urlToRedirect]);
                } 
                return 'error 4';
              }
              return 'error 3';
            }
            return 'error 2';
          } 
          return 'error 1';
	}
        
        public function paymentTest() {
          $amount = 1;
           $urlToRedirect = $this->generateOrderPaymentBooking(
                          11,
                          22,
                          'test@tesset.com',
                          'test',
                          $amount
                          );
        }

    public function processPaymentBook(Request $request, $id, $payment)
    {
    $book = \App\Book::find($id);
    $this->payBook($id, $payment);
    return redirect()->route('book.update', ['id' => $book->id]);

    }

    public function link(Request $request)
    {
        if ($request->book != 0)
            $book = \App\Book::find($request->book);
        //$importe = base64_encode($request->importe);
        $params['amount']          = ($request->importe) * 100;
        $params['customer_ext_id'] = "admin@" . $_SERVER['REQUEST_URI'];
        $params['operative']       = "AUTHORIZATION";
        $params['secure']          = false;
        $params['signature']       = config('app.payland.signature');
        $params['service']         = config('app.payland.service');
        $params['description']     = "COBRO ESTANDAR";
        $params['url_ok']          = route('thanks-you');
        $params['url_ko']          = route('thanks-you');

        $orderPayment  = $this->getPaylandApiClient()->payment($params);
        $urlToRedirect = $this->getPaylandApiClient()->processPayment($orderPayment->order->token);
        $url           = $urlToRedirect;
        return view('backend.bookStatus.bookPaylandPay', [ 'url' => $url]);
    }

    public function linkSingle(Request $request)
    {
      $amount = $request->input('importe',null);
      $subject= $request->input('subject',null);
      $bookingID = $request->input('book',null);
      
      $urlPay = $this->generateOrder($amount,$subject,$bookingID);
      if ($urlPay){
        $urlPay = get_shortlink($urlPay);
        return $this->getPaymentText($urlPay,$bookingID,$amount);
      }
      return 'error';
    }
    
    public function generateOrder($amount,$subject,$bookingID,$is_deferred=false) {
      $book = \App\Book::find($bookingID);
      $token = str_random(32).time();
      $urlPay = getUrlToPay($token);
      $site_id = null;
      if ($amount){
        if ($book){
          $client = $book->customer()->first();
          $description = "COBRO RESERVA CLIENTE " . $client->name;
          $client_email = 'no_email';
          if ($client && trim($client->email)){
            $client_email = $client->email;
          }
          
          $site_id = $book->room->site_id;
          
          $PaymentOrders = new \App\PaymentOrders();
          $PaymentOrders->book_id = $bookingID;
          $PaymentOrders->cli_id = $client->id;
          $PaymentOrders->cli_email = $client_email;
          $PaymentOrders->amount = $amount;
          $PaymentOrders->status = 0;
          $PaymentOrders->token = $token;
          $PaymentOrders->description = $description;
          $PaymentOrders->is_deferred = $is_deferred;
          $PaymentOrders->site_id = $site_id;
          $PaymentOrders->save();
          return $urlPay;
        } else {
          $PaymentOrders = new \App\PaymentOrders();
          $PaymentOrders->book_id = -1;
          $PaymentOrders->cli_id = -1;
          $PaymentOrders->cli_email = config('app.payland.mail');
          $PaymentOrders->amount = $amount;
          $PaymentOrders->status = 0;
          $PaymentOrders->token = $token;
          $PaymentOrders->description = $subject;
          $PaymentOrders->is_deferred = $is_deferred;
          $PaymentOrders->site_id = $site_id;
          $PaymentOrders->save();
          
          return $urlPay;
        }
      }
      return null;
    }
    
    private function getPaymentText($urlPay,$bookingID=false,$amount=null) {
      $texto = null;
      $phone = null;
      $whatsapp = '';
      if ($bookingID){
        $book = \App\Book::where('id',$bookingID)->with('room')->first();
        if ($book && $book->room){
          $texto = $this->getMailData($book, 'text_payment_link');
          $texto = str_replace('{payment_amount}', $amount, $texto);
          $texto = str_replace('{urlPayment}', $urlPay, $texto);
          if ($book->customer->phone) $phone=$book->customer->phone;
        }
      } 
      if (!$texto){
        $texto = 'En este link podrás realizar el pago de la señal.<br /> En el momento en que efectúes el pago, te legará un email ';
        $texto .= '<br />'.$urlPay;
      }
      
      $whatsapp = whatsappFormat($texto);
//      $whatsapp = ($texto);
      $textoUnformat = strip_tags(str_replace('<br />', '&#10;', $texto));
      
     
      
//      str_replace('<br>', '&#10;',$texto)
//      $whatsapp = nl2br(strip_tags($whatsapp));  
      if (config('app.is_mobile') && $phone){
        $linkWSP = 'href="whatsapp://send?text='.$whatsapp.'"
             data-action="share/whatsapp/share"';
      } else {
        $linkWSP = 'href="https://web.whatsapp.com/send?phone='.$phone.'&text='.$whatsapp.'" target="_blank"';
      }
            
      
      $response = '
              <div id="textPayment">'.whatsappUnFormat($texto).'</div>
              <div class="row text-center">
              
                  <a style="margin: 15px;" '.$linkWSP.'>
                      <i class="fab fa-whatsapp fa-2x" aria-hidden="true"></i>
                  </a>
                  <span style="margin: 15px;" id="copyLinkStripe">
                    <i class="fa fa-copy  fa-2x" aria-hidden="true"></i>
                  </span>  
                <textarea type="text" id="cpy_link" style="display:none;border: none;color: #fff;">' . $textoUnformat . '</textarea>
          </div>';
      return $response;
    }

    public function thansYouPayment($key_token,$redirect=true)
    {
      
      $bookOrder = BookOrders::where('key_token',$key_token)->whereNull('paid')->first();
//      $bookOrder = BookOrders::where('key_token',$key_token)->first();
      if ($bookOrder){
        $bookOrder->paid = true;
        $bookOrder->save();
        $amount = ($bookOrder->amount/100).' €';
        \App\BookLogs::saveLogStatus($bookOrder->book_id,null,$bookOrder->cli_email,"Pago de $amount ($key_token)");
        $book = \App\Book::find($bookOrder->book_id);
        if ($book){
          //temporalmente no envíe los mails
          $book->customer->send_mails = FALSE;
          $book->customer->save();
          $this->payBook($bookOrder->book_id, $bookOrder->amount);
          $book->customer->send_mails = 1;
          $book->customer->save();
          
          
          $sPaylandActions = new \App\Services\PaylandActions();
          $sPaylandActions->proceessPaymentActions($bookOrder->id,$bookOrder->book_id);
          
          //BEGIN: check if is a final payment
         

          if ($book->customer->send_notif){
            $subject = translateSubject('RECIBO PAGO RESERVA',$book->customer->country);
            $subject .= ' '. $book->customer->name;
            $this->sendEmail_confirmCobros($book,$subject,floatval($amount),$book->customer->email_notif);
//            dd($book->customer);
          }else {
             
            $totalPayment = 0;
            $payments     = \App\Payments::where('book_id', $book->id)->get();
            if (count($payments) > 0)
            {
                foreach ($payments as $key => $pay)
                {
                    $totalPayment += $pay->import;
                }
            }
            $pendiente         = ($book->total_price - $totalPayment);
          
            if ($pendiente<=0){
              $subject = translateSubject('Confirmación de Pago',$book->customer->country);

              $subject .= ' '. $book->customer->name;
              $this->sendEmail_confirmSecondPayBook($book,$subject,$totalPayment, floatval($amount));
            }
          }
          //END: check if is a final payment
        }
         
      }
      
      if ($redirect)  return redirect()->route('thanks-you');
        
    }
    
    public function widgetPayment($key_token)
    {
      
      $bookOrder = BookOrders::where('key_token',$key_token)->whereNull('paid')->first();
      $siteID = null;
      if ($bookOrder){
        $siteID = $bookOrder->site_id;
        $bookOrder->paid = true;
        $bookOrder->save();
        $amount = ($bookOrder->amount/100).' €';
        \App\BookLogs::saveLogStatus($bookOrder->book_id,null,$bookOrder->cli_email,"Pago de $amount ($key_token)");
        $book = \App\Book::find($bookOrder->book_id);
        if ($book){
          $siteID = $book->room->site_id;
          //temporalmente no envíe los mails
          $book->customer->send_mails = FALSE;
          $book->customer->save();
          $this->payBook($bookOrder->book_id, $bookOrder->amount);
//          $book->type_book = 11;
//          $book->save();
          $book->customer->send_mails = 1;
          $book->customer->save();
          $this->sendEmail_WidgetPayment($book,floatval($amount));
          $sPaylandActions = new \App\Services\PaylandActions();
          $sPaylandActions->proceessPaymentActions($bookOrder->id,$bookOrder->book_id);
        }
        return view('frontend.stripe.widget', ['site'=>$siteID]);
      }
      
      die("La información que solicitó ya no se encuentra disponible.");
    }
    
    public function widgetPaymentSuplementos($key_token)
    {
      
      $bookOrder = BookOrders::where('key_token',$key_token)->whereNull('paid')->first();
      if ($bookOrder){
        $siteID = $bookOrder->site_id;
        $bookOrder->paid = true;
        $bookOrder->save();
        $amount = ($bookOrder->amount/100).' €';
        \App\BookLogs::saveLogStatus($bookOrder->book_id,null,$bookOrder->cli_email,"Pago de Suplementos $amount ($key_token)");
        $book = \App\Book::find($bookOrder->book_id);
        if ($book){
          //temporalmente no envíe los mails
          $book->customer->send_mails = FALSE;
          $book->customer->save();
          $this->payBook($bookOrder->book_id, $bookOrder->amount);
          $book->customer->send_mails = 1;
          $book->customer->save();
          $sPaylandActions = new \App\Services\PaylandActions();
          $sPaylandActions->proceessPaymentActions($bookOrder->id,$bookOrder->book_id);
        }
        return view('frontend.stripe.widgetSuplementos', ['site'=>$siteID]);
      }
      
      die("La información que solicitó ya no se encuentra disponible.");
    }
    public function errorPayment($key_token)
    {
      $bookOrder = BookOrders::where('key_token',$key_token)->first();
      if ($bookOrder){
        $amount = ($bookOrder->amount/100).' €';
        \App\BookLogs::saveLogStatus($bookOrder->book_id,null,$bookOrder->cli_email,"Error en Pago de $amount ($key_token)");
      }
      return redirect()->route('paymeny-error');
    }
    public function processPayment(Request $request, $id)
    {
      $dir = storage_path().'/payland';
      if (!file_exists($dir)) {
          mkdir($dir, 0775, true);
      }
      file_put_contents($dir."/$id-".time(), $id."\n". json_encode($request->all()));
      $order = $request->input('order', null);
      if ($order){//"amount":100000,"currency":"978","paid":true
        $uuid = $order['uuid'];
        $amount = $order['amount'];
        $paid = $order['paid'];
        if ($paid === true){
          $bookOrder = BookOrders::where('order_uuid',$uuid)->whereNull('paid')->first();
          if ($bookOrder){
            
            if ($bookOrder->amount == $amount){
              $this->thansYouPayment($bookOrder->key_token,false);
            }
          }
          
        }
      }
      die('ok');

    }
    
    public function getOrder($key_token) {

      $bookOrder = BookOrders::where('key_token',$key_token)->first();
      if (!$bookOrder) die('orden no encontrada');
      $orderPayment = $this->getPaylandApiClient()->getOrder($bookOrder->order_uuid);
      if (!$orderPayment) die('orden no encontrada');
      $html = '<div><b>ORDEN ID '.$bookOrder->order_uuid.'</b></div>';
      $html .= '<div><b>Mensaje:</b>'.$orderPayment->message.'</div>';
      $html .= '<div><b>Monto:</b>'.moneda($orderPayment->order->amount/100).'</div>';
      
      $html .= '<div><b>Pagado:</b>'.($orderPayment->order->paid ? 'SI' : 'NO').'</div>';
      $html .= '<div><b>Estado:</b>'.$orderPayment->order->status.'</div><hr>';
      return $html;
         
    } 
    
    public function getOrders(Request $request, $isAjax = true) {
      
      
      
          $year = $request->input('year',null);
          $month = $request->input('month',null);
          if ($year && $month){
            // First day of a specific month
            $d = new \DateTime($year.'-'.$month.'-01');
            $d->modify('first day of this month');
            $startDate = $d->format('YmdHi');
             // First day of a specific month
            $d = new \DateTime($year.'-'.$month.'-01');
            $d->modify('last day of this month');
            $endDate = $d->format('Ymd').'2359';
          } else {
            $year = $this->getActiveYear();
            $d = str_replace('-','', $year->start_date);
            $startDate = $d.'0000';
            $d = str_replace('-','', $year->end_date);
            $endDate = $d.'2359';
          } 

          
          $orderPayment = $this->getPaylandApiClient()->getOrders($startDate,$endDate);
           $respo_list = [];
          $total_month = 0;
        if ($orderPayment){
          if ($orderPayment->message == 'OK')
          foreach ($orderPayment->transactions as $order){
            
            $time = strtotime($order->created);
            $amount = floatval($order->amount/100);
              
            $status = '';
            switch ($order->status){
              case 'SUCCESS':
                $status = 'pagada';
                $total_month += $amount;
                break;
              case 'REFUSED':
                $status = 'rechazada';
                break;
              case 'ERROR':
                $status = 'error';
                break;
            }
            $date = date('d M H:i',$time);
            $respo_list[] = [
                'customer' => $order->customerExtId,
                'customer_name' => $order->holder,
                'sourceType' => $order->sourceType,
                'pan' => $order->pan,
                'date' => $date,
                'status' => $status,
                'amount' => number_format($amount, 2, ',', '.'),
                'currency' => ($order->currency == 978) ? '€' : '$',
                
                  ];
            
            
          }
        }
         
          $response = [
                'status'     => 'true',
                'total_month' => moneda($total_month),
                'comision' => moneda(paylandCost($total_month),true,1),
                'respo_list' => array_reverse($respo_list),
            ];
          if ($isAjax){
            return response()->json($response);
          }else {
            return $response;
          }
    } 
    /**
         * Get Limpieza index
         * 
         * @return type
         */
        public function lstOrders() {
          
          $year = $this->getActiveYear();
          
          $obj1  = $this->getMonthlyData($year);
          return view('backend/sales/payland', [
              'year'=>$year,
              'selected'    => $obj1['selected'],
              'selectedID'  => $obj1['selectedID'],
              'months_obj'=> $obj1['months_obj'],
              'months_label'=> $obj1['months_label'],
              ]

                  );
        }
        
        public function getSummary() {
          
          $year = $this->getActiveYear();
          $startYear = new Carbon($year->start_date);
          $endYear   = new Carbon($year->end_date);
          $today = date('Y-m-d');
          $months = [];
          
          while ($startYear<$endYear && $startYear<$today){
            $months[] = $startYear->format('Y-m');
            $startYear->addMonth();
          }
          $SUCCESS = $REFUSED = $ERROR = [];
          $count = [
                  'SUCCESS' => 0,
                  'REFUSED' => 0,
                  'ERROR' => 0,
              ];
          
          foreach ($months as $m){
            $SUCCESS[$m] = 0;
            $REFUSED[$m] = 0;
            $ERROR[$m]   = 0;
            $oItem = \App\PaylandsSummary::where('date_ym',$m)->first();
            if ($oItem){
              $SUCCESS[$m] = $oItem->p_success;
              $REFUSED[$m] = $oItem->p_refused;
              $ERROR[$m]   = $oItem->p_error;
              $aux = json_decode($oItem->counts,true);
              if (isset($aux['SUCCESS'])) $count['SUCCESS'] += $aux['SUCCESS'];
              if (isset($aux['REFUSED'])) $count['REFUSED'] += $aux['REFUSED'];
              if (isset($aux['ERROR'])) $count['ERROR'] += $aux['ERROR'];
            }
    
          }
          
        $totals = [
                'SUCCESS' => 0,
                'REFUSED' => 0,
                'ERROR' => 0,
            ];
        $result = [
            'SUCCESS' => [],
            'REFUSED' => [],
            'ERROR' => [],
        ];
        foreach ($SUCCESS as $r){ 
          $result['SUCCESS'][] = $r;
          $totals['SUCCESS'] += $r;
        }
        foreach ($REFUSED as $r){ 
          $result['REFUSED'][] = $r;
          $totals['REFUSED'] += $r;
        }
        foreach ($ERROR as $r){ 
          $result['ERROR'][] = $r;
          $totals['ERROR'] += $r;
        }
        
        $average = ($count['SUCCESS']) ? $totals['SUCCESS']/$count['SUCCESS'] : 0;
        $totalToday = 0;
        $response = [
                'status'     => 'true',
                'result' => $result,
                'today' => '--',
                'average' => number_format($average, 0, ',', '.'),
                'season' => moneda($totals['SUCCESS']),
                'count' => $count,
                'totals' => $totals,
                'comision' => ceil(paylandCost($totals['SUCCESS'])),
            ];
          

          return response()->json($response);
         
          
        }
        
    /**
         * Get Limpieza Objet by Year Object
         * 
         * @param Object $year
         * @return array
         */
        private function getMonthlyData($year) {
          
          
          $startYear = new Carbon($year->start_date);
          $endYear   = new Carbon($year->end_date);
          $diff      = $startYear->diffInMonths($endYear) + 1;
          $thisMonth = date('m');
          $arrayMonth = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
          $arrayMonthMin = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'];
          //Prepare objets to JS Chars
          $months_lab = '';
          $months_val = [];
          $months_obj = [];
          $aux = $startYear->format('n');
          $auxY = $startYear->format('y');
          $selected = null;
          for ($i=0; $i<$diff;$i++){
            $c_month = $aux+$i;
            if ($c_month == 13){
              $auxY++;
            }
            if ($c_month>12){
              $c_month -= 12;
            }
            
            if ($thisMonth == $c_month){
              $selected = "$auxY,$c_month";
              $selectedID = $auxY.'_'.$c_month;
            }
            
            $months_lab .= "'".$arrayMonth[$c_month-1]."',";
            //Only to the Months select
            $months_obj[] = [
                'id'    => $auxY.'_'.$c_month,
                'dateYear'    => '20'.$auxY,
                'month' => $c_month,
                'year'  => $auxY,
                'name'  => $arrayMonthMin[$c_month-1],
                't_pvp' => 0
            ];
          }
          
          foreach ($months_obj as $k=>$months){
            $tPVP = \App\Payments::whereYear('datePayment', '=', $months['dateYear'])
                    ->whereMonth('datePayment', '=', $months['month'])
                    ->whereIn('type',[2,3])
                    ->sum('import');
            if ($tPVP)
              $months_obj[$k]['t_pvp'] = $tPVP;
            
          }
          
          return [
              'year'        => $year->year,
              'selected'    => $selected,
              'selectedID'  => $selectedID,
              'months_obj'  => $months_obj,
              'months_label'=> $months_lab,
              ];
          
        }
        
      public function paymentsForms($token) {
        
        if ($token){

          $room = $name = $dates = $urlPayland = null;
          $oSite = null; 
          $payment = \App\PaymentOrders::where('token',$token)->first();
          if ($payment){
            $book = \App\Book::find($payment->book_id);
            $request_dni = false;
            $aSuplem = null;
            if ($book){
              if ($book->type_book !=1 && $book->type_book !=2 ){
                return redirect()->route('paymeny-error');
              }
            
              $payments = \App\Payments::where('book_id', $book->id)->first();
              $customer = $book->customer;
//              $customer->accepted_hiring_policies = null;
              if($customer && !$customer->accepted_hiring_policies){
                $request_dni = true;
                $room = '';
                $roomChGr = null;
                if ($book->room && $book->room->channel_group){
                  $room = $book->room->name;
                 
                  $roomType = \App\RoomsType::where('channel_group',$book->room->channel_group)->first();
                  if ($roomType){
                    $room = $roomType->title;
                  }
//                  $room .= ($book->type_luxury == 1) ? " Lujo" : " Estandar";
                }
                $name = $book->customer->name;

                $start = strtotime($book->start);
                $finish = strtotime($book->finish);

                $monthStart = date('n',$start);
                $monthFinish = date('n',$finish);
                if ($monthStart == $monthFinish){
                  $dates = date('d',$start).'-'.date('d',$finish);
                  $dates .= ' '.getMonthsSpanish($monthStart);
                } else {
                  $dates = date('d',$start).' '.getMonthsSpanish($monthStart);
                  $dates .= ' - '.date('d',$finish).' '.getMonthsSpanish($monthFinish);
                }
                
                // Suplementos
                $sSuplementos = new \App\Services\Bookings\ExtrasPurchase();
                $aSuplem = $sSuplementos->getExtrasData($book,$book->room->site_id);
              }
              $oSite = \App\Sites::siteData($book->room->site_id);
            }
            $urlPayland = $this->generateOrderPaymentBooking(
                    $payment->book_id,
                    $payment->cli_id,
                    $payment->cli_email,
                    $payment->description,
                    $payment->amount,
                    $payment->is_deferred,
                    $payment->site_id
                    );
            $has_fianza = true;
            $site = null;
            
            $background = assetV('img/riad/lockscreen.jpg');
            $aSite = $payment->getPaymentSite();
            $has_fianza = $aSite['has_fianza'];
            
             return view('frontend.bookStatus.paylandPay', 
                     [
                         'urlPayland' => $urlPayland,
                         'background'=>$background,
                         'room' => $room,
                         'urlSend'=>route('front.payments.dni',$token),
                         'urlSend2'=>route('front.payments.addExtrs',$token),
                         'dates' => $dates,
                         'name' => $name,
                         'has_fianza' => $has_fianza,
                         'site' => $oSite,
                         'request_dni' =>$request_dni,
                         'aSuplem' =>$aSuplem,
                         ]);
          }
          return redirect()->route('paymeny-error');
        }
        return redirect()->route('paymeny-error');
      }
    
   public function saveDni(Request $request,$token) {
      $dni = $request->input('dni', null);
      $accepted_hiring_policies = $request->input('accepted_hiring_policies', null);
      $accepted_bail_conditions = $request->input('accepted_bail_conditions', null);
      
     
      if (trim($dni) == '' || strlen(trim($dni))<7){
        return response()->json('Por favor, ingrese su DNI para continuar');
      }
      if ($accepted_hiring_policies !== "true"){
        return response()->json('Por favor, acepte las políticas de contratación');
      }
      
      if ($token){
          $payment = \App\PaymentOrders::where('token',$token)->first();
          if ($payment){
            $rules = $payment->getPaymentSite();
            $has_fianza = true;
            if ($rules){
              $has_fianza = $rules['has_fianza'];
            }
            if ($has_fianza && $accepted_bail_conditions !== "true"){
              return response()->json('Por favor, acepte las políticas de fianza');
            }
      
      
            $book = \App\Book::find($payment->book_id);
            $customer = $book->customer;
            if ($customer){
              $customer->dni = $dni;
              $customer->accepted_hiring_policies = date('Y-m-d H:i:s');
              if ($has_fianza)  $customer->accepted_bail_conditions = date('Y-m-d H:i:s');
              $customer->save();
              return response()->json('ok');
            }
            return response()->json('Reserva no encontrada');
          } else {
            return response()->json('Reserva no encontrada');
            echo 'Reserva no encontrada'; die;
          }
            return response()->json('Reserva no encontrada');
          echo 'Reserva no encontrada'; die;
      }

      return response()->json('Error: solicitud de pago no encontrada');
      echo 'Error: solicitud de pago no encontrada'; die;
   }
   
   /**
    * 
    */
   public function addExtrs(Request $request,$token) {
      $items = $request->input('items', null);
      if ($token){
          $payment = \App\PaymentOrders::where('token',$token)->first();
          if ($payment){
            $book = \App\Book::find($payment->book_id);
            if ($book){
              if ($items && count($items)>0){
              $selectExtr = [];
              foreach ($items as $v){
                $selectExtr[desencriptID($v['k'])] = $v['q'];
              }
              //-------------------------------------------------
              $order = [];
              $oExtras = \App\ExtraPrices::getDynamicToFront();
              $lstExtrs = [];
              $totalPay = 0;
              $description = '';
              if ($oExtras){
                foreach ($oExtras as $e){
                  if (isset($selectExtr[$e->id])){
                    $qty = $selectExtr[$e->id];
                    $amount = $qty*$e->price;
                    $order[] = [
                        'id'=>$e->id,
                        'qty'=>$qty,
                        'price'=>$amount
                    ];
                  }

                }
              }
              //--------------------------------------------------
              // Busco el ID de la ORDER para asignar los supl en el pago
              $BookOrders = \App\BookOrders::where('book_id',$book->id)
                      ->orderBy('id','DESC')->first();


              $BookData = \App\BookData::findOrCreate('payAction'.$BookOrders->id,$book->id);
              $BookData->content = json_encode(['acc'=>'buySupl','data'=>$order]);
              $BookData->save();
              //--------------------------------------------------
            }
            return response()->json('ok');
          } else {
            return response()->json('Reserva no encontrada');
            echo 'Reserva no encontrada'; die;
          }
      }
      }

      return response()->json('Error: solicitud de pago no encontrada');
      echo 'Error: solicitud de pago no encontrada'; die;
   }
   
   public function getPaymentByType(Request $request) {
      $type = $request->input('type','link');
      if ($type == 'form'){
        return $this->payment($request);
      }
      if ($type == 'link'){
        $subject = '';
        $booking = $request->input('booking', null);
        $amount = $request->input('amount', null);
        if ($booking && $amount){
          $aux = explode('-', $booking);
          if (is_array($aux) && count($aux) == 2){
            $bookingID = desencriptID($aux[1]);
            $clientID = desencriptID($aux[0]);
            $is_deferred = $request->input('is_deferred', null); //fianza
            $urlPay = $this->generateOrder($amount,$subject,$bookingID,$is_deferred);
            if ($urlPay){
              $urlPay = get_shortlink($urlPay);
              return $this->getPaymentText($urlPay,$bookingID,$amount);
            }
            
          }
        }

      }
      return 'error';
    }
           
    public function thansYouPaymentDeferred($key_token)
    {
      $bookOrder = \App\BookDeferred::where('key_token',$key_token)->whereNull('paid')->first();
      if ($bookOrder){
        $bookOrder->paid = true;
        $bookOrder->save();
        $totalPayment = ($bookOrder->amount/100);
        $amount = $totalPayment.' €';
        \App\BookLogs::saveLogStatus($bookOrder->book_id,null,$bookOrder->cli_email,"Confirmación de Fianza de $amount ($key_token)");

        $book = \App\Book::find($bookOrder->book_id);
        if ($book){
          $subject = translateSubject('Confirmación de Fianza',$book->customer->country);

          $subject .= ' en '.config('app.name');
          $this->sendEmail_confirmDeferrend($book,$subject,$totalPayment);
        }
      }
              ?>
          <div style="text-align: center;margin-top: 2em; color: #fff;">
            <h2 style="line-height: 1;">
              ¡Muchas gracias por confiar en nosotros!
            </h2>
            <br>
            <p style="line-height: 1;">
              Te enviaremos un email con la confirmación de tu fianza.<br><br>
              Un saludo
            </p>
          </div>
          <?php
          return '';
        
    }
    
        
    /**
     * Payment by MoTo
     * @param Request $req
     */
    public function processPaymentMoto(Request $req)
    {
        $order = $req->input('order');
        $uuid = $order['uuid'];
          
        if (!$uuid || trim($uuid) == '') return 'Orden vacia';
        
        $paylandClient = $this->getPaylandApiClient();
        $orderPayment  = $paylandClient->getOrder($uuid);
        if ($orderPayment && $orderPayment->message == 'OK'){
            $order = $orderPayment->order;
        } else {
            return 'Orden No Encontrada';
        }
        
        $uuid = $order->uuid;
        $created = $order->created;
        $amount = $order->amount;
        $refunded = $order->refunded;
        $paid = $order->paid; // true
        $comment = $order->additional;
        $bookID = intval($order->customer);
        
        
//        $created = $order['created'];
//        $amount = $order['amount'];
//        $refunded = $order['refunded'];
//        $paid = $order['paid']; // true
//        $comment = $order['additional'];
//        $bookID = intval($order['customer']);
        
        if ($paid === TRUE && $bookID>0){
            if ($refunded>0)  $amount = $amount-$refunded;
            $amount = $amount/100;
            
            $alreadyExist = false;
            $oPaym = \App\Payments::where('uuid',$uuid)->first();
            if ($oPaym){
                $alreadyExist = true;
                $bookID = $oPaym->book_id;
            } else {
                $oPaym = new \App\Payments();
                $oPaym->book_id = $bookID;
                $oPaym->datePayment = date('Y-m-d', strtotime($created));
                $oPaym->comment = $comment;
                $oPaym->type = 2;
                $oPaym->uuid = $uuid;
            }

            $oPaym->import = $amount;
            $oPaym->save();
            
            if (!$alreadyExist){
                $book = \App\Book::find($bookID);
                if (in_array($book->type_book, [1,9,11,99])){
                  $book->type_book = 2;
                  $book->save();
                }

                if ($book->customer->send_notif){
                  $subject = translateSubject('RECIBO PAGO RESERVA',$book->customer->country);
                  $subject .= ' '. $book->customer->name;
                  $this->sendEmail_confirmCobros($book,$subject,$amount,$book->customer->email_notif);
                }
            }
            
            return 'Pago guardado';
            
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Book;
use App\Repositories\CachedRepository;
use App\Rooms;
use App\Services\PaylandService;
use App\SizeRooms;
use App\Years;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppController extends Controller
{
    private $cachedRepository;
    private $paylandClient;
    const SANDBOX_ENV = "/sandbox";

    public function __construct(CachedRepository $cachedRepository)
    {
      $endpoint = config('app.payland.endpoint');
      $this->cachedRepository = $cachedRepository;
      $endPoint               = (config('app.payland.enviromment') == "dev") ? $endpoint . self::SANDBOX_ENV : $endpoint;
      $paylandConfig          = [
          'endpoint'  => $endPoint,
          'api_key'   => config('app.payland.key'),
          'signarute' => config('app.payland.signature'),
          'service'   => config('app.payland.service')
      ];
      $this->paylandClient    = new PaylandService($paylandConfig);
    }

    protected function getPaylandApiClient()
    {
        return $this->paylandClient;
    }

    /**
     * @return mixed
     */
    protected static function getActiveYear()
    {
      $activeYear = null;
      $idYear = getYearActive();
      if (is_numeric($idYear) && $idYear>0)
        $activeYear = Years::find($idYear);
      if (!$activeYear){
        $activeYear = Years::where('year', date('Y'))->first();
        if ($activeYear) setYearActive($activeYear->id);
      }
      return $activeYear;
    }
    /**
     * @return mixed
     */
    protected static function getYearData($year)
    {
        $activeYear = Years::where('year', $year)->first();
        return $activeYear;
    }

    public function payBook($id, $payment,$comment="Pago desde Payland")
    {
      $book = \App\Book::find($id);
      $realPrice = ($payment / 100);

      $payment = new \App\Payments();

      $date                 = Carbon::now()->format('Y-m-d');
      $payment->book_id     = $book->id;
      $payment->datePayment = $date;
      $payment->import      = $realPrice;
      $payment->comment     = $comment;
      $payment->type        = 2;
      $payment->save();

      $data['concept']     = $payment->comment;
      $data['date']        = $date;
      $data['import']      = $realPrice;
      $data['comment']     = $payment->comment;
      $data['typePayment'] = 2;
      $data['type']        = 0;

      if ($book->type_book != 2) {
        // WEBDIRECT
        if ($book->is_fastpayment == 1){
          $book->type_book = 11;
          $book->is_fastpayment = 0;
          $book->save();
        } else $book->changeBook(2, "", $book);
        
      }
      LiquidacionController::addBank($data);
      return true;
      
    }

    public function generateOrderPayment($params)
    {
        $response = $params;
        if (isset($response['_token'])) unset($params['_token']);
        $customer                    = \App\Customers::find($response['customer_id']);
        $response['amount']          = ($response['amount']) * 100;
        $response['customer_ext_id'] = $customer->email;
        $response['operative']       = "AUTHORIZATION";
        $response['secure']          = false;
        $response['signature']       = config('app.payland.signature');
        $response['service']         = config('app.payland.service');
        $response['description']     = "COBRO RESERVA CLIENTE " . $customer->name;
        $response['url_ok']          .= "/" . $response['amount'];
        $response['url_ko']          .= "/" . $response['amount'];
        $orderPayment  = $this->paylandClient->payment($response);
        $urlToRedirect = $this->paylandClient->processPayment($orderPayment->order->token);
        return $urlToRedirect;

    }

    /**
     * Create link to new Payland
     * 
     * @param type $bookingID
     * @param type $clientID
     * @param type $client_email
     * @param type $description
     * @param type $amount
     * @return type
     */
    public function generateOrderPaymentBooking($bookingID,$clientID,$client_email,$description,$amount,$is_deferred=false,$siteID=null){
          
      $key_token = md5($bookingID.'-'.time().'-'.$clientID);
      $type = $is_deferred ? 'DEFERRED' : 'AUTHORIZATION';
      $amount = ($amount * 100); // esto hay que revisar
      $response['_token']          = null;
      $response['amount']          = $amount;
      $response['customer_ext_id'] = $client_email;
//      $response['operative']       = "AUTHORIZATION";
//      $response['operative']       = "DEFERRED";
      $response['operative']       = $type;
      $response['secure']          = false;
      $response['signature']       = config('app.payland.signature');
      $response['service']         = config('app.payland.service');
      $response['description']     = $description;
      $response['url_ok']          = route('payland.thanks.payment',$key_token);
      $response['url_ko']          = route('payland.error.payment',$key_token);
      $response['url_post']        = route('payland.process.payment',$key_token);
      
      if ($is_deferred)   $response['url_ok']  = route('payland.thanks.deferred',$key_token);
      $paylandClient = $this->getPaylandApiClient();
      $orderPayment  = $paylandClient->payment($response);

      if ($is_deferred)
        $BookOrders = new \App\BookDeferred();
      else 
        $BookOrders = new \App\BookOrders();
      
      $BookOrders->book_id = $bookingID;
      $BookOrders->cli_id = $clientID;
      $BookOrders->cli_email = $client_email;
      $BookOrders->subject = $description;
      $BookOrders->key_token = $key_token;
      $BookOrders->order_uuid = $orderPayment->order->uuid;
      $BookOrders->order_created = $orderPayment->order->created;
      $BookOrders->amount = $orderPayment->order->amount;
      $BookOrders->refunded = $orderPayment->order->refunded;
      $BookOrders->currency = $orderPayment->order->currency;
      $BookOrders->additional = $orderPayment->order->additional;
      $BookOrders->service = $orderPayment->order->service;
      $BookOrders->status = $orderPayment->order->status;
      $BookOrders->token = $orderPayment->order->token;
      $BookOrders->transactions = json_encode($orderPayment->order->transactions);
      $BookOrders->client_uuid = $orderPayment->client->uuid;
      $BookOrders->is_deferred = $is_deferred;
      $BookOrders->site_id = $siteID;
      $bo_id = $BookOrders->save();


      $urlToRedirect = $paylandClient->processPayment($orderPayment->order->token);
      return $urlToRedirect;

    }

     /**
     * Create link to new Payland
     * 
     * @param type $bookingID
     * @param type $clientID
     * @param type $client_email
     * @param type $description
     * @param type $amount
     * @return type
     */
    public function generateOrderPaymentForfaits($bookingID,$orderID,$client_email,$description,$amount,$lastOrderId=null,$forfats_id=null){
          
      $key_token = md5($bookingID.'-'.time().'-'.$orderID);

      $amount = ($amount * 100); // esto hay que revisar
      $response['_token']          = null;
      $response['amount']          = $amount;
      $response['customer_ext_id'] = $client_email;
      $response['operative']       = "AUTHORIZATION";
      $response['secure']          = false;
      $response['signature']       = config('app.payland.signature');
      $response['service']         = config('app.payland.service');
      $response['description']     = $description;
      $response['url_ok']          = route('payland.thanks.forfait',$key_token);
      $response['url_ko']          = route('payland.error.forfait',$key_token);
      $response['url_post']        = route('payland.process.forfait',$key_token);
      $paylandClient = $this->getPaylandApiClient();
      $orderPayment  = $paylandClient->payment($response);


      $BookOrders = new \App\Models\Forfaits\ForfaitsOrderPayments();
      $BookOrders->book_id = $bookingID;
      $BookOrders->order_id = $orderID;
      $BookOrders->cli_email = $client_email;
      $BookOrders->subject = $description;
      $BookOrders->key_token = $key_token;
      $BookOrders->order_uuid = $orderPayment->order->uuid;
      $BookOrders->order_created = $orderPayment->order->created;
      $BookOrders->amount = $orderPayment->order->amount;
      $BookOrders->refunded = $orderPayment->order->refunded;
      $BookOrders->currency = $orderPayment->order->currency;
      $BookOrders->additional = $orderPayment->order->additional;
      $BookOrders->service = $orderPayment->order->service;
      $BookOrders->status = $orderPayment->order->status;
      $BookOrders->token = $orderPayment->order->token;
      $BookOrders->transactions = json_encode($orderPayment->order->transactions);
      $BookOrders->client_uuid = $orderPayment->client->uuid;
      $BookOrders->last_item_id = $lastOrderId;
      $BookOrders->forfats_id = $forfats_id;
      $bo_id = $BookOrders->save();


      $urlToRedirect = $paylandClient->processPayment($orderPayment->order->token);
      return $urlToRedirect;

    }

    /**
     * Confirm a DEFERRED with the amount by post
     * @param Request $request
     * @return type
     */
     public function cobrarFianza(Request $request) {
       
       $bookID = $request->input('id',null);
       $amount = $request->input('amount',null);
       if (!$bookID){
        return [
          'status'   => 'danger',
          'response' => "No existe la Reserva asociada."
        ];
       }
       
       $bookID = $request->input('id',null);
       
       $book = Book::find($bookID);
       if(!$book){
        return [
          'status'   => 'danger',
          'response' => "Reserva no encontrada."
        ];
       }
       
       $Order = \App\BookDeferred::where('book_id',$bookID)
              ->where('is_deferred',1)->where('paid',1)->first();
      
      if (!$Order){
        return [
          'status'   => 'danger',
          'response' => "Registro de Fianza no encontrado."
        ];
        
      }
      if ($Order->was_confirm){
        return [
          'status'   => 'danger',
          'response' => "Registro de Fianza ya cobrado."
        ];
        
      }
      $uuid = $Order->order_uuid;
      $amount = $amount*100;
      
      $rest = ($Order->amount>0) ? $Order->amount-$Order->payment : 0;
      
      if (!$amount || !is_numeric($amount) || $amount<0 || $amount > $rest ){
         return [
          'status'   => 'danger',
          'response' => "El monto no debe superar a ".($rest/100).'€.'
        ];
      }
//        $amount = $Order->amount;
      
      $response = array();
      $response['secure']          = false;
      $response['signature']       = config('app.payland.signature');
      $response['service']         = config('app.payland.service');
      $response['order_uuid']      = $uuid;
      $response['amount']          = $amount;

      $paylandClient = $this->getPaylandApiClient();
      $orderPayment  = $paylandClient->confirmationPayment($response);
      if (isset($orderPayment)){
        if (is_string($orderPayment)){
          return [
            'status'   => 'danger',
            'response' => "Pago fallado: ".$orderPayment
          ];
        }
        if ($orderPayment->message == 'OK'){
          $Order->payment = $amount;
          $Order->was_confirm = true;
          $Order->save();
          $msg = 'Se han cobrado '.($amount/100).'€';
          /** @ToDo enviar mensaje al cliente*/
          \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'confirmationPayment','Cobro de la fianza',$msg);
          
          return [
            'status'   => 'success',
            'response' => "Cobro de Fianza realizado."
          ];
        }
        
      }
      
      return [
          'status'   => 'danger',
          'response' => "Pago fallado."
        ];
      
    }
    
    /**
     * Create a new DEFERRED pay
     * @param type $book
     * @return type
     */
    public function createPaymentFianza($book){
    
    if ($book){
      $token = md5(encrypt($book->id.'&fianzas'));
      $urlPay = getUrlToPay($token);
      $client = $book->customer()->first();
      $description = "FIANZA CLIENTE " . $client->name;
      $client_email = 'no_email';
      if ($client && trim($client->email)){
        $client_email = $client->email;
      }

      $PaymentOrders = new \App\PaymentOrders();
      $PaymentOrders->book_id = $book->id;
      $PaymentOrders->cli_id = $client->id;
      $PaymentOrders->cli_email = $client_email;
      $PaymentOrders->amount = 300;
      $PaymentOrders->status = 0;
      $PaymentOrders->token = $token;
      $PaymentOrders->description = $description;
      $PaymentOrders->is_deferred = true;
      $PaymentOrders->save();
      return $urlPay;
    }
    return null;
  }
  
  function get_404() {return view('404');}
  function staticToken() {return csrf_token();}
  function calculateBook(Request $request) {
    $data = ['name'=>'','date'=>'','phone'=>'','email'=>'','pax'=>1,'site_id'=>null,'cr_id'=>null];
//    $param = $request->input('cr_id',null);
//    if ($param){
//      $usrData = \App\CustomersRequest::find($param);
//      if ($usrData){
//        $usrData->user_id = $request->input('userID',null);
//        $usrData->comment = $request->input('comments',null);
//        $usrData->save();
//        $data['name'] = $usrData->name;
//        $data['phone'] = $usrData->phone;
//        $data['email'] = $usrData->email;
//        $data['pax'] = $usrData->pax;
//        $data['site_id'] = $usrData->site_id;
//        $data['date'] = date('d M, y', strtotime($usrData->start)).' - '.date('d M, y', strtotime($usrData->finish));
//        $data['cr_id'] = $param;
//      }
//    }
            
    return view('backend.planning.calculateBook.form',$data);
  }
  function no_allowed() {return view('no-allowed');}
  function importFromUrl() { \Artisan::call('SafeBox:asignAndSend'); }
  function admin() {
    $user = \Auth::user();
    if ($user->role == "propietario") {
      $room = \App\Rooms::where('owned', $user->id)->first();
      if ($room)
        return redirect('/admin/propietario/' . $room->nameRoom);
      return redirect('/admin/propietario/');
    } else {
      return redirect('/admin/reservas');
    }
  }
  function partee_checkHuespedes() {
    $partee = new \App\Services\ParteeService();
    $partee->conect();
    $id = 0;
    $partee->getCheckHuespedes($id);
    dd($partee);
  }
  function parteeCheckStatus() {
    $partee = new \App\Services\ParteeService();
    $partee->conect();
    $partee->getCheckStatus($id);
    dd($partee);
  }
  function parteeGetPDF() {
    $partee = new \App\Services\ParteeService();
    $partee->conect();
    $partee->getParteePDF($id);
    dd($partee);
  }
}
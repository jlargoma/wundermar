<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RoomsType;
use App\RoomsPhotos;
use App\Rooms;
use App\Book;
use App\Http\Requests;
use App\Services\Bookings\GetRoomsSuggest;

class ApiController extends AppController
{
  private $discount_1 = 0.15;
  private $discount_2 = 0.1;
  private $siteID = 1;
  private $customerToken = null;
  
  public function __construct(){
    $tokens = [
      1=>'zdWIiOiIxMnR5cCI6IkpZSI6IkpvaG4', //'riad.virtual'
     // 2=>'CsquvpHowa1Zl0oAIeyARVWFFSPCJb1H4VRAyBMf-DISABLE', //'hotelrosadeoro.es' => 
      5=>'eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ' //'siloeplaza.es' => 
    ];
    if (isset($_SERVER['HTTP_TOKEN_API']))
      $this->siteID = array_search($_SERVER['HTTP_TOKEN_API'], $tokens);
    else $this->siteID = -1;
  }
  
  public function index()
    {
      die('inicio');
      //  return view('api/index');
    }

    public function getItemsSuggest(Request $request) {

      if ($this->siteID>0){
        $pax = $request->input('pax',null);
        $date_start = $request->input('start',null);
        $date_finish = $request->input('end',null);
        $usr = $request->input('usr',null);
        $oGetRoomsSuggest = new GetRoomsSuggest();
        $oGetRoomsSuggest->set_siteID($this->siteID);
        $response = $oGetRoomsSuggest->getItemsSuggest($pax,$date_start,$date_finish);
        if (count($response)>0) return response()->json($response);
        return response('empty');
      } else {
        return response('empty');
      }
    }
    
    
    
    public function getExtasOpcion(Request $request) {
      return response()->json([]);
      $extras = \App\ExtraPrices::getDynamicToFront();
      
      $pax = intval($request->input('pax',null));
      if ($pax<1){
        return response()->json([]);
      }
      $date_start = $request->input('start',null);
      $date_finish = $request->input('end',null);
      $nigths = calcNights($date_start,$date_finish);
      if ($nigths<1) $nigths = 1;
      
      $response = [];
      if ($extras){
        foreach ($extras as $e){
          $price = $e->price * $nigths;
          $name  = $e->name;
          $info = null;
          if ($e->type == 'parking'){
            $info = \App\Settings::getContent('widget_extras_paking','es',$this->siteID);
          }
          if ($e->type == 'breakfast'){
            $price *= $pax;
            $info = \App\Settings::getContent('widget_extras_breakfast','es',$this->siteID);
//            $name .= ' ( '.moneda($e->price). ' c/u )';
          }
          
          $response[] = [
              'k'=> encriptID($e->id),
              'n'=> $name,
              'i'=> clearTitle($e->name),
              'p' => $price,
              'info'=>$info
          ];
        }
      }
      return response()->json($response);
    }
    
       
    private function getRoomsWithAvail($startDate,$endDate,$pax,$channel_group){
      $book = new Book();
      $oRooms = Rooms::where('channel_group',$channel_group)
              ->where('maxOcu','>=', $pax)->where('state',1)->get();
      if ($oRooms){
        foreach ($oRooms as $room){
          if ($book->availDate($startDate, $endDate, $room->id)){
            return $room;
          }
        }
      }
      return null;
    }
    
    public function finishBooking(Request $request) {

//      return response()->json(['data'=>'Sistema en mantenimiento..'],401);
      if ($this->siteID<1)  return null;
      
      $usr = $request->input('usr',null);
      $pax = $request->input('pax',null);
      $date_start = $request->input('start',null);
      $date_finish = $request->input('end',null);
      $selected = $request->input('item',null);
      $code = null;
      $comments = '';
      if (isset($selected['code'])){
        $code = trim($selected['code']);
      }

      $rate = isset($selected['rt']) ? $selected['rt'] : 2;

      if (isset($selected['rt_text'])){
        $comments = trim($selected['rt_text']);
      }
      $roomTypeID = desencriptID($code);
      
      $aborrar = [];
      $roomType = \App\RoomsType::where('id',$roomTypeID)
            ->where('status',1)                
            ->first();
    
      /***************************/
      $nigths = calcNights($date_start,$date_finish);
      
      $oGetRoomsSuggest = new GetRoomsSuggest();
      $oGetRoomsSuggest->set_siteID($this->siteID);
      $roomData = $oGetRoomsSuggest->getRoomsPvpAvail($date_start,$date_finish,$pax,$roomType->channel_group);
      $minStay = isset($roomData['minStay']) ? $roomData['minStay'] : 0;
      $price = isset($roomData['prices']) ? $roomData['prices'] : 0;
      
      if ($nigths<$minStay){
        $response =  'Estancia mínima: '.$minStay.' Noches';
        return response()->json(['data'=>$response],401);
      }
      if (!isset($price['pvp']) || $price['pvp']<1){
        $response =  'Ocurrió un error a procesar su reserva';
        return response()->json(['data'=>$response],401);
      }
      $widget_observations = \App\Settings::getContent('widget_observations','es',$this->siteID);
      /*******************************/
      if ($roomType){
        $oRoom = $this->getRoomsWithAvail($date_start,$date_finish,$pax,$roomType->channel_group);
        $customer = [
            'name'  => $usr['c_name'],
            'email' => $usr['c_email'],
            'phone' => $usr['c_phone'],
            'token' => isset($usr['token']) ? $usr['token'] : time(),
        ];
        $ext = isset($selected['ext']) ? $selected['ext'] : [];
        $response = $this->createBooking($date_start,$date_finish,$customer,$oRoom,$comments,$pax,$ext,$rate);
        if ($response){
          return response()->json(['data'=>$response,'c_token'=>$this->customerToken,'observations'=>$widget_observations],200);
        }
      }
      $response =  'No hay información disponible';
      return response()->json(['data'=>$response,'c_token'=>$this->customerToken,'observations'=>$widget_observations],401);
     
    }
    
    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function createBooking($date_start,$date_finish,$cData,$room,$comments,$pax,$Extras,$rate)
    {
      $alreadyExist = null;

      if ($cData && isset($cData['token'])){ //check if is a upd
        $token = $cData['token'];
        $customer = \App\Customers::where('api_token',$token)->first();
        if ($customer && $customer->api_token === $token){
          $alreadyExist = $customer->id;
          $customer->name    = $cData['name'];
          $customer->email   = $cData['email'];
          $customer->phone   = $cData['phone'];
        }
      }
      if (!$alreadyExist){
        //createacion del cliente
        $customer          = new \App\Customers();
        $customer->name    = $cData['name'];
        $customer->email   = $cData['email'];
        $customer->phone   = $cData['phone'];
        
        $customer->user_id = 98;
        if (!$customer->save()) return FALSE;
        
        \App\CustomersRequest::removeIfExist($cData['email'],$this->siteID);
      }

      $customer->api_token= encriptID($customer->id).bin2hex(time());
      $customer->save();
      
        if ($alreadyExist){
          //busco de la reserva
          $book   = \App\Book::where('customer_id',$customer->id)->first();
          if (!$book || $book->customer_id != $customer->id){
            $book = new \App\Book();  //Creacion de la reserva
          } else {
            $this->removeOldExtras($book);
          }
        } else {
          //Creacion de la reserva
          $book   = new \App\Book();
        }
        
        $book->room_id       = $room->id;
        $book->start         = $date_start;
        $book->finish        = $date_finish;
//        $book->type_book     = 99;
        $book->comment       = isset($cData['c_observ']) ? $cData['c_observ']: '';
        $book->pax           = $pax;
        $book->real_pax      = $pax;
        $book->nigths        = calcNights($date_start, $date_finish);
        $book->PVPAgencia    = 0;
        $book->is_fastpayment = 1;//1;
        $book->user_id = 98;
        $book->agency = 31;
        $book->customer_id = $customer->id;
        if (!$alreadyExist) $book->type_book     = 99;

        if (!$book->save())  return FALSE;
 
        $totales = $book->getPriceBook($date_start,$date_finish,$room->id);
        $book->cost_limp = $totales['cost_limp'];
        /**********************************/
        $meta_price = $room->getRoomPrice($date_start, $date_finish,$book->pax);
        
        $pvp = round($meta_price['pvp']);
        $pvp_total = round($meta_price['pvp_init']+$meta_price['price_limp']);
        
        $discount = 15;
        $comments .= PHP_EOL."Descuento: ".($discount+$meta_price['discount']).'%';
        // promociones tipo 7x4
        $book->promociones = 0;
        if ($meta_price['promo_pvp']>0){
          $comments .= PHP_EOL."Promoción ".$meta_price['promo_name'];
          $book->promociones = $meta_price['promo_pvp'];
          $book->book_owned_comments = 'Promoción '.$meta_price['promo_name'].': '. moneda($meta_price['promo_pvp'],true,2);
        }
        // promociones tipo 7x4  
        
        $book->real_price = $pvp_total;
        $book->total_price= $pvp;
        $book->cost_apto   = $totales['cost'];
        $book->extraCost   = $totales['cost_extra_fixed'];
        $book->sup_limp    = $totales['limp'];
        $book->cost_limp   = $totales['cost_limp'];
        $book->cost_total  = $book->cost_total + $totales['cost_extra_dynamic'];
        $book->total_ben  = $book->total_price - $book->cost_total;
        
        $book->book_comments = $comments .PHP_EOL.'Precio publicado: '.moneda($pvp);
        if ($book->save()) {
          $this->setExtras($Extras,$book);
          $book->setMetaContent('price_detail', serialize($meta_price));
          $amount = ($book->total_price / 2);
          $client_email = 'no_email';
          if ($customer->emaill && trim($customer->emaill)) {
            $client_email = $customer->emaill;
          }
          //check if already exist another FastPayment to the user
          if ($client_email) {
            $clientExist = Book::select('book.*')->join('customers', function ($join) use($client_email) {
                      $join->on('book.customer_id', '=', 'customers.id')
                              ->where('customers.email', '=', $client_email);
                    })->where('type_book', 99)->get();
            if ($clientExist) {
              foreach ($clientExist as $oldBook) {
                if ($book->id != $oldBook->id) {
                  $oldBook->type_book = 0;
                  $oldBook->save();
                }
              }
            }
          }
//          $book->sendAvailibilityBy_dates($book->start, $book->finish);
          $this->customerToken = $customer->api_token;
          //Prin box to payment
          $description = "COBRO RESERVA CLIENTE " . $book->customer->name;
//          $urlPayland = 'http://admin.riadpuertasdelalbaicin.com/mantenimiento.html';
//          $urlPayland = route('widget.thanks.payment','123123123123');
          $oPaylands = new \App\Models\Paylands();
          $urlPayland = $oPaylands->generateOrderPaymentWidget(
                  $book->id, $book->customer->id, $client_email, $description, $amount
          );

          return $urlPayland;
      }
      
    }
    
    
    
    
    public function changeCustomer(Request $request) {
      // c_name c_mail c_phone c_tyc 
      $cData = $request->input('usr',null);
      if ($cData){
        $token = $cData['token'];
        $oCustomer = \App\Customers::where('api_token',$token)->first();
        if ($oCustomer && $oCustomer->api_token === $token){
          
          if (!(isset($cData['c_name']) && isset($cData['c_email']) && isset($cData['c_phone']))){
            return response()->json(['success'=>false,'data'=>'Los campos son requeridos'],401);
          }
          
          $name = trim($cData['c_name']);
          $email = trim($cData['c_email']);
          $phone = trim($cData['c_phone']);
          
          if (strlen($name)<1 || strlen($name)>125)
            return response()->json(['success'=>false,'data'=>'El nombre es requerido.'],401);
          
          if (strlen($email)<1 || strlen($email)>125)
            return response()->json(['success'=>false,'data'=>'La dirección de correo es requerida.'],401);
            
          if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE)
            return response()->json(['success'=>false,'data'=>'La dirección de correo es requerida.'],401);
          
          if (strlen($phone)<1 || strlen($phone)>125)
            return response()->json(['success'=>false,'data'=>'El teléfono es requerido.'],401);
          
          
          if ($name)  $oCustomer->name    = $name;
          if ($email) $oCustomer->email   = $email;
          if ($phone) $oCustomer->phone   = $phone;
          
          $oCustomer->api_token= encriptID($oCustomer->id).bin2hex(time());
          $oCustomer->save();
          
          if (isset($cData['c_observ'])){
            $booking = Book::where('customer_id',$oCustomer->id)->first();
            if ($booking){
              $booking->comment = $booking->comment."\n".$cData['c_observ'];
              $booking->save();
            }
          }
          
          return response()->json(['success'=>true,'data'=>$oCustomer->api_token],200);
        }
      }
        
      return response()->json(['success'=>false,'data'=>'Acceso denegado'],401);
    }
    
    
    
    
    /***********************************************/
    
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                $response['status'] = "Error";
                $response['data']['errors'] = "invalid credentials. Please check your email or password";
                return response()->json($response, 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            $response['status'] = "Error could_not_create_token";
            return response()->json($response, 500);
        }

        if ( Auth::attempt($credentials) ) {
            $tokenAux = compact('token');
            $response['status'] = "OK";
            $response['data']['user'] = Auth::user();
            $response['data']['token'] = $tokenAux['token'];
        }
        // all good so return the token
        return response()->json($response);
    }

    public function view() {
      return view('frontend.booking.view');
    }

    
    private function removeOldExtras($book){
      $extras = \App\ExtraPrices::getDynamicToFront();
      $extrasID = [];
      if ($extras){
        foreach ($extras as $e){
          $extrasID[] = $e->id;
        }
        \App\BookExtraPrices::where('book_id',$book->id)
                ->whereIN('extra_id',$extrasID)
                ->delete();
      }
    }
    private function setExtras($aExtrs,$book){
      $qty = 1;
      $total_price = 0;
      if (count($aExtrs)>0){
        $aExtrsID = [];
        foreach ($aExtrs as $e){
          if ($e['s'] == "true") $aExtrsID[] = desencriptID($e['k']);
        }
        $extras = \App\ExtraPrices::getDynamicToFront();
        $bookingExtras = [];
        if ($extras){
          foreach ($extras as $oExtra){
            if (in_array($oExtra->id, $aExtrsID)){
              
              if ($oExtra->type == 'breakfast'){
                $qty = ($book->nigths*$book->pax);
              } else {
                $qty = $book->nigths;
              }
//              $qty = $book->nigths;
          
              $oBookExtra = new \App\BookExtraPrices();
              $oBookExtra->book_id = $book->id;
              $oBookExtra->extra_id = $oExtra->id;
              $oBookExtra->qty = $qty;
              $oBookExtra->price = $oExtra->price*$qty;
              $oBookExtra->cost = $oExtra->cost*$qty;
              $oBookExtra->status = 1;
              $oBookExtra->vdor = null;
              $oBookExtra->type = $oExtra->type;
              $oBookExtra->fixed = 0;
              $oBookExtra->deleted = 0;
              $oBookExtra->save();
              //Increase the booking price
              $total_price += $oExtra->price*$qty;
            }
          }
          $book->total_price += $total_price;
          $book->save();
        }
      }
      
    }
    
    function rvaSuplementos(Request $request){
      $tkn = $request->input('tkn',null);
     
      $sSuplementos = new \App\Services\Bookings\ExtrasPurchase();
      $bID = $sSuplementos->getBookingID($tkn);
      if ($bID<1) return response()->json('empty');
      
      $oBkg = Book::find($bID);
      if (!$oBkg) return response()->json('empty');
      return response()->json($sSuplementos->getExtrasData($oBkg,$this->siteID));
      
    }
    
     public function rvaSuplementosFinish(Request $request) {

//      return response()->json(['data'=>'Sistema en mantenimiento..'],401);
      $tkn = $request->input('tkn',null);
     
      $sSuplementos = new \App\Services\Bookings\ExtrasPurchase();
      $bID = $sSuplementos->getBookingID($tkn);
      $response = ['status' => "Error",'nsg'=>''];
      if ($bID<1){
        $response['nsg'] = "Token inválido Err01";
        return response()->json($response, 401);
      }
      
      $oBkg = Book::find($bID);
      if (!$oBkg){
        $response['nsg'] = "Token inválido Err02";
        return response()->json($response, 401);
      }
      
      $selectExtr = $tkn = $request->input('extr',null);
      
      if (!is_array($selectExtr) || count($selectExtr)<1){
        $response['nsg'] = "Token inválido Err03";
        return response()->json($response, 401);
      }
        
      $aux = [];
      foreach ($selectExtr as $k=>$v){
        $aux[desencriptID($v['key'])] = $v['qty'];
      }
      $selectExtr = $aux;
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
            $totalPay += $amount;
            
            $description .= $qty.' Supl. '.$e->name.': '.$amount.'<br/>';
          }
         
        }
      }
      //-------------------------------------------------
            
      if (count($order)<1){
        $response['nsg'] = "No hay registros cargados";
        return response()->json($response, 204);
      }
      if ($totalPay<1){
        $response['nsg'] = "No hay registros cargados";
        return response()->json($response, 204);
      }
      
      $oPaylands = new \App\Models\Paylands();
      $urlPayland = $oPaylands->generateOrderPaymentWidget(
        $oBkg->id, $oBkg->customer->id, $oBkg->customer->email, $description, $totalPay,
              false, null, true
      );
          
      //--------------------------------------------------
      // Busco el ID de la ORDER para asignar los supl en el pago
      $BookOrders = \App\BookOrders::where('book_id',$oBkg->id)
              ->orderBy('id','DESC')->first();
      
      
      $BookData = \App\BookData::findOrCreate('payAction'.$BookOrders->id,$oBkg->id);
      $BookData->content = json_encode(['acc'=>'buySupl','data'=>$order]);
      $BookData->save();
      //--------------------------------------------------
      
      
      return response()->json(['data'=>$urlPayland],200);
     
    }
    
}

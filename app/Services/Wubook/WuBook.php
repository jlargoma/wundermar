<?php
namespace App\Services\Wubook;
use App\Services\Wubook\XML_RPC;
use App\Services\Wubook\Config as WBConfig;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use App\Models\DailyPrices;

class WuBook{
 
    public $response;
    public $responseCode;
    public $channels;
    private $price_plan;
    private $rplan;
    protected   $token;
    protected   $iCode;
    protected   $WBConfig;
    
    public function __construct()
    {
      $this->iCode = 9999999999; //getPropID
      $this->token = "9999999999";
      $this->price_plan = 99999;
      $this->WBConfig = new WBConfig();
    }
    
    private function call($method,$param) {
      $response = XML_RPC::CallMethod("https://wired.wubook.net/", $method, $param);
      if ($response){
        if (isset($response->params->param->value->array->data)){
          $response = $response->params->param->value->array->data;
          if (isset($response->value)){
            if (isset($response->value[0]) && $response->value[0]->int != 0){
              echo $response->value[1]->string;
              
              return null;
            }
            return $response->value[1];
          }
        }
      }
      return null;
    }
    
    public function conect(){

      $params = array(
          config('app.wubook.usr'),
          config('app.wubook.psw'),
          config('app.wubook.key'),
          );
      $aResponse = $this->call('acquire_token', $params);
      if ($aResponse){
        $this->token = strval($aResponse->string);
//        dd($this->token);
        return true; 
      } 
      return false;
    }
    
    public function disconect(){
      if ($this->token){
        $aResponse = $this->call('release_token', array($this->token));
        $this->token = null;
      }
      return FALSE; 
    }
    
    public function pushURL($site,$url, $test=0){
      // tdocs.wubook.net/wired/fetch.html#setting-up-the-push-notification
      if ($this->token){
        $this->iCode = $this->WBConfig->getPropID($site);
        $aResponse = $this->call('push_activation', array($this->token,$this->iCode,$url, $test));
        if ($aResponse){
          if ($aResponse[0]->string == 'Ok'){
            return true;
          }
        }
      }
      return FALSE; 
    }
    
    public function get_pushURL($site){
      // tdocs.wubook.net/wired/fetch.html#setting-up-the-push-notification
      if ($this->token){
        $this->iCode = $this->WBConfig->getPropID($site);
        $aResponse = $this->call('push_url', array($this->token,$this->iCode));
        dd($aResponse);
      }
      return FALSE; 
    }

    
    
    /**
     * 
     * @param type $aResponse
     * @return type
     */
    private function processData($aResponse) {
      $result = array();
      if ($aResponse){
        if (!$aResponse->array->data || empty($aResponse->array->data)) return null;
        if (!property_exists($aResponse->array->data, 'value')) return null;
 
        $structs = $aResponse->array->data->value;
        foreach ($structs as $struct){
          
          if (isset($struct->struct))
            $member = $struct->struct->member;
          else 
            $member = $struct->member;
          $aData = array();
//          var_dump($member);die;
          foreach ($member as $data){
            $k = $data->name;

            $content = null;
            switch ($k){
              case 'dayprices':
                $aux = $data->value->struct->member->value->array->data->value;
                $content = array();
                foreach ($aux as $item){
                  if (is_array($item)){
                    foreach ($item as $item2){
                      $content[] = $item2;//->__toString();
                    }
                  } else {
                    $content[] = $item;
                  }
                }
                break;
              case 'rooms_occupancies':
                if (is_array($data->value->array->data->value)){
                  $data->value->array->data->value = $data->value->array->data->value[0];
                }
                if (is_object($data->value->array->data->value)){
                  $aux = $data->value->array->data->value->struct->member;
                  $content = array();
                  foreach ($aux as $item){
                    $content[$item->name] = $item->value->int;
                  }
                }
                break;
              case 'booked_rooms':
                break;

              default :
                $aux = $data->value;
                foreach ($aux as $item){
                  $content =$item;
                }
                break;
            }
            $aData[$k] = $content;

          }
          $result[] = $aData;
        }
      }
      return $result;
    }
    
    
    /**
     * check and apply Closes by dates
     * 
     * @param type $rCode
     * @return boolean
     */
    public function set_Closes($sID,$roomdays) {
      if ($this->token){

        $this->iCode = $this->WBConfig->getPropID($sID);
//        $roomdays= [
//          ['id'=> 433743, 'days'=> [['avail'=> 1,'date'=>'27/11/2016']],
//          ['id'=> 433742, 'days'=> [['avail'=> 2,'date'=>'07/11/2016'], [], ['avail'=> 2,'date'=>'17/11/2016']]],
//        ];
        $param = [
            $this->token,
            $this->iCode,
            $roomdays
        ];
     
        $aResponse = $this->call('update_sparse_avail',$param);
       
        if ($aResponse){
          if ($aResponse[0]->string == 'Ok'){
            echo 'Disponibilidad Actualizada';
            return true;
          }
        }
      }
      return FALSE; 
    }
    /**
     * check and apply Closes by dates
     * 
     * @param type $rCode
     * @return boolean
     */
    public function set_ClosesRange($dfrom='2020-01-28',$roomdays) {
      
      if ($this->token){
       $dfromTime = strtotime($dfrom);
//        $roomdays= [
//          ['id'=> 433743, 'days'=> [['avail'=> 1], ['avail'=> 1], ['avail'=> 1]]],
//          ['id'=> 433742, 'days'=> [['avail'=> 2], [], ['avail'=> 2]]],
//        ];
          
        $param = [
            $this->token,
            $this->iCode,
            date('d/m/Y',$dfromTime),
            $roomdays
        ];
      
        $aResponse = $this->call('update_avail',$param);
        if ($aResponse){
          if ($aResponse[0]->string == 'Ok'){
//            echo 'Disponibilidad Actualizada';
            return true;
          }
        }
      }
      return FALSE; 
    }
    
    
     /**
     * check and apply Closes by dates
     * 
     * @param type $rCode
     * @return boolean
     */
    public function set_Prices($dfrom='2020-01-28',$prices) {
      
      if ($this->token){
       $dfromTime = strtotime($dfrom);
       $this->iCode = $this->WBConfig->getPropID(1);
       $this->price_plan  = $this->WBConfig->pricePlan(1);
//        $prices= [
//          "_int_433743" => [100, 101, 102],
//          "_int_433742" => [200, 201, 202],
//        ];
               
          
        $param = [
            $this->token,
            $this->iCode,
            $this->price_plan,
            date('d/m/Y',$dfromTime),
            $prices
        ];
        $aResponse = $this->call('update_plan_prices',$param);
        if ($aResponse){
          if ($aResponse->string == 'Ok'){
//            echo 'Precios Actualizados';
            return true;
          }
        }
      }
      return FALSE; 
    }
    
    
     /**
     * check and apply Closes by dates
     * 
     * @param type $rCode
     * @return boolean
     */
    public function set_Restrictions($site,$dfrom='2029-01-28',$min_stay) {  
      if ($this->token){
        $dfromTime = strtotime($dfrom);
        $this->iCode = $this->WBConfig->getPropID($site);
        $this->rplan  = $this->WBConfig->restricPlan($site);
       
        $param = [
            $this->token,
            $this->iCode,
            $this->rplan,
            date('d/m/Y',$dfromTime),
            $min_stay
        ];
        $aResponse = $this->call('rplan_update_rplan_values',$param);
        if ($aResponse){
          if ($aResponse->string == 'Ok'){
            return true;
          }
        }
      }
      
      return FALSE; 
    }
    
     /**
     * fetch_bookings by dates
     * 
     * @param type $rCode
     * @return boolean
     */
    public function fetch_bookings($site,$dfrom='01/05/2020',$dto='21/12/2020') {
      
      $result = [];
      if ($this->token){
        $this->iCode = $this->WBConfig->getPropID($site);
        $param = [
            $this->token,
            $this->iCode,
            $dfrom,
            $dto,
//            1,//ancillary
        ];
        
        $aResponse = $this->call('fetch_bookings',$param);
    
        $aux = json_encode($aResponse);
        $aResponse = json_decode($aux);
        return $this->processData($aResponse);
      }
      return FALSE; 
    }
    
    
     /**
     * Booking by ID-book
     * 
     * @param type $rCode
     * @return boolean
     */
    public function fetch_booking($iCode,$rCode=null) {
      
      $propList = $this->WBConfig->getPropID();
      if ($iCode != $propList) return false;
      
      if ($this->token && $rCode){
     
        $this->iCode = $iCode;
        $param = [
            $this->token,
            $this->iCode,
            $rCode,
        ];
        $aResponse = $this->call('fetch_booking',$param);
        $aResponse = json_decode(json_encode($aResponse));
        
//        include_once public_path('/tests/WuBook-booking.php');
//        $aResponse = json_decode($bookings);
        
        $reserv = $this->processData($aResponse);
        if ($reserv){
          return $reserv[0];
        }
      }
      
      return FALSE; 
    }
    
    
     /**
     * Add event to calendar
     * 
     * @param $event ICal\Event
     * @param $agency integer Agency from where come the book
     * @param $room_id Room belong the book
     */
    private function addBook_OLD($data)
    {
      /***********************************************************/
      /** CANCEL THE BOOKING **/
      if ($data['status'] == 5){
        $alreadyExist = \App\Book::where('external_id',$data['reservation_code'])->first();
        if ($alreadyExist){
          $alreadyExist->type_book = 98;
          $alreadyExist->save();
        }
        return null;
      }
      /***********************************************************/
   
      
      foreach ($data as $k=>$v){
        if ($k != 'rooms_occupancies')
          $data[$k] = is_object($v) ? null : $v;
      }
      
      foreach ($data as $k=>$v){
        if (!$v){
          switch ($k){
            case 'customer_mail': $data[$k] = ''; break;
          }
        }
      }
     
      $start  = convertDateToDB($data['date_arrival']);
      $finish = convertDateToDB($data['date_departure']);
      $nights = calcNights($start, $finish);
      $data['start_date'] = $start;
      $data['end_date'] = $finish;
      $data['nights'] = $nights;
      
      
      /** UPDATE THE BOOKING **/
      $alreadyExist = \App\Book::where('external_id',$data['reservation_code'])->first();
      if ($alreadyExist){
        // Customer
        $customer             = \App\Customers::find($alreadyExist->customer_id);
        if ($customer && $customer->id == $alreadyExist->customer_id){
          $customer->name       = $data['customer_name'].' '.$data['customer_surname'];
          $customer->email      = $data['customer_mail'];
          $customer->phone      = $data['customer_phone'];
          $customer->address    = $data['customer_address'];
          $customer->country    = $data['customer_country'];
          $customer->city       = $data['customer_city'];
          $customer->zipCode    = $data['customer_zip'];
          $customer->comments   = $customer->comments.$data['customer_notes'];
          $customer->save();
        }
        
        $this->updBooking($alreadyExist, $data);
        return null;
      }
      
      /** CREATE THE BOOKING **/
      $roomGroup = isset($this->channels[$data['rooms']]) ? $this->channels[$data['rooms']] : 'ROSASJ';
      $room = new \App\Rooms();
      $roomID = $room->calculateRoomToFastPayment($roomGroup, $start, $finish);
      if ($roomID<0){
        $roomID = 33;
      }
            
      $book = new \App\Book();

      // Customer
      $customer             = new \App\Customers();
      $customer->user_id    = 23;
      $customer->name       = $data['customer_name'].' '.$data['customer_surname'];
      $customer->email      = $data['customer_mail'];
      $customer->phone      = $data['customer_phone'];
      $customer->address    = $data['customer_address'];
      $customer->country    = $data['customer_country'];
      $customer->city       = $data['customer_city'];
      $customer->zipCode    = $data['customer_zip'];
      $customer->language   = $data['customer_language_iso'];
      $customer->comments   = $data['customer_notes'];
      $customer->email_notif= $data['customer_mail'];
      $customer->send_notif = 1;
      $customer->DNI        = '';

      if ($customer->zipCode>0){
        $customer->province = substr($customer->zipCode, 0,2);
      } else {
        if ($customer->country == 'es' || $customer->country == 'ES'){
          if (trim($customer->city) != ''){
            $obj = \App\Provinces::where('province','LIKE', '%'.trim($customer->city).'%')->first();
            if ($obj) {
              $customer->province = $obj->code;
            }
          }
        }
      }
      $customer->save();
    
      //Create Book
      $book->user_id = 39;
      $book->customer_id = $customer->id;
      $book->room_id = $roomID;
      $book->external_id = $data['reservation_code'];
      $book->propertyId = $data['rooms'];
      $book->save();

      $this->updBooking($book, $data);

      

      return $book->id;
      
     
    }
    
    
    
    public function getRoomsEquivalent($channels) {
      return $this->WBConfig->getRooms($channels);
    }
    
    function getPricesBooking($siteID=null,$dfrom, $dto) {
      $prices = [];
      //nights+1 para corregir fechas
      $nights = calcNights(convertDateToDB($dfrom), convertDateToDB($dto))+1;
      if ($siteID>0){
        $lcode = $this->WBConfig->getPropID($siteID);
        $pID   = $this->WBConfig->pricePlanMetaBuscador($siteID);
        if ($pID){
          $prices = $this->getPricesBookingBySite($lcode,$pID,$dfrom, $dto);
          $aDiscounts = $this->getDiscounts($lcode,$dfrom, $dto);
          $aExtras = $this->getExtras($lcode,$dfrom, $dto);
          if ($aDiscounts['type'] == 1){ //1: Percentage
            foreach ($prices as $k=>$v){
              $prices[$k] = $v-$v*($aDiscounts['value']/100);
            }
          }
          if ($aDiscounts['type'] == 2){ //2: Fixed
            foreach ($prices as $k=>$v){
              $prices[$k] = $v-$aDiscounts['value'];
            }
          }
          if ($aExtras) {
            foreach ($prices as $k => $v) {
              if ($aExtras['extra'] == 1) {
                if ($aExtras['perday'] == 1){
                  $v = $v + ($nights * $aExtras['price']);
                }
                else
                  $v = $v + $aExtras['price'];
              } else {
                if ($aExtras['perday'] == 1)
                  $v = $v - ($nights * $aExtras['price']);
                else
                  $v = $v - $aExtras['price'];
              }
              $prices[$k] = $v;
            }
          }
        }
      } else {
        for($i=1;$i<4;$i++){
          $lcode = $this->WBConfig->getPropID($i);
          $pID   = $this->WBConfig->pricePlanMetaBuscador($i); 
          if ($pID){
            $priceAux = $this->getPricesBookingBySite($lcode,$pID,$dfrom, $dto);
            $aDiscounts = $this->getDiscounts($lcode,$dfrom, $dto);
            $aExtras = $this->getExtras($lcode,$dfrom, $dto);
            foreach ($priceAux as $k=>$v){
              if ($aDiscounts['type'] == 1){ //1: Percentage
                $v = $v-$v*($aDiscounts['value']/100);
              }
              if ($aDiscounts['type'] == 2){ //2: Fixed
                $v = $v-$aDiscounts['value'];
              }
              
              if ($aExtras){
                if ($aExtras['extra'] == 1){
                  if ($aExtras['perday'] == 1){
                   
                    $v = $v + ($nights*$aExtras['price']);
                  }
                  else $v = $v + $aExtras['price'];
                } else {
                  if ($aExtras['perday'] == 1)
                    $v = $v - ($nights*$aExtras['price']);
                  else $v = $v - $aExtras['price'];
                }
              }
          
              $prices[$k] = $v;
            }
          }
        }
      }
      return $prices;
      
    }
    function getPricesBookingBySite($lcode=null,$pID=null,$dfrom, $dto) {
      $param = [
          $this->token,
          $lcode,
          $pID,
          $dfrom,
          $dto,
        ];
//      $aResponse = $this->call('fetch_rooms_values',$param);
      $aResponse = $this->call('fetch_plan_prices',$param);
      $aResponse = json_decode(json_encode($aResponse));
      $channels = $this->WBConfig->roomsEquivalent();
      $prices = [];
      
      @$registers = $aResponse->struct;
      if (!$registers) return [];
      $registers = $registers->member;
      foreach ($registers as $data){
        $price = 0;
        if (isset($data->value->array->data->value)){
          foreach ($data->value->array->data->value as $p){
            if (is_object($p))  $price += floatval($p->double);
            else $price += floatval($p);
          }
        }
        $ch = isset($channels[$data->name]) ? $channels[$data->name] : '';
        $prices[$ch] = $price;
      }
      return $prices;
    }
    
    private function getDiscounts($lcode=null,$dfrom, $dto) {
      $return = ['type'=>null,'value'=>null];
       $param = [
          $this->token,
          $lcode,
          $dfrom,
          $dto,
        ];
      
      $aResponse = $this->call('fetch_soffers',$param);
      $aResponse = json_decode(json_encode($aResponse));
      if ($aResponse){
        try{
          @$registers = $aResponse->array->data->value;
          if (!$registers) return $return;
          $registers = $registers->struct->member;
          foreach ($registers as $data){
            if ($data->name == 'dtype'){
              $return['type'] = intval($data->value->int);
            }
            if ($data->name == 'dvalue'){
              $return['value'] = floatval($data->value->double);
            }
          }
        } catch (Exception $ex) {

        }
      }
      return $return;
    }
    
    public function getCC_Data($siteID,$rcode) {
      
      $lcode = $this->WBConfig->getPropID($siteID);
      $pwd_used_to_store_ccs = config('app.wubook.cc_hey');
      $param = [
          $this->token,
          $lcode,
          $rcode,
          $pwd_used_to_store_ccs
        ];
        $aResponse = $this->call('fetch_ccard',$param);
        $aResponse = json_decode(json_encode($aResponse));
        if (!$aResponse) return null;
      $registers = $aResponse->struct->member;
      $cc = [];
      foreach ($registers as $data){
        foreach ($data->value as $v){
          $cc[$data->name] = $v;
        }
      }
      
      $cvc = isset($cc['cc_cvv']) ? $cc['cc_cvv'] : null;
      if(!$cvc){
        $cvc = isset($cc['cc_code']) ? $cc['cc_code'] : '';
      }
      $fieldsCard = [
          "name"=> isset($cc['cc_owner']) ? $cc['cc_owner'] : '',
          "number"=>isset($cc['cc_number']) ? $cc['cc_number'] : '',
          'date'=>isset($cc['cc_expiring']) ? $cc['cc_expiring'] : '',
          "cvc"=>$cvc,
          'type'=>isset($cc['cc_type']) ? $cc['cc_type'] : '',
          ];
      
      return $fieldsCard;
            
    }
    
    private function getExtras($lcode=null,$dfrom, $dto) {
      
      $return= ['extra'=>null,'price'=>null,'active'=>null,'perday'=>null];
      $param = [
        $this->token,
        $lcode,
        $dfrom,
        $dto,
      ];
      
     $aResponse = $this->call('fetch_opportunities',$param);
//      echo json_encode($aResponse); die;
//      $aResponse = json_decode(json_encode($aResponse));
       $aResponse = json_decode('{"array":{"data":{"value":{"struct":{"member":[{"name":"extra","value":{"int":"1"}},{"name":"wdays","value":{"array":{"data":{"value":[{"int":"1"},{"int":"1"},{"int":"1"},{"int":"1"},{"int":"1"},{"int":"1"},{"int":"1"}]}}}},{"name":"dto","value":{"string":"27\/04\/2030"}},{"name":"name","value":{"string":"DESYUNO BUFFET"}},{"name":"perday","value":{"int":"1"}},{"name":"dfrom","value":{"string":"29\/04\/2020"}},{"name":"price","value":{"double":"10.0"}},{"name":"oid","value":{"int":"25141"}},{"name":"how","value":{"int":"1"}},{"name":"rooms","value":{"array":{"data":{"0":"\n"}}}},{"name":"active","value":{"int":"1"}}]}}}}}');
      if ($aResponse){
       
        try{
          @$registers = $aResponse->array->data->value;
          if (!$registers) return $return;
          $registers = $registers->struct->member;
          
          foreach ($registers as $data){
            //extra	1 if it’s an add-on, 0 if it’s a reduction
            if ($data->name == 'extra'){
              $return['extra'] = intval($data->value->int);
            }
            if ($data->name == 'active'){
              $return['active'] = intval($data->value->int);
            }
            //perday	1 for daily price, 0 for global price
            if ($data->name == 'perday'){
              $return['perday'] = intval($data->value->int);
            }
            if ($data->name == 'price'){
              $return['price'] = floatval($data->value->double);
            }
          }
        } catch (Exception $ex) {

        }
      }
      if ($return['active'] == 1) return $return;
      return null;
    }

    
  function addBook($rva)
  {
   
  $oConfig = new WBConfig();

 
    $channel_group = $oConfig->getChannelByRoom($rva['rooms']);
    $customer_notes = implode(',',(array)($rva['customer_notes']));
    $start  = convertDateToDB($rva['date_arrival']);
    $finish = convertDateToDB($rva['date_departure']);
    $comision = 0;
    $pvpFinal = $rva['amount'];
    if ($rva['amount']>0){
      $comision = $comision + ($comision/100*21);
      // comision = (PVP_final 15%) +  (PVP_final 15%) 21%
      // x+[y/100*15 + (y/100*15/100*21)] = PVP final
      // PVP final = $rva['amount'] / 0.8185

      $pvpFinal = round(($rva['amount']/0.8185) , 2);
      $comision = $pvpFinal - $rva['amount'];
    }


    $reserv = [
        'channel' => null,
        'bkg_number' => $rva['reservation_code'],
        // 'rate_id' => $rva['plan_id'],
        'external_roomId' => $rva['rooms'],
        'reser_id' => $rva['channel_reservation_code'],
        'comision' => $comision,
        'channel_group' => $channel_group,
        'status' => ($rva['status'] == 5) ? 2 : 1,
        'agency' => 4,//just airbnb
        'customer_name' => $rva['customer_name'].' '.$rva['customer_surname'],
        'customer_email' => $rva['customer_mail'],
        'customer_phone' => $rva['customer_phone'],
        'customer_comment' => $customer_notes,
        'totalPrice' => $pvpFinal,
        'adults' => $rva['men'],
        'children' => $rva['children'],
        'extra_array' => [],
        'start' => $start,
        'end' => $finish,
        'modified_from' => null,
        'modified_to' => null,
        'pax'=>$rva['men']+ $rva['children'],
    ];

    $OTA_service = new \App\Services\OtaGateway\OtaGateway();
    $bookID = $OTA_service->addBook($channel_group, $reserv);
    return $bookID;
    
  }
    
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;
use DB;
use Mail;
use App\Traits\BookEmailsStatus;
use App\BookPartee;

/**
 * Class Book
 *
 */
class Book extends Model {

  protected $table = 'book';

  /**
   * The attributes that should be casted to native types.
   *
   * @var array
   */
  protected $typeBooks = [
      0 => 'ELIMINADA',
      1 => 'Reservado - stripe',
      2 => 'Pagada-la-señal',
      3 => 'SIN RESPONDER',
      4 => 'Bloqueado',
      5 => 'Contestado(EMAIL)',
      6 => 'Denegada',
      7 => 'Reserva Propietario',
      8 => 'ATIPICAS',
      //'SubComunidad',
      9 => 'Booking',
      10 => 'Overbooking',
      11 => 'blocked-ical',
//      12 => 'ICAL - INVISIBLE',
      98 => 'cancel-XML',
      99 => 'FASTPAYMENT - SOLICITUD',
  ];
  
  var $typeBooksReserv = [
      1,// => 'Reservado - stripe',
      2,// => 'Pagada-la-señal',
      4,// => 'Bloqueado',
      7,// => 'Reserva Propietario',
      8,// => 'ATIPICAS',
      9,// => 'Booking',
      11,// => 'blocked-ical',
      //99,// => 'blocked-ical',
  ];

  use BookEmailsStatus;

  public function customer() {
    return $this->hasOne('\App\Customers', 'id', 'customer_id');
  }

  public function room() {
    return $this->hasOne('\App\Rooms', 'id', 'room_id');
  }

  public function extrasBook() {
    return $this->hasMany('\App\BookExtraPrices', 'book_id', 'id');
  }

  public function pago() {
    return $this->hasMany('\App\Payments', 'book_id', 'id');
  }

  public function user() {
    return $this->hasOne('\App\User', 'id', 'user_id');
  }

  public function notifications() {
    return $this->hasMany('\App\BookNotification', 'book_id', 'id');
  }
  public function LogImages() {
    return $this->hasMany('\App\LogImages', 'book_id', 'id');
  }
  public function leads() {
    return $this->hasOne('\App\CustomersRequest', 'book_id', 'id');
  }

  //Para poner nombre al dia del calendario//
  static function getDayWeek($dayweek) {
    $array = [
        1 => "L",
        2 => "M",
        3 => "X",
        4 => "J",
        5 => "V",
        6 => "S",
        0 => "D"
    ];

    return $dayweek = $array[$dayweek];
  }

  //Para poner nombre al estado de la reserva//
  public function getStatus($status) {
    return isset($this->typeBooks[$status]) ? $this->typeBooks[$status] : $status;
  }

  public function getTypeBooks() {
    return $this->typeBooks;
  }

  //Para poner nombre al tipo de cobro//
  static function getTypeCobro($typePayment=NULL) {
    $array = [
        0 => "CASH",//"Metalico Jorge",
        1 => "CASH",// "Metalico Jaime",
        2 => "TPV",//"Banco Jorge",
        3 => "TPV",//"Banco Jaime"
        4 => "REINTEGRO"//Devoluciones
    ];

    if (!is_null($typePayment)) return $typePayment = $array[$typePayment];
    
    return $array;
  }

  //Para poner nombre a la agencia//
  static function listAgency() {
    $array = [
        0 => "Directa",
        1 => "Booking",
        2 => "Trivago",
        3 => "Agoda",
        4 => "AirBnb",
        5 => "Jaime Diaz",
        6 => "Expedia",
        7 => "google-hotel"
    ];
    for($i=1;$i<21;$i++){
      $array[] = 'Agencia-'.$i;
    }
    $array[] = ("S.essence");
    $array[] = "Bed&Snow";
    $array[] = "Cerogrados";
    $array[] = "WEBDIRECT";
    $array[999999] = 'Google-old';
    return $array;
  }
  
  //Para poner nombre a la agencia//
  static function getAgency($agency) {
    $array = self::listAgency();
    
    for($i=1;$i<21;$i++){
      $array[] = 'Agencia '.$i;
    }
//echo count($array); die;
    return isset($array[$agency]) ? $array[$agency] : 'Sin Nombre';
  }

  //Para comprobar el dia de la reserva en el calendario
  static function availDate($startDate, $endDate, $room,$bookID=null) {

    $qry = self::where_type_book_reserved()->where('room_id',$room)
            ->where('finish','>=',$startDate)->where('start','<=',$endDate);
    if ($bookID && $bookID>0) {
      $qry->where('id','!=',$bookID);
    } 
    $books = $qry->get();
    if (count($books)==0) return true;
    
    foreach ($books as $b){
      if ($b->finish == $startDate) continue;
      if ($b->start == $endDate)   continue;
      return false;
    }
    return true;
  }

  // Funcion para cambiar la reserva de estado
  public function changeBook($status, $room, $book) {
    $status = intval($status);
    $this->customer->send_mails = true;
    $response = ['status' => '', 'title' => 'OK', 'response' => '','changed'=>false];
    if (empty($status)){
      return ['status' => 'danger', 'title' => 'Error', 'response' => 'Sin estado','changed'=>false];
    }
    if ($this->type_book == 0){
        $response['status'] = "warning";
        $response['response'] = "La Reserva esta eliminada";
        return $response;
    }
    
    if ($status == 3 || $status == 10 || $status == 12 || $status == 6 || $status == 98) {
      $this->type_book = $status;
      $this->save();
      $response['status'] = "success";
      $response['changed'] = true;
      $response['response'] = $this->getResponseStatusChanged($status);
      if ($status == 6) $this->sendEmailChangeStatus($book, 'Reserva denegada', $status);
     
      
      \App\BookLogs::saveLogStatus($this->id, $this->room_id, $this->customer->email, $this->getStatus($status));
      return $response;
      
    } else {
      
      //check if is Availiable Date
      if (Book::availDate($this->start, $this->finish, $this->room_id,$this->id)) {

        $this->type_book = $status;
        $this->save();
        
        if ($status == 2) {
          $this->sendToPartee();
        }

        if ($this->customer->email == "") {
          \App\BookLogs::saveLogStatus($this->id, $this->room_id, $this->customer->email, $this->getStatus($status));
          $response['status'] = 'warning';
          $response['title'] = 'Cuidado';
          $response['changed'] = true;
          $response['response'] = 'No tiene Email asignado';

          return $response;
        
        } else {
           switch ($status) {
              case 1:
                $this->sendEmailChangeStatus($book, 'Bloqueo de reserva y datos de pago', $status);
                break;
              case 2:
                $this->sendEmailChangeStatus($book, 'Confirmación de reserva (pago parcial)', $status);
                break;
              case 7:
                $this->sendEmailChangeStatus($book, 'Correo de Reserva de Propietario', $status);
                break;
            }

            $response['status'] = "success";
            $response['changed'] = true;
            /** @ToDo: REVISAR: Creamos las notificaciones de booking */
            /* Comprobamos que la room de la reserva este cedida a booking.com */
            if ($this->room->isAssingToBooking()) {

              $isAssigned = \App\BookNotification::where('book_id', $book->id)->get();

              if (count($isAssigned) == 0) {
                $notification = new \App\BookNotification();
                $notification->book_id = $book->id;
                $notification->save();
              }
            }
            $response['response'] = $this->getResponseStatusChanged($status);
            \App\BookLogs::saveLogStatus($this->id, $this->room_id, $this->customer->email, $this->getStatus($status));
            return $response;
        }
      } // END: Check availibility
    }
    
    $response['status'] = 'danger';
    $response['title'] = 'Peligro';
    $response['response'] = 'No puedes cambiar el estado - Los aptos no están disponibles';
   
    return $response;
      
  }
  
  function getResponseStatusChanged($status) {
    $response = '';
    switch ($status) {
      case 3:  $response = "Estado Cambiado a Sin Responder";
        break;
      case 10: $response = "Reserva cambiada a Overbooking";
        break;
      case 12: $response = "Reserva cambiada a ICAL - INVISIBLE";
        break;
      case 98: $response = "Reserva cambiada a cancel-XML";
        break;
      case 6:  $response = "Reserva cambiada a ICAL - INVISIBLE";
        break;
      case 1:  $response = "Email Enviado Reserva";
        break;
      case 2:  $response = "Email Enviado Pagada la señal";
        break;
      case 7:  $response = "Estado Cambiado a Reserva Propietario";
        break;
      case 8:  $response = "Estado Cambiado a Subcomunidad";
        break;
      case 4:  $response = "Estado Cambiado a Bloqueado";
        break;
      case 5:  $response = "Contestado por email";
        break;
      default: $response = "Estado Cambiado";
        break;
    }
    return $response;
  }

  function getJorgeProfit(){return 0;}
  function getJaimeProfit(){return 0;}
  
  public function getPayment($tipo) {
    return $this->payments->filter(function ($payment) use ($tipo) {
              return $payment->type == $tipo;
            })->sum('import');
  }

  public function getLastPayment() {
    $lastPayment = 0;
    if (count($this->payments) > 0) {
      foreach ($this->payments as $index => $payment) {
        $lastPayment = $payment->import;
      }
    }

    return $lastPayment;
  }

   /**
   * Send the Booking to Partee
   */
 public function sendToPartee() {
    
    if(config('app.partee.disable') == 1) return;
    
    $BookPartee = BookPartee::where('book_id', $this->id)->first();

    if ($BookPartee) {
      if ($BookPartee->partee_id > 0) {
        return FALSE;
      }
    } else {
      $BookPartee = new BookPartee();
      $BookPartee->book_id = $this->id;
    }

    //Create Partee
    $partee = new \App\Services\ParteeService();
    $partee->setID(Settings::getParteeBySite($this->room->site_id));
    if ($partee->conect()) {

      $result = $partee->getCheckinLink($this->customer->email, strtotime($this->start));

      if ($result) {

        $BookPartee->link = $partee->response->checkInOnlineURL;
        $BookPartee->partee_id = $partee->response->id;
        $BookPartee->status = 'sent';
        $BookPartee->log_data = $BookPartee->log_data . "," . time() . '- Sent';
        $BookPartee->save();
      } else {
        $BookPartee->status = 'error';
        $BookPartee->log_data = $BookPartee->log_data . "," . time() . '-' . $partee->response;
        $BookPartee->save();
      }
    } else {

      $BookPartee->status = 'error';
      $BookPartee->log_data = $BookPartee->log_data . "," . time() . '-' . $partee->response;
     
      $BookPartee->save();
    }
    
  }

  public static function getBeneficioJorge() {
    
  }

  public static function getBeneficioJaime() {
    
  }

  public function payments() {
    return $this->hasMany(Payments::class);
  }

  public function partee() {
    return $this->hasOne(BookPartee::class)->first();
  }
  
  public function SafetyBox() {
    return \Illuminate\Support\Facades\DB::table('book_safety_boxes')
            ->where('book_id',$this->id)
            ->whereNull('deleted')
            ->first();
  }

  public function getSumPaymentsAttribute() {
    return $this->payments->sum('import');
  }

    /**
   * @return mixed
   */
  public function getCostsAttribute() {
    return $this->cost_total;
    return $this->cost_apto + $this->cost_park + $this->cost_lujo + $this->PVPAgencia + $this->cost_limp + $this->stripeCost + $this->extraCost;
  }

  /**
   * @return int
   */
  public function getPendingAttribute() {
    return $this->total_price - $this->payments->sum('import');
  }
  
  /**
   * @return mixed
   */
  public function getProfitAttribute() {
    return intval($this->total_price) - intval($this->costs);
  }

  public function hasSendPicture() {
    return (count($this->LogImages)>0);
//    $sendPictures = DB::select("SELECT * FROM log_images WHERE book_id = '" . $this->id . "'");
//    return (count($sendPictures) == 0) ? false : true;
  }

  public function getSendPicture() {
    $sendPictures = \App\LogImages::where('book_id', $this->id)->get();
    return (count($sendPictures) > 0) ? $sendPictures : false;
  }

  /**
   * Get the inc_percent from the book
   * 
   * @return int inc_percent
   */
  public function get_inc_percent() {
    $profit = $this->profit;
    $total_price = $this->total_price;
    $inc_percent = 0;
    if ($this->room->luxury == 0 && $this->cost_lujo > 0) {
      $profit = $this->profit - $this->cost_lujo;
      $total_price = $this->total_price - $this->sup_lujo;
    }

    if ($total_price != 0) {
      $inc_percent = ($profit / $total_price ) * 100;
    }

    return $inc_percent;
  }

  /**
   * Get the total cost
   * 
   * @return int $cost_total
   */
  public function get_costeTotal() {
    $cost_total = $this->get_costProp() + $this->cost_limp + $this->PVPAgencia + $this->extraCost;
    $paymentTPV = $this->getPayment(2);
    if ($paymentTPV>0) $cost_total += paylandCost($paymentTPV);
    return $cost_total;
  }
  
  function get_costProp(){
    return 0;  // no tienen costo prop
    $cost = $this->cost_apto +  $this->cost_park;
    $cost += $this->get_costLujo();
    return $cost;
  }
  
  function get_costLujo(){
    if ($this->type_luxury == 1 || $this->type_luxury == 3 || $this->type_luxury == 4) {
      return $this->cost_lujo;
    }
    return 0;
  }

  /**
   * Get object Book that has status 2,7,8
   * 
   * @return Object Query
   */
  static function where_type_book_sales($reservado_stripe=false,$ota=false) {
    
    $types = self::get_type_book_sales($reservado_stripe,$ota);
                
    return self::whereIn('type_book',$types);
    
  }
  /**
   * Get object Book that has status 2,7,8
   * 
   * @return Object Query
   */
  static function where_type_book_reserved($real=false) {
    return self::whereIn('type_book', self::get_type_book_reserved($real));
  }
  
  static function get_type_book_sales($reservado_stripe=false,$ota=false) {
     $types = [2, 7, 8];
    if ($reservado_stripe) $types[] = 1;
    if ($ota) $types[] = 11;
    $types[] = 10; //Agrega OVERBOOKING
    //Pagada-la-señal / Reserva Propietario / ATIPICAS
    return $types;
  }
  static function get_type_book_reserved($real=false) {
    if ($real) return [1,2,7,8,9,10,11];
    return [1,2,4,7,8,9,10,11];
  }
  static function get_type_book_pending() {
    return [3,4,5,6,10,11,99];
  }
  
  /**
   * Get object Book that has status 2,7,8
   * 
   * @return Object Query
   */
  static function where_book_times($startYear,$endYear) {
    
     return self::where(function ($query) use ($startYear,$endYear) {
       $query->where(function ($query2) use ($startYear,$endYear) {
          $query2->where('start', '>=', $startYear)->Where('start', '<', $endYear);
        })->orWhere(function ($query2) use ($startYear,$endYear) {
          $query2->where('finish', '>', $startYear)->Where('finish', '<=', $endYear);
        })->orWhere(function ($query2) use ($startYear,$endYear) {
          $query2->where('start', '<', $startYear)->Where('finish', '>', $endYear);
        });
      });
  }
  
  /**
   * Get object Book that has status 2,7,8
   * 
   * @return Object Query
   */
  static function w_book_times($qry,$start,$finish) {
      
    $match1 = [['start', '>=', $start], ['start', '<=', $finish]];
    $match2 = [['finish', '>=', $start], ['finish', '<=', $finish]];
    $match3 = [['start', '<', $start], ['finish', '>', $finish]];
    return $qry->where(function ($query) use ($match1, $match2, $match3) {
                      $query->where($match1)
                      ->orWhere($match2)
                      ->orWhere($match3);
                    });
  }
  
  /**
   *  Filter book used today
   * 
   * @return Object Query
   */
  static function where_book_today($day) {
    return self::where('start', '>=', $day)->Where('finish', '<=', $day);
  }
  
/**
 * 
 * @param type $dStart
 * @param type $dEnd
 * @param type $roomID
 * @param type $cant
 * @return string
 */
  public function getPriceBook($dStart,$dEnd,$roomID,$cant=1) {

    $oRoom = Rooms::find($roomID);
    $return = [
      'status'        => 'error',  
      'msg'           => 'error',  
      'pvp'           => 0,  
      'cost'           => 0,  
      'extra_fixed'      => 0,
      'extra_dynamic'    => 0,
      'limp'             => 0,
      'cost_extra_fixed' => 0,
      'cost_extra_dynamic'=> 0,
      'cost_limp'     => 0,
      'price_total'   => 0,
      'cost_total'    => 0,
    ];
    if (!$oRoom){
      $return['msg'] = "Apto no encontrado";
      return $return;
    }
    if ($oRoom->state != 1){
      $return['msg'] = 'Apto '.$oRoom->name.' no Habilitado';
      return $return;
    }
    
    $priceCost = $oRoom->getPVP($dStart,$dEnd,$this->pax,true);
    
    $return['cost']= $priceCost['cost'];
    $return['pvp'] = $priceCost['price'];

    $extraPrice = 0;
    $extraCost = 0;
    if ($oRoom){
     $ExtraPrices = \App\ExtraPrices::getFixed($oRoom->channel_group);
     foreach ($ExtraPrices as $e){
       if(trim($e->name) == "Limpieza"){
        $return['limp'] = $e->price;
        $return['cost_limp'] = $e->cost;
       } else {
        $extraPrice += $e->price;
        $extraCost += $e->cost;
       }
     }
    }
    $return['cost_extra_fixed'] = $extraCost;
    $return['extra_fixed'] = $extraPrice;
    
    if($this->cost_limp && $this->cost_limp != $return['cost_limp']){
      $return['cost_limp'] = $this->cost_limp;
    }
    if($this->extraCost>0 && $this->extraCost != $return['cost_extra_fixed']){
      $return['cost_extra_fixed'] = intval($this->extraCost);
    }


    $extraPrice = 0;
    $extraCost = 0;
    $dynamic_extras = BookExtraPrices::getDynamic($this->id);
    foreach ($dynamic_extras as $e){
       $extraPrice += $e->price;
       $extraCost += $e->cost;
    }
    $return['extra_dynamic'] = $extraPrice;
    $return['cost_extra_dynamic'] = $extraCost;

    $return['price_total'] =  $return['pvp']+ $return['extra_fixed']+ $return['extra_dynamic']+$return['limp'];
    $return['cost_total'] =  $return['cost']+ $return['cost_extra_dynamic']+ $return['cost_extra_fixed']+$return['cost_limp'];
    $return['status'] = 'ok';
    return $return;
  }
  

  static function getMonthSum($field,$filter,$date1,$date2) {
    
    $typeBooks = '(1,2,7,8,11)';
  
    return DB::select('SELECT new_date, SUM('.$field.') as total '
            . ' FROM ('
            . '        SELECT '.$field.',DATE_FORMAT('.$filter.', "%m-%y") new_date '
            . '        FROM book'
            . '        WHERE type_book IN '.$typeBooks
            . '        AND '.$filter.' >= "'.$date1.'" '
            . '        AND '.$filter.' <= "'.$date2.'" '
            . '      ) AS temp_1 '
            . ' GROUP BY temp_1.new_date'
            );
    
  }
    
  /**
   * send to channel manager the availibility
   * @param type $available
   */
  public function sendAvailibilityBy_dates($start=null,$finish=null) {
    //check si es del mismo grupo
    $room = Rooms::find($this->room_id);
    if(!$start) $start = $this->start;
    if(!$finish) $finish = $this->finish;
    if (in_array($this->type_book,$this->typeBooksReserv)){
      $this->sendAvailibility($this->room_id,$start,$finish);
    }
  }
  /**
   * send to channel manager the availibility
   * @param type $available
   */
  public function sendAvailibilityBy_Rooms($old_room,$start=null,$finish=null) {
    //check si es del mismo grupo
    $room = Rooms::find($this->room_id);
    if(!$start) $start = $this->start;
    if(!$finish) $finish = $this->finish;
    if ($room){
//      $oRooms = Rooms::where('channel_group',$room->channel_group)->pluck('id')->toArray();
      $oRooms = Rooms::RoomsCH_IDs($room->channel_group);
      if (!in_array($old_room,$oRooms)){
        $this->sendAvailibility($old_room,$start,$finish);
      } 
      $this->sendAvailibility($this->room_id,$start,$finish);
    }
  }
  /**
   * send to channel manager the availibility
   * @param type $available
   */
  public function sendAvailibilityBy_status() {
    $this->sendAvailibility($this->room_id,$this->start,$this->finish);
  }
  /**
   * send to channel manager the availibility
   * @param type $available
   */
  public function sendAvailibility($room_id,$start,$finish) {
    $room     = Rooms::find($room_id);
    if ($room){
      $oOta = new Services\OtaGateway\OtaGateway();
      $oRooms = Rooms::RoomsCH_IDs($room->channel_group);
            
      $match1 = [['start','>=', $start ],['start','<=', $finish ]];
      $match2 = [['finish','>=', $start ],['finish','<=', $finish ]];
      $match3 = [['start','<', $start ],['finish','>', $finish ]];
      
      $books = self::where_type_book_reserved()->whereIn('room_id',$oRooms)
            ->where(function ($query) use ($match1,$match2,$match3) {
              $query->where($match1)
                      ->orWhere($match2)
                      ->orWhere($match3);
            })->get();
            
      $avail  = count($oRooms);
      
      
      //Prepara la disponibilidad por día de la reserva
      $today = strtotime(date('Y-m-d'));
      $startAux = strtotime($start);
      $endAux = strtotime($finish);
      if ($startAux<$today) $startAux = $today;
      $aLstDays = [];
      while ($startAux<$endAux){
        $aLstDays[date('Y-m-d',$startAux)] = $avail;
        $startAux = strtotime("+1 day", $startAux);
      }
      $control = [];
      if ($books) {
        foreach ($books as $book) {
          //Resto los días reservados
          $startAux = strtotime($book->start);
          $endAux = strtotime($book->finish);

          while ($startAux < $endAux) {
            $auxTime = date('Y-m-d', $startAux);
            $keyControl = $book->room_id.'-'.$auxTime;
            if (!in_array($keyControl, $control)){
              if (isset($aLstDays[$auxTime]))
                $aLstDays[$auxTime] --;

              $control[] = $keyControl;
            }

            $startAux = strtotime("+1 day", $startAux);
          }
        }
      }
      //BEGIN wubook
      $oAux = \App\ProcessedData::findOrCreate('wubookAvail');
      $oAux->content=time();
      $oAux->save();
      //END wubook

      $return = false;
      if ($oOta->conect($room->site_id))
        $return = $oOta->sendAvailabilityByCh($room->channel_group,$aLstDays);
      
      return $return;
    }
  }
    
    
   /**
   * Get Availibility Room By channel
   * @param type $available
   */
  public function getAvailibilityBy_channel($apto, $start, $finish,$return = false,$justSale=false,$real=false) {

//    $oRooms = Rooms::where('channel_group', $apto)->pluck('id')->toArray();
    $oRooms = Rooms::RoomsCH_IDs($apto);
    $match1 = [['start', '>=', $start], ['start', '<=', $finish]];
    $match2 = [['finish', '>=', $start], ['finish', '<=', $finish]];
    $match3 = [['start', '<', $start], ['finish', '>', $finish]];

    if ($justSale) $sqlBooks = self::where_type_book_sales();
    else  $sqlBooks = self::where_type_book_reserved($real);
    
    $books = $sqlBooks->whereIn('room_id', $oRooms)
                    ->where(function ($query) use ($match1, $match2, $match3) {
                      $query->where($match1)
                      ->orWhere($match2)
                      ->orWhere($match3);
                    })->get();

    $avail = count($oRooms);
   

    //Prepara la disponibilidad por día de la reserva
    $startAux = strtotime($start);
    $endAux = strtotime($finish);
    $aLstDays = [];
    while ($startAux <= $endAux) {
      $aLstDays[date('Y-m-d', $startAux)] = $avail;
      $startAux = strtotime("+1 day", $startAux);
    }


    $control = [];
    if ($books) {
      foreach ($books as $book) {
        //Resto los días reservados
        $startAux = strtotime($book->start);
        $endAux = strtotime($book->finish);

        if ($startAux == $endAux){
          $auxTime = date('Y-m-d', $startAux);
          $keyControl = $book->room_id.'-'.$auxTime;
          if (!in_array($keyControl, $control)){
            if (isset($aLstDays[$auxTime]))
              $aLstDays[$auxTime]--;
            $control[] = $keyControl;
          }
        } else {
          while ($startAux < $endAux) {
            $auxTime = date('Y-m-d', $startAux);
            $keyControl = $book->room_id.'-'.$auxTime;
            if (!in_array($keyControl, $control)){
              if (isset($aLstDays[$auxTime]))
                $aLstDays[$auxTime] --;

              $control[] = $keyControl;
            }

            $startAux = strtotime("+1 day", $startAux);
          }
        }
      }
    }
    if($return){
      return [$aLstDays,$avail];
    }
    return $aLstDays;
  }
  

  function saveFixedExtras($channel_group){
    
    $ExtraPrices = \App\ExtraPrices::getFixed($channel_group);
    if ($ExtraPrices){
      foreach ($ExtraPrices as $e){
        $item = new BookExtraPrices();
        $item->extra_id = $e->id;
        $item->book_id = $this->id;
        $item->qty   = 1;
        $item->price = $e->price;
        $item->cost  = $e->cost;
        $item->channel_group = $channel_group;
        $item->fixed   = 1;
        $item->status  = 1;
        $item->deleted = 0;
        $item->save();
       }
    }
    
  }
  
  function resetFixedExtras($channel_group){
    BookExtraPrices::where('book_id', $this->id)->where('fixed',1)->delete();
    
    $this->saveFixedExtras($channel_group);
  }
  
  function getPaymentPercent(){
    $percent = 0.5;
    $siteID = $this->room->site_id;
    $paymentRule = \App\Settings::where('key','payment_rule')->where('site_id',$siteID)->first();
    if ($paymentRule){
      
      $rule = json_decode($paymentRule->content);
      if ($rule){
        $date    = Carbon::createFromFormat('Y-m-d', $this->start);
        $now     = Carbon::now();
        $diff    = $now->diffInDays($date);
        $numDays = $rule->days;
        if ($diff <= $numDays){
          return 1;
        }
        return $rule->percent/100;
      }
      
    }
    return 0.5;
  }
  
  function printExtraIcon(){
    $breakfast = 0;
    $excursion = 0;
    $parking = 0;
    
    if (isset($this->extrasBook)){
      $extras = [];
      foreach ($this->extrasBook as $e){
        if ($e->deleted == 0){
          
          if ($e->type == 'breakfast') $breakfast += $e->qty;
          if ($e->type == 'excursion') $excursion += $e->qty;
          if ($e->type == 'parking') $parking += $e->qty;
        }
      }
    }
    if ($breakfast>0) echo '<icon><i class="fas fa-coffee" title="Desayuno incluido ('.$breakfast.')" ></i>'.$breakfast.'</icon>';
    if ($excursion>0) echo '<icon><i class="fas fa-guitar" title="Excursiones ('.$excursion.')"></i>'.$excursion.'</icon>';
    if ($parking>0)   echo '<icon><i class="fas fa-parking" title="Parking ('.$parking.')"></i>'.$parking.'</icon>';
  }
  
  function getExtraInfo(){
    $breakfast = $pvpB = 0;
    $excursion = $pvpE = 0;
    $parking   = $pvpP = 0;
    
    if (isset($this->extrasBook)){
      $extras = [];
      foreach ($this->extrasBook as $e){
        if ($e->deleted == 0){
            switch ($e->type){
                case 'breakfast':
                    $breakfast += $e->qty;
                    $pvpB += $e->price;
                    break;
                case 'parking':
                    $parking += $e->qty;
                    $pvpP += $e->price;
                    break;
                case 'excursion':
                    $excursion += $e->qty;
                    $pvpE += $e->price;
                    break;
            }
        }
      }
    }
    $result = '';
    if ($pvpB>0) $result .= '<br /> '.$breakfast.' Desayuno(s): *'.moneda($pvpB).'* ';
    if ($pvpP>0) $result .= '<br /> '.$parking.' Tiket(s) Parkings: *'.moneda($pvpP).'* ';
    if ($pvpE>0) $result .= '<br /> '.$excursion.' Tiket(s) Excursión: *'.moneda($pvpE).'* ';
    return $result;
  }
  
  public function extrasDynamicList() {
    return $this->hasMany('\App\BookExtraPrices', 'book_id', 'id')->where('deleted',0)->where('fixed', 0)->get();
  }
  
  public function extrasDynamicCost($type) {
    return $this->hasMany('\App\BookExtraPrices', 'book_id', 'id')
            ->where('deleted',0)
            ->where('fixed', 0)
            ->where('type', $type)
            ->sum('cost');
  }
  
    
  static function getBy_temporada(){
    // $activeYear = Years::getActive();
    $activeYear = getObjYear();
    return Book::where_type_book_sales()
            ->where('start', '>=', $activeYear->start_date)
            ->where('start', '<=', $activeYear->end_date)->get();
  }
  
  public function getMinStay() {
    $room = $this->room()->first();
    
    if ($room){
      $minDay = $room->getMin_estancia($this->start,$this->finish);
      if ($minDay){
        return $minDay;
      }
    }
    return 0;
  }
  
  /**
   * Show the price cell in plannings
   * @param type $payment
   */
  public function showPricePlanning($payment) {
    
    $pay = isset($payment[$this->id]) ? $payment[$this->id] : null;
    $color = (intval($this->total_price-$pay)<1)? '' : ' style="color:red" ';
    
    echo '<div class="col-xs-6 not-padding">'.round($this->total_price) . '€';
    if ($pay !== null)
      echo '<p '.$color.'>'.round($pay).'€</p>';
    echo '</div>';
     if ($pay){ 
        $bg = '';
        if ($pay && $pay > 0 && $this->total_price > 0)
          $total = number_format(100 / ($this->total_price / $pay), 0);
        else {
          $total = 0;
          $bg = 'bg-success';
        }
        
        echo '<div class="col-xs-6 pay-percent '.$bg.'">
                <b '.$color.'>'.$total.'%</b>
              </div>';
        
     } else { 
       
       echo '<div class="col-xs-6 pay-percent bg-success">
                  <b style="color: red;">0%</b>
                </div>';
     }
           
  }
  
  public function getCustomerName() {
    $cust = $this->customer;
    if ($cust) return $cust->name;
    return '--';
  }
  
    
  static function cliHas_lst($bIDs){
    $result = [[],[]];
    $cliHas = BookData::whereIn('key',['client_has_beds','client_has_babyCarriage'])
            ->where('content',1)
            ->whereIn('book_id',$bIDs)->get();
    if ($cliHas){
      foreach ($cliHas as $d){
        if ($d->key == 'client_has_beds') $result[0][] = $d->book_id;
        else $result[1][] = $d->book_id;
      }
    }
    return $result;
  }
  
  /**********************************************************************/
  /////////  book_meta //////////////
  public function setMetaContent($key,$content) {
      
      
    $updated =  DB::table('book_meta')->where('book_id',$this->id)
              ->where('meta_key',$key)
              ->update(['meta_value' => $content]);

    if (!$updated) {
      DB::table('book_meta')->insert(
            ['book_id' => $this->id, 'meta_key' => $key,'meta_value' => $content]
        );
    }
  }
  
  public function getMetaContent($key) {
    
    $book_meta = DB::table('book_meta')
            ->where('book_id',$this->id)->where('meta_key',$key)->first();
    
    if ($book_meta) {
      return $book_meta->meta_value;
    }
    return null;
  }
}

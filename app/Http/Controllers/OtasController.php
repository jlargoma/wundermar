<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use \Carbon\Carbon;
use Auth;
use App\Services\OtaGateway\OtaGateway;
use App\Services\OtaGateway\Config as oConfig;
use App\Rooms;
use App\DailyPrices;
use App\Traits\OtasTraits;
use App\Traits\LoadByOTA;

class OtasController extends AppController {

  //https://swagger.sandbox.reservationsteps.ru/?urls.primaryName=English#/

  use OtasTraits,LoadByOTA;

  private $aptos;
  private $sOta;
  var $oConfig;

  function __construct() {
    $this->sOta = new OtaGateway();
    $this->aptos = configZodomusAptos();
    $this->oConfig = new oConfig();
  }

  function index($apto = null) {
    $oConfig = new oConfig();
    $aptos = configZodomusAptos();
    $aRooms = [];
    $oRooms = Rooms::orderBy('name')->get();
    foreach ($oRooms as $r) {
      if (!isset($aRooms[$r->channel_group]))
        $aRooms[$r->channel_group] = [];

      $aRooms[$r->channel_group][] = $r->name . " ( $r->nameRoom )";
    }
    $channels = [];
    if (!$apto) {
      $aux = reset($aptos);
      $apto = null;
    }

    return view('backend/prices/avails', [
        'aptos' => $aptos,
        'apto' => $apto,
        'aRooms' => $aRooms,
        'channels' => $channels,
    ]);
  }

  /**
   * /admin/otaGate/test
   */
  function test() {
    $oConfig = new oConfig();
//   $rooms = $oConfig->getRooms();
//   dd($rooms);
   // $conexion = $this->sOta;
     $this->createBooking();
//    $this->getBooking();
    //  $this->createWebHook();
//    $this->setRates();
//    $this->setRooms(2);
//    $this->setMinStay();
//    $this->sendAvail();
  }

  function setRooms($siteID) {
    $aptos = getAptosBySite($siteID);
    $oOta = new OtaGateway();
    $start = date('Y-m-d');
    $finish = '2021-' . date('m-d');
    foreach ($aptos as $channel_group) {

      $oRooms = Rooms::where('channel_group', $channel_group)->pluck('id')->toArray();

      $match1 = [['start', '>=', $start], ['start', '<=', $finish]];
      $match2 = [['finish', '>=', $start], ['finish', '<=', $finish]];
      $match3 = [['start', '<', $start], ['finish', '>', $finish]];

      $books = \App\Book::where_type_book_reserved()->whereIn('room_id', $oRooms)
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
      while ($startAux < $endAux) {
        $aLstDays[date('Y-m-d', $startAux)] = $avail;
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
            $keyControl = $book->room_id . '-' . $auxTime;
            if (!in_array($keyControl, $control)) {
              if (isset($aLstDays[$auxTime]))
                $aLstDays[$auxTime]--;

              $control[] = $keyControl;
            }

            $startAux = strtotime("+1 day", $startAux);
          }
        }
      }
      $oOta->conect();
      $return = $oOta->sendAvailabilityByCh($channel_group, $aLstDays);
    }

//     $return = $this->oConfig->setRooms();
////     $return = $this->sOta->getRooms();
//      dd($return);
  }

  function getBooking() {

//  $param = ['D953V_280720','NUDDS_280720','VUPSA_280720'];
//  $this->loadBooking($param);
//  $cg='DDE';
//    $ext = ['2931064261','3521844214'];
//  $response = $this->sOta->getBooking(null);
//  foreach ($response->bookings as $b){
//    if (in_array($b->ota_booking_id, $ext))
//      echo $b->surname .' '.$b->surname.' > '.$b->number.' - '. $b->ota_booking_id.'      '.$b->status_id.'<br>';
////    if($b->status_id ==2 ){ echo '"'. $b->ota_booking_id.'",<br>';  }
//  }
//  dd($response);
  }

  function sendAvailTest() {
    //Prepara la disponibilidad por día de la reserva
    //desde el 28-03-2021
//      $startAux = strtotime('2021-03-28');//time();
//      $endAux = strtotime('2021-03-28'. ' +1 year');
//      $ogAvail = [];
//      while ($startAux<$endAux){
//        $ogAvail[date('Y-m-d',$startAux)] = 1;
//        $startAux = strtotime("+1 day", $startAux);
//      }

    $ogAvail = ["2020-10-28" => 1, "2020-10-29" => 2, "2020-10-30" => 1];
    $return = $this->sOta->sendAvailability(['availability' => [52355 => $ogAvail]]);
    dd($return, $this->sOta->response);
  }

  function setMinStay() {
    $stay = ['min_stay' => 1];
    $stay2 = ['min_stay' => 3];
    $aux = ["2020-11-28" => $stay, "2020-11-29" => $stay2];
    $restrictions = [61165 => $aux]; //, 60625=>$aux];//RoomsID
    $param = [
        "restriction_plan_id" => 1634598, //restriction_plan_id "Booking.com",
        "restrictions" => $restrictions
    ];
    $return = $this->sOta->setMinStay($param);
    dd($return, $this->sOta);
  }

  function setRates() {

    $aux = ["2020-11-28" => 5600, "2020-11-29" => 6600, "2020-11-30" => 7600];
    $aux = ["2020-12-18" => 5600];
    $prices = [61165 => $aux, 61166 => $aux]; //RoomsID
    $prices = [52355 => $aux]; //RoomsID
    $param = [
        "plan_id" => 25057, //Plan "Booking.com",
        "price" => $prices
    ];

//     'SILOE_4' => 60624,
//      'SILOE_6' => 60625,
    $return = $this->sOta->setRates($param);
    dd($return, $this->sOta);
  }

  function createWebHook() {
//   +"webhook_id": 5606
    $url = 'https://admin.riadpuertasdelalbaicin.net/Ota-Gateway-Webhook';

//    https://admin.riadpuertasdelalbaicin.com/Ota-Gateway-Webhook/1
    $param = [
        "type" => "bookings",
        "url" => $url,
    ];
    $return = $this->sOta->createWebhook($param);
    dd($return);
  }

  function createBooking() {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/public/tests/ota-bookings.php';
    $oBookings = \json_decode($bookings);
//    dd($oBookings);
    if (isset($oBookings->bookings))
      $oBookings = $oBookings->bookings;
    if (!$oBookings) {
      var_dump('booking no found');
      return null;
    }
    $this->loadBooking($oBookings);
  }

  /*   * *************************************************************** */
  /*   * *************************************************************** */
  /*   * *************************************************************** */
  /*   * *************************************************************** */
  /*   * *************************************************************** */

  
    function sendAvail(Request $request, $apto) {
         
    //BEGIN wubook
    $oAux = \App\ProcessedData::findOrCreate('sendAvail');
    $oAux->content=time();
    $oAux->save();
    //END wubook

    if ($apto == 'allSeasson'){
      $oYear = \App\Years::where('active', 1)->first();
      $sentData = \App\ProcessedData::findOrCreate('send_dispSeason_'.$oYear->id);
      $sentData->content = 1;
      $sentData->save();
      $startTime = $oYear->start_date;
      $endTime = $oYear->end_date;
      $apto = 'all';
    } else {
      $date_range = $request->input('date_range', null);

      if (!$date_range)
        return back()->withErrors(['Debe seleccionar al menos una fecha de inicio']);

      $date = explode(' - ', $date_range);
      $startTime = (convertDateToDB($date[0]));
      $endTime = (convertDateToDB($date[1]));
    }
    if ($apto == 'all'){
      $aptos = configZodomusAptos();
      if (isset($aptos['ELV'])) unset($aptos['ELV']);
      $book = new \App\Book();
      foreach ($aptos as $ch=>$v){
        $room = Rooms::where('channel_group',$ch)->first();
        if ($room){
          $resp = $book->sendAvailibility($room->id,$startTime,$endTime);
          if (!$resp) return back()->withErrors(['ocurrió un error al enviar los datos']);
        }
      }
      return back()->with(['success'=>'Disponibilidad enviada']);
    } else {
      $room = Rooms::where('channel_group',$apto)->first();
      if ($room){
        $book = new \App\Book();
        $resp = $book->sendAvailibility($room->id,$startTime,$endTime);
        if (!$resp) return back()->withErrors(['ocurrió un error al enviar los datos']);
        return back()->with(['success'=>'Disponibilidad enviada']);
      } else {
        return back()->withErrors(['No posee apartamentos asignados']);
      }
    }
    
  }
  /**
   * 
   * @param Request $request
   * rcode (the reservation code) 
   * lcode (the property identifier, 
   */
  public function webHook($siteID, Request $request) {

    $params = $request->all();
    //save a copy
    $json = json_encode($params);
    $dir = storage_path() . '/OtaWateway';
    if (!file_exists($dir)) {
      mkdir($dir, 0775, true);
    }
    file_put_contents($dir . "/" . time(), $json);

    $data = $request->input('data', null);
    if (!is_array($data)) {
      $data = json_decode($data, true);
    }

    if (isset($data)) {
      $data = (isset($data['request'])) ? $data['request']['data'] : $data;
      if (isset($data['booking_numbers'])) {
        $this->sOta->conect($siteID);
        //BEGIN: getBookings
        $response = $this->sOta->getBooking($data['booking_numbers']);
        $oBookings = null;
        if (isset($response->booking))
          $oBookings = [$response->booking];
        if (isset($response->bookings))
          $oBookings = $response->bookings;
        if (!$oBookings) {
          var_dump('booking no found', $data['booking_numbers']);
        } else {
          $this->loadBooking($oBookings);
        }
        //END: getBookings
        $this->sOta->disconect($siteID);
      } else {
        var_dump('empty data', $data);
      }
    } else {
      var_dump('Unset data', $data);
    }
    return response('', 200);
  }

    /**
   * 
   * @param Request $request
   * rcode (the reservation code) 
   * lcode (the property identifier, 
   */
  public function webHook_Wubook(Request $request) {
    
    $rcode = $request->input('rcode');
    $lcode = $request->input('lcode');
    
         
    //save the params to response quikly the HTTP 200
    $oData = \App\ProcessedData::findOrCreate('wubook_webhook');
    $content = json_decode($oData->content,true);
    if (!$content || !is_array($content)) $content = [];
      
    $content[] = [
          'date' =>time(),
          'rcode'=>$rcode,
          'lcode'=>$lcode,
      ];
    
    $oData->content = json_encode($content);
    $oData->save();
    
    //save a copy
    $json = json_encode($request->all());
    $dir = storage_path().'/wubook';
    if (!file_exists($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents($dir."/".time().'-'.$rcode.'-'.$lcode,$json);
    
    return response('',200);
  }
  

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function pricesOTAs() {

    $year = $this->getActiveYear();
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);

    $otaConfig = new \App\Services\OtaGateway\Config();
    $agencies = $otaConfig->getAllAgency();
    $rooms = $otaConfig->getRoomsName();
    $prices_ota = \App\Settings::getContent('prices_ota');
    if ($prices_ota) {
      $prices_ota = unserialize($prices_ota);
    } else {
      $prices_ota = [];
    }
    $aPricesOta = [];
    foreach ($rooms as $k => $n) {
      foreach ($agencies as $name => $id)
        $aPricesOta[$k . $id] = isset($prices_ota[$k . $id]) ? $prices_ota[$k . $id] : ['f' => 0, 'p' => 0];
      
      $aPricesOta[$k.'t'] = isset($prices_ota[$k.'t']) ? $prices_ota[$k.'t'] : '';
    }

    /*     * ********************************************************************* */
    $sentData = \App\ProcessedData::findOrCreate('create_baseSeason_' . $year->id);
    $sendDataInfo = 'No ha sido enviado aún';
    if ($sentData->content) {
      $sentData->content = json_decode($sentData->content);
      $sendDataInfo = 'Enviado el ' . convertDateTimeToShow_text($sentData->updated_at);
      $sendDataInfo .= "\n" . 'Por ' . $sentData->content->u;
    }

    return view('backend/prices/pricesOTAs', [
        'aPricesOta' => $aPricesOta,
        'agencies' => $agencies,
        'sendDataInfo' => $sendDataInfo,
        'rooms' => $rooms,
    ]);
  }

  public function pricesOTAsUpd(Request $request) {

    $otaConfig = new \App\Services\OtaGateway\Config();
    $agencies = $otaConfig->getAllAgency();
    $rooms = $otaConfig->getRoomsName();

    $prices_ota = null;
    $oSetting = \App\Settings::where('key', 'prices_ota')->first();
    if ($oSetting) {
      $prices_ota = $oSetting->content;
    } else {
      $oSetting = new \App\Settings();
      $oSetting->key = 'prices_ota';
      $oSetting->value = '';
      $oSetting->site_id = 1;
      $oSetting->name = "Porcentajes y extras de las OTAs";
    }

    if ($prices_ota) {
      $prices_ota = unserialize($prices_ota);
    } else {
      $prices_ota = [];
    }

    $aPricesOta = [];
    foreach ($rooms as $k => $n) {
      foreach ($agencies as $name => $id)
        $aPricesOta[$k . $id] = isset($prices_ota[$k . $id]) ? $prices_ota[$k . $id] : ['f' => 0, 'p' => 0];
      
      $aPricesOta[$k.'t'] = isset($prices_ota[$k.'t']) ? $prices_ota[$k.'t'] : '';
    }

    $ota = $request->input('ota');
    $room = $request->input('room');
    $type = $request->input('type');
    
    if ($ota == 0 && $type == 't'){
      $key = $room.'t';
      $aPricesOta[$key] = $request->input('val');
    } else {
      $key = $room.$ota;
      if (isset($aPricesOta[$key])){
        $aPricesOta[$key][$type] = intval($request->input('val'));
      }
    }
    $oSetting->content = serialize($aPricesOta);
    $oSetting->save();

    return response()->json(['status' => 'OK', 'msg' => 'datos cargados']);
  }

 

}

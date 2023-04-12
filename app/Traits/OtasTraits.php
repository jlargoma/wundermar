<?php

namespace App\Traits;

use App\Settings;
use Carbon\Carbon;
use App\Repositories\CachedRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\DailyPrices;
use App\Rooms;

trait OtasTraits
{
 /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function calendRoom($room = null) {

    $oConfig = $this->oConfig;
    
    $agencies = $oConfig->getAllAgency();
    $roomsLst = $oConfig->getRoomsName();
    
    $rooms = [];
    foreach ($roomsLst as $k => $name) {
      if (!$room) $room = $k;
      $rooms[$k] = $name;
    }

    $dw = listDaysSpanish(true);
    $data = [
        'rooms' => $rooms,
        'room' => $room,
        'dw' => $dw,
        'price_booking' => 0,
        'price_expedia' => 0,
        'price_airbnb' => 0,
        'price_google' => 0,
    ];
    foreach ($agencies as $ag=>$k){
      if ($ag == 'google-hotel') $ag = 'google';
      $data['price_'.$ag] = $oConfig->priceByChannel(0,$k,$room,true);
    }
    
    return view('backend.prices.cal-room', $data);
  }

  /**
   * 
   * @param Request $request
   * @param type $apto
   * @return type
   */
  public function calendRoomUPD(Request $request, $apto) {
    if ($apto == 'ALL'){ //update by siteID
      $site_ids = $request->input('site_ids',[]);
      $count = 0;
      $min_estancia = $request->input('min_estancia', null);
      if (count($site_ids) == 0)
        return response()->json(['status'=>'error','msg'=>'Debe seleccionar al menos un edificio']);

      if ((!$min_estancia || $min_estancia<0) )
        return response()->json(['status'=>'error','msg'=>'Debe seleccionar la estancia mínima']);

     
    
      foreach ($site_ids as $site){
        $aptos = getAptosBySite($site);
        
        foreach ($aptos as $k){
          $resp = $this->calendRoomUPD_byRoom($request, $k,$site);
          $count++;
        }
      }
      // save log data
      $lData = new \App\LogsData();
          
      $date_range = $request->input('date_range', null);
      $date = explode(' - ', $date_range);
      $startTime = convertDateToDB($date[0]);
      $endTime = convertDateToDB($date[1]);
      
      $weekDays = [];
      $diaSemana = listDaysSpanish(true); 
      for($i=0;$i<7;$i++){
        if ($request->input('dw_' . $i, null)){
          $weekDays[] = $diaSemana[$i];
        }
      }
 
      $dataLog = [
          'site_ids' => $site_ids,
          'min_estancia' => $min_estancia,
          'startDate'=> $startTime,
          'endDate'  => $endTime,
          'weekDays' => implode(', ', $weekDays),
          'userID'   => Auth::user()->id
      ];
      $lData->key  = "min_stay_sites";
      $lData->data =  $count.' Registros cargados';
      $lData->long_info = json_encode($dataLog);
      $lData->save();
            
            
            
      return response()->json(['status'=>'OK','msg'=>'datos cargados']);
    } else {
      $allCH = getAptosSite();
      $siteID = null;
      foreach ($allCH as $site => $aptos)
        if (in_array($apto, $aptos)) $siteID = $site;
        return $this->calendRoomUPD_byRoom($request, $apto,$siteID);
    }
  }
  
  /**
   * 
   * @param Request $request
   * @param type $apto
   * @return type
   */
  public function calendRoomUPD_byRoom(Request $request, $apto,$siteID) {
    
    $date_start = $request->input('date_start', null);
    $date_end   = $request->input('date_end', null);

    $price = $request->input('price', null);
    $min_estancia = $request->input('min_estancia', null);

    if (!$siteID)
      return response()->json(['status'=>'error','msg'=>'Sitio no encontrado']);
    if (!$date_start || !$date_end)
      return response()->json(['status'=>'error','msg'=>'Debe seleccionar al menos una fecha de inicio']);

    if ((!$price || $price<0) && (!$min_estancia || $min_estancia<0) )
      return response()->json(['status'=>'error','msg'=>'Debe seleccionar el precio o estancia mínima']);

    
    $startTime = strtotime($date_start);
    $endTime = strtotime($date_end);
    $dw = listDaysSpanish(true);
    $dayWeek = [];
    
    $weekDaysID =  [
          0=>"sun",
          1=>"mon",
          2=>"tue",
          3=>"wed",
          4=>"thu",
          5=>"fri",
          6=>"sat",
        ];
            
    $weekDays = [];
    foreach ($dw as $k => $v) {
      if ($request->input('dw_' . $k, null)){
        $dayWeek[] = $k;
        $weekDays[] = $weekDaysID[$k];
      }
    }
    $weekDays = implode('|', $weekDays);
    
    
   
    if (count($dayWeek) == 0)
      return response()->json(['status'=>'error','msg'=>'Debe seleccionar al menos un día de la semana']);
    
    
    $uID = Auth::user()->id;
    
    $startTimeAux = $startTime;
    $aPrices = $aMinStay = [];
    while ($startTimeAux<=$endTime){
      $dWeek = date('w',$startTimeAux);
      if (in_array($dWeek,$dayWeek)){
        $dateItem = date('Y-m-d',$startTimeAux);
        $oPrice = DailyPrices::where('channel_group', $apto)
              ->where('date', '=', $dateItem)
              ->first();
        if (!$oPrice){
          $oPrice = new DailyPrices();
          $oPrice->channel_group = $apto;
          $oPrice->date = $dateItem;
        }
        if($price && $price>0){
          $oPrice->price = $price;
          $aPrices[$dateItem] = $price;
        }
        if($min_estancia && $min_estancia>0){
          $oPrice->min_estancia = $min_estancia;
          $aMinStay[$dateItem] = ['min_stay'=> intval($min_estancia)];
        }
        $oPrice->user_id = $uID;
        $oPrice->save();
      }
      $startTimeAux = strtotime('+1 day', $startTimeAux);
    }

    $roomTypeID = $this->oConfig->getRooms($apto);

    
    $OtaGateway = new \App\Services\OtaGateway\OtaGateway();
    if (!$OtaGateway->conect($siteID) )
      return response()->json(['status'=>'error','msg'=>'Ota no conectada']);
    
    $resp = false;
    $keyWubook = null;
    if (count($aPrices)){
      $resp = $OtaGateway->setRates(["price"=>[$roomTypeID=>$aPrices]]);
      $keyWubook = 'wubookRate';
    }
    
    if (count($aMinStay)){
      $keyWubook = 'wubookMinStay';
      $resp = $OtaGateway->setMinStay(['restrictions'=>[$roomTypeID=>$aMinStay]]);
    }
    //BEGIN wubook
    if($keyWubook){
      $oAux = \App\ProcessedData::findOrCreate($keyWubook);
      $oAux->content=time();
      $oAux->save();
    }
    //END wubook
    
    if ($resp){
      return response()->json(['status'=>'OK','msg'=>'datos cargados y enviados']);
    } else {
      return response()->json(['status'=>'error','msg'=>'Datos cargados, pero ocurrió un error al enviarlos']);
    }
    
  }
  
  
    /**
   * Para el calendation por habitación
     * 
   * @param Request $request
   * @param type $apto
   * @return type
   */
  public function listBy_room(Request $request, $apto) {

    $prices = [];
    $start = $request->input('start', null);
    $end = $request->input('end', null);

    $room = Rooms::where('channel_group',$apto)->first();
    if (!$room)
      return null;

    $pax = $room->minOcu;
    if ($start && $end) {
      
      $start = (convertDateToDB($start));
      $end = (convertDateToDB($end));


      $defaults = $room->defaultCostPrice($start, $end, $pax);
      $priceDay = $defaults['priceDay'];
      $min = [];
      
      $oPrice = DailyPrices::where('channel_group', $apto)
              ->where('date', '>=', $start)
              ->where('date', '<=', $end)
              ->get();
      if ($oPrice) {
        foreach ($oPrice as $p) {
          if (isset($priceDay[$p->date]) && $p->price)
            $priceDay[$p->date] = $p->price;
          $min[$p->date] = $p->min_estancia;
        }
      }
      $priceLst = [];
      $redDays = [];
      $agencies = $this->oConfig->getAllAgency();
      
      foreach ($priceDay as $d => $p) {
        $data = [
            'price_booking' => 0,
            'price_expedia' => 0,
            'price_airbnb' => 0,
            'price_google' => 0,
        ];
        foreach ($agencies as $ag=>$k){
          if ($ag == 'google-hotel') $ag = 'google';
          $data['price_'.$ag] = $this->oConfig->priceByChannel($p,$k,$apto);
        }
    
        $min_estancia = isset($min[$d]) ? $min[$d] : 0;
        
        
        $priceLst[] = [
            "title" => ''.$p.' €<p class="min-estanc">'.$min_estancia.' dias</p>'
            . '<table class="t-otas">'
            . '<tr><td><span class="price-booking">'.$data['price_booking'].'</span></td><td><span class="price-airbnb">'.$data['price_airbnb'].'</span></td></tr>'
            . '<tr><td><span class="price-expedia">'.$data['price_expedia'].'</span></td><td><span class="price-google">'.$data['price_google'].'</span></td></tr>'
            . '</table>',
            "start" => $d,
            'classNames' => 'prices',
        ];

      }

      $book = new \App\Book();
      $availibility = $book->getAvailibilityBy_channel($apto, $start, $end);
      foreach ($availibility as $d => $p) {
        $class = ($p>0) ? 'yes' : 'no';
        
        if ($p<=0) $redDays[] = $d;
       
        $priceLst[] = [
            "title" => $p,
            "start" => $d.' 01:00',
            'classNames' =>  'availibility '.$class
        ];

      }

      return response()->json(['priceLst' => $priceLst,'redDays'=>$redDays]);
    }




    return response()->json($prices);
  }
  
  
    /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  function calendSite($site = 1,$month=null,$year=null) {
    $oConfig = $this->oConfig;
    $agencies = $oConfig->getAllAgency();
    $roomsLst = $oConfig->getRoomsName();
    $roomsSite = getAptosBySite($site);
    foreach ($roomsLst as $k=>$v){
      if (!in_array($k,$roomsSite)) unset ($roomsLst[$k]);
    }
    $months = getMonthsSpanish(null,false,true);
    
    /**************************************************************************************/
    
    //Armo el calendario
    if(!$month) $month = date('m');
    if(!$year) $year = date('Y');
    
    $days = [];
    $dateTime = strtotime("$year-$month-01");
    $current = getMonthsSpanish($month,false).' '.$year;
    $start = date('Y-m-d',$dateTime);
    
    
    $dateTime = strtotime('-1 day', $dateTime);
    for($i=0;$i<35;$i++){
      $dateTime = strtotime('+1 day', $dateTime);
      
      $days[date('Y-m-d',$dateTime)] = [
          'day' => date('d',$dateTime),
          'w' => date('w',$dateTime),
          'month' => date('n',$dateTime),
          'monthText' => getMonthsSpanish(date('n',$dateTime)),
          'rooms' => []
      ];
    }
    $finish = date('Y-m-d',$dateTime);
    
    $aMonth = [];
    $count = 0;
    $aux = true;
    foreach ($days as $k=>$day){
      $count++;
      if ($month != $day['month'] && $aux){
        $aux = false;
        $aMonth[] = ['colspan'=>$count-1,'text'=>getMonthsSpanish($month)];
        $count = 1;
      } elseif($count>6){
        $aMonth[] = ['colspan'=>$count,'text'=>getMonthsSpanish($month)];
        $count = 0;
      }
    }
    if ($count>0)   $aMonth[] = ['colspan'=>$count,'text'=>getMonthsSpanish($day['month'])];
    //END: Armo el calendario
    
    
    // listo los Channels Group del sitio
    $rooms = [];
    foreach ($roomsLst as $room => $roomNAme) {
        $dataAux =[
            'tit' => $roomNAme,
            'price_booking' => 0,
            'price_expedia' => 0,
            'price_airbnb'  => 0,
            'price_google'  => 0,
            'price_agoda'  => 0,
        ];
        
        
        foreach ($agencies as $ag=>$agencID){
          if ($ag == 'google-hotel') $ag = 'google';
          $dataAux['price_'.$ag] = $oConfig->priceByChannel(0,$agencID,$room,true);
        }
        $rooms[$room] = $dataAux;
    }
    //END: listo los Channels Group del sitio
    
    
    //Cargo la disponibilidad y precios por día
    foreach ($rooms as $k=>$v){
      $rooms[$k]['data'] = $this->getPriceDay_group($k,$start,$finish);
    }
    $dw = listDaysSpanish(true);

    return view('backend.prices.cal-sites', [
        'rooms' => $rooms,
        'site' => $site,
        'dw' => $dw,
        'days' => $days,
        'month' => $month,
        'monthsLst' => $months,
        'otaAvail' => $this->getOtaAvail($start,$finish,$site),
        'year' => $year,
        'aMonth' => $aMonth,
        'current' => $current,
        'prev' => date('m/Y',strtotime('-1 month'.$start)),
        'next' => date('m/Y',strtotime('+1 month'.$start)),
    ]);
  
  }
  
  function getPriceDay_group($ch,$start,$end){
    // public function listBy_room(Request $request, $apto) {
    $prices = [];
    $oConfig = $this->oConfig;
    $room = Rooms::where('channel_group',$ch)->first();
    if (!$room)   return null;
    $defaults = $room->defaultCostPrice($start, $end, $room->pax);
    $priceDay = $defaults['priceDay'];
    $costDay = $defaults['costDay'];
    $costOthers = \App\ExtraPrices::getFixed($room->channel_group)->sum('cost');
    /* ------------------------------------------------------- */
    
    $min = [];
    $oPrice = DailyPrices::where('channel_group', $ch)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end)
            ->get();
    if ($oPrice) {
      foreach ($oPrice as $p) {
        if ($p->price) $priceDay[$p->date] = $p->price;
        $min[$p->date] = $p->min_estancia;
      }
    }
    $priceLst = [];
    $redDays = [];
    foreach ($priceDay as $d => $p) {
      $min_estancia = isset($min[$d]) ? $min[$d] : 0;
      $pvpGH = ceil($oConfig->priceByChannel($p,7,$ch));
      
      $inc_percent = $tCost = 0;
      if ($pvpGH > 0 && isset($costDay[$d])) {
        $tCost = $costDay[$d]+$costOthers;
        $profit = $pvpGH-$tCost;
        $inc_percent = intval(($profit / $pvpGH ) * 100,0);
      }
      
      $priceLst[$d] = [
          $p,
          $min_estancia,
          'booking'=>ceil($oConfig->priceByChannel($p,1,$ch)),
          'expedia'=>ceil($oConfig->priceByChannel($p,6,$ch)),
          'airbnb'=>ceil($oConfig->priceByChannel($p,4,$ch)),
          'google'=>ceil($pvpGH),
          'inc_percent'=>$inc_percent,
        ];
    }
    $book = new \App\Book();
    $availibility = $book->getAvailibilityBy_channel($ch, $start, $end,true);
    
    return [
        'priceLst' => $priceLst,
        'avail'=>$availibility[0],
        't_rooms'=>$availibility[1]
    ];
      
  }
  
    /**
   * Update Price or Min by cal-2
   * @param Request $request
   * @return type
   */
  function calendSiteUpd(Request $request){
  
    $items = $request->input('items');
    $val = $request->input('val');
    $type = $request->input('type');
    $siteID = $request->input('siteID');
    $lst = array();
    $lstAllDays = array();
    
    if ($type == 'price') $val = floatval ($val);
    if ($type == 'minDay') $val = intval ($val);

    if (!$val || $val<0)
      return response()->json(['status'=>'error','msg'=>'Debe seleccionar el valor a modificar']);

    if (!$type)
      return response()->json(['status'=>'error','msg'=>'Error de tipo de datos']);

    if (!$items || count($items)<1)
      return response()->json(['status'=>'error','msg'=>'Error de datos']);
      
      
    if ($items){
      foreach ($items as $v){
        $aux = explode('@',$v);
        if (!isset($lst[$aux[0]])) $lst[$aux[0]] = array();
        if (!isset($lstAllDays[$aux[0]])) $lstAllDays[$aux[0]] = array();
         if ($type == 'minDay')
           $lst[$aux[0]][$aux[1]] = ['min_stay'=> $val];
         else
           $lst[$aux[0]][$aux[1]] = $val;
           
        $lstAllDays[$aux[0]][] = $aux[1];

      }
    }
    $otaRooms = $this->oConfig->getRooms();
    
//  ---------------------------------------------------------------------------
    $lstRoomsOta = [];
    foreach ($lst as $ch=>$items){
      if (isset($otaRooms[$ch])){
        $lstRoomsOta[$otaRooms[$ch]] = $items;
      }
    }
    
    $this->saveTable($lstAllDays,$type,$val);
    
    
    $OtaGateway = new \App\Services\OtaGateway\OtaGateway();
    if (!$OtaGateway->conect($siteID) )
      return response()->json(['status'=>'error','msg'=>'Ota no conectada']);
    
    $keyWubook = null;
    if ($type == 'price'){
      $resp = $OtaGateway->setRates(["price"=>$lstRoomsOta]);
      $keyWubook = 'wubookRate';
    }
    if ($type == 'minDay'){
      $resp = $OtaGateway->setMinStay(['restrictions'=>$lstRoomsOta]);
      $keyWubook = 'wubookMinStay';
    }

    //BEGIN wubook
    if($keyWubook){
      $oAux = \App\ProcessedData::findOrCreate($keyWubook);
      $oAux->content=time();
      $oAux->save();
    }
    //END wubook


//    ---------------------------------------------------------------------------
  return response()->json(['status'=>'OK','msg'=>'datos cargados']);
 
  }
  
  private function saveTable($lst, $type, $val) {
    //  ---------------------------------------------------------------------------
    //save registers
    $uID = Auth::user()->id;

    $inset = [];
    foreach ($lst as $ch => $dates) {
      $auxDates = $dates;
      $oPrice = DailyPrices::where('channel_group', $ch)
          ->whereIn('date', $dates)
          ->get();
      if ($oPrice) {
        foreach ($oPrice as $p) {
          if (($key = array_search($p->date, $dates)) !== false) {
            unset($auxDates[$key]);
            if ($type == 'price')
              $p->price = $val;
            if ($type == 'minDay')
              $p->min_estancia = $val;
            $p->user_id = $uID;
            $p->save();
          }
        }
      }
      if (count($auxDates) > 0) {
        foreach ($auxDates as $date) {
          $inset[] = [
              'price' => ($type == 'price') ? $val : null,
              'min_estancia' => ($type == 'minDay') ? $val : null,
              'user_id' => $uID,
              'channel_group' => $ch,
              'date' => $date
          ];
        }
      }
    }
    if (count($inset) > 0) {
      DailyPrices::insert($inset);
    }

  }

  
  function getOtaAvail($start,$end,$sID){
  
    $cKey = md5('OA'.$start.$end.$sID);
    $sCache = new \App\Services\CacheData($cKey);
    $cache = $sCache->get();
    if ($cache) return $cache;
    
    $oConfig = $this->oConfig;
    $OtaGateway = new \App\Services\OtaGateway\OtaGateway();
    $aRoomIDs = $this->oConfig->getRooms();
    $auxDay = arrayDays($start,$end,'Y-m-d',0);
    $result = [];
    foreach ($aRoomIDs as $ch=>$r){
      $result[$ch] = $auxDay;
    }
    
    if (!$OtaGateway->conect($sID))   return 'Ota no conectada';
    $avail = $OtaGateway->getAvailability($start,$end);
    if (isset($avail->availability)){
      foreach ($avail->availability as $rID => $a){
        $ch = array_search($rID, $aRoomIDs);
        if($ch){
          foreach ($a as $date => $av)
            $result[$ch][$date]=$av;
        }
      }
    }
    
    $sCache->set($result);
    return $result;
  }

   
   
  /**
   * Display a listing of prices with diff.
   *
   * @return \Illuminate\Http\Response
   */
  function controlOta() {
    $oConfig = $this->oConfig;
    
      
    $aux = $aux2 = [];
    $oLst = \App\PricesOtas::all();
    foreach ($oLst as $item){
      $plan = $item['plan'];
      $ch = $item['ch'];
      if (!isset($aux[$plan])) $aux[$plan] = [];
      if (!isset($aux[$plan][$ch]))  $aux[$plan][$ch] = [];
      $aux[$plan][$ch][] = [convertDateToShow($item['date']),moneda($item['price_admin']), moneda($item['price_ota'])];
      $aux2[$plan] = 1;
    }
    
    $aAgenc   = [];
    foreach ($oConfig->getAllAgency() as $name=>$id){
      if (isset($aux2[$id]))    $aAgenc[$id] = $name;
    }
    

    return view('backend/prices/controlOta', [
        'aAgenc' => $aAgenc,
        'aChRooms' => $oConfig->getRoomsName(),
        'logLines' => \App\LogsData::getLastInfo('OTAs_prices',15),
        'lst' => $aux,
    ]);
  
  }

  
  /* 
  * @param Request $request
  * rcode (the reservation code) 
  * lcode (the property identifier, 
  */
 public function webHookWubook(Request $request) {
   
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
}

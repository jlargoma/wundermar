<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;
use App\Http\Requests;
use App\Services\OtaGateway\Config as oConfig;
use App\Promotions;

class PromotionsController extends AppController {

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {

    $year = $this->getActiveYear();
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    
    $oConfig = new oConfig();
    $ch_group = $oConfig->getRoomsName();
    
    /***********************************************************************/
    $oPromotions = Promotions::where([['finish','>=', $startYear ],['start','<=', $endYear ]])->get();
        
    $lstPromotions = [];
    if ($oPromotions){
      foreach ($oPromotions as $item){
        /**************/
        $lstRooms = [];
        $rooms = unserialize($item->rooms);
        if ($rooms)
          foreach ($rooms as $r){
            if (isset($ch_group[$r]))
              $lstRooms[] = $ch_group[$r];
          }
        /**************/
        $lstExcepts = [];
        $exceptions = unserialize($item->exceptions);
        if ($exceptions)
          foreach ($exceptions as $e){
           $lstExcepts[] = convertDateToShow_text($e['start']).' - '.convertDateToShow_text($e['end']);
          }
        /**************/
        $discount = $item->value.'%';
        if ($item->type == 'nights'){
          $discount = 'cada '.$item->nights.' paga '.($item->night_apply);
        }
        /**************/
        $weekDay = null;
        if ($item->weekday == 'working') $weekDay = 'Laborables';
        if ($item->weekday == 'end') $weekDay = 'Fin de Semana';
        /**************/
        $lstPromotions[] = [
          'start' =>convertDateToShow_text($item->start,true),
          'finish' =>convertDateToShow_text($item->finish,true),
          'rooms' => $lstRooms,
          'except' => $lstExcepts,
          'value' => $discount,
          'name' => $item->name,
          'weekDay' => $weekDay,
          'id' => $item->id
        ];

      }
    }
    /***********************************************************************/
     
    $sentData = \App\ProcessedData::findOrCreate('create_baseSeason_'.$year->id);
    $sendDataInfo = 'No ha sido enviado aún';
    if ($sentData->content){
      $sentData->content = json_decode($sentData->content);
      $sendDataInfo = 'Enviado el '. convertDateTimeToShow_text($sentData->updated_at);
      $sendDataInfo .= "\n".'Por '.$sentData->content->u;
    }
    
    return view('backend/prices/promotions', [
        'ch_group' => $ch_group,
        'lstPromotions' =>$lstPromotions,
        'sendDataInfo' => $sendDataInfo
    ]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function getItem($id) {
    /***********************************************************************/
    $item = Promotions::find($id);
    if ($item){
        /**************/
        $rooms = unserialize($item->rooms);
        if (!$rooms || !is_array($rooms)) $rooms = [];
        /**************/
        $exceptions = unserialize($item->exceptions);
        if (!$exceptions || !is_array($exceptions)) $exceptions = [];
        for($i=0; $i< count($exceptions); $i++){
          $exceptions[$i] = [
              'start' => convertDateToShow($exceptions[$i]['start'],true),
              'end' => convertDateToShow($exceptions[$i]['end'],true)
              ];
        }
        /**************/
        return response()->json([
          'start'  => convertDateToShow($item->start,true),
          'finish' => convertDateToShow($item->finish,true),
          'rooms'  => $rooms,
          'except' => $exceptions,
          'value'  => $item->value,
          'name'   => $item->name,
          'weekday'=> $item->weekday,
          'type'   => $item->type,
          'nights' => $item->nights,
          'night_apply' => $item->night_apply,
        ]);
      
      }
      return 'not_found';
  }
  
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request) {
    
    $data = $request->all();
    
    

    /***********************************/
    /***    Exceptions   *************/
    $exceptions = [];
    $exceptLst  = [];
    foreach ($data as $k=>$v){
      if (preg_match('/^date/', $k) && trim($v) != ''){
        $auxRange = explode(' - ', $v);
        $startAux = convertDateToDB($auxRange[0]);
        $endAux   = convertDateToDB($auxRange[1]);
         
        $exceptLst[] = ['start'=>$startAux,'end'=>$endAux];
        
        $startAux = strtotime($startAux);
        $endAux = strtotime($endAux);
        
        while ($startAux<=$endAux){
          $exceptions[] = date('Y-m-d',$startAux);
          $startAux = strtotime("+1 day", $startAux);
        }
      }
    }
    /***********************************/
    /***    Range-Days     *************/
    $aRange = explode(' - ', $data['range']);
    $start  = convertDateToDB($aRange[0]);
    $finish  = convertDateToDB($aRange[1]);
    
    $startAux = strtotime($start);
    $endAux = strtotime($finish);
    $days = [];
    while ($startAux<$endAux){
      $dateAux= date('Y-m-d',$startAux);
      $active = (in_array($dateAux, $exceptions)) ? 0 : 1;
      
      if ($active === 1){
        $weekday = date('w',$startAux);
        switch ($data['weekday']){
          case 'working':
            if ($weekday > 4) $active = 0;
            break;
          case 'end':
            if ($weekday < 5) $active = 0;
            break;
        }
      }
      $days[$dateAux] = $active;
      $startAux = strtotime("+1 day", $startAux);
    }
    /***********************************/
    /***    Channel Group *************/
    $chGroupSel = [];
    $oConfig = new oConfig();
    $ch_group = $oConfig->getRoomsName();
    foreach ($ch_group as $k=>$name) 
      if (isset ($data['apto'.$k])) $chGroupSel[] = $k;
    
      
    $oPromotion = null;
    if (isset($data['itemID']) && $data['itemID'])
      $oPromotion = Promotions::find($data['itemID']);
    if (!$oPromotion)  $oPromotion = new Promotions();
    
    $oPromotion->start  = $start;
    $oPromotion->finish = $finish;
    $oPromotion->name = $data['name'];
    $oPromotion->weekday = $data['weekday'];
    $oPromotion->type = $data['type'];
    $oPromotion->nights = $data['nights'];
    $oPromotion->night_apply = $data['night_apply'];
    $oPromotion->value = $data['discount'];
    $oPromotion->rooms = serialize($chGroupSel);
    $oPromotion->days  = serialize($days);
    $oPromotion->exceptions  = serialize($exceptLst);
    $oPromotion->save();

    /*******************************************************************************/
    if($data['type'] == 'perc'){
      $response = $this->sendToGHotel($chGroupSel,$start,$finish,$days);
      if ($response) return redirect()->back()->with(['success'=>'Precios actualizados en OTA']);
      return redirect()->back()->withErrors(['Error: Precios no enviados a OTA']);
    }
    /*******************************************************************************/
    return redirect()->back();
    
  }

  function delete(Request $request){
    $oPromotion = Promotions::find($request->input('id'));
    if ($oPromotion->delete()) return 'OK';
    
    return 'error';
  }
  
  function sendToGHotel($chGroupSel,$start,$finish,$days){
        
    $enviado = false; 
    
    $OtaGateway = new \App\Services\OtaGateway\OtaGateway();
    $oConfig = new \App\Services\OtaGateway\Config();
    $prices = [];
    $roomsCode = $oConfig->getRooms();
    foreach ($chGroupSel as $room){
      $auxPrices = [];
      $oRoom = \App\Rooms::where('channel_group',$room)->first();
      $pvps = $oRoom->getPVP($start, $finish,$oRoom->minOcu,false,true);
      foreach ($days as $d=>$v){
        if ($v == 1){
          if (isset($pvps[$d])){
            $auxPrices[$d] = $pvps[$d];
          }
        }
      
      }
      if (!isset($prices[$oRoom->site_id])) $prices[$oRoom->site_id]=[];
      if (isset($roomsCode[$room]))   $prices[$oRoom->site_id][$roomsCode[$room]] = $auxPrices;
    }
    $resp = null;
    if (count($prices)>0){
      foreach ($prices as $siteID=>$pricesLst)
      if ($OtaGateway->conect($siteID) ){
        $resp = $OtaGateway->setRatesGHotel(["price"=>$pricesLst]);
        if ($resp == 200) $enviado = true;
        $OtaGateway->disconect($siteID);
      }
    }
    return $enviado;
    
  }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon\Carbon;
use App\Http\Requests;
use App\Services\Zodomus\Config as ZConfig;
use App\Prices;
use App\ExtraPrices;
use App\Models\prepareDefaultPrices;

class PricesController extends AppController {

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {
    $year = $this->getActiveYear();
    $startYear = new Carbon($year->start_date);
    $endYear = new Carbon($year->end_date);
    $diff = $startYear->diffInMonths($endYear) + 1;
   
    $seasonTemp = \App\Seasons::where('start_date', '>=', $startYear)
            ->where('finish_date', '<=', $endYear)
            ->orderBy('start_date', 'ASC')
            ->get();


    $oSeasonType = \App\TypeSeasons::orderBy('order', 'ASC')->get();
    
    $ch_group = configZodomusAptos();
        
    $allPrices = [];
    for($i=1; $i<13;$i++){
      foreach($oSeasonType as $season){
        $price = Prices::where('occupation', $i)->where('season', $season->id)->first();
        $aux = [
            'pvp'=>0,
            'cost'=>0,
            'benef'=>0,
        ];
        if ($price){
          $aux['pvp'] = $price->price;
          $aux['cost'] = $price->cost;
          
          if ($price->price > 0 && $price->cost > 0){
            $aux['benef'] = ( 1 - ($price->cost/$price->price) ) * 100;
          }
        }
        $allPrices[$i.'-'.$season->id] = $aux;
      }
    }
    
    
    $extp_fixed = ExtraPrices::getFixed();
    $extp_dinamic = ExtraPrices::getDynamic();
    
    $SpecialSegment = \App\SpecialSegment::where('start','>=',$startYear)
                ->where('finish','<=',$endYear)
                ->get();
    
    
    $sentData = \App\ProcessedData::findOrCreate('create_baseSeason_'.$year->id);
    $sendDataInfo = 'No ha sido enviado aún';
    if ($sentData->content){
      $sentData->content = json_decode($sentData->content);
      
      $sendDataInfo = 'Enviado el '. convertDateTimeToShow_text($sentData->updated_at);
      $sendDataInfo .= "\n".'Por '.$sentData->content->u;
    }
    
    $sentData = \App\ProcessedData::findOrCreate('send_minStaySeason_'.$year->id);
    $sendDataInfo_minStay = 'No ha sido enviado aún';
    if ($sentData->content){
      $sentData->content = json_decode($sentData->content);
      $sendDataInfo_minStay = 'Enviado el '. convertDateTimeToShow_text($sentData->updated_at);
      $sendDataInfo_minStay .= "\n".'Por '.$sentData->content->u;
    }
    
    $priceExtrPax = \App\Settings::getKeyValue('price_extr_pax');
    
    $dw = listDaysSpanish(true);
    $aSites = \App\Sites::allSites();
    /************************************************************************/
    
    $logMinStays = [];
    $min_stay_sites = \App\LogsData::where('key','min_stay_sites')->orderBy('created_at','DESC')->get();
    if ($min_stay_sites){
      $usersNames = \App\User::getUsersNames();
      foreach ($min_stay_sites as $item){
        $dataLog = json_decode($item->long_info);
        $site_ids = $dataLog->site_ids;
      
          
        $logMinStays[] = [
          'start'    => convertDateToShow_text($dataLog->startDate),
          'end'      => convertDateToShow_text($dataLog->endDate),
          'user'     => isset($usersNames[$dataLog->userID]) ?  $usersNames[$dataLog->userID] : '',
          'min_stay' => $dataLog->min_estancia,  
          'weekDays' => $dataLog->weekDays,  
          'sites'    => $site_ids,  
        ];
      }
    }
    /************************************************************************/
    
    return view('backend/prices/index', [
        'seasons' => $oSeasonType,
        'newseasons' => $oSeasonType,
        'seasonsTemp' => $seasonTemp,
        'newtypeSeasonsTemp' => $oSeasonType,
        'typeSeasonsTemp' => $oSeasonType,
        'logMinStays' => $logMinStays,
        'year' => $year,
        'diff' => $diff,
        'dw' => $dw,
        'aSites' => $aSites,
        'startYear' => $startYear,
        'endYear' => $endYear,
        'ch_group' => $ch_group,
        'allPrices' => $allPrices,
        'extp_fixed' => $extp_fixed,
        'extp_dinamic' => $extp_dinamic,
        'sendDataInfo_minStay' => $sendDataInfo_minStay,
        'sendDataInfo' => $sendDataInfo,
        'specialSegments' => $SpecialSegment,
        'priceExtrPax' => $priceExtrPax,
    ]);
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  int                      $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request) {
    $key = explode('-',  $request->input('id'));
    if (!is_array($key) || count($key) != 2 ){
      return ('ops, algo salió mal.');
    }
    $oPrice = Prices::findOrCreate($key[0],$key[1]);
    $oPrice->price = $request->input('price');
    $oPrice->cost = $request->input('cost');

    if ($oPrice->save()) return 'OK';
    
     return ('ops, algo salió mal.');
  }
  
  
  /*******************************************/
  /*******************************************/
  public function prepareYearPrices(Request $request) {
   
    $year = $this->getActiveYear();
    $cUser = \Illuminate\Support\Facades\Auth::user();
    // Sólo lo puede ver jorge
    if ($cUser->email != "jlargo@mksport.es"){
      return redirect('no-allowed');
    }
    
    $store = \App\ProcessedData::findOrCreate('create_baseSeason_'.$year->id);
    $store->content = json_encode([
        'u' =>$cUser->email,
        'ip'=>getUserIpAddr()
    ]);
    $store->save();
    
    $prepareDefaultPrices = new prepareDefaultPrices($year->start_date,$year->end_date);
    if ($prepareDefaultPrices->error){
      return back()->withErrors($prepareDefaultPrices->error);
    }
    foreach (\App\Sites::siteIDs() as $sID){
      $prepareDefaultPrices->setSiteID($sID);
      $prepareDefaultPrices->process_OtaGateway();
    }

    //BEGIN wubook
    $oAux = \App\ProcessedData::findOrCreate('wubookRate');
    $oAux->content=time();
    $oAux->save();


//    $prepareDefaultPrices->process();
    return back()->with('success','Precios cargados para ser enviados');
    
  }
  /*******************************************/
  public function prepareYearMinStay(Request $request) {
    $year = $this->getActiveYear();
    $cUser = \Illuminate\Support\Facades\Auth::user();
    // Sólo lo puede ver jorge
//    if ($cUser->email != "jlargo@mksport.es"){
//      return redirect('no-allowed');
//    }
//    
    $store = \App\ProcessedData::findOrCreate('send_minStaySeason_'.$year->id);
    $store->content = json_encode([
        'u' =>$cUser->email,
        'ip'=>getUserIpAddr()
    ]);
    $store->save();
    $prepareMinStay = new \App\Models\PrepareMinStay($year->start_date,$year->end_date);
    if ($prepareMinStay->error){
      return back()->withErrors([$prepareMinStay->error]);
    }
    foreach (\App\Sites::siteIDs() as $sID){
      $prepareMinStay->setSiteID($sID);
      $prepareMinStay->process();
    }

     //BEGIN wubook
     $oAux = \App\ProcessedData::findOrCreate('wubookMinStay');
     $oAux->content=time();
     $oAux->save();


    return back()->with('success','Estancias mínimas cargadas para ser enviadas');
  }
  
  
  public function promotions(Request $req){
      
    $site = $req->input('site',null);
    $ch_sel = $req->input('ch_sel',null);
    $room_gr_sel = $req->input('room_gr_sel',null);
    $isMobile = config('app.is_mobile');
    $zConfig = new ZConfig();
    $aAptos = [];
    $aptosZodomus = configZodomusAptos();
    $aChannels = $zConfig->Channels();
    foreach ($aptosZodomus as $k=>$v){
      $aAptos[$k] = $v->name;
    }
    $aSites = \App\Sites::allSites();
    
    $oPromotion  = new \App\Promotions();
    $oPromotion->getItems($ch_sel,$site,$room_gr_sel);
    $oPromotions = $oPromotion->prepareItems($aAptos,$aChannels,$aSites);
    
    $aTabChAux = getAptosBySite($site);
    $aRooms = [];
    foreach ($aTabChAux as $ch){
      if (isset($aAptos[$ch]))  $aRooms[$ch] = $aAptos[$ch];
    }
    return view('backend/prices/promotios', 
            compact('aChannels','aAptos',
                    'oPromotions','isMobile',
                    'room_gr_sel','ch_sel','aSites','site',
                    'aRooms'));
   
            
  }

}

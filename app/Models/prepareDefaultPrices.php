<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

/**
 * Description of prepareYearPricesAndMinStay
 *
 * @author cremonapg
 */
class prepareDefaultPrices {
  
  private $startDate;
  private $endDate;
  private $dailyPrices;
  private $roomsPrices;
  private $specialSegment;
  private $ogData;
  private $siteID;
  public $error;

  public function __construct($start,$end){
    
    $this->error = null;
    $today = date('Y-m-d');
    if ($today>$start) $start = $today;
    if ($end<=$start){
      $this->error = 'Las fecha de inicio debe se mayor a la de final.';
      return false;
    }
    $this->startDate = $start;
    $this->endDate = $end;
    $otaGateway = new \App\Services\OtaGateway\Config();
    $this->ogData = $otaGateway->getRooms();
  }

  public function setSiteID($id) {
    $this->siteID = $id;
  }
  
  public function process() {
    //obtengo todos los aptos - OTA-GATEWAY
    $this->process_OtaGateway();
  }
  
  public function process_OtaGateway() {
    $aptos = getAptosBySite($this->siteID);
    if (!is_array($this->ogData) || count($this->ogData) == 0) return;
    foreach ($this->ogData as $chGroup=>$v){
      if (in_array($chGroup, $aptos)){
        $this->dailyPrices = [];
        $oRoom = \App\Rooms::where('channel_group',$chGroup)->first();

        if ($oRoom){
          $this->dailyPrice($oRoom);
          $this->generateQueriesToSendOtaGateWay($chGroup);
        } 
      }
    }
  }
  
  private function dailyPrice($oRoom){
    
    $defaults = $oRoom->defaultCostPrice( $this->startDate, $this->endDate,$oRoom->minOcu);
    $priceDay = $defaults['priceDay'];
    $oPrice = \App\DailyPrices::where('channel_group',$oRoom->channel_group)
                ->where('date','>=',$this->startDate)
                ->where('date','<=',$this->endDate)
                ->get();
   
    
    if ($oPrice) {
        foreach ($oPrice as $p) {
          if (isset($priceDay[$p->date]) && $p->price)
            $priceDay[$p->date] = $p->price;
        }
      }
      
    $this->dailyPrices = $priceDay;
  }
  /*******************************************/
  
  function getSpecialSegments(){
    $oSS = \App\SpecialSegment::where('start','>=',$this->startDate)
                ->where('finish','<=',$this->endDate)
                ->get();
    $ssDays = [];
    
    if ($oSS){
      foreach ($oSS as $item){
        
        $startTime = strtotime($item->start);
        $endTime = strtotime($item->finish);
        
        while ($startTime<=$endTime){
          $ssDays[date('Y-m-d',$startTime)] = $item->minDays;
          $startTime = strtotime('+1 day', $startTime);
        }
      }
    }
    $this->specialSegment = $ssDays;
  }
  function prepareSpecialSegments(){
    
    
//    $this->dailyPrices
//    $this->specialSegment = $ssDays;
  }
    
  /*******************************************/
  
  function generateQueriesToSendOtaGateWay($chGroup){
    $d1 = $this->startDate;
    $d2 = $this->endDate;
    $to_send = [];
    $precio = null;
    if (!isset($this->ogData[$chGroup])) return null;
    $aApto = $this->ogData[$chGroup];
     
    $nameProcess = $this->startDate.'_'.$this->endDate.'_'.$chGroup;
    $key = 'SendToOtaGateway-'.$this->siteID;

    $data = [
      'key'=>$key,
      'name'=>$nameProcess,
      'content'=> json_encode(['room'=>$aApto,'prices'=>$this->dailyPrices])
    ];
    \App\ProcessedData::where('key',$key)
            ->where('name',$nameProcess)->delete();
    
    \App\ProcessedData::insert($data);
  }
}

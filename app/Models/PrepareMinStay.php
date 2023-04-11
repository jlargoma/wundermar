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
class PrepareMinStay {
  
  private $startDate;
  private $endDate;
  private $siteID;
  private $dailyMinStay;
  private $zData;
  private $wData;
  private $roomsMinStay;
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
    $this->endDate = date('Y-m-d', strtotime($end . ' +1 day'));
    $otaGateway = new \App\Services\OtaGateway\Config();
    $this->ogData = $otaGateway->getRooms();
  }

  public function setSiteID($id) {
    $this->siteID = $id;
  }
   public function process() {
    $this->process_OtaGateway();
  }
  
  public function process_OtaGateway() {
    
    $aptos = getAptosBySite($this->siteID);
    
    foreach ($this->ogData as $chGroup=>$v){
      if (in_array($chGroup, $aptos)){
        $this->dailyMinStay = [];
        $oRoom = \App\Rooms::where('channel_group',$chGroup)->first();

        if ($oRoom){
          $this->dailyMinStay($oRoom);
          $this->prepareQueriesToSendOtaGateWay($chGroup);
        } 
      }
    }
  }
  
  
  private function dailyMinStay($oRoom){
 
    $startTime = strtotime($this->startDate);
    $endTime = strtotime($this->endDate);
    $aDays = [];
    while ($startTime<$endTime){
      $aux = date('Y-m-d',$startTime);
      $md = isset($this->specialSegment[$aux]) ? $this->specialSegment[$aux] : 0;
      $aDays[$aux] = $md;
      $startTime = strtotime('+1 day', $startTime);
    }
      
    $oPrice = \App\DailyPrices::where('channel_group',$oRoom->channel_group)
                ->where('date','>=',$this->startDate)
                ->where('date','<=',$this->endDate)
                ->get();
   
    if ($oPrice) {
        foreach ($oPrice as $p) {
          if (isset($aDays[$p->date]) && $p->min_estancia)
            $aDays[$p->date] = $p->min_estancia;
        }
      }

    $this->dailyMinStay = $aDays;
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
  /*******************************************/
  /**************************************************************/
  
  function prepareQueriesToSendOtaGateWay($chGroup){
    
    $d1 = $this->startDate;
    $d2 = $this->endDate;
    $to_send = [];
    $precio = null;
    
    if (!isset($this->ogData[$chGroup])) return null;
    $aApto = $this->ogData[$chGroup];
    
    $MinStay = [];
    foreach ($this->dailyMinStay as $day=>$v) {
      $MinStay[$day] = ['min_stay'=>$v];
    }
    
    $nameProcess = $this->startDate.'_'.$this->endDate.'_'.$chGroup;
    $key = 'SendToOtaGateway_minStay-'.$this->siteID;

    $data = [
      'key'=>$key,
      'name'=>$nameProcess,
      'content'=> json_encode(['room'=>$aApto,'MinStay'=>$MinStay])
    ];
    \App\ProcessedData::where('key',$key)
            ->where('name',$nameProcess)->delete();
    
    \App\ProcessedData::insert($data);
    

  }
}

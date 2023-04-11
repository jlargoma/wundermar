<?php

namespace App\Services\Wubook;

use Illuminate\Support\Facades\DB;
use App\ProcessedData;
use App\RateCheckerSnaphots;

class RateChecker {

  public $response;
  public $responseCode;
  public $channels;
  protected $token;
  protected $URL;

  public function __construct() {
    $this->token = "999999999999999";
    $this->URL = 'https://wubook.net/wrpeeker/api/';
  }

  /**
   * 
   * @param type $endpoint
   * @param type $method
   * @param type $data
   * @return boolean
   */
  public function call($endpoint, $data = []) {



    $url = $this->URL . $endpoint.'?format=json&token='.$this->token;
    
    if (count($data)) {
      $param = [];
      foreach ($data as $k => $d) {
        $param[] = "$k=$d";
      }
      $url .= '&' . implode('&', $param);
    }
   
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7); //Timeout after 7 seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); //  CURLOPT_TIMEOUT => 10,
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $this->response = null;
    $this->responseCode = $httpCode;
    switch ($httpCode) {
      case 200:
        if (!$result) {
          $this->response = null;
          return FALSE;
        }
        $this->response = \json_decode($result);
        return TRUE;
        break;
      case 400:
        $this->response = 'Wrong data - Bad Request';
        break;
      case 401:
        $this->response = $result;
        break;
      case 404:
        $this->response = $result; //'NotFound';
        break;
      default :
        $this->response = 'Server error';
        break;
    }

    return FALSE;
  }
  
  function setCompetitorsData() {
    
    $param = [
        'user_id' => 'auto'
    ];
    $this->call('user',$param);
    
    $oData = ProcessedData::findOrCreate('RateChecker-competitors');
    $oData->content = json_encode($this->response);
    $oData->save();
    
    //Stays from a given competitor
    $competitors = $this->response;
    $Stays = [];
    if ($competitors){
      foreach ($competitors as $item){
        
        $this->call('competitor',['competitor_id'=>$item->competitor_id]);
        if ($this->response){
          foreach ($this->response as $stay){
            $Stays[] = [
                "competitor_id" => $item->competitor_id,
                "stay_id" => $stay->stay_id,
                "stay" => $stay->stay,
            ];
          }
        }
      }
    }
    
    $oData = ProcessedData::findOrCreate('RateChecker-stays');
    $oData->content = json_encode($Stays);
    $oData->save();
    
    //Snapshots available from a given stay
    
    $Snapshots = [];
    if ($Stays){
      foreach ($Stays as $item){
        $this->call('stay',['stay_id'=>$item['stay_id']]);
        if ($this->response){
          $Snapshots[] = [
              'stay_id'=>$item['stay_id'],
              'items' => $this->response
            ];
        }
      }
    }
    $oData = ProcessedData::findOrCreate('RateChecker-Snapshots');
    $oData->content = json_encode($Snapshots);
    $oData->save();
    
    
    return 'OK';
  }

  
  function setRateData() {
    
    
    //Price and Avails from a given snapshot
    $oData = ProcessedData::findOrCreate('RateChecker-Snapshots');
//    var_dump($oData);
    $Snapshots = json_decode($oData->content);
    $result = [];
    foreach ($Snapshots as $Snapshot){
      if (is_array($Snapshot->items)){
        $item = $Snapshot->items[0]; // just the las scanner
        if($item){
            
          $this->call('snapshot',['snapshot_id'=>$item->snaphot_id]);
          if ($this->response){
            
            dd($item->snaphot_id);
            $obj = RateCheckerSnaphots::where('snaphot_id',$item->snaphot_id)->first();
            if (!$obj){
              $obj = new RateCheckerSnaphots();
              $obj->snaphot_id  = $item->snaphot_id;
              $obj->stay_id     = $Snapshot->stay_id;
              $obj->currency    = $item->currency;
              $obj->date_start  = $item->date_start;
              $obj->scan_range  = $item->scan_range;
            }
            
            $obj->content = json_encode($this->response);
      
            $obj->save();
          }
        }
      }
    }
    
    return 'OK';
  }
  
  
  function getCompetitorsData() {
  
    $result = [];
    
    $oData = ProcessedData::findOrCreate('RateChecker-Snapshots');
    $data = json_decode($oData->content);
    $Snapshots = [];
    
    foreach ($data as $d){
      if (is_array($d->items))  $Snapshots[$d->stay_id] = $d->items[0]->snaphot_id;
    }
    
    $oData = ProcessedData::findOrCreate('RateChecker-stays');
    $data = json_decode($oData->content);
    $stays = [];
    
    foreach ($data as $d){
      if (isset($Snapshots[$d->stay_id])){
        $stays[$d->competitor_id] = $Snapshots[$d->stay_id];
      }
    }
    
    $oData = ProcessedData::findOrCreate('RateChecker-competitors');
    $competitors = json_decode($oData->content);
  
    foreach ($competitors as $k=>$c){
      $result[$c->competitor_id] = [
          'name' => $c->name,
          'snaphot' => isset($stays[$c->competitor_id]) ? $stays[$c->competitor_id] : null
        ];
    }
    return $result;
  }
  
  function getRateData($Competitors){
    $snaphotIDs = [];
    foreach ($Competitors as $k=>$v){
      $snaphotIDs[] = $v['snaphot'];
    }
    
    $oSnaphot =  RateCheckerSnaphots::whereIN('snaphot_id',$snaphotIDs)->get();
    $lstSnaphot=[];
    $oneDay = 24*60*60;
    $maxRange = 0;
    $startRange = time();
    if ($oSnaphot){
      foreach ($oSnaphot as $s){
        $start = $s->date_start-($oneDay);
        
        if (!is_numeric($s->scan_range) || $s->scan_range<0 || $s->scan_range>35)
          $s->scan_range = 20;
        
        if ($maxRange<$s->scan_range){
          $maxRange = $s->scan_range;
          $startRange = $start;
        }
        
        
        //BEGIN: Obtengo listado de habitaciones
        $content = json_decode($s->content);
        $lstRooms = [];
        if ($content){
          
          $aDays = [];
          for($i=0;$i<=$s->scan_range;$i++){
            $aDays[] = date('Ymd',$start+($oneDay*$i));
          }
    
          foreach ($content as $c){
//            if ($c->name != 'Double or Twin Room') continue;
//            echo '<h2>'.$c->name.'</h2>';
            $prices = [];
            $i = 0;
            foreach ($c->price as $k=>$p){
               $prices[$aDays[$k]] = $p;
               if ($p>0){
//                 echo '<b>'.$aDays[$k].'</b>: '. $p.'€<br>';
               }

            }
            
            $lstRooms[] = [
                'name' => $c->name,
                'size' => $c->size,
                'prices' => $prices
            ];
          }
        
        
        }
//        echo  '<hr><hr><hr>';
        //END: Obtengo listado de habitaciones
        $lstSnaphot[$s->snaphot_id] = [
            'range'      => $s->scan_range,
            'currency'   => $s->currency,
            'date_start' => $s->date_start,
            'lstRooms'    => $lstRooms
        ];
      }
    }
   
    
    foreach ($Competitors as $k=>$v){
      if (isset($lstSnaphot[$v['snaphot']]))
        $Competitors[$k]['snaphot'] = $lstSnaphot[$v['snaphot']];
      else $Competitors[$k]['snaphot'] = null;
    }
//     dd($Competitors,$maxRange);
    $this->maxRange = $maxRange;
    $this->startRange = $startRange;
    return $Competitors;
  }
}

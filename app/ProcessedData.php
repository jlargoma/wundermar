<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of ProcessedData
 *
 * @author cremonapg
 */
use Illuminate\Database\Eloquent\Model;

class ProcessedData extends Model{
  
      
  protected $table = 'processed_data';
      
  // Put this in any model and use
  // Modelname::findOrCreate($id);
  public static function findOrCreate($k)
  {
      $obj = static::where('key',$k)->first();
      if ($obj) return $obj;
      
      $obj = new static;
      $obj->key = $k;
      $obj->name = $k;
      $obj->save();
      return $obj;
  }
  
  public static function emptyContent($k)
  {
      $obj = static::where('key',$k)->first();
      if ($obj){
        if ($obj->content == null) return true;
        return false;
      }
      return true;
  }
  
    public static function savePriceUPD_toOtaGateway($start,$finish)
  {
    $obj = self::where('key', 'sentUPD_OtaGateway')->first();
    if (!$obj) {
      $obj = new self;
      $obj->key = 'sentUPD_OtaGateway';
      $obj->name = 'DateRange to send prices to OtaGateway';
    }

    $content = json_decode($obj->content);
    //fix incremente 1 day
//    $finish = date('Y-m-d', strtotime($finish) + (24*60*60));
    if (!$content) {
      $content = (object) [
                  'start' => $start,
                  'finish' => $finish,
      ];
    } else {
      if ($content->start > $start) $content->start = $start;
      if ($content->finish < $finish) $content->finish = $finish;
    }
    $obj->content = json_encode($content);
    $obj->save();
  }

  public static function saveMinDayUPD_toOtaGateway($start,$finish)
  {
    $obj = self::where('key', 'sentUPD_OtaGateway_minStay')->first();
    if (!$obj) {
      $obj = new self;
      $obj->key = 'sentUPD_OtaGateway_minStay';
      $obj->name = 'DateRange to send minStay to OtaGateway';
    }

    $content = json_decode($obj->content);
    if (!$content) {
      $content = (object) [
                  'start' => $start,
                  'finish' => $finish,
      ];
    } else {
      if ($content->start > $start) $content->start = $start;
      if ($content->finish < $finish) $content->finish = $finish;
    }
    $obj->content = json_encode($content);
    $obj->save();
  }


}

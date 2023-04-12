<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotions extends Model {

  public $timestamps = false;

  function getDiscount($startDate, $endDate, $ch_group) {
    $oPromotions = \App\Promotions::where('type', 'perc')->get();
    $maxDiscunt = 0;
    $name = '';
    if ($oPromotions) {
     
      $startAux = strtotime($startDate);
      $endAux = strtotime($endDate);
      $days = [];
      while ($startAux < $endAux) {
        $days[] = date('Y-m-d', $startAux);
        $startAux = strtotime("+1 day", $startAux);
      }

      $maxDiscunt = 0;
      foreach ($oPromotions as $promo) {
        $rooms = unserialize($promo->rooms);
        
        if (!is_array($rooms) || count($rooms) == 0)
          continue;

        if (!in_array($ch_group, $rooms))
          continue;
        if( !$promo->inDays($days) ) continue;
        
        if ($promo->value>$maxDiscunt){
          $maxDiscunt = $promo->value;
          $name = $promo->name;
        }
      }
    }
    
    return ['n'=>$name,'v'=>$maxDiscunt];
  }

  
  function getPromo($startDate, $endDate, $ch_group) {
    $oPromotions = \App\Promotions::where('type', 'nights')->get();
    if ($oPromotions) {
     
      $startAux = strtotime($startDate);
      $endAux = strtotime($endDate);
      $days = [];
      while ($startAux < $endAux) {
        $days[] = date('Y-m-d', $startAux);
        $startAux = strtotime("+1 day", $startAux);
      }
 
      foreach ($oPromotions as $promo) {
        $rooms = unserialize($promo->rooms);
        
        if (!is_array($rooms) || count($rooms) == 0)
          continue;
 
        if (!in_array($ch_group, $rooms))
          continue;
      
        if( !$promo->inDays($days) ) continue;
        
        return [
            'name' => $promo->name,
            'night' => $promo->nights,
            'night_apply' => $promo->night_apply
            ];
        
      }
    }

    return null;
  }

  private function inDays($days) {
    $promoDays = unserialize($this->days);
    if (!is_array($promoDays) || count($promoDays) == 0)
      return false;
    if ($days){
      foreach ($days as $day){
        if (!isset($promoDays[$day])) 
          return false;
        if ($promoDays[$day] == 0)
          return false;
      }
    }
    return true;
  }

  function getAllDiscount($ch_group) {
    $oPromotions = \App\Promotions::where('type', 'perc')->get();

    $result = [];

    if ($oPromotions) {
      foreach ($oPromotions as $promo) {
        $rooms = unserialize($promo->rooms);
        if (!is_array($rooms) || count($rooms) == 0)
          continue;

        $days = unserialize($promo->days);
        if (!is_array($days) || count($days) == 0)
          continue;

        if (!in_array($ch_group, $rooms))
          continue;

        foreach ($days as $d => $v) {
          if ($v == 1) {
            if (isset($result[$d]) && $promo->value < $result[$d]) {
              continue;
            } else {
              $result[$d] = $promo->value;
            }
          }
        }
      }
    }

    return $result;
  }
}

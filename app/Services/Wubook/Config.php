<?php

namespace App\Services\Wubook;

class Config {


   public function getPropID($site = null) {
    return 11111111111; //solo vamos a usa una
  }

  function getRooms($room=null) {

    $lst = [
      'RIAD1' => 1111111,
      'RIAD2' => 1111111,
    ];
    if ($room){
      return isset($lst[$room]) ? $lst[$room] : -1;
    }
    return $lst;
  }

  function getChannelByRoom($roomID){
    $all = $this->getRooms();
    foreach($all as $chn=>$rid){
      if($rid == $roomID) return $chn;
    }
    return null;
  }

  public function pricePlan($site) {
    return 1111111; // solo vamos a usar una propiedad
  }
  
  public function restricPlan($site) {
    return 1111111;  // solo vamos a usar una propiedad
  }
}

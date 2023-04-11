<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SizeRooms extends Model {

  protected $table = 'sizerooms';

  public function rooms() {
    return $this->hasMany('\App\Rooms', 'sizeApto', 'id');
  }

  public function getRoomsFastPayment() {
    $count = 0;
    $rooms = $this->rooms();
    foreach ($rooms as $index => $room) {
      if ($rooms->fast_payment == 1)
        $count++;
    }

    return $count;
  }

  static function allSizeApto() {
    $lst = self::all();
    $return = [];
    foreach ($lst as $l) $return[$l->id] = $l->name;
      
    return $return;
  }
  
  static function findSizeApto($apto, $luxury, $quantity) {
    $sizeRoom = 0;
    $typeApto = 'None';

    if ($apto == '2dorm' && $luxury == 'si') {
      //$roomAssigned = 115;
      $typeApto = "2 DORM Lujo";
      $sizeRoom = 6;
    } elseif ($apto == '2dorm' && $luxury == 'no') {
      //$roomAssigned = 122;
      $typeApto = "2 DORM estandar";
      $sizeRoom = 2;
    } elseif ($apto == 'estudio' && $luxury == 'si') {
      //$roomAssigned = 138;
      $sizeRoom = 5;
      $typeApto = "Estudio Lujo";
    } elseif ($apto == 'estudio' && $luxury == 'no') {
      //$roomAssigned = 110;
      $typeApto = "Estudio estandar";
      $sizeRoom = 1;
    } elseif ($apto == 'chlt' && $luxury == 'no') {
      //$roomAssigned = 144;
      $typeApto = "CHALET los pinos";
      $sizeRoom = 9;
    } elseif ($apto == '3dorm') {
      /* Rooms para grandes capacidades */
      if ($quantity >= 8 && $quantity <= 10) {
        //$roomAssigned = 153;
        $sizeRoom = 3;
      } else {
        //$roomAssigned = 149;
        $sizeRoom = 4;
      }
      $typeApto = "4 DORM";
    }
    
    return ['sizeRoom' => $sizeRoom,'typeApto'=>$typeApto];
  }

}

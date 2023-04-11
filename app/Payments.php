<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{

  protected $type = 0;
  
  protected $aTypes = [
      0 => "CASH",//"Metalico Jorge",
      1 => "CASH",// "Metalico Jaime",
      2 => "TPV",//"Banco Jorge",
      3 => "TPV",//"Banco Jaime"
      4 => "REINTEGRO"//Devoluciones
  ];

  public function book()
  {
      return $this->hasOne('\App\Book', 'id', 'book_id');
  }

  //Para poner nombre al tipo de cobro//
  static function getTypeCobro($typePayment=NULL) {
    $array = $this->aTypes;

    if (!is_null($typePayment)) 
      return isset ($array[$typePayment]) ? $array[$typePayment] : null;
    
    return $array;
  }

  public function getTypeId($typeName) {
    return array_search($typeName, $this->aTypes);
  }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtraPrices extends Model {

  static function getFixed($channelGroup = null) {
    $qry = self::whereNull('deleted')->where('fixed', 1);

    if ($channelGroup)
      $qry->where('channel_group', $channelGroup);

    return $qry->get();
  }

  static function getLimp($channelGroup = null) {
    $qry = self::whereNull('deleted')->where('fixed', 1);

    $qry->where('name', 'like', '%limpieza%');
    if ($channelGroup)
      $qry->where('channel_group', $channelGroup);

    return $qry->pluck('id')->all();
  }

  static function getDynamic($id=null) {
    $qry = self::whereNull('deleted')->where('fixed', 0);

    if ($id) {
      $qry->where('id', $id);
      return $qry->first();
    }

    return $qry->get();
  }
  
  static function getFixedChannel(){
    
    $lst = self::getFixed();
    $return = [];
    foreach ($lst as $item){
      if (!isset($return[$item->channel_group]))
        $return[$item->channel_group] = 0;
      
      $return[$item->channel_group] += $item->price;
      
    }
    return $return;
    
  }
  
  static function getDynamicToFront($id=null) {
    $qry = self::whereNull('deleted')
            ->whereIn('id',[10,12])->where('fixed', 0)
            ->orderBy('id','DESC');
//            ->whereIn('type',['minibar','breakfast'])->where('fixed', 0);

    if ($id) {
      $qry->where('id', $id);
      return $qry->first();
    }

    return $qry->get();
  }
  
  static function getParkings() {
    return self::whereNull('deleted')
            ->where('type','parking')->get();
  }
  
  static function getParkingsGroups($year) {
    $lst =  self::select('code','qty')
            ->whereNull('extra_prices.deleted')
            ->where('extra_prices.type','parking')
            ->join('book_extra_prices','extra_id','=','extra_prices.id')
            ->where('book_extra_prices.deleted','!=',1)
            ->whereYear('book_extra_prices.created_at','=',$year)->get();
    $result = [];
    if($lst){
      foreach ($lst as $item){
        if (!isset($result[$item->code])) $result[$item->code] = 0;
        $result[$item->code] += $item->qty;
      }
    }
    return $result;
  }
  
  static function getTypes(){
    return [
      'breakfast' => 'Desayuno',
      'excursion' => 'Excursión',
      'minibar' => 'Minibar',
      'parking' => 'Parking',
      'supple' => 'Suplemento',
      'others' => 'Otros',
    ];
 
  }
  static function getVendrs(){
    
    $roles = ['admin',
          'agente',
          'jaime',
          'recepcionista',
          'subadmin'
        ];
    $allUsers = \App\User::whereIn('role',$roles)->get();
    $users = [];
    foreach ($allUsers as $u) $users[$u->id] = $u->name;
    
    return $users;
    return [
      'Jose' => 'Jose',
      'Pedro' => 'Pedro',
      'Fabiana' => 'Fabiana',
      'others' => 'Otros',
    ];
 
  }

}

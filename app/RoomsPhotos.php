<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Rooms;

class RoomsPhotos extends Model
{
  
  
  public function room()
  {
    return $this->belongsTo(Rooms::class)->first();
  }
  
  static function getGalleries(){
    $gals = \App\RoomsType::all();
    $lst = [];
    foreach ($gals as $item){
      $lst[$item->slug] = $item->title;
    }
    return $lst;
  }
  

  public function existsGal($gallery) {
    
    $all = self::getGalleries();
    
    return isset($all[$gallery]);
    
  }
  
  
  
}

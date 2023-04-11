<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecialSegment extends Model {

  protected $table = 'special_segments';
  protected $fillable = ['start', 'finish', 'minDays'];

  static function getMinStay($date_start,$date_finish) {
    
    $segment = SpecialSegment::where('start','<=',$date_finish)
            ->where('finish','>=',$date_start)
            ->orderBy('minDays','desc')->first();
//        ->get();
    $minStay = 0;
    
    if ($segment){
      $minStay = $segment->minDays;
    }
    return $minStay;
  }
}

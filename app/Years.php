<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Years extends Model
{
    protected $table = 'years';
    static function getActive(){
      return self::where('active', 1)->first();
    }
}

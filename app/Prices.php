<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prices extends Model
{
    public function typeSeasons()
    {
        return $this->hasOne('\App\TypeSeasons', 'id', 'season');
    }
    
    // Put this in any model and use
    public static function findOrCreate($pax,$seasson)
    {
        $obj = static::where('occupation', $pax)->where('season', $seasson)->first();
        if ($obj) return $obj;

        $obj = new static;
        $obj->occupation = $pax;
        $obj->season = $seasson;
        $obj->save();
        return $obj;
    }
}

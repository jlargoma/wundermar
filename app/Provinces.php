<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provinces extends Model
{
//    protected $table = 'provinces';
    

    public function country()
    {
        return $this->hasOne('\App\Countries', 'code', 'code_country');
    }

    
}

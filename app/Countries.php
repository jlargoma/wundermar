<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    protected $table = 'countries';
    
    public function cities()
    {
        return $this->hasMany('\App\Cities', 'code', 'code_country');
    }
    
    
    public function getCountry($code){
        global $lstCountries;
        if (!$lstCountries){
            $lstCountries = $this::all()->pluck('country','code');
        }
        
        $code = strtoupper($code);
        return isset($lstCountries[$code]) ? $lstCountries[$code] : '-';
    }
}

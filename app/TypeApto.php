<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeApto extends Model
{

	protected $table = 'typeapto';

    public function rooms()
    {
        return $this->hasMany('\App\Rooms', 'id', 'tipeApto');
    }
}

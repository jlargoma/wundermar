<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    public function book()
    {
        return $this->hasMany('\App\Book', 'id', 'customer_id');
    }
}

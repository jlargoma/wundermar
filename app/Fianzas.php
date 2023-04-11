<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fianzas extends Model
{
    protected $table = 'fianza';
    public function book()
    {
        return $this->hasOne('\App\Book', 'id', 'book_id');
    }

    
}

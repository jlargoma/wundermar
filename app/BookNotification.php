<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookNotification extends Model
{
    protected $table = 'bookingnotification';

    public function book()
    {
        return $this->hasOne('\App\Book', 'id', 'book_id');
    }
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paymentspro extends Model
{
    
    //
    protected $table = 'paymentspro';
    protected $typePayment = 0;
    public function paymentRoom()
    {
        return $this->hasOne('\App\Rooms', 'room_id', 'id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IcalImport extends Model
{
   protected $table = "ical_import";

   public function room()
    {
        return $this->hasOne('\App\Rooms', 'id', 'room_id');
    }
    
}

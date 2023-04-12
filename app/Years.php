<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Years extends Model
{
  protected $table = 'years';
  static function getActive()
  {
    return self::where('active', 1)->first();
  }

  function getNumDays()
  {

    //Create a date object out of a string (e.g. from a database):
    $date1 = date_create_from_format('Y-m-d', $this->start_date);
    $date2 = date_create_from_format('Y-m-d', $this->end_date);
    //Create a comparison of the two dates and store it in an array:
    $diff = date_diff($date1, $date2);

    return $diff->days+1;
  }
}

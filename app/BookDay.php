<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookDay extends Model
{
  /*SELECT * FROM `book_days` where room_id in (SELECT id  FROM `rooms` WHERE `site_id` = 2) and type in (1,2,7,8)  and years(date) = 2020
ORDER BY `book_days`.`date` DESC*/
  
  /*SELECT month(date),count(*) FROM `book_days` where room_id in (SELECT id FROM `rooms` WHERE `site_id` = 2) and type in (1,2,7,8) and year(date) = 2020 GROUP BY month(date)
*/
  
  
  static function createSeasson($start,$end){
    $type_book = self::get_type_book_sales(true,true);
    $lst = Book::where_book_times($start,$end)
              ->whereIn('type_book',$type_book)->get();
    
    
    self::where('date','>=',$start)->where('date','<=',$end)->delete();
    $errors = [];
    $start = strtotime($start);
    $end = strtotime($end);
    $insert = [];
    foreach ($lst as $b){
      $b_start = strtotime($b->start);
      $b_finish = strtotime($b->finish);
      $nigth = 0; //control de noches
      $pvp = ($b->nigths>0) ? $b->total_price / $b->nigths : $b->total_price;
      
      // BEGIN EXTRAS
      $extrs = '';
      $oAdditional = $b->extrasDynamicList();
      if (count($oAdditional) > 0){
        $extrs = [];
        foreach ($oAdditional as $e){
          if (!isset($extrs[$e->extra_id])) $extrs[$e->extra_id] = 0;
          $extrs[$e->extra_id] += ($b->nigths>0) ? $e->price / $b->nigths : $e->price;
        }
        $extrs = json_encode($extrs);
      }
      
      $tCosts = $b->get_costeTotal();
      // END EXTRAS
      while ($b_start < $b_finish) {
        if ($b_start>=$start && $b_start<=$end)
          $insert[] = [
              'book_id'=>$b->id,
              'room_id'=>$b->room_id,
              'agency'=>$b->agency,
              'type'=>$b->type_book,
              'pax'=>$b->pax,
              'date'=>date('Y-m-d', $b_start),
              'pvp'=>$pvp,
              'extrs'=>$extrs,
              'costs'=>$tCosts,
          ];
        
        $b_start = strtotime('+1 day', $b_start);
        $nigth++;
        $tCosts = 0;
      }
      
      if ($nigth != $b->nigths){
//        $errors[$b->id] = $nigth.'!='. $b->nigths;
        $errors[] = $b->id;
      }

    }
    
    self::insert($insert);
    return $errors;
  }
  
   /**
   * Get object Book that has status 2,7,8
   * 
   * @return Object Query
   */
  static function where_type_book_sales($reservado_stripe=false,$ota=false) {
    $types = self::get_type_book_sales($reservado_stripe,$ota);
    return self::whereIn('type',$types);
  }
  
  static function get_type_book_sales($reservado_stripe=false,$ota=false) {
     $types = [2, 7, 8];
    if ($reservado_stripe) $types[] = 1;
    if ($ota) $types[] = 11;
    //Pagada-la-señal / Reserva Propietario / ATIPICAS
    $types[] = 10; //Agrega OVERBOOKING
    return $types;
  }
  
    /**
   * Get object Book that has status 2,7,8
   * 
   * @return Object Query
   */
  static function where_book_times($startYear,$endYear) {
    
     return self::where('date','>=',$startYear)
             ->where('date','<=',$endYear);
  }
  
    
  static function getBy_temporada($oYear=null){
    if (!$oYear) $oYear = getObjYear();
    return self::where_type_book_sales(true,true)
            ->where('date', '>=', $oYear->start_date)
            ->where('date', '<=', $oYear->end_date)->get();
  }
  
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookExtraPrices extends Model
{
  
    public function book()
    {
      return $this->hasOne('\App\Book', 'id', 'book_id');
    }

    public function extra()
    {
        return $this->hasOne('\App\ExtraPrices', 'id', 'extra_id');
    }
    
    static function getStatus(){
      return ['CASH','TPV','PNDTE'];
    }
    
    static function getDynamic($bookID){
      return self::where('deleted',0)->where('fixed', 0)->where('book_id',$bookID)->get();
    }
    static function getDynamicWithExtr($bookID){
      return self::where('deleted',0)->where('fixed', 0)->where('book_id',$bookID)->with('extra')->get();
    }
    static function getTotalByYear($year){
      
      $total = \App\Book::leftJoin('book_extra_prices','book_extra_prices.book_id','=','book.id')
              ->whereIn('type_book', [2,7,8])
              ->whereYear('start', '=', $year)
              ->where('book_extra_prices.deleted',0)
              ->sum('book_extra_prices.price');
      
      
      return round($total);
    }
    
    static function getVendors(){
      return [
          
      ];
    }
}

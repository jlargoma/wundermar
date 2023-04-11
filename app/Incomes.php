<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Incomes extends Model
{
    protected $table = 'incomes';
    
    static function getTypes(){
      return [
        'extr' => 'EXTRAORDINARIOS',
//        'book' => 'SUPLEMENTOS / RESERVAS',
        'others' => 'OTROS',
      ];
    }
    
    static function generateFromBook($import,$comment,$book_id=0,$site=0,$payment_id=null){
      $obj = new Incomes();
      $obj->type = 'book';
      $obj->concept = $comment;
      $obj->date = date('Y-m-d');
      $obj->month = date('m');
      $obj->year = date('Y');
      $obj->import = $import;
      $obj->site_id = $site;
      $obj->book_id = $book_id;
      $obj->payment_id = $payment_id;
      $obj->comment = '';
      $obj->save();
    }
    
    static function updFromBook($import,$id){
      if (!$id) return null;
      $obj = Incomes::where('payment_id',$id)->first();
      if ($obj && $obj->payment_id == $id){
        $obj->import = $import;
        $obj->save();
      }
    }
    static function deleteFromBook($id){
      if (!$id) return null;
      $obj = Incomes::where('payment_id',$id)->first();
      if ($obj && $obj->payment_id == $id){
        $obj->delete();
      }
    }
    
    static function getIncomesYear($year){
      return self::whereYear('date', '=', $year)
            ->where('type','!=','book')->sum('import');
    }
    
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * clase para guardar LOGs de la Rva
 */
class BookData extends Model
{
  protected $table = 'book_data';
    // Put this in any model and use
  // Modelname::findOrCreate($id);
  public static function findOrCreate($k,$bID)
  {
      $obj = static::where('key',$k)->where('book_id',$bID)->first();
      if ($obj) return $obj;
      
      $obj = new static;
      $obj->key = $k;
      $obj->book_id = $bID;
      $obj->content = '';
      $obj->save();
      return $obj;
  }
  
  public static function getObjs($k,$bID)
  {
    return static::where('key',$k)->where('book_id',$bID)->get();
  }
  
  public static function getVal($k,$bID)
  {
      $obj = static::where('key',$k)->where('book_id',$bID)->first();
      if ($obj){
        return $obj->content;
      }
      return null;
  }
}
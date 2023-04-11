<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
  
  use SoftDeletes; //Implementamos 
      
  protected $dates = ['deleted_at']; //Registramos la nueva columna
   
  public function getNumAttribute(){
    $numb = '';
    if ( $this->code && trim($this->code) != '')
      $numb = '#'.$this->code.' / ';
    
    return $numb.str_pad($this->number, 5, "0", STR_PAD_LEFT);;
    
  }
  
  public function books() {
    return $this->hasOne('\App\Book', 'id', 'book_id');
  }
  public function rooms() {
    return $this->hasOne('\App\Rooms', 'id', 'room_id');
  }
  
  public function emisor($emisor = null) {  
    $emisores = [
      'riad' => [
          'name' => 'wundermar',
          'nif' => '99999999',
          'address' => 'Cuesta 23',
          'phone' => '',
          'zipcode' => '18010 d',
          'REAT'=>'A/dd291',
          'url'=>'https://www.wundermar.es/',
          'site_id'=>1
      ],
      'other' => [
          'name' => '---',
          'nif' => '00-00000000',
          'address' => '',
          'phone' => '',
          'zipcode' => '',
          'REAT'=>'',
          'url'=>''
      ]  
    ];
    
    if ($emisor){
      return isset($emisores[$emisor]) ? $emisores[$emisor] : null;
    }
    
    return $emisores;
  }
  
  
  /**********************************************************************/
  /////////  invoice_meta //////////////
  public function setMetaContent($key,$content) {
    DB::table('invoices_meta')
    ->updateOrInsert(
        ['invoice_id' => $this->id, 'meta_key' => $key],
        ['meta_value' => $content]
    );
  }
  public function getMetaContent($key) {
    
    $book_meta = DB::table('invoices_meta')
            ->where('invoice_id',$this->id)->where('meta_key',$key)->first();
    
    if ($book_meta) {
      return $book_meta->meta_value;
    }
    return null;
  }
  public function getMetaObj($key) {
    return DB::table('invoices_meta')
            ->where('invoice_id',$this->id)->where('meta_key',$key)->get();
    
  }
   public function deleteMetaContent($key) {
    DB::table('invoices_meta')
            ->where('invoice_id',$this->id)->where('meta_key',$key)->delete();
  }
}

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
          'name' => 'RIAD PUERTAS DEL ALBAICIN',
          'nif' => 'B19591205',
          'address' => 'Cuesta San Gerónimo 23',
          'phone' => '',
          'zipcode' => '18010 Granada',
          'REAT'=>'A/GR/00291',
          'url'=>'www.riadpuertasdelalbaicin.com',
          'site_id'=>1
      ],
      'siloe' => [
          'name' => 'SILOE PLAZA',
          'nif' => 'B19591205',
          'address' => 'Cuesta San Gerónimo 23',
          'phone' => '',
          'zipcode' => '18010 Granada',
          'REAT'=>'A/GR/00281',
          'url'=>'www.siloeplaza.es',
          'site_id'=>5
      ],
      'gloria' => [
          'name' => 'GLORIA SUITES',
          'nif' => 'B19591205',
          'address' => 'Cuesta San Gerónimo 23',
          'phone' => '',
          'zipcode' => '18010 Granada',
          'REAT'=>'A/GR/00385',
          'url'=>'gloriasuitesgranada.com',
          'site_id'=>3
      ],
      'rosa' => [
          'name' => 'HOTEL ROSA DE ORO 3*',
          'nif' => 'B19591205',
          'address' => 'Cuesta San Gerónimo 23',
          'phone' => '',
          'zipcode' => '18010 Granada',
          'REAT'=>'H/GR/01352',
          'url'=>'www.hotelrosadeoro.es',
          'site_id'=>2
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

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;

class SafetyBox extends Model
{
    
  public function getBoxNameAttribute(){
    if ( $this->color && trim($this->color) != '')
      return $this->color.' - '.$this->caja;  
    return $this->caja;
    
  }
  public function room() {
    return $this->hasOne('\App\Rooms', 'id', 'room_id');
  }
  static public function getNameList(){
    $all = self::all();
    $return = [];
    foreach ($all as $item){
      $return[$item->id] = $item->color.' - '.$item->caja; 
    }
    return $return;
  }
  public function getBySite($site_ID){
    return self::where('site_id',$site_ID)->get();
  }
  public function getByRoom($r_ID){
    return self::where('room_id',$r_ID)->get();
  }
  public function getByBook($book_ID){
    return self::select('safety_boxes.*','book_safety_boxes.deleted','book_safety_boxes.log')
            ->join('book_safety_boxes','box_id','=','safety_boxes.id')
            ->where('book_id',$book_ID)
            ->whereNull('deleted')
            ->first();
  }

  public function usedBy_day($ID,$start,$finish){
    
    $otherBooks = Book::where_book_times($start,$finish)
                ->join('book_safety_boxes','book_id','=','book.id')
                ->whereNull('deleted')
                ->where('box_id',$ID)->count();
    return ($otherBooks>0) ? true : false;
  }
  
  public function cancelBoxBooking($bookID){
    return \DB::table('book_safety_boxes')->where('book_id',$bookID)
                ->update(['deleted'=>1,'key'=>-1]);
  }


  public function getBookSafetyBox($bookID) {
    return \DB::table('book_safety_boxes')->where('book_id',$bookID)->first();
  }
  public function newBookSafetyBox($bookID,$value) {
    return \DB::table('book_safety_boxes')
            ->insert(['book_id'=>$bookID,'box_id'=>$value]);
  }
  
   public function getByBookIDs($bookIDs) {
    return \DB::table('book_safety_boxes')->whereIn('book_id', $bookIDs)
                  ->whereNull('deleted')->get();
//                  ->whereNull('log')
  }
  
  public function updBookSafetyBox($ID,$value) {
    return \DB::table('book_safety_boxes')->where('id', $ID)
              ->update(['box_id'=>$value,'deleted' => null]);
  }
  

  public function updLog($bookID,$value) {
    $msg = ",".time() . '-' .$value;
    $item = \DB::table('book_safety_boxes')->where('book_id', $bookID)
            ->where('box_id', $this->id)->first();
    
    return \DB::table('book_safety_boxes')->where('id', $item->id)
              ->update(['log'=>$item->log.$msg]);
  }

  
  static public function canEdit() {
    $usrAdmin = \Illuminate\Support\Facades\Auth::user()->email;
    if( $usrAdmin == "jlargo@mksport.es" || $usrAdmin == "info@eysed.es")
      return TRUE;
    
    return FALSE;
  }


    
  public function unassingBookSafetyBox($bookIDs) {
    return \DB::table('book_safety_boxes')->whereIn('book_id', $bookIDs)->update(['deleted' => date('Y-m-d H:i:s')]);
  }
  




  /*
  static $keys = [
     'caja_1' => '0110', 
     'caja_2' => '0220', 
     'caja_3' => '0330', 
     'caja_4' => '0440', 
     'caja_5' => '0550', 
     'caja_6' => '0660', 
     'caja_7' => '0770', 
     'caja_8' => '0880', 
     'caja_9' => '0990', 
     'caja_10' => '0000', 
  ];
  static $keys_name = [
     'caja_1' => 'BUZON ROJO - CAJA N1', 
     'caja_2' => 'BUZON ROJO - CAJA N2', 
     'caja_3' => 'BUZON AMARILLO - CAJA N3', 
     'caja_4' => 'BUZON AMARILLO - CAJA N4', 
     'caja_5' => 'BUZON VERDE - CAJA N5', 
     'caja_6' => 'BUZON VERDE - CAJA N6', 
     'caja_7' => 'BUZON AZUL - CAJA N7', 
     'caja_8' => 'BUZON AZUL - CAJA N8', 
     'caja_9' => 'BUZON NARANJA - CAJA N9', 
     'caja_10' => 'BUZON NARANJA - CAJA N10', 
  ];
  static $keys_color = [
     'caja_1' => 'ROJO',
     'caja_2' => 'ROJO',
     'caja_3' => 'AMARILLO',
     'caja_4' => 'AMARILLO',
     'caja_5' => 'VERDE',
     'caja_6' => 'VERDE',
     'caja_7' => 'AZUL',
     'caja_8' => 'AZUL',
     'caja_9' => 'NARANJA',
     'caja_10' => 'NARANJA',
  ];
  
  static $keys_caja = [
     'caja_1' => 'N1', 
     'caja_2' => 'N2', 
     'caja_3' => 'N3', 
     'caja_4' => 'N4', 
     'caja_5' => 'N5', 
     'caja_6' => 'N6', 
     'caja_7' => 'N7', 
     'caja_8' => 'N8', 
     'caja_9' => 'N9', 
     'caja_10' => 'N10', 
  ];
  
          
      
  
  static function getall(){
   $aux = self::$keys;   
   $aux1 = self::$keys_name;
   $aux2 = self::$keys_color;
   $aux3 = self::$keys_caja;
   foreach ($aux as $k=>$v){
     echo "('".$aux1[$k]."','".$v."', '".$aux2[$k]."','".$aux3[$k]."'),";
   }
  }
  function getKey(){
    $aux = self::$keys;
    if (isset($aux[$this->key])) return $aux[$this->key];
    return '--';
  }
  
  function getBuzon(){
    $aux = self::$keys_name;
    if (isset($aux[$this->key])) return $aux[$this->key];
    return '--';
  }
  function getBuzonColor(){
    $aux = self::$keys_color;
    if (isset($aux[$this->key])) return $aux[$this->key];
    return '--';
  }
  function getBuzonCaja(){
    $aux = self::$keys_caja;
    if (isset($aux[$this->key])) return $aux[$this->key];
    return '--';
  }*/
}

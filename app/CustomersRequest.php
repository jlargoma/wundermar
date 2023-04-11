<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomersRequest extends Model
{
  public static function createOrEdit($data,$site_id){
    if (isset($data['usr'])){
      $usr = $data['usr'];
      $email = isset($usr['c_mail']) ? trim($usr['c_mail']) : '';
      $customer = CustomersRequest::where('email',$email)->first();
      if (!$customer){
        $customer = new CustomersRequest();
        $customer->email = $email;
        $customer->site_id = $site_id;
      }
      
      if(isset($usr['c_name']))  $customer->name = trim($usr['c_name']);
      if(isset($usr['c_phone'])) $customer->phone = trim($usr['c_phone']);
      if(isset($usr['c_ip']))    $customer->ip = trim($usr['c_ip']);
      if(isset($data['pax']))    $customer->pax = trim($data['pax']);
      if(isset($data['start']))  $customer->start = trim($data['start']);
      if(isset($data['end']))    $customer->finish = trim($data['end']);
      $customer->status = 0;
      $customer->save();
    }   
  }
  
  public static function removeIfExist($email,$site_id){
    $customer = CustomersRequest::where('email',$email)->where('site_id',$site_id)->first();
    if ($customer){
      $customer->delete();
    }
  }
  
  
  public static function getCustomersLst(){
    return CustomersRequest::whereNull('book_id')->get();
  }
  
  public function getMediaPrice(){
    if (!$this->site_id || !$this->pax) return 0;
    $oItems = RoomsType::where('status',1)
                ->where('site_id',$this->site_id)
                ->where('min_pax','<=',$this->pax)
                ->where('max_pax','>=',$this->pax)
                ->get();
    
    $aPrices = [];
    if ($oItems){
        foreach ($oItems as $item){
          $oRoom = Rooms::where('channel_group',$item->channel_group)
              ->where('maxOcu','>=', $this->pax)->where('state',1)->first();
          if ($oRoom){
            $price = $oRoom->getPvp($this->start,$this->finish,$this->pax);
            $ExtraPrices = \App\ExtraPrices::getFixed($item->channel_group)->sum('price');
            $roomPrice = $item->getPriceOrig($price,$item->channel_group); //Booking price
            $aPrices[] = $roomPrice + $ExtraPrices;
          }
        }
       if (count($aPrices))
       return ceil(array_sum($aPrices) / count($aPrices));
    }
    return 0;
  }
  
  /**********************************************************************/
  /////////  customers_requests_meta //////////////
  
  public function getMetasContet($key) {
    
    $lst = DB::table('customers_requests_meta')
            ->where('cr_id',$this->id)->where('meta_key',$key)->get();
    $return = [];
    if ($lst){
      
      $users = \App\User::where('role','!=','limpieza')->get();
      $aUsers = [];
      foreach ($users as $v){
        $aUsers[$v->id] = $v->name;
      }
    
      foreach ($lst as $item){
        $return[] = [
          'user_id' => $item->user_id,
          'username' => isset($aUsers[$item->user_id]) ? $aUsers[$item->user_id] : '--',
          'date' => convertDateTimeToShow_text($item->created_at),
          'val' => unserialize($item->meta_value),
        ];
      }
    }
    
    return $return;
    
  }
  public function sentMail($siteData) {
    $siteData['content'] = $this->comment;
    DB::table('customers_requests_meta')->insert([
        ['user_id' => $this->update_by, 'cr_id' => $this->id,'meta_key' => 'mailSent', 'meta_value' => serialize($siteData)],
    ]);
  }
}

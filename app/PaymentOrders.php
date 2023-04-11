<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentOrders extends Model
{
  function getPaymentSite(){
    $siteID = $this->site_id;
    $paymentRule = \App\Settings::where('key','payment_rule')->where('site_id',$siteID)->first();
    $has_fianza = false;
//    if ($paymentRule){
//      $rule = json_decode($paymentRule->content);
//      if ($rule){
//          $has_fianza  = isset( $rule->fianza ) ?  $rule->fianza : 1;
//      }
//    }
    
    $Site = Sites::find($siteID);
    return ['site'=>$Site, 'has_fianza'=>$has_fianza];
  }
}

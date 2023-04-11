<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sites extends Model
{
  static function siteData($id) {
    
    $data = [
        'name' => config('app.site'),
        'url' => config('app.url'),
        'mail_from' => config('mail.from.address'),
        'mail_name' => config('mail.from.name'),
    ];
    if ($id>0){
      $site = self::find($id);
      if ($site && $site->id == $id){
         $data['name'] = $site->name;
         $data['url']  = $site->url;
         $data['mail_from']  = $site->mail_from;
         $data['mail_name']  = $site->mail_name;
      }
    }
    
    return $data;
    
  }
  
  static function siteIDs() {
    return [
        1,//RIAD
//        2,//Rosa D'Oro
        3,//Gloria
//        4,//CALLE ELVIRA
        5,//Siloe
        6,//Zahira
    ];
  }
  
  static function bSite() {
    return[
        0=>['Todos',''],1=>['',0],3=>['',0],4=>['',0],5=>['',0],6=>['',0]];
        // 0=>['Todos',''],1=>['',0],2=>['',0],3=>['',0],4=>['',0],5=>['',0],6=>['',0]];
  }
  
  
  static function allSites() {

    $site = self::where('status',1)->get();
    $data = [];
    foreach ($site as $s){
       $data[$s->id] = $s->name;
    }
    
    return $data;
    
  }
  
  static function allSitesKey() {

    $site = self::where('status',1)->get();
    $data = [];
    foreach ($site as $s){
       $data[$s->id] = $s->site;
    }
    
    return $data;
    
  }
  
  static function allSitesEnable() {

    $site = self::whereIn('id', self::siteIDs())->get();
    $data = [];
    foreach ($site as $s){
       $data[$s->id] = $s->name;
    }
    
    return $data;
    
  }
}

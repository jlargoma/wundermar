<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogsData extends Model
{
  protected $table = 'logs_data';
  
   public function infoProceess($key,$msg,$content=null) {
    LogsData::insert([
        'key' => $key,
        'data' => $msg,
        'long_info' => $content,
    ]);
  }
  
  static function getLastInfo($key,$limit = 5) {
    $oLst = LogsData::where('key',$key)
            ->orderBy('created_at','DESC')->limit($limit)
            ->get();
  
    $lst = '<ul>';
    if ($oLst){
      foreach ($oLst as $l)
        $lst .= '<li><b>'. convertDateTimeToShow_text($l->created_at).':</b> '.$l->data.'</li>';
      
    } else {
      $lst .= '<li>Aún no hay registros cargados para el mes en curso</li>';
    }
    $lst .= '</ul>';
    return $lst;
  }
  
  
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];
    
    
    static function getKeysSettingsGen() {
      return [
          'partee_apartament_1'   => array('label' => 'Partee: ID Apartamento Aptos Riad','val'=>null),
//          'partee_apartament_2'   => array('label' => 'Partee: ID Apartamento hotel Rosa de Oro','val'=>null),
          'partee_apartament_3'   => array('label' => 'Partee: ID Apartamento Apartamentos Gloria','val'=>null),
//          'partee_apartament_4'   => array('label' => 'Partee: ID EDIF. ELVIRA 109','val'=>null),
          'partee_apartament_5'   => array('label' => 'Partee: ID SILOE','val'=>null),
          'partee_apartament_6' => array('label' => 'Partee: ID ZAHIRA SUITES', 'val' => null),
          'send_sms_days'       => array('label' => 'Enviar SMS Partee a % días del CheckIn','val'=>null),
      ];
    }
    
    static function getKeyValue($key){     
      $obj = Settings::select('value')->where('key', $key)->first();
      if ($obj){
        return $obj->value;
      } else {
        return null;
      }
      
    }

    static function getKeysWSP() {
      return [
           'send_encuesta_subject',
           'text_payment_link',
           'SMS_Partee_msg',
           'SMS_buzon',
      ];
    }
    static function getKeysTxtMails($lng='es') {
      $lng = self::getLenguaje(strtoupper($lng));
      
      $lst = [
          'new_request_rva'                   =>'Solicitud RVA',
          'reservation_state_changed_reserv'  =>'Reservado RVA',
          'reservation_state_changed_reserv_airbnb'  =>'Reservado AirBnb',
          'reservation_state_changed_confirm' =>'Confirmado RVA',
          'reservation_state_changed_cancel'  =>'Denegada RVA',
          'reserva-propietario'               =>'RVA Propietario',
          'second_payment_reminder'           =>'Recordatorio 2º pago',
          'web_payment'                       =>'Pago desde la web',
          'second_payment_confirm'            =>'Confirmación del 2º pago',
          'reservation_state_mail_response'   =>'Constestado Email',
          'book_email_buzon'                  =>'Mail Buzón',
          'SMS_buzon'                         =>'WSP Buzón',
          'MAIL_Partee'                       =>'Mail Partee', // SMS_Partee_upload_dni
          'SMS_Partee_msg'                    =>'WSP Partee',
          'payment_receipt'                   =>'Mail de recibos de pagos',
          'send_encuesta'                     =>'Mail de Encuestas',
          'send_encuesta_subject'             =>'Asunto de Encuestas',
          'text_payment_link'                 =>'WSP LINKS PAGO',
          'widget_observations'               =>'Widget: Observaciones de su reserva',
//          'widget_extras_paking'              =>'Widget: Observaciones Suplemento Parking',
//          'widget_extras_breakfast'           =>'Widget: Observaciones Suplemento Desayuno',
//          'widget_alert_cancelation'          =>'Widget: Popover Cancelación gratuita',
          //'book_email_supplements'            =>'Mail compra de Suplementos',
          //'puncharseSupl'                     =>'Mail de Suplementos pagados',
          'mail_checkin_msg'                  => 'Mensaje contacto y teléf. checkin',
          'mail_cancelBloq' => 'Bloqueo Cancelado - Pago Vencido',
      ];
      if ($lng && $lng != 'es'){
        $lstNew = [];
        foreach ($lst as $k=>$v){
          $lstNew[$k.'_'.$lng] = $v." ($lng)";
        }
        return $lstNew;
      }
      
      return $lst;
    }
    
    static function getContent($key,$lng='es',$site=1) {

      $lng = self::getLenguaje(strtoupper($lng));
      $Object = null;
      
      if ($lng == 'en'){
        $Object = Settings::where('site_id',$site)->where('key',$key.'_en')->first();
      }
       
      if (!$Object || trim($Object->content) == ''){
        $Object = Settings::where('site_id',$site)->where('key',$key)->first(); 
      }
            
      if ($Object){
       return $Object->content;
      }
      
      return '';
      
    }
    
    static function CountriesLang(){
      $countryEs = [
          'Argentina','Bolivia','Chile','Colombia','Costa Rica','Cuba',
          'Ecuador','El Salvador','España','Guatemala','Guinea Ecuatorial',
          'Honduras','México','Nicaragua','Panamá','Paraguay','Perú',
          'República Dominicana','Uruguay','Venezuela'
          ];
      
      $lst = [];
      foreach ($countryEs as $c){
        $lst['es'] = $c;
      }
      
      $lst['en'] = 'Otros';
      return $lst;
    }
   
    static function getParteeBySite($site_id=1){
      $key = 'partee_apartament_'.$site_id;
      return Settings::getKeyValue($key);
    }
    
    static function priceParking(){
      $parkCostSetting = self::where('key','parking_book_cost')->first();
      if (!$parkCostSetting) return 0;
      return $parkCostSetting->value;
    }
    
    static function priceLujo(){
      $luxuryCostSetting = self::where('key', 'luxury_book_cost')->first();
      if ($luxuryCostSetting)  return $luxuryCostSetting->value;
      return 0;
    }
    
    static function getLenguaje($country){
      $lngSP = ['AR','BO','CL','CO','CR','CU','EC','ES','SV','DO','GQ','GT','HN','MX','NI','PA','PE','PY','UY','VE'];
      if (in_array($country, $lngSP)){
        return 'es';
      }
      return 'en';
    }
    
    // Put this in any model and use
    // Modelname::findOrCreate($id);
    public static function findOrCreate($key,$site=1)
    {
        $obj = static::where('site_id',$site)->where('key',$key)->first(); 
        if ($obj) return $obj;

        $obj = new static;
        $obj->site_id = $site;
        $obj->key = $key;
        $obj->save();
        return $obj;
    }
    
    
        
    static function getLongKeysSettingsGen() {
      $return = [];
      $sites = Sites::allSites();
      foreach ($sites as $id=>$name){
        $return['gha_sitio_'.$id] = array('label' => 'URL GH '.$name,'val'=>null);
      }
      return $return;
    }
    
    static function getLongKeyValue($key){     
      $obj = Settings::select('content')->where('key', $key)->first();
      if ($obj){
        return $obj->content;
      } else {
        return null;
      }
      
    }
}

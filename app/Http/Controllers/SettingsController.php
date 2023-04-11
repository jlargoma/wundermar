<?php

namespace App\Http\Controllers;

use App\AgentsRooms;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\ExtraPrices;

class SettingsController extends AppController {
  /*
   * key basics to get price of book and pay it
   * */

  const PARK_COST_SETTING_CODE = "parking_book_cost";
  const PARK_PVP_SETTING_CODE = "parking_book_price";
  const LUXURY_COST_SETTING_CODE = "luxury_book_cost";
  const LUXURY_PVP_SETTING_CODE = "luxury_book_price";
  const DISCOUNT_BOOKS_SETTING_CODE = "discount_books";

  private $settingsForBooks = [
      self::PARK_COST_SETTING_CODE => 'Cost Sup Park',
      self::PARK_PVP_SETTING_CODE => 'PVP Sup Park',
      self::LUXURY_COST_SETTING_CODE => 'Cost Sup Lujo',
      self::LUXURY_PVP_SETTING_CODE => 'PVP Sup Lujo',
      self::DISCOUNT_BOOKS_SETTING_CODE => 'Descuento directo sobre las reservas ',
          //'book_instant_payment',
  ];

  public function index() {
    //Prepare general settings values
    $generalKeys = Settings::getKeysSettingsGen();
    $generalValues = Settings::whereIn('key', array_keys($generalKeys))->get();

    if ($generalValues) {
      foreach ($generalValues as $s) {
        if (isset($generalKeys[$s->key])) {
          $generalKeys[$s->key]['val'] = $s->value;
        }
      }
    }
    //END: Prepare general settings values
    
    //Prepare long settings values
    $generalLongsKeys = Settings::getLongKeysSettingsGen();
    $generalLongsValues = Settings::whereIn('key', array_keys($generalLongsKeys))->get();
    if ($generalLongsValues) {
      foreach ($generalLongsValues as $s) {
        if (isset($generalLongsKeys[$s->key])) {
          $generalLongsKeys[$s->key]['val'] = $s->content;
        }
      }
    }
    //END: Prepare long settings values


    $channelGroups =configZodomusAptos();
                          
    
    $payment_rule = Settings::where('key', 'payment_rule')->get();
    
    $oSites = \App\Sites::all();
    $sites = [];
    foreach ($oSites as $s) $sites[$s->id] = $s->name;
            
    

    return view('backend/settings/index', [
        'general' => $generalKeys,
        'generalLongsKeys' => $generalLongsKeys,
        'payment_rule' => $payment_rule,
//        'sites' => \App\Sites::all(),
        'sites' => $sites,
        'channelGroups' => $channelGroups,
        'oSites' => $oSites,
//            'extras'          => \App\Extras::all(),
        'settingsBooks' => $this->settingsForBooks,
        'agentsRooms' => \App\AgentsRooms::all(),
        
        'year' => $this->getActiveYear(),
    ]);
  }

  public function createAgentRoom(Request $request) {

    $agent = AgentsRooms::create([
                "room_id" => $request->input('room_id'),
                "user_id" => $request->input('user_id'),
                "agency_id" => $request->input('agency_id'),
    ]);
    if ($agent) {
      return redirect()->back();
    }
  }

  public function deleteAgentRoom($id) {
    $agent = \App\AgentsRooms::find($id);
    if ($agent->delete()) {
      return redirect()->back();
    }
  }

  public function createUpdateSetting(Request $request) {
    $code = $request->input('code');
    $value = $request->input('value');
    $issetSetting = \App\Settings::where('key', $code)->first();

    if ($issetSetting) {
      $setting = $issetSetting;
    } else {
      $setting = new \App\Settings();
    }

    $setting->name = $this->settingsForBooks[$code];
    $setting->key = $code;
    $setting->value = $value;

    if ($setting->save())
      return new Response(\GuzzleHttp\json_encode([
                  'status' => 'OK',
                  'message' => 'Datos Guardados correctamente',
              ]), 200);
    else
      return new Response(\GuzzleHttp\json_encode([
                  'status' => 'KO',
                  'message' => 'Error durante el proceso de guardado, intentelo de nuevo más tarde',
              ]), 200);
  }

  /**
   * Get messages page
   */
  public function messages($site = 1, $key='new_request_rva') {
    //get all emial's options
    $settings = Settings::getKeysTxtMails();

    //get from DB all messages
    $data = ['es'=>null,'en'=>null,'es_ota'=>null,'en_ota'=>null];
    $content = Settings::where('site_id', $site)->where('key', $key)->first();
    if ($content) {
      $data['es'] = $content->content;
    }
    $content = Settings::where('site_id', $site)->where('key', $key.'_en')->first();
    if ($content) {
      $data['en'] = $content->content;
    }
    if( $key == 'reservation_state_changed_reserv'){
      $content = Settings::where('site_id', $site)->where('key', $key.'_ota')->first();
      if ($content) {
        $data['es_ota'] = $content->content;
      }
      $content = Settings::where('site_id', $site)->where('key', $key.'_ota_en')->first();
      if ($content) {
        $data['en_ota'] = $content->content;
      }
    }
   
    
    $url_sp = '/admin/settings_msgs/'.$site.'/es';
    $url_en = '/admin/settings_msgs/'.$site.'/en';
    
    
    $kWSP = Settings::getKeysWSP();
    $ckeditor = true;
    if ( in_array($key,$kWSP)) $ckeditor = false;
         
    include_once app_path('Help/VariablesTxts.php');
                
    return view('backend/settings/txt-email', [
        'settings' => $settings,
        'data' => $data,
        'lng' => 'es',
        'key' => $key,
        'site' => $site,
        'ckeditor' => $ckeditor,
        'kWSP' => $kWSP,
        'url_en' => $url_en,
        'url_sp' => $url_sp,
        'varsTxt' => $varsTxt,
    ]);
  }

  /**
   * Save the email template setting
   *
   * @param Request $request
   * @return type
   */
  public function messages_upd(Request $request, $site = 1, $lng = 'es') {
    
    $key = $request->input('key');
    $sNames = Settings::getKeysTxtMails();
    $n = isset($sNames[$key]) ? $sNames[$key]:'';
    
    
    $subKeys=['','_ota','_en','_ota_en'];
    foreach ($subKeys as $k){
      $key2 = $key.$k;
      $text = $request->input($key2,null);
      $this->saveTextMails($site,$key2,$n,$text);
    }

    return back()->with('status', 'Setting updated!');
  }
  
  private function saveTextMails($site,$key,$name,$text) {
    $Object = Settings::where('site_id', $site)->where('key', $key)->first();
    if ($Object) {
      $Object->site_id = $site;
      $Object->content = $text;
      $Object->save();
    } else {

      $Object = new Settings();
      $Object->key = $key;
      $Object->name = $name;
      $Object->value = 0;
      $Object->content = $text;
      $Object->site_id = $site;
      $Object->save();
    }
      
  }

  /**
   * Save the general setting
   *
   * @param Request $request
   * @return type
   */
  public function upd_general(Request $request) {

    //Prepare general settings values
    $generalKeys = Settings::getKeysSettingsGen();
    if ($generalKeys) {
      foreach ($generalKeys as $k => $v) {
        $value = $request->input($k, '');
        $obj = Settings::firstOrNew(array('key' => $k));
        $obj->value = $value;
        $obj->save();
      }
    }
    return back()->with('success-gral', 'Setting updated!');
  }
  
  public function upd_sites(Request $request) {

     $oSites = \App\Sites::all();
    if ($oSites) {
      foreach ($oSites as $site) {
        $site->title = $request->input('title'.$site->id, '');
        $site->name = $request->input('name'.$site->id, '');
        $site->mail_name = $request->input('mail_name'.$site->id, '');
        $site->mail_from = $request->input('mail_from'.$site->id, '');
        $site->url = $request->input('url'.$site->id, '');
        $site->save();
      }
    }
    return back()->with('success-sites', 'Edificios actualizados!');
  }
    /**
   * Save the general setting
   *
   * @param Request $request
   * @return type
   */
  public function upd_longs_general(Request $request) {

    //Prepare general settings values
    $generalKeys = Settings::getLongKeysSettingsGen();
    if ($generalKeys) {
      foreach ($generalKeys as $k => $v) {
        $value = $request->input($k, '');
        $obj = Settings::firstOrNew(array('key' => $k));
        $obj->content = $value;
        $obj->save();
      }
    }
    return back()->with('success-longs', 'Setting updated!');
  }

  public function createExtraPrices(Request $request) {
    
      $extra = new ExtraPrices();
      $extra->name = $request->input('name');
      $extra->price = $request->input('price');
      $extra->cost = $request->input('cost');
      $extra->channel_group = $request->input('channel_group');
      $extra->fixed = $request->input('fixed');
      $extra->type = $request->input('type');
      $extra->save();
//      id	price	cost	channel_group	fixed	deleted	created_at	updated_at


      return redirect()->back();
   
  }
  public function updExtraPrices(Request $request) {
    
    $id = $request->input('id');
    $extra = ExtraPrices::find($id);
    if ($extra->id == $id){
      $extra->price = $request->input('price');
      $extra->cost = $request->input('cost');
      $extra->channel_group = $request->input('apto');
      $extra->type = $request->input('type');
      $extra->save();
      echo "ok";
      exit();
    }
    echo 'Extra no encontrado';
  }
  public function delteExtraPrices(Request $request) {
    
    $id = $request->input('id');
    $extra = ExtraPrices::find($id);
    if ($extra->id == $id){
      $extra->deleted = 1;
      if ($extra->save()) return "OK";
    }
    return 'Extra no encontrado';
  }
  public function updateWeiland(Request $request) {
    
    $id = $request->input('id');
    $Weiland = Settings::find($id);
    if ($Weiland->id == $id){
      
      $data = [
          'percent' => $request->input('percent'),
          'days' => $request->input('numDays'),
          'fianza' => $request->input('fianza'),
      ];
      $Weiland->content = json_encode($data);
      $Weiland->save();
      echo "ok";
      exit();
    }
    echo 'Weiland no encontrado';
  }
  
   public function updExtraPaxPrice(Request $request) {
    
    $price = $request->input('price');
    
    $obj = Settings::where('key', 'price_extr_pax')->first();
    if (!$obj){
      $obj = new Settings();
      $obj->key =  'price_extr_pax';
    }
    $obj->value = intval($price);
    $obj->save();
    return redirect()->back()->with('success','Precio por PAX estras guardado.');
  }
  
  /**
   * http://riad.virtual/test-text/es/text_payment_link
   * @param type $lng
   */
  function testText($lng='es',$key=null,$ota=null){
    
    $settings = Settings::getKeysTxtMails();
    $name = isset($settings[$key]) ? $settings[$key] : $key;
    
    $keyFind = $key;
    if($ota) $keyFind .= '_ota';
    if($lng == 'en') $keyFind .= '_en';
   
    
    $sites = \App\Sites::allSites();
    //---------------------------------------------------------// 
    $data = [];
    if ($key){
      foreach ($sites as $k=>$v) 
        $data[$key][$k] = '<warning>No Cargado</warning>';
      
      $keysValue = Settings::where('key', $keyFind)->get();
      foreach ($keysValue as $item) {
        $data[$key][$item->site_id] = $item->content;
      }
    } else {
      foreach ($settings as $k1=>$v1)
        foreach ($sites as $k=>$v) 
          $data[$k1][$k] = '<warning>No Cargado</warning>';
      
      $keysValue = Settings::whereIn('key', array_keys($settings))->get();
      foreach ($keysValue as $item) {
        $data[$item->key][$item->site_id] = $item->content;
      }
    }
    //---------------------------------------------------------// 
   
    include_once app_path('Help/VariablesTxts.php');
    
        
    $kWSP = Settings::getKeysWSP();
    $wsp = false;
    if ( in_array($key,$kWSP)) $wsp = true;
    
    return view('backend/settings/test-txt-email', [
        'data' => $data,
        'sites' => $sites,
        'lng' => $lng,
        'key' => $key,
        'ota' => $ota,
        'wsp' => $wsp,
        'name' => $name,
        'varsTxt'=>$varsTxt,
        'settings' => $settings,
    ]);
  }
  
}

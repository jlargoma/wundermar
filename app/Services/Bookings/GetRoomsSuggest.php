<?php

namespace App\Services\Bookings;

use App\Rooms;
use App\Book;
use App\Settings;
use Illuminate\Support\Facades\Auth;
use App\Services\OtaGateway\Config;
use App\RoomsType;

/**
 * Description of MultipleRoomLock
 *
 * @author cremonapg
 */
class GetRoomsSuggest {

  var $size_apto;
  var $siteID;
  
  public function __construct() {
    $this->size_apto = null;
    $this->siteID = null;
  }

  function size_apto($size_apto){
    $this->size_apto = $size_apto;
  }

  function set_siteID($id){
    $this->siteID = $id;
  }

  private function getItems($pax) {
      
   
    if (!is_numeric($pax) || $pax < 0)
      return [];

    $qry= RoomsType::where('min_pax', '<=', $pax)
        ->where('max_pax', '>=', $pax);
    
    if ($this->siteID && is_numeric($this->siteID)){
      $qry->where('site_id',$this->siteID);
    }
    return $qry->get();
  }

  public function getItemsSuggest($pax, $date_start, $date_finish) {
    $result = [];
    $response = $auxPrices = [];
    $oItems = $this->getItems($pax);
    $nigths = calcNights($date_start, $date_finish);
    $infoCancel = Settings::getContent('widget_alert_cancelation', 'es',$this->siteID);
    if ($oItems) {
      $index = 0;
      foreach ($oItems as $item) {

        $roomData = $this->getRoomsPvpAvail($date_start, $date_finish, $pax, $item->channel_group);
        if (!isset($roomData['prices']) || !$roomData['prices'])
          continue;
        if ($roomData['prices']['pvp'] < 1)
          continue;
        $roomPrice = $roomData['prices'];
        $minStay = $roomData['minStay'];
        //descriminamos el precio de limpieza
        $pvp = $roomPrice['pvp_init'];
        $pvp_1 = $roomPrice['pvp'] - $roomPrice['price_limp'];
        // promociones tipo 7x4
        $hasPromo = 0;
        if ($roomPrice['promo_pvp'] > 0)
          $hasPromo = $roomPrice['promo_name'];
        ////////////////////////
        $pvp_2 = 99999;
        $auxPrices[] = $pvp;
        $response[] = [
            'name' => $item->name,
            'title' => $item->title,
            'sid' => $item->site_id,
            'max_pax' => $item->max_pax,
            'availiable' => $roomData['availiable'],
            'code' => encriptID($item->id),
            'sugID' => encriptID($roomData['sugID']),
            'price' => moneda($pvp, true),
            'pvp' => round($pvp),
            'extr_costs' => $roomPrice['price_limp'],
            'price_1' => moneda($pvp_1, true),
            'pvp_1' => round($pvp_1),
            'discount_1' => round(15+$roomPrice['discount']),
            'promo_discount' => round(15+$roomPrice['discount']),
            'pvp_discount' => round($roomPrice['discount_pvp']),
            'promo_name' => $hasPromo,
            'pvp_promo' => round($roomPrice['promo_pvp']),
            'promo_discount_name' => $roomPrice['discount_name'],
            'promo_discount_pvp' => $roomPrice['discount_pvp'],
//            'price_2' => moneda($pvp_2),
//            'pvp_2' => round($pvp_2),
//            'discount_2' => $this->discount_2 * 100,
            'minStay' => ($nigths < $minStay) ? $minStay : 0,
            'infoCancel' => $infoCancel,
        ];
      }
    }
    if (count($response) > 0) {
      asort($auxPrices);

      foreach ($auxPrices as $k => $v) {
        $result[] = $response[$k];
      }
    }
    return $result;
  }

  public function getRoomsPvpAvail($startDate, $endDate, $pax, $channel_group) {

    $book = new Book();
    $return = [
        'prices' => null,
        'minStay' => 0,
        'availiable' => 0
    ];

    $qry = Rooms::where('channel_group', $channel_group)
            ->where('maxOcu', '>=', $pax)->where('state', 1);
    
    if ($this->size_apto){
      $qry->where('sizeApto',$this->size_apto);
    }
    $oRooms = $qry->get();
    
    if ($oRooms) {
      $availibility = $book->getAvailibilityBy_channel($channel_group, $startDate, $endDate);
      $availiable = count($oRooms);
      if (count($availibility)) {
        foreach ($availibility as $day => $avail) {
          if ($avail < $availiable) {
            $availiable = $avail;
          }
        }
      }
      foreach ($oRooms as $room) {
        if ($book->availDate($startDate, $endDate, $room->id)) {
          $meta_price = $room->getRoomPrice($startDate, $endDate, $pax);
          return [
              'prices' => $meta_price,
              'minStay' => $room->getMin_estancia($startDate, $endDate),
              'availiable' => $availiable,
              'sugID'=>$room->id,
          ];
        }
      }
    }
    return $return;
  }

}

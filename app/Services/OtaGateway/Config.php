<?php

namespace App\Services\OtaGateway;

class Config {

  /**
   * restriction_plan_id
   */
  public function restriction_plan($ota = null, $site = null) {
    $ota = intval($ota);
    switch ($ota) {
      case 1://"Booking.com",
        switch ($site) {
          case 1: return null;
        }
      case 6://Expedia,
      case 28://Expedia,
        switch ($site) {
          case 1: return null;
        }
      case 4://"AirBnb"
        return null;
      case 99:
      case 7://google GHotels
        switch ($site) {
          case 1: return null;
        }
      case 3://"agoda"
        return null;
    }
    return null;
  }

  /**
   * Rate Plan ID
   */
  public function Plans($ota = null, $site = null) {
    $ota = intval($ota);
//    if ($ota == 1) return 862;
//    if ($ota == 4) return 863;

    switch ($ota) {
      case 1://"Booking.com",
        switch ($site) {
          case 1: return null;
        }

      case 28://Expedia,
      case 6://Expedia,
        switch ($site) {
          case 1: return null;
        }
      case 4://"AirBnb"
        return null;
      case 99:
      case 7://google GHotels
        switch ($site) {
          case 1: return null;
        }
      case 3://"agoda"
        return null;
    }
    return null;
  }

  /**
   * Apply math function to the price based on the Channel
   * @param type $params
   * @return type
   */
  public function processPriceRates($params, $channel_group) {

    if (isset($params['prices'])) {
      $channelId = $params['channelId'];
      $price = $params['prices']['price'];
      $priceSingle = isset($params['prices']['priceSingle']) ? $params['prices']['priceSingle'] : 0;

      $price = $this->priceByChannel($price, $channelId, $channel_group);
      $priceSingle = $this->priceByChannel($priceSingle, $channelId, $channel_group);

      $params['prices']['price'] = ceil($price);
      if (isset($params['prices']['priceSingle']))
        $params['prices']['priceSingle'] = ceil($priceSingle);
    }

    return $params;
  }

  public function priceByChannel($price, $channelId = null, $room = null, $text = false, $nights = 1, $day = null) {


    if (!$price || !is_numeric($price)) {
      if ($text)
        $price = 1;
      else
        return null;
    }

    global $roomsLst, $agencyLst, $discounts, $prices_ota;

    if (!$roomsLst)
      $roomsLst = $this->getRooms();
    if (!$agencyLst)
      $agencyLst = $this->getAllAgency();
    if (!$discounts)
      $discounts = [];

    if (is_numeric($room)) {
      $aux = array_search($room, $roomsLst);
      if ($aux)
        $room = $aux;
    }

    $priceText = '';
    if (!$prices_ota) {
      $prices_ota = \App\Settings::getContent('prices_ota');
      $prices_ota = unserialize($prices_ota);
    }
    if (!isset($discounts[$room]))
      $discounts[$room] = $this->getAllDiscounts($room);
    if ($prices_ota) {
      if (is_array($prices_ota) && isset($prices_ota[$room . $channelId])) {
        $priceData = $prices_ota[$room . $channelId];
        //incremento el valor fijo por noche
        if ($priceData['f']) {
          $price += $priceData['f'] * $nights;
          $priceText = '(PVP+' . $priceData['f'] . '€)';
        }

        //incremento el valo por porcentaje
        if ($priceData['p']) {
          $price = $price * (1 + ($priceData['p'] / 100));
          $priceText .= '+' . $priceData['p'] . '%';
        }


        if ($text)
          return $priceText;
        /** BEGIN: descuento para GHotels */
        if ($day && ($channelId == 99 || $channelId == 7)) {
          if (isset($discounts[$room]) && isset($discounts[$room][$day])) {
            $discount = $discounts[$room][$day];
            if ($discount > 0) {
              $price = round($price - ($price * $discount / 100));
            }
          }
        }
        /** END: descuento para GHotels */
        return $price;
      }

      //if the price is not load
      if ($text)
        return '--';
      return null;
    } else {
      return null;
    }
  }

  public function priceAirbnb($p, $chnGr = null) {
    return $this->priceByChannel($p,4,$chnGr);
  }
  function get_detailRate($rateID) {
    
  }

  function get_comision($price, $channelId = null) {
    $comision = 0;
    switch ($channelId) {
      case 1:
      case "1": //"Booking.com",
        $comision = ($price * 0.17);
        break;
    }
    return round($comision, 2);
  }

  public function getAllAgency() {
    return [
        'airbnb' => 4,
        'booking' => 1,
        'expedia' => 6,
        'google-hotel' => 7,
//        'agoda' => 3,
    ];
  }

  public function getAgency($id_chanel) {
    // airbnb => 4,
    //booking => 1
    if (!$id_chanel)
      return 7;
    $chanels = $this->getAllAgency();

    return isset($chanels[$id_chanel]) ? $chanels[$id_chanel] : -1;
  }

  /////////////////////////////////////////////////////////////////////////////


  function setRooms() {

    include_once dirname(__FILE__) . '/rooms.php';
    foreach ($rooms['roomtypes'] as $roomtypes) {
      echo "'{$roomtypes["name"]}' => {$roomtypes["id"]},<br/>";
//      echo "'{$roomtypes["name"]}' => [<br/>"
//      . "&nbsp;&nbsp; 'name' => '{$roomtypes["description"]}',<br/>"
//      . "&nbsp;&nbsp;  'roomID'=> {$roomtypes["id"]} <br/>],<br/>";
    }
    dd($rooms);
  }

  function getRooms($room = null) {
    $lst = [
        'RIAD1' => null,
        'LOCAL' => null,
    ];
    if ($room) {
      return isset($lst[$room]) ? $lst[$room] : -1;
    }
    return $lst;
  }

  function getRoomsName() {
    return [
        'RIAD1' => '',
        'LOCAL' => 'LOCAL',
    ];
  }

  function getChannelByRoom($roomtype_id) {
    $ch = array_search($roomtype_id, $this->getRooms());
    return $ch === FALSE ? 'ROOMDD' : $ch;
  }

  public function getAllDiscounts($ota) {
    $oPromo = new \App\Promotions();
    return $oPromo->getAllDiscount($ota);
  }

}

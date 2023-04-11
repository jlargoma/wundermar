<?php

namespace App\Services\Bookings;

use App\Rooms;
use App\Customers;
use App\Book;
use App\Settings;
use Illuminate\Support\Facades\Auth;

/**
 * Description of MultipleRoomLock
 *
 * @author cremonapg
 */
class MultipleRoomLock {

  protected $type_book = '';
  public $start;
  public $finish;
  public $days;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    $this->type_book = Book::get_type_book_reserved();
  }

  public function roomLockBy_site($siteID, $start, $finish) {

    $this->start = $start;
    $this->finish = $finish;
    $this->days = arrayDays($this->start, $this->finish, 'ymd', 0);

    $oBookings = new Book();
    $customerID = null;
    $aRoomsLst = Rooms::where('site_id', $siteID)->get();
    $channelsRoom = [];
    $oUser = Auth::user();
    foreach ($aRoomsLst as $room) {
      $aDays = $this->calcRangBkg($room->id);
      if (count($aDays)>0){
        $channelsRoom[$room->channel_group] = $room->id;
      }
      foreach ($aDays as $range){
        if (!$customerID) {
          $oCustomers = new Customers();
          $oCustomers->user_id = $oUser ? $oUser->id : 1;
          $oCustomers->name = 'Bloqueo ' . ($oUser ? $oUser->name : 'automatico');
          $oCustomers->save();
          $customerID = $oCustomers->id;
        }
        
        $book = new Book();
        $book->user_id = $oUser ? $oUser->id : 1;
        $book->customer_id = $customerID;
        $book->room_id = $room->id;
        $book->start = $range[0];
        $book->finish = $range[1];
        $book->nigths = calcNights($book->start, $book->finish);
        $book->type_book = 4;
        $book->save();
      }
    }
    if (count($channelsRoom)>0){
      foreach ($channelsRoom as $ch => $roomID) {
        $oBookings->sendAvailibility($roomID, $start, $finish);
      }
    }
  }

  public function get_RoomLockSetting($sites) {

    $oTaskData = Settings::findOrCreate('multiple_room_lock', null);
    $aTaskData = json_decode($oTaskData->content, true);
    if (!$aTaskData) {
      $aTaskData = ['time' => 0, 'sites' => []];
    }
    foreach ($sites as $k => $v) {
      if (!isset($aTaskData['sites'][$k]))
        $aTaskData['sites'][$k] = null;
    }
    $oTaskData->content = json_encode($aTaskData);
    $oTaskData->save();

    return $aTaskData;
  }

  public function set_RoomLockSetting($aTaskData) {

    $oTaskData = Settings::findOrCreate('multiple_room_lock', null);
    $oTaskData->content = json_encode($aTaskData);
    $oTaskData->save();
  }

  private function calcRangBkg($room_id) {

    $result = [];
    $aDays = $this->days;
    $lst = Book::where_book_times($this->start, $this->finish)
                    ->where('room_id', $room_id)
                    ->whereIn('type_book', $this->type_book)->get();

    if (count($lst)) {
      foreach ($lst as $b) {
        $lstDays = arrayDays($b->start, $b->finish, 'ymd', 1,false);
        foreach ($lstDays as $d => $v) {
          if (isset($aDays[$d]))
            $aDays[$d] = 1;
        }
        
      }

      $start = null;
      $end = null;
      foreach ($aDays as $k => $v) {
        if ($v == 1) {
          if ($start) {
            $result[] = [$start, $k];
            $start = null;
          }
        } else {
          if (!$start)
            $start = $k;
        }
        $end = $k;
      }
      if ($start) //last element
        $result[] = [$start, $end];
    } else {
      //Not exists RVS
      $start = null;
      $end = null;
      foreach ($aDays as $k => $v) {
        if (!$start)
          $start = $k;
        $end = $k;
      }
      $result[] = [$start, $end];
    }

    //Render days
    foreach ($result as $k=>$v){
      if ($v[0]<$v[1]){
        $result[$k] = [
          $this->renderDate($v[0]),
          $this->renderDate($v[1])
        ];
      } else {
        unset($result[$k]);
      }
    }
    return $result;
  }
  
  private function renderDate($date) {
    return '20'.substr($date,0,2).'-'.substr($date,2,2).'-'.substr($date,4,2);
  }

}

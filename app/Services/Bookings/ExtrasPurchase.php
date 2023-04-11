<?php

namespace App\Services\Bookings;

use App\Rooms;
use App\Book;
use App\Sites;

/**
 * Description of GetBooksLimp
 *
 * @author cremonapg
 */
class ExtrasPurchase {

  public function __construct() {
    
  }

  static function getLink($rva) {


    $link = 'compra-de-extras?tkn=' . encriptID($rva->id) . '-' . getKeyControl($rva->id);

    $siteID = $rva->room->site_id;
    if ($siteID) {
      $oSite = Sites::find($siteID);
      return 'https://' . $oSite->url . '/' . $link;
    }

    return $link;
  }

  function getBookingID($tkn) {

    $aTkn = explode('-', $tkn);
    if (count($aTkn) != 2)
      return -1;

    $bID = desencriptID($aTkn[0]);
    if (!is_numeric($bID))
      return -1;

    if (getKeyControl($bID) != $aTkn[1])
      return -1;

    return $bID;
  }

  function getExtrasData($oBkg, $siteID) {
    //-------------------------------------------------
    $oExtras = \App\ExtraPrices::getDynamicToFront();
    $extrasID = array();
    $pax = $oBkg->pax;
    $nigths = $oBkg->nigths;
    $lstExtrs = [];
    if ($oExtras) {
      foreach ($oExtras as $e) {
        $extrasID[] = $e->id;
        $qty = $nigths;
        $name = $e->name;
        $info = null;
        if ($e->type == 'parking') {
          $info = \App\Settings::getContent('widget_extras_paking', 'es', $siteID);
        }
        if ($e->type == 'breakfast') {
          $qty = $nigths * $pax;
          $info = \App\Settings::getContent('widget_extras_breakfast', 'es', $siteID);
        }
        $pattern = '/<p>(.*)<\/p>/';
        $replacement = '${1}<br>';

        $info = preg_replace($pattern, $replacement, $info);
        $info = strip_tags($info, '<br><b><strong>');
        $lstExtrs[] = [
            'k' => encriptID($e->id),
            'n' => $e->name,
            'q' => $qty,
            'i' => clearTitle($e->name),
            'p' => $e->price,
            'info' => $info
        ];
      }
    }

    //-------------------------------------------------
    $haveExtr = [];
    $extrasAsig = \App\BookExtraPrices::getDynamicWithExtr($oBkg->id);
    foreach ($extrasAsig as $e) {
      if (isset($haveExtr[$e->extra_id])) {
        $haveExtr[$e->extra_id]['q'] += $e->qty;
        $haveExtr[$e->extra_id]['p'] += $e->price;
      } else {
        $haveExtr[$e->id] = ['n' => $e->extra->name, 'q' => $e->qty, 'p' => $e->price];
      }
    }
    $extrasAsig = []; //para no enviar los IDs
    foreach ($haveExtr as $v) {
      $v['p'] = moneda($v['p']);
      $extrasAsig[] = $v;
    }
    //-------------------------------------------------



    return [
        'rvaCli' => $oBkg->customer->name,
        'rvaRoom' => $oBkg->room->name,
        'rvaIn' => dateMin($oBkg->start),
        'rvaOut' => dateMin($oBkg->finish),
        'rvaNight' => $oBkg->nigths,
        'rvaPax' => $oBkg->pax,
        'pax' => $oBkg->pax,
        'start' => $oBkg->start,
        'finish' => $oBkg->finish,
        'lstExtr' => $lstExtrs,
        'lstAssig' => $extrasAsig
    ];
  }

  function getAdminAlerts($noRooms) {
    $today = date('Y-m-d');
    $oBookExtrs = Book::whereIn('type_book', [1, 2, 7, 8, 10])
            ->with('room', 'customer', 'extrasBook')
            ->whereNotIn('room_id', $noRooms)
            ->where('start', '<=', $today)
            ->where('finish', '>=', $today)
            ->get();
    $lstExtrs = [];
    $toDeliver = 0;
    if ($oBookExtrs) {
      //book_data delivered 
      $aDelivered = \App\BookData::where('key', 'extr_delivered')
                      ->whereIn('book_id', $oBookExtrs->pluck('id'))
                      ->where('content', 1)
                      ->pluck('book_id')->toArray();
      $toDeliver = 0;
      foreach ($oBookExtrs as $b) {
        if ($b->extrasBook) {
          $breakfast = 0;
          $excursion = 0;
          $parking = 0;
          foreach ($b->extrasBook as $e) {
            if ($e->deleted == 0) {
              if ($e->type == 'breakfast')
                $breakfast += $e->qty;
              if ($e->type == 'excursion')
                $excursion += $e->qty;
              if ($e->type == 'parking')
                $parking += $e->qty;
            }
          }

          if ($breakfast > 0 || $excursion > 0 || $parking > 0) {
            $delivered = ($aDelivered && in_array($b->id, $aDelivered)) ? 1 : 0;
            if ($delivered == 0)
              $toDeliver++;
            $lstExtrs[] = (object) [
                        'bID' => $b->id,
                        'cli' => $b->customer->name,
                        'phone' => $b->customer->phone,
                        'in' => dateMin($b->start),
                        'out' => dateMin($b->finish),
                        'room' => $b->room->name,
                        'breakfast' => $breakfast,
                        'excursion' => $excursion,
                        'parking' => $parking,
                        'delivered' => $delivered
            ];
          }
        }
      }
    }
    return ['lst'=>$lstExtrs,'toDeliver'=>$toDeliver];
  }

}

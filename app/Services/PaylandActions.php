<?php

namespace App\Services;

use App\Book;
use App\BookData;
use App\ExtraPrices;
use App\BookExtraPrices;
use \App\Traits\BookEmailsStatus;

class PaylandActions {

  use BookEmailsStatus;
  
  function proceessPaymentActions($orderID, $bID) {

    $BookData = BookData::getObjs('payAction' . $orderID, $bID);
    if ($BookData) {
      foreach ($BookData as $obj) {
        $data = json_decode($obj->content);
        switch ($data->acc) {
          case 'buySupl':
            $supl = $data->data;
            if ($supl) {
              $lst = [];
              foreach ($supl as $s){
                $lst[] = $this->addSuplements($s, $bID);
              }
              $this->sendMailSupl($bID,$lst);
            }
            break;
        }
//        
        $obj->key = 'payAction'.$orderID.'process';
        $obj->save();
      }
    }
  }

  function addSuplements($item, $bID) {
    $oExtra = ExtraPrices::getDynamic($item->id);
    $return = '';
    $import = $item->price;
    $qty = $item->qty;
    if ($oExtra->id = $item->id) {
      
      //----------------------------------------------
      if ( $oExtra->type == "breakfast")
        $return = $qty . ' ' . $oExtra->name . '(s) : ' . moneda($import);
      else 
        $return = $qty . ' Tiket(s) de ' . $oExtra->name . ' : ' . moneda($import);
      //----------------------------------------------
      
      $cost = $oExtra->cost * $qty;
      $oBookExtra = new BookExtraPrices();
      $oBookExtra->book_id = $bID;
      $oBookExtra->extra_id = $oExtra->id;
      $oBookExtra->qty = $qty;
      $oBookExtra->price = $import;
      $oBookExtra->cost = $cost;
      $oBookExtra->type = $oExtra->type;
      $oBookExtra->fixed = 0;
      $oBookExtra->deleted = 0;
      $oBookExtra->save();

      $book = Book::find($bID);
      if ($book && $import > 0) {
        $book->total_price += $import;
        $book->real_price += $import;
        $book->cost_total += $cost;
        $book->save();
      }
    }
    
    return $return;
  }
  
  function sendMailSupl($bID,$lst){
    if (count($lst)){
      $oBook = Book::find($bID);
      if ($oBook){
        $data = ['extras_comprados' => '<p>* '.implode('<br/>* ',$lst).'</p>'];
        $this->sendMailGral($oBook,'puncharseSupl','Compra de suplementos',$data);
      }
    }
  }

}

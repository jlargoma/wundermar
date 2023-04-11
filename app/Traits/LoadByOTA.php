<?php

namespace App\Traits;

use App\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Book;
use App\Services\OtaGateway\OtaGateway;
use App\Services\OtaGateway\Config as oConfig;


trait LoadByOTA {

 function loadBooking($oBookings) {
    $oConfig = new oConfig();

    foreach ($oBookings as $oBooking) {
      $channel_group = $oConfig->getChannelByRoom($oBooking->roomtype_id);
      $extra_array = is_string($oBooking->extra_array) ? json_decode($oBooking->extra_array) : $oBooking->extra_array;
      if (isset($extra_array->from_google) && $extra_array->from_google) {
        $oBooking->ota_id = 'google-hotel';
      }
      //BEGIN: si es una cancelación por modificación -> la salto
      if ($oBooking->modified_to) {
        if ($this->otaModified($oBooking,$channel_group))
          continue; //sólo si se actualizó
      }
      //END: si es una cancelación por modificación -> la salto
      $reserv = [
          'channel' => $oBooking->ota_id,
          'bkg_number' => $oBooking->number,
          'rate_id' => $oBooking->plan_id,
          'external_roomId' => $oBooking->roomtype_id,
          'reser_id' => $oBooking->ota_booking_id,
          'comision' => 0,
          'channel_group' => $channel_group,
          'status' => $oBooking->status_id,
          'agency' => $oConfig->getAgency($oBooking->ota_id),
          'customer_name' => $oBooking->name . ' ' . $oBooking->surname,
          'customer_email' => $oBooking->email,
          'customer_phone' => $oBooking->phone,
          'customer_comment' => $oBooking->comment,
          'totalPrice' => $oBooking->amount,
          'adults' => $oBooking->adults,
          'children' => $oBooking->children,
          'extra_array' => $extra_array,
          'start' => $oBooking->arrival,
          'end' => $oBooking->departure,
          'modified_from' => $oBooking->modified_from,
          'modified_to' => $oBooking->modified_to,
          'pax'=>$oBooking->adults+$oBooking->children
      ];
      
      
      
      //PAXs -------------------------------
      $checkPAX = false;
      
      foreach ($reserv['extra_array'] as $k => $v) {
        if ($k == 'Guests'){
          foreach ($v as $k2 => $v2) {
            if ($k2 == 'Requested occupancy'){
              $pax = $adults = $children = 0;
             
              foreach ($v2 as $k3 => $v3) {
                $pax += $v3->count;
                if ($v3->type ==  "child") $children += $v3->count;
                if ($v3->type ==  "adult") $adults += $v3->count;
              }
              if ($reserv['pax'] != $pax && $reserv['status'] == 1){
                if ($oBooking->link_id == "0" || $children == 0){ //el problema son los chicos
                  $reserv['pax'] = $pax;
                  $reserv['adults'] = $adults;
                  $reserv['children'] = $children;
                } else {
//                  echo '"'.$oBooking->number.'", <br>';
                  //es una reserva doble -> control manual
                  $checkPAX = [
                    'bookID'=>null,  
                    'link_id'=>$oBooking->link_id,  
                    'customer'=>$reserv['customer_name'],  
                    'pax'=>$reserv['pax'],  
                    'reser_id' => $oBooking->ota_booking_id,
                    'adults'=>$adults,  
                    'children'=>$children,  
                    'channel_group' => $channel_group,
                  ];
                  
                }
              }
            }
          }
        }
      }
      //PAXs -------------------------------
//      $bookID = null;

      if ($oBooking->modified_from) {
        $alreadyExist = \App\Book::where('bkg_number', $oBooking->number)->first();
        if (!$alreadyExist) { // espero la confirmacion de eliminar
          $oData = \App\ProcessedData::findOrCreate('OTA_rva_save');
          $content = json_decode($oData->content,true);
          if (!$content || !is_array($content)) $content = [];
          $content[] = [$oBooking->number,$channel_group, $reserv];
          $oData->content = json_encode($content);
          $oData->save();

          Mail::send('backend.emails.base', [
            'mailContent' => 'Reserva en espera: modified_from '.$oBooking->modified_from.' bkg number '.$oBooking->number,
            'title'       => 'reserva en espera '.$oBooking->number
          ], function ($message) {
              $message->from(config('mail.from.address'));
              $message->to(config('mail.from.address'));
              $message->cc('pingodevweb@gmail.com');
              $message->subject('reserva en espera');
          });


          continue;
        }
      }

      $bookID = $this->sOta->addBook($channel_group, $reserv);
//      var_dump($reserv,$oBooking);// die;

      if ($bookID && $oBooking->ota_id == 'google-hotel') {

        $book = \App\Book::find($bookID);
        $body = 'Hola, ha entrado una nueva reserva desde Google Hotel:<br/><br/>';

        $customer = $book->customer;
        $subject = 'RESERVA GOOGLEHOTELS : ' . $customer->name;
        $body .= '<b>Nombre:</b>: ' . $customer->name . '<br/><br/>';
        $body .= '<b>e-mail:</b>: ' . $customer->email . '<br/><br/>';
        $body .= '<b>Teléfono:</b>: ' . $customer->phone . '<br/><br/>';
        $body .= '<b>Habitación:</b> ' . $book->room->name . '<br/><br/>';
        $body .= '<b>PVP:</b>: ' . number_format($book->total_price, 0, '', '.') . '<br/><br/>';
        $body .= '<b>Fechas:</b> ' . convertDateToShow_text($book->start) . ' - ' . convertDateToShow_text($book->finish) . '<br/><br/>';
        $body .= '<b>Noches:</b> ' . $book->nigths . '<br/><br/>';
        $body .= '<b>Paxs:</b> ' . $book->pax . '<br/><br/>';
        $body .= '<b>Comtentarios:</b> ' . $book->book_comments . '<br/><br/>';

        $sended = \Illuminate\Support\Facades\Mail::send('backend.emails.base', [
                    'mailContent' => $body,
                    'title' => $subject
                        ], function ($message) use ($book, $subject) {
                          $message->from(config('mail.from.address'));
                          $message->to("reservas@riadpuertasdelalbaicin.com");
                          $message->subject($subject);
                          $message->replyTo(config('mail.from.address'));
                        });
      }
      
      if ($bookID && $checkPAX){
        $checkPAX['bookID'] = $bookID;
        
        $oProcessData = \App\ProcessedData::findOrCreate('checkPaxs');
        $content = json_decode($oProcessData->content,true);
        
        if (is_array($content)){ 
          foreach ($content as $k=>$v){
            if ($v['bookID'] == $bookID){
              unset($content[$k]); //borrar si es actualización
            }
          }
          $content[] = $checkPAX;
        } else $content = [$checkPAX];
        
        $oProcessData->content = json_encode($content);
        $oProcessData->save();
      }
    }
  }
  

  function otaModified($oBooking,$channel_group) {
    $site = '';
    $oBook = \App\Book::where('bkg_number', $oBooking->number)->first();
    if ($oBook) {
      $bkgNumbers = explode(',',$oBooking->modified_to);
      $updated = false;
      $newNumber = '';
      foreach ($bkgNumbers as $number){
        if ($updated) continue; //ya encontró uno
        $oBook2 = \App\Book::where('bkg_number', $number)->first();
        if (!$oBook2){ // aún no se ha cargado
          $updated = true;
          $newNumber = $number;
          $oBook->bkg_number = $number;
          $oBook->save();
        }
      }
      if (!$updated) return false; //no encontró lugar
      
      $bData = \App\BookData::findOrCreate('modified_to', $oBook->id);
      $bData->content .= $oBooking->number.',';
      $bData->save();
      $site = $oBook->room->site_id;

      $resp = \Illuminate\Support\Facades\Mail::send('backend.emails.base-admin', [
        'content' => 'La reserva ' . $oBooking->number . ' / ' . $channel_group .
        ' (site '.$site.' )'.
        ' fue modificada por bkg_number ' . $newNumber,
            ], function ($message) {
              $message->from(config('mail.from.address'));
              $message->to('pingodevweb@gmail.com');
              $message->subject('Actualización de reservas');
            });
    }

    return true; //si no existe, la tomo como modificada
  }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Services\OtaGateway\OtaGateway;
use Illuminate\Support\Facades\Mail;
use App\Book;

class CheckBookingsCheckin extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'OTAs:checkBookingsCheckin';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check Bookings numbers on checkin';

  /**
   * The console command result.
   *
   * @var string
   */
  var $result = array();
  var $resultIDs = array();
  var $from;
  var $to;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    $this->result = array();
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    $this->from = date('Y-m-d');
    foreach (\App\Sites::siteIDs() as $sID){
      $this->check_booking_otaGateway($sID);
    }
    $this->sendMessage();
  }

  private function check_booking_otaGateway($siteID) {
    $OtaGateway = new OtaGateway();
    if (!$OtaGateway->conect($siteID)) return;
    $items = $OtaGateway->getBookingsCheckin($this->from);
    if ($items && is_array($items)){
        $numbers = $ota_b_id = [];
        foreach ($items as $item){
          if ($item){
            foreach ($item as $reserv){
                if ($reserv->modified_to)  continue;
                $numbers[] = $reserv->number;
                $ota_b_id[] = $reserv->ota_booking_id;
            }
          }
        }

      $this->controlBkgNumber($numbers,$siteID);
      $this->controlIdNumber($ota_b_id);
    }
    $OtaGateway->disconect($siteID);
  }

  function controlBkgNumber($aControl,$siteID) {
    $exist = Book::whereIn('bkg_number', $aControl)->pluck('bkg_number')->toArray();
    $resultado = array_diff($aControl, $exist);
    if (count($resultado) > 0) {
      $this->result[$siteID] = [];
      foreach ($resultado as $v) {
        if ($v)  $this->result[$siteID][] = $v;
      }
    }
  }

  function controlIdNumber($aControl) {
    $exist = Book::whereIn('external_id', $aControl)->pluck('external_id')->toArray();
    $resultado = array_diff($aControl, $exist);
    if (count($resultado) > 0) {
      foreach ($resultado as $v) {
        if ($v)  $this->resultIDs[] = $v;
      }
    }
  }

  private function sendMessage() {
    $subject = 'Atención: Control Semanal de Reservas OTAs';
    
    $mailContent = '<h3>Las siguientes reservas deben controlarse:</h3>';
    $mailContent .= '<p>Tienen checking próximos</p>';
    $send = false;
    if (count($this->result) > 0) {
      $send = true;
      foreach ($this->result as $site=>$v) {
      $mailContent .= '<div><h4>Booking Numbers('.$site.'):</h4>'.implode(',',$v).'</div>';
      }
    }
    if (count($this->resultIDs) > 0) {
      $send = true;
      $mailContent .= '<div><h4>Booking ID:</h4>'.implode(',',$this->resultIDs).'</div>';
    }
    
    if ($send)
      Mail::send('backend.emails.base', [
            'mailContent' => $mailContent,
            'title'       => $subject
        ], function ($message) use ($subject) {
            $message->from(config('mail.from.address'));
            $message->to(config('mail.from.address'));
            $message->cc('pingodevweb@gmail.com');
            $message->subject($subject);
        });
  
  }
    
}

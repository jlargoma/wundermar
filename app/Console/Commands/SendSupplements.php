<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Book;
use App\Rooms;
use App\Traits\BookEmailsStatus;
use App\Services\LogsService;

class SendSupplements extends Command {

  use BookEmailsStatus;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'Supplements:sendBuy';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send link to add new supplements';
  private $sLog;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    return;
    $this->sLog = new LogsService('schedule', 'Supplements');

    try {

      foreach (\App\Sites::siteIDs() as $sID){
        $this->process($sID);
      }
    } catch (Exception $ex) {
      $this->sLog->error('error en Supplements');
    }
  }

  private function process($siteID) {

    $today = date('Y-m-d', strtotime('+1 Days '));
    $rooms = Rooms::getRoomsBySite($siteID)->toArray();
    $roomIDs = array_keys($rooms);

    $books = Book::whereIn('type_book', [1, 2])
            ->whereIn('room_id', $roomIDs)
            ->where('start', '=', $today)
            ->get();
    
    if ($books) {
      //-----------------------------------------------------------//
      $aSend = $aNoSend = [];
      foreach ($books as $b) {
        $sended = $this->sendMailGral($b,
                'book_email_supplements',
                'venta Tickets descuento Parking'
                );
        if ($sended) {
          $aSend[] = $b->id;
        } else {
          $aNoSend[] = $b->id;
        }
      }
      if (count($aSend))
        $this->sLog->info('Suplementos enviados ' . implode(',', $aSend));
      if (count($aNoSend))
        $this->sLog->info('Suplementos NO enviados ' . implode(',', $aNoSend));
    } else
      $this->sLog->info('Suplementos: No hay reservas ' . $today);
  }

}

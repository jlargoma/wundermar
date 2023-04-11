<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Services\LogsService;
use App\Services\OtaGateway\OtaGateway;
use Illuminate\Support\Facades\Mail;
use App\Book;

class CheckOtaRrvs extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'OTAs:CheckOtaRrvs';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check if existe bookings to save';

  var $sLog = null;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    $this->sLog = new LogsService('OTAs','testing');
    $this->result = array();
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
   $OtaGateway = new OtaGateway();

      $oData = \App\ProcessedData::findOrCreate('OTA_rva_save');
      $content = json_decode($oData->content,true);
      if ($content && is_array($content)){
        foreach($content as $k=>$v){
          $alreadyExist = Book::where('bkg_number', $v[0])->first();
          if ($alreadyExist){
            $bookID = $OtaGateway->addBook($v[1], $v[2]);
            if ($bookID){
              $this->sLog->info('Add Booking '.$bookID.' '.$v[0]);
              unset($content[$k]);
            } else {
              $this->sLog->warning('Booking no agregado '.$bookID.' '.$v[0]);
            }
          } else {
            $this->sLog->warning('Booking aun no actualizado '.$v[0]);
          }
        }
      }
      $oData->content = json_encode($content);
      $oData->save();
  }

}

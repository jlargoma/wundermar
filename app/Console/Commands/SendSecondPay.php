<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Book;
use App\Traits\BookEmailsStatus;
use Illuminate\Support\Facades\DB;
use App\Settings;
use Carbon\Carbon;
use App\Services\LogsService;

class SendSecondPay extends Command {

  use BookEmailsStatus;
  private $sLog;
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'secondPay:sendEmails';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check books checkin into 15 days and send emials';
  
  private $message;

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
    $this->sLog = new LogsService('schedule','SegundoPago');
    $payment_rule = Settings::where('key', 'payment_rule')->get();
    
    if ($payment_rule){
      foreach ($payment_rule as $pr){
        $site_id = $pr->site_id;
        $rule = json_decode($pr->content);
        if ($rule && isset($rule->days)){
          if (!($rule->days>0)) continue;
          $roomsID = \App\Rooms::where('site_id',$site_id)->pluck('id')->all();
          if (count($roomsID)<1) continue;
          $oSite = \App\Sites::find($site_id);
          
          $this->checkSecondPay($roomsID,$rule->days,$oSite->name);
          
        }
      }
    }
    
//    
  }

  /**
   * Check the books into 15 (or settings) days
   */
  public function checkSecondPay($roomsIDs,$daysToCheck,$siteName) {
    
    
    $today = Carbon::now();
    $enddate = $today->copy()->addDays($daysToCheck);
    $books = Book::where('start', '>=', $today)
            ->where('start', '<=', $enddate)
            ->where('type_book', 2)
            ->whereNotIn('agency', [1,4])
            ->whereIn('room_id', $roomsIDs)
            ->where('send', 0)
            ->orderBy('created_at', 'DESC')->get();
    if (count($books)>0){
      foreach ($books as $book){
        if (!empty($book->customer->email)){
          // check the pending amount
          $totalPayment = 0;
          $payments = \App\Payments::where('book_id', $book->id)->get();
          if ($payments){
            foreach ($payments as $key => $pay)
            {
                $totalPayment += $pay->import;
            }
          }
          $pending = ($book->total_price - $totalPayment);
          
//          echo $subject . $book->customer->name.', checkin: '.$book->start.', pendiente: '.$pending."\n";
//          continue;
          
          if ($pending>0){
            $subject = translateSubject('Recordatorio Pago',$book->customer->country).' '.$siteName.' ';
            $this->sendEmail_secondPayBook($book,$subject .' '. $book->customer->name);
            $this->sLog->info("Recordatorio enviado a: ".$book->id);
          }
          $book->send = 1;
          $book->save();
        } else {
          $this->sLog->warning("La reserva no posee mail: ".$book->id);
        }
      }
    } else {
      $this->sLog->warning("No hay reservas para $siteName entre los días $today al $enddate");
    }
  }
  
 

}

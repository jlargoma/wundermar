<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Book;
use App\BookPartee;
use App\Traits\BookEmailsStatus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Settings;
use Carbon\Carbon;
use App\Services\LogsService;

class CancelCustomerBlockBooks extends Command {

  use BookEmailsStatus;
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'CancelCustomerBlocks:sendExpiredPayment';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check last apyment  and send email to user';
  
  private $message;
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
    $this->sLog = new LogsService('schedule','Checkin');
    $this->checkExpiredPayments();
  }

  
  /**
   * Check the Partee HUESPEDES completed
   */
  public function checkExpiredPayments() {
    $yesterday = date('Y-m-d', strtotime('-1 days'));
    $books = book::select('book.*')->where('type_book',1)->whereNotIn('agency',[1,4])
    ->join('book_orders','book_orders.book_id','=','book.id')->where('book_orders.created_at','<=',$yesterday)->where('book_orders.paid')
    ->get();
    $subject = 'Su bloqueo ha caducado al no recibir tu confirmación de pago';


    $send = [];

    if ($books){
    foreach ($books as $book) {
        try {
          if( !in_array($book->id,$send)){
            $send[] = $book->id;
            $book->type_book = 98;
            $book->save();
            $message = $this->sendEmail_blockCancel($book,$subject);
          }

            


        } catch (\Exception $e) {
          Log::error("Error CheckIn " . $book->id . ". Error  message => " . $e->getMessage());
          echo $e->getMessage();
          continue;
        }
      }
   
    }
  }
}

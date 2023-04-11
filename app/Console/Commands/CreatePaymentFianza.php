<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\Mail;
use App\MailsLogs;
use App\Book;
use Carbon\Carbon;
use App\Traits\BookEmailsStatus;
use App\PaymentOrders;


class CreatePaymentFianza extends Command {
  
  use BookEmailsStatus;
  
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'fianzas:crearOrder';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a deferred order to Fianzas';

  private $urlPay;
  private $amount;
  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();
    $this->amount = 300;
    $this->urlPay = getUrlToPay('|token|');
   
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
   
    $daysToCheck = Carbon::now();
    //Book without airbnb Agency
    
//    $books = Book::where_type_book_sales()
//             ->where('start', $daysToCheck->toDateString())
//            ->orderBy('created_at', 'DESC')->get();
    $books = Book::where('id', -999)
            ->where('agency', '!=',4)
            ->orderBy('created_at', 'DESC')->get();
     
    $site = config('app.name');
    if ($books) {
      
      foreach ($books as $book){
        $hasFianza = PaymentOrders::where('book_id',$book->id)->where('is_deferred',1)->first();
        if ($hasFianza){
          continue;
        }
        $urlPayment = $this->getPayment($book);
        if ($urlPayment){
         $this->sendEmail_FianzaPayment($book,$this->amount,$urlPayment);
        }
      }
    }
  }
  
  private function getPayment($book){
    
    if ($book){
      $token = encrypt($book->id.'&fianzas');
      
      $urlPay = str_replace('|token|', $token, $this->urlPay);
      $client = $book->customer()->first();
      $description = "FIANZA CLIENTE " . $client->name;
      $client_email = 'no_email';
      if ($client && trim($client->email)){
        $client_email = $client->email;
      }

      $PaymentOrders = new PaymentOrders();
      $PaymentOrders->book_id = $book->id;
      $PaymentOrders->cli_id = $client->id;
      $PaymentOrders->cli_email = $client_email;
      $PaymentOrders->amount = $this->amount;
      $PaymentOrders->status = 0;
      $PaymentOrders->token = $token;
      $PaymentOrders->description = $description;
      $PaymentOrders->is_deferred = true;
      $PaymentOrders->save();
      return $urlPay;
    }
    return null;
  }
}

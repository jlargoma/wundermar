<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\Mail;
use App\MailsLogs;
use App\Book;
use Carbon\Carbon;
use App\Models\Forfaits\ForfaitsOrders;
use App\Models\Forfaits\ForfaitsOrderPayments;
use App\Traits\BookEmailsStatus;


class forfaitPaymentReminder extends Command {
  
  use BookEmailsStatus;
  
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'forfait:sendReminder';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send mails to payment reminder the forfait order';

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
   
    $daysToCheck = Carbon::now()->addDays(7);
    
    $books = Book::where_type_book_sales()
             ->where('start', $daysToCheck->toDateString())
            ->orderBy('created_at', 'DESC')->get();
     
    $site = config('app.name');
    if ($books) {
      
      foreach ($books as $book){
        
        $order = ForfaitsOrders::where('book_id',$book->id)->first();
        if ($order){
          $totalPrice = $order->total;
          $totalPayment =  ForfaitsOrderPayments::where('order_id', $order->id)->where('paid',1)->sum('amount');
          if ($totalPayment>0){
            $totalPayment = $totalPayment/100;
          }
          $totalToPay = $totalPrice - $totalPayment;
          
          if ($totalToPay>0){
            $link = config('app.forfait.page').encriptID($book->id).'-'.encriptID($book->customer->id);
            $subject = translateSubject('Recordatorio Pago',$book->customer->country).' '.$site.' ';
            $this->sendEmail_RemindForfaitPayment($book,$order->id,$link);
            
          }
        }
            
      }
    }
  }
}

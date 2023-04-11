<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Book;
use App\BookPartee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Settings;
use Carbon\Carbon;

class SendParteeAdmin extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'partee:sendAlert';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check partee checkin to finish and send email to admin';
  
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
    if(config('app.partee.disable') == 1) return;
    $this->checkInStatus();
  }

  /**
   * Check the Partee HUESPEDES completed
   */
  public function checkInStatus() {
    
      $now         = Carbon::now();
      $qry = 'SELECT book_partees.status,book_partees.partee_id,guestNumber,book.start,book.id as bookID'
              . ' FROM book INNER JOIN book_partees '
              . ' ON book.id = book_partees.book_id '
              . ' WHERE  book.type_book = 2 '
              . ' AND book_partees.status != "FINALIZADO" '
              . ' AND DATEDIFF(book.start,"'.$now->format('Y-m-d').'") = -1';
      $listBookPartee = DB::select($qry);

      $link = '<a href="'. url('/').'/admin/reservas/update/%d" title="Ir a la reserva">Ir a la reserva</a>';
      $listToSend = [];
      if ($listBookPartee){
      foreach ($listBookPartee as $item) {
          $status = $item->status;
          $book = Book::find($item->bookID);
          if ($book){
            if ($status == 'HUESPEDES'){
              if ($item->guestNumber == $book->tax){
                $text = 'Número incompleto de Huespedes';
              } else {
                $text = 'Partee completo';
              }
            } else {
              $text = 'Partee no cargado';
            }
          }
          $customer = $book->customer;
          if ($customer){
            $listToSend[] = '<b>'.$customer->name.':</b> '.$text.'  '. str_replace('%d',$item->bookID,$link);
          }
      }
    } 
    
    if (count($listToSend)>0)
      $this->sendMessage($listToSend);
  }
  
  private function sendMessage($items) {
    $subject = 'Atención: enviar Partee a la policía';
    
    $mailContent = '<h3>Las siguientes reservas deben enviarse a la polícia hoy:</h3>';
           
    if (count($items)>0)
      $mailContent .= implode('<br>', $items);
    else 
      $mailContent .= 'Parece que no tienes ninguna reserva para enviar el checkin hoy';
      
    
    Mail::send('backend.emails.base', [
            'mailContent' => $mailContent,
            'title'       => $subject
        ], function ($message) use ($subject) {
            $message->from(config('mail.from.address'));
            $message->to(config('mail.from.address'));
            $message->subject($subject);
        });
  
  }
  
}

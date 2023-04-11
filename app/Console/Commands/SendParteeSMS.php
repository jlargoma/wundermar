<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Book;
use App\BookPartee;
use App\Traits\BookEmailsStatus;
use App\Services\SMSService;
use Illuminate\Support\Facades\DB;
use App\Settings;

class SendParteeSMS extends Command {

  use BookEmailsStatus;
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'partee:sendSMS';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check partee checkin status VACIO and send SMS with link';
  
  private $SMSService;
  private $phone;
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
    $this->SMSService = new SMSService();
    if ($this->SMSService->conect()){
      $this->checkInStatus();
    } else {
      $log = time() . '- Send SMS -' . $this->SMSService->response;
      Log::error($log);
      echo $log;
    }
  }

  /**
   * Check the Partee HUESPEDES completed
   */
  public function checkInStatus() {
    $apiPartee = new \App\Services\ParteeService();
    //conect to Partee and get the JWT
    if ($apiPartee->conect()) {

      $daysLimit = intval(Settings::getKeyValue('send_sms_days'));
      
      if ($daysLimit<1){
        return FALSE;
      }
      $qry = 'SELECT book_partees.id,book_partees.partee_id FROM book
                INNER JOIN book_partees
                ON book.id = book_partees.book_id
                WHERE DATEDIFF(book.start,CURDATE()) < ?
                AND book_partees.partee_id>0
                AND book_partees.status = "VACIO"
                AND book_partees.sentSMS IS NULL';
      
      $listBookPartee = DB::select($qry, [$daysLimit]);

      foreach ($listBookPartee as $item) {
        //Read a $BookPartee            
        try {

          $partee_id = $item->partee_id;
          //check Partee status
          $result = $apiPartee->getCheckStatus($partee_id);
          
          if ($result) {
            $BookPartee = BookPartee::find($item->id);
            
            if ($apiPartee->response->status == 'VACIO'){
              $this->phone = null;
              $this->message = null;
              if ($this->prepareMessage($BookPartee->book_id, $BookPartee->link)){
                if ($this->sendSMS()){
                  $BookPartee->sentSMS=1;
                  $log = $BookPartee->log_data . "," . time() . '-' .'sentSMS';
                  $BookPartee->log_data = $log;
                  $BookPartee->save();
                }
              }
              
            } else {
              //Save the new status
              $log = $BookPartee->log_data . "," . time() . '-' . $apiPartee->response->status;
              $BookPartee->status = $apiPartee->response->status;
              $BookPartee->log_data = $log;
              $BookPartee->guestNumber = $apiPartee->response->guestNumber;
              $BookPartee->save();
            }

          } else {
            
            $log = time() . '- Send SMS -' . $apiPartee->response;
            Log::error($log);
          }
        } catch (\Exception $e) {
          Log::error("Error CheckIn Partee " . $item->id . ". Error  message => " . $e->getMessage());
          echo $e->getMessage();
          continue;
        }
      }
    } else {
      //Can't conect to partee
      Log::error("Error Conect Partee " . $apiPartee->response);
      echo $apiPartee->response;
    }
  }
  
  private function prepareMessage($bookID,$link) {
    //Get Book object
    $book = Book::find($bookID);
    if (!$book){
      return false;
    }
    //Get msg content
    $content = $this->getMailData($book,'SMS_Partee_upload_dni');
    $link = get_shortlink($link);
    $content = str_replace('{partee}', $link, $content);
    $content = $this->clearVars($content);
    $content = strip_tags($content);
    $this->phone = $book->customer['phone'];
    $this->message = $content;
    return true;
  
  }
  
  private function sendSMS() {
    if ($this->phone && $this->message){
      if ($this->SMSService->sendSMS($this->message,$this->phone)){
        $log = 'Sent SMS -' . $this->SMSService->response;
        Log::error($log);
        return true;
      } else {
        $log = 'Error Sent SMS -' . $this->SMSService->response;
        Log::error($log);
        return true;
      }
      
    }
  }

}

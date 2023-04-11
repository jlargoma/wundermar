<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Book;
use App\BookPartee;
use App\Sites;
use App\Traits\BookEmailsStatus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Settings;
use Carbon\Carbon;
use App\Services\LogsService;

class SendParteeReminder extends Command {

  use BookEmailsStatus;
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'partee:sendReminder';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check partee checkin to finish and send email reminder to user';
  
  private $message;
  private $email;
  private $apiPartee;
  private $sLog;
  private $sS_urls;

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
    $this->sLog = new LogsService('schedule','Partee');
    $this->sS_urls = new \App\Services\ShortUrlService();
    if(config('app.partee.disable') == 1){
      $this->sLog->warning('partee.disable');
      return;
    }
    $this->checkInStatus();
  }

  
  /**
   * Check the Partee HUESPEDES completed
   */
  public function checkInStatus() {
    $this->apiPartee = new \App\Services\ParteeService();
    //conect to Partee and get the JWT
    if ($this->apiPartee->conect()) {
      
      $this->createPartee();

      $daysLimit = intval(Settings::getKeyValue('send_sms_days'));
      
      if ($daysLimit<1){
        return FALSE;
      }
      $qry = 'SELECT book_partees.id,book_partees.partee_id FROM book
                INNER JOIN book_partees
                ON book.id = book_partees.book_id
                WHERE ( DATEDIFF(book.start,CURDATE()) <= ?
                OR book.start = CURDATE() )
                AND book.start >= CURDATE()
                AND book_partees.partee_id>0
                AND book_partees.status = "VACIO"';
      
      $listBookPartee = DB::select($qry, [$daysLimit]);
      foreach ($listBookPartee as $item) {
        //Read a $BookPartee            
        try {

          $partee_id = $item->partee_id;
          //check Partee status
          $result = $this->apiPartee->getCheckStatus($partee_id);
          if ($result) {
            $BookPartee = BookPartee::find($item->id);
            $this->sLog->info('Partee '.$item->id.'-'.$BookPartee->book_id.' Estado: '.$this->apiPartee->response->status);
            if ($this->apiPartee->response->status == 'VACIO'){
              $this->email = null;
              $this->message = null;
//              $link = '<a href="'.$BookPartee->link.'" title="Ir a Partee">'.$BookPartee->link.'</a>';
              if ($this->sendMail($BookPartee->book_id, $BookPartee->link)){
                  $log = $BookPartee->log_data . "," . time() . '-' .'sentReminder';
                  $BookPartee->log_data = $log;
                  $BookPartee->save();
              }
              
            } else {
              //Save the new status
              $log = $BookPartee->log_data . "," . time() . '-' . $this->apiPartee->response->status;
              $BookPartee->status = $this->apiPartee->response->status;
              $BookPartee->log_data = $log;
              $BookPartee->guestNumber = $this->apiPartee->response->guestNumber;
              $BookPartee->save();
            }

          } 
        } catch (\Exception $e) {
          Log::error("Error CheckIn Partee " . $item->id . ". Error  message => " . $e->getMessage());
          echo $e->getMessage();
          continue;
        }
      }
    } else {
      //Can't conect to partee
      $this->sLog->warning("Error Conect Partee " . $this->apiPartee->response);
//      Log::error("Error Conect Partee " . $this->apiPartee->response);
      echo $this->apiPartee->response;
    }
  }
  
  /**
   * 
   */
  private function sendMail($bookID,$link) {
     //Get Book object
    $book = Book::find($bookID);
    if (!$book){
      return false;
    }
    
    $parteeRemenber = $book->getMetaContent('parteeRemenber');
    if ($parteeRemenber){
      $this->sLog->warning("Ya fue enviado el recordatorio a " . $bookID);
      return false;
    }
    
    if (!($book->customer->email && trim($book->customer->email)!='') && trim($book->customer->email)!='--' ){
      $this->sLog->warning("La reserva no tiene mail " . $bookID);
      return false;
    }
    
    
    //Get msg content
    $link = $this->sS_urls->create($link);
    $link = '<a href="'.$link.'" title="Ir a Partee">'.$link.'</a>';
    $subject = translateSubject('Recordatorio envío datos Policía Nacional',$book->customer->country);
    $mailClientContent = $this->getMailData($book,'SMS_Partee_msg');
    $mailClientContent = str_replace('{partee}', $link, $mailClientContent);
    $mailClientContent = nl2br($mailClientContent);
    $mailClientContent = $this->clearVars($mailClientContent);
    
    $site = Sites::siteData($book->room->site_id);
    
    $this->sendMailSite($site,$mailClientContent,$subject,$book->customer->email,$book->agency);
    $book->setMetaContent('parteeRemenber', $book->customer->email);
    \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'SMS_Partee_msg',$subject,$mailClientContent);
            
        return true;
  }

  /**
   * 
   */
  function sendPartee($book){
    $BookPartee = BookPartee::where('book_id', $book->id)->first();
    if ($BookPartee) {
      if ($BookPartee->partee_id > 0) {
        return FALSE;
      }
    } else {
      $BookPartee = new BookPartee();
      $BookPartee->book_id = $book->id;
    }
    //Create Partee
    /**************************************/
    $this->apiPartee->setID(Settings::getParteeBySite($book->room->site_id));
    $result = $this->apiPartee->getCheckinLink($book->customer->email, strtotime($book->start));
    if ($result) {
      $BookPartee->link = $this->apiPartee->response->checkInOnlineURL;
      $BookPartee->partee_id = $this->apiPartee->response->id;
      $BookPartee->status = 'VACIO';
      $BookPartee->log_data = $BookPartee->log_data . "," . time() . '- Sent';
      $BookPartee->save();
    } else {
      $BookPartee->status = 'error';
      $BookPartee->log_data = $BookPartee->log_data . "," . time() . '-' . $this->apiPartee->response;
      $BookPartee->save();
    }
    
    $this->sLog->info('Partee creado: '.$book->id.' - partee: '.$BookPartee->id);
  }
  
  /**
   * 
   */
  function createPartee(){
    
//    1 => 'Reservado - stripe',
//    2 => 'Pagada-la-señal',
//    11 => 'blocked-ical',
            
    $oBooks = \App\Book::whereIn('type_book', [1,2,11])
            ->where('start', '>=', date('Y-m-d'))
            ->where('start', '<=', date('Y-m-d', strtotime('+2 days')))
            ->get();
    if (count($oBooks)>0){
      foreach ($oBooks as $b){
        $this->sendPartee($b);
      }
    } else {
      $this->sLog->info('SafeBox: No hay reservas entre '.date('Y-m-d').' y '.date('Y-m-d', strtotime('+2 days')));
    }
  }
}

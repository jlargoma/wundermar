<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Book;
use App\Traits\BookEmailsStatus;
use Illuminate\Support\Facades\DB;
use App\Settings;
use App\BookData;
use App\Rooms;
use Carbon\Carbon;

class SendPoll extends Command {

  use BookEmailsStatus;
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'SendPoll:sendEmails';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Enviar encuesta automáticamente el dia de check out a las 12am';
  
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

    foreach (\App\Sites::siteIDs() as $sID){
      $this->checkCheckoutsAndSend($sID);
    }
      
  }

  /**
   * Check the books into 15 (or settings) days
   */
  public function checkCheckoutsAndSend($siteID) {

    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-2 days'));
  
    $roomsIDs = \App\Rooms::where('site_id',$siteID)->pluck('id')->toArray();
    $books = Book::where('finish', '<=', $today)
            ->where('finish', '>', $yesterday)
            ->where('type_book', 2)
            ->whereIn('room_id', $roomsIDs)
            ->whereNotIn('agency', [1,4,6])
            ->orderBy('created_at', 'DESC')->get();
    if ($books){
      $bList = [];
      foreach ($books as $book){
        $bList[] = $book->id;
      }
      
      if (count($bList) == 0){
        return null;
      }
      
      $sent = BookData::whereIn('book_id',$bList)
              ->where('key','sent_poll')->pluck('book_id')->toArray();
      
      foreach ($books as $book){
        if (!empty($book->customer->email)){
          if (!in_array($book->id, $sent)){
            
            if ($this->sendEmail_Encuesta($book,"DANOS 5' Y TE INVITAMOS A DESAYUNAR")){
              $save = new BookData();
              $save->book_id = $book->id;
              $save->key = 'sent_poll';
              $save->content = date('Y-m-d H:i:s').' - '.$book->customer->email;
              $save->save();
            }
          }
        }
      }
    }
  }
  
 

}

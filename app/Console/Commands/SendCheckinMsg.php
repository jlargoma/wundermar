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

class SendCheckinMsg extends Command {

  use BookEmailsStatus;
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'checking:sendMsg';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check Checkin and send email to user';
  
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
    $this->checkInStatus();
  }

  
  /**
   * Check the Partee HUESPEDES completed
   */
  public function checkInStatus() {
    $tomorrow = date('Y-m-d', strtotime('+1 days'));
    $books = book::where_type_book_sales()->where('start',$tomorrow)->get();
    $subject = 'CONTACTO PARA REALIZAR EL CHECK IN';
    if ($books){
    foreach ($books as $book) {
        try {
          $message = $this->sendEmail_contactCheckin($book,$subject);
        } catch (\Exception $e) {
          Log::error("Error CheckIn " . $book->id . ". Error  message => " . $e->getMessage());
          echo $e->getMessage();
          continue;
        }
      }
   
    }
  }
}

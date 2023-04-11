<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Book;
use App\Rooms;
use App\SafetyBox;
use App\BookSafetyBox;
use App\Traits\BookEmailsStatus;

class SafeBoxUnassing extends Command {

  use BookEmailsStatus;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'SafeBoxUnassing:unasign';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Un-Asign the SafeBox to checking at 6am';
  private $oSafetyBox;

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
    $this->oSafetyBox = new SafetyBox();
    $this->unassing();
  }

  private function unassing() {

    $today = date('Y-m-d', strtotime('-1 days'));
    $booksIDs = Book::whereIn('type_book', [1, 2])
            ->where('start', '=', $today)
            ->pluck('id');
    
    if ($booksIDs) {
      $this->oSafetyBox->unassingBookSafetyBox($booksIDs);
    }
  }

}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\BookDay;
use App\Years;
use App\Services\LogsService;
///admin/Wubook/Availables?detail=1
class BookingsDays extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'BookingsDays:load';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = '';

  private $sLog;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    $this->sLog = new LogsService('schedule','BookingsDays');
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    $cYear = date('Y');
//    $oYear = Years::where('year', $cYear-2)->first();
//    if ($oYear){
//      $error =BookDay::createSeasson($oYear->start_date,$oYear->end_date);
//      if (count($error)>0) $this->sLog->error(implode(',', $error));
//    }
    $oYear = Years::where('year', $cYear-1)->first();
    if ($oYear){
      $error =BookDay::createSeasson($oYear->start_date,$oYear->end_date);
      if (count($error)>0) $this->sLog->error(implode(',', $error));
    }
    $oYear = Years::where('year', $cYear)->first();
    if ($oYear){
      $error =BookDay::createSeasson($oYear->start_date,$oYear->end_date);
      if (count($error)>0) $this->sLog->error(implode(',', $error));
    }
    $oYear = Years::where('year', $cYear+1)->first();
    if ($oYear){
      $error =BookDay::createSeasson($oYear->start_date,$oYear->end_date);
      if (count($error)>0) $this->sLog->error(implode(',', $error));
    }
  }

 
}

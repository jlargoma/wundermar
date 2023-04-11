<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Expenses;
use Illuminate\Support\Facades\DB;
use App\Settings;
use App\Book;
use App\Rooms;

class CreateMonthAgency extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'monthAgency:create';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create the monthly expense for Airbnb Agency';

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
   /* for($i=2;$i<12;$i++){
      $date = strtotime("-$i months");
      foreach (\App\Sites::siteIDs() as $sID){
        $this->create($date,$sID);
      }
    }*/
    $date = strtotime("-1 months");
    foreach (\App\Sites::siteIDs() as $sID){
      $this->create($date,$sID);
    }
  }
  
  public function create($date,$site_id) {
    
//    $date = strtotime('-1 months');
    $dateStart = date('Y-m-01', $date);
    $dateEnd = date('Y-m-t', $date);
    //var_dump($dateStart,$dateEnd);
    $roomsApto = Rooms::where('site_id',$site_id)->get()->pluck('id');
    $cost = \App\Book::whereIn('room_id',$roomsApto)
            ->where('start', '>=', $dateStart)
            ->where('start', '<=', $dateEnd)
            ->where('agency', 4)
            ->sum('PVPAgencia');
    if ($cost > 0) {
      $item = \App\Expenses::where('date', '=', $dateEnd)
              ->where('type', 'agencias')
              ->where('site_id', $site_id)
              ->where('concept', 'AIRBNB MENSUAL')
              ->first();
      if (!$item) {
        $monthItem = new \App\Expenses();
        $monthItem->type = 'agencias';
        $monthItem->concept = 'AIRBNB MENSUAL';
        $monthItem->comment = 'AIRBNB MENSUAL';
        $monthItem->date = $dateEnd;
        $monthItem->site_id = $site_id;
        $monthItem->import = $cost;
        $monthItem->save();
      }
    }
  }

}

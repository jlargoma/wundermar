<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Expenses;
use Illuminate\Support\Facades\DB;
use App\Settings;

class CreateMonthLimpieza extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'monthLimpieza:create';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create the monthly expense for Limpieza';

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
    
      $d = new \DateTime('first day of this month');
      $date = $d->format('Y-m-d');
       
      $cost = 0;
      $extraUpdate = \App\Extras::first();
      if ($extraUpdate){
        $cost = $extraUpdate->cost;
      }
                
      $item  = \App\Expenses::where('date', '=', $date)
                      ->where('type','LIMPIEZA')
                      ->where('concept','LIMPIEZA MENSUAL')
                      ->first();
      if (!$item){
        $monthItem = new \App\Expenses();
        $monthItem->type = 'LIMPIEZA';
        $monthItem->concept = 'LIMPIEZA MENSUAL';
        $monthItem->comment = 'LIMPIEZA MENSUAL';
        $monthItem->date = $date; 
        $monthItem->import = $cost;
        $monthItem->save();
      }
  }

}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Settings;

class GetDailyFFSeason extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'FFSeasson:get';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Get and save the Season of Forfaits';

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
    
    $skiResortId = config('app.forfait.id');
    $curl = curl_init();
    $endpoint = config('app.forfait.endpoint') . 'getseasons';
    $Bearer = config('app.forfait.token');
    if (!$Bearer) return ;
    curl_setopt_array($curl, array(
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer $Bearer"
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if (!$err) {
      $list = json_decode($response);

      if (isset($list->success) && $list->success) {
        foreach ($list->data->seasons as $lst) {
          if ($lst->skiResortId == $skiResortId) {
            $start = explode('/', $lst->startDate);
            $end = explode('/', $lst->endDate);
            
            $dates = [
                'startDate' => $start[2] . '-' . $start[1] . '-' . $start[0],
                'endDate' => $end[2] . '-' . $end[1] . '-' . $end[0],
            ];
            $Settings = Settings::where('key','FORFAIT_SEASONS')->first();
            if (!$Settings){
              $Settings = new Settings();
              $Settings->key = 'FORFAIT_SEASONS';
              $Settings->name = 'FORFAIT SEASONS';
            }
            $Settings->value = json_encode($dates);
            $Settings->save();
            return;
          }
        }
      }
    }
  }

}

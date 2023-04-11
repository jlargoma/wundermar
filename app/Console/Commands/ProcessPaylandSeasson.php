<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\PaylandService;
use App\PaylandsSummary;


class ProcessPaylandSeasson extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'PaylandSeasson:process';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check payland seasson';

  /**
   * The console command result.
   *
   * @var string
   */
  var $result = array();

  
  private $paylandClient;
  const SANDBOX_ENV = "/sandbox";
    
  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    $this->result = array();
    $endpoint = config('app.payland.endpoint');
    $endPoint = (config('app.payland.enviromment') == "dev") ? $endpoint . self::SANDBOX_ENV : $endpoint;
    $paylandConfig = [
        'endpoint'  => $endPoint,
        'api_key'   => config('app.payland.key'),
        'signarute' => config('app.payland.signature'),
        'service'   => config('app.payland.service')
    ];
    $this->paylandClient    = new PaylandService($paylandConfig);
      
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    
    
    $start  = date('Ymd', strtotime('first day of this month')).'0000';
    $finish = date('Ymd').'0000';
    $filter = date('Y-m');
    $this->process($start,$finish,$filter);
    
    
    
//    $year = \App\Years::where('active', 1)->first();
//    $startYear = new Carbon($year->start_date);
//    $endYear   = new Carbon($year->end_date);
//    $today = date('Y-m-d');
//    while ($startYear<$endYear && $startYear<$today){
//     $filter = $startYear->format('Y-m');
//     $s =  $startYear->format('Ymd').'0000';
//     $startYear->addMonth();
//     $f =  $startYear->format('Ymd').'0000';
//     $this->process($s,$f,$filter);
//    }
  }

  private function process($start,$end,$filter) {
    
    $orderPayment = $this->paylandClient->getOrders($start,$end);
    $SUCCESS = $REFUSED = $ERROR = 0;
    $count = ['SUCCESS' => 0,'REFUSED' => 0,'ERROR' => 0];
    if ($orderPayment){
      if ($orderPayment->message == 'OK')
        foreach ($orderPayment->transactions as $order){
          $amount = $order->amount/100;
          switch ($order->status){
            case 'SUCCESS':
              $SUCCESS += $amount;
              break;
            case 'REFUSED':
              $REFUSED += $amount;
              break;
            case 'ERROR':
              $ERROR += $amount;
              break;
          }
          $count[$order->status]++;
        }
    }
    
    $oItem = PaylandsSummary::where('date_ym',$filter)->first();
    if (!$oItem){
      $oItem = new PaylandsSummary();
      $oItem->date_ym = $filter;
    }
    
    $oItem->p_success = $SUCCESS;
    $oItem->p_refused = $REFUSED;
    $oItem->p_error   = $ERROR;
    $oItem->counts    = json_encode($count);
    $oItem->save();
  }
}

<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LogsService;
use App\Services\OtaGateway\Config as oConfigOtas;
use App\Services\Wubook\WuBook;
use App\DailyPrices;

///admin/Wubook/Availables?detail=1
class WubookMinStay extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'wubook:sendMinStay';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Mark Rates rooms in wubook';


  /**
   * The console command result.
   *
   * @var string
   */
  var $result = array();
  var $sLog = null;
  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->result = array();
    $this->sLog = new LogsService('OTAs_wubook','console');
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $kyControl = 'wubookMinStay';
    $control = \App\ProcessedData::findOrCreate($kyControl);
    $control = json_decode($control->content);
    if (!$control){
      $forse = intval(date('Hi'));
      if ($forse>22) return null;
    }

    if (!$this->check_and_send()) return null;
    // Clear the auxiliar datatable
    $oControl = \App\ProcessedData::findOrCreate($kyControl);
    $contentControl = json_decode($oControl->content);
    if ($contentControl == $control){
      $oControl->content = null;
      $oControl->save();
    }
  }

  private function check_and_send()
  {

    //$channels = ['RIAD1', 'RIAD2'];

    $channels = getAptosChannel();
    $items = [];
    $start = date('Y-m-d');
    // $end = date('Y-m-d', strtotime("+2 days"));
    $end = date('Y-m-d',strtotime("+12 months"));

    $default =[];
    $nigths = calcNights($start,$end);
    $startTime = strtotime($start);
    while ($nigths>0){
      $default[date('Y-m-d',$startTime)] = ['min_stay'=>1,'min_stay_arrival'=>1]; 
      $startTime = strtotime('+1 day', $startTime);
      $nigths--;
    }



    foreach ($channels as $ch) {
        $items[$ch] = $this->getMinStay($ch, $start, $end,$default);
    }
    $WuBook = new WuBook();
    
    $rChanels = $WuBook->getRoomsEquivalent(null);
    $roomdays = [];
    foreach ($items as $ch => $data) {
      $rID = $rChanels[$ch];
      if ($rID > 0){
        $roomdays['_int_'.$rID] = $data;
      } 
    }
   

    if (count($roomdays) > 0) {
      $WuBook->conect();
      if (!$WuBook->set_Restrictions(1,$start,$roomdays)) {
        $this->sLog->warning( 'error send Min Stay');
        return null;
      }
      $this->sLog->info(' Min Stay sender');
      $WuBook->disconect();
      return true;
    }
  }

  function getMinStay($ch, $start, $end, $result)
  {

    $oPrice = DailyPrices::where('channel_group', $ch)
              ->where('date', '>=', $start)
              ->where('date', '<', $end)
              ->get();
      if ($oPrice) {
        foreach ($oPrice as $p) {
          $val = ($p->min_estancia) ? $p->min_estancia : 1;
          $result[$p->date] = ['min_stay'=>$val,'min_stay_arrival'=>$val, 'closed'=>0]; 
        }
      }
    $aux = [];
    foreach ($result as $p) $aux[] = $p;
    return $aux;
  }
}

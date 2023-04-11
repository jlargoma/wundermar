<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
//use App\Services\Wubook\WuBook;
//use App\Services\Zodomus\Zodomus;
use App\Services\OtaGateway\OtaGateway;
use App\ProcessedData;

///admin/Wubook/Availables?detail=1
class MinStaySeason extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OTAs:sendMinStaySeason';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Min Stays by season to Zodomus and Wubook';
    
    
    /**
     * The console command result.
     *
     * @var string
     */
    var $result = array();

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->result = array();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      foreach (\App\Sites::siteIDs() as $sID){
       $this->check_and_send_otaGateway($sID);
      }
    }
    
    private function check_and_send_zodumos(){
      
      $oZodomus = new Zodomus();
      $items = ProcessedData::where('key','SendToZoodomus_minStay')->limit(10)->get();
      if ($items){
        foreach ($items as $item){
          $data = json_decode($item->content,true);
          $errorMsg = $oZodomus->setRates($data[0],$data[1]);
          if ($errorMsg){
            $item->name = $errorMsg;
            $item->key = 'SendToZoodomus_minStay-error';
            $item->save();
          } else {
            $item->delete();
          }
        }
      }
    }
    
    private function check_and_send_wubooks(){
      $WuBook = new WuBook();

      $items = ProcessedData::where('key','SendToWubook_minStay')->limit(5)->get();

      if (count($items)>0){
        $WuBook->conect();
        foreach ($items as $item){
          $data = json_decode($item->content,true);
          $response = $WuBook->set_Restrictions($data['site'],$data['start'],$data['min_stay']);
          
          if ($response) { $item->delete();}
          else {
            $item->key = 'SendToWubook_minStay-error';
            $item->save();
          }
        }
        $WuBook->disconect();
      }
    }
  private function check_and_send_otaGateway($siteID){
      $OtaGateway = new OtaGateway();

      $items = ProcessedData::where('key','SendToOtaGateway_minStay-'.$siteID)->limit(10)->get();
      if (count($items)>0){
        $OtaGateway->conect($siteID);
        foreach ($items as $item){
          $data = json_decode($item->content,true);
          $response = $OtaGateway->setMinStay(['restrictions'=>[$data['room']=>$data['MinStay']]]);
          
          if ($response == 200) { $item->delete();}
          else {
            $item->key = 'SendToOtaGateway_minStay-'.$siteID.'-error';
            $item->save();
          }
          
        }
        $OtaGateway->disconect($siteID);

      }
    }
    
}

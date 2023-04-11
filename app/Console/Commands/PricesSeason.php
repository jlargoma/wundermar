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
class PricesSeason extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OTAs:sendPricesSeason';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send prices by season to Zodomus and Wubook';
    
    
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

    private function check_and_send_OtaGateway($siteID){
      $OtaGateway = new OtaGateway();

      $items = ProcessedData::where('key','SendToOtaGateway-'.$siteID)->limit(10)->get();

      if (count($items)>0){
        $OtaGateway->conect($siteID);
        foreach ($items as $item){
          $data = json_decode($item->content,true);
          $prices = [$data['room']=>$data['prices']];
          $response = $OtaGateway->setRates(["price"=>$prices]);
          if ($response == 200) { $item->delete();}
          else {
            $item->key = 'SendToOtaGateway-'.$siteID.'-error';
            $item->save();
          }
        }
        $OtaGateway->disconect($siteID);
      }
    }
}

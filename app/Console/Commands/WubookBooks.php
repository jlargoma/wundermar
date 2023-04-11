<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Services\LogsService;
use App\Services\Wubook\WuBook;
use App\Services\OtaGateway\OtaGateway;
use App\Services\Wubook\Config as oConfigWB;

///admin/Wubook/Availables?detail=1
class WubookBooks extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'wubook:WubookBooks';

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
  var $sOta = null;

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
    $this->sOta = new OtaGateway();
    $this->sLog = new LogsService('OTAs_wubook','testing');
    $this->check_and_save();
  }

  private function check_and_save()
  {
    $oData = \App\ProcessedData::findOrCreate('wubook_webhook');
    $content = json_decode($oData->content);
  //  $content = [(object)["rcode"=>"1652871646","lcode"=>"1578949667"]];
    if (is_array($content) && count($content)){
      
      $WuBook = new WuBook();
      $WuBook->conect();
      foreach ($content as $c){
        $rva =  $WuBook->fetch_booking($c->lcode,$c->rcode);
        if($rva){
          $bookID = $WuBook->addBook($rva);
          $this->sLog->info('Add Booking '.$bookID);
        } else {
          $this->sLog->warning( 'error Booking '.$c->lcode.'-'.$c->rcode);
        }
      }
      $WuBook->disconect();
      
      // Clear the auxiliar datatable
      $oData = \App\ProcessedData::findOrCreate('wubook_webhook');
      $contentControl = json_decode($oData->content);
      if ($contentControl == $content){
        $oData->content = null;
        $oData->save();
      }
    }
     
   
  }

  function addBook($rva)
  {
   
  $oConfig = new oConfigWB();

 
    $channel_group = $oConfig->getChannelByRoom($rva['rooms']);
    $customer_notes = implode(',',(array)($rva['customer_notes']));
    $start  = convertDateToDB($rva['date_arrival']);
    $finish = convertDateToDB($rva['date_departure']);
    $comision = 0;
    $pvpFinal = $rva['amount'];
    if ($rva['amount']>0){
      $comision = $comision + ($comision/100*21);
      // comision = (PVP_final 15%) +  (PVP_final 15%) 21%
      // x+[y/100*15 + (y/100*15/100*21)] = PVP final
      // PVP final = $rva['amount'] / 0.8185

      $pvpFinal = round(($rva['amount']/0.8185) , 2);
      $comision = $pvpFinal - $rva['amount'];
    }


    $reserv = [
        'channel' => null,
        'bkg_number' => $rva['reservation_code'],
        // 'rate_id' => $rva['plan_id'],
        'external_roomId' => $rva['rooms'],
        'reser_id' => $rva['channel_reservation_code'],
        'comision' => $comision,
        'channel_group' => $channel_group,
        'status' => ($rva['status'] == 5) ? 2 : 1,
        'agency' => 4,//just airbnb
        'customer_name' => $rva['customer_name'].' '.$rva['customer_surname'],
        'customer_email' => $rva['customer_mail'],
        'customer_phone' => $rva['customer_phone'],
        'customer_comment' => $customer_notes,
        'totalPrice' => $pvpFinal,
        'adults' => $rva['men'],
        'children' => $rva['children'],
        'extra_array' => [],
        'start' => $start,
        'end' => $finish,
        'modified_from' => null,
        'modified_to' => null,
        'pax'=>$rva['men']+ $rva['children'],
    ];

//      $bookID = null;
    $bookID = $this->sOta->addBook($channel_group, $reserv);
 
    $this->sLog->info('Add Booking '.$bookID);
  }
}

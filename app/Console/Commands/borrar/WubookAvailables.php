<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\WobookAvails;
use App\Book;
use App\Services\Wubook\WuBook;

///admin/Wubook/Availables?detail=1
class WubookAvailables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wubook:sendAvaliables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark Avaliables rooms in wubook';
    
    
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
       $this->check_and_send();
    }
    
    private function check_and_send(){
       //clear before dates
    WobookAvails::where('date','<',date('Y-m-d'))->delete();
    
    $year = date('Y')+2;
    $list = WobookAvails::where('date','<',$year.'-01-01')->orderBy('id','desc')->get();
    $items = [];
    $delIDs = [];
    $already = [];
    if ($list){
      foreach ($list as $v){
        if (!in_array($v->channel_group.$v->date,$already)){
          $already[] = $v->channel_group.$v->date;
          if (!isset($items[$v->channel_group])) $items[$v->channel_group] = [];
          $items[$v->channel_group][] = [
            'avail'=> $v->avail,
            'date'=> convertDateToShow($v->date,true)
            ];
        }
        if (!isset($delIDs[$v->channel_group])) $delIDs[$v->channel_group] = [];
        $delIDs[$v->channel_group][] = $v->id;
      }
      
    }

    $WuBook = new WuBook();
    
    //Get the Channel -> Wubook rooms ID
//    $getRooms = Rooms::where('state',1)->get();

    $SiteChannels = $this->getSiteChannels();
    
    $roomdays = [];
    $delDaySite = [1=>[],2=>[]];
    foreach ($SiteChannels as $site=>$channels){
      if (count($channels)>0){ //the Site has channels
        
        $rIDs = $WuBook->getRoomsEquivalent($channels);
        if (count($rIDs)){ //the channels has a WuBook's room
          $roomdaysAux = [];
          foreach ($rIDs as $ch=>$rid){
            if (isset($items[$ch]))
              $roomdaysAux[] = ['id'=> $rid, 'days'=> $items[$ch]];
            if (isset($delIDs[$ch]))
              $delDaySite[$site] = array_merge ($delDaySite[$site],$delIDs[$ch]);
          }
          
          if (count($roomdaysAux)>0){
            $roomdays[$site] = $roomdaysAux;
          }
        }
      }
    }
    
    if (count($roomdays)>0){
      $WuBook->conect();
      foreach ($roomdays as $site=>$items){
        if ($WuBook->set_Closes($site,$items)){
          //delete the aux table data
          WobookAvails::whereIn('id',$delDaySite[$site])->delete();
        }
      }
      $WuBook->disconect();
      
    }
    /*************************************************/
    
    }
    
    
  private function getSiteChannels() {
    $getRooms = \App\Rooms::where('state',1)->groupBy('channel_group')->pluck('site_id','channel_group')->toArray();
    $channelsSite = [1=>[],2=>[]];
    if ($getRooms){
      foreach ($getRooms as $ch=>$site){
        if (isset($channelsSite[$site])){
          $channelsSite[$site][] = $ch;
        }
      }
    }
    return $channelsSite;
  }

}

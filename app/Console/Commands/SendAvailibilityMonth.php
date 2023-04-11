<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Services\OtaGateway\OtaGateway;
use App\Services\OtaGateway\Config as oConfig;

class SendAvailibilityMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OTAs:SendAvailibilityMonth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Availibility form the next 3 Months';
    
    
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
      
      $startTime = date('Y-m-d');
      $endTime = date('Y-m-d', strtotime("+3 months"));
      $oConfig = new oConfig();
      $aptos = $oConfig->getRooms();
      $book = new \App\Book();
      foreach ($aptos as $ch=>$v){
        $room = \App\Rooms::where('channel_group',$ch)->first();
        if ($room){
          $book->sendAvailibility($room->id,$startTime,$endTime);
        }
      }
    
    }
    
}

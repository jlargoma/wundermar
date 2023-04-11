<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\WobookAvails;
use App\Book;
use App\Services\Wubook\WuBook;

///admin/Wubook/Availables?detail=1
class WubookGetBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wubook:getBookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read and get the bookings from wubook webHook';
    
    
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
      $oData = \App\ProcessedData::findOrCreate('wubook_webhook');
      $content = json_decode($oData->content);
      
//      $content = json_decode(json_encode([['lcode'=>1,'rcode'=>1]]));
      if ($content && count($content)){

        $WuBook = new WuBook();
        $WuBook->conect();
        foreach ($content as $c){
          $WuBook->fetch_booking($c->lcode,$c->rcode);
        }
        $WuBook->disconect();

        //Clear the auxiliar datatable
        $oData->content = null;
        $oData->save();
      }
    }
  
}

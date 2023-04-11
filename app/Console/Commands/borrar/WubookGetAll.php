<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\WobookAvails;
use App\Book;
use App\Services\Wubook\WuBook;

///admin/Wubook/Availables?detail=1
class WubookGetAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wubook:getAllBookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read and get All bookings';
    
    
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
      
      $dateFrom=date('d/m/Y');
      $dateTo=date('d/m/Y', strtotime('+1 month'));
      $dateFrom='17/06/2020';
      $dateTo='19/06/2020';
        $WuBook = new WuBook();
        $WuBook->conect();
        $WuBook->fetch_bookings(1,$dateFrom,$dateTo);
//        $WuBook->fetch_bookings(2);
//        $WuBook->fetch_bookings(1);
        $WuBook->disconect();

    }
  
}

<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LogsService;
use App\Services\Wubook\WuBook;

///admin/Wubook/Availables?detail=1
class WubookGetAllBooking extends Command
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

    var $sLog = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->result = array();
        $this->sLog = new LogsService('OTAs_wubook','testing');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $dateFrom = date('d/m/Y');
        $dateTo = date('d/m/Y', strtotime('+1 month'));
        // $dateFrom = '17/05/2022';
        // $dateTo = '19/05/2022';
        $WuBook = new WuBook();
        $WuBook->conect();
        $rvas = $WuBook->fetch_bookings(1, $dateFrom, $dateTo);


        if ($rvas) {
            foreach ($rvas as $rva) {
                $bookID = $WuBook->addBook($rva);
                $this->sLog->info('Add Booking ' . $bookID);
            }
        } else {
            $this->sLog->warning('not Bookings ' . $dateFrom . '-' . $dateTo);
        }
        $WuBook->disconect();
    }
}

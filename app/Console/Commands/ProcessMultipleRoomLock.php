<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Services\Bookings\MultipleRoomLock;
use Carbon\Carbon;

class ProcessMultipleRoomLock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MultipleRoomLock:Process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera bloqueos de departamentos';
    
    
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
          
      $sites = \App\Sites::allSites();
      $MultipleRoomLock = new MultipleRoomLock();
      $aTaskData = $MultipleRoomLock->get_RoomLockSetting($sites);
      
      $today = Carbon::now('Europe/Madrid');
      $tomorrow = Carbon::tomorrow('Europe/Madrid');
      $now = intval($today->format('H'));
      if ($aTaskData){
        if ( $now !== intval($aTaskData['time'])){
          return null;
        }
        foreach ($aTaskData['sites'] as $k=>$v){
          if ($v === 1)
          $MultipleRoomLock->roomLockBy_site($k, $today->format('Y-m-d'),  $tomorrow->format('Y-m-d'));
        }
      }
      
    }
}

<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Services\LogsService;

class ClearSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ClearSessions:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borrar las sessiones de usuarios antiguas';
    
    
    private $sLog;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
      parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $this->sLog = new LogsService('schedule','testing');
      $c = 0;
      $sessions = storage_path('framework/sessions');
      $lastWeek = strtotime('-2 weeks');
      if ($gestor = opendir($sessions)) {

       /* Esta es la forma correcta de iterar sobre el directorio. */
       while (false !== ($entrada = readdir($gestor))) {
         if ($entrada == '.' || $entrada == '..') continue;
         else {
          $time = filemtime($sessions.'/'.$entrada);
          if ($time<$lastWeek){
            $c++;
            unlink($sessions.'/'.$entrada);
//            echo date('Y-m-d',$time)." - Eliminar $entrada\n";
          } 
         }
       }
       closedir($gestor);
      }
      $this->sLog->info($c.' Sessiones eliminadas antes de '. date('Y-m-d',$lastWeek) );
    }
    
    
}

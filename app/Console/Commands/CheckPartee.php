<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\BookPartee;

class CheckPartee extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'partee:check';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check partee checkin status';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    $this->checkInStatus();
  }

  /**
   * Check the Partee HUESPEDES completed
   */
  public function checkInStatus() {
    $apiPartee = new \App\Services\ParteeService();
    
    //conect to Partee and get the JWT
    if ($apiPartee->conect()) {

      $date = date('Y-m-d', strtotime('-2 days'));
      //List the no-complete partee
      $listBookPartee = BookPartee::select('book_partees.*','book.pax')
              ->Join('book','book.id','=','book_partees.book_id')
              ->where('partee_id','>',0)
//              ->where('type_book','2')
              ->where('has_checked','0')
              ->where('start','>=',$date)
              ->get();

      $aux = new BookPartee();
      $aux->whereNotIn('status', ['FINALIZADO'])->update(['has_checked' => 0]); //pasa a todos a listo para leer
      if ($listBookPartee){
        foreach ($listBookPartee as $BookPartee) {
        //Read a $BookPartee     
        try {
          $partee_id = $BookPartee->partee_id;
          
          //check Partee status
          $result = $apiPartee->getCheckStatus($partee_id);

          if($apiPartee->response && isset($apiPartee->responseCode) && $apiPartee->responseCode == 200) {
            
            //Save the new status
            $log = $BookPartee->log_data . "," . time() . '-' . $apiPartee->response->status;
            $BookPartee->status = $apiPartee->response->status;
            $BookPartee->log_data = $log;
            $BookPartee->has_checked = 1;
            if ($apiPartee->response->status == 'HUESPEDES'){
              $BookPartee->guestNumber = $apiPartee->response->guestNumber;
              $BookPartee->date_complete = date('Y-m-d H:i:s');
            }
            
            $BookPartee->save();
            
          } else {
              if( isset($apiPartee->responseCode) && $apiPartee->responseCode == 404){
                $log = $BookPartee->log_data . "," . time() . '-NotFound '.$BookPartee->partee_id;
                $BookPartee->log_data = $log;
                $BookPartee->partee_id = -1;
                $BookPartee->save();
              } else {
                Log::error($apiPartee->response);
                $BookPartee->status = 'error';
                $BookPartee->has_checked = 1;
                $BookPartee->save();
              }
          }
        } catch (\Exception $e) {
          Log::error("Error CheckIn Partee " . $BookPartee->id . ". Error  message => " . $e->getMessage());
          echo $e->getMessage();
          $BookPartee->has_checked = 1;
          $BookPartee->save();
          continue;
        }
      }
    
      }
    } else {
      //Can't conect to partee
      Log::error("Error Conect Partee " . $apiPartee->response);
      echo $apiPartee->response;
    }
  }

}

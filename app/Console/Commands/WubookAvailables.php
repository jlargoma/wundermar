<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use App\Services\LogsService;
use App\Book;
use App\Rooms;
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
  var $sLog = null;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->result = array();
    $this->sLog = new LogsService('OTAs_wubook','console');
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $kyControl = 'wubookAvail';
    $control = \App\ProcessedData::findOrCreate($kyControl);
    $control = json_decode($control->content);
    if (!$control){
      $forse = intval(date('Hi'));
      if ($forse>22) return null;
    }

    if (!$this->check_and_send()) return null;

    // Clear the auxiliar datatable
    $oControl = \App\ProcessedData::findOrCreate($kyControl);
    $contentControl = json_decode($oControl->content);
    if ($contentControl == $control){
      $oControl->content = null;
      $oControl->save();
    }
  }

  private function check_and_send()
  {

    //$channels = ['RIAD1', 'RIAD2'];

    $channels = getAptosChannel();
    $items = [];
    $start = date('Y-m-d');
    // $end = date('Y-m-d', strtotime("+2 days"));
    $end = date('Y-m-d',strtotime("+12 months"));
    // $end = date('Y-m-d',strtotime("+1 months"));

    foreach ($channels as $ch) {
        $items[$ch] = $this->getAvaility($ch, $start, $end);
    }
    $WuBook = new WuBook();
    
    $rChanels = $WuBook->getRoomsEquivalent(null);
    $roomdays = [];
    foreach ($items as $ch => $data) {
      $rID = $rChanels[$ch];
      if ($rID > 0){
        $roomdays[] = ['id' => '_int_'.$rID, 'days' => $data];
      } 
    }
   


    if (count($roomdays) > 0) {
      $WuBook->conect();
      if (!$WuBook->set_Closes(1,$roomdays)) {
        $this->sLog->warning( 'error send Avails');
        return null;
      }
      $this->sLog->info('Avails sender');
      $WuBook->disconect();
      return true;
    }
  }

  function getAvaility($ch, $start, $finish)
  {

    $oRooms = Rooms::RoomsCH_IDs($ch);

    $match1 = [['start', '>=', $start], ['start', '<=', $finish]];
    $match2 = [['finish', '>=', $start], ['finish', '<=', $finish]];
    $match3 = [['start', '<', $start], ['finish', '>', $finish]];

    $books = Book::where_type_book_reserved()->whereIn('room_id', $oRooms)
      ->where(function ($query) use ($match1, $match2, $match3) {
        $query->where($match1)
          ->orWhere($match2)
          ->orWhere($match3);
      })->get();

    $avail  = count($oRooms);


    //Prepara la disponibilidad por día de la reserva
    $today = strtotime(date('Y-m-d'));
    $startAux = strtotime($start);
    $endAux = strtotime($finish);
    if ($startAux < $today) $startAux = $today;
    $aLstDays = [];
    while ($startAux < $endAux) {
      $aLstDays[date('Y-m-d', $startAux)] = $avail;
      $startAux = strtotime("+1 day", $startAux);
    }
    $control = [];
    if ($books) {
      foreach ($books as $book) {
        //Resto los días reservados
        $startAux = strtotime($book->start);
        $endAux = strtotime($book->finish);

        while ($startAux < $endAux) {
          $auxTime = date('Y-m-d', $startAux);
          $keyControl = $book->room_id . '-' . $auxTime;
          if (!in_array($keyControl, $control)) {
            if (isset($aLstDays[$auxTime]))
              $aLstDays[$auxTime]--;

            $control[] = $keyControl;
          }

          $startAux = strtotime("+1 day", $startAux);
        }
      }
    }

    foreach ($aLstDays as $d => $v) {
      $result[] = [
        'date'          => convertDateToShow($d,true),
        'avail'         => $v
      ];
    }

    return $result;
  }
}
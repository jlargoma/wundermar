<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Book;
use App\Rooms;
use App\SafetyBox;
use App\Traits\BookEmailsStatus;
use App\Services\LogsService;

class SafeBox extends Command
{
  use BookEmailsStatus;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SafeBox:asignAndSend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asign the SafeBox to checking at 10am and send the mail';
    
    
    private $oSafetyBox;
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
      $this->sLog = new LogsService('schedule','safeBox');

      try{
        $this->oSafetyBox = new SafetyBox();
        foreach (\App\Sites::siteIDs() as $sID){
          $this->process($sID);
        }
        
      } catch (Exception $ex) {
        $this->sLog->error('error en SafeBox');
      }
    }
    
    private function process($siteID){
      
      $safeBooxLst = $this->oSafetyBox->getBySite($siteID);
      if (!$safeBooxLst) return null;
      
      $today = date('Y-m-d');
      $rooms = Rooms::getRoomsBySite($siteID)->toArray();
      $roomIDs = array_keys($rooms);
      
      $sb_by_room = [];
      foreach ($safeBooxLst as $sb){
        $sb_by_room[$sb->room_id] = $sb;
        $sb_by_ID[$sb->id] = $sb;
      }

      $books = Book::whereIn('type_book',[1,2])
                ->whereIn('room_id',$roomIDs)
                ->where('start','=',$today)
                ->get();
      $bookIDs = [];
      $booksByID = [];
      if ($books){
        //-----------------------------------------------------------//
        $bookIDs = $books->pluck('id')->toArray();
        $BookSafetyBox = $this->oSafetyBox->getByBookIDs($bookIDs);
        $bAsig = [];
        
        if ($BookSafetyBox) {
          foreach ($BookSafetyBox as $bsf)  $bAsig[] = $bsf->book_id;
        }
        
        //-------   ASIGNAR         -------------------------//
        foreach ($books as $book){
          $booksByID[$book->id] = $book;
          if (in_array($book->id, $bAsig)) continue;
          $box = isset($sb_by_room[$book->room_id]) ? $sb_by_room[$book->room_id] : null;
          if (!$box){
            $this->sLog->info('SafeBox: No hay caja para '.$book->room_id);
            continue;
          }

          if (!$this->oSafetyBox->usedBy_day($box->id,$book->start,$book->finish)){
            $this->oSafetyBox->newBookSafetyBox($book->id,$box->id);
            $this->sLog->info('SafeBox: Caja asignada '.$book->id.' - '.$box->id.': '.$book->start);
          } else {
            $this->sLog->info('SafeBox: No hay caja libres '.$book->id.' - '.$box->id.': '.$book->start);
          }
        }
        
        //----------  ENVIAR MAIL  -----------------------------/
        $BookSafetyBox = $this->oSafetyBox->getByBookIDs($bookIDs); // cargo de nuevo el listado
        if ($BookSafetyBox) {
          $sfSend = $sfNoSend = [];
          foreach ($BookSafetyBox as $bsf) {
            $box = isset($sb_by_ID[$bsf->box_id]) ? $sb_by_ID[$bsf->box_id] : null;
            if ($box){
              $book   = $booksByID[$bsf->book_id];
              $sended = $this->sendSafeBoxMensaje($book, 'book_email_buzon', $box);
              if ($sended){
                $box->updLog($bsf->book_id, 'sentMail');
                $sfSend[] = $bsf->book_id;
              } else {
                $sfNoSend[] = $bsf->book_id;
              }
            } 
          }
          if (count($sfSend)) $this->sLog->info('SF enviados '.implode(',',$sfSend));
          if (count($sfNoSend)) $this->sLog->info('SF NO enviados '.implode(',',$sfNoSend));
        }
      
      
      }
      else  $this->sLog->info('SafeBox: No hay reservas '.$today);
    }
    
}

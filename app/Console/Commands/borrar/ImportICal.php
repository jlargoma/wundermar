<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use DateInterval;
use ICal\ICal;
use Log;
use App\IcalImport;
use Illuminate\Support\Facades\DB;

/***
 * Add in /vendor/johngrogg/ics-parser/src/ICal/ICal.php:2700
 * $context = stream_context_create(
            array(
                "http" => array(
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/188.164.197.231 Safari/537.36"
                )
            )
        );
 */

///admin/ical/importFromUrl?detail=1
class ImportICal extends Command
{
    /**
     * Customer ID for assign to the new books
     *
     * @var int
     */
    private $customer_id = 1780;

    /**
     * User ID for assign to the new books
     *
     * @var int
     */
    private $user_id = 39;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ical:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import ICal from agencies';
    
    
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
       $this->importICalendar();
    }

    /**
     * Import ICaledar for the agencies
     */
    public function importICalendar()
    {              
//        $icalendars_to_import = IcalImport::where('processed','!=',1)->get();
        $icalendars_to_import = IcalImport::all();
        if (count($icalendars_to_import)>0){
        foreach ($icalendars_to_import as $ical_to_import) {
            $this->result = [];
            //id releated with the icalendar to import
            $room_id = $ical_to_import->room_id;

            $ical_to_import->processed = 1;
            //agency from where we are importing the calendar
            $agency = $this->getAgencyFromURL($ical_to_import->url);

            //Read a iCal            
            try {
                //FIXME: Use model of import ical
                $ical = new ICal($ical_to_import->url);
                //user_agent ?
            } catch (\Exception $e) {
                Log::error("Error importing icalendar " . $ical_to_import->id . ". Error  message => " . $e->getMessage());
                echo $e->getMessage();
                continue;
            }
    
            // All events on iCal
            $events = $ical->sortEventsWithOrder($ical->events());

            $valid_events = [];

//            $this->printEvents($events);
            $count = 0;
            if (true){
              $ical_to_import->save();
              foreach ($events as $event) {

                if ($agency == 4 && strpos(strtoupper($event->summary),'NOT AVAILABLE') !== false){
                  continue;
                }
                $checEvent = $this->isEventValidForAdd($event, $agency, $room_id);
                if ($checEvent['valid']) {
                    if ($this->addBook($event, $agency, $room_id)){
                      $count++;
                    } else {
                      Log::error("Adding event => " . print_r($event,true));
                    }
                } else {
                  
                  $date_start_book = new DateTime($event->dtstart);
                  $date_end_book = new DateTime($event->dtend);
                  $this->result[] = [
                      $agency,'No valido '.$checEvent['msg'],$date_start_book->format("Y-m-d"),$date_end_book->format("Y-m-d"),0
                  ];
                }
              }
            }
            
            // save log data
            $lData = new \App\LogsData();
            $dataLog = [
                'room'    => $room_id,
                'url'     => $ical_to_import->url,
                'result'  => $this->result
            ];
            $lData->key  = ($agency == 4) ? 'ical_airbnb' : 'ical_booking';
            $lData->data =  $count.' Registros importados';
            $lData->long_info = json_encode($dataLog);
            $lData->save();
            if (isset($_GET['detail']))
              $this->printResults($room_id,$ical_to_import->url);
        }
        } else{
          DB::table('ical_import')->update(['processed' => 0]);
        }
        if (isset($_SERVER['REQUEST_METHOD'])){
          echo 'ok';
        }
    }

    function printEvents($events){
      $bloqued = array();
        foreach ($events as $event) {
          if (strpos(strtoupper($event->summary),'NOT AVAILABLE') !== false){
            $bloqued[] = $event->summary;
            continue;
          }
          echo $event->dtstart.' - '.$event->dtend.' - '.($event->summary)."\n";
        }
        print_r($bloqued);
    }
    /**
     * Add event to calendar
     * 
     * @param $event ICal\Event
     * @param $agency integer Agency from where come the book
     * @param $room_id Room belong the book
     */
    private function addBook(\ICal\Event $event, $agency, $room_id)
    {
        $start = new DateTime($event->dtstart);
        $finish = new DateTime($event->dtend);
        $interval = $start->diff($finish);
        $nights = $interval->format("%a");
        $phone = '';

        if ($agency == 4){ //AIRBNB
          $lines = explode(PHP_EOL, $event->description);
          foreach ($lines as $data){
            if (strpos($data, 'PHONE:') !== false) {
              $phone = preg_replace('[PHONE\:|\\n]', '', $data);
            }
          }
        }


        $book = new \App\Book();

        $customer          = new \App\Customers();
        $customer->user_id = 23;
        $customer->name    = str_replace('CLOSED - ','',$event->summary);
        $customer->name    .= ($agency == 1)? " -BOOKING-": " -AIRBNB-";
        $customer->email   = "";
        $customer->phone   = trim($phone);
        $customer->DNI     = "";
        $customer->save();

        //Create Book
        $book->user_id       = $this->user_id;
        $book->customer_id   = $customer->id;//$this->customer_id;// user to book imports / user to airbnb imports
        $book->room_id       = $room_id;
        $book->start         = $start->format("Y-m-d");
        $book->finish        = $finish->format("Y-m-d");
        $book->comment       = $event->summary;
        $book->book_comments = $event->summary . " #IMPORTING_TASK_SCHEDULED";
        $book->type_book     = 11;
        $book->nigths        = $nights;
        $book->agency        = $agency;
        
        $this->result[] = [
            $agency,$customer->name,$book->start,$book->finish,$nights,
        ];
        return $book->save();
    }

    /**
     * Check if the event i valid for add
     * 1. finish date equal or bigger than today
     * 2. Do not exist a book with the same dates, 
     * same user, same agency, customer
     * 
     * @param $event ICal\Event
     * @param $agency integer Agency from where come the book
     * @param $room_id Room belong the book
     *
     * @return boolean
     */
    private function isEventValidForAdd(\ICal\Event $event, $agency, $room_id)
    {
        $date_now = new DateTime();
        $date_start_book = new DateTime($event->dtstart);
        $date_end_book = new DateTime($event->dtend);

        if ($date_now->format("Y-m-d") >= $date_end_book->format("Y-m-d"))
          return ['valid'=>false,'msg'=>'Fecha posterior'];
//            return false;

        // if summary event start on #ADMIN, #BOOKING, #TRIVAGO, #BED&SNOW, #AIRBNB

        if (  preg_match("/^#ADMIN/", $event->summary ) || preg_match("/^#BOOKING/", $event->summary ) || preg_match("/^#TRIVAGO/", $event->summary ) || preg_match("/^#BED&SNOW/", $event->summary ) || preg_match("/^#AIRBNB/", $event->summary )
            )
          return ['valid'=>false,'msg'=>'error '.$event->summary];
//           return false;

        //  $start & $finish in format d/m/Y
        //  \App\Book::existDate($start, $finish, %room_id ) 


        $books  = \App\Book::where('room_id',$room_id)
                        // ->where('user_id', $this->user_id)
                        // ->where('customer_id', $this->customer_id)
                        ->where('start', $date_start_book->format("Y-m-d"))
                        ->where('finish', $date_end_book->format("Y-m-d"))
                        ->where('agency', $agency)
                        ->get();
        if (count($books) == 0){
          return ['valid'=>true,'msg'=>''];
        }
        return ['valid'=>false,'msg'=>'Evento ya cargado'];
//        return count($books) == 0;
    }

    /**
     * Detect agency ical importer
     * 
     * @param $url
     *
     * @return integer
     */
    private function getAgencyFromURL($url)
    {
        $urls_agencies = [
            "/airbnb/" => 4,
            "/booking/" => 1
        ];

        foreach ($urls_agencies as $reg_agency => $agency_id) {
            if (preg_match($reg_agency, $url))
                return $agency_id;
        }

        //if we dont match any agency is a book of Jorge
        //but this is weird
        return 0;
    }
    
     
    /**
     * Print result in the website
     * 
     * @return type
     */
    function printResults($room_id, $url){
      
      if (!isset($_SERVER['REQUEST_METHOD'])) return;
      
      $room = \App\Rooms::find($room_id);
      if ($room){
        echo '<h5>'.$room->name.'</h5>';
      }
      echo $url.'<br>';
      echo '<style>
table {
  border-collapse: collapse;
}

table, td, th {
  border: 1px solid black;
}
table, td, th,td {
 text-align: center;
 }
</style>';
      if (count($this->result)){
        ?>
        <p>Registros Importados (<?php echo count($this->result); ?>)</p>
        <table class="table text-center">
          <thead>
            <tr>
              <th  style="min-width: 90px;">Agencia</th>
              <th  style="min-width: 90px;">Cliente</th>
              <th  style="min-width: 90px;">CheckIn</th>
              <th  style="min-width: 90px;">CheckOut</th>
              <th >Noches</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($this->result as $book):?>
            <tr>
              <td style="height: 5em;">
                <?php if ($book[0]==1) echo 'Booking' ?>
                <?php if ($book[0]==4) echo 'Airbnb' ?>
              </td>
              <td><?php echo $book[1]; ?></td>
              <td><?php echo $book[2]; ?></td>
              <td><?php echo $book[3]; ?></td>
              <td><?php echo $book[4]; ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php
      } else {
        ?>
        <p class="alert alert-warning text-center mt-15">No hay registros que importar</p>
        <?php
      }
    }
}

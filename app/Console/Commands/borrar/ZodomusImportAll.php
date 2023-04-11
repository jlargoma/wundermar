<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Services\Zodomus\Zodomus;
use App\Services\Zodomus\Config as ZConfig;
use App\Rooms;
use App\SizeRooms;
use App\Book;

/// /admin/zodomus/import?detail=1
class ZodomusImportAll extends Command {

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
  protected $signature = 'zodomus:importAll';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Import Booking from zodomus';

  /**
   * The console command result.
   *
   * @var string
   */
  var $result = array();

  /**
   * The console command result.
   *
   * @var string
   */
  var $sZodomus = array();

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    $this->result = array();
    $this->sZodomus = new Zodomus();
    $this->zConfig = new ZConfig();
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    
   
    
    $cannels = configZodomusAptos();
    $alreadySent = [];
    $reservas = [];
    foreach ($cannels as $cg => $apto) {
     
      //get all channels
      foreach ($apto->rooms as $room) {
        if (true) {
          $keyIteration = $room->channel . '-' . $room->propID;
          if (in_array($keyIteration, $alreadySent))
            continue;

          $channelId = $room->channel;
          $propertyId = $room->propID;
          $bookings = $this->sZodomus->getBookingsQueue($room->channel, $room->propID);
          
          /** test */
//          $response = $this->importAnReserv(1, 91378,1582557396);
          /** test */
       
          if ($bookings && isset($bookings->status) && $bookings->status->returnCode == 200) {
            $alreadySent[] = $keyIteration;
            
            foreach ($bookings->reservations as $book) {
              $reservas[] = [
                  $room->channel, $room->propID,$book->id
                ];
            }
          }
        }
      }
    }
    $response = null;
    foreach ($reservas as $r){
//      $alreadyExist = \App\Book::where('external_id', $r[2])->first();
//      if ($alreadyExist){
//        $this->result[] = [$r[2], 'Reserva ya creada', $alreadyExist->id];
//      } else {
        $response = $this->importAnReserv($r[0],$r[1],$r[2]);
//      }
    }
    
    if (isset($_SERVER['REQUEST_METHOD'])) {
      echo 'ok';
    }

    if (isset($_GET['detail']))
      $this->printResults();
    
  }

  function importAnReserv($channelId,$propertyId,$reservationId,$force=false){
    
        $param = [
                "channelId" =>  $channelId,
                "propertyId" => $propertyId,
                "reservationId" =>  $reservationId,
              ];

        $reservation = $this->sZodomus->getBooking($param);

        if ($reservation && isset($reservation->status) && $reservation->status->returnCode == 200){
          $booking = $reservation->reservations;
          if (!isset($booking->rooms)) {
            if ($booking->reservation->status == 3) { //Cancelada
              $alreadyExist = \App\Book::where('external_id', $reservationId)->get();
              if ($alreadyExist) {
                foreach ($alreadyExist as $item){
                  $response = $item->changeBook(98, "", $item);
                  echo $item->id.' cancelado - ';
                  $this->result[] = [
                    $reservationId, 'Reserva cancelada', $item->id
                  ];
                  if ($response['status'] == 'success' || $response['status'] == 'warning') {
                    //Ya esta disponible
                    $item->sendAvailibilityBy_status();
                  }
                }
              } else {
                 $this->result[] = [$reservationId, 'Reserva cancelada', '-1'];
              }
            }
            return;
          }
          //Una reserva puede tener multiples habitaciones
          $rooms = $booking->rooms;
          foreach ($rooms as $room){
            $update = false;
            $roomId = $room->id;
            $roomReservationId = $room->roomReservationId;
             //check if exists
            $alreadyExist = \App\Book::where('external_id', $reservationId)
                    ->where(function ($query) use ($roomReservationId) {
                                            $query->where('external_roomId', $roomReservationId)
                                                  ->orWhereNull('external_roomId');
                                        })->first();
                    
            if ($alreadyExist){
              if ($booking->reservation->status == 3){ //Cancelada

                $response = $alreadyExist->changeBook(98, "", $alreadyExist);
                $this->result[] = [$reservationId, 'Reserva cancelada', $alreadyExist->id];
                if ($response['status'] == 'success' || $response['status'] ==  'warning'){
                  //Ya esta disponible
                  $alreadyExist->sendAvailibilityBy_status();
                }
                continue;
                
              } else {
                $update = $alreadyExist->id;
                $this->result[] = [$reservationId, 'Reserva ya creada',$alreadyExist->id];
              }
              
              
            }              
            
            $cg = $this->sZodomus->getChannelManager($channelId,$propertyId,$roomId);
            if (!$cg){//'no se encontro channel'
              $this->result[] = [$reservationId, 'channel no encontrado',$propertyId.' - '.$roomId];
              continue;
            }
            
            $totalPrice = $room->totalPrice;
            if (isset($room->priceDetails)){
              foreach ($room->priceDetails as $priceDetails){
                if ($totalPrice < $priceDetails->total){
                  $totalPrice = $priceDetails->total;
                }
              }
            }
            if (isset($room->priceDetailsExtra)){
              foreach ($room->priceDetailsExtra as $pExtr){
                if ($pExtr->type == "guest" && $pExtr->included == 'no'){
                  $totalPrice += round($pExtr->amount,2);
                }
              }
            }
            
            $rateId   = isset($room->prices[0]) ? $room->prices[0]->rateId : 0;
            $rewrittenFromId  = isset($room->prices[0]->rewrittenFromId) ? $room->prices[0]->rewrittenFromId : 0;
            $comision = $this->zConfig->get_comision($totalPrice,$channelId);
            $reserv = [
                'channel' => $channelId,
                'propertyId' => $propertyId,
                'rate_id' => $rateId,
                'rewrittenFromId' => $rewrittenFromId,
                'comision'=>$comision,
                'external_roomId' => $roomReservationId,
                'channel_group' => $cg,
                'agency' => $this->zConfig->getAgency($channelId),
                'reser_id' => $reservationId,
                'status' => $booking->reservation->status,
                'customer' => $booking->customer,
                'totalPrice' => $totalPrice,
                'numberOfGuests' => $room->numberOfGuests,
                'mealPlan' => $room->mealPlan,
                'start' => $room->arrivalDate,
                'end' => $room->departureDate,
            ];
            if ($update){
              $bookID = $this->sZodomus->updBooking($cg,$reserv,$update);
            } else {
              $bookID = $this->sZodomus->saveBooking($cg,$reserv);
            }
            $this->result[] = [
                $reservationId,
                $booking->customer->firstName . ' ' . $booking->customer->lastName.': '.$room->arrivalDate.'-'.$room->departureDate,
                $bookID];
          }
        }
        
    }
    
  

    /**
   * Print result in the website
   * 
   * @return type
   */
  function printResults() {

    if (!isset($_SERVER['REQUEST_METHOD']))
      return;


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
    if (count($this->result)) {
      ?>
      <p>Registros Importados (<?php echo count($this->result); ?>)</p>
      <table class="table text-center">
        <thead>
          <tr>
            <th  style="min-width: 90px;">ID Reserva</th>
            <th  style="min-width: 90px;">Resultado</th>
            <th  style="min-width: 90px;">Reserva Admin</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->result as $book): ?>
            <tr>
              <td style="height: 5em;"><?php echo $book[0]; ?></td>
              <td><?php echo $book[1]; ?></td>
              <td><?php echo $book[2]; ?></td>
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

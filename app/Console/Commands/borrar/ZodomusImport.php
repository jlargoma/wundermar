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
class ZodomusImport extends Command {

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
  protected $signature = 'zodomus:import';

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
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    $cannels = configZodomusAptos();
//if (!isset($_GET['detail'])) return 'ok';


    $Zodomus = $this->sZodomus;
    $ZConfig = new ZConfig();
    $reservations = [];
    $reservIDs = [];
    $alreadySent = [];
    foreach ($cannels as $cg => $apto) {
      //get all channels
      foreach ($apto->rooms as $room) {
        if (true) {
          $keyIteration = $room->channel . '-' . $room->propID;
          if (in_array($keyIteration, $alreadySent))
            continue;

          $channelId = $room->channel;
          $propertyId = $room->propID;
          $agency = $ZConfig->getAgency($channelId);

          $bookings = $Zodomus->getBookings($room->channel, $room->propID);
          if ($bookings && $bookings->status->returnCode == 200) {
            $alreadySent[] = $keyIteration;
            if (count($bookings->reservations) > 0) {
              foreach ($bookings->reservations as $book) {

                $reservationId = $book->reservation->id;
                //Una reserva puede tener multiples habitaciones
                $rooms = $book->rooms;
                foreach ($rooms as $Zroom) {
                  $roomId = $Zroom->id;


                  $totalPrice = $Zroom->totalPrice;
                  if (isset($Zroom->priceDetails)){
                    foreach ($Zroom->priceDetails as $priceDetails){
                      if ($totalPrice < $priceDetails->total){
                        $totalPrice = $priceDetails->total;
                      }
                    }
                  }
                  if (isset($Zroom->priceDetailsExtra)){
                    foreach ($Zroom->priceDetailsExtra as $pExtr){
                      if ($pExtr->type == "guest" && $pExtr->included == 'no'){
                        $totalPrice += round($pExtr->amount,2);
                      }
                    }
                  }
                  
                  $roomReservationId = $room->roomReservationId;
                  
                  $rateId = isset($Zroom->prices[0]) ? $Zroom->prices[0]->rateId : 0;
                  $comision = $zConfig->get_comision($totalPrice,$channelId);
                  
                  $reservations[] = [
                      'channel' => $channelId,
                      'propID' => $propertyId,
                      'rate_id' => $rateId,
                      'external_roomId' => $roomReservationId,
                      'comision'=>$comision,
                      'channel_group' => $cg,
                      'status' => $book->reservation->status,
                      'agency' => $agency,
                      'reser_id' => $reservationId,
                      'status' => $book->reservation->status,
                      'customer' => $book->customer,
                      'totalPrice' => $totalPrice,
                      'numberOfGuests' => $Zroom->numberOfGuests,
                      'mealPlan' => $Zroom->mealPlan,
                      'start' => $Zroom->arrivalDate,
                      'end' => $Zroom->departureDate,
                  ];
                }
              }
            }
            $this->loadBookings($reservations);
          }
        }
      }
    }
    if (isset($_SERVER['REQUEST_METHOD'])) {
      echo 'ok';
    }

    if (isset($_GET['detail']))
      $this->printResults();
  }

  /**
   * Import ICaledar for the agencies
   */
  public function loadBookings($reservations) {

    $agencies = [
        1 => 'booking',
        4 => 'airbnb',
    ];

    foreach ($reservations as $reserv) {

      $agency = isset($agencies[$reserv['agency']]) ? $agencies[$reserv['agency']] : '';

      //BEGIN: check if exists
      $external_roomId = $reserv['external_roomId'];
      $alreadyExist = \App\Book::where('external_id', $reserv['reser_id'])
                      ->where(function ($query) use ($external_roomId) {
                        $query->where('external_roomId', $external_roomId)
                        ->orWhereNull('external_roomId');
                      })->first();
      if ($alreadyExist) {
        if ($reserv['status'] == 3) { //Cancelada
          $response = $alreadyExist->changeBook(3, "", $alreadyExist);
          if ($response['status'] == 'success' || $response['status'] == 'warning') {
            //Ya esta disponible
            $alreadyExist->sendAvailibilityBy_status();
          }
        }
        $this->result[] = [
            $agency, $reserv['reser_id'], 'Reserva ya registrada', $reserv['start'], $reserv['end'], '', $alreadyExist->id
        ];
        continue;
      }
      //END: check if exists


      $channelGroup = $this->sZodomus->getChannelManager($reserv['channel'], $reserv['propID'], $reserv['external_roomId']);
      $roomID = $this->sZodomus->calculateRoomToFastPayment($channelGroup, $reserv['start'], $reserv['end']);
      if ($roomID < 0) {
        $roomID = 33;
        $this->result[] = [
            $agency, $reserv['reser_id'], 'No dispone de Habitaciones para la reserva', $reserv['start'], $reserv['end'], '', '', $reserv['channel_group']
        ];
//        continue;
      }


      $nights = calcNights($reserv['start'], $reserv['end']);



      $book = new Book();

      //"customer" 
//        firstName-lastName-address-city-zipCode-countryCode-email
//        -phone-phoneCountryCode-phoneCityArea


      $rCustomer = $reserv['customer'];

      $customer = new \App\Customers();
      $customer->user_id = 23;
      $customer->name = $rCustomer->firstName . ' ' . $rCustomer->lastName . ' - ' . $agency;
      $customer->email = $rCustomer->email;
      $customer->phone = $rCustomer->phoneCountryCode . ' ' . $rCustomer->phoneCityArea . ' ' . $rCustomer->phone;
      $customer->DNI = "";
      $customer->save();

      $comment = $ZConfig->get_detailRate($reserv['rate_id']);
      
      //Create Book
      $book->user_id = $this->user_id;
      $book->customer_id = $customer->id;
      $book->room_id = $roomID;
      $book->start = $reserv['start'];
      $book->finish = $reserv['end'];
      $book->comment = $comment;//$reserv['mealPlan'];
      $book->type_book = 11;
      $book->nigths = $nights;
      $book->agency = $reserv['agency'];
      $book->pax = $reserv['numberOfGuests'];
      $book->real_pax = $reserv['numberOfGuests'];
      $book->PVPAgencia = $reserv['comision'];
      $book->total_price = $reserv['totalPrice'];
      $book->propertyId = $reserv['propID'];
      $book->external_id = $reserv['reser_id'];
      $book->external_roomId = $reserv['external_roomId'];

      $book->save();
      $this->result[] = [
          $agency, $book->external_id, $customer->name, $book->start, $book->finish, $nights, $book->id, $reserv['channel_group']
      ];
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
            <th  style="min-width: 90px;">Agencia</th>
            <th  style="min-width: 90px;">ID Reserva</th>
            <th  style="min-width: 90px;">Cliente</th>
            <th  style="min-width: 90px;">CheckIn</th>
            <th  style="min-width: 90px;">CheckOut</th>
            <th  style="min-width: 90px;">Noches</th>
            <th  style="min-width: 90px;">Reserva Admin</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->result as $book): ?>
            <tr>
              <td style="height: 5em;"><?php echo $book[0]; ?></td>
              <td><?php echo $book[1]; ?></td>
              <td><?php echo $book[2]; ?></td>
              <td><?php echo $book[3]; ?></td>
              <td><?php echo $book[4]; ?></td>
              <td><?php echo $book[5]; ?></td>
              <td><?php echo $book[6]; ?></td>
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

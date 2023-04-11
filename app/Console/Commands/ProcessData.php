<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Book;
use App\Rooms;
use App\ProcessedData;

///admin/Wubook/Availables?detail=1
class ProcessData extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'ProcessData:all';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = '';

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
  public function __construct() {
    $this->result = array();
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    $this->bookingsWithoutCvc();
    $this->check_overbooking();
    $this->check_pendienteCobro();
    $this->check_customPricesOtaGateway();
//       $this->check_customMinStayWubook();
    $this->check_customMinStayOtaGateway();
  }

  private function check_overbooking() {

    $endDate = date('Y-m-d', strtotime('+6 month'));
    $startDate = date('Y-m-d', strtotime('-1 week'));
   
    $overbooking = [];


    $rooms = Rooms::select('id')->where('state', 1)->get();
    foreach ($rooms as $room) {


      $booksCount = book::where_type_book_reserved()
        ->where('room_id', $room->id)
        ->where('start', '>=', $startDate)
        ->where('start', '<=', $endDate)
        ->get();

      $aLstDays = [];
      foreach ($booksCount as $b) {


        //Prepara la disponibilidad por día de la reserva
        $startAux = strtotime($b->start);
        $endAux = strtotime($b->finish);

        while ($startAux < $endAux) {
          $aLstDays[date('Y-m-d', $startAux)][] = $b->id;
          $startAux = strtotime("+1 day", $startAux);
        }
      }

      foreach ($aLstDays as $d => $v) {
        if (count($v) > 1) {
          foreach ($v as $bID) {
            $overbooking[] = $bID;
          }
        }
      }
    }



    $overbooking = array_unique($overbooking);

    $oData = ProcessedData::findOrCreate('overbooking');
    $oData->content = json_encode($overbooking);
    $oData->save();
  }

  

  function check_customPricesOtaGateway() {

    $sentUPD = \App\ProcessedData::findOrCreate('sentUPD_OtaGateway');
    $dates = json_decode($sentUPD->content);
    if ($dates) {

      //increment  1 DAY
      $finish = date('Y-m-d', strtotime('+1 day', strtotime($dates->finish)));

      $sendPrices = new \App\Models\prepareDefaultPrices($dates->start, $finish);
      if ($sendPrices->error) {
        echo $sendPrices->error;
        $sentUPD->content = null;
        $sentUPD->save();
      }
      
      foreach (\App\Sites::siteIDs() as $sID){
        $sendPrices->setSiteID($sID);
        $sendPrices->process_OtaGateway();
      }
      $sentUPD->content = null;
      $sentUPD->save();
    } else {
      // Enviar todos los precios una vez al día
      $sendPrice = false;
      $controlDay = \App\ProcessedData::findOrCreate('OtaGateway_DailyPricesControl');
      if (!$controlDay->content){
        $sendPrice = true;
      } else {
        if ($controlDay->content != date('Ymd')){
        $sendPrice = true;
        }
      }
      if ($sendPrice){
        $controlDay->content = date('Ymd');
        $controlDay->save();
        $activeYear = \App\Years::where('active', 1)->first();
        ProcessedData::savePriceUPD_toOtaGateway($activeYear->start_date,$activeYear->end_date);
      }

    }
    
  }

  /**
   * 
   * @param Request $request
   * @return type
   */
  public function check_customMinStayOtaGateway() {

    $sentUPD = \App\ProcessedData::findOrCreate('sentUPD_OtaGateway_minStay');
    $dates = json_decode($sentUPD->content);
    if (!$dates) {//No hay registros que enviar
      // Enviar todos las restrinciones una vez al día
      $sendPrice = false;
      $controlDay = \App\ProcessedData::findOrCreate('OtaGateway_DailyMinStayControl');
      if (!$controlDay->content){
        $sendPrice = true;
      } else {
        if ($controlDay->content != date('Ymd')){
        $sendPrice = true;
        }
      }
      if ($sendPrice){
        $controlDay->content = date('Ymd');
        $controlDay->save();
        $activeYear = \App\Years::where('active', 1)->first();
        ProcessedData::saveMinDayUPD_toOtaGateway($activeYear->start_date,$activeYear->end_date);
      }
      return null;
    }
    $start = $dates->start;
    $today = date('Y-m-d');
    $end = $dates->finish;

    //No se pueden enviar registros anteriores a la fecha actual
    if ($today > $end) {
      $sentUPD->content = null;
      $sentUPD->save();
      return null;
    }

    if ($start < $today)
      $start = $today;

    $oPrepareMinStay = new \App\Models\PrepareMinStay($start, $end);

    if ($oPrepareMinStay->error) {
      $sentUPD->content = null;
      $sentUPD->save();
    }
    
    foreach (\App\Sites::siteIDs() as $sID){
      $oPrepareMinStay->setSiteID($sID); 
      $oPrepareMinStay->process_OtaGateway();
    }

    $sentUPD->content = null;
    $sentUPD->save();
  }

  public function bookingsWithoutCvc() {

    $finish = date('Y-m-d', strtotime('-2 days'));
    $lst = Book::whereIn('type_book',[1,2])
          ->where('finish', '>', $finish)
          ->where('agency', '!=', 4)->pluck('id')->toArray();
    
    if (count($lst)>0){
      $loaded = \App\BookData::whereIn('book_id',$lst)
          ->where('key','creditCard')->pluck('book_id')->toArray();
   
      if (count($loaded)>0){
        foreach ($lst as $k=>$b){
          if (in_array($b, $loaded)) unset($lst[$k]);
        }
      }
    }
    $sentUPD = \App\ProcessedData::findOrCreate('bookings_without_Cvc');
    $sentUPD->content = json_encode($lst);
    $sentUPD->save();
  }

    public function check_pendienteCobro() {

    $finish = date('Y-m-d', strtotime('+15 days'));
    $booksAlarms = \App\Book::where('start', '>=', date('Y-m-d'))
            ->where('start', '<=', $finish)->whereIn('type_book', [1,2])->get();

    $lst = array();
    foreach ($booksAlarms as $b){
      $payment = $b->SumPayments;
        
      $percent = 0;
      if ($payment>0){
        $percentAux = $b->total_price / $payment;
        if ($percentAux>0)  $percent = ceil(100 / $percentAux);
      }
      if ($percent<100){
        $lst[] = $b->id;
      }
    }
     
    $sentUPD = \App\ProcessedData::findOrCreate('alarmsPayment');
    $sentUPD->content = json_encode($lst);
    $sentUPD->save();
  }
}

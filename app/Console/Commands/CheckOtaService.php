<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Services\OtaGateway\OtaGateway;
use App\Services\OtaGateway\Config as oConfig;
use Illuminate\Support\Facades\Mail;
use App\ProcessedData;

class CheckOtaService extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'OTAs:CheckService';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Check channels on the otas';

  /**
   * The console command result.
   *
   * @var string
   */
  var $from;
  var $to;
  var $OtaGateway;
  private $oConfig = null;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    $this->result = array();
    $this->oConfig = new oConfig();
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    $resultOta = [];

    foreach (\App\Sites::siteIDs() as $sID) {
      if ($sID == 6) continue;
      $result = $this->channelControl($sID);
      if (count($result) > 0)
        $resultOta[$sID] = $result;
    }
   
    if (count($resultOta) > 0) {
      $oData = ProcessedData::findOrCreate('otasDisconect');
      $oData->content = json_encode($resultOta);
      $oData->save();
      $this->sendMessage($resultOta);
    } else {
      $oData = ProcessedData::findOrCreate('otasDisconect');
      $oData->content = null;
      $oData->save();
    }
  }

  function channelControl($sID) {
    $this->OtaGateway = new \App\Services\OtaGateway\OtaGateway();
    if (!$this->OtaGateway->conect($sID)) {
      $oLog = new \App\LogsData();
      $oLog->infoProceess('OTAs_prices', 'Error al conectarse a la API');
      return ['Ota no conectada'];
    }
    $result = [];
    $lstChannels = $this->OtaGateway->getChannelStatus();
    
    if ($lstChannels && isset($lstChannels->ota_settings)) {
      foreach ($lstChannels->ota_settings as $ch) {
        if ($ch->ota_id == "airbnb") continue;
        $last_push_date = ' [última conección ' . $ch->last_connection_date.']';
        switch ($ch->status) {
          case 1:
            break;
          case 2:
            $result[] = $ch->ota_id . " <b>requires settings</b> (associations and rates, rooms) $last_push_date";
            break;

          case 3:
            $result[] = $ch->ota_id . " <b>is stopped</b> $last_push_date";
            break;

          case 4:
            $result[] = $ch->ota_id . " <b>is off</b> $last_push_date";
            break;

          case 5:
            $result[] = $ch->ota_id . " <b>in the process of connection</b>, actions are expected on the OTA side $last_push_date";
            break;
        }
      }
    } else {
      $result[] = "<b>Error al acceder a la OTA</b>";
    }


    $this->OtaGateway->disconect($sID);

    return $result;
  }

  //-----------------------------------------------------------

  private function sendMessage($result) {
    $subject = 'Atención: Control de OTAs Conección';

    $mailContent = '<h3>Los siguientes Channels deben controlarse:</h3>';

    $sNames = \App\Sites::allSites();

    foreach ($result as $key => $value) {
      $mailContent .= '<p><b>' . $sNames[$key] . '</b></p>';
      $mailContent .= implode('<br/>', $value);
    }



    Mail::send('backend.emails.base', [
        'mailContent' => $mailContent,
        'title' => $subject
            ], function ($message) use ($subject) {
              $message->from(config('mail.from.address'));
              $message->to(config('mail.from.address'));
              //$message->cc('pingodevweb@gmail.com');
              $message->subject($subject);
            });
  }

}

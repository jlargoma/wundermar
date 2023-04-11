<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Webklex\IMAP\Client;
use Illuminate\Support\Facades\Mail;
use App\Services\IMAPService;
use App\MailsLogs;

class ChatEmails extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'mails:read';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Read mails';

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
    $user_id = 'reservas@apartamentosierranevada.net';
    $password = 'exitO@007';
    $email = new IMAPService();
    $connect = $email->connect(
            '{mail.apartamentosierranevada.net:143/novalidate-cert}INBOX', //host
            $user_id, $password
    );
    if ($connect) {

      $inbox = $email->getMessages('html');
      if ($inbox) {
        //check if is new
        $uIDs = array();
        foreach ($inbox as $r) {
          $uIDs[] = $r['uid'];
        }
        $alreadyLst = MailsLogs::select('uid')->whereIn('uid',$uIDs)->get();
        $uID_lst = array();
        if ($alreadyLst){
          foreach ($alreadyLst as $item){
            $uID_lst[] = $item->uid;
          }
        }

        foreach ($inbox as $r) {
          
          if (in_array($r['uid'], $uID_lst)) ///already imported
            continue;
          
          $obj = new MailsLogs();
          $obj->uid = $r['uid'];
          $obj->subject = $r['subject'];
          $obj->address = isset($r['from']['address']) ? $r['from']['address'] : '';
          $obj->from_user = isset($r['from']['name']) ? $r['from']['name'] : '';
          $obj->time_msg = $r['date'];
          $obj->msg = $r['message'];
          $obj->save();
        }
      }

    } else {
      echo json_encode(array("status" => "error", "message" => "Not connect."), JSON_PRETTY_PRINT);
    }
    $email->close();
  }

}

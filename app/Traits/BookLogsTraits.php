<?php

namespace App\Traits;

use Carbon\Carbon;
use App\BookLogs;
use App\User;
use App\MailsLogs;

trait BookLogsTraits {

  public function printBookLogs($bookID,$date=null) {

    $userLst = [];
    $roomLst = [];
    $allLogs = null;
    
    if(!$date){
      $month = date('m');
      $year = date('Y');
    } else {
      $aux = explode('-', $date);
      $month = isset($aux[0]) ? $aux[0] : date('m');
      $year = isset($aux[1]) ? $aux[1] : date('Y');
    }
    


    $book = \App\Book::find($bookID);
    if ($book && $book->customer->email) {

      $dueDateTime = Carbon::createFromFormat('d-m-Y',"01-$month-$year")->addMonth(); 
      if ($book->created_at > $dueDateTime){
        return '<h5>Reserva Creada</h5>';
      }
      $allLogs = BookLogs::where('cli_email', $book->customer->email)
              ->whereMonth('created_at','=',$month)
              ->whereYear('created_at','=',$year)
              ->orderBy('id')->get();
      $user_ids = [];
      
      $room_ids = [];
      if ($allLogs) {
        $auxLst = [];
        foreach ($allLogs as $item) {
          if ($item->book_id && $item->book_id != $bookID){
            continue;
          }
          $user_ids[] = $item->user_id;
          $room_ids[] = $item->room_id;
          $item->content = str_limit(strip_tags($item->content), 50, $end = '...');
          $auxLst[] = $item;
        }
        
        $allLogs = $auxLst;
      }
      
      
      /**
       * Read Emails
       * 
       */
       $MailsLogs = MailsLogs::where('address', $book->customer->email)
              ->whereMonth('time_msg','=',$month)
              ->whereYear('time_msg','=',$year)->get();
      if ($MailsLogs){
        foreach ($MailsLogs as $m){
          $allLogs[strtotime($m->time_msg)] = $m;
        }
      }
      sort($allLogs);
     

      $user_ids = array_unique($user_ids);
      $lstUsers = User::whereIn('id', $user_ids)->get();
      if ($lstUsers) {
        foreach ($lstUsers as $u) {
          $userLst[$u->id] = $u->name;
        }
      }
      $userLst[-1] = "No user";

      $room_ids = array_unique($room_ids);
      $lstRooms = \App\Rooms::whereIn('id', $room_ids)->get();
      if ($lstRooms) {
        foreach ($lstRooms as $r) {
          $roomLst[$r->id] = $r->name;
        }
      }
    }
                      
    return view('backend/planning/listados/bookLogs', [
        'allLogs' => $allLogs,
        'userLst' => $userLst,
        'month'   => intval($month),
        'year'    => $year,
        'roomLst' => $roomLst,
        'date' => date('m-Y', strtotime("-1 month", strtotime("01-$month-$year"))),
        'bookID' => $bookID,
    ]);
  }

  public function sendEmailResponse(\Illuminate\Http\Request $req) {

    $subject = $req->input('subject', 'Repuesta RIAD');
    $mailContent = $req->input('content', null);
    $book_id = $req->input('booking', null);
    $book = \App\Book::find($book_id);
    if ($book) {
      if (isset($book->customer)) {
        $to = $book->customer->email;
        
        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
          $site = \App\Sites::siteData($book->room->site_id);

          $email = \Illuminate\Support\Facades\Mail::send('backend.emails.base', [
              'siteName'    => $site['name'],
              'siteUrl'     => $site['url'],
              'mailContent' => $mailContent,
              'title'       => $subject,
              'agency'      => 0,
          ], function ($message) use ($to, $subject) {
              $message->from(config('mail.from.address'));
              $message->to($to);
              $message->subject($subject);
          });

          if ($email) {
            \App\BookLogs::saveLog($book->id, $book->room_id, $to, 'user_mail_response', $subject, $mailContent);
            return back()->with('success', 'Email enviado');
          }
        }
        return redirect()->back()->withErrors(['Error al enviar el Email: email de destino erroneo']);
      }
      return redirect()->back()->withErrors(['Error al enviar el Email: Cliente no encontrado']);
    }

    return redirect()->back()->withErrors(['Error al enviar el Email: reserva no encontrada']);
  }

  public function getBookLog($id) {
    $bookLog = BookLogs::find($id);
    $user_name = $room_name = '';
    $response = 'empty';
    if ($bookLog) {

      if ($bookLog->room_id) {
        $room = \App\Rooms::find($bookLog->room_id);
        if ($room)
          $room_name = $room->name;
      }

      if ($bookLog->user_id) {
        $user = \App\User::find($bookLog->user_id);
        if ($user)
          $user_name = $user->name;
      }
      
      
      $v = trim($bookLog->content);//so we are sure it is whitespace free at both ends
      //preserve newline for textarea answers
      $aCodes = array("\n",'<b>','</b>','<br>','<strong>','</strong>');
      $aAux   = array('[NEWLINE]','[bold_op]','[bold_cl]','[NEWLINE]','[STRONG_1]','[STRONG_2]');
      
      
      
      $v=str_replace($aCodes,$aAux,$v); 

       //sanitise string
      $v = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);

       //now replace the placeholder with the original newline
      $v=str_replace($aAux,$aCodes,$v); 
      if ( $bookLog->action == 'bookUpd') $v = 'Actualización de la reserva hecha por <b>'.$user_name.'</b>';
        
//        echo $v;
//        echo $bookLog->content;DIE;
      $response =  [
                        'subj' => $bookLog->subject,
                        'room' => $room_name,
                        'user' => $user_name,
                        'date' => $bookLog->created_at->format('d.m.Y H:i'),
                        'content' => nl2br($v)
                ];
    }
    
    
    return response()->json($response);
                   
  }
   public function getMailLog($id) {
    $bookLog = MailsLogs::find($id);
    $user_name = $room_name = '';
    $response = 'empty';
    if ($bookLog) {

      if ($bookLog->from_user) {
          $user_name = $bookLog->from_user;
      }
      
      
      $v = trim($bookLog->msg);//so we are sure it is whitespace free at both ends
      $v = preg_replace('#<head[^>]*>.*?</head>#si', '', $v);
      $v = preg_replace( "/\r|\n\r|\n/", "", $v );
//echo $v;
      //preserve newline for textarea answers
      $aCodes = array("\n",'<b>','</b>','<br>','<strong>','</strong>');
      $aAux   = array('[NEWLINE]','[bold_op]','[bold_cl]','[NEWLINE]','[STRONG_1]','[STRONG_2]');
      
      
      
      $v=str_replace($aCodes,$aAux,$v); 

       //sanitise string
      $v = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK);

       //now replace the placeholder with the original newline
      $v=str_replace($aAux,$aCodes,$v); 
        
//        echo $v;
//        echo $bookLog->content;DIE;
      $response =  [
                        'subj' => $bookLog->subject,
                        'room' => $room_name,
                        'user' => $user_name,
                        'date' => date('d.m.Y H:i', strtotime($bookLog->time_msg)),
                        'content' => nl2br($v)
                ];
    }
    
    
    return response()->json($response);
                   
  }

  public function resendMailLog($id) {
    $bookLog = MailsLogs::find($id);
    $user_name = $room_name = '';
    $response = 'empty';
    if ($bookLog) {
dd($bookLog);
      // $response = $bookLog->msg;
      // $this->sendSimpleMail($subject,$site_id,$email,$message);
    }
    
    
    return response()->json($response);
                   
  }

}

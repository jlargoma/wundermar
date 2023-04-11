<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Auth;

class BookLogs extends Model
{
	
  static function saveLog($book_id,$room_id,$cli_email,$action,$subject='',$content=''){

    $obj = new BookLogs();
    $obj->book_id = $book_id;
    $obj->room_id = $room_id;
    $obj->user_id = (Auth::user()) ? Auth::user()->id : -1;
    $obj->cli_email = $cli_email;
    $obj->action = $action;
    $obj->subject = $subject;
    $obj->content = $content;
    $obj->save();

  }
  static function saveLogStatus($book_id,$room_id,$cli_email,$subject){

    $obj = new BookLogs();
    $obj->book_id = $book_id;
    $obj->room_id = $room_id;
    $obj->user_id = (Auth::user()) ? Auth::user()->id : -1;
    $obj->cli_email = $cli_email;
    $obj->action = 'change_status';
    $obj->subject = $subject;
    $obj->save();

  }
  
  public function user()
  {
    return $this->hasOne('\App\User', 'id', 'user_id');
  }
}

<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Illuminate\Support\Facades\DB;
use App\Services\LogsService;
use App\Traits\BookEmailsStatus;
use Illuminate\Support\Facades\Mail;

class Testing extends Command
{
    use BookEmailsStatus;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Testing:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analizar zona horaria';
    
    
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
      // $this->sLog = new LogsService('schedule','testing');
      // $this->sLog->info('tarea enviada '. date('Y-m-d H:i') );
      // $box = \App\SafetyBox::first();
      // $book = \App\Book::find(5877);
      // $sended = $this->sendSafeBoxMensaje($book, 'book_email_buzon', $box);


      $subject = 'automatic control';
      $message = 'check email';
      $fromMail = config('mail.from.address');
      $fromName = config('mail.from.name');
     $sended = Mail::raw('This is a test message', function($message) use ($fromMail,$fromName) {
        $message->from($fromMail,$fromName);
        $message->to('pingodevweb@gmail.com');
        $message->subject('automatic control');
    });  

      dd($sended);
    }
    
    
}

// /opt/plesk/php/7.2/bin/php artisan Testing:process
//app/Console/Commands/Testing.php
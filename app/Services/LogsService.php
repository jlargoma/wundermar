<?php
namespace App\Services;

      
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogsService{
  
  var $logger;
  
  public function __construct($folder,$channel) {
    $this->logger = new Logger($channel);
    $folder.= date('Ym');
    $this->logger->pushHandler(
            new StreamHandler(storage_path('logs/'.$folder.'.log'), Logger::DEBUG)
            );
  }
  
  public function info($text) {
    $this->logger->info($text);
  }
  public function warning($text) {
    $this->logger->warning($text);
  }
  public function error($text,$context = []) {
    try{
      $this->logger->error($text,$context);
    } catch (\Exception $e){
      //return $e->getMessage();
    }
  }
  
}



<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CacheData {

  var $file;

  public function __construct($folder) {

    $directory = storage_path('cache/');
    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }
    $this->file = $directory . $folder;
  }

  public function set($result) {
    $content = [
      'time'=>time()+300,
      'data'=>$result
    ];
    file_put_contents($this->file,json_encode($content));
  }

  public function get() {
    if (!File::exists($this->file))
      return null;
    $data = File::get($this->file);
    $cache = json_decode($data, true);
    if ($cache){
      if ($cache && $cache['time'] > time()){
        return $cache['data'];
      }
    }
    
    return null;
  }

  public function date() {
    
  }

}
?>
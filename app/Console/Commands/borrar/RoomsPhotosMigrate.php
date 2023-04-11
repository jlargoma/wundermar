<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Rooms;
use App\RoomsPhotos;
use Intervention\Image\ImageManagerStatic as Image;

class RoomsPhotosMigrate extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'RoomsPhotos:create';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create table to rooms photos';

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
    $this->migratePhotos();
    $this->migratePhotosGalleries();
  }

  public function migratePhotosGalleries() {
    $galleries = RoomsPhotos::getGalleries();
    $path = public_path();
    foreach ($galleries as $k=>$name) {
      $rute = "/img/miramarski/apartamentos/" . $k . '/';
      $directory = "$path/img/miramarski/apartamentos/" . $k;
      $thumbnails = $directory . "/thumbnails";
      if (!file_exists($directory)) {
        mkdir($directory, 0775, true);
      }
      if (!file_exists($thumbnails)) {
        mkdir($thumbnails, 0775, true);
      }
      $directorio = dir($directory);
      $i = 0;
      while ($archivo = $directorio->read()) {
        if ($archivo != '.' && $archivo != '..' && $archivo != 'thumbnails') {
          $obj = new RoomsPhotos();
          $obj->room_id = null;
          $obj->file_rute = $rute;
          $obj->file_name = $archivo;
          $obj->status = 'public';
          $obj->position = $i;
          $obj->gallery_key = $k;
          $obj->main = ($i == 0) ? 1 : 0;
          $obj->save();

          //thumbnails
          if (!is_file($thumbnails . '/' . $archivo)) {
            $img = Image::make($directory . '/' . $archivo);
            $img->resize(250, 250, function ($constraint) {
              $constraint->aspectRatio();
            })->save($thumbnails . '/' . $archivo);
          }

          echo $rute . $archivo . "\n";
          $i++;
        }
      }
    }
  }
  public function migratePhotos() {
    $rooms = Rooms::all();
    $path = public_path();
    foreach ($rooms as $room) {
      $rute = "/img/miramarski/apartamentos/" . $room->nameRoom . '/';
      $directory = "$path/img/miramarski/apartamentos/" . $room->nameRoom;
      $thumbnails = $directory . "/thumbnails";
      if (!file_exists($directory)) {
        mkdir($directory, 0775, true);
      }
      if (!file_exists($thumbnails)) {
        mkdir($thumbnails, 0775, true);
      }
      $directorio = dir($directory);
      $i = 0;
      while ($archivo = $directorio->read()) {
        if ($archivo != '.' && $archivo != '..' && $archivo != 'thumbnails') {
          $obj = new RoomsPhotos();
          $obj->room_id = $room->id;
          $obj->file_rute = $rute;
          $obj->file_name = $archivo;
          $obj->status = 'public';
          $obj->position = $i;
          $obj->main = ($i == 0) ? 1 : 0;
          $obj->save();

          //thumbnails
          if (!is_file($thumbnails . '/' . $archivo)) {
            $img = Image::make($directory . '/' . $archivo);
            $img->resize(250, 250, function ($constraint) {
              $constraint->aspectRatio();
            })->save($thumbnails . '/' . $archivo);
          }

          echo $rute . $archivo . "\n";
          $i++;
        }
      }
    }
  }

}

<?php

namespace App\Http\Controllers;

use App\Repositories\CachedRepository;
use App\Rooms;
use Illuminate\Http\Request;
use App\Http\Requests;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mail;
use File;
use PDF;
use App\RoomsPhotos;
use Intervention\Image\ImageManagerStatic as Image;

class RoomsController extends AppController {

  /**
   * @var CachedRepository
   */
  private $repository;
  
  private $otaConfig;

  /**
   * RoomsController constructor.
   * @param CachedRepository $repository
   */
  public function __construct(CachedRepository $repository) {
    $this->repository = $repository;
    $this->otaConfig = new \App\Services\OtaGateway\Config();
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index() {
    
    $sites = \App\Sites::all();
    $rooms =  Rooms::with('RoomsType')->orderBy('order', 'ASC')->get();
    return view('backend/rooms/index', [
        'rooms' => $rooms,
        'roomsdesc' => Rooms::where('state', 1)->orderBy('order', 'ASC')->get(),
        'sizes' => \App\SizeRooms::all(),
        'types' => \App\TypeApto::all(),
        'owners' => \App\User::all(),
        'otaAptos' => $this->otaConfig->getRoomsName(),
        'sites' => $sites,
        'site' => null,
        'channel_group' => null,
    ]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function galleries($id=null) {
    $rooms = \App\RoomsType::all();
    $sites = \App\Sites::all();
    $siteLst = [];
    
    $roomsChannels = $this->otaConfig->getRoomsName();
    $obj = null;
    if ($id){
      $obj = \App\RoomsType::find($id);
    }
    
    foreach ($sites as $s) $siteLst[$s->id] = $s->name;
    
    return view('backend/rooms/galleries', ['rooms'=> $rooms,'obj'=>$obj,'sites'=>$siteLst,'rChannels'=>$roomsChannels]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request) {
    $room = new Rooms();


    if ($request->input('luxury') == "on") {
      $luxury = 1;
    } else {
      $luxury = 0;
    }
    $room->name = $request->input('name');
    $room->nameRoom = $request->input('nameRoom');
    $room->minOcu = $request->input('minOcu');
    $room->maxOcu = $request->input('maxOcu');
    $room->owned = $request->input('owner');
    $room->typeApto = $request->input('type');
    $room->sizeApto = $request->input('sizeRoom');
    $room->site_id = $request->input('site_id');
    $room->profit_percent = 0;
    $room->luxury = $luxury;
    $room->order = 99;
    $room->state = 1;
    $room->cost = 1;

    // $directory =public_path()."/img/miramarski/galerias/".$room->nameRoom;
    // if (!file_exists($directory)) {
    //     mkdir($directory, 0777, true);
    // }

    if ($room->save()) {
      return redirect()->action('RoomsController@index');
    }
  }

  public function createType(Request $request) {
    $existTypeRoom = \App\TypeApto::where('name', $request->input('name'))->count();
    if ($existTypeRoom == 0) {
      $roomType = new \App\TypeApto();

      $roomType->name = $request->input('name');

      if ($roomType->save()) {
        return redirect()->action('RoomsController@index');
      }
    } else {
      echo "Ya existe este tipo de apartamento";
    }
  }

  public function createSize(Request $request) {
    $existTypeRoom = \App\SizeRooms::where('name', $request->input('name'))->count();
    if ($existTypeRoom == 0) {
      $roomType = new \App\SizeRooms();

      $roomType->name = $request->input('name');

      if ($roomType->save()) {
        return redirect()->action('RoomsController@index');
      }
    } else {
      echo "Ya existe este tipo de apartamento";
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  int                      $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request) {
    $id = $request->input('id');
    $roomUpdate = Rooms::find($id);

    $roomUpdate->luxury = $request->input('luxury');
    $roomUpdate->minOcu = $request->input('minOcu');
    $roomUpdate->maxOcu = $request->input('maxOcu');
    $roomUpdate->cost   = $request->input('cost');


    if ($roomUpdate->save()) {
      return 'OK';
    }
    return 'ERROR';
  }

  public function updateType(Request $request) {
    $id = $request->id;
    $roomUpdate = Rooms::find($id);


    $roomUpdate->typeApto = $request->tipo;


    if ($roomUpdate->save()) {
      echo "Cambiada!!";
    }
  }

  public function updateOwned(Request $request) {
    $id = $request->id;
    $roomUpdate = Rooms::find($id);
    $roomUpdate->owned = $request->owned;


    if ($roomUpdate->save()) {
      echo "Cambiada!!";
    }
  }

  // Funcion para cambiar el nombre del apartamento
  public function updateName(Request $request) {
    $id = $request->id;
    $roomUpdate = Rooms::find($id);
    $roomUpdate->name = $request->name;
    if ($roomUpdate->save()) {
      
    }
  }

  // Funcion para cambiar el nombre del apartamento
  public function updateNameRoom(Request $request) {
    $id = $request->id;
    $roomUpdate = Rooms::find($id);
    $roomUpdate->nameRoom = $request->nameRoom;
    if ($roomUpdate->save()) {
      
    }
  }

  // Funcion para cambiar el orden
  public function updateOrder(Request $request) {
    $id = $request->id;
    $roomUpdate = Rooms::find($id);
    $roomUpdate->order = $request->orden;
    if ($roomUpdate->save()) {
      
    }
  }

  // Funcion para cambiar el Tamaño
  public function updateSize(Request $request) {
    $id = $request->id;
    $roomUpdate = Rooms::find($id);
    $roomUpdate->sizeApto = $request->size;
    if ($roomUpdate->save()) {
      
    }
  }

  // Funcion para cambiar el parking
  public function updateParking(Request $request) {
    $id = $request->id;
    $roomUpdate = Rooms::find($id);
    $roomUpdate->parking = $request->parking;
    if ($roomUpdate->save()) {
      
    }
  }

  // Funcion para cambiar la Taquilla
  public function updateTaquilla(Request $request) {
    $id = $request->id;
    $roomUpdate = Rooms::find($id);
    $roomUpdate->locker = $request->taquilla;
    if ($roomUpdate->save()) {
      
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   * @return \Illuminate\Http\Response
   */
  public function state(Request $request) {
    $room = Rooms::find($request->id);
    $state = $request->input('state');
    $room->state = intval($state);
   
    if ($room->save()) {
      return 1;
    }
    
    $book = \App\Book::where('room_id', '=', $request->id)->where('start', '>', '2017-09-01')->get();

    if (count($book) > 0) {
      return 0;
    } else {
      $room->state = $request->state;
      if ($room->save()) {
        return 1;
      }
    }
  }

  public static function getPaxPerRooms($roomId) {
    return Rooms::select('minOcu')->where('id', $roomId)->first()->minOcu ?? null;
    return \Cache::remember("pax_from_room_{$roomId}", 5 * 24 * 60, function () use ($roomId) {
              return Rooms::select('minOcu')->where('id', $roomId)->first()->minOcu ?? null;
            });
  }

  public static function getLuxuryPerRooms($room) {

    $room = Rooms::where('id', $room)->first();
    // echo "$room->luxury";
    return $room->luxury;
  }

  public function photo($id) {
    $room = Rooms::where('nameRoom', $id)->first();
    $photos = null;
    if ($room) {
      $photos = RoomsPhotos::where('room_id', $room->id)->orderBy('position')->get();
    }

    return view('backend/rooms/photo', [
    'photos' => $photos,
    'roomName' => $id,
    'key_gal' => null,
    ]);
  }
  public function gallery($id) {
    
    $photos = RoomsPhotos::where('gallery_key', $id)->orderBy('position')->get();
    return view('backend/rooms/photo', [
    'photos' => $photos,
    'key_gal' => $id,
    'roomName' => null,
    ]);
  }
  public function headers($type,$id) {
    
    if ($type == 'room_type')
      $photo = \App\RoomsHeaders::where('room_type_id', $id)->first();
    if ($type == 'room')
      $photo = \App\RoomsHeaders::where('room_id', $id)->first();
    
    if ($type == 'edificio') 
      $photo = \App\RoomsHeaders::where('url', 'edificio')->first();
    if ($type == 'default') 
      $photo = \App\RoomsHeaders::where('url', 'default')->first();
    return view('backend/rooms/header-img', [
    'photo' => $photo,
    'id' => $id,
    'type' => $type,
    'roomName' => null,
    ]);
  }
  
  

  public function uploadFile(Request $request, $id) {

    $file = ($_FILES);

    $room = Rooms::where('nameRoom', $id)->first();

    $directory = public_path() . "/img/miramarski/apartamentos/" . $room->nameRoom;
    $directoryThumbnail = public_path() . "/img/miramarski/apartamentos/" . $room->nameRoom . "/thumbnails";

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    if (!file_exists($directoryThumbnail)) {
      mkdir($directoryThumbnail, 0777, true);
    }
    // RECORREMOS LOS FICHEROS
    for ($i = 0; $i < count($_FILES['uploadedfile']['name']); $i++) {
      //Obtenemos la ruta temporal del fichero
      $fichTemporal = $_FILES['uploadedfile']['tmp_name'][$i];

      //Si tenemos fichero procedemos
      if ($fichTemporal != "") {
        //Definimos una ruta definitiva para guardarlo
        $destino = $directory . "/" . $_FILES['uploadedfile']['name'][$i];

        //Movemos a la ruta final
        if (move_uploaded_file($fichTemporal, $destino)) {
          //imprimimos el nombre del archivo subido
          printf("Se ha subido el fichero %s.", $_FILES['uploadedfile']['name'][$i]);
          $status = true;
        } else {
          $statu = false;
        }

        $destino = $directoryThumbnail . "/" . $_FILES['uploadedfile']['name'][$i];

        //Movemos a la ruta final
        if (move_uploaded_file($fichTemporal, $destino)) {
          //imprimimos el nombre del archivo subido
          printf("Se ha subido el fichero %s.", $_FILES['uploadedfile']['name'][$i]);
          $status = true;
        } else {
          $statu = false;
        }
      }
    }

    if ($status) {
      return redirect()->action('RoomsController@index');
    } else {
      return redirect()->action('RoomsController@index');
    }
  }

  public function deletePhoto(Request $request, $id) {

    $archivo = public_path() . "/img/miramarski/apartamentos/" . $request->input('apto') . "/" . $id;

    if (unlink($archivo)) {
      return "OK";
    } else {
      return "no se puede eliminar";
    }
  }
  
  /**
   * Remove item image
   * @param Request $request
   * @return type
   */
  public function deletePhotoItem(Request $request) {

    $id = $request->input('id',null);
    if ($id){
      $photo = RoomsPhotos::find($id);
      if ($photo){
        
        $rute = public_path() . $photo->file_rute;
        $imagename = $photo->file_name;
        $photo->delete();
        $file = $rute.'/'.$imagename;
        $file2 = $rute.'/thumbnails/'.$imagename;
        $error = false;
        if (is_file($file)){
          if (!unlink($file)) {
            $error = true;
          }
        }
        if (is_file($file2)){
          if (!unlink($file2)) {
            $error = true;
          }
        }
     
        if ($error) {
          return response()->json(['status'=>'del_error','msg'=>"Imagen no encontrado"]);
        } else {
          return response()->json(['status'=>'ok']);
        }
      }
    }
    
    return response()->json(['status'=>'false','msg'=>"Objeto no encontrado"]);
  }
  /**
   * Mark a photo as principal
   * 
   * @param Request $request
   * @return type
   */
  public function photoIsMain(Request $request) {

    $id = $request->input('id',null);
    if ($id){
      $photo = RoomsPhotos::find($id);
      if ($photo){
        
        if ($photo->gallery_key){
          RoomsPhotos::where('gallery_key',$photo->gallery_key)->update(['main' => 0]);
        }
        if ($photo->room_id){
          RoomsPhotos::where('room_id',$photo->room_id)->update(['main' => 0]);
        }
        
        $photo->main = 1;
        $photo->save();

        return response()->json(['status'=>'ok']);
       
      }
    }
    
    return response()->json(['status'=>'false','msg'=>"Objeto no encontrado"]);
  }

  public function email($id) {

    $user = \App\User::find($id);
    $rooms = Rooms::where('owned', $id)->get();
    return view('backend/emails/_emailingToOwned', [
        'user' => $user,
        'rooms' => $rooms,
    ]);
  }

  public function sendEmailToOwned(Request $request) {
    ini_set('max_execution_time', 3600);
    $user = \App\User::find($request->input('user'));
    if ($request->input('attachment') == 1) {
      $room = Rooms::where('owned', $user->id)->first();
      $path = public_path('/contratos/contrato-comercializacion-' . $room->nameRoom . '-' . $user->name . '.pdf');
      $data = [
          'user' => $user,
          'room' => $room,
      ];
      $pdf = PDF::loadView('backend.ownedContract', compact('data'))->save($path);

      Mail::send('backend.emails.accesoPropietario', ['data' => $request->input()], function ($message) use ($user, $room) {
        $message->from(config('mail.from.address'));
        $message->attach(public_path('/contratos/contrato-comercializacion-' . $room->nameRoom . '-' . $user->name_business . '.pdf'));
        $message->to($user->email);
        $message->subject('Datos de acceso para '. config('app.name'));
      });
    } else {
      Mail::send('backend.emails.accesoPropietario', ['data' => $request->input()], function ($message) use ($user) {
        $message->from(config('mail.from.address'));
        $message->to($user->email);
        $message->subject('Datos de acceso para '. config('app.name'));
      });
    }
    return redirect()->back();
  }

  public function downloadContractoUser($userId, Request $request) {
    $user = \App\User::find($userId);
    $room = Rooms::where('owned', $user->id)->first();
    $path = public_path('/contratos/contrato-comercializacion-' . $room->nameRoom . '-' . $user->name . '.pdf');
    $data = [
        'user' => $user,
        'room' => $room,
    ];
    $pdf = PDF::loadView('backend.ownedContract', compact('data'))->save($path);
    return $pdf->download('contrato-comercializacion-' . $room->nameRoom . '-' . $user->name_business . '.pdf');

    //return response()->file(public_path("contrato-comercializacion-17-18.pdf"));
  }

  public function assingToBooking(Request $request) {
    $room = Rooms::find($request->id);

    if ($request->assing == 1) {

      if ($room->isAssingToBooking()) {
        return "Este apto. ya esta cedido a booking";
      } else {

        $date = Carbon::now();

        if ($date->copy()->format('n') >= 9) {
          $start = new Carbon('first day of September ' . $date->copy()->format('Y'));
        } else {
          $start = new Carbon('first day of September ' . $date->copy()->subYear()->format('Y'));
        }


        $bookToAssign = new \App\Book();

        $bookToAssign->user_id = 39;
        $bookToAssign->customer_id = 1818;
        $bookToAssign->room_id = $room->id;
        $bookToAssign->start = $start->format('Y-m-d');
        $bookToAssign->finish = $start->copy()->addMonths(9)->format('Y-m-d');
        $bookToAssign->comment = "";
        $bookToAssign->book_comments = "";
        $bookToAssign->type_book = 9;
        $bookToAssign->pax = 1;
        $bookToAssign->nigths = 121;
        $bookToAssign->agency = 0;
        $bookToAssign->PVPAgencia = 0;
        $bookToAssign->sup_limp = 0;
        $bookToAssign->sup_park = 0;
        $bookToAssign->type_park = 0;
        $bookToAssign->cost_park = 0;
        $bookToAssign->type_luxury = 2;
        $bookToAssign->sup_lujo = 0;
        $bookToAssign->cost_lujo = 0;
        $bookToAssign->cost_apto = 0;
        $bookToAssign->cost_total = 0;
        $bookToAssign->total_price = 0;
        $bookToAssign->real_price = 0;
        $bookToAssign->total_ben = 0;
        $bookToAssign->extraPrice = 0;
        $bookToAssign->extraCost = 0;
        //Porcentaje de beneficio
        $bookToAssign->inc_percent = 0;
        $bookToAssign->ben_jorge = 0;
        $bookToAssign->ben_jaime = 0;

        if ($bookToAssign->save()) {
          echo "Assignado a booking";
        } else {
          echo "Error al crear el bookeo";
        }
      }
    } else {

      $books = \App\Book::where('room_id', $request->id)->where('type_book', 9)->get();
      foreach ($books as $key => $book) {
        $book->delete();
      }

      $redo = \App\Book::where('room_id', $request->id)->where('type_book', 9)->get();
      if (count($redo) == 0)
        echo "Bloqueo borrado!";
    }
  }

  public function percentApto(Request $request) {
    $typesApto = \App\TypeApto::all();

    return view('backend/rooms/typesApto', ['typesApto' => $typesApto]);
  }

  public function updatePercent(Request $request) {
    $typeApto = \App\TypeApto::find($request->input('id'));
    $tipo = $request->input('tipo');
    $percent = $request->input('percent');

    if (preg_match('/jorge/i', $tipo)) {
      $typeApto->PercentJorge = $percent;
      if ($typeApto->save()) {
        return "ok";
      }
    } else {
      $typeApto->PercentJaime = $percent;
      if ($typeApto->save()) {
        return "ok";
      }
    }
  }

  public function saveupdate(Request $request) {


    $room = Rooms::find($request->input('id'));
    $room->name = ($request->input('name')) ? $request->input('name') : $room->name;
    $room->nameRoom = ($request->input('nameRoom')) ? $request->input('nameRoom') : $room->nameRoom;
    $room->minOcu = ($request->input('minOcu')) ? $request->input('minOcu') : $room->minOcu;
    $room->maxOcu = ($request->input('maxOcu')) ? $request->input('maxOcu') : $room->maxOcu;
    $room->owned = ($request->input('owned')) ? $request->input('owned') : $room->owned;
    $room->typeApto = ($request->input('type')) ? $request->input('type') : $room->typeApto;
    $room->sizeApto = ($request->input('sizeApto')) ? $request->input('sizeApto') : $room->sizeApto;
    $room->parking = ($request->input('parking')) ? $request->input('parking') : $room->parking;
    $room->locker = ($request->input('locker')) ? $request->input('locker') : $room->locker;
    $room->profit_percent = ($request->input('profit_percent')) ? $request->input('profit_percent') : $room->profit_percent;
    $room->description = ($request->input('description')) ? $request->input('description') : $room->description;
    $room->num_garage = ($request->input('num_garage')) ? $request->input('num_garage') : $room->num_garage;
    $room->site_id = $request->input('site_id');
    $room->channel_group = $request->input('channel_group',null);
    $room->price_extra_pax = $request->input('price_extra_pax',null);
    $room->cost = $request->input('cost',null);

    $room->meta_title = $request->input('meta_title', null);
    $room->meta_descript = $request->input('meta_descript', null);
    
    if ($room->save()) {
      return redirect()->action('RoomsController@index');
    }
  }

  public function getUpdateForm(Request $request) {
    return view('backend/rooms/_updateFormRoom', 
            ['room' => Rooms::find($request->id),
             'otaAptos' => $this->otaConfig->getRoomsName()]);
  }

  public function searchByName(Request $request) {
    
    $sqlRooms = Rooms::where('state', 1);
    $sqlRoomsdesc = Rooms::where('state', 0);
    if ($request->searchString){
      $searchString = trim($request->searchString);
      $sqlRooms->where(function ($query) use ($searchString) {
              $query->where('name', 'LIKE', '%' . $searchString . '%')
                      ->orWhere('nameRoom', 'LIKE', '%' . $searchString . '%');
            });
      $sqlRoomsdesc->where(function ($query) use ($searchString) {
              $query->where('name', 'LIKE', '%' . $searchString . '%')
                      ->orWhere('nameRoom', 'LIKE', '%' . $searchString . '%');
            });
    }
    
    if ($request->channel_group){
       $channel_group = trim($request->channel_group);
       
       $sqlRooms->where('channel_group',$channel_group);
       $sqlRoomsdesc->where('channel_group',$channel_group);
    }
    
    if ($request->channel_site){
       $channel_site = trim($request->channel_site);
       
       $sqlRooms->where('site_id',$channel_site);
       $sqlRoomsdesc->where('site_id',$channel_site);
    }
    
    
    $rooms = $sqlRooms->orderBy('order', 'ASC')->get();
    $roomsdesc = $sqlRoomsdesc->orderBy('order', 'ASC')->get();

    return view('backend/rooms/_tableRooms', [
        'rooms' => $rooms,
        'roomsdesc' => $roomsdesc,
        'show' => 1,
    ]);
  }

  public function getImagesRoom(Request $request, $id = "", $bookId = "") {
    ini_set('max_execution_time', 300);
    if ($id != '') {
      $room = Rooms::find($id);
      $photos = null;
      if ($room) {
        $photos = RoomsPhotos::where('room_id', $room->id)->orderBy('position')->get();
        $book = ($bookId != "") ? \App\Book::find($bookId) : null;
        
        return view('backend/rooms/_imagesByRoom', [
            'photos' => $photos,
            'room' => $room,
            'book' => $book,
        ]);
      } else {
        return '<h2 class="text-center">NO HAY IMAGENES PARA ESTE APTO.</h2>';
      }
    } else {
      return '';
    }
  }

  public function sendImagesRoomEmail(Request $request) {
    ini_set('max_execution_time', 300);

    $email = $request->email;
    $room = Rooms::find($request->roomId);
    $aSite = \App\Sites::siteData($room->site_id);
    $path = public_path() . '/img/miramarski/apartamentos/' . $room->nameRoom . '/';
    $images = RoomsPhotos::where('room_id', $room->id)->orderBy('position')->get();
   
    if ($images && $images->count()>0 ){
     
      $aptoName = $room->nameRoom. ' ('.$room->sizeRooms->name.')';
      $messaj = 'Hola, te adjunto las fotos del apartamento de tu reserva:<p>';
      $messaj .= '<p><b>Apartamento:</b> '.$aptoName.'</p><br>'.$room->description;
      
      $title = 'Imagenes del apartamento ' . $aptoName;
      $send = Mail::send('backend.emails.base', ['mailContent' => $messaj,'title'=>$title], function ($message) use ($email, $images, $title, $path,$aSite) {
                $message->from($aSite['mail_from']);
                foreach ($images as $key => $image):
                  if (file_exists($path . $image->file_name))
                    $message->attach($path . $image->file_name);
                endforeach;
                $message->to($email);
                $message->subject($title);
              });
      if ($send){
        echo "EMAIL SALIENDO";
        \App\BookLogs::saveLog($request->input('register',-1),$request->roomId,$email,'sendImagesRoomEmail');
      }

      $log = new \App\LogImages();
      $log->email = $email;
      $log->room_id = $room->id;
      $log->admin_id = Auth::user()->id;
      if ($request->register != 0) {
        $log->book_id = $request->register;
      }
      $log->save();

      if (isset($request->returned))
        return redirect()->back();
    } else {
      echo "No exite el directorio";
    }
  }
  
    public function uploadHeaderFile(Request $request) {

    $id  = $request->input('id',null);
    $type  = $request->input('type',null);
    
    if ($type == 'room_type')
      $obj = \App\RoomsHeaders::where('room_type_id', $id)->first();
    if ($type == 'room')
      $obj = \App\RoomsHeaders::where('room_id', $id)->first();
    if ($type == 'edificio' || $type == 'default')
      $obj = \App\RoomsHeaders::where('url', $id)->first();
    
    if (!$obj){
      $obj = new \App\RoomsHeaders();
      if ($type == 'room_type') $obj->room_type_id = $id;
      if ($type == 'room') $obj->room_id = $id;
      if ($type == 'edificio'  || $type == 'default') $obj->url = $id;
    }
    
//    $key = ($type == 'room_type') ? '-category-' : '-room-';
    $key = '-'.time().'-';
    $rute = "/img/miramarski/apartamentos/headers";
    $directory = public_path() . $rute;
    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }
    /** Upload FILES */
    if($request->hasfile('img_desktop')){
      $img_desktop = $request->file('img_desktop');
      $extension = explode('.', $img_desktop->getClientOriginalName());
      $imagename_desktop = $id.$key.'img-desktop.'.$extension[count($extension)-1];
      $img = Image::make($img_desktop->getRealPath());
      $width = $img->width();
      if ($width>1024){
        $img->widen(1024);
      }

      $img->save($directory.'/'.$imagename_desktop);
      //Save photo item
      $obj->img_desktop = $rute.'/'.$imagename_desktop;
      $obj->save();
      
    }
    if($request->hasfile('img_mobile')){
      $img_mobile = $request->file('img_mobile');
      $extension = explode('.', $img_mobile->getClientOriginalName());
      $imagename_mobile = $id.$key.'img-mobile.'.$extension[count($extension)-1];
      $img = Image::make($img_mobile->getRealPath());
      $width = $img->width();
      if ($width>1024){
        $img->widen(1024);
      }

      $img->save($directory.'/'.$imagename_mobile);
      //Save photo item
      $obj->img_mobile = $rute.'/'.$imagename_mobile;
      $obj->save();
      
    }
      
    
    return redirect()->back()->with('success', 'Imágenes Guardadas');  
    
   
  }

  
  public function uploadRoomFile(Request $request) {

    $id = $request->input('room',null);
    $key_gal = $request->input('key_gal',null);
    
    if ($key_gal){ //Is a gelleri
      
      $obj = \App\RoomsType::where('gallery_key','=',$key_gal)->get();
      if(!$obj){
        return redirect()->back()->withErrors(['Galería no encontrada']);
      }
      
      $folder   = $key_gal;
      $room_id  = null;
      
    }else{ //is a apto
      
      $room = Rooms::where('nameRoom', $id)->first();
      if (!$room){
        return redirect()->back()->withErrors(['Apto no encontrado']);
      }
      $folder   = $room->nameRoom;
      $room_id  = $room->id;
      
    }

    $rute = "/img/miramarski/apartamentos/" . $folder;
    $directory = public_path() . $rute;
    $directoryThumbnail = public_path() . "/img/miramarski/apartamentos/" . $folder . "/thumbnails";
    $directoryMobile = public_path() . "/img/miramarski/apartamentos/" . $folder . "/mobile";

    if (!file_exists($directory)) {
      mkdir($directory, 0777, true);
    }

    if (!file_exists($directoryThumbnail)) {
      mkdir($directoryThumbnail, 0777, true);
    }
    if (!file_exists($directoryMobile)) {
      mkdir($directoryMobile, 0777, true);
    }
    
    /** Upload FILES */
    
      $this->validate($request, [
	'uploadedfile' => 'required',
        'uploadedfile.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:4086',
      ]);
    
      if($request->hasfile('uploadedfile')){
        foreach($request->file('uploadedfile') as $image){
          $extension = explode('.', $image->getClientOriginalName());
          $newName = implode('-',array_slice($extension,0,count($extension)-1));
          $imagename = preg_replace("/[^a-zA-Z0-9]+/", "-",$newName);
          $imagename .= '.'.$extension[count($extension)-1];
          $destinationPath = $directory;
          $img = Image::make($image->getRealPath())->interlace();
          $width = $img->width();
          if ($width>1024){
            $img->widen(1024);
          }
          $img->save($destinationPath.'/'.$imagename);
//          $img->resize(1024, 1024, function ($constraint) {
//                      $constraint->aspectRatio();
//                  })->save($destinationPath.'/'.$imagename);

          //Save photo item
          $obj = new RoomsPhotos();
          $obj->room_id = $room_id;
          $obj->file_rute = $rute;
          $obj->file_name = $imagename;
          $obj->status = 'public';
          $obj->position = 99;
          $obj->main = 0;
          $obj->gallery_key = $key_gal;
          $obj->save();


          //thumbnail
          $destinationPath = $directoryThumbnail;
          $img = Image::make($image->getRealPath())->interlace();
          $img->resize(250, 250, function ($constraint) {
                      $constraint->aspectRatio();
                  })->save($destinationPath.'/'.$imagename);
                  
          //mobile
          $destinationPath = $directoryMobile;
          $img = Image::make($image->getRealPath())->interlace();
          $img->resize(480, 480, function ($constraint) {
                      $constraint->aspectRatio();
                  })->save($destinationPath.'/'.$imagename);

        }
      }
      return redirect()->back()->with('success', 'Imágenes Guardadas');  
    
   
  }

  public function getCupos(Request $request)
  {
      return view('backend.rooms.modal_cupos_fast_payment', [
          'rooms'     => \App\Rooms::where('state', "!=", 0)->orderBy('order', 'ASC')->get(),
          'sizes'     => \App\SizeRooms::orderBy('order', 'ASC')->get(),
      ]);
  }
    
  function photoOrden(Request $req){
    
    $id =  $req->input('id',null);
    $galley =  $req->input('galley',null);
    $order =  $req->input('order',null);
    $photos = null;
    
    if ($galley){ //Is a gelleri
      
      $obj = new RoomsPhotos();
      if(!$obj->existsGal($galley)){
        return response()->json(['status'=>'false','msg'=>"Objeto no encontrado"]);
      }
      $photos = RoomsPhotos::where('gallery_key', $galley)->get();

    }else{ //is a apto
      
        $room = Rooms::where('nameRoom', $id)->first();
        if (!$room){
          return response()->json(['status'=>'false','msg'=>"Objeto no encontrado"]);
        }
        $photos = RoomsPhotos::where('room_id', $room->id)->get();
    }
    
    
    if ($photos) {
      
      $orderArray = explode('-', $order);
      $orderIDs = [];
      foreach ($orderArray as $k=>$v){
        $orderIDs[$v] = $k;
      }
      foreach ($photos as $p){
        if (isset($orderIDs[$p->id])){
          $p->position = $orderIDs[$p->id];
        } else {
          $p->position = 99;
        }
        $p->save();
      }
        
      return response()->json(['status'=>'ok']);
      
    }
    
    return response()->json(['status'=>'false','msg'=>"Objeto no encontrado"]);
    
    
  }
    /**
   * Get RoomsType description
   * @param int $apto
   * @return json
   */
  public function editDescript($apto) {
    
    $room = \App\RoomsType::find($apto);
    if (!$room){
      return response()->json(['status'=>'false','msg'=>"Apto no encontrado"]);
    }
    return response()->json([
        'result'=>'ok',
        'title'=>$room->title,
        'text'=>$room->description,
        'status'=>$room->status,
        'name'=>$room->name,
        'site'=>$room->site_id,
        ]);
    
    
  }
  /**
   * Get Room front description
   * @param int $apto
   * @return json
   */
  public function editRoomDescript($apto) {
    
    $room = \App\Rooms::find($apto);
    if (!$room){
      return response()->json(['status'=>'false','msg'=>"Apto no encontrado"]);
    }
    return response()->json([
        'result'=>'ok',
        'name'=>$room->name,
        'text'=>$room->content_front,
        ]);
    
    
  }
  
  /**
   * Upd the room from description
   * @param Request $request
   * @return back()
   */
  public function updRoomDescript(Request $request) {
    $apto = $request->input('room', null);
    if ($apto){
      $room = \App\Rooms::find($apto);
      if (!$room){
        return redirect()->back()->withErrors(['Apto no encontrado']);
      }
      $room->content_front = $request->input('apto_descript', null);
      $room->save();
      return redirect()->back()->with('success', 'Registro Guardado');  
    }
    return redirect()->back()->withErrors(['Apto no encontrado']);
    
  }
    /**
   * Upd the RoomsType description or create a new RoomsType
   * @param Request $request
   * @return back()
   */
  public function updDescript(Request $request) {
    $apto = $request->input('room', null);
    $item_nombre = $request->input('item_nombre', null);
    $item_status = $request->input('item_status', null);
    $site_id = $request->input('item_site', null);
    $name = $request->input('item_name', $item_nombre);
    
    if ($apto){ //edit
      $room = \App\RoomsType::find($apto);
      if (!$room){
        return redirect()->back()->withErrors(['Apto no encontrado']);
      }
      if ($item_nombre){
        $room->title = $item_nombre;
      }
      $room->status = $item_status;
      $room->site_id = $site_id;
      $room->channel_group = $request->input('channel_group', null);
      $room->min_pax = $request->input('min_pax', null);
      $room->max_pax = $request->input('max_pax', null);
      $room->summary = $request->input('summary', null);
      $room->description = $request->input('apto_descript', null);
      $room->name = $this->clearTitle($name); 
      $room->meta_title = $request->input('meta_title', null);
      $room->meta_descript = $request->input('meta_descript', null);
      $room->save();
      return redirect()->back()->with('success', 'Registro Guardado');  
      
    } else { //create
      $room = new \App\RoomsType();
      $room->title = $item_nombre;
      $room->status = $item_status;
      $room->site_id = $site_id;
      $room->channel_group = $request->input('channel_group', null);
      $room->min_pax = $request->input('min_pax', null);
      $room->max_pax = $request->input('max_pax', null);
      $room->name = $this->clearTitle($name); 
      $room->summary = $request->input('summary', null);
      $room->description = $request->input('apto_descript', null);
      $room->meta_title = $request->input('meta_title', null);
      $room->meta_descript = $request->input('meta_descript', null);
      $room->save();
      return redirect()->back()->with('success', 'Registro Guardado'); 
    }
    return redirect()->back()->withErrors(['Apto no encontrado']);
    
  }
  
  /**
   * Format a String to Slug
   * @param type $string
   * @return type
   */
  public function clearTitle($string){
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string );
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string );
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string );
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string );
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C'),
        $string
    );
    return str_replace(' ','-', strtolower($string));
}


  public function updateOrderFastPayment(Request $request){
    $id                = $request->id;
    $roomUpdate        = \App\Rooms::find($id);
    $roomUpdate->order_fast_payment = $request->orden;
    if ($roomUpdate->save())
    {
      return view('backend.rooms.table_rooms_order_fast_payment', [
            'rooms'     => \App\Rooms::where('state', "!=", 0)->orderBy('order', 'ASC')->get(),
      ]);
    }
  }

  public function updateSizeAptos(Request $request){
    $id                = $request->id;
    $roomUpdate        = \App\SizeRooms::find($id);
    $roomUpdate->num_aptos_fast_payment = $request->num_aptos;
    if ($roomUpdate->save())
    {
        return view('backend.rooms.table_size_aptos_summary', [
            'sizes'     => \App\SizeRooms::all(),
        ]);
    }
  }

  public function updateFastPayment(Request $request){
    $id                = $request->id;
    $roomUpdate        = \App\Rooms::find($id);
    $roomUpdate->fast_payment = intval($request->state);
    if ($roomUpdate->save()){return 1;} else { return 0;}
  }

 
  function getRoomsType(){
    $aptos = configZodomusAptos();
    $ch_group = [];
    foreach ($aptos as $k=>$v){
      $ch_group[$k]= $v;
      $minPax[$k]  = 0;
      $maxPax[$k]  = 0;
      $slug[$k]    = '';
      $title[$k]    = '';
      $url[$k]    = '';
    }
    
    
    $rooms = \App\RoomsType::all();
    foreach ($rooms as $r){
      $ch = $r->channel_group;
      if (isset($minPax[$ch])) $minPax[$ch] = $r->min_pax;
      if (isset($maxPax[$ch])) $maxPax[$ch] = $r->max_pax;
      if (isset($slug[$ch])) $slug[$ch] = $r->name;
      if (isset($title[$ch])) $title[$ch] = $r->title;
      if (isset($url[$ch])) $url[$ch] = $r->url;
//      dd($r);
    }
//    dd($minPax,$maxPax);
    
    return view('backend.rooms.tableRoomsTypes', compact('ch_group','minPax','maxPax','slug','title','url'));
  }

  public function updRoomsType(Request $request){
    $id   = $request->input('id');
    $type = $request->input('type');
    $val  = $request->input('val');
    
    $oObj = \App\RoomsType::where('channel_group',$id)->first();
    if (!$oObj){
      $oObj = new \App\RoomsType();
      $oObj->channel_group = $id;
    }
    
    switch($type){
      case 'minPax': $oObj->min_pax = intval($val); break;
      case 'maxPax': $oObj->max_pax = intval($val); break;
      case 'slug':   $oObj->name = ($val); break;
      case 'title':  $oObj->title = ($val); break;
      case 'url':  $oObj->url = ($val); break;
    }
      

    if ($oObj->save()) return 'OK';
    return 'Registro no encontrado';
    
  }

}

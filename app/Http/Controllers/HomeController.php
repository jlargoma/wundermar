<?php

namespace App\Http\Controllers;

use App\Classes\Mobile;
use Auth;
use Carbon\Carbon;
use File;
use Illuminate\Http\Request;
use Mail;
use Route;
use URL;
use App\Contents;
use App\Sites;
class HomeController extends AppController
{
    const minDays = 2;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $host = $request->getHost();
        if ($host == 'riad.virtual' || strpos($host,'admin.') !== false){
          return redirect()->route('dashboard.planning');
        }
        global $is_mobile;
        return $this->blog();
        
        $site = Sites::where('url',$host)->first();
        if ($site){
          $is_mobile = config('app.is_mobile');
        
          $oContents = new Contents();

          $sliderHome = $oContents->getContentByKey('slider_home',false);
          return view('frontend.home', [
              'slidesEdificio' => null,
              'is_mobile' => $is_mobile,
              'sliderHome' => $sliderHome,
              'edificio' => $oContents->getContentByKey('edificio',false)
          ]);
        }
         return $this->blog();

    }

    
    private function getRoomData($url,$room) {
      $aptoHeading       = ($room->luxury) ? $room->sizeRooms->name  : $room->sizeRooms->name;
      $aptoHeadingMobile = ($room->luxury) ? $room->sizeRooms->name  : $room->sizeRooms->name;
      $typeApto          = $room->sizeRooms->id;
      $directory = public_path() . "/img/miramarski/apartamentos/" . $room->nameRoom.'/';
      $directoryThumbnail = "/img/miramarski/apartamentos/" . $room->nameRoom . "/thumbnails/";

      $photos = null;
      if ($room) {
        $photos = \App\RoomsPhotos::where('room_id', $room->id)->orderBy('main','DESC')->orderBy('position')->get();
      }
      
      
      $oPhotoHeader = \App\RoomsHeaders::where('room_id', $room->id)->first();
      if (!$oPhotoHeader){
        $oPhotoHeader = \App\RoomsHeaders::where('url', 'default')->first();
      }
      $photoHeader = asset('/frontend/images/home/aptos-tit.png');
      if ($oPhotoHeader){
        if (config('app.is_mobile')){
          $aux = public_path().$oPhotoHeader->img_mobile;
          if (is_file($aux)){
            $photoHeader = $oPhotoHeader->img_mobile;
          }
        } else {
          $aux = public_path().$oPhotoHeader->img_desktop;
          if (is_file($aux)){
            $photoHeader = $oPhotoHeader->img_desktop;
          }
        }
      }
//    dd('asdff');
       return view('frontend.pages.fotos', [
              'photos'            => $photos,
              'photoHeader'       => $photoHeader,
              'isMobile'          => config('app.is_mobile'),
              'aptoHeading'       => $aptoHeading,
              'aptoHeadingMobile' => $aptoHeadingMobile,
              'typeApto'          => $typeApto,
              'room'              => $room,
              'url'               => $url,
              'directory'         => $directory,
              'directoryThumbnail'=> $directoryThumbnail,
          ]);
       
    }
    
    
    public function apartamento($apto)
    {
        $url  = $apto;
        $apto = str_replace('-', ' ', $apto);
        $apto = str_replace(' sierra nevada', '', $apto);
        $site_id = config('app.site_id');
        $room = \App\Rooms::where('nameRoom', $url)->first();
        
        if ($room)
        {
          return $this->getRoomData($url,$room);
        } else {
          $room = \App\RoomsType::where('name',$url)
                  ->where('status',1)
                  ->where('site_id',$site_id)
                  ->first();
          if ($room){
            
            $aptoHeading = $room->title;
            $photos = \App\RoomsPhotos::where('gallery_key', $room->id)
                    ->orderBy('main','DESC')->orderBy('position')->get();
            
            
            $aptos = array();
            $roomsType = \App\RoomsType::select('name')->where('status',1)->get();
            if ($roomsType){
              foreach ($roomsType as $v){
                $aptos[] = $v->name;
              }
            }
            
            $oPhotoHeader = \App\RoomsHeaders::where('room_type_id', $room->id)->first();
            if (!$oPhotoHeader){
              $oPhotoHeader = \App\RoomsHeaders::where('url', 'default')->first();
            }
            $photoHeader = asset('/frontend/images/home/apart-bg.jpg');

            if ($oPhotoHeader){
              if (config('app.is_mobile')){
                $aux = public_path().$oPhotoHeader->img_mobile;
                if (is_file($aux)){
                  $photoHeader = $oPhotoHeader->img_mobile;
                }
              } else {
                $aux = public_path().$oPhotoHeader->img_desktop;
                if (is_file($aux)){
                  $photoHeader = $oPhotoHeader->img_desktop;
                }
              }
            }
            
            
            return view('frontend.pages.apartamento', [
              'photos'   => $photos,
              'aptoHeading'   => $aptoHeading,
              'photoHeader'   => $photoHeader,
              'isMobile'    => config('app.is_mobile'),
              'room'     => $room,
              'aptos'    => $aptos,
              'url'      => $url,
              'meta_tit' => $room->meta_title,
              'meta_descript' => $room->meta_descript,
            ]);
            
          }
        }

        return redirect('404');
        
    
    }

  
    public function edificio()
    {
      
      $aptos  = [];
      
      $oContents = new Contents();
      
      
      $data = $oContents->getContentByKey('edificio_page',true);
      $arraySlides = [];
      for($i=0;$i<7;$i++){
        if (isset($data['imagen_'.$i]) && trim($data['imagen_'.$i]) != '')
        $arraySlides[] = url($data['imagen_'.$i]);
      }
      
      $oPhotoHeader = \App\RoomsHeaders::where('url', 'edificio')->first();
      $photoHeader = asset('/frontend/images/home/apart-bg.jpg');

      if ($oPhotoHeader){
        if (config('app.is_mobile')){
          $aux = public_path().$oPhotoHeader->img_mobile;
          if (is_file($aux)){
            $photoHeader = $oPhotoHeader->img_mobile;
          }
        } else {
          $aux = public_path().$oPhotoHeader->img_desktop;
          if (is_file($aux)){
            $photoHeader = $oPhotoHeader->img_desktop;
          }
        }
      }
      
//         dd($arraySlides);
      return view('frontend.pages.edificio',[
          'data'     => $data,
          'aptos'    => $aptos,
          'isMobile' => config('app.is_mobile'),
          'slides'      => $arraySlides,
          'photoHeader' => $photoHeader,
              ]);
       
   
      
      
      
      
      $folder = '/img/miramarski/edificio/';
      $slides = File::allFiles(public_path() . $folder);
      if ($slides){
       foreach ($slides as $key => $slide)
        {
            $arraySlides[] = $folder.$slide->getFilename();
        }
        natcasesort($arraySlides);
      }
      
      
     
            
        
      return view('frontend.pages.edificio',[
          'aptoHeading' => 'Edificio Miramar Ski',
          'aptos'       => $aptos,
          'photoHeader' => $photoHeader,
          'slides'      => $arraySlides,
          'isMobile'    => config('app.is_mobile'),
              ]);
    }

    public function contacto()
    {
      $site_id = config('app.site_id');
      $view = ($site_id>0) ? 'frontend.contacto' : 'frontend.contacto-blog';
      return view($view, 
              [
                'mobile' => new Mobile(),
                'site_id'=>$site_id,
              ]
              );
    }

    public function restaurantes()
    {
        return view('frontend.restaurantes', ['mobile' => new Mobile(),]);
    }

    
    // Correos frontend

    public function formContacto(Request $request)
    {
        $data['name']    = $request->input('name');
        $data['email']   = $request->input('email');
        $data['phone']   = $request->input('phone');
        $data['subject'] = $request->input('subject');
        $data['message'] = $request->input('message');
        $contact         = Mail::send(['html' => 'frontend.emails.contact'], ['data' => $data,], function ($message) use ($data) {
            $message->from($data['email'], $data['name']);
            $message->to(config('mail.from.address'));
            $message->subject('Formulario de contacto '. config('app.name'));
        });
        if ($contact)
        {
            return view('frontend.contacto', [
                'mobile'    => new Mobile(),
                'contacted' => 1
            ]);
        } else
        {
            return view('frontend.contacto', [
                'mobile'    => new Mobile(),
                'contacted' => 0
            ]);
        }
    }

    public function formAyuda(Request $request)
    {
        $data['name']    = $request->input('name');
        $data['email']   = $request->input('email');
        $data['phone']   = $request->input('phone');
        $data['subject'] = $request->input('subject');
        $data['message'] = $request->input('message');
        $contact         = Mail::send(['html' => 'frontend.emails.ayuda'], ['data' => $data,], function ($message) use ($data) {
            $message->from($data['email'], $data['name']);
            $message->to(config('mail.from.address')); /* $data['email'] */ // $message->bcc('jlargo@mksport.es');
            // $message->bcc('jlargoma@gmail.com');
            $message->subject('Formulario de Ayudanos a Mejorar '. config('app.name'));
        });
        if ($contact)
        {
            return view('frontend.ayudanos-a-mejorar', [
                'mobile'    => new Mobile(),
                'contacted' => 1
            ]);
        } else
        {
            return view('frontend.ayudanos-a-mejorar', [
                'mobile'    => new Mobile(),
                'contacted' => 0
            ]);
        }
    }

    public function formPropietario(Request $request)
    {
        $data['name']    = $request->input('name');
        $data['email']   = $request->input('email');
        $data['phone']   = $request->input('phone');
        $data['subject'] = $request->input('subject');
        $data['message'] = $request->input('message');
        $contact         = Mail::send(['html' => 'frontend.emails.propietario'], ['data' => $data,], function ($message) use ($data) {
            $message->from($data['email'], $data['name']);
            $message->to(config('mail.from.address')); /* $data['email'] */ // $message->bcc('jlargo@mksport.es');
            // $message->bcc('jlargoma@gmail.com');
            $message->subject('Formulario de Propietario '. config('app.name'));
        });
        if ($contact)
        {
            return view('frontend.eres-propietario', [
                'mobile'    => new Mobile(),
                'contacted' => 1
            ]);
        } else
        {
            return view('frontend.eres-propietario', [
                'mobile'    => new Mobile(),
                'contacted' => 0
            ]);
        }
    }

    public function formGrupos(Request $request)
    {
        $data['name']     = $request->input('name');
        $data['email']    = $request->input('email');
        $data['phone']    = $request->input('phone');
        $data['destino']  = $request->input('destino');
        $data['personas'] = $request->input('personas');
        $data['message']  = $request->input('message');
        $contact          = Mail::send(['html' => 'frontend.emails.grupos'], ['data' => $data,], function ($message) use ($data) {
            $message->from($data['email'], $data['name']);
            $message->to(config('mail.from.address')); /* $data['email'] */ // $message->to('jbaz@daimonconsulting.com'); /* $data['email'] */
            // $message->bcc('jlargo@mksport.es');
            // $message->bcc('jlargoma@gmail.com');
            $message->subject('Formulario de Grupos '. config('app.name'));
        });
        if ($contact)
        {
            return view('frontend.grupos', [
                'mobile'    => new Mobile(),
                'contacted' => 1
            ]);
        } else
        {
            return view('frontend.grupos', [
                'mobile'    => new Mobile(),
                'contacted' => 0
            ]);
        }
    }

    public function form()
    {
        return view('frontend._formBook', ['mobile' => new Mobile()]);
    }

    public function terminos()
    {
        return view('frontend.terminos', ['mobile' => new Mobile()]);
    }

    public function politicaPrivacidad()
    {
        return view('frontend.privacidad', ['mobile' => new Mobile()]);
    }

    public function politicaCookies()
    {
        return view('frontend.cookies', ['mobile' => new Mobile()]);
    }

    public function condicionesGenerales()
    {
        return view('frontend.condiciones-generales', ['mobile' => new Mobile()]);
    }
    public function buzon()
    {
        return view('frontend.buzon', ['mobile' => new Mobile()]);
    }

    public function preguntasFrecuentes()
    {
        return view('frontend.preguntas-frecuentes', ['mobile' => new Mobile()]);
    }

    public function eresPropietario()
    {
        return view('frontend.eres-propietario', ['mobile' => new Mobile()]);
    }

    public function grupos()
    {
        return view('frontend.grupos', ['mobile' => new Mobile()]);
    }

    public function quienesSomos()
    {
        return view('frontend.quienes-somos', ['mobile' => new Mobile()]);
    }

    public function ayudanosAMejorar()
    {
        return view('frontend.ayudanos-a-mejorar', ['mobile' => new Mobile()]);
    }

    public function avisoLegal()
    {
        return view('frontend.aviso-legal', ['mobile' => new Mobile()]);
    }

    public function huesped()
    {
        return view('frontend.huesped', ['mobile' => new Mobile()]);
    }

    public function tiempo()
    {
        $aptos = [
            'apartamento-lujo-sierra-nevada',
            'estudio-lujo-sierra-nevada',
            'apartamento-standard-sierra-nevada',
            'estudio-standard-sierra-nevada'
        ];
        return view('frontend.tiempo', [
            'mobile' => new Mobile(),
            'aptos'  => $aptos,
        ]);
    }

    public function getCitiesByCountry(Request $request)
    {
        return view('frontend.responses._citiesByCountry', [
            'cities' => \App\Cities::where('code_country', $request->code)->orderBy('city', 'ASC')->get()
        ]);
    }

    public function solicitudForfait(Request $request)
    {
        $arrayProducts     = array();
        $commissions       = [];
        $anon_request_text = NULL;
        // die();
        $data             = $request->input();
        $solicitud        = new \App\Solicitudes();
        $solicitud->name  = $data['nombre'];
        $solicitud->email = $data['email'];
        $solicitud->phone = $data['telefono'];
        if (!empty($data['date-entrada1']))
        {
            $solicitud->start = Carbon::createFromFormat('d-m-Y', $data['date-entrada1'])->format('Y-m-d');
        }
        if (!empty($data['date-salida1']))
        {
            $solicitud->finish = Carbon::createFromFormat('d-m-Y', $data['date-salida1'])->format('Y-m-d');
        }
        if (!isset($data['prices']))
        {
            $data['prices'] = [];
        }
        if (isset($data['forfaits']))
        {
            $solicitud->request_forfaits = serialize($data['forfaits']);
        }
        if (isset($data['material']))
        {
            $solicitud->request_material = serialize($data['material']);
        }
        if (isset($data['classes']))
        {
            $solicitud->request_classes = serialize($data['classes']);
        }
        if (isset($data['prices']))
        {
            $solicitud->request_prices = serialize($data['prices']);
        }
        foreach (FortfaitsController::getCommissions() as $commission)
        {
            $commissions[] = $commission->value;
        }
        $solicitud->commissions = serialize($commissions);
        $solicitud->cc_name     = $_POST['cc_name'];
        $solicitud->cc_pan      = $_POST['cc_pan'];
        $solicitud->cc_expiry   = $_POST['cc_expiry'];
        $solicitud->cc_cvc      = $_POST['cc_cvc'];
        if ($solicitud->save())
        {
            if (HomeController::getLastBookByPhone($solicitud->id, trim($data['telefono'])) === false)
            {
                $anon_request_text = '<span style="color:#red; font-weight:bold;">Solicitud Anónima - Consultar en Pestaña Forfaits</span>';
            }
            //print_r($_POST);
            //print_r($solicitud);
            //print_r($data);
            foreach ($data['carrito'] as $key => $carrito)
            {
                $carrito                = ltrim($carrito);
                $producto               = new \App\SolicitudesProductos();
                $producto->id_solicitud = $solicitud->id;
                $producto->name         = $carrito;
                // $producto->price = $solicitud->id;
                if ($producto->save())
                {
                    $arrayProducts[$key] = $producto;
                }
            }
            // echo "<pre>";
            // print_r($request->input());
            // echo "</pre>";
            // return view('frontend.emails._responseSolicitudForfait' ,['solicitud' => $solicitud, 'productos' => $arrayProducts,'data' => $data]);
            // die();
            $emailsTo = [
                'forfait@miramarski.com',
                'escuela@sierranevadaeee.com',
                $data['email']
            ];
            foreach ($emailsTo as $emailTo)
            {
                //         echo $emailTo.'<br/>';
                $arrayProductsCloned = $arrayProducts;
                $data['emailTo']     = $emailTo;
                if ($emailTo == 'escuela@sierranevadaeee.com')
                {
                    $products = [];
                    foreach ($arrayProducts as $key => $arrayProduct)
                    {
                        if (!isset($data['classes'][$key]) && !isset($data['material'][$key]))
                        {
                            unset($arrayProductsCloned[$key]);
                        }
                    }
                }
                //            print_r($arrayProductsCloned);
                if (count($arrayProductsCloned) > 0)
                {
                    $sended = Mail::send(['html' => 'frontend.emails._responseSolicitudForfait'], [
                        'solicitud'         => $solicitud,
                        'productos'         => $arrayProductsCloned,
                        'precios'           => $data['prices'],
                        'anon_request_text' => $anon_request_text,
                        'data'              => $data
                    ], function ($message) use ($data) {
                        $message->from(config('mail.from.address'));
                        $message->to($data['emailTo']);
                        $message->replyTo($data['email']);
                        $message->subject('Solicitud de FORFAIT');
                    });
                }
            }
            /*$sended = Mail::send(['html' => 'frontend.emails._responseSolicitudForfait'], [
               'solicitud' => $solicitud,
               'productos' => $arrayProducts,
               'precios' => $data['prices'],
               'data'      => $data
            ], function ($message) use ($data) {
               $message->from(config('mail.from.address'));
               // $message->to('iavila@daimonconsulting.com');
               // $message->to('joyragdoll@gmail.com');
               // $message->to(config('mail.from.address'));
               // $message->to('escuela@sierranevadaeee.com');
               // $message->to($data['email']);
               $message->replyTo($data['email']);
               $message->subject('Solicitud de FORFAIT');
            });*/
            if ($sended)
            {
                return redirect('/forfait?sended=true');
                //            return redirect()->back();
            }
        }
    }

    public function getForfaitsRequests()
    {
        $requests = \App\Solicitudes::where("enable", "=", 1)->orderBy('id', 'DESC')->get();
        return view('/backend/forfaits/index')->with('requests', $requests);
    }

    public function condicionesContratacion(Request $request)
    {
        return view('frontend.condiciones-contratacion', ['mobile' => new Mobile()]);
    }
    public function condicionesFianza(Request $request)
    {
        return view('frontend.condiciones-fianza', ['mobile' => new Mobile()]);
    }

    public static function getLastBookByPhone($ff_request_id, $phone)
    {
        $customers = \App\Customers::where("phone", "=", "$phone")->orderBy('ID', 'DESC')->take(1)->get();
        foreach ($customers as $customer)
        {
            $books = \App\Book::where("customer_id", "=", "$customer->id")->orderBy('ID', 'DESC')->take(1)->get();
            foreach ($books as $book)
            {
                $db = \App\Book::find($book->id);
                if ($db->ff_request_id == NULL)
                {
                    $db->ff_request_id = $ff_request_id;
                    if ($db->save())
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function thanksYou(Request $request)
    {
        return view('frontend.stripe.stripe', ['mobile' => new Mobile()]);
    }
    public function thanksYou_forfait(Request $request)
    {
        return view('frontend.stripe.forfait', ['mobile' => new Mobile()]);
    }
    public function paymenyError(Request $request)
    {
        return view('frontend.stripe.error', ['mobile' => new Mobile()]);
    }

    public function getPriceBook(Request $request)
    {
      return view('frontend.bookStatus.bookError');
        $aux       = str_replace('Abr', 'Apr', $request->input('fechas'));
        $date      = explode('-', $aux);
        $start     = Carbon::createFromFormat('d M, y', trim($date[0]));
        $finish    = Carbon::createFromFormat('d M, y', trim($date[1]));
        $countDays = $finish->diffInDays($start);
        $aSize = \App\SizeRooms::findSizeApto($request->input('apto'),$request->input('luxury'),$request->input('quantity'));
        
        $sizeRoom = $aSize['sizeRoom'];
        $typeApto = $aSize['typeApto'];

        $size = \App\SizeRooms::find($sizeRoom);
        $getRoomToBook = $this->calculateRoomToFastPayment($size, $start, $finish, $request->input('luxury'));
        $roomAssigned = $getRoomToBook['id'];
        $paxPerRoom = \App\Rooms::getPaxRooms($request->input('quantity'), $roomAssigned);
        $pax        = $request->input('quantity');
        if ($paxPerRoom > $pax)
        {
            $pax = $paxPerRoom;
        }
        
        $room = \App\Rooms::find($roomAssigned);

        if (!$room){
          return view('frontend.bookStatus.bookError');
        }
        
        $price = $room->getPVP($start,$finish,$pax);
        
        if ($price>0){
        
            $costes = $room->priceLimpieza($room->sizeApto);
            $limp   = $costes['price_limp'];


            if ($request->input('parking') == 'si')
            {
                $priceParking = BookController::getPricePark(1, $countDays) * $room->num_garage;
                $parking      = 1;
            } else
            {
                $priceParking = 0;
                $parking      = 2;
            }
            if ($request->input('luxury') == 'si')
            {
                $luxury = BookController::getPriceLujo(1);
            } else
            {
                $luxury = BookController::getPriceLujo(2);
            }

            $total   = $price + $priceParking + $limp + $luxury;
            $dni     = $request->input('dni');
            $address = $request->input('address');
            $setting = \App\Settings::where('key', 'discount_books')->first();
        

            return view('frontend.bookStatus.response', [
                'id_apto'      => $roomAssigned,
                'pax'          => $pax,
                'nigths'       => $countDays,
                'apto'         => $typeApto,
                'name'         => $request->input('name'),
                'phone'        => $request->input('phone'),
                'email'        => $request->input('email'),
                'start'        => $start,
                'finish'       => $finish,
                'parking'      => $parking,
                'priceParking' => $priceParking,
                'luxury'       => $luxury,
                'total'        => $total,
                'dni'          => $dni,
                'address'      => $address,
                'room'         => $room,
                'isFastPayment'         => $getRoomToBook['isFastPayment'],
                'setting'      => ($setting) ? $setting : 0,
                'comment'      => $request->input('comment'),
            ]);
        } else //$pice == 0
        {
          return view('frontend.bookStatus.bookError');
        }
    }
    
    public function blog($name = null) {
      
      
      $oContents = new Contents();
      $data = $oContents->getContentByKey('blog',true);
      if ($name){
        $article = \App\Blog::where('name',$name)->where('status',1)->first();
        if ($article){
          $articles = \App\Blog::where('status',1)->where('id','!=',$article->id)->get();
          return view('frontend/blog/article',['data'=> $data,'obj'=>$article,'lst_art'=>$articles]);
        }
      }
      
      
      
      $articles = \App\Blog::where('status',1)->get();
      $excursions = \App\Excursions::where('status',2)->get();
      
      return view('frontend/blog/index',[
          'data'     => $data,
          'articles' => $articles,
          'excursions' => $excursions,
              ]);
       
    }
    
    
    public function checkPrices() {
      $sites = Sites::allSites();
      $urls = [];
      $oSetting = new \App\Settings();
      foreach ($sites as $id=>$k){
        if ($id>0){
          $url = $oSetting->getLongKeyValue('gha_sitio_'.$id);
          if ($url && strlen(trim($url))>0)   $urls[$id] = $url;
        }
      }
                        
      return view('backend/planning/_consultar-google',['sites'=>$sites,'urls'=>$urls]);
    }
}

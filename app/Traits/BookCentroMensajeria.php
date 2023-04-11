<?php

namespace App\Traits; 
use App\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Book;
use App\BookPartee;
use App\PaymentOrders;
use App\Sites;
use Auth;

trait BookCentroMensajeria {
        /**
         * Get the custom message to Partee
         */
        public function getParteeMsg() {
          //Get Book ID
          $bookID = intval($_REQUEST['bookID']);
          
          //Get Book object
          $book = Book::find($bookID);
          if (!$book){
            die('empty');
          }
          //get BookPartee object
          $BookPartee = BookPartee::where('book_id',$bookID)->first();
          if ($BookPartee){
            //Get msg content
            $url = get_shortlink($BookPartee->link);
            $content = $this->getMailData($book,'SMS_Partee_msg');
            $content = str_replace('{partee}', $url, $content);
            $content = $this->clearVars($content);
            die($content);
          } else {
            die('empty');
          }
        }
        
        /**
         * Send Partee to Finish CheckIn
         * 
         * @param Request $request
         * @return type
         */
        public function finishParteeCheckIn(Request $request) {
          
          $bookID = $request->input('id',null);
          
          $BookPartee = BookPartee::where('book_id',$bookID)->first();
          
          if ($BookPartee){
            
            if ($BookPartee->status == "FINALIZADO"){
              return [
                'status'   => 'danger',
                'response' => "El registro Partee ya se encuentra finalizado."
              ];
            }
            
            if ($BookPartee->status == "HUESPEDES"){
              
              //Create Partee
              $partee = new \App\Services\ParteeService();
              $book = Book::find($bookID);
              $partee->setID(Settings::getParteeBySite($book->room->site_id));
              if ($partee->conect()){

                $result = $partee->finish($BookPartee->partee_id);
                
                if($partee->response && isset($partee->responseCode) && $partee->responseCode == 200) {
                  if ($partee->response->isError){
                     return [
                      'status'   => 'danger',
                      'response' => $partee->response->errorMessage,
                    ];
                  }
                  

                  $BookPartee->status = 'FINALIZADO';
                  $BookPartee->log_data = $BookPartee->log_data .",". time() .'-FINALIZADO';
                  $BookPartee->date_finish = date('Y-m-d H:i:s');
                  $BookPartee->save();
                  return [
                    'status'   => 'success',
                    'response' => "Registro Partee finalizado",
                  ];

                } 
              }
          
              return [
                'status'   => 'danger',
                'response' => $partee->response
              ];
             
            }
            
            return [
              'status'   => 'danger',
              'response' => "El registro Partee aún no está preparado."
            ];
              
          }
          
          return [
            'status'   => 'danger',
            'response' => "No existe el registro solicitado."
          ];
        }
        
                /**
         * Send Partee to Finish CheckIn
         * 
         * @param Request $request
         * @return type
         */
        public function finishParteeMail(Request $request) {
          
          $bookID = $request->input('id',null);
          
          //Get Book object
          $book = Book::find($bookID);
          if (!$book){
            return [
                'status'   => 'danger',
                'response' => "Reserva no encontrada."
              ];
          }
          if (!$book->customer->email || trim($book->customer->email) == ''){
              return [
                'status'   => 'danger',
                'response' => "La Reserva no posee email."
              ];
          }
          $BookPartee = BookPartee::where('book_id',$bookID)->first();
          
          if ($BookPartee){
            
            if ($BookPartee->status == "FINALIZADO"){
              return [
                'status'   => 'danger',
                'response' => "El registro Partee ya se encuentra finalizado."
              ];
            }
            $url = get_shortlink($BookPartee->link);
            $link = '<a href="'.$url.'" title="Ir a Partee">'.$url.'</a>';
            $subject = translateSubject('Recordatorio para Completado de Partee',$book->customer->country);
            $message = $this->getMailData($book,'MAIL_Partee');
            $message = str_replace('{partee}', $link, $message);
            $message = $this->clearVars($message);
//            $message = strip_tags($message);
            $site = Sites::siteData($book->room->site_id);
            $sended = $this->sendMailSite($site,$message,$subject,$book->customer->email,$book->agency);
            \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'MAIL_Partee',$subject,$message);
            if ($sended){
                  $BookPartee->sentSMS=2;
                  $BookPartee->log_data = $BookPartee->log_data .",".time() . '-' .'sentMail';
                  $BookPartee->save();
                  return [
                    'status'   => 'success',
                    'response' => "Registro Partee enviado",
                  ];
              } 
            }
            
            return [
              'status'   => 'danger',
              'response' => "El registro Partee aún no está preparado."
            ];
              
          }
        
        
        /**
         * Send Partee to Finish CheckIn
         * 
         * @param Request $request
         * @return type
         */
        public function finishParteeSMS(Request $request) {
          
          $bookID = $request->input('id',null);
          
          //Get Book object
          $book = Book::find($bookID);
          if (!$book){
            return [
                'status'   => 'danger',
                'response' => "Reserva no encontrada."
              ];
          }
          
          $BookPartee = BookPartee::where('book_id',$bookID)->first();
          
          if ($BookPartee){
            
            if ($BookPartee->status == "FINALIZADO"){
              return [
                'status'   => 'danger',
                'response' => "El registro Partee ya se encuentra finalizado."
              ];
            }
            if ($BookPartee->status == "HUESPEDES"){
              
              
              if ($BookPartee->guestNumber){
                if ($BookPartee->guestNumber == $book->pax){
                  return [
                    'status'   => 'danger',
                    'response' => "El registro Partee ya se encuentra en proceso de finalización."
                  ];
                }
              }
            }
            
            if ($BookPartee->status == "VACIO" || $BookPartee->status == "HUESPEDES"){
              
              //Send SMS
              $SMSService = new \App\Services\SMSService();
              if ($SMSService->conect()){
                $url = get_shortlink($BookPartee->link);
                $message = $this->getMailData($book,'MAIL_Partee');
                $message = str_replace('{partee}', $url, $message);
                $phone = $book->customer['phone'];
                $message = $this->clearVars($message);
                $message = strip_tags($message);
                
                if ($SMSService->sendSMS($message,$phone)){
                  $BookPartee->sentSMS=2;
                  $BookPartee->log_data = $BookPartee->log_data .",".time() . '-' .'sentSMS';
                  $BookPartee->save();
                  return [
                    'status'   => 'success',
                    'response' => "Registro Partee enviado",
                  ];

                } 
              }
          
              return [
                'status'   => 'danger',
                'response' => $SMSService->response
              ];
             
            }
            
            return [
              'status'   => 'danger',
              'response' => "El registro Partee aún no está preparado."
            ];
              
          }
          
          return [
            'status'   => 'danger',
            'response' => "No existe el registro solicitado."
          ];
        }
        
        public function getParteeLst(){
          
          $today = Carbon::now();
          $books = \App\Book::where('start', '>=', $today->copy()->subDays(2))
                  ->where('start', '<=', $today->copy()->addDays(5))
                  ->where('type_book', 2)->orderBy('start', 'ASC')->get();
          
          $payment = array();
            foreach ($books as $key => $book)
            {
                $payment[$book->id] = 0;
                $payments = \App\Payments::where('book_id', $book->id)->get();
                if (count($payments) > 0) 
                  foreach ($payments as $key => $pay) $payment[$book->id] += $pay->import;

            }
          $rooms = \App\Rooms::where('state', '=', 1)->get();
          $mobile    = new \App\Classes\Mobile();
//          $parteeToActive = BookPartee::whereIn('status', ['HUESPEDES',"FINALIZADO"])->get();
//          $parteeToActive = BookPartee::get();
          return view('backend/planning/_alarmsPartee',compact('books','payment','rooms','mobile','today'));
          
        }
        
        public function showSendRemember($bookID) {
          $book = Book::find($bookID);
          $disableEmail = 'disabled';
          $phone = null;
          $linkWSP = '';
          $disablePhone = 'disabled';
          if ($book){
            if ($book->customer->email) $disableEmail = '';
            if ($book->customer->phone){
              $phone = $book->customer->phone;
              $disablePhone = '';
            }
          }
          $partee = BookPartee::where('book_id',$bookID)->first();
          $showInfo = [];
          if ($partee && $partee->partee_id>0 && trim($partee->link) !=''){
            $data = $partee->log_data;
            if ($data){
              preg_match_all('|([0-9])*(\-sentSMS)|', $data, $info);
              if (isset($info[0])){
                foreach ($info[0] as $t){
                  $showInfo[intval($t)] = '<b>SMS</b> enviado el '.date('d/m H:i', intval($t));
                }
              }
              preg_match_all('|([0-9])*(\-sentMail)|', $data, $info);
              if (isset($info[0])){
                foreach ($info[0] as $t){
                  $showInfo[intval($t)] = '<b>Mail</b> enviado el '.date('d/m H:i', intval($t));
                }
              }
             
            }
            $url = get_shortlink($partee->link);
            $link = '<a href="'.$url.'" title="Ir a Partee">'.$url.'</a>';
            
            $subject = translateSubject('Recordatorio para Completado de Partee',$book->customer->country);
            $message = $this->getMailData($book,'SMS_Partee_msg');
            $message = str_replace('{partee}', $url, $message);
            $message = $this->clearVars($message);
            
            $whatsapp = whatsappFormat($message);
            if (config('app.is_mobile') || !$phone){
              $linkWSP = 'href="whatsapp://send?text='.$whatsapp.'"
                   data-action="share/whatsapp/share"';
            } else {
              $linkWSP = 'href="https://web.whatsapp.com/send?phone='.$phone.'&text='.$whatsapp.'" target="_blank"';
            }
            
          } else {
            ?>
            <p class="alert alert-warning">Partee no encontrado</p>
            <p class="text-center">
              <a href="/admin/sendPartee/<?php echo $book->id; ?>">Crear Partee</a>
            </p>
            <?php
            return;
          }
         
          ?>
          <input type="hidden" value="<?php echo csrf_token(); ?>" id="partee_csrf_token">
        <div class="col-md-6 minH-4">
          <button class="sendSMS btn btn-default <?php echo $disablePhone;?>" title="Enviar Texto partee por SMS" data-id="<?php echo $bookID;?>">
            <i class="sendSMSIcon"></i>Enviar SMS
          </button>
        </div>
        <div class="col-md-6 minH-4">
          <button class="sendParteeMail btn btn-default <?php echo $disableEmail;?>" title="Enviar Texto partee por Correo" data-id="<?php echo $bookID;?>">
            <i class="fa fa-inbox"></i> Enviar Email
          </button>
        </div>
        <div class="col-md-6 minH-4">
          <a <?php echo $linkWSP; ?>
                 data-original-title="Enviar Partee link"
                 data-toggle="tooltip"
                 class="btn btn-default <?php echo $disablePhone;?>">
            <i class="fa  fa-whatsapp" aria-hidden="true" style="color: #000; margin-right: 7px;"></i>Enviar Whatsapp
              </a>
        </div>
        <div class="col-md-6 minH-4">
          <button class="showParteeLink btn btn-default" title="Mostrar link Partee">
            <i class="fa fa-link"></i> Mostart Link
          </button>
        </div>  
        <div id="linkPartee" class="col-xs-12" style="display:none;"><?php echo $link; ?></div>
        <div class="col-md-6 minH-4"> 
          <button class="showParteeData btn btn-default" title="Mostrar Partee" data-partee_id="<?php echo $partee->partee_id; ?>">
            <i class="fa fa-eye"></i> Mostart Partee
          </button>
        </div>
        <div class="col-md-6 minH-4"> 
          <button class="btn btn-default" title="Mostrar Partee" onclick="copyParteeMsg(<?php echo $book->id; ?>,'cpMsgPartee',0)">
            <i class="far fa-copy"></i>  Copiar mensaje Partee
          </button>
          <div id="cpMsgPartee"></div>
        </div>
          <?php
           if (count($showInfo)){
            ksort($showInfo);
            echo '<div class="col-md-12" style="margin-top:3em;" ><b>Histórico:</b><br>';
            echo implode('<br>', $showInfo);
            echo '</div>';
          }
        }
        function sendPartee($bookID) {
          $book = Book::find($bookID);
          if ($book){
            $return = $book->sendToPartee();
          }
          return back();
        }
        function seeParteeHuespedes($id){
          $partee = new \App\Services\ParteeService();
          if ($partee->conect()){
            $partee->getCheckHuespedes($id);
            if ($partee){
              $obj = $partee->response;
              if (!is_object($obj)){
                echo 'No se ha encontrado el Registro asociado';
                return ;
              }
              ?>
                <h1>Datos Partee</h1>
                <?php if ($obj->borrador): ?>
                <strong class="text-danger">Borrador: no enviado aún a la policia</strong>
                <?php else: ?>
                <strong class="text-success">Enviado a la policia</strong>
                <?php endif; ?>
                <p><b>Creado: </b> <?php echo date('d/m H:i', strtotime($obj->fecha_creacion)); ?>Hrs</p>
                <p><b>Entrada: </b> <?php echo date('d/m H:i', strtotime($obj->fecha_entrada)); ?>Hrs</p> 
                <?php if (isset($obj->checkin_online_url)): ?>
                <p><b>Enlace Checkin: </b> <a href="<?php echo $obj->checkin_online_url; ?>" ><?php echo $obj->checkin_online_url; ?></a></p>
                <?php endif; ?>
                <h3>Viajeros Cargados</h3>
                <?php 
                if ($obj->viajeros):
                  foreach ($obj->viajeros as $v):
                    if ($v->borrador): ?>
                    <strong>Borrador</strong>
                    <?php endif; ?>
                      <h4><?php echo $v->nombre_viajero.' '.$v->primer_apellido_viajero; ?></h4>
                      <p><b>Sexo: </b> <?php echo $v->sexo_viajero; ?></p>
                      <p><b>Dias estancia: </b> <?php echo $v->dias_estancia; ?></p>
                      <p><b>Entrada: </b> <?php echo $v->fecha_expedicion_documento; ?></p>
                      <p><b>Indentificación: </b> <?php echo $v->numero_identificacion.' - '.$v->pais_nacimiento_viajero; ?></p>
                    <?php
                  endforeach;
                else: ?>
                  <p class="alert alert-warning">No han cargado datos aún</p>
                <?php
                endif;
              return;
            }
          }
          
          echo '<h1>Partee no encontrado</h1>';
          
        }
        
        
  /**
   * Check the Partee HUESPEDES completed
   */
  public function syncCheckInStatus() {
    $apiPartee = new \App\Services\ParteeService();
    
    //conect to Partee and get the JWT
    if ($apiPartee->conect()) {

      $today = Carbon::now();
      $books = \App\Book::where('start', '>=', $today->copy()->subDays(2))
                  ->where('start', '<=', $today->copy()->addDays(5))
                  ->where('type_book', 2)->orderBy('start', 'ASC')->get();
          
    
      if ($books){
        foreach ($books as $Book) {
        //Read a $BookPartee            
        try {
          $BookPartee = $Book->partee();
          if ($BookPartee){
            $partee_id = $BookPartee->partee_id;
            //check Partee status
            $result = $apiPartee->getCheckStatus($partee_id);
            if( isset($apiPartee->responseCode) && $apiPartee->responseCode == 200) {
              if ($apiPartee->response && $apiPartee->response != $apiPartee->response->status){
                //Save the new status
                $log = $BookPartee->log_data . "," . time() . '-' . $apiPartee->response->status;
                $BookPartee->status = $apiPartee->response->status;
                $BookPartee->log_data = $log;
                $BookPartee->has_checked = 1;
                if ($apiPartee->response->status == 'HUESPEDES'){
                  $BookPartee->guestNumber = $apiPartee->response->guestNumber;
                  $BookPartee->date_complete = date('Y-m-d H:i:s');
                }
                if ($apiPartee->response->status == 'FINALIZADO'){
                  $BookPartee->date_finish = date('Y-m-d H:i:s');
                }
                $BookPartee->save();
              }
            } else {
              if( isset($apiPartee->responseCode) && $apiPartee->responseCode == 404){
                $log = $BookPartee->log_data . "," . time() . '-NotFound '.$BookPartee->partee_id;
                $BookPartee->partee_id = -1;
                $BookPartee->save();
              } 
            }
          }
        } catch (\Exception $e) {
          Log::error("Error CheckIn Partee " . $BookPartee->id . ". Error  message => " . $e->getMessage());
          continue;
        }
      }
    
      }
    }
    
    return redirect('/admin/reservas');
  
  }
  
  
  public function createFianza($bookID) {
    $book = Book::find($bookID);
    if ($book){
      $hasFianza = PaymentOrders::where('book_id',$book->id)->where('is_deferred',1)->first();
      if (!$hasFianza){
        $urlPayment = $this->createPaymentFianza($book);
        if ($urlPayment){
         $this->sendEmail_FianzaPayment($book,300,$urlPayment);
          return [
            'status'   => 'success',
            'response' => "Fianza creada y Mail enviado",
          ];
        }
        return [
          'status'   => 'success',
          'response' => "Fianza creada",
        ];
      }
    }
    return [
          'status'   => 'danger',
          'response' => "Fianza ya creada",
        ];
  }


  public function showFianza($bookID) {
    
    
   
    $book = Book::find($bookID);
    $disableEmail = 'disabled';
    $disablePhone = 'disabled';
    if ($book){
      if ($book->customer->email) $disableEmail = '';
      if ($book->customer->phone) $disablePhone = '';
    }
    
    if ($this->showPaymentFianzaForm($book)) return;
    
    $Order = PaymentOrders::where('book_id',$book->id)->where('is_deferred',1)->first();
    $showInfo = [];
    if ($Order){
      
      $urlPay = getUrlToPay($Order->token);
      $link = '<a href="'.$urlPay.'" title="Ir a Partee">'.$urlPay.'</a>';
      $totalPayment = $Order->amount/100;
      $subject = translateSubject('Fianza de reserva',$book->customer->country);
      $message = $this->getMailData($book, 'SMS_fianza');
      $message = str_replace('{payment_amount}', number_format($totalPayment, 2, ',', '.'), $message);
      $message = str_replace('{urlPayment}', $urlPay, $message);
      $message = $this->clearVars($message);
    } else {
      ?>
      <p class="alert alert-warning">Fianza no encontrada</p>
      <p class="text-center">
        <button type="button" class="createFianza btn btn-success" data-id="<?php echo $book->id; ?>">Crear Fianza</button>
      </p>
      <?php
      return;
    }

    ?>
  <div class="col-md-6 minH-4">
    <button class="sendFianzaSMS btn btn-default <?php echo $disablePhone;?>" title="Enviar Texto Fianza por SMS" data-id="<?php echo $bookID;?>">
      <i class="sendSMSIcon"></i>Enviar SMS
    </button>
  </div>
  <div class="col-md-6 minH-4">
    <button class="sendFianzaMail btn btn-default <?php echo $disableEmail;?>" title="Enviar Texto Fianza por Correo" data-id="<?php echo $bookID;?>">
      <i class="fa fa-inbox"></i> Enviar Email
    </button>
  </div>
  <div class="col-md-6 minH-4">
    <a href="whatsapp://send?text=<?php echo $message; ?>"
           data-action="share/whatsapp/share"
           data-original-title="Enviar Partee link"
           data-toggle="tooltip"
           class="btn btn-default <?php echo $disablePhone;?>">
      <i class="fa  fa-whatsapp" aria-hidden="true" style="color: #000; margin-right: 7px;"></i>Enviar Whatsapp
        </a>
  </div>
  <div class="col-md-6 minH-4">
    <button class="showParteeLink btn btn-default" title="Mostrar link Fianza">
      <i class="fa fa-link"></i> Mostart Link
    </button>
  </div>  
  <div class="col-md-6 minH-4"> 
    <button class="btn btn-default copyMsgFianza" title="Copiar mensaje Fianza" data-msg="<?php echo strip_tags($message); ?>">
      <i class="far fa-copy"></i>  Copiar mensaje Fianza
    </button>
    <div id="copyMsgFianza"></div>
  </div>
  <div id="linkPartee" class="col-xs-12" style="display:none;max-width: 320px;overflow: auto;"><?php echo $link; ?></div>
    <?php

      
  }
  
  function sendFianzaMail(request $request){
    $bookID = $request->input('id',null);
          
    $book = Book::find($bookID);
    if ($book){
      $Order = PaymentOrders::where('book_id',$book->id)->where('is_deferred',1)->first();
      $showInfo = [];
      if ($Order){
        $urlPayment = getUrlToPay($Order->token);
        $totalPayment = $Order->amount;
        $this->sendEmail_FianzaPayment($book,$totalPayment,$urlPayment);
        return [
          'status'   => 'success',
          'response' => "Mail de Fianza enviado",
        ];
      }
    }
    
    return [
      'status'   => 'danger',
      'response' => "Registro de Fianza no encontrado."
    ];
              
  }
  
      /**
     * Send Fianza by SMS
     * @param Request $request
     * @return type
     */
    public function sendFianzaSMS(Request $request) {
      $bookID = $request->input('id',null);
          
    $book = Book::find($bookID);
    if ($book){
      $Order = PaymentOrders::where('book_id',$book->id)->where('is_deferred',1)->first();
      $showInfo = [];
      if ($Order){
        $urlPayment = getUrlToPay($Order->token);
        $totalPayment = $Order->amount;
        //Send SMS
        $SMSService = new \App\Services\SMSService();
        if ($SMSService->conect()){

          $message = $this->getMailData($book,'SMS_fianza');
          $message = str_replace('{urlPayment}', $urlPayment, $message);
          $message = str_replace('{payment_amount}', $totalPayment, $message);
          $phone = $book->customer['phone'];
          $message = $this->clearVars($message);
          $message = strip_tags($message);

          if ($SMSService->sendSMS($message,$phone)){
            return [
              'status'   => 'success',
              'response' => "Mail de Fianza enviado",
            ];
          }
        }
      }
    }
    
    return [
      'status'   => 'danger',
      'response' => "Registro de Fianza no encontrado."
    ];

    }
    
    public function showPaymentFianzaForm($book) {
      
      $Order = \App\BookDeferred::where('book_id',$book->id)
              ->where('is_deferred',1)->where('paid',1)->first();
      
      if ($Order){
        ?>
  
        <div class="col-md-12 minH-4">
          
          <?php 
          
          if ($Order->was_confirm && $Order->payment>0):
            echo '<p>Pago efectuado: <b>'. ($Order->payment/100).'€.</b></p>';
          else:
          ?>
          <div style="margin-bottom: 1em;">
              <label for="name">Cobrar de la fianza, la suma de:</label>
              <input class="form-control" type="number" id="amount_fianza" value="300">
          </div>
          <button class="sendPayment btn btn-success" title="Cobrar Fianza" data-id="<?php echo $book->id; ?>">
            <i class="fa fa-euro"></i> Generar Cobro
          </button>
          <p>
            <small>
              <strong>Importante:</strong> Sólo puedes efectuar un cobro a la Fianza (sin importart el monto).
            </small>
          </p>
          <?php 
          endif;
          ?>
          
          <p>
            <strong>ID de Orden De Payland:</strong> <?php echo $Order->order_uuid; ?>
          </p>
        </div> 
        <?php
        return true;
      }
      return false;
    }
    
    public function updSafetyBox($bookID,$value,$min=false) {
      
      $SafetyBox = new \App\SafetyBox();
      if ($value == -1){
        $oObject = $SafetyBox->cancelBoxBooking($bookID);
        if ($min) return 'ok';
        return $this->showSafetyBox($bookID);
      }
      
      $book = Book::find($bookID);
      if ($book){
        $otherBooks = $SafetyBox->usedBy_day($value,$book->start,$book->finish);
        if ($otherBooks){
          return 'overlap';
        }
      }
      
      $oObject = $SafetyBox->getBookSafetyBox($bookID);
      if ($oObject){
        $SafetyBox->updBookSafetyBox($oObject->id,$value);
      } else {
        $oObject = $SafetyBox->newBookSafetyBox($bookID,$value);
      }
      if ($min) return 'ok';
      return $this->showSafetyBox($bookID);
      
    }
    public function showSafetyBox($bookID) {
          $book = Book::find($bookID);
          $disableEmail = 'disabled';
          $disablePhone = 'disabled';
          $messageSMS   = null;
          $oObject   = null;
          $caja = null;
          $phone = null;
          $linkWSP = '';
          if ($book){
            if ($book->customer->email) $disableEmail = '';
            if ($book->customer->phone){
              $disablePhone = '';
              $phone=$book->customer->phone;
            }
          }
          $SafetyBox = new \App\SafetyBox();
          
          $current = $SafetyBox->getByBook($book->id);
          $showInfo = [];
          $used = [];
          $lstBoxs = $SafetyBox->getByRoom($book->room->id); 
          if ($book){
            foreach ($lstBoxs as $box){
              if ($SafetyBox->usedBy_day($box->id,$book->start,$book->finish)){
                $used[] = $box->id;
              }
            }
          }
          if ($current && !$current->deleted){
            $data = $current->log;
            $caja = $current->id;
            if ($data){
              preg_match_all('|([0-9])*(\-sentSMS)|', $data, $info);
              if (isset($info[0])){
                foreach ($info[0] as $t){
                  $showInfo[intval($t)] = '<b>SMS</b> enviado el '.date('d/m H:i', intval($t));
                }
              }
              preg_match_all('|([0-9])*(\-sentMail)|', $data, $info);
              if (isset($info[0])){
                foreach ($info[0] as $t){
                  $showInfo[intval($t)] = '<b>Mail</b> enviado el '.date('d/m H:i', intval($t));
                }
              }
             
            }

            
            $messageSMS = $this->getSafeBoxMensaje($book,'book_email_buzon',$current);
            $whatsapp = whatsappFormat($messageSMS);
            $messageSMS = html_entity_decode(str_replace ("<br />", "\r\n", $whatsapp));
           
          
            if (config('app.is_mobile') || !$phone){
              $linkWSP = 'href="whatsapp://send?text='.$whatsapp.'"
                   data-action="share/whatsapp/share"';
            } else {
              $linkWSP = 'href="https://web.whatsapp.com/send?phone='.$phone.'&text='.$whatsapp.'" target="_blank"';
            }
          }        
        ?>
  <p class="alert alert-warning">Los buzones se asignan de manera automática a las 14Hrs de cada día (check-in)</p>
        <div class="col-md-5 mb-1em">
          <b><?php echo $book->customer->name; ?></b>
        </div>
        <div class="col-md-3 mb-1em">
          <b>Apto: </b><?php echo $book->room->nameRoom; ?>
        </div>
        <div class="col-md-4 mb-1em">
          <b>Check-in: </b><?php echo dateMin($book->start).' - '.$book->schedule.' Hrs'; ?>
        </div>
        <div class="col-md-12 mb-1em"><br/></div>
        <?php
        if(!is_null($messageSMS)):
          ?>
        <input type="hidden" value="<?php echo csrf_token(); ?>" id="buzon_csrf_token">
        <div class="col-xs-6 minH-4">
          <button class="sendBuzonSMS btn btn-default <?php echo $disablePhone;?>" title="Enviar Texto Buzón por SMS" data-id="<?php echo $bookID;?>">
            <i class="sendSMSIcon"></i>Enviar SMS
          </button>
        </div>
        <div class="col-xs-6 minH-4">
          <button class="sendBuzonMail btn btn-default <?php echo $disableEmail;?>" title="Enviar Texto Buzón por Correo" data-id="<?php echo $bookID;?>">
            <i class="fa fa-inbox"></i> Enviar Email
          </button>
        </div>
        <div class="col-md-12 col-xs-12 minH-4 mb-1em">
          <a <?php echo $linkWSP; ?>
                 data-original-title="Enviar Buzon link"
                 data-toggle="tooltip"
                 class="btn btn-default <?php echo $disablePhone;?>">
            <i class="fa  fa-whatsapp" aria-hidden="true" style="color: #000; margin-right: 7px;"></i>Enviar Whatsapp
              </a>
        </div>
        <div class="col-md-12 col-xs-12 minH-4 mb-1em"> 
          <button class="btn btn-default" title="Copiar mensaje Buzon" onclick="copyBuzonMsg(<?php echo $book->id; ?>,'cpMsgBuzon',0)">
            <i class="far fa-copy"></i>  Copiar mensaje Buzon
          </button>
          <div id="cpMsgBuzon"></div>
        </div>
        <?php endif; ?>
        <div class="col-md-6 minH-4 mb-1em" style="padding-right: 2em;overflow: auto;"> 
          <label>Caja <?= $book->room->nameRoom ?></label>
          <select id="change_CajaAsig" class="form-control" data-id="<?php echo $book->id; ?>">
            <option value="-1"> Sin Asignar </option>
            <?php 
            
            foreach ($lstBoxs as $box){
              $selected = ($caja == $box->id) ? 'selected': ''; 
              $disabled =  (in_array($box->id, $used) && $caja != $box->id) ? 'disabled': ''; 
              echo '<option value="'.$box->id.'" '.$selected.' '.$disabled.'>Asignar</option>';
            }
            ?>
          </select>
        </div>
        
          <?php if($caja){ ?>
          <div class="safebox_msgSuccess">
            <p class="text-danger">OK CAJA ASIGNADA:</p>
            El cliente recibirá por email los codigos el dia del checkin a las 14h<br/>
            (Ademas se lo puedes enviar tu manualmente en cualquier momento)
          </div>
          <?php } ?>
        
          <?php
           if (count($showInfo)){
            ksort($showInfo);
            echo '<div class="col-md-6" ><b>Histórico:</b><br>';
            echo implode('<br>', $showInfo);
            echo '</div>';
          }
          echo '<div class="col-md-12" >Hora actual del servidor:'.date('H:i').'</div>';
        }
        
    public function showSafetyBoxBySite($siteID) {
      
      
      $SafetyBox = new \App\SafetyBox();
      $oSite = \App\Sites::find($siteID);
      if (!$oSite){
        ?>
        <div class="alert alert-warning">Sitio no encontrado</div>
        <?php
        return;
      }
      $roomList = \App\Rooms::getRoomsBySite($siteID);
      $lst = $SafetyBox->getBySite($siteID);
      $canEdit = \App\SafetyBox::canEdit();
      if ($lst){
        ?>
        <table class="table">
          <tr>
            <th>Caja</th>
            <th>Hab.</th>
            <th>Clave</th>
          </tr>
          <?php
            foreach ($lst as $sb):
              ?>
                <tr>
                  <td><?php echo $sb->box_name; ?></td>
                  <?php if ($canEdit):?>
                  <td>
                    <select data-id="<?php echo $sb->id; ?>" data-field="room" class="form-control editSafeBox">
                      <option> -- </option>
                      <?php 
                        foreach ($roomList as $k=>$v){
                          $selected = ($sb->room_id == $k) ? 'selected' : '';
                          echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
                        }
                      ?>
                    </select>
                  </td>
                  <td>
                    <input data-id="<?php echo $sb->id; ?>" data-field="key" class="form-control editSafeBox" value="<?php echo $sb->keybox; ?>">
                  </td>
                  <?php else: ?>
                  <td><?php  echo show_isset($roomList, $sb->room_id);  ?></td>
                  <td><?php  echo $sb->keybox;  ?></td>
                  <?php  endif; ?>
                </tr>
              <?php
            endforeach;
          ?>
        </table>
        <?php
        
      }
      if ($canEdit){
        ?>
        <div class="box-new">
          <h3>Crear nueva caja de seguridad</h3>
          <div class="row">
            <div class="col-md-3 col-xs-6">
              <label>Caja</label>
              <input id="safetyBox_caja" class="form-control">
            </div>
            <div class="col-md-3 col-xs-6">
              <label>Color</label>
              <input id="safetyBox_color" class="form-control">
            </div>
            <div class="col-md-3 col-xs-6">
              <label>Habitación</label>
              <select id="safetyBox_room" class="form-control">
                <option> -- </option>
                <?php 
                  foreach ($roomList as $k=>$v){
                    echo '<option value="'.$k.'">'.$v.'</option>';
                  }
                ?>
              </select>
            </div>
            <div class="col-md-3 col-xs-6">
              <label>Clave</label>
              <input id="safetyBox_clave" class="form-control">
            </div>
            <div class="col-md-3 col-xs-6">
              <input type="hidden" id="safetyBox_site" value="<?php echo $siteID; ?>">
              <input type="button" id="createSafetyBox" class="btn btn-success" value="Crear">
            </div>
          </div>
        </div>
        <?php
      }
    
      }
      public function updKeySafetyBoxBySite(Request $request) {
        
        if(!\App\SafetyBox::canEdit()) return 'No tienes permiso para la tarea solicitada';
        $id = $request->input('id',null);
        $val = $request->input('val',null);
        $field = $request->input('field',null);
        
        if (!$id || !$val)
          return 'Caja no encontrada';
        
        $SafetyBox = \App\SafetyBox::find($id);
        if (!$SafetyBox || $SafetyBox->id != $id)
          return 'Caja no encontrada';
        
        if ($field == "room") $SafetyBox->room_id = $val;
        if ($field == "key")  $SafetyBox->keybox = $val;
        if ($SafetyBox->save()){
          return 'OK';
        }
        return 'No se pudo actualizar la clave de la Caja de seguridad';
      }
      public function createSafetyBox(Request $request) {

        if(!\App\SafetyBox::canEdit()) return 'No tienes permiso para la tarea solicitada';
      
        $caja = $request->input('caja',null);
        $color = $request->input('color',null);
        $clave = $request->input('clave',null);
        $siteID = $request->input('site',null);
        $room_id = $request->input('room_id',null);
        if (!$caja || !$clave || !$siteID)
          return 'Debe ingresar todos los campos';
        
        $SafetyBox = new \App\SafetyBox();
        $SafetyBox->keybox  = $clave;
        $SafetyBox->color   = $color;
        $SafetyBox->caja    = $caja;
        $SafetyBox->site_id = $siteID;
        $SafetyBox->room_id = $room_id;
        if ($SafetyBox->save()){
          return 'OK';
        }
        return 'No se pudo crear la Caja de seguridad';
      }
      
      /**
         * Get the custom message to Cajas
         */
        public function getSafetyBoxMsg($bookID) {
          //Get Book object
          $book = Book::find($bookID);
          
          if (!$book){
            die('empty');
          }
          //get BookSafetyBox object
          $SafetyBox = new \App\SafetyBox();
          $oObject = $SafetyBox->getByBook($bookID);
          if ($oObject){
            //Get msg content
            $content = $this->getSafeBoxMensaje($book,'SMS_buzon',$oObject);
            $content = whatsappFormat($content);
            $content = str_replace('%0D%0A',"\n", $content);
            die($content);
          } else {
            die('empty');
          }
        }
        
        /**
         * Send Partee to Finish CheckIn
         * 
         * @param Request $request
         * @return type
         */
        public function sendSafetyBoxSMS(Request $request) {
          
          $bookID = $request->input('id',null);
          
          //Get Book object
          $book = Book::find($bookID);
          if (!$book){
            return [
                'status'   => 'danger',
                'response' => "Reserva no encontrada."
              ];
          }
          
          //get BookSafetyBox object
          $SafetyBox = new \App\SafetyBox();
          $oObject = $SafetyBox->getByBook($bookID);
          if ($oObject){
              //Send SMS
              $SMSService = new \App\Services\SMSService();
              if ($SMSService->conect()){

                $messageSMS = $this->getSafeBoxMensaje($book,'SMS_buzon',$oObject);
                $message = strip_tags($messageSMS);
                if ($SMSService->sendSMS($message,$phone)){
                  $oObject->updLog($bookID,'sentSMS');
                  return [
                    'status'   => 'success',
                    'response' => "Registro enviado",
                  ];

                } 
              }
          
              return [
                'status'   => 'danger',
                'response' => $SMSService->response
              ];
             
            }
            
            return [
              'status'   => 'danger',
              'response' => "El registro aún no está preparado."
            ];
              
          }
               /**
         * Send Partee to Finish CheckIn
         * 
         * @param Request $request
         * @return type
         */
        public function sendSafetyBoxMail(Request $request) {
          
          $bookID = $request->input('id',null);
          
          //Get Book object
          $book = Book::find($bookID);
          if (!$book){
            return [
                'status'   => 'danger',
                'response' => "Reserva no encontrada."
              ];
          }
          if (!$book->customer->email || trim($book->customer->email) == ''){
              return [
                'status'   => 'danger',
                'response' => "La Reserva no posee email."
              ];
          }
         //get BookSafetyBox object
          $SafetyBox = new \App\SafetyBox();
          $oObject = $SafetyBox->getByBook($bookID);
          if ($oObject){
            
            $sended = $this->sendSafeBoxMensaje($book,'book_email_buzon',$oObject);
            
            if ($sended){
              
              $oObject->updLog($bookID,'sentMail');
              return [
                'status'   => 'success',
                'response' => "Registro enviado",
              ];
              } 
            }
            
            return [
              'status'   => 'danger',
              'response' => "El registro aún no está preparado."
            ];
              
          }
          
          
  public function getSafetyBoxLst(){
          
          $today = Carbon::now();
          $books = \App\Book::select('book.*','book_safety_boxes.box_id')
                  ->join('book_safety_boxes','book_id','=','book.id')
                  ->where('start', '>=', $today->copy()->subDays(3))
                  ->whereNull('deleted')
                  ->whereYear('start','=', $today->copy()->format('Y'))
                  ->orderBy('start', 'ASC')->get();
          $safety_boxes = \App\SafetyBox::all();
          $keys = $boxs = $bSite = [];
          foreach ($safety_boxes as $sb){
            $keys[$sb->id] = $sb->keybox;
            $boxs[$sb->id] = $sb->caja;
            if (!isset($bSite[$sb->site_id])) $bSite[$sb->site_id] = [];
            $bSite[$sb->site_id][$sb->id] = $sb->caja;
          }
          $isMobile = config('app.is_mobile');
          $today = $today->format('Y-m-d');
          return view('backend/planning/_safety-boxs',compact('books','keys','boxs','isMobile','today','bSite'));
          
        }   
        
  public function getCustomersRequestLst(){

    $items = \App\CustomersRequest::where('status',0)
                ->where('updated_at','>=',date('Y-m-d', strtotime('-7 days')))
                ->get();
     
    $sites = \App\Sites::all();
    $aSites = [];
    foreach ($sites as $v){
      $aSites[$v->id] = $v->name;
    }
    
    /*********************************************/
    
    $urls = [];
    $oSetting = new \App\Settings();
    foreach ($sites as $id=>$k){
      if ($id>0){
        $url = $oSetting->getLongKeyValue('gha_sitio_'.$id);
        if ($url && strlen(trim($url))>0)   $urls[$id] = $url;
      }
    }
        
    /*********************************************/
    
    $users = \App\User::where('role','!=','limpieza')->get();
    $aUsers = [];
    foreach ($users as $v){
      $aUsers[$v->id] = $v->name;
    }
    $isMobile = config('app.is_mobile');
    return view('backend/planning/_customers-request',compact('items','isMobile','aSites','aUsers','urls'));

  }   
  public function hideCustomersRequest(Request $request) {
          
    $ID = $request->input('id',null);
    $user_id = $request->input('userID',null);
    $comment = $request->input('comments',null);

    $item = \App\CustomersRequest::find($ID);
    if ($item){
      $item->user_id = $user_id;
      $item->comment = $comment;
      $item->update_by = Auth::user()->id;
      $item->status =  1; // ignered;
      $item->save();
      return 'OK';
    }
    return 'error';
  }   
    
  public function saveCustomerRequest(Request $request) {
          
    $ID = $request->input('id',null);
    $user_id = $request->input('userID',null);
    $comment = $request->input('comments',null);
    $send_mail = $request->input('send_mail',null);

    $item = \App\CustomersRequest::find($ID);
    if ($item){
      if ($user_id == -2){
        $item->delete();
        return 'OK';
      }
      $item->user_id = $user_id;
      $item->comment = $comment;
      $item->update_by = Auth::user()->id;
      $item->save();
  
      if ($send_mail){
        $aSite = \App\Sites::siteData($item->site_id);
       
        $subject = $aSite['name'];
        $email = $item->email;
        $sended = Mail::send('backend.emails.base', [
            'mailContent' => nl2br($comment),
            'title'       => $subject,
            'siteName'    => $aSite['name'],
            'siteUrl'     => $aSite['url'],
        ], function ($message) use ($subject,$email,$aSite) {
            $message->from($aSite['mail_from'],$aSite['mail_name']);
            $message->to($email);
            $message->subject($subject);
        });
        if ($sended){
          $item->sentMail($aSite);
        } else {
          return 'errorMail';
        }
      }
      
      return 'OK';
    }
    return 'error';
  }   
  
  public function getCustomersRequest(Request $request) {
          
    $ID = $request->input('id',null);

    $item = \App\CustomersRequest::where('id', $ID)->first();
    if ($item){
      $sites = \App\Sites::all();
      $site = '--';
      foreach ($sites as $v){
        if ( $item->site_id == $v->id ) $site = $v->name;
      }
      
      $status = 'Sin atender';
      
      if ($item->status == 1) $status = 'Ignorada';
      
      $booking = '';
      if ($item->book_id){
        $status = 'Converida a Reserva';
        $booking = '<a href="/admin/reservas/update/'.$item->book_id.'" tarjet="_black">'.$item->book_id.'</a>';
      }
    
      if (!$item->user_id){
        $item->user_id = Auth::user()->id;
      }
      
      $req = [
          'id'     => $item->id,
          'user_id'=> $item->user_id,
          'name'   => $item->name,
          'email'  => $item->email,
          'pax'    => $item->pax,
          'price'  => moneda($item->getMediaPrice()),
          'phone'  => '<a href="tel:+'.$item->phone.'">'.$item->phone.'</a>',
          'comment'=> $item->comment,
          'date'   => dateMin($item->start).' - '.dateMin($item->finish),
          'site'   => $site,
          'status' => $status,
          'booking'=> $booking,
          'created'=> convertDateTimeToShow_text($item->created_at),
          'canBooking' => ($item->start>=date('Y-m-d')),
          'mails' => $item->getMetasContet('mailSent')
      ];
      
      return response()->json($req);
    }
  }   
  
  public function getCustomersRequest_book($bookID) {
    $userID = Auth::user()->id;
    $comment = '';
    $item = \App\CustomersRequest::where('book_id', $bookID)->first();
    if ($item){
      if ($item->user_id){
        $userID = $item->user_id;
      }
      $comment = $item->comment;
    } else {
      $oBook = Book::find($bookID);
      $item = new \App\CustomersRequest();
      $item->user_id = $userID;
      $item->book_id = $bookID;
      $item->site_id =  $oBook->room->site_id;
      $item->pax =  $oBook->pax;
      $item->name = $oBook->customer->name.' B-'.$oBook->id;
      $item->status =  1; // ignered;
      $item->save();
    }
    
    
    $users = \App\User::where('role','!=','limpieza')->get();
    $aUsers = [];
    foreach ($users as $v){
      $aUsers[$v->id] = $v->name;
    }
    ?>
      <div class="">
        <div class="form-group">
          <label>Usuario</label>
          <select id="CRE_user" class="form-control">
            <?php  foreach ($users as $v):
              $select = ($v->id === $userID) ? 'selected' : '';
              echo '<option value="'.$v->id.'" '.$select.'>'.$v->name.'</option>';
              endforeach;
            ?>
            <option value="-2">ELIMINAR</option>
          </select>
        </div>
        <div class="form-group">
          <label>Comentario</label>
          <textarea class="form-control" id="CRE_comment" rows="5"><?php echo $comment; ?></textarea>
        </div>
        <button class="btn btn-primary" id="saveCustomerRequest" type="button" data-id="<?php echo $item->id; ?>">Guardar</button>
      </div>
    <?php
  }   
  
    function showFormEncuesta($bookID) {
    $book = Book::find($bookID);
    $alerts = [];
    if (!$book->customer->email || trim($book->customer->email) == ''){
      $alerts[] = 'El cliente no posee un emial cargado.';
    }
    
    $alreadySent = \App\BookData::where('book_id',$book->id)
              ->where('key','sent_poll')->first();
    
    $bloqueada = false;
    if ($alreadySent){
      $content = json_decode($alreadySent->content);
      foreach ($content as $c){
        $date = date('d/m', strtotime($c->date));
        if (isset($c->status)){
          switch ($c->status){
            case 'block':
              $alerts[] = $date.' Bloqueado por '.$c->userName;
              $bloqueada = true;
              break;
            case 'unblock':
              $alerts[] = $date.' Desbloqueado por '.$c->userName;
              $bloqueada = false;
              break;
            default :
              $alerts[] = $date.' enviado a '.$c->mail;
              break;
          }
        }
      }
    }
    
    ?>
      <div class="box-new">
          <div>
            <ul>
            <?php
                foreach ($alerts as $a) echo '<li>'.$a.'</li>';
            ?>
            </ul>
          </div>
          <div class="row">
            <div class="col-md-4 col-xs-12 mb-1em">
              <?php if (!$bloqueada): ?>
              <button class="btn btn-primary form_sendEncuesta" 
                      type="button" 
                      data-id="<?php echo $bookID; ?>" 
                      data-action="send" 
                      <?php echo ($bloqueada) ? 'disabled' : ''; ?>>
                Enviar encuesta mail
              </button>
              <?php endif; ?>
            </div>
            <div class="col-md-4 col-xs-12 mb-1em">
              <button class="btn  btn-danger form_sendEncuesta" 
                      type="button" 
                      data-id="<?php echo $bookID; ?>" 
                      data-action="block" 
                      >
                <?php echo ($bloqueada) ? 'Desbloquear' : 'Bloquear' ?>  encuesta mail
              </button>
            </div>
            <div class="col-md-4 col-xs-12 mb-1em">
              <?php $text = "Hola, esperamos que hayas disfrutado de tu estancia con nosotros." . "\n" . "Nos gustaria que valorarás, para ello te dejamos este link : https://www.apartamentosierranevada.net/encuesta-satisfaccion/" . base64_encode($book->id); ?>
              <a href="whatsapp://send?text=<?php echo $text; ?>"
                 data-action="share/whatsapp/share"
                 data-original-title="Enviar encuesta de satisfacción"
                 data-toggle="tooltip"
                 class="btn btn-primary">
                Enviar por Whatsapp
              </a>
            </div>
          </div>
        </div>
    <?php
    
    return;
  }
  function sendEncuesta(Request $request) {
          
    $bookID = $request->input('id',null);
    $action = $request->input('action',null);
    $book = Book::find($bookID);
    $auth = Auth::user();
    $bloqueada = false;
    if (!$book){
      return response()->json(['status'=>'error','msg'=>"Reserva no encontrada"]);
    }
    
    /****************************************************/
    if ($action == 'block'){
      $alreadySent = \App\BookData::where('book_id',$book->id)
              ->where('key','sent_poll')->first();
      $dataSent = [
          'date' => date('Y-m-d H:i:s'),
          'status' => 'block',
          'userName' => $auth->name,
          'userID' => $auth->id,
          'mail' =>$book->customer->email
      ];
      if ($alreadySent){
        $content = json_decode($alreadySent->content,true);
        
        foreach ($content as $c){
          if (isset($c['status'])){
            switch ($c['status']){
              case 'block':
                $bloqueada = true;
                break;
              case 'unblock':
                $bloqueada = false;
                break;
            }
          }
        }
        if ($bloqueada){
          $dataSent['status'] = 'unblock';
          $bloqueada = false;
        }
        $content[] = $dataSent;
        $alreadySent->content = json_encode($content);
        $alreadySent->save();
        
      } else {
        $save = new \App\BookData();
        $save->book_id = $book->id;
        $save->key = 'sent_poll';
        $save->content = json_encode([$dataSent]);
        $save->save();
      }
      if ($bloqueada)  return response()->json(['status'=>'OK','msg'=>"encuesta desbloqueada"]);
      return response()->json(['status'=>'OK','msg'=>"encuesta bloqueada"]);
    }
    
    
    /****************************************************/
    if (!$book->customer->email || trim($book->customer->email) == ''){
      return response()->json(['status'=>'error','msg'=>'El cliente no posee un emial cargado.']);
    }
    
    $alreadySent = \App\BookData::where('book_id',$book->id)
              ->where('key','sent_poll')->first();
    
    if ($alreadySent){
      $content = json_decode($alreadySent->content);
      $bloqueada = false;
      foreach ($content as $c){
        if (isset($c->status)){
          switch ($c->status){
            case 'block':
              $bloqueada = true;
            case 'unblock':
              $bloqueada = false;
          }
        }
      }
      if ($bloqueada){
        return response()->json(['status'=>'error','msg'=>'La Encuesta está bloqueada.']);
      }
    }
    
    if ($this->sendEmail_Encuesta($book,"DANOS 5' Y TE INVITAMOS A DESAYUNAR")){
   
      $dataSent = [
          'date' => date('Y-m-d H:i:s'),
          'status' => 'sent',
          'userName' => $auth->name,
          'userID' => $auth->id,
          'mail' =>$book->customer->email
      ];
      if ($alreadySent){
        $content = json_decode($alreadySent->content,true);
        $content[] = $dataSent;
        $alreadySent->content = json_encode($content);
        $alreadySent->save();
      } else {
        $save = new \App\BookData();
        $save->book_id = $book->id;
        $save->key = 'sent_poll';
        $save->content = json_encode([$dataSent]);
        $save->save();
      }
      return response()->json(['status'=>'OK','msg'=>'Encuesta enviada']);
    }
    
    return response()->json(['status'=>'error','msg'=>'Encuesta no enviada']);
  }
  
  public function multipleRoomLock_print() {
    $sites = \App\Sites::allSites();
    $start = Carbon::now();
    $finish = Carbon::now()->addDay(1);
    
    $MultipleRoomLock = new \App\Services\Bookings\MultipleRoomLock();
    $aTaskData = $MultipleRoomLock->get_RoomLockSetting($sites);
   
    return view('backend/planning/_multiple-room-lock', compact('sites','start','finish','aTaskData'));
  }
  
  public function multipleRoomLock_tasks(Request $request) {
    $siteSelect = $request->input('sites',null);
    $time  = $request->input('time',null);
    $sites = \App\Sites::allSites();
    $aTaskData = ['time'=>$time,'sites'=>[]];
    if (!is_array($siteSelect)) $siteSelect = [];
    foreach ($sites as $k=>$v){
      if (in_array($k,$siteSelect)) $aTaskData['sites'][$k] = 1;
      else $aTaskData['sites'][$k] = 0;
    }
    $MultipleRoomLock = new \App\Services\Bookings\MultipleRoomLock();
    $MultipleRoomLock->set_RoomLockSetting($aTaskData);
      
    return response()->json(['title'=>'OK','status'=>'success','msg'=>'Bloqueo Automático guardado']);
  }
  
   public function multipleRoomLock_send(Request $request) {
    $siteID = $request->input('site',null);
    $start  = $request->input('start',null);
    $finish = $request->input('finish',null);
    
    if ($siteID>0){
      $MultipleRoomLock = new \App\Services\Bookings\MultipleRoomLock();
      $MultipleRoomLock->roomLockBy_site($siteID,$start,$finish);
      return response()->json(['title'=>'OK','status'=>'success','msg'=>'Apartamento bloqueados']);
    }
    
    return response()->json(['title'=>'error','status'=>'danger','msg'=>'No se pudo llevar a cabo la acción solicitada']);

  }



  /**
   * Payment Remenber
   */

   
   public function showPaymentRemember($bookID) {



    $book = Book::find($bookID);
    $disableEmail = 'disabled';
    $disablePhone = 'disabled';
    if ($book) {
      if ($book->customer->email)
        $disableEmail = '';
      if ($book->customer->phone)
        $disablePhone = '';
    }



    $secondPayAlert = \App\BookLogs::where('book_id',$book->id)->where('action','second_payment_reminder')->orderBy('created_at','DESC')->first();
    if ($secondPayAlert) $secondPayAlert = convertDateTimeToShow_text($secondPayAlert->created_at);
    


    $Order = PaymentOrders::where('book_id', $book->id)->orderBy('created_at','DESC')->first();
    $showInfo = [];
    $toPay = 0;
    $link = ''; $urlPay = '';
    if ($Order) {
      $urlPay = getUrlToPay($Order->token);
      $toPay = $Order->amount;
      $link = '<a href="' . $urlPay . '" title="Pagar reserva">' . $urlPay . '</a>';
    } else {
      ?>
      <p class="alert alert-warning">Orden de pago no encontrada</p>
      <?php
      return;
    }

   
      $message = 'Recodatorio del pago de <strong>'.moneda($toPay).'</strong> de la reserva: ';
      $message .= '<br />*Nombre:* '.$book->customer->name;
      $message .= '<br />*Fecha entrada:* '. date('d/m/Y', strtotime($book->start));
      $message .= '<br />*Fecha salida:* '. date('d/m/Y', strtotime($book->start));
      $message .= '<br />*Noches:* '. $book->nigths;
      $message .= '<br />*Ocupantes:* '. $book->pax;
      $message .= '<br />*Apartamento:* '.  $book->room->sizeRooms->name.' '.(($book->type_luxury == 1) ? "Lujo" : "Estandar");
      $message .= '<br /><br />Puede hacer el pago a travez del siguiente link: '.$urlPay;
    

    ?>
    <div class="col-md-6 minH-4">
      <button class="sendPaymentRemember btn btn-default <?php echo $disableEmail; ?>" title="Enviar Texto Recodatorio de pago por Correo" data-id="<?php echo $bookID; ?>">
        <i class="fa fa-inbox"></i> Enviar Email
      </button>
    </div>
    <div class="col-md-6 minH-4"> 
      <button class="btn btn-default copyMsgRemeberPayment" title="Copiar mensaje Recodatorio de pago" data-msg="<?php echo strip_tags($message); ?>">
        <i class="far fa-copy"></i>  Copiar mensaje
      </button>
      <div id="copyMsgRemeberPayment"></div>
    </div>
    <div class="col-md-6 minH-4">
      <a href="https://api.whatsapp.com/send?phone=<?= $book->customer->phone ?>&text=<?php echo whatsappFormat($message); ?>"
         data-action="share/whatsapp/share"
         data-original-title="Enviar Payment link"
         data-toggle="tooltip"
         class="btn btn-default <?php echo $disablePhone; ?>">
        <i class="fa  fa-whatsapp" aria-hidden="true" style="color: #000; margin-right: 7px;"></i>Enviar Whatsapp Movil
      </a>
    </div>
    <div class="col-md-6 minH-4">
      <a href="https://web.whatsapp.com/send?phone=<?= $book->customer->phone ?>&text=<?php echo whatsappFormat($message); ?>"
         target="_blank"
         class="btn btn-default <?php echo $disablePhone; ?>">
        <i class="fa  fa-whatsapp" aria-hidden="true" style="color: #000; margin-right: 7px;"></i>Enviar Whatsapp Web
      </a>
    </div>
    
    <div id="RemeberPaymentLink" class="col-xs-12" ><?php echo $link; ?></div>
    <?php
  }

  function sendRemenberPaymentMail($bookID) {

    $book = Book::find($bookID);
    if ($book) {

      if (!empty($book->customer->email)){
        // check the pending amount
        $totalPayment = 0;
        $payments = \App\Payments::where('book_id', $book->id)->get();
        if ($payments){
          foreach ($payments as $key => $pay)
          {
              $totalPayment += $pay->import;
          }
        }
        $pending = ($book->total_price - $totalPayment);
        
        if ($pending>0){
          $subject = translateSubject('Recordatorio Pago',$book->customer->country);
          $this->sendEmail_secondPayBook($book,$subject .' '. $book->customer->name);
          return [
            'status' => 'success',
            'response' => "Mail de enviado",
          ];
        }
        $book->send = 1;
        $book->save();

        return [
          'status' => 'danger',
          'response' => "Reserva ya pagada."
        ];
    
       
      }

    }

    return [
        'status' => 'danger',
        'response' => "Registro no encontrado."
    ];
  }

}

<?php

namespace App\Traits;

use App\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\BookPartee;
use App\Repositories\CachedRepository;
use App\Sites;

trait BookEmailsStatus
{
    private function isValidEmail($email){
      if ($email === false) return false;
      if (!$email) return false;
      if (trim($email) == '') return false;
      if (!str_contains($email,'@')) return false;
      return true;
    }

    public function sendMailSite($site,$mailClientContent,$subject,$to,$agency = 0,$template = 'backend.emails.base'){
      if ( trim($mailClientContent) == '') return false;
      $fromMail = config('mail.from.address');
      $fromName = $site['name'];
      $replyTo = ($site['mail_from']) ? $site['mail_from'] : $fromMail;
      return  Mail::send($template, [
            'siteName'    => $site['name'],
            'siteUrl'     => $site['url'],
            'mailContent' => $mailClientContent,
            'title'       => $subject,
            'agency'      => $agency,
        ], function ($message) use ($to, $subject,$fromMail,$fromName,$replyTo) {
            $message->from($fromMail,$fromName);
            $message->to($to);
            $message->subject($subject);
            $message->replyTo($replyTo);
        });
    }

    /**
     *
     * @param type $book
     * @param type $subject
     * @param type $status
     */
    public function sendEmailChangeStatus($book, $subject, $status)
    {
     
      if (!$book->customer->send_mails || !$this->isValidEmail($book->customer->email)) return;
      
        $cachedRepository  = new CachedRepository();
        $otaAgencies = [1,4,6];
        if ($status == 1){
          if (in_array($book->agency,$otaAgencies)){ 
            if ($book->agency == 4){ // Sólo para AirBnb
              $keyMail = $this->getKeyTemplate('1.4');
            } else {
              $keyMail = $this->getKeyTemplate('1.1');
            }
            $subject = "Confirmación de reserva";
          } else {
            $keyMail = $this->getKeyTemplate($status);
          }
        } else {
          //don't send with OTAs
          if ($status == 2 && in_array($book->agency,$otaAgencies)) return;
          $keyMail = $this->getKeyTemplate($status);
        }
       
        if (!$keyMail){
          return;
        }
        $mailClientContent = $this->getMailData($book, $keyMail);
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");

        $subject = translateSubject($subject,$book->customer->country);
        switch ($status)
        {
            case "1":
                $percent            = $this->getPercent($book);
                $mount_percent      = number_format(($book->total_price * $percent), 2, ',', '.');
                $PaylandsController = new \App\Http\Controllers\PaylandsController($cachedRepository);
                
                $amount             = (round($book->total_price * $percent));
                $urlToPayment       = $PaylandsController->generateOrder($amount,'',$book->id);
                $mailClientContent = str_replace('{urlToPayment}', $urlToPayment, $mailClientContent);
                $mailClientContent = str_replace('{mount_percent}', $mount_percent, $mailClientContent);
                if ($percent<=1){
                  $percent = $percent*100;
                }
                $mailClientContent = str_replace('{percent}', $percent, $mailClientContent);
                break;

            case "2":

                $linkPartee = null;
                $BookPartee = BookPartee::where('book_id', $book->id)->first();

                if ($BookPartee && $BookPartee->partee_id > 0)
                {
                    $link = get_shortlink($BookPartee->link);
                    $linkPartee = '<a href="'.$link.'" title="link partee">'.$link.'</a>';
                }
                $mailClientContent = str_replace('{partee}', $linkPartee, $mailClientContent);
                $mailClientContent = str_replace('{LastPayment}', number_format($book->getLastPayment(), 2, ',', '.'), $mailClientContent);
                break;
        }

        $site = Sites::siteData($book->room->site_id);
        $mailClientContent = $this->clearVars($mailClientContent);
       
        $this->sendMailSite($site,$mailClientContent,$subject,$book->customer->email,$book->agency);
        $fromMail = config('mail.from.address');
        $mailClientContent .= '<br/> FROM: '.$fromMail;
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,$keyMail,$subject,$mailClientContent);
    }

    /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_secondPayBook($book, $subject)
    {
      if (!$book->customer->send_mails || !$this->isValidEmail($book->customer->email)) return;
      // if (!$book->customer->email || trim($book->customer->email) == '') return;
      
        $mailClientContent = $this->getMailData($book, 'second_payment_reminder');
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");

        $totalPayment = 0;
        $payments     = \App\Payments::where('book_id', $book->id)->get();
        if (count($payments) > 0)
        {
            foreach ($payments as $key => $pay)
            {
                $totalPayment += $pay->import;
            }
        }
        $percent           = 100 - (round(($totalPayment / $book->total_price) * 100));
        $pendiente         = ($book->total_price - $totalPayment);
        $cachedRepository  = new CachedRepository();
        $PaylandsController= new \App\Http\Controllers\PaylandsController($cachedRepository);
        $urlPayment        = $PaylandsController->generateOrder($pendiente,'',$book->id);
        $mailClientContent = str_replace('{pend_percent}', $percent, $mailClientContent);
        $mailClientContent = str_replace('{payment_amount}', number_format($totalPayment, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{total_payment}', number_format($totalPayment, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{pend_payment}', number_format($pendiente, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{urlPayment_rest}', $urlPayment, $mailClientContent);

        $mailClientContent = $this->clearVars($mailClientContent);

        $site = Sites::siteData($book->room->site_id);
        
        $sended = $this->sendMailSite($site,$mailClientContent,$subject,$book->customer->email,$book->agency);
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'second_payment_reminder',$subject,$mailClientContent);

        return $sended;
    }

    /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_confirmSecondPayBook($book, $subject,$totalPayment,$lastPayment)
    {
      if (!$book->customer->send_mails || !$this->isValidEmail($book->customer->email)) return;
        $mailClientContent = $this->getMailData($book, 'second_payment_confirm');
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");

        $totalPayment = 0;
        $payments     = \App\Payments::where('book_id', $book->id)->get();
        if (count($payments) > 0)
        {
            foreach ($payments as $key => $pay)
            {
                $totalPayment += $pay->import;
            }
        }
        $pendiente         = ($book->total_price - $totalPayment);
        if ($pendiente>0) return; //only if the Booking is totally payment
        
        $mailClientContent = str_replace('{total_payment}', number_format($totalPayment, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{LastPayment}', number_format($lastPayment, 2, ',', '.'), $mailClientContent);
        $mailClientContent = $this->clearVars($mailClientContent);
        $site = Sites::siteData($book->room->site_id);
        
        $sended = $this->sendMailSite($site,$mailClientContent,$subject,$book->customer->email,$book->agency);
       
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'second_payment_reminder',$subject,$mailClientContent);

        return $sended;
    }

    
     /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_confirmCobros($book, $subject,$lastPayment,$email)
    {
      if (!$book->customer->send_mails || !$this->isValidEmail($email)) return;
      // if (!$email || trim($email) == '') return;
        $mailClientContent = $this->getMailData($book, 'payment_receipt');
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");
        
        
        $totalPayment = 0;
        $payments     = \App\Payments::where('book_id', $book->id)->get();
        $cobros_list  = '';
        if ($payments){
          $cobros_list = '<table style="width: 100%; text-align: left;">
                          <tr><th width="30%">Fecha</th><th width="30%">Importe</th><th width="30%">Método</th></tr>
                          <tr><td colspan="3">&nbsp;</td></tr>';

          foreach ($payments as $key => $pay){
            $cobros_list.= '<tr>
                            <th>'. convertDateToShow_text($pay->datePayment,true).'</th>
                            <th>'.round($pay->import,2).' €</th>
                            <th>'.$book->getTypeCobro($pay->type).'</th>
                          </tr>';
            $totalPayment += $pay->import;
           
          }
          
          $cobros_list .= '</table>';
        }
        $pendiente  = ($book->total_price - $totalPayment);
       
        
        $status = 'Su reserva se encuentra al corriente de pago';
        if ($pendiente>0){
          $status = 'Su reserva tiene pendiente de abonar '.round($pendiente,2).' €';
        }
        
        $oAdditional = $book->extrasDynamicList();
        $t_addtional = 0;
        if (count($oAdditional) > 0){
          foreach ($oAdditional as $e){
            $t_addtional += $e->price;
          }
        }
            
        
        
        $linkPartee = null;
        $BookPartee = BookPartee::where('book_id', $book->id)->first();
        if ($BookPartee && $BookPartee->partee_id > 0){
            $link = get_shortlink($BookPartee->link);
            $linkPartee = '<a href="'.$link.'" title="link partee">'.$link.'</a>';
            
        }
        $mailClientContent = str_replace('{partee}', $linkPartee, $mailClientContent);
                
                
        
        $mailClientContent = str_replace('{total_payment}', number_format($totalPayment, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{LastPayment}', number_format($lastPayment, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{priceOTA}', number_format($book->priceOTA, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{cobros_estado}', $status, $mailClientContent);
        $mailClientContent = str_replace('{cobros_list}', $cobros_list, $mailClientContent);
        $mailClientContent = str_replace('{total_adicionales}', $t_addtional, $mailClientContent);
        $mailClientContent = $this->clearVars($mailClientContent);
        $site = Sites::siteData($book->room->site_id);
        
        $sended = $this->sendMailSite($site,$mailClientContent,$subject,$book->customer->email,$book->agency);
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'second_payment_reminder',$subject,$mailClientContent);

        return $sended;
    }
    

    private function getKeyTemplate($status)
    {
      $key = null;
        switch ($status)
        {
            case 1:
            case "1":
                $key = 'reservation_state_changed_reserv';
                break;
            case 1.1:
            case "1.1":
                $key = 'reservation_state_changed_reserv_ota';
                break;
            case 1.4:
            case "1.4":
                $key = 'reservation_state_changed_reserv_airbnb';
                break;
            case 2:
            case "2":
                $key = 'reservation_state_changed_confirm';
                break;
            case 6:
            case "6":
                $key = 'reservation_state_changed_cancel';
                break;
            case 7:
            case "7":
                $key = 'reserva-propietario';
                break;
        }
        return $key;
    }

    /**
     *
     * @param Booking $data
     * @param String  $key
     * @return String HTML
     */
    public function getMailData($data, $keyTemp,$siteName='',$siteUrl='')
    {
      
        $mailClientContent = Settings::getContent($keyTemp,$data->customer->country,$data->room->site_id);

        
        $room_type = $data->room->name;
        if ($data->room->channel_group){
          $roomType = \App\RoomsType::where('channel_group',$data->room->channel_group)->first();
          if ($roomType){
            $room_type = $roomType->title;
          }
        }
        
        $dataContent = array(
            'customer_name'             => $data->customer->name,
            'customer_email'            => $data->customer->email,
            'customer_phone'            => $data->customer->phone,
            'room'                      => $data->room->sizeRooms->name,
            'room_type'                 => $room_type,
            'room_name'                 => $data->room->name,
            'date_start'                => date('d-m-Y', strtotime($data->start)),
            'date_end'                  => date('d-m-Y', strtotime($data->finish)),
            'nigths'                    => $data->nigths,
            'pax'                       => $data->pax,
            'sup_lujo'                  => number_format($data->sup_lujo, 0, '', '.'),
            'comment'                   => $data->comment,
            'book_comments'             => $data->book_comments,
            'total_price'               => number_format($data->total_price, 0, '', '.'),
            'url-condiciones-generales' => url('/condiciones-generales'),
            'link_forfait'              => '',
            'extras_info'               => '',
            'extras_link'               => '',
            'site_name'               => $siteName,
            'site_url'               => $siteUrl
        );
        
     
                
        if (config('app.appl') == 'riad'){
          $dataContent['room'] = $data->room->nameRoom;
        }
        
        if (str_contains($mailClientContent,'link_forfait')){
          $orderFF = \App\Models\Forfaits\Forfaits::getByBook($data->id);
          if ($orderFF){
            $dataContent['link_forfait'] = config('app.forfait.page').encriptID($orderFF->id).'-'. getKeyControl($orderFF->id);
          } else {
            $dataContent['link_forfait'] = config('app.forfait.page');
          }
        }
        if (str_contains($mailClientContent,'extras_link')){
          $dataContent['extras_link'] = get_shortlink(\App\Services\Bookings\ExtrasPurchase::getLink($data));
        }
        
        
        $extrasInfo = $data->getExtraInfo();
        if ($extrasInfo != '') 
            $dataContent['extras_info'] = $extrasInfo;
        /** process the mail content */
        foreach ($dataContent as $k => $v)
        {
            $mailClientContent = str_replace('{' . $k . '}', $v, $mailClientContent);
        }
        return $mailClientContent;

    }

    public function getPercent($book)
    {
        $percent = 0.5;
        $date    = Carbon::createFromFormat('Y-m-d', $book->start);
        $now     = Carbon::now();
        $diff    = $now->diffInDays($date);
        $content = json_decode(Settings::getContent('payment_rule',null,$book->room->site_id));
        if ($content){
          if (isset($content->days) && isset($content->percent)){
            if ($diff <= $content->days){
                $percent = 1;
            } else
            {
                $percent = ($content->percent / 100);
            }
          }
        }
        
        return $percent;
    }

    /**
     * Clear all not loaded vars
     * @param type $text
     * @return type
     */
    public function clearVars($text)
    {

        return preg_replace('/\{(\w+)\}/i', '', $text);

    }

    
    /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_confirmForfaitPayment($cli_email,$cli_name,$subject, $orderText,$totalPayment,$book)
    {
        if(!$this->isValidEmail($cli_email)) return;
        $mailClientContent = $mailClientContent = Settings::getContent('Forfait_email_confirmation_payment');
        $mailClientContent = str_replace('{customer_name}', $cli_name, $mailClientContent);
        $mailClientContent = str_replace('{total_payment}', $totalPayment, $mailClientContent);
        $mailClientContent = str_replace('{forfait_order}', $orderText, $mailClientContent);
        $mailClientContent = $this->clearVars($mailClientContent);
        $sended = Mail::send('backend.emails.forfait', [
            'mailContent' => $mailClientContent,
            'title'       => $subject
        ], function ($message) use ($cli_email, $subject) {
            $message->from(config('mail.from.forfaits'));
            $message->to($cli_email);
            $message->subject($subject);
            $message->replyTo(config('mail.from.forfaits'));
        });
        if ($book){
          \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'second_payment_reminder',$subject,$mailClientContent);
        }

        return $sended;
    }
    
    /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_linkForfaitPayment($cli_email,$cli_name,$subject,$orderText,$link,$book)
    {
      if(!$this->isValidEmail($cli_email)) return;
      // if (trim($cli_email) == '') return;
        $mailClientContent = $mailClientContent = Settings::getContent('Forfait_email_payment_request');
        $mailClientContent = str_replace('{customer_name}', $cli_name, $mailClientContent);
        $mailClientContent = str_replace('{link_forfait}', $link, $mailClientContent);
        $mailClientContent = str_replace('{forfait_order}', $orderText, $mailClientContent);
        $mailClientContent = $this->clearVars($mailClientContent);
        
        $sended = Mail::send('backend.emails.forfait', [
            'mailContent' => $mailClientContent,
            'title'       => $subject
        ], function ($message) use ($cli_email, $subject) {
            $message->from(config('mail.from.forfaits'));
            $message->to($cli_email);
            $message->subject($subject);
            $message->replyTo(config('mail.from.forfaits'));
        });
        
        if ($book){
          \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'send_payment_Forfait',$subject,$mailClientContent);
        }

        return $sended;
    }
    
     /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_CancelForfaitItem($orderText,$link)
    {
        $subject = 'Forfait Cancelado';
        
        $mailClientContent = '<strong>Se ha cancelado un Forfait ya pagado en ForfaitExpress</strong><br/><br/>';
        $mailClientContent .= $orderText.'<br/><br/>';
        $mailClientContent .= "Url de la Orden: <a href='$link' title='Ver Orden'>$link</a>";
        $mailClientContent .= '<p><strong>Compruebe que el forfait fue cancelado correctamente en <a href="forfaitexpress.com">www.forfaitexpress.com</a></strong></p>';
        $sended = Mail::send('backend.emails.forfait', [
            'mailContent' => $mailClientContent,
            'title'       => $subject
        ], function ($message) use ($subject) {
            $message->from(config('mail.from.forfaits'));
            $message->to(config('mail.from.forfaits'));
            $message->subject($subject);
        });
        
        return $sended;
    }
    
     /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_RemindForfaitPayment($book, $orderID,$link)
    {
      if(!$this->isValidEmail($book->customer->email)) return;
      // if (!$book->customer->email || trim($book->customer->email) == '') return;
        $mailClientContent = $this->getMailData($book, 'Forfait_email_payment_request');
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");

        $subject = translateSubject('Recordatorio de pago de Forfait',$book->customer->country);
        
        
        $cachedRepository       = new CachedRepository();
        $ForfaitsItemController = new \App\Http\Controllers\ForfaitsItemController($cachedRepository);
        
        $orderText = $ForfaitsItemController->renderOrder($orderID);
        
        $mailClientContent = str_replace('{forfait_order}', $orderText, $mailClientContent);
        $mailClientContent = str_replace('{link_forfait}', $link, $mailClientContent);
        $mailClientContent = $this->clearVars($mailClientContent);
        $sended = Mail::send('backend.emails.forfait', [
            'mailContent' => $mailClientContent,
            'title'       => $subject
        ], function ($message) use ($book, $subject) {
            $message->from(config('mail.from.forfaits'));
            $message->to($book->customer->email);
            $message->subject($subject);
            $message->replyTo(config('mail.from.forfaits'));
        });
        
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'send_forfait_payment_reminder',$subject,$mailClientContent);

        return $sended;
    }
    
     /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_FianzaPayment($book,$total_price, $link)
    {
      if(!$this->isValidEmail($book->customer->email)) return;
      // if (!$book->customer->email || trim($book->customer->email) == '') return;
        $mailClientContent = $this->getMailData($book, 'fianza_request_deferred');
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");

        $subject = translateSubject('Generar Fianza',$book->customer->country);
        
        $mailClientContent = str_replace('{payment_amount}', $total_price, $mailClientContent);
        $mailClientContent = str_replace('{urlPayment}', $link, $mailClientContent);
        $mailClientContent = $this->clearVars($mailClientContent);
        $site = Sites::siteData($book->room->site_id);
        
        $sended = Mail::send('backend.emails.base', [
            'mailContent' => $mailClientContent,
            'title'       => $subject,
            'siteName'    => $site['name'],
            'siteUrl'     => $site['url'],
        ], function ($message) use ($book, $subject) {
            $message->from(config('mail.from.address'));
            $message->to($book->customer->email);
            $message->subject($subject);
            $message->replyTo(config('mail.from.address'));
        });
        
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'fianza_request_deferred',$subject,$mailClientContent);

        return $sended;
    }
    
        /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_confirmDeferrend($book, $subject,$totalPayment)
    {
      if(!$this->isValidEmail($book->customer->email)) return;
      // if (!$book->customer->email || trim($book->customer->email) == '') return;
        $mailClientContent = $this->getMailData($book, 'fianza_confirm_deferred');
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");
        
        $mailClientContent = str_replace('{payment_amount}', number_format($totalPayment, 2, ',', '.'), $mailClientContent);
        $mailClientContent = $this->clearVars($mailClientContent);

        $site = Sites::siteData($book->room->site_id);
        
        $sended = Mail::send('backend.emails.base', [
            'siteName'    => $site['name'],
            'siteUrl'     => $site['url'],
            'mailContent' => $mailClientContent,
            'title'       => $subject
        ], function ($message) use ($book, $subject) {
            $message->from(config('mail.from.address'));
            $message->to($book->customer->email);
            $message->subject($subject);
            $message->replyTo(config('mail.from.address'));
        });
        
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'fianza_confirm_deferred',$subject,$mailClientContent);

        return $sended;
    }
    
     /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_ForfaitClasses($orderText,$email,$subject)
    {
       
      if(!$this->isValidEmail($email)) return;
        $mailClientContent = 'Hola, confírmanos disponibilidad para las clases de este cliente:<br/><br/>';
        $mailClientContent .= $orderText.'<br/><br/>';
        $sended = Mail::send('backend.emails.forfait', [
            'mailContent' => $mailClientContent,
            'title'       => $subject
        ], function ($message) use ($subject,$email) {
            $message->from(config('mail.from.forfaits'));
            $message->to($email);
            $message->subject($subject);
        });
        
        return $sended;
    }
     /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_ForfaitNewOrder($order,$link)
    {
       
      $subject = 'Nueva solicitud pública de Forfaits';
      
      $mailClientContent = 'Hola, un nuevo usuario ha solicitado Forfaits desde la parte pública:<br/><br/>';
       
      $clientMail = $order->email;
      $mailClientContent .= '<b>Nombre:</b>:'.$order->name.'<br/><br/>';
      $mailClientContent .= '<b>e-mail:</b>:'.$clientMail.'<br/><br/>';
      $mailClientContent .= '<b>Teléfono:</b>:'.$order->phone.'<br/><br/>';
      $mailClientContent .= '<b>Petición:</b>:'.$order->more_info.'<br/><br/>';

      $mailClientContent .= '<br/><br/>Puedes acceder a la vista pública de la orden a travéz del enlace'
              . '<br/><a href="'.$link.'" title="Ir al Forfaits">'.$link.'</a><br/><br/>';
        $sended = Mail::send('backend.emails.forfait', [
            'mailContent' => $mailClientContent,
            'title'       => $subject
        ], function ($message) use ($subject,$clientMail) {
            $message->from($clientMail);
            $message->to(config('mail.from.forfaits'));
            $message->subject($subject);
        });
        
        return $sended;
    }
    
    
    /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_Encuesta($book, $subject)
    {
      if(!$this->isValidEmail($book->customer->email)) return;
      // if (!$book->customer->email || trim($book->customer->email) == '') return false;
        $mailClientContent = $this->getMailData($book, 'send_encuesta');
        $subject = $this->getMailData($book, 'send_encuesta_subject');
        
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");
        $site = Sites::siteData($book->room->site_id);
        $linkG = '9LroAhWdErkGHbhcChwQ4dUDCAs&uact=5';
        switch ($book->room->site_id){
          case 1:
            $linkG = 'https://www.google.com/search?q='. urlencode($site['url']);
            break;
          case 2:
            $linkG = 'https://www.google.com/search?q='. urlencode($site['url']);
            break;
          case 3:
            $linkG = 'https://www.google.com/search?q='. urlencode($site['url']);
            break;
        }
        $link = '<a href="'.$linkG.'" title="Cargar opinión"><img src="'.url('/img/g_store.jpg').'" width="80px" height="80px"></a>';
        $email = $book->customer->email;
        
                
        $mailClientContent = str_replace('{url_encuesta}', url('/encuesta-satisfaccion/' . base64_encode($book->id)), $mailClientContent);
        $mailClientContent = str_replace('{google_link}', $link, $mailClientContent);
        $mailClientContent = $this->clearVars($mailClientContent);
        
        
        $sended = $this->sendMailSite($site,$mailClientContent,$subject,$book->customer->email,$book->agency);
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'second_encuesta',$subject,$mailClientContent);

        return $sended;
    }
    
     /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_ContestadoAdvanced($book,$text)
    {
        $subject = 'Disponibilidad para tu reserva';
        if(!$this->isValidEmail($book->customer->email)) return;
        // if (!$book->customer->email || trim($book->customer->email) == '') return false;
        
        $to = trim($book->customer->email);
        $site = Sites::siteData($book->room->site_id);
        $sended = $this->sendMailSite($site,$text,$subject,$book->customer->email,$book->agency);
        return $sended;
    }
    
    function getSafeBoxMensaje($book,$msgKey,$safebox){
      $messageSMS = $this->getMailData($book,$msgKey);
      $content = str_replace('{buzon}', $safebox->box_name, $messageSMS);
      $content = str_replace('{buzon_key}', $safebox->keybox, $content);
      $content = str_replace('{buzon_color}', $safebox->color, $content);
      $content = str_replace('{buzon_caja}', $safebox->caja, $content);
      return $this->clearVars($content);
    }
    
    function sendSafeBoxMensaje($book,$msgKey,$safebox){
      
      if(!$this->isValidEmail($book->customer->email)) return;
      // if (!$book->customer->email || trim($book->customer->email) == '') return false;
      
      $message = $this->getSafeBoxMensaje($book,$msgKey,$safebox);
      $subject = translateSubject('Recordatorio para retiro de llaves',$book->customer->country);
      
      
      $to = trim($book->customer->email);
      $site = Sites::siteData($book->room->site_id);
      $sended = $this->sendMailSite($site,$message,$subject,$to);
      return $sended;
    }
    
    function sendSimpleMail($subject,$site_id,$email,$message){
      
      // if (!isset($email) || trim($email) == '') return false;
      if(!$this->isValidEmail($email)) return;
      
      $to = trim($email);
      $site = Sites::siteData($site_id);
      $sended = $this->sendMailSite($site,$message,$subject,$to);
      return $sended;
    }
    
     public function sendEmail_WidgetPayment($book,$amount)
    {
       
//      $subject = 'Nueva reserva desde web pública';
      
      $body = 'Hola, un nuevo usuario ha pagado el 50% de la reserva hecha desde la web pública:<br/><br/>';
       
      $customer = $book->customer;
      $subject = 'RESERVA WEBDIRECT : '.$customer->name;
      $body .= '<b>Nombre:</b>: '.$customer->name.'<br/><br/>';
      $body .= '<b>e-mail:</b>: '.$customer->email.'<br/><br/>';
      $body .= '<b>Teléfono:</b>: '.$customer->phone.'<br/><br/>';
      $body .= '<b>Habitación:</b> '.$book->room->name.'<br/><br/>';
      $body .= '<b>PVP:</b>: '.number_format($book->total_price, 0, '', '.').'<br/><br/>';
      $body .= '<b>Pagado:</b> '. moneda($amount).'<br/><br/>';
      $body .= '<b>Fechas:</b> '.convertDateToShow_text($book->start).' - '. convertDateToShow_text($book->finish).'<br/><br/>';
      $body .= '<b>Noches:</b> '.$book->nigths.'<br/><br/>';
      $body .= '<b>Paxs:</b> '.$book->pax.'<br/><br/>';
      $body .= '<b>Comtentarios:</b> '.$book->book_comments.'<br/><br/>';
      
      $site = Sites::siteData($book->room->site_id);
      $sended = $this->sendMailSite($site,$body,$subject,"reservas@riadpuertasdelalbaicin.com");
//enviar al cliente
        $subject = 'RESERVA EN '.$site['name'];
        $mailClientContent = $this->getMailData($book, 'web_payment');
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");

        $totalPayment = $amount;
        $percent           = 100 - (round(($totalPayment / $book->total_price) * 100));
        $pendiente         = ($book->total_price - $totalPayment);
        $cachedRepository  = new CachedRepository();
        $PaylandsController= new \App\Http\Controllers\PaylandsController($cachedRepository);
        $urlPayment        = $PaylandsController->generateOrder($pendiente,'',$book->id);
        $mailClientContent = str_replace('{pend_percent}', $percent, $mailClientContent);
        $mailClientContent = str_replace('{payment_amount}', number_format($totalPayment, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{total_payment}', number_format($totalPayment, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{pend_payment}', number_format($pendiente, 2, ',', '.'), $mailClientContent);
        $mailClientContent = str_replace('{urlPayment_rest}', $urlPayment, $mailClientContent);

        $mailClientContent = $this->clearVars($mailClientContent);

        $site = Sites::siteData($book->room->site_id);
        $sended = $this->sendMailSite($site,$mailClientContent,$subject,$book->customer->email,$book->agency);       
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'web_payment',$subject,$mailClientContent);

        return $sended;
    }
    
    function sendMailGral($book,$mailKey,$subject,$data=array()){
      
      if (!$book->customer->email || trim($book->customer->email) == '') return;
        $mailText = $this->getMailData($book, $mailKey);
        $site = Sites::siteData($book->room->site_id);

        foreach ($data as $k=>$v){
          $mailText = str_replace('{'.$k.'}', $v, $mailText);
        }
        
        $mailText = $this->clearVars($mailText);
        $sended = $this->sendMailSite($site,$mailText,$subject,$book->customer->email,$book->agency);
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'mail_gral',$subject,$mailText);

        return $sended;
    }
    
    /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_contactCheckin($book, $subject)
    {
      // if (!$book->customer->email || trim($book->customer->email) == '') return false;
      if(!$this->isValidEmail($book->customer->email)) return;
        $mailClientContent = $this->getMailData($book, 'mail_checkin_msg');
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");
        $mailClientContent = $this->clearVars($mailClientContent);
        $site = Sites::siteData($book->room->site_id);

        $sended = $this->sendMailSite($site,$mailClientContent,$subject,$book->customer->email,$book->agency);
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'mail_checkin_msg',$subject,$mailClientContent);

        return $sended;
    }

    
    /**
     *
     * @param type $book
     * @param type $subject
     */
    public function sendEmail_blockCancel($book, $subject)
    {
      if (!$book->customer->email || trim($book->customer->email) == '') return false;
      
        $site = Sites::siteData($book->room->site_id);
        $mailClientContent = $this->getMailData($book, 'mail_cancelBloq',$site['name'],$site['url']);
        setlocale(LC_TIME, "ES");
        setlocale(LC_TIME, "es_ES");
        $mailClientContent = $this->clearVars($mailClientContent);

        $sended = $this->sendMailSite($site,$mailClientContent,$subject,$book->customer->email,$book->agency);
        \App\BookLogs::saveLog($book->id,$book->room_id,$book->customer->email,'mail_cancelBloq',$subject,$mailClientContent);

        return $sended;
    }




}

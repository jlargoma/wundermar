<?php
namespace App\Services;

class POP3Service{
 
    //incluye el servidor, el puerto 993 que es para imap, e indicamos que no valide con ssl
    var $mailbox="{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX";
 
    function pop3_login($host,$port,$user,$pass,$folder="INBOX",$ssl=false) 
    { 
        $ssl=($ssl==false)?"/novalidate-cert":""; 
        return (imap_open("{"."$host:$port/pop3$ssl"."}$folder",$user,$pass)); 
    } 
    
    function getMails($inbox,$filter,$type='from') {
      switch ($type){
        case 'mail':
          $emails=imap_search($inbox,'CC "'.$filter.'" OR FROM "'.$filter.'" OR TO "'.$filter.'"');
          break;
        case 'from':
          $emails=imap_search($inbox,'FROM "'.$filter.'"');
          break;
        case 'to':
          $emails=imap_search($inbox,'TO "'.$filter.'"');
          break;
        case 'cc':
          $emails=imap_search($inbox,'CC "'.$filter.'"');
          break;
        case 'body':
          $emails=imap_search($inbox,'BODY "'.$filter.'" SINCE "20-OCT-2019"');
          break;
        case 'since':
          $emails=imap_search($inbox,'SINCE "'.$filter.'"');
          break;
        default :
          $emails=imap_search($inbox,'NEW');
          break;
          
      }
      return $emails;
    }
    
    function getMailsData($inbox,$emails){
      $result = [];
      $count = 0;
      if($emails) {
        foreach($emails as $email_number){
          $headers = imap_fetch_overview($inbox,$email_number);
          //Read the headers
          $subject = $from = $time = null;
          foreach($headers as $over){
            if(isset($over->subject)){
              $subject = $this->fix_text_subject($over->subject);
            }
            if(isset($over->from)){
              $from = $over->from;
            }
            if(isset($over->date)){
              $time = strtotime($over->date);
            }
          }
          
          //Create the item
          $result[] = [
              'msgKey'       => md5($from).':'.$time,
              'subject'      => $subject,
              'email_number' => $email_number,
              'from'         => $from,
              'time'         => $time
           ];
        }
        
        imap_close($inbox); 
      return $result;
      }
      
      imap_close($inbox); 
      return $result;
    }

    //metodo que realiza todo el trabajo
    function obtenerAsuntosDelMails($inbox){
 
      
          //con la instrucción SINCE mas la fecha entre apostrofes ('')
          //indicamos que deseamos los mails desde una fecha en especifico
          //imap_search sirve para realizar un filtrado de los mails.
         $emails=imap_search($inbox,'SINCE "'.$this->fecha.'"');
 
         //comprbamos si existen mails con el la busqueda otorgada
            if($emails) {
                 //ahora recorremos los mails
                 foreach($emails as $email_number)
                {
                     //leemos las cabeceras de mail por mail enviando el inbox de nuestra conexión
                     //enviando el identificdor del mail
                    $overview = imap_fetch_overview($inbox,$email_number);
//                    $mensaje  = imap_fetchbody($inbox, $email_number);
                    $mensaje = imap_qprint(imap_body($inbox, $email_number));
                    
                    $PLAIN = imap_fetchbody($inbox,$email_number,1);
                    $HTML = imap_fetchbody($inbox,$email_number,2);
                        
                    echo $mensaje.'<hr><br><hr>';
                    echo $PLAIN.'<hr><br><hr>';
                    echo $HTML.'<hr><br><hr>';
                            
                     imap_close($inbox); 
                     
                    //ahora recorremos las cabeceras para obtener el asunto
                    foreach($overview as $over){
                        //comprobamos que exista el asunto (subject) en la cabecera
                        //y si es asi continuamos
                        if(isset($over->subject)){
 
                            //aqui pasa algo curioso
                            //el asunto vendra con caracteres raros
                            //para ello anexo una función que lo limpia y lo muestra ya legible
                            //en lenguaje mortal
                            $asunto=$this->fix_text_subject($over->subject);

//                            echo utf8_decode($asunto)."<br>";
                            echo ($asunto)."<br>";
                        }
                    }
 
                }
            }
 
    }
 
    //arregla texto de asunto
    function fix_text_subject($str)
    {
        $subject = '';
        $subject_array = imap_mime_header_decode($str);
 
        foreach ($subject_array AS $obj)
            $subject .= utf8_encode(rtrim($obj->text, "t"));
 
        return $subject;
    }
    
    public function message($number)
    {
        $info = imap_fetchstructure($this->connection, $number, 0);
 
        if($info->encoding == 3){
            $message = base64_decode(imap_fetchbody($this->connection, $number, 1));
        }
        elseif($info->encoding == 4){
            $message = imap_qprint(imap_fetchbody($this->connection, $number, 1));
        }
        else
        {
            $message = imap_fetchbody($this->connection, $number, 1);
        }
        //$message = imap_fetchbody($this->connection, $number, 2);
        return decode_qprint($message);
    }
 
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Book;
use Carbon\Carbon;

class BookPartee extends Model
{
  static $status = array(
    "VACIO",// indicando que ningún huésped ha cubierto el formulario de check-in online todavía; 
    "HUESPEDES", // indicando que al menos un huésped ha cubierto el enlace de check-in online;
    "FINALIZADO" // indicando que el parte de viajeros ha sido finalizado, es decir, se han creado los partes de viajeros y se ha realizado el envío al cuerpo policial correspondiente
  );
  
  
  public function book()
  {
          return $this->belongsTo(Book::class)->first();
  }
  
  public function print_status($bookID,$bookStart,$bookGuest,$action=false) {
//    date_default_timezone_set('Europe/Madrid');
    
    // para enviar a la policia: finish_partee
    
    /**
     * Estados:
     * VACIO indicando que ningún huésped ha cubierto el formulario de check-in online todavía; 
     * HUESPEDES indicando que al menos un huésped ha cubierto el enlace de check-in online;
     * FINALIZADO indicando que el parte de viajeros ha sido finalizado, es decir, se han creado los partes de viajeros y se ha realizado el envío al cuerpo policial
correspondiente.
     */

    $msgPolice = '';
    $msgPartee = null;
      
    $policeAction = '';
    $ParteeAction ='';
    
    $limitDay = date('d \d\e M', strtotime('+1 days '.$bookStart));
    $alertDays = '<br/><small>a partir del día '.$limitDay.' , el enlace no estará disponible.... <br/>'
            . 'tendrás que hacer el registro directamente en www.partee.com</small>';
    
    if(!$this->partee_id || $this->partee_id<1){
      $policeman = '<div class="policeman grey tooltip-2"> <div class="tooltiptext">Enviar Partee a la Policia</div></div>';
      return '<div class="tooltip-2 sendPartee cursor pf-icon" data-id="'.$bookID.'" >'
      . '<i class="fa fa-file-powerpoint partee-form"></i>'
      . '<div class="tooltiptext">Partee no creado'.$alertDays.'</div>'
      . '</div>'.$policeman;
      
    }
    
    if ($this->status == "FINALIZADO"){
      if (isset($this->date_finish)){
        $msgPolice = '<br>'.date('d M H:i', strtotime($this->date_finish));
      } else {
        preg_match('|([0-9])*(\-FINALIZADO)|', $this->log_data, $data);
        if (isset($data[0])){
          $msgPolice = 'Finalizado el '.date('d M H:i', intval($data[0]));
        }
      }
      
      return '<div class="tooltip-2 showParteeData cursor pf-icon" data-partee_id="'.$this->partee_id.'" >'
      . '<i class="fa fa-file-powerpoint partee-form complete"></i>'
      . '<div class="tooltiptext">Partee Completado<br>'.intval($this->guestNumber).' huéspeds</div></div>'
      . '<div class="policeman green tooltip-2"> <div class="tooltiptext">Partee enviado a la Policia<br> '.$msgPolice.'</div></div>';
    }
       
    
    $msgPartee = 'Partee';  
    $parteeStatus = '';  
    $policeStatus = 'grey';
    if ($this->status == "HUESPEDES"){
      
      if ($this->guestNumber){
        if ($this->guestNumber !== $bookGuest){
          $msgPartee .= '<br>Incompleto: '.$this->guestNumber.' de '.$bookGuest;
          if ($action){
            $ParteeAction = 'sendPartee';
            $msgPartee .= '<br><b>Enviar recordatorio</b>';
            if ($this->TimeControl($bookStart)) $policeStatus = 'red finish_partee';
              else $policeStatus = 'grey';
          }
        } else {
          $alertDays = '';
          $parteeStatus = 'complete';
          $msgPartee .= '<br>Completado';
          if ($this->TimeControl($bookStart)) $policeStatus = $action ? 'red finish_partee' : '';
            else $policeStatus = 'grey';
        }
      } else {
          $msgPartee .= '<br>No posee huéspeds cargados';
          if ($action){
            $ParteeAction = 'sendPartee';
            $policeStatus = 'grey';
            $msgPartee .= '<br><b>Enviar recordatorio</b>';
          }
      }
      if (isset($this->date_complete)){
        $msgPartee .= '<br>Chequeado el '.date('d M H:i', strtotime($this->date_complete));
      }
    } else {
      if ($action){
        $ParteeAction = 'sendPartee';
        $msgPartee .= '<br><b>Enviar recordatorio</b>';
      }

    }

    $msgPartee .=  $alertDays;
    $policeman = '<div class="policeman  tooltip-2 '.$policeStatus.'" data-id="'.$bookID.'"> <div class="tooltiptext">Enviar Partee a la Policia</div></div>';
     
    
      return '<div class="tooltip-2 pf-icon">'
      . '<i class="fa fa-file-powerpoint partee-form '.$parteeStatus.' '.$ParteeAction.' cursor" data-id="'.$bookID.'" data-sms="'.intval($this->sentSMS).'"></i>'
      . '<div class="tooltiptext">'.$msgPartee.'</div>'
      . '</div>'.$policeman;
   
  }
  
  /**
   * Check if can send to the police by time
   * 
   * @param type $bookStart
   * @return boolean
   */
  private function TimeControl($bookStart) {
    date_default_timezone_set('Europe/Madrid');
    $current = Carbon::now();
    $checkin = new Carbon($bookStart);
    if ($current>=$checkin){
      //Check after 12 am
      if ( intval($current->format('H')) >= 12){
        return true;
      }
    }
    return false;
  }
}

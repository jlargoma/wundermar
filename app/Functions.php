<?php

function desencriptID($text){

    $text = trim($text);
    $char_list = "ASDFNIUBNFJGBUOABFOGSJLAVV";
    $char_salt = "ABCDEFabcdef";
    $text_len = strlen($text);
    $result = "";

    for($i = 0; $i < $text_len; $i++)
    {
      if (strpos($char_salt, $text[$i]) !== FALSE){
        $result = $text[$i].$result;
      } else {
        $aux = strpos($char_list, $text[$i]);
        if ($aux > 9){
          $result = ($aux-10).$result;
        } else {
          $result = $aux.$result;
        }
      }
    }
    $id = hexdec($result);
    $cantControl = strlen($result);
    if (substr($text,-1) == $cantControl) return $id/217;
    if (substr($text,-2) == $cantControl) return $id/217;
    return 'null';
}

function encriptID($data){
    $text = strtoupper(dechex($data*217));
    $char_list = "ASDFNIUBNFJGBUOABFOGSJLAVV";
    $char_salt = "ABCDEFabcdef";
    $text_len = strlen($text);
    $result = "";

    for($i = 0; $i < $text_len; $i++)
    {
      if (strpos($char_salt, $text[$i]) !== FALSE){
        $result = $text[$i].$result;
      } else {
        if (($i%2) == 0){
          $result = $char_list[$text[$i]+10].$result;
        } else {
          $result = $char_list[$text[$i]].$result;
        }
      }
    }
    
    $length = strlen($result);
    $newVal = '';
    for ($i=0; $i<$length; $i++) {
      $newVal .= (rand(0, 117)). $result[$i];
    }
    return ($newVal).$length;
}

function getKeyControl($id){
  $aux = md5($id);
  return strtoupper(preg_replace('/[0-9]/','', $aux)).intval(preg_replace('/[a-z]/','', $aux));
}

function assetV($uri){
  $uri_asset = asset($uri);
  $v = '2.33';//config('app.version');
  return $uri_asset.'?'.$v;
}
function assetNew($uri){
  $uri_asset = asset('/new-asset/'.$uri);
  $v = config('app.version');
  return $uri_asset.'?'.$v;
}



function lstMonths($startYear,$endYear,$format='ym',$name=false){
  $diff = $startYear->diffInMonths($endYear) + 1;
  $lstMonths = [];
  if (is_numeric($diff) && $diff>0){
    $aux = strtotime($startYear);
    while ($diff>0){
        switch ($name){
            case 'short':
                $lstMonths[date($format,$aux)] =['m' => date('n',$aux), 'y' => date('y',$aux),'name'=> getMonthsSpanish(date('n',$aux))];
                break;
            case 'long':
                $lstMonths[date($format,$aux)] =['m' => date('n',$aux), 'y' => date('y',$aux),'name'=> getMonthsSpanish(date('n',$aux),false)];
                break;
            default :
                $lstMonths[date($format,$aux)] =['m' => date('n',$aux), 'y' => date('y',$aux)];
                break;
                
        }
      $aux = strtotime("+1 month", $aux);
      $diff--;
    }
  }
  
  return $lstMonths;
}

function getMonthsSpanish($m,$min=true,$list=false){
  if ($min){
    $arrayMonth = ['','Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sept', 'Oct', 'Nov', 'Dic'];
  } else {
    $arrayMonth = ['','Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
  }
  if ($list)return $arrayMonth;
  $m = intval($m);
  return isset($arrayMonth[$m]) ? $arrayMonth[$m] : '';
}

function listDaysSpanish($min = false) {
    if ($min) {
      $array = [
          1 => 'Lun', 
          2 => 'Mar', 
          3 => 'Mié', 
          4 => 'Jue', 
          5 => 'Vie',
          6 => 'Sáb',
          0 => 'Dom', 
          ];
    } else {
      $array = [
          1 => 'Lunes', 
          2 => 'Martes', 
          3 => 'Miércoles', 
          4 => 'Jueves', 
          5 => 'Viernes',
          6 => 'Sábado',
          0 => 'Domingo', 
          ];
    }
    return $array;
  }
  
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function translateSubject($text,$lng='es'){
  
  $lng = App\Settings::getLenguaje(strtoupper($lng));
  
  if ($lng == 'es'){
    return $text;
  }
  
  $texts = [
  'Bloqueo de reserva y datos de pago',
  'Correo de Reserva de Propietario',
  'Reserva denegada',
  'Confirmación de reserva (pago parcial)',
  'Solicitud disponibilidad',
  'Recordatorio Pago',
  'Confirmación de Pago',
  'Recordatorio para Completado de Partee'
  ];
  
  $trasl = [
    'Booking Request and payment details',
    'Correo de Reserva de Propietario',
    'unavailable request',
    'Booking confirmation (partial payment. done)',
    'Solicitud disponibilidad',
    'Second Payment Reminder',
    'Payment confirmation',
    'Reminder to Complete Police information'
  ];
  
  $text = trim($text);
  foreach ($texts as $k=>$t){
    if ($t == $text){
      return isset($trasl[$k]) ? $trasl[$k] : $text;
    }
  }
  
  return $text;
}

function getUrlToPay($token){
  if (config('app.appl') == "riad" || config('app.appl') == "miramarLocal"){
    $urlPay = route('front.payments',$token);
  } else {
    $urlPay = 'https://miramarski.com/payments-forms?t='.$token;
  }
  
  return $urlPay;
}

function getCloudfl($url){
  
  return $url;
  global $CDN_URL;
  
  if (!$CDN_URL){
    $CDN_URL = config('app.cdn');
  }
  if (strpos($url, 'apartamentosierranevada.net')>0){
    $aux = parse_url($url);
    if (is_array($aux)){
      $return = $CDN_URL;
      if (isset($aux['path'])) $return .= $aux['path'];
      if (isset($aux['query'])) $return .= '?'.$aux['query'];
      return $return;
    }
  }

  return $CDN_URL.$url;
}

  function convertDateToShow($date,$yearsComplete=false){
    $date= trim($date);
    if ($date){
      
      $aux = explode('-',$date);
      if (is_array($aux) && count($aux)==3){
        if ($yearsComplete) return $aux[2].'/'.$aux[1].'/'.$aux[0];
        return $aux[2].'/'.$aux[1].'/'.($aux[0]-2000);
      }
    }
    return null;
  }
  
  function convertDateToShow_text($date,$year = false){
    $date= trim($date);
    if ($date){
      
      $aux = explode('-',$date);
      if (is_array($aux) && count($aux)==3){
        if ($year){
          if ($year == 2){
            return $aux[2].' de '.getMonthsSpanish($aux[1]).' '.$aux[0];
          }
          return $aux[2].' '.getMonthsSpanish($aux[1]).', '.($aux[0]-2000);
        }
        return $aux[2].' '.getMonthsSpanish($aux[1]);
      }
    }
    return null;
  }
  function convertDateTimeToShow_text($datetime,$year = false){
    $datetime= trim($datetime);
    if ($datetime){
      $time = strtotime($datetime);
      return date('d',$time).' '.getMonthsSpanish(date('n',$time)).', '.date('y H:i',$time)."hrs";
    }
    return null;
  }
  function convertDateToDB($date){
    $date= trim($date);
    if ($date){
      $aux = explode('/',$date);
      if (is_array($aux) && count($aux)==3){
        $month = ($aux[1]<10) ? '0'.intval($aux[1]) : $aux[1];
        $day = ($aux[0]<10) ? '0'.intval($aux[0]) : $aux[0];
        return $aux[2].'-'.$month.'-'.$day;
      }
    }
    return null;
  }
  function dateMin($date){
    $date= trim($date);
    if ($date){
      
      $aux = explode('-',$date);
      if (is_array($aux) && count($aux)==3){
        return $aux[2].' '.getMonthsSpanish(intval($aux[1]));
      }
    }
    return null;
  }
  
function paylandCost($val){
  if ($val>0)
    return round((1.2 * $val) / 100,2);
  
  return 0;
}

function urlBase(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}

function date_policies($date){
  $time = strtotime($date);
  
  return  date('d',$time).' '.
          getMonthsSpanish(date('n',$time)).
          ' del '.date('Y',$time).
          ' a las '.date('H:i',$time).' hrs.';
}

function ob_html_compress($buffer){
  
   $search = array(
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    );

    $replace = array(
        '>',
        '<',
        '\\1',
        ''
    );

    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;
  
  return preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/"),array('',' '),str_replace(array("\n","\r","\t"),'',$buf));
    return str_replace(array("\n","\r","\t"),'',$buf);
}

function getArrayMonth($startYear,$endYear,$index=false){

  $diff = $startYear->diffInMonths($endYear) + 1;
  $thisMonth = date('m');
  
  $result = [];
  $aux = $startYear->format('n');
  $auxY = $startYear->format('y');
  $c_month = null;
  for ($i = 0; $i < $diff; $i++) {
      $c_month = $aux + $i;
      if ($c_month == 13) {
        $auxY++;
      }
      if ($c_month > 12) {
        $c_month -= 12;
      }
      $tMonth = $c_month;
      if ($tMonth<10) $tMonth = '0'.$tMonth;
      if($index) $result[$auxY.$tMonth] = ['y' => $auxY,'m'=> $tMonth];
        else $result[] = ['y' => $auxY,'m'=> $tMonth];
    }
  return $result;
}


function getUsrRole(){
  global $uRole;
  
  if (isset($uRole) && !empty($uRole)) {
    return $uRole;
  }
  
  $uRole = Auth::user()->role;
  return $uRole;
}

function configZodomusAptos(){
  $obj = new \App\Services\OtaGateway\Config();
  return $obj->getRoomsName();
}

function calcNights($start,$end) {
  //Create a date object out of a string (e.g. from a database):
  $date1 = date_create_from_format('Y-m-d', date('Y-m-d', strtotime($start)));

  //Create a date object out of today's date:
  $date2 = date_create_from_format('Y-m-d', date('Y-m-d', strtotime($end)));

  //Create a comparison of the two dates and store it in an array:
  $diff = date_diff($date1, $date2);

  return $diff->days;
//  return intval(ceil((strtotime($end)-strtotime($start))/(24*60*60)));
}

function getAptosSite(){
  return [
    1=>['APTO'],
    ];
}
function getAptosChannel(){
  return [
    'APTO'
    ];
}
function getAptosBySite($site=1){
  $all = getAptosSite();
  if (isset($all[$site])){
    return $all[$site];
  }
  return null;
  
}
function configOtasAptosName($site=1){
  
  if ($site == 1){
    return [
      'APTO' => 'APTO',
    ];
  }
  
  return [
      'APTO' => 'APTO',
  ];
}

function moneda($mount,$cero=true,$decimals=0){
  if ($cero)  return number_format($mount, $decimals, ',', '.' ).' €';
  
  if ($mount != 0) return number_format($mount, $decimals, ',', '.' ).' €';
  return '--';
  
}

function numero($mount,$cero=true,$decimals=0){
  if(!is_numeric($mount)) $mount = intval($mount);
  if ($cero)  return number_format($mount, $decimals, ',', '.' );
  
  if ($mount != 0) return number_format($mount, $decimals, ',', '.' );
  return '--';
  
}

function noIndex(){
  $haystack = ['politica-privacidad','aviso-legal','politica-cookies','condiciones-contratacion'];
  $pathRequest = Request::path(); 
  if (in_array($pathRequest, $haystack)){
    ?>
    <meta name="robots" content="noindex">
    <?php
  }
}


function createDateToDB($y,$m,$d){
  
  if($m<10) $m = '0'.$m;
  if($d<10) $d = '0'.$d;
  
  return $y.'-'.$m.'-'.$d;
 
}

function show_isset($array,$index,$dfl=null){
  if(isset($array[$index])){
    echo $array[$index];
  } else {
    if($dfl) echo $dfl;
  }
}
/**
 * Format a String to Slug
 * @param type $string
 * @return type
 */
function clearTitle($string){
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

function firstDayMonth($year,$month){
  // First day of a specific month
  $d = new \DateTime($year . '-' . $month . '-01');
  $d->modify('first day of this month');
  return $d->format('Y-m-d');
}
function lastDayMonth($year,$month){
      // last day of a specific month
      $d = new \DateTime($year . '-' . $month . '-01');
      $d->modify('last day of this month');
      return $d->format('Y-m-d');
}
function colors(){
  return ['#9b59ff','#295d9b','#FCD000','red','#7d3d25','#47CF73','#a7dae7','#1fa7c0','#b2d33d','#3aaa49'];
}

function printColor($id){
  $lst = colors();
  $count = count($lst);
  if ($id<$count) return $lst[$id];
  
  $id2 = $id/$count;
  if ($id2<$count) return $lst[$id2];
  
  $id = intval($id%$count);
  return $lst[$id];
}

function value_isset($array,$index){
  if(isset($array[$index])){
    echo 'value="'.$array[$index].'"';
  } else {
    echo 'value=""';
  }
}

function convertBold($detail){
  $detail = trim(nl2br($detail));
  if (!$detail || trim($detail) == '') return '';
  $start = false;
  $aDetail = explode('*', $detail);
  $result = '';
  if ($detail[0] == '*'){
    $result = '<b>';
    $start =  true;
  }
  if (count($aDetail)>0){
    foreach ($aDetail as $v){
      if ($v == "") continue;
      $result .= $v;
      if ($start)  $result .= '</b>';
      else  $result .= '<b>';
      
      $start = !$start;
    }
  }
  
  if ($start)  $result .= '</b>';
  return $result;
    
}

function removeIVA($price,$iva){
  if (!$price || !$iva) return 0;
  return round($price / (1 + $iva/100),2);
}

function whatsappFormat($texto){
      $texto = nl2br($texto);
      $whatsapp = str_replace('&nbsp;', ' ', $texto);
      $whatsapp = str_replace('<strong>', '*', $whatsapp);
      $whatsapp = str_replace('</strong>', '*', $whatsapp);
      $whatsapp = str_replace('<br />', '%0D%0A', $whatsapp);
      $whatsapp = str_replace('</p>', '%0D%0A', $whatsapp);
      $whatsapp = strip_tags($whatsapp);
      return $whatsapp;
}

function whatsappUnFormat($text){
      $string = htmlentities($text, null, 'utf-8');
      $content = str_replace("&nbsp;", " ", $string);
      $text = html_entity_decode($content);
      $text = str_replace(' *', ' <b>', $text);
      $text = str_replace("* ", '</b> ', $text);
      $text = nl2br($text);
      $text = str_replace('*', ' <b>', $text);
      return $text;
}

/**
 * 
 * @param type $cc
 * @param type $extra_check
 * @return type
 */
function check_cc($cc, $extra_check = false){ 
  if (empty($cc)) return null;
  $cards = array( 
      "visa" => "(4\d{12}(?:\d{3})?)", 
      "amex" => "(3[47]\d{13})", 
      "jcb" => "(35[2-8][89]\d\d\d{10})", 
      "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)", 
      "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)", 
      "mastercard" => "(5[1-5]\d{14})", 
      "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)", ); 
  $names = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Mastercard", "Switch"); 
  $matches = array(); 
  $pattern = "#^(?:".implode("|", $cards).")$#"; 
  $result = preg_match($pattern, str_replace(" ", "", $cc), $matches); 
  if($extra_check && $result > 0){ 
    $result = (validatecard($cc))?1:0; 
    
  } 
  return ($result>0)?$names[sizeof($matches)-2]:null; 
  
}

function get_shortlink($url){
  $sS_urls = new \App\Services\ShortUrlService();
  return $sS_urls->create($url);
}

function arrayDays($start,$end,$format,$val=0,$includeLast = true){
  $allDay = [];
  $inicio = new DateTime($start);
  $intervalo = new DateInterval('P1D');
  $fin = new DateTime($end);
  $periodo = new DatePeriod($inicio, $intervalo, $fin);
  
  foreach ($periodo as $fecha) {
    $allDay[$fecha->format($format)] = $val;
  }
  if ($includeLast) $allDay[$fin->format($format)] = $val;
  return $allDay;
}

function getYearActive() {
    if (isset($_COOKIE['ActiveYear'])) {
        return $_COOKIE['ActiveYear'];
    } else {
      $activeYear = \App\Years::where('year', date('Y'))->first();
      if ($activeYear){
        setYearActive($activeYear->id);
        return $activeYear->id;
      }
    }
  return -1;
}
function setYearActive($yID) {
  setcookie('ActiveYear',$yID, time() + (86400 * 30), "/"); // 86400 = 1 day
}

function getObjYear()
{
  $activeYear = null;
  if (isset($_COOKIE['ActiveYear'])) {
    $idYear =  $_COOKIE['ActiveYear'];
    if (is_numeric($idYear) && $idYear>0)
      $activeYear = \App\Years::find($idYear);
  } 
  if(!$activeYear) {
    $activeYear = \App\Years::where('year', date('Y'))->first();
    if ($activeYear) setYearActive($activeYear->id);
  }
  return $activeYear;
}
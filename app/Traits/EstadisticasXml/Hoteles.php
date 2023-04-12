<?php

namespace App\Traits\EstadisticasXml; 
use App\Rooms;
use App\Book;
use SimpleXMLElement;

trait Hoteles {
  
    
  function createXML_hotel(){
      /**
      * Cargar, actualizar, y guardar un documento XML
      */
     try {
       include_once dirname(dirname(dirname(__FILE__))).'/Help/hoteles.php';
       $encuesta = new SimpleXMLElement($xmlstr);
       $data = $this->getEncuestaDataHotel();
       foreach ($data as $k=>$v){
         if (is_array($v)){
           foreach ($v as $k1=>$v1){
              if (is_array($v1)){
                /*********************************************************/
                /**BEGIN: HABITACIONES            ************************/
                /*********************************************************/
                if($k == 'HABITACIONES'){
                  $aux = ($encuesta->{$k});
                  foreach ($v1 as $k2=>$v2){
                    $MOVIMIENTO = $aux->addChild('HABITACIONES_MOVIMIENTO');
                    $MOVIMIENTO->addChild('HABITACIONES_N_DIA', $v2['HABITACIONES_N_DIA']);
                    $MOVIMIENTO->addChild('PLAZAS_SUPLETORIAS', $v2['PLAZAS_SUPLETORIAS']);
                    $MOVIMIENTO->addChild('HABITACIONES_DOBLES_USO_DOBLE', $v2['HABITACIONES_DOBLES_USO_DOBLE']);
                    $MOVIMIENTO->addChild('HABITACIONES_DOBLES_USO_INDIVIDUAL', $v2['HABITACIONES_DOBLES_USO_INDIVIDUAL']);
                    $MOVIMIENTO->addChild('HABITACIONES_OTRAS', $v2['HABITACIONES_OTRAS']);
                  }
                  continue;
                }
                /*********************************************************/
                /* END: HABITACIONES            ************************/
                /*********************************************************/
               
                foreach ($v1 as $k2=>$v2){
                  if (is_array($v2)){
                    
                    if ($k == 'ALOJAMIENTO'){
                    $aux = ($encuesta->{$k});
                    $RESIDENCIA = $aux->addChild('RESIDENCIA');
                    if ($v2['ID_PAIS']) $RESIDENCIA->addChild('ID_PAIS', $v2['ID_PAIS']);
                    if ($v2['ID_PROVINCIA_ISLA']) $RESIDENCIA->addChild('ID_PROVINCIA_ISLA', $v2['ID_PROVINCIA_ISLA']);
                      $count = 1;
                      $lastPernotacion = 0;
                      foreach ($v2['MOVIMIENTO'] as $k3=>$v3){
                        $count++;
                        $MOVIMIENTO = $RESIDENCIA->addChild('MOVIMIENTO');
                        $MOVIMIENTO->addChild('N_DIA', $v3['N_DIA']);
                        $MOVIMIENTO->addChild('ENTRADAS', $v3['ENTRADAS']);
                        $MOVIMIENTO->addChild('SALIDAS', $v3['SALIDAS']);
                        $MOVIMIENTO->addChild('PERNOCTACIONES', $v3['PERNOCTACIONES']);
                        $lastPernotacion = $v3['PERNOCTACIONES'];
                      }
                      
//                      for($i=$count; $i<32;$i++){
//                        $MOVIMIENTO = $RESIDENCIA->addChild('MOVIMIENTO');
//                        $MOVIMIENTO->addChild('N_DIA', $i);
//                        $MOVIMIENTO->addChild('ENTRADAS', 0);
//                        $MOVIMIENTO->addChild('SALIDAS', 0);
//                        $MOVIMIENTO->addChild('PERNOCTACIONES', $lastPernotacion);
//                      }
                    } 
                    
                    
                  } else {
//                    if ($k2 == 'MES') $encuesta->{$k}->{$k1}->{$k2} = ($v2<10) ? '0'.$v2 : $v2;
//                    else
                      $encuesta->{$k}->{$k1}->{$k2} = "$v2";
                  }
                }
               
              } else {
                $encuesta->{$k}->{$k1} = $v1;
              }
           }
         }
       }
       
      $encuesta->saveXML(public_path().'/estadisticas-xml/salida-hoteles.xml');
      return $encuesta->asXML();

     } catch (SDO_Exception $e) {
        print($e->getMessage());
     }
    
  }
  
//     view-source:http://riad.virtual/admin/INE 
  function getEncuestaDataHotel(){
    $obj = $this->getEncuestaStructHotel();
    $site_id = 2;
    $roomsID = Rooms::where('state',1)->where('site_id',$site_id)->pluck('id')->all();
    //hay que configurar el informe INE para que no tomo las habitaciones OVERBOOKIN G y INDV
    foreach ($roomsID as $k=>$v){
      if ($v == 33 || $v == 24) unset ($roomsID[$k]);
    }
    $start  = $this->start;
    $finish = $this->finish; //date('Y-m-d',(strtotime('next month',strtotime($start))));
    $month = date('m',strtotime($start));
    $year = date('Y',strtotime($start));

    $firstDate  = new \DateTime($start);
    $secondDate = new \DateTime($finish);
    $intvl = $firstDate->diff($secondDate);
    $days  = $intvl->days+1;
    
    $obj['CABECERA']['FECHA_REFERENCIA']['MES'] = $month;
    $obj['CABECERA']['FECHA_REFERENCIA']['ANYO'] = $year;
    $obj['CABECERA']['DIAS_ABIERTO_MES_REFERENCIA'] = $days;
    $obj['CABECERA']['RAZON_SOCIAL'] = 'RIAD PUERTAS DEL ALBAICIN SL';
    $obj['CABECERA']['NOMBRE_ESTABLECIMIENTO'] = "HOTEL ROSA D'ORO";
    $obj['CABECERA']['CIF_NIF'] = 'B19591205';
    $obj['CABECERA']['NUMERO_REGISTRO'] = '1802200000U';
    $obj['CABECERA']['DIRECCION'] = 'CUESTA SAN GREGORIO No 11';
    $obj['CABECERA']['CODIGO_POSTAL'] = '18004';
    $obj['CABECERA']['LOCALIDAD'] = 'Granada';
    $obj['CABECERA']['MUNICIPIO'] = 'Granada';
    $obj['CABECERA']['PROVINCIA'] = 'GRANADA';
    $obj['CABECERA']['TELEFONO_1'] = '656828854';
    $obj['CABECERA']['TELEFONO_2'] = '656828854';
    $obj['CABECERA']['FAX_1'] = '656828854';
    $obj['CABECERA']['FAX_2'] = '656828854';
    $obj['CABECERA']['TIPO'] = 'Hoteles';
    $obj['CABECERA']['CATEGORIA'] = 'H3';
    $obj['CABECERA']['HABITACIONES'] = 12;//count($roomsID);
    $obj['CABECERA']['PLAZAS_DISPONIBLES_SIN_SUPLETORIAS'] = 28;//Rooms::where('state',1)->where('site_id',$site_id)->sum('minOcu');
    $obj['CABECERA']['URL'] = 'hotelrosadeoro.es';
    
    /*******************************************/
      $match1 = [['start','>=', $start ],['start','<=', $finish ]];
      $match2 = [['finish','>=', $start ],['finish','<=', $finish ]];
      $match3 = [['start','<', $start ],['finish','>', $finish ]];
      
      $books = Book::whereIn('type_book', [1,2])
              ->where('total_price','>',0)
            ->where(function ($query) use ($match1,$match2,$match3) {
              $query->where($match1)
                      ->orWhere($match2)
                      ->orWhere($match3);
            })->whereIn('room_id',$roomsID)->get();
         
      
      
      //Prepara la disponibilidad por día de la reserva
      $startAux = strtotime($start);
      $endAux = strtotime($finish);
      $aLstDays = [];
      $allRooms = Rooms::select('id')->where('site_id',$site_id)->where('state',1)->get();
      $auxDay = ['in'=>0,'out'=>0,'pern'=>0];
      foreach ($allRooms as $r){
        $auxDay[$r->id] = 0;
      }
      
      $roomsType = [];
      $auxRooms = ['indiv' => 0,'doble' => 0,'supl'  => 0];
      while ($startAux<=$endAux){
        $aLstDays[date('Y-m-d',$startAux)] = $auxDay;
        $roomsType[date('Y-m-d',$startAux)] = $auxRooms;
        $startAux = strtotime("+1 day", $startAux);
      }
      
      
      //Obtengo los paices y provincias para generar el listado
      $list_recidencias = [28=>$aLstDays];
      if ($books){
        foreach ($books as $b){
          
          $country = trim($b->customer->country);
          if ($country == '--' || $country == '--Seleccione país --') $country = 'ES';
          
          $country = strtoupper($country);
          $province = strtoupper($b->customer->province);
          
          
          if ($country && !($country == 'ES') ){
            if (!isset($list_recidencias[$country])) $list_recidencias[$country] = $aLstDays;
          }
          
          if (($country == 'ES') && $province && $province != "--SEL"){
            if (!isset($list_recidencias[$province])) 
              $list_recidencias[$province] = $aLstDays;
          }
          
        }
      }
      //Tarifa promedio
      $adr = ['agency'=>0,'direct'=>0,'c_agency'=>0,'c_direct'=>0];
      $auxTemp = ['agency'=>[],'direct'=>[]];
      if ($books){
        foreach ($books as $b){
          $startAux = strtotime($b->start);
          $endAux = strtotime($b->finish);
          $pax = ($b->real_pax>0) ? $b->real_pax : 1;
          
          
          $province = 0;
          $country = 'ES';
          if ($b->customer->country)  $country = $b->customer->country;
          if ($country == 'ES' || $country == 'es'){
            $country = 28;
            if ($b->customer->province) $country = $b->customer->province;
          }
          
          //Tipo de tarifa
          $tipoTarifa = 'direct';
          $nigths = 0;
          if ($b->agency > 0){
            $tipoTarifa = 'agency'; 
          }

          //tipo de habitación
          $typeRoom = ($pax == 1) ? 'indiv' : 'doble';
          $SUPLETORIAS = intval($pax-$b->room->minOcu);
          
          
          while ($startAux<$endAux){
            $day = date('Y-m-d',$startAux);
            if (isset($list_recidencias[$country][$day])){
              $list_recidencias[$country][$day]['pern'] += $pax;
              $list_recidencias[$country][$day][$b->room_id] += $pax;
              $roomsType[$day][$typeRoom] += 1; 
              if ($SUPLETORIAS>0) $roomsType[$day]['supl'] += $SUPLETORIAS; 
              
            }
            if (isset($aLstDays[$day])){
              $nigths++;
            }
            
            $startAux = strtotime("+1 day", $startAux);
          }
          if ($nigths > 0){
            $adr['c_'.$tipoTarifa] += $nigths;
            $adr[$tipoTarifa] += ($b->nigths>0) ? ($b->total_price/$b->nigths)*$nigths : 0;
            $auxTemp[$tipoTarifa][] = '<a href="/admin/reservas/update/'.$b->id.'" tarjet="_blak">ir</a>  '.round($b->total_price/$b->nigths);
          }
      
          if (isset($list_recidencias[$country][$b->start])){
            $list_recidencias[$country][$b->start]['in'] += $pax;
          }
          if (isset($list_recidencias[$country][$b->finish])){
            $list_recidencias[$country][$b->finish]['out'] += $pax;
          }
        }
      }
      
//echo 'Agencia:<br/>'; 
//echo implode('<br/>', $auxTemp['agency']);
//echo '<br/>'.array_sum($auxTemp['agency']) .'<br/>'.array_sum($auxTemp['agency']) / count($auxTemp['agency']);
//echo '<br/><br/><br/>';
//echo 'Directo:<br/>'; 
//echo implode('<br/>', $auxTemp['direct']);
//echo '<br/>'.array_sum($auxTemp['direct']) / count($auxTemp['direct']); 
      
      $iso3Country =  $this->getCountries();
      $iso3Prov =  $this->getProvinces();
      $RESIDENCIAS = [];
            
      foreach ($list_recidencias as $country => $lst_by_prov){
          $MOVIMIENTOS = [];
          foreach ($lst_by_prov as $day=>$v){
            $MOVIMIENTOS[] = [
              'N_DIA'=>date('d', strtotime($day)),
              'ENTRADAS'=>$v['in'],
              'SALIDAS'=>$v['out'],
              'PERNOCTACIONES'=>$v['pern'],
            ];
          }
          
          if (is_numeric($country)){
            $RESIDENCIAS[] = [
              'ID_PAIS' => null,
              'ID_PROVINCIA_ISLA' => (isset($iso3Prov[$country])) ? $iso3Prov[$country] : $country.'ES300' ,
              'MOVIMIENTO' => $MOVIMIENTOS
            ];
          } else {
            $country_up = strtoupper($country);
            $RESIDENCIAS[] = [
              'ID_PAIS' => isset($iso3Country[$country_up]) ? $iso3Country[$country_up] : $country_up.'',
              'ID_PROVINCIA_ISLA' => null,
              'MOVIMIENTO' => $MOVIMIENTOS
            ];
          }
        }
    $obj['ALOJAMIENTO']['RESIDENCIA'] = $RESIDENCIAS;
    
    /*******************************************/
    
    $habMov = [];
    foreach ($roomsType as $day=>$data){
      $habMov[] = [
        'HABITACIONES_N_DIA' => date('d', strtotime($day)),
        'PLAZAS_SUPLETORIAS' => $data['supl'],
        'HABITACIONES_DOBLES_USO_DOBLE' => $data['doble'],
        'HABITACIONES_DOBLES_USO_INDIVIDUAL' => $data['indiv'],
        'HABITACIONES_OTRAS' => 0,
      ];
    }
        
    $obj['HABITACIONES']['HABITACIONES_MOVIMIENTO'] = $habMov;

    /*******************************************/
//    echo implode('<br/>', $auxTemp['agency']);
//    echo '<br/><br/><br/>';
//    echo implode('<br/>', $auxTemp['direct']);
  
    $ADR_AGENCIA_DE_VIAJE_ONLINE = 0;
    $ADR_INTERNET = 0;
    $PCTN_HABITACIONES_OCUPADAS_AGENCIA_ONLINE = 0;
    $PCTN_HABITACIONES_OCUPADAS_INTERNET = 0;
    $PCTN_total = $adr['c_direct']+$adr['c_agency'];
    if ($adr['c_agency']>0){
       $ADR_AGENCIA_DE_VIAJE_ONLINE = round($adr['agency']/$adr['c_agency'],2);
       $PCTN_HABITACIONES_OCUPADAS_AGENCIA_ONLINE = round(($adr['c_agency']/$PCTN_total*100),2);
    }
    
    if ($adr['c_direct']>0){
       $ADR_INTERNET = round($adr['direct']/$adr['c_direct'],2);
       $PCTN_HABITACIONES_OCUPADAS_INTERNET = 100-$PCTN_HABITACIONES_OCUPADAS_AGENCIA_ONLINE;
    }
    
    $obj['PRECIOS']['REVPAR_MENSUAL'] = 0;
    $obj['PRECIOS']['ADR_MENSUAL'] = 0;
    $obj['PRECIOS']['ADR_TOUROPERADOR_TRADICIONAL'] = 0;
    $obj['PRECIOS']['PCTN_HABITACIONES_OCUPADAS_TOUROPERADOR_TRADICIONAL'] = 0;
    $obj['PRECIOS']['ADR_TOUROPERADOR_ONLINE'] = 0;
    $obj['PRECIOS']['PCTN_HABITACIONES_OCUPADAS_TOUROPERADOR_ONLINE'] = 0;
    $obj['PRECIOS']['ADR_EMPRESAS'] = 0;
    $obj['PRECIOS']['PCTN_HABITACIONES_OCUPADAS_EMPRESAS'] = 0;
    $obj['PRECIOS']['ADR_AGENCIA_DE_VIAJE_TRADICIONAL'] = 0;
    $obj['PRECIOS']['PCTN_HABITACIONES_OCUPADAS_AGENCIA_TRADICIONAL'] = 0;
    $obj['PRECIOS']['ADR_AGENCIA_DE_VIAJE_ONLINE'] = $ADR_AGENCIA_DE_VIAJE_ONLINE;
    $obj['PRECIOS']['PCTN_HABITACIONES_OCUPADAS_AGENCIA_ONLINE'] = $PCTN_HABITACIONES_OCUPADAS_AGENCIA_ONLINE;
    $obj['PRECIOS']['ADR_PARTICULARES'] = 0;
    $obj['PRECIOS']['PCTN_HABITACIONES_OCUPADAS_PARTICULARES'] = 0;
    $obj['PRECIOS']['ADR_GRUPOS'] = 0;
    $obj['PRECIOS']['PCTN_HABITACIONES_OCUPADAS_GRUPOS'] = 0;
    $obj['PRECIOS']['ADR_INTERNET'] = $ADR_INTERNET;
    $obj['PRECIOS']['PCTN_HABITACIONES_OCUPADAS_INTERNET'] = $PCTN_HABITACIONES_OCUPADAS_INTERNET;
    $obj['PRECIOS']['ADR_OTROS'] = 0;
    $obj['PRECIOS']['PCTN_HABITACIONES_OCUPADAS_OTROS'] = 0;
    $obj['PERSONAL_OCUPADO']['PERSONAL_NO_REMUNERADO'] = '0';
    $obj['PERSONAL_OCUPADO']['PERSONAL_REMUNERADO_FIJO'] = 0;
    $obj['PERSONAL_OCUPADO']['PERSONAL_REMUNERADO_EVENTUAL'] = '1';
    
    if (isset($this->force['p_n_remun']))  
      $obj['PERSONAL_OCUPADO']['PERSONAL_NO_REMUNERADO'] = $this->force['p_n_remun'];
    if (isset($this->force['p_remun_fijo']))  
      $obj['PERSONAL_OCUPADO']['PERSONAL_REMUNERADO_FIJO'] = $this->force['p_remun_fijo'];
    if (isset($this->force['p_remun_eventual']))  
      $obj['PERSONAL_OCUPADO']['PERSONAL_REMUNERADO_EVENTUAL'] = $this->force['p_remun_eventual'];
            
            
    return $obj;
  }
  
  
  function getEncuestaStructHotel(){
    return [
            'CABECERA'=>[
              'FECHA_REFERENCIA'=>[
                'MES'=>'0',
                'ANYO'=>'0',
              ],    
              'DIAS_ABIERTO_MES_REFERENCIA'=>'0',
              'RAZON_SOCIAL'=>'0',
              'NOMBRE_ESTABLECIMIENTO'=>'0',
              'CIF_NIF'=>'0',
              'NUMERO_REGISTRO'=>'0',
              'DIRECCION'=>'0',
              'CODIGO_POSTAL'=>'0',
              'LOCALIDAD'=>'0',
              'MUNICIPIO'=>'0',
              'PROVINCIA'=>'0',
              'TELEFONO_1'=>'0',
              'TELEFONO_2'=>'0',
              'FAX_1'=>'0',
              'FAX_2'=>'0',
              'TIPO'=>'0',
              'CATEGORIA'=>'0',
              'HABITACIONES'=>'0',
              'PLAZAS_DISPONIBLES_SIN_SUPLETORIAS'=>'0',
              'URL'=>'0',
            ],  
            'ALOJAMIENTO'=>[],  
            'HABITACIONES'=>[
              'HABITACIONES_MOVIMIENTO'=>[
                'HABITACIONES_N_DIA'=>'0',
                'PLAZAS_SUPLETORIAS'=>'0',
                'HABITACIONES_DOBLES_USO_DOBLE'=>'0',
                'HABITACIONES_DOBLES_USO_INDIVIDUAL'=>'0',
                'HABITACIONES_OTRAS'=>'0',
              ],
            ],
            'PRECIOS'=>[
              'REVPAR_MENSUAL'=>'0',
              'ADR_MENSUAL'=>'0',
              'ADR_TOUROPERADOR_TRADICIONAL'=>'0',
              'PCTN_HABITACIONES_OCUPADAS_TOUROPERADOR_TRADICIONAL'=>'0',
              'ADR_TOUROPERADOR_ONLINE'=>'0',
              'PCTN_HABITACIONES_OCUPADAS_TOUROPERADOR_ONLINE'=>'0',
              'ADR_EMPRESAS'=>'0',
              'PCTN_HABITACIONES_OCUPADAS_EMPRESAS'=>'0',
              'ADR_AGENCIA_DE_VIAJE_TRADICIONAL'=>'0',
              'PCTN_HABITACIONES_OCUPADAS_AGENCIA_TRADICIONAL'=>'0',
              'ADR_AGENCIA_DE_VIAJE_ONLINE'=>'0',
              'PCTN_HABITACIONES_OCUPADAS_AGENCIA_ONLINE'=>'0',
              'ADR_PARTICULARES'=>'0',
              'PCTN_HABITACIONES_OCUPADAS_PARTICULARES'=>'0',
              'ADR_GRUPOS'=>'0',
              'PCTN_HABITACIONES_OCUPADAS_GRUPOS'=>'0',
              'ADR_INTERNET'=>'0',
              'PCTN_HABITACIONES_OCUPADAS_INTERNET'=>'0',
              'ADR_OTROS'=>'0',
              'PCTN_HABITACIONES_OCUPADAS_OTROS'=>'0',
            ],
            'PERSONAL_OCUPADO'=>[
              'PERSONAL_NO_REMUNERADO'=>'0',
              'PERSONAL_REMUNERADO_FIJO'=>'0',
              'PERSONAL_REMUNERADO_EVENTUAL'=>'0',
            ]
        ];
  }
}

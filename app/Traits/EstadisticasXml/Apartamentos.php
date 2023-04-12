<?php

namespace App\Traits\EstadisticasXml; 
use App\Rooms;
use App\Book;
use SimpleXMLElement;

trait Apartamentos {
  /**********************************************************************/  
/************               APARTAMENTOS        **********************/  
/**********************************************************************/   
  function createXML_Apartamento(){
      /**
      * Cargar, actualizar, y guardar un documento XML
      */
     try {
       include_once dirname(dirname(dirname(__FILE__))).'/Help/apartamentos.php';
       $encuesta = new SimpleXMLElement($xmlstr);
       $data = $this->getEncuestaDataApartamento();
       foreach ($data as $k=>$v){
         if (is_array($v)){
           foreach ($v as $k1=>$v1){
              if (is_array($v1)){
                /*********************************************************/
                /**BEGIN: HABITACIONES            ************************/
                /*********************************************************/
                if($k == 'OCUPACION'){
                  $aux = ($encuesta->{$k});
                  foreach ($v1 as $k2=>$v2){
                    $MOVIMIENTO = $aux->addChild('MOVIMIENTO');
                    $MOVIMIENTO->addChild('N_DIA_AP', $v2['N_DIA_AP']);
                    $MOVIMIENTO->addChild('APARTAMENTOS_OCUPADOS_ESTUDIO', $v2['APARTAMENTOS_OCUPADOS_ESTUDIO']);
                    $MOVIMIENTO->addChild('APARTAMENTOS_OCUPADOS_2-4pax', $v2['APARTAMENTOS_OCUPADOS_2-4pax']);
                    $MOVIMIENTO->addChild('APARTAMENTOS_OCUPADOS_4-6pax', $v2['APARTAMENTOS_OCUPADOS_4-6pax']);
                    $MOVIMIENTO->addChild('APARTAMENTOS_OCUPADOS_OTROS', 0);
                    $MOVIMIENTO->addChild('PLAZAS_SUPLETORIAS', $v2['PLAZAS_SUPLETORIAS']);
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
                    } 
                    
                    
                  } else {
                      $encuesta->{$k}->{$k1}->{$k2} = "$v2";
                  }
                }
               
              } else {
                $encuesta->{$k}->{$k1} = $v1;
              }
           }
         }
       }
      
      $encuesta->saveXML(public_path().'/estadisticas-xml/salida-apartamentos.xml');
      return $encuesta->asXML();

     } catch (SDO_Exception $e) {
        print($e->getMessage());
     }
    
  }
  
  //     view-source:http://riad.virtual/admin/INE 
  function getEncuestaDataApartamento(){
    $obj = $this->getEncuestaStructApartamento();
    $site_id = 1;
    $roomsID = Rooms::where('state',1)->where('site_id',$site_id)->pluck('id')->all();
        
    //hay que configurar el informe INE para que no tomo las habitaciones OVERBOOKIN
    foreach ($roomsID as $k=>$v){
      if ($v == 7) unset ($roomsID[$k]);
    }
            
    $start  = $this->start;
    $finish = $this->finish; //date('Y-m-d',(strtotime('next month',strtotime($start))));
    $month = date('m',strtotime($start));
    $year = date('Y',strtotime($start));
  
    $obj = $obj['APARTAMENTOS'];
    
    $firstDate  = new \DateTime($start);
    $secondDate = new \DateTime($finish);
    $intvl = $firstDate->diff($secondDate);
    $days  = $intvl->days+1;
    
    $obj['CABECERA']['FECHA_REFERENCIA']['MES'] = $month;
    $obj['CABECERA']['FECHA_REFERENCIA']['ANYO'] = $year;
    $obj['CABECERA']['DIAS_ABIERTO_MES_REFERENCIA'] = $days;
    $obj['CABECERA']['RAZON_SOCIAL'] = 'RIAD PUERTAS DEL ALBAICIN SL';
    $obj['CABECERA']['NOMBRE_ESTABLECIMIENTO'] = "RIAD PUERTAS DEL ALBAICIN";
    $obj['CABECERA']['CIF_NIF'] = 'B19591205';
    $obj['CABECERA']['DIRECCION'] = 'CUESTA SAN GREGORIO No 11';
    $obj['CABECERA']['CODIGO_POSTAL'] = '18004';
    $obj['CABECERA']['LOCALIDAD'] = 'Granada';
    $obj['CABECERA']['MUNICIPIO'] = 'Granada';
    $obj['CABECERA']['PROVINCIA'] = 'GRANADA';
    $obj['CABECERA']['TELEFONO_1'] = '656828854';
    $obj['CABECERA']['TELEFONO_2'] = '000000000';
    $obj['CABECERA']['FAX_1'] = '000000000';
    $obj['CABECERA']['FAX_2'] = '000000000';
    $obj['CABECERA']['URL'] = 'riadpuertasdelalbaicin.com';
    $obj['CABECERA_APARTAMENTOS']['CATEGORIA'] = '1';
    
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
          $province = $b->customer->province;
          if ($country == '--' || $country == '--Seleccione país --') $country = 'ES';
          $country = strtoupper($country);
          if ($country && !($country == 'ES')){
            if (!isset($list_recidencias[$country])) $list_recidencias[$country] = $aLstDays;
          }
          
          if ( ($country == 'ES') && $province){
            if (!isset($list_recidencias[$province])) 
              $list_recidencias[$province] = $aLstDays;
          }
          
        }
      }
      
      //Tarifa promedio
      $adr = [
          'indiv' => ['agency'=>0,'direct'=>0,'c_agency'=>0,'c_direct'=>0],
          'doble' => ['agency'=>0,'direct'=>0,'c_agency'=>0,'c_direct'=>0],
        ];
    
      if ($books){
        foreach ($books as $b){
          $startAux = strtotime($b->start);
          $endAux = strtotime($b->finish);
          $pax = ($b->real_pax>0) ? $b->real_pax : 1;
          
          
          $province = 0;
          $country = 'ES';
          if ($b->customer->country)  $country = $b->customer->country;
          
          if ($country == '--' || $country == '--Seleccione país --') $country = 'ES';
          
          if ($country == 'ES'  || $country == 'es'){
            $country = 28;
            if ($b->customer->province) $country = $b->customer->province;
          }
          
          //tipo de habitación
          $typeRoom = ($b->room_id == 2) ? 'indiv' : 'doble';
          $SUPLETORIAS = intval($pax-$b->room->minOcu);
          
          //Tipo de tarifa
          $tipoTarifa = 'direct';
          $nigths = 0;
          if ($b->agency > 0){
            $tipoTarifa = 'agency'; 
          }
          
          
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
            $adr[$typeRoom]['c_'.$tipoTarifa] += $nigths;
            if  ($b->nigths>0){
              $adr[$typeRoom][$tipoTarifa] +=  ($b->nigths == $nigths) ? $b->total_price : ($b->total_price/$b->nigths)*$nigths;
              //$auxTemp[$tipoTarifa][] = $b->total_price.'€  / ' .$b->nigths.' * '.$nigths.' / <a href="/admin/reservas/update/'.$b->id.'" tarjet="_blak">ir</a>';
            }
          }
      
          if (isset($list_recidencias[$country][$b->start])){
            $list_recidencias[$country][$b->start]['in'] += $pax;
          }
          if (isset($list_recidencias[$country][$b->finish])){
            $list_recidencias[$country][$b->finish]['out'] += $pax;
          }
        }
      }
      
      
      
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
          
          if (is_numeric($country) || trim($country) == '--'){
            $RESIDENCIAS[] = [
              'ID_PAIS' => null,
              'ID_PROVINCIA_ISLA' => (isset($iso3Prov[$country])) ? $iso3Prov[$country] : 'ES300' ,
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
        'N_DIA_AP' => date('d', strtotime($day)),
        'APARTAMENTOS_OCUPADOS_ESTUDIO' => 0,
        'APARTAMENTOS_OCUPADOS_2-4pax' => $data['indiv'],
        'APARTAMENTOS_OCUPADOS_4-6pax' => $data['doble'],
        'APARTAMENTOS_OCUPADOS_OTROS' => 0,
        'PLAZAS_SUPLETORIAS' => $data['supl'],
      ];
    }
        
    $obj['OCUPACION']['MOVIMIENTO'] = $habMov;

    /*******************************************/
    
    /**------------------------------------------------------**/
    $TARIFA_NORMAL = 0;
    $PCTN_TARIFA_NORMAL = 0;
    $TARIFA_TOUROPERADOR = 0;
    $PCTN_TARIFA_TOUROPERADOR = 0;
    $PCTN_total = $adr['indiv']['c_direct']+$adr['indiv']['c_agency'];
    if ($adr['indiv']['c_agency']>0){
       $TARIFA_TOUROPERADOR = $adr['indiv']['agency']/$adr['indiv']['c_agency'];
       $PCTN_TARIFA_TOUROPERADOR =  ($adr['indiv']['c_agency']/$PCTN_total*100);
    }
    
    if ($adr['indiv']['c_direct']>0){
       $TARIFA_NORMAL = $adr['indiv']['direct']/$adr['indiv']['c_direct'];
       $PCTN_TARIFA_NORMAL = 100-$PCTN_TARIFA_TOUROPERADOR;
//       $PCTN_TARIFA_NORMAL = $adr['indiv']['c_direct']/$PCTN_total*100);
    }
    $obj['PRECIOS']['APARTAMENTOS_2-4pax']['TARIFA_NORMAL'] = round($TARIFA_NORMAL,2);
    $obj['PRECIOS']['APARTAMENTOS_2-4pax']['PCTN_TARIFA_NORMAL'] = round($PCTN_TARIFA_NORMAL,2);
    $obj['PRECIOS']['APARTAMENTOS_2-4pax']['TARIFA_TOUROPERADOR'] = round($TARIFA_TOUROPERADOR,2);
    $obj['PRECIOS']['APARTAMENTOS_2-4pax']['PCTN_TARIFA_TOUROPERADOR'] = round($PCTN_TARIFA_TOUROPERADOR,2);
    
    /**------------------------------------------------------**/
    $TARIFA_NORMAL = 0;
    $PCTN_TARIFA_NORMAL = 0;
    $TARIFA_TOUROPERADOR = 0;
    $PCTN_TARIFA_TOUROPERADOR = 0;
    $PCTN_total = $adr['doble']['c_direct']+$adr['doble']['c_agency'];
    if ($adr['doble']['c_agency']>0){
       $TARIFA_TOUROPERADOR = $adr['doble']['agency']/$adr['doble']['c_agency'];
       $PCTN_TARIFA_TOUROPERADOR =  ($adr['doble']['c_agency']/$PCTN_total*100);
    }
    
    if ($adr['doble']['c_direct']>0){
       $TARIFA_NORMAL = $adr['doble']['direct']/$adr['doble']['c_direct'];
//       $PCTN_TARIFA_NORMAL = $adr['doble']['c_direct']/$PCTN_total*100);
       $PCTN_TARIFA_NORMAL = 100-$PCTN_TARIFA_TOUROPERADOR;
    }
    $obj['PRECIOS']['APARTAMENTOS_4-6pax']['TARIFA_NORMAL'] = round($TARIFA_NORMAL,2);
    $obj['PRECIOS']['APARTAMENTOS_4-6pax']['PCTN_TARIFA_NORMAL'] = round($PCTN_TARIFA_NORMAL,2);
    $obj['PRECIOS']['APARTAMENTOS_4-6pax']['TARIFA_TOUROPERADOR'] = round($TARIFA_TOUROPERADOR,2);
    $obj['PRECIOS']['APARTAMENTOS_4-6pax']['PCTN_TARIFA_TOUROPERADOR'] = round($PCTN_TARIFA_TOUROPERADOR,2);
    
    
    $obj['PERSONAL_OCUPADO']['PERSONAL_NO_REMUNERADO'] = 0;
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
  
  function getEncuestaStructApartamento(){
    $tarifas = [
        'TARIFA_NORMAL' => 0,
        'PCTN_TARIFA_NORMAL' => 0,
        'TARIFA_FIN_DE_SEMANA' => 0,
        'PCTN_TARIFA_FIN_DE_SEMANA' => 0,
        'TARIFA_TOUROPERADOR' => 0,
        'PCTN_TARIFA_TOUROPERADOR' => 0,
        'TARIFA_OTRAS' => 0,
        'PCTN_TARIFA_OTRAS' => 0,
    ];
    
    return [
    'APARTAMENTOS' =>[
      'CABECERA'=>[
        'FECHA_REFERENCIA'=>[
            'MES'=>'0',
            'ANYO'=>'0',
          ],    
        'DIAS_ABIERTO_MES_REFERENCIA'=>'0',
        'RAZON_SOCIAL'=>'0',
        'NOMBRE_ESTABLECIMIENTO'=>'0',
        'CIF_NIF'=>'0',
        'DIRECCION'=>'0',
        'CODIGO_POSTAL'=>'0',
        'LOCALIDAD'=>'0',
        'MUNICIPIO'=>'0',
        'PROVINCIA'=>'0',
        'TELEFONO_1'=>'0',
        'TELEFONO_2'=>'000000000',
        'FAX_1'=>'000000000',
        'FAX_2'=>'000000000',
        'URL'=>'0',
      ],
      'CABECERA_APARTAMENTOS' =>['CATEGORIA'=>1],
      'INFORMANTE'=>[
          'NOMBRE' => 'Jorge Largo',
          'CARGO' => 'Gerente',
          'TELEFONOINF' => '656828854',
          'FAXINF' => '000000000',
          'EMAIL' => 'jlargo@mksport.es',
      ],
      'EMPRESA_GESTORA' =>[
        'RAZON_SOCIAL'=>'RIAD PUERTAS DEL ALBAICIN SL',
        'DIRECCION'=>'Cuesta de San Gregorio 11',
        'CODIGO_POSTAL'=>18004,
        'MUNICIPIO'=>'Granada',
        'PROVINCIA'=>'Granada',
        'TELEFONO'=>'656828854',
        'FAX'=>'000000000',
      ],
      'ALOJAMIENTO'=>null,
      'CAPACIDAD' =>[
        'N_APARTAMENTOS_ESTUDIO'=>0,
        'N_APARTAMENTOS_2-4pax'=>1,
        'N_APARTAMENTOS_4-6pax'=>5,
        'N_APARTAMENTOS_OTROS'=>0,
        'PLAZAS_TOTALES_APARTAMENTOS_ESTUDIO'=>0,
        'PLAZAS_TOTALES_APARTAMENTOS_2-4pax'=>2,
        'PLAZAS_TOTALES_APARTAMENTOS_4-6pax'=>26,
        'PLAZAS_TOTALES_APARTAMENTOS_OTROS'=>0,
      ],
      'OCUPACION'=>null,
      'PRECIOS' =>[
        'ESTUDIOS'=>$tarifas,
        'APARTAMENTOS_2-4pax'=>$tarifas,
        'APARTAMENTOS_4-6pax'=>$tarifas,
        'OTROS'=>$tarifas,
      ],
      'PERSONAL_OCUPADO'=>[
        'PERSONAL_NO_REMUNERADO'=>'0',
        'PERSONAL_REMUNERADO_FIJO'=>'0',
        'PERSONAL_REMUNERADO_EVENTUAL'=>'0',
        ]
      ]
    ];
  }
  
}

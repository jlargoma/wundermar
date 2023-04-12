<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use SoapClient;
use DOMDocument;
use App\Traits\EstadisticasXml\Apartamentos;
use App\Traits\EstadisticasXml\Hoteles;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
//use SDO_DAS_XML;
    
//use GoetasWebservices\XML\XSDReader\SchemaReader;
/**
 * Description of INEController
 *
 * @author cremonapg
 */
class INEController  extends AppController{
  
  use Apartamentos,Hoteles;
  var $m = '00';
  var $y = '00';
  var $force = '00';
  var $start = null;
  var $finish= null;
  
   
  
  
  // admin/INE
  function index(){
    $this->m = date('m');
    $this->y = date('Y');
    $this->force = ['p_n_remun'=>null,'p_remun_fijo'=>null,'p_remun_eventual'=>null,];
    
    return view('backend.ine',
            ['encuesta'=>null,
                'dwnl_url'=>null,
                'force'=>$this->force,
                'm'=>$this->m,
                'y'=>$this->y,
                'type'=>'riad',
                'start'=>date('Y-m-d'),
                'finish'=>date('Y-m-d'),
                'range'=>date('d M, y').'-'.date('d M, y'),
                ]);
  }
  
  function sendData(Request $request){

    $type = $request->input('type');
    
    $order = $request->input('NumeroOrden');
    $code = $request->input('CodigoControl');
    $force = $request->input('force');
    $this->force = json_decode(base64_decode($force),true);
    if (!$order && !$code) return back();
    
    $this->start = $request->input('start');
    $this->finish = $request->input('finish');
    
   
    
    $encuesta = null;
    switch ($type){
      case 'rosa':
        $encuesta = $this->createXML_hotel();
        $file= public_path(). "/estadisticas-xml/salida-hoteles.xml";
        break;
      case 'riad':
        $encuesta = $this->createXML_Apartamento();
        $file= public_path(). "/estadisticas-xml/salida-apartamentos.xml";
        break;
    }
    
    
    if ($encuesta){
      $clienteSOAP = new SoapClient('https://arce.ine.es/ARCE/ficheros/ServicioXmlTurismo.wsdl',['stream_context' => stream_context_create(
         array(
             'ssl' => array(
                 'verify_peer'       => false,
                 'verify_peer_name'  => false,
             )
         )
      )]);
      $resultado = null;
      $param = [
           'SolicitudEncuesta'=>[
              'CabeceraSolicitud'=>[
                   'NumeroOrden'=>$order,//'2020HOT002L',
                   'CodigoControl'=>$code,//'0002L',
                   'CorreoElectronico'=>'pingodevweb@gmail.com',
               ],
              'Encuesta'=> $encuesta
           ]
       ];
     
      $resultado = $clienteSOAP->__soapCall('procesarSolicitudEncuesta',$param); 
      dd($resultado);
    } 
    
    return redirect('404');
    
//     var_dump($clienteSOAP->__getFunctions());

  }
  
  function download($type,$range,$force,$unic){
    $encuesta = null;
    $this->force = json_decode(base64_decode($force),true);
    $range = json_decode(base64_decode($range),true);
    $this->start  = $range[0];
    $this->finish = $range[1];
    
    
    $fileName = date('m',strtotime($this->start)).'-'.date('Y',strtotime($this->start));
    switch ($type){
      case 'rosa':
        $encuesta = $this->createXML_hotel();
        $file= public_path(). "/estadisticas-xml/salida-hoteles.xml";
        break;
      case 'riad':
        $encuesta = $this->createXML_Apartamento();
        $file= public_path(). "/estadisticas-xml/salida-apartamentos.xml";
        break;
      default:
        $encuesta = $this->createXML_Apartamento();
        $file= public_path(). "/estadisticas-xml/salida-apartamentos.xml";
        break;
    }
    
    if ($encuesta){
      $headers = array('Content-Type: application/xml');
      return Response::download($file, $type.'-'.$fileName.'.xml', $headers);
    } 
    
    return redirect('404');
  }
  
  function showData(Request $request){
     
    $type = $request->input('type');
    $this->start = $request->input('start');
    $this->finish = $request->input('finish');
    
                
    $this->m = date('m',strtotime($this->start));
    $this->y = date('Y',strtotime($this->start));
    
    $encuesta = null;
    $this->force = ['p_n_remun'=>null,'p_remun_fijo'=>null,'p_remun_eventual'=>null,];
    if ($request->input('p_n_remun') != null) $this->force['p_n_remun'] = $request->input('p_n_remun');
    if ($request->input('p_remun_fijo') != null) $this->force['p_remun_fijo'] = $request->input('p_remun_fijo');
    if ($request->input('p_remun_eventual')  != null) $this->force['p_remun_eventual'] = $request->input('p_remun_eventual');
    
    $dwnl_url = '/admin/download-INE/'.$type.'/';
    $dwnl_url .= base64_encode(json_encode([$this->start,$this->finish])).'/'.base64_encode(json_encode($this->force));
    $dwnl_url .= '/'.time().'-'. rand();
    switch ($type){
      case 'hotel':
        $encuesta = $this->getEncuestaDataHotel();
        break;
      case 'apto':
        $encuesta = $this->getEncuestaDataApartamento();
        break;
      default:
        $encuesta = $this->getEncuestaDataApartamento();
          break;
    }

    //RENDER MOVIM
    $prov = $this->getProvincesName();
    $alojamientos = [];
    $dias = [];
    $first = true;
    $index = null;
    foreach ($encuesta['ALOJAMIENTO']['RESIDENCIA'] as $k=>$v){
      $lugar = $index = $v['ID_PAIS'];
      if ($v['ID_PROVINCIA_ISLA'] && isset($prov[$v['ID_PROVINCIA_ISLA']])){
        $lugar = $prov[$v['ID_PROVINCIA_ISLA']].' (ESP)';
        $index = '0 '.$lugar;
      }
      $mov = [];
      $t_entradas = 0;
      foreach ($v['MOVIMIENTO'] as $k1=>$v1){
        $t_entradas += $v1['ENTRADAS'];
        if ($first)  $dias[] = $v1['N_DIA'];
        $mov[] = ($v1['PERNOCTACIONES']>0) ? $v1['PERNOCTACIONES'] : '-';
      }
      
      $first = false;
      
      $alojamientos[$index] = [
          'lugar'=>$lugar,
          't_entradas'=>$t_entradas,
          'mov'=>$mov,
      ];
      
    }
    ksort($alojamientos);
     
    $movApart = null;
    $movApartTit = [];
    if(isset($encuesta['HABITACIONES'])){
      $movApart = [[],[],[]];
      $movApartTit = ['PLAZAS SUPLETORIAS','HABITACIONES DOBLES USO DOBLE','HABITACIONES DOBLES USO INDIVIDUAL'];
      $obj = $encuesta['HABITACIONES']['HABITACIONES_MOVIMIENTO'];
      foreach ($obj as $k=>$v){
        $movApart[0][] = ($v['PLAZAS_SUPLETORIAS']>0) ? $v['PLAZAS_SUPLETORIAS'] : '-';
        $movApart[1][] = ($v['HABITACIONES_DOBLES_USO_DOBLE']>0) ? $v['HABITACIONES_DOBLES_USO_DOBLE'] : '-';
        $movApart[2][] = ($v['HABITACIONES_DOBLES_USO_INDIVIDUAL']>0) ? $v['HABITACIONES_DOBLES_USO_INDIVIDUAL'] : '-';
      }
    } elseif(isset($encuesta['OCUPACION'])){
      $movApart = [[],[],[],[]];
      $movApartTit = ['2-4pax','4-6pax','OTROS','PLAZAS SUPLETORIAS'];
      $obj = $encuesta['OCUPACION']['MOVIMIENTO'];
      foreach ($obj as $k=>$v){
        $movApart[0][] = ($v['APARTAMENTOS_OCUPADOS_2-4pax']>0) ? $v['APARTAMENTOS_OCUPADOS_2-4pax'] : '-';
        $movApart[1][] = ($v['APARTAMENTOS_OCUPADOS_4-6pax']>0) ? $v['APARTAMENTOS_OCUPADOS_4-6pax'] : '-';
        $movApart[2][] = ($v['APARTAMENTOS_OCUPADOS_OTROS']>0) ? $v['APARTAMENTOS_OCUPADOS_OTROS'] : '-';
        $movApart[3][] = ($v['PLAZAS_SUPLETORIAS']>0) ? $v['PLAZAS_SUPLETORIAS'] : '-';
      }
    }
      
//    dd($movApart);
    
    
    
    
    
    return view('backend.ine',
            ['encuesta'=>$encuesta,
                'm'=>$this->m,
                'force'=>$this->force,
                'y'=>$this->y,
                'type'=>$type,
                'prov'=>$prov,
                'dwnl_url'=>$dwnl_url,
                'start'=>$this->start,
                'finish'=>$this->finish,
                'alojamientos'=>$alojamientos,
                'movApart'=>$movApart,
                'movApartTit'=>$movApartTit,
                'dias'=>$dias,
                'range'=>date('d M, y', strtotime($this->start)).' - '.date('d M, y', strtotime($this->finish)),
                ]);
  }
  
  
  
/***************************************************************/  
/************          AUXILIARES        **********************/  
/**************************************************************/  
  
  function readXML(){
    $xmldata = \simplexml_load_file(public_path().'/provincias.xml') or die("Failed to load");
  
    $insert = [];
    foreach ($xmldata as $k=>$v){
      $aux = strtoupper($v->NOM_PROVINCIA_ISLA);
//      dd($v);
//      echo "'$v->NOM_PROVINCIA_ISLA' => '$v->ID_PROVINCIA_ISLA',<br/>";
      echo "UPDATE provinces SET nuts_3 = '$v->ID_PROVINCIA_ISLA' WHERE province = '$aux';<br/>";
//      
//     $insert[] =  [
//         'code'=>"$v->ID_PAIS",
//         'country'=>$v->Nom_PAIS,
//     ];
//      
//     
//     nuts_3
     
    } 
    
//     \App\Countries::insert($insert);
    dd($xmldata);
  }
  function readXSL(){
    global $doc, $xpath;
      /**
     * Cargar, actualizar, y guardar un documento XML
     */
      $doc = new DOMDocument();
      $doc->load(public_path().'/estadisticas-xml/apartamentos.xsd');
      $xpath = new DOMXPath($doc);
      $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');
      

      $elementDefs = $xpath->evaluate("/xs:schema/xs:element");
      foreach($elementDefs as $elementDef) {
        $this->echoElementsXML("", $elementDef);
      }             
  }
  
  
  function echoElements($indent, $elementDef) {
        global $doc, $xpath;
        $name = $elementDef->getAttribute('name');
        $aux = $indent;
        $indent .= '  ';
        
        $elementDefs = $xpath->evaluate("xs:complexType/xs:sequence/xs:element", $elementDef);
        if (count($elementDefs)>0){
          echo $aux."['" . $name . "' =>[\n";
        } else {
          echo $aux."'" . $name . "'=>0,\n";
        }
//        echo count($elementDefs);
        foreach($elementDefs as $elementDef) {
          
          $this->echoElements($indent, $elementDef);
        }
         if (count($elementDefs)>0){
            echo $aux."],\n";
//           echo $aux."</" . $name . ">\n";
//          echo '';
        }
        
      }
    function echoElementsXML($indent, $elementDef) {
        global $doc, $xpath;
        $name = $elementDef->getAttribute('name');
        $aux = $indent;
        $indent .= '  ';
        
        $elementDefs = $xpath->evaluate("xs:complexType/xs:sequence/xs:element", $elementDef);
        if (count($elementDefs)>0){
          echo $aux."<" . $name . ">\n";
        } else {
          echo $aux."<" . $name . ">0"."</" . $name . ">\n";
        }
//        echo count($elementDefs);
        foreach($elementDefs as $elementDef) {
          
          $this->echoElementsXML($indent, $elementDef);
        }
         if (count($elementDefs)>0){
           echo $aux."</" . $name . ">\n";
//          echo '';
        }
        
      }
      
  function getCountries() {    
      $iso3Country = [];
      $countries = \App\Countries::all();
      foreach ($countries as $c){
        $iso3Country[$c->code] = $c->ISO_3;
      }
      return $iso3Country;
  }
  function getProvinces() {    
      $iso3Prov = [];
      $obj = \App\Provinces::all();
      foreach ($obj as $o){
        $iso3Prov[$o->code] = $o->nuts_3;
      }
      return $iso3Prov;
  }
  
  function getProvincesName() {    
      $iso3Prov = [];
      $obj = \App\Provinces::all();
      foreach ($obj as $o){
        $iso3Prov[$o->nuts_3] = $o->province;
      }
      return $iso3Prov;
  }
  
  
function testste() {
  
  $data =  $this->getEncuestaStructApartamento();
  foreach ($data as $k=>$v){
    echo "<" . $k . ">\n";
    if (is_array($v)){
      foreach ($v as $k1=>$v1){
        echo "  <" . $k1 . ">\n";
        if (is_array($v1)){
          foreach ($v1 as $k2=>$v2){
            echo "   <" . $k2 . "> </" . $k2 . ">\n";
          }
        } 
        echo "  </" . $k1 . ">\n";
      }
    }
    echo "</" . $k . ">\n";
  }
}
  
}
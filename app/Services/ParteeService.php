<?php

namespace App\Services;
use App\Settings;

/**
 * Partee Integration
 * 
 * Status: 
 *    VACIO indicando que ningún huésped ha cubierto el formulario de check-in online todavía; 
 *    HUESPEDES indicando que al menos un huésped ha cubierto el enlace de check-in online;
 *    FINALIZADO indicando que el parte de viajeros ha sido finalizado, es decir,
 *  se han creado los partes de viajeros y se ha realizado el envío al cuerpo policial correspondiente.
 */
class ParteeService
{
    public $response;
    public $responseCode;
    public      $PARTEE_ID;
    protected   $api;
    protected   $usr;
    protected   $psw;
    protected   $JWT;
    protected   $PARTEE_URL;
    

    public function __construct($PARTEE_ID=-1)
    {
      $this->PARTEE_URL = (config('app.partee.environment') == "dev")? config('app.partee.sandbox') : config('app.partee.endpoint');
      $this->JWT = null;
      $this->PARTEE_ID = $PARTEE_ID;//Settings::getKeyValue('partee_apartament');
    }
    
    /**
     * Set the ParteeID
     * @param type $PARTEE_ID
     */
    public function setID($PARTEE_ID)
    {
      $this->PARTEE_ID = $PARTEE_ID;//Settings::getKeyValue('partee_apartament');
    }
    
    public function conect(){
      
//      if ($this->PARTEE_ID<1){
//        $this->response = 'Server error - empty Partee Departament ID';
//        return FALSE;
//      }
      
      $data = array("username" =>  config('app.partee.usr'), "password" => config('app.partee.psw'),"rememberMe"=> false);                                                                    
      $data_string = json_encode($data);                                                                                   
                                                                                                                     
      $ch = curl_init($this->PARTEE_URL."authenticate");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
          'Content-Type: application/json',                                                                                
          'Content-Length: ' . strlen($data_string))                                                                       
      );                                                                                                                   
      $result = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
      curl_exec($ch);
      curl_close($ch);
      
      
      if(!$result) 
      { 
        $this->response = 'Server error - empty response';
        $this->responseCode = 400;
        return FALSE; 
      } 
      $this->responseCode = $httpCode;
      switch ($httpCode){
        case 200:
          $response = \json_decode($result);
          $this->JWT = $response->id_token;
          return TRUE; 
          break;
        case 400:
          $this->response = 'Wrong data';
          break;
        default :
          $this->response = 'Server error';
          break;
      }

      return FALSE; 
    }
    
    
    public function call( $endpoint,$method = "POST", $data = [])
    {
      if(!$this->JWT) 
      { 
        $this->response = 'Token required';
        return FALSE; 
      } 
      
      if ($method == "POST" || $method == "PUT"){
        
        $data_string = json_encode($data);   
        $ch = curl_init($this->PARTEE_URL.$endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     
        curl_setopt($ch, CURLOPT_TIMEOUT , 10); //  CURLOPT_TIMEOUT => 10,
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',        
            'Authorization:Bearer '.$this->JWT,
            'Content-Length: ' . strlen($data_string))                                                                       
        );          
        
      } else {
        
        $ch = curl_init($this->PARTEE_URL.$endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);                                                                     
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7); //Timeout after 7 seconds
        curl_setopt($ch, CURLOPT_TIMEOUT , 10); //  CURLOPT_TIMEOUT => 10,
        curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',        
            'Authorization:Bearer '.$this->JWT,
        ));     
      }
      
      $result = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
      curl_close($ch);
      $this->response = null;
      $this->responseCode = $httpCode;
      switch ($httpCode){
        case 200:
          if(!$result) 
          { 
            $this->response = null;
            return FALSE; 
          } 
          $this->response = \json_decode($result);
          return TRUE; 
          break;
        case 400:
          $this->response = 'Wrong data - Bad Request';
          break;
        case 401:
          $this->response = $result;
          break;
        case 404:
          $this->response = 'NotFound';
          break;
        default :
          $this->response = 'Server error';
          break;
      }

      return FALSE; 
      
    }

    /**
     * Send book to Partee and save the link Partee
     * 
     * @param type $email
     * @param type $dateTime
     * @param type $request_docs
     * @return type
     * @throws \Exception
     */
    public function getCheckinLink($email,$dateTime,$request_docs = false)
    {
        $code = null;
        try
        {
          
          $data = [
            "email"=> null,
//            "email"=> empty($email) ? null : $email,
            'fecha_entrada'=> date("Y-m-d\TH:i:s", $dateTime), //"2018-03-19T16:11:37.567Z",
//            'fecha_entrada'=> date("Y-m-d\TH:i:s", $dateTime),
            'fecha_entrada_str'=> date("Y m d", $dateTime), //"2018 03 19",
            'request_docs'=>$request_docs, //si es true, el huésped obligatoriamente ha de adjuntar fotos de sus documentos de identidad.
            'establecimiento_id'=> $this->PARTEE_ID // Se puede consultar en Partee > Gestión > Alojamientos > Ver es el valor que aparece al lado de Alojamiento.
          ];
          
          $endpoint = 'parteviajeros/autocheckinMail';

          return $this->call( $endpoint,"POST", $data);
          
        } catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * Check status Partee
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function getCheckStatus($id)
    {
        $code = null;
        try
        {
          
          $endpoint = 'parteviajeros/status/'.$id;
          return $this->call( $endpoint,"GET");
          
        } catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * Get Partee CheckIn data
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function getCheckHuespedes($id)
    {
        $code = null;
        try
        {
          $endpoint = 'parteviajeros/'.$id;
          return $this->call( $endpoint,"GET");
        } catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }
    
    /**
     * Finish Partee CheckIn 
     * 
     * @param Partee ID $id
     * @return boolean
     * @throws \Exception
     */
    public function finish($id)
    {
        $code = null;
        try{
          
          $endpoint = 'parteviajeros/finalizar/'.$id;
          return ($this->call( $endpoint,"PUT"));
          
        } catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

     /**
     * Check status Partee
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function getParteePDF($id)
    {
        $code = null;
        try
        {
          $endpoint = 'parteviajeros/download/parte/'.$id;
          return $this->call( $endpoint,"GET");
          
        } catch (\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }
 
}
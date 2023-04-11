<?php

namespace App\Services;

class SMSService
{
    public $response;
    protected   $api;
    protected   $usr;
    protected   $psw;
    protected   $JWT;
    protected   $MENSAGIA_URL;
    protected   $API_VERSION;
    protected   $ENDPOINT;
    protected   $API_CONFIGURATION_NAME;
    

    public function __construct()
    {
      $this->MENSAGIA_URL = config('app.mensagia.base_uri');
      // Set the API SEND CONFIGURATION
      // Manage and create new configurations at https://mensagia.com/api/configurations
      $this->API_CONFIGURATION_NAME = config('app.mensagia.api_name');
      $this->API_VERSION = 'v1';
      $this->ENDPOINT = $this->MENSAGIA_URL.'/'.$this->API_VERSION;
      $this->JWT = null;
  
    }
    
    public function conect(){
      
      $data = array("email" =>  config('app.mensagia.usr'), "password" => config('app.mensagia.psw'));                                                                    
      $data_string = http_build_query($data);
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->ENDPOINT.'/login');
      curl_setopt($ch, CURLOPT_POST, 1);                                                                
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
      $result = curl_exec($ch);
      curl_exec($ch);
      curl_close($ch);
      
      $output = json_decode($result, true);
      if ( ! isset( $output['error'] ) )
      {
          // Successful authentication.
          // We keep the response for use in API requests
          $this->JWT = $output['data']['token'];
          return TRUE;
      }
      else
      {
        $this->response = $output['error']['message'];
        return false;
      }
    }
    
    
    public function sendSMS( $msg,$phone)
    {
      if(!$this->JWT) 
      { 
        $this->response = 'Token required';
        return FALSE; 
      } 
      
      $phone = preg_replace('/[^0-9]+/', '', $phone); //just numbers
      
      if (empty(trim($phone))){
        $this->response = 'Phone number required';
        return FALSE;
      }

      // Add authorization header to the request
      // It contains the authorization for the request
      $headers = array(
      'Authorization: Bearer '.$this->JWT
      );

      // Post variables to add to the request.
      $push_simple = array(
          'configuration_name'  =>  $this->API_CONFIGURATION_NAME,
          'message'             =>  $msg,
          'numbers'             =>  $phone
      );

      
      // CURL REQUEST FOR SIMPLE PUSH
      $ch2 = curl_init();
      curl_setopt($ch2, CURLOPT_URL,$this->ENDPOINT.'/push/simple');
      curl_setopt($ch2, CURLOPT_POST, 1);
      curl_setopt($ch2, CURLOPT_POSTFIELDS, $push_simple);
      curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
      $result = curl_exec ($ch2);
      curl_close ($ch2);
      $result = json_decode($result, true);


      // CHECK THE REQUEST
      if (isset($result['data']))
      {
        $this->response = json_encode($result['data']);
        return true;
      }
      else if (isset($result['error']))
      {
        //Api request failed
        $return  = $result['error']['message'];

        if (isset($result['error']['validation_errors']))
          $return .= json_encode($result['error']['validation_errors']);

        $this->response = $return;
        return FALSE;
      }

    }
 
}
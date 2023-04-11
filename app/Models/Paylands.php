<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;
use App\Services\PaylandService;
/**
 * Description of payments
 *
 * @author cremonapg
 */
class Paylands {

  private $paylandClient;
  private $signature;
  private $service;

  const SANDBOX_ENV = "/sandbox";

  public function __construct() {
    $endpoint = config('app.payland.endpoint');
    $endPoint = (config('app.payland.enviromment') == "dev") ? $endpoint . self::SANDBOX_ENV : $endpoint;
    
    $this->signature = config('app.payland.signature');
    $this->service = config('app.payland.service');
    $paylandConfig = [
        'endpoint' => $endPoint,
        'api_key' => config('app.payland.key'),
        'signarute' => $this->signature,
        'service' => $this->service
    ];
    $this->paylandClient = new PaylandService($paylandConfig);
  }
  
  public function generateOrderPaymentWidget($bookingID,$clientID,$client_email,$description,$amount,$is_deferred=false,$siteID=null,$suplemento=false){
      $key_token = md5($bookingID.'-'.time().'-'.$clientID);
      $urls = [
          'url_ok'   => route('widget.thanks.payment',$key_token),
          'url_ko'   => route('payland.error.payment',$key_token),
          'url_post' => route('payland.process.payment',$key_token),
          'token'    => $key_token
      ];
      
      // if ($suplemento){
      //   $urls['url_ok'] = route('widget.thanks.suplement',$key_token);
      // }

      return $this->getOrderPaymentBooking(
         $bookingID,
         $clientID,
         $client_email,
         $description,
         $amount,
         $is_deferred,
         $siteID,
         $urls);
   }
   
   public function generateOrderPaymentBooking($bookingID,$clientID,$client_email,$description,$amount,$is_deferred=false,$siteID=null){
      $key_token = md5($bookingID.'-'.time().'-'.$clientID);
      $urls = [
          'url_ok'   => route('payland.thanks.payment',$key_token),
          'url_ko'   => route('payland.error.payment',$key_token),
          'url_post' => route('payland.process.payment',$key_token),
          'token'    => $key_token
      ];
      
      if ($is_deferred)   $urls['url_ok']  = route('payland.thanks.deferred',$key_token);
      
     return $this->getOrderPaymentBooking(
         $bookingID,
         $clientID,
         $client_email,
         $description,
         $amount,
         $is_deferred,
         $siteID,
         $urls);
   }
      /**
     * Create link to new Payland
     * 
     * @param type $bookingID
     * @param type $clientID
     * @param type $client_email
     * @param type $description
     * @param type $amount
     * @return type
     */
    public function getOrderPaymentBooking($bookingID,$clientID,$client_email,$description,$amount,$is_deferred,$siteID,$urls){
          
      $key_token = $urls['token'];
      $type = $is_deferred ? 'DEFERRED' : 'AUTHORIZATION';
      $amount = ($amount * 100); // esto hay que revisar
      $response['_token']          = null;
      $response['amount']          = $amount;
      $response['customer_ext_id'] = $client_email;
      $response['operative']       = $type;
      $response['secure']          = false;
      $response['signature']       = $this->signature;
      $response['service']         = $this->service;
      $response['description']     = $description;
      $response['url_ok']          = $urls['url_ok'];
      $response['url_ko']          = $urls['url_ko'];
      $response['url_post']        = $urls['url_post'];
      
      $paylandClient = $this->paylandClient;
      $orderPayment  = $paylandClient->payment($response);

      if ($is_deferred)
        $BookOrders = new \App\BookDeferred();
      else 
        $BookOrders = new \App\BookOrders();
      
      $BookOrders->book_id = $bookingID;
      $BookOrders->cli_id = $clientID;
      $BookOrders->cli_email = $client_email;
      $BookOrders->subject = $description;
      $BookOrders->key_token = $key_token;
      $BookOrders->order_uuid = $orderPayment->order->uuid;
      $BookOrders->order_created = $orderPayment->order->created;
      $BookOrders->amount = $orderPayment->order->amount;
      $BookOrders->refunded = $orderPayment->order->refunded;
      $BookOrders->currency = $orderPayment->order->currency;
      $BookOrders->additional = $orderPayment->order->additional;
      $BookOrders->service = $orderPayment->order->service;
      $BookOrders->status = $orderPayment->order->status;
      $BookOrders->token = $orderPayment->order->token;
      $BookOrders->transactions = json_encode($orderPayment->order->transactions);
      $BookOrders->client_uuid = $orderPayment->client->uuid;
      $BookOrders->is_deferred = $is_deferred;
      $BookOrders->site_id = $siteID;
      $bo_id = $BookOrders->save();


      $urlToRedirect = $paylandClient->processPayment($orderPayment->order->token);
      return $urlToRedirect;

    }


}

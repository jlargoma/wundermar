<?php

namespace App\Http\Controllers;

class X_TEST extends AppController
{
  function call_1(){
    
    //https://admin.riadpuertasdelalbaicin.com/admin/reservas/update/1287/

    $WuBook = new \App\Services\Wubook\WuBook();
    $WuBook->conect();
    foreach ($content as $c){
      $WuBook->fetch_booking($c->lcode,$c->rcode);
    }
    $WuBook->disconect();
      
  }
  
  function call_2(){
    
  }
}
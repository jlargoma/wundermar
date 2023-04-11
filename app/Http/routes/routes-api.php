<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//API
  

Route::group(['prefix' => 'api'], function () {
  Route::get('page-booking', 'ApiController@view');
//  Route::post('get-items-suggest','ApiController@getItemsSuggest_withContent');
});

Route::group(['middleware' => 'apiControl','prefix' => 'api'], function () {
  Route::get('get-items-suggest','ApiController@getItemsSuggest');
  Route::post('get-items-suggest','ApiController@getItemsSuggest');
  Route::post('finish-booking-external','ApiController@finishBooking');
  Route::get('extas-opcions','ApiController@getExtasOpcion');
  Route::get('finish_booking','ApiController@finishBooking');
  Route::post('change-customer-booking-external','ApiController@changeCustomer');
  Route::get('booking/detail/{apto}', 'ApiController@getDetail');
  Route::get('booking/', 'ApiController@index');
//  Route::get('get-data-supplements', 'ApiController@rvaSuplementos');
//  Route::post('finish-supplements-external', 'ApiController@rvaSuplementosFinish');
});
?>
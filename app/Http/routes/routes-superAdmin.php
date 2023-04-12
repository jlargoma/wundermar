<?php

Route::group(['middleware' => 'authAdmin'], function () {
  
  Route::get('/partee-checkStatus/{id}', 'AppController@parteeCheckStatus');
  Route::get('/partee-getPDF/{id}', 'AppController@parteeGetPDF');
  Route::get('admin/cambiarCostes', 'BookController@changeCostes');
  // Usuarios
  Route::get('admin/usuarios', 'UsersController@index');
  Route::get('admin/usuarios/update/{id}', 'UsersController@update');
  Route::post('admin/usuarios/saveAjax', 'UsersController@saveAjax');
  Route::post('admin/usuarios/saveupdate', 'UsersController@saveUpdate');
  Route::post('admin/usuarios/create', 'UsersController@create');
  Route::get('admin/usuarios/delete/{id}', 'UsersController@delete');
  Route::post('admin/usuarios/search', 'UsersController@searchUserByName');

  // Clientes
  Route::get('admin/clientes', 'CustomersController@index');
  Route::get('/admin/galleries/{id?}','RoomsController@galleries');
// Prices
  Route::get('admin/precios','PricesController@index')->name('precios.base');
  Route::get('admin/precios/update','PricesController@update');
  Route::post('admin/precios/create','PricesController@create');
  Route::get('admin/precios/delete/{id}','PricesController@delete');
  Route::post('admin/precios/prepare-crom','PricesController@prepareYearPrices')->name('precios.prepare-cron');
  Route::post('admin/precios/prepare-crom-minStay','PricesController@prepareYearMinStay')->name('precios.prepare-cron-minStay');
  


  // seasons
  Route::get('admin/temporadas', 'SeasonsController@index');
  Route::get('admin/temporadas/new', 'SeasonsController@newSeasons');
  Route::get('admin/temporadas/new-type', 'SeasonsController@newTypeSeasons');
  Route::get('admin/temporadas/update/{id}', 'SeasonsController@update');
  Route::post('admin/temporadas/update/{id}', 'SeasonsController@update');
  Route::post('admin/temporadas/saveupdate', 'SeasonsController@saveUpdate');
  Route::post('admin/temporadas/create', 'SeasonsController@create');
  Route::post('admin/temporadas/create-type', 'SeasonsController@createType');
  Route::get('admin/temporadas/delete/{id}', 'SeasonsController@delete');

  //Liquidacion
  Route::get('admin/perdidas-ganancias/{year?}','LiquidacionController@perdidasGanancias')->name('pyg');
  Route::get('admin/perdidas-ganancias/show-detail/{key}','LiquidacionController@perdidasGananciasShowDetail');
  Route::post('admin/perdidas-ganancias/show-hide','LiquidacionController@perdidasGananciasShowHide');
  Route::post('admin/perdidas-ganancias/upd-ingr','LiquidacionController@perdidasGananciasUpdIngr');

  Route::get('admin/encuestas/{year?}/{apto?}', 'QuestionsController@admin');

  // AUX PROPIETARIOS
  Route::get('admin/propietarios/dashboard/{name?}/{year?}', 'OwnedController@index');
  Route::post('/ajax/booking/getBookingAgencyDetails', 'LiquidacionController@getBookingAgencyDetails');
  Route::get('/ajax/booking/getBookingAgencyDetails', 'LiquidacionController@getBookingAgencyDetails');
  /* ICalendar links */
  Route::post('/ical/import/saveUrl', 'ICalendarController@saveUrl');
  Route::get('/ical/urls/deleteUrl', 'ICalendarController@deleteUrl');
  Route::get('/ical/getLasts', 'ICalendarController@getLasts');
  Route::get('/ical/urls/getAllUrl/{aptoID}', 'ICalendarController@getAllUrl');
 
  Route::get('/procesarReservasTemporada', 'RouterActionsController@loadBookingsDays');
  
});

/**
 * GENERAL
 */
Route::group(['middleware' => 'authAdmin', 'prefix' => 'admin'], function () {
  
  Route::get('/reservas/api/getAlertLowProfits', 'BookController@getAlertLowProfits');
  Route::get('/reservas/api/activateAlertLowProfits', 'BookController@activateAlertLowProfits');
  Route::get('/reservas/fianzas/cobrar/{id}', 'BookController@cobrarFianzas');
  // Clientes
  Route::post('/clientes/create', 'CustomersController@create');
  Route::get('/clientes/export-excel', 'CustomersController@createExcel');
  Route::get('/customers/importExcelData', 'CustomersController@createExcel');
  Route::get('/clientes/delete/{id}', 'CustomersController@delete');
});

Route::group(['middleware' => ['auth','role:admin|subadmin|agente|recepcionista'], 'prefix' => 'admin',], function () {
  Route::get('/reservas/update/{id}', 'BookController@update')->name('book.update');
  Route::post('/reservas/saveUpdate/{id}', 'BookController@saveUpdate');
  Route::post('/reservas/api/getExtra', 'BookController@getDynamicExtraPrice');
  Route::post('/reservas/api/setExtra', 'BookController@setDynamicExtraPrice');
  Route::post('/reservas/api/updateExtra', 'BookController@updDynamicExtraPrice');
  Route::get('/reservas/api/get-all-extras/{id}', 'BookController@getDynamicExtraItems');
  Route::get('/reservas/api/getOTAsLogs', 'BookController@getLogErros_notRead');
  
   // Rooms
  Route::get('/apartamentos', 'RoomsController@index');
  Route::get('/apartamentos/new', 'RoomsController@newRoom');
  Route::get('/apartamentos/new-type', 'RoomsController@newTypeRoom');
  Route::get('/apartamentos/new-size', 'RoomsController@newSizeRoom');
  Route::post('/apartamentos/update', 'RoomsController@update');
  Route::get('/apartamentos/update-type', 'RoomsController@updateType');
  Route::get('/apartamentos/update-name', 'RoomsController@updateName');
  Route::get('/apartamentos/update-nameRoom', 'RoomsController@updateNameRoom');
  Route::get('/apartamentos/update-order', 'RoomsController@updateOrder');
  Route::get('/apartamentos/update-size', 'RoomsController@updateSize');
  Route::get('/apartamentos/update-owned', 'RoomsController@updateOwned');
  Route::get('/apartamentos/update-parking', 'RoomsController@updateParking');
  Route::get('/apartamentos/update-taquilla', 'RoomsController@updateTaquilla');
  Route::post('/apartamentos/saveupdate', 'RoomsController@saveUpdate');
  Route::post('/apartamentos/create', 'RoomsController@create');
  Route::post('/apartamentos/create-type', 'RoomsController@createType');
  Route::post('/apartamentos/create-size', 'RoomsController@createSize');
  Route::get('/apartamentos/state', 'RoomsController@state');
  Route::get('/apartamentos/percentApto', 'RoomsController@percentApto');
  Route::get('/apartamentos/update-Percent', 'RoomsController@updatePercent');
  Route::get('/apartamentos/email/{id}', 'RoomsController@email');
  Route::get('/apartamentos/fotos/{id}', 'RoomsController@photo');
  Route::get('/apartamentos/gallery/{id}', 'RoomsController@gallery');
  Route::get('/apartamentos/headers/{type}/{id}', 'RoomsController@headers');
  Route::get('/apartamentos/deletePhoto/{id}', 'RoomsController@deletePhoto');
  Route::post('/apartamentos/deletePhoto', 'RoomsController@deletePhotoItem');
  Route::post('/apartamentos/photo_main', 'RoomsController@photoIsMain');
  Route::post('/apartamentos/photo_orden', 'RoomsController@photoOrden');
  Route::post('/apartamentos/send/email/owned', 'RoomsController@sendEmailToOwned');

  Route::post('/apartamentos/upload-img-header', 'RoomsController@uploadHeaderFile');
  Route::post('/apartamentos/uploadFile', 'RoomsController@uploadRoomFile');
  Route::post('/apartamentos/uploadFile/{id}', 'RoomsController@uploadFile');
  Route::get('/apartamentos/assingToBooking', 'RoomsController@assingToBooking');
  Route::get('/apartamentos/download/contrato/{userId}', 'RoomsController@downloadContractoUser');
  
  
  Route::get('/apartamentos/fast-payment', 'RoomsController@updateFastPayment');
  Route::get('/apartamentos/update-order-payment', 'RoomsController@updateOrderFastPayment');
  Route::get('/sizeAptos/update-num-fast-payment', 'RoomsController@updateSizeAptos');
  Route::get('/rooms/getUpdateForm', 'RoomsController@getUpdateForm');
  Route::get('/rooms/cupos', 'RoomsController@getCupos');
  Route::get('/rooms/rooms-type', 'RoomsController@getRoomsType');
  Route::post('/rooms/rooms-type', 'RoomsController@updRoomsType');
});
Route::group(['middleware' => ['auth','role:admin|subadmin'], 'prefix' => 'admin'], function () {
  
  Route::get('/otaGate/test','OtasController@test');
  Route::get('/precios/preciosOTAs','OtasController@pricesOTAs')->name('precios.pricesOTAs');
  Route::post('/precios/preciosOTAs','OtasController@pricesOTAsUpd')->name('precios.pricesOTAs.upd');
  
  // OTAS  
  Route::get('/channel-manager/price/{apto?}','OtasController@calendRoom')->name('channel.price.cal');
  Route::post('/channel-manager/price/{apto?}','OtasController@calendRoomUPD')->name('channel.price.cal.upd');
  Route::get('/channel-manager/price/precios/list/{apto}','OtasController@listBy_room')->name('channel.price.cal.list');
  Route::get('/channel-manager/config', 'OtasController@generate_config');
  Route::get('/channel-manager/index', 'OtasController@index')->name('channel.index');
  Route::post('/channel-manager/send-avail/{apto}', 'OtasController@sendAvail')->name('channel.sendAvail');
  Route::get('/channel-manager/price-site/{site?}/{month?}/{year?}','OtasController@calendSite')->name('channel.price.site');
  Route::post('/upd-price-site/','OtasController@calendSiteUpd')->name('channel.price.site.upd');
  Route::get('/channel-manager/promocion/{promoID?}','PromotionsController@getItem')->name('channel.promotions.get');
  Route::get('/channel-manager/promociones/','PromotionsController@index')->name('channel.promotions');
  Route::post('/channel-manager/promociones/create','PromotionsController@create')->name('channel.promotions.new');
  Route::post('/channel-manager/promociones/upd','PromotionsController@update')->name('channel.promotions.upd');
  Route::delete('/channel-manager/promociones','PromotionsController@delete')->name('channel.promotions.delete');
  Route::get('/channel-manager/controlOta/','OtasController@controlOta')->name('channel.price.diff');

});

Route::group(['middleware' => ['auth','role:admin|subadmin|recepcionista'], 'prefix' => 'admin',], function () {
  
  // Clientes
  Route::get('/get_mails', 'ChatEmailsController@index');
  Route::get('/galleries', 'RoomsController@galleries');

    
  Route::post('/reservas/save-creditCard', 'BookController@save_creditCard')->name('booking.save_creditCard');
  Route::post('/reservas/get-visa', 'BookController@getVisa')->name('booking.get_visa');
  Route::post('/reservas/upd-visa', 'BookController@updVisa')->name('booking.upd_visa');
  Route::get('/get-books-without-cvc', 'BookController@getBooksWithoutCvc');
  
  Route::post('/reservas/change-mail-notif', 'BookController@changeMailNotif')->name('booking.changeMailNotif');
  Route::get('/reservas/get-payment-block/{bookingID}', 'BookController@paymentBlock')->name('booking.paymentBlock');
  Route::get('/book-logs/see-more/{id}', 'BookController@getBookLog');
  Route::get('/book-logs/see-more-mail/{id}', 'BookController@getMailLog');
  Route::get('/book-logs/{id}/{month?}', 'BookController@printBookLogs');
  Route::post('/response-email', 'BookController@sendEmailResponse');
  
  Route::post('/reservas/stripe/save/fianza', 'StripeController@fianza');
  Route::post('/reservas/stripe/pay/fianza', 'StripeController@payFianza');
  Route::get('/reservas/delete/{id}', 'BookController@delete');

  Route::get('/reservas/changeBook/{id}', 'BookController@changeBook');
  Route::get('/reservas/changeStatusBook/{id}', 'BookController@changeBook');
  Route::get('/reservas/ansbyemail/{id}', 'BookController@ansbyemail');
  Route::post('/reservas/sendEmail', 'BookController@sendEmail');
  Route::get('/reservas/saveFianza', 'BookController@saveFianza');
  Route::get('/reservas/reserva/{id}', 'BookController@tabReserva');
  Route::get('/reservas/cobrar/{id}', 'BookController@cobroBook');
  Route::get('/reservas/api/lastsBooks/{type?}', 'BookController@getLastBooks');
  Route::get('/reservas/api/intercambio', 'BookController@getIntercambio');
  Route::get('/reservas/api/intercambio-search/{block}/{search?}', 'BookController@getIntercambioSearch');
  Route::post('/reservas/api/intercambio-change', 'BookController@intercambioChange');
  Route::get('/reservas/api/calendarBooking', 'BookController@getCalendarBooking');
  Route::get('/reservas/api/alertsBooking', 'BookController@getAlertsBooking');

  Route::get('/reservas/api/sendSencondEmail', 'BookController@sendSencondEmail');
  Route::get('/reservas/api/toggleAlertLowProfits', 'BookController@toggleAlertLowProfits');
  
    //Paylands
  Route::get('/orders-payland', 'PaylandsController@lstOrders');
  Route::post('/getOrders-payland', 'PaylandsController@getOrders');
  Route::get('/get-summary-payland', 'PaylandsController@getSummary');
  Route::get('/getOrderID/{uid}', 'PaylandsController@getOrder');
  
  
  // WUBOOK
  Route::get('/Wubook', 'WubookController@index');
  Route::post('/Wubook/sendPrices', 'WubookController@sendPricesGroup')->name('Wubook.sendPrices');
  Route::post('/Wubook/sendMinStay', 'WubookController@sendMinStayGroup')->name('Wubook.sendMinStay');
  Route::get('/Wubook/createAvails', 'WubookController@createAvails')->name('Wubook.createAvails');
  Route::get('/Wubook/sendAvails', 'WubookController@sendAvails')->name('Wubook.sendAvails');
  Route::get('/Wubook/processHook', 'WubookController@processHook')->name('Wubook.processHook');

  

  Route::get('/liquidacion', 'LiquidacionController@index');
  Route::get('/liquidacion-apartamentos', 'LiquidacionController@apto');
  Route::get('/liquidacion/export/excel', 'LiquidacionController@exportExcel');

  //Caja
  Route::get('/caja', 'LiquidacionController@caja');
  Route::post('/caja/cajaLst', 'LiquidacionController@getTableCaja');
  Route::post('/caja/del-item', 'LiquidacionController@delCajaItem');
  Route::get('/caja/getTableMoves/{year?}/{type}', 'LiquidacionController@getTableMoves');
  Route::post('/arqueo/create', 'LiquidacionController@arqueoCreate');
  Route::post('/ingresos/create', 'LiquidacionController@ingresosCreate');
  Route::post('/delIngr', 'LiquidacionController@delIngr');
  Route::post('/gastos/create', 'LiquidacionController@gastoCreate');

});
  
Route::group(['middleware' => ['auth','role:admin|subadmin'], 'prefix' => 'admin',], function () {
 
  //Liquidacion
    
  Route::post('/gastos/importar', 'LiquidacionController@gastos_import');
  Route::post('/gastos/gastosLst', 'LiquidacionController@getTableGastos');
  Route::post('/gastos/update', 'LiquidacionController@updateGasto');
  Route::get('/gastos/getHojaGastosByRoom/{year?}/{id}', 'LiquidacionController@getHojaGastosByRoom');
  Route::get('/gastos/containerTableExpensesByRoom/{year?}/{id}', 'LiquidacionController@getTableExpensesByRoom');
  Route::post('/gastos/del', 'LiquidacionController@gastosDel');
  Route::get('/gastos/delete/{id}','RouterActionsController@gastos_delete');
  Route::get('/gastos/{year?}', 'LiquidacionController@gastos');
  Route::get('/ingresos', 'LiquidacionController@ingresos');
  Route::post('/ingresos/upd', 'LiquidacionController@ingresosUpd');
  Route::get('/ingresos/delete/{id}', 'RouterActionsController@ingresos_delete');
  Route::get('/estadisticas/{year?}','LiquidacionController@Statistics');
  Route::get('/contabilidad','LiquidacionController@contabilidad');
  Route::get('/processData','RouterActionsController@processData');
  

  Route::post('/cashBox/create', 'LiquidacionController@cashBoxCreate');
  Route::get('/cashbox/updateSaldoInicial/{id}/{type}/{importe}','RouterActionsController@cashbox_updateSaldoInicial');
  Route::get('/banco', 'LiquidacionController@bank');
  Route::get('/banco/getTableMoves/{year?}/{type}', 'LiquidacionController@getTableMovesBank');
  Route::get('/bank/updateSaldoInicial/{id}/{type}/{importe}','RouterActionsController@bank_updateSaldoInicial');
  Route::get('/rules/stripe/update', 'RulesStripeController@update');
  Route::get('/days/secondPay/update/{id}/{days}','RouterActionsController@days_secondPay_update');
  Route::get('/estadisticas/{year?}', 'LiquidacionController@Statistics');
  
  Route::get('/ical/importFromUrl', 'AppController@importFromUrl');
  Route::get('/zodomus/import', 'ZodomusController@importBookings');
  Route::get('/channel-manager-test', 'ZodomusController@channel_manager_test');
});
?>

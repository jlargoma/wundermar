<?php


   
    
Route::group(['middleware' => ['auth','role:admin|limpieza|subadmin|recepcionista']], function () {
  
  Route::post('admin/sendEncuesta', 'BookController@sendEncuesta')->name('sendEncuesta');
  Route::get('admin/showFormEncuesta/{id?}', 'BookController@showFormEncuesta')->name('showFormEncuesta');
  
  //LIMPIEZA
  Route::get('admin/limpiezas/{year?}','LimpiezaController@limpiezas');
  Route::post('admin/limpiezasLst/','LimpiezaController@get_limpiezas');
  Route::post('admin/limpiezasUpd/','LimpiezaController@upd_limpiezas');
  Route::post('admin/limpiezas/pdf','LimpiezaController@export_pdf_limpiezas');
  Route::post('admin/limpieza/bloquear', 'LimpiezaController@bloqueos');
  Route::post('admin/limpieza/extr-deliver', 'LimpiezaController@deliverExtra');
  Route::get('admin/limpieza/delete-block/{id}', 'LimpiezaController@bloqueos_delete');
  Route::get('admin/limpieza', 'LimpiezaController@index');

  //Extras
  Route::get('admin/sales', 'ExtrasController@sales_index')->name('revenue.sales');
  Route::get('admin/sales/{year?}','ExtrasController@sales_index');
  Route::post('admin/salesLst/','ExtrasController@get_sales_list');

  //Estadísticas XML
  Route::post('admin/INE', 'INEController@sendData')->name('INE.sendEncuesta');
  Route::get('admin/show-INE', 'INEController@index');
  Route::post('admin/show-INE', 'INEController@showData');
  Route::get('admin/download-INE/{type}/{range}/{force}/{unic}', 'INEController@download');
  
  Route::get('admin/revenue/getRateCheckWubook', 'RevenueController@getRateCheckWubook');
  Route::get('admin/revenue/DISPONIBLIDAD-x-ALOJAMIENTO/{apto?}/{site?}/{range?}', 'RevenueController@disponibilidad')->name('revenue.disponibilidad');
  Route::post('admin/revenue/descargar-disponibilidad', 'RevenueController@donwlDisponib')->name('revenue.donwlDisponib');
  Route::post('admin/revenue/upd-disponibl', 'RevenueController@updDisponib');
  Route::get('admin/revenue/PICK-UP/{month?}', 'RevenueController@pickUp')->name('revenue.pickUp');
  Route::get('admin/revenue/RATE-SHOPPER', 'RevenueController@rate_shopper')->name('revenue.rate');
  Route::get('admin/revenue/VENTAS-POR-DIA', 'RevenueController@daily')->name('revenue.daily');
  Route::post('admin/revenue/donwlVtasDia', 'RevenueController@donwlDaily')->name('revenue.donwlVtasDia');
  Route::get('admin/revenue/RATE-SHOPPER/generate', 'RevenueController@setRateCheckWubook');
  Route::get('admin/revenue/DASHBOARD', 'RevenueController@index')->name('revenue');
  Route::post('admin/revenue/generate', 'RevenueController@generate')->name('revenue.generate');
  Route::post('admin/revenue/generatePickUp', 'RevenueController@generatePickUp')->name('revenue.generatePickUp');
  Route::post('admin/revenue/donwlPickUp', 'RevenueController@donwlPickUp')->name('revenue.donwlPickUp');
  Route::post('admin/revenue/PickUp/update', 'RevenueController@updPickUp')->name('revenue.updPickUp');
  Route::get('admin/revenue/getMonthKPI/{mes}', 'RevenueController@getMonthKPI');
  Route::get('admin/revenue/getMonthDisp/{mes}', 'RevenueController@getMonthDisp');
  Route::get('admin/revenue/getOverview/{mes}', 'RevenueController@getOverview');
  Route::post('admin/revenue/upd-Overview', 'RevenueController@updOverview');
  Route::post('admin/revenue/upd-fixedcosts', 'RevenueController@updFixedcosts');
  Route::get('admin/revenue/getComparativaAnual/{year}', 'RevenueController@getComparativaAnual');
  Route::get('admin/revenue/getFixedcostsAnual/{year}', 'RevenueController@getFixedcostsAnual');
  Route::post('admin/revenue/copyFixedcostsAnualTo/{year}/{site}', 'RevenueController@copyFixedcostsAnualTo');
  Route::get('admin/revenue/anioNatura/{site?}/{year?}/{trim?}', 'RevenueController@balanceAnioNatural');
  
  Route::get('admin/createFianza/{id}', 'BookController@createFianza');
  //PARTEE
  Route::get('admin/sendPartee/{id}', 'BookController@sendPartee');
  Route::get('ajax/partee-checkHuespedes/{id}', 'BookController@seeParteeHuespedes')->name('partee.checkHuespedes');
  Route::get('ajax/partee-syncCheckInStatus', 'BookController@syncCheckInStatus')->name('partee.sinc');
  Route::get('admin/get-partee', 'BookController@getParteeLst');
  Route::get('/get-partee-msg', 'BookController@getParteeMsg');
  Route::post('/ajax/send-partee-finish', 'BookController@finishParteeCheckIn');
  Route::post('/ajax/send-partee-sms', 'BookController@finishParteeSMS');
  Route::post('/ajax/send-fianza-sms', 'BookController@sendFianzaSMS');
  Route::post('/ajax/send-partee-mail', 'BookController@finishParteeMail');
  Route::post('/ajax/send-fianza-mail', 'BookController@sendFianzaMail');
  Route::get('/ajax/showSendRemember/{bookID}', 'BookController@showSendRemember');
  
  Route::get('/ajax/showSafetyBox/{bookID}', 'BookController@showSafetyBox');
  Route::get('/ajax/showSafetyBox-site/{siteID}', 'BookController@showSafetyBoxBySite');
  Route::post('/ajax/showSafetyBox-updKey', 'BookController@updKeySafetyBoxBySite');
  Route::post('/ajax/createSafetyBox', 'BookController@createSafetyBox');
  Route::get('/ajax/updSafetyBox/{bookID}/{value}/{min?}', 'BookController@updSafetyBox');
  Route::post('/ajax/updSafetyBoxKey-site', 'BookController@updSafetyBoxKey');
  Route::get('/ajax/SafetyBoxMsg/{bookID}', 'BookController@getSafetyBoxMsg');
  Route::post('/ajax/send-SafetyBox-sms', 'BookController@sendSafetyBoxSMS');
  Route::post('/ajax/send-SafetyBox-mail', 'BookController@sendSafetyBoxMail');
  Route::get('/admin/get-SafetyBox', 'BookController@getSafetyBoxLst');

  Route::post('admin/removeAlertPax', 'BookController@removeAlertPax');
  Route::post('admin/saveCustomerRequest', 'BookController@saveCustomerRequest');
  Route::post('admin/getCustomersRequest', 'BookController@getCustomersRequest');
  Route::get('/admin/getCustomerRequestBook/{bID}', 'BookController@getCustomersRequest_book');
  
  Route::get('/ajax/showFianza/{bookID}', 'BookController@showFianza');
  
  // Route::get('/resume-by-book/{id}', 'ForfaitsItemController@getResumeBy_book');
  Route::get('/ajax/get-book-comm/{bookID}', 'BookController@getComment');
  Route::get('/ajax/get-size-site/{ID}', 'BookController@getSizesBySite');

  Route::get('/ajax/getPaymentRemainder/{bookID}', 'BookController@showPaymentRemember');
  Route::get('/ajax/sendPaymentRemainder/{bookID}', 'BookController@sendRemenberPaymentMail');


  
  /**
 * Aptos
 */
  Route::group(['prefix' => 'admin/aptos',], function () {
    Route::get('/edit-room-descript/{id}', 'RoomsController@editRoomDescript');
    Route::get('/edit-descript/{id}', 'RoomsController@editDescript');
    Route::post('/edit-room-descript', 'RoomsController@updRoomDescript');
    Route::post('/edit-descript', 'RoomsController@updDescript');
  });


  //AGENTES
  Route::post('/admin/agentRoom/create', 'SettingsController@createAgentRoom');
  Route::get('/admin/agentRoom/delete/{id}', 'SettingsController@deleteAgentRoom');
  
    //PDFÂ´s
  Route::get('admin/pdf/pdf-reserva/{id}', 'PdfController@invoice');
  Route::get('admin/pdf/descarga-excel-propietario/{id}', 'PdfController@pdfPropietario');
  
  Route::get('admin/reservas/search/searchByName', 'BookController@searchByName');
  Route::get('/reservas/stripe/pagos/{id_book}', 'StripeController@stripePayment');
  Route::post('/reservas/stripe/payment/', 'StripeController@stripePaymentResponse');
  Route::post('/admin/reservas/stripe/paymentsBooking', 'StripeController@stripePaymentBooking');

  /* Planing */
  Route::post('/getPriceBook', 'HomeController@getPriceBook');
  Route::get('/getFormBook', 'HomeController@form');
  Route::get('/getCitiesByCountry', 'HomeController@getCitiesByCountry');
  Route::get('/getCalendarMobile/{month?}', 'BookController@getCalendarMobileView');
  Route::get('/getCalendarChannel/{room}/{month?}', 'BookController@getCalendarChannelView');
  Route::get('/getCalendarSite/{site_id}/{month?}', 'BookController@getCalendarSiteView');
  Route::post('admin/reservas/create', 'BookController@create')->name('book.create');

  Route::post('/ajax/toggleCliHas', 'BookController@toggleCliHas');
});

Route::group(['middleware' => 'authSubAdmin'], function () {
    Route::get('admin/reservas', 'BookController@index')->name('dashboard.planning');
    Route::get('admin/reservas/emails/{id}',  'BookController@emails');
    Route::get('admin/reservas/sendJaime',  'BookController@sendJaime');
    Route::get('admin/reservas/saveCobro', 'BookController@saveCobro');
    Route::get('admin/reservas/deleteCobro/{id}', 'BookController@deleteCobro');
   });
Route::group(['middleware' => ['auth','role:admin|propietario|recepcionista'], 'prefix' => 'admin',], function () {
  
  Route::get('/consultar-google', 'HomeController@checkPrices');
  
  //Facturas
  Route::get('/facturas/ver/{id}', 'InvoicesController@view')->name('invoice.view');
  Route::get('/facturas/editar/{id}', 'InvoicesController@update')->name('invoice.edit');
  Route::post('/facturas/guardar', 'InvoicesController@save')->name('invoice.save');
  Route::post('/facturas/enviar', 'InvoicesController@sendMail')->name('invoice.sendmail');
  Route::get('/facturas/modal/editar/{id}', 'InvoicesController@update_modal');
  Route::post('/facturas/modal/guardar', 'InvoicesController@save_modal');
  Route::delete('/facturas/borrar', 'InvoicesController@delete')->name('invoice.delete');
  Route::get('/facturas/descargar/{id}', 'InvoicesController@download')->name('invoice.downl');
  Route::get('/facturas/descargar-todas', 'InvoicesController@downloadAll');
  Route::get('/facturas/descargar-todas/{year}/{id}', 'InvoicesController@downloadAllProp');
  Route::get('/facturas/solicitudes/{year?}', 'InvoicesController@solicitudes');
  Route::get('/facturas/{order?}', 'InvoicesController@index');
  
  
});

/** Moved form routers */
Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
  Route::get('/rooms/api/getImagesRoom/{id?}/{bookId?}', 'RoomsController@getImagesRoom');
  Route::get('/reservas/api/getTableData', 'BookController@getTableData');
  Route::get('/reservas/new', 'BookController@newBook');
  Route::post('/reservas/new', 'BookController@newBook');
  Route::get('/apartamentos/getPaxPerRooms/{id}', 'RoomsController@getPaxPerRooms');
  Route::get('/apartamentos/getLuxuryPerRooms/{id}', 'RoomsController@getLuxuryPerRooms');
  Route::get('/api/reservas/getDataBook', 'BookController@getAllDataToBook');
  
  Route::get('/reservas/help/calculateBook','AppController@calculateBook');
  Route::post('/reservas/help/calculateBook','AppController@calculateBook');
  Route::get('/update/seasonsDays/{val}', 'RouterActionsController@seasonsDays');
  Route::get('/update/percentBenef/{val}', 'LiquidacionController@changePercentBenef');
  Route::post('/reservas/help/getTotalBook', 'BookController@getTotalBook');
  Route::get('/delete/nofify/{id}', 'RouterActionsController@nofify');
  Route::get('/reservas/changeSchedule/{id}/{type}/{schedule}', 'RouterActionsController@changeSchedule');
  Route::get('/reservas/restore/{id}/', 'RouterActionsController@restore');
  Route::get('/books/{idBook}/comments/{type}/save', 'BookController@saveComment');
  Route::get('/liquidation/searchByName', 'LiquidacionController@searchByName');
  Route::get('/liquidation/searchByRoom', 'LiquidacionController@searchByRoom');
  Route::get('/liquidation/orderByBenefCritico', 'LiquidacionController@orderByBenefCritico');
  Route::get('/apartamentos/rooms/getTableRooms/', 'RouterActionsController@getTableRooms');
  Route::get('/rooms/search/searchByName', 'RoomsController@searchByName');
  Route::get('/customer/change/phone/{id}/{phone}','RouterActionsController@customer_change');
  
  Route::get('/sendImagesRoomEmail', 'RoomsController@sendImagesRoomEmail');
  Route::get('/books/getStripeLink/{book}/{importe}','RouterActionsController@books_getStripeLink');

  Route::get('/sales/updateLimpBook/{id}/{importe}','RouterActionsController@sales_updateLimpBook');
  Route::get('/sales/updateExtraCost/{id}/{importe}','RouterActionsController@sales_updateExtraCost');
  Route::get('/sales/updateCostApto/{id}/{importe}','RouterActionsController@sales_updateCostApto');
  Route::get('/sales/updateCostPark/{id}/{importe}','RouterActionsController@sales_updateCostPark');
  Route::get('/sales/updateCostTotal/{id}/{importe}','RouterActionsController@sales_updateCostTotal');
  Route::get('/sales/updatePVP/{id}/{importe}','RouterActionsController@sales_updatePVP');
  Route::get('/invoices/searchByName/{searchString?}','RouterActionsController@invoices_searchByName');
  Route::post('/specialSegments/delete/{id?}', 'SpecialSegmentController@delete');
 
  //YEARS
  Route::get('/years/get', 'YearsController@getYear')->name('years.get');
  Route::post('/years/change', 'YearsController@changeActiveYear')->name('years.change');
  Route::post('/years/change/months', 'YearsController@changeMonthActiveYear')->name('years.change.month');
  //SETTINGS
  Route::post('/settings/createUpdate', 'SettingsController@createUpdateSetting')->name('settings.createUpdate');
  Route::get('/settings', 'SettingsController@index');
  Route::post('/settings-general', 'SettingsController@upd_general')->name('settings.gral.upd');
  Route::post('/settings-edificios', 'SettingsController@upd_sites')->name('settings.sites.upd');
  Route::post('/settings-longs-general', 'SettingsController@upd_longs_general')->name('settings.longs.upd');
  Route::get('/settings_msgs/{site?}/{key?}', 'SettingsController@messages')->name('settings.msgs');
  Route::post('/settings_msgs/{site}/{lng}', 'SettingsController@messages_upd')->name('settings.msgs.upd');
  Route::post('/specialSegments/create', 'SpecialSegmentController@create');
  Route::get('/specialSegments/update/{id?}', 'SpecialSegmentController@update');
  Route::post('/specialSegments/update/{id?}', 'SpecialSegmentController@update');
  Route::delete('/specialSegments/delete', 'SpecialSegmentController@delete');
  Route::post('settings/updExtraPaxPrice','SettingsController@updExtraPaxPrice')->name('settings.extr_pax_price');
  Route::get('settings/updateExtra','SettingsController@updExtraPrices')->name('settings.extr_price.upd');
  Route::post('settings/createExtras','SettingsController@createExtraPrices')->name('settings.extr_price.create');
  Route::delete('settings/createExtras','SettingsController@delteExtraPrices')->name('settings.extr_price.del');
  Route::get('settings/updateWeiland','SettingsController@updateWeiland')->name('settings.weiland.upd');
  
  //PAYMENTS
  Route::get('/links-payland', 'PaylandsController@link');
  Route::get('/links-payland-single', 'PaylandsController@linkSingle');
});

Route::group(['middleware' => ['auth','role:admin|subadmin|recepcionista']], function () {

  // Clientes
  Route::get('admin/clientes/update', 'CustomersController@update');
  Route::post('admin/clientes/save', 'CustomersController@save');
  Route::get('admin/customer/delete/{id}','CustomersController@delete');
  Route::post('/clientes/create', 'CustomersController@create');
  Route::get('admin/customers/searchByName/{searchString?}','CustomersController@searchByName');

  
  // Pagos
  Route::get('admin/pagos/create', 'PaymentsController@create');
  Route::get('admin/pagos/update', 'PaymentsController@update');
  Route::post('admin/pagos/cobrar', 'PaymentsController@cobrarFianza');
  Route::get('admin/pagos', 'PaymentsController@index');
  
});
  
  
Route::get('test-text/{lng}/{key?}/{ota?}', 'SettingsController@testText');

?>
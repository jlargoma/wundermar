<?php
    /*
    |--------------------------------------------------------------------------
    | Application Routes
    |--------------------------------------------------------------------------
    |
    | IMPORTANT...!!!
    | File created by console: > php app/Http/routes/create.php
    | The changes in this file will be removed
    | No change this file
    |
    */
    
    Route::auth();
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    Route::get('create-cache', 'HomeController@index')->middleware('page-cache');
    Route::get('/404', 'AppController@get_404');
    Route::get('/no-allowed','AppController@no_allowed');
    Route::get('403','AppController@no_allowed');
    Route::post('static-token','AppController@staticToken');
    Route::get('/partee-checkHuespedes','AppController@partee_checkHuespedes');
//    Route::post('zodomus-Webhook','ZodomusController@webHook');
    // Route::get('wubook-Webhook', 'OtasController@webHook_Wubook');
    Route::post('wubook-Webhook', 'OtasController@webHook_Wubook');
    Route::post('Ota-Gateway-Webhook/{siteID}', 'OtasController@webHook');
  
    Route::get('/thanks-you', 'HomeController@thanksYou')->name('thanks-you');
    Route::get('/thanks-you-forfait','AppController@thanksYou_forfait')->name('thanks-you-forfait');
    Route::get('/paymeny-error', 'HomeController@paymenyError')->name('paymeny-error');
    Route::get('/form-demo', 'BookController@demoFormIntegration');
    Route::post('/api/check_rooms_avaliables', 'BookController@apiCheckBook')->name('api.proccess');
    
    Route::group(['middleware' => 'web'], function () {
      Route::get('/sitemap', 'HomeController@siteMap');
      Route::get('/apartamentos/galeria/{apto}', 'HomeController@galeriaApartamento');
      Route::get('/apartamentos/{apto}', 'HomeController@apartamento')->name('web.apto');
      Route::get('/fotos/{apto}', 'HomeController@apartamento');
      Route::get('/el-edificio', 'HomeController@edificio')->name('web.edificio');
      Route::get('/el-hotel', 'HomeController@edificio');
      Route::get('/contacto', 'HomeController@contacto');
      Route::get('/blog/{name?}', 'HomeController@blog')->name('blog');
    });

    Route::post('/excursiones', 'ExcursionsControllers@sendWishList');
    Route::get('/excursiones', 'ExcursionsControllers@frontend')->name('excursions.frontend');
    
    
    //   /*Correos Frontend */
    Route::post('/contacto-form', 'HomeController@formContacto');
    Route::post('/contacto-ayuda', 'HomeController@formAyuda');
    Route::post('/contacto-propietario', 'HomeController@formPropietario');
    Route::post('/contacto-grupos', 'HomeController@formGrupos');
    //   /* Correos Frontend */getCalendarMobile
    Route::get('/buzon', 'HomeController@buzon');
    Route::get('/terminos-condiciones', 'HomeController@terminos');
    Route::get('/politica-cookies', 'HomeController@politicaCookies');
    Route::get('/politica-privacidad', 'HomeController@politicaPrivacidad');
    Route::get('/condiciones-generales', 'HomeController@condicionesGenerales');
    Route::get('/preguntas-frecuentes', 'HomeController@preguntasFrecuentes');
    Route::get('/eres-propietario', 'HomeController@eresPropietario');
    Route::get('/grupos', 'HomeController@grupos');
    Route::get('/quienes-somos', 'HomeController@quienesSomos');
    Route::get('/ayudanos-a-mejorar', 'HomeController@ayudanosAMejorar');
    Route::get('/aviso-legal', 'HomeController@avisoLegal');
    Route::get('/huesped', 'HomeController@huesped');
    Route::get('/el-tiempo', 'HomeController@tiempo');
    Route::get('/condiciones-contratacion', 'HomeController@condicionesContratacion')->name('cond.contratacion');
    Route::get('/condiciones-fianza', 'HomeController@condicionesFianza')->name('cond.fianza');
    Route::get('/restaurantes', 'HomeController@restaurantes')->name('cond.resto');
    Route::post('/solicitudForfait', 'HomeController@solicitudForfait');
    Route::get('/admin/links-stripe', 'StripeController@link');
    Route::get('/payments-forms/{token}', 'PaylandsController@paymentsForms')->name('front.payments');
    Route::post('/payments-save-dni/{token}', 'PaylandsController@saveDni')->name('front.payments.dni')->middleware('cors');
    Route::post('/payments-save-supplement/{token}', 'PaylandsController@addExtrs')->name('front.payments.addExtrs')->middleware('cors');


    /* ENCUESTAS */
    Route::get('/encuesta-satisfaccion/{id}', 'QuestionsController@index');
    Route::post('/questions/vote', 'QuestionsController@vote');
    /* FIN ENCUESTAS*/
    /* CRONTABS */
    Route::get('/admin/reservas/api/checkSecondPay', 'BookController@checkSecondPay');
    // AJAX REQUESTS
    Route::post('/ajax/requestPrice', 'FortfaitsController@calculatePrice');
    Route::post('/ajax/forfaits/updateRequestStatus', 'FortfaitsController@updateRequestStatus');
    Route::post('/ajax/forfaits/updateRequestPAN', 'FortfaitsController@updateRequestPAN');
    Route::post('/ajax/forfaits/updateRequestComments', 'FortfaitsController@updateRequestComments');
    Route::post('/ajax/forfaits/updateCommissions', 'FortfaitsController@updateCommissions');
    Route::post('/ajax/forfaits/updatePayments', 'FortfaitsController@updatePayments');
    Route::post('/ajax/forfaits/requestPriceForfaits', 'FortfaitsController@requestPriceForfaits');

    // ReCaptcha v3
    Route::post('/ajax/checkRecaptcha', 'FortfaitsController@checkReCaptcha');

    //PAYLANDS
    Route::post('/paylands/payment', 'PaylandsController@payment')->name('payland.payment');
    Route::post('/paylands/get-payment-by-type', 'PaylandsController@getPaymentByType')->name('payland.get_payment');
    Route::get('payment/thansk-you/{key_token}', 'PaylandsController@thansYouPayment')->name('payland.thanks.payment');
    Route::get('payment/thansk-you-deferred/{key_token}', 'PaylandsController@thansYouPaymentDeferred')->name('payland.thanks.deferred');
    Route::get('payment/thansk-you-payment/{key_token}', 'PaylandsController@widgetPayment')->name('widget.thanks.payment');
    Route::get('payment/suplement-payment/{key_token}', 'PaylandsController@widgetPaymentSuplementos')->name('widget.thanks.suplement');
    Route::get('payment/error/{key_token}', 'PaylandsController@errorPayment')->name('payland.error.payment');
    Route::get('payment/process/{key_token}', 'PaylandsController@processPayment')->name('payland.process.payment');
    Route::post('payment/process/{key_token}', 'PaylandsController@processPayment')->name('payland.process.payment');
    //});
    
    Route::get('/paylands/payment', 'PaylandsController@paymentTest');
    Route::get('/admin','AppController@admin')->middleware('auth');   
    /* ICalendar links */
    Route::get('/ical/{aptoID}', [
        'as' => 'import-iCalendar',
        'uses' => 'ICalendarController@getIcalendar'
    ])->where('aptoID', '[0-9]+');

     
    Route::get('/test/call-1/{param?}','X_TEST@call_1')->middleware('auth');   
    Route::get('/test/call-2/{param?}','X_TEST@call_2')->middleware('auth');   
//    Route::get('/ical/importFromUrl', function () {
//      \Artisan::call('ical:import');
////      return redirect('/admin/reservas');
//    });
    
    
  Route::get('/factura/{id}/{num}/{emial}', 'InvoicesController@donwload_external');
  Route::post('/payments-moto', 'PaylandsController@processPaymentMoto');
  Route::get('/clear-cookies','RouterActionsController@clearCookies');


    Route::get('/payments-forms-forfaits/{token}', 'ForfaitsItemController@paymentsForms')->name('front.payments.forfaits');
    Route::get('/payments-forms-forfaits-complete/{token}', 'ForfaitsItemController@paymentsFormsAll')->name('front.payments.forfaits.all');
    Route::get('/api/forfaits/thansk-you/{key_token}', 'ForfaitsItemController@thansYouPayment')->name('payland.thanks.forfait');
    Route::get('/api/forfaits/error/{key_token}', 'ForfaitsItemController@errorPayment')->name('payland.error.forfait');
    Route::get('/api/forfaits/process/{key_token}', 'ForfaitsItemController@processPayment')->name('payland.process.forfait');
    Route::post('/api/forfaits/process/{key_token}', 'ForfaitsItemController@processPayment')->name('payland.process.forfait');
    Route::get('/api/forfaits/token', 'ForfaitsItemController@getUserAdmin');
    Route::post('/api/forfaits/getPayments', 'ForfaitsItemController@getPayments');
    Route::post('/api/forfaits/createPayment', 'ForfaitsItemController@createPayment');
    Route::post('/api/forfaits/createPaylandsUrl', 'ForfaitsItemController@createPaylandsUrl');
    Route::post('/api/forfaits/quickOrders', 'ForfaitsItemController@quickOrders');
    Route::post('/api/forfaits/changeStatus', 'ForfaitsItemController@changeStatus');
    Route::get('/api/forfaits/class', 'ForfaitsItemController@api_getClasses');
    Route::get('/api/forfaits/categ', 'ForfaitsItemController@api_getCategories');
    Route::get('/api/forfaits/items/{id}', 'ForfaitsItemController@api_items');
    Route::post('/api/forfaits/cancelItem', 'ForfaitsItemController@cancelItem');
    Route::post('/api/forfaits/saveCart', 'ForfaitsItemController@saveCart');
    Route::post('/api/forfaits/checkout', 'ForfaitsItemController@checkout');
    Route::post('/api/forfaits/forfaits', 'ForfaitsItemController@getForfaitUser');
    Route::post('/api/forfaits/insurances', 'ForfaitsItemController@getInsurances');
    Route::get('/api/forfaits/bookingData/{bID}/{uID}', 'ForfaitsItemController@bookingData');
    Route::get('/api/forfaits/getCurrentCart/{bID}/{uID}', 'ForfaitsItemController@getCurrentCart');
    Route::get('/api/forfaits/sedOrder/{bID}/{uID}/{type}', 'ForfaitsItemController@sendClientEmail');
    Route::post('/api/forfaits/sendOrder', 'ForfaitsItemController@sendOrdenToClient');
    Route::post('/api/forfaits/showOrder', 'ForfaitsItemController@showOrder');
    Route::post('/api/forfaits/sendConsult', 'ForfaitsItemController@sendEmail');
    Route::get('/api/forfaits/getSeasons', 'ForfaitsItemController@getForfaitSeasons');
    Route::post('/api/forfaits/createNewOrder', 'ForfaitsItemController@createNewOrder');
    Route::post('/api/forfaits/sendClassToAdmin', 'ForfaitsItemController@sendClassToAdmin');
    Route::post('/api/forfaits/getFFOrders', 'ForfaitsItemController@getFFOrders');
    Route::post('/api/forfaits/remove-order', 'ForfaitsItemController@removeOrder');
    Route::post('/api/forfaits/orders-history', 'ForfaitsItemController@ordersHistory');
    Route::post('/api/forfaits/get-payment', 'ForfaitsItemController@getPayment');
//    Route::get('/aaaa', 'ForfaitsItemController@aaaa');
    
    
    /**
 * FORFAITS
 */
Route::group(['middleware' => ['auth','role:admin|subadmin'], 'prefix' => 'admin/forfaits',], function () {
  Route::get('/orders', 'ForfaitsItemController@listOrders');
  Route::get('/balance', 'ForfaitsItemController@getBalance');
  Route::post('/open', 'ForfaitsItemController@getOpenData');
  Route::get('/edit/{id}', 'ForfaitsItemController@edit');
  Route::post('/upd', 'ForfaitsItemController@update');
  Route::get('/createItems', 'ForfaitsItemController@createItems');
  Route::get('/getBookItems/{bookingID}', 'ForfaitsItemController@getBookingFF');
  Route::post('/loadComment', 'ForfaitsItemController@loadComment');
  Route::post('/sendBooking', 'ForfaitsItemController@sendBooking');
  Route::get('/resume/{id}', 'ForfaitsItemController@getResume');
  Route::get('/resume-by-book/{id}', 'ForfaitsItemController@getResumeBy_book');
  Route::get('/resent_thansYouPayment/{id}', 'ForfaitsItemController@resent_thansYouPayment');
  Route::get('/getBookData/{id}', 'ForfaitsItemController@getBookData');
  Route::get('/changeBook/{id}/{ffID}', 'ForfaitsItemController@changeBook');
  Route::get('/sendFFExpress/{order_id}/{ffID}', 'ForfaitsItemController@sendFFExpress');
  Route::get('/{class?}', 'ForfaitsItemController@index');
});



   
    
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
  Route::get('admin/excursiones', 'ExtrasController@index');
  Route::get('admin/excursiones/{year?}','ExtrasController@index');
  Route::post('admin/excursionesLst/','ExtrasController@get_list');
  
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
  Route::get('/admin/multiple-room-lock', 'BookController@multipleRoomLock_print');
  Route::post('/admin/multiple-room-lock', 'BookController@multipleRoomLock_send');
  Route::post('/admin/multiple-room-lock-task', 'BookController@multipleRoomLock_tasks');
  Route::get('/admin/get-CustomersRequest', 'BookController@getCustomersRequestLst');
  Route::post('admin/hideCustomersRequest', 'BookController@hideCustomersRequest');
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
  
  
 //Propietario
  Route::get('/propietario/bloquear', 'OwnedController@bloqOwned');
  Route::get('/propietario/{name?}/operativa', 'OwnedController@operativaOwned');
  Route::get('/propietario/{name?}/tarifas', 'OwnedController@tarifasOwned');
  Route::get('/propietario/{name?}/descuentos', 'OwnedController@descuentosOwned');
  Route::get('/propietario/{name?}/fiscalidad', 'OwnedController@fiscalidadOwned');
  Route::get('/propietario/{name?}/facturas', 'OwnedController@facturasOwned');
  Route::get('/propietario/{name?}', 'OwnedController@index');
  Route::get('/propietario/create/password/{email}', 'UsersController@createPasswordUser');
  Route::post('/propietario/create/password/{email}', 'UsersController@createPasswordUser');
  
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

  
  Route::get('/paymentspro/delete/{id}', 'RouterActionsController@paymentspro_del');

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
  Route::get('/stripe-connect/{id}/acceptStripeConnect', 'StripeConnectController@acceptStripe');
  Route::get('/stripe-connect', 'StripeConnectController@index');
  Route::post('/stripe-connect/create-account-stripe-connect', 'StripeConnectController@createAccountStripeConnect');
  Route::post('/stripe-connect/load-transfer-form', 'StripeConnectController@loadTransferForm');
  Route::get('/stripe-connect/load-table-owneds', 'StripeConnectController@loadTableOwneds');
  Route::post('/stripe-connect/send-transfers', 'StripeConnectController@sendTransfers');
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
//  Route::get('settings/deleteExtra/{id}','SettingsController@delExtraPrices')->name('settings.extr_price.del');
  Route::get('settings/updateWeiland','SettingsController@updateWeiland')->name('settings.weiland.upd');
  
  
  
  //PAYMENTS
//  Route::post('/', 'SettingsController@createUpdateSetting')->name('settings.createUpdate');
  Route::get('/links-payland', 'PaylandsController@link');
  Route::get('/links-payland-single', 'PaylandsController@linkSingle');
});

Route::group(['middleware' => ['auth','role:admin|subadmin|recepcionista']], function () {
  Route::post('admin/excursions/edit', 'ExcursionsControllers@save')->name('excursions.edit');
  Route::post('admin/excursions/delete', 'ExcursionsControllers@delete')->name('excursions.delete');
  Route::get('admin/excursions/gallery/{id?}', 'ExcursionsControllers@gallery')->name('excursions.gallery');
  Route::post('admin/excursions/deletePhoto/{id?}', 'ExcursionsControllers@deletePhoto')->name('excursions.deleteImage');
  Route::post('admin/excursions/uploadFile', 'ExcursionsControllers@uploadImages')->name('excursions.uploadImages');
  Route::post('admin/excursions/photo_orden', 'ExcursionsControllers@updOrderImages')->name('excursions.updOrderImages');
  Route::get('admin/excursions/{id?}', 'ExcursionsControllers@index')->name('excursions');
  
    
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



Route::group(['middleware' => 'authAdmin'], function () {
  
  Route::get('/partee-checkStatus/{id}', 'AppController@parteeCheckStatus');
  Route::get('/partee-getPDF/{id}', 'AppController@parteeGetPDF');
  Route::get('admin/re_saveImg', 'ContentsControllers@re_saveImg');
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
//  Route::post('/ajax/forfaits/deleteRequestPopup', 'FortfaitsController@deleteRequestPopup');
  Route::post('/ajax/booking/getBookingAgencyDetails', 'LiquidacionController@getBookingAgencyDetails');
  Route::get('/ajax/booking/getBookingAgencyDetails', 'LiquidacionController@getBookingAgencyDetails');
  /* ICalendar links */
  Route::post('/ical/import/saveUrl', 'ICalendarController@saveUrl');
  Route::get('/ical/urls/deleteUrl', 'ICalendarController@deleteUrl');
  Route::get('/ical/getLasts', 'ICalendarController@getLasts');
  Route::get('/ical/urls/getAllUrl/{aptoID}', 'ICalendarController@getAllUrl');
 
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
//  Route::get('/list/{apto}','ZodomusController@listBy_apto')->name('channel.cal.list');
  Route::get('/channel-manager/config', 'OtasController@generate_config');
//  Route::get('/disponibilidad/{apto}/{start}/{end}','ZodomusController@listDisponibilidad');
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
//  Route::post('/Wubook/sendMinStay', 'WubookController@sendMinStayGroup')->name('Wubook.sendMinStay');  
//  Route::get('/ZODOMUS', 'ZodomusController@zodomusTest');
//  Route::get('/index', 'ZodomusController@index')->name('channel.index');
//  Route::get('/forceImport', 'ZodomusController@forceImport')->name('channel.forceImport');
//  Route::post('/send-avail/{apto}', 'ZodomusController@sendAvail')->name('channel.sendAvail');
//  Route::get('', 'ZodomusController@calendRoom')->name('channel');
});

Route::group(['middleware' => ['auth','role:admin|subadmin|recepcionista'], 'prefix' => 'admin',], function () {
  
  // Clientes
//  Route::get('/clientes/update', 'CustomersController@update');
//  Route::get('/clientes/save', 'CustomersController@save');
//  Route::post('/clientes/create', 'CustomersController@create');
//  
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
  
  
//    // ZODOMUS  
//  Route::get('/channel-manager/disponibilidad/{apto}/{start}/{end}','ZodomusController@listDisponibilidad');
//  Route::get('/channel-manager/price/{apto?}','ZodomusController@calendRoom')->name('channel.price.cal');
//  Route::get('/channel-manager/price-site/{site?}/{month?}/{year?}','ZodomusController@calendSite')->name('channel.price.site');
//  Route::post('/channel-manager/upd-price-site/','ZodomusController@calendSiteUpd')->name('channel.price.site.upd');
//  Route::post('/channel-manager/price/{apto?}','ZodomusController@calendRoomUPD')->name('channel.price.cal.upd');
//  Route::get('/channel-manager/price/precios/list/{apto}','ZodomusController@listBy_room')->name('channel.price.cal.list');
//  Route::get('/channel-manager/list/{apto}','ZodomusController@listBy_apto')->name('channel.cal.list');
//  Route::get('/channel-manager/config', 'ZodomusController@generate_config');
//  Route::get('/channel-manager/ZODOMUS', 'ZodomusController@zodomusTest');
//  Route::get('/channel-manager/index', 'ZodomusController@index')->name('channel.index');
//  Route::get('/channel-manager/forceImport', 'ZodomusController@forceImport')->name('channel.forceImport');
//  Route::get('/channel-manager/promociones', 'PricesController@promotions')->name('channel.promotions');
//  Route::post('/channel-manager/send-avail/{apto}', 'ZodomusController@sendAvail')->name('channel.sendAvail');
//  Route::get('/channel-manager', 'ZodomusController@calendRoom')->name('channel');
//  
  
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
  

  
  // Pagos-Propietarios
  Route::get('/pagos-propietarios', 'PaymentsProController@index');
  Route::post('/pagos-propietarios/create', 'PaymentsProController@create');
  Route::get('/pagos-propietarios/update/{id}/{month?}', 'PaymentsProController@update');
  Route::get('/paymentspro/getBooksByRoom/{idRoom}', 'PaymentsProController@getBooksByRoom');
  Route::get('/paymentspro/getLiquidationByRoom', 'PaymentsProController@getLiquidationByRoom');
  Route::get('/paymentspro/getLiquidationByMonth', 'PaymentsProController@getLiquidationByMonth');
  Route::get('/pagos-propietarios/get/historic_production/{room_id}', 'PaymentsProController@getHistoricProduction');
  Route::post('/pagos-propietarios', 'PaymentsProController@indexByDate');
  
  Route::get('/ical/importFromUrl', 'AppController@importFromUrl');
  Route::get('/zodomus/import', 'ZodomusController@importBookings');
  Route::get('/channel-manager-test', 'ZodomusController@channel_manager_test');
});
?>
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
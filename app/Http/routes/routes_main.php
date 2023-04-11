<?php
    Route::auth();
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@index');
    Route::get('/404', 'AppController@get_404');
    Route::get('/no-allowed','AppController@no_allowed');
    Route::get('403','AppController@no_allowed');
    Route::post('static-token','AppController@staticToken');
    Route::get('/partee-checkHuespedes','AppController@partee_checkHuespedes');
    Route::post('wubook-Webhook', 'OtasController@webHook_Wubook');
    Route::post('Ota-Gateway-Webhook/{siteID}', 'OtasController@webHook');
  
    Route::get('/thanks-you', 'HomeController@thanksYou')->name('thanks-you');
    Route::get('/thanks-you-forfait','AppController@thanksYou_forfait')->name('thanks-you-forfait');
    Route::get('/paymeny-error', 'HomeController@paymenyError')->name('paymeny-error');
    Route::get('/form-demo', 'BookController@demoFormIntegration');
    Route::post('/api/check_rooms_avaliables', 'BookController@apiCheckBook')->name('api.proccess');
    
  
    //   /* Correos Frontend */getCalendarMobile
    Route::get('/buzon', 'HomeController@buzon');
    Route::get('/terminos-condiciones', 'HomeController@terminos');
    Route::get('/politica-cookies', 'HomeController@politicaCookies');
    Route::get('/politica-privacidad', 'HomeController@politicaPrivacidad');
    Route::get('/condiciones-contratacion', 'HomeController@condicionesContratacion')->name('cond.contratacion');
    Route::get('/condiciones-fianza', 'HomeController@condicionesFianza')->name('cond.fianza');
    Route::get('/payments-forms/{token}', 'PaylandsController@paymentsForms')->name('front.payments');
    Route::post('/payments-save-dni/{token}', 'PaylandsController@saveDni')->name('front.payments.dni')->middleware('cors');
    Route::post('/payments-save-supplement/{token}', 'PaylandsController@addExtrs')->name('front.payments.addExtrs')->middleware('cors');

    /* CRONTABS */
    Route::get('/admin/reservas/api/checkSecondPay', 'BookController@checkSecondPay');
    // AJAX REQUESTS
    Route::post('/ajax/requestPrice', 'FortfaitsController@calculatePrice');
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
    
  Route::get('/factura/{id}/{num}/{emial}', 'InvoicesController@donwload_external');
  Route::post('/payments-moto', 'PaylandsController@processPaymentMoto');
  Route::get('/clear-cookies','RouterActionsController@clearCookies');
?>
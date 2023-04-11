<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'reservas/stripe/payment',
        '/solicitudForfait',
        '/admin/reservas/stripe/paymentsBooking',
        '/admin/stripe-connect/load-transfer-form',
        '/admin/stripe-connect/create-account-stripe-connect',
        '/admin/gastos/create',
        '/ajax/requestPrice',
        '/ajax/forfaits/updateRequestStatus',
        '/ajax/forfaits/updateRequestPAN',
        '/ajax/forfaits/updateRequestComments',
        '/ajax/forfaits/updateCommissions',
        '/ajax/forfaits/updatePayments',
        '/ajax/forfaits/requestPriceForfaits',
        '/ajax/reservas/getBookData',
        '/ajax/checkRecaptcha',
        '/ajax/booking/getBookingAgencyDetails',
        '/ajax/booking/getBookingAgencyDetails',
        '/admin/years/change',
        '/admin/years/change/months',
        '/admin/settings/createUpdate',
        '/api/check_rooms_avaliables',
        '/api/forfaits/*',
        '/payment/process/*',
        '/ajax/send-partee-finish',
        '/admin/ingresos/upd',
        '/payments-save-dni/*',
        '/static-token',
        '/zodomus-Webhook',
        '/wubook-Webhook',
        '/Ota-Gateway-Webhook/*',
        '/admin/clientes/save',
        '/api/*',
        '/ajax/*',
        '/payments-moto'
    ];
}

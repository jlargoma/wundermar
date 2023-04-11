<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            
        ],

        'api' => [
            'throttle:60,1',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'page-cache' => \App\Http\Middleware\CacheResponse::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'can' => \Illuminate\Foundation\Http\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'authAdmin' => \App\Http\Middleware\RoleMiddleware::class,
        'authPropietario' => \App\Http\Middleware\RolePropietario::class,
        'authSubAdmin' => \App\Http\Middleware\RoleSubAdmin::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'cors' => \App\Http\Middleware\Cors::class,
        'apiControl' => \App\Http\Middleware\ApiControl::class,
        \Cuatao\LaravelHtmlCaching\Http\Middleware\ResponseHtmlCachingBeforeMiddleware::class,
        \Cuatao\LaravelHtmlCaching\Http\Middleware\ResponseHtmlCachingAfterMiddleware::class,
    ];
}

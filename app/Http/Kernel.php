<?php

    namespace App\Http;

    use Illuminate\Foundation\Http\Kernel as HttpKernel;

    class Kernel extends HttpKernel
    {

        protected $middleware = [
            \App\Http\Middleware\TrustProxies::class,
            \App\Http\Middleware\CheckForMaintenanceMode::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ];

        protected $middlewareGroups = [
            'web'         => [
                \App\Http\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                // \Illuminate\Session\Middleware\AuthenticateSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \App\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ],
            'api'         => [
                'throttle:60,1',
                'bindings',
            ],
            'load_entity' => [
                \App\Http\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \App\Http\Middleware\AjaxVariables::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \App\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ],
        ];

        protected $routeMiddleware = [
            'auth'                 => \App\Http\Middleware\Authenticate::class,
            'auth.basic'           => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'bindings'             => \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'cache.headers'        => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'                  => \Illuminate\Auth\Middleware\Authorize::class,
            'guest'                => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'signed'               => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle'             => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'             => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'role'                 => \Spatie\Permission\Middlewares\RoleMiddleware::class,
            'permission'           => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
            'localize'             => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            //            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'       => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            'responseRender'       => \App\Http\Middleware\ResponseRender::class,
            'ajaxVariables'        => \App\Http\Middleware\AjaxVariables::class,
            'auth.verify_email'    => \App\Http\Middleware\AuthenticateVerifyEmail::class,
            'redirects'            => \App\Http\Middleware\Redirect::class,
        ];

        protected $middlewarePriority = [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\Authenticate::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ];

    }

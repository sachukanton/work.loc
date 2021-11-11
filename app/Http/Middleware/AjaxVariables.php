<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Support\Facades\App;

    class AjaxVariables
    {

        public function handle($request, Closure $next, $guard = NULL)
        {
            $_language = $request->header('LOCALE', DEFAULT_LOCALE);
            $_device = $request->header('device', 'pc');
            wrap()->set('locale', $_language);
            wrap()->set('device.type', $_device);
            App::setLocale($_language);

            return $next($request);
        }

    }

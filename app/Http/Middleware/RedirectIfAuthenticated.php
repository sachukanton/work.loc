<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Support\Facades\Auth;

    class RedirectIfAuthenticated
    {

        public function handle($request, Closure $next, $guard = NULL)
        {
            if (Auth::guard($guard)->check()) {
                return redirect('/');
            }

            return $next($request);
        }

    }

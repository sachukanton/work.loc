<?php

    namespace App\Http\Middleware;

    use Illuminate\Auth\Middleware\Authenticate as Middleware;

    class AuthenticateVerifyEmail extends Middleware
    {

        protected function redirectTo($request)
        {
            $request->session()
                ->flash('message', trans('forms.messages.verify_email.authenticate'));
            if (!$request->expectsJson()) {
                return route('front');
            }
        }

    }

<?php

namespace App\Exceptions;

use App\Models\Shop\Category;
use App\Models\Structure\Page;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Handler extends ExceptionHandler
{

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        global $wrap;
        if(!is_null($wrap) && is_array($wrap) && count($wrap)){
        }else{
            wrap()->_load($request);
        }
        wrap()->set('page.scripts', config('os_frontend.scripts'), true);
        wrap()->set('page.styles', config('os_frontend.styles'), true);
        $e = $this->prepareException($exception);
        $_status_code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        if ($e instanceof HttpResponseException) {
            $_status_code = $e->getStatusCode();
        } elseif ($e instanceof AuthenticationException) {
            $_status_code = 401;
        } elseif ($e instanceof UnauthorizedException) {
            $_status_code = 403;
        }
        $_item = new Page();
        $_item->setWrap([
            'seo.robots' => 'noindex, nofollow',
            'seo.title'  => trans("pages.titles.error_page_{$_status_code}"),
        ]);
        $_wrap = wrap()->render();
        View::share([
            '_wrap'           => $_wrap,
            '_authUser'       => Auth::user(),
            '_locale'         => $_wrap['locale'],
            '_accessEdit'     => NULL,
            '_alert'          => Session::get('alert'),
            '_searchCategory' => Category::getSearchCategory(),
        ]);

        return parent::render($request, $exception);
    }

}

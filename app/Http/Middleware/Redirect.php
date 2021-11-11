<?php

namespace App\Http\Middleware;

use App\Models\Seo\Redirect as Redirect_;
use Closure;

class Redirect
{

    public function handle($request, Closure $next, $guard = NULL)
    {
        $_redirectPath = NULL;
        $_redirectStatus = 301;
        $_fullPath = $_SERVER['REQUEST_URI'];
        $_base_url = config('app.url');
        $_base_url_ar = [
            $_base_url,
            str_replace('https://', 'http://', $_base_url)
        ];
        $_product_id = $_GET['product_id'] ?? NULL;
        //            $_fullPath = urldecode(trim(str_replace($_base_url_ar, '', $_fullPath), '/'));
        $_fullPath = trim(str_replace($_base_url_ar, '', $_fullPath), '/');
        $_url_redirect_exists = Redirect_::whereIn('redirect', [
            $_fullPath,
            "index.php/{$_fullPath}"
        ])
            ->when($_product_id, function ($query) use ($_product_id) {
                $query->orWhere('redirect', 'like', "%product_id={$_product_id}");
            })
            ->remember(REMEMBER_LIFETIME)
            ->first();
        if ($_url_redirect_exists) {
            $_redirectPath = $_url_redirect_exists->_alias->exists ? $_url_redirect_exists->_alias->alias : ($_url_redirect_exists->link ? : '/');
            $_redirectStatus = $_url_redirect_exists->status;
        }
        if (is_null($_redirectPath) && ($_currentPage = current_page($_fullPath)) && $_currentPage == 1) {
            $_redirectPath = '/' . trim(preg_replace('/page-[0-9]+/i', '', $_fullPath), '/');
        }
        if ($_redirectPath) {
            return redirect()
                ->to($_redirectPath, $_redirectStatus);
        }

        return $next($request);
    }

}

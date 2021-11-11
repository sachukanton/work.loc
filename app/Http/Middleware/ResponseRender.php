<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ResponseRender
{
    public function handle(Request $request, Closure $next, $guard = NULL)
    {
        global $wrap;
        $_response = $next($request);
        if (isset($wrap['page']) && !$wrap['page']['is_dashboard'] && $_response instanceof Response && ($_content = $_response->getOriginalContent()) instanceof View && $request->isMethod('GET')) {
            $_content = clear_html($_response->content());
            $_response->setVary('User-Agent');
            if ($wrap['use']['compress']) {
                $_response->setContent($_content);
//                $_response->header('Content-Length', mb_strlen($_content));
            }
            if ($wrap['use']['last_modified'] && ($_last_modified = $wrap['seo']['last_modified'])) {
                $_eTag = md5($_content);
                $_requestETag = str_replace('"', '', $request->getETags());
                $_response->setExpires(Carbon::now()->addMinute(15));
                $_response->setCache([
                    'etag'          => $_eTag,
                    'last_modified' => $_last_modified,
                    'max_age'       => 3600,
                ]);
                $_if_modified_since = FALSE;
                if (isset($_ENV['HTTP_IF_MODIFIED_SINCE'])) $_if_modified_since = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
                if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) $_if_modified_since = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
                if (($_if_modified_since && $_if_modified_since >= strtotime($_last_modified)) || (isset($_requestETag[0]) && ends_with($_requestETag[0], $_eTag))) {
                    $_response->setNotModified();
                }
            }

        }

        return $_response;
    }
}

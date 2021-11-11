<?php

namespace App\Http\Controllers;

use App\Models\Seo\UrlAlias;
use App\Models\Shop\FilterPage;
use App\Models\Shop\ViewList;
use App\Models\Shop\Product;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewClass;
use League\Flysystem\Exception;

class QueryPathController extends Controller
{

    use Authorizable;

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request, $path = NULL)
    {
//        $_basket = app('basket');
//        $_basket->addCertificateProduct(1016);
//        $_basket->bClear();
//        dd($_basket->amount);

        $_item = NULL;
        $_others = NULL;
        $_response = NULL;
        $_alias = format_alias($request);
        $_auth_user = Auth::user();
        if ($_alias && ($_current_alias = UrlAlias::where('alias', $_alias)
                ->with([
                    'model'
                ])
                ->remember(REMEMBER_LIFETIME)
                ->first())
        ) {
            if ($_current_alias->model) {
                $_model_class_basename = strtolower(class_basename($_current_alias->model->getMorphClass()));
                wrap()->set('model_class_basename', strtolower($_model_class_basename));
                if ($request->isMethod('GET')) {
                    $_item = $_current_alias->model->_render();
                    if ($_item->status) $_response = View::first($_item->template, compact('_item'));
                } elseif ($request->ajax()) {
                    $_response = $_current_alias->model->_render_ajax($request);
                }
            }
        } elseif (($_alias == '/' || $_alias == '') && ($_item = page_render('front'))) {
            wrap()->set('page.is_front', TRUE);
            if ($_item->status) $_response = View::first($_item->template, compact('_item'));
            if ($_item->status) {
                $_response = View::first($_item->template, compact('_item'));
            }
            $_others['recommended_front'] = Product::shop_product_view_list_recommended_front();
            $_others['new'] = Product::shop_product_view_list_new();
        }
        else {
            if ($_item = FilterPage::getCategory($request, $_alias)) {
                if (is_string($_item)) {
                    return redirect()
                        ->to($_item);
                } elseif ($request->isMethod('GET')) {
                    if ($_item->status || ($_auth_user && $_auth_user->hasAllPermissions(['pages_update']))) {
                        $_op = wrap()->get('seo.open_graph');
                        $_op['url'] = $request->fullUrl();
                        wrap()->set('seo.open_graph', $_op, TRUE);
                        $_response = View::first($_item->template, compact('_item'));
                    }
                } elseif ($request->ajax()) {
                    $_response = $_item;
                }
            }
        }
        $_wrap = wrap()->render();
        if ($_response) {
            if (is_a($_response, ViewClass::class)) {
                $_response->with(compact('_others', '_wrap'));
            } elseif ($request->ajax()) {
                $_response = response($_response, 200);
            }

            return $_response;
        }
        abort(404);
    }

}

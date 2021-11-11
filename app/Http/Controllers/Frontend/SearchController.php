<?php

    namespace App\Http\Controllers\Frontend;

    use App\Library\BaseController;
    use App\Models\Seo\SearchIndex;
    use App\Models\Shop\Product;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\View;
    use Illuminate\View\View as ViewClass;

    class SearchController extends BaseController
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function index(Request $request)
        {
            $_response = NULL;
//            try {
                $_query_string = $request->input('query', NULL);
                $_category = $request->input('category', 'all');
                $_search_model = new SearchIndex(new Product(), 'sku');
                if ($request->isMethod('GET')) {
                    $_item = $_search_model->_render($_query_string, $_category);
                    $_response = View::first($_item->template, compact('_item'));
                } elseif ($request->ajax()) {
                    $_response = $_search_model->_render_ajax($request);
                }
                $_wrap = wrap()->render();
                if ($_response) {
                    if (is_a($_response, ViewClass::class)) {
                        $_response->with(compact('_wrap'));
                    } elseif ($request->ajax()) {
                        $_response = response($_response, 200);
                    }

                    return $_response;
                }
                abort(404);
//            } catch (\Exception $exception) {
//            }

            return $_response;
        }

    }

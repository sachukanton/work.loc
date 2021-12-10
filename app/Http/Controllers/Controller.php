<?php
    namespace App\Http\Controllers;

    use App\Models\Shop\Category;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Foundation\Bus\DispatchesJobs;
    use Illuminate\Foundation\Validation\ValidatesRequests;
    use Illuminate\Http\Request;
    use Illuminate\Routing\Controller as BaseController;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\View;

    class Controller extends BaseController
    {
        use AuthorizesRequests,
            DispatchesJobs,
            ValidatesRequests;
        public $device = 'pc';
        public $deviceTemplate = 'default';

        public function __construct()
        {
           $this->middleware(function (Request $request, $next) {
                $_wrap = wrap()->_load($request);
                $_dashboard = $_wrap['page']['is_dashboard'];
                $this->device = $_wrap['device']['type'];
                $this->deviceTemplate = $_wrap['device']['template'];
                if (!$_dashboard) {
                    wrap()->set('page.scripts', config('os_frontend.scripts'));
                    wrap()->set('page.styles', config('os_frontend.styles'));
                }
                if ($_wrap['user']) {
                    $_access_edit = [
                        'page'           => $_wrap['user']->hasAllPermissions('pages_update'),
                        'node'           => $_wrap['user']->hasAllPermissions('nodes_update'),
                        'tag'            => $_wrap['user']->hasAllPermissions('tags_update'),
                        'faq'            => $_wrap['user']->hasAllPermissions('faqs_update'),
                        'block'          => $_wrap['user']->hasAllPermissions('blocks_update'),
                        'banner'         => $_wrap['user']->hasAllPermissions('banners_update'),
                        'brand'          => $_wrap['user']->hasAllPermissions('shop_brands_update'),
                        'advantage'      => $_wrap['user']->hasAllPermissions('advantages_update'),
                        'comment'        => $_wrap['user']->hasAllPermissions('comments_update'),
                        'menu'           => $_wrap['user']->hasAllPermissions('menus_update'),
                        'slider'         => $_wrap['user']->hasAllPermissions('sliders_update'),
                        'shop_brand'     => $_wrap['user']->hasAllPermissions('shop_brands_update'),
                        'shop_category'  => $_wrap['user']->hasAllPermissions('shop_categories_update'),
                        'shop_product'   => $_wrap['user']->hasAllPermissions('shop_products_update'),
                        'form'           => $_wrap['user']->hasAllPermissions('forms_update'),
                        'pharm_city'     => $_wrap['user']->hasAllPermissions('pharm_cities_update'),
                        'pharm_pharmacy' => $_wrap['user']->hasAllPermissions('pharm_pharmacies_update'),
                    ];
                }
                View::share([
                    '_authUser'       => $_wrap['user'],
                    '_locale'         => $_wrap['locale'],
                    '_accessEdit'     => $_access_edit ?? NULL,
                    '_alert'          => Session::get('alert'),
                    '_searchCategory' => Category::getSearchCategory(),
                ]);

                return $next($request);
            });
        }

    }
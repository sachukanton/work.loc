<?php

namespace App\Providers;

use App\Models\Components\Advantage;
use App\Models\Components\Banner;
use App\Models\Components\Block;
use App\Models\Components\Comment;
use App\Models\Components\Menu;
use App\Models\Components\ModalBanner;
use App\Models\Components\Slider;
use App\Models\Components\Variable;
use App\Models\File\File;
use App\Models\Form\Forms;
use App\Models\Form\FormsData;
use App\Models\Pharm\PharmCity;
use App\Models\Pharm\PharmPharmacy;
use App\Models\Seo\Redirect;
use App\Models\Seo\UrlAlias;
use App\Models\Shop\AdditionalItem;
use App\Models\Shop\Brand;
use App\Models\Shop\Category;
use App\Models\Shop\FilterPage;
use App\Models\Shop\Form;
use App\Models\Shop\Gift;
use App\Models\Shop\Stock;
use App\Models\Shop\Order;
use App\Models\Shop\Param;
use App\Models\Shop\ParamItem;
use App\Models\Shop\Price;
use App\Models\Shop\Product;
use App\Models\Shop\ViewList;
use App\Models\Structure\Faq;
use App\Models\Structure\Node;
use App\Models\Structure\Page;
use App\Models\Structure\Tag;
use App\Models\User\Role;
use App\Models\User\User;
use App\Models\Varz\Service;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class RouteServiceProvider extends ServiceProvider
{

    protected $namespace = 'App\Http\Controllers';

    public function boot()
    {
        parent::boot();
        //            Route::domain('https://yummy-king.com.ua');
        Route::pattern('id', '[0-9]+');
        Route::pattern('key', '[0-9a-zA-Z]+');
        Route::pattern('action', '(add|edit|create|clear|save|update|delete|remove|destroy)');
        Route::pattern('page_number', 'page-[0-9]+');
        Route::model('user', User::class);
        Route::model('role', Role::class);
        Route::model('page', Page::class);
        Route::model('node', Node::class);
        Route::model('file', File::class);
        Route::model('faq', Faq::class);
        Route::model('variable', Variable::class);
        Route::model('block', Block::class);
        Route::model('banner', Banner::class);
        Route::model('tag', Tag::class);
        Route::model('advantage', Advantage::class);
        Route::model('slider', Slider::class);
        Route::model('menu', Menu::class);
        Route::model('shop_brand', Brand::class);
        Route::model('shop_param', Param::class);
        Route::model('shop_param_item', ParamItem::class);
        Route::model('shop_category', Category::class);
        Route::model('shop_filter_page', FilterPage::class);
        Route::model('shop_filter_page_2', FilterPage::class);
        Route::model('shop_product', Product::class);
        Route::model('shop_product_modify', Product::class);
        Route::model('shop_forms_datum', Form::class);
        Route::model('shop_product_list', ViewList::class);
        Route::model('shop_order', Order::class);
        Route::model('shop_price', Price::class);
        Route::model('shop_gift', Gift::class);
        Route::model('shop_stock', Stock::class);    
        Route::model('form', Forms::class);
        Route::model('forms_datum', FormsData::class);
        Route::model('url_alias', UrlAlias::class);
        Route::model('redirect', Redirect::class);
        Route::model('pharm_city', PharmCity::class);
        Route::model('pharm_pharmacy', PharmPharmacy::class);
        Route::model('modal_banner', ModalBanner::class);
        Route::model('additional_item', AdditionalItem::class);
       
    }

    public function map()
    {
        $this->mapLoadEntityRoutes();
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapCallbackRoutes();
        $this->mapDashboardRoutes();
    }

    protected function mapLoadEntityRoutes()
    {
        Route::prefix('load')
            ->middleware([
                'load_entity',
            ])
            ->namespace('App\Http\Controllers')
            //                ->domain('https://yummy-king.com.ua')
            ->group(base_path('routes/load.php'));
    }

    protected function mapWebRoutes()
    {
        Route::middleware([
            'redirects',
            'web',
            'localize',
            'localizationRedirect',
            //                'localeSessionRedirect',
            'localeViewPath',
            'responseRender'
        ])
            ->prefix(LaravelLocalization::setLocale())
            ->namespace($this->namespace)
            //                ->domain('https://yummy-king.com.ua')
            ->group(base_path('routes/web.php'));
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            //                ->domain('https://yummy-king.com.ua')
            ->group(base_path('routes/api.php'));
    }

    protected function mapDashboardRoutes()
    {
        Route::prefix('oleus')
            ->middleware([
                'web',
                'auth',
                'verified',
                'permission:access_dashboard'
            ])
            //                ->domain('https://yummy-king.com.ua')
            ->namespace('App\Http\Controllers\Dashboard')
            ->group(base_path('routes/dashboard.php'));
    }

    protected function mapCallbackRoutes()
    {
        Route::prefix('ajax')
            ->middleware([
                'web',
                'ajaxVariables'
            ])
            //                ->domain('https://yummy-king.com.ua')
            ->namespace('App\Http\Controllers')
            ->group(base_path('routes/callback.php'));
    }

}

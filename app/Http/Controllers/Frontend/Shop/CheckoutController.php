<?php

namespace App\Http\Controllers\Frontend\Shop;

use App\Library\BaseController;
use App\Models\Shop\Basket;
use App\Models\Shop\Gift;
use App\Models\Shop\Order;
use App\Models\Structure\Page;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CheckoutController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $_basket = app('basket');
        if ($_basket->composition->isEmpty()) {
            return redirect()
                ->to('/');
        }
        $_template = [
            "frontend.{$this->deviceTemplate}.pages.checkout",
            "frontend.default.pages.checkout",
            'backend.base.shop_checkout_page',
            'backend.base.page',
        ];
        $_item = new Page();
        $_item->setWrap([
            'seo.title'       => trans('shop.titles.checkout.title'),
            'seo.robots'      => 'noindex, nofollow',
            'page.title'      => trans('shop.titles.checkout.title'),
            'alias'           => 'checkout',
            'page.breadcrumb' => collect([
                [
                    'name'     => trans('pages.titles.home'),
                    'url'      => _u(LaravelLocalization::getLocalizedURL(wrap()->getLocale(), '/')),
                    'position' => 1
                ],
                [
                    'name'     => trans('shop.titles.checkout.breadcrumb'),
                    'url'      => NULL,
                    'position' => 2
                ]
            ]),
            'page.scripts'    => [
                [
                    'url'      => 'template/js/checkout_form.js',
                    'position' => 'footer',
                    'sort'     => 2000
                ],
                [
                    'url'      => 'template/js/checkout_part.js',
                    'position' => 'footer',
                    'sort'     => 2001
                ],
            ],
        ]);
        $_wrap = wrap()->render();
        $_item->checkoutProductsOutput = $_basket->show_checkout_products($_basket, TRUE);
        $_item->checkoutFormOutput = $_basket->show_checkout_form($_basket);
        //        if (request()->has('code')) {
        //            $_response['commands'][] = [
        //                'command' => 'eval',
        //                'options' => [
        //                    'data' => "if (typeof fbq == 'function'){var a = {}; a.locale = '{$_wrap['locale']}'; a.device = '{$_wrap['device']['type']}'; a.content_type = 'product'; a.content_name = 'Оформлення замовлення'; a.content_ids = '{$_basket->sku_list}'; a.value = " . ($_basket->amount ? $_basket->amount['original']['price'] : NULL) . "; a.currency = 'UAH'; a.num_items = {$_basket->quantity_in}; a.contents = {$_basket->product_list}; fbq('track', 'Purchase', a);}"
        //                ]
        //            ];
        //            dd($_response);
        //        }

        return View::first($_template, compact('_item', '_wrap', '_basket'));
    }

    public function thanks_page()
    {
        $_template = [
            "frontend.{$this->deviceTemplate}.pages.checkout_thanks",
            "frontend.default.pages.checkout_thanks",
            'backend.base.shop_checkout_page',
            'backend.base.page',
        ];
        $_item = new Page();
        $_item->setWrap([
            'seo.title'       => trans('shop.titles.checkout.thanks'),
            'seo.robots'      => 'noindex, nofollow',
            'page.title'      => trans('shop.titles.checkout.thanks'),
            'alias'           => 'checkout',
            'page.breadcrumb' => collect([
                [
                    'name'     => trans('pages.titles.home'),
                    'url'      => _u(LaravelLocalization::getLocalizedURL(wrap()->getLocale(), '/')),
                    'position' => 1
                ],
                [
                    'name'     => trans('shop.titles.checkout.thanks'),
                    'url'      => NULL,
                    'position' => 2
                ]
            ]),
        ]);
        $_item->order = NULL;
        $_wrap = wrap()->render();
        $_order = request()->session()->get('order');
        $_fb = request()->session()->get('fb');
        if ($_order) {
            $_item->order = Order::where('id', $_order)
                ->with([
                    '_products'
                ])
                ->first();
            $_item->fb = $_fb;
            //            session()->forget('order');
            //            session()->forget('fb');
        }

        return View::first($_template, compact('_item', '_wrap'));
    }

}

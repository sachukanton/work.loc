<?php

    namespace App\Http\Controllers\Auth;

    use App\Library\BaseController;
    use App\Models\Shop\Basket;
    use App\Models\Structure\Page;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\View;
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    class AccountController extends BaseController
    {

        use Notifiable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_personal',
                'verified'
            ]);
        }

        public function __call($name, $arguments)
        {
            $_template = [
                "frontend.{$this->deviceTemplate}.user.account.{$name}",
                "frontend.default.user.account.{$name}"
            ];
            $_request = request();
            $_item = new Page();
            $_user = Auth::user();
            $_basket = Basket::init();
            $_locale = wrap()->getLocale();
            $_ajax = $_request->isMethod('GET') ? FALSE : TRUE;
            $_commands = [];
            switch ($name) {
                case 'edit':
                    $_item->setWrap([
                        'seo.title'       => trans('frontend.titles.account.edit'),
                        'seo.robots'      => 'noindex, nofollow',
                        'page.title'      => trans('frontend.titles.account.title'),
                        'alias'           => 'account/edit',
                        'page.breadcrumb' => collect([
                            [
                                'name'     => trans('frontend.titles.home'),
                                'url'      => _u(LaravelLocalization::getLocalizedURL($_locale, '/')),
                                'position' => 1
                            ],
                            [
                                'name'     => trans('frontend.titles.account.edit'),
                                'url'      => NULL,
                                'position' => 2
                            ]
                        ]),
                    ]);
                    break;
                case 'wish_list':
                    $_item->setWrap([
                        'seo.title'       => trans('frontend.titles.account.wish_list'),
                        'seo.robots'      => 'noindex, nofollow',
                        'page.title'      => trans('frontend.titles.account.title'),
                        'alias'           => 'account/wish-list',
                        'page.breadcrumb' => collect([
                            [
                                'name'     => trans('frontend.titles.home'),
                                'url'      => _u(LaravelLocalization::getLocalizedURL($_locale, '/')),
                                'position' => 1
                            ],
                            [
                                'name'     => trans('frontend.titles.account.wish_list'),
                                'url'      => NULL,
                                'position' => 2
                            ]
                        ]),
                    ]);
                    $_item->_items = $_user->_wish_lists;
                    if ($_item->_items->isNotEmpty()) {
                        $_item->_items->transform(function ($_list) {
                            if ($_list->_products->isNotEmpty()) {
                                $_list->_products->transform(function ($_product) {
                                    $_product->price = $_product->_render_price();

                                    return $_product;
                                });
                                $_list->quantity_in = $_list->_products->count();
                                $_list->amount = $_list->_products->sum(function ($_product) {
                                    return $_product->global_price->base_price ?? 0;
                                });
                                $_list->amount = view_price($_list->amount, $_list->amount);
                            } else {
                                $_list->quantity_in = 0;
                                $_list->amount = view_price(0, 0);
                            }

                            return $_list;
                        });
                    }
                    break;
                case 'reviews':
                    $_page_number = current_page();
                    if (!$_ajax) {
                        $_item->setWrap([
                            'seo.title'       => trans('frontend.titles.account.reviews') . ($_page_number ? trans('frontend.title_suffix_page', ['page' => $_page_number]) : NULL),
                            'seo.robots'      => 'noindex, nofollow',
                            'seo.url_alias'   => 'account/reviews',
                            'page.title'      => trans('frontend.titles.account.title'),
                            'alias'           => 'account/reviews',
                            'page.breadcrumb' => collect([
                                [
                                    'name'     => trans('frontend.titles.home'),
                                    'url'      => _u(LaravelLocalization::getLocalizedURL($_locale, '/')),
                                    'position' => 1
                                ],
                                [
                                    'name'     => trans('frontend.titles.account.reviews') . ($_page_number ? trans('frontend.title_suffix_page', ['page' => $_page_number]) : NULL),
                                    'url'      => NULL,
                                    'position' => 2
                                ]
                            ]),
                        ]);
                        $_item->_items = $_user->_comment_items($_page_number);
                    } else {
                        $_load_more = $_request->has('load_more') ? TRUE : FALSE;
                        wrap()->set('seo.url_alias', 'account/reviews', TRUE);
                        if ($_load_more) {
                            $_items = $_user->_comment_items($_page_number);
                            $_items_output = NULL;
                            $_item_template = [
                                "frontend.{$this->deviceTemplate}.user.account.review_item",
                                "frontend.default.user.account.review_item"
                            ];
                            foreach ($_items as $_comment) $_items_output .= clear_html(View::first($_item_template, compact('_comment')));
                            $_commands['commands'][] = [
                                'command' => 'append',
                                'options' => [
                                    'target' => '#uk-items-list',
                                    'data'   => clear_html($_items_output)
                                ]
                            ];
                            $_commands['commands'][] = [
                                'command' => 'html',
                                'options' => [
                                    'target' => '#uk-items-list-pagination',
                                    'data'   => clear_html($_items->links('frontend.default.partials.pagination'))
                                ]
                            ];
                        }
                    }
                    break;
                default:
                    $_page_number = current_page();
                    if (!$_ajax) {
                        $_item->setWrap([
                            'seo.robots'      => 'noindex, nofollow',
                            'seo.url_alias'   => 'account',
                            'seo.title'       => trans('pages.titles.account.title'),
                            'page.title'      => trans('pages.titles.account.title'),
                            'alias'           => 'account',
                            'page.breadcrumb' => collect([
                                [
                                    'name'     => trans('pages.titles.home'),
                                    'url'      => _u(LaravelLocalization::getLocalizedURL($_locale, '/')),
                                    'position' => 1
                                ],
                                [
                                    'name'     => trans('pages.titles.account.orders') . ($_page_number ? trans('frontend.title_suffix_page', ['page' => $_page_number]) : NULL),
                                    'url'      => NULL,
                                    'position' => 2
                                ]
                            ]),
                        ]);
                        //                        $_item->checkoutProductsOutput = $_basket->show_checkout_products($_basket);
                        //                        $_item->_items = $_user->_order_items($_page_number);
                        $_item->checkoutProductsOutput = NULL;
                        $_item->_items = collect([]);
                    } else {
                        $_load_more = $_request->has('load_more') ? TRUE : FALSE;
                        wrap()->set('seo.url_alias', 'account', TRUE);
                        if ($_load_more) {
                            $_items = $_user->_order_items($_page_number);
                            $_items_output = NULL;
                            $_item_template = [
                                "frontend.{$this->deviceTemplate}.user.account.order_item",
                                "frontend.default.user.account.order_item"
                            ];
                            foreach ($_items as $_order) $_items_output .= clear_html(View::first($_item_template, compact('_order')));
                            $_commands['commands'][] = [
                                'command' => 'append',
                                'options' => [
                                    'target' => '#uk-items-list',
                                    'data'   => clear_html($_items_output)
                                ]
                            ];
                            $_commands['commands'][] = [
                                'command' => 'html',
                                'options' => [
                                    'target' => '#uk-items-list-pagination',
                                    'data'   => clear_html($_items->links('frontend.default.partials.pagination'))
                                ]
                            ];
                        }
                    }
                    break;
            }
            $_wrap = wrap()->render();

            return $_ajax ? response($_commands, 200) : View::first($_template, compact('_item', '_wrap', '_basket'));
        }

    }

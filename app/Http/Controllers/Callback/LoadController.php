<?php

namespace App\Http\Controllers\Callback;

use App\Library\BaseController;
use App\Models\Shop\Basket;
use App\Models\Shop\Compare;
use App\Models\Shop\LastViewed;
use App\Models\Shop\Product;
use App\Models\Shop\ViewList;
use App\Models\Structure\Faq;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\View;

class LoadController extends BaseController
{

    use Authorizable;
    use Notifiable;

    public $localeView;

    public function __construct()
    {
        parent::__construct();
    }

    public function load(Request $request)
    {
        $_response = NULL;
        try {
            $_entity = $request->get('entity');
            $this->localeView = wrap()->get('locale');
            if ($_entity) {
                $_response = call_user_func([
                    self::class,
                    $_entity
                ], $request->get('options', []));
            }
        } catch (\Exception $exception) {
        }

        return response($_response, 200);
    }

    public function page_last_nodes($arg)
    {
        $_response = NULL;
        if (isset($arg['id']) && ($_page_id = $arg['id'])) {
            $_page_load = page_load($_page_id);
            $_count_items = $arg['count_items'] ?? 10;
            if ($_page_load->status) {
                $_response = [
                    'object' => View::first([
                        "frontend.{$this->deviceTemplate}.load_entities.page_last_nodes_{$_page_id}",
                        "frontend.default.load_entities.page_last_nodes_{$_page_id}",
                        "frontend.{$this->deviceTemplate}.load_entities.page_last_nodes",
                        'frontend.default.load_entities.page_last_nodes'
                    ], [
                        '_page'   => $_page_load,
                        '_items'  => $_page_load->_last_nodes($_count_items),
                        '_title'  => trans("frontend.block.page_{$_page_id}_last_nodes"),
                        '_more'   => trans("frontend.block.page_{$_page_id}_more_nodes"),
                        '_locale' => $this->localeView
                    ])->render(function ($view, $_content) {
                        return clear_html($_content);
                    })
                ];
            }
        }

        return $_response;
    }

    public function tag_last_nodes(...$arg)
    {
        $_response = NULL;
        if (isset($arg[0]['id']) && ($_page_id = $arg[0]['id'])) {
            $_tag_load = tag_load($_page_id);
            if ($_tag_load->status) {
                $_response = [
                    'object' => View::first([
                        "frontend.{$this->deviceTemplate}.load_entities.tag_last_nodes",
                        "frontend.{$this->deviceTemplate}.load_entities.page_last_nodes",
                        'frontend.default.load_entities.tag_last_nodes',
                        'frontend.default.load_entities.page_last_nodes'
                    ], [
                        '_page'   => $_tag_load,
                        '_items'  => $_tag_load->_last_nodes(4),
                        '_title'  => trans("frontend.block.tag_{$_page_id}_last_nodes"),
                        '_more'   => trans("frontend.block.tag_{$_page_id}_more_nodes"),
                        '_locale' => $this->localeView
                    ])->render(function ($view, $_content) {
                        return clear_html($_content);
                    })
                ];
            }
        }

        return $_response;
    }

    public function faq_items(...$arg)
    {
        $_response = NULL;
        $_entity = new Faq();
        $_render = $_entity->_render_block(['view' => NULL]);
        if ($_render) {
            $_response = [
                'object' => clear_html($_render)
            ];
        }

        return $_response;
    }

    public function shop_product_related_product($arg)
    {
        $_id = $arg['id'] ?? NULL;
        if ($_id && $_item = Product::find($_id)) {
            $_item->relatedProduct = $_item->_product_related('related');
            if ($_item->relatedProduct->isNotEmpty()) {
                $_item->relatedProduct = $_item->relatedProduct->map(function ($_item) {
                    if (method_exists($_item, '_load')) $_item->_load('teaser');

                    return $_item;
                });
                if ($_item->relatedProduct->count() > 5) $_item->relatedProduct = $_item->relatedProduct->random(5);
            }

            return [
                'object'   => View::first([
                    "frontend.{$this->deviceTemplate}.load_entities.related_product",
                    'frontend.default.load_entities.related_product'
                ], [
                    '_item' => $_item
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                }),
                'commands' => [
                    [
                        'command' => 'swiper',
                        'options' => [
                            'target' => '#promotional_goods'
                        ]
                    ]
                ]
            ];
        } else {
            return NULL;
        }
    }

    public function shop_product_consist_product($arg)
    {
        $_id = $arg['id'] ?? NULL;
        if ($_id && $_item = Product::find($_id)) {
            $_item->consistProduct = $_item->_product_consist('consist');
            if ($_item->consistProduct->isNotEmpty()) {
                $_item->consistProduct = $_item->consistProduct->map(function ($_item) {
                    if (method_exists($_item, '_load')) $_item->_load('teaser');

                    return $_item;
                });
                if ($_item->consistProduct->count() > 5) $_item->consistProduct = $_item->consistProduct->random(5);
            }

            return [
                'object'   => View::first([
                    "frontend.{$this->deviceTemplate}.load_entities.consist_product",
                    'frontend.default.load_entities.consist_product'
                ], [
                    '_item' => $_item
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                }),
                'commands' => [
                    [
                        'command' => 'swiper',
                        'options' => [
                            'target' => '#promotional_goods'
                        ]
                    ]
                ]
            ];
        } else {
            return NULL;
        }
    }

    public function store_management_block(...$arg)
    {
        return [
            'object' => View::first([
                "frontend.{$this->deviceTemplate}.load_entities.store_management_block",
                'frontend.default.load_entities.store_management_block'
            ], [
                //                    '_compareCount' => Compare::getCount(),
                '_compareCount' => collect([]),
                '_basket'       => app('basket')
            ])->render(function ($view, $_content) {
                return clear_html($_content);
            })
        ];
    }

    public function shop_recommended_products($arg)
    {
        $_basket = Basket::init();

        return [
            'object' => View::first([
                "frontend.{$this->deviceTemplate}.load_entities.recommended_products",
                'frontend.default.load_entities.recommended_products'
            ], [
                '_items' => $_basket->composition
            ])->render(function ($view, $_content) {
                return clear_html($_content);
            })
        ];
    }

    public function renderProductViewList($list, $title)
    {
        $_response = NULL;
        $_products = ViewList::get($list);
        $_eCommerce = NULL;
        if ($_products->isNotEmpty()) {
            if ($_products->count() > ViewList::PRODUCT_VIEW_LIST_MAX_ITEM) $_products = $_products->random(ViewList::PRODUCT_VIEW_LIST_MAX_ITEM);
            $_s = 0;
            $_products->transform(function ($_item) use (&$_eCommerce, &$_s) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');
                $_categories = $_item->_category;
                $_category_product = '';
                if ($_categories->isNotEmpty()) {
                    $_category_product = $_categories->first()->getTranslation('title', 'ua');
                }
                $_eCommerce[] = [
                    'id'            => $_item->sku,
                    'name'          => $_item->getTranslation('title', 'ua'),
                    'category'      => $_category_product,
                    'list_name'     => 'Хіти продажів',
                    'list_position' => $_s,
                    'quantity'      => 1,
                    'price'         => $_item->price['view_price'] ? (count($_item->price['view']) > 1 ? $_item->price['view'][1]['format']['price'] : $_item->price['view'][0]['format']['price']) : 0
                ];
                $_s++;

                return $_item;
            });

            return [
                'commands' => [
                    [
                        'command' => 'eval',
                        'options' => [
                            'data' => 'if (typeof gtag == "function") { gtag("event", "view_item_list", { items: '. json_encode($_eCommerce) .' }) }'
                        ]
                    ]
                ],
                'object' => View::first([
                    "frontend.{$this->deviceTemplate}.load_entities.view_lists_{$list}_product",
                    "frontend.default.load_entities.view_lists_{$list}_product",
                    "frontend.{$this->deviceTemplate}.load_entities.view_lists_product",
                    'frontend.default.load_entities.view_lists_product'
                ], [
                    '_title'     => $title,
                    '_items'     => $_products,
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                })
            ];
        }

        return $_response;
    }

    public function shop_product_view_list_new($arg)
    {
        return $this->renderProductViewList('new', trans('shop.titles.view_list_new'));
    }

    public function shop_product_view_list_hit($arg)
    {
        return $this->renderProductViewList('hit', trans('shop.titles.view_list_hit'));
    }

    public function shop_product_view_list_discount($arg)
    {
        return $this->renderProductViewList('discount', trans('shop.titles.view_list_discount'));
    }

    public function shop_product_view_list_recommended_front($arg)
    {
        return $this->renderProductViewList('recommended_front', trans('shop.titles.view_list_recommended'));
    }

    public function shop_product_view_list_recommended_checkout($arg)
    {
        return $this->renderProductViewList('recommended_checkout', trans('frontend.titles.view_list_recommended_checkout'));
    }

    public function shop_product_analogues($arg)
    {
        $_id = $arg['id'] ?? NULL;
        if ($_id && $_item = Product::find($_id)) {
            return [
                'object' => View::first([
                    "frontend.{$this->deviceTemplate}.load_entities.product_analogues",
                    'frontend.default.load_entities.product_analogues'
                ], [
                    '_items' => $_item->_analogues()
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                })
            ];
        } else {
            return [
                'object' => "<div class='uk-alert uk-alert-warning'>" . trans('shop.notifications.not_analogues') . "</div>"
            ];
        }
    }

    public function shop_product_availability($arg)
    {
        $_id = $arg['id'] ?? NULL;
        if ($_id && $_item = Product::find($_id)) {
            $_availability_product = $_item->getAvailability();

            return [
                'object'   => View::first([
                    "frontend.{$this->deviceTemplate}.shops.product_availability_price",
                    "frontend.default.shops.product_availability_price",
                ], [
                    '_items' => $_availability_product,
                    '_item'  => $_item
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                }),
                'commands' => [
                    [
                        'command' => 'useCounterBox',
                        'options' => []
                    ]
                ]
            ];
        } else {
            return [
                'object' => "<div id='uk-product-availability-price-box-{{ $_id }}'><div class='uk-alert uk-alert-warning'>" . trans('shop.notifications.not_availability') . "</div></div>"
            ];
        }
    }

    public function shop_product_last_view($arg)
    {
        $_exclude = isset($arg['exclude']) && $arg['exclude'] ? Product::find($arg['exclude']) : NULL;
        $_last_view = LastViewed::get($_exclude);
        if ($_last_view->isNotEmpty()) {
            return [
                'object' => View::first([
                    "frontend.{$this->deviceTemplate}.load_entities.product_last_view",
                    'frontend.default.load_entities.product_last_view'
                ], [
                    '_items' => $_last_view
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                })
            ];
        } else {
            return NULL;
        }
    }

}

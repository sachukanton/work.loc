<?php

namespace App\Models\Shop;

use App\Jobs\OptionQuery;
use App\Library\BaseModel;
use App\Library\Frontend;
use App\Models\Components\Banner;
use App\Models\Form\Review;
use App\Models\Seo\TmpMetaTags;
use App\Models\Seo\UrlAlias;
use App\Models\ShopAdditionalItem;
use App\Models\ShopParamItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class Category extends BaseModel
{

    protected $table = 'shop_categories';
    protected $guarded = [];
    public $translatable = [
        'title',
        'sub_title',
        'breadcrumb_title',
        'menu_title',
        'body',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];
    public $filterCategories = [];
    public $filterRequest = [
        'use'           => FALSE,
        'where'         => [
            'and'        => NULL,
            'or'         => NULL,
            'between'    => NULL,
            'to_collect' => NULL,
        ],
        'brands'        => [],
        'price'         => NULL,
        'sort'          => NULL,
        'params'        => [],
        'replace_title' => FALSE
    ];
    public $filterPage = FALSE;
    public $replaceTitle = NULL;
    public $filterOutput = NULL;
    public $productOutput = NULL;
    public $sortOutput = NULL;
    public $subCategoriesOutput = NULL;
    public $originalData = [
        'title'            => NULL,
        'breadcrumb_title' => NULL,
    ];
    public $originalBreadcrumbTitle = NULL;
    public $viewItem = 'module';
    public $metaMask;
    public $schemaData = [
        "lowPrice"   => 0,
        "highPrice"  => 0,
        "offerCount" => 0,
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Relationships
     */
    public function _parent()
    {
        return $this->hasOne(self::class, 'id', 'parent_id')
            ->with([
                '_parent',
                '_children'
            ]);
    }

    public function _children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with([
                '_children',
                '_alias'
            ]);
    }

    public function _params()
    {
        return $this->morphToMany(Param::class, 'model', 'shop_category_param')
            ->with([
                '_items' => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                }
            ])
            ->withPivot([
                'sort',
                'visible_in_filter',
                'collapse',
                'type',
                'condition'
            ])
            ->orderByRaw(DB::raw('(CASE WHEN `shop_params`.`type` = \'select\' THEN 1 ELSE 0 END) DESC'));
    }

    public function _banners()
    {
        return $this->morphToMany(Banner::class, 'model', 'shop_category_banner')
            ->withPivot([
                'sort'
            ]);
    }

    public function _products()
    {
        return $this->belongsToMany(Product::class, 'shop_product_category', 'category_id', 'model_id')
            ->with([
                '_alias'       => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_category'    => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_param_items' => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_preview'     => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_price'       => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_brand'       => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
            ]);
    }

    public function _additional_items()
    {
        return $this->hasMany(AdditionalItem::class, 'category_id')
            ->with([
                '_ingredient'
            ])
            ->orderBy('sort');
    }

    public function _ai()
    {
        return $this->belongsToMany(ParamItem::class, 'shop_category_additional_items', 'category_id', 'item_id', 'id', 'id')
            ->select([
                'shop_category_additional_items.id',
                'shop_param_items.id as ingredient_id',
                'shop_param_items.title',
                'shop_param_items.name',
                'shop_param_items.icon_fid',
                'shop_category_additional_items.sort',
                'shop_param_items.param_id',
                'shop_category_additional_items.sku',
                'shop_category_additional_items.value as weight',
                'shop_category_additional_items.default',
                'shop_category_additional_items.sort',
                'shop_category_additional_items.price'
            ])
            ->orderBy('shop_category_additional_items.default')
            ->orderBy('shop_category_additional_items.sort')
            ->remember(REMEMBER_LIFETIME * 24);
    }

    public function _additional_ingredients()
    {
        return ParamItem::where('param_id', 8)
            ->orderBy('title')
            ->pluck('title', 'id');
    }

    public function _filter_pages()
    {
        return $this->belongsToMany(UrlAlias::class, 'shop_filter_pages', 'category_id', 'id', 'id', 'model_id')
            ->where('url_alias.model_type', FilterPage::class)
            ->select([
                'url_alias.id',
                'url_alias.alias',
                'shop_filter_pages.base_path',
            ])
            ->remember(REMEMBER_LIFETIME);
    }

    public function getAllChildrenAttribute()
    {
        $_response = collect([]);
        $_children = $this->_children()
            ->remember(REMEMBER_LIFETIME)
            ->with([
                '_children',
                '_alias'
            ])
            ->get();
        if ($_children->isNotEmpty()) {
            $this->get_children($_response, $_children);
        }

        return $_response;
    }

    public function getRandomBannerAttribute()
    {
        $_response = NULL;
        $_banners = $this->_banners()
            ->active()
            ->remember(REMEMBER_LIFETIME)
            ->get();
        if ($_banners->isNotEmpty()) {
            $_response = $_banners->random();
        }

        return $_response;
    }

    public function getBannerAttribute()
    {
        $_response = NULL;
        $_banners = $this->_banners()
            ->get();
        if ($_banners->isNotEmpty()) {
            $_response = $_banners;
        }

        return $_response;
    }

    public function _product_list(&$filter, $page_number)
    {
        $_categories = $this->filterCategories->pluck('id');
        $_items = Product::from('shop_products as p')
            ->leftJoin('shop_products as m', 'm.id', '=', 'p.modify')
            ->leftJoin('shop_product_category as cp', 'cp.model_id', '=', 'p.id')
            ->leftJoin('shop_product_prices as pp', 'pp.product_id', '=', 'p.id')
            ->whereIn('cp.category_id', $_categories)
            ->where('p.status', 1)
            ->with([
                '_alias'             => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
                '_param_items'       => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
                '_preview'           => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
                '_product_count_buy' => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
                '_price'             => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
                '_mod'               => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
            ])
            ->select([
                'm.id',
                'm.modify',
                'm.sku',
                'm.title',
                'm.preview_fid',
                'm.mobile_fid',
                'm.full_fid',
                'm.mark_hit',
                'm.mark_new',
                'm.sort',
                'm.status',
                'm.double_card',
                'm.use_spicy',
            ])
            ->orderBy('pp.not_available');
        $_use_modify_param = NULL;
        $_query = $filter['query'];
        if ($_query['use']) {
            $_param_index = 0;
            //            if ($_query['brands']) {
            //                $_items->leftJoin('shop_brands as b', 'b.id', '=', 'p.brand_id')
            //                    ->whereIn('b.name', $_query['brands']);
            //            }
            if ($_query['where']['to_collect']) {
                foreach ($_query['where']['to_collect'] as $_where) {
                    $_param_index++;
                    $_items->leftJoin("shop_product_param as cp{$_param_index}", "cp{$_param_index}.model_id", '=', 'p.id');
                    if (is_array($_where)) {
                        $_items->whereIn("cp{$_param_index}.name", $_where);
                    } else {
                        $_items->where("cp{$_param_index}.name", $_where);
                    }
                }
            }
            if ($_query['price']) {
                $_items->whereBetween("pp.base_price", [
                    $_query['price']['min'],
                    $_query['price']['max']
                ]);
            }
            if ($_query['where']['between']) {
                foreach ($_query['where']['between'] as $_param_name => $_param_value) {
                    $_param_index++;
                    $_items->leftJoin("shop_product_param as cp{$_param_index}", "cp{$_param_index}.model_id", '=', 'p.id');
                    $_items->whereRaw(DB::raw("convert(`cp{$_param_index}`.`value`, signed integer) >= '{$_param_value['min']}' and convert(`cp{$_param_index}`.`value`, signed integer) <= '{$_param_value['max']}'"));
                }
            }
        }
        if ($_query['sort']) {
            switch ($_query['sort']) {
                case 'title_asc':
                    $_items->leftJoin('search_index as si', 'si.model_id', '=', 'p.id')
                        ->where('si.model_type', Product::class)
                        ->orderBy('p.title');
                    break;
                case 'topic_asc':
                    $_items->orderByDesc('p.sale_statistics');
                    break;
                case 'price_asc':
                    $_items->orderBy('pp.base_price');
                    break;
                case 'price_desc':
                    $_items->orderByDesc('pp.base_price');
                    break;
            }
        } else {
            $_items->orderBy('p.sort');
        }
        if ($page_number) {
            Paginator::currentPageResolver(function () use ($page_number) {
                return $page_number;
            });
        }
        $_items = $_items->distinct()
            ->remember(REMEMBER_LIFETIME)
            ->get();
        //            ->paginate(100, ['m.id']);
        if ($filter['selected_params'] && $this->modify_param) {
            $_modify_param_id = $this->modify_param;
            $filter['selected_params']->map(function ($p) use (&$_use_modify_param, $_modify_param_id) {
                if ($p['param']['id'] = $_modify_param_id) {
                    $_use_modify_param = $p['options']->sortBy('sort')->first()['id'];
                }
            });
        }
        if ($_items->isNotEmpty()) {
            $_category = $this->id;
            //            $_items->getCollection()->transform(function ($_item) use ($_use_modify_param, $_category) {
            $_items->transform(function ($_item) use ($_use_modify_param, $_category) {
                $_item->selected_modify = $_use_modify_param;
                $_item->selected_category = $_category;
                if (method_exists($_item, '_load')) $_item = $_item->_load('teaser');

                return $_item;
            });
            $this->schemaData['offerCount'] =
                //            $filter['count_products'] = $_items->total();
            $filter['count_products'] = $_items->count();
            if (request()->method() == 'GET') {
                $_banner = $this->_banners->first();
                $_items->prepend($_banner);
            }
        }

        return $_items;
    }

    public function _filter_request_params()
    {
        $_response = $this->filterRequest;
        if (!$_response['use']) {
            $_exclude_query_param = [
                'show_more',
                'view_load'
            ];
            if ($query_url = request()->all()) {
                if (is_array($query_url) && $query_url) {
                    $_response['use'] = TRUE;
                    foreach ($query_url as $_param => $_options) {
                        if (!in_array($_param, $_exclude_query_param)) {
                            if (!in_array($_param, [
                                'sort',
                                'price'
                            ])) {
                                if (is_array($_options) && isset($_options['min']) && isset($_options['max'])) {
                                    $_response['where']['between'][$_param] = $_options;
                                }
                            } elseif ($_param == 'price') {
                                $_response['price'] = $_options;
                            } else {
                                $_response['sort'] = $_options;
                            }
                        }
                    }
                }
            }
        }

        return $_response;
    }

    public function _sort($query)
    {
        $_sort_data = [
            'default'    => trans('shop.labels.catalog_sort_default'),
            // 'topic_asc'  => trans('shop.labels.catalog_sort_topic_asc'),
            // 'title_asc'  => trans('shop.labels.catalog_sort_title_asc'),
            'price_asc'  => trans('shop.labels.catalog_sort_price_asc'),
            'price_desc' => trans('shop.labels.catalog_sort_price_desc'),
        ];
        $_query_string = NULL;
        $_request_path = request()->path();
        if ($query['price']) $_query_string[] = "price[min]={$query['price']['min']}&price[max]={$query['price']['max']}";
        if ($query['where']['between']) foreach ($query['where']['between'] as $_key => $_query) $_query_string[] = "{$_key}[min]={$_query['min']}&{$_key}[max]={$_query['max']}";
        if ($query['sort'] && isset($_sort_data[$query['sort']])) {
            $_response['use'] = [
                'key'   => $query['sort'],
                'title' => $_sort_data[$query['sort']]
            ];
            foreach ($_sort_data as $_sort_key => $_sort_title) {
                if ($_sort_key != $query['sort']) {
                    $_tmp = $_query_string;
                    if ($_sort_key != 'default') $_tmp[] = "sort={$_sort_key}";
                    $_response['list'][] = [
                        'key'   => $_sort_key,
                        'title' => $_sort_title,
                        'alias' => is_array($_tmp) ? "/{$_request_path}?" . implode('&', $_tmp) : "/{$_request_path}"
                    ];
                }
            }
        } else {
            $_response['use'] = [
                'key'   => 'default',
                'title' => $_sort_data['default']
            ];
            foreach ($_sort_data as $_sort_key => $_sort_title) {
                if ($_sort_key != $query['sort']) {
                    $_tmp = $_query_string;
                    $_tmp[] = "sort={$_sort_key}";
                    $_response['list'][] = [
                        'key'   => $_sort_key,
                        'title' => $_sort_title,
                        'alias' => "/{$_request_path}?" . implode('&', $_tmp)
                    ];
                }
            }
        }

        return $_response;
    }

    public function _price_path($query)
    {
        $_response = NULL;
        $_query_string = NULL;
        $_request_path = request()->path();
        if (str_is('*-cfp-', $_request_path)) $_request_path = str_replace('-cfp-', '', $_request_path);
        if ($query['where']['between']) foreach ($query['where']['between'] as $_key => $_query) $_query_string[] = "{$_key}[min]={$_query['min']}&{$_key}[max]={$_query['max']}";
        if ($query['sort']) $_query_string[] = "sort={$query['sort']}";

        return [
            'path'  => "/{$_request_path}",
            'query' => $_query_string ? implode('&', $_query_string) : NULL
        ];
    }

    public function _filter()
    {
        $_filter_request = $this->_filter_request_params();
        //        $_filter_categories = $this->filterCategories;
        $_selected_count = count($_filter_request['params']) + count($_filter_request['brands']);
        $_response = [
            'categories_tree'      => [],
            //$this->categories_tree_render($_filter_categories, $this->id),
            'categories'           => [],
            'params'               => [],
            'query'                => $_filter_request,
            'params_product_count' => [],
            'selected_params'      => [],
            'selected_count'       => $_selected_count,
            'nofollow'             => $_selected_count > 3 ? TRUE : FALSE,
            'output'               => NULL,
            'js_params'            => NULL,
            'js_selected'          => NULL,
            'top_bar_output'       => NULL,
            'count_products'       => 0,
            'filter'               => [
                'mark'      => [],
                'exception' => [],
                'others'    => []
            ],
        ];
        //        $_categories = $_filter_categories->pluck('id');
        //        $_param_categories = $this->_params->isNotEmpty() ? collect([$this->id]) : $_filter_categories->pluck('id');
        $_categories = collect([$this->id]);
        $_param_categories = collect([$this->id]);
        $_response['categories'] = $_param_categories;
        $_category_alias = $this->generate_url;
        if ($_filter_request['replace_title']) {
            $this->replaceTitle = [
                'title'            => $this->title,
                'breadcrumb_title' => $this->breadcrumb_title ? : $this->title
            ];
        }
        $_all_params = Param::from('shop_params as p')
            ->join('shop_category_param as cp', 'cp.param_id', '=', 'p.id')
            ->with([
                '_items'
            ])
            ->whereIn('cp.model_id', $_param_categories)
            ->where('cp.visible_in_filter', 1)
            ->orderBy('cp.sort')
            ->distinct()
            ->select([
                'id',
                'p.type',
                'name',
                'title',
                'seo_title',
                'sort',
                'visible_in_filter',
                'collapse',
                'condition',
            ])
            ->remember(REMEMBER_LIFETIME)
            ->get();
        $_categories_product_params = Cache::remember("categories_params_" . $_categories->implode('_'), REMEMBER_LIFETIME, function () use ($_categories) {
            return DB::table('shop_products as p')
                ->join('shop_product_category as pc', 'p.id', '=', 'pc.model_id')
                ->leftJoin('shop_category_param as pcp', 'pcp.model_id', '=', 'pc.category_id')
                ->join('shop_product_param as cpi', 'cpi.model_id', '=', 'p.id')
                ->whereIn('pc.category_id', $_categories)
                ->where('p.status', 1)
                ->where('pcp.visible_in_filter', 1)
                ->distinct()
                ->select([
                    'p.id',
                    'cpi.name'
                ])
                ->get()
                ->groupBy('name');
        });
        $_products_params = ParamItem::from('shop_param_items as cpi')
            ->leftJoin('shop_category_param as pcp', 'pcp.param_id', '=', 'cpi.param_id')
            ->leftJoin('shop_product_category as pc', 'pcp.model_id', '=', 'pc.category_id')
            ->whereIn('pc.category_id', $_categories)
            ->where('pcp.visible_in_filter', 1)
            ->select([
                'cpi.name',
            ])
            ->distinct()
            ->remember(REMEMBER_LIFETIME)
            ->get()
            ->transform(function ($_i) use ($_categories_product_params) {
                $_products = $_categories_product_params->get($_i->name, collect([]));
                $_i->count_product = $_products->count();
                $_i->products = $_products->pluck('id');

                return $_i;
            })
            ->filter(function ($_i) {
                return $_i->count_product;
            })
            ->keyBy('name');
        if ($_products_params->isNotEmpty()) {
            $_products_params->each(function ($_param_item) use (&$_response) {
                if ($_param_item->count_product) $_response['params_product_count'][$_param_item->name] = $_param_item->count_product;
            });
        }
        if ($_filter_request['use']) {
            $_query = [
                'and' => NULL,
                'or'  => NULL,
            ];
            if ($_all_params->isNotEmpty()) {
                $_all_params->each(function ($_param) use (&$_query, $_response) {
                    foreach ($_response['query']['params'] as $_option) {
                        if ($_param->_items->isNotEmpty() && ($_use_option = $_param->_items->keyBy('name')->get($_option))) {
                            if ($_response['query']['where']['and']) {
                                foreach ($_response['query']['where']['and'] as $_option_and) {
                                    if ((is_array($_option_and) && in_array($_option, $_option_and)) || $_option_and == $_option) {
                                        $_query['and'][$_use_option->param_id][$_use_option->id] = $_option;
                                    }
                                }
                            }
                            if ($_response['query']['where']['or']) {
                                foreach ($_response['query']['where']['or'] as $_option_or) {
                                    if (in_array($_option, $_option_or)) {
                                        $_query['or'][$_use_option->param_id][$_use_option->id] = $_option;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            $_response['query']['where']['and'] = $_query['and'];
            $_response['query']['where']['or'] = $_query['or'];
        }
        if ($_all_params->isNotEmpty()) {
            $_all_params->each(function ($_param) use (&$_response, $_categories, $_category_alias, $_filter_request) {
                $_param_options = [];
                if ($_param->type == 'select') {
                    if ($_param->_items->isNotEmpty()) {
                        $_param->_items->each(function ($_option) use (&$_param_options, $_param, &$_response, $_categories, $_category_alias, $_filter_request) {
                            if ($_option->visible_in_filter) {
                                $_count_default_product = isset($_response['params_product_count'][$_option->name]) ? $_response['params_product_count'][$_option->name] : 0;
                                if ($_count_default_product) {
                                    $_query = $_option->get_count_products($_categories, $_response['query'], $_param);
                                    $_param_options[$_option->id] = [
                                        'id'                    => $_option->id,
                                        'name'                  => $_option->name,
                                        'title'                 => $_option->title,
                                        'sort'                  => $_option->sort,
                                        'style'                 => [
                                            'id'    => $_option->style_id,
                                            'class' => $_option->style_class,
                                            'icon'  => $_option->icon_fid ? $_option->_icon_asset(NULL, ['only_way' => FALSE]) : NULL
                                        ],
                                        'nofollow'              => $_response['nofollow'],
                                        'attributes'            => $_option->attribute,
                                        'unit'                  => $_option->unit_value,
                                        'count_default'         => $_count_default_product,
                                        'count_request'         => $_filter_request['use'] ? $_query['count'] : $_count_default_product,
                                        'count_query_request'   => $_query['query'],
                                        'count_query_request_2' => $_query['query_2'],
                                        'alias'                 => $_query['alias'] ? "{$_category_alias}-cfp-{$_query['alias']}" : $_category_alias,
                                        'alias_rollback'        => $_query['alias_rollback'] ? "{$_category_alias}-cfp-{$_query['alias_rollback']}" : ($_query['active'] ? $_category_alias : NULL),
                                        'base_alias'            => $_query['alias'] ? "{$_category_alias}-cfp-{$_query['base_alias']}" : $_category_alias,
                                        'active'                => $_query['active'],
                                    ];
                                    if ($_query['active']) {
                                        $_response['selected_params'][$_param->id]['param'] = [
                                            'id'         => $_param->id,
                                            'name'       => $_param->name,
                                            'type'       => $_param->type,
                                            'title'      => $_param->title,
                                            'meta_title' => $_param->seo_title,
                                            'condition'  => $_param->condition,
                                            'sort'       => $_param->sort
                                        ];
                                        $_meta_option_title = $_option->meta_title;
                                        if (!$_meta_option_title) {
                                            $_meta_option_title = $_param->seo_title ? str_replace('[:option]', $_option->title, $_param->seo_title) : "{$_param->title} {$_option->title}";
                                        }
                                        $_response['selected_params'][$_param->id]['options'][$_option->id] = [
                                            'id'         => $_option->id,
                                            'name'       => $_option->name,
                                            'title'      => $_option->title,
                                            'meta_title' => $_meta_option_title,
                                            'sort'       => $_option->sort,
                                            'style'      => [
                                                'id'    => $_option->style_id,
                                                'class' => $_option->style_class
                                            ],
                                            'nofollow'   => $_response['nofollow'],
                                            'attributes' => NULL,
                                            'unit'       => NULL,
                                            'alias'      => $_query['alias_rollback'] ? "{$_category_alias}-cfp-{$_query['alias_rollback']}" : ($_query['active'] ? $_category_alias : NULL),
                                        ];
                                    }
                                }
                            }
                        });
                    }
                } else {
                    if ($_param->_items->isNotEmpty()) {
                        $_param_option = $_param->_items->first();
                        $_min_max_values = DB::table('shop_products as p')
                            ->leftJoin('shop_product_category as pc', 'p.id', '=', 'pc.model_id')
                            ->leftJoin('shop_product_param as pi', 'pi.model_id', '=', 'p.id')
                            ->whereIn('pc.category_id', $_categories)
                            ->where('pi.param_item_id', $_param_option->id)
                            ->where('p.status', 1)
                            ->select(DB::raw('max(convert(pi.value, signed integer)) as max'), DB::raw('min(convert(pi.value, signed integer)) as min'))
                            ->first();
                        $_response['params'][$_param->id]['options'] = [
                            'min'   => [
                                'default' => floor($_min_max_values->min),
                                'request' => floor(isset($_response['query']['where']['between'][$_param_option->name]['min']) && $_response['query']['where']['between'][$_param_option->name]['min'] >= $_min_max_values->min ? $_response['query']['where']['between'][$_param_option->name]['min'] : $_min_max_values->min)
                            ],
                            'max'   => [
                                'default' => ceil($_min_max_values->max),
                                'request' => ceil(isset($_response['query']['where']['between'][$_param_option->name]['max']) && $_response['query']['where']['between'][$_param_option->name]['max'] <= $_min_max_values->max ? $_response['query']['where']['between'][$_param_option->name]['max'] : $_min_max_values->max)
                            ],
                            'alias' => $this->_price_path($_response['query'], $_param_option->name)
                        ];
                    }
                }
                $_response['params'][$_param->id]['param'] = [
                    'name'      => $_param->name,
                    'type'      => $_param->type == 'input_number' ? 'range' : $_param->type,
                    'title'     => $_param->title,
                    'condition' => $_param->condition,
                    'collapse'  => (int)$_param->collapse,
                    'sort'      => $_param->sort
                ];
                if ($_param->type == 'select') {
                    $_response['params'][$_param->id]['options'] = collect($_param_options)
                        ->sortBy('sort')
                        ->values()
                        ->toArray();
                }
                $_response['query']['use'] = $_filter_request['use'];
            });
        }
        $_options_count_result = collect([]);
        $_options_count_result_use = FALSE;
        if ($_response['params']) {
            $_response['params'] = collect($_response['params'])
                ->sortBy('param.sort')
                ->keyBy('param.name');
        }
        if ($_response['params'] && $_filter_request['use']) {
            $_query_select = NULL;
            $_query_select_2 = NULL;
            foreach ($_response['params'] as $_param) {
                if ($_param['param']['type'] == 'select') {
                    foreach ($_param['options'] as $_option) {
                        if ($_option['active'] == FALSE && $_option['count_query_request_2']) $_query_select_2[] = DB::raw("({$_option['count_query_request_2']}) as `{$_option['name']}`");
                        if ($_option['active'] == FALSE && $_option['count_query_request']) $_query_select[$_option['name']] = $_option['base_alias'];
                    }
                }
            }
            $_result_count = collect([]);
            if ($_query_select) {
                $_result_count = DB::table('shop_param_items_count')
                    ->whereIn('alias', $_query_select)
                    ->pluck('count', 'alias');
            }
            $_response['params'] = $_response['params']->transform(function ($_p) use ($_result_count, $_response, &$_options_count_result_use) {
                if ($_p['param']['type'] == 'select') {
                    foreach ($_p['options'] as $_id => $_option) {
                        if ($_result_count->has($_option['base_alias'])) {
                            $_p['options'][$_id]['count_request'] = (int)$_result_count->get($_option['base_alias'], 0);
                        } else {
                            if (is_null($_response['query']['price']) && $_option['active'] == FALSE && $_p['param']['type'] == 'select') {
                                dispatch((new OptionQuery([
                                    'query'    => $_option['count_query_request'],
                                    'alias'    => $_option['base_alias'],
                                    'category' => $_response['categories']->first(),
                                ]))->onQueue('high'));
                            }
                            if ($_option['active'] == FALSE && $_p['param']['type'] == 'select') $_options_count_result_use = TRUE;
                        }
                    }
                }

                return $_p;
            });
            if (isset($_response['query']['where']['between']) && $_response['query']['where']['between'] || isset($_response['query']['price']) && $_response['query']['price']) $_options_count_result_use = TRUE;
            if ($_options_count_result_use) {
                $_query_where = NULL;
                $_query_join = NULL;
                if (isset($_response['query']['price']) && $_response['query']['price']) {
                    $_query_where[] = "`pr`.`base_price` >= '{$_response['query']['price']['min']}' and `pr`.`base_price` <= '{$_response['query']['price']['max']}'";
                }
                if (isset($_response['query']['where']['between']) && $_response['query']['where']['between']) {
                    $_po = 1;
                    foreach ($_response['query']['where']['between'] as $_name => $data) {
                        $_query_join [] = [
                            "shop_product_param as po{$_po}",
                            "po{$_po}.model_id",
                        ];
                        $_query_where[] = "convert(`po{$_po}`.`value`, signed integer) >= '{$data['min']}' and convert(`po{$_po}`.`value`, signed integer) <= '{$data['max']}'";
                        $_po++;
                    }
                }
                $_query_where = $_query_where ? implode(' and ', $_query_where) : '1=1';
                if ($_query_select_2) {
                    $_param_options_count_result = DB::table('shop_products as p')
                        ->leftJoin('shop_product_category as pc', 'pc.model_id', '=', 'p.id')
                        ->leftJoin('shop_brands as b', 'b.id', '=', 'p.brand_id')
                        ->leftJoin('shop_product_prices as pr', 'pr.product_id', '=', 'p.id')
                        ->when($_query_join, function ($query) use ($_query_join) {
                            foreach ($_query_join as $_join) {
                                $query->leftJoin($_join[0], $_join[1], '=', 'p.id');
                            }
                        })
                        ->whereIn('pc.category_id', $_categories)
                        ->where('p.status', 1)
                        ->select($_query_select_2)
                        ->whereRaw($_query_where)
                        ->get();
                    $_param_options_count_result->each(function ($_q) use (&$_options_count_result) {
                        foreach ($_q as $_o => $_c) {
                            if ($_c) {
                                $_count = (int)$_options_count_result->get($_o, 0) + 1;
                                $_options_count_result->put($_o, $_count);
                            }
                        }
                    });
                }
            }
            $_response['params'] = $_response['params']->transform(function ($_p) use ($_options_count_result, $_filter_request, $_options_count_result_use) {
                if ($_p['param']['type'] == 'select' && ($_filter_request['use'] || !$_filter_request['use'] && $_p['param']['name'] != 'brands')) {
                    if (!is_array($_p['options'])) $_p['options'] = $_p['options']->toArray();
                    if ($_options_count_result_use) {
                        foreach ($_p['options'] as $_id => $_option) {
                            $_p['options'][$_id]['count_request'] = $_options_count_result->get($_option['name'], 0);
                        }
                    }
                }

                return $_p;
            })
                ->toArray();
            if ($_filter_request['use'] && $_response['selected_params']) {
                $_tmp_meta_tags = $this->_tmp_meta_tags
                    ->where('type', 'filter')->first();
                $_meta_params = NULL;
                $_h1_title_params = NULL;
                foreach ($_response['selected_params'] as $_param) {
                    $_param_meta_options = [];
                    $_param_title_options = [];
                    foreach ($_param['options'] as $_option) {
                        $_param_title_options[] = $_option['title'];
                        $_param_meta_options[] = $_option['meta_title'];
                    }
                    $_meta_params[] = mb_strtolower(implode(', ', $_param_meta_options));
                    $_h1_title_params[] = mb_strtolower($_param['param']['title'] . ': ' . implode(', ', $_param_title_options));
                }
                if ($_meta_params) {
                    $this->originalData['title'] = $this->title;
                    $this->originalData['breadcrumb_title'] = $this->breadcrumb_title;
                    if ($this->filterPage instanceof FilterPage) {
                        $this->title = $this->filterPage->title;
                        $this->sub_title = $this->filterPage->sub_title ? : NULL;
                        $this->breadcrumb_title = $this->filterPage->breadcrumb_title ? : NULL;
                        $this->meta_title = $this->filterPage->meta_title ? : $this->filterPage->title;
                        $this->body = $this->filterPage->body;
                        if (!$this->filterPage->meta_description && isset($_tmp_meta_tags->meta_description) && $_tmp_meta_tags->meta_description) {
                            $this->meta_description = short_code($_tmp_meta_tags->meta_description, [
                                'title'  => $this->title,
                                'params' => implode('; ', $_meta_params)
                            ]);
                        } elseif ($this->filterPage->meta_description) {
                            $this->meta_description = $this->filterPage->meta_description;
                        }
                        if (!$this->filterPage->meta_keywords && isset($_tmp_meta_tags->meta_keywords) && $_tmp_meta_tags->meta_keywords) {
                            $this->meta_keywords = short_code($_tmp_meta_tags->meta_keywords, [
                                'title'  => $this->title,
                                'params' => implode(', ', $_meta_params)
                            ]);
                        } elseif ($this->filterPage->meta_keywords) {
                            $this->meta_keywords = $this->filterPage->meta_keywords;
                        }
                    } else {
                        if (isset($_tmp_meta_tags->meta_title) && $_tmp_meta_tags->meta_title) {
                            $this->meta_title = short_code($_tmp_meta_tags->meta_title, [
                                'title'  => $this->title,
                                'params' => implode('; ', $_meta_params)
                            ]);
                        }
                        if (isset($_tmp_meta_tags->meta_description) && $_tmp_meta_tags->meta_description) {
                            $this->meta_description = short_code($_tmp_meta_tags->meta_description, [
                                'title'  => $this->title,
                                'params' => implode('; ', $_meta_params)
                            ]);
                        }
                        if (isset($_tmp_meta_tags->meta_keywords) && $_tmp_meta_tags->meta_keywords) {
                            $this->meta_keywords = short_code($_tmp_meta_tags->meta_keywords, [
                                'title'  => $this->title,
                                'params' => implode(', ', $_meta_params)
                            ]);
                        }
                        $this->title = short_code("$this->title [:params]", [
                            'params' => implode('; ', $_h1_title_params)
                        ]);
                        $this->body =
                        $this->sub_title =
                        $this->breadcrumb_title =
                        $this->teaser = NULL;
                    }
                }
            }
        }
        if ($_response['selected_params']) {
            $_response['selected_params'] = collect($_response['selected_params'])
                ->sortBy('param.sort')
                ->values()
                ->transform(function ($_i) {
                    $_i['options'] = collect($_i['options'])
                        ->sortBy('sort')
                        ->values();

                    return $_i;
                });
        }

        return $_response;
    }

    public function _renderFilter(&$_filter)
    {
        $_all_filter_pages = $this->_filter_pages->pluck('alias', 'base_path');
        foreach ($_filter['params'] as $_param_id => $_param) {
            if ($_param['param']['type'] == 'select') {
                if (!count($_param['options'])) {
                    unset($_filter['params'][$_param_id]);
                } else {
                    foreach ($_param['options'] as $_option_id => $_option) {
                        $_update = FALSE;
                        if ($_all_filter_pages->isNotEmpty()) {
                            if (isset($_option['alias']) && $_option['alias'] && ($_get_alias = $_all_filter_pages->get(trim($_option['alias'], '/')))) {
                                $_update = TRUE;
                                $_option['alias'] = $_get_alias;
                            }
                            if (isset($_option['alias_rollback']) && $_option['alias_rollback'] && ($_get_alias_rollback = $_all_filter_pages->get(trim($_option['alias_rollback'], '/')))) {
                                $_update = TRUE;
                                $_option['alias_rollback'] = $_get_alias_rollback;
                            }
                        }
                        if ($_update) {
                            $_filter['params'][$_param_id]['options'][$_option_id] = $_option;
                            foreach ($_filter['selected_params'] as $_selected_id => $_selected_param) {
                                if ($_selected_param['param']['name'] == $_param['param']['name']) {
                                    $_filter['selected_params'][$_selected_id]['options']->transform(function ($_o) use ($_option) {
                                        if ($_option['name'] == $_o['name']) $_o['alias'] = $_option['alias_rollback'];

                                        return $_o;
                                    });
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($_filter['params']) {
            foreach ($_filter['params'] as $_key => $_param) {
                if (isset($_filter['filter'][$_key])) {
                    $_filter['filter'][$_key] = $_param['options'];
                } else {
                    $_filter['filter']['others'] = array_merge($_filter['filter']['others'], $_param['options']);
                }
            }
        }
        $_template = [
            "frontend.{$this->deviceTemplate}.shops.filter_category_{$this->id}",
            "frontend.{$this->deviceTemplate}.shops.filter",
            "frontend.default.shops.filter_category_{$this->id}",
            "frontend.default.shops.filter",
            'backend.base.shop_filter'
        ];
        $_filter['output'] = View::first($_template, [
            //            '_sub_categories' => $_filter['categories_tree'],
            //            '_selected'       => $_filter['selected_params'],
            '_filter'   => $_filter['filter'],
            '_category' => $this
        ])
            ->render(function ($view, $_content) {
                return clear_html($_content);
            });
        $_filter['js_params'] = json_encode($_filter['filter']);
        $_filter['js_selected'] = json_encode($_filter['selected_params']);
        $_template = [
            "frontend.{$this->deviceTemplate}.shops.top_bar_category_{$this->id}",
            "frontend.{$this->deviceTemplate}.shops.top_bar",
            "frontend.default.shops.top_bar_category_{$this->id}",
            "frontend.default.shops.top_bar",
            'backend.base.shop_top_bar'
        ];
        $_filter['top_bar_output'] = View::first($_template, [
            '_view'           => $this->viewItem,
            '_sort'           => $this->_sort($_filter['query']),
            '_count_products' => $_filter['count_products'],
        ])
            ->render(function ($view, $_content) {
                return clear_html($_content);
            });
        $_template = [
            "frontend.{$this->deviceTemplate}.shops.sub_categories_{$this->id}",
            "frontend.{$this->deviceTemplate}.shops.sub_categories",
            "frontend.default.shops.sub_categories_{$this->id}",
            "frontend.default.shops.sub_categories",
            'backend.base.shop_sub_categories'
        ];
        $_filter['sub_categories_output'] = View::first($_template, [
            '_sub_categories' => $_filter['categories_tree']
        ])
            ->render(function ($view, $_content) {
                return clear_html($_content);
            });
        $this->filterOutput = $_filter['output'];
        $this->sortOutput = $_filter['top_bar_output'];
        $this->subCategoriesOutput = $_filter['sub_categories_output'];
    }

    public function _tmp_meta_tags()
    {
        return $this->morphMany(TmpMetaTags::class, 'model');
    }

    public function _load($view_mode = 'full')
    {
        if (!$this->status) $this->style_class = $this->style_class ? "{$this->style_class} uk-page-not-published" : 'uk-page-not-published';
        switch ($view_mode) {
            default:
                $this->body = content_render($this);
                //                    $this->relatedMedias = $this->_files_related()->wherePivot('type', 'medias')->get();
                //                    $this->relatedFiles = $this->_files_related()->wherePivot('type', 'files')->get();
                break;
        }

        return $this;
    }

    public function _render($options = NULL)
    {
        global $wrap;
        $this->_eCommerce = collect([]);
        $this->viewItem = Cookie::get('shop_category_view', 'module');
        $_view = $options['view_mode'] ?? NULL;
        $this->_load($_view);
        $_categories = $this->all_children;
        if ($_categories->isNotEmpty()) {
            $_categories = $_categories->filter(function ($_category) {
                return $_category->status;
            });
        }
        $this->filterCategories = $_categories->put($this->id, $this);
        $_filter = $this->_filter();
        $this->filterOutput = $_filter['output'];
        $this->sortOutput = $_filter['top_bar_output'];
        $_page_number = current_page();
        $this->_items = $this->_product_list($_filter, $_page_number);
        $this->_renderFilter($_filter);
        $_set_wrap = [
            'seo.title'         => $this->meta_title ? : ($this->metaMask['meta_title'] ?? $this->title),
            'seo.keywords'      => $this->meta_keywords ? : ($this->metaMask['meta_keywords'] ?? NULL),
            'seo.description'   => $this->meta_description ? : ($this->metaMask['meta_description'] ?? NULL),
            'seo.robots'        => $this->meta_robots,
            'seo.last_modified' => $this->last_modified,
            'page.title'        => $this->title,
            'page.style_id'     => $this->style_id,
            'page.style_class'  => $this->style_class ? [$this->style_class] : NULL,
            'page.breadcrumb'   => breadcrumb_render(['entity' => $this]),
            'seo.open_graph'    => [
                'title'       => $this->title,
                'description' => ($this->meta_description ? : ($this->metaMask['meta_description'] ?? NULL)) ? : config_data_load(config('os_seo'), 'settings.*.description', $wrap['locale']),
                'url'         => $wrap['seo']['base_url'] . $this->generate_url,
            ],
            'page.scripts'      => [
                [
                    'url'      => 'template/js/custom.js',
                    'position' => 'footer',
                    'sort'     => 1000
                ]
            ],
        ];
        $_item_template = [
            "frontend.{$this->deviceTemplate}.shops.product_teaser_category_{$this->id}",
            "frontend.{$this->deviceTemplate}.shops.product_teaser",
            "frontend.default.shops.product_teaser_category_{$this->id}",
            "frontend.default.shops.product_teaser",
            'backend.base.shop_product_teaser'
        ];
        $_s = 0;
        if ($this->_items->count()) {
            foreach ($this->_items as $_item) {
                $_view = $this->viewItem;
                $this->productOutput .= clear_html(View::first($_item_template, compact('_item', '_view')));
                if ($_item instanceof Product) {
                    $this->_eCommerce->push([
                        'id'            => $_item->sku,
                        'name'          => $_item->getTranslation('title', 'ua'),
                        'category'      => $this->getTranslation('title', 'ua'),
                        'list_name'     => 'Товари категорії',
                        'list_position' => $_s,
                        'quantity'      => 1,
                        'price'         => count($_item->price['view']) > 1 ? $_item->price['view'][1]['format']['price'] : $_item->price['view'][0]['format']['price']
                    ]);
                }
                $_s++;
            }
        }
        if (!$this->productOutput) {
            $this->productOutput = '<div class="col-sm-12"><div class="alert alert-warning">' . trans('shop.alerts.empty') . '</div></div>';
            $_set_wrap['seo.robots'] = 'noindex, nofollow';
        }
        if ($this->_items->isEmpty() && $_page_number) abort(404);
        if ($_page_number) $_set_wrap['seo.robots'] = 'noindex, follow';
        //        if ($this->_items->isNotEmpty() && $this->_items->hasMorePages()) {
        //            $_page_number = $_page_number ? : 1;
        //            $_page_number++;
        //            $_current_url = wrap()->get('seo.url_alias');
        //            $_current_url_query = wrap()->get('seo.url_query');
        //            $_url = trim($_current_url, '/') . "/page-{$_page_number}";
        //            $_next_page_link = _u($_url) . $_current_url_query;
        //            $_set_wrap['seo.link_next'] = ($wrap['locale'] != DEFAULT_LOCALE ? "/{$wrap['locale']}" : NULL) . $_next_page_link;
        //        }
        $this->setWrap($_set_wrap);
        $_template = [
            "frontend.{$this->deviceTemplate}.shops.category_{$this->id}",
            "frontend.{$this->deviceTemplate}.shops.category",
            "frontend.default.shops.category_{$this->id}",
            "frontend.default.shops.category",
            'backend.base.shop_category'
        ];
        if (isset($options['view']) && $options['view']) array_unshift($_template, $options['view']);
        $this->template = $_template;

        return $this;
    }

    public function _render_ajax(Request $request)
    {
        global $wrap;
        $_categories = $this->all_children;
        $_load_more = $request->has('load_more') ? TRUE : FALSE;
        if ($_categories->isNotEmpty()) {
            $_categories = $_categories->filter(function ($_category) {
                return $_category->status;
            });
        }
        $this->filterCategories = $_categories->put($this->id, $this);
        $_filter = $this->_filter();
        $_page_number = current_page();
        $_items = $this->_product_list($_filter, $_page_number);
        $this->_renderFilter($_filter);
//        if ($_items->isNotEmpty() && $_items->hasMorePages()) {
//            $_page_number = $_page_number ? : 1;
//            $_page_number++;
//            $_current_url = wrap()->get('seo.url_alias');
//            $_current_url_query = wrap()->get('seo.url_query');
//            $_url = trim($_current_url, '/') . "/page-{$_page_number}";
//            $_next_page_link = ($wrap['locale'] != DEFAULT_LOCALE ? "/{$wrap['locale']}" : NULL) . _u($_url) . $_current_url_query;
//            wrap()->set('seo.link_next', $_next_page_link);
//        }
        if ($_load_more == FALSE) {
            $commands['commands'][] = [
                'command' => 'eval',
                'options' => [
                    'data' => "catalogFilterParam = {$_filter['js_params']};catalogSelected = {$_filter['js_selected']};app.__vue__.refreshFilter = true;"
                ]
            ];
            $commands['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'target' => '#uk-items-list-top-bar',
                    'data'   => $_filter['top_bar_output']
                ]
            ];
            $commands['commands'][] = [
                'command' => 'changeUrl',
                'options' => [
                    'url' => $request->fullUrl(),
                ]
            ];
            $commands['commands'][] = [
                'command' => 'changeTitle',
                'options' => [
                    'title' => $this->meta_title ? : $this->title,
                ]
            ];
            $commands['commands'][] = [
                'command' => 'text',
                'options' => [
                    'data'   => $this->title,
                    'target' => '.title-category h1',
                ]
            ];
            // $commands['commands'][] = [
            //     'command' => 'replaceWith',
            //     'options' => [
            //         'data'   => View::first([
            //             "frontend.{$this->deviceTemplate}.partials.breadcrumbs",
            //             'frontend.default.partials.breadcrumbs',
            //             'backend.base.breadcumb'
            //         ], ['_items' => breadcrumb_render(['entity' => $this])])
            //             ->render(function ($view, $_content) {
            //                 return clear_html($_content);
            //             }),
            //         'target' => '#breadcrumbs',
            //     ]
            // ];
            $commands['commands'][] = [
                'command' => 'replaceWith',
                'options' => [
                    'data'   => View::first([
                        "frontend.{$this->deviceTemplate}.shops.menu",
                        'frontend.default.shops.menu',
                    ], ['_item' => $this])
                        ->render(function ($_content) {
                            return is_string($_content) ? clear_html($_content) : NULL;
                        }),
                    'target' => '#uk-items-list-menu',
                ]
            ];
        }
//        $commands['commands'][] = [
//            'command' => 'html',
//            'options' => [
//                'target' => '#uk-items-list-pagination',
//                'data'   => clear_html($_items->links('frontend.default.partials.pagination'))
//            ]
//        ];
        $_items_output = NULL;
        $_item_template = [
            "frontend.{$this->deviceTemplate}.shops.product_teaser_category_{$this->id}",
            "frontend.{$this->deviceTemplate}.shops.product_teaser",
            "frontend.default.shops.product_teaser_category_{$this->id}",
            "frontend.default.shops.product_teaser",
            'backend.base.shop_product_teaser'
        ];
        $_view = Cookie::get('shop_category_view', 'module');
        foreach ($_items as $_item) {
            $_items_output .= View::first($_item_template, compact('_item', '_view'))
                ->render(function ($view, $_content) {
                    return clear_html($_content);
                });
        }
        if (!$_items_output) $_items_output = '<div class="col-sm-12"><div class="alert alert-warning">' . trans('shop.alerts.not_found_product') . '</div></div>';
        $commands['commands'][] = [
            'command' => $_load_more ? 'append' : 'html',
            'options' => [
                'target' => '#uk-items-list',
                'data'   => $_items_output
            ]
        ];
        if ($_page_number || !$this->body) {
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-body',
                    'data'   => ''
                ]
            ];
        } elseif (is_null($_page_number) && $this->body) {
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-body',
                    'data'   => "<div class=\"node-content uk-margin-medium-bottom\">{$this->body}</div>"
                ]
            ];
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-title',
                    'data'   => "<h1 class=\"title-01 uk-text-uppercase uk-padding uk-padding-remove-horizontal uk-padding-remove-bottom uk-margin-remove\"><span class=\"title-color\">{$this->title}</span></h1>"
                ]
            ];
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-subtitle',
                    'data'   => "<div class=\"sub-title uk-margin-medium-top uk-margin-small-bottom uk-text-uppercase\">{$this->sub_title}</div>"
                ]
            ];
        }
        if (($_user = $request->user()) && $_user->can('shop_categories_update')) {
            $_locale = wrap()->getLocale();
            $_output_edit = NULL;
            if ($this->filterPage === TRUE) {
                $_output_edit .= _l('добавить страницу', 'oleus.shop_filter_pages.create', [
                    'p'          => [
                        'category' => $this->id,
                        'alias'    => request()->path()
                    ],
                    'attributes' => [
                        'target' => '_blank',
                        'class'  => 'uk-link-success'
                    ]
                ]);
            } elseif ($this->filterPage instanceof FilterPage) {
                if ($_locale == DEFAULT_LOCALE) {
                    $_output_edit .= _l('редактировать', 'oleus.shop_filter_pages.edit', [
                        'p'          => ['shop_filter_page' => $this->filterPage->id],
                        'attributes' => [
                            'target' => '_blank',
                            'class'  => 'uk-link-primary'
                        ]
                    ]);
                } else {
                    $_output_edit .= _l('редактировать', 'oleus.shop_filter_pages.translate', [
                        'p'          => [
                            'shop_filter_page' => $this->filterPage->id,
                            'locale'           => $_locale
                        ],
                        'attributes' => [
                            'target' => '_blank',
                            'class'  => 'uk-link-primary'
                        ]
                    ]);
                }
            } else {
                if ($_locale == DEFAULT_LOCALE) {
                    $_output_edit .= _l('редактировать', 'oleus.shop_categories.edit', [
                        'p'          => ['id' => $this->id],
                        'attributes' => [
                            'target' => '_blank',
                            'class'  => 'uk-link-primary'
                        ]
                    ]);
                } else {
                    $_output_edit .= _l('редактировать', 'oleus.shop_categories.translate', [
                        'p'          => [
                            'page'   => $this->id,
                            'locale' => $_locale
                        ],
                        'attributes' => [
                            'target' => '_blank',
                            'class'  => 'uk-link-primary'
                        ]
                    ]);
                }
            }
            if ($_output_edit) {
                $commands['commands'][] = [
                    'command' => 'replaceWith',
                    'options' => [
                        'data'   => clear_html("<li>{$_output_edit}</li>"),
                        'target' => '#control-edit-box ul.dropdown-menu li',
                    ]
                ];
            }
        }

        return $commands;
    }

    public function get_children(&$_response, Collection $children)
    {
        if ($children->isNotEmpty()) {
            $children->map(function ($_child) use (&$_response) {
                $_response->put($_child->id, $_child);
                $_child->get_children($_response, $_child->_children);
            });
        }
    }

    public function get_parents(&$_response, self $parent = NULL)
    {
        if ($parent) {
            $_response->put($_response->count(), $parent);
            $parent->get_parents($_response, $parent->_parent);
        }
    }

    public function categories_tree_render($categories, $parent = NULL)
    {
        $_response = NULL;
        $categories->each(function ($_category) use (&$_response, $categories, $parent) {
            if ($_category->parent_id == $parent && $_category->status) {
                $_children = $this->categories_tree_render($categories, $_category->id);
                if ($_category->preview_fid) {
                    $_preview = $_category->_preview_asset('shopSubCategoryThumb_177_52', ['only_way' => TRUE]);
                } else {
                    $_preview = image_render(NULL, 'shopSubCategoryThumb_177_52', ['only_way' => TRUE]);
                }
                $_response[] = [
                    'entity'   => $_category,
                    'preview'  => $_preview,
                    'title'    => $_category->title,
                    'alias'    => $_category->generate_url,
                    'children' => $_children
                ];
            }
        });

        return $_response;
    }

    public function getBreadcrumb()
    {
        $_response = NULL;
        if ($_category = $this->_parent) {
            $_categories = Category::orderBy('parent_id')
                ->where('status', 1)
                ->orderBy('sort')
                ->remember(REMEMBER_LIFETIME)
                ->with([
                    '_parent',
                    '_children'
                ])
                ->get([
                    'id',
                    'title',
                    'breadcrumb_title',
                    'parent_id'
                ])
                ->keyBy('id');
            if ($_categories->isNotEmpty()) {
                $_tree = collect([]);
                $_categories->each(function ($_item) use (&$_tree, $_categories) {
                    if ($_item->parent_id) return FALSE;
                    $_data = [
                        'entity'   => $_item,
                        'parent'   => NULL,
                        'children' => [],
                    ];
                    self::tree_category_parents($_tree, $_categories, $_data);
                    $_tree->put($_item->id, $_data);
                });
                $_current_category = $_tree->get($_category->id);
                if ($_current_category['parent']) {
                    $_parent_category_level_1 = $_tree->get($_current_category['parent']->id);
                    if ($_parent_category_level_1['children']) $_response[1] = array_merge(($_response[1] ?? []), $_parent_category_level_1['children']);
                    if ($_parent_category_level_1['parent']) {
                        $_parent_category_level_2 = $_tree->get($_parent_category_level_1['parent']->id);
                        if ($_parent_category_level_2['children']) $_response[2] = array_merge(($_response[2] ?? []), $_parent_category_level_2['children']);
                        if ($_parent_category_level_2['parent']) {
                            $_parent_category_level_3 = $_tree->get($_parent_category_level_2['parent']->id);
                            if ($_parent_category_level_3['children']) $_response[3] = array_merge(($_response[3] ?? []), $_parent_category_level_3['children']);
                        } else {
                            $_response[3] = array_merge(($_response[3] ?? []), [$_parent_category_level_2['entity']]);
                        }
                    } else {
                        $_response[2] = array_merge(($_response[2] ?? []), [$_parent_category_level_1['entity']]);
                    }
                } else {
                    $_response[1] = array_merge(($_response[1] ?? []), [$_category]);
                }
                if ($_response) krsort($_response);
            }
        }

        return $_response;
    }

    public static function tree_category_parents(&$_response, $categories, &$parent = NULL)
    {
        $categories->each(function ($_item) use (&$_response, $categories, &$parent) {
            $_data = NULL;
            if ($_item->parent_id == $parent['entity']->id) {
                $_data = [
                    'entity'   => $_item,
                    'parent'   => $parent['entity'],
                    'children' => [],
                ];
                $parent['children'] = array_merge($parent['children'], [
                    $_item
                ]);
                self::tree_category_parents($_response, $categories, $_data);
                $_response->put($_item->id, $_data);
            }
        });
    }

    public static function getSearchCategory()
    {
        return Cache::remember('search_category', (REMEMBER_LIFETIME * 24), function () {
            $_response = collect([
                'all' => trans('shop.labels.categories')
            ]);
            $_categories = self::tree_parents();
            $_categories->each(function ($_item) use (&$_response) {
                $_count_parents = count($_item['parents']);
                $_title = NULL;
                if ($_count_parents) {
                    if ($_count_parents == 1) $_title .= '&nbsp;-&nbsp;';
                    if ($_count_parents == 2) $_title .= '&nbsp;&nbsp;-&nbsp;';
                    if ($_count_parents == 3) $_title .= '&nbsp;&nbsp;&nbsp;-&nbsp;';
                    if ($_count_parents == 4) $_title .= '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;';
                    if ($_count_parents == 5) $_title .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;';
                }
                $_title .= $_item['title'];
                $_response->put($_item['id'], $_title);
            });

            return $_response;
        });
    }

    public function getSchemaAttribute()
    {
        $_response = [
            "@context"    => "https://schema.org",
            "@type"       => "Product",
            "name"        => $this->title,
            "description" => $this->body,
            "offers"      => [
                "@type"         => "AggregateOffer",
                "lowPrice"      => $this->schemaData['lowPrice'],
                "highPrice"     => $this->schemaData['highPrice'],
                "priceCurrency" => 'UAH',
                "offerCount"    => $this->schemaData['offerCount'],
            ],
            "image"       => ""
        ];

        return json_encode($_response);
    }

}

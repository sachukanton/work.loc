<?php

    namespace App\Models\Shop;

    use App\Library\BaseModel;
    use App\Library\Frontend;
    use App\Models\Form\Review;
    use Illuminate\Http\Request;
    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\Facades\View;

    class Brand extends BaseModel
    {

        protected $table = 'shop_brands';
        protected $guarded = [];
        public $translatable = [
            'title',
            'sub_title',
            'breadcrumb_title',
            'body',
            'meta_title',
            'meta_keywords',
            'meta_description',
        ];

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Relationships
         */
        public function _products()
        {
            return $this->hasMany(Product::class, 'brand_id', 'id')
                ->with([
                    '_alias',
                    '_param_items',
                    '_preview',
                    '_brand',
                    '_prices'
                ]);
        }

        public function _categories()
        {
            $_response = NULL;
            $_brand_categories = Category::from('shop_categories as c')
                ->leftJoin('shop_product_category as pc', 'pc.category_id', '=', 'c.id')
                ->leftJoin('shop_products as p', 'p.id', '=', 'pc.model_id')
                ->where('p.brand_id', $this->id)
                ->where('p.status', 1)
                ->orderBy('c.sort')
                ->distinct()
                ->remember(REMEMBER_LIFETIME)
                ->pluck('c.id', 'c.id');
            if ($_brand_categories) {
                $_categories = Category::orderBy('parent_id')
                    ->orderBy('sort')
                    ->with([
                        '_alias'
                    ])
                    ->remember(REMEMBER_LIFETIME)
                    ->get([
                        'id',
                        'title',
                        'menu_title',
                        'parent_id'
                    ])
                    ->keyBy('id');
                if ($_categories->isNotEmpty()) {
                    $_response = [];
                    $_categories->each(function ($_item) use (&$_response, $_categories, $_brand_categories) {
                        if ($_item->parent_id) return FALSE;
                        $_children_use = FALSE;
                        if ($_children = self::tree_parents_item($_categories, $_brand_categories, $_item->id)) {
                            $_children_use = array_filter($_children, function ($_innerArray) {
                                return ($_innerArray['use'] == TRUE);
                            });
                            $_children_use = count($_children_use) ? TRUE : FALSE;
                        }
                        $_data = [
                            'id'       => $_item->id,
                            'title'    => $_item->getTranslation('menu_title', app()->getLocale()) ? : $_item->getTranslation('title', app()->getLocale()),
                            'alias'    => $_item->generate_url,
                            'children' => $_children,
                            'use'      => $_children_use ? $_children_use : $_brand_categories->has($_item->id)
                        ];
                        $_response[$_item->id] = $_data;
                    });
                }
            }

            return $_response;
        }

        public static function tree_parents_item(&$categories, $brand_categories, $parent = NULL)
        {
            $_response = NULL;
            $categories->each(function ($_item) use (&$_response, $categories, $brand_categories, $parent) {
                if ($parent && $_item->parent_id == $parent) {
                    $_children_use = FALSE;
                    if ($_children = self::tree_parents_item($categories, $brand_categories, $_item->id)) {
                        $_children_use = array_filter($_children, function ($_innerArray) {
                            return ($_innerArray['use'] == TRUE);
                        });
                        $_children_use = count($_children_use) ? TRUE : FALSE;
                    }
                    $_data = [
                        'id'       => $_item->id,
                        'title'    => $_item->getTranslation('menu_title', app()->getLocale()) ? : $_item->getTranslation('title', app()->getLocale()),
                        'alias'    => $_item->generate_url,
                        'children' => $_children,
                        'use'      => $_children_use ? $_children_use : $brand_categories->has($_item->id)
                    ];
                    $_response[$_item->id] = $_data;
                }
            });

            return $_response;
        }

        public function _load($view_mode = 'full')
        {
            if (!$this->status) $this->style_class = $this->style_class ? "{$this->style_class} uk-page-not-published" : 'uk-page-not-published';
            switch ($this->type) {
                default:
                    break;
            }
            switch ($view_mode) {
                default:
                    $this->body = content_render($this);
                    $this->relatedMedias = $this->_files_related()->wherePivot('type', 'medias')->get();
                    $this->relatedFiles = $this->_files_related()->wherePivot('type', 'files')->get();
                    break;
            }

            return $this;
        }

        public function _render($options = NULL)
        {
            global $wrap;
            $_view = $options['view_mode'] ?? NULL;
            $this->_load($_view);
            $_set_wrap = [
                'seo.title'         => $this->meta_title ? : $this->title,
                'seo.keywords'      => $this->meta_keywords,
                'seo.description'   => $this->meta_description,
                'seo.robots'        => $this->meta_robots,
                'seo.last_modified' => $this->last_modified,
                'page.title'        => $this->title,
                'page.style_id'     => $this->style_id,
                'page.style_class'  => $this->style_class ? [$this->style_class] : NULL,
                'page.breadcrumb'   => breadcrumb_render(['entity' => $this]),
            ];
            $_page_number = current_page();
            $_items = $this->_products()
                ->leftJoin('shop_product_prices as pp', 'pp.product_id', '=', 'shop_products.id')
                ->select([
                    'shop_products.id',
                    'shop_products.sku',
                    'shop_products.title',
                    'shop_products.preview_fid',
                    'shop_products.mark_hit',
                    'shop_products.mark_new',
                    'shop_products.sort',
                    'shop_products.status',
                    'shop_products.brand_id',
                ])
                ->where('shop_products.status', 1)
                ->orderByRaw("case when `pp`.`status` = 'not_available' then 2 when `pp`.`status` = 'under_order' then 1 end asc")
                ->orderBy('shop_products.sort');
            if ($_page_number) {
                Paginator::currentPageResolver(function () use ($_page_number) {
                    return $_page_number;
                });
            }
            $this->_categories = NULL;
            $this->_items = $_items->remember(REMEMBER_LIFETIME)
                ->paginate(12, ['shop_products.id'])
                ->onEachSide(1);
            if ($this->_items->isNotEmpty()) {
                $this->_categories = $this->_categories();
                $this->_items->getCollection()->transform(function ($_item) {
                    if (method_exists($_item, '_load')) $_item->_load('teaser');

                    return $_item;
                });
            }
            if ($this->_items->isEmpty() && $_page_number) abort(404);
            if ($_page_number) $_set_wrap['seo.robots'] = 'noindex, follow';
            if ($this->_items->isNotEmpty() && $this->_items->hasMorePages()) {
                $_page_number = $_page_number ? : 1;
                $_page_number++;
                $_current_url = $wrap['seo']['url_alias'];
                $_current_url_query = $wrap['seo']['url_query'];
                $_url = trim($_current_url, '/') . "/page-{$_page_number}";
                $_next_page_link = _u($_url) . $_current_url_query;
                $_set_wrap['seo.link_next'] = $_next_page_link;
            }
            $this->setWrap($_set_wrap);
            $_template = [
                "frontend.{$this->deviceTemplate}.shops.brand_{$this->id}",
                "frontend.{$this->deviceTemplate}.shops.brand",
                "frontend.default.shops.brand_{$this->id}",
                "frontend.default.shops.brand",
                'backend.base.brand'
            ];
            if (isset($options['view']) && $options['view']) array_unshift($_template, $options['view']);
            $this->template = $_template;

            return $this;
        }

        public function _render_ajax(Request $request)
        {
            global $wrap;
            $_load_more = $request->has('load_more') ? TRUE : FALSE;
            if ($_load_more == FALSE) {
                return [
                    'commands' => [
                        [
                            'command' => 'UK_notification',
                            'options' => [
                                'text'   => trans('frontend.notice_an_error_has_occurred'),
                                'status' => 'danger',
                            ]
                        ]
                    ]
                ];
            }
            $_page_number = current_page();
            $_items = $this->_products()
                ->leftJoin('shop_product_prices as pp', 'pp.product_id', '=', 'shop_products.id')
                ->select([
                    'shop_products.id',
                    'shop_products.sku',
                    'shop_products.title',
                    'shop_products.preview_fid',
                    'shop_products.mark_hit',
                    'shop_products.mark_new',
                    'shop_products.sort',
                    'shop_products.status',
                    'shop_products.brand_id',
                ])
                ->where('shop_products.status', 1)
                ->orderByRaw("case when `pp`.`status` = 'not_available' then 2 when `pp`.`status` = 'under_order' then 1 end asc")
                ->orderBy('shop_products.sort');
            if ($_page_number) {
                Paginator::currentPageResolver(function () use ($_page_number) {
                    return $_page_number;
                });
            }
            $this->_categories = NULL;
            $this->_items = $_items->remember(REMEMBER_LIFETIME)
                ->paginate(12, ['shop_products.id']);
            if ($this->_items->isNotEmpty()) {
                $this->_categories = $this->_categories();
                $this->_items->getCollection()->transform(function ($_item) {
                    if (method_exists($_item, '_load')) $_item->_load('teaser');

                    return $_item;
                });
            }
            //            if ($this->_items->isNotEmpty() && $this->_items->hasMorePages()) {
            //                $_page_number = $_page_number ? : 1;
            //                $_page_number++;
            //                $_current_url = $wrap['seo']['url_alias'];
            //                $_current_url_query = $wrap['seo']['url_query'];
            //                $_url = trim($_current_url, '/') . "/page-{$_page_number}";
            //                $_next_page_link = _u($_url) . $_current_url_query;
            //                $_set_wrap['seo.link_next'] = $_next_page_link;
            //            }
            $_items_output = NULL;
            $_item_template = [
                "frontend.{$this->deviceTemplate}.shops.product_teaser_category_{$this->id}",
                "frontend.{$this->deviceTemplate}.shops.product_teaser",
                "frontend.default.shops.product_teaser_category_{$this->id}",
                "frontend.default.shops.product_teaser",
                'backend.base.shop_product_teaser'
            ];
            foreach ($this->_items as $_item) {
                $_items_output .= View::first($_item_template, compact('_item'))
                    ->render(function ($view, $_content) {
                        return clear_html($_content);
                    });
            }
            $commands['commands'][] = [
                'command' => 'append',
                'options' => [
                    'target' => '#uk-items-list',
                    'data'   => clear_html($_items_output)
                ]
            ];
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-pagination',
                    'data'   => clear_html($this->_items->links('backend.base.pagination'))
                ]
            ];
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-body',
                    'data'   => ''
                ]
            ];

            return $commands;
        }

    }

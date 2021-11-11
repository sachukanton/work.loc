<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use App\Library\Frontend;
use App\Models\Form\Review;
use App\Models\Seo\SearchIndex;
use App\Models\Seo\UrlAlias;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class Product extends BaseModel
{

    protected $table = 'shop_products';
    protected $guarded = [];
    public $paramOptions;
    public $metaMask;
    public $priceId;
    public $basket;
    public $selected_modify;
    public $selected_category;
    public $modification_items;
    public $js_modification_items;
    public $is_spicy = NULL;
    public $weight;
    public $translatable = [
        'title',
        'sub_title',
        'breadcrumb_title',
        'teaser',
        'body',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'specifications'
    ];
    protected $attributes = [
        'video_preview_fid' => NULL,
        'video_fid'         => NULL,
        'video_youtube'     => NULL,
    ];

    const ID_MARK_SPICY = 4;
    const ID_WEIGHT = 3;
    const ID_MARKS = 1;

    public function __construct()
    {
        parent::__construct();
    }

    public function getSlideShowAttribute()
    {
        $_slide_show = NULL;
        $_slide_show_index = 0;
        //        if ($this->preview_fid) {
        //            $_slide_show['slide'][$_slide_show_index] = _l(image_render($this->_preview, 'slideShow_350_350', ['attributes' => ['']]), image_render($this->_preview, 'slideLightBox_1024', [
        //                'only_way'       => TRUE,
        //                'no_last_modify' => TRUE,
        //            ]), [
        //                'attributes' => [
        //                    'data-caption' => $this->_preview->title
        //                ]
        //            ]);
        //            $_slide_show['nav'][$_slide_show_index] = image_render($this->_preview, 'slideShow_70_70');
        //            $_slide_show_index++;
        //        }
        if ($this->full_fid) {
            $_device_type = wrap()->get('device.type');
            if ($_device_type == 'pc') {
                $_slide_show['slide'][$_slide_show_index] = image_render($this->_preview_full, 'slideShow_800_800', ['attributes' => ['uk-img' => TRUE]]);
            } else {
                $_slide_show['slide'][$_slide_show_index] = image_render($this->_preview_full, 'slideShow_350_350', ['attributes' => ['uk-img' => TRUE]]);
            }

            $_slide_show_index++;
        }
        //        if ($this->mobile_fid) {
        //            $_slide_show['slide'][$_slide_show_index] = _l(image_render($this->_preview_mobile, 'slideShow_350_350', ['attributes' => ['']]), image_render($this->_preview_mobile, 'slideLightBox_1024', [
        //                'only_way'       => TRUE,
        //                'no_last_modify' => TRUE,
        //            ]), [
        //                'attributes' => [
        //                    'data-caption' => $this->_preview_mobile->title
        //                ]
        //            ]);
        //            $_slide_show['nav'][$_slide_show_index] = image_render($this->_preview_mobile, 'slideShow_70_70');
        //            $_slide_show_index++;
        //        }
        if ($this->relatedMedias) {
            foreach ($this->relatedMedias as $_media) {
                $_device_type = wrap()->get('device.type');
                if ($_device_type == 'pc') {
                    $_slide_show['slide'][$_slide_show_index] = image_render($_media, 'slideShow_800_800', ['attributes' => ['uk-img' => TRUE]]);
                } else {
                    $_slide_show['slide'][$_slide_show_index] = image_render($_media, 'slideShow_350_350', ['attributes' => ['uk-img' => TRUE]]);
                }
                $_slide_show_index++;
            }
        }
        if ($this->video_fid) {
            $_slide_show['slide'][$_slide_show_index] = _l(image_render($this->_video_preview, 'slideShow_350_350', ['attributes' => ['']]) . '<span uk-icon="icon: play_circle_outline; ratio: 3.5" class="uk-position-absolute uk-position-center uk-text-color-red"></span>', "/storage/{$this->_video->filename}");
            $_slide_show['nav'][$_slide_show_index] = image_render($this->_video_preview, 'slideShow_70_70') . '<span uk-icon="icon: play_circle_outline; ratio: 1.3" class="uk-position-absolute uk-position-center uk-text-color-red"></span>';
            $_slide_show_index++;
        }
        if ($this->video_youtube) {
            $_slide_show['slide'][$_slide_show_index] = _l(image_render(NULL, 'slideShow_350_350', [
                    'outside_file' => [
                        'path' => "https://img.youtube.com/vi/{$this->video_youtube}/0.jpg",
                        'name' => "{$this->video_youtube}.jpg"
                    ],
                    'attributes'   => ['uk-cover']
                ]) . '<span uk-icon="icon: play_circle_outline; ratio: 3.5" class="uk-position-absolute uk-position-center uk-text-color-red"></span>', "https://www.youtube.com/watch?v={$this->video_youtube}");
            $_slide_show['nav'][$_slide_show_index] = image_render(NULL, 'slideShow_70_70', [
                    'outside_file' => [
                        'path' => "https://img.youtube.com/vi/{$this->video_youtube}/0.jpg",
                        'name' => "{$this->video_youtube}.jpg"
                    ],
                ]) . '<span uk-icon="icon: play_circle_outline; ratio: 1.3" class="uk-position-absolute uk-position-center uk-text-color-red"></span>';
            $_slide_show_index++;
        }

        return $_slide_show;
    }

    public function getMarkAttribute()
    {
        $_response = NULL;
        $_location = NULL;
        if ($this->mark_hit) $_response[] = 'hit';
        if ($this->mark_new) $_response[] = 'new';
        if ($this->mark_recommended_front) $_response[] = 'recommended';
        if ($this->mark_recommended_checkout) $_response[] = 'recommended';
        $_price = $this->_price;
        if ($_price->discount_price) $_response[] = 'discount';

        return $_response;
    }

    public function getSpecificationAttribute()
    {
        return is_json($this->specifications) ? json_decode($this->specifications) : NULL;
    }

    public function getSchemaAttribute()
    {
        global $wrap;
        $_response = [
            "@context"    => "https://schema.org",
            "@type"       => "Product",
            "name"        => $this->title,
            "description" => strip_tags($this->body),
            "brand"       => $this->_brand->title,
            "sku"         => $this->model ? : $this->sku,
            "offers"      => [
                "@type"           => "Offer",
                "availability"    => $this->_price->_quantity->quantity ? "http://schema.org/InStock" : "https://schema.org/OutOfStock",
                "itemCondition"   => "https://schema.org/NewCondition",
                "price"           => $this->_price->base_price,
                "priceCurrency"   => 'UAH',
                "priceValidUntil" => Carbon::now()->format('Y-m-d'),
                "url"             => "{$wrap['seo']['base_url']}{$this->generate_url}",
            ],
            "image"       => $this->preview_fid ? $wrap['seo']['base_url'] . $this->_preview_asset('productTeaser_300_300', [
                    'only_way'       => TRUE,
                    'no_last_modify' => TRUE
                ]) : NULL
        ];

        return json_encode($_response);
    }

    public function _brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id')
            ->select([
                'id',
                'title',
                'status',
                'sort',
            ])
            ->with([
                '_alias'
            ])
            ->withDefault();
    }

    public function _price()
    {
        return $this->hasOne(Price::class, 'product_id')
            ->where('default', 1);
        //            ->with([
        //                '_quantity'
        //            ]);
    }

    public function _prices()
    {
        return $this->hasMany(Price::class, 'product_id');
        //            ->with([
        //                '_quantity'
        //            ]);
    }

    public function _categories_products()
    {
        return self::leftJoin('shop_product_category', 'shop_product_category.model_id', '=', 'shop_products.id')
            ->whereIn('shop_product_category.category_id', $this->_category->pluck('id'))
            ->whereRaw(DB::raw('shop_products.modify = shop_products.id'))
            ->pluck('title', 'id')
            ->prepend('- Выбрать -', '');
    }

    public function _modifications()
    {
        return self::where('modify', $this->modify)
            ->where('id', '<>', $this->modify)
            ->orderBy('sort')
            ->get([
                'id',
                'sku',
                'title',
                'sort',
                'status',
                'modify'
            ]);
    }

    public function _parent_modify()
    {
        return self::where('id', $this->modify)
            ->first();
    }

    public function _mod()
    {
        return $this->hasMany(self::class, 'modify', 'modify')
            ->with([
                '_param_items' => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
                '_alias'       => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
                '_preview'     => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
                '_price'       => function ($q) {
                    return $q->remember(REMEMBER_LIFETIME * 24);
                },
            ])
            ->where('status', 1)
            ->select([
                'id',
                'sku',
                'modify',
                'title',
                'sub_title',
                'preview_fid',
                'mobile_fid',
                'sort',
                'status',
                'mark_new',
                'mark_hit',
                'mark_recommended_front',
                'mark_recommended_checkout',
                'double_card',
                //                DB::raw('(select concat(`shop_categories`.`id`, "::", `shop_categories`.`modify_param`, "::", `shop_categories`.`use_spicy`) from `shop_categories` left join `shop_product_category` on `shop_product_category`.`category_id` = `shop_categories`.`id` where `shop_product_category`.`model_id` = `shop_products`.`id`) as category_modify_param'),
                DB::raw('(SELECT GROUP_CONCAT((CASE WHEN `shop_categories`.`id` IS NULL THEN "::" ELSE CONCAT(`shop_categories`.`id` , "::") END), (CASE WHEN `shop_categories`.`modify_param` IS NULL THEN "::" ELSE CONCAT(`shop_categories`.`modify_param`) END)) from `shop_categories` left join `shop_product_category` on `shop_product_category`.`category_id` = `shop_categories`.`id` where `shop_product_category`.`model_id` = `shop_products`.`id`) as category_modify_param')
            ]);
    }

    //    public function _min_price_in_pharmacy()
    //    {
    //        return $this->hasOne(MinPriceInPharmacy::class, 'product_id');
    //    }

    //    public function _availability()
    //    {
    //        return $this->hasMany(Price::class, 'product_id')
    //            ->leftJoin('pharm_pharmacies as p', 'p.id', '=', 'shop_product_prices.pharmacy_id')
    //            ->leftJoin('pharm_cities as c', 'c.id', '=', 'p.pharm_city_id')
    //            ->with([
    //                '_pharmacy',
    //                '_quantity',
    //            ])
    //            ->select([
    //                'shop_product_prices.id',
    //                'shop_product_prices.price',
    //                'shop_product_prices.old_price',
    //                'shop_product_prices.discount_price',
    //                'shop_product_prices.base_price',
    //                'shop_product_prices.status',
    //                'shop_product_prices.product_id',
    //                'shop_product_prices.pharmacy_id',
    //                'shop_product_prices.part',
    //                'shop_product_prices.multiplicity',
    //                'shop_product_prices.quantity_id',
    //                'c.id as pharmacy_city',
    //                'c.sort as pharmacy_city_sort',
    //            ])
    //            ->where('shop_product_prices.default', '<>', 1)
    //            ->whereIn('shop_product_prices.status', [
    //                'in_stock',
    //                'not_limited'
    //            ])
    //            ->orderBy('c.sort')
    //            ->orderBy('shop_product_prices.base_price');
    //    }

    public function _analogues()
    {
        $_response = collect([]);
        $_analog_group = Analog::where('product_id', $this->id)
            ->value('group_1c');
        if ($_analog_group) {
            $_response = self::from('shop_products as p')
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
                    '_prices'      => function ($q) {
                        $q->remember(REMEMBER_LIFETIME);
                    },
                    '_brand'       => function ($q) {
                        $q->remember(REMEMBER_LIFETIME);
                    },
                ])
                ->leftJoin('shop_product_analog as a', 'a.product_id', '=', 'p.id')
                ->where('a.group_1c', $_analog_group)
                ->where('p.status', 1)
                ->where('p.id', '<>', $this->id)
                ->select([
                    'p.id',
                    'p.sku',
                    'p.model',
                    'p.title',
                    'p.preview_fid',
                    'p.mobile_fid',
                    'p.full_fid',
                    'p.mark_hit',
                    'p.mark_new',
                    'p.mark_recommended_front',
                    'p.mark_recommended_checkout',
                    'p.sort',
                    'p.status',
                    'p.brand_id',
                ])
                ->remember(REMEMBER_LIFETIME)
                ->distinct()
                ->get();
        }

        return $_response;
    }

    public function _param_items()
    {
        return $this->morphToMany(ParamItem::class, 'model', 'shop_product_param')
            ->withPivot([
                'name',
                'value',
                'text',
            ])
            ->with([
                '_param',
                '_icon'
            ]);
    }

    public function _product_count_buy()
    {
        return $this->hasMany(OrderProduct::class, 'product_id', 'id')
            ->select(['id']);
        //            ->with([
        //                '_product'
        //            ]);
    }

    public function _product_related($type = 'related', $control = FALSE)
    {
        if ($control) {
            $_response = self::from('shop_products as p')
                ->join('shop_product_related as pr', 'pr.entity_id', 'p.id')
                ->where('pr.product_id', $this->id)
                ->where('pr.entity_type', self::class)
                ->where('pr.type', $type)
                ->distinct()
                ->get([
                    'p.*',
                    'pr.id as key_by'
                ])
                ->keyBy('key_by');
            $_related_categories = Category::from('shop_categories as c')
                ->join('shop_product_related as pr', 'pr.entity_id', 'c.id')
                ->where('pr.product_id', $this->id)
                ->where('pr.entity_type', Category::class)
                ->where('pr.type', $type)
                ->distinct()
                ->get([
                    'c.*',
                    'pr.id as key_by'
                ])
                ->keyBy('key_by');
            if ($_related_categories->isNotEmpty()) {
                $_related_categories->each(function ($_category, $_key) use (&$_response) {
                    $_response->put($_key, $_category);
                });
            }
        } else {
            $_response = self::from('shop_products as p')
                ->join('shop_product_related as pr', 'pr.entity_id', 'p.id')
                ->where('pr.product_id', $this->id)
                ->where('pr.entity_type', self::class)
                ->where('pr.type', $type)
                ->where('p.status', 1)
                ->distinct()
                ->with([
                    '_alias',
                    '_param_items',
                    '_preview',
                    '_brand',
                ])
                ->get([
                    'p.*'
                ])
                ->keyBy('id');
            $_related_categories = Category::from('shop_categories as c')
                ->join('shop_product_related as pr', 'pr.entity_id', 'c.id')
                ->where('pr.product_id', $this->id)
                ->where('pr.entity_type', Category::class)
                ->where('c.status', 1)
                ->where('pr.type', $type)
                ->distinct()
                ->with([
                    '_children'
                ])
                ->get([
                    'c.id',
                    'c.parent_id',
                ]);
            if ($_related_categories->isNotEmpty()) {
                $_categories = collect([]);
                $_related_categories->each(function ($_category) use (&$_categories) {
                    $_categories->put($_category->id, $_category->id);
                    $_children = $_category->all_children;
                    if ($_children->isNotEmpty()) {
                        $_children->each(function ($_child) use (&$_categories) {
                            if ($_child->status) $_categories->put($_child->id, $_child->id);
                        });
                    }
                });
                $_response_2 = self::from('shop_products as p')
                    ->join('shop_product_category as pc', 'pc.model_id', 'p.id')
                    ->whereIn('pc.category_id', $_categories)
                    ->where('p.status', 1)
                    ->distinct()
                    ->with([
                        '_alias',
                        '_param_items',
                        '_preview',
                        '_brand',
                    ])
                    ->get([
                        'p.*'
                    ]);
                if ($_response_2->isNotEmpty()) {
                    $_response_2->each(function ($_product) use (&$_response) {
                        if (!$_response->has($_product->id)) $_response->put($_product->id, $_product);
                    });
                }
            }
        }
        if ($_response->isNotEmpty()) {
            $_response->transform(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });
        }

        return $_response;
    }

    public function _product_consist($control = FALSE)
    {
        if ($control) {
            $_response = self::from('shop_products as p')
                ->join('shop_product_consists as pr', 'pr.entity_id', 'p.id')
                ->where('pr.product_id', $this->id)
                ->where('pr.entity_type', self::class)
                ->distinct()
                ->get([
                    'p.*',
                    'pr.id as key_by',
                    'pr.quantity as quantity'
                ])
                ->keyBy('key_by');
        } else {
            $_response = self::from('shop_products as p')
                ->join('shop_product_consists as pr', 'pr.entity_id', 'p.id')
                ->where('pr.product_id', $this->id)
                ->where('pr.entity_type', self::class)
                // ->where('p.status', 1)
                ->distinct()
                ->with([
                    '_alias',
                    '_param_items',
                    '_preview',
                    '_brand',
                ])
                ->get([
                    'p.*',
                    'pr.id as key_by',
                    'pr.quantity as quantity'
                ])
                ->keyBy('id');
        }
        if ($_response->isNotEmpty()) {
            $_response->transform(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });
        }

        return $_response;
    }

    public function _render_price(Basket $basket = NULL)
    {
        //        $_basket = is_null($basket) ? app('basket') : $basket;
        $_location = NULL;
        $_response = NULL;
        $_price = $this->_price;
        //        if ($this->priceId) {
        //            $_price_id = $this->priceId;
        //            $__price = $this->_prices->filter(function ($_item) use ($_price_id) {
        //                return $_item->id == $_price_id;
        //            })->first();
        //            if ($__price) $_price = $__price;
        //        }
        $_quantity = 100; //$_price->count_in_stock;
        $_basket_balance = 0; //$_basket->product_balance($_price);
        $_quantity -= $_basket_balance;
        $_view_available = '<div class="card-not-available">' . trans('shop.product.not_available') . '</div>';
        $_view_available_text = trans('shop.product.not_available');
        $_view_available_status = 'not_available';
        if ($_basket_balance && $_quantity < 1) {
            $_view_available = '<div class="uk-text-color-amber darken-4 uk-text-light">' . trans('shop.product.everything_is_already_in_basket') . '</div>';
            $_view_available_text = trans('shop.product.everything_is_already_in_basket');
            $_view_available_status = 'everything_is_already_in_basket';
        }
        $_view = TRUE;
        $_price->status = 'not_limited';
        //        if ($_price->status == 'not_available') $_view = FALSE;
        //        if ($_price->status == 'in_stock' && !$_quantity) $_view = FALSE;
        //        if ($_price->status == 'in_stock' && $_quantity) {
        //            if ($_quantity < 5) {
        //                $_view_available = '<div class="product-ends">' . trans('shop.product.ends') . '</div>';
        //                $_view_available_text = trans('shop.product.ends');
        //                $_view_available_status = 'ends';
        //            } else {
        //            $_view_available = '<div class="product-available">' . trans('shop.product.are_available') . '</div>';
        //            $_view_available_text = trans('shop.product.are_available');
        //            $_view_available_status = 'are_available';
        //            }
        //        }
        if ($_price->status == 'not_limited') {
            $_view_available = '<div class="uk-text-success uk-text-light">' . trans('shop.product.are_available') . '</div>';
            $_view_available_text = trans('shop.product.are_available');
            $_view_available_status = 'are_available';
        }
        //        if ($_price->status == 'under_order') {
        //            $_view_available = '<div class="card-under-order">' . trans('shop.product.under_order') . '</div>';
        //            $_view_available_text = trans('shop.product.under_order');
        //            $_view_available_status = 'under_order';
        //            $_view = FALSE;
        //        }
        $_render_price = transform_price(($_price->price ? : 0));
        $_render_old_price = transform_price(($_price->old_price ? : 0));
        $_render_discount_price = transform_price(($_price->discount_price ? : 0));
        //        $_render_min_price = transform_price(0);
        //        $_pharmacy_min_price_exist = FALSE;
        //        if ($_view == FALSE) {
        //            $_min_in_pharmacy = $this->_min_price_in_pharmacy;
        //            if (isset($_min_in_pharmacy->base_price) && $_min_in_pharmacy->base_price > 0) {
        //                $_render_min_price = transform_price($_min_in_pharmacy->base_price);
        //                $_view_available = '<div class="uk-text-success uk-text-light">' . trans('shop.product.are_available_in_pharmacy') . '</div>';
        //                $_view_available_text = trans('shop.product.are_available_in_pharmacy');
        //                $_view_available_status = 'are_available_in_pharmacy';
        ////                $_pharmacy_min_price_exist = TRUE;
        //            }
        //        }
        if ($_price->discount_price) {
            $_price_diff = [
                $_render_price,
                $_render_discount_price,
            ];
        } elseif ($_price->old_price) {
            $_price_diff = [
                $_render_old_price,
                $_render_price,
            ];
        } else {
            $_price_diff = [$_render_price];
        }
        //        $_multiplicity = $this->multiplicity ? : 1;
        //        $_count_in_stock_whole = floor($_quantity);
        //        $_count_in_stock_part = floor($_quantity / (1 / $_multiplicity));

        return [
            'id'                    => $_price->id,
            'status'                => $_price->status,
            //            'count_in_stock'        => [
            //                'whole' => $_count_in_stock_whole,
            //                'part'  => $_count_in_stock_part
            //            ],
            'count_in_basket'       => $_basket_balance,
            //            'quantity_max'          => [
            //                'whole' => floor($_count_in_stock_whole + ($_basket->data[$_price->id] ?? 0)),
            //                'part'  => floor($_count_in_stock_part + ($_basket->data[$_price->id] ?? 0))
            //            ],
            'view_price'            => $_view,
            'view_available'        => $_view_available,
            'view_available_text'   => $_view_available_text,
            'view_available_status' => $_view_available_status,
            'view'                  => $_price_diff,
            'price'                 => $_render_price,
            'old_price'             => $_render_old_price,
            'discount_price'        => $_render_discount_price,
            //            'pharmacy_min_price_exist' => $_pharmacy_min_price_exist,
            //            'pharmacy_min_price'       => $_render_min_price,
        ];
    }

    public function _category()
    {
        return $this->morphToMany(Category::class, 'model', 'shop_product_category')
            ->with([
                '_params',
                '_tmp_meta_tags'
            ])
            ->select([
                'id',
                'parent_id',
                'title'
            ]);
    }

    public function _view_list()
    {
        return $this->hasOne(ViewList::class, 'product_id');
    }

    public static function shop_product_view_list_recommended_front()
    {
        $_response = NULL;
        $_products = ViewList::get('recommended_front');

        if ($_products->isNotEmpty()) {
            if ($_products->count() > ViewList::PRODUCT_VIEW_LIST_MAX_ITEM) $_products = $_products->random(ViewList::PRODUCT_VIEW_LIST_MAX_ITEM);


            $_products->transform(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });

            return [
                'object' => View::first([
                    //                    "frontend.default.load_entities.view_lists_recommended_front_product",
                    'frontend.default.load_entities.view_lists_product'
                ], [
                    '_title' => trans('shop.titles.view_list_recommended'),
                    '_items' => $_products
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                })
            ];
        }

        return $_response;
    }

    public static function shop_product_view_list_new()
    {
        $_response = NULL;
        $_products = ViewList::get('new');

        if ($_products->isNotEmpty()) {
            if ($_products->count() > ViewList::PRODUCT_VIEW_LIST_MAX_ITEM) $_products = $_products->random(ViewList::PRODUCT_VIEW_LIST_MAX_ITEM);


            $_products->transform(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });

            return [
                'object' => View::first([
                    //                    "frontend.default.load_entities.view_lists_recommended_front_product",
                    'frontend.default.load_entities.view_lists_product'
                ], [
                    '_title' => trans('shop.titles.view_list_new'),
                    '_items' => $_products
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                })
            ];
        }

        return $_response;
    }

    public static function shop_product_view_list_recommended_checkout()
    {
        $_response = NULL;
        $_products = ViewList::get('recommended_checkout');

        if ($_products->isNotEmpty()) {
            if ($_products->count() > ViewList::PRODUCT_VIEW_LIST_MAX_ITEM) $_products = $_products->random(ViewList::PRODUCT_VIEW_LIST_MAX_ITEM);


            $_products->transform(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });

            return [
                'object' => View::first([
                    "frontend.default.load_entities.view_lists_recommended_checkout_product",
                ], [
                    '_title' => trans('shop.titles.view_list_recommended'),
                    '_items' => $_products
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                })
            ];
        }

        return $_response;
    }

    public function getParamItemsFields($categories = NULL)
    {
        if ($categories) {
            $_categories = Category::whereIn('id', $categories)
                ->with([
                    '_params'
                ])
                ->get([
                    'id',
                    'parent_id',
                    'title'
                ]);
        } else {
            $_categories = $this->_category;
        }
        $_params = collect([]);
        $_product_param_items = $this->_param_items()
            ->withPivot([
                'value',
                'text'
            ])
            ->get([
                'id',
                'param_id'
            ])
            ->groupBy('param_id');
        foreach ($_categories as $_category) {
            foreach ($_category->_params as $_param) {
                if (!$_params->has($_param->id)) {
                    $_selected = NULL;
                    switch ($_param->type) {
                        case 'select':
                            $_inFilter = isset($_param->pivot->visible_in_filter) && $_param->pivot->visible_in_filter ? 1 : 0;
                            $_isMultiple = isset($_param->pivot->type) && $_param->pivot->type == 'multiple' ? TRUE : FALSE;
                            $_options = collect([]);
                            if ($_param->_items->isNotEmpty()) {
                                $_options = $_param->_items->keyBy('id')->map(function ($_item) {
                                    return $_item->getTranslation('title', $this->defaultLocale);
                                });
                            }
                            if ($_options->isNotEmpty() && $_isMultiple == FALSE) $_options->prepend('-- Выбрать --', '');
                            if ($_product_param_item = $_product_param_items->get($_param->id)) {
                                if ($_isMultiple) {
                                    $_selected = $_product_param_item->map(function ($_i) {
                                        return $_i->id;
                                    });
                                } else {
                                    $_selected = $_product_param_item->first()->id;
                                }
                            }
                            $_params->put($_param->id, [
                                'in_filter' => $_inFilter,
                                'markup'    => field_render("product_params.select.{$_param->id}", [
                                    'type'     => 'select',
                                    'label'    => $_param->getTranslation('title', $this->defaultLocale) . ($_inFilter ? ' <span class="uk-text-color-orange">(Отображается в фильтре)</span>' : NULL),
                                    'selected' => $_selected,
                                    'values'   => $_options,
                                    'multiple' => $_isMultiple,
                                    'class'    => 'uk-select2',
                                ])
                            ]);
                            break;
                        case 'input_number':
                            $_options = $_param->_relation_item;
                            $_attributes = [];
                            $_unit = NULL;
                            if ($_options->min_value) $_attributes['min'] = $_options->min_value;
                            if ($_options->max_value) $_attributes['max'] = $_options->max_value;
                            if ($_options->step_value) $_attributes['step'] = $_options->step_value;
                            if ($_options->unit_value) $_unit = $_options->getTranslation('unit_value', $this->defaultLocale);
                            $_inFilter = isset($_param->pivot->visible_in_filter) && $_param->pivot->visible_in_filter ? 1 : 0;
                            if ($_product_param_item = $_product_param_items->get($_param->id)) $_selected = $_product_param_item->first()->pivot->value;
                            $_params->put($_param->id, [
                                'in_filter' => $_inFilter,
                                'markup'    => field_render("product_params.number.{$_options->id}", [
                                    'type'       => 'number',
                                    'label'      => $_param->getTranslation('title', $this->defaultLocale) . ", {$_unit}" . ($_inFilter ? ' <span class="uk-text-color-orange">(Отображается в фильтре)</span>' : NULL),
                                    'value'      => $_selected,
                                    'attributes' => $_attributes
                                ])
                            ]);
                            break;
                        default:
                            $_options = $_param->_relation_item;
                            if ($_product_param_item = $_product_param_items->get($_param->id)) $_selected = $_product_param_item->first()->pivot->text;
                            $_params->put($_param->id, [
                                'in_filter' => 0,
                                'markup'    => field_render("product_params.text.{$_options->id}", [
                                    'label' => $_param->getTranslation('title', $this->defaultLocale),
                                    'value' => $_selected
                                ])
                            ]);
                            break;
                    }
                }
            }
        }

        return $_params;
    }

    public function setParamItems()
    {
        $this->_category()->detach();
        $this->_param_items()->detach();
        $_response = NULL;
        if ($_categories = request()->get('categories')) {
            $this->_category()->attach($_categories);
        }
        if ($_params = request()->get('product_params')) {
            $_params_all = ParamItem::pluck('name', 'id');
            $_attach = [];
            foreach ($_params as $_param_type => $_param) {
                foreach ($_param as $_param_id => $_param_value) {
                    switch ($_param_type) {
                        case 'select':
                            if (is_array($_param_value)) {
                                foreach ($_param_value as $_value) {
                                    if ($_value) {
                                        $_attach[$_value] = [
                                            'name'  => $_params_all->get($_value),
                                            'value' => NULL,
                                            'text'  => NULL,
                                        ];
                                    }
                                }
                            } elseif ($_param_value) {
                                $_attach[$_param_value] = [
                                    'name'  => $_params_all->get($_param_value),
                                    'value' => NULL,
                                    'text'  => NULL,
                                ];
                            }
                            break;
                        case 'number':
                            if ($_param_value) {
                                $_attach[$_param_id] = [
                                    'name'  => $_params_all->get($_param_id),
                                    'value' => $_param_value,
                                    'text'  => NULL,
                                ];
                            }
                            break;
                        case 'text':
                            if ($_param_value) {
                                $_attach[$_param_id] = [
                                    'name'  => $_params_all->get($_param_id),
                                    'value' => NULL,
                                    'text'  => $_param_value,
                                ];
                            }
                            break;
                    }
                }
            }
            $this->_param_items()->attach($_attach);
        }
    }

    public function setPrices()
    {
        $_prices = request()->get('prices');
        foreach ($_prices as &$_price) {
            //            $_quantity = $_price['quantity'] ? : 0;
            $_quantity = 1000;
            $_price['product_id'] = $this->id;
            //            $_price['not_available'] = $_price['status'] == 'not_available' ? 1 : ($_price['status'] == 'in_stock' && !$_price['quantity'] ? 1 : 0);
            $_price['not_available'] = 0;
            $_price['status'] = 'not_limited';
            $_price['base_price'] = $_price['discount_price'] ? $_price['discount_price'] : $_price['price'];
            $_price['default'] = 1;
            //            $_price['pharm_1c'] = '00000000-0000-0000-0000-000000000000';
            //            $_price['product_1c'] = $this->id_1c;
            //            $_price['multiplicity'] = $this->multiplicity;
            unset($_price['quantity']);
            $_entity = Price::updateOrCreate([
                'id' => $_price['id']
            ],
                $_price);
            if ($_entity->quantity_id) {
                $_entity->_quantity()->update([
                    'quantity' => $_quantity,
                ]);
            } else {
                $_save = new Quantity();
                $_save->fill([
                    'quantity' => $_quantity,
                    //                    'pharm_1c'   => '00000000-0000-0000-0000-000000000000',
                    //                    'product_1c' => $this->id_1c,
                ]);
                $_entity_quantity = $_entity->_quantity()->save($_save);
                $_entity->update([
                    'quantity_id' => $_entity_quantity->id
                ]);
            }

        }
        //            $this->_prices()->delete();
        //            $this->_prices()->insert($_prices);
    }

    public function setViewLists()
    {
        $_insert = NULL;
        if ((boolean)request()->input('mark_hit.1')) {
            $_insert['hit'] = 1;
        }
        if ((boolean)request()->input('mark_new.1')) {
            $_insert['new'] = 1;
        }
        if ((boolean)request()->input('mark_recommended_front.1')) {
            $_insert['recommended_front'] = 1;
        }
        if ((boolean)request()->input('mark_recommended_checkout.1')) {
            $_insert['recommended_checkout'] = 1;
        }
        //        $_prices = request()->get('prices');
        //        foreach ($_prices as $_price) {
        //            if ($_price['discount_price']) {
        //                $_insert['discount'] = 1;
        //                break;
        //            }
        //        }
        $this->_view_list()->delete();
        if ($_insert) {
            $this->_view_list()->insert(array_merge($_insert, [
                'product_id' => $this->id
            ]));
        }
    }

    public function _load($view_mode = 'full')
    {
        //        if (!$this->status) $this->style_class = $this->style_class ? "{$this->style_class} uk-page-not-published" : 'uk-page-not-published';

        $_category_selected = $this->selected_category;
        $_category_selected_modify = $this->selected_modify;
        $_category_product = $_category_selected ?? NULL;
        $_selected_product = NULL;
        $_modifications = $this->_mod;
        $_use_spicy = (int)$this->use_spicy;
        $_js_modifications = NULL;
        $_item_template_teaser = [
            "frontend.{$this->deviceTemplate}.shops.product_teaser_category_{$this->id}",
            "frontend.{$this->deviceTemplate}.shops.product_teaser",
            "frontend.default.shops.product_teaser_category_{$this->id}",
            "frontend.default.shops.product_teaser",
            'backend.base.shop_product_teaser'
        ];
        if ($_modifications->isNotEmpty()) {
            $_modifications->transform(function ($_item) use (&$_category_product, $_use_spicy, $view_mode, $_category_selected) {
                $_modify = NULL;
                $_item->modify_param_item_id = NULL;
                $_item->modify_param_item_name = NULL;
                $_item->modify_param_item_title = NULL;
                $_item->modify_param_item_sort = 0;
                if ($_item->category_modify_param) {
                    $_item->modify_param_item_name = NULL;
                    $_item->modify_param_item_sort = 0;
                    $_cats = explode(',', $_item->category_modify_param);
                    foreach ($_cats as $c) {
                        $_exp = explode('::', $c);
                        if (isset($_exp[0]) && $_exp[0]) {
                            if ($_category_selected && $_exp[0] == $_category_selected) {
                                if (isset($_exp[1]) && $_exp[1]) {
                                    $_modify = $_item->_param_items->groupBy('param_id')->get($_exp[1]);
                                    if ($_modify) {
                                        $_first = $_modify->first();
                                        $_item->modify_param_item_id = $_first->id;
                                        $_item->modify_param_item_name = $_first->name;
                                        $_item->modify_param_item_title = $_first->title;
                                        $_item->modify_param_item_sort = (int)$_first->sort;
                                    }
                                }
                            } elseif (!$_category_selected && isset($_exp[1]) && $_exp[1]) {
                                $_category_product = $_exp[0];
                                $_modify = $_item->_param_items->groupBy('param_id')->get($_exp[1]);
                                if ($_modify) {
                                    $_first = $_modify->first();
                                    $_item->modify_param_item_id = $_first->id;
                                    $_item->modify_param_item_name = $_first->name;
                                    $_item->modify_param_item_title = $_first->title;
                                    $_item->modify_param_item_sort = (int)$_first->sort;
                                }
                                break;
                            }
                        }
                    }
                }
                $_param_options = $_item->_param_items;
                if ($view_mode == 'teaser') {
                    $_item->price = $_item->_render_price();
                    if ($_param_options->isNotEmpty()) {
                        $_param_options->groupBy('param_id')->each(function ($_options) use (&$_params_output) {
                            $_options->each(function ($_option) use (&$_params_output) {
                                $_param = $_option->_param;
                                if ($_param->visible_in_teaser) {
                                    $_params_output[$_param->id]['title'] = $_param->teaser_title ? : $_param->title;
                                    $_params_output[$_param->id]['unit'] = $_option->unit_value ? : NULL;
                                    switch ($_param->type) {
                                        case 'select':
                                            $_params_output[$_param->id]['options'][$_option->id] = [
                                                'title'     => $_option->title,
                                                'sub_title' => $_option->sub_title,
                                                'attribute' => $_option->attribute,
                                                'icon'      => $_option->icon_fid ? $_option->_icon_asset(NULL, ['only_way' => FALSE]) : NULL
                                            ];
                                            break;
                                        case 'input_number':
                                            if (is_null($_option->pivot->value)) {
                                                unset($_params_output[$_param->id]);
                                            } else {
                                                $_params_output[$_param->id]['options'] = $_option->pivot->value;
                                            }
                                            break;
                                        case 'input_text':
                                            if (is_null($_option->pivot->text)) {
                                                unset($_params_output[$_param->id]);
                                            } else {
                                                $_params_output[$_param->id]['options'] = $_option->pivot->text;
                                            }
                                            break;
                                    }
                                }
                            });
                        });
                    }
                    //                    $_item->paramOptions = $_params_output;
                }
                if (self::ID_MARK_SPICY && $_use_spicy) {
                    $_item->is_spicy = $_param_options->filter(function ($pi) {
                        return $pi->id == self::ID_MARK_SPICY;
                    })->isNotEmpty();
                }

                return $_item;
            })
                ->filter(function ($m) {
                    return $m->modify_param_item_id;
                })
                ->sortBy('modify_param_item_sort');
        }
        if ($_modifications->isNotEmpty() && $_modifications->count() > 1) {
            $_modifications->transform(function ($_item) use (&$_js_modifications, &$_selected_product, $_item_template_teaser, $_modifications, $_category_selected_modify) {

                $_param_options = $_item->_param_items;
                $_params_output = NULL;
                if ($_param_options->isNotEmpty()) {
                    $_param_options->groupBy('param_id')->each(function ($_options) use (&$_params_output) {
                        $_options->each(function ($_option) use (&$_params_output) {
                            $_param = $_option->_param;
                            if ($_param->visible_in_teaser) {
                                $_params_output[$_param->id]['title'] = $_param->teaser_title ? : $_param->title;
                                $_params_output[$_param->id]['unit'] = $_option->unit_value ? : NULL;
                                switch ($_param->type) {
                                    case 'select':
                                        $_params_output[$_param->id]['options'][$_option->id] = [
                                            'title'     => $_option->title,
                                            'sub_title' => $_option->sub_title,
                                            'attribute' => $_option->attribute,
                                            'icon'      => $_option->icon_fid ? $_option->_icon_asset(NULL, ['only_way' => FALSE]) : NULL
                                        ];
                                        break;
                                    case 'input_number':
                                        if (is_null($_option->pivot->value)) {
                                            unset($_params_output[$_param->id]);
                                        } else {
                                            $_params_output[$_param->id]['options'] = $_option->pivot->value;
                                        }
                                        break;
                                    case 'input_text':
                                        if (is_null($_option->pivot->text)) {
                                            unset($_params_output[$_param->id]);
                                        } else {
                                            $_params_output[$_param->id]['options'] = $_option->pivot->text;
                                        }
                                        break;
                                }
                            }
                        });
                    });
                }
                //                $this->product_count_buy = $this->_product_count_buy->count();
                $_item->paramOptions = $_params_output;
                $_item->modification_items = $_modifications;
                $_js_modifications[$_item->id] = [
                    'dom_id' => "dom-item-card-product-{$_item->modify}",
                    'html'   => clear_html(View::first($_item_template_teaser, compact('_item')))
                ];
                if ($_category_selected_modify && $_category_selected_modify == $_item->modify_param_item_id) $_selected_product = $_item;

                return $_item;
            });
        }
        $this->js_modification_items = $_js_modifications;
        $this->modification_items = $_modifications->isNotEmpty() ? $_modifications : NULL;
        $_param_options = $this->_param_items;

        if ($this->id == 7) {

            //            dd($this->modification_items);

        }

        if (self::ID_MARK_SPICY && $_use_spicy) {
            $this->is_spicy = $_param_options->filter(function ($pi) {
                return $pi->id == self::ID_MARK_SPICY;
            })->isNotEmpty();
        }
        $this->price = $this->_render_price();
        switch ($view_mode) {
            case 'teaser':
                $_params_output = NULL;
                if ($_param_options->isNotEmpty()) {
                    $_param_options->groupBy('param_id')->each(function ($_options) use (&$_params_output) {
                        $_options->each(function ($_option) use (&$_params_output) {
                            $_param = $_option->_param;
                            if ($_param->visible_in_teaser) {
                                $_params_output[$_param->id]['title'] = $_param->teaser_title ? : $_param->title;
                                $_params_output[$_param->id]['unit'] = $_option->unit_value ? : NULL;
                                switch ($_param->type) {
                                    case 'select':
                                        $_params_output[$_param->id]['options'][$_option->id] = [
                                            'title'     => $_option->title,
                                            'sub_title' => $_option->sub_title,
                                            'attribute' => $_option->attribute,
                                            'icon'      => $_option->icon_fid ? $_option->_icon_asset(NULL, ['only_way' => FALSE]) : NULL
                                        ];
                                        break;
                                    case 'input_number':
                                        if (is_null($_option->pivot->value)) {
                                            unset($_params_output[$_param->id]);
                                        } else {
                                            $_params_output[$_param->id]['options'] = $_option->pivot->value;
                                        }
                                        break;
                                    case 'input_text':
                                        if (is_null($_option->pivot->text)) {
                                            unset($_params_output[$_param->id]);
                                        } else {
                                            $_params_output[$_param->id]['options'] = $_option->pivot->text;
                                        }
                                        break;
                                }
                            }
                        });
                    });
                }
                //                $this->product_count_buy = $this->_product_count_buy->count();
                $this->paramOptions = $_params_output;
                break;
            default:
                $cat = NULL;
                $_categories = $this->_category;
                $_mask_meta_tags = NULL;
                //                $_quiz = NULL;
                $_entity = $this;
                if ($_categories->isNotEmpty()) {
                    $_category_product = $_categories->first();
                    $_categories->map(function ($_category) use (&$cat, &$_mask_meta_tags, &$_quiz, $_entity) {
                        $cat[] = "'{$_category->title}'";
                        $_tmp_meta_tags = $_category->_tmp_meta_tags
                            ->where('type', 'product')->first();
                        if ($_tmp_meta_tags && is_null($_mask_meta_tags)) {
                            $_mask_meta_tags['meta_title'] = short_code($_tmp_meta_tags->meta_title, [
                                'title' => $_entity->title,
                                'sky'   => $_entity->sku,
                            ]);
                            $_mask_meta_tags['meta_description'] = short_code($_tmp_meta_tags->meta_description, [
                                'title' => $_entity->title,
                                'sky'   => $_entity->sku,
                            ]);
                            $_mask_meta_tags['meta_keywords'] = short_code($_tmp_meta_tags->meta_keywords, [
                                'title' => $_entity->title,
                                'sky'   => $_entity->sku,
                            ]);
                        }
                    });
                }
                $this->cat = NULL;
                if ($cat) {
                    $this->cat = '[' . implode(',', $cat) . ']';
                }
                if ($_mask_meta_tags) $this->metaMask = $_mask_meta_tags;
                $this->body = content_render($this);
                $this->relatedMedias = $this->_files_related()
                    ->wherePivot('type', 'medias')
                    ->remember(REMEMBER_LIFETIME * 7 * 24)
                    ->get();
                $this->relatedFiles = $this->_files_related()
                    ->wherePivot('type', 'files')
                    ->remember(REMEMBER_LIFETIME * 7 * 24)
                    ->get();
                $_params_output = NULL;
                if ($_param_options->isNotEmpty()) {
                    $_category = $this->global_category ? $this->global_category : NULL;
                    $_category_params = $_category ? $_category->_params()->where('visible_in_filter', 1)->pluck('id')->toArray() : NULL;
                    $_param_options->groupBy('param_id')->each(function ($_options) use (&$_params_output, $_category, $_category_params) {
                        $_options->each(function ($_option) use (&$_params_output, $_category, $_category_params) {
                            $_param = $_option->_param;
                            $_params_output[$_param->id]['title'] = $_param->title;
                            $_params_output[$_param->id]['unit'] = $_option->unit_value ? : NULL;
                            $_params_output[$_param->id]['visible_in_teaser'] = (int)$_param->visible_in_teaser;
                            switch ($_param->type) {
                                case 'select':
                                    $_title = $_option->title;
                                    if ($_category && is_array($_category_params) && in_array($_param->id, $_category_params)) $_title = _l($_title, $_option->get_alias_to_page($_category), ['attributes' => ['target' => '_blank']]);
                                    $_params_output[$_param->id]['options'][$_option->id] = $_title;
                                    break;
                                case 'input_number':
                                    if (is_null($_option->pivot->value)) {
                                        unset($_params_output[$_param->id]);
                                    } else {
                                        $_params_output[$_param->id]['options'] = $_option->pivot->value;
                                    }
                                    break;
                                case 'input_text':
                                    if (is_null($_option->pivot->text)) {
                                        unset($_params_output[$_param->id]);
                                    } else {
                                        $_params_output[$_param->id]['options'] = $_option->pivot->text;
                                    }
                                    break;
                            }
                        });
                    });
                }
                $this->paramOptions = $_params_output;
                if (isset($_params_output[self::ID_WEIGHT])) $this->weight = $_params_output[self::ID_WEIGHT];

                //               if (request()->has('quiz')) {
                //                $this->quiz = $_quiz;
                //                    dd($_quiz->settings);
                //                }
                $this->relatedProduct = $this->_product_related();
                $this->consistProduct = $this->_product_consist();
                $this->productOrder = $this->getAdditionalElements($_category_product, $_param_options);
                break;
        }
        if ($_selected_product) $_selected_product->js_modification_items = $this->js_modification_items;

        return $_selected_product ? : $this;
    }

    //    public function _load($view_mode = 'full')
    //    {
    //        $this->price = $this->_render_price($this->basket);
    //        switch ($view_mode) {
    //            case 'teaser':
    //                $_params_output = NULL;
    //                $this->paramOptions = $_params_output;
    //                break;
    //            default:
    //                $_price = NULL;
    //                $_categories = $this->_category;
    //                $_mask_meta_tags = NULL;
    //                $_entity = $this;
    //                if ($_entity->price['view_price']) {
    //                    if (count($_entity->price['view']) > 1) {
    //                        $_price = 'от ' . $_entity->price['view'][1]['format']['view_price'] . ' ' . $_entity->price['view'][1]['currency']['suffix'];
    //                    } else {
    //                        $_price = 'от ' . $_entity->price['view'][0]['format']['view_price'] . ' ' . $_entity->price['view'][0]['currency']['suffix'];
    //                    }
    //                } elseif ($_entity->price['pharmacy_min_price_exist']) {
    //                    $_price = trans('frontend.from') . ' ' . $_entity->price['pharmacy_min_price']['format']['view_price'] . ' ' . $_entity->price['pharmacy_min_price']['currency']['suffix'];
    //                }
    //                if ($_categories->isNotEmpty()) {
    //                    $_categories->each(function ($_category) use (&$_mask_meta_tags, $_entity, $_price) {
    //                        $_tmp_meta_tags = $_category->_tmp_meta_tags
    //                            ->where('type', 'product')->first();
    //                        if ($_tmp_meta_tags && is_null($_mask_meta_tags)) {
    //                            $_mask_meta_tags['meta_title'] = short_code($_tmp_meta_tags->meta_title, [
    //                                'title' => $_entity->title,
    //                                'brand' => $_entity->_brand->title,
    //                                'sku'   => $_entity->sku,
    //                                'price' => $_price,
    //                            ]);
    //                            $_mask_meta_tags['meta_description'] = short_code($_tmp_meta_tags->meta_description, [
    //                                'title' => $_entity->title,
    //                                'brand' => $_entity->_brand->title,
    //                                'sku'   => $_entity->sku,
    //                                'price' => $_price,
    //                            ]);
    //                            $_mask_meta_tags['meta_keywords'] = short_code($_tmp_meta_tags->meta_keywords, [
    //                                'title' => $_entity->title,
    //                                'brand' => $_entity->_brand->title,
    //                                'sku'   => $_entity->sku,
    //                                'price' => $_price,
    //                            ]);
    //                        }
    //                    });
    //                }
    //                if ($_mask_meta_tags) $this->metaMask = $_mask_meta_tags;
    //                $this->body = content_render($this);
    //                $this->relatedMedias = $this->_files_related()->wherePivot('type', 'medias')->get();
    //                $this->relatedFiles = $this->_files_related()->wherePivot('type', 'files')->get();
    //                $_params_output = NULL;
    //                $this->paramOptions = $_params_output;
    //
    //                $this->relatedProduct = $this->_product_related();
    //                break;
    //        }
    //
    //        return $this;
    //    }

    public function _render($options = NULL)
    {
        global $wrap;

        $this->_eCommerce = collect([]);
        $_view = $options['view_mode'] ?? NULL;
        $this->_load($_view);
        $_set_wrap = [
            //  'seo.title'         => $this->meta_title ? : ($this->metaMask['meta_title'] ? : $this->title),
            //  'seo.keywords'      => $this->meta_keywords ? : ($this->metaMask['meta_keywords'] ? : ''),
            //  'seo.description'   => $this->meta_description ? : ($this->metaMask['meta_description'] ? : ''),
            'seo.robots'        => $this->meta_robots,
            'seo.last_modified' => $this->last_modified,
            'page.title'        => $this->title,
            'page.style_id'     => $this->style_id,
            'page.style_class'  => $this->style_class ? [$this->style_class] : '',
            'page.breadcrumb'   => breadcrumb_render(['entity' => $this]),
            'seo.open_graph'    => [
                'title'       => $this->title,
                'description' => ($this->meta_description ? : ($this->metaMask['meta_description'] ?? '')) ? : config_data_load(config('os_seo'), 'settings.*.description', $wrap['locale']),
                'url'         => $wrap['seo']['base_url'] . $this->generate_url,
            ],
            'page.scripts'      => [
                [
                    'url'      => 'template/js/custom.js',
                    'position' => 'footer',
                    'sort'     => 999
                ],
                [
                    'url'      => 'template/js/app.js',
                    'position' => 'footer',
                    'sort'     => 1000
                ],
            ],
        ];

        $this->setWrap($_set_wrap);
        $_template = [
            "frontend.{$this->deviceTemplate}.shops.product_{$this->id}",
            "frontend.{$this->deviceTemplate}.shops.product",
            "frontend.default.shops.product_{$this->id}",
            "frontend.default.shops.product",
            'backend.base.shop_product'
        ];

        if (isset($options['view']) && $options['view']) array_unshift($_template, $options['view']);
        $this->template = $_template;
        $this->increment('view_statistics');
        LastViewed::set($this);
        $_categories = $this->_category;
        $_category_product = '';

        if ($_categories->isNotEmpty()) {
            $_category_product = $_categories->first()->getTranslation('title', 'ua');
        }

        $this->_eCommerce->push([
            'id'            => $this->sku,
            'name'          => $this->getTranslation('title', 'ua'),
            'category'      => $_category_product,
            'list_name'     => 'Карточка товара',
            'list_position' => 0,
            'quantity'      => 1,
            'price'         => $this->price['view_price'] ? (count($this->price['view']) > 1 ? $this->price['view'][1]['format']['price'] : $this->price['view'][0]['format']['price']) : 0
        ]);


        return $this;
    }

    public function getBreadcrumb()
    {
        $_response = NULL;
        $_this_categories = $this->_category;
        if ($_this_categories->isEmpty()) return $_response;
        $_this_categories->each(function ($_category) use (&$_response) {
            $_tree = collect([]);
            $_structure = [];
            $_category->get_parents($_tree, $_category->_parent);
            if ($_tree->isNotEmpty()) {
                for ($_i = ($_tree->count() - 1); $_i >= 0; $_i--) {
                    $_structure[] = $_tree->get($_i);
                }
            }
            $_structure[] = $_category;
            $_response[$_category->id] = $_structure;
        });

        return $_response;
    }

    public function getAvailability()
    {
        $_response = collect([]);
        if ($this->_availability->isNotEmpty()) {
            $_basket = app('basket');
            $_availability = NULL;
            $_multiplicity = $this->multiplicity ? : 1;
            $this->_availability->groupBy('pharmacy_id')->each(function ($_prices) use (&$_availability, $_basket, $_multiplicity) {
                $_prices->sortBy('part')->each(function ($_price) use (&$_availability, $_basket, $_multiplicity) {
                    $_basket_balance = $_basket->product_balance($_price);
                    $_quantity = $_price->count_in_stock - $_basket_balance;
                    $_type = 'whole';
                    if ($_price->part && $_quantity > 0) {
                        $_quantity = $_quantity / (1 / $_multiplicity);
                        if ($_quantity < 1 && $_quantity > 0.99) {
                            $_quantity = 1;
                        } else {
                            $_quantity = floor($_quantity);
                        }
                        $_type = 'part';
                    } elseif ($_quantity > 0) {
                        $_quantity = floor($_quantity);
                    }
                    if ($_quantity >= 1) {
                        $_availability[$_price->pharmacy_id]['id'][$_type] = $_price->id;
                        $_availability[$_price->pharmacy_id]['count_in_stock'][$_type] = $_quantity;
                        $_availability[$_price->pharmacy_id]['price'][$_type] = view_price($_price->price, $_price->price);
                        $_availability[$_price->pharmacy_id]['pharm_city'] = $_price->_pharmacy->_pharm_city;
                        $_availability[$_price->pharmacy_id]['pharm_city_id'] = $_availability[$_price->pharmacy_id]['pharm_city']->id;
                        $_availability[$_price->pharmacy_id]['pharm_city_sort'] = $_availability[$_price->pharmacy_id]['pharm_city']->sort;
                        $_availability[$_price->pharmacy_id]['pharmacy'] = $_price->_pharmacy;
                        $_availability[$_price->pharmacy_id]['pharmacy_sort'] = $_availability[$_price->pharmacy_id]['pharmacy']->sort;
                        $_availability[$_price->pharmacy_id]['multiplicity'] = str_replace('.', ',', (floor((1 / $_multiplicity * 100)) / 100));
                    }
                });
            });
            if ($_availability) {
                $_response = collect($_availability)->sortBy('pharm_city_sort')->groupBy('pharm_city_id')->transform(function ($_pharmacies) {
                    $_pharmacies = collect([
                        'location'   => $_pharmacies->first()['pharm_city'],
                        'pharmacies' => $_pharmacies->sortBy('pharmacy_sort')
                    ]);

                    return $_pharmacies;
                });
            }
        }

        return $_response;
    }

    /**
     * Imports
     */
    public static function import($props)
    {
        $_item = self::where('id_1c', $props['ref'])
            ->first();
        if (is_null($_item)) {
            $_item = new self();
            $_locale = config('app.locale');
            $_item->fill([
                'id_1c'         => $props['ref'],
                'group_1c'      => $props['group'],
                'sku'           => $props['code'],
                'model'         => $props['code'],
                'brand_id'      => $props['brand'],
                'original_name' => $props['name'],
                'multiplicity'  => $props['multip'] ? : 1,
                'status'        => $props['del'] ? 0 : 1,
                'title'         => $props['name'],
            ]);
            $_item->save();
            if (isset($props['category'])) $_item->_category()->attach($props['category']);
            $_generate_alias = generate_alias($_item->title);
            if (UrlAlias::where('alias', $_generate_alias)
                    ->count() > 0
            ) {
                $index = 0;
                while ($index <= 100) {
                    $_generate_url = "{$_generate_alias}-{$index}";
                    if (UrlAlias::where('alias', $_generate_url)
                            ->count() == 0
                    ) {
                        $_generate_alias = $_generate_url;
                        break;
                    }
                    $index++;
                }
            }
            $_url_alias = new UrlAlias();
            $_url_alias->fill([
                'alias'               => $_generate_alias,
                'model_default_title' => $_item->title,
            ]);
            $_item->_alias()->save($_url_alias);
            $_quantity = new Quantity();
            $_quantity->fill([
                'quantity'   => 0,
                'pharm_1c'   => '00000000-0000-0000-0000-000000000000',
                'product_1c' => $props['ref'],
            ]);
            $_quantity->save();
            $_item->_price()->insert([
                'product_id'    => $_item->id,
                'price'         => NULL,
                'base_price'    => NULL,
                'default'       => 1,
                'not_available' => 1,
                'status'        => 'under_order',
                'multiplicity'  => $_item->multiplicity,
                'quantity_id'   => $_quantity->id,
                'pharm_1c'      => '00000000-0000-0000-0000-000000000000',
                'product_1c'    => $props['ref'],
            ]);
            //                if (isset($props['category'])) $_item->_category()->attach($props['category']);
            if ($props['analog'] != '00000000-0000-0000-0000-000000000000' && $props['analog']) {
                $_analog = new Analog();
                $_analog->fill([
                    'group_1c'   => $props['analog'],
                    'product_id' => $_item->id,
                ]);
                $_analog->save();
            }
            $_search_index = new SearchIndex();
            $_search_index->locale = $_locale;
            $_search_index->title = $_item->title;
            $_search_index->status = $_item->status;
            $_item->_search_index()
                ->save($_search_index);
            spy('Добавлен новый товар <a href="/oleus/shop-products/' . $_item->id . '/edit" target="_blank" class="uk-text-bold">' . $_item->title . '</a>.', 'success');
        }

        return $_item;
    }

    public static function update_price($props, $product = NULL, $pharmacy = NULL, $storage = FALSE)
    {
        if ($product && $storage) {
            $_price = ceil((float)($props['price'] ?? 0));
            $_status = $props['quantity'] && $_price ? 'in_stock' : 'under_order';
            $_whole_price = DB::table('shop_product_prices')
                ->where('product_id', $product)
                ->whereNull('pharmacy_id')
                ->first([
                    'id',
                    'quantity_id'
                ]);
            DB::table('shop_product_prices')
                ->where('id', $_whole_price->id)
                ->update([
                    'price'         => $_price,
                    'base_price'    => $_price,
                    'status'        => $_status,
                    'not_available' => $_status == 'in_stock' ? 0 : 1,
                ]);
            DB::table('shop_product_quantity')
                ->where('id', $_whole_price->quantity_id)
                ->update([
                    'quantity' => (float)$props['quantity']
                ]);
            unset($_whole_price);
        } elseif ($product && $pharmacy) {
            $_whole_price = DB::table('shop_product_prices')
                ->where('product_id', $product)
                ->where('pharmacy_id', $pharmacy)
                ->where('part', 0)
                ->first([
                    'multiplicity',
                    'id',
                    'quantity_id'
                ]);
            $_price = (float)($props['price'] ?? 0);
            $_status = $props['quantity'] && $_price ? 'in_stock' : 'under_order';
            $_min_quantity = 1;
            if ($_whole_price) {
                $_multiplicity = (int)($_whole_price->multiplicity ? : 1);
                if ($_multiplicity > 1) {
                    $_min_quantity = 1 / $_multiplicity;
                    $_decimals_pow_3 = pow(10, 3);
                    $_min_quantity = (float)ceil((float)$_min_quantity * $_decimals_pow_3) / $_decimals_pow_3;
                }
                if ($_min_quantity != 1 && ((float)$props['quantity'] < $_min_quantity)) $_status = 'under_order';
                DB::table('shop_product_quantity')
                    ->where('id', $_whole_price->quantity_id)
                    ->update([
                        'quantity' => (float)$props['quantity']
                    ]);
                DB::table('shop_product_prices')
                    ->where('id', $_whole_price->id)
                    ->update([
                        'price'         => $_price,
                        'base_price'    => $_price,
                        'status'        => $_status,
                        'not_available' => $_status == 'in_stock' ? 0 : 1,
                    ]);
                if ($_multiplicity > 1) {
                    if ($_price) {
                        $_price = $_price / $_multiplicity;
                        $_decimals_pow = pow(10, 2);
                        $_price = ceil((float)$_price * $_decimals_pow) / $_decimals_pow;
                    }
                    DB::table('shop_product_prices')
                        ->where('product_id', $product)
                        ->where('pharmacy_id', $pharmacy)
                        ->where('part', 1)
                        ->update([
                            'price'         => $_price,
                            'base_price'    => $_price,
                            'status'        => $_status,
                            'not_available' => $_status == 'in_stock' ? 0 : 1,
                        ]);
                }
            } else {
                $_multiplicity = DB::table('shop_products')
                    ->where('id', $product)
                    ->value('multiplicity');
                $_multiplicity = (int)($_multiplicity ? : 1);
                if ($_multiplicity > 1) {
                    $_min_quantity = 1 / $_multiplicity;
                    $_decimals_pow_3 = pow(10, 3);
                    $_min_quantity = (float)ceil((float)$_min_quantity * $_decimals_pow_3) / $_decimals_pow_3;
                }
                if ($_min_quantity != 1 && ((float)$props['quantity'] < $_min_quantity)) $_status = 'under_order';
                $_quantity_id = DB::table('shop_product_quantity')
                    ->insertGetId([
                        'quantity'   => (float)$props['quantity'],
                        'pharm_1c'   => $props['pharm'],
                        'product_1c' => $props['drug'],
                    ]);
                $_prices_insert = [
                    [
                        'product_id'    => $product,
                        'pharmacy_id'   => $pharmacy,
                        'pharm_1c'      => $props['pharm'],
                        'product_1c'    => $props['drug'],
                        'default'       => 0,
                        'part'          => 0,
                        'multiplicity'  => $_multiplicity,
                        'price'         => $_price,
                        'base_price'    => $_price,
                        'status'        => $_status,
                        'not_available' => $_status == 'in_stock' ? 0 : 1,
                        'quantity_id'   => $_quantity_id,
                    ]
                ];
                if ($_multiplicity > 1) {
                    if ($_price) {
                        $_price = $_price / $_multiplicity;
                        $_decimals_pow = pow(10, 2);
                        $_price = ceil((float)$_price * $_decimals_pow) / $_decimals_pow;
                    }
                    $_prices_insert[] = [
                        'product_id'    => $product,
                        'pharmacy_id'   => $pharmacy,
                        'pharm_1c'      => $props['pharm'],
                        'product_1c'    => $props['drug'],
                        'default'       => 0,
                        'part'          => 1,
                        'multiplicity'  => $_multiplicity,
                        'price'         => $_price,
                        'base_price'    => $_price,
                        'status'        => $_status,
                        'not_available' => $_status == 'in_stock' ? 0 : 1,
                        'quantity_id'   => $_quantity_id,
                    ];
                }
                DB::table('shop_product_prices')
                    ->insert($_prices_insert);
            }
        }
        unset($product, $pharmacy, $storage);

        return;
    }

    public static function update_price_ORG($props, $product = NULL, $pharmacy = NULL, $storage = FALSE)
    {
        if ($product && $storage) {
            $_price = $props['price'] ?? NULL;
            $_status = $props['quantity'] && $_price ? 'in_stock' : 'under_order';
            $_whole_price = Price::updateOrCreate([
                'product_id' => $product,
                'default'    => 1
            ], [
                'product_id'    => $product,
                'price'         => $_price,
                'base_price'    => $_price,
                'status'        => $_status,
                'not_available' => $_status == 'in_stock' ? 0 : 1,
            ]);
            $_whole_price->_quantity->update([
                'quantity' => (float)$props['quantity']
            ]);
            unset($_whole_price);
        } elseif ($product) {
            if ($pharmacy) {
                $_prices = Price::where('product_id', $product)
                    ->where('pharmacy_id', $pharmacy)
                    ->get();
                $_price = $props['price'] ?? NULL;
                $_status = $props['quantity'] && $_price ? 'in_stock' : 'under_order';
                if ($_prices->isNotEmpty()) {
                    $_whole = $_prices->first(function ($_i) {
                        return $_i->part == 0;
                    });
                    $_multiplicity = (int)$_whole->multiplicity ? : 1;
                    $_whole->_quantity->update([
                        'quantity' => (float)$props['quantity']
                    ]);
                    $_whole->update([
                        'price'         => $_price,
                        'base_price'    => $_price,
                        'status'        => $_status,
                        'not_available' => $_status == 'in_stock' ? 0 : 1,
                    ]);
                    if ($_multiplicity > 1) {
                        $_price = $_price / $_multiplicity;
                        $_decimals_pow = pow(10, 2);
                        $_price = ceil((float)$_price * $_decimals_pow) / $_decimals_pow;
                        $_part_price = Price::updateOrCreate([
                            'product_id'  => $product,
                            'pharmacy_id' => $pharmacy,
                            'part'        => 1
                        ], [
                            'product_id'    => $product,
                            'pharmacy_id'   => $pharmacy,
                            'pharm_1c'      => $props['pharm'],
                            'product_1c'    => $props['drug'],
                            'part'          => 1,
                            'multiplicity'  => $_multiplicity,
                            'price'         => $_price,
                            'base_price'    => $_price,
                            'status'        => $_status,
                            'not_available' => $_status == 'in_stock' ? 0 : 1,
                            'quantity_id'   => $_whole->_quantity->id,
                        ]);
                    }
                    unset($_part_price);
                } else {
                    $_multiplicity = DB::table('shop_product_prices')
                        ->where('default', 1)
                        ->where('product_id', $product)
                        ->value('multiplicity');
                    $_quantity = new Quantity();
                    $_quantity->fill([
                        'quantity'   => (float)$props['quantity'],
                        'pharm_1c'   => $props['pharm'],
                        'product_1c' => $props['drug'],
                    ]);
                    $_quantity->save();
                    $_whole_price = new Price();
                    $_whole_price->fill([
                        'product_id'    => $product,
                        'pharmacy_id'   => $pharmacy,
                        'price'         => $_price,
                        'base_price'    => $_price,
                        'status'        => $_status,
                        'multiplicity'  => $_multiplicity,
                        'not_available' => $_status == 'in_stock' ? 0 : 1,
                        'default'       => 0,
                        'quantity_id'   => $_quantity->id,
                        'pharm_1c'      => $props['pharm'],
                        'product_1c'    => $props['drug'],
                    ]);
                    $_whole_price->save();
                    if ($_multiplicity > 1) {
                        $_price = $_price / $_multiplicity;
                        $_decimals_pow = pow(10, 2);
                        $_price = ceil((float)$_price * $_decimals_pow) / $_decimals_pow;
                        $_part_price = Price::updateOrCreate([
                            'product_id'  => $product,
                            'pharmacy_id' => $pharmacy,
                            'part'        => 1
                        ], [
                            'product_id'    => $product,
                            'pharmacy_id'   => $pharmacy,
                            'pharm_1c'      => $props['pharm'],
                            'product_1c'    => $props['drug'],
                            'part'          => 1,
                            'multiplicity'  => $_multiplicity,
                            'price'         => $_price,
                            'base_price'    => $_price,
                            'status'        => $_status,
                            'not_available' => $_status == 'in_stock' ? 0 : 1,
                            'quantity_id'   => $_quantity->id,
                        ]);
                        unset($_part_price);
                    }
                    unset($_quantity, $_price);
                }
                unset($_prices);
            }
        }
        unset($product, $pharmacy, $storage);

        return;
    }

    public function dupl($name)
    {
        $_new = $this->replicate();
        $_new->title = $name;
        $_new->modify = $this->id;
        $_new->sku = NULL;
        $_new->status = 0;
        $_new->sort = 0;
        $_new->save();
        $_alias = $this->_alias
            ->replicate()
            ->fill([
                'model_default_title' => $name,
                'alias'               => $this->_alias->alias . "-{$_new->id}"
            ]);
        $_new->_alias()->save($_alias);
        $_quantity = new Quantity();
        $_quantity->fill([
            'quantity' => 0,
        ]);
        $_quantity->save();
        $_price = $this->_price->replicate()
            ->fill([
                'product_id'  => $_new->id,
                'quantity_id' => $_quantity->id
            ]);
        $_new->_price()->save($_price);
        if ($this->_view_list) {
            $_view_list = $this->_view_list->replicate()
                ->fill([
                    'product_id' => $_new->id,
                ]);
            $_new->_view_list()->save($_view_list);
        }
        $_new->_files_related()->attach($this->_files_related);
        //        $_new->_files_consist()->attach($this->_files_consist);
        $_new->_category()->attach($this->_category);
        if ($this->_param_items) {
            $this->_param_items->map(function ($p) use ($_new) {
                $_new->_param_items()->attach($_new->id, [
                    'param_item_id' => $p->pivot->param_item_id,
                    'name'          => $p->pivot->name,
                    'value'         => $p->pivot->value,
                    'text'          => $p->pivot->text,
                ]);
            });
        };
        if ($this->_search_index) {
            $this->_search_index->map(function ($s) use ($_new, $name) {
                $_s = $s->replicate()
                    ->fill([
                        'title' => $name,
                    ]);
                $_new->_search_index()->save($_s);
            });
        }

        return $_new;
    }

    public function getAdditionalElements($category, $params)
    {
        $_ai = $category->_ai;
        $_ingredients = $params->filter(function ($i) {
            return $i->param_id == 5;
        })
            ->pluck('id', 'id');
        $_weight = NULL;
        if ($this->weight) {
            $_product_weight = explode('/', $this->weight['options']);
            $_product_weight_max = $_product_weight[0] ? ceil((($_product_weight[0] ?? 0) + ($_product_weight[0] ?? 0) * .3) / 100) * 100 : 0;
            $_weight = [
                'label'   => ":weight" . ($this->weight['unit'] ? " {$this->weight['unit']}" : NULL),
                'value'   => $_product_weight,
                'max'     => $_product_weight_max,
                'message' => str_replace(':weight', $_product_weight_max, variable('modal_message_overweight_modified_product'))
            ];
        } elseif (isset($this->paramOptions[7])) {
            $_product_weight = isset($this->paramOptions[7]) ? explode('/', $this->paramOptions[7]['options']) : NULL;
            $_product_weight_max = $_product_weight ? ($_product_weight[0] ? ceil((($_product_weight[0] ?? 0) + ($_product_weight[0] ?? 0) * .3) / 100) * 100 : 0) : 0;
            $_weight = [
                'label'   => ":weight" . ($this->paramOptions[7]['unit'] ? " {$this->paramOptions[7]['unit']}" : NULL),
                'value'   => $_product_weight,
                'max'     => $_product_weight_max,
                'message' => str_replace(':weight', $_product_weight_max, variable('modal_message_overweight_modified_product'))
            ];
        }
        $_response = [
            'additions' => FALSE,
            'items'     => [
                'default'     => [],
                'ingredients' => [],
                'additions'   => [],
                'price'       => [],
                'weight'      => [],
                'items'       => [],
            ],
            'product'   => [
                'product'     => $this->id,
                'count'       => 1,
                'spicy'       => [
                    'default' => !is_null($this->is_spicy) ? (int)$this->is_spicy : '',
                    'value'   => !is_null($this->is_spicy) ? (int)$this->is_spicy : ''
                ],
                'composition' => [
                    'default'     => [],
                    'ingredients' => [],
                    'additions'   => [],
                ],
                'price' => [
                    'id'    => $this->price['id'],
                    'label' => ":price <span class=\"currency-suffix\">{$this->price['view'][0]['currency']['suffix']}</span>",
                    'value' => $this->price['view'][1]['format']['price'] ?? $this->price['view'][0]['format']['price']
                ]
            ]
        ];
        if ($_weight) $_response['product']['weight'] = $_weight;

        $variable_ingredients = $this->variable_ingredients ?? [];
        if ($_ai->isNotEmpty() && $this->price && $this->price['view_price']) {
            $_response['additions'] = TRUE;
            $_response['items'] = [
                'default'     => $_ai->filter(function ($i) {
                    return $i->default;
                })
                    ->sortBy('sort')
                    ->transform(function ($i) {
                        return [
                            'id'     => $i->id,
                            'title'  => $i->title,
                            'name'   => $i->name,
                            'sku'    => $i->sku,
                            'weight' => $i->weight,
                            'price'  => (float)$i->price,
                        ];
                    })
                    ->values()
                    ->toArray(),
                'ingredients' => $_ai->filter(function ($i) use ($_ingredients, $variable_ingredients) {
                    return $_ingredients->has($i->ingredient_id) && (count($variable_ingredients) ? in_array($i->ingredient_id, $variable_ingredients) : TRUE);
                })
                    ->sortBy('sort')
                    ->transform(function ($i) {
                        return [
                            'id'     => $i->id,
                            'title'  => $i->title,
                            'name'   => $i->name,
                            'sku'    => $i->sku,
                            'weight' => $i->weight,
                            'price'  => (float)$i->price,
                        ];
                    })
                    ->values()
                    ->toArray(),
                'additions'   => $_ai->filter(function ($i) use ($_ingredients) {
                    return !$i->default && !$_ingredients->has($i->ingredient_id);
                })
                    ->sortBy('sort')
                    ->transform(function ($i) {
                        return [
                            'id'     => $i->id,
                            'title'  => $i->title,
                            'name'   => $i->name,
                            'sku'    => $i->sku,
                            'weight' => $i->weight,
                            'price'  => (float)$i->price,
                        ];
                    })
                    ->values()
                    ->toArray(),
                'items'       => $_ai->map(function ($i) {
                    return [
                        'id'     => $i->id,
                        'title'  => $i->title,
                        'name'   => $i->name,
                        'sku'    => $i->sku,
                        'weight' => $i->weight,
                        'price'  => (float)$i->price,
                    ];
                })
                    ->keyBy('id')
                    ->toArray()
            ];
            if ($this->id == $this->modify) {
                if ($_response['items']['default']) {
                    foreach ($_response['items']['default'] as $v) {
                        $_response['product']['composition']['default'][$v['id']] = 0;
                    }
                }
            } else {
                $_response['items']['default'] = [];
            }
            if ($_response['items']['ingredients']) {
                foreach ($_response['items']['ingredients'] as $v) {
                    $_response['product']['composition']['ingredients'][$v['id']] = 1;
                }
            }
            if ($_response['items']['additions']) {
                foreach ($_response['items']['additions'] as $v) {
                    $_response['product']['composition']['additions'][$v['id']] = 0;
                }
            }

            return $_response;
        }

        return $_response;
    }

    public function getSpicyMark()
    {
        $m = Cache::remember('product_spicy_mark', REMEMBER_LIFETIME * 24 * 7, function () {
            return ParamItem::select([
                'title',
                'sub_title',
                'icon_fid',
            ])
                ->find(4);
        });
        if ($m) {
            return [
                'title'     => $m->title,
                'sub_title' => $m->sub_title,
                'icon'      => $m->icon_fid ? $m->_icon_asset(NULL, ['only_way' => FALSE]) : NULL
            ];
        }

        return NULL;
    }

    public static function getIngredientsList()
    {
        return ParamItem::where('param_id', 5)
            ->pluck('title', 'id');
    }

    public function getVariableIngredientsAttribute()
    {
        return (array)json_decode($this->attributes['variable_ingredients']);
    }

    public function setVariableIngredientsAttribute($value = [])
    {
        $this->attributes['variable_ingredients'] = json_encode($value);
    }

    public function getShortcut($options = [])
    {
        if (!is_bool($this->view_access)) return NULL;
        $_options = array_merge([
            'view'  => NULL,
            'index' => NULL,
            'items' => collect([]),
        ], $options);
        if ($_options['items']->isNotEmpty()) {
            $_options['items']->transform(function ($_item) {
                if (method_exists($_item, '_load')) $_item = $_item->_load('teaser');

                return $_item;
            });
            $_template = [];
            if (isset($_options['view']) && $_options['view']) {
                $_template = [
                    "frontend.{$this->deviceTemplate}.{$_options['view']}",
                    "frontend.default.{$_options['view']}",
                ];
            }
            $_template = array_merge($_template, [
                "frontend.{$this->deviceTemplate}.shops.shortcut_products",
                'frontend.default.shops.shortcut_products',
            ]);

            return View::first($_template, ['_items' => $_options['items']])
                ->render(function ($view, $_content) {
                    return clear_html($_content);
                });
        }

        return NULL;
    }


}

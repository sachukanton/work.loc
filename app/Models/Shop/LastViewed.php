<?php

    namespace App\Models\Shop;

    use App\Library\BaseModel;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    class LastViewed extends BaseModel
    {

        protected $table = 'shop_product_last_viewed';
        protected $guarded = [];
        public $timestamps = FALSE;
        protected $dates = [
            'added_at'
        ];
        const LAST_VIEWED_LIMIT_VIEW = 10;

        public function __construct()
        {
            parent::__construct();
        }

        public function _products()
        {
            return $this->hasOne(Product::class, 'id', 'product_id')
                ->with([
                    '_alias',
                    '_preview',
                    '_prices',
                    '_brand',
                ]);
        }

        public static function set(Product $product)
        {
            $_items = NULL;
            $_user = Auth::user();
            if ($_user) {
                if ($product->id) {
                    self::updateOrCreate([
                        'product_id' => $product->id,
                        'user_id'    => $_user->id,
                    ], [
                        'product_id' => $product->id,
                        'user_id'    => $_user->id,
                        'added_at'   => Carbon::now()
                    ]);
                }
                $_items = self::where('user_id', $_user->id)
                    ->orderByDesc('added_at')
                    ->get();
                $_count_view = self::LAST_VIEWED_LIMIT_VIEW;
                if ($_items->isNotEmpty()) {
                    $_items = $_items->map(function ($_product) use (&$_count_view) {
                        $_count_view--;
                        if ($_count_view >= 0) {
                            return $_product;
                        } else {
                            $_product->delete();

                            return FALSE;
                        }
                    })->filter(function ($_item_data) {
                        return $_item_data;
                    });
                } else {
                    $_items = NULL;
                }
            } else {
                $_items = Session::get('product_last_viewed', collect([]));
                if ($product->id && $_items->has($product->id)) {
                    $_item = $_items->get($product->id);
                    $_item->added_on = Carbon::now()->timestamp;
                    $_items->put($product->id, $_item);
                } elseif ($product->id) {
                    $_item = (object)[
                        'product_id' => $product->id,
                        'added_at'   => Carbon::now()->timestamp
                    ];
                    $_items->put($product->id, $_item);
                }
                $_count_view = self::LAST_VIEWED_LIMIT_VIEW;
                if ($_items->isNotEmpty()) {
                    $_items = $_items->sortByDesc('added_at')->map(function ($_drug) use (&$_count_view) {
                        $_count_view--;
                        if ($_count_view >= 0) {
                            return $_drug;
                        } else {
                            return FALSE;
                        }
                    })->filter(function ($_item_data) {
                        return $_item_data;
                    });
                    Session::put('product_last_viewed', $_items);
                } else {
                    Session::forget('product_last_viewed');
                    $_items = NULL;
                }
            }

            return $_items;
        }

        public static function get(Product $exclude = NULL)
        {
            $_response = collect([]);
            $_user = Auth::user();
            if ($_user) {
                if (Session::has('product_last_viewed') && ($_items = Session::get('product_last_viewed', collect([])))) {
                    $_items->map(function ($_product) use ($_user) {
                        self::updateOrCreate([
                            'product_id' => $_product->product_id,
                            'user_id'    => $_user->id,
                        ], [
                            'product_id' => $_product->product_id,
                            'user_id'    => $_user->id,
                            'added_at'   => Carbon::createFromTimestamp($_product->added_at)
                        ]);
                    });
                    Session::forget('product_last_viewed');
                }
                $_items = Product::from('shop_products as p')
                    ->leftJoin('shop_product_last_viewed as v', 'v.product_id', '=', 'p.id')
                    ->where('v.user_id', $_user->id)
                    ->when($exclude, function ($query) use ($exclude) {
                        $query->where('v.product_id', '<>', $exclude->id);
                    })
                    ->orderByDesc('v.added_at')
                    ->with([
                        '_alias',
                        '_preview',
                        '_price'
                    ])->get([
                        'p.id',
                        'p.title',
                        'p.sku',
                        'p.preview_fid',
                        'p.brand_id'
                    ]);
                if ($_items->isNotEmpty()) {
                    $_count_view = self::LAST_VIEWED_LIMIT_VIEW;
                    $_response = $_items->map(function ($_product) use (&$_count_view) {
                        $_count_view--;
                        if ($_count_view >= 0) {
                            if (method_exists($_product, '_load')) $_product->_load('teaser');

                            return $_product;
                        } else {
                            self::where('product_id', $_product->id)
                                ->delete();

                            return FALSE;
                        }
                    })->filter(function ($_item_data) {
                        return $_item_data;
                    });
                }
            } else {
                $_items = Session::get('product_last_viewed', collect([]));
                if ($_items->isNotEmpty()) {
                    $_items = Product::whereIn('id', $_items->keys())
                        ->when($exclude, function ($query) use ($exclude) {
                            $query->where('id', '<>', $exclude->id);
                        })
                        ->with([
                            '_alias',
                            '_preview',
                            '_prices',
                            '_brand',
                        ])->get([
                            'id',
                            'title',
                            'sku',
                            'preview_fid',
                            'brand_id'
                        ]);
                    if ($_items->isNotEmpty()) {
                        $_response = $_items->transform(function ($_product) {
                            if (method_exists($_product, '_load')) $_product->_load('teaser');

                            return $_product;
                        });
                    }
                }
            }

            return $_response;
        }

    }

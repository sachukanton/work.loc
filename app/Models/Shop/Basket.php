<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use App\Models\Pharm\PharmCity;
use App\Models\Pharm\PharmPharmacy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class Basket extends BaseModel
{

    protected $table = 'basket';
    protected $primaryKey = 'key';
    protected $fillable = [
        'key',
        'user_id',
        'data',
        'last_load_at'
    ];
    public $timestamps = FALSE;
    protected $dates = [
        'last_load_at'
    ];
    public $compositionLoad = NULL;
    public $delivery = 'delivery';
    public $certificate = NULL;
    public $certificateClear = FALSE;
    public $total_amount = 0;
    public $total_amount_without_modification = 0;

    public function __construct($entity = NULL)
    {
        parent::__construct();
    }

    public function getDataAttribute()
    {
        return isset($this->attributes['data']) && $this->attributes['data'] ? unserialize($this->attributes['data']) : NULL;
    }

    public function getPricesAttribute()
    {
        $_response = collect([]);
        $_data = $this->data;
        if ($_data) {
            $_response = Price::whereIn('id', array_keys($this->data))
                ->remember(REMEMBER_LIFETIME)
                ->get([
                    'id',
                    'product_id',
                    'pharmacy_id',
                    'quantity_id',
                    'multiplicity',
                    'part',
                ])->keyBy('id');
            $_response->transform(function ($_item) use ($_data) {
                $_item->quantity = $_data[$_item->id];

                return $_item;
            });
        }

        return $_response;
    }

    public function getQuantityInAttribute()
    {
        $_response = 0;
        $_composition = $this->compositionLoad ? : $this->composition;
        $_composition->map(function ($p) use (&$_response) {
            $_response += $p->quantity;
        });

        return $_response;
    }

    public function getAmountAttribute()
    {
        $_total_amount = $this->total_amount;
        $_total_amount_without_modification = $this->total_amount_without_modification;
        $_config = config('os_shop');
        if ($this->delivery == 'pickup') {
            $_percent = config('os_shop.delivery_pickup_percent', 10);
            $_total_amount = $_total_amount - ceil($_total_amount_without_modification * ($_percent / 100));
        }
        if ($this->delivery != 'pickup' && $_total_amount < $_config['delivery_free_amount']) {
            $_total_amount += $_config['delivery_amount'];
        }

        return transform_price($_total_amount);
    }

    public function getSkuListAttribute()
    {
        $_response = NULL;
        $_composition = $this->compositionLoad ? : $this->composition;
        if ($_composition) foreach ($_composition as $_product) $_response[] = "'{$_product->sku}'";
        $_response = $_response ? '[' . implode(',', $_response) . ']' : NULL;

        return $_response;
    }

    public function getProductListAttribute()
    {
        $_response = NULL;
        $_composition = $this->compositionLoad ? : $this->composition;
        if ($_composition) {
            foreach ($_composition as $_product) {
                $_t = str_replace("'", "\'", $_product->title);
                $_response[] = "{id: '{$_product->sku}', sku: '{$_product->sku}', name: '{$_t}', quantity: {$_product->quantity}}";
            }
        }
        $_response = $_response ? '[' . implode(',', $_response) . ']' : NULL;

        return $_response;
    }

    public function getDeliveryAmount()
    {
        $_config = config('os_shop');
        if (!$_config['delivery_pickup_percent']) return 0;
        $_amount = $this->total_amount_without_modification;
        $_delivery_amount = ceil($_amount * ($_config['delivery_pickup_percent'] / 100));

        return transform_price($_delivery_amount)['format']['price'];
    }

    public function getCompositionAttribute()
    {
        $_response = collect([]);
        $_certificate = NULL;
        $_data = $this->data;
        if (is_null($this->certificate) && $this->certificateClear == FALSE) {
            $_certificate = Cookie::get('frontPad_certificate');
        } elseif ($this->certificate) {
            $_certificate = json_encode($this->certificate);
        }
        if ($_certificate && is_null($this->compositionLoad)) {
            $_certificate = (array)json_decode($_certificate);
            $_discount_amount = 0;
            if (isset($_certificate['sale'])) {
                $_certificate_type = 'sale';
                $_certificate_application = trans('shop.certificate.sale', ['sale' => $_certificate['sale']]);
            } elseif (isset($_certificate['product_id'])) {
                $_certificate_type = 'product';
                $_product = Product::leftJoin('shop_product_prices', 'shop_products.id', '=', 'shop_product_prices.product_id')
                    ->where('sku', $_certificate['product_id'])
                    ->first([
                        'shop_products.title',
                        'shop_product_prices.id',
                        'shop_product_prices.base_price',
                        DB::raw('(select count(`shop_product_param`.`param_item_id`) from `shop_product_param` where `shop_product_param`.`model_id` = `shop_products`.`id` and `shop_product_param`.`param_item_id` = ' . Product::ID_MARK_SPICY . ') as spicy')
                    ]);
                $_data[$_product->id][$_product->spicy]['certificate'] = [
                    'quantity'    => 1,
                    'price'       => (float)$_certificate['price'],
                    'composition' => NULL
                ];
                $_certificate_application = trans('shop.certificate.product', ['product' => $_product->title]);
                $_discount_amount = (float)($_product->base_price - $_certificate['price']);
            } else {
                $_certificate_type = 'amount';
                $_certificate_application = trans('shop.certificate.amount', ['amount' => $_certificate['amount']]);
                $_discount_amount = (float)$_certificate['amount'];
            }
            $this->certificate = array_merge([
                'type'            => $_certificate_type,
                'discount_amount' => $_discount_amount,
                'application'     => $_certificate_application
            ], $_certificate);
        }
        if ($_data && is_null($this->compositionLoad)) {
            $_basket = $this;
            $_basket->total_amount = 0;
            $_basket->total_amount_without_modification = 0;
            $_basket_composition = $_data;

            $_response = Product::from('shop_products as p')
                ->leftJoin('shop_product_prices as sp', 'sp.product_id', '=', 'p.id')
                ->with([
                    '_alias',
                    '_param_items',
                    '_preview',
                    '_price'
                ])
                ->whereIn('sp.id', array_keys($_basket_composition))
                ->select([
                    'p.id',
                    'p.sku',
                    'p.iiko_id',
                    'p.title',
                    'p.preview_fid',
                    'p.full_fid',
                    'p.mark_hit',
                    'p.mark_new',
                    'p.sort',
                    'p.status',
                    'p.use_spicy',
                    'p.brand_id',
                    'p.multiplicity',
                    'sp.id as price_id',
                    'sp.part as price_part',
                    'sp.quantity_id as quantity_id',
                ])
                ->remember(REMEMBER_LIFETIME)
                ->get();
            if ($_response->isNotEmpty()) {
                $_response->transform(function ($_item) use (&$_total_amount, $_basket_composition, $_basket) {
                    $_item->priceId = $_item->price_id;
                    $_item->basket = $_basket;
                    $_item->product_mark = $_item->mark[0] ?? NULL;
                    $_item->subtract = $_item->quantity;
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
                    if (isset($_params_output[$_item::ID_WEIGHT])) {
                        $_item->weight = $_params_output[$_item::ID_WEIGHT];
                        $_item->weight['options'] = (int)$_item->weight['options'];
                    } else {
                        $_item->weight = [];
                    }
                    $_item->marks = $_params_output[$_item::ID_MARKS] ?? [];
                    $_item->paramOptions = $_params_output;
                    $_price = $_item->_render_price();
                    $_item->price = isset($_price['view'][1]) ? $_price['view'][1] : $_price['view'][0];
                    $_item->price_certificate = NULL;
                    if ($_basket->certificate && $_basket->certificate['type'] == 'product' && $_item->sku == $_basket->certificate['product_id']) {
                        $_item->price_certificate = view_price($_basket->certificate['price'], $_basket->certificate['price']);
                    }
                    $_amount_products_without_modification = 0;
                    $_amount = 0;
                    $__quantity = 0;
                    $__composition = [];
                    foreach ($_basket_composition[$_item->price_id] as $_type => $_rows) {
                        foreach ($_rows as $_key => $_row) {
                            $__weight = array_merge([], $_item->weight);
                            $__marks = array_merge([], $_item->marks);
                            $__quantity+=$_row['quantity'];
                            $__additions_amount = 0;
                            if (is_array($_row['composition'])) {
                                foreach ($_row['composition'] as $_t => $_r) {
                                    foreach ($_r as $_t1 => $_r1) {
                                        $__additions_amount += $_r1['amount'];
                                        if ($__weight) {
                                            if ($_t == 'ingredients') {
                                                $__weight['options'] -= (int)$_r1['weight'] * (int)$_r1['quantity'];
                                            } else {
                                                $__weight['options'] += (int)$_r1['weight'] * (int)$_r1['quantity'];
                                            }
                                        }
                                    }
                                }
                            }
                            $_tp = (float)$_item->price['format']['price'];
                            if ($_basket->certificate && $_key === 'certificate') {
                                $_tp = (float)$_item->price_certificate['format']['price'];
                            }
                            $__price = $__additions_amount + $_tp;
                            $__amount = $__price * $_row['quantity'];
                            $_amount_products_without_modification += $_tp * $_row['quantity'];
                            $_amount += $__amount;
                            if ($_type == 1) {
                                $_m = $_item->getSpicyMark();
                                if ($__marks && !isset($__marks['options'][4]) && $_m) {
                                    $__marks['options'][4] = $_m;
                                } elseif ($_m) {
                                    $__marks = [
                                        'title'   => NULL,
                                        'unit'    => NULL,
                                        'options' => [
                                            4 => $_m
                                        ]
                                    ];
                                }
                            } elseif ($__marks && isset($__marks['options'][4])) {
                                unset($__marks['options'][4]);
                                if (!$__marks['options']) $__marks = [];
                            }
                            $__composition[] = [
                                'key'              => $_key,
                                'spicy'            => $_type,
                                'use_spicy'        => (boolean)$_item->use_spicy,
                                'marks'            => $__marks,
                                'quantity'         => $_row['quantity'],
                                'weight'           => $__weight,
                                'price'            => transform_price($__price),
                                'additions_amount' => $__additions_amount ? transform_price($__additions_amount) : NULL,
                                'amount'           => transform_price($__amount),
                                'composition'      => $_row['composition'],
                                'sort'             => ($_basket->certificate && $_key === 'certificate' ? -100 : ($_key == 0 ? 0 : 1)),
                            ];
                        }
                    }
                    $_item->amount = transform_price($_amount);
                    $_basket->total_amount += $_item->amount['format']['price'];
                    $_basket->total_amount_without_modification += $_amount_products_without_modification;
                    $_item->quantity = $__quantity;


            

                    $_item->composition = collect($__composition)
                        ->sortBy('sort');

                    return $_item;
                });
            }
            if ($this->certificate && $this->certificate['type'] == 'sale') {
                $_amount = ceil($_basket->total_amount_without_modification * ($this->certificate['sale'] / 100));
                $_amount = transform_price($_amount);
                $_basket->certificate['discount_amount'] = $_amount['format']['price'];
                $_basket->total_amount -= $_basket->certificate['discount_amount'];
            } elseif ($this->certificate && $this->certificate['type'] == 'amount') {
                $_basket->total_amount -= $_basket->certificate['discount_amount'];
                if ($_basket->total_amount <= 0) $_basket->total_amount = 0;
            }
            $this->compositionLoad = $_response;
        } elseif ($this->compositionLoad) {
            $_response = $this->compositionLoad;
        }

        return $_response;
    }

    public function getHasStoreAttribute()
    {
        if ($this->data) {
            return Price::whereIn('id', array_keys($this->data))
                ->where('default', 1)
                ->exists();
        }

        return FALSE;
    }

    public function getOrderRequest(Request $request)
    {
        $_composition = $this->composition;
        if ($_composition->isEmpty()) return NULL;
        if ($request->input('type') == 'quick') {
            $_data = $request->only([
                'name',
                'phone'
            ]);
        } else {
            $_data = $request->only([
                'name',
                'phone',
                'delivery_method',
                'delivery_address',
                'payment_method',
                'pre_order_at',
                'comment',
            ]);
        }
        $_phone = '+' . preg_replace('/^\+|\D/m', '', $_data['phone']);
        $_delivery_method = $_data['delivery_method'] ?? 'pickup';
        $_items = [];
        $_composition->map(function ($p) use (&$_items) {
            foreach ($p->composition as $_spicy => $_comp) {
                $_items[] = [
                    'id'     => $p->iiko_id,
                    'code'   => $p->sku,
                    'amount' => (int)$_comp['quantity'],
                    'sum'    => (float)$_comp['price']['original']['price'],
                ];
            }
        });
        $_pre_order_at = $_data['pre_order_at'] ?? NULL;
        if ($_pre_order_at) {
            $_pre_order_at = Carbon::parse($_pre_order_at);
            $_new_at = Carbon::now();
            $_pre_order_at = $_new_at->diffInMinutes($_pre_order_at) < 90 ? NULL : $_pre_order_at->toDateTimeString();
        }
        $iiko = app('iiko');
        $organization = $iiko->OrganizationsApi()->getList()[0];
        $_response = [
            'organization' => $organization['id'],
            'customer'     => [
                'name'  => $_data['name'],
                'phone' => $_phone,
            ],
            'order'        => [
                'date'         => $_pre_order_at,
                'items'        => $_items,
                'phone'        => $_phone,
                'customerName' => $_data['name'],
                'comment'      => $_data['comment'] ?? NULL,
            ],
            'coupon'       => NULL,
        ];
        if ($_delivery_method == 'pickup') {
            $_response['order']['isSelfService'] = TRUE;
        } else {
            $_response['order']['address'] = [
                'city'      => 'Харьков',
                'street'    => $_data['delivery_address']['street'],
                'home'      => $_data['delivery_address']['house'],
                'floor'     => $_data['delivery_address']['floor'],
                'apartment' => $_data['delivery_address']['apartment'],
            ];
        }
//        if ($_payment_method == 'cash'){
//            $_response['order']['paymentItems'] = [
//                'code' => 'CASH'
//            ];
//        }else{
//            $_response['order']['paymentItems'] = [
//                'code' => 'CARD'
//            ];
//        }
//        dd($_response);
//        $problems = $iiko->OrdersApi()->checkCreate($_response);
//        dd($problems, $_response);

        return $_response;
    }

    public function render_key()
    {
        return md5(uniqid(microtime(), TRUE));
    }

    public static function init($attrs = [])
    {
        $_basket = NULL;
        $__basket = NULL;
        $_user = Auth::user();
        $_basket_key = Cookie::get('basket_key');
        if ($_basket_key) {
            $_basket_key = trim($_basket_key);
            $__basket = self::where('key', $_basket_key)
                ->first();
        }
        if ($_user) {
            $_basket = self::where('user_id', $_user->id)
                ->first();
            if (($_basket && $_basket_key && $_basket->key != $_basket_key) || (is_null($_basket) && $__basket)) {
                if ($__basket) {
                    if ($_basket instanceof self) $_basket->delete();
                    $__basket->user_id = $_user->id;
                    $_basket = $__basket;
                }
            }
        }
        if (is_null($_basket)) {
            if ($__basket) {
                if (is_array($attrs) && count($attrs)) {
                    foreach ($attrs as $attr => $value) {
                        $__basket->{$attr} = $value;
                    }
                }
                $__basket->last_load_at = Carbon::now()->format('Y-m-d H:i:s');
                $__basket->save();
                $__basket->composition;

                return $__basket;
            } else {
                Cookie::queue(Cookie::forget('basket_key'));
            }
        } elseif ($_basket) {
            if (is_array($attrs) && count($attrs)) {
                foreach ($attrs as $attr => $value) {
                    $_basket->{$attr} = $value;
                }
            }
            $_basket->last_load_at = Carbon::now()->format('Y-m-d H:i:s');
            $_basket->save();
            $_basket->composition;
            Cookie::queue(Cookie::make('basket_key', $_basket->key));

            return $_basket;
        }

        return new self;
    }

    public function add(Price $price, $count = 1, $spicy = 0, $composition = NULL)
    {
        $_location = NULL;
        $_product = $price->_product;
        $_composition = $this->exists == FALSE ? [] : $this->data;
        if ($price->status == 'in_stock') {
            $_count = 0;
            if ($price->part) $_count = $count * (1 / $_product->multiplicity);
            $_quantity = $price->count_in_stock;
            $_basket_balance = $this->product_balance($price);
            $_quantity -= ($_basket_balance + $_count);
            if ($_quantity < 0) return FALSE;
        }
        if ($price->status == 'not_available') return FALSE;
        $_key_row = 0;
        $__composition = NULL;
        $__composition_amount = 0;
        if ($composition) {
            $_key_composition = [];
            foreach ($composition as $_type => $_data) {
                foreach ($_data as $_value) {
                    $_key_composition[] = $_value['id'];
                    $__composition[$_type][$_value['id']] = [
                        'title'    => $_value['title'],
                        'sku'      => $_value['sku'],
                        'price'    => (float)$_value['price'],
                        'weight'   => $_value['weight'],
                        'quantity' => (int)$_value['quantity'],
                        'amount'   => (int)$_value['quantity'] * (float)$_value['price'],
                    ];
                    $__composition_amount += (int)$_value['quantity'] * (float)$_value['price'];
                }
            }
            sort($_key_composition);
            $_key_row = implode('_', $_key_composition);
        }
        $_count = $count + ($_composition[$price->id][$spicy][$_key_row]['quantity'] ?? 0);
        //        if ($price->part && $_product->multiplicity > 1) {
        //            $_whole = intdiv($_count, $_product->multiplicity);
        //            if ($_whole && ($_whole_entity = $price->_whole_packing())) {
        //                $_composition[$_whole_entity->id] = $_whole + ($_composition[$_whole_entity->id] ?? 0);
        //                $_count -= ($_whole * $_product->multiplicity);
        //            }
        //        }
        if ($_count) {
            $_composition[$price->id][$spicy][$_key_row] = [
                'quantity'    => $_count,
                'certificate' => FALSE,
                'composition' => $__composition
            ];
        } elseif (isset($_composition[$price->id])) {
            unset($_composition[$price->id]);
        }

        return [
            'basket'             => $this->bSave($_composition),
            'composition_amount' => $__composition_amount
        ];
    }

    public function recount($composition)
    {
        $_data = $this->data;
        foreach ($composition as $_price => $_quantity) {
            $_keys = explode('::', $_price);
            if (isset($_data[$_keys[0]][$_keys[1]][$_keys[2]]['quantity'])) {
                $_data[$_keys[0]][$_keys[1]][$_keys[2]]['quantity'] = $_quantity;
            }
        }

        return $this->bSave($_data);
    }

    public function check_availability()
    {
        $_response = [
            'state'         => TRUE,
            'pharmacies'    => [],
            'not_available' => [],
        ];
        $_prices = $this->prices->groupBy('pharmacy_id');
        foreach ($_prices as $_pharmacy_id => $_data) {
            if (!$_pharmacy_id) $_pharmacy_id = 0;
            foreach ($_data as $_price) {
                $_product = $_price->_product;
                if ($_price->part) {
                    $_quantity = $_price->quantity * (1 / $_product->multiplicity);
                } else {
                    $_quantity = $_price->quantity;
                }
                if (!isset($_response['pharmacies'][$_pharmacy_id][$_product->id]['in_store'])) {
                    $_response['pharmacies'][$_pharmacy_id][$_product->id]['in_store'] = $_price->count_in_stock;
                    $_response['pharmacies'][$_pharmacy_id][$_product->id]['product'] = $_price->_product;
                    $_response['pharmacies'][$_pharmacy_id][$_product->id]['pharmacy'] = $_price->_pharmacy;
                }
                $_response['pharmacies'][$_pharmacy_id][$_product->id]['in_order'] = $_quantity + ($_response['pharmacies'][$_pharmacy_id][$_product->id]['in_order'] ?? 0);
            }
        }
        foreach ($_response['pharmacies'] as $_pharmacy_id => $_products) {
            foreach ($_products as $_product_id => $_product) {
                $_diff = $_product['in_store'] - $_product['in_order'];
                $_order = $_diff >= 0 ? TRUE : FALSE;
                $_product['diff'] = $_diff;
                $_product['order'] = $_order;
                $_response['pharmacies'][$_pharmacy_id][$_product_id] = $_product;
                if (!$_order) {
                    $_response['state'] = FALSE;
                    $_response['not_available'][$_pharmacy_id]['pharmacy'] = $_product['pharmacy']->exists ? $_product['pharmacy'] : NULL;
                    $_response['not_available'][$_pharmacy_id]['products'][$_product_id] = $_product;
                }
            }
        }

        return $_response;
    }

    public function product_balance(Price $price)
    {
        $_quantity = 0;
        if ($this->prices) {
            $this->prices->each(function ($_item) use (&$_quantity, $price) {
                if ($_item->id == $price->id && isset($this->data[$price->id])) {
                    $_quantity += $price->part ? $this->data[$price->id] * (1 / $_item->multiplicity) : $this->data[$price->id];
                } elseif ($_item->id != $price->id && $_item->product_id == $price->product_id && $_item->pharmacy_id == $price->pharmacy_id && isset($this->data[$_item->id])) {
                    $_quantity += $_item->part ? $this->data[$_item->id] * (1 / $_item->multiplicity) : $this->data[$_item->id];
                }
            });
        }

        return (float)$_quantity;
    }

    public function bSave($composition)
    {
        global $wrap;
        if ($this->exists == FALSE) {
            $_new_key = $this->render_key();
            Cookie::queue(Cookie::make('basket_key', $_new_key));
            $this->key = $_new_key;
            $this->fill([
                'key'          => $_new_key,
                'user_id'      => $wrap['user']->id ?? NULL,
                'data'         => serialize($composition),
                'last_load_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
            $this->save();
            $this->key = $_new_key;
            $_response = $this;
        } else {
            $this->data = serialize($composition);
            $this->save();
            $_response = $this;
        }
        $this->compositionLoad = NULL;
        $this->composition;

        return $_response;
    }

    public function bClear()
    {
        if ($this->exists) {
            $this->delete();
            Cookie::queue(Cookie::forget('basket_key'));
        }
    }

    public function getFormationOrders()
    {
        $_response = NULL;
        $_composition = $this->composition;
        if ($_composition->isNotEmpty()) {
            $_composition->each(function ($_product) use (&$_response) {
                foreach ($_product->composition as $_spicy => $_comp) {
                    $_response[] = [
                        'id'          => $_product->id,
                        'sku'         => $_product->sku,
                        'title'       => $_product->title,
                        'price'       => (float)$_comp['price']['format']['price'],
                        'quantity'    => (int)$_comp['quantity'],
                        'amount'      => (float)$_comp['amount']['format']['price'],
                        'spicy'       => $_comp['use_spicy'] ? $_comp['spicy'] : NULL,
                        'composition' => $_comp['composition'],
                        'certificate' => $_comp['key'] === 'certificate' ? 1 : 0,
                        'product'     => $_product,
                    ];
                }
            });
        }

        return $_response;
    }

    public function setDelivery($delivery = 'delivery')
    {
        $this->delivery = $delivery;
    }

    public function show_checkout_form($basket)
    {
        $_response = NULL;
        if ($this->composition->isNotEmpty()) {
            $_has_store = $this->has_store;
            $_form_id = 'form-checkout-order';
            $_user = Auth::user();
            $_shop_settings = config('os_shop');
            $_payment_fields_values = [];
            foreach ($_shop_settings['payment_method'] as $_type => $_value) {
                if (!$_value['use']) continue;
                $_payment_fields_values[$_type] = [
                    $_value['title'],
                    $_value['help'],
                ];
            }
            $_delivery_fields_values = [];
            foreach ($_shop_settings['delivery_method'] as $_type => $_value) {
                if (!$_value['use']) continue;
                $_delivery_fields_values[$_type] = $_value['title'];
            }
            $_response = form_generate([
                'id'         => $_form_id,
                'ajax'       => FALSE,
                'action'     => _r('ajax.shop_buy'),
                'form_class' => 'form-checkout use-ajax',
                'fields'     => [
                    field_render('type', [
                        'type'    => 'hidden',
                        'value'   => 'quick',
                        'form_id' => $_form_id,
                    ]),
                    '<div class="tabs"><ul class="tabs__caption" uk-tab="connect: #uk-tab-checkout-form; animation: uk-animation-fade; swiping: false;">
                    <li class="uk-active"><a href="#" data-type="quick">',
                    variable('quick'),
                    '</a></li>
                    <li><a href="#" data-type="full">',
                    variable('full'),
                    '</a></li></ul></div>',
                    '<div class="form__main tabs__content"><div class="uk-grid-small uk-grid uk-margin-top uk-margin-bottom"><div class="row"><label>',
                    trans('forms.fields.checkout.name'),
                    '</label>',
                    field_render('name', [
                        'value'      => $_user->_profile->name ?? NULL,
                        'required'   => TRUE,
                        'attributes' => [
                            'placeholder' => trans('forms.fields.checkout.name'),
                            'class'       => ' ',
                        ],
                        'form_id'    => $_form_id,
                    ]),
                    '</div><div class="row"><label>',
                    trans('forms.fields.checkout.phone'),
                    '</label>',
                    field_render('phone', [
                        'value'      => $_user->_profile->phone ?? NULL,
                        'attributes' => [
                            'placeholder' => trans('forms.fields.checkout.phone'),
                            'class'       => 'phone-mask',
                        ],
                        'form_id'    => $_form_id,
                        'required'   => TRUE,
                    ]),
                    '</div></div>',
                    '<div><ul id="uk-tab-checkout-form" class="uk-switcher"><li class="uk-active"><span class="cash">',
                    variable('cash'),
                    '</span></li><li>',
                    '<div class="uk-birthday">',
                    // field_render('birthday', [
                    //     'form_id' => $_form_id,
                    //     'type'    => 'checkbox',
                    //     'values'  => [
                    //         1 => trans('forms.fields.checkout.birthday') . '<img uk-img="data-src:template/images/icon-birthday.svg"
                    //                  alt="" width="45" height="44">',
                    //     ],
                    // ]),
                    '</div>',
                    '<h3>' . trans("forms.labels.checkout.delivery_method") . '</h3>',
                    $_has_store ? field_render('delivery_method', [
                        'type'       => 'radio',
                        'selected'   => $basket->delivery,
                        'values'     => $_delivery_fields_values,
                        'form_id'    => $_form_id,
                        'class'      => 'use-ajax',
                        'attributes' => [
                            'data-path' => _r('ajax.checkout_delivery_box'),
                        ]
                    ]) : NULL,
                    '<label>' . trans("forms.labels.checkout.datepicker") . '</label>',
                    '<div class="box-datepicker uk-position-relative">',
                    field_render('pre_order_at', [
                        'value'      => Carbon::now()->format('d.m.Y H:i'),
                        'attributes' => [
                            'class' => 'uk-input uk-datepicker',
                        ],
                        'form_id'    => $_form_id,
                    ]),
                    '<h3>' . variable('city') . '</h3>',
                    view('frontend.default.shops.checkout_delivery_fields', [
                        'type'    => $basket->delivery,
                        'form_id' => $_form_id
                    ]),
                    '</div>',
                    '<label>' . variable('comment') . '</label>',
                    field_render('comment', [
                        'type'       => 'textarea',
                        'value'      => NULL,
                        'attributes' => [
                            'rows'        => 1,
                            'placeholder' => trans('forms.fields.checkout.comments'),
                            'class'       => 'text-area uk-textarea'
                        ],
                        'form_id'    => $_form_id,
                    ]),
                    '<h6>' . trans("forms.labels.checkout.payment_method") . '</h6>',
                    '<div class="uk-payment uk-position-relative">',
                    field_render('payment_method', [
                        'type'     => 'radio',
                        'selected' => 'cash',
                        'values'   => $_payment_fields_values,
                        'form_id'  => $_form_id,
                        'class'    => 'uk-checkbox',
                    ]),
                    '<div class="uk-surrender">',
                    field_render('surrender', [
                        'form_id'    => $_form_id,
                        'type'       => 'number',
                        'attributes' => [
                            'placeholder' => trans('forms.fields.checkout.surrender'),
                            'min'         => '0',
                        ],
                    ]),
                    '</div></div>',
                    // '<div id="certificate-box" class="uk-grid-small uk-grid uk-margin-medium-top certificate-box"><div class="uk-width-expand">',
                    // field_render('certificate', [
                    //     'form_id'    => $_form_id,
                    //     'value'      => $this->certificate ? $this->certificate['certificate'] : NULL,
                    //     'attributes' => [
                    //         'placeholder' => "Промокод",
                    //     ],
                    //     'suffix'     => '<div class="description-certificate uk-text-small" style="color: #fff">' . ($this->certificate ? $this->certificate['application'] : NULL) . '</div>'
                    // ]),
                    // '</div><div class="uk-width-auto">',
                    // '<button type="button" name="certificate" class="uk-button uk-button-success uk-position-relative' . ($this->certificate ? ' certificate-used' : NULL) . '">' . ($this->certificate ? trans('forms.buttons.checkout.clear') : trans('forms.buttons.checkout.use')) . '</button>',
                    // '</div></div>',
                    '</li></ul><div class="total"><div class="row" id="checkout-order-delivery-amount">' . $this->showDeliveryString() . '</div><div class="row sums" id="checkout-order-total-amount"><h6>',
                    trans('forms.labels.checkout.total_amount_3', ['amount' => '&nbsp;</h6><span class="price-amount">' . $this->amount['format']['view_price'] . '&nbsp;' . $this->amount['currency']['suffix'] . '</span> ']),
                    '</div></div>',


                    //                    field_render('person', [
                    //                        'form_id'    => $_form_id,
                    //                        'type'       => 'number',
                    //                        'attributes' => [
                    //                            'placeholder' => trans('forms.fields.checkout.person'),
                    //                            'min'         => '1',
                    //                        ],
                    //                    ]),
                    // field_render('comment', [
                    //     'type'       => 'textarea',
                    //     'value'      => NULL,
                    //     'attributes' => [
                    //         'rows'        => 1,
                    //         'placeholder' => trans('forms.fields.checkout.comments'),
                    //         'class'       => 'text-area uk-textarea'
                    //     ],
                    //     'form_id'    => $_form_id,
                    // ]),
                    '<div class="box-agreement">',
                    // field_render('call_me_back', [
                    //     'form_id'  => $_form_id,
                    //     'type'     => 'checkbox',
                    //     'selected' => 1,
                    //     'values'   => [
                    //         1 => trans('forms.fields.checkout.call_me_back'),
                    //     ],
                    // ]),
                    field_render('agreement', [
                        'form_id'  => $_form_id,
                        'type'     => 'checkbox',
                        'selected' => 1,
                        'values'   => [
                            1 => trans('forms.fields.checkout.agreement'),
                        ],
                    ]),
                    '</div>',
                    '</div>',


                ],
                'buttons'    => [
                    '<div class="active-checkout box-btn-default uk-margin-remove uk-text-right">',
                    //                    '<div class="uk-flex-1 box-agree">',
                    //                        field_render('agree', [
                    //                            'type'    => 'checkbox',
                    //                            'value'   => NULL,
                    //                            'values'  => [
                    //                                1 => trans('forms.fields.checkout.agree', ['link' => ''])
                    //                            ],
                    //                            'form_id' => $_form_id,
                    //                        ]),
                    //                    '</div>',
                    '<button type="submit" class="uk-button uk-button-default btn-submit uk-position-relative uk-overflow-hidden" value="1" name="send_form">' . trans('forms.buttons.checkout.to_issue') . '</button>',
                    '</div></div>'
                ]
            ]);
        }

        return $_response;
    }

    public static function recommended_checkout($is_page = FALSE)
    {
        $_response = NULL;
        $_products = ViewList::get('recommended_checkout');

        if ($_products->isNotEmpty()) {
            if ($_products->count() > ViewList::PRODUCT_VIEW_LIST_MAX_ITEM) $_products = $_products->random(ViewList::PRODUCT_VIEW_LIST_MAX_ITEM);


            $_products->transform(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });

            //            dd($_products);
            return [
                'object' => View::first([
                    'frontend.default.load_entities.view_lists_product_checkout'
                ], [
                    '_title'   => trans('shop.titles.view_list_recommended'),
                    '_items'   => $_products,
                    '_is_page' => $is_page,
                ])->render(function ($view, $_content) {
                    return clear_html($_content);
                })
            ];
        }

        return $_response;
    }

    public function show_checkout_products($basket, $is_page = FALSE)
    {
        return View::first([
            "frontend.{$this->deviceTemplate}.shops.checkout_products",
            "frontend.default.shops.checkout_products",
            'backend.base.shop_products_checkout'
        ], [
            '_items'   => $basket->composition,
            '_basket'  => $this,
            '_is_page' => $is_page,
        ])->render(function ($view, $_content) {
            return clear_html($_content);
        });
    }

    public function showDeliveryString()
    {
        $_response = NULL;
        $_certificate_amount = $this->certificate && $this->certificate['discount_amount'] ? $this->certificate['discount_amount'] : 0;
        $_amount = $this->total_amount;
        $_amount_without_modification = $this->total_amount_without_modification;
        $_config = config('os_shop');

        if ($_amount < $_config['min_amount']) {
            $_response .= '<h6>' . trans('shop.checkout.min_amount') . '</h6><span>' . $_config['min_amount'] . ' грн</span>';
        }

        if ($this->delivery != 'pickup') {
            // $_price = transform_price($_certificate_amount);
            // if ($_amount && ($_amount - $_config['delivery_free_amount']) < 0) {
            //     //                $_response = trans('forms.labels.checkout.delivery_amount', [
            //     //                    'amount' => $_config['delivery_amount'],
            //     //                    'min'    => $_config['delivery_free_amount'],
            //     //                ]);

            //     $_response .= '<h6>' . trans('shop.checkout.delivery_value') . '</h6><span>' . $_config['delivery_free_amount'] . ' грн</span>';


            // } else {
            //    $_response .= trans('forms.labels.checkout.delivery_free');
            // }
            //            if ($_certificate_amount) {
            //                $_price = transform_price($_certificate_amount);
            //                $_response .= '<div>' . trans('forms.labels.checkout.you_saved', [
            //                        'amount' => $_price['format']['view_price']
            //                    ]) . '</div>';
            //            }
        } elseif ($_config['delivery_pickup_percent']) {
            $_price = ceil($_amount_without_modification * ($_config['delivery_pickup_percent'] / 100)) + $_certificate_amount;
            $_delivery_amount = transform_price($_price);
            $_response = trans('forms.labels.checkout.you_saved', [
                'amount' => '<span>' . $_delivery_amount['format']['view_price'] . $this->amount['currency']['suffix'] . '</span>'
            ]);
        }

        return $_response;
    }

}

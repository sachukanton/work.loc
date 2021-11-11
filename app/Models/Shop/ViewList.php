<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use App\Library\Frontend;
use App\Models\Form\Review;

class ViewList extends BaseModel
{

    protected $table = 'shop_product_lists';
    protected $guarded = [];
    public $timestamps = FALSE;
    const PRODUCT_VIEW_LIST_MAX_ITEM = 10;
    const PRODUCT_VIEW_LIST = [
        'new'                  => 'shop.mark_lists.new',
        'hit'                  => 'shop.mark_lists.hit',
        'discount'             => 'shop.mark_lists.discount',
        'recommended_front'    => 'shop.mark_lists.recommended_front',
        'recommended_checkout' => 'shop.mark_lists.recommended_checkout',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function _product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')
            ->with([
                '_alias',
                '_param_items',
                '_preview',
                '_brand',
                '_price',
                //                    '_comments'
            ])
            ->withDefault();
    }

    public function updateDataProduct($product = NULL)
    {
        $_location = NULL;
        if (is_null($product)) {
            $_product = $this->_product;
        } elseif ($product instanceof Product) {
            $_product = $product;
        } else {
            $_product = Product::find($product);
        }
        $_product->update([
            'mark_hit'                  => $this->hit,
            'mark_new'                  => $this->new,
            'mark_recommended_front'    => $this->recommended_front,
            'mark_recommended_checkout' => $this->recommended_checkout,
        ]);
        $_prices = $_product->_prices->keyBy('location');
        $_price = $_prices->get($_location);
        $_price->update([
            'discount_price' => $this->discount ? request()->input('discount_price') : NULL
        ]);
    }

    public function clearDataProduct($product = NULL)
    {
        $_location = NULL;
        if (is_null($product)) {
            $_product = $this->_product;
        } elseif ($product instanceof Product) {
            $_product = $product;
        } else {
            $_product = Product::find($product);
        }
        if ($_product) {
            $_product->update([
                'mark_hit'                  => 0,
                'mark_new'                  => 0,
                'mark_recommended_front'    => 0,
                'mark_recommended_checkout' => 0,
            ]);
            $_prices = $_product->_prices->keyBy('location');
            $_price = $_prices->get($_location);
            $_price->update(['discount_price' => NULL]);
        }
    }

    public static function get($field = 'new')
    {
        $_response = NULL;
        try {
            $_response = Product::from('shop_products')
                ->join('shop_product_lists', 'shop_product_lists.product_id', '=', 'shop_products.id')
                ->where("shop_product_lists.{$field}", 1)
                ->with([
                    '_alias',
                    '_param_items',
                    '_preview',
                    '_category',
//                    '_brand',
                    '_price',
                    //                        '_comments'
                ])
                ->remember(REMEMBER_LIFETIME)
                ->select([
                    'shop_products.*'
                ])
                ->get();
        } catch (Exception $exception) {
        }

        return $_response;
    }

}

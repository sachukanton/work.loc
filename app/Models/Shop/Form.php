<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use App\Library\Frontend;
use App\Models\Form\Review;
use Illuminate\Support\Facades\DB;

class Form extends BaseModel
{

    protected $table = 'shop_forms';
    protected $guarded = [];
    const FORM_TYPE = [
        'buy_one_click'      => 'Купить в один клик',
        'submit_application' => 'Заказать товар',
        'notification'       => 'Уведомить о наличии'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function getTypeAttribute()
    {
        return self::FORM_TYPE[$this->form];
    }

    public function getDataAttribute()
    {
        return is_json($this->attributes['data']) ? json_decode($this->attributes['data']) : NULL;
    }

    public function getLinkToProductAttribute()
    {
        $_product = $this->_product;
        $_base_url = config('app.url');

        return $_product->exists && $_product->status ? "<a href='{$_base_url}{$_product->generate_url}' title='{$_product->title}'>{$_product->title}</a>" : $this->product_name;
    }

    public function getAvailabilityProductAttribute()
    {
        $_product = $this->_product;

        return DB::table('shop_product_quantity')
            ->where('product_1c', $_product->id_1c)
            ->where('pharm_1c', '00000000-0000-0000-0000-000000000000')
            ->where('quantity', '>', 1)
            ->exists();
    }

    /**
     * Relationships
     */
    public function _product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')
            ->with([
                '_alias'
            ])
            ->withDefault();
    }

    public function _notice()
    {
        return $this->hasOne(StockNotice::class, 'form_id', 'id')
            ->withDefault();
    }

}

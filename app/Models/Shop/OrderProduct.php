<?php

namespace App\Models\Shop;

use App\Library\BaseModel;


class OrderProduct extends BaseModel
{

    protected $table = 'order_products';
    protected $guarded = [];
    public $timestamps = FALSE;

    public function _product()
    {
        return $this->belongsTo(Product::class, 'product_id')
            ->with([
                '_alias',
                '_param_items',
                '_preview',
                '_brand',
                '_prices'
            ]);
    }

    public function getCompositionAttribute()
    {
        $_response = NULL;
        $c = $this->attributes['composition'] ? json_decode($this->attributes['composition']) : NULL;
        if ($c) {
            if (isset($c->default)) foreach ($c->default as $i) $_response['add'][] = $i;
            if (isset($c->additions)) foreach ($c->additions as $i) $_response['add'][] = $i;
            if (isset($c->ingredients)) foreach ($c->ingredients as $i) $_response['exclude'][] = $i;
        }

        return $_response;
    }
}

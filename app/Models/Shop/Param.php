<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use App\Library\Frontend;
use App\Models\Form\Review;

class Param extends BaseModel
{

    protected $table = 'shop_params';
    protected $guarded = [];
    public $translatable = [
        'title',
        'teaser_title',
        'seo_title'
    ];
    public $timestamps = FALSE;

    public function __construct()
    {
        parent::__construct();
    }

    public function _items()
    {
        return $this->hasMany(ParamItem::class, 'param_id')
            ->orderBy('sort');
    }

    public function _relation_item()
    {
        return $this->hasOne(ParamItem::class, 'param_id')
            ->withDefault();
    }

    public function _categories()
    {
        return $this->belongsToMany(Category::class, 'shop_category_param', 'param_id', 'model_id')
            ->withPivot([
                'visible_in_teaser',
                'collapse'
            ]);
    }

}

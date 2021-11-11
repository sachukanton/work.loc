<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use Illuminate\Database\Eloquent\Model;

class AdditionalItem extends BaseModel
{

    protected $table = 'shop_category_additional_items';
    protected $guarded = [];
    public $timestamps = FALSE;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * others
     */
    public function _ingredient()
    {
        return $this->hasOne(ParamItem::class, 'id', 'item_id');
    }

}

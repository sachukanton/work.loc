<?php

    namespace App\Models\Shop;

    use App\Library\BaseModel;
    use App\Library\Frontend;
    use App\Models\Form\Review;
    use App\Models\Pharm\PharmPharmacy;

    class Quantity extends BaseModel
    {

        protected $table = 'shop_product_quantity';
        protected $guarded = [];
        public $timestamps = FALSE;

        public function __construct()
        {
            parent::__construct();
        }

        public function _prices()
        {
            return $this->hasMany(Price::class, 'quantity_id');
        }

    }

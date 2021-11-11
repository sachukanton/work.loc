<?php

    namespace App\Models\Shop;

    use App\Library\BaseModel;
    use App\Library\Frontend;
    use App\Models\Form\Review;
    use App\Models\Pharm\PharmPharmacy;

    class MinPriceInPharmacy extends BaseModel
    {

        protected $table = 'view_shop_product_price';
        protected $guarded = [];
        public $timestamps = FALSE;
        public $incrementing = FALSE;

        public function __construct()
        {
            parent::__construct();
        }

    }

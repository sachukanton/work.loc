<?php

    namespace App\Models\Shop;

    use App\Library\BaseModel;
    use App\Library\Frontend;
    use App\Models\Form\Review;

    class Analog extends BaseModel
    {

        protected $table = 'shop_product_analog';
        protected $primaryKey = null;
        public $incrementing = false;
        protected $guarded = [];
        public $timestamps = FALSE;

        public function __construct()
        {
            parent::__construct();
        }

    }

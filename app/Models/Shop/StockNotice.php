<?php

    namespace App\Models\Shop;

    use App\Library\BaseModel;
    use App\Library\Frontend;
    use App\Models\Form\Review;

    class StockNotice extends BaseModel
    {

        protected $table = 'shop_product_stock_notice';
        protected $guarded = [];
        public $timestamps = FALSE;

        public function __construct()
        {
            parent::__construct();
        }

        public function _form()
        {
            return $this->belongsTo(Form::class, 'form_id', 'id')
                ->withDefault();
        }

    }

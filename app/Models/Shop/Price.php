<?php

    namespace App\Models\Shop;

    use App\Library\BaseModel;
    use App\Library\Frontend;
    use App\Models\Form\Review;
    use App\Models\Pharm\PharmPharmacy;

    class Price extends BaseModel
    {

        protected $table = 'shop_product_prices';
        protected $guarded = [];
        public $timestamps = FALSE;

        protected $casts = [
            'price'          => 'float',
            'old_price'      => 'float',
            'discount_price' => 'float',
            'base_price'     => 'float',
        ];

        public function __construct()
        {
            parent::__construct();
        }

        public function _product()
        {
            return $this->belongsTo(Product::class, 'product_id', 'id')
                ->with([
                    '_alias',
                    '_brand'
                ])
                ->select([
                    'id',
                    'title',
                    'multiplicity',
                    'preview_fid',
                    'sku',
                    'model',
                    'id_1c',
                    'brand_id',
                    'mark_hit',
                    'mark_new',
                    'mark_recommended_front',
                    'mark_recommended_checkout',
                    'sort',
                    'status',
                ])
                ->withDefault();
        }

        public function _pharmacy()
        {
            return $this->belongsTo(PharmPharmacy::class, 'pharmacy_id', 'id')
                ->with([
                    '_alias'
                ])
                ->select([
                    'id',
                    'pharm_city_id',
                    'pharm_city_district_id',
                    'title',
                    'breadcrumb_title',
                    'phones',
                    'email',
                    'address',
                    'working_hours',
                    'sort',
                    'status',
                ])
                ->withDefault();
        }

        public function _quantity()
        {
            return $this->hasOne(Quantity::class, 'id', 'quantity_id')
                ->withDefault();
        }

        public function _whole_packing()
        {
            $_response = NULL;
            if ($this->part) {
                $_response = self::with([
                    '_quantity'
                ])
                    ->where('product_id', $this->product_id)
                    ->where('pharmacy_id', $this->pharmacy_id)
                    ->where('part', 0)
                    ->first();
            }

            return $_response;
        }

        public function getCountInStockAttribute()
        {
            return (float)$this->_quantity->quantity;
        }

    }

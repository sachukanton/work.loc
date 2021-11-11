<?php

    namespace App\Models\Components;

    use App\Library\BaseModel;

    class SliderItems extends BaseModel
    {

        protected $table = 'slider_items';
        protected $guarded = [];
        public $translatable = [
            'title',
            'sub_title',
            'body',
        ];
        public $timestamps = FALSE;

        public function _slider()
        {
            return $this->belongsTo(Slider::class, 'slider_id');
        }

    }

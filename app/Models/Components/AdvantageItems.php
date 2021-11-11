<?php

    namespace App\Models\Components;

    use App\Library\BaseModel;

    class AdvantageItems extends BaseModel
    {

        protected $table = 'advantage_items';
        protected $guarded = [];
        public $translatable = [
            'title',
            'sub_title',
            'body'
        ];

    }

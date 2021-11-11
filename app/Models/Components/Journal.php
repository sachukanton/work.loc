<?php

    namespace App\Models\Components;

    use App\Library\BaseModel;

    class Journal extends BaseModel
    {

        protected $table = 'journal';
        protected $fillable = [
            'type',
            'message'
        ];

        public function __construct($attributes = [])
        {
            parent::__construct();
            $this->fill($attributes);
        }

    }
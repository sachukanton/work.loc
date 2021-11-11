<?php

    namespace App\Models\Form;

    use App\Library\BaseModel;

    class FormsData extends BaseModel
    {

        protected $table = 'forms_data';
        protected $guarded = [];

        public function getDataAttribute()
        {
            return is_json($this->attributes['data']) ? json_decode($this->attributes['data']) : NULL;
        }

        public function getViewDataAttribute()
        {
            $_response = NULL;
            switch ($this->form) {

            }

            return $_response;
        }

        public function _form()
        {
            return $this->belongsTo(Forms::class, 'form_id');
        }

    }

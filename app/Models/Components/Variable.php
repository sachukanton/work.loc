<?php

    namespace App\Models\Components;

    use App\Library\BaseModel;

    class Variable extends BaseModel
    {

        protected $table = 'variables';
        protected $guarded = [];
        public $timestamps = FALSE;
        public $translatable = [
            'value',
        ];

        public function __construct()
        {
            parent::__construct();
        }

        public function _load($key)
        {
            global $wrap;
            $_item = self::where('key', $key)
                ->remember(REMEMBER_LIFETIME)
                ->first();
            if ($_item && ($_item->visible_entity || (isset($wrap['user_role']) && $wrap['user_role'] == 'super_admin'))) {
                return $_item->value ? ($_item->use_php ? eval($_item->value) : $_item->value) : NULL;
            }

            return NULL;
        }

        public function _short_code($options = [])
        {
            return $this->value ? ($this->use_php ? eval($this->value) : $this->value) : NULL;
        }

    }
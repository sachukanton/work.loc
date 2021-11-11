<?php

    namespace App\Models\Components;

    use Illuminate\Database\Eloquent\Model;
    use Watson\Rememberable\Rememberable;

    class DisplayRules extends Model
    {

        use Rememberable;

        protected $table = 'display_rules';
        protected $guarded = [];
        protected $entity;
        protected $rules;
        public $timestamps = FALSE;

        public function __construct($entity = NULL)
        {
            $this->entity = $entity;
            $this->rules = request()->get('display_rules');
        }

        public function updating_rules()
        {
            if ($this->entity) $this->entity->_display_rules()->delete();
            if ($this->entity && $this->rules) {
                foreach ($this->rules as $_rule => $_values) {
                    if (is_array($_values) && count($_values) >= 1 && !isset($_values['all'])) {
                        foreach ($_values as $_value => $_state) {
                            if ($_state) {
                                $_save = new self();
                                $_save->rule = $_rule;
                                $_save->value = $_value;
                                $this->entity->_display_rules()->save($_save);
                            }
                        }
                    } elseif (is_string($_values)) {
                        $_values = preg_split('/\\r\\n?|\\n/', $_values);
                        if (is_array($_values) && count($_values)) {
                            foreach ($_values as $_value) {
                                $_value = trim($_value);
                                if ($_value) {
                                    $_save = new self();
                                    $_save->rule = $_rule;
                                    $_save->value = $_value;
                                    $this->entity->_display_rules()->save($_save);
                                }
                            }
                        }
                    }
                }
            }
        }

        public function model()
        {
            return $this->morphTo();
        }

    }
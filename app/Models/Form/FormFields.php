<?php

    namespace App\Models\Form;

    use App\Library\BaseModel;

    class FormFields extends BaseModel
    {

        protected $table = 'form_fields';
        protected $guarded = [];
        public $timestamps = FALSE;
        public $translatable = [
            'title',
            'help',
            'options',
            'markup',
        ];

        public function getDataAttribute()
        {
            return isset($this->attributes['data']) && $this->attributes['data'] ? unserialize($this->attributes['data']) : NULL;
        }

        public function get_field_data()
        {
            $_fields = [
                'text'     => [
                    'name' => 'Текстовое поле',
                ],
                'number'   => [
                    'name' => 'Числовое поле',
                ],
                'textarea' => [
                    'name' => 'Текстовая область',
                ],
                'hidden'   => [
                    'name' => 'Скрытое поле',
                ],
                'select'   => [
                    'name' => 'Элементы списка'
                ],
                'checkbox' => [
                    'name' => 'Флажки'
                ],
                'radio'    => [
                    'name' => 'Переключатели'
                ],
                'file'     => [
                    'name' => 'Выбор файла',
                ],
                'markup'   => [
                    'name' => 'Разметка'
                ],
                //                'break'    => [
                //                    'name' => 'Шаг формы'
                //                ],
            ];

            return $_fields[$this->type] ?? ['name' => NULL];
        }

    }

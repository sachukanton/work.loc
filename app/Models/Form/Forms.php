<?php

    namespace App\Models\Form;

    use App\Library\BaseModel;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\View;

    class Forms extends BaseModel
    {

        protected $table = 'forms';
        protected $guarded = [];
        public $timestamps = FALSE;
        public $translatable = [
            'title',
            'sub_title',
            'body',
            'button_send',
            'button_open_form',
            'completion_modal_text'
        ];
        public $renderIndex = NULL;

        public function getSettingsAttribute()
        {
            return isset($this->attributes['settings']) && $this->attributes['settings'] ? json_decode($this->attributes['settings']) : NULL;
        }

        public function _items()
        {
            return $this->hasMany(FormFields::class, 'form_id')
                ->active()
                ->orderBy('sort')
                ->orderBy('title')
                ->remember(REMEMBER_LIFETIME);
        }

        public function _render($options = [])
        {
            if ($this->invisible) return NULL;
            $_options = array_merge([
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            if (isset($_options['index']) && $_options['index']) $this->renderIndex = $_options['index'];
            $_form_data = $this->formatted_data();
            $_template = [
//                "frontend.{$this->deviceTemplate}.forms.form_{$this->id}",
//                "frontend.{$this->deviceTemplate}.forms.form",
                "frontend.default.forms.form_{$this->id}",
                "frontend.default.forms.form",
                'backend.base.form'
            ];
            if (isset($_options['view']) && $_options['view']) array_unshift($_template, $_options['view']);
            $_item = $this;

            return View::first($_template, compact('_item', '_form_data'))
                ->render(function ($view, $_content) {
                    return clear_html($_content);
                });
        }

        public function formatted_data($index = NULL)
        {
            global $wrap;
            $_device_template = $wrap['device']['template'] ?? 'default';
            $_render_index = !is_null($index) ? "-{$index}" : ($this->renderIndex ? "-{$this->renderIndex}" : NULL);
            $_response = [
                'form_id'        => ($this->style_id ? $this->style_id : "form-entity-{$this->id}") . ($_render_index ? : NULL),
                'use_steps'      => FALSE,
                'first_step'     => NULL,
                'last_step'      => NULL,
                'steps'          => [],
                'options_fields' => [],
                'render_fields'  => [],
                'validation'     => []
            ];
            $_step = NULL;
            $_session_save_form = Session::get("step_form_{$this->id}");
            $this->_items->each(function ($_field) use (&$_response, &$_step, $_device_template, $_session_save_form) {
                if ($_field->type != 'break') {
                    $_field_name = "fields.field_{$_field->id}" . ($_field->multiple ? '.*' : NULL);
                    $_field_id = "{$_response['form_id']}-field-{$_field->id}";
                    $_field_data = $_field->data;
                    $_field_selected = $_field->value;
                    if (isset($_session_save_form['fields'][$_field->id])) {
                        if (is_array($_session_save_form['fields'][$_field->id])) {
                            $_field_selected = implode('|', $_session_save_form['fields'][$_field->id]);
                        } else {
                            $_field_selected = $_session_save_form['fields'][$_field->id];
                        }
                    }
                    $_option = [
                        'type'        => $_field->type,
                        'id'          => $_field_id,
                        'field_label' => $_field->title,
                        'field_name'  => "field_{$_field->id}",
                        'theme'       => [
                            "frontend.{$_device_template}.constructor_field.field_{$_field->type}",
                            "frontend.default.constructor_field.field_{$_field->type}",
                            "backend.constructor_field.field_{$_field->type}",
                            "backend.constructor_field.field_text"
                        ],
                        'selected'    => $_field_selected,
                        'sort'        => $_field->sort,
                    ];
                    if ($_field->value) $_option['selected'] = $_field->value;
                    if (isset($_field_data['prefix']) && $_field_data['prefix']) $_option['prefix'] = $_field_data['prefix'];
                    if (isset($_field_data['suffix']) && $_field_data['suffix']) $_option['suffix'] = $_field_data['suffix'];
                    if (isset($_field_data['item_class']) && $_field_data['item_class']) $_option['item_class'] = $_field_data['item_class'];
                    if (isset($_field_data['class']) && $_field_data['class']) $_option['class'] = $_field_data['class'];
                    if (isset($_field_data['attributes']) && $_field_data['attributes']) $_option['attributes'] = [$_field_data['attributes'] => TRUE];
                    $_validation_rules = NULL;
                    if ($_field->other_rules) {
                        if (str_is('*required*', $_field->other_rules)) $_option['required'] = TRUE;
                        $_validation_rules = [
                            'id'       => $_field_id,
                            'name'     => $_field_name,
                            'title'    => $_field->title,
                            'rule'     => $_field->other_rules,
                            'multiple' => $_field->multiple ? TRUE : FALSE,
                        ];
                    } elseif ($_field->required) {
                        $_option['required'] = TRUE;
                        $_validation_rules = [
                            'id'       => $_field_id,
                            'name'     => $_field_name,
                            'title'    => $_field->title,
                            'rule'     => 'required',
                            'multiple' => $_field->multiple ? TRUE : FALSE,
                        ];
                    }
                    if (!$_field->hidden_label && !$_field->placeholder_label) {
                        $_option['label'] = $_field->title;
                    } elseif ($_field->placeholder_label) {
                        $_option['attributes']['placeholder'] = $_field->title;
                    }
                    if ($_field->help) $_option['help'] = $_field->help;
                    if ($_field->multiple) $_option['multiple'] = TRUE;
                    if ($_field->type == 'markup' && $_field->markup) $_option['html'] = $_field->markup;
                    if ($_field->type == 'file') {
                        $_option['attributes']['placeholder'] = 'Select file';
                        $_option['ajax_url'] = FALSE;
                    }
                    if ($_field->type == 'select' || $_field->type == 'checkbox' || $_field->type == 'radio') {
                        $_field_options = $_field->options ? explode(PHP_EOL, $_field->options) : NULL;
                        $_field_values = NULL;
                        if ($_field_options) {
                            foreach ($_field_options as $field_option) {
                                $_field_option = explode('|', $field_option);
                                if (isset($_field_option[1]) && $_field_option[1]) {
                                    $_field_values[$_field_option[0]] = $_field_option[1];
                                } elseif (isset($_field_option[0]) && $_field_option[0]) {
                                    $_field_values[$_field_option[0]] = $_field_option[0];
                                }
                            }
                            if ($_field_values) {
                                $_option['values'] = $_field_values;
                            } else {
                                return FALSE;
                            }
                        } elseif($_field->type == 'select') {
                            return FALSE;
                        }
                        if ($_option['selected']) $_option['selected'] = explode('|', $_option['selected']);
                    }
                    $_render = field_render($_field_name, $_option);
                    $_response['options_fields'][$_field->id] = $_option;
                    $_response['render_fields'][$_field->id] = $_render;
                    if ($_step) {
                        $_response['steps'][$_step->id]['fields'][$_field->id] = $_render;
                        if ($_validation_rules) $_response['validation'][$_step->id][$_field->id] = $_validation_rules;
                    } else {
                        if ($_validation_rules) $_response['validation'][$_field->id] = $_validation_rules;
                    }
                } else {
                    $_step = $_field;
                    $_response['use_steps'] = TRUE;
                    if (is_null($_response['first_step'])) $_response['first_step'] = $_field->id;
                    $_response['last_step'] = $_field->id;
                    $_response['steps'][$_field->id] = [
                        'id'     => $_field->id,
                        'title'  => !$_field->hidden_label ? $_field->title : NULL,
                        'prev'   => NULL,
                        'next'   => NULL,
                        'fields' => [
                            field_render("fields.form_step", [
                                'type'  => 'hidden',
                                'value' => $_field->id,
                            ])
                        ]
                    ];
                }
            });
            if ($_response['options_fields']) $_response['options_fields'] = collect($_response['options_fields'])->sortBy('sort');
            if ($_response['use_steps']) {
                $_prev_step = NULL;
                foreach ($_response['steps'] as $_id => &$_step) {
                    $_step['prev'] = $_prev_step;
                    if ($_prev_step) $_response['steps'][$_prev_step]['next'] = $_id;
                    $_prev_step = $_id;
                }
            }

            return (object)$_response;
        }

        public function getShortcut($options = [])
        {
            if (!is_bool($this->view_access)) return NULL;
            $_options = array_merge([
                'type'  => 'form',
                'view'  => NULL,
                'index' => NULL,
            ], $options);
            if ($_options['type'] == 'form') {
                $_template = [];
                if ($this->style_id && $_options['index']) $this->style_id .= "-{$_options['index']}";
                $_form_data = $this->formatted_data();
                if (isset($_options['view']) && $_options['view']) {
                    $_template = [
                        "frontend.{$this->deviceTemplate}.{$_options['view']}",
                        "frontend.default.{$_options['view']}",
                    ];
                }
                $_template = array_merge($_template, [
//                    "frontend.{$this->deviceTemplate}.forms.shortcut_form_{$this->id}",
//                    "frontend.{$this->deviceTemplate}.forms.shortcut_form",
                    "frontend.default.forms.shortcut_form_{$this->id}",
                    'frontend.default.forms.shortcut_form',
                    'backend.base.form'
                ]);
                $_item = $this;
                $_view = NULL;
                if ($_item) {
                    return View::first($_template, compact('_item', '_form_data'))
                        ->render(function ($view, $_content) {
                            return clear_html($_content);
                        });
                }

                return $_view;
            } elseif ($_options['type'] == 'form_button') {
                $_form_button_name = $this->button_open_form ? : 'Open the Form';
                $_form_button_class = isset($this->settings->open_form->class) && $this->settings->open_form->class ? " {$this->settings->open_form->class}" : NULL;
                $_form_button_path = _r('ajax.open_form', ['form' => $this]);
                $_form_button = "<button type=\"button\" data-path=\"{$_form_button_path}\" data-index=\"{$_options['index']}\" data-view=\"{$_options['view']}\" class=\"uk-button-default uk-button use-ajax{$_form_button_class}\">{$_form_button_name}</button>";

                return $_form_button;
            }

            return NULL;
        }

    }

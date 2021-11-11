<?php

    namespace App\Library;

    use Illuminate\Support\Facades\View;

    class Fields
    {

        protected $params;
        protected $errors;
        protected $variables;
        protected $fieldName;

        public function __construct($name, $variables = [])
        {
            $this->variables = $variables = collect($variables);
            $this->errors = session('errors');
            $_form_id = $variables->get('form_id');
            $_field_id = $variables->has('id') ? str_slug($variables->get('id')) : generate_field_id($name, $_form_id);
            $_field_type = $variables->get('type', 'text');
            if ($_themes = $variables->get('theme')) $_field_theme = is_array($_themes) ? $_themes : [$_themes];
            $_use_placeholder = $variables->get('placeholder', FALSE);
            $_field_theme[] = "backend.fields.{$_field_type}";
            $_field_theme[] = "backend.fields.text";
            $_field_label = NULL;
            $_field_required = $variables->get('required', FALSE);
            $_field_attributes = $variables->get('attributes');
            $_variable_label = $variables->get('label');
            if ($_variable_label && !$_use_placeholder) $_field_label = __($_variable_label);
            if (isset($_field_attributes['placeholder'])) {
                $_variable_label = $_field_attributes['placeholder'];
                $_use_placeholder = TRUE;
                $_field_attributes['placeholder'] = __($_field_attributes['placeholder']) . ($_field_required ? ' *' : NULL);
            }
            if ($_use_placeholder && !isset($_field_attributes['placeholder']) && $_variable_label) $_field_attributes['placeholder'] = __($_variable_label) . ($_field_required ? ' *' : NULL);
            $_field_options = [
                'form_id'    => $_form_id,
                'type'       => $_field_type,
                'id'         => $_field_id,
                'class'      => $variables->get('class'),
                'item_class'      => $variables->get('item_class'),
                'label'      => !$_use_placeholder ? $_field_label : NULL,
                'base_label' => $_variable_label,
                'icon'       => $variables->get('icon'),
                'name'       => $this->render_field_name($name),
                'old'        => $name,
                'value'      => $variables->get('value'),
                'values'     => $variables->get('values', []),
                'selected'   => old($name, ($variables->has('selected') ? $variables->get('selected') : ($variables->has('value') ? $variables->get('value') : NULL))),
                'attributes' => render_attributes($_field_attributes),
                'help'       => $variables->get('help'),
                'error'      => $this->errors && $this->errors->has($name) ? $this->errors->first($name) : NULL,
                'required'   => $_field_required,
                'prefix'     => $variables->get('prefix'),
                'suffix'     => $variables->get('suffix'),
                'options'    => $variables->get('options', []),
                'multiple'   => $variables->get('multiple') ? TRUE : FALSE,
                'ajax_url'   => $variables->get('ajax_url', _r('ajax.file.upload')),
                'editor'     => $variables->has('editor') ? TRUE : FALSE,
                'html'       => $variables->get('html'),
                'theme'      => $_field_theme,
            ];
            switch ($_field_type) {
                case 'checkbox':
                case 'radio':
                case 'select':
                    if ($_field_options['values']) {
                        foreach ($_field_options['values'] as &$_item) {
                            if (is_array($_item)) {
                                $_item[0] = __($_item[0]);
                                if (isset($_item[1])) $_item[1] = __($_item[1]);
                            } else {
                                $_item = __($_item);
                            }
                        }
                    }
                    break;
                case 'password_confirmation':
                    $_field_options['label_confirmation'] = !$_use_placeholder && $_variable_label ? __("{$_variable_label}_confirmation") . ($_field_required ? ' *' : NULL) : NULL;
                    $_field_options['name_confirmation'] = $this->render_field_name("{$name}_confirmation");
                    if ($_use_placeholder && $_variable_label) $_field_attributes['placeholder'] = __("{$_variable_label}_confirmation") . ($_field_required ? ' *' : NULL);
                    $_field_options['attributes_confirmation'] = render_attributes($_field_attributes);
                    break;
                case 'file':
                    $_field_options['upload_allow'] = ($_allow = $variables->get('allow')) ? '*.(' . $_allow . ')' : '*.(jpg|jpeg|gif|png)';
                    $_field_options['upload_view'] = $variables->get('view');
                    break;
            }
            $this->params = collect($_field_options);
        }

        public function _render()
        {
            $_params = $this->params;
            $_field = NULL;
            if ($_params->has('name') && $_params->get('name')) {
                switch ($_params->get('type')) {
                    case 'textarea':
                        if ($_params->get('editor')) {
                            $class = $_params->get('class');
                            $_params->put('class', ($class ? "{$class} uk-ckEditor" : 'uk-ckEditor'));
                        }
                        $_field = View::first($_params->get('theme', []))
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'radio':
                        $selected = $_params->get('selected');
                        if (!old() && !$selected && is_array($_params->get('values'))) {
                            $selected = array_key_first($_params->get('values'));
                            $_params->put('selected', $selected);
                        }
                        $_field = View::first($_params->get('theme', []))
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'select':
                        if ($_params->get('multiple', FALSE) && !str_is('*\[\]', $_params->get('name'))) $_params->put('name', $_params->get('name') . '[]');
                        $_field = View::first($_params->get('theme', []))
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'autocomplete':
                        $_field_name = $_params->get('old');
                        $_params->put('name', $this->render_field_name("{$_field_name}.name"));
                        $_params->put('autocomplete_name', $this->render_field_name("{$_field_name}.value"));
                        $_selected = $_params->get('selected');
                        if (is_array($_selected)) {
                            $_params->put('selected', $_selected['name']);
                            $_params->put('value', $_selected['value']);
                        }
                        $_errors = session('errors');
                        if ($_errors && $_errors->has("{$_field_name}.value")) {
                            $_params->put('error', $this->errors->first("{$_field_name}.value"));
                        }
                        $_field = View::first($_params->get('theme', []))
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'table':
                        $_options = [
                            'cols' => 2,
                        ];
                        $_params->put('options', array_merge($_options, $_params->get('options', [])));
                        $_field = View::first($_params->get('theme', []))
                            ->with('params', $_params)
                            ->render();
                        break;

                    case 'checkbox':
                    case 'file':
                    case 'file_default':
                    case 'avatar':
                    case 'hidden':
                    case 'box':
                    case 'password_confirmation':
                    case 'markup':
                    default:
                        $_field = View::first($_params->get('theme', []))
                            ->with('params', $_params)
                            ->render();
                        break;
                }
                if ($_field) $_field = $_params->get('prefix') . $_field . $_params->get('suffix');
            }

            return $_field;
        }

        protected function render_field_name($name)
        {
            $_name = $name;
            if (str_contains($name, '.')) {
                $name = explode('.', $name);
                $_name = NULL;
                foreach ($name as $_item) {
                    $_item = str_replace('*', '', $_item);
                    $_name .= is_null($_name) ? (string)$_item : (string)"[{$_item}]";
                }
            }

            return $_name;
        }

    }


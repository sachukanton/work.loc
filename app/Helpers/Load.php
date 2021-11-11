<?php

    use App\Models\Structure\Page;
    use App\Models\Structure\Tag;
    use Illuminate\Support\Facades\Session;

    if (!function_exists('config_data_load')) {
        function config_data_load($config, $variable, $locale = NULL, $fallback_locale = NULL)
        {
            $_response = NULL;
            if (is_string($config)) $config = config($config);
            if ($config) {
                if ($locale) {
                    $_variable = str_contains($variable, '*') ? str_replace('*', $locale, $variable) : "{$variable}.{$locale}";
                    $_response = array_get($config, $_variable);
                    if (!$_response) {
                        $_fallback_locale = $fallback_locale ? : config('app.fallback_locale');
                        $_variable = str_contains($variable, '*') ? str_replace('*', $_fallback_locale, $variable) : "{$variable}.{$_fallback_locale}";
                        $_response = array_get($config, $_variable);
                    }
                } else {
                    $_response = array_get($config, $variable);
                }
            }

            return $_response;
        }
    }

    if (!function_exists('contacts_load')) {
        function contacts_load($locale = DEFAULT_LOCALE)
        {
            $_response = config('os_contacts');
            $_response['address'] = $_response['address'][$locale];
            $_response['working_hours'] = $_response['working_hours'][$locale];
            $_phones = [];
            foreach ($_response['phones'] as $_phone) if ($_phone) $_phones[] = format_phone_number($_phone);
            $_response['phones'] = $_phones;
            if ($_response['schema']) {
                $_response['schema'] = preg_replace('/[\r\n\t]+/', ' ', $_response['schema']);
                $_response['schema'] = preg_replace('/[\s]+/', ' ', $_response['schema']);
                $_response['schema'] = json_encode(json_decode($_response['schema'], TRUE));
            }

            return $_response;
        }
    }

    if (!function_exists('currency_load')) {
        function currency_load($locale = NULL)
        {
            $_locale = $locale ? : app()->getLocale();
            $currency = config('os_currencies');
            $_choice_currency = Session::get('currency', $currency['default_currency']);
            $_response = [
                'all'     => NULL,
                'current' => NULL
            ];
            foreach ($currency['currencies'] as $_currency_key => $_currency_data) {
                if ($_currency_data['use']) {
                    if ($_currency_key == $_choice_currency) {
                        $_response['current'] = [
                            'key'            => $_currency_key,
                            'full_name'      => $_currency_data['full_name'],
                            'iso_code'       => $_currency_data['iso_code'],
                            'precision_mode' => (int)$_currency_data['precision_mode'],
                            'precision'      => (int)$_currency_data['precision'],
                            'prefix'         => $_currency_data['markup'][$_locale]['prefix'],
                            'suffix'         => $_currency_data['markup'][$_locale]['suffix'],
                            'ratio'          => $_currency_data['ratio'],
                        ];
                    }
                    $_currency_data['key'] = $_currency_key;
                    $_response['all'][$_currency_key] = [
                        'key'            => $_currency_key,
                        'full_name'      => $_currency_data['full_name'],
                        'iso_code'       => $_currency_data['iso_code'],
                        'precision_mode' => (int)$_currency_data['precision_mode'],
                        'precision'      => (int)$_currency_data['precision'],
                        'prefix'         => $_currency_data['markup'][$_locale]['prefix'],
                        'suffix'         => $_currency_data['markup'][$_locale]['suffix'],
                        'ratio'          => $_currency_data['ratio'],
                    ];
                }
            }

            return $_response;
        }
    }

    if (!function_exists('page_load')) {
        function page_load($entity, $view = NULL)
        {
            $_item = NULL;
            if ($entity instanceof Page) {
                $_item = $entity;
            } elseif (is_numeric($entity)) {
                $_item = Page::where('id', $entity)
                    ->with([
                        '_alias'
                    ])
                    ->remember(REMEMBER_LIFETIME)
                    ->first();
            } else {
                $_item = Page::where('type', $entity)
                    ->with([
                        '_alias'
                    ])
                    ->remember(REMEMBER_LIFETIME)
                    ->first();
            }
            if ($_item) $_item->_load($view);

            return $_item;
        }
    }

    if (!function_exists('tag_load')) {
        function tag_load($entity, $view = NULL)
        {
            $_item = NULL;
            if ($entity instanceof Tag) {
                $_item = $entity;
            } elseif (is_numeric($entity)) {
                $_item = Tag::where('id', $entity)
                    ->with([
                        '_alias'
                    ])
                    ->remember(REMEMBER_LIFETIME)
                    ->first();
            } else {
                $_item = Tag::where('type', $entity)
                    ->with([
                        '_alias'
                    ])
                    ->remember(REMEMBER_LIFETIME)
                    ->first();
            }
            if ($_item) $_item->_load($view);

            return $_item;
        }
    }
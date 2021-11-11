<?php

use App\Models\Seo\UrlAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

if (!function_exists('_u')) {
    function _u($path = NULL, $parameters = [])
    {
        $_base_url = config('app.url');
        if (is_null($path)) return NULL;
        if (str_is($_base_url . '*', $path)) $path = str_replace($_base_url, '', $path);
        $_parse_path = parse_url($path);
        if (isset($_parse_path['host'])) return $path;
        $_url = url(trim($path, '/'), $parameters, config('os_seo.path.secure'));
        $_url = str_replace('/index.php', '/', $_url);
        $_url = preg_replace('/([^:])(\/{2,})/', '$1/', str_replace('/index.php', '/', $_url));
        $_prepend = NULL;

        return formalize_url($_url, FALSE);
    }
}

if (!function_exists('_r')) {
    function _r($path, $parameters = [])
    {
        $_route = route($path, $parameters, config('os_seo.path.absolute'));

        return formalize_url($_route, FALSE);
    }
}

if (!function_exists('_l')) {
    function _l($name, $path, $parameters = [])
    {
        $_link_attributes = [];
        $_link_parameters = $parameters['p'] ?? [];
        $_link_prefix = $parameters['prefix'] ?? NULL;
        $_link_suffix = $parameters['suffix'] ?? NULL;
        $_full_path = $parameters['full_path'] ?? FALSE;
        $_wrapper = $parameters['wrapper'] ?? FALSE;
        if (($_link_anchor = $parameters['anchor'] ?? NULL)) $_link_anchor = '#' . trim($_link_anchor, '#');
        if (($_link_description = $parameters['description'] ?? NULL)) $_link_description = "<div class='uk-description-link'>{$_link_description}</div>";
        $_link_active = FALSE;
        if ($path) {
            $parse_path = parse_url($path);
            if (!isset($parse_path['host'])) $path = $parse_path['path'];
        }
        if (!$path) {
            $_link_path = NULL;
            $_link_attributes['class'] = 'not-link';
        } elseif (Route::has($path)) {
            $_link_path = _r(trim($path, '/'), $_link_parameters);
        } else {
            $_link_path = _u($path, $_link_parameters);
        }
        if (isset($parameters['attributes']) && is_array($parameters['attributes'])) {
            foreach ($parameters['attributes'] as $key => $attribute) {
                switch ($key) {
                    case 'class':
                        if ($attribute && isset($_link_attributes['class'])) {
                            $_link_attributes['class'] .= is_array($attribute) ? ' ' . implode(' ', $attribute) : " {$attribute}";
                        } elseif ($attribute) {
                            $_link_attributes['class'] = is_array($attribute) ? implode(' ', $attribute) : $attribute;
                        }
                        break;
                    case 'data':
                        if ($attribute && is_array($attribute)) foreach ($attribute as $data_name => $data_value) if ($data_value) $_link_attributes["data-{$data_name}"] = $data_value;
                        break;
                    default:
                        if ($attribute) $_link_attributes[$key] = $attribute;
                        break;
                }
            }
        }
        if (!is_null($_link_path)) {
            $_link_path_active = config('os_seo.path.absolute') ? str_replace(config('app.url'), '', $_link_path) : $_link_path;
            if (request()->path() == '/' && request()->is($_link_path_active)) {
                $_link_attributes['class'] = isset($_link_attributes['class']) ? "{$_link_attributes['class']} active" : 'active';
                $_link_active = TRUE;
            } elseif (request()->path() != '/' && request()->is(trim($_link_path_active, '/'))) {
                $_link_attributes['class'] = isset($_link_attributes['class']) ? "{$_link_attributes['class']} active" : 'active';
                $_link_active = TRUE;
            }
        }
        $_link_wrapper = $_wrapper ? "<span class='uk-name-link'>{$name}</span>" : $name;
        if ($_link_active || is_null($_link_path)) {
            if (!$_link_anchor) $_link_attributes['class'] = isset($_link_attributes['class']) ? "{$_link_attributes['class']} uk-current-link" : $_link_attributes['class'];
            $_attributes = render_attributes($_link_attributes);
            if ($_link_anchor) {
                $output = "<a href=\"{$_link_anchor}\" {$_attributes}>{$_link_prefix}{$_link_wrapper}{$_link_suffix}{$_link_description}</a>";
            } else {
                $output = "<span {$_attributes}>{$_link_prefix}{$_link_wrapper}{$_link_suffix}{$_link_description}</span>";
            }
        } else {
            if ($_full_path) $_link_path = trim(config('app.url'), '/') . $_link_path;
            $_attributes = render_attributes($_link_attributes);
            $output = "<a href=\"{$_link_path}{$_link_anchor}\" {$_attributes}>{$_link_prefix}{$_link_wrapper}{$_link_suffix}{$_link_description}</a>";
        }

        return $output;
    }
}

if (!function_exists('_ar')) {
    function _ar($path, $params = NULL, $class = 'uk-active')
    {
        $_current_url = trim(str_replace(trim(config('app.url'), '/'), '', URL::current()), '/');
        $_active = FALSE;
        if (is_string($path)) {
            if (Route::has($path)) {
                $_route = trim(str_replace(trim(config('app.url'), '/'), '', route($path, $params)), '/');
                if ($_route == $_current_url) $_active = TRUE;
            } else {
                $_url = trim(str_replace(trim(config('app.url'), '/'), '', url($path)), '/');
                if ($_url == $_current_url) $_active = TRUE;
            }

            return $_active ? $class : NULL;
        } elseif (is_array($path)) {
            foreach ($path as $_path) if (stristr(Route::currentRouteName(), $_path)) return ' ' . $class;
        }

        return NULL;
    }
}

if (!function_exists('get_url_entity')) {
    function get_entity_url($entity_model, $id)
    {
        $_response = NULL;
        if (is_array($id) && is_assoc_array($id)) {
            $_response = [];
            $_aliases = UrlAlias::where('model_type', $entity_model)
                ->remember(15)
                ->whereIn('model_id', array_keys($id))
                ->get();
            if ($_aliases->isNotEmpty()) {
                $_aliases->map(function ($_alias) use ($id, &$_response) {
                    $_alias->_alias = $_alias;
                    if (isset($id[$_alias->model_id])) $_response[$id[$_alias->model_id]] = $_alias->generate_url;
                });
            }
        } else {
            $_alias = UrlAlias::where('model_type', $entity_model)
                ->remember(15)
                ->where('model_id', $id)
                ->first();
            if ($_alias) $_alias->_alias = $_alias;
            $_response = $_alias->generate_url ?? NULL;
        }

        return $_response;
    }
}

if (!function_exists('formalize_url')) {
    function formalize_url($url, $is_file = FALSE)
    {
        $_config_path = config('os_seo.path');
        $_url = NULL;
        $_prepend = NULL;
        $_url_parse = parse_url($url);
        if ($_config_path['absolute'] === TRUE) $_url = ($_config_path['secure'] === TRUE ? 'https://' : 'http://') . str_replace('www', '', $_url_parse['host']);
        $_url_params = isset($_url_parse['query']) && $_url_parse['query'] ? "?{$_url_parse['query']}" : '';
        $_url .= (isset($_url_parse['path']) && $_url_parse['path'] ? $_url_parse['path'] : '/') . ($_url_params ? $_url_params : NULL) . (isset($_url_parse['fragment']) && $_url_parse['fragment'] ? "#{$_url_parse['fragment']}" : NULL);

        return preg_replace('/([\/]){2,}/', '$1', $_url);
    }
}

if (!function_exists('formalize_path')) {
    function formalize_path($path, $do_no_use_timestamp = FALSE)
    {
        if (Storage::disk('base')->exists($path) && !$do_no_use_timestamp) {
            $_file_lastModified = Storage::disk('base')->lastModified($path);
            $_asset = asset("$path?{$_file_lastModified}", config('os_seo.path.secure'));
        } else {
            $_asset = asset($path, config('os_seo.path.secure'));
        }

        return formalize_url($_asset, TRUE);
    }
}

if (!function_exists('formalize_url_query')) {
    function formalize_url_query($query = NULL, $element = NULL, $prefix = '?')
    {
        $query = $query ?? request()->query();
        if (is_string($element) && isset($query[$element])) {
            unset($query[$element]);
        } elseif (is_array($element)) {
            $query[$element['param']] = $element['data'];
        }
        if ($query) return $prefix . http_build_query($query);

        return NULL;
    }
}

if (!function_exists('generate_alias')) {
    function generate_alias($alias, $founder = [])
    {
        if ($founder) {
            $founder[] = str_slug($alias);

            return implode('/', $founder);
        } else {
            $_alias = explode('/', $alias);
            $alias = [];
            foreach ($_alias as $data) $alias[] = str_slug($data);

            return implode('/', $alias);
        }
    }
}

if (!function_exists('format_alias')) {
    function format_alias(Request $request)
    {
        global $wrap;
        $_locale = $wrap['locale'];
        $_alias = $request->path();
        if ($request->ajax()) {
            $_language = $request->header('LOCALE', DEFAULT_LOCALE);
            $_locale = $_language;
            $_device = $request->header('device', 'pc');
            wrap()->set('locale', $_language);
            wrap()->set('device.type', $_device);
            App::setLocale($_locale);
            if($_locale != DEFAULT_LOCALE) $_alias = str_replace("{$_locale}/", '', $_alias);
        } else {
            $_alias = LaravelLocalization::getNonLocalizedURL($_alias);
            $_base_url = wrap()->get('seo.base_url');
            if (str_is($_base_url . '*', $_alias)) $_alias = trim(str_replace($_base_url, '', $_alias), '/');
        }
        if ($_page_number = current_page($_alias)) {
            $_current_url_query = $wrap['seo']['url_query'];
            $_alias = trim(preg_replace('/page-[0-9]+/i', '', $_alias), '/');
            if ($_prev_page = ($_page_number - 1)) {
                if ($_page_number > 2) {
                    $_url = trim($_alias, '/') . "/page-{$_prev_page}";
                    $_prev_page_link = _u($_url) . $_current_url_query;
                } elseif ($_page_number = 2) {
                    $_url = trim($_alias, '/');
                    $_prev_page_link = _u($_url) . $_current_url_query;
                }
                wrap()->set('seo.link_prev', ($_locale != DEFAULT_LOCALE ? "/{$_locale}" : NULL) . $_prev_page_link);
            }
        }
        wrap()->set('seo.page_number', $_page_number);
        wrap()->set('seo.url_alias', $_alias);
        if ($_page_number) wrap()->set('seo.page_number_suffix', trans('frontend.title_suffix_page', ['page' => $_page_number]));

        return $_alias;
    }
}

if (!function_exists('current_page')) {
    function current_page($alias = NULL)
    {
        $_current_page = NULL;
        if (is_null($alias)) $alias = request()->path();
        $pattern = '/page-[0-9]+/';
        preg_match($pattern, $alias, $_page);
        if (count($_page)) {
            $_page = array_shift($_page);
            $_current_page = (int)str_replace('page-', '', $_page);
        }

        return $_current_page;
    }
}

if (!function_exists('active_path')) {
    function active_path($path = NULL)
    {
        $_response = 0;
        if ($path) {
            $_parse_path = parse_url($path);
            if (!isset($_parse_path['host']) && !isset($_parse_path['fragment'])) {
                if (request()->is(trim($_parse_path['path'], '/'))) $_response = 1;
                if (request()->is(trim($_parse_path['path'], '/') . '/*')) $_response = 2;
            }
        }

        return $_response;
    }
}

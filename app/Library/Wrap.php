<?php

namespace App\Library;

use App;
use App\Models\Shop\Basket;
use App\Models\Structure\Page;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Wrap extends Container
{

    public $requestMethod;
    public $authUser;

    public function __construct(Request $request, Application $app)
    {
        $this->requestMethod = $request->method();
    }

    public function _load(Request $request)
    {
        $_config_app = config('app');
        $_config_seo = config('os_seo');
        $_config_service = config('os_services');
        $_auth_user = Auth::user();
        $_CSRF = csrf_token();
        if ($request->ajax()) {
            $_device = $request->header('device', 'pc');
            $_locale = $request->header('locale', DEFAULT_LOCALE);
        } else {
            $_device = device()->isTablet() ? 'tablet' : (device()->isMobile() ? 'mobile' : 'pc');
            $_locale = App::getLocale();
        }
        $_page_class = [
            "used-device-type-{$_device}",
            "uk-height-min-vh"
        ];
        if ($_config_seo['page_class']) $_page_class[] = $_config_seo['page_class'];
        $_canonical_path = _u($_config_app['url'] . '/' . preg_replace('/page-[0-9]+/i', '', $request->path()));
        $_canonical_path = preg_replace('/([^:])(\/{2,})/', '$1/', $_canonical_path);
        $_locale_regional = config("laravellocalization.supportedLocales.{$_locale}.regional", 'ru_RU');
        $_instance = [
            'user'                => $_auth_user,
            'user_role'           => $_auth_user ? $_auth_user->getRoleNames()->first() : 'anonymous',
            'user_verified_email' => $_auth_user->api_token ?? NULL,
            'token'               => $_CSRF,
            'locale'              => $_locale,
            'fallback_locale'     => $_config_app['fallback_locale'],
            'device'              => [
                'type'     => $_device,
                'template' => $_device == 'mobile' ? 'mobile' : 'default'
            ],
            'eloquent'            => NULL,
            'seo'                 => [
                'base_url'           => $_config_app['url'],
                'url'                => _u($request->fullUrl()),
                'url_query'          => formalize_url_query(),
                'url_alias'          => NULL,
                'canonical'          => $_canonical_path,
                'title'              => NULL,
                'title_suffix'       => config_data_load($_config_seo, 'settings.*.suffix_title', $_locale),
                'keywords'           => config_data_load($_config_seo, 'settings.*.keywords', $_locale),
                'description'        => config_data_load($_config_seo, 'settings.*.description', $_locale),
                'copyright'          => config_data_load($_config_seo, 'settings.*.copyright', $_locale),
                'robots'             => $_config_seo['robots'],
                'last_modified'      => NULL,
                'color'              => $_config_seo['theme_color'],
                'page_number'        => NULL,
                'page_number_suffix' => NULL,
                'link_prev'          => NULL,
                'link_next'          => NULL,
                'open_graph'         => [
                    'locale'      => $_locale_regional,
                    'type'        => 'article',
                    'title'       => NULL,
                    'description' => NULL,
                    'url'         => NULL,
                    'image'       => [
                        $_config_app['url'] . '/og/logo_600_600.jpg',
                        $_config_app['url'] . '/og/logo_1024_512.png',
                        $_config_app['url'] . '/og/logo_600_600.jpg',
                    ],
                    'site_name'   => config_data_load($_config_seo, 'settings.*.site_name', $_locale),
                ],
            ],
            'use'                 => [
                'compress'       => (bool)$_config_seo['use']['compress'],
                'last_modified'  => (bool)$_config_seo['use']['last_modified'],
                'block_scan'     => (bool)$_config_seo['use']['block_scan'],
                'multi_language' => (bool)$_config_seo['use']['multi_language']
            ],
            'page'                => [
                'is_front'       => FALSE,
                'is_dashboard'   => $request->routeIs('oleus*') ? TRUE : FALSE,
                'breadcrumb'     => NULL,
                'title'          => NULL,
                'style_id'       => NULL,
                'style_class'    => $_page_class,
                'attributes'     => NULL,
                'favicon'        => TRUE,
                'logotype'       => $_config_seo['logotype'],
                'site_name'      => config_data_load($_config_seo, 'settings.*.site_name', $_locale),
                'site_slogan'    => config_data_load($_config_seo, 'settings.*.site_slogan', $_locale),
                'site_copyright' => str_replace(':year', date('Y'), config_data_load($_config_seo, 'settings.*.site_copyright', $_locale)),
                'styles'         => [],
                'scripts'        => [],
                'js_settings'    => [
                    'locale'    => $_config_app['locale'],
                    'base_url'  => $_config_app['url'],
                    'csrfToken' => $_CSRF,
                    'apiToken'  => NULL,
                    'ajaxLoad'  => FALSE,
                    'device'    => $_device,
                    'routes'    => NULL
                ],
            ],
            'services'            => [
                'reCaptcha'     => $_config_service['google']['reCaptcha_public'],
                'googleMap'     => $_config_service['google']['googleMap'],
                'googleTag'     => $_config_service['google']['gTag'],
                'facebookPixel' => $_config_service['facebook']['pixel'],
            ],
            'routes'              => Cache::remember('wrap_routes', REMEMBER_LIFETIME * 24 * 7, function () {
                return get_entity_url(Page::class, [
                    13   => 'blog',
                ]);
            }),
            'loads'               => [
                'currency' => currency_load($_locale),
                'contacts' => contacts_load($_locale)
            ],
        ];
        $_instance_dot = array_dot($_instance);
        foreach ($_instance_dot as $_ket => $_value) $this->instance($_ket, $_value);
        $GLOBALS['wrap'] = $_instance;

        return $_instance;
    }

    public function set($key, $value = NULL, $replace = FALSE)
    {
        $_wrap = array_undot($this->instances);
        $_value = array_get($_wrap, $key);
        if ($replace) {
            $_value = $value;
        } else {
            if (is_string($_value) || is_null($_value) || is_bool($_value)) {
                $_value = $value;
            } elseif (is_array($_value)) {
                if (is_array($value) && is_assoc_array($value)) {
                    $_value = array_merge_recursive_distinct($_value, $value);
                } elseif (is_array($value)) {
                    $_value = array_merge($_value, $value);
                } else {
                    $_value[] = $value;
                }
            }
        }
        $this->instance($key, $_value);
        $GLOBALS['wrap'] = $this->get();
    }

    public function get($key = NULL, $default = NULL)
    {
        $_instances = array_undot($this->instances);
        if (is_null($key)) return $_instances;

        return array_get($_instances, $key, $default);
    }

    public function add($instance, $value)
    {
        $this->instance($instance, $value);
    }

    public function forget($instance = NULL)
    {
        if (is_null($instance)) {
            $this->forgetInstances();
        } else {
            $this->forgetInstance($instance);
        }
    }

    public function getLocale()
    {
        global $wrap;

        return $wrap['locale'] ?? DEFAULT_LOCALE;
    }

    public function getDevice()
    {
        global $wrap;

        return $wrap['device']['type'] ?? 'pc';
    }

    public function getDeviceTemplate()
    {
        global $wrap;

        return $wrap['device']['template'] ?? 'default';
    }

    public function render()
    {
        $_instances = array_undot($this->instances);
        if ($this->requestMethod == 'GET') {
            $_logotypes = array_get($_instances, 'page.logotype');
            foreach ($_logotypes as $_key => $_fid) {
                if ($_fid && is_numeric($_fid)) {
                    $_instances['page']['logotype'][$_key] = f_get($_fid);
                }
            }
            $this->renderScriptsAndStyles($_instances);
            if (isset($_instances['page']['js_settings']) && $_instances['page']['js_settings']) {
                $_instances['page']['js_settings'][] = json_encode($_instances['page']['js_settings']);
            }
            $_instances['page']['attributes'] = render_attributes([
                'id'    => $_instances['page']['style_id'] ?? FALSE,
                'class' => render_attributes($_instances['page']['style_class'])
            ]);
            $_og = NULL;
            if(isset($_instances['seo']['open_graph']) && is_array($_instances['seo']['open_graph'])) {
                foreach ($_instances['seo']['open_graph'] as $_key => $_value) {
                    if (is_array($_value)) {
                        foreach ($_value as $__value) {
                            $_og .= "<meta property=\"og:{$_key}\" content=\"{$__value}\" />";
                        }
                    } else {
                        $_og .= "<meta property=\"og:{$_key}\" content=\"{$_value}\" />";
                    }
                }
            }
            $_instances['seo']['open_graph'] = $_og;
        }

        $this->forgetInstances();
        $_instance_dot = array_dot($_instances);
        foreach ($_instance_dot as $_ket => $_value) $this->instance($_ket, $_value);

        return $_instances;
    }

    public function renderScriptsAndStyles(&$instances)
    {
        $_storage_disk = Storage::disk('base');
        $_array_render_files = NULL;
        if (isset($instances['page']['scripts']) && $instances['page']['scripts'] && !isset($instances['page']['scripts']['header'])) {
            $_array_render_files['scripts'] = collect($instances['page']['scripts']);
            if ($_array_render_files['scripts']->isNotEmpty()) {
                $_array_render_files['scripts'] = $_array_render_files['scripts']->transform(function ($_script) {
                    if (!isset($_script['sort'])) $_script['sort'] = 0;

                    return $_script;
                })->sortBy('sort')
                    ->toArray();
            }
        }
        if (isset($instances['page']['styles']) && $instances['page']['styles'] && !isset($instances['page']['scripts']['header'])) {
            $_array_render_files['styles'] = collect($instances['page']['styles']);
            if ($_array_render_files['styles']->isNotEmpty()) {
                $_array_render_files['styles'] = $_array_render_files['styles']->transform(function ($_style) {
                    if (!isset($_style['sort'])) $_style['sort'] = 0;

                    return $_style;
                })->sortBy('sort')
                    ->toArray();
            }
        }
        if ($_array_render_files) {
            foreach ($_array_render_files as $_type_key => $_type_data) {
                if (is_array($_type_data) && count($_type_data)) {
                    $_render = [];
                    foreach ($_type_data as $_row_key => $_row_data) {
                        $_using_device = FALSE;
                        if (isset($_row_data['device'])) {
                            if (is_string($_row_data['device']) && $_row_data['device'] == $instances['device']['type']) {
                                $_using_device = TRUE;
                            } elseif (is_array($_row_data['device']) && in_array($instances['device']['type'], $_row_data['device'])) {
                                $_using_device = TRUE;
                            }
                        } else {
                            $_using_device = TRUE;
                        }
                        if (is_numeric($_row_key) && $_using_device) {
                            if ($_row_data['url']) {
                                $_parse_url = parse_url($_row_data['url']);
                                $_file_lastModified = NULL;
                                $_path = $_row_data['url'];
                                if (!isset($_parse_url['host'])) {
                                    if ($_storage_disk->exists($_row_data['url'])) $_file_lastModified = '?v=' . $_storage_disk->lastModified($_row_data['url']);
                                    $_path = $_file_lastModified ? "/{$_row_data['url']}{$_file_lastModified}" : "/{$_row_data['url']}";
                                }
                                $_attributes = isset($_row_data['attributes']) && $_row_data['attributes'] ? ' ' . render_attributes($_row_data['attributes']) : NULL;
                                $_position = isset($_row_data['position']) && $_row_data['position'] == 'footer' ? 'footer' : 'header';
                                if (!isset($_render[$_position])) $_render[$_position] = NULL;
                                if ($_type_key == 'scripts') {
                                    $_render[$_position] .= "<script src=\"{$_path}\" {$_attributes}></script>";
                                } else {
                                    $_render[$_position] .= "<link href=\"{$_path}\" rel=\"stylesheet\" {$_attributes}>";
                                }
                            }
                        }
                    }
                    $instances['page'][$_type_key] = $_render;
                }
            }
        }
    }

}

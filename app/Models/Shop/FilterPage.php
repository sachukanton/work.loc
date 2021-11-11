<?php

namespace App\Models\Shop;

use App\Library\BaseModel;
use App\Library\Frontend;
use App\Models\Form\Review;
use App\Models\Seo\UrlAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Util\Filter;

class FilterPage extends BaseModel
{
    protected $table = 'shop_filter_pages';
    protected $guarded = [];
    public $translatable = [
        'title',
        'sub_title',
        'breadcrumb_title',
        'menu_title',
        'body',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];

    public $filterRequest = NULL;

    public function __construct()
    {
        parent::__construct();
    }

    public function _category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id')
            ->with([
                '_parent',
                '_children'
            ]);
    }

    public function _pages()
    {
        return $this->morphToMany(self::class, 'model', 'shop_filter_page_page')
            ->with([
                '_alias'
            ])
            ->withPivot([
                'sort',
            ])
            ->orderBy('sort');
    }

    public function getMenuAttribute()
    {
        $_response = NULL;
        $_pages = $this->_pages;
        if ($_pages->isNotEmpty()) {
            $_response = $_pages->transform(function ($_p) {
                return [
                    'title' => $_p->menu_title ? : $_p->title,
                    'alias' => $_p->generate_url
                ];
            });
        }

        return $_response;
    }

    public static function getCategory(Request $request, $alias = NULL)
    {
        $_response = NULL;
        if (!is_null($alias) && str_is('*-cfp-*', $alias)) {
            $_alias_parse = explode('-cfp-', $alias);
            try {
                if ($_alias_parse[0]) {
                    $_alias_model = UrlAlias::where('alias', $_alias_parse[0])
                        ->where('model_type', Category::class)
                        ->first();
                    if ($_alias_model) {
                        if ($_alias_model->model->status) {
                            $_category = $_alias_model->model;
                            $_category->filterPage = self::getPage($request);
                            if ($_category->filterPage instanceof FilterPage) {
                                $_alias = $_category->filterPage->_alias->alias;
                                if ($_alias != $alias) {
                                    return $_alias;
                                }
                            }
                            $_category->filterRequest = self::parse_category_params($_alias_parse[1]);
                            //wrap()->set('seo.canonical', $_category->generate_url);
                            if ($request->isMethod('GET')) {
                                $_response = $_category->_render();
                            } elseif ($request->ajax()) {
                                $_response = $_category->_render_ajax($request);
                            }
                        }
                    }
                }
            } catch (\Exception $exception) {
            }
        }

        return $_response;
    }

    public static function getPage(Request $request)
    {
        $_path = $request->path();
        $_response = self::where('base_path', 'like', $_path)
            ->first();

        return is_null($_response) ? TRUE : $_response;
    }

    public static function parse_category_params($alias_query = NULL)
    {
        $_response = [
            'use'           => FALSE,
            'where'         => [
                'and'        => NULL,
                'or'         => NULL,
                'between'    => NULL,
                'to_collect' => NULL,
            ],
            'brands'        => [],
            'price'         => NULL,
            'sort'          => NULL,
            'params'        => [],
            'replace_title' => FALSE,
        ];
        $_exclude_query_param = [
            'show_more',
            'view_load'
        ];
        if (!is_null($alias_query)) {
            $query_params = explode('?', $alias_query);
            if (isset($query_params[0]) && $query_params[0]) {
                $_query = explode('-&-', $query_params[0]);
                $_response['use'] = TRUE;
                if (is_array($_query) && count($_query)) {
                    $_response['replace_title'] = TRUE;
                    foreach ($_query as $_param_options) {
                        try {
                            if (str_is('brands-*', $_param_options)) {
                                $_param_options = str_replace('brands-', '', $_param_options);
                                $_response['brands'] = explode('-or-', $_param_options);
                            } else {
                                if (str_is('*-or-*', $_param_options)) {
                                    $_options_or = explode('-or-', $_param_options);
                                    $_response['where']['or'][] = $_options_or;
                                    $_response['where']['to_collect'][] = $_options_or;
                                    foreach ($_options_or as $_option) {
                                        $_response['params'][] = $_option;
                                    }
                                } elseif (str_is('*-and-*', $_param_options)) {
                                    $_options_and = explode('-and-', $_param_options);
                                    $_response['where']['and'][] = $_options_and;
                                    foreach ($_options_and as $_option) {
                                        $_response['where']['to_collect'][] = $_option;
                                        $_response['params'][] = $_option;
                                    }
                                } else {
                                    $_response['where']['and'][] = $_param_options;
                                    $_response['where']['to_collect'][] = $_param_options;
                                    $_response['params'][] = $_param_options;
                                }
                            }
                        } catch (\Exception $exception) {
                        }
                    }
                }
            }
        }
        if ($query_url = request()->all()) {
            if (is_array($query_url) && $query_url) {
                $_response['use'] = TRUE;
                foreach ($query_url as $_param => $_options) {
                    if (!in_array($_param, $_exclude_query_param)) {
                        if (!in_array($_param, [
                            'sort',
                            'price'
                        ])) {
                            if (is_array($_options) && isset($_options['min']) && isset($_options['max'])) {
                                $_response['where']['between'][$_param] = $_options;
                            }
                        } elseif ($_param == 'price') {
                            $_response['price'] = $_options;
                        } else {
                            $_response['sort'] = $_options;
                        }
                    }
                }
            }
        }

        return $_response;
    }

    public function _render($options = NULL)
    {
        $_alias_parse = explode('-cfp-', $this->base_path);
        $_category = $this->_category;
        $_category->filterRequest = self::parse_category_params($_alias_parse[1]);
        $_category->filterPage = $this;

        // wrap()->set('seo.canonical', $_category->generate_url);

        return $_category->_render();
    }

    public function _render_ajax(Request $request)
    {
        $_alias_parse = explode('-cfp-', $this->base_path);
        $_category = $this->_category;
        $_category->filterRequest = self::parse_category_params($_alias_parse[1]);
        $_category->filterPage = $this;
        wrap()->set('seo.canonical', $_category->generate_url);

        return $_category->_render_ajax($request);
    }
}

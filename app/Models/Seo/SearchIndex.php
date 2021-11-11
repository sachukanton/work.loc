<?php

namespace App\Models\Seo;

use App\Library\BaseModel;
use App\Models\Shop\Category;
use App\Models\Structure\Page;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use phpMorphy;
use phpMorphy_FilesBundle;

class SearchIndex extends BaseModel
{

    protected $table = 'search_index';
    protected $guarded = [];
    protected $entity;
    public $timestamps = FALSE;
    protected $place = ['search_index.title'];
    protected $entity_table = NULL;
    protected $recursion = FALSE;
    const SEARCH_LOCALES = [
        'ru' => 'ru_RU',
        'en' => 'en_EN',
        'ua' => 'uk_UA'
    ];
    const SEARCH_MODELS = [
        'Page',
        'Node',
        'Tag',
        'Brand',
        'Category',
        'Product',
        'FilterPage',
    ];

    public function __construct($entity = NULL, ...$place)
    {
        parent::__construct();
        $this->entity = $entity;
        if ($entity && $place) {
            $this->entity_table = $this->entity->getTable();
            foreach ($place as $_pace) $this->place[] = "{$this->entity_table}.{$_pace}";
        }
    }

    /**
     * Relationships
     */
    public function related_model()
    {
        return $this->hasOne($this->model_type, 'id', 'model_id')
            ->with([
                '_alias'
            ]);
    }

    public function model()
    {
        return $this->morphTo()
            ->with([
                '_alias',
                '_preview'
            ]);
    }

    /**
     * Others
     */
    public function set()
    {
        if ($this->entity && in_array(class_basename($this->entity), self::SEARCH_MODELS)) {
            $_locale = request()->get('locale', config('app.default_locale'));
            $_exist = $this->entity
                ->_search_index()
                ->where('locale', $_locale)
                ->first();
            if ($_exist) {
                $_exist->title = $this->entity->title;
                $_exist->status = $this->entity->status ?? 1;
                $_exist->save();
            } else {
                $_item = new self();
                $_item->locale = $_locale;
                $_item->title = $this->entity->title;
                $_item->status = $this->entity->status ?? 1;
                $this->entity->_search_index()->save($_item);
            }
        }
    }

    public function query_search($query_string, $category = 'all', $page = TRUE)
    {
        global $wrap;
        $_response = collect([]);
        if ($query_string) {
            $_words = NULL;
            $_locale = $wrap['locale'];
            $_search_string = rawurldecode(urldecode($query_string));
            $_phpMorphy_options = array(
                'storage'           => 'file',
                'with_gramtab'      => FALSE,
                'predict_by_suffix' => TRUE,
                'predict_by_db'     => TRUE
            );
            $_locale_code = self::SEARCH_LOCALES[$_locale] ?? self::SEARCH_LOCALES[config('app.locale')];
            $_dir_phpMorphy = base_path('vendor/vladkolodka/phpmorphy/libs/phpmorphy/dicts');
            $_dict_bundle = new phpMorphy_FilesBundle($_dir_phpMorphy, $_locale_code);
            $_morphy = new phpMorphy($_dict_bundle, $_phpMorphy_options);
            $_query_words = preg_split('/\s+/', $_search_string);
            foreach ($_query_words as $_word) $_words[] = '%' . str_replace("'", "\'", $this->format_query($_morphy, $_word)) . '%';
            $_query_like = "(";
            foreach ($this->place as $_i => $_place) {
                if ($_i) {
                    $_query_like .= " OR ";
                }
                $_query_like .= "(";
                foreach ($_words as $_t => $_word) {
                    if ($_t) {
                        $_query_like .= " AND ";
                    }
                    $_query_like .= "({$_place} LIKE '{$_word}')";
                }
                $_query_like .= ")";
            }
            $_query_like .= ")";
            $_query = self::from('search_index');
            if ($this->entity) {
                $_query->join($this->entity_table, "{$this->entity_table}.id", '=', 'search_index.model_id')
                    ->where('search_index.model_type', get_class($this->entity));
            }
            $_query->where('search_index.status', 1)
                ->where('search_index.locale', $_locale)
                ->when($category != 'all', function ($__query) use ($category) {
                    $_category = Category::find($category);
                    if ($_category) {
                        $_categories = $_category->all_children;
                        if ($_categories->isNotEmpty()) {
                            $_categories = $_categories->filter(function ($_category) {
                                return $_category->status;
                            });
                        }
                        $_categories->put($_category->id, $_category);
                        $__query->leftJoin('shop_product_category', 'shop_product_category.model_id', '=', 'search_index.model_id')
                            ->whereIn('shop_product_category.category_id', $_categories->keys());
                    }
                })
                ->whereRaw(DB::raw($_query_like))
                ->with([
                    'model'
                ])
                ->select([
                    'search_index.*'
                ]);
            if ($page) {
                $_page_number = current_page();
                if ($_page_number) {
                    Paginator::currentPageResolver(function () use ($_page_number) {
                        return $_page_number;
                    });
                }
                $_response = $_query->remember(REMEMBER_LIFETIME)
                    ->paginate(12, ['search_index.model_id']);
                if ($_response->isNotEmpty()) {
                    $_response->getCollection()->transform(function ($_item) {
                        $_model = $_item->model;
                        if (method_exists($_model, '_load')) $_model->_load('teaser');

                        return $_model;
                    });
                }
                wrap()->set('seo.url_alias', 'search');
                if ($_response->isEmpty() && $_page_number) abort(404);
                if ($_page_number) wrap()->set('seo.robots', 'noindex, follow', TRUE);
                if ($_page_number) wrap()->set('seo.page_number_suffix', trans('frontend.title_suffix_page', ['page' => $_page_number]), TRUE);
                if ($_response->isNotEmpty() && $_response->hasMorePages()) {
                    $_page_number = $_page_number ? : 1;
                    $_page_number++;
                    $_current_url = 'search';
                    $_current_url_query = $wrap['seo']['url_query'] ?? '';
                    $_url = trim($_current_url, '/') . "/page-{$_page_number}";
                    $_next_page_link = _u($_url) . $_current_url_query;
                    wrap()->set('seo.link_next', $_next_page_link);
                }
            } else {
                $_response = $_query->take(3)
                    ->get();
                if ($_response->isNotEmpty()) {
                    $_response->transform(function ($_item) {
                        $_model = $_item->model;
                        if (method_exists($_model, '_load')) $_model->_load('teaser');

                        return $_model;
                    });
                } elseif ($this->recursion == FALSE) {
                    $this->recursion = TRUE;
                    $_response = $this->query_search($this->switcher($query_string), FALSE);
                }
            }
        }

        return $_response;
    }

    private function format_query($morphy, $word)
    {
        $_format = $morphy->getPseudoRoot(mb_strtoupper($word));
        if ($_format && is_array($_format) && reset($_format)) return mb_strtolower(reset($_format));

        return $word;
    }

    public function switcher($string, $arrow = NULL)
    {
        $_data['cyrillic'] = [
            'й' => 'q',
            'ц' => 'w',
            'у' => 'e',
            'к' => 'r',
            'е' => 't',
            'н' => 'y',
            'г' => 'u',
            'ш' => 'i',
            'щ' => 'o',
            'з' => 'p',
            'х' => '[',
            'ъ' => ']',
            'ф' => 'a',
            'ы' => 's',
            'в' => 'd',
            'а' => 'f',
            'п' => 'g',
            'р' => 'h',
            'о' => 'j',
            'л' => 'k',
            'д' => 'l',
            'ж' => ';',
            'э' => '\'',
            'я' => 'z',
            'ч' => 'x',
            'с' => 'c',
            'м' => 'v',
            'и' => 'b',
            'т' => 'n',
            'ь' => 'm',
            'б' => ',',
            'ю' => '.',
            'Й' => 'Q',
            'Ц' => 'W',
            'У' => 'E',
            'К' => 'R',
            'Е' => 'T',
            'Н' => 'Y',
            'Г' => 'U',
            'Ш' => 'I',
            'Щ' => 'O',
            'З' => 'P',
            'Х' => '[',
            'Ъ' => ']',
            'Ф' => 'A',
            'Ы' => 'S',
            'В' => 'D',
            'А' => 'F',
            'П' => 'G',
            'Р' => 'H',
            'О' => 'J',
            'Л' => 'K',
            'Д' => 'L',
            'Ж' => ';',
            'Э' => '\'',
            '?' => 'Z',
            'ч' => 'X',
            'С' => 'C',
            'М' => 'V',
            'И' => 'B',
            'Т' => 'N',
            'Ь' => 'M',
            'Б' => ',',
            'Ю' => '.'
        ];
        $_data['latin'] = [
            'q'  => 'й',
            'w'  => 'ц',
            'e'  => 'у',
            'r'  => 'к',
            't'  => 'е',
            'y'  => 'н',
            'u'  => 'г',
            'i'  => 'ш',
            'o'  => 'щ',
            'p'  => 'з',
            '['  => 'х',
            ']'  => 'ъ',
            'a'  => 'ф',
            's'  => 'ы',
            'd'  => 'в',
            'f'  => 'а',
            'g'  => 'п',
            'h'  => 'р',
            'j'  => 'о',
            'k'  => 'л',
            'l'  => 'д',
            ';'  => 'ж',
            '\'' => 'э',
            'z'  => 'я',
            'x'  => 'ч',
            'c'  => 'с',
            'v'  => 'м',
            'b'  => 'и',
            'n'  => 'т',
            'm'  => 'ь',
            ','  => 'б',
            '.'  => 'ю',
            'Q'  => 'Й',
            'W'  => 'Ц',
            'E'  => 'У',
            'R'  => 'К',
            'T'  => 'Е',
            'Y'  => 'Н',
            'U'  => 'Г',
            'I'  => 'Ш',
            'O'  => 'Щ',
            'P'  => 'З',
            '['  => 'Х',
            ']'  => 'Ъ',
            'A'  => 'Ф',
            'S'  => 'Ы',
            'D'  => 'В',
            'F'  => 'А',
            'G'  => 'П',
            'H'  => 'Р',
            'J'  => 'О',
            'K'  => 'Л',
            'L'  => 'Д',
            ';'  => 'Ж',
            '\'' => 'Э',
            'Z'  => '?',
            'X'  => 'ч',
            'C'  => 'С',
            'V'  => 'М',
            'B'  => 'И',
            'N'  => 'Т',
            'M'  => 'Ь',
            ','  => 'Б',
            '.'  => 'Ю'
        ];

        return strtr($string, ($arrow && isset($str[$arrow]) ? $_data[$arrow] : array_merge($_data['cyrillic'], $_data['latin'])));
    }

    public function _render($query_string, $category = 'all')
    {
        global $wrap;
        $_item = new Page();
        $_item->type = 'search';
        $_item->meta_title = trans('pages.titles.search', ['query' => ": {$query_string}"]);
        $_item->title = trans('pages.titles.search', ['query' => ": {$query_string}"]);
        $_item->breadcrumb_title = trans('frontend.titles.search', ['query' => '']);
        $_item->_render();
        $_item->_items = $this->query_search($query_string, $category);
        $_item_template = [
            "frontend.{$this->deviceTemplate}.shops.product_teaser",
            "frontend.default.shops.product_teaser",
            'backend.base.shop_product_teaser'
        ];
        foreach ($_item->_items as $_i) {
            $_item->productOutput .= clear_html(View::first($_item_template, [
                '_item' => $_i
            ]));
        }
        if (!$_item->productOutput) {
            $_item->productOutput = '<div class="col-sm-12"><div class="alert alert-warning">' . trans('frontend.not_found_items') . '</div></div>';
        }
        wrap()->set('page.breadcrumb', collect([
            [
                'name'     => trans('frontend.titles.home'),
                'url'      => _u(LaravelLocalization::getLocalizedURL($wrap['locale'], '/')),
                'position' => 1
            ],
            [
                'name'     => $_item->breadcrumb_title . ($wrap['seo']['page_number_suffix'] ?? NULL),
                'url'      => NULL,
                'position' => 3
            ]
        ]), TRUE);

        return $_item;
    }

    public function _render_ajax(Request $request)
    {
        $_items = NULL;
        $_load_more = $request->has('load_more') ? TRUE : FALSE;
        if ($_load_more == FALSE) {
            return [
                'commands' => [
                    [
                        'command' => 'UK_notification',
                        'options' => [
                            'text'   => trans('frontend.notice_an_error_has_occurred'),
                            'status' => 'danger',
                        ]
                    ]
                ]
            ];
        }
        $commands = [];
        $_query_string = $request->input('query', NULL);
        $_category = $request->input('category', 'all');
        $_items = $this->query_search($_query_string, $_category);
        if ($_load_more) {
            $_items_output = NULL;
            $_item_template = [
                "frontend.{$this->deviceTemplate}.shops.product_teaser",
                "frontend.default.shops.product_teaser",
                'backend.base.shop_product_teaser'
            ];
            foreach ($_items as $_item) $_items_output .= clear_html(View::first($_item_template, compact('_item')));
            $commands['commands'][] = [
                'command' => 'append',
                'options' => [
                    'target' => '#uk-items-list',
                    'data'   => clear_html($_items_output)
                ]
            ];
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-pagination',
                    'data'   => clear_html($_items->links('backend.base.pagination'))
                ]
            ];
            $commands['commands'][] = [
                'command' => 'html',
                'options' => [
                    'target' => '#uk-items-list-body',
                    'data'   => ''
                ]
            ];
        }

        return $commands;
    }

}

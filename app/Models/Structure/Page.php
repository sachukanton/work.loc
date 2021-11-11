<?php

namespace App\Models\Structure;

use App\Library\BaseModel;
use App\Models\Pharm\PharmPharmacy;
use App\Models\Shop\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class Page extends BaseModel
{

    protected $table = 'pages';
    protected $guarded = [];
    protected $types;
    public $translatable = [
        'title',
        'sub_title',
        'breadcrumb_title',
        'teaser',
        'body',
        'meta_title',
        'meta_keywords',
        'meta_description',
    ];
    const TYPES_USING_DEFAULT_TAGS = [
        'list_nodes',
        'galleries',
        'reviews',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->types = [
            'normal'     => 'Обычная страница',
            'list_nodes' => 'Страница со спискам материалов',
            'front'      => 'Главная страница',
            'sitemap'    => 'Карта сайта',
            'search'     => 'Страница с результатами поиска',
            'contacts'   => 'Контакты',
            'reviews'    => 'Отзывы',
            'galleries'  => 'Галерея',
            'faq'        => 'Вопрос / Ответ',
        ];
    }

    public function _types($type)
    {
        return $this->types[$type] ?? NULL;
    }

    public function _load($view_mode = 'full')
    {
        if (!$this->status) $this->style_class = $this->style_class ? "{$this->style_class} uk-page-not-published" : 'uk-page-not-published';
        switch ($view_mode) {
            default:
                $this->body = content_render($this);
                $this->relatedMedias = $this->_files_related()->wherePivot('type', 'medias')->get();
                $this->relatedFiles = $this->_files_related()->wherePivot('type', 'files')->get();
                break;
        }

        return $this;
    }

    public function _last_nodes($take = 5, $exclude = [])
    {
        $_items = $this->_nodes();
        if (is_array($exclude)) $_items->whereNotIn('id', $exclude);
        $_items = $_items->active()
            ->visibleOnBlock()
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take($take)
            ->get();
        if ($_items->isNotEmpty()) {
            $_items = $_items->map(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });
        }

        return $_items;
    }

    public function _nodes()
    {
        return $this->hasMany(Node::class, 'page_id')
            ->where('visible_on_list', 1)
            ->with([
                '_alias'   => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_page'    => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_user'    => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                },
                '_preview' => function ($q) {
                    $q->remember(REMEMBER_LIFETIME);
                }
            ])
            ->remember(REMEMBER_LIFETIME);
    }

    public function _faqs()
    {
        return Faq::active()
            ->orderBy('sort')
            ->orderBy('question')
            ->remember(REMEMBER_LIFETIME)
            ->get();
    }

    public function _render($options = NULL)
    {
        global $wrap;
        $_view = $options['view_mode'] ?? NULL;
        $this->_load($_view);
        $_set_wrap = [
            'seo.title'         => $this->meta_title ? : $this->title,
            'seo.keywords'      => $this->meta_keywords,
            'seo.description'   => $this->meta_description,
            'seo.robots'        => $this->meta_robots,
            'seo.last_modified' => $this->last_modified,
            'page.title'        => $this->title,
            'page.style_id'     => $this->style_id,
            'page.style_class'  => $this->style_class ? [$this->style_class] : NULL,
            'page.breadcrumb'   => breadcrumb_render(['entity' => $this]),
            'seo.open_graph'    => [
                'title'       => $this->title,
                'description' => $this->meta_description ?: config_data_load(config('os_seo'), 'settings.*.description', $wrap['locale']),
                'url'         => $wrap['seo']['base_url'] . $this->generate_url,
            ]
        ];
        $_items = collect([]);
        $_page_number = current_page();
        switch ($this->type) {
            case 'list_nodes':
                $_items = $this->_nodes()
                    ->select([
                        'id',
                        'sub_title',
                        'title',
                        'page_id',
                        'preview_fid',
                        'sort',
                        'published_at',
                        'teaser',
                        'body'
                    ])
                    ->active()
                    ->visibleOnList()
                    ->orderBy('sort')
                    ->orderByDesc('published_at')
                    ->orderByDesc('created_at');
                if ($this->per_page) {
                    if ($_page_number) {
                        Paginator::currentPageResolver(function () use ($_page_number) {
                            return $_page_number;
                        });
                    }
                    $_items = $_items->paginate($this->per_page)
                        ->onEachSide(1);
                } else {
                    $_items = $_items->get();
                }
                break;
            case 'faq':
                $_items = $this->_faqs();
                break;
            case 'contacts':
                break;
        }
//        switch ($this->id) {
//            case 16:
//                $this->pharmacies = PharmPharmacy::_pharmacies_maps();
//                break;
//        }
        if ($this->per_page && $_items->isNotEmpty()) {
            $_items->getCollection()->transform(function ($_item) {
                if (method_exists($_item, '_load')) $_item->_load('teaser');

                return $_item;
            });
        } else {
            $_items->map(function (&$_node) {
                if (method_exists($_node, '_load')) $_node->_load('teaser');

                return $_node;
            });
        }
        $this->_items = $_items;
        if ($this->per_page && $_page_number && $this->_items->isEmpty()) abort(404);
        if ($this->per_page && $_page_number) $_set_wrap['seo.robots'] = 'noindex, follow';
        if ($this->per_page && $_items->isNotEmpty() && $_items->hasMorePages()) {
            $_page_number = $_page_number ? : 1;
            $_page_number++;
            $_current_url = $wrap['seo']['url_alias'];
            $_current_url_query = $wrap['seo']['url_query'];
            $_url = trim($_current_url, '/') . "/page-{$_page_number}";
            $_next_page_link = _u($_url) . $_current_url_query;
            $_set_wrap['seo.link_next'] = ($wrap['locale'] != DEFAULT_LOCALE ? "/{$wrap['locale']}" : NULL) . $_next_page_link;
        }
        $this->setWrap($_set_wrap);
        $_template = [
            "frontend.{$this->deviceTemplate}.pages.page_{$this->id}",
            "frontend.{$this->deviceTemplate}.pages.{$this->type}_{$this->id}",
            "frontend.{$this->deviceTemplate}.pages.{$this->type}",
            "frontend.{$this->deviceTemplate}.pages.page",
            "frontend.default.pages.page_{$this->id}",
            "frontend.default.pages.{$this->type}_{$this->id}",
            "frontend.default.pages.{$this->type}",
            "frontend.default.pages.page",
            "backend.base.page_{$this->type}",
            'backend.base.page'
        ];
        if (isset($options['view']) && $options['view']) array_unshift($_template, $options['view']);
        $this->template = $_template;

        return $this;
    }

    public function _render_ajax(Request $request)
    {
        global $wrap;
        $this->_load();
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
        $_page_number = current_page();
        switch ($this->type) {
            case 'list_nodes':
                $_items = $this->_nodes()
                    ->select([
                        'id',
                        'title',
                        'page_id',
                        'preview_fid',
                        'sort',
                        'published_at',
                        'teaser',
                        'body'
                    ])
                    ->active()
                    ->visibleOnList()
                    ->orderBy('sort')
                    ->orderByDesc('published_at')
                    ->orderByDesc('created_at');
                if ($this->per_page) {
                    if ($_page_number) {
                        Paginator::currentPageResolver(function () use ($_page_number) {
                            return $_page_number;
                        });
                    }
                    $_items = $_items->paginate($this->per_page);
                } else {
                    $_items = $_items->get();
                }
                break;
            case 'faq':
                $_items = $this->_faqs();
                break;
        }
        if ($this->per_page) {
            if ($_items->isNotEmpty()) {
                $_items->getCollection()->transform(function ($_item) {
                    if (method_exists($_item, '_load')) $_item->_load('teaser');

                    return $_item;
                });
            }
        }
        if ($_items->isNotEmpty() && $_items->hasMorePages()) {
            $_page_number = $_page_number ? : 1;
            $_page_number++;
            $_current_url = wrap()->get('seo.url_alias');
            $_current_url_query = wrap()->get('seo.url_query');
            $_url = trim($_current_url, '/') . "/page-{$_page_number}";
            $_next_page_link = ($wrap['locale'] != DEFAULT_LOCALE ? "/{$wrap['locale']}" : NULL) . _u($_url) . $_current_url_query;
            wrap()->set('seo.link_next', $_next_page_link);
        }
        if ($_load_more) {
            $_items_output = NULL;
            $_item_template = [
                "frontend.{$this->deviceTemplate}.nodes.node_teaser_{$this->id}",
                "frontend.{$this->deviceTemplate}.nodes.node_teaser",
                "frontend.default.nodes.node_teaser_{$this->id}",
                "frontend.default.nodes.node_teaser",
                'backend.base.node_teaser'
            ];
            foreach ($_items as $_item) {
                $_items_output .= View::first($_item_template, compact('_item'))
                    ->render(function ($view, $_content) {
                        return clear_html($_content);
                    });
            }
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
                    'data'   => clear_html($_items->links('frontend.default.partials.pagination'))
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

    public function getSchemaAttribute()
    {
        global $wrap;
        $_response = [
            "@context"    => "https://schema.org",
            "@type"       => "WebPage",
            "name"        => $this->title,
            "description" => "",
            "publisher"   => [
                "@type" => "Organization",
                "name"  => $wrap['page']['site_name'],
                "logo"  => [
                    "@type"  => "ImageObject",
                    "url"    => "{$wrap['seo']['base_url']}/template/logotypes/logotype.png",
                    "width"  => NULL,
                    "height" => NULL
                ]
            ],
        ];

        return json_encode($_response);
    }

    public function _category_menu()
    {
        $_items = Category::orderBy('parent_id')
            ->where('status', 1)
            ->orderBy('sort')
            ->remember(REMEMBER_LIFETIME)
            ->with([
                '_parent',
                '_children'
            ])
            ->get([
                'id',
                'parent_id',
                'title',

            ]);

        return $_items;
    }
}
